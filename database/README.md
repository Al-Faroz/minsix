# Database MIN SIX

Database aplikasi menggunakan SQL dump, bukan CodeIgniter Migration/Seeder.

Canonical schema:
`database/minsix.sql`

PHASE 1:
- `app_users`
- `audit_logs`

Import ke database lokal `minsix`, lalu buat Admin pertama:

`php spark minsix:user:create admin "Administrator" ADMIN`

Jika ada upgrade schema production, simpan SQL upgrade terpisah pada:
`database/upgrades/YYYYMMDD_description.sql`

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
