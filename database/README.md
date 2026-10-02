# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 3

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

Database lokal yang sudah lulus PHASE 2 **jangan di-reset**. Jalankan:

`database/upgrades/20261002_phase3_profile_program_gtk.sql`

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Setelah upgrade, menu Profil, Program, GTK, dan Jabatan GTK dapat digunakan.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
