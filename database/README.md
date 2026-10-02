# Database MIN SIX

Database aplikasi menggunakan **SQL dump**, bukan CodeIgniter Migration/Seeder.

Canonical schema:

`database/minsix.sql`

## PHASE 7

PHASE 7 menambahkan:
- `instagram_posts` sebagai local cache API + fallback manual;
- `instagram_source_mode` (`HYBRID` / `MANUAL`);
- `instagram_display_count` (6–8 item).

Database lokal yang sudah lulus PHASE 6 **jangan di-reset**. Jalankan hanya:

`database/upgrades/20261002_phase7a_instagram.sql`

Batch 7B tidak menambah tabel baru.

### Credential API

Credential **tidak disimpan di database/repository**. Atur pada file lokal `.env`:

```ini
instagram.apiBaseUrl = 'https://graph.instagram.com'
instagram.apiVersion = 'vXX.X'   # opsional
instagram.userId = '...'
instagram.accessToken = '...'
```

Gunakan API version yang valid bila konfigurasi aplikasi Meta Anda memerlukannya. Jika endpoint Instagram Login yang digunakan tidak memakai version segment, biarkan `instagram.apiVersion` kosong.

### Sinkronisasi

Manual dari CMS Admin: `/manager/instagram-settings`

CLI / cron:

```bash
php spark instagram:sync
```

Jika fetch gagal, cache API lama tidak dihapus. Jika cache tidak tersedia, homepage memakai fallback MANUAL. Mode MANUAL tidak melakukan fetch API.

Untuk instalasi baru dari database kosong, import `database/minsix.sql`.

Jangan commit credential, plaintext password, token, atau dump production yang berisi data sensitif.
