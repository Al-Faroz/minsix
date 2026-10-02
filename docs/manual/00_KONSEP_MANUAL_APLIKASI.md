# KONSEP MANUAL APLIKASI — WEBSITE MIN 6 JEMBER

## Tujuan
Manual aplikasi dibagi menjadi beberapa file agar mudah dipakai oleh Administrator dan Operator sebagai panduan kerja harian. Manual berfokus pada penggunaan aplikasi, bukan struktur source code.

## Prinsip Pembagian
1. Satu file membahas satu kelompok pekerjaan.
2. Urutan mengikuti alur pengguna: website publik → login → dashboard → konten → integrasi → pengaturan → SOP.
3. Screenshot dipakai sebagai ilustrasi langkah dan dipotong per area penting agar tetap terbaca.
4. Setiap prosedur memakai pola yang sama: Tujuan → Hak Akses → Langkah → Hasil → Catatan/Peringatan.
5. Fungsi yang berisiko seperti Hapus, Reset Password, Putuskan Instagram, dan perubahan Fitur diberi peringatan khusus.
6. Istilah UI mengikuti teks yang benar-benar tampil pada aplikasi.

## Struktur File Manual

### MANUAL_00 — Pengantar & Peta Aplikasi
Isi:
- Identitas aplikasi Website MIN 6 JEMBER.
- Tujuan CMS.
- Perbedaan website publik dan CMS Manager.
- Peran Administrator dan Operator.
- Struktur menu CMS.
- Arti status umum seperti AKTIF, PUBLISHED, DRAFT, CURRENT.
- Cara membaca manual.
- Peta hubungan modul CMS dengan halaman publik.

### MANUAL_01 — Website Publik
Isi:
- Struktur halaman publik.
- Navigasi utama.
- Beranda publik.
- Profil.
- Program.
- GTK.
- Kabar: Berita, Agenda, Prestasi, Galeri.
- SPMB.
- Konten Instagram.
- Footer dan akses Manager.
- Penjelasan bahwa konten publik berasal dari CMS.

### MANUAL_02 — Login, Dashboard & Akun
Isi:
- Membuka halaman Manager.
- Login dengan Username dan Password.
- Penjelasan tampilan login.
- Dashboard CMS.
- Ringkasan statistik.
- Perubahan terakhir.
- Quick Action.
- Navigasi sidebar.
- Informasi pengguna di topbar.
- Ubah Password.
- Logout.
- Penanganan login gagal.

### MANUAL_03 — Beranda & Profil
Isi:
- Mengelola section Beranda.
- Mengubah judul, teks, media, urutan, dan elemen yang tersedia pada tiap section.
- Menyimpan perubahan per section.
- Mengelola Profil MIN 6 JEMBER.
- Sambutan.
- Visi.
- Misi.
- Perjalanan/identitas/lokasi dan section profil lain yang tersedia.
- Memilih media/foto.
- Memeriksa hasil di website publik.

### MANUAL_04 — Program & GTK
Isi:
- Daftar Program.
- Tambah Program.
- Edit Program.
- Kategori, urutan, status, slug bila ditampilkan sistem.
- Hapus Program.
- Daftar GTK.
- Tambah GTK.
- Edit GTK.
- Gelar depan/belakang.
- Bio singkat.
- Foto.
- Urutan.
- Status aktif.
- Multi-role/Jabatan.
- Kelola Jabatan.
- Hubungan data GTK dengan halaman publik.
- Hapus GTK dan dampaknya.

### MANUAL_05 — Kabar Madrasah
Isi:
- Berita.
- Agenda.
- Prestasi.
- Galeri.
- Pola daftar, tambah, edit, publish, dan hapus.
- Tanggal publikasi/kegiatan.
- Gambar/media.
- Detail konten.
- Item Galeri.
- Cara mengecek hasil di halaman Kabar publik.

### MANUAL_06 — SPMB & Media
Isi:
- Daftar periode SPMB.
- Tambah/Edit periode.
- Menetapkan periode CURRENT.
- Persyaratan.
- FAQ.
- Tahapan.
- Highlight.
- Pemeriksaan halaman SPMB publik.
- Media Library.
- Upload media.
- Edit metadata media.
- Penggunaan media pada konten.
- Hapus media dan perhatian terhadap media yang sedang dipakai.

### MANUAL_07 — Instagram Content & Integrasi Instagram
Isi:
- Perbedaan Instagram Content dan Pengaturan Instagram.
- Konten manual/fallback.
- Mode HYBRID dan MANUAL.
- Jumlah item carousel.
- Meta App ID dan App Secret.
- OAuth Redirect URI.
- Hubungkan Instagram.
- Tes Koneksi.
- Sinkronkan Sekarang.
- Refresh Token.
- Lifecycle token.
- Putuskan Akun.
- Dukungan CAROUSEL_ALBUM.
- Cache Instagram dan fallback bila API gagal.
- Catatan keamanan: App Secret dan token tidak dibagikan.

### MANUAL_08 — Pengaturan Administrator
Isi:
- Hak akses ADMIN.
- Pengaturan Website.
- Fitur ON/OFF.
- Dampak fitur terhadap menu/section publik.
- SEO.
- Pengguna.
- Tambah/Edit pengguna.
- Role ADMIN dan OPERATOR.
- Aktif/nonaktif pengguna.
- Reset Password.
- Praktik aman pengelolaan akun.

### MANUAL_09 — SOP Operasional & Troubleshooting
Isi:
- SOP sebelum menerbitkan konten.
- SOP mengganti foto/media.
- SOP perubahan besar Beranda/Profil.
- SOP publikasi Berita/Agenda/Prestasi/Galeri.
- SOP periode SPMB.
- SOP Instagram.
- Checklist setelah perubahan.
- Masalah umum dan tindakan awal.
- Error validasi form.
- Konten tidak tampil.
- Media tidak tampil.
- Login gagal.
- Instagram tidak tersambung/sinkron.
- Kapan harus menghubungi pengelola teknis.
- Checklist keamanan Administrator/Operator.

## Template Isi Setiap Prosedur
Setiap fungsi ditulis dengan format:
1. **Tujuan**
2. **Hak Akses** — ADMIN / OPERATOR / keduanya.
3. **Lokasi Menu**
4. **Tampilan Awal** — screenshot utama.
5. **Langkah Penggunaan** — langkah bernomor.
6. **Penjelasan Field** — bila ada form.
7. **Hasil yang Diharapkan**
8. **Perhatian** — khusus fungsi berisiko.
9. **Cek di Website Publik** — bila perubahan memengaruhi halaman publik.
10. **Masalah Umum** — bila relevan.

## Standar Visual Manual
- Ukuran A4 Portrait.
- Sampul konsisten: logo MIN 6 JEMBER, judul manual, nomor dokumen, versi/tanggal.
- Header: “Website MIN 6 JEMBER — Manual Pengguna”.
- Footer: nama file manual + nomor halaman.
- Warna mengikuti identitas website/CMS.
- Screenshot diberi crop fokus dan nomor callout.
- Maksimal satu screenshot besar atau dua screenshot detail per halaman.
- Gunakan ikon Catatan, Tips, Perhatian, dan Admin Only secara konsisten.
- Jangan menampilkan password, App Secret, access token, atau credential asli.
- Daftar isi dan bookmark PDF aktif.

## Pemetaan Screenshot Acuan Saat Ini
- Website publik penuh → MANUAL_01.
- Masuk CMS → MANUAL_02.
- Dashboard CMS → MANUAL_02.
- Beranda CMS → MANUAL_03.
- Profil CMS → MANUAL_03.
- Program CMS → MANUAL_04.
- GTK CMS → MANUAL_04.
- Tambah GTK → MANUAL_04.
- Edit GTK → MANUAL_04.

## Urutan Pembuatan
1. MANUAL_00 sebagai standar dan peta dokumen.
2. MANUAL_02 sebagai pola/template halaman manual operasional.
3. MANUAL_03 dan MANUAL_04 untuk mengunci gaya dokumentasi form dan CRUD.
4. MANUAL_05–08 mengikuti pola yang sudah disepakati.
5. MANUAL_01 dibuat setelah semua hubungan CMS → publik terpetakan.
6. MANUAL_09 dibuat terakhir dari seluruh temuan operasional dan troubleshooting.

## Output Akhir yang Disarankan
Setiap manual dibuat dalam:
- DOCX editable sebagai master.
- PDF untuk distribusi/pencetakan.

Folder final:
```
docs/manual/
├── 00_MANUAL_PENGANTAR_DAN_PETA_APLIKASI.docx
├── 01_MANUAL_WEBSITE_PUBLIK.docx
├── 02_MANUAL_LOGIN_DASHBOARD_DAN_AKUN.docx
├── 03_MANUAL_BERANDA_DAN_PROFIL.docx
├── 04_MANUAL_PROGRAM_DAN_GTK.docx
├── 05_MANUAL_KABAR_MADRASAH.docx
├── 06_MANUAL_SPMB_DAN_MEDIA.docx
├── 07_MANUAL_INSTAGRAM.docx
├── 08_MANUAL_PENGATURAN_ADMINISTRATOR.docx
└── 09_MANUAL_SOP_DAN_TROUBLESHOOTING.docx
```

Versi PDF menggunakan nama yang sama dengan ekstensi `.pdf`.
