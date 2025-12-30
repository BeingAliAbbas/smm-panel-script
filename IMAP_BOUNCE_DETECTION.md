# IMAP Bounce Detection for Email Marketing Module

This enhancement adds automatic bounce detection to the email marketing module using IMAP inbox monitoring. It helps maintain a clean mailing list by automatically detecting and suppressing bounced, invalid, and failed email addresses.

## Features

### 1. IMAP Configuration
- Configure IMAP settings for each SMTP account
- Support for Gmail, Outlook, and other IMAP providers
- SSL/TLS encryption support
- Test IMAP connection before saving

### 2. Automatic Bounce Detection
- Monitors SMTP inboxes via IMAP
- Scans incoming emails from mailer-daemon
- Parses bounce messages to extract failed email addresses
- Identifies bounce types:
  - **Hard Bounces**: Permanent failures (invalid address, mailbox not found)
  - **Soft Bounces**: Temporary failures (mailbox full, temporary unavailability)
  - **Invalid Emails**: Address format or validation issues
  - **Spam Complaints**: Marked as spam by recipient

### 3. Suppression List Management
- Automatically adds bounced emails to suppression list
- Prevents sending to suppressed addresses in future campaigns
- Supports temporary suppression with expiry
- Manual removal from suppression list
- Tracks bounce history and retry count

### 4. Execution Modes
- **Automatic**: Via cron job (recommended)
- **Manual**: From admin panel with one click
- Rate limiting to prevent overload

### 5. Logging & Auditing
- Detailed bounce detection logs
- Bounce activity tracking
- Statistics dashboard

## Installation

### 1. Database Setup

Run the SQL migration to add required tables and fields:

```bash
mysql -u your_user -p your_database < database/email-marketing-imap-bounce.sql
```

This will:
- Add IMAP configuration fields to `email_smtp_configs` table
- Create `email_bounces` table for suppression tracking
- Create `email_bounce_logs` table for activity logs
- Add suppression fields to `email_recipients` table
- Insert default bounce detection settings

### 2. Configure IMAP for SMTP Accounts

1. Navigate to **Email Marketing > SMTP Configurations**
2. Edit an existing SMTP config or create a new one
3. Scroll to **IMAP Configuration** section
4. Enable IMAP bounce detection
5. Fill in IMAP details:
   - **Host**: e.g., `imap.gmail.com`
   - **Port**: Usually `993` for SSL
   - **Encryption**: Choose SSL (recommended) or TLS
   - **Username**: Usually same as SMTP username
   - **Password**: Use the same password or app-specific password

6. Click **Test IMAP Connection** to verify settings
7. Save the configuration

### 3. Setup Cron Job

Add the following cron job to run bounce detection automatically:

```bash
# Run every 30 minutes
*/30 * * * * wget --spider -q -O - "https://yourdomain.com/cron/email_bounce?token=YOUR_TOKEN" >/dev/null 2>&1
```

Replace:
- `yourdomain.com` with your actual domain
- `YOUR_TOKEN` with your email bounce cron token (found in settings or generate using: `md5('email_bounce_cron_' . ENCRYPTION_KEY)`)

## Configuration

### Bounce Detection Settings

Navigate to **Email Marketing > Settings** to configure:

- **Enable Bounce Detection**: Master toggle
- **Hard Bounce Suppression**: Keep permanently suppressed
- **Soft Bounce Retry Count**: Number of retries before permanent suppression
- **Soft Bounce Retry Hours**: Hours to wait before removing temporary suppression
- **Check Interval**: Minimum minutes between bounce detection runs
- **Time Window**: Hours to look back for bounce emails
- **Max Emails per Check**: Limit processing per run

## Usage

### Viewing Bounce List

1. Navigate to **Email Marketing > Bounces**
2. View statistics:
   - Total suppressed emails
   - Hard bounces
   - Soft bounces
   - Invalid emails
3. Browse the suppression list with details:
   - Email address
   - Bounce type and reason
   - SMTP code
   - Retry count
   - Last bounce time

### Manual Bounce Detection

1. Navigate to **Email Marketing > Bounces**
2. Click **Run Bounce Detection** button
3. Wait for the process to complete
4. Review newly detected bounces

### Removing from Suppression List

1. Navigate to **Email Marketing > Bounces**
2. Find the email you want to remove
3. Click the green checkmark button
4. Confirm the removal
5. The email will be available for future campaigns

## How It Works

