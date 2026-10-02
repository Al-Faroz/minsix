# 07 — DEPLOYMENT & SECURITY WEBSITE MIN 6 JEMBER

## 1. Environment

Development:

```text
G:\xampp\htdocs\minsix
```

Repository:

```text
https://github.com/Al-Faroz/minsix
```

Framework:
```text
CodeIgniter 4
```

Database:
```text
MySQL/MariaDB
```

---

## 2. Struktur CI4

MIN SIX menggunakan CI4 standar untuk `app/`, tetapi **isi folder bawaan `public/` ditempatkan di root proyek** sesuai pola localhost/hosting yang dipilih.

```text
app/
├── Config/
├── Controllers/
│   ├── Frontend/
│   └── Manager/
├── Filters/
├── Models/
├── Services/
├── Views/
│   ├── frontend/
│   └── manager/
└── ...

assets/
uploads/
index.php
.htaccess

database/
└── minsix.sql

docs/
└── dokumen acuan
```

Tidak perlu HMVC untuk versi awal.

---

## 3. Web Root

Web root proyek adalah root repository/folder `minsix`, bukan `public/`.

Konsekuensi keamanan:
- root `.htaccess` wajib memblokir akses HTTP langsung ke `app/`, `vendor/`, `writable/`, `tests/`, `docs/`, `database/`, `.env`, Composer files, dan file internal lain;
- `uploads/.htaccess` wajib mencegah eksekusi script;
- perubahan struktur deployment tidak boleh dilakukan tanpa audit ulang rule proteksi.

---

## 4. `.env`

Development:

```ini
CI_ENVIRONMENT = development
```

Production:

```ini
CI_ENVIRONMENT = production
```

`.env` tidak boleh commit.

Rahasia yang ada di `.env` antara lain:
- database;
- encryption key;
- token;
- API credential.

---

## 5. Base URL

Development:

```text
http://localhost/minsix/
```

Production diatur sesuai domain final.

Semua link internal menggunakan helper/route CI4, bukan hardcode domain production.

---

## 6. Database Deployment

Tidak menggunakan Migration/Seeder.

Setup awal:
1. buat database;
2. import `database/minsix.sql`;
3. isi `.env`;
4. buat akun Admin secara aman;
5. uji login.

Upgrade schema:
1. backup production;
2. jalankan SQL upgrade;
3. update `database/minsix.sql` canonical;
4. verifikasi.

Jika perubahan signifikan, upgrade SQL dapat disimpan:

```text
database/upgrades/YYYYMMDD_description.sql
```

Canonical dump tetap `database/minsix.sql`.

---

## 7. Authentication Security

Wajib:
- `password_hash`;
- `password_verify`;
- session regenerate setelah login;
- CSRF;
- role filter;
- login throttling/rate limiting;
- generic login error;
- inactive user blocked.

---

## 8. Authorization

Security tidak boleh bergantung pada tampilan menu.

Semua endpoint Manager:
- auth;
- role check sesuai kebutuhan.

Admin-only:
- settings;
- features;
- users;
- global SEO;
- API config.

---

## 9. CSRF

Aktifkan CSRF pada form Manager.

AJAX menggunakan token sesuai mekanisme CI4.

---

## 10. Output Escaping

Default:
- escape output teks;
- rich-text content hanya dirender setelah policy/sanitization yang jelas.

Jangan mencetak input user mentah ke HTML.

---

## 11. Rich Text

Editor hanya untuk konten yang membutuhkan format.

Allowed:
- paragraph;
- heading terbatas;
- bold/italic;
- list;
- link;
- image dari media manager bila didukung.

Hindari arbitrary script/style.

---

## 12. Upload Security

- MIME check;
- extension allowlist;
- random filename;
- max size;
- no executable;
- no direct script execution pada uploads;
- image validation.

PDF diperbolehkan untuk brosur/dokumen SPMB.

---

## 13. HTTPS

Production wajib HTTPS.

Cookie:
- Secure;
- HttpOnly;
- SameSite sesuai kebutuhan.

---

## 14. Security Headers

Pertimbangkan:
- X-Content-Type-Options;
- Referrer-Policy;
- frame policy;
- Content-Security-Policy setelah asset flow stabil.

CSP jangan dipasang terlalu ketat sebelum integrasi eksternal dipetakan.

---

## 15. Error Handling

Production:

```text
CI_ENVIRONMENT = production
```

Error detail tidak ditampilkan ke visitor.

Log error disimpan server sesuai konfigurasi CI4.

---

## 16. Caching

Prioritas:
- homepage query/fragment cache jika diperlukan;
- setting cache;
- feature cache;
- Instagram cache lokal;
- browser cache static assets.

Jangan cache halaman manager secara agresif.

---

## 17. Performance

- optimized image;
- WebP;
- lazy load;
- minified production assets;
- defer non-critical JS;
- sedikit dependency;
- pagination query;
- DB index;
- hindari N+1 query.

---

## 18. Backup

Sebelum update production:
- database backup;
- uploads backup jika ada operasi media besar;
- commit code jelas.

---

## 19. Git Policy

Branch utama:

```text
main
```

Jangan commit:
- `.env`;
- database password;
- token;
- API secret;
- generated session;
- production logs;
- temporary uploads yang tidak perlu.

Commit message dibuat jelas per phase/batch.

---

## 20. Git + Database

`database/minsix.sql` boleh di-repo sebagai schema canonical selama:
- tidak mengandung credential;
- tidak mengandung password plaintext;
- tidak berisi data rahasia.

Production dump tidak boleh dimasukkan repository publik.

---

## 21. Deploy Checklist

### Sebelum deploy
- functional check;
- config production;
- DB backup;
- SQL upgrade reviewed;
- asset path valid;
- uploads writable;
- `.env` production siap.

### Setelah deploy
- homepage;
- navigation;
- login;
- role Admin;
- role Operator;
- feature ON/OFF;
- upload;
- berita;
- SPMB;
- Instagram fallback;
- sitemap;
- 404;
- mobile.

---

## 22. Security Checklist

```text
[ ] .env tidak di-repo
[ ] password di-hash
[ ] CSRF aktif
[ ] auth filter aktif
[ ] admin filter aktif
[ ] upload restricted
[ ] production error off
[ ] HTTPS aktif
[ ] rate limit login
[ ] no plaintext secret
[ ] feature endpoint admin-only
```

---

## 23. Compatibility

Website publik harus bekerja baik pada browser modern desktop dan mobile.

Manager desktop-first tetapi tetap responsive.

---

## 24. Out of Scope Awal

Tidak termasuk:
- e-learning;
- pembayaran;
- PPDB transactional penuh;
- login siswa/orang tua;
- absensi;
- nilai;
- page builder;
- multi-school;
- multi-language;
- complex workflow approval.

---

## 25. Final Principle

Website MIN 6 Jember adalah sistem publikasi institusi.

> Prioritas utama: **stabil, cepat, aman, mudah dikelola, dan terlihat modern.**

Backend dibuat sesederhana mungkin. Kompleksitas hanya ditambahkan jika memberi manfaat nyata.
