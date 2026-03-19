CREATE DATABASE IF NOT EXISTS reporting_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reporting_db;

CREATE TABLE IF NOT EXISTS saved_views (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED NOT NULL DEFAULT 1,
    name         VARCHAR(255) NOT NULL,
    columns_config JSON,
    filters      JSON,
    sort_config  JSON,
    is_default   TINYINT(1) NOT NULL DEFAULT 0,
    shared_with  JSON,
    version      INT UNSIGNED NOT NULL DEFAULT 1,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS column_config (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id   INT UNSIGNED NOT NULL DEFAULT 1,
    report_id VARCHAR(100) NOT NULL DEFAULT 'default',
    config    JSON,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_report (user_id, report_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id   INT UNSIGNED NOT NULL DEFAULT 1,
    action    VARCHAR(100) NOT NULL,
    report_id VARCHAR(100),
    payload   JSON,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_action (user_id, action)
) ENGINE=InnoDB;
