-- WhatsApp Marketing Management System Database Schema

-- Table structure for table `whatsapp_api_configs`
DROP TABLE IF EXISTS `whatsapp_api_configs`;
CREATE TABLE `whatsapp_api_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `profile_name` varchar(255) NOT NULL,
  `api_endpoint` varchar(500) NOT NULL DEFAULT 'http://waapi.beastsmm.pk/send-message',
  `api_key` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `changed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `whatsapp_campaigns`
DROP TABLE IF EXISTS `whatsapp_campaigns`;
CREATE TABLE `whatsapp_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT 0,
  `campaign_name` varchar(255) NOT NULL,
  `message_content` text NOT NULL,
  `api_config_id` int(11) NOT NULL,
  `status` enum('pending','running','paused','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_recipients` int(11) NOT NULL DEFAULT 0,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `delivered_count` int(11) NOT NULL DEFAULT 0,
  `failed_count` int(11) NOT NULL DEFAULT 0,
  `limit_per_hour` int(11) DEFAULT NULL,
  `limit_per_day` int(11) DEFAULT NULL,
  `retry_failed` tinyint(1) NOT NULL DEFAULT 0,
  `max_retries` int(11) NOT NULL DEFAULT 3,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `changed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `status` (`status`),
  KEY `api_config_id` (`api_config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `whatsapp_recipients`
DROP TABLE IF EXISTS `whatsapp_recipients`;
CREATE TABLE `whatsapp_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Reference to general_users.id if from database',
  `phone_number` varchar(20) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `balance` decimal(15,4) DEFAULT 0.0000,
  `source` enum('database','import') NOT NULL DEFAULT 'database',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `user_id` (`user_id`),
  KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `whatsapp_messages`
DROP TABLE IF EXISTS `whatsapp_messages`;
CREATE TABLE `whatsapp_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `message_content` text NOT NULL,
  `status` enum('pending','sent','delivered','failed') NOT NULL DEFAULT 'pending',
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `api_response` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `changed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ids` (`ids`),
  KEY `campaign_id` (`campaign_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `status` (`status`),
  KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for better performance
ALTER TABLE `whatsapp_messages` ADD INDEX `idx_campaign_status` (`campaign_id`, `status`);
ALTER TABLE `whatsapp_messages` ADD INDEX `idx_sent_at` (`sent_at`);
ALTER TABLE `whatsapp_campaigns` ADD INDEX `idx_status_changed` (`status`, `changed`);
