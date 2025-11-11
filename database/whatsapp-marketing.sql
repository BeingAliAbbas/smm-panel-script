-- WhatsApp Marketing Management System Database Schema
-- This schema adds complete WhatsApp marketing functionality to the SMM Panel

-- =====================================================
-- TABLE: whatsapp_api_configs
-- Purpose: Store multiple WhatsApp API configuration profiles
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_api_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `api_key` VARCHAR(500) NOT NULL,
  `api_endpoint` VARCHAR(500) NOT NULL DEFAULT 'http://waapi.beastsmm.pk/send-message',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_campaigns
-- Purpose: Manage WhatsApp marketing campaigns
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_campaigns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `message` LONGTEXT NOT NULL,
  `api_config_id` INT(11) DEFAULT NULL,
  `status` ENUM('Pending', 'Running', 'Completed', 'Paused') NOT NULL DEFAULT 'Pending',
  `total_recipients` INT(11) NOT NULL DEFAULT 0,
  `sent_count` INT(11) NOT NULL DEFAULT 0,
  `failed_count` INT(11) NOT NULL DEFAULT 0,
  `delivered_count` INT(11) NOT NULL DEFAULT 0,
  `hourly_limit` INT(11) DEFAULT 100,
  `daily_limit` INT(11) DEFAULT 1000,
  `started_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `status` (`status`),
  KEY `api_config_id` (`api_config_id`),
  CONSTRAINT `fk_wa_campaign_api_config` FOREIGN KEY (`api_config_id`) REFERENCES `whatsapp_api_configs` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_recipients
-- Purpose: Store recipients for each campaign
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_recipients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `user_id` INT(11) DEFAULT NULL,
  `phone_number` VARCHAR(50) NOT NULL,
  `name` VARCHAR(255) DEFAULT NULL,
  `custom_data` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Sent', 'Failed', 'Delivered') NOT NULL DEFAULT 'Pending',
  `sent_at` DATETIME DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_wa_recipient_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `whatsapp_campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_logs
-- Purpose: Detailed logging of all WhatsApp sending activities
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `recipient_id` INT(11) NOT NULL,
  `phone_number` VARCHAR(50) NOT NULL,
  `message` LONGTEXT NOT NULL,
  `status` ENUM('Success', 'Failed') NOT NULL DEFAULT 'Success',
  `response` TEXT DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `status` (`status`),
  CONSTRAINT `fk_wa_log_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `whatsapp_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wa_log_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `whatsapp_recipients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_settings
-- Purpose: Store general WhatsApp marketing settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `whatsapp_settings` (`setting_key`, `setting_value`) VALUES
('cron_token', MD5(CONCAT(RAND(), NOW()))),
('default_hourly_limit', '100'),
('default_daily_limit', '1000'),
('retry_failed_messages', '1'),
('max_retry_attempts', '3')
ON DUPLICATE KEY UPDATE `setting_key` = VALUES(`setting_key`);
