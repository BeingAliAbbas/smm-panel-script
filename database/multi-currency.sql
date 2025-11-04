-- Multi-currency support table
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` decimal(18,8) NOT NULL DEFAULT '1.00000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default currencies
INSERT INTO `currencies` (`code`, `name`, `symbol`, `exchange_rate`, `is_default`, `status`) VALUES
('USD', 'US Dollar', '$', 1.00000000, 1, 1),
('EUR', 'Euro', '€', 0.92000000, 0, 1),
('GBP', 'British Pound', '£', 0.79000000, 0, 1),
('INR', 'Indian Rupee', '₹', 83.12000000, 0, 1),
('PKR', 'Pakistani Rupee', 'Rs', 278.50000000, 0, 1),
('AUD', 'Australian Dollar', 'A$', 1.52000000, 0, 1),
('CAD', 'Canadian Dollar', 'C$', 1.36000000, 0, 1);
