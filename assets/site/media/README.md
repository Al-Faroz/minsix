# Media Website MIN 6 JEMBER

Folder ini menjadi catatan inventaris aset visual website. Foto dokumentasi asli yang akan dipakai pada halaman publik dikelola melalui **CMS > Media Library**, bukan disimpan sebagai file statis di folder source code.

## Aset brand yang sudah tersedia

Lihat `assets/brand/`:
- `min6-logo.png` — logo utama.
- `min6-logo-white.png` — logo putih untuk latar gelap.
- `favicon-32x32.png` — favicon.
- `/favicon.ico` — favicon format ICO di root.

Logo utama sudah dipakai pada header frontend, sidebar CMS, dan apple-touch reference. Logo putih sudah dipakai pada footer frontend.

## Foto dokumentasi yang dibutuhkan

Foto asli perlu disediakan oleh madrasah/pengelola konten. Jangan mengisi kategori berikut dengan foto stok atau gambar generatif yang seolah-olah dokumentasi resmi.

| Area | Kebutuhan awal | Rasio |
|---|---:|---|
| Hero homepage | 4–6 foto pilihan | 16:9 / 3:2 |
| Gedung madrasah | 3 foto | 3:2 |
| Halaman/lapangan | 2–3 foto | 3:2 |
| Kegiatan pembelajaran | beberapa foto | 3:2 |
| Sambut pagi dan upacara | dokumentasi kegiatan | 3:2 |
| Kegiatan keagamaan, Yanbu'a, tahfidz/tilawah | dokumentasi kegiatan | 3:2 |
| Karya siswa dan ekstrakurikuler | dokumentasi kegiatan | 3:2 |
| Kepala madrasah dan GTK | foto tiap personel yang disetujui | 4:5 |
| Prestasi | dokumentasi penerimaan/pencapaian | 4:5 / 3:2 |
| Galeri dan SPMB | dokumentasi relevan | 3:2 |

## Standar pengelolaan

- Upload foto melalui Media Library agar file otomatis dioptimalkan dan metadata tersimpan di tabel `media`.
- Isi alt text yang menggambarkan isi foto secara singkat.
- Pastikan izin penggunaan foto siswa/personel telah diperoleh sesuai kebijakan madrasah.
- Hindari mengunggah foto yang sama berulang kali.
- Jangan menghapus file yang masih direferensikan konten website.

## Data demo SPMB

Data SPMB 2027/2028 disediakan terpisah di `database/seeds/spmb_demo_2027_2028.sql`. Data tersebut sengaja tidak mengaitkan QR/brosur media karena belum ada file resmi yang diberikan.
