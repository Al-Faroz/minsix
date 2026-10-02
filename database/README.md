# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 5

Tabel aktif:
- `app_users`
- `audit_logs`
- `site_settings`
- `site_features`
- `media`
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

Database lokal yang sudah lulus PHASE 4 **jangan di-reset**. Jalankan hanya:

`database/upgrades/20261002_phase5_spmb.sql`

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Aturan SPMB:
- maksimal satu periode `is_current = 1` dijaga pada level aplikasi;
- persyaratan dan FAQ mengikuti periode;
- menghapus periode non-current menghapus persyaratan dan FAQ melalui foreign key cascade;
- QR dan brosur tetap memakai Media Library;
- feature SPMB OFF tidak menghapus data.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