### Bounce Detection Flow

1. **IMAP Connection**: Connects to configured SMTP inbox
2. **Email Scanning**: Searches for emails from mailer-daemon within time window
3. **Bounce Parsing**: Extracts recipient emails and bounce details
4. **Classification**: Determines bounce type (hard/soft/invalid)
5. **Suppression**: Adds to suppression list with appropriate expiry
6. **Campaign Integration**: Updates pending recipients to skip suppressed emails

### Email Sending Integration

When adding recipients to campaigns:
- System checks if email is in suppression list
- Suppressed emails are automatically skipped
- Temporary bounces past expiry are re-enabled

When sending emails:
- System fetches next pending recipient
- Automatically skips suppressed emails
- Ensures clean sending list

## Bounce Message Examples

### Hard Bounce Example
```
Subject: Delivery Status Notification (Failure)
From: mailer-daemon@googlemail.com

Address not found
Your message wasn't delivered to user@example.com because 
the address couldn't be found or is unable to receive email.
Error code: 550
```

### Soft Bounce Example
```
Subject: Delivery incomplete
From: mailer-daemon@googlemail.com

The recipient's inbox is out of storage space.
Error code: 452 4.2.2
Will retry for 23 more hours.
```

## Gmail Setup Guide

### For Gmail SMTP/IMAP:

1. **Enable 2-Factor Authentication** on your Google account
2. **Generate App Password**:
   - Go to Google Account > Security
   - Select "App passwords"
   - Choose "Mail" and "Other"
   - Generate password
   - Use this password for both SMTP and IMAP

3. **SMTP Configuration**:
   - Host: `smtp.gmail.com`
   - Port: `587` (TLS) or `465` (SSL)
   - Username: Your Gmail address
   - Password: App password

4. **IMAP Configuration**:
   - Host: `imap.gmail.com`
   - Port: `993`
   - Encryption: SSL
   - Username: Your Gmail address
   - Password: App password (same as SMTP)

## Troubleshooting

### IMAP Connection Fails

**Error**: "IMAP connection failed"

**Solutions**:
- Verify IMAP host and port are correct
- Check if IMAP is enabled in your email account settings
- For Gmail: Use app-specific password, not regular password
- Check firewall/security settings allow IMAP connections
- Verify encryption type (SSL/TLS) matches server requirements

### No Bounces Detected

**Possible Causes**:
- No bounce emails in inbox within time window
- Bounce emails already processed and moved to processed folder
- IMAP connection not established
- Time window too short

**Solutions**:
- Check IMAP connection test succeeds
- Increase time window setting
- Verify bounce emails are in inbox
- Check bounce detection logs

### Bounces Not Suppressing Recipients

**Possible Causes**:
- Recipients added before bounce was detected
- Suppression check not implemented
- Database not updated

**Solutions**:
- Re-run bounce detection manually
- Check `email_bounces` table has entries
- Verify `is_suppressed` field in `email_recipients`

## Security Considerations

1. **Token Protection**: Keep your cron token secure
2. **Password Storage**: IMAP passwords stored encrypted in database
3. **Rate Limiting**: Prevents abuse of manual bounce detection
4. **Access Control**: Only admin users can manage bounces
5. **Email Privacy**: Bounce messages stored with limited data

## Performance Tips

1. **Adjust Time Window**: Shorter windows process faster
2. **Limit Max Emails**: Prevent long-running processes
3. **Schedule Wisely**: Run during off-peak hours
4. **Multiple SMTP Configs**: Distribute load across accounts

## API Integration

### Cron Endpoint

```
GET /cron/email_bounce?token=YOUR_TOKEN
```

**Response**:
```json
{
  "status": "success",
  "message": "Bounce detection completed",
  "duration_ms": 1234.56,
  "statistics": {
    "total_suppressed": 50,
    "hard_bounces": 30,
    "soft_bounces": 15,
    "recent_24h": 5
  },
  "time": "2025-12-29T20:30:00Z"
}
```

## Support

For issues or questions:
1. Check application logs: `app/logs/email_bounce_detector.log`
2. Review bounce detection logs in admin panel
3. Verify database schema is up to date
4. Test IMAP connection in SMTP configuration

## Future Enhancements

Potential improvements:
- Webhook notifications for bounces
- Advanced bounce analytics
- Automatic re-validation of soft bounces
- Integration with email validation APIs
- Bulk import/export of suppression list
- Custom bounce rules and filters
