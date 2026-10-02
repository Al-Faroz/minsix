# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 4

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

Database lokal yang sudah lulus PHASE 3 **jangan di-reset**. Jalankan hanya:

`database/upgrades/20261002_phase4_kabar.sql`

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Kabar/Berita dapat dimatikan dari Pengaturan Fitur tanpa menghapus data CMS.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
