# WhatsApp Marketing Management System

## Overview
A comprehensive WhatsApp marketing management system integrated into the SMM panel that allows administrators to send WhatsApp messages to users from the database using the integrated WhatsApp API server.

## Features

### 1. Campaign Management
- Create, edit, and delete WhatsApp campaigns
- Campaign includes:
  - Campaign name
  - Message content with dynamic placeholders
  - Selected recipients
  - API profile selection
  - Sending limits (per hour/per day)
  - Retry failed messages option

### 2. Campaign Status Tracking
- **Pending**: Campaign created but not started
- **Running**: Campaign is actively sending messages
- **Paused**: Campaign temporarily stopped
- **Completed**: All messages sent
- **Cancelled**: Campaign manually cancelled

### 3. Message Management
- Compose custom WhatsApp messages
- Support for dynamic placeholders:
  - `{username}` - User's first and last name
  - `{phone}` - User's phone number
  - `{balance}` - User's current balance
  - `{email}` - User's email address

### 4. Recipient Management
- Fetch recipients from general_users table (users with WhatsApp numbers)
- Import external contacts via CSV/TXT file
- Automatic validation:
  - Remove duplicates
  - Remove "+" sign from international numbers (e.g., +923001234567 → 923001234567)
- CSV Format: `phone,name,email` (one per line)

### 5. API Configuration
- Multiple API profiles support
- Store API configurations securely:
  - Profile name
  - API endpoint (default: http://waapi.beastsmm.pk/send-message)
  - API key
- Create, edit, delete, and select API profiles per campaign

### 6. Cron Job System
- Automated message dispatching via cron
- Endpoint: `/whatsapp_marketing/cron`
- Features:
  - Rate limiting (hourly/daily limits)
  - Retry logic for failed messages (up to 3 attempts)
  - Automatic campaign completion detection
  - Returns informational message when no active campaign exists

### 7. Statistics & Reporting
- Real-time campaign statistics:
  - Total messages
  - Sent count
  - Delivered count
  - Failed count
  - Remaining messages
- Detailed message logs with:
  - Phone number
  - Username
  - Status
  - Timestamp
  - Error messages (if any)
- Filter logs by status and date
- Export functionality (CSV format)

## Database Schema

The system uses 4 main tables:

### 1. whatsapp_api_configs
Stores API configuration profiles.

### 2. whatsapp_campaigns
Stores campaign information and settings.

### 3. whatsapp_recipients
Stores all recipients for each campaign.

### 4. whatsapp_messages
Stores individual message sending logs and status.

## Installation

### 1. Database Setup
Run the SQL migration file:
```bash
mysql -u username -p database_name < database/whatsapp-marketing.sql
```

### 2. Verify Installation
- Navigate to the admin panel
- You should see "WhatsApp Marketing" in the left sidebar under the Admin Role section

## Usage

### Creating a Campaign

1. Navigate to **WhatsApp Marketing** → **Create Campaign**
2. Fill in the required fields:
   - Campaign Name
   - Select API Profile (create one if needed)
   - Compose your message (use placeholders)
   - Choose recipient source (Database or Import)
   - Set sending limits (optional)
3. Click **Create Campaign**

### Starting a Campaign

1. Go to **WhatsApp Marketing**
2. Find your campaign in the list
3. Click the **Play** button (▶) to start
4. The campaign status will change to "Running"
5. Messages will be sent automatically by the cron job

### Managing Campaigns

- **Pause**: Temporarily stop sending messages
- **Resume**: Continue sending from where it was paused
- **Delete**: Remove campaign and all associated data
- **View**: See detailed statistics and message logs
- **Export**: Download message logs as CSV

### Setting Up Cron Job

Add the following to your crontab to run every minute:
```bash
* * * * * curl http://yourpanel.com/whatsapp_marketing/cron
```

Or use wget:
```bash
* * * * * wget -q -O - http://yourpanel.com/whatsapp_marketing/cron >/dev/null 2>&1
```

## API Integration

The system uses the WhatsApp API with the following format:

**Endpoint**: `POST http://waapi.beastsmm.pk/send-message`

**Request Body**:
```json
{
    "apiKey": "YOUR_API_KEY",
    "phoneNumber": "923XXXXXXXXX",
    "message": "Hello"
}
```

**Response**: 
- HTTP 200: Success
- Other codes: Error (will be logged and retried based on settings)

## Access Control

- Only administrators can access the WhatsApp Marketing module
- All features are restricted to admin role

## File Structure

```
app/modules/whatsapp_marketing/
├── controllers/
│   └── whatsapp_marketing.php    # Main controller
├── models/
│   └── whatsapp_marketing_model.php    # Database model
└── views/
    ├── index.php                 # Campaign list
    ├── create.php                # Create campaign form
    ├── edit.php                  # Edit campaign form
    ├── view.php                  # Campaign details & logs
    ├── api_config.php            # API configuration list
    ├── api_config_form.php       # API config form
    └── config.php                # Module config
```

## Navigation

The module is accessible via:
- Main menu: **Admin Role** → **WhatsApp Marketing**
- Direct URL: `/whatsapp_marketing`

## Security Features

- Admin-only access
- CSRF protection on all forms
- API keys stored securely in database
- Input validation and sanitization
- SQL injection protection via CodeIgniter query builder

## Troubleshooting

### Campaign not sending messages
1. Check if cron job is running
2. Verify campaign status is "Running"
3. Check rate limits aren't blocking sends
4. Review message logs for errors

### No active campaign message from cron
This is normal and means no campaigns are currently running. Start a campaign to activate sending.

### API errors
1. Verify API configuration is correct
2. Check API key is valid
3. Ensure endpoint URL is accessible
4. Review error messages in message logs

## Support

For issues or questions, please contact the system administrator.
