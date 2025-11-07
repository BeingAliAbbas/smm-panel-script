# Balance Logs Feature - Implementation Guide

## Overview

The Balance Logs feature provides comprehensive tracking of all financial activities in the SMM Panel. It records every balance change for users including order deductions, payment additions, refunds, and manual adjustments by administrators.

## Features

### User Features
- View personal balance change history
- See detailed information about each transaction:
  - Action type (Order, Payment, Refund, etc.)
  - Amount changed (with +/- indicator)
  - Balance before and after
  - Description of the change
  - Date and time
- Search through personal transaction history

### Admin/Supporter Features
- View all users' balance changes
- Access additional information:
  - User email and ID
  - Related order/transaction IDs
  - Related record type
  - Full transaction details
- Advanced search by:
  - User email
  - Related ID (Order ID, Transaction ID)
  - Action type
- Bulk actions (delete logs, clear all)

## Installation

### 1. Database Migration

Run the SQL migration to create the balance logs table:

```bash
mysql -u username -p database_name < database/balance-logs.sql
```

Or manually run the SQL:

```sql
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
```

### 2. Verify Installation

After installation, verify that:
- The `general_balance_logs` table exists in your database
- The Balance Logs menu item appears in the sidebar
- You can access the Balance Logs page

## Usage

### Accessing Balance Logs

#### For Users:
1. Login to your account
2. Click on "Balance Logs" in the sidebar (below Transaction Logs)
3. View your personal balance change history

#### For Admins:
1. Login as admin or supporter
2. Click on "Balance Logs" in the sidebar
3. View all users' balance changes with full details
4. Use search functionality to find specific records

### Action Types

The system tracks the following action types:

1. **Deduction** - Balance deducted when placing orders
   - Color: Red badge
   - Description includes order ID

2. **Addition** - Balance added from payments
   - Color: Green badge
   - Description includes transaction ID and payment method

3. **Refund** - Balance refunded from cancelled/partial orders
   - Color: Cyan badge
   - Description includes order ID

4. **Manual Add** - Balance manually added by admin
   - Color: Blue badge
   - Description includes admin note

5. **Manual Deduct** - Balance manually deducted by admin
   - Color: Orange badge
   - Description includes admin note

## Technical Details

### Module Structure

```
app/modules/balance_logs/
├── controllers/
│   └── balance_logs.php          # Main controller
├── models/
│   └── balance_logs_model.php    # Database model
└── views/
    ├── index.php                 # Main view
    └── ajax_search.php           # AJAX search view
```

### Helper Functions

The `balance_logs_helper.php` provides convenient functions for logging:

```php
// Log a balance change
log_balance_change($uid, $action_type, $amount, $balance_before, $balance_after, $description, $related_id, $related_type);

// Log order deduction
log_order_deduction($uid, $order_id, $amount, $balance_before, $balance_after);

// Log payment addition
log_payment_addition($uid, $transaction_id, $amount, $balance_before, $balance_after, $payment_method);

// Log refund
log_refund($uid, $order_id, $amount, $balance_before, $balance_after);

// Log manual funds
log_manual_funds($uid, $amount, $balance_before, $balance_after, $note, $transaction_id);
```

### Integration Points

The feature is integrated with:

1. **Order Module** (`app/modules/order/controllers/order.php`)
   - Logs balance deductions when orders are placed
   - Logs refunds when orders are cancelled/refunded

2. **Transaction Module** (`app/modules/transactions/controllers/transactions.php`)
   - Logs balance additions when payments are completed
   - Logs manual fund additions by admins

### Database Schema

**Table Name:** `general_balance_logs`

| Column | Type | Description |
|--------|------|-------------|
| id | int(11) | Primary key |
| ids | varchar(32) | Unique identifier |
| uid | int(11) | User ID |
| action_type | enum | Type of action (deduction, addition, refund, manual_add, manual_deduct) |
| amount | decimal(15,4) | Amount of change |
| balance_before | decimal(15,4) | Balance before change |
| balance_after | decimal(15,4) | Balance after change |
| description | text | Description of the change |
| related_id | varchar(100) | Related record ID (Order ID, Transaction ID, etc.) |
| related_type | varchar(50) | Type of related record (order, transaction, refund, manual) |
| created | datetime | Date and time of change |

## Multi-Currency Support

The Balance Logs feature is fully compatible with the multi-currency system:
- All amounts are converted to the user's selected currency
- Currency symbol is displayed according to user preference
- Conversion happens at display time, preserving original values

## Search Functionality

### For Users:
- Search through personal logs using the AJAX search box
- Searches in: description, related ID, action type

### For Admins:
- Advanced search with specific field selection:
  - **User Email** - Find logs for specific users
  - **Related ID** - Find logs by order ID or transaction ID
  - **Action Type** - Filter by action type
- Results are paginated for performance

## Permissions

- **Users**: Can view only their own balance logs
- **Admin/Supporter**: Can view all users' balance logs with full details
- **Admin Only**: Can delete logs and perform bulk actions

## API Access

The balance logs can be accessed programmatically through:
- Controller: `balance_logs/index`
- AJAX Search: `balance_logs/ajax_search`
- Advanced Search: `balance_logs/search`

## Troubleshooting

### Balance Logs Not Showing

1. **Check database table exists:**
   ```sql
   SHOW TABLES LIKE 'general_balance_logs';
   ```

2. **Verify constant is defined:**
   Check `app/config/constants.php` for `BALANCE_LOGS` constant

3. **Check helper is loaded:**
   The helper is auto-loaded in controllers using:
   ```php
   $this->load->helper('balance_logs');
   ```

### Menu Item Not Appearing

Check `app/modules/blocks/views/header.php` for the Balance Logs menu item:
```php
<a href="<?=cn('balance_logs')?>" class="nav-link <?=(segment(1) == 'balance_logs')?"active":""?>">
  <div class="nav-item" class="sidenavContent"><i class="fe fe-activity"></i><?=lang("Balance_Logs")?></div>
</a>
```

### Logs Not Being Created

Verify that the helper functions are being called in:
- Order placement (order/controllers/order.php)
- Transaction approval (transactions/controllers/transactions.php)
- Refunds (order/controllers/order.php)
- Manual funds (transactions/controllers/transactions.php)

## Future Enhancements

Potential improvements for future versions:
- Export balance logs to CSV/Excel
- Email notifications for balance changes
- Balance log statistics and charts
- Scheduled balance log cleanup
- Balance change alerts

## Support

For issues or questions:
1. Check this documentation
2. Review the code comments in the module files
3. Check database table structure
4. Verify permissions for the user role

## Changelog

### Version 1.0 (2025)
- Initial release
- User balance change tracking
- Admin detailed view
- Search functionality
- Integration with orders, transactions, and refunds
- Multi-currency support
