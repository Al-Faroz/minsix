# 09 — PHASE 9 RUNTIME & VISUAL AUDIT CHECKLIST

Dokumen ini dipakai setelah source PHASE 9A–9C selesai.

## 1. Prinsip Audit

Prioritas:
1. fungsi tidak error;
2. role sesuai;
3. feature toggle bekerja;
4. layout tidak overflow;
5. visual publik mengikuti sistem Rival;
6. mobile bukan sekadar desktop yang diperkecil.

Referensi visual utama:
- Rival — BootstrapMade
- https://bootstrapmade.com/demo/Rival/

Yang dibandingkan:
- typography hierarchy;
- spacing;
- dark/light rhythm;
- hero composition;
- header/nav;
- numbered sections;
- editorial image grid;
- card treatment;
- button treatment;
- responsive behavior;
- motion/reveal ringan.

Asset Rival tidak disalin.

---

## 2. Viewport Audit

Minimal cek:

```text
Desktop Wide   1440 x 900
Desktop        1366 x 768
Laptop         1280 x 720
Tablet         768 x 1024
Mobile Large   430 x 932
Mobile         390 x 844
Mobile Small   360 x 800
```

Tidak boleh ada horizontal overflow selain carousel Instagram yang memang disengaja.

---

## 3. Homepage

Cek:
- sticky header;
- logo/nama;
- nav active;
- SPMB CTA;
- mobile menu;
- hero hierarchy;
- hero image crop;
- CTA;
- stats;
- About;
- Keseharian;
- Program numbered grid;
- Prestasi;
- Cerita/Galeri;
- Kabar/Agenda;
- Instagram;
- Kepala Madrasah;
- SPMB CTA;
- Kontak;
- footer.

Visual checkpoint:
- heading tidak terlalu besar hingga mematahkan layout;
- whitespace terasa seperti Rival;
- dark section tidak terlalu hijau;
- accent lime hanya sebagai aksen;
- radius tidak berlebihan;
- foto dominan, bukan dekorasi card.

---

## 4. Public Pages

Cek:

```text
/
profil
program
gtk
kabar
spmb
sitemap.xml
robots.txt
```

Detail:

```text
kabar/berita/{slug}
kabar/prestasi/{slug}
kabar/agenda/{slug}
kabar/galeri/{slug}
```

Validasi:
- 404 bila slug tidak ada;
- DRAFT tidak tampil;
- metadata SEO ada;
- foto tidak overflow;
- detail Galeri lightbox bekerja;
- SPMB hanya Current + Published.

---

## 5. Feature Toggle

Kabar OFF:
- nav hilang;
- homepage section Kabar/Prestasi/Galeri terkait tidak tampil;
- /kabar -> 404;
- detail Kabar -> 404;
- data CMS tetap ada.

Subfeature OFF:
- section terkait hilang;
- detail route terkait -> 404.

Instagram OFF:
- carousel hilang;
- data cache/manual tetap ada.

SPMB OFF:
- nav CTA hilang;
- homepage CTA hilang;
- /spmb -> 404;
- data periode tetap ada.

---

## 6. Admin / Operator

ADMIN:
- seluruh konten;
- Settings;
- Feature;
- SEO;
- Instagram Settings/Sync.

OPERATOR:
- Beranda;
- Profil;
- Program;
- GTK;
- Berita;
- Agenda;
- Prestasi;
- Galeri;
- SPMB;
- Media;
- Instagram Content.

OPERATOR tidak boleh membuka:
- /manager/settings
- /manager/features
- /manager/seo
- /manager/instagram-settings

---

## 7. Media

Tes:
- JPG;
- PNG;
- WebP;
- PDF;
- file > 8 MB ditolak;
- ekstensi selain allowlist ditolak;
- gambar ekstrem > 40 MP ditolak;
- alt text tersimpan;
- media yang masih dipakai tidak dapat dihapus.

Jika GD tersedia:
- foto besar ter-resize maksimum sisi 2400 px.

---

## 8. Instagram

MANUAL:
- tambah 6–8 item;
- edit;
- visible ON/OFF;
- urutan;
- carousel;
- permalink.

HYBRID:
- cache API menjadi prioritas;
- API gagal tidak menghapus cache;
- cache kosong memakai MANUAL.

Mobile:
- swipe horizontal;
- tidak menyebabkan body horizontal overflow.

---

## 9. SEO

Cek source halaman:
- title;
- meta description;
- canonical;
- og:title;
- og:description;
- og:url;
- og:image bila tersedia;
- twitter card;
- EducationalOrganization JSON-LD.

Berita:
- Article JSON-LD.

Sitemap:
- hanya Published;
- mengikuti feature toggle.

Development:
- Canonical Base URL dikosongkan.

---

## 10. Security Runtime

Cek:
- CSRF form Manager;
- throttling login;
- user inactive kehilangan akses;
- Operator ditolak dari Admin route;
- /app, /database, /docs, /.env tidak dapat diakses HTTP;
- uploads tidak menjalankan PHP/script;
- Manager mengirim no-store/noindex.

Production nanti:
- CI_ENVIRONMENT=production;
- HTTPS;
- secure cookie;
- canonical domain final;
- cron Instagram bila API digunakan.

---

## 11. PASS Rule

Satu kelompok hanya diberi PASS setelah:
- tidak ada fatal/database error;
- fungsi utama berjalan;
- desktop + mobile diperiksa;
- visual defect penting dibereskan.

Urutan audit runtime yang disarankan:

```text
1. Auth / Role
2. Settings / Feature / SEO
3. Media
4. Homepage CMS
5. Profil
6. Program
7. GTK
8. Kabar
9. SPMB
10. Instagram
11. Homepage publik
12. Public inner pages
13. Responsive
14. SEO / sitemap / robots
15. 404 / security route
```
