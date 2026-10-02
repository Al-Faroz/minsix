-- Upgrade PHASE 7 -> PHASE 8A/B
-- SEO global + SEO per Prestasi.
-- Jalankan pada database yang sudah lulus PHASE 7.

SET NAMES utf8mb4;

ALTER TABLE achievements
    ADD COLUMN meta_title VARCHAR(255) NULL AFTER published_at,
    ADD COLUMN meta_description VARCHAR(320) NULL AFTER meta_title,
    ADD COLUMN og_media_id BIGINT UNSIGNED NULL AFTER meta_description,
    ADD KEY idx_achievements_og_media (og_media_id),
    ADD CONSTRAINT fk_achievements_og
        FOREIGN KEY (og_media_id) REFERENCES media(id)
        ON UPDATE CASCADE ON DELETE SET NULL;

INSERT IGNORE INTO site_settings (setting_key, setting_value, value_type, is_public) VALUES
('seo_default_title', 'MIN 6 Jember', 'string', 1),
('seo_default_description', 'Berakhlaqul Karimah dan Berprestasi', 'text', 1),
('seo_default_og_media_id', '', 'integer', 1),
('seo_canonical_base_url', '', 'string', 1);
