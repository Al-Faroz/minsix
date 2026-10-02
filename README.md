# MIN SIX — Website MIN 6 Jember

Website resmi MIN 6 Jember berbasis CodeIgniter 4 dengan CMS sederhana.

## Environment
- PHP 8.2+
- CodeIgniter 4
- MySQL/MariaDB
- Local: `G:\xampp\htdocs\minsix`
- URL: `http://localhost/minsix/`
- CMS: `http://localhost/minsix/manager`

## Database
Schema aplikasi menggunakan SQL dump:
`database/minsix.sql`

Tidak menggunakan Migration dan Seeder untuk schema aplikasi.

## Setup awal
1. `composer install`
2. Siapkan `.env` lokal.
3. Buat database `minsix`.
4. Import `database/minsix.sql`.
5. Buat Admin:
   `php spark minsix:user:create admin "Administrator" ADMIN`
6. Login ke `/manager/login` dan ubah password sementara.

## Role
- ADMIN: sistem + konten
- OPERATOR: konten saja

## Catatan deployment
Isi folder bawaan `public/` ditempatkan di root proyek. Karena itu root `.htaccess` wajib melindungi folder internal CI4.

Lihat `docs/` untuk seluruh dokumen acuan.
