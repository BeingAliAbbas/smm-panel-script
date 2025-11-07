# Balance Logs - Quick Reference

## Installation (One-Time Setup)

```bash
# Run this SQL command in your database
mysql -u username -p database_name < database/balance-logs.sql
```

## Access

**User View:**
- Login → Sidebar → "Balance Logs"
- Shows only personal balance changes

**Admin View:**
- Login as Admin → Sidebar → "Balance Logs"
- Shows all users' balance changes with full details

## What Gets Logged

| Event | Action Type | Description |
|-------|-------------|-------------|
| User places order | `deduction` | Balance deducted (red) |
| Payment approved | `addition` | Balance added (green) |
| Order refunded | `refund` | Balance refunded (cyan) |
| Admin adds funds | `manual_add` | Manual addition (blue) |
| Admin removes funds | `manual_deduct` | Manual deduction (orange) |

## Admin Search Options

1. **User Email** - Find logs for specific user
2. **Related ID** - Search by order/transaction ID
3. **Action Type** - Filter by action type

## View Details

### User View Shows:
- Action type
- Amount (+ or -)
- Balance before/after
- Description
- Date & time

### Admin View Shows (Additional):
- User name/email/ID
- Related order/transaction ID
- Related record type
- All user view fields

## Tips

✓ All amounts automatically convert to user's selected currency
✓ Search is case-insensitive
✓ Results are paginated (default: 10 per page)
✓ Admin can delete individual logs or clear all
✓ Color-coded badges indicate action types

## Common Use Cases

**Track user spending:**
```
Search Type: User Email
Query: user@example.com
```

**Find order-related changes:**
```
Search Type: Related ID
Query: [order_id]
```

**View all refunds:**
```
Search Type: Action Type
Query: refund
```

## Troubleshooting

**Logs not showing?**
1. Check database table exists: `general_balance_logs`
2. Verify constant in `app/config/constants.php`
3. Check file permissions

**Menu item missing?**
1. Clear browser cache
2. Check `app/modules/blocks/views/header.php`
3. Refresh page

## Integration Status

✓ Orders module - Deductions logged
✓ Transactions module - Payments logged
✓ Refunds - Refunds logged
✓ Manual funds - Admin actions logged

## File Locations

```
Database:
  database/balance-logs.sql

Module:
  app/modules/balance_logs/
    ├── controllers/balance_logs.php
    ├── models/balance_logs_model.php
    └── views/
        ├── index.php
        └── ajax_search.php

Helper:
  app/helpers/balance_logs_helper.php

Config:
  app/config/constants.php (BALANCE_LOGS constant)

Language:
  app/language/english/common_lang.php

Navigation:
  app/modules/blocks/views/header.php
```

## Support

For detailed documentation, see `BALANCE_LOGS_GUIDE.md`
