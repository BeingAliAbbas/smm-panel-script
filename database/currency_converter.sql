-- Currency Converter Database Table
-- This table is used to cache exchange rates to reduce API calls

CREATE TABLE IF NOT EXISTS `currency_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_currency` varchar(3) NOT NULL,
  `rates` text NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_currency` (`base_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
