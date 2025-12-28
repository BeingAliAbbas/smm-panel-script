# Transactional Email Notifications Implementation

## Overview
This implementation adds comprehensive transactional email notifications to the SMM Panel system for orders, payments, and provider balance monitoring. These notifications are **independent** of the `email_marketing` module and use the existing PHPMailer infrastructure.

## Features Implemented

### 1. Order Notifications
- **New Order Email**: Sent to admin when a user places an order
  - Includes: Order ID, User Email, Service Name, Quantity, Total Charge
  - Trigger: `order/save_order()` method

- **Order Error Email**: Sent to admin when an order encounters an error
  - Includes: Order ID, User Email, Service Name, Error Message
  - Trigger: `api_provider/cron_place_orders()` when API errors occur

### 2. Payment Notifications
- **Payment Submitted Email**: Sent to admin when a user submits a payment
  - Includes: Transaction ID, User Email, Amount, Payment Method
  - Trigger: Payment controllers (easypaisa, jazzcash, etc.) on pending status

- **Payment Approved Email**: Sent to user when admin approves payment
  - Includes: Transaction ID, Amount Credited, Payment Method
  - Trigger: `transactions/ajax_update()` when status changes from pending to paid

### 3. Provider Balance Monitoring
- **Low Balance Alert**: Sent to admin when provider balance falls below threshold
  - Includes: Provider Name, Current Balance, Threshold Value
  - Trigger: `/cron/check_provider_balance` cron job
  - **Cooldown**: Maximum one email per provider per 24 hours to prevent spam

## Configuration

### Admin Settings Panel
Access: **Admin Panel → Settings → Email Notifications**

#### Available Settings:
1. **Admin Notification Email**: Primary email for receiving notifications (defaults to admin account email)
2. **Enable/Disable Toggles** for each notification type:
   - New Order Email
   - Order Error Email
   - Payment Submitted Email
   - Payment Approved Email
   - Low Provider Balance Alert
3. **Provider Balance Threshold**: Configurable minimum balance before alerts are sent (default: 100)

#### Test Email Feature
- Button to send a test email to verify configuration
- Endpoint: `setting/ajax_send_test_email`

## Technical Implementation

### Core Library
**File**: `app/libraries/Transactional_email.php`

Key Methods:
- `is_enabled($notification_type)`: Check if a notification type is enabled
- `get_admin_email()`: Get the configured admin email
- `send_new_order_email()`: Send new order notification
- `send_order_error_email()`: Send order error notification
- `send_payment_submitted_email()`: Send payment submission notification
- `send_payment_approved_email()`: Send payment approval notification
- `send_low_balance_alert()`: Send provider low balance alert

### Cron Job
**File**: `app/controllers/Provider_balance_cron.php`
**Route**: `/cron/check_provider_balance`

This cron job should be scheduled to run periodically (recommended: every 4-6 hours) to check provider balances.

#### Setup Cron Job:
Add to your server's crontab:
```bash
# Run every 4 hours
0 */4 * * * /usr/bin/php /path/to/your/panel/index.php cron check_provider_balance
```

Or use wget/curl:
```bash
0 */4 * * * wget -q -O /dev/null https://yourpanel.com/cron/check_provider_balance
```

### Modified Files
1. **app/modules/order/controllers/order.php**
   - Added new order email notification in `save_order()` method

2. **app/modules/api_provider/controllers/api_provider.php**
   - Added order error email notification in `cron_place_orders()` method
   - Added helper method `send_order_error_notification()`

3. **app/modules/transactions/controllers/transactions.php**
   - Added payment approved email in `ajax_update()` method

4. **app/modules/add_funds/controllers/easypaisa.php**
   - Added payment submitted email for pending payments

5. **app/modules/add_funds/controllers/jazzcash.php**
   - Added payment submitted email for pending payments

6. **app/modules/setting/controllers/setting.php**
   - Added `ajax_send_test_email()` method for test email functionality

7. **app/modules/setting/views/email_notifications.php**
   - New settings UI view

8. **app/language/english/common_lang.php**
   - Added language strings for new settings

## Database Storage
Settings are stored in the `general_options` table:

| Option Name | Description | Default Value |
|------------|-------------|---------------|
| `admin_notification_email` | Primary admin email | Empty (uses admin account) |
| `enable_email_new_order` | Enable new order emails | 0 (disabled) |
| `enable_email_order_error` | Enable order error emails | 0 (disabled) |
| `enable_email_payment_submitted` | Enable payment submission emails | 0 (disabled) |
| `enable_email_payment_approved` | Enable payment approval emails | 0 (disabled) |
| `enable_email_low_provider_balance` | Enable low balance alerts | 0 (disabled) |
| `provider_balance_threshold` | Minimum balance before alert | 100 |
| `low_balance_last_sent_{provider_id}` | Last alert timestamp per provider | 0 |

## Email Templates
All emails use the existing PHPMailer template (`app/libraries/PHPMailer/template.php`) and support the following merge fields:
- `{{website_name}}`
- `{{website_logo}}`
- `{{website_link}}`
- `{{copyright}}`
- `{{email_content}}`

## Error Handling
- All email send operations are wrapped in try-catch blocks
- Email failures are logged silently using `error_log()` and CI's `log_message()`
- Email failures **do not block** core operations (order placement, payment processing, etc.)
- If SMTP is not configured, emails will fail gracefully without impacting functionality

## Prerequisites
- SMTP must be configured in **Settings → SMTP Settings** for emails to be sent
- Admin email must be configured in **Settings → Email Notifications** (or will use default admin account email)

## Testing

### Test Email Feature
1. Navigate to **Settings → Email Notifications**
2. Enter an admin email address
3. Click **Send Test Email** button
4. Check inbox for test email

### Test Individual Notifications
1. **New Order**: Enable the setting and place a test order
2. **Order Error**: Monitor cron logs for API provider errors
3. **Payment Submitted**: Submit a manual payment request (EasyPaisa, JazzCash, etc.)
4. **Payment Approved**: Approve a pending payment in Transactions
5. **Low Balance**: Set threshold high and run `/cron/check_provider_balance`

## Maintenance

### Monitoring
- Check `app/logs/` directory for email send errors
- Review cron logs in `cron_logs` table for provider balance checks

### Troubleshooting
1. **Emails not sending**: 
   - Verify SMTP settings in Settings → SMTP Settings
   - Check server logs: `app/logs/log-YYYY-MM-DD.php`
   - Run test email from Email Notifications settings

2. **Provider balance alerts not working**:
   - Verify cron job is running: Check `cron_logs` table
   - Ensure providers have balance data populated
   - Check cooldown - only 1 email per 24 hours per provider

3. **Wrong admin email**:
   - Update admin notification email in Settings → Email Notifications
   - Clear any cached options

## Future Enhancements
The modular design allows for easy addition of new notification types:
1. Add new method to `Transactional_email` library
2. Add trigger point in relevant controller
3. Add enable/disable toggle in settings UI
4. Add language strings

## Integration with Email Marketing Module
**Important**: This implementation is **completely independent** of the `email_marketing` module. Both can coexist without conflicts:
- Transactional emails use `Transactional_email` library
- Marketing emails use `email_marketing` module
- Different settings tables
- Different trigger mechanisms
- No shared code

## Security Considerations
- All email content is escaped properly using `htmlspecialchars()`
- Email sending is non-blocking and won't expose sensitive data
- Failed email attempts are logged for debugging but don't expose credentials
- Test email feature requires admin access only
