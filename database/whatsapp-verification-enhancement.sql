-- WhatsApp Verification Enhancement for Google Sign-In
-- This migration adds necessary fields for WhatsApp OTP verification after Google authentication

-- Add columns to general_users table for WhatsApp verification and signup type tracking
ALTER TABLE `general_users` 
ADD COLUMN IF NOT EXISTS `whatsapp_verified` TINYINT(1) DEFAULT 0 COMMENT 'Whether WhatsApp number is verified',
ADD COLUMN IF NOT EXISTS `whatsapp_otp` VARCHAR(10) DEFAULT NULL COMMENT 'Temporary OTP for verification',
ADD COLUMN IF NOT EXISTS `whatsapp_otp_expires_at` DATETIME DEFAULT NULL COMMENT 'OTP expiration timestamp',
ADD COLUMN IF NOT EXISTS `whatsapp_otp_attempts` INT(11) DEFAULT 0 COMMENT 'Number of OTP verification attempts',
ADD COLUMN IF NOT EXISTS `signup_type` ENUM('manual', 'google') DEFAULT 'manual' COMMENT 'User signup method';

-- Update existing users with manual signup_type
UPDATE `general_users` SET `signup_type` = 'manual' WHERE `signup_type` IS NULL AND `google_id` IS NULL;
UPDATE `general_users` SET `signup_type` = 'google' WHERE `google_id` IS NOT NULL AND `google_id` != '';

-- Set whatsapp_verified for existing users who have whatsapp_number
UPDATE `general_users` SET `whatsapp_verified` = 1 WHERE `whatsapp_number` IS NOT NULL AND `whatsapp_number` != '';

-- Create index for faster lookups
ALTER TABLE `general_users` ADD INDEX IF NOT EXISTS `idx_whatsapp_verified` (`whatsapp_verified`);
ALTER TABLE `general_users` ADD INDEX IF NOT EXISTS `idx_signup_type` (`signup_type`);

-- Add OTP verification notification template if whatsapp_notifications table exists
INSERT IGNORE INTO `whatsapp_notifications` (`event_type`, `name`, `template`, `status`, `description`) 
VALUES ('otp_verification', 'OTP Verification', '*🔐 WhatsApp Verification - {website_name}*\n\nYour verification code is: *{otp}*\n\nThis code will expire in {expiry_minutes} minutes.\n\n⚠️ Do not share this code with anyone.', 1, 'Sends OTP code for WhatsApp number verification');

