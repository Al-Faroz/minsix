-- PHASE 10C — Integration credential storage
-- Safe for existing database after PHASE 10B.
-- Secrets are encrypted by the application before storage.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS integration_settings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    provider VARCHAR(50) NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value LONGTEXT NULL,
    is_secret TINYINT(1) NOT NULL DEFAULT 0,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_integration_setting (provider, setting_key),
    KEY idx_integration_provider (provider),
    KEY idx_integration_secret (is_secret),
    CONSTRAINT fk_integration_settings_user FOREIGN KEY (updated_by)
        REFERENCES app_users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
