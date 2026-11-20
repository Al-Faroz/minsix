# Pengisian Konten Awal Website MIN 6 Jember

## Tujuan

Perintah `php spark minsix:content:demo:install` mengisi pengaturan website berdasarkan informasi resmi yang tersedia dan menambahkan tiga artikel referensi sebagai DRAFT. Perintah ini bersifat idempotent untuk berita (slug yang sudah ada tidak dibuat ulang) dan hanya mengganti nilai pengaturan yang kosong atau placeholder bawaan yang diketahui.

## Data pengaturan yang digunakan

| Field | Nilai |
|---|---|
| Nama website | MIN 6 Jember |
| Tagline | Madrasah Maju, Bermutu, Mendunia |
| Alamat | Jl. Urip Sumoharjo Gg. Legog, Tanggul Wetan, Kecamatan Tanggul, Kabupaten Jember, Jawa Timur |
| Telepon | 082330634295 |
| Email | mintanggulwetan@gmail.com |

WhatsApp, Instagram, dan Google Maps tidak diisi karena kanal/tautan tersebut belum diverifikasi. Nilai operator yang sudah terisi dipertahankan, kecuali placeholder bawaan yang diketahui.

## Artikel yang ditambahkan

Semua artikel berstatus `DRAFT`, `published_at = NULL`, dan tidak tampil pada halaman publik sebelum diperiksa serta diterbitkan oleh operator.

1. Transformasi Digital GTK Madrasah melalui Google Workspace — Kementerian Agama, 24 September 2026.
2. Studi Pembelajaran Digital Menyoroti Peluang Peningkatan Hasil Belajar — Kementerian Agama, 12 November 2026. Angka studi dijelaskan sebagai temuan kelompok penelitian, bukan jaminan hasil di MIN 6 Jember.
3. Penguatan Karakter, Literasi, dan Numerasi untuk Generasi Emas 2045 — Kemendikdasmen, 20 Maret 2025.

Setiap artikel mencantumkan tautan sumber primer dan catatan bahwa artikel merupakan ringkasan sumber eksternal, bukan laporan kegiatan MIN 6 Jember.

## Foto dan lisensi

Foto ilustrasi diunduh saat perintah dijalankan dari sumber Pexels berikut:

- Thirdman, foto siswa membaca di perpustakaan: https://www.pexels.com/photo/students-reading-books-in-a-library-8926840/
- Ron Lach, foto siswa menggunakan komputer: https://www.pexels.com/photo/students-using-computers-in-their-class-10638075/
- Ketentuan lisensi: https://www.pexels.com/license/

Foto disimpan di `public/uploads/news-illustrations/` dan didaftarkan ke Media Library. Alt text dan caption menyatakan dengan jelas bahwa foto adalah ilustrasi dan bukan dokumentasi MIN 6 Jember. Kredit fotografer dan tautan sumber disimpan di caption media.

Pexels mengizinkan penggunaan foto gratis pada website; atribusi tidak diwajibkan, tetapi tetap dicatat untuk transparansi. Foto tidak boleh digunakan untuk menyiratkan dukungan/endorsement orang yang tampil.

## Cara menjalankan

Dari root project setelah pull:

```bash
php spark minsix:content:demo:install
```

Pastikan komputer/server memiliki koneksi internet dan ekstensi PHP cURL (disarankan) atau `allow_url_fopen` aktif agar foto dapat diunduh. Perintah memvalidasi MIME dan dimensi gambar sebelum menyimpan file.

Setelah berhasil:
1. Buka Manager → Pengaturan Website dan periksa data.
2. Buka Manager → Media Library dan pastikan dua foto ilustrasi masuk.
3. Buka Manager → Berita, periksa tiga artikel DRAFT, tautan sumber, foto, ringkasan, dan isi.
4. Jangan terbitkan artikel sebelum editor madrasah menyetujui redaksi dan kesesuaiannya.

## Catatan sumber data sekolah

Nama, tagline, kontak, dan alamat merujuk pada situs resmi MIN 6 Jember. NPSN 60715787 dan identitas satuan pendidikan dapat diverifikasi pada Referensi Data Kemendikdasmen. NPSN dan nama kepala madrasah tidak dimasukkan karena halaman Pengaturan Website saat ini tidak menyediakan field khusus untuk data tersebut.
