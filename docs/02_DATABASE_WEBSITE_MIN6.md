# 02 — DATABASE WEBSITE MIN 6 JEMBER

## 1. Prinsip Database

Database website MIN 6 Jember menggunakan **MySQL/MariaDB**.

Ketentuan:
- schema dikelola melalui **SQL dump**;
- **tidak menggunakan Migration**;
- **tidak menggunakan Seeder**;
- gunakan `utf8mb4`;
- gunakan InnoDB;
- foreign key digunakan untuk relasi yang jelas;
- index ditambahkan pada kolom pencarian/filter utama;
- data publik tidak dihapus hanya karena fitur dinonaktifkan.

File canonical:

```text
database/minsix.sql
```

SQL dump harus dapat digunakan untuk membangun database baru dari keadaan kosong.

---

## 2. Nama Database

Nama lokal yang disarankan:

```text
minsix
```

Contoh `.env` lokal:

```ini
database.default.hostname = localhost
database.default.database = minsix
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_unicode_ci
```

Credential production tidak boleh masuk repository.

---

## 3. Tabel Inti

```text
app_users
site_settings
site_features
homepage_sections
profile_sections
programs

gtk
gtk_roles
gtk_role_assignments

news
events
achievements
galleries
gallery_items

spmb_periods
spmb_requirements
spmb_faq

media
instagram_posts

audit_logs
```

---

## 4. `app_users`

```text
id                  BIGINT UNSIGNED PK AI
username            VARCHAR(100) UNIQUE NOT NULL
password_hash       VARCHAR(255) NOT NULL
name                VARCHAR(150) NOT NULL
role                ENUM('ADMIN','OPERATOR') NOT NULL
is_active           TINYINT(1) DEFAULT 1
last_login_at       DATETIME NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

Aturan:
- hanya ADMIN dan OPERATOR;
- password menggunakan `password_hash()`;
- tidak pernah menyimpan plaintext password.

---

## 5. `site_settings`

```text
id                  BIGINT UNSIGNED PK AI
setting_key         VARCHAR(100) UNIQUE NOT NULL
setting_value       LONGTEXT NULL
value_type          VARCHAR(30) DEFAULT 'string'
is_public           TINYINT(1) DEFAULT 0
updated_by          BIGINT UNSIGNED NULL
updated_at          DATETIME NULL
```

Contoh key:

```text
site_name
site_tagline
logo_media_id
favicon_media_id
address
phone
whatsapp
email
instagram_username
instagram_url
google_maps_embed
seo_default_title
seo_default_description
seo_default_og_media_id
seo_canonical_base_url
```

Hanya Admin mengubah setting global.

---

## 6. `site_features`

```text
id                  BIGINT UNSIGNED PK AI
feature_key         VARCHAR(100) UNIQUE NOT NULL
label               VARCHAR(150) NOT NULL
is_enabled          TINYINT(1) DEFAULT 1
show_in_nav         TINYINT(1) DEFAULT 1
show_on_home        TINYINT(1) DEFAULT 1
updated_by          BIGINT UNSIGNED NULL
updated_at          DATETIME NULL
```

Baseline:

```text
kabar
news
events
achievements
gallery
instagram
spmb
```

Jika `kabar.is_enabled = 0`:
- navbar Kabar Madrasah hilang;
- route publik Kabar dinonaktifkan;
- section publik tidak tampil;
- data tetap ada;
- CMS tetap dapat mengelola data.

Hanya Admin dapat mengubah `site_features`.

---

## 7. `homepage_sections`

```text
id                  BIGINT UNSIGNED PK AI
section_key         VARCHAR(100) UNIQUE NOT NULL
eyebrow             VARCHAR(200) NULL
title               VARCHAR(255) NULL
subtitle            TEXT NULL
body                LONGTEXT NULL
content_json        JSON NULL
primary_media_id    BIGINT UNSIGNED NULL
cta_label           VARCHAR(120) NULL
cta_url             VARCHAR(255) NULL
secondary_cta_label VARCHAR(120) NULL
secondary_cta_url   VARCHAR(255) NULL
display_order       INT DEFAULT 0
updated_by          BIGINT UNSIGNED NULL
updated_at          DATETIME NULL
```

Baseline:

```text
hero
stats
about
habits
program_intro
achievement_intro
story_intro
news_intro
instagram_intro
headmaster
spmb_cta
contact
```

Operator boleh mengubah isi. Visibility global tetap hak Admin.

---

## 8. `profile_sections`

```text
id                  BIGINT UNSIGNED PK AI
section_key         VARCHAR(100) UNIQUE NOT NULL
title               VARCHAR(255) NOT NULL
body                LONGTEXT NULL
content_json        JSON NULL
primary_media_id    BIGINT UNSIGNED NULL
display_order       INT DEFAULT 0
updated_by          BIGINT UNSIGNED NULL
updated_at          DATETIME NULL
```

Baseline:
`about`, `vision`, `history`, `timeline`, `headmaster_message`, `identity`, `location`.

---

## 9. `programs`

```text
id                  BIGINT UNSIGNED PK AI
slug                VARCHAR(180) UNIQUE NOT NULL
name                VARCHAR(200) NOT NULL
category            VARCHAR(100) NULL
summary             TEXT NULL
content             LONGTEXT NULL
primary_media_id    BIGINT UNSIGNED NULL
display_order       INT DEFAULT 0
status              ENUM('DRAFT','PUBLISHED') DEFAULT 'DRAFT'
published_at        DATETIME NULL
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

