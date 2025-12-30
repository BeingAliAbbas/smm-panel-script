# IMAP Bounce Detection for Email Marketing Module

## Overview

The IMAP Bounce Detection feature automatically monitors your SMTP inbox for bounced emails and adds failed email addresses to a suppression list. This improves deliverability and maintains a clean mailing list.

## Features

- **Automatic Bounce Detection**: Connects to IMAP inbox and scans for bounce messages from mailer-daemon
- **Bounce Classification**: Categorizes bounces as Hard, Soft, or Complaint
- **Email Extraction**: Intelligently parses bounce messages to extract failed recipient email addresses
- **Suppression List**: Automatically adds bounced emails to suppression list
- **Manual Management**: Admin can manually add/remove emails from suppression list
- **Detailed Logging**: Maintains comprehensive logs of all detected bounces
- **Multi-SMTP Support**: Can monitor multiple SMTP/IMAP accounts
- **Cron Integration**: Runs automatically via cron job or manually from admin panel

## Installation

### 1. Database Migration

Run the SQL migration script to add necessary tables and fields:

```bash
mysql -u username -p database_name < database/email-marketing-imap-bounce.sql
```

This will:
- Add IMAP configuration fields to `email_smtp_configs` table
- Create `email_bounce_logs` table
- Create `email_suppression_list` table
- Add suppression tracking fields to existing tables

### 2. PHP IMAP Extension

Ensure PHP IMAP extension is installed and enabled:

```bash
# For Ubuntu/Debian
sudo apt-get install php-imap
sudo systemctl restart apache2

# For CentOS/RHEL
sudo yum install php-imap
sudo systemctl restart httpd

# Verify installation
php -m | grep imap
```

### 3. Cron Job Setup

Add this cron job to run bounce detection every 30 minutes:

```bash
*/30 * * * * curl -X GET "https://yourdomain.com/bounce_cron/run?token=YOUR_TOKEN" >/dev/null 2>&1
```

Replace:
- `yourdomain.com` with your actual domain
- `YOUR_TOKEN` with the token from system settings (auto-generated based on ENCRYPTION_KEY)

You can also set custom check intervals via settings.

## Configuration

### 1. Enable IMAP for SMTP Configuration

1. Navigate to **Email Marketing → SMTP Configurations**
2. Edit or create an SMTP configuration
3. Scroll down to **IMAP Bounce Detection Settings**
4. Check **Enable IMAP Bounce Detection**
5. Fill in IMAP details:

#### Gmail Example:
- **IMAP Host**: `imap.gmail.com`
- **IMAP Port**: `993`
- **IMAP Encryption**: `SSL`
- **IMAP Username**: Your Gmail address (or leave empty to use SMTP username)
- **IMAP Password**: App password (or leave empty to use SMTP password)

**Note**: For Gmail, you need to generate an App Password:
1. Go to Google Account Settings → Security
2. Enable 2-Step Verification
3. Generate App Password for "Mail"
4. Use the generated password

#### Outlook/Office 365 Example:
- **IMAP Host**: `outlook.office365.com`
- **IMAP Port**: `993`
- **IMAP Encryption**: `SSL`
- **IMAP Username**: Your email address
- **IMAP Password**: Your password or app password

### 2. Configure Settings

Navigate to **Email Marketing → Settings** to configure:

- **IMAP Check Interval**: How often cron checks for bounces (default: 30 minutes)
- **Auto Suppress Bounces**: Automatically add bounced emails to suppression list (default: enabled)
- **Max Emails Per Check**: Maximum bounce emails to process per run (default: 50)
- **Delete Processed Bounces**: Whether to delete bounce emails after processing (default: disabled)

## Usage

### Automatic Detection (Recommended)

Once configured, bounce detection runs automatically via cron job:

1. Cron connects to IMAP inbox
2. Searches for emails from `mailer-daemon` or `Mail Delivery Subsystem`
3. Parses bounce messages to extract recipient email addresses
4. Logs bounce details (type, reason, SMTP code)
5. Adds email to suppression list (if auto-suppress enabled)
6. Marks all pending campaign recipients with that email as suppressed

### Manual Detection

You can also run bounce detection manually:

1. Navigate to **Email Marketing → Bounce Logs**
2. Click **Run Bounce Detection Now**
3. Wait for processing to complete
4. Review results

### Suppression List Management

**View Suppression List:**
- Navigate to **Email Marketing → Suppression List**
- View all suppressed emails with reasons and statistics

**Add Email Manually:**
1. Click **Add Email** button
2. Enter email address
3. Select reason (Manual, Bounced, Invalid, Complaint, Unsubscribed)
4. Add optional notes
5. Click **Add to Suppression List**

**Remove from Suppression List:**
1. Find email in suppression list
2. Click **Remove** button
3. Confirm removal

## Bounce Types Detected

### Hard Bounce (Permanent Failure)
Emails that cannot be delivered permanently:
- User unknown / mailbox not found
- Address not found / does not exist
- Invalid recipient / address rejected
- SMTP codes: 550 5.1.1, 550 5.7.1

**Action**: Email is automatically suppressed and will not be sent to in future campaigns.

### Soft Bounce (Temporary Failure)
Temporary delivery issues:
- Mailbox full / inbox full
- Quota exceeded / over quota
- Out of storage space
- SMTP codes: 452 4.2.2, 452 4.3.1

**Action**: Email is logged. After multiple soft bounces, email may be suppressed.

