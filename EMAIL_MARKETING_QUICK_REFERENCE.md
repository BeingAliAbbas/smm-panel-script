# Email Marketing Module - Quick Reference

## Quick Access URLs

- **Dashboard**: `/email_marketing`
- **Campaigns**: `/email_marketing/campaigns`
- **Templates**: `/email_marketing/templates`
- **SMTP Config**: `/email_marketing/smtp`
- **Reports**: `/email_marketing/reports`
- **Cron URL**: `/cron/email_marketing?token=YOUR_TOKEN`

## Quick Start (5 Steps)

### 1. Install Database
```bash
mysql -u username -p database_name < database/email-marketing.sql
```

### 2. Configure SMTP
- Go to: Email Marketing > SMTP Config
- Click: Add New
- Fill in your SMTP details
- Set as default

### 3. Create Template
- Go to: Email Marketing > Templates
- Click: Add New
- Use variables: {username}, {email}, {balance}
- Save

### 4. Create Campaign
- Go to: Email Marketing > Campaigns
- Click: Add New
- Select template & SMTP
- Add recipients
- Click: Start Sending

### 5. Setup Cron
```cron
* * * * * curl "https://yoursite.com/cron/email_marketing?token=TOKEN" >/dev/null 2>&1
```

## Common Variables

```
{username}      - User's name
{email}         - User's email
{balance}       - Account balance
{site_name}     - Website name
{site_url}      - Website URL
{current_date}  - Today's date
{current_year}  - Current year
```

## Campaign Statuses

- **Pending**: Created, not started
- **Running**: Actively sending emails
- **Paused**: Temporarily stopped
- **Completed**: All emails sent
- **Cancelled**: Manually cancelled

## Recipient Statuses

- **Pending**: Waiting to be sent
- **Sent**: Successfully sent
- **Opened**: Email was opened
- **Failed**: Send failed
- **Bounced**: Email bounced

## CSV Import Format

```csv
email,name
user1@example.com,John Doe
user2@example.com,Jane Smith
user3@example.com,Bob Johnson
```

## SMTP Quick Config

### Gmail
```
Host: smtp.gmail.com
Port: 587
Encryption: TLS
```

### SendGrid
```
Host: smtp.sendgrid.net
Port: 587
Encryption: TLS
Username: apikey
Password: YOUR_API_KEY
```

### Mailgun
```
Host: smtp.mailgun.org
Port: 587
Encryption: TLS
```

## Troubleshooting

### Emails not sending?
1. Check campaign status is "Running"
2. Verify cron job is configured
3. Check SMTP settings
4. Review error logs in campaign details

### Low open rates?
1. Improve subject line
2. Send at optimal times
3. Clean recipient list
4. Personalize content

### High failure rate?
1. Validate SMTP credentials
2. Check email format
3. Review sending limits
4. Verify recipient emails

## Security Checklist

- ✅ Admin-only access
- ✅ CSRF protection enabled
- ✅ Secure cron token
- ✅ Rate limiting active
- ✅ Input validation

## Performance Tips

1. **Set Sending Limits**: Prevent throttling
2. **Clean Lists**: Remove invalid emails
3. **Monitor Stats**: Track open/failure rates
4. **Test First**: Small test campaign before bulk
5. **Optimize Timing**: Send when users are active

## Support

- Check: `EMAIL_MARKETING_README.md` for full documentation
- Review: Campaign activity logs for errors
- Test: Cron URL in browser to verify it works
- Export: Campaign reports for analysis

---

**Need Help?** See EMAIL_MARKETING_README.md for detailed documentation.
