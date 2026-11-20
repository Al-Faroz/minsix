# Media Website MIN 6 JEMBER

Folder ini menjadi catatan inventaris aset visual website. Foto dokumentasi asli yang akan dipakai pada halaman publik dikelola melalui **CMS > Media Library**, bukan disimpan sebagai file statis di folder source code.

## Identitas resmi

Gunakan aset yang sudah disetujui dan tersedia di `assets/brand/`:
- `min6-logo.png` — logo utama.
- `min6-logo-white.png` — varian putih untuk latar gelap.
- `favicon-32x32.png` dan `/favicon.ico` — favicon.

Jangan membuat atau memakai logo alternatif. Bentuk, warna, dan proporsi logo tidak boleh diubah.

## Prinsip foto dan ilustrasi

- Foto gedung, siswa, guru, kepala madrasah, fasilitas, prestasi, dan kegiatan harus berasal dari dokumentasi MIN 6 Jember yang memang diizinkan untuk dipublikasikan.
- Jangan memakai foto stok atau gambar generatif seolah-olah foto dokumentasi resmi.
- Foto ilustrasi eksternal hanya boleh dipakai untuk mendampingi artikel tematik yang relevan, dengan caption/alt yang menyebutkan bahwa itu ilustrasi serta kredit/sumber yang sesuai.
- Area yang belum memiliki foto memakai fallback grafis abstrak dari CSS. Fallback ini sengaja bukan foto dan tidak mengklaim sebagai dokumentasi madrasah.
- Jangan mengisi data statistik, jumlah siswa, jumlah GTK, atau prestasi tanpa data yang sudah diverifikasi.

## Inventaris foto yang dibutuhkan

| Halaman / modul | Materi dokumentasi yang disarankan | Target awal | Rasio |
|---|---|---:|---|
| Homepage hero | Foto landscape terbaik yang memperlihatkan lingkungan/aktivitas belajar | 4–6 pilihan | 16:9 atau 3:2 |
| Homepage mengenal madrasah | Gedung, halaman, suasana lingkungan | 2–3 | 3:2 |
| Homepage keseharian | Sambut pagi, upacara, pembiasaan, pembelajaran | 4–6 | 3:2 |
| Profil | Gedung, sejarah/arsip, visi-misi, fasilitas, lingkungan | 1–2 per section relevan | 3:2 |
| Program | Foto kegiatan yang benar-benar mewakili program terkait | 1–2 per program utama | 4:3 |
| GTK | Potret resmi tiap personel yang disetujui | 1 per personel | 4:5 |
| Kepala madrasah | Potret resmi kepala madrasah | 1–2 | 4:5 |
| Berita/agenda | Foto kegiatan sesuai isi berita/agenda | 1 cover per konten | 16:9 atau 3:2 |
| Prestasi | Foto penyerahan/peserta/piagam yang relevan | 1–3 per prestasi | 4:5 atau 3:2 |
| Galeri | Dokumentasi pilihan per album | 6–12 per album | 4:3 atau 3:2 |
| SPMB | Foto lingkungan, fasilitas, kegiatan penerimaan yang relevan | 3–5 | 16:9 atau 3:2 |

## Panduan pengambilan foto

1. Utamakan foto asli beresolusi cukup, fokus tajam, pencahayaan wajar, dan tidak terlalu banyak filter.
2. Ambil foto landscape untuk hero, profil, program, berita, dan galeri; portrait untuk GTK.
3. Sisakan ruang aman di tepi foto karena CMS/frontend dapat melakukan crop responsif.
4. Hindari teks penting, wajah, atau objek utama terlalu dekat tepi frame.
5. Untuk foto siswa/personel, pastikan izin publikasi sesuai kebijakan madrasah; hindari menampilkan data pribadi pada papan, layar, kartu identitas, atau dokumen.
6. Simpan file asli sebagai arsip internal sebelum mengunggah versi publik.

## Panduan kurasi dan pengisian CMS

- Upload melalui Media Library agar file dioptimalkan dan metadata tersimpan di tabel `media`.
- Isi alt text yang mendeskripsikan isi visual secara ringkas; jangan menjejalkan kata kunci.
- Caption dipakai untuk konteks, tanggal, lokasi, kredit fotografer, atau keterangan ilustrasi.
- Hubungkan media ke konten yang tepat; satu media dapat digunakan ulang jika konteksnya memang sesuai.
- Hindari mengunggah foto yang sama berulang kali.
- Jangan menghapus file yang masih direferensikan konten website.

### Contoh alt text

- Tepat: `Siswa mengikuti pembelajaran kelompok di ruang kelas MIN 6 Jember.`
- Tepat: `Guru mendampingi siswa membaca Al-Qur'an.`
- Tidak tepat: `IMG_20261120_001.jpg`
- Tidak tepat: `Foto terbaik sekolah nomor satu di Jember.` (klaim promosi yang tidak menggambarkan isi foto)

## Optimasi dan keamanan

- Ikuti batas MIME, ekstensi, ukuran, dimensi, dan resolusi yang diterapkan Media Library.
- Jangan mengunggah SVG atau file aktif ke Media Library.
- Foto kamera berukuran besar sebaiknya diunggah melalui Media Library agar diproses sebelum dipakai publik.
- Pastikan backup mencakup database dan folder `uploads/`.

## Ilustrasi artikel

Dua foto Pexels yang dipakai sebagai ilustrasi artikel awal dicatat pada `docs/10_INITIAL_EDITORIAL_CONTENT.md`. Keduanya bukan dokumentasi MIN 6 Jember. Jangan menggunakannya untuk hero madrasah, profil GTK, prestasi, galeri, atau SPMB seolah-olah foto internal.

## Status kebutuhan

Aset logo resmi tersedia. Ilustrasi grafis SPMB tersedia sebagai visual pendukung dan diberi label ilustrasi. Foto dokumentasi madrasah untuk daftar halaman di atas masih harus dipilih/diunggah oleh pengelola konten.