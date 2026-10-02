# 06 — MEDIA, INSTAGRAM & SEO WEBSITE MIN 6 JEMBER

## 1. Media Strategy

Website bersifat image-driven, sehingga pengelolaan gambar menjadi komponen penting.

Tujuan:
- kualitas visual baik;
- ukuran file terkontrol;
- media dapat dipakai ulang;
- tidak meng-upload duplikasi;
- alt text tersedia;
- aman dari file berbahaya.

---

## 2. Lokasi Upload

Direkomendasikan:

```text
uploads/
└── YYYY/MM/
    └── file-acak.ext
```

Gunakan random/stored filename, bukan nama asli sebagai filename final.

---

## 3. Upload Validation

Validasi:
- MIME;
- extension;
- ukuran file;
- image dimension;
- jenis media;
- filename.

Tolak:
- PHP;
- executable/script;
- file dengan double extension berbahaya;
- SVG sampai ada sanitization policy yang jelas.

Tambahkan proteksi server pada `uploads/` agar script tidak dieksekusi. Pada struktur MIN SIX, isi folder bawaan `public/` telah dipindahkan ke root.

---

## 4. Image Optimization

Saat upload gambar:
1. validasi extension + MIME + gambar nyata;
2. tolak nama file dengan pola executable/double-extension berbahaya;
3. batasi resolusi ekstrem untuk mencegah pixel bomb;
4. simpan menggunakan random filename;
5. jika GD tersedia, orientasi JPEG diperbaiki lalu gambar di-resize maksimal 2400 px dan di-reencode agar ukuran/metadata kamera berkurang;
6. simpan metadata hasil akhir ke tabel `media`;
7. WebP derivative terpisah dapat ditambahkan kemudian bila kebutuhan frontend memerlukan variant khusus.

Gambar kamera berukuran multi-megabyte tidak boleh langsung dikirim apa adanya ke visitor.

---

## 5. Rasio Media

```text
Hero        16:9 / 3:2
Content     3:2
Card        4:3
Portrait    4:5
Instagram   1:1 / 4:5
OG Image    1.91:1
```

Crop harus memperhatikan posisi subjek.

---

## 6. Media Library

Field utama:
- preview;
- original name;
- stored path;
- type;
- file size;
- width/height;
- alt text;
- caption;
- created at.

Media dapat direuse.

Delete media harus memeriksa reference.

---

## 7. Instagram Resmi

Akun resmi:

```text
@min6jember
https://www.instagram.com/min6jember
```

Integrasi menggunakan pola **Hybrid**.

---

## 8. Instagram Hybrid Flow

```text
Source Instagram/API
        │
        ▼
Fetch via scheduled process
        │
        ▼
Store/update instagram_posts
        │
        ▼
Homepage membaca LOCAL CACHE
        │
        ├── jika cache tersedia -> tampilkan
        │
        └── jika tidak -> fallback manual
```

Homepage tidak melakukan fetch langsung ke Instagram pada setiap request visitor.

---

## 9. Fetch Frequency

Rekomendasi:
- 1–6 jam sekali;
- tidak perlu real-time.

Dapat dijalankan melalui:
- cron hosting;
- CLI command CI4;
- scheduler hosting jika tersedia.

Jika fetch gagal:
- jangan kosongkan carousel;
- gunakan cache terakhir;
- jika cache tidak ada gunakan fallback manual.

---

## 10. Source Mode

Admin dapat mengatur:

```text
AUTO/HYBRID
MANUAL
```

AUTO/HYBRID:
- coba source Instagram;
- fallback cache/manual.

MANUAL:
- hanya data manual.

---

## 11. Credential Instagram

Token/credential:
- tidak boleh masuk repository;
- hanya Admin yang dapat membuka konfigurasi API;
- Base URL, API Version, dan User ID dapat diisi dari CMS;
- Access Token dapat diisi dari CMS tetapi disimpan sebagai ciphertext Base64 di `integration_settings`;
- encryption/decryption menggunakan Encryption Service CodeIgniter dan `encryption.key` dari `.env`;
- token tersimpan tidak pernah ditampilkan kembali ke browser;
- token `.env` lama tetap didukung sebagai fallback;
- Base URL dibatasi ke `graph.instagram.com` dan `graph.facebook.com` agar token tidak dikirim ke host sembarangan;
- tersedia Tes Koneksi tanpa menulis cache dan Sinkronkan Sekarang untuk memperbarui cache.

Generate encryption key dengan:

```bash
php spark minsix:key:generate
```

Salin hasilnya ke `.env`. Jangan commit key tersebut.

---

## 12. Instagram Fallback

Operator dapat mengelola:
- gambar;
- permalink;
- caption pendek;
- tanggal;
- urutan;
- visible.

Fallback harus tetap terlihat seperti bagian website, bukan embed mentah.

---

## 13. Homepage Carousel

Baseline:
- 6–8 item;
- desktop 4 visible;
- tablet 2–3;
- mobile 1–2;
- Swiper atau library ringan;
- lazy load;
- klik menuju Instagram.

---

## 14. SEO Global

Admin mengatur:
- default site title;
- default meta description;
- default OG image;
- canonical base URL;
- social account.

Pola title:

```text
{Page Title} | MIN 6 JEMBER
```

---

## 15. SEO Per Konten

Berita/Prestasi dapat memiliki:
- slug;
- meta title;
- meta description;
- OG image.

Jika kosong:
- meta title dari title;
- meta description dari summary;
- OG image dari primary image/default.

Operator boleh mengedit SEO per konten karena merupakan metadata konten, bukan konfigurasi sistem.

---

## 16. URL

Gunakan slug bersih:

```text
/kabar/berita/judul-berita
/kabar/prestasi/juara-...
```

Hindari query URL untuk detail publik jika tidak perlu.

---

## 17. Sitemap

Sediakan:

```text
/sitemap.xml
```

Berisi halaman publik aktif dan konten published.

Jika Kabar OFF, URL Kabar tidak dimasukkan.

Jika SPMB OFF, `/spmb` tidak dimasukkan.

---

## 18. Robots

Sediakan:

```text
/robots.txt
```

Manager:

```text
Disallow: /manager/
```

Robots.txt bukan mekanisme keamanan; manager tetap wajib login.

---

## 19. Open Graph

Minimal:

```text
og:title
og:description
og:image
og:url
og:type
```

Twitter card dapat mengikuti metadata yang sama bila diperlukan.

---

## 20. Structured Data

Jika implementasi memungkinkan:
- Organization/EducationalOrganization;
- BreadcrumbList;
- Article untuk berita.

Jangan memasukkan data yang belum tervalidasi.

---

## 21. Alt Text

Setiap foto utama sebaiknya mempunyai alt text yang singkat dan deskriptif.

Contoh:
> Siswa MIN 6 JEMBER mengikuti pembelajaran di kelas.

---

## 22. Privacy dan Third Party

Embed eksternal hanya jika memberi manfaat nyata.

Instagram:
- carousel berbasis cache lokal lebih disukai daripada banyak embed mentah.

Google Maps:
- dapat menggunakan link atau embed;
- lazy-load bila embed digunakan.

---

## 23. Media Retention

Jangan menghapus asset yang masih direferensikan.

Jika media diganti:
- record baru dapat dipilih;
- file lama dibersihkan setelah reference audit.

---

## 24. Backup

Backup harus mencakup:
- database;
- `uploads/`.

Database tanpa uploads tidak cukup untuk memulihkan website visual.
