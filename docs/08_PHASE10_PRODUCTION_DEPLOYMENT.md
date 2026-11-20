# 08 — PHASE 10 PRODUCTION DEPLOYMENT

Dokumen ini adalah runbook go-live Website MIN 6 JEMBER. Tidak ada domain production yang di-hardcode di repository.

## 1. Kondisi sebelum go-live

Wajib:
- source berada pada commit PHASE 10 atau lebih baru;
- seluruh SQL upgrade lokal sudah dijalankan;
- data DEMO sudah ditinjau/diganti;
- periode SPMB yang tampil benar-benar masih berlaku;
- kontak, email, telepon, alamat, dan Instagram sudah diverifikasi internal;
- logo/media final sudah tersedia;
- backup database dan folder uploads tersedia.

## 2. Environment production

Buat `.env` di server, jangan commit.

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://DOMAIN-FINAL/'
app.forceGlobalSecureRequests = true
app.allowedHostnames.0 = 'DOMAIN-FINAL'

database.default.hostname = 'DB-HOST'
database.default.database = 'DB-NAME'
database.default.username = 'DB-USER'
database.default.password = 'DB-PASSWORD'
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

encryption.key = 'RANDOM-SECRET-KEY'

cookie.secure = true
cookie.httponly = true
cookie.samesite = Lax

logger.threshold = 4
```

Jika domain memakai host lain seperti `www`, masukkan host tersebut sebagai allowed hostname hanya bila memang dipakai. Canonical www/non-www ditentukan di panel hosting/CDN, bukan di-hardcode pada `.htaccess` aplikasi.

Jangan mengisi proxy IP dengan wildcard. Isi hanya bila hosting/CDN benar-benar memberi daftar proxy tepercaya.

## 3. Struktur web root

Proyek memakai root repository sebagai web root:

```text
app/
assets/
uploads/
vendor/
writable/
index.php
.htaccess
```

Karena itu `.htaccess` adalah lapisan keamanan wajib. Jika hosting tidak menghormati `.htaccess`, jangan go-live dengan struktur ini sebelum DocumentRoot/pengamanan server diperbaiki.

## 4. Deploy dengan terminal/SSH

Jika hosting menyediakan terminal:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
```

Pastikan PHP memenuhi versi pada `composer.json`.

## 5. Deploy jika hanya File Manager

Jika hosting tidak menyediakan Composer/SSH:

1. di lokal jalankan `composer install --no-dev --optimize-autoloader`;
2. upload source production beserta folder `vendor/`;
3. jangan upload `.git/`, dump database production, log, session, atau `.env` lokal;
4. buat `.env` production langsung di server;
5. pastikan `writable/` dan `uploads/` dapat ditulis PHP.

## 6. Database

Untuk server baru: import canonical `database/minsix.sql`, lalu isi data production/akun sesuai kebutuhan.

Untuk server yang sudah memiliki data: backup dulu, jalankan hanya SQL upgrade yang belum pernah dijalankan, dan jangan import ulang canonical dump karena bersifat destructive untuk fresh install.

## 7. Permission

- source PHP/config: read oleh web process;
- `writable/`: writable;
- `uploads/`: writable;
- jangan memberi permission 777 bila 755/775 sesuai hosting sudah cukup.

## 8. CMS setelah deploy

Login sebagai Admin lalu isi Pengaturan Website, Fitur, SEO, canonical base URL HTTPS final, Instagram mode MANUAL/HYBRID, dan SPMB current yang benar. Untuk Instagram: siapkan encryption.key, simpan Meta App ID/App Secret, daftarkan OAuth Redirect URI yang ditampilkan CMS pada Meta Developer, lalu klik Hubungkan Instagram.

## 9. Security smoke test

Semua berikut harus lolos sebelum diumumkan:

```text
/app/Config/App.php        -> 403
/database/minsix.sql       -> 403
/docs/README.md            -> 403
/.env                      -> 403
/vendor/                   -> 403/404
/writable/                 -> 403
/manager                   -> login/noindex
/robots.txt                -> text + sitemap HTTPS final
/sitemap.xml               -> XML valid
```

Periksa response HTTPS:
- Strict-Transport-Security ada;
- Content-Security-Policy ada;
- X-Content-Type-Options: nosniff;
- Referrer-Policy: strict-origin-when-cross-origin;
- X-Frame-Options: SAMEORIGIN;
- cookie session/CSRF memiliki Secure dan HttpOnly sesuai jenis cookie.

## 10. Functional smoke test

