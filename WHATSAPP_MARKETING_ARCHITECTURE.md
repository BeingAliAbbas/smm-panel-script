# WhatsApp Marketing System Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                         ADMIN PANEL                                 │
│                                                                     │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐            │
│  │  Campaigns   │  │ API Configs  │  │  Recipients  │            │
│  │              │  │              │  │              │            │
│  │ • Create     │  │ • API Key    │  │ • Import DB  │            │
│  │ • Edit       │  │ • Endpoint   │  │ • Upload CSV │            │
│  │ • Delete     │  │ • Multiple   │  │ • Validate   │            │
│  │ • Start/Pause│  │   Profiles   │  │ • Sanitize   │            │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘            │
│         │                 │                  │                     │
└─────────┼─────────────────┼──────────────────┼─────────────────────┘
          │                 │                  │
          ▼                 ▼                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER                                 │
│                                                                     │
│  ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐     │
│  │ whatsapp_       │ │ whatsapp_       │ │ whatsapp_       │     │
│  │ campaigns       │ │ api_configs     │ │ recipients      │     │
│  │                 │ │                 │ │                 │     │
│  │ • name          │ │ • name          │ │ • phone_number  │     │
│  │ • message       │ │ • api_key       │ │ • name          │     │
│  │ • status        │ │ • api_endpoint  │ │ • user_id       │     │
│  │ • limits        │ │ • is_default    │ │ • status        │     │
│  │ • stats         │ │ • status        │ │ • custom_data   │     │
│  └────────┬────────┘ └────────┬────────┘ └────────┬────────┘     │
│           │                   │                    │               │
│           └───────────────────┴────────────────────┘               │
│                               │                                     │
│                    ┌──────────┴──────────┐                         │
│                    │                     │                         │
│           ┌────────▼────────┐   ┌───────▼────────┐                │
│           │ whatsapp_logs   │   │ whatsapp_      │                │
│           │                 │   │ settings       │                │
│           │ • phone_number  │   │                │                │
│           │ • message       │   │ • hourly_limit │                │
│           │ • status        │   │ • daily_limit  │                │
│           │ • error_message │   │ • retry_config │                │
│           │ • api_response  │   │                │                │
│           └─────────────────┘   └────────────────┘                │
└─────────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      CRON PROCESSING                                │
│                                                                     │
│  Campaign 1 Cron         Campaign 2 Cron         Campaign N Cron   │
│  ┌──────────────┐       ┌──────────────┐       ┌──────────────┐  │
│  │ Every Minute │       │ Every Minute │       │ Every Minute │  │
│  │              │       │              │       │              │  │
│  │ • Get Next   │       │ • Get Next   │       │ • Get Next   │  │
│  │   Pending    │       │   Pending    │       │   Pending    │  │
│  │ • Check Limit│       │ • Check Limit│       │ • Check Limit│  │
│  │ • Send 1 Msg │       │ • Send 1 Msg │       │ • Send 1 Msg │  │
│  │ • Update Log │       │ • Update Log │       │ • Update Log │  │
│  │ • Rate Limit │       │ • Rate Limit │       │ • Rate Limit │  │
│  │   60s Min    │       │   60s Min    │       │   60s Min    │  │
│  └──────┬───────┘       └──────┬───────┘       └──────┬───────┘  │
│         │                      │                      │           │
│         └──────────────────────┴──────────────────────┘           │
│                               │                                    │
│                    Isolated Processing                             │
│                    No Interference                                 │
└───────────────────────────────┼────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      MESSAGE PROCESSING                             │
│                                                                     │
│  1. Fetch Recipient                                                │
│     │                                                               │
│  2. Load Campaign & API Config                                     │
│     │                                                               │
│  3. Build Message with Placeholders                                │
│     │                                                               │
│     ┌───────────────────────────────────┐                          │
│     │ {username} → John Doe             │                          │
│     │ {phone} → 923001234567            │                          │
│     │ {balance} → 100.00                │                          │
│     │ {email} → user@example.com        │                          │
│     └───────────────────────────────────┘                          │
│     │                                                               │
│  4. Sanitize Phone Number                                          │
│     │  (Remove + symbol)                                           │
│     │                                                               │
│  5. Send to WhatsApp API                                           │
│     │                                                               │
└─────┼───────────────────────────────────────────────────────────────┘
      │
      ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    WHATSAPP API SERVER                              │
│                                                                     │
│  POST http://waapi.beastsmm.pk/send-message                        │
│                                                                     │
│  {                                                                  │
│    "apiKey": "YOUR_API_KEY",                                       │
│    "phoneNumber": "923XXXXXXXXX",   ← No + symbol                 │
│    "message": "Hello John Doe! Your balance is 100.00"            │
│  }                                                                  │
│                                                                     │
│  Response ────────────────────┐                                    │
└───────────────────────────────┼────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────────┐
│                      RESULT LOGGING                                 │
│                                                                     │
│  Success (HTTP 200-299)              Failed (HTTP 4xx/5xx)         │
│  ┌──────────────────┐               ┌──────────────────┐          │
│  │ • Status: sent   │               │ • Status: failed │          │
│  │ • Timestamp      │               │ • Error message  │          │
│  │ • API response   │               │ • API response   │          │
│  │ • Update stats   │               │ • Update stats   │          │
│  └──────────────────┘               └──────────────────┘          │
│                                                                     │
│  Both → Update Campaign Stats                                      │
│         (total, sent, failed, delivered)                           │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow Example

### Creating and Running a Campaign

