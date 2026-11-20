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

Lokasi (root proyek, sesuai konfigurasi `base_url('assets/...')`):
- `assets/brand/min6-logo-concept.svg` — logo horizontal.
- `assets/brand/min6-mark-concept.svg` — simbol mandiri, cocok untuk favicon/app icon setelah disetujui.
- `assets/brand/min6-spmb-cover-concept.svg` — ilustrasi sampul informasi SPMB, juga dipakai sebagai visual pendukung hero halaman publik SPMB dengan keterangan ilustrasi.

Halaman pratinjau: `/brand-preview.html` (untuk review internal; tidak ditautkan dari navigasi publik).

Ketiganya adalah **konsep desain digital**, bukan klaim logo resmi yang telah disahkan. Simbol menggabungkan buku terbuka, cahaya, dan tunas; palet hijau dan emas menjaga kesan pendidikan, pertumbuhan, dan identitas madrasah.

Ilustrasi SPMB bukan foto kegiatan atau fasilitas MIN 6 Jember. Gunakan sebagai elemen grafis sementara, bukan bukti dokumentasi.

### Penerapan

- Pratinjau semua konsep melalui `/brand-preview.html`.
- Ilustrasi SPMB sudah diintegrasikan ke hero halaman publik `/spmb`; pada layar kecil ilustrasi berpindah ke bawah teks. Caption menyatakan bahwa visual adalah ilustrasi konseptual, bukan foto dokumentasi.
- Pratinjau SVG langsung melalui browser.
- Logo horizontal dapat digunakan di header/footer setelah persetujuan.
- Simbol dapat dijadikan favicon setelah persetujuan.
- Sampul SPMB dapat dipakai sebagai visual sementara pada materi informasi; halaman SPMB publik tetap mengikuti data periode Current yang PUBLISHED.
- Jangan mengganti identitas/logo resmi yang telah ditetapkan madrasah tanpa persetujuan Kepala Madrasah.

## Validasi setelah pull

- [ ] Import SQL demo berhasil tanpa error foreign key.
- [ ] Periode 2027/2028 tampil di Manager → SPMB dengan status DRAFT.
- [ ] Tidak ada periode yang berubah menjadi Current.
- [ ] Persyaratan, tahapan, program, dan FAQ tampil pada halaman edit.
- [ ] Logo utama, varian putih, dan favicon resmi tampil di `/brand-preview.html`.
- [ ] Tidak ada aset konsep yang disalahartikan sebagai foto dokumentasi.
