-- Balance Logs Table Migration
-- This table records all balance changes for users including order deductions, payments, refunds, etc.

CREATE TABLE IF NOT EXISTS `general_balance_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ids` varchar(32) NOT NULL,
  `uid` int(11) NOT NULL,
  `action_type` enum('deduction','addition','refund','manual_add','manual_deduct') NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `balance_before` decimal(15,4) NOT NULL,
  `balance_after` decimal(15,4) NOT NULL,
  `description` text,
  `related_id` varchar(100) DEFAULT NULL COMMENT 'Order ID, Transaction ID, etc.',
  `related_type` varchar(50) DEFAULT NULL COMMENT 'order, transaction, refund, etc.',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ids` (`ids`),
  KEY `action_type` (`action_type`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