Uji homepage, Profil + Google Maps, Program, GTK, Kabar/detail Berita, Agenda, Prestasi, Galeri/lightbox, SPMB, Instagram fallback MANUAL, Instagram Login callback, Tes Koneksi, Sync Sekarang, refresh token, post CAROUSEL_ALBUM, login Admin/Operator, User Management Admin-only, CRUD konten, upload image/PDF, serta feature OFF.

## 11. Cache dan browser

Setelah upload versi baru: hard refresh browser, purge cache hosting/CDN bila ada, jangan cache `/manager`, dan static assets boleh memakai cache dari `.htaccess`.

## 12. Backup/rollback

Sebelum setiap deploy: backup DB, backup `uploads/`, dan catat commit production.

Rollback source memakai commit sebelumnya. Rollback database hanya dari backup yang dibuat sebelum SQL upgrade.

## 13. Go-live gate

Website baru dianggap production PASS bila HTTPS final aktif, baseURL/canonical benar, internal path terlindungi, RBAC benar, CRUD smoke test lolos, mobile/desktop visual lolos, tidak ada konten DEMO yang tidak dimaksudkan, tidak ada SPMB kedaluwarsa sebagai current, dan error 400/404/500 tidak membocorkan debug detail.

## 14. Hosting preflight dan paket deployment

Informasi provider/paket hosting, domain final, PHP hosting, document root, serta akses terminal belum ditetapkan pada dokumen proyek. Jangan mengunci nilai .env atau menganggap hosting menyediakan SSH sebelum data tersebut dikonfirmasi.

Persyaratan minimum server:
- PHP 8.2 atau lebih baru.
- Ekstensi PHP intl dan mbstring (wajib framework), mysqli (MySQL/MariaDB), serta fileinfo dan curl untuk fitur upload/integrasi API. Periksa GD bila image processing menggunakannya.
- MySQL/MariaDB dengan InnoDB, foreign key, dan utf8mb4.
- Apache dengan mod_rewrite, mod_headers, dan AllowOverride yang mengizinkan .htaccess pada root serta uploads.
- HTTPS aktif dan PHP dapat menulis ke writable/ serta uploads/.

PENTING: aplikasi saat ini memakai root proyek sebagai web root; isi bawaan folder public CI4 telah ditempatkan di root. Proteksi direktori internal bergantung pada .htaccess. Jika hosting memakai Nginx tanpa aturan proteksi ekuivalen atau tidak mengizinkan .htaccess, jangan go-live sebelum document root dan aturan keamanan disesuaikan serta diuji.

Folder vendor/ tidak disimpan di Git. Sebelum membuat ZIP deployment pada komputer pengembang, jalankan dari root repository:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

Pastikan vendor/autoload.php tersedia. ZIP harus memuat app/, assets/, uploads/ yang diperlukan, vendor/, writable/ beserta placeholder, index.php, .htaccess, composer.json, dan composer.lock. Jangan sertakan .git/, .env, tests/, docs/, cache/log/session lokal, credential, atau dump database production.

Jika hanya tersedia File Manager dan pengelola database: backup folder tujuan; upload dan ekstrak ZIP; pastikan index.php, .htaccess, app/, dan vendor/ berada pada level yang benar; buat .env production langsung di server; atur permission writable/ dan uploads/; import SQL hanya untuk database baru atau upgrade yang belum diterapkan; lalu hapus ZIP dan SQL sementara setelah verifikasi. Jangan pernah mengunggah .env lokal.

Untuk PHP production, pastikan konfigurasi Database memakai DBDebug nonaktif saat CI_ENVIRONMENT=production. Build paket memakai dependency production saja, bukan vendor hasil instalasi development.

## 15. Informasi yang harus dikonfirmasi sebelum deployment nyata

- Provider dan nama paket hosting.
- Domain final dan pilihan www/non-www.
- Versi PHP dan ekstensi yang aktif.
- Apache/Nginx, dukungan .htaccess/mod_rewrite, dan document root.
- Akses SSH/terminal atau hanya File Manager.
- Host database, nama database, dan user database (jangan kirim password).
- Status SSL/HTTPS.

Jangan mengirim password database, encryption key, Meta App Secret, atau token melalui chat.

## 16. Batas status

PHASE 10 source preparation tidak sama dengan production PASS. Status production hanya boleh dinyatakan PASS setelah paket terpasang pada hosting sebenarnya, HTTPS dan proteksi direktori teruji, konfigurasi database/permission benar, dan seluruh functional serta security smoke test lulus.
