# WhatsApp Marketing System

This module provides a complete WhatsApp marketing solution similar to the email marketing system, allowing you to send automated WhatsApp messages to your users through campaigns.

## Features

- ✅ Campaign Management (Create, Edit, Delete, Start, Pause, Resume)
- ✅ Message Templates with variable support
- ✅ WhatsApp API Configuration Management
- ✅ Recipient Management (Import from users or CSV)
- ✅ Automated Message Sending via Cron
- ✅ Rate Limiting (Hourly and Daily limits)
- ✅ Comprehensive Logging and Tracking
- ✅ Campaign Statistics and Reports
- ✅ Failed Message Retry System

## Installation

### 1. Database Setup

Run the SQL schema to create necessary tables:

```bash
mysql -u username -p database_name < database/whatsapp-marketing.sql
```

Or import the file via phpMyAdmin.

### 2. WhatsApp API Configuration

1. Navigate to **WhatsApp Marketing > API Configuration**
2. Click **Add New API Config**
3. Fill in the details:
   - **Name**: A friendly name for this configuration
   - **API URL**: http://waapi.beastsmm.pk/send-message (or your custom API endpoint)
   - **API Key**: Your WhatsApp API key (e.g., "123456")
   - **Set as Default**: Yes (for the first config)
   - **Status**: Active

### 3. Cron Job Setup

To enable automated message sending, set up a cron job:

#### Get Your Cron Token

The cron token is automatically generated. You can find it in your system settings or use:
```php
md5('whatsapp_marketing_cron_' . ENCRYPTION_KEY)
```

#### Add Cron Job

Add this to your crontab (runs every minute):

```bash
* * * * * curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN" >/dev/null 2>&1
```

Or using wget:

```bash
* * * * * wget -q -O /dev/null "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN"
```

#### Campaign-Specific Cron (Optional)

To run cron for a specific campaign only:

```bash
* * * * * curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_CRON_TOKEN&campaign_id=CAMPAIGN_IDS" >/dev/null 2>&1
```

## Usage Guide

### Creating a Campaign

1. **Navigate to Campaigns**
   - Go to **WhatsApp Marketing > Campaigns**
   - Click **Create New Campaign**

2. **Fill Campaign Details**
   - **Name**: Give your campaign a descriptive name
   - **Template**: Select a message template
   - **API Configuration**: Select your WhatsApp API config
   - **Hourly Limit**: Max messages per hour (optional)
   - **Daily Limit**: Max messages per day (optional)

3. **Add Recipients**
   - After creating the campaign, click **Manage Recipients**
   - Import from:
     - **Users Database**: Imports users with WhatsApp numbers (from `whatsapp_number` column) and order history
     - **CSV File**: Upload a CSV file with phone numbers (format: phone_number, name)

4. **Start Campaign**
   - Click **Start Campaign**
   - The cron job will automatically process messages based on limits

### Creating Message Templates

1. Go to **WhatsApp Marketing > Templates**
2. Click **Create New Template**
3. Use variables in your message:
   - `{username}` - User's name
   - `{email}` - User's email
   - `{balance}` - User's balance
   - `{total_orders}` - Total orders count
   - `{site_name}` - Your website name
   - `{site_url}` - Your website URL
   - `{current_date}` - Current date
   - `{current_year}` - Current year

**Example Template:**
```
Hello {username}! Welcome to {site_name}. Your current balance is {balance}. You have placed {total_orders} orders with us. Thank you for being a valued customer!
```

### Managing Campaigns

#### Campaign Statuses
- **Pending**: Campaign created but not started
- **Running**: Actively sending messages
- **Paused**: Temporarily stopped
- **Completed**: All messages sent
- **Cancelled**: Manually cancelled

#### Campaign Actions
- **Start**: Begin sending messages
- **Pause**: Temporarily stop sending
- **Resume**: Continue after pause
- **Resend Failed**: Reset failed messages for retry
- **View Details**: See campaign statistics and logs
- **Export Report**: Download CSV report

