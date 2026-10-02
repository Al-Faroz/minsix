-- Upgrade PHASE 1 -> PHASE 2
-- Jalankan pada database yang sudah memiliki app_users + audit_logs.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS site_settings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL,
    setting_value LONGTEXT NULL,
    value_type VARCHAR(30) NOT NULL DEFAULT 'string',
    is_public TINYINT(1) NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_site_settings_key (setting_key),
    KEY idx_site_settings_public (is_public),
    CONSTRAINT fk_site_settings_user FOREIGN KEY (updated_by)
        REFERENCES app_users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_features (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    feature_key VARCHAR(100) NOT NULL,
    label VARCHAR(150) NOT NULL,
    is_enabled TINYINT(1) NOT NULL DEFAULT 1,
    show_in_nav TINYINT(1) NOT NULL DEFAULT 0,
    show_on_home TINYINT(1) NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_site_features_key (feature_key),
    KEY idx_site_features_enabled (is_enabled),
    CONSTRAINT fk_site_features_user FOREIGN KEY (updated_by)
        REFERENCES app_users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    relative_path VARCHAR(500) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    extension VARCHAR(20) NULL,
    file_size BIGINT UNSIGNED NOT NULL,
    width INT NULL,
    height INT NULL,
    alt_text VARCHAR(255) NULL,
    caption TEXT NULL,
    media_type ENUM('IMAGE','DOCUMENT') NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_media_relative_path (relative_path),
    KEY idx_media_type (media_type),
    KEY idx_media_created_at (created_at),
    CONSTRAINT fk_media_user FOREIGN KEY (created_by)
        REFERENCES app_users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO site_settings (setting_key, setting_value, value_type, is_public) VALUES
('site_name', 'MIN 6 Jember', 'string', 1),
('site_tagline', 'Berakhlakul Karimah dan Berprestasi', 'string', 1),
('address', '', 'text', 1),
('phone', '', 'string', 1),
('whatsapp', '', 'string', 1),
('email', '', 'string', 1),
('instagram_username', 'min6jember', 'string', 1),
('instagram_url', 'https://www.instagram.com/min6jember', 'string', 1),
('google_maps_embed', '', 'text', 1);

INSERT IGNORE INTO site_features (feature_key, label, is_enabled, show_in_nav, show_on_home) VALUES
('kabar', 'Kabar Madrasah', 1, 1, 0),
('news', 'Berita', 1, 0, 1),
('events', 'Agenda', 1, 0, 1),
('achievements', 'Prestasi', 1, 0, 1),
('gallery', 'Galeri', 1, 0, 1),
('instagram', 'Instagram', 1, 0, 1),
('spmb', 'SPMB', 1, 1, 1);
