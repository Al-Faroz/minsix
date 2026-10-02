# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 8

Jika database lokal sudah lulus PHASE 7, jalankan hanya:

`database/upgrades/20261002_phase8a_seo.sql`

Upgrade PHASE 8 menambahkan SEO per Prestasi:
- `meta_title`
- `meta_description`
- `og_media_id`

Serta setting global:
- `seo_default_title`
- `seo_default_description`
- `seo_default_og_media_id`
- `seo_canonical_base_url`

Batch 8C–8D tidak menambah schema.

Frontend PHASE 8 menyediakan:
- title/meta description;
- canonical;
- Open Graph;
- Twitter Card;
- EducationalOrganization JSON-LD;
- Article JSON-LD untuk Berita;
- `/sitemap.xml` dinamis mengikuti feature toggle dan konten published.

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.


## FINAL-D2 — Structured SPMB

Untuk database existing yang sudah memiliki PHASE 8, jalankan:

`database/upgrades/20261002_finald2_spmb_structured.sql`

Menambahkan:
- `spmb_steps` untuk Alur Pendaftaran;
- `spmb_highlights` untuk Program Unggulan.

Keduanya mengikuti periode SPMB dan otomatis terhapus ketika periodenya dihapus.


## PHASE 10 — Branding Uppercase

Untuk database existing, jalankan:

`database/upgrades/20261002_phase10_brand_uppercase.sql`

Upgrade ini hanya mengganti teks exact `MIN 6 Jember` menjadi `MIN 6 JEMBER` pada setting dan konten. Tidak mengubah schema.

## PHASE 10C — Instagram Login + Token Lifecycle + Carousel

Untuk database existing, jalankan berurutan:

`database/upgrades/20261002_phase10c_integration_settings.sql`

`database/upgrades/20261002_phase10c2_instagram_login.sql`

Upgrade pertama menambahkan tabel generik `integration_settings`. Upgrade kedua menambahkan `instagram_posts.children_json` untuk cache child media `CAROUSEL_ALBUM`.

Autentikasi sekarang **Instagram Login only**. User ID dan Access Token tidak diinput manual. App Secret dan token OAuth disimpan terenkripsi; siapkan `encryption.key` terlebih dahulu:

```bash
php spark minsix:key:generate
```

Setelah upgrade, buka CMS > Pengaturan Instagram, simpan Meta App ID/App Secret, daftarkan OAuth Redirect URI pada Meta Developer, lalu klik **Hubungkan Instagram**. Token manual/ENV lama tidak dianggap sebagai koneksi Login baru.
