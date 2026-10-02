-- MIN SIX — canonical SQL dump
-- PHASE 5: Auth + Settings + Features + Media + Profil + Program + GTK + Kabar + SPMB
-- Schema aplikasi menggunakan SQL dump, bukan Migration/Seeder.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS spmb_faq;
DROP TABLE IF EXISTS spmb_requirements;
DROP TABLE IF EXISTS spmb_periods;
DROP TABLE IF EXISTS gallery_items;
DROP TABLE IF EXISTS galleries;
DROP TABLE IF EXISTS achievements;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS news;
DROP TABLE IF EXISTS gtk_role_assignments;
DROP TABLE IF EXISTS gtk_roles;
DROP TABLE IF EXISTS gtk;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS profile_sections;
DROP TABLE IF EXISTS media;
DROP TABLE IF EXISTS site_features;
DROP TABLE IF EXISTS site_settings;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS app_users;

CREATE TABLE app_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(150) NOT NULL,
    role ENUM('ADMIN','OPERATOR') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_app_users_username (username),
    KEY idx_app_users_role (role),
    KEY idx_app_users_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(100) NULL,
    record_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_audit_logs_user_id (user_id),
    KEY idx_audit_logs_action (action),
    KEY idx_audit_logs_module (module),
    KEY idx_audit_logs_created_at (created_at),
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_settings (
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
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_features (
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
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
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
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profile_sections (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    section_key VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    body LONGTEXT NULL,
    content_json JSON NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    display_order INT NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_profile_sections_key (section_key),
    KEY idx_profile_sections_order (display_order),
    KEY idx_profile_sections_media (primary_media_id),
    CONSTRAINT fk_profile_sections_media FOREIGN KEY (primary_media_id)
        REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_profile_sections_user FOREIGN KEY (updated_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE programs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(180) NOT NULL,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    primary_media_id BIGINT UNSIGNED NULL,
    display_order INT NOT NULL DEFAULT 0,
    status ENUM('DRAFT','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
    published_at DATETIME NULL,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_programs_slug (slug),
    KEY idx_programs_category (category),
    KEY idx_programs_status (status),
    KEY idx_programs_order (display_order),
    CONSTRAINT fk_programs_media FOREIGN KEY (primary_media_id)
        REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_programs_created_by FOREIGN KEY (created_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_programs_updated_by FOREIGN KEY (updated_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gtk (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(180) NOT NULL,
    front_title VARCHAR(50) NULL,
    back_title VARCHAR(120) NULL,
    photo_media_id BIGINT UNSIGNED NULL,
    short_bio TEXT NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_gtk_name (name),
    KEY idx_gtk_active (is_active),
    KEY idx_gtk_order (display_order),
    CONSTRAINT fk_gtk_photo FOREIGN KEY (photo_media_id)
        REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_gtk_created_by FOREIGN KEY (created_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_gtk_updated_by FOREIGN KEY (updated_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gtk_roles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_key VARCHAR(100) NOT NULL,
    role_name VARCHAR(150) NOT NULL,
    category ENUM('LEADERSHIP','CLASS_TEACHER','SUBJECT_TEACHER','STAFF') NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gtk_roles_key (role_key),
    UNIQUE KEY uq_gtk_roles_name (role_name),
    KEY idx_gtk_roles_category (category),
    KEY idx_gtk_roles_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gtk_role_assignments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    gtk_id BIGINT UNSIGNED NOT NULL,
    gtk_role_id BIGINT UNSIGNED NOT NULL,
    role_label_override VARCHAR(180) NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gtk_role_assignment (gtk_id, gtk_role_id),
    KEY idx_gtk_role_assignments_role (gtk_role_id),
    CONSTRAINT fk_gtk_role_assignments_gtk FOREIGN KEY (gtk_id)
        REFERENCES gtk(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gtk_role_assignments_role FOREIGN KEY (gtk_role_id)
        REFERENCES gtk_roles(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS spmb_periods (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    academic_year VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    registration_url VARCHAR(500) NULL,
    qr_media_id BIGINT UNSIGNED NULL,
    brochure_media_id BIGINT UNSIGNED NULL,
    contact_name VARCHAR(180) NULL,
    contact_phone VARCHAR(50) NULL,
    status ENUM('DRAFT','PUBLISHED','ARCHIVED') NOT NULL DEFAULT 'DRAFT',
    is_current TINYINT(1) NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    KEY idx_spmb_periods_year (academic_year),
    KEY idx_spmb_periods_status (status),
    KEY idx_spmb_periods_current (is_current),
    KEY idx_spmb_periods_qr_media (qr_media_id),
    KEY idx_spmb_periods_brochure_media (brochure_media_id),
    CONSTRAINT fk_spmb_periods_qr FOREIGN KEY (qr_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_spmb_periods_brochure FOREIGN KEY (brochure_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_spmb_periods_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_spmb_periods_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spmb_requirements (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    spmb_period_id BIGINT UNSIGNED NOT NULL,
    requirement_text TEXT NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_spmb_requirements_period_order (spmb_period_id, display_order),
    CONSTRAINT fk_spmb_requirements_period FOREIGN KEY (spmb_period_id) REFERENCES spmb_periods(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spmb_faq (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    spmb_period_id BIGINT UNSIGNED NOT NULL,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_spmb_faq_period_order (spmb_period_id, display_order),
    CONSTRAINT fk_spmb_faq_period FOREIGN KEY (spmb_period_id) REFERENCES spmb_periods(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings (setting_key, setting_value, value_type, is_public) VALUES
('site_name', 'MIN 6 Jember', 'string', 1),
('site_tagline', 'Berakhlakul Karimah dan Berprestasi', 'string', 1),
('address', '', 'text', 1),
('phone', '', 'string', 1),
('whatsapp', '', 'string', 1),
('email', '', 'string', 1),
('instagram_username', 'min6jember', 'string', 1),
('instagram_url', 'https://www.instagram.com/min6jember', 'string', 1),
('google_maps_embed', '', 'text', 1);

INSERT INTO site_features (feature_key, label, is_enabled, show_in_nav, show_on_home) VALUES
('kabar', 'Kabar Madrasah', 1, 1, 0),
('news', 'Berita', 1, 0, 1),
('events', 'Agenda', 1, 0, 1),
('achievements', 'Prestasi', 1, 0, 1),
('gallery', 'Galeri', 1, 0, 1),
('instagram', 'Instagram', 1, 0, 1),
('spmb', 'SPMB', 1, 1, 1);

INSERT INTO profile_sections (section_key, title, body, display_order) VALUES
('about', 'Mengenal MIN 6 Jember', '', 10),
('vision', 'Visi', 'Berakhlaqul Karimah dan Berprestasi', 20),
('history', 'Sejarah', '', 30),
('timeline', 'Perjalanan Madrasah', '', 40),
('headmaster_message', 'Sambutan Kepala Madrasah', '', 50),
('identity', 'Identitas Madrasah', '', 60),
('location', 'Lokasi', '', 70);

INSERT INTO gtk_roles (role_key, role_name, category, display_order, is_active) VALUES
('HEADMASTER', 'Kepala Madrasah', 'LEADERSHIP', 10, 1),
('PKM_CURRICULUM', 'PKM Kurikulum', 'LEADERSHIP', 20, 1),
('PKM_STUDENT', 'PKM Kesiswaan', 'LEADERSHIP', 30, 1),
('PKM_INFRASTRUCTURE', 'PKM Sarana Prasarana', 'LEADERSHIP', 40, 1),
('PKM_PUBLIC_RELATIONS', 'PKM Humas', 'LEADERSHIP', 50, 1),
('CLASS_TEACHER', 'Guru Kelas', 'CLASS_TEACHER', 10, 1),
('SUBJECT_TEACHER', 'Guru Mata Pelajaran', 'SUBJECT_TEACHER', 10, 1),
('ADMIN_STAFF', 'Tenaga Kependidikan', 'STAFF', 10, 1);

SET FOREIGN_KEY_CHECKS = 1;
