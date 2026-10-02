-- FINAL-D2 — Structured SPMB
-- Run on database that already contains spmb_periods.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS spmb_steps (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    spmb_period_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    display_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_spmb_steps_period_order (spmb_period_id, display_order),
    CONSTRAINT fk_spmb_steps_period FOREIGN KEY (spmb_period_id)
        REFERENCES spmb_periods(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spmb_highlights (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    spmb_period_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    display_order INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_spmb_highlights_period_order (spmb_period_id, display_order),
    CONSTRAINT fk_spmb_highlights_period FOREIGN KEY (spmb_period_id)
        REFERENCES spmb_periods(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
