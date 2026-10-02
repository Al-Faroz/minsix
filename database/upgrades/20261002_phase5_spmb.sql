-- Upgrade PHASE 4 -> PHASE 5
-- SPMB: periode + persyaratan + FAQ
-- Jalankan pada database yang sudah lulus PHASE 4.

SET NAMES utf8mb4;

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
