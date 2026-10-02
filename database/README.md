# Database MIN SIX

Database proyek menggunakan SQL dump, bukan CodeIgniter Migration/Seeder.

Canonical dump yang akan dibuat saat schema diimplementasikan:

`database/minsix.sql`

Jika ada upgrade schema production yang perlu dijalankan terpisah:

`database/upgrades/YYYYMMDD_description.sql`

Jangan commit credential atau dump production yang mengandung data/rahasia.
