# 11 — Data Demo SPMB dan Aset Identitas Visual

## Data demo SPMB 2027/2028

File: `database/demo_spmb_2027_2028.sql`

- Diimpor setelah `database/minsix.sql`.
- Membuat satu periode Tahun Pelajaran 2027/2028.
- Status awal **DRAFT**, `is_current = 0`; tidak mengubah halaman publik.
- Menyediakan contoh persyaratan, tahapan, program unggulan, dan FAQ.
- Tidak membuat akun pendaftar, tabel calon murid, atau menyimpan data pribadi.
- URL pendaftaran, QR, dan brosur dibiarkan kosong sampai tautan/dokumen resmi tersedia.
- Tanggal pada draft hanya contoh rancangan, bukan pengumuman resmi.
- Import bersifat aman untuk pengulangan: jika tahun ajaran sudah ada, record periode yang ada tidak ditimpa.

### Cara import

1. Pastikan database MIN SIX sudah dibuat dan `database/minsix.sql` sudah diimpor.
2. Buka phpMyAdmin → pilih database `minsix`.
3. Pilih tab **Import**.
4. Pilih file `database/demo_spmb_2027_2028.sql` dari repository.
5. Jalankan import.
6. Buka Manager → SPMB. Periode 2027/2028 akan tampil sebagai DRAFT.
7. Edit semua jadwal, persyaratan, tahapan, kontak, dan isi sebelum publikasi. Jangan mengubah ke PUBLISHED/Current sebelum disetujui panitia.

Jika periode 2027/2028 sudah pernah dibuat, script tidak menimpa isinya. Perubahan lanjutan dilakukan melalui CMS.

## Aset identitas visual

Logo resmi MIN 6 Jember adalah satu-satunya identitas logo yang digunakan aplikasi. Jangan membuat, mengganti, atau memakai logo konsep/alternatif tanpa persetujuan resmi madrasah.

File logo resmi yang digunakan:
- `assets/brand/min6-logo.png` — logo utama.
- `assets/brand/min6-logo-white.png` — varian putih untuk latar gelap.
- `assets/brand/favicon-32x32.png` — favicon.
- `favicon.ico` — favicon ICO di root.

`brand-preview.html` hanya menjadi halaman pratinjau internal untuk memeriksa file logo resmi. Path asset menggunakan path relatif agar dapat dibuka pada instalasi subfolder seperti `/minsix`.

Ilustrasi SPMB `assets/brand/min6-spmb-cover-concept.svg` adalah elemen grafis pendukung, bukan logo dan bukan foto dokumentasi madrasah. Hero halaman publik SPMB boleh menggunakannya dengan keterangan yang jelas bahwa visual tersebut merupakan ilustrasi konseptual.

### Penerapan

- Header publik menggunakan `min6-logo.png`.
- Footer publik menggunakan `min6-logo-white.png`.
- Favicon menggunakan `favicon-32x32.png` dan `favicon.ico`.
- Jangan mengganti aset logo resmi dengan konsep desain, hasil generatif, atau logo alternatif.
- Jangan menyatakan ilustrasi SPMB sebagai dokumentasi kegiatan/fasilitas MIN 6 Jember.

## Validasi setelah pull

- [ ] Import SQL demo berhasil tanpa error foreign key.
- [ ] Periode 2027/2028 tampil di Manager → SPMB dengan status DRAFT.
- [ ] Tidak ada periode yang berubah menjadi Current.
- [ ] Persyaratan, tahapan, program, dan FAQ tampil pada halaman edit.
- [ ] Logo utama, varian putih, dan favicon resmi tampil di `/brand-preview.html`.
- [ ] Tidak ada aset konsep yang disalahartikan sebagai foto dokumentasi.
