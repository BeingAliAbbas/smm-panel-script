# WhatsApp Marketing System - Installation Guide

## Quick Start

This guide will help you install and configure the WhatsApp Marketing System for your SMM Panel.

## Prerequisites

- SMM Panel already installed and working
- MySQL database access
- PHP 7.0 or higher with cURL extension
- Admin access to the panel
- WhatsApp API credentials (API key from your provider)

## Installation Steps

### Step 1: Database Installation

1. **Access your database** (via phpMyAdmin or command line)

2. **Run the SQL schema:**
   ```bash
   mysql -u your_username -p your_database < database/whatsapp-marketing.sql
   ```
   
   Or in phpMyAdmin:
   - Open phpMyAdmin
   - Select your database
   - Click "Import"
   - Choose `database/whatsapp-marketing.sql`
   - Click "Go"

3. **Verify tables created:**
   The following tables should now exist:
   - `whatsapp_campaigns`
   - `whatsapp_templates`
   - `whatsapp_api_configs`
   - `whatsapp_recipients`
   - `whatsapp_logs`
   - `whatsapp_settings`

### Step 2: Test WhatsApp API

Before proceeding, test your WhatsApp API connection:

1. **Edit the test script:**
   ```bash
   nano test-whatsapp-api.php
   ```

2. **Update these values:**
   ```php
   $apiKey = "YOUR_ACTUAL_API_KEY";
   $testPhoneNumber = "YOUR_PHONE_NUMBER";  // With country code, no +
   ```

3. **Run the test:**
   ```bash
   php test-whatsapp-api.php
   ```

4. **Expected output:**
   ```
   ✅ SUCCESS! Message sent successfully.
   ```

   If you see an error, verify:
   - Your API key is correct
   - Phone number format is correct (e.g., 923001234567)
   - API endpoint is accessible

### Step 3: Configure WhatsApp API in Panel

1. **Login to your admin panel**

2. **Navigate to:** WhatsApp Marketing > API Configuration

3. **Click:** "Add API Config"

4. **Fill in the form:**
   - **Name:** "My WhatsApp API" (or any name)
   - **API URL:** `http://waapi.beastsmm.pk/send-message`
   - **API Key:** Your actual API key
   - **Set as Default:** ✓ (checked)
   - **Status:** ✓ Active (checked)

5. **Click:** "Save Configuration"

### Step 4: Create Your First Template

1. **Navigate to:** WhatsApp Marketing > Templates

2. **Click:** "Create Template"

3. **Fill in the form:**
   - **Name:** "Welcome Message"
   - **Message:**
     ```
     Hello {username}! 
     
     Welcome to {site_name}. Your current balance is {balance}.
     You have placed {total_orders} orders with us.
     
     Thank you for being a valued customer!
     ```
   - **Description:** "Welcome message for new users"

4. **Click:** "Create Template"

### Step 5: Create Your First Campaign

1. **Navigate to:** WhatsApp Marketing > Campaigns

2. **Click:** "Create Campaign"

3. **Fill in the form:**
   - **Campaign Name:** "Test Campaign"
   - **Template:** Select "Welcome Message"
   - **API Configuration:** Select your API config
   - **Hourly Limit:** 100 (recommended for testing)
   - **Daily Limit:** 1000

4. **Click:** "Create Campaign"

### Step 6: Add Recipients

1. **Click:** "Recipients" button next to your campaign

2. **Choose import method:**

   **Option A: Import from Users**
   - Click "Import from Users"
   - System imports all users with phone numbers

   **Option B: Import from CSV**
   - Create a CSV file:
     ```csv
     phone_number,name
     923001234567,John Doe
     923007654321,Jane Smith
     ```
   - Click "Import from CSV"
   - Upload your file

3. **Verify recipients imported successfully**

### Step 7: Setup Cron Job

1. **Get your cron token:**
   - In your panel, go to Settings
   - Or use: `md5('whatsapp_marketing_cron_' . YOUR_ENCRYPTION_KEY)`

2. **Add cron job** (choose one method):

   **Method A: Using crontab (Linux/Mac)**
   ```bash
   crontab -e
   ```
   Add this line:
   ```
   * * * * * curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN" >/dev/null 2>&1
   ```

   **Method B: Using wget**
   ```
   * * * * * wget -q -O /dev/null "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN"
   ```

   **Method C: Using cPanel Cron Jobs**
   - Login to cPanel
   - Go to "Cron Jobs"
   - Set: Every minute
   - Command: `curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN"`

