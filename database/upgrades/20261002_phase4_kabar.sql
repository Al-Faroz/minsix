-- Upgrade PHASE 3 -> PHASE 4
-- Kabar Madrasah: Berita + Agenda + Prestasi + Galeri
-- Jalankan pada database yang sudah lulus PHASE 3.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS news (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(200) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NOT NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    published_at DATETIME NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(320) NULL,
    og_media_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_news_slug (slug),
    KEY idx_news_status_published (status, published_at),
    KEY idx_news_primary_media (primary_media_id),
    KEY idx_news_og_media (og_media_id),
    CONSTRAINT fk_news_primary_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_news_og_media FOREIGN KEY (og_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_news_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_news_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(200) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    description LONGTEXT NULL,
    location VARCHAR(255) NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_events_slug (slug),
    KEY idx_events_status_start (status, start_at),
    KEY idx_events_primary_media (primary_media_id),
    CONSTRAINT fk_events_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_events_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_events_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS achievements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(200) NOT NULL,
    title VARCHAR(255) NOT NULL,
    participant_name VARCHAR(255) NULL,
    field_name VARCHAR(150) NULL,
    award VARCHAR(150) NULL,
    level VARCHAR(100) NULL,
    organizer VARCHAR(255) NULL,
    achievement_date DATE NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    published_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_achievements_slug (slug),
    KEY idx_achievements_status_date (status, achievement_date),
    KEY idx_achievements_primary_media (primary_media_id),
    CONSTRAINT fk_achievements_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_achievements_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_achievements_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galleries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(200) NOT NULL,
    title VARCHAR(255) NOT NULL,
    gallery_date DATE NULL,
    description TEXT NULL,
    cover_media_id BIGINT UNSIGNED NULL,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    published_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_galleries_slug (slug),
    KEY idx_galleries_status_date (status, gallery_date),
    KEY idx_galleries_cover_media (cover_media_id),
    CONSTRAINT fk_galleries_cover FOREIGN KEY (cover_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_galleries_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_galleries_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    gallery_id BIGINT UNSIGNED NOT NULL,
    media_id BIGINT UNSIGNED NOT NULL,
    caption VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gallery_item_media (gallery_id, media_id),
    KEY idx_gallery_items_order (gallery_id, display_order),
    KEY idx_gallery_items_media (media_id),
    CONSTRAINT fk_gallery_items_gallery FOREIGN KEY (gallery_id) REFERENCES galleries(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gallery_items_media FOREIGN KEY (media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
