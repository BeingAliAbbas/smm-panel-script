# Email Marketing Management System

## Overview

Complete email marketing management module for SMM Panel Script. Provides full control over campaigns, scheduling, templates, configurations, and tracking.

## Features

### 1. Campaign Management
- Create, edit, and delete email campaigns
- Campaign statistics: total emails, sent, remaining, failed, opened
- Campaign status: Pending, Running, Completed, Paused
- Pause, resume, or delete campaigns
- Real-time progress tracking

### 2. Email Sending via Cron
- Automatic email sending through cron jobs
- One email sent per cron execution
- Configurable sending limits (hourly/daily)
- Clean informational messages when no active campaigns
- Start/stop controls from admin panel

### 3. Template Management
- Create, edit, and delete email templates
- Support for variables/placeholders:
  - `{username}` - User's name
  - `{email}` - User's email
  - `{balance}` - User's balance
  - `{site_name}` - Website name
  - `{site_url}` - Website URL
  - `{current_date}` - Current date
  - `{current_year}` - Current year
- HTML template support

### 4. Recipient Management
- Import from user database
- CSV/TXT file import support
- Email validation and duplicate removal
- Recipient status tracking

### 5. SMTP Configuration
- Multiple SMTP profile support
- Full SMTP settings:
  - Host, Port, Username, Password
  - Encryption (None, SSL, TLS)
  - From Name/Email
  - Reply-To address
- Set default SMTP configuration
- Enable/disable SMTP profiles

### 6. Tracking & Reporting
- Campaign statistics dashboard
- Email open tracking (via pixel)
- Failed delivery tracking
- Detailed activity logs with timestamps
- CSV export for campaign reports

## Installation

### 1. Database Setup

Run the SQL migration script:

```bash
mysql -u username -p database_name < database/email-marketing.sql
```

This creates the following tables:
- `email_campaigns`
- `email_templates`
- `email_smtp_configs`
- `email_recipients`
- `email_logs`
- `email_settings`

### 2. File Structure

The module is located at:
```
app/modules/email_marketing/
├── controllers/
│   └── Email_marketing.php
├── models/
│   └── Email_marketing_model.php
└── views/
    ├── index.php
    ├── campaigns/
    │   ├── index.php
    │   ├── create.php
    │   ├── edit.php
    │   └── details.php
    ├── templates/
    │   ├── index.php
    │   ├── create.php
    │   └── edit.php
    ├── smtp/
    │   ├── index.php
    │   ├── create.php
    │   └── edit.php
    ├── recipients/
    │   └── index.php
    └── reports/
        └── index.php
```

The cron controller is at:
```
app/controllers/Email_cron.php
```

### 3. Cron Job Setup

Add this cron job to your server (crontab -e):

```cron
* * * * * curl "https://yoursite.com/cron/email_marketing?token=YOUR_TOKEN" >/dev/null 2>&1
```

**Getting Your Token:**
1. The token is automatically generated and stored in settings
2. Default token: MD5 hash of 'email_marketing_cron_' + your ENCRYPTION_KEY
3. You can view the cron URL in the Email Marketing dashboard

### 4. Access the Module

Navigate to: `https://yoursite.com/email_marketing`

(Admin access required)

## Usage Guide

### Creating Your First Campaign

1. **Set up SMTP Configuration**
   - Go to Email Marketing > SMTP Config
   - Click "Add New"
   - Enter your SMTP details
   - Set as default if desired

2. **Create Email Template**
   - Go to Email Marketing > Templates
   - Click "Add New"
   - Enter template name and subject
   - Design your HTML email body
   - Use variables like {username}, {email}, etc.
   - Save template

3. **Create Campaign**
   - Go to Email Marketing > Campaigns
   - Click "Add New"
   - Enter campaign name
   - Select template and SMTP config
   - Set sending limits (optional)
   - Save campaign

4. **Add Recipients**
   - Open the campaign
   - Click "Manage Recipients"
   - Import from database or upload CSV file
   - CSV format: `email,name` (one per line)

5. **Start Campaign**
   - Return to campaign list
   - Click "Start" button
   - Cron will begin sending emails automatically

### Monitoring Campaigns

- **Dashboard**: Overview of all statistics
- **Campaign Details**: View progress, recipients, and logs
- **Export Reports**: Download CSV with all recipient data

## Email Template Variables

Available variables for use in email templates:

| Variable | Description |
|----------|-------------|
| `{username}` | Recipient's name |
| `{email}` | Recipient's email address |
| `{balance}` | User's account balance |
| `{site_name}` | Your website name |
| `{site_url}` | Your website URL |
| `{current_date}` | Current date (Y-m-d format) |
| `{current_year}` | Current year |

## SMTP Configuration Examples

### Gmail

```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: your-app-password
```

### SendGrid

```
Host: smtp.sendgrid.net
Port: 587
Encryption: TLS
Username: apikey
Password: your-sendgrid-api-key
```

### Mailgun

```
Host: smtp.mailgun.org
Port: 587
Encryption: TLS
Username: your-mailgun-smtp-username
Password: your-mailgun-smtp-password
```

## Security

- Admin-only access control
- CSRF protection on all forms
- Input sanitization and validation
- Secure token-based cron access
- Rate limiting on cron execution

## Sending Limits

### Purpose
Prevent email server throttling and improve deliverability

### Configuration
- **Hourly Limit**: Maximum emails per hour per campaign
- **Daily Limit**: Maximum emails per day per campaign
- Leave empty for no limit

### How It Works
- Cron sends one email per execution
- Checks limits before sending
- Skips campaign if limits reached
- Continues next hour/day when limits reset

## Troubleshooting

### Emails Not Sending

1. **Check Campaign Status**
   - Must be "Running" (click Start button)

2. **Verify Cron Job**
   - Ensure cron is configured correctly
   - Test cron URL in browser with token

3. **Check SMTP Configuration**
   - Verify SMTP settings are correct
   - Ensure SMTP profile is active
   - Test with a simple email client

4. **Review Error Logs**
   - Check campaign details for failed emails
   - Review activity log for error messages

### Low Open Rates

1. **Subject Line**: Make it compelling
2. **From Name**: Use recognizable sender
3. **Email Content**: Provide value to recipients
4. **Sending Time**: Test different times of day
5. **List Quality**: Remove inactive subscribers

### High Bounce Rate

1. **Validate Emails**: Use email validation before importing
2. **Clean Lists**: Remove invalid addresses
3. **Check SMTP**: Ensure proper authentication
4. **Reputation**: Build sender reputation gradually

## API Endpoints

### Cron Endpoint
```
GET /cron/email_marketing?token=YOUR_TOKEN
```

Returns JSON with sending status and statistics.

### Tracking Pixel
```
GET /email_marketing/track/{tracking_token}
```

Transparent 1x1 pixel for open tracking (public endpoint).

## Database Schema

### email_campaigns
- Campaign information and statistics
- Status, limits, timestamps

### email_templates
- Email template content
- Subject, body, variables

### email_smtp_configs
- SMTP server configurations
- Credentials, settings

### email_recipients
- Campaign recipients
- Status, tracking tokens

### email_logs
- Detailed activity logs
- Sent/failed records, errors

### email_settings
- Global system settings
- Limits, tracking options

## Support

For issues or questions:
1. Check this documentation
2. Review error messages in logs
3. Verify configuration settings
4. Test with a small campaign first

## Version

Version: 1.0  
Compatibility: SMM Panel Script v2.x+  
License: Same as main application

---

**Ready to send emails! 🚀**
