<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MediaModel;
use CodeIgniter\HTTP\RedirectResponse;

class MediaController extends BaseController
{
    public function index(): string
    {
        $model = new MediaModel();
        $media = $model->orderBy('id', 'DESC')->paginate(24, 'media');

        return view('manager/media/index', [
            'title' => 'Media | CMS MIN 6 Jember',
            'pageTitle' => 'Media',
            'media' => $media,
            'pager' => $model->pager,
        ]);
    }

    public function upload(): RedirectResponse
    {
        $rules = [
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,8192]|ext_in[file,jpg,jpeg,png,webp,pdf]|mime_in[file,image/jpeg,image/png,image/webp,application/pdf]',
            ],
            'alt_text' => 'permit_empty|max_length[255]',
            'caption' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File upload tidak valid.');
        }

        $originalName = $file->getClientName();

        if (preg_match('/\.(?:php\d*|phtml|phar|cgi|pl|py|sh|exe|bat|cmd)(?:\.|$)/i', $originalName)) {
            return redirect()->back()->with('error', 'Nama file mengandung ekstensi yang tidak diizinkan.');
        }

        $mime = $file->getMimeType();
        $extension = strtolower($file->getExtension());
        $size = $file->getSize();
        $storedName = $file->getRandomName();
        $subdir = date('Y/m');
        $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m');

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            return redirect()->back()->with('error', 'Folder upload tidak dapat dibuat.');
        }

        try {
            $file->move($targetDir, $storedName);
        } catch (\Throwable $e) {
            log_message('error', 'Upload media gagal: {message}', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'File gagal disimpan.');
        }

        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $storedName;
        $width = null;
        $height = null;
        $mediaType = 'DOCUMENT';

        if (str_starts_with($mime, 'image/')) {
            $mediaType = 'IMAGE';
            $dimensions = @getimagesize($fullPath);

            if (! is_array($dimensions) || empty($dimensions[0]) || empty($dimensions[1])) {
                @unlink($fullPath);
                return redirect()->back()->with('error', 'File gambar tidak dapat diverifikasi.');
            }

            $width = (int) $dimensions[0];
            $height = (int) $dimensions[1];

            if (($width * $height) > 40000000) {
                @unlink($fullPath);
                return redirect()->back()->with('error', 'Resolusi gambar terlalu besar. Maksimal 40 megapixel.');
            }

            $optimized = $this->optimizeImage($fullPath, $mime, $width, $height);
            if ($optimized !== null) {
                $width = $optimized['width'];
                $height = $optimized['height'];
                $size = (int) (filesize($fullPath) ?: $size);
            }
        }

        $relativePath = 'uploads/' . $subdir . '/' . $storedName;
        $model = new MediaModel();
        $id = $model->insert([
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'relative_path' => $relativePath,
            'mime_type' => $mime,
            'extension' => $extension,
            'file_size' => $size,
            'width' => $width,
            'height' => $height,
            'alt_text' => trim((string) $this->request->getPost('alt_text')),
            'caption' => trim((string) $this->request->getPost('caption')),
            'media_type' => $mediaType,
            'created_by' => (int) session()->get('auth_user_id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($id === false) {
            @unlink($fullPath);
            return redirect()->back()->with('error', 'Metadata media gagal disimpan.');
        }

        $this->audit('MEDIA_UPLOADED', (int) $id, 'Upload media: ' . $originalName);
        return redirect()->to(site_url('manager/media'))->with('success', 'Media berhasil di-upload.');
    }

    public function update(int $id): RedirectResponse
    {
        $model = new MediaModel();
        $media = $model->find($id);

        if (! $media) {
            return redirect()->to(site_url('manager/media'))->with('error', 'Media tidak ditemukan.');
        }

        if (! $this->validate([
            'alt_text' => 'permit_empty|max_length[255]',
            'caption' => 'permit_empty|max_length[1000]',
        ])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $updated = $model->update($id, [
            'alt_text' => trim((string) $this->request->getPost('alt_text')),
            'caption' => trim((string) $this->request->getPost('caption')),
        ]);

        if ($updated === false) {
            return redirect()->back()->with('error', 'Metadata media gagal diperbarui.');
        }

        $this->audit('MEDIA_UPDATED', $id, 'Metadata media diperbarui.');
        return redirect()->to(site_url('manager/media'))->with('success', 'Metadata media berhasil diperbarui.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new MediaModel();
        $media = $model->find($id);

        if (! $media) {
            return redirect()->to(site_url('manager/media'))->with('error', 'Media tidak ditemukan.');
        }

        $reference = $this->findReference($id);
        if ($reference !== null) {
            return redirect()->to(site_url('manager/media'))->with('error', 'Media tidak dapat dihapus karena masih digunakan pada ' . $reference . '.');
        }

        $uploadBase = realpath(FCPATH . 'uploads');
        $fullPath = realpath(FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $media['relative_path']));

        if ($model->delete($id) === false) {
            return redirect()->to(site_url('manager/media'))->with('error', 'Media gagal dihapus.');
        }

        if ($uploadBase && $fullPath && str_starts_with($fullPath, $uploadBase . DIRECTORY_SEPARATOR) && is_file($fullPath)) {
            @unlink($fullPath);
        }

        $this->audit('MEDIA_DELETED', $id, 'Media dihapus: ' . $media['original_name']);

        return redirect()->to(site_url('manager/media'))->with('success', 'Media berhasil dihapus.');
    }

    private function optimizeImage(string $path, string $mime, int $width, int $height): ?array
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $create = match ($mime) {
            'image/jpeg' => function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : null,
            'image/png' => function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : null,
            'image/webp' => function_exists('imagecreatefromwebp') ? 'imagecreatefromwebp' : null,
            default => null,
        };

        if ($create === null) {
            return null;
        }

        $source = @$create($path);
        if (! $source) {
            return null;
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;

            if ($orientation === 3) {
                $rotated = imagerotate($source, 180, 0);
                if ($rotated) {
                    imagedestroy($source);
                    $source = $rotated;
                }
            } elseif ($orientation === 6) {
                $rotated = imagerotate($source, -90, 0);
                if ($rotated) {
                    imagedestroy($source);
                    $source = $rotated;
                }
            } elseif ($orientation === 8) {
                $rotated = imagerotate($source, 90, 0);
                if ($rotated) {
                    imagedestroy($source);
                    $source = $rotated;
                }
            }
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $maxSide = 2400;
        $scale = min(1, $maxSide / max($sourceWidth, $sourceHeight));
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        if (! $target) {
            imagedestroy($source);
            return null;
        }

        if (in_array($mime, ['image/png', 'image/webp'], true)) {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        if (! imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        )) {
            imagedestroy($source);
            imagedestroy($target);
            return null;
        }

        $tmp = $path . '.opt';
        $saved = match ($mime) {
            'image/jpeg' => @imagejpeg($target, $tmp, 84),
            'image/png' => @imagepng($target, $tmp, 6),
            'image/webp' => function_exists('imagewebp') ? @imagewebp($target, $tmp, 84) : false,
            default => false,
        };

        imagedestroy($source);
        imagedestroy($target);

        if (! $saved || ! is_file($tmp) || filesize($tmp) === 0) {
            @unlink($tmp);
            return null;
        }

        if (! @rename($tmp, $path)) {
            @unlink($tmp);
            return null;
        }

        return [
            'width' => $targetWidth,
            'height' => $targetHeight,
        ];
    }

    private function findReference(int $mediaId): ?string
    {
        $db = db_connect();
        $checks = [
            ['homepage_sections', 'primary_media_id', 'Beranda'],
            ['instagram_posts', 'local_media_id', 'Instagram Manual'],
            ['profile_sections', 'primary_media_id', 'Profil'],
            ['programs', 'primary_media_id', 'Program'],
            ['gtk', 'photo_media_id', 'GTK'],
            ['news', 'primary_media_id', 'Berita'],
            ['news', 'og_media_id', 'SEO Berita'],
            ['events', 'primary_media_id', 'Agenda'],
            ['achievements', 'primary_media_id', 'Prestasi'],
            ['achievements', 'og_media_id', 'SEO Prestasi'],
            ['galleries', 'cover_media_id', 'Galeri'],
            ['gallery_items', 'media_id', 'Item Galeri'],
            ['spmb_periods', 'qr_media_id', 'QR SPMB'],
            ['spmb_periods', 'brochure_media_id', 'Brosur SPMB'],
        ];

        foreach ($checks as [$table, $field, $label]) {
            if ($db->tableExists($table) && $db->table($table)->where($field, $mediaId)->countAllResults() > 0) {
                return $label;
            }
        }

        if ($db->tableExists('site_settings')) {
            $globalOg = $db->table('site_settings')
                ->where('setting_key', 'seo_default_og_media_id')
                ->where('setting_value', (string) $mediaId)
                ->countAllResults();

            if ($globalOg > 0) {
                return 'SEO Global';
            }
        }

        return null;
    }

    private function audit(string $action, int $recordId, string $description): void
    {
        try {
            (new AuditLogModel())->insert([
                'user_id' => (int) session()->get('auth_user_id'),
                'action' => $action,
                'module' => 'MEDIA',
                'record_id' => $recordId,
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Audit log gagal: {message}', ['message' => $e->getMessage()]);
        }
    }
}
