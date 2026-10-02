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
            if (is_array($dimensions)) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
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

        $model->update($id, [
            'alt_text' => trim((string) $this->request->getPost('alt_text')),
            'caption' => trim((string) $this->request->getPost('caption')),
        ]);

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

        if ($uploadBase && $fullPath && str_starts_with($fullPath, $uploadBase . DIRECTORY_SEPARATOR) && is_file($fullPath)) {
            @unlink($fullPath);
        }

        $model->delete($id);
        $this->audit('MEDIA_DELETED', $id, 'Media dihapus: ' . $media['original_name']);

        return redirect()->to(site_url('manager/media'))->with('success', 'Media berhasil dihapus.');
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