### Complaint
Spam complaints or abuse reports:
- Marked as spam
- Abuse complaint
- Blocked

**Action**: Email is immediately suppressed to maintain sender reputation.

## Integration with Campaign Sending

The suppression list is automatically checked during email sending:

1. Before sending, the email address is checked against suppression list
2. If found, the email is skipped and marked as "bounced"
3. Campaign stats are updated accordingly
4. Email is never sent to suppressed addresses

This happens in **Step 0** of the email sending process (before validation and actual sending).

## Monitoring and Logs

### Bounce Logs
View detailed logs of all detected bounces:
- Navigate to **Email Marketing → Bounce Logs**
- See bounce type, reason, SMTP code, detection time
- View full bounce message details

### Statistics
Monitor bounce detection performance:
- Total bounces detected
- Hard vs soft bounce breakdown
- Suppressed emails count
- Bounce rate trends

### IMAP Connection Status
Check IMAP health in SMTP configuration:
- Last successful IMAP check timestamp
- Last IMAP connection error (if any)

## Troubleshooting

### IMAP Connection Failed

**Symptoms**: Error message "Failed to connect to IMAP server"

**Solutions**:
1. Verify IMAP credentials are correct
2. Check IMAP host and port
3. Ensure PHP IMAP extension is installed: `php -m | grep imap`
4. For Gmail: Use App Password instead of account password
5. Check firewall allows outbound IMAP connections (port 993)
6. Verify certificate validation (or disable with novalidate-cert flag)

### No Bounces Detected

**Possible Causes**:
1. No bounce emails in inbox
2. Bounce emails already marked as "Seen"
3. IMAP search criteria not matching
4. Bounce emails in different folder (not INBOX)

**Solutions**:
1. Send test email to invalid address and check inbox
2. Ensure bounce emails are unread
3. Check bounce emails are from "mailer-daemon" or "Mail Delivery Subsystem"

### Emails Still Sending to Suppressed Addresses

**Causes**:
1. Email added to suppression list after campaign started
2. Cache issue
3. Suppression check not running

**Solutions**:
1. Update campaign stats
2. Restart web server
3. Check email_cron.php includes suppression check (Step 0)

### Performance Issues

**Symptoms**: Slow bounce detection or cron timeouts

**Solutions**:
1. Reduce `imap_max_emails_per_check` setting (default 50)
2. Increase cron interval (e.g., every hour instead of 30 minutes)
3. Optimize database indexes
4. Delete old processed bounce logs

## API Endpoints

### Cron Endpoint
```
GET /bounce_cron/run?token=YOUR_TOKEN&smtp_id=SMTP_ID
```

Parameters:
- `token` (required): Security token
- `smtp_id` (optional): Specific SMTP config ID to check

Response:
```json
{
  "status": "success",
  "message": "Bounce detection completed. Checked 2 SMTP config(s), found 5 bounce(s), suppressed 3 email(s)",
  "smtp_checked": 2,
  "total_bounces": 5,
  "total_suppressed": 3,
  "results": [...],
  "time": "2024-01-15T10:30:00+00:00"
}
```

### Manual Run Endpoint
```
POST /email_marketing/ajax_run_bounce_detection
```

Response:
```json
{
  "status": "success",
  "message": "Bounce detection completed. Found 3 bounce(s), suppressed 2 email(s)",
  "results": [...]
}
```

## Best Practices

1. **Enable IMAP for All SMTP Accounts**: Monitor all sending accounts for bounces
2. **Use App Passwords**: For Gmail and Outlook, use app-specific passwords
3. **Set Appropriate Check Intervals**: 30 minutes is recommended for most use cases
4. **Review Bounce Logs Regularly**: Identify patterns and fix issues
5. **Clean Suppression List Periodically**: Remove old entries if needed
6. **Test Before Production**: Send test emails to verify bounce detection works
7. **Monitor IMAP Connection Status**: Check for connection errors
8. **Keep Suppression List Backed Up**: Export suppression list regularly

## Example Bounce Messages Detected

### Gmail Inbox Full
```
Subject: Delivery incomplete
From: Mail Delivery Subsystem <mailer-daemon@googlemail.com>

There was a temporary problem while delivering your message to fatdeeenahmad908@gmail.com.
Gmail will retry for 46 more hours.

The response was: 452 4.2.2 The recipient's inbox is out of storage space.
```

### Address Not Found
```
Subject: Address not found
From: Mail Delivery Subsystem <mailer-daemon@googlemail.com>

Your message wasn't delivered to custom6lucky@gmail.com because the address couldn't be found 
or is unable to receive email.
```

Both examples will be automatically detected, parsed, and the recipient email addresses will be added to the suppression list.

## Security Considerations

1. **IMAP Credentials**: Stored encrypted in database
2. **Token Authentication**: Cron endpoint requires security token
3. **Rate Limiting**: Prevents abuse of cron endpoint (5-minute minimum interval)
4. **Admin Only**: All management features require admin role
5. **No Email Deletion**: By default, emails are marked as read, not deleted

## Support

For issues or questions:
1. Check troubleshooting section above
2. Review bounce logs and IMAP connection status
3. Verify PHP IMAP extension is installed
4. Check system logs for error messages

## Changelog

### Version 1.0.0 (2024-01-15)
- Initial release
- IMAP bounce detection
- Automatic suppression list
- Hard/soft bounce classification
- Manual bounce detection trigger
- Suppression list management
- Detailed bounce logging
- Multi-SMTP support
