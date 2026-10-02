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

Integrasi publik tetap memakai pola **Hybrid Cache**, tetapi autentikasi API dikunci menjadi **Instagram Login only**.

---

## 8. Instagram Hybrid Flow

```text
Instagram Login (Admin)
        │
        ▼
Long-lived token terenkripsi
        │
        ▼
Scheduled sync + token lifecycle
        │
        ▼
Fetch media + carousel children
        │
        ▼
Store/update instagram_posts
        │
        ▼
Homepage membaca LOCAL CACHE
        │
        ├── jika cache tersedia -> tampilkan
        └── jika tidak -> fallback manual
```

Homepage tidak melakukan request langsung ke Instagram pada setiap kunjungan visitor.

---

## 9. Fetch Frequency & Token Lifecycle

Rekomendasi sync:
- setiap 1–6 jam melalui cron/scheduler hosting;
- command: `php spark instagram:sync`.

Setiap sync:
1. memeriksa status koneksi;
2. mencoba refresh token bila sudah memenuhi syarat refresh dan mendekati expiry;
3. mengambil media terbaru;
4. menyimpan/update cache tanpa menghapus cache lama jika API gagal.

Long-lived token disimpan dengan metadata:
- `token_issued_at`;
- `token_expires_at`;
- `last_refreshed_at`;
- `connection_status`.

Jika token benar-benar expired atau tidak dapat dipakai lagi, Admin harus menjalankan **Hubungkan Instagram** ulang.

---

## 10. Source Mode

Admin dapat mengatur:

```text
HYBRID
MANUAL
```

HYBRID:
- homepage memakai cache hasil Instagram API;
- jika cache API tidak tersedia, fallback ke konten manual.

MANUAL:
- homepage hanya memakai konten manual;
- bila akun Instagram masih terhubung, scheduled sync tetap boleh memeriksa lifecycle token tetapi tidak mengambil media baru.

---

## 11. Instagram Login & Credential

Aturan final PHASE 10C:
- tidak ada input manual Instagram User ID;
- tidak ada input/paste Access Token;
- autentikasi dilakukan melalui tombol **Hubungkan Instagram**;
- scope minimum integrasi feed: `instagram_business_basic`;
- App ID dan App Secret berasal dari Meta Developer;
- App Secret dapat disimpan melalui CMS dan dienkripsi di `integration_settings`;
- Access Token hanya dibuat dari OAuth callback lalu ditukar menjadi long-lived token;
- Access Token disimpan terenkripsi dan tidak pernah dikirim kembali ke browser;
- `encryption.key` wajib tersedia sebelum App Secret/token disimpan;
- legacy `instagram.userId` dan `instagram.accessToken` tidak lagi dipakai;
- cache lama tidak dihapus saat akun diputus atau re-auth dibutuhkan.

Generate encryption key:

```bash
php spark minsix:key:generate
```

Daftarkan **OAuth Redirect URI** yang ditampilkan CMS secara persis pada konfigurasi Instagram API di Meta Developer.

---

## 12. Carousel Album Support

Untuk post bertipe `CAROUSEL_ALBUM`:
- sync meminta field `children`;
- child media dinormalisasi dan disimpan ke `instagram_posts.children_json`;
- bila nested field expansion tidak tersedia pada versi API tertentu, aplikasi mencoba endpoint `/{media-id}/children`;
- cover homepage memakai URL parent bila tersedia, atau child pertama yang memiliki media/thumbnail;
- kartu homepage menampilkan badge jumlah media;
- klik kartu tetap menuju permalink post Instagram.

Cache children disimpan agar homepage tidak perlu meminta detail carousel ke Instagram pada request visitor.

---

## 13. Instagram Fallback & Homepage

Operator tetap dapat mengelola fallback manual:
- gambar;
- permalink;
- caption pendek;
- tanggal;
- urutan;
- visible.

Baseline carousel homepage:
- 6–8 post;
- desktop 4 visible;
- tablet 2–3;
- mobile 1–2;
- lazy load;
- klik menuju Instagram.

Fallback harus tetap terlihat sebagai bagian website, bukan embed mentah.

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
