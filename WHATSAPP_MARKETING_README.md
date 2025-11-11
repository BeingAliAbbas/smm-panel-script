# WhatsApp Marketing Management Module

Complete WhatsApp marketing automation system for bulk campaigns with API integration, recipient management, and automated cron-based delivery.

## Features

### Campaign Management
- Create, edit, and delete WhatsApp campaigns
- Display campaign statistics: total messages, sent, remaining, failed, and delivered
- Show campaign status: Pending, Running, Completed, or Paused
- Pause, resume, or delete campaigns with ease

### Sending Logic & Cron Behavior
- Messages sent exclusively via cron jobs
- When campaign is marked as "Started", cron processes messages one by one
- "Start Sending" button in panel initiates cron processing
- Clean informational message when no active campaign found
- Configurable sending limits (per hour, per day)
- Real-time progress tracking (total, sent, remaining)

### Message Management
- Direct message entry in campaign (no template management needed)
- Support for placeholders in messages:
  - `{username}` - Recipient's name
  - `{phone}` - Recipient's phone number
  - `{balance}` - User's account balance
  - `{site_name}` - Your site name
  - `{site_url}` - Your site URL

### Recipient Management
- Auto-fetch phone numbers from `general_users.whatsapp_number`
- **Order-based filtering**: Only imports users with at least 1 order
- CSV/TXT import for external phone lists
- Automatic removal of + symbols from phone numbers before sending
- Validation: duplicate removal and format checking

### API Configuration
- Database-stored WhatsApp API configurations
- Support for multiple API profiles
- Default profile selection
- API endpoint URL configuration
- API Key storage

**API Format:**
```
POST http://waapi.beastsmm.pk/send-message
{
    "apiKey": "YOUR_API_KEY",
    "phoneNumber": "923XXXXXXXXX",
    "message": "Hello {username}, your balance is {balance}"
}
```

### Tracking & Reporting
- Campaign-level stats: total messages, sent, delivered, failed
- Detailed logs with timestamps for each message
- CSV export for reports
- Error message tracking

## Installation

1. **Import Database Schema**
   ```bash
   mysql -u username -p database_name < database/whatsapp-marketing.sql
   ```

2. **Set Up Cron Job**
   
   For all campaigns:
   ```bash
   */5 * * * * curl "https://yoursite.com/cron/whatsapp_marketing?token=YOUR_TOKEN"
   ```
   
   For specific campaign:
   ```bash
   */5 * * * * curl "https://yoursite.com/cron/whatsapp_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"
   ```

3. **Configure API**
   - Go to WhatsApp Marketing → API Configurations
   - Add your WhatsApp API endpoint and key
   - Set as default if needed

## Usage

1. **Create API Configuration**
   - Navigate to WhatsApp Marketing → API Configurations
   - Click "New API Config"
   - Enter API URL (e.g., `http://waapi.beastsmm.pk/send-message`)
   - Enter API Key
   - Optionally set as default

2. **Create Campaign**
   - Go to WhatsApp Marketing → Campaigns
   - Click "New Campaign"
   - Enter campaign name and message
   - Use placeholders like {username}, {phone}, {balance}
   - Select API configuration
   - Set sending limits (hourly/daily)

3. **Import Recipients**
   - Open campaign details
   - Click "Manage Recipients"
   - Import from users (only those with orders) or upload CSV
   - Phone numbers will be automatically sanitized

4. **Start Campaign**
   - In campaign details, click "Start Sending"
   - Cron will begin processing messages
   - Monitor progress in real-time

5. **View Reports**
   - Go to WhatsApp Marketing → Reports
   - View campaign statistics
   - Export to CSV for analysis

## Troubleshooting

**Import button stuck loading:**
- Check browser console for errors
- Verify database connection
- Ensure users have phone numbers and order history

**Messages not sending:**
- Verify cron job is running
- Check API configuration is correct
- Review campaign sending limits
- Check WhatsApp Marketing logs

**Phone number format issues:**
- Module automatically removes + symbols
- Accepts international format (+923001234567)
- Converts to numeric only (923001234567)

## Security

- Admin-only access control
- CSRF protection on all mutations
- Token-based cron authentication
- Input sanitization and validation
- Prepared statements for SQL queries

## Support

For issues or questions, please refer to the main repository documentation.
