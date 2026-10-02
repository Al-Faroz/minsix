-- Upgrade PHASE 5 -> PHASE 6A
-- Homepage CMS + frontend foundation.
-- Jalankan pada database yang sudah lulus PHASE 5.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS homepage_sections (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    section_key VARCHAR(100) NOT NULL,
    eyebrow VARCHAR(200) NULL,
    title VARCHAR(255) NULL,
    subtitle TEXT NULL,
    body LONGTEXT NULL,
    content_json JSON NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    cta_label VARCHAR(120) NULL,
    cta_url VARCHAR(255) NULL,
    secondary_cta_label VARCHAR(120) NULL,
    secondary_cta_url VARCHAR(255) NULL,
    display_order INT NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_homepage_sections_key (section_key),
    KEY idx_homepage_sections_order (display_order),
    KEY idx_homepage_sections_media (primary_media_id),
    CONSTRAINT fk_homepage_sections_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_homepage_sections_user FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO homepage_sections
(section_key, eyebrow, title, subtitle, body, cta_label, cta_url, secondary_cta_label, secondary_cta_url, display_order)
VALUES
('hero', 'MADRASAH IBTIDAIYAH NEGERI 6 JEMBER', 'Berakhlakul Karimah. Tumbuh dalam Prestasi.', 'Lingkungan belajar untuk menumbuhkan ilmu, karakter, kreativitas, dan nilai-nilai keislaman sejak usia dasar.', NULL, 'Jelajahi Madrasah', '#mengenal', 'Kenali MIN 6 Jember', '/profil', 10),
('stats', 'MIN 6 JEMBER', 'Madrasah yang terus tumbuh', NULL, NULL, NULL, NULL, NULL, NULL, 20),
('about', 'MENGENAL MIN 6 JEMBER', 'Ruang belajar yang menumbuhkan ilmu dan karakter.', NULL, NULL, 'Selengkapnya', '/profil', NULL, NULL, 30),
('habits', 'KESEHARIAN MADRASAH', 'Keseharian yang Membentuk Karakter', NULL, NULL, NULL, NULL, NULL, NULL, 40),
('program_intro', 'BELAJAR & BERKEMBANG', 'Program yang mendampingi setiap proses tumbuh.', NULL, NULL, 'Lihat Program', '/program', NULL, NULL, 50),
('achievement_intro', 'PRESTASI PESERTA DIDIK', 'Tumbuh melalui proses, berkembang melalui pengalaman.', NULL, NULL, NULL, NULL, NULL, NULL, 60),
('story_intro', 'CERITA DARI MADRASAH', 'Potret keseharian MIN 6 Jember.', NULL, NULL, NULL, NULL, NULL, NULL, 70),
('news_intro', 'KABAR TERBARU', 'Informasi terbaru dari madrasah.', NULL, NULL, 'Lihat Semua Kabar', '/kabar', NULL, NULL, 80),
('instagram_intro', 'INSTAGRAM', 'Ikuti keseharian @min6jember', NULL, NULL, NULL, NULL, NULL, NULL, 90),
('headmaster', 'SAMBUTAN KEPALA MADRASAH', 'Menyambut setiap langkah tumbuh bersama MIN 6 Jember.', NULL, NULL, 'Mengenal Madrasah', '/profil', NULL, NULL, 100),
('spmb_cta', 'SPMB', 'Mari tumbuh bersama MIN 6 Jember.', NULL, NULL, 'Informasi SPMB', '/spmb', NULL, NULL, 110),
('contact', 'KONTAK', 'Terhubung dengan MIN 6 Jember', NULL, NULL, NULL, NULL, NULL, NULL, 120);
