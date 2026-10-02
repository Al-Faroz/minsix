-- PHASE 10C2 — Instagram Login lifecycle + carousel children
-- Run after 20261002_phase10c_integration_settings.sql on an existing database.
-- Adds cached child media payload for CAROUSEL_ALBUM posts.

SET NAMES utf8mb4;

SET @has_children_json := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'instagram_posts'
      AND COLUMN_NAME = 'children_json'
);

SET @sql := IF(
    @has_children_json = 0,
    'ALTER TABLE instagram_posts ADD COLUMN children_json LONGTEXT NULL AFTER thumbnail_url',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
