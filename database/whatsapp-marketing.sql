-- WhatsApp Marketing Management System Database Schema
-- This schema adds complete WhatsApp marketing functionality to the SMM Panel

-- =====================================================
-- TABLE: whatsapp_api_configs
-- Purpose: Store multiple WhatsApp API configuration profiles
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_api_configs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255) NOT NULL COMMENT 'Profile name for identification',
  `api_key` VARCHAR(500) NOT NULL,
  `api_endpoint` VARCHAR(500) NOT NULL DEFAULT 'http://waapi.beastsmm.pk/send-message',
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `retry_attempts` INT(11) NOT NULL DEFAULT 3,
  `retry_delay_minutes` INT(11) NOT NULL DEFAULT 5,
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
  `message` TEXT NOT NULL COMMENT 'Current message with placeholders',
  `api_config_id` INT(11) NOT NULL,
  `status` ENUM('pending', 'running', 'paused', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `total_messages` INT(11) NOT NULL DEFAULT 0,
  `sent_messages` INT(11) NOT NULL DEFAULT 0,
  `failed_messages` INT(11) NOT NULL DEFAULT 0,
  `delivered_messages` INT(11) NOT NULL DEFAULT 0,
  `sending_limit_hourly` INT(11) DEFAULT NULL,
  `sending_limit_daily` INT(11) DEFAULT NULL,
  `last_sent_at` DATETIME DEFAULT NULL,
  `started_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `api_config_id` (`api_config_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_recipients
-- Purpose: Store campaign recipients
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_recipients` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL COMMENT 'Phone number without + symbol',
  `name` VARCHAR(255) DEFAULT NULL,
  `user_id` INT(11) DEFAULT NULL COMMENT 'Reference to general_users if imported from DB',
  `custom_data` TEXT DEFAULT NULL COMMENT 'JSON data for template variables',
  `status` ENUM('pending', 'sent', 'failed', 'delivered') NOT NULL DEFAULT 'pending',
  `sent_at` DATETIME DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `phone_number` (`phone_number`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_logs
-- Purpose: Detailed logging of all WhatsApp activities
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `campaign_id` INT(11) NOT NULL,
  `recipient_id` INT(11) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('queued', 'sent', 'failed', 'delivered') NOT NULL DEFAULT 'queued',
  `error_message` TEXT DEFAULT NULL,
  `api_response` TEXT DEFAULT NULL,
  `sent_at` DATETIME DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `phone_number` (`phone_number`),
  KEY `status` (`status`),
  KEY `sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: whatsapp_settings
-- Purpose: Global WhatsApp marketing settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `whatsapp_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insert default settings
-- =====================================================
INSERT INTO `whatsapp_settings` (`setting_key`, `setting_value`) VALUES
('default_hourly_limit', '100'),
('default_daily_limit', '1000'),
('retry_failed_attempts', '3'),
('retry_delay_minutes', '5'),
('enable_delivery_tracking', '1')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- =====================================================
-- Insert sample WhatsApp API configuration (must be updated)
-- =====================================================
INSERT INTO `whatsapp_api_configs` 
(`ids`, `name`, `api_key`, `api_endpoint`, `is_default`, `status`) 
VALUES
(MD5(CONCAT('whatsapp_default', NOW())), 'Default WhatsApp API', 'YOUR_API_KEY', 'http://waapi.beastsmm.pk/send-message', 1, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- =====================================================
-- Add foreign key constraints (optional, for data integrity)
-- =====================================================
-- Note: Uncomment these if you want strict referential integrity
-- ALTER TABLE `whatsapp_campaigns` 
--   ADD CONSTRAINT `fk_whatsapp_campaign_api` FOREIGN KEY (`api_config_id`) REFERENCES `whatsapp_api_configs` (`id`) ON DELETE RESTRICT;

-- ALTER TABLE `whatsapp_recipients` 
--   ADD CONSTRAINT `fk_whatsapp_recipient_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `whatsapp_campaigns` (`id`) ON DELETE CASCADE;

-- ALTER TABLE `whatsapp_logs` 
--   ADD CONSTRAINT `fk_whatsapp_log_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `whatsapp_campaigns` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `fk_whatsapp_log_recipient` FOREIGN KEY (`recipient_id`) REFERENCES `whatsapp_recipients` (`id`) ON DELETE CASCADE;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