---

## 10. `gtk`

```text
id                  BIGINT UNSIGNED PK AI
name                VARCHAR(180) NOT NULL
front_title         VARCHAR(50) NULL
back_title          VARCHAR(120) NULL
photo_media_id      BIGINT UNSIGNED NULL
short_bio           TEXT NULL
display_order       INT DEFAULT 0
is_active           TINYINT(1) DEFAULT 1
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

Satu orang hanya satu master GTK.

---

## 11. `gtk_roles`

```text
id                  BIGINT UNSIGNED PK AI
role_key            VARCHAR(100) UNIQUE NOT NULL
role_name           VARCHAR(150) NOT NULL
category            ENUM('LEADERSHIP','CLASS_TEACHER','SUBJECT_TEACHER','STAFF') NOT NULL
display_order       INT DEFAULT 0
is_active           TINYINT(1) DEFAULT 1
```

---

## 12. `gtk_role_assignments`

```text
id                  BIGINT UNSIGNED PK AI
gtk_id              BIGINT UNSIGNED NOT NULL
gtk_role_id         BIGINT UNSIGNED NOT NULL
role_label_override VARCHAR(180) NULL
display_order       INT DEFAULT 0
created_at          DATETIME NULL
```

Constraint:
`UNIQUE(gtk_id, gtk_role_id)`.

---

## 13. `news`

```text
id                  BIGINT UNSIGNED PK AI
slug                VARCHAR(200) UNIQUE NOT NULL
title               VARCHAR(255) NOT NULL
summary             TEXT NULL
content             LONGTEXT NOT NULL
primary_media_id    BIGINT UNSIGNED NULL
status              ENUM('DRAFT','PUBLISHED') DEFAULT 'DRAFT'
published_at        DATETIME NULL
meta_title          VARCHAR(255) NULL
meta_description    VARCHAR(320) NULL
og_media_id         BIGINT UNSIGNED NULL
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

---

## 14. `events`

```text
id                  BIGINT UNSIGNED PK AI
slug                VARCHAR(200) UNIQUE NOT NULL
title               VARCHAR(255) NOT NULL
summary             TEXT NULL
description         LONGTEXT NULL
location            VARCHAR(255) NULL
start_at            DATETIME NOT NULL
end_at              DATETIME NULL
primary_media_id    BIGINT UNSIGNED NULL
status              ENUM('DRAFT','PUBLISHED') DEFAULT 'DRAFT'
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

---

## 15. `achievements`

```text
id                  BIGINT UNSIGNED PK AI
slug                VARCHAR(200) UNIQUE NOT NULL
title               VARCHAR(255) NOT NULL
participant_name    VARCHAR(255) NULL
field_name          VARCHAR(150) NULL
award               VARCHAR(150) NULL
level               VARCHAR(100) NULL
organizer           VARCHAR(255) NULL
achievement_date    DATE NULL
summary             TEXT NULL
content             LONGTEXT NULL
primary_media_id    BIGINT UNSIGNED NULL
status              ENUM('DRAFT','PUBLISHED') DEFAULT 'DRAFT'
published_at        DATETIME NULL
meta_title          VARCHAR(255) NULL
meta_description    VARCHAR(320) NULL
og_media_id         BIGINT UNSIGNED NULL
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

> Revisi PHASE 8: field SEO Prestasi ditambahkan agar sinkron dengan dokumen `06_MEDIA_INSTAGRAM_SEO_WEBSITE_MIN6.md`.

---

## 16. `galleries`

