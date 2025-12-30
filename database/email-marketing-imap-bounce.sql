-- =====================================================
-- IMAP Bounce Detection Enhancement for Email Marketing
-- This adds IMAP configuration support and bounce tracking
-- =====================================================

-- =====================================================
-- UPDATE: email_smtp_configs table
-- Add IMAP configuration fields
-- =====================================================
ALTER TABLE `email_smtp_configs` 
ADD COLUMN `imap_enabled` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Enable IMAP bounce detection' AFTER `status`,
ADD COLUMN `imap_host` VARCHAR(255) DEFAULT NULL COMMENT 'IMAP server host' AFTER `imap_enabled`,
ADD COLUMN `imap_port` INT(11) DEFAULT 993 COMMENT 'IMAP server port' AFTER `imap_host`,
ADD COLUMN `imap_encryption` ENUM('none', 'ssl', 'tls') DEFAULT 'ssl' COMMENT 'IMAP encryption type' AFTER `imap_port`,
ADD COLUMN `imap_username` VARCHAR(255) DEFAULT NULL COMMENT 'IMAP username (often same as SMTP)' AFTER `imap_encryption`,
ADD COLUMN `imap_password` TEXT DEFAULT NULL COMMENT 'IMAP password - NOTE: Should be encrypted at application level' AFTER `imap_username`,
ADD COLUMN `imap_last_check` DATETIME DEFAULT NULL COMMENT 'Last successful IMAP check' AFTER `imap_password`,
ADD COLUMN `imap_last_error` TEXT DEFAULT NULL COMMENT 'Last IMAP connection error' AFTER `imap_last_check`;

-- =====================================================
-- TABLE: email_bounce_logs
-- Purpose: Store detailed bounce detection logs
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_bounce_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `smtp_config_id` INT(11) NOT NULL COMMENT 'SMTP config that received the bounce',
  `bounced_email` VARCHAR(255) NOT NULL COMMENT 'Email address that bounced',
  `bounce_type` ENUM('hard', 'soft', 'complaint', 'unknown') NOT NULL DEFAULT 'unknown' COMMENT 'Type of bounce',
  `bounce_reason` VARCHAR(255) DEFAULT NULL COMMENT 'Bounce reason category',
  `bounce_code` VARCHAR(50) DEFAULT NULL COMMENT 'SMTP error code if available',
  `raw_bounce_message` LONGTEXT DEFAULT NULL COMMENT 'Full bounce email content',
  `parsed_details` TEXT DEFAULT NULL COMMENT 'JSON of parsed bounce details',
  `message_id` VARCHAR(255) DEFAULT NULL COMMENT 'Original message ID if available',
  `detected_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When bounce was detected',
  `processed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Whether bounce was processed',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `bounced_email` (`bounced_email`),
  KEY `bounce_type` (`bounce_type`),
  KEY `detected_at` (`detected_at`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: email_suppression_list
-- Purpose: Maintain list of suppressed/bounced emails
-- =====================================================
CREATE TABLE IF NOT EXISTS `email_suppression_list` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ids` VARCHAR(32) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `reason` ENUM('bounced', 'invalid', 'complaint', 'unsubscribed', 'manual') NOT NULL DEFAULT 'bounced',
  `reason_detail` TEXT DEFAULT NULL COMMENT 'Additional details about suppression',
  `bounce_count` INT(11) NOT NULL DEFAULT 1 COMMENT 'Number of times email bounced',
  `first_bounce_date` DATETIME DEFAULT NULL COMMENT 'First bounce occurrence',
  `last_bounce_date` DATETIME DEFAULT NULL COMMENT 'Most recent bounce',
  `smtp_config_id` INT(11) DEFAULT NULL COMMENT 'SMTP config where bounce occurred',
  `bounce_log_id` INT(11) DEFAULT NULL COMMENT 'Reference to bounce log',
  `added_by` ENUM('auto', 'manual') NOT NULL DEFAULT 'auto',
  `notes` TEXT DEFAULT NULL COMMENT 'Admin notes',
  `status` ENUM('active', 'removed') NOT NULL DEFAULT 'active',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ids` (`ids`),
  UNIQUE KEY `email_unique` (`email`),
  KEY `reason` (`reason`),
  KEY `status` (`status`),
  KEY `smtp_config_id` (`smtp_config_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- UPDATE: email_recipients table
-- Add suppression tracking
-- =====================================================
ALTER TABLE `email_recipients` 
ADD COLUMN `is_suppressed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is email in suppression list' AFTER `status`,
ADD COLUMN `suppression_reason` VARCHAR(255) DEFAULT NULL COMMENT 'Reason for suppression' AFTER `is_suppressed`,
ADD KEY `is_suppressed` (`is_suppressed`);

-- =====================================================
-- UPDATE: email_logs table
-- Add bounce tracking fields
-- =====================================================
ALTER TABLE `email_logs` 
ADD COLUMN `bounce_detected` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Bounce detected via IMAP' AFTER `status`,
ADD COLUMN `bounce_log_id` INT(11) DEFAULT NULL COMMENT 'Link to bounce log' AFTER `bounce_detected`,
ADD KEY `bounce_detected` (`bounce_detected`);

-- =====================================================
-- Insert default IMAP settings
-- =====================================================
INSERT INTO `email_settings` (`setting_key`, `setting_value`) VALUES
('imap_check_interval_minutes', '30'),
('imap_auto_suppress_bounces', '1'),
('imap_max_emails_per_check', '50'),
('imap_delete_processed_bounces', '0'),
('imap_validate_ssl_cert', '0'),
('imap_last_global_check', 'never')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- =====================================================
-- Add indexes for performance
-- =====================================================
-- Already added as part of table creation above

-- =====================================================
-- END OF IMAP BOUNCE DETECTION SCHEMA
-- =====================================================