3. **Test cron manually:**
   ```bash
   curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN"
   ```

   Expected response:
   ```json
   {
     "status": "success",
     "message": "Message processing completed",
     "campaigns_checked": 1,
     "messages_sent": 1,
     "time": "2024-11-12T10:30:00+00:00"
   }
   ```

### Step 8: Start Your Campaign

1. **Navigate to:** WhatsApp Marketing > Campaigns

2. **Click:** "Start" button next to your campaign

3. **Campaign status changes to:** "Running"

4. **Wait for cron to process** (runs every minute)

5. **Monitor progress:**
   - Dashboard shows statistics
   - Campaign Details shows logs
   - Recipients page shows sent status

## Verification Checklist

✓ Database tables created  
✓ API test successful  
✓ API configuration added  
✓ Template created  
✓ Campaign created  
✓ Recipients imported  
✓ Cron job configured  
✓ Campaign started  
✓ Messages being sent  

## Common Issues & Solutions

### Issue: Cron not running

**Solution:**
1. Verify cron is enabled on your server
2. Check cron logs: `/var/log/cron` or `/var/log/syslog`
3. Test manually: `curl "https://yoursite.com/whatsapp_cron/run?token=TOKEN"`

### Issue: Messages not sending

**Solution:**
1. Check campaign status is "Running"
2. Verify API configuration is active
3. Check rate limits aren't exceeded
4. Review campaign logs for errors

### Issue: "Rate limited" error

**Solution:**
- Wait 60 seconds between manual cron runs
- Cron automatically handles rate limiting

### Issue: Phone number format errors

**Solution:**
- Use format: 923001234567 (country code + number, no + sign)
- Remove spaces, dashes, parentheses
- Minimum 10 digits required

### Issue: API authentication failed

**Solution:**
1. Verify API key is correct
2. Check API endpoint URL
3. Test with the test script: `php test-whatsapp-api.php`

## Testing Your Setup

### Test 1: Manual Cron Run

```bash
curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN"
```

Should return JSON with success status.

### Test 2: Campaign-Specific Cron

```bash
curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_IDS"
```

Processes only the specified campaign.

### Test 3: Send Test Message

1. Create campaign with 1 recipient (your phone number)
2. Start campaign
3. Run cron manually
4. Check your WhatsApp for the message

## Advanced Configuration

### Custom API Endpoint

If using a different WhatsApp provider:

1. Update API Configuration with your endpoint
2. Ensure endpoint accepts this format:
   ```json
   {
     "apiKey": "your-key",
     "phoneNumber": "923001234567",
     "message": "Your message"
   }
   ```

### Rate Limiting

Adjust based on your provider's limits:

- **Conservative:** 50/hour, 500/day
- **Moderate:** 100/hour, 1000/day  
- **Aggressive:** 200/hour, 2000/day

### Multiple API Configurations

You can add multiple API configs and switch between them per campaign.

## Monitoring & Maintenance

### Daily Tasks

1. Check dashboard for statistics
2. Review failed messages
3. Monitor API limits

### Weekly Tasks

1. Export campaign reports
2. Clean up completed campaigns
3. Review failure rates

### Monthly Tasks

1. Archive old campaigns
2. Update templates
3. Review and optimize sending limits

## Security Best Practices

1. **Never share** your cron token
2. **Keep API keys** secure
3. **Regularly rotate** API keys
4. **Monitor** for unusual activity
5. **Use HTTPS** for all panel access

## Getting Help

If you encounter issues:

1. Check the logs in Campaign Details
2. Review the README documentation
3. Test API with the test script
4. Verify cron is running correctly
5. Check server error logs

## Next Steps

Now that your WhatsApp Marketing System is installed:

1. Create more templates for different scenarios
2. Set up multiple campaigns
3. Import your user base
4. Monitor and optimize delivery rates
5. Analyze campaign performance

---

**Congratulations!** Your WhatsApp Marketing System is now ready to use.

For detailed usage instructions, see `WHATSAPP_MARKETING_README.md`
