# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 2

Tabel aktif:
- `app_users`
- `audit_logs`
- `site_settings`
- `site_features`
- `media`

Karena database lokal PHASE 1 sudah berisi akun Admin, **jangan import ulang canonical dump** jika ingin mempertahankan akun/data. Jalankan upgrade:

`database/upgrades/20261002_phase2_settings_features_media.sql`

Untuk instalasi baru dari database kosong, cukup import `database/minsix.sql`.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
