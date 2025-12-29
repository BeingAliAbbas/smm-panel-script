-- WhatsApp Authentication Enhancement
-- Database schema updates for WhatsApp number verification after Google login

-- Add signup_type column to general_users if not exists
ALTER TABLE `general_users` 
ADD COLUMN IF NOT EXISTS `signup_type` VARCHAR(20) DEFAULT 'manual' COMMENT 'manual or google' AFTER `login_type`;

-- Add whatsapp_verified column to general_users if not exists
ALTER TABLE `general_users` 
ADD COLUMN IF NOT EXISTS `whatsapp_verified` TINYINT(1) DEFAULT 0 COMMENT 'WhatsApp number verification status' AFTER `whatsapp_number`;

-- Add whatsapp_setup_completed column to track if setup page was completed
ALTER TABLE `general_users` 
ADD COLUMN IF NOT EXISTS `whatsapp_setup_completed` TINYINT(1) DEFAULT 0 COMMENT 'Whether WhatsApp setup step completed' AFTER `whatsapp_verified`;

-- Create table for WhatsApp OTP verification tracking
CREATE TABLE IF NOT EXISTS `whatsapp_otp_verification` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `whatsapp_number` VARCHAR(20) NOT NULL,
  `otp_code` VARCHAR(6) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `verified_at` DATETIME DEFAULT NULL,
  `attempts` INT(11) DEFAULT 0 COMMENT 'Number of verification attempts',
  `status` ENUM('pending', 'verified', 'expired', 'failed') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default setting for WhatsApp OTP verification (disabled by default)
INSERT INTO `general_options` (`name`, `value`, `created`)
SELECT 'whatsapp_otp_verification_enabled', '0', NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM `general_options` WHERE `name` = 'whatsapp_otp_verification_enabled'
);

-- Add WhatsApp OTP notification template
INSERT INTO `whatsapp_notifications` (`event_type`, `event_name`, `template`, `status`, `description`)
SELECT 
  'whatsapp_otp',
  'WhatsApp OTP Verification',
  '*🔐 OTP Verification - {website_name}*\n\nYour OTP code is: *{otp_code}*\n\nThis code will expire in *{expiry_minutes} minutes*.\n\nDo not share this code with anyone.',
  1,
  'OTP code sent to users for WhatsApp number verification'
WHERE NOT EXISTS (
  SELECT 1 FROM `whatsapp_notifications` WHERE `event_type` = 'whatsapp_otp'
);

-- Update existing Google login users to have signup_type set to 'google'
UPDATE `general_users` 
SET `signup_type` = 'google' 
WHERE `login_type` = 'google' AND (`signup_type` IS NULL OR `signup_type` = '');

-- Update existing manual signup users to have signup_type set to 'manual'
UPDATE `general_users` 
SET `signup_type` = 'manual' 
WHERE (`login_type` IS NULL OR `login_type` = '' OR `login_type` = 'manual') 
  AND (`signup_type` IS NULL OR `signup_type` = '');

-- Mark existing users as having completed WhatsApp setup
UPDATE `general_users` 
SET `whatsapp_setup_completed` = 1
WHERE `whatsapp_setup_completed` IS NULL OR `whatsapp_setup_completed` = 0;

-- Mark users with WhatsApp numbers as verified (for existing users)
UPDATE `general_users` 
SET `whatsapp_verified` = 1
WHERE `whatsapp_number` IS NOT NULL AND `whatsapp_number` != '' AND (`whatsapp_verified` IS NULL OR `whatsapp_verified` = 0);

