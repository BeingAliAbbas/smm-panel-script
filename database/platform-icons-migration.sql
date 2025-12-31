-- Migration for Database-Driven Platform Filters and Icons
-- Date: 2025-12-31
-- Description: Add tables for platform management and icon/keyword configuration

-- =====================================================
-- 1. Create platforms table for platform filter management
-- =====================================================
CREATE TABLE IF NOT EXISTS `platforms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Display name (e.g., TikTok, Instagram)',
  `slug` varchar(100) NOT NULL COMMENT 'Unique identifier (e.g., tiktok, instagram)',
  `icon_class` varchar(255) DEFAULT NULL COMMENT 'Font Awesome icon class (e.g., fa-brands fa-tiktok)',
  `icon_url` text DEFAULT NULL COMMENT 'URL to custom icon/GIF image',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Display order in filter bar',
  `status` tinyint(1) DEFAULT 1 COMMENT '1=active, 0=disabled',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `changed` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Insert default platforms
-- =====================================================
INSERT INTO `platforms` (`name`, `slug`, `icon_class`, `sort_order`, `status`) VALUES
('All', 'all', 'fas fa-bars', 0, 1),
('TikTok', 'tiktok', 'fa-brands fa-tiktok', 1, 1),
('YouTube', 'youtube', 'fa-brands fa-youtube', 2, 1),
('Instagram', 'instagram', 'fa-brands fa-instagram', 3, 1),
('Telegram', 'telegram', 'fa-brands fa-telegram', 4, 1),
('Facebook', 'facebook', 'fa-brands fa-facebook', 5, 1),
('Twitter', 'twitter', 'fa-brands fa-x-twitter', 6, 1),
('WhatsApp', 'whatsapp', 'fa-brands fa-whatsapp', 7, 1),
('Snapchat', 'snapchat', 'fa-brands fa-snapchat', 8, 1),
('LinkedIn', 'linkedin', 'fa-brands fa-linkedin', 9, 1),
('Other', 'other', 'fas fa-plus', 999, 1);

-- =====================================================
-- 3. Create platform_keywords table for keyword-based matching
-- =====================================================
CREATE TABLE IF NOT EXISTS `platform_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_id` int(11) NOT NULL COMMENT 'Reference to platforms.id',
  `keyword` varchar(100) NOT NULL COMMENT 'Keyword to match in category/service names',
  `priority` int(11) DEFAULT 0 COMMENT 'Higher priority = checked first',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `platform_id` (`platform_id`),
  KEY `keyword` (`keyword`),
  KEY `priority` (`priority`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. Insert default keywords for platform detection
-- =====================================================
INSERT INTO `platform_keywords` (`platform_id`, `keyword`, `priority`) VALUES
-- TikTok
(2, 'tiktok', 10),
(2, 'tik tok', 9),

-- YouTube
(3, 'youtube', 10),
(3, 'yt ', 9),

-- Instagram
(4, 'instagram', 10),
(4, 'insta', 9),
(4, 'ig ', 8),

-- Telegram
(5, 'telegram', 10),
(5, 'tg ', 9),

-- Facebook
(6, 'facebook', 10),
(6, 'fb ', 9),

-- Twitter
(7, 'twitter', 10),
(7, ' x ', 9),
(7, 'tweet', 8),

-- WhatsApp
(8, 'whatsapp', 10),
(8, 'wa ', 9),

-- Snapchat
(9, 'snapchat', 10),
(9, 'snap', 9),

-- LinkedIn
(10, 'linkedin', 10),
(10, 'linked', 9);

-- =====================================================
-- 5. Create category_icons table for category-specific icon overrides
-- =====================================================
CREATE TABLE IF NOT EXISTS `category_icons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'Reference to categories.id',
  `icon_type` enum('class','url','gif') DEFAULT 'class' COMMENT 'Icon type: class (Font Awesome), url (image), gif (animated)',
  `icon_value` text NOT NULL COMMENT 'Icon class name or URL',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `changed` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. Add platform_id column to categories table
-- =====================================================
ALTER TABLE `categories` 
ADD COLUMN `platform_id` int(11) DEFAULT NULL COMMENT 'Associated platform (for filtering)' AFTER `cate_id`,
ADD KEY `platform_id` (`platform_id`);

-- =====================================================
-- 7. Create platform settings cache table for performance
-- =====================================================
CREATE TABLE IF NOT EXISTS `platform_cache` (
  `cache_key` varchar(100) NOT NULL,
  `cache_data` longtext NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`cache_key`),
  KEY `expires` (`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. Insert default GIF icons for existing platforms (backward compatibility)
-- =====================================================
UPDATE `platforms` SET `icon_url` = 'https://storage.perfectcdn.com/etopvh/xk5ab1173935x41z.gif' WHERE `slug` = 'facebook';
UPDATE `platforms` SET `icon_url` = 'https://storage.perfectcdn.com/etopvh/r2726iff1gsgb78r.gif' WHERE `slug` = 'instagram';
UPDATE `platforms` SET `icon_url` = 'https://i.ibb.co/846d9Whj/372108180-WHATSAPP-ICON-400.gif' WHERE `slug` = 'whatsapp';
UPDATE `platforms` SET `icon_url` = 'https://storage.perfectcdn.com/etopvh/p7pol1se08k6yc2x.gif' WHERE `slug` = 'tiktok';
UPDATE `platforms` SET `icon_url` = 'https://storage.perfectcdn.com/etopvh/duea6r011zfl9fo8.gif' WHERE `slug` = 'youtube';
UPDATE `platforms` SET `icon_url` = 'https://storage.perfectcdn.com/etopvh/8d1btd44mgx8geie.gif' WHERE `slug` = 'twitter';
UPDATE `platforms` SET `icon_url` = 'https://i.ibb.co/KcX4v9Fb/372102050-LINKEDIN-ICON-TRANSPARENT-1080.gif' WHERE `slug` = 'linkedin';
UPDATE `platforms` SET `icon_url` = 'https://i.ibb.co/23F0G4BY/images-7.jpg' WHERE `slug` = 'snapchat';

-- =====================================================
-- Migration complete
-- =====================================================