### Rate Limiting

Set rate limits to avoid being blocked:

- **Hourly Limit**: Recommended 100-200 messages/hour
- **Daily Limit**: Recommended 1000-2000 messages/day

Adjust based on your WhatsApp API provider's limits.

### CSV Import Format

Create a CSV file with the following format:

```csv
phone_number,name
923001234567,John Doe
923007654321,Jane Smith
923009876543,Ali Khan
```

**Important Notes:**
- Phone numbers should include country code without '+' sign
- Example: 923001234567 for Pakistan (+92)
- First row is treated as header and skipped
- Invalid phone numbers are automatically filtered

## WhatsApp API Integration

The system uses the following API format (as per your provided example):

### Request
```php
POST http://waapi.beastsmm.pk/send-message
Content-Type: application/json

{
    "apiKey": "123456",
    "phoneNumber": "923483469617",
    "message": "Hello, this is a test message from your API!"
}
```

### Custom API

If you're using a different WhatsApp API provider, simply update the API configuration with:
1. Your API endpoint URL
2. Your API key

The system automatically formats requests in the standard format shown above.

## Database Schema

The system creates the following tables:

1. **whatsapp_campaigns** - Campaign information
2. **whatsapp_templates** - Message templates
3. **whatsapp_api_configs** - API configurations
4. **whatsapp_recipients** - Campaign recipients
5. **whatsapp_logs** - Activity logs
6. **whatsapp_settings** - System settings

## Troubleshooting

### Messages Not Sending

1. **Check Cron Job**: Ensure cron is running
   ```bash
   curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN"
   ```

2. **Verify API Config**: Check API URL and key are correct

3. **Check Campaign Status**: Must be "Running"

4. **Review Logs**: Check campaign details for error messages

### Rate Limiting Issues

If you see "rate_limited" in cron response:
- Wait 60 seconds between manual cron runs
- Cron automatically respects campaign limits

### Failed Messages

View failed messages in campaign details:
- Check error messages for specific issues
- Common issues:
  - Invalid phone numbers
  - API authentication errors
  - Network timeouts
  - API rate limits exceeded

Use **Resend Failed** button to retry failed messages.

## Security

- Cron endpoints are protected by secure tokens
- Admin-only access to all features
- Rate limiting prevents abuse
- API keys are stored securely

## Support

For issues or questions:
1. Check logs in **Campaign Details**
2. Review **Recent Activity** on dashboard
3. Export campaign reports for analysis
4. Check API provider's documentation

## API Response Handling

The system handles various HTTP response codes:
- **200-299**: Success - Message marked as sent
- **400+**: Error - Message marked as failed with error details

## Testing

To test your setup:

1. Create a test campaign with 1-2 recipients
2. Use your own phone number as recipient
3. Start the campaign
4. Manually trigger cron:
   ```bash
   curl "https://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_IDS"
   ```
5. Check campaign logs for results

## Advanced Features

### Template Variables

Create dynamic messages using custom data:
- User information is automatically populated
- Custom variables can be added via import

### Campaign Analytics

Track campaign performance:
- Total messages
- Sent count
- Failed count
- Failure rate
- Sending progress

### Bulk Operations

- Import thousands of recipients from CSV
- Export campaign reports
- Bulk retry failed messages

## Best Practices

1. **Test First**: Always test with small campaigns
2. **Monitor Limits**: Set appropriate hourly/daily limits
3. **Quality Data**: Ensure phone numbers are valid
4. **Message Content**: Keep messages concise and relevant
5. **Compliance**: Follow WhatsApp's terms of service
6. **Scheduling**: Use rate limits to spread messages over time
7. **Monitoring**: Regularly check logs and failure rates

## Updates and Maintenance

- Regularly backup campaign data
- Monitor API provider's status
- Keep API keys secure
- Review failed messages weekly
- Clean up completed campaigns monthly

---

**Version**: 1.0  
**Last Updated**: 2024-11-12  
**Compatible With**: SMM Panel Script
