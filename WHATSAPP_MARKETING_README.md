# WhatsApp Marketing Management System

## Overview

This module adds complete WhatsApp marketing functionality to the SMM Panel, similar to the existing Email Marketing system. It allows you to send WhatsApp messages to users from the `general_users` database using your own WhatsApp API server.

## Installation

### 1. Database Setup

Run the SQL installation script to create the required tables:

```bash
mysql -u your_username -p your_database < database/whatsapp-marketing.sql
```

Or import the SQL file through phpMyAdmin.

This will create the following tables:
- `whatsapp_api_configs` - Store WhatsApp API configurations
- `whatsapp_campaigns` - Manage campaigns
- `whatsapp_recipients` - Store campaign recipients
- `whatsapp_logs` - Track all message activities
- `whatsapp_settings` - Global settings

### 2. Configure WhatsApp API

1. Navigate to **WhatsApp Marketing > API Configuration**
2. Click "Add API Configuration"
3. Enter your API details:
   - **Configuration Name**: A descriptive name (e.g., "Main WhatsApp API")
   - **API Key**: Your WhatsApp API key
   - **API Endpoint**: Default is `http://waapi.beastsmm.pk/send-message`
   - **Default**: Check if this should be the default configuration
   - **Active**: Enable the configuration

### 3. API Format

The module expects the WhatsApp API to accept the following JSON format:

```json
POST http://waapi.beastsmm.pk/send-message
{
  "apiKey": "YOUR_API_KEY",
  "phoneNumber": "923XXXXXXXXX",
  "message": "Hello"
}
```

**Note:** Phone numbers are automatically sanitized by removing the `+` symbol before sending.

## Usage

### Creating a Campaign

1. Go to **WhatsApp Marketing > Campaigns**
2. Click the "+" icon to create a new campaign
3. Fill in the campaign details:
   - **Campaign Name**: Descriptive name for your campaign
   - **Message**: Your WhatsApp message with placeholders
   - **API Configuration**: Select which API profile to use
   - **Hourly Limit**: Maximum messages per hour (optional)
   - **Daily Limit**: Maximum messages per day (optional)

### Available Message Placeholders

You can use the following placeholders in your messages:

- `{username}` - User's name
- `{phone}` - User's phone number
- `{phone_number}` - Same as {phone}
- `{balance}` - User's account balance
- `{email}` - User's email address

**Example Message:**
```
Hello {username}! 
Your current balance is {balance}.
Thank you for using our service!
```

### Adding Recipients

After creating a campaign, you need to add recipients:

#### Option 1: Import from Database
- Click "Import from Database" to automatically import all users with WhatsApp numbers from the `general_users` table
- Phone numbers are automatically sanitized and duplicates are removed

#### Option 2: Import from CSV/TXT File
- Upload a CSV or TXT file with phone numbers
- Format: `phone_number, name` (name is optional)
- Example:
  ```
  923001234567, John Doe
  923001234568, Jane Smith
  ```

### Starting a Campaign

1. After adding recipients, go to the campaign list
2. Click the three-dot menu (⋮) on your campaign
3. Select "Start Sending"
4. The campaign status will change to "Running"

**Important:** Messages are sent exclusively via cron jobs (see Cron Setup below).

### Campaign Status

- **Pending**: Campaign created but not started
- **Running**: Campaign is active and processing messages
- **Paused**: Campaign temporarily stopped
- **Completed**: All messages sent
- **Cancelled**: Campaign cancelled

### Campaign Actions

- **Start**: Begin sending messages (for Pending or Paused campaigns)
- **Pause**: Temporarily stop sending
- **Resume**: Continue a paused campaign
- **View Recipients**: See all campaign recipients and their status
- **View Logs**: See detailed sending logs
- **Edit**: Modify campaign settings
- **Delete**: Remove campaign and all associated data

## Cron Setup

Messages are sent via cron jobs. Each campaign should have its own cron job for isolated processing.

### Campaign-Specific Cron (Recommended)

Set up a separate cron job for each campaign:

```bash
* * * * * curl "http://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"
```

Replace:
- `yoursite.com` with your domain
- `YOUR_TOKEN` with your cron security token (found in the WhatsApp Marketing dashboard)
- `CAMPAIGN_ID` with the specific campaign ID

**Benefits:**
- Each campaign processes independently
- No interference between campaigns
- Better control over sending schedules
- Easier to pause/resume individual campaigns

### Process All Running Campaigns

