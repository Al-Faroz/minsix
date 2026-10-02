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
