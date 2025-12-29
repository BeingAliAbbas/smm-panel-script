-- Migration for WhatsApp Verification Feature
-- This adds support for mandatory WhatsApp verification after Google sign-in

-- Add columns to general_users table if they don't exist
ALTER TABLE `general_users` 
ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(255) DEFAULT NULL COMMENT 'Google OAuth ID',
ADD COLUMN IF NOT EXISTS `signup_type` ENUM('manual', 'google') DEFAULT 'manual' COMMENT 'User signup method',
ADD COLUMN IF NOT EXISTS `whatsapp_verified` TINYINT(1) DEFAULT 0 COMMENT 'WhatsApp verification status',
ADD COLUMN IF NOT EXISTS `whatsapp_verified_at` DATETIME DEFAULT NULL COMMENT 'WhatsApp verification timestamp';

-- Create WhatsApp OTP verification table
CREATE TABLE IF NOT EXISTS `whatsapp_otp_verifications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `whatsapp_number` VARCHAR(20) NOT NULL,
  `otp_code` VARCHAR(6) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `verified` TINYINT(1) DEFAULT 0,
  `attempts` INT(11) DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_whatsapp_number` (`whatsapp_number`),
  INDEX `idx_expires_at` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create OTP request rate limiting table
CREATE TABLE IF NOT EXISTS `whatsapp_otp_rate_limit` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(100) NOT NULL COMMENT 'User ID or IP address',
  `request_count` INT(11) DEFAULT 1,
  `first_request_at` DATETIME NOT NULL,
  `last_request_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_identifier` (`identifier`),
  INDEX `idx_last_request` (`last_request_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert WhatsApp OTP notification template (if whatsapp_notifications table exists)
INSERT IGNORE INTO `whatsapp_notifications` (`event_type`, `name`, `template`, `status`, `created_at`)
VALUES (
  'otp_verification',
  'OTP Verification',
  'Your verification code is: *{otp_code}*\n\nThis code will expire in {expiry_minutes} minutes.\n\nDo not share this code with anyone.\n\n- {website_name}',
  1,
  NOW()
);

-- Update existing users with login_type to have signup_type
UPDATE `general_users` SET `signup_type` = 'google' WHERE `login_type` = 'google' AND `signup_type` IS NULL;
UPDATE `general_users` SET `signup_type` = 'manual' WHERE `login_type` IS NULL AND `signup_type` IS NULL;
UPDATE `general_users` SET `signup_type` = 'manual' WHERE `login_type` = 'manual' AND `signup_type` IS NULL;

-- Mark existing manual users as WhatsApp verified (backward compatibility)
UPDATE `general_users` SET `whatsapp_verified` = 1, `whatsapp_verified_at` = NOW() 
WHERE `signup_type` = 'manual' AND `whatsapp_verified` = 0 AND `whatsapp_number` IS NOT NULL;