Alternatively, you can process all running campaigns together:

```bash
* * * * * curl "http://yoursite.com/whatsapp_cron/run?token=YOUR_TOKEN"
```

**Note:** This processes all running campaigns in a single execution.

### Cron Token Security

The cron token is automatically generated using your encryption key. You can find it in the WhatsApp Marketing dashboard. This token prevents unauthorized access to your cron endpoints.

### Rate Limiting

The cron system includes built-in rate limiting:
- Minimum 60 seconds between runs for the same campaign
- Respects hourly and daily limits set in campaign settings
- Sends one message per cron execution

## Tracking & Reporting

### Campaign Statistics

View real-time statistics for each campaign:
- **Total Messages**: Total recipients
- **Sent**: Successfully sent messages
- **Failed**: Failed deliveries
- **Delivered**: Confirmed deliveries

### Message Logs

Access detailed logs for each campaign:
- Phone number
- Message content
- Status (sent/failed/delivered)
- Timestamp
- Error messages (if any)
- API responses

### Export Logs

Export campaign logs to CSV format:
1. Go to campaign logs
2. Click "Export Logs"
3. Download the CSV file with all log data

## Phone Number Format

The system automatically handles phone number formatting:

1. **Sanitization**: All non-numeric characters (except +) are removed
2. **+ Symbol Removal**: The + symbol is removed before sending (as per API requirement)
3. **Validation**: Numbers must be 10-15 digits
4. **Duplicate Removal**: Duplicate phone numbers are automatically filtered

**Accepted Formats:**
- `923001234567` ✓
- `+923001234567` ✓ (+ will be removed)
- `92-300-1234567` ✓ (dashes will be removed)
- `(92) 300 1234567` ✓ (parentheses and spaces will be removed)

## Troubleshooting

### Campaign Not Sending Messages

1. **Check Campaign Status**: Ensure the campaign is set to "Running"
2. **Verify Cron Setup**: Make sure the cron job is configured correctly
3. **Check API Configuration**: Verify the API config is active and has correct credentials
4. **Review Logs**: Check the campaign logs for error messages
5. **Verify Recipients**: Ensure recipients have valid phone numbers

### API Connection Errors

1. **Verify API Endpoint**: Check that the API URL is correct
2. **Check API Key**: Ensure your API key is valid
3. **Network Access**: Verify your server can reach the API endpoint
4. **Review Error Logs**: Check the message logs for specific API errors

### No Recipients Found

1. **Database Import**: Ensure users have WhatsApp numbers in the `general_users` table
2. **CSV Format**: Check that your CSV file is properly formatted
3. **Phone Validation**: Invalid phone numbers are automatically skipped

## Best Practices

1. **Test First**: Create a small test campaign before sending to all users
2. **Use Limits**: Set hourly and daily limits to avoid overwhelming your API
3. **Monitor Logs**: Regularly check logs for failed messages
4. **Campaign-Specific Crons**: Use separate cron jobs for each campaign
5. **Phone Number Quality**: Ensure phone numbers in the database are valid
6. **Message Personalization**: Use placeholders to personalize messages
7. **Regular Backups**: Backup your WhatsApp marketing data regularly

## Security Considerations

1. **Cron Token**: Keep your cron token secret
2. **API Keys**: Store API keys securely
3. **Admin Access**: Only admins can access WhatsApp Marketing features
4. **Data Privacy**: Handle user phone numbers responsibly
5. **Rate Limiting**: Prevents abuse and API overload

## Support

For issues or questions:
1. Check the campaign logs for error details
2. Verify your API configuration
3. Review the cron job setup
4. Check the general system logs

## Module Structure

```
app/
├── controllers/
│   └── Whatsapp_cron.php          # Cron controller for message processing
└── modules/
    └── whatsapp_marketing/
        ├── controllers/
        │   └── Whatsapp_marketing.php    # Main controller
        ├── models/
        │   └── Whatsapp_marketing_model.php  # Database operations
        └── views/
            ├── index.php                 # Dashboard
            ├── campaigns/                # Campaign management views
            ├── recipients/               # Recipient management views
            ├── logs/                     # Log viewing
            └── api_configs/              # API configuration views

database/
└── whatsapp-marketing.sql          # Database schema
```

## Version History

- **1.0.0** - Initial release
  - Campaign management
  - Recipient import from database and CSV/TXT
  - Cron-based sending
  - Message placeholders
  - Campaign statistics and logs
  - Export functionality
