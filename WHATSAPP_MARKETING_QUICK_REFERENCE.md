# WhatsApp Marketing - Quick Reference Guide

## Quick Start (5 Steps)

1. **Import Database**
   ```bash
   mysql -u username -p database_name < database/whatsapp-marketing.sql
   ```

2. **Add API Configuration**
   - Admin Panel → WhatsApp Marketing → API Configuration → Add
   - Enter: Name, API Key, Endpoint (default: http://waapi.beastsmm.pk/send-message)

3. **Create Campaign**
   - WhatsApp Marketing → Campaigns → Click "+"
   - Enter: Name, Message (with placeholders), Select API Config

4. **Add Recipients**
   - Campaign Actions → Recipients → Import from Database or Upload CSV

5. **Start & Setup Cron**
   - Campaign Actions → Start Sending
   - Add to crontab: `* * * * * curl "http://yoursite.com/whatsapp_cron/run?token=TOKEN&campaign_id=ID"`

## Message Placeholders

| Placeholder | Description | Example |
|------------|-------------|---------|
| `{username}` | User's full name | John Doe |
| `{phone}` | Phone number | 923001234567 |
| `{balance}` | Account balance | 100.00 |
| `{email}` | Email address | user@example.com |

## Campaign Status Flow

```
Pending → Start → Running → Completed
                    ↓
                  Pause → Resume → Running
```

## API Request Format

```json
POST http://waapi.beastsmm.pk/send-message
Content-Type: application/json

{
  "apiKey": "YOUR_API_KEY",
  "phoneNumber": "923XXXXXXXXX",
  "message": "Hello {username}!"
}
```

**Note:** Phone numbers are sent WITHOUT the `+` symbol

## CSV Import Format

```csv
phone_number,name
923001234567,John Doe
923001234568,Jane Smith
923009999999,Test User
```

**Alternative (phone only):**
```
923001234567
923001234568
923009999999
```

## Cron Setup Examples

**Campaign-Specific (Recommended):**
```bash
# Every minute
* * * * * curl "http://yoursite.com/whatsapp_cron/run?token=abc123&campaign_id=xyz789"

# Every 5 minutes
*/5 * * * * curl "http://yoursite.com/whatsapp_cron/run?token=abc123&campaign_id=xyz789"

# Every hour
0 * * * * curl "http://yoursite.com/whatsapp_cron/run?token=abc123&campaign_id=xyz789"
```

**All Campaigns:**
```bash
* * * * * curl "http://yoursite.com/whatsapp_cron/run?token=abc123"
```

## Common Commands

**View Module Files:**
```bash
ls -la app/modules/whatsapp_marketing/
```

**Test Module Installation:**
```bash
./test-whatsapp-module.sh
```

**Check PHP Syntax:**
```bash
php -l app/modules/whatsapp_marketing/controllers/Whatsapp_marketing.php
```

**View Database Tables:**
```sql
SHOW TABLES LIKE 'whatsapp_%';
```

**Count Campaign Recipients:**
```sql
SELECT campaign_id, COUNT(*) as total 
FROM whatsapp_recipients 
GROUP BY campaign_id;
```

**View Campaign Statistics:**
```sql
SELECT 
  name, 
  status,
  total_messages,
  sent_messages,
  failed_messages,
  delivered_messages
FROM whatsapp_campaigns;
```

## Troubleshooting Quick Fixes

**Campaign not sending?**
```bash
# 1. Check campaign status
# Admin → WhatsApp Marketing → Campaigns → Verify status is "Running"

# 2. Test cron manually
curl "http://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"

# 3. Check logs
# Admin → WhatsApp Marketing → Campaign → View Logs
```

**No recipients showing?**
```sql
-- Check if users have WhatsApp numbers
SELECT COUNT(*) FROM general_users WHERE whatsapp_number IS NOT NULL AND whatsapp_number != '';
```

**API errors?**
```bash
# Test API directly
curl -X POST http://waapi.beastsmm.pk/send-message \
  -H "Content-Type: application/json" \
  -d '{"apiKey":"YOUR_KEY","phoneNumber":"923001234567","message":"Test"}'
```

## File Locations

| Component | Location |
|-----------|----------|
| Controller | `app/modules/whatsapp_marketing/controllers/Whatsapp_marketing.php` |
| Model | `app/modules/whatsapp_marketing/models/Whatsapp_marketing_model.php` |
| Cron | `app/controllers/Whatsapp_cron.php` |
| Views | `app/modules/whatsapp_marketing/views/` |
| Routes | `app/config/routes.php` (lines with whatsapp_cron) |
| Database | `database/whatsapp-marketing.sql` |
| Docs | `WHATSAPP_MARKETING_README.md` |
| Test | `test-whatsapp-module.sh` |

## Module URLs

| Page | URL |
|------|-----|
| Dashboard | `/whatsapp_marketing` |
| Campaigns | `/whatsapp_marketing/campaigns` |
| API Configs | `/whatsapp_marketing/api_configs` |
| Recipients | `/whatsapp_marketing/recipients/{campaign_id}` |
| Logs | `/whatsapp_marketing/logs/{campaign_id}` |
| Cron | `/whatsapp_cron/run?token=TOKEN&campaign_id=ID` |

## Rate Limits

| Limit Type | Default | Configurable |
|------------|---------|-------------|
| Hourly | 100 | Yes (per campaign) |
| Daily | 1000 | Yes (per campaign) |
| Cron Interval | 60 sec | No (hardcoded) |
| Messages per Cron | 1 | No (hardcoded) |

## Security Checklist

- [x] Admin-only access
- [x] Cron token authentication
- [x] SQL injection protection (Query Builder)
- [x] XSS protection (htmlspecialchars)
- [x] CSRF protection (framework)
- [x] Input validation
- [x] Phone number sanitization
- [x] File upload validation (CSV/TXT only)
- [x] Rate limiting

## Support Resources

- Full Documentation: `WHATSAPP_MARKETING_README.md`
- Test Script: `./test-whatsapp-module.sh`
- Database Schema: `database/whatsapp-marketing.sql`

## Version

**WhatsApp Marketing Module v1.0.0**
- Initial release with full campaign management
- Cron-based sending with isolated per-campaign jobs
- Database and CSV/TXT import
- Message placeholders support
- Statistics and detailed logging