```
1. Admin Creates Campaign
   ↓
   [Campaign: "Monthly Promotion"]
   - Message: "Hello {username}! Special offer for you. Balance: {balance}"
   - API Config: "Main WhatsApp API"
   - Limits: 100/hour, 1000/day

2. Admin Imports Recipients
   ↓
   [From Database]
   - 500 users from general_users
   - Phone numbers sanitized
   - Duplicates removed
   
   [From CSV]
   - 200 external contacts
   - Format validated

3. Admin Starts Campaign
   ↓
   Status: pending → running
   started_at: 2024-01-15 10:00:00

4. Cron Job Runs (Every Minute)
   ↓
   GET next pending recipient
   
   Recipient #1:
   - Phone: +92-300-1234567
   - Name: John Doe
   - Balance: 100.00
   
   ↓ Sanitize Phone
   - Phone: 923001234567  (+ and - removed)
   
   ↓ Replace Placeholders
   - Message: "Hello John Doe! Special offer for you. Balance: 100.00"
   
   ↓ Check Limits
   - Hourly: 5/100 ✓
   - Daily: 50/1000 ✓
   
   ↓ Send to API
   POST http://waapi.beastsmm.pk/send-message
   {
     "apiKey": "abc123...",
     "phoneNumber": "923001234567",
     "message": "Hello John Doe! Special offer..."
   }
   
   ↓ API Response: 200 OK
   
   ↓ Update Records
   - Recipient status: sent
   - Campaign: sent_messages++
   - Log created with timestamp
   
   ↓ Wait 60 seconds (rate limit)
   
   ↓ Next Cron Run
   Process Recipient #2...

5. All Recipients Processed
   ↓
   Status: running → completed
   completed_at: 2024-01-15 18:30:00
   
   Final Stats:
   - Total: 700
   - Sent: 685
   - Failed: 15
   - Delivered: 685
```

## Security Flow

```
User Request
    ↓
┌─────────────────┐
│ Authentication  │ ← Admin role check
└────────┬────────┘
         ↓
┌─────────────────┐
│ Authorization   │ ← get_role("admin")
└────────┬────────┘
         ↓
┌─────────────────┐
│ Input Validation│ ← post(), sanitize
└────────┬────────┘
         ↓
┌─────────────────┐
│ Database Query  │ ← Query Builder (SQL injection protection)
└────────┬────────┘
         ↓
┌─────────────────┐
│ Output Encoding │ ← htmlspecialchars() (XSS protection)
└────────┬────────┘
         ↓
    Response
```

## Module Structure

```
whatsapp_marketing/
├── controllers/
│   └── Whatsapp_marketing.php
│       ├── index()                    # Dashboard
│       ├── campaigns()                # List campaigns
│       ├── campaign_create()          # Create form
│       ├── ajax_campaign_create()     # Process create
│       ├── campaign_edit()            # Edit form
│       ├── ajax_campaign_edit()       # Process edit
│       ├── ajax_campaign_delete()     # Delete campaign
│       ├── ajax_campaign_status()     # Start/Pause/Resume
│       ├── recipients()               # View recipients
│       ├── ajax_import_recipients()   # Import recipients
│       ├── logs()                     # View logs
│       ├── ajax_export_logs()         # Export CSV
│       ├── api_configs()              # List API configs
│       ├── api_config_create()        # Create API config
│       ├── ajax_api_config_create()   # Process create
│       ├── api_config_edit()          # Edit API config
│       ├── ajax_api_config_edit()     # Process edit
│       └── ajax_api_config_delete()   # Delete config
│
├── models/
│   └── Whatsapp_marketing_model.php
│       ├── get_campaigns()            # Fetch campaigns
│       ├── get_campaign()             # Get single campaign
│       ├── create_campaign()          # Create campaign
│       ├── update_campaign()          # Update campaign
│       ├── delete_campaign()          # Delete campaign
│       ├── update_campaign_stats()    # Update statistics
│       ├── get_api_configs()          # Fetch API configs
│       ├── create_api_config()        # Create config
│       ├── update_api_config()        # Update config
│       ├── delete_api_config()        # Delete config
│       ├── add_recipients()           # Add recipients
│       ├── get_recipients()           # Fetch recipients
│       ├── get_next_pending_recipient()
│       ├── update_recipient_status()  # Update status
│       ├── import_from_general_users()# Import from DB
│       ├── import_from_csv()          # Import from file
│       ├── add_log()                  # Create log entry
│       ├── get_logs()                 # Fetch logs
│       ├── sanitize_phone_number()    # Clean phone
│       ├── remove_duplicate_recipients()
│       └── process_message_variables()# Replace placeholders
│
└── views/
    ├── index.php                      # Main dashboard
    ├── campaigns/
    │   ├── index.php                  # Campaign list
    │   ├── create.php                 # Create form
    │   └── edit.php                   # Edit form
    ├── recipients/
    │   └── index.php                  # Recipients & import
    ├── logs/
    │   └── index.php                  # Logs & export
    └── api_configs/
        ├── index.php                  # Config list
        ├── create.php                 # Create form
        └── edit.php                   # Edit form

Whatsapp_cron.php (Cron Controller)
├── run()                              # Main entry point
├── process_messages()                 # Process campaign messages
├── can_send_message()                 # Check rate limits
├── send_message()                     # Send to API
├── log_failed()                       # Log failed sends
└── respond()                          # JSON response
```

---

**Total Lines of Code: ~2,641 lines**
**Total Files: 16 files**
**Database Tables: 5 tables**
