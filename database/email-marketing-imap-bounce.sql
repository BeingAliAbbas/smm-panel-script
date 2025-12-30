-- =====================================================
-- IMAP Bounce Detection Enhancement for Email Marketing
-- =====================================================

-- Add IMAP configuration fields to email_smtp_configs table
ALTER TABLE `email_smtp_configs` 
ADD COLUMN `imap_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Enable IMAP bounce detection' AFTER `status`,
ADD COLUMN `imap_host` VARCHAR(255) DEFAULT NULL COMMENT 'IMAP server host' AFTER `imap_enabled`,
ADD COLUMN `imap_port` INT(11) DEFAULT 993 COMMENT 'IMAP server port' AFTER `imap_host`,
ADD COLUMN `imap_encryption` ENUM('none', 'ssl', 'tls') DEFAULT 'ssl' COMMENT 'IMAP encryption type' AFTER `imap_port`,
ADD COLUMN `imap_username` VARCHAR(255) DEFAULT NULL COMMENT 'IMAP username (usually same as SMTP)' AFTER `imap_encryption`,
ADD COLUMN `imap_password` TEXT DEFAULT NULL COMMENT 'IMAP password or app password' AFTER `imap_username`,
ADD COLUMN `imap_last_check` DATETIME DEFAULT NULL COMMENT 'Last bounce check timestamp' AFTER `imap_password`,
ADD COLUMN `imap_processed_folder` VARCHAR(100) DEFAULT 'Processed_Bounces' COMMENT 'Folder to move processed bounces' AFTER `imap_last_check`;

-- =====================================================
-- TABLE: email_bounces
-- Purpose: Track bounced, invalid, and suppressed email addresses
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_bounces` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `smtp_config_id` INT(11) DEFAULT NULL COMMENT 'SMTP config that detected the bounce',
  `campaign_id` INT(11) DEFAULT NULL COMMENT 'Campaign that caused the bounce (if known)',
  `bounce_type` ENUM('hard', 'soft', 'invalid', 'spam_complaint', 'unsubscribe', 'manual') NOT NULL DEFAULT 'hard' COMMENT 'Type of bounce',
  `bounce_reason` VARCHAR(500) DEFAULT NULL COMMENT 'Specific reason extracted from bounce message',
  `bounce_code` VARCHAR(50) DEFAULT NULL COMMENT 'SMTP error code (e.g., 550, 452)',
  `raw_bounce_message` TEXT DEFAULT NULL COMMENT 'Original bounce message for debugging',
  `source` ENUM('imap', 'smtp_response', 'manual', 'validation_api') NOT NULL DEFAULT 'imap' COMMENT 'How bounce was detected',
  `status` ENUM('active', 'removed', 'temporary') NOT NULL DEFAULT 'active' COMMENT 'Suppression status',
  `suppressed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When email was added to suppression list',
  `expires_at` DATETIME DEFAULT NULL COMMENT 'For temporary bounces, when to retry',
  `retry_count` INT(11) NOT NULL DEFAULT 0 COMMENT 'Number of times this email has bounced',
  `last_bounce_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  UNIQUE KEY `email_unique` (`email`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `bounce_type` (`bounce_type`),
  KEY `status` (`status`),
  KEY `email_status` (`email`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: email_bounce_logs
-- Purpose: Detailed log of all bounce detection activities
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_bounce_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `smtp_config_id` INT(11) NOT NULL,
  `bounce_id` INT(11) DEFAULT NULL COMMENT 'Reference to email_bounces if created',
  `email` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(500) DEFAULT NULL,
  `bounce_type` VARCHAR(50) DEFAULT NULL,
  `bounce_reason` VARCHAR(500) DEFAULT NULL,
  `action_taken` VARCHAR(100) DEFAULT NULL COMMENT 'e.g., added_to_suppression, updated_existing',
  `message_uid` VARCHAR(50) DEFAULT NULL COMMENT 'IMAP message UID',
  `processed` TINYINT(1) NOT NULL DEFAULT 0,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `bounce_id` (`bounce_id`),
  KEY `email` (`email`),
  KEY `processed` (`processed`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Add settings for bounce detection
-- =====================================================
INSERT INTO `email_settings` (`setting_key`, `setting_value`) VALUES
('bounce_detection_enabled', '1'),
('bounce_hard_suppress_permanent', '1'),
('bounce_soft_retry_count', '3'),
('bounce_soft_retry_hours', '24'),
('bounce_check_interval_minutes', '30'),
('bounce_time_window_hours', '48'),
('bounce_max_emails_per_check', '50')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- =====================================================
-- Update email_recipients table to track suppression
-- =====================================================
ALTER TABLE `email_recipients` 
ADD COLUMN `is_suppressed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether email is in suppression list' AFTER `status`,
ADD COLUMN `suppression_reason` VARCHAR(255) DEFAULT NULL COMMENT 'Reason for suppression' AFTER `is_suppressed`,
ADD INDEX `idx_suppressed` (`is_suppressed`);

-- =====================================================
-- Update email_logs to track bounce information
-- =====================================================
ALTER TABLE `email_logs`
ADD COLUMN `smtp_config_id` INT(11) DEFAULT NULL COMMENT 'SMTP config used for sending' AFTER `recipient_id`,
ADD COLUMN `bounce_detected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether bounce was detected' AFTER `status`,
ADD COLUMN `bounce_type` VARCHAR(50) DEFAULT NULL COMMENT 'Type of bounce if detected' AFTER `bounce_detected`;

-- Add index for bounce detection queries
ALTER TABLE `email_logs`
ADD INDEX `idx_bounce_detected` (`bounce_detected`);

-- =====================================================
-- END OF SCHEMA
-- =====================================================
