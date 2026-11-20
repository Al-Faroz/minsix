<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstallDemoAssets extends BaseCommand
{
    protected $group = 'MIN SIX';
    protected $name = 'minsix:assets:install';
    protected $description = 'Pasang aset logo dan media demo ke website serta Media Library.';
    protected $usage = 'minsix:assets:install';

    public function run(array $params)
    {
        if (! class_exists(\ZipArchive::class)) {
            CLI::error('Ekstensi PHP ZipArchive belum aktif. Aktifkan extension=zip lalu ulangi.');
            return;
        }

        $mediaZip = ROOTPATH . 'storage/recovery/MIN6_Demo_Media_Pack.zip';
        $logoZip = ROOTPATH . 'storage/recovery/MIN6_Logo_Asset_Pack.zip';

        if (! is_file($mediaZip) || ! is_file($logoZip)) {
            CLI::error('Paket ZIP tidak ditemukan di storage/recovery/. Pastikan kedua ZIP ikut ter-pull dari Git.');
            return;
        }

        $logo = new \ZipArchive();
        if ($logo->open($logoZip) !== true) {
            CLI::error('Paket logo tidak dapat dibuka.');
            return;
        }
        $logoOk = $logo->extractTo(ROOTPATH);
        $logo->close();
        if (! $logoOk) {
            CLI::error('Ekstraksi aset logo gagal.');
            return;
        }
        CLI::write('Aset logo dan favicon berhasil dipasang.', 'green');

        $media = new \ZipArchive();
        if ($media->open($mediaZip) !== true) {
            CLI::error('Paket media tidak dapat dibuka.');
            return;
        }

        $db = db_connect();
        if (! $db->tableExists('media')) {
            $media->close();
            CLI::error('Tabel media belum tersedia. Jalankan migrasi CMS terlebih dahulu.');
            return;
        }

        $createdBy = 0;
        if ($db->tableExists('users')) {
            $userRow = $db->table('users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
            $createdBy = (int) ($userRow['id'] ?? 0);
        }

        $installed = 0;
        $linked = 0;
        $skipped = 0;
        $targetRoot = FCPATH;

        for ($index = 0; $index < $media->numFiles; $index++) {
            $entry = $media->getNameIndex($index);
            if (! is_string($entry) || str_ends_with($entry, '/')) {
                continue;
            }

            $relativePath = str_replace('\\', '/', $entry);
            if (! preg_match('#^uploads/demo/(gtk|activities)/[a-z0-9._-]+\.(jpg|jpeg|png|webp|pdf)$#i', $relativePath)) {
                continue;
            }

            $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
            $destination = $targetRoot . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $directory = dirname($destination);
            if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
                $skipped++;
                continue;
            }

            $stream = $media->getStream($entry);
            if ($stream === false) {
                $skipped++;
                continue;
            }
            $output = fopen($destination, 'wb');
            if ($output === false) {
                fclose($stream);
                $skipped++;
                continue;
            }
            stream_copy_to_stream($stream, $output);
            fclose($stream);
            fclose($output);

            $size = (int) (filesize($destination) ?: 0);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($destination) ?: 'application/octet-stream';
            $isImage = str_starts_with($mime, 'image/');
            $dimensions = $isImage ? @getimagesize($destination) : false;
            if ($isImage && ! is_array($dimensions)) {
                @unlink($destination);
                $skipped++;
                continue;
            }

            $originalName = basename($relativePath);
            $existing = $db->table('media')->where('relative_path', $relativePath)->get()->getRowArray();
            $metadata = [
                'original_name' => $originalName,
                'stored_name' => $originalName,
                'relative_path' => $relativePath,
                'mime_type' => $mime,
                'extension' => $extension,
                'file_size' => $size,
                'width' => $dimensions ? (int) $dimensions[0] : null,
                'height' => $dimensions ? (int) $dimensions[1] : null,
                'alt_text' => $isImage ? $this->altText($originalName) : '',
                'caption' => 'Aset demo MIN 6 Jember. Silakan verifikasi dan ganti dengan dokumentasi resmi bila diperlukan.',
                'media_type' => $isImage ? 'IMAGE' : 'DOCUMENT',
                'created_by' => $createdBy > 0 ? $createdBy : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('media')->where('id', $existing['id'])->update($metadata);
                $mediaId = (int) $existing['id'];
            } else {
                $db->table('media')->insert($metadata);
                $mediaId = (int) $db->insertID();
            }

            if ($mediaId > 0) {
                $installed++;
                if (str_starts_with($relativePath, 'uploads/demo/gtk/') && $db->tableExists('gtk')) {
                    $stem = pathinfo($originalName, PATHINFO_FILENAME);
                    foreach ($db->table('gtk')->get()->getResultArray() as $gtk) {
                        if ($this->slug((string) $gtk['name']) === $stem && empty($gtk['photo_media_id'])) {
                            $db->table('gtk')->where('id', $gtk['id'])->update(['photo_media_id' => $mediaId]);
                            $linked++;
                            break;
                        }
                    }
                }
            }
        }
        $media->close();

        $spmbSeed = ROOTPATH . 'database/seeds/spmb_demo_2027_2028.sql';
        if (! is_file($spmbSeed) || ! $this->runSqlSeed($db, $spmbSeed)) {
            CLI::error('Aset media terpasang, tetapi seed demo SPMB gagal dijalankan. Periksa database/seeds/spmb_demo_2027_2028.sql.');
            return;
        }

        CLI::newLine();
        CLI::write('Pemasangan aset selesai.', 'green');
        CLI::write('Media terdaftar/diperbarui : ' . $installed);
        CLI::write('Foto GTK tertaut otomatis : ' . $linked);
        CLI::write('Data demo SPMB 2027/2028  : DRAFT (tidak tampil publik)');
        CLI::write('File dilewati             : ' . $skipped);
        CLI::write('Lokasi media               : public/uploads/demo/');
        CLI::write('Periksa Media Library dan halaman GTK setelah proses ini.');
    }

    private function runSqlSeed(\CodeIgniter\Database\BaseConnection $db, string $path): bool
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            return false;
        }

        $statements = preg_split('/;\\s*(?:\\r?\\n|$)/', $sql) ?: [];
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '' || str_starts_with($statement, '--')) {
                continue;
            }
            if ($db->query($statement) === false) {
                return false;
            }
        }

        return true;
    }

    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }

    private function altText(string $filename): string
    {
        $name = ucwords(str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME)));
        return 'Dokumentasi MIN 6 Jember - ' . $name;
    }
}
