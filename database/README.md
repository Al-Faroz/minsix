# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 6

Tabel aktif:
- `app_users`
- `audit_logs`
- `site_settings`
- `site_features`
- `media`
- `homepage_sections`
- `profile_sections`
- `programs`
- `gtk`
- `gtk_roles`
- `gtk_role_assignments`
- `news`
- `events`
- `achievements`
- `galleries`
- `gallery_items`
- `spmb_periods`
- `spmb_requirements`
- `spmb_faq`

Database lokal yang sudah lulus PHASE 5 **jangan di-reset**. Jalankan hanya:

`database/upgrades/20261002_phase6a_homepage.sql`

Upgrade PHASE 6 menambahkan `homepage_sections`. Batch 6B–6D tidak menambah tabel baru.

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Frontend publik membaca:
- `homepage_sections` untuk copy dan foto homepage;
- `site_features` untuk visibility Kabar, submodul, Instagram, dan SPMB;
- konten berstatus `PUBLISHED` untuk Program/Kabar/SPMB;
- `site_settings` untuk identitas dan kontak.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
