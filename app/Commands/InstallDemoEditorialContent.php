<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstallDemoEditorialContent extends BaseCommand
{
    protected $group = 'MIN SIX';
    protected $name = 'minsix:content:demo:install';
    protected $description = 'Isi pengaturan website terverifikasi dan berita edukasi draft beserta foto ilustrasi berlisensi Pexels.';
    protected $usage = 'minsix:content:demo:install';

    private const IMAGES = [
        'literasi' => [
            'url' => 'https://images.pexels.com/photos/8926840/pexels-photo-8926840.jpeg?cs=srgb&dl=pexels-thirdman-8926840.jpg&fm=jpg',
            'filename' => 'pexels-thirdman-8926840-literasi.jpg',
            'alt' => 'Dua siswi membaca buku bersama di perpustakaan, foto ilustrasi literasi',
            'caption' => 'Foto ilustrasi literasi. Fotografer: Thirdman / Pexels. Bukan dokumentasi kegiatan MIN 6 Jember.',
            'photographer' => 'Thirdman',
            'source' => 'https://www.pexels.com/photo/students-reading-books-in-a-library-8926840/',
        ],
        'digital' => [
            'url' => 'https://images.pexels.com/photos/10638075/pexels-photo-10638075.jpeg?cs=srgb&dl=pexels-ron-lach-10638075.jpg&fm=jpg',
            'filename' => 'pexels-ron-lach-10638075-pembelajaran-digital.jpg',
            'alt' => 'Siswa menggunakan komputer dalam kegiatan belajar, foto ilustrasi pembelajaran digital',
            'caption' => 'Foto ilustrasi pembelajaran digital. Fotografer: Ron Lach / Pexels. Bukan dokumentasi kegiatan MIN 6 Jember.',
            'photographer' => 'Ron Lach',
            'source' => 'https://www.pexels.com/photo/students-using-computers-in-their-class-10638075/',
        ],
    ];

    public function run(array $params)
    {
        $db = db_connect();

        foreach (['site_settings', 'media', 'news', 'app_users'] as $table) {
            if (! $db->tableExists($table)) {
                CLI::error("Tabel {$table} belum tersedia. Jalankan seluruh migrasi CMS terlebih dahulu.");
                return;
            }
        }

        $user = $db->table('app_users')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();
        $createdBy = (int) ($user['id'] ?? 0);
        if ($createdBy < 1) {
            CLI::error('Akun administrator belum tersedia.');
            return;
        }

        $this->installSettings($db, $createdBy);

        $mediaIds = [];
        foreach (self::IMAGES as $key => $image) {
            $mediaId = $this->installImage($db, $image, $createdBy);
            if ($mediaId > 0) {
                $mediaIds[$key] = $mediaId;
            } else {
                CLI::write("Foto {$key} tidak berhasil diunduh; berita terkait tetap dibuat tanpa foto.", 'yellow');
            }
        }

        $this->installNews($db, $createdBy, $mediaIds);

        CLI::newLine();
        CLI::write('Pengisian konten awal selesai.', 'green');
        CLI::write('Pengaturan: data resmi sekolah diisi; Instagram, WhatsApp, dan Maps dibiarkan kosong karena belum diverifikasi.');
        CLI::write('Berita: 3 artikel dibuat sebagai DRAFT, tidak tampil di publik sebelum diperiksa dan diterbitkan.');
        CLI::write('Foto: sumber Pexels, dicatat sebagai ilustrasi beserta fotografer dan tautan sumber.');
    }

    private function installSettings(\CodeIgniter\Database\BaseConnection $db, int $userId): void
    {
        $values = [
            'site_name' => ['MIN 6 Jember', 'string'],
            'site_tagline' => ['Madrasah Maju, Bermutu, Mendunia', 'string'],
            'address' => ['Jl. Urip Sumoharjo Gg. Legog, Tanggul Wetan, Kecamatan Tanggul, Kabupaten Jember, Jawa Timur', 'text'],
            'phone' => ['082330634295', 'string'],
            'email' => ['mintanggulwetan@gmail.com', 'string'],
            'whatsapp' => ['', 'string'],
            'instagram_username' => ['', 'string'],
            'instagram_url' => ['', 'string'],
            'google_maps_embed' => ['', 'text'],
        ];

        foreach ($values as $key => [$value, $type]) {
            $existing = $db->table('site_settings')->where('setting_key', $key)->get()->getRowArray();
            $current = trim((string) ($existing['setting_value'] ?? ''));

            // Replace only known starter placeholders or empty values; preserve operator-entered values.
            $replaceable = $current === ''
                || ($key === 'site_tagline' && $current === 'Berakhlakul Karimah dan Berprestasi')
                || ($key === 'instagram_username' && $current === 'min6jember')
                || ($key === 'instagram_url' && $current === 'https://www.instagram.com/min6jember');

            if ($existing && ! $replaceable) {
                continue;
            }

            $data = [
                'setting_key' => $key,
                'setting_value' => $value,
                'value_type' => $type,
                'is_public' => 1,
                'updated_by' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($existing) {
                $db->table('site_settings')->where('id', $existing['id'])->update($data);
            } else {
                $db->table('site_settings')->insert($data);
            }
        }
    }

    private function installImage(\CodeIgniter\Database\BaseConnection $db, array $image, int $userId): int
    {
        $relativePath = 'uploads/news-illustrations/' . $image['filename'];
        $existing = $db->table('media')->where('relative_path', $relativePath)->get()->getRowArray();
        $destination = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $directory = dirname($destination);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return 0;
        }

        if (! is_file($destination) || filesize($destination) < 1000) {
            $bytes = $this->download($image['url']);
            if ($bytes === null) {
                return 0;
            }

            $details = @getimagesizefromstring($bytes);
            if (! is_array($details) || ($details['mime'] ?? '') !== 'image/jpeg' || strlen($bytes) > 12000000) {
                return 0;
            }

            if (file_put_contents($destination, $bytes, LOCK_EX) === false) {
                return 0;
            }
        }

        $size = (int) filesize($destination);
        $dimensions = @getimagesize($destination);
        if (! is_array($dimensions)) {
            @unlink($destination);
            return 0;
        }

        $metadata = [
            'original_name' => $image['filename'],
            'stored_name' => $image['filename'],
            'relative_path' => $relativePath,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'file_size' => $size,
            'width' => (int) $dimensions[0],
            'height' => (int) $dimensions[1],
            'alt_text' => $image['alt'],
            'caption' => $image['caption'] . ' Lisensi: Pexels License. Sumber: ' . $image['source'],
            'media_type' => 'IMAGE',
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $db->table('media')->where('id', $existing['id'])->update($metadata);
            return (int) $existing['id'];
        }

        $db->table('media')->insert($metadata);
        return (int) $db->insertID();
    }

    private function download(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $handle = curl_init($url);
            curl_setopt_array($handle, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 4,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_USERAGENT => 'MIN6Jember-CMS/1.0',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            $bytes = curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);
            return is_string($bytes) && $status >= 200 && $status < 300 ? $bytes : null;
        }

        $context = stream_context_create([
            'http' => ['timeout' => 45, 'follow_location' => 1, 'user_agent' => 'MIN6Jember-CMS/1.0'],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $bytes = @file_get_contents($url, false, $context);
        return is_string($bytes) ? $bytes : null;
    }

    private function installNews(\CodeIgniter\Database\BaseConnection $db, int $userId, array $mediaIds): void
    {
        $articles = [
            [
                'slug' => 'transformasi-digital-gtk-madrasah-google-workspace',
                'title' => 'Transformasi Digital GTK Madrasah melalui Google Workspace',
                'summary' => 'Pemanfaatan perangkat kolaborasi digital menjadi salah satu upaya meningkatkan kompetensi guru dan tenaga kependidikan serta mendukung pembelajaran yang lebih efektif.',
                'image' => 'digital',
                'meta' => 'Transformasi digital GTK madrasah melalui Google Workspace menurut Kementerian Agama.',
                'content' => '<p><strong>Catatan redaksi:</strong> Artikel ringkasan sumber eksternal untuk bahan informasi pendidikan. Ini bukan laporan kegiatan MIN 6 Jember.</p>
<p>Transformasi digital di lingkungan madrasah tidak hanya berkaitan dengan penggunaan perangkat teknologi, tetapi juga penguatan kompetensi guru dan tenaga kependidikan dalam mengelola informasi, berkolaborasi, serta mengembangkan pembelajaran.</p>
<p>Dalam publikasi tanggal 24 September 2026, Kementerian Agama membahas pemanfaatan Google Workspace (GWS) Madrasah sebagai bagian dari penguatan keterampilan digital GTK. Pemanfaatan akun dan perangkat kolaborasi digital diharapkan membantu komunikasi, pengelolaan dokumen, dan kegiatan belajar yang semakin terhubung.</p>
<p>Bagi satuan pendidikan, pengembangan tersebut perlu disertai pendampingan, tata kelola akun yang baik, perlindungan data, serta pemilihan teknologi yang sesuai dengan kebutuhan pembelajaran.</p>
<p><strong>Sumber:</strong> <a href="https://kemenag.go.id/opini/transformasi-digital-gtk-madrasah-melalui-google-workspace-gws-madrasah-kementerian-agama-6WpvW" target="_blank" rel="noopener noreferrer">Kementerian Agama RI — Transformasi Digital GTK Madrasah melalui Google Workspace (24 September 2026)</a>.</p>
<p><em>Foto hanya ilustrasi pembelajaran digital, bukan dokumentasi MIN 6 Jember.</em></p>',
            ],
            [
                'slug' => 'pembelajaran-digital-dan-hasil-belajar-siswa-madrasah',
                'title' => 'Studi Pembelajaran Digital Menyoroti Peluang Peningkatan Hasil Belajar',
                'summary' => 'Studi yang dipublikasikan Kementerian Agama terhadap 2.221 siswa di 60 madrasah menyoroti hubungan keterlibatan belajar digital dengan capaian penilaian akhir.',
                'image' => 'digital',
                'meta' => 'Ringkasan studi pembelajaran digital pada 60 madrasah menurut publikasi Kemenag.',
                'content' => '<p><strong>Catatan redaksi:</strong> Artikel ini merangkum hasil studi eksternal dan tidak menggambarkan hasil belajar siswa MIN 6 Jember.</p>
<p>Pemanfaatan teknologi dalam pembelajaran perlu diarahkan pada aktivitas belajar yang bermakna, bukan sekadar memindahkan materi ke layar. Keterlibatan siswa, pendampingan guru, dan kesesuaian materi tetap menjadi unsur penting.</p>
<p>Publikasi Kementerian Agama pada 12 November 2026 mengulas studi Alef Education yang melibatkan 2.221 siswa dari 60 madrasah di 20 provinsi selama tahun ajaran 2025–2026. Studi tersebut melaporkan bahwa siswa dengan keterlibatan aktif dalam pembelajaran digital memperoleh rata-rata nilai asesmen akhir tahun hampir 7 persen lebih tinggi.</p>
<p>Temuan tersebut merupakan hasil studi pada kelompok peserta yang diteliti. Angka itu tidak boleh langsung dianggap sebagai jaminan peningkatan nilai di setiap madrasah, karena hasil belajar juga dipengaruhi banyak faktor seperti proses pembelajaran, dukungan keluarga, kesiapan perangkat, dan kondisi siswa.</p>
<p><strong>Sumber:</strong> <a href="https://kemenag.go.id/en/daerah/studi-alef-education-siswa-di-60-madrasah-raih-nilai-hampir-7-persen-lebih-tinggi-lewat-pembelajaran-digital-sVHaq" target="_blank" rel="noopener noreferrer">Kementerian Agama RI — Studi Alef Education: Siswa di 60 Madrasah Raih Nilai Hampir 7 Persen Lebih Tinggi lewat Pembelajaran Digital (12 November 2026)</a>.</p>
<p><em>Foto hanya ilustrasi pembelajaran digital, bukan dokumentasi MIN 6 Jember.</em></p>',
            ],
            [
                'slug' => 'penguatan-karakter-literasi-dan-numerasi-generasi-emas-2045',
                'title' => 'Penguatan Karakter, Literasi, dan Numerasi untuk Generasi Emas 2045',
                'summary' => 'Literasi, numerasi, dan pembentukan karakter menjadi fondasi penting dalam menyiapkan generasi muda menghadapi tantangan masa depan.',
                'image' => 'literasi',
                'meta' => 'Pentingnya penguatan karakter, literasi, dan numerasi dalam pendidikan.',
                'content' => '<p><strong>Catatan redaksi:</strong> Artikel ini merupakan ringkasan kebijakan pendidikan nasional, bukan laporan program yang telah dilaksanakan MIN 6 Jember.</p>
<p>Pendidikan dasar memiliki peran penting dalam membangun kebiasaan belajar, kemampuan memahami informasi, bernalar, dan membentuk karakter peserta didik. Penguatan kemampuan tersebut membutuhkan pembiasaan yang konsisten di sekolah maupun di rumah.</p>
<p>Dalam siaran pers tanggal 20 Maret 2025, Kementerian Pendidikan Dasar dan Menengah menekankan penguatan karakter, literasi, dan numerasi sebagai bagian dari upaya mewujudkan Generasi Emas 2045. Salah satu contoh pembiasaan yang disoroti adalah meningkatkan budaya membaca, termasuk mendorong peserta didik membaca buku secara rutin.</p>
<p>Penguatan literasi tidak berhenti pada kemampuan mengeja. Peserta didik perlu dibimbing untuk memahami bacaan, menemukan gagasan utama, menghubungkan informasi, dan menyampaikan kembali pemahamannya. Sementara itu, numerasi perlu hadir dalam konteks kehidupan sehari-hari agar siswa terbiasa menggunakan penalaran.</p>
<p>Kolaborasi guru, keluarga, dan lingkungan sekolah menjadi kunci agar pembiasaan positif tersebut tumbuh secara berkelanjutan.</p>
<p><strong>Sumber:</strong> <a href="https://www.kemendikdasmen.go.id/siaran-pers/12505-kemendikdasmen-tekankan-penguatan-karakter-literasi-dan-numerasi-wujudkan-generasi-emas-2045" target="_blank" rel="noopener noreferrer">Kementerian Pendidikan Dasar dan Menengah — Penguatan Karakter, Literasi, dan Numerasi (20 Maret 2025)</a>.</p>
<p><em>Foto hanya ilustrasi kegiatan membaca, bukan dokumentasi MIN 6 Jember.</em></p>',
            ],
        ];

        foreach ($articles as $article) {
            if ($db->table('news')->where('slug', $article['slug'])->countAllResults() > 0) {
                continue;
            }

            $mediaId = $mediaIds[$article['image']] ?? null;
            $db->table('news')->insert([
                'slug' => $article['slug'],
                'title' => $article['title'],
                'summary' => $article['summary'],
                'content' => $article['content'],
                'primary_media_id' => $mediaId,
                'status' => 'DRAFT',
                'published_at' => null,
                'meta_title' => $article['title'],
                'meta_description' => $article['meta'],
                'og_media_id' => $mediaId,
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
