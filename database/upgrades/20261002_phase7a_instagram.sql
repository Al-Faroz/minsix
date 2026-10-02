-- Upgrade PHASE 6 -> PHASE 7A
-- Instagram local cache + manual fallback + safe configuration.
-- Jalankan pada database yang sudah lulus PHASE 6.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS instagram_posts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source ENUM('API','MANUAL') NOT NULL,
    instagram_media_id VARCHAR(255) NULL,
    permalink VARCHAR(500) NULL,
    caption TEXT NULL,
    media_type VARCHAR(50) NULL,
    media_url TEXT NULL,
    thumbnail_url TEXT NULL,
    local_media_id BIGINT UNSIGNED NULL,
    published_at DATETIME NULL,
    is_fallback TINYINT(1) NOT NULL DEFAULT 0,
    is_visible TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    fetched_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_instagram_source (source),
    KEY idx_instagram_media_id (instagram_media_id),
    KEY idx_instagram_visible_sort (is_visible, sort_order),
    KEY idx_instagram_published (published_at),
    KEY idx_instagram_local_media (local_media_id),
    CONSTRAINT fk_instagram_posts_media FOREIGN KEY (local_media_id)
        REFERENCES media(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT IGNORE INTO site_settings (setting_key, setting_value, value_type, is_public) VALUES
('instagram_source_mode', 'HYBRID', 'string', 0),
('instagram_display_count', '8', 'integer', 0);
