-- MIN SIX — canonical SQL dump
-- PHASE 1: Authentication & Manager Skeleton
-- Schema aplikasi menggunakan SQL dump, bukan Migration/Seeder.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
        REFERENCES app_users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