```text
id                  BIGINT UNSIGNED PK AI
slug                VARCHAR(200) UNIQUE NOT NULL
title               VARCHAR(255) NOT NULL
gallery_date        DATE NULL
description         TEXT NULL
cover_media_id      BIGINT UNSIGNED NULL
status              ENUM('DRAFT','PUBLISHED') DEFAULT 'DRAFT'
published_at        DATETIME NULL
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

## 17. `gallery_items`

```text
id                  BIGINT UNSIGNED PK AI
gallery_id          BIGINT UNSIGNED NOT NULL
media_id            BIGINT UNSIGNED NOT NULL
caption             VARCHAR(255) NULL
display_order       INT DEFAULT 0
created_at          DATETIME NULL
```

---

## 18. `spmb_periods`

```text
id                  BIGINT UNSIGNED PK AI
academic_year       VARCHAR(20) NOT NULL
title               VARCHAR(255) NOT NULL
summary             TEXT NULL
content             LONGTEXT NULL
start_date          DATE NULL
end_date            DATE NULL
registration_url    VARCHAR(500) NULL
qr_media_id         BIGINT UNSIGNED NULL
brochure_media_id   BIGINT UNSIGNED NULL
contact_name        VARCHAR(180) NULL
contact_phone       VARCHAR(50) NULL
status              ENUM('DRAFT','PUBLISHED','ARCHIVED') DEFAULT 'DRAFT'
is_current          TINYINT(1) DEFAULT 0
created_by          BIGINT UNSIGNED NULL
updated_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

Maksimal satu periode current pada level aplikasi.

---

## 19. `spmb_requirements`

```text
id                  BIGINT UNSIGNED PK AI
spmb_period_id      BIGINT UNSIGNED NOT NULL
requirement_text    TEXT NOT NULL
display_order       INT DEFAULT 0
```

## 20. `spmb_faq`

```text
id                  BIGINT UNSIGNED PK AI
spmb_period_id      BIGINT UNSIGNED NOT NULL
question            VARCHAR(255) NOT NULL
answer              TEXT NOT NULL
display_order       INT DEFAULT 0
```

---

## 21. `media`

```text
id                  BIGINT UNSIGNED PK AI
original_name       VARCHAR(255) NOT NULL
stored_name         VARCHAR(255) NOT NULL
relative_path       VARCHAR(500) NOT NULL
mime_type           VARCHAR(100) NOT NULL
extension           VARCHAR(20) NULL
file_size           BIGINT UNSIGNED NOT NULL
width               INT NULL
height              INT NULL
alt_text            VARCHAR(255) NULL
caption             TEXT NULL
media_type          ENUM('IMAGE','DOCUMENT') NOT NULL
created_by          BIGINT UNSIGNED NULL
created_at          DATETIME NULL
```

File fisik tidak disimpan di database.

---

## 22. `instagram_posts`

```text
id                  BIGINT UNSIGNED PK AI
source              ENUM('API','MANUAL') NOT NULL
instagram_media_id  VARCHAR(255) NULL
permalink           VARCHAR(500) NULL
caption             TEXT NULL
media_type          VARCHAR(50) NULL
media_url           TEXT NULL
thumbnail_url       TEXT NULL
local_media_id      BIGINT UNSIGNED NULL
published_at        DATETIME NULL
is_fallback         TINYINT(1) DEFAULT 0
is_visible          TINYINT(1) DEFAULT 1
sort_order          INT DEFAULT 0
fetched_at          DATETIME NULL
created_at          DATETIME NULL
updated_at          DATETIME NULL
```

---

## 23. `audit_logs`

```text
id                  BIGINT UNSIGNED PK AI
user_id             BIGINT UNSIGNED NULL
action              VARCHAR(100) NOT NULL
module              VARCHAR(100) NULL
record_id           BIGINT UNSIGNED NULL
description         TEXT NULL
ip_address          VARCHAR(45) NULL
created_at          DATETIME NOT NULL
```

Catat minimal login, perubahan user, perubahan settings/features, delete konten, perubahan SPMB current, dan konfigurasi Instagram.

---

## 24. Status Konten

Konten utama:
```text
DRAFT
PUBLISHED
```

Operator boleh `DRAFT -> PUBLISHED`.

SPMB tambahan:
```text
ARCHIVED
```

---

## 25. Delete Policy

- Feature OFF tidak menghapus data.
- Media tidak boleh dihapus jika masih direferensikan.
- Dependency diperiksa sebelum delete data berelasi.

---

## 26. SQL Dump Policy

`database/minsix.sql` menjadi sumber schema canonical.

Boleh berisi:
- `CREATE TABLE`;
- index;
- foreign key;
- default `site_features`;
- setting aman/non-rahasia;
- master aman seperti role GTK.

Tidak boleh berisi:
- password plaintext;
- credential DB production;
- token Instagram;
- API key;
- session;
- dump production yang bersifat sensitif.

---

## 27. Upgrade Schema

Jika perlu upgrade production, gunakan:

```text
database/upgrades/YYYYMMDD_description.sql
```

Setelah upgrade, `database/minsix.sql` wajib diperbarui agar tetap menjadi canonical dump terbaru.

---

## 28. Backup

Production:
- backup database berkala;
- backup uploads bersama database;
- SQL dump development bukan pengganti backup production.
