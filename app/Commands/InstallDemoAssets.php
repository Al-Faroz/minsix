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
        if ($db->tableExists('app_users')) {
            $userRow = $db->table('app_users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
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
                        if ($this->normalizeName((string) $gtk['name']) === $this->normalizeName($stem) && empty($gtk['photo_media_id'])) {
                            $db->table('gtk')->where('id', $gtk['id'])->update(['photo_media_id' => $mediaId]);
                            $linked++;
                            break;
                        }
                    }
                }
            }
        }
        $gtkSeedSql = $media->getFromName('20261002_initial_content_seed.sql');
        $media->close();

        // Impor hanya data GTK dan penugasan peran dari seed media demo PHASE 8.
        // Pernyataan lain (SPMB, berita, homepage, dan modul lain) sengaja tidak dijalankan.
        $gtkInserted = 0;
        if (is_string($gtkSeedSql) && $db->tableExists('gtk')) {
            $gtkInserted = $this->installGtkDemoData($db, $gtkSeedSql, $createdBy);
        }

        // Hubungkan aset demo ke section publik beranda PHASE 10C.
        // Hanya mengisi relasi yang masih kosong agar media pilihan operator tidak tertimpa.
        $homepageMedia = [
            'hero' => 'uploads/demo/activities/hero_welcome.jpg',
            'about' => 'uploads/demo/activities/ceremony.jpg',
            'habits' => 'uploads/demo/activities/religious.jpg',
            'program_intro' => 'uploads/demo/activities/classroom.jpg',
            'achievement_intro' => 'uploads/demo/activities/achievement.jpg',
            'story_intro' => 'uploads/demo/activities/extracurricular.jpg',
            'headmaster' => 'uploads/demo/activities/hero_welcome.jpg',
        ];
        if ($db->tableExists('homepage_sections')) {
            foreach ($homepageMedia as $sectionKey => $path) {
                $asset = $db->table('media')->select('id')->where('relative_path', $path)->get()->getRowArray();
                if ($asset) {
                    $db->table('homepage_sections')
                        ->where('section_key', $sectionKey)
                        ->where('primary_media_id', null)
                        ->update(['primary_media_id' => (int) $asset['id']]);
                }
            }
        }

        $spmbSeed = ROOTPATH . 'database/seeds/spmb_demo_2027_2028.sql';
        if (! is_file($spmbSeed) || ! $this->runSqlSeed($db, $spmbSeed)) {
            CLI::error('Aset media terpasang, tetapi seed demo SPMB gagal dijalankan. Periksa database/seeds/spmb_demo_2027_2028.sql.');
            return;
        }

        $qr = $db->table('media')->select('id')->where('relative_path', 'uploads/demo/activities/spmb_qr.jpg')->get()->getRowArray();
        $brochure = $db->table('media')->select('id')->where('relative_path', 'uploads/demo/activities/spmb_2026_2027_poster.pdf')->get()->getRowArray();
        $spmbDemo = $db->table('spmb_periods')->where('title', '[DATA DEMO] SPMB MIN 6 JEMBER 2027/2028')->get()->getRowArray();
        if ($spmbDemo) {
            $db->table('spmb_periods')->where('id', $spmbDemo['id'])->update([
                'qr_media_id' => $qr['id'] ?? null,
                'brochure_media_id' => $brochure['id'] ?? null,
            ]);
        }

        CLI::newLine();
        CLI::write('Pemasangan aset selesai.', 'green');
        CLI::write('Media terdaftar/diperbarui : ' . $installed);
        $gtkPhotoCount = 0;
        if ($db->tableExists('gtk')) {
            $gtkPhotoCount = $db->table('gtk g')
                ->join('media m', 'm.id = g.photo_media_id', 'inner')
                ->like('m.relative_path', 'uploads/demo/gtk/', 'after')
                ->countAllResults();
        }
        CLI::write('Data GTK demo ditambahkan : ' . $gtkInserted);
        CLI::write('Foto GTK demo terhubung   : ' . $gtkPhotoCount);
        CLI::write('Data demo SPMB 2027/2028  : DRAFT (tidak tampil publik)');
        CLI::write('File dilewati             : ' . $skipped);
        CLI::write('Lokasi media               : uploads/demo/');
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

    private function normalizeName(string $value): string
    {
        $value = strtolower(trim($value));
        return preg_replace('/[^a-z0-9]/', '', $value) ?? '';
    }

    private function installGtkDemoData(\CodeIgniter\Database\BaseConnection $db, string $sql, int $createdBy): int
    {
        $inserted = 0;
        $actor = $createdBy > 0 ? (string) $createdBy : 'NULL';
        $statements = preg_split('/;\\s*(?:\\r?\\n|$)/', $sql) ?: [];

        foreach ($statements as $statement) {
            $statement = trim($statement);
            $statement = preg_replace('/^(?:\\s*--[^\\r\\n]*(?:\\r?\\n|$))+/', '', $statement) ?? $statement;
            if (preg_match('/^INSERT\\s+INTO\\s+gtk\\s*\\(/i', $statement)) {
                $statement = preg_replace(
                    '/,\\s*1\\s*,\\s*1\\s*,\\s*NOW\\(\\)\\s*,\\s*NOW\\(\\)/i',
                    ',' . $actor . ',' . $actor . ',NOW(),NOW()',
                    $statement,
                    1
                ) ?? $statement;
            } elseif (preg_match('/^INSERT\\s+INTO\\s+gtk_role_assignments\\s*\\(/i', $statement)) {
                if (! $db->tableExists('gtk_role_assignments') || ! $db->tableExists('gtk_roles')) {
                    continue;
                }
            } else {
                continue;
            }

            $isGtkRecord = (bool) preg_match('/^INSERT\\s+INTO\\s+gtk\\s*\\(/i', $statement);
            if ($db->query($statement) === false) {
                log_message('error', 'Gagal memasang data demo GTK: ' . $db->error()['message']);
                continue;
            }

            if ($isGtkRecord) {
                $inserted += $db->affectedRows();
            }
        }

        return $inserted;
    }

    private function altText(string $filename): string
    {
        $name = ucwords(str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME)));
        return 'Dokumentasi MIN 6 Jember - ' . $name;
    }
}
