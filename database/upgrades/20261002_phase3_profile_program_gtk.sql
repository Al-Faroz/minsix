-- Upgrade PHASE 2 -> PHASE 3
-- Profil + Program + GTK
-- Aman dijalankan pada database yang sudah lulus PHASE 2.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS profile_sections (
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
    CONSTRAINT fk_profile_sections_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_profile_sections_user FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS programs (
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
    CONSTRAINT fk_programs_media FOREIGN KEY (primary_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_programs_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_programs_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gtk (
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
    CONSTRAINT fk_gtk_photo FOREIGN KEY (photo_media_id) REFERENCES media(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_gtk_created_by FOREIGN KEY (created_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_gtk_updated_by FOREIGN KEY (updated_by) REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gtk_roles (
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

CREATE TABLE IF NOT EXISTS gtk_role_assignments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    gtk_id BIGINT UNSIGNED NOT NULL,
    gtk_role_id BIGINT UNSIGNED NOT NULL,
    role_label_override VARCHAR(180) NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gtk_role_assignment (gtk_id, gtk_role_id),
    KEY idx_gtk_role_assignments_role (gtk_role_id),
    CONSTRAINT fk_gtk_role_assignments_gtk FOREIGN KEY (gtk_id) REFERENCES gtk(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_gtk_role_assignments_role FOREIGN KEY (gtk_role_id) REFERENCES gtk_roles(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO profile_sections (section_key, title, body, display_order) VALUES
('about', 'Mengenal MIN 6 Jember', '', 10),
('vision', 'Visi', 'Berakhlaqul Karimah dan Berprestasi', 20),
('history', 'Sejarah', '', 30),
('timeline', 'Perjalanan Madrasah', '', 40),
('headmaster_message', 'Sambutan Kepala Madrasah', '', 50),
('identity', 'Identitas Madrasah', '', 60),
('location', 'Lokasi', '', 70);

INSERT IGNORE INTO gtk_roles (role_key, role_name, category, display_order, is_active) VALUES
('HEADMASTER', 'Kepala Madrasah', 'LEADERSHIP', 10, 1),
('PKM_CURRICULUM', 'PKM Kurikulum', 'LEADERSHIP', 20, 1),
('PKM_STUDENT', 'PKM Kesiswaan', 'LEADERSHIP', 30, 1),
('PKM_INFRASTRUCTURE', 'PKM Sarana Prasarana', 'LEADERSHIP', 40, 1),
('PKM_PUBLIC_RELATIONS', 'PKM Humas', 'LEADERSHIP', 50, 1),
('CLASS_TEACHER', 'Guru Kelas', 'CLASS_TEACHER', 10, 1),
('SUBJECT_TEACHER', 'Guru Mata Pelajaran', 'SUBJECT_TEACHER', 10, 1),
('ADMIN_STAFF', 'Tenaga Kependidikan', 'STAFF', 10, 1);
