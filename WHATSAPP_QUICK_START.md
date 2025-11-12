# WhatsApp Marketing System - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Install Database (30 seconds)
```bash
mysql -u your_username -p your_database < database/whatsapp-marketing.sql
```
Or import via phpMyAdmin.

### Step 2: Test API (30 seconds)
```bash
# Edit test-whatsapp-api.php with your API key
php test-whatsapp-api.php
```
Expected: ✅ SUCCESS! Message sent successfully.

### Step 3: Configure in Panel (1 minute)
1. Login to admin panel
2. Go to: **WhatsApp Marketing > API Configuration**
3. Click **Add API Config**
4. Enter:
   - Name: My WhatsApp API
   - API URL: `http://waapi.beastsmm.pk/send-message`
   - API Key: `YOUR_API_KEY`
   - Check "Default" and "Active"
5. Save

### Step 4: Create Template (1 minute)
1. Go to: **WhatsApp Marketing > Templates**
2. Click **Create Template**
3. Name: Welcome Message
4. Message: `Hello {username}! Welcome to {site_name}.`
5. Save

### Step 5: Create Campaign (1 minute)
1. Go to: **WhatsApp Marketing > Campaigns**
2. Click **Create Campaign**
3. Fill in:
   - Name: Test Campaign
   - Template: Welcome Message
   - API Config: My WhatsApp API
   - Limits: 100/hour, 1000/day
4. Save

### Step 6: Add Recipients (30 seconds)
1. Click **Recipients** next to your campaign
2. Click **Import from Users** (imports users with WhatsApp numbers from `whatsapp_number` column)
   OR
3. Upload CSV file (format: phone_number,name)

### Step 7: Setup Cron (1 minute)
```bash
# Get your cron token from panel or use:
# md5('whatsapp_marketing_cron_' . YOUR_ENCRYPTION_KEY)

# Add to crontab:
* * * * * curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN" >/dev/null 2>&1
```

### Step 8: Start Campaign (10 seconds)
1. Go to campaigns list
2. Click **Start** next to your campaign
3. Wait for cron to send messages

## 📊 Monitor Progress

**Dashboard:** WhatsApp Marketing > Dashboard
- See total campaigns, messages sent, failures

**Campaign Details:** Click campaign name
- View progress, logs, errors

**Recipients:** Click Recipients button
- See who received messages, who failed

## 🎯 Quick Actions

### Send Test Message
```bash
# Create campaign with 1 recipient (your number)
# Start campaign
# Manually trigger cron:
curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN"
```

### Resend Failed Messages
1. Go to campaign details
2. Click **Resend Failed**
3. Failed messages reset to pending

### Export Report
1. Go to: WhatsApp Marketing > Reports
2. Click **Export CSV** next to campaign

## 📝 Template Variables

Use these in your messages:
- `{username}` - User's name
- `{email}` - User's email
- `{balance}` - Account balance
- `{total_orders}` - Number of orders
- `{site_name}` - Website name
- `{site_url}` - Website URL
- `{current_date}` - Today's date

**Example:**
```
Hello {username}!

Your balance: {balance}
Total orders: {total_orders}

Visit {site_url}
Thank you!
```

## 🔧 Common Tasks

### Import from CSV
**Format:**
```csv
phone_number,name
923001234567,John Doe
923007654321,Jane Smith
```

**Upload:**
1. Go to Recipients
2. Click "Import from CSV"
3. Upload file

### Change Rate Limits
1. Edit campaign
2. Update Hourly/Daily limits
3. Save

### Switch API
1. Edit campaign
2. Select different API config
3. Save

### Pause/Resume Campaign
- Click **Pause** to stop temporarily
- Click **Resume** to continue

## ⚠️ Important Notes

1. **Phone Format:** 923001234567 (country code + number, no +)
2. **Rate Limits:** Start with 100/hour to avoid blocks
3. **Cron:** Must run every minute for auto-sending
4. **Testing:** Always test with your own number first

## 🆘 Troubleshooting

**Messages not sending?**
- Check campaign status is "Running"
- Verify cron is configured
- Test manually: `curl "https://yoursite.com/whatsapp_cron/run?token=TOKEN"`

**API errors?**
- Run test script: `php test-whatsapp-api.php`
- Check API key is correct
- Verify API endpoint is accessible

**No recipients?**
- Ensure users have phone numbers in database
- Check CSV format is correct
- Verify phone numbers are valid

## 📚 Full Documentation

- **Installation:** `WHATSAPP_MARKETING_INSTALLATION.md`
- **User Guide:** `WHATSAPP_MARKETING_README.md`
- **Technical:** `WHATSAPP_MARKETING_SUMMARY.md`

## 🎉 You're Ready!

Your WhatsApp Marketing System is now set up and ready to use. Start sending automated messages to your users!

---

**Need Help?** Check the full documentation or contact support.
