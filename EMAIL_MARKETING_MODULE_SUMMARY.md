# Email Marketing Module - Comprehensive Summary

## Overview

The **Email Marketing** module is a full-featured email campaign management system built for the SMM Panel script. It enables administrators to create, manage, and track email marketing campaigns with advanced features including template management, SMTP configuration, recipient management, email tracking, and detailed reporting.

**Location:** `/app/modules/email_marketing/`

**Architecture:** MVC (Model-View-Controller) using CodeIgniter HMVC pattern

**Module Icon:** `fa fa-envelope`

---

## Table of Contents

1. [Module Structure](#module-structure)
2. [Controller: email_marketing.php](#controller-email_marketingphp)
3. [Model: email_marketing_model.php](#model-email_marketing_modelphp)
4. [Views](#views)
5. [Database Schema](#database-schema)
6. [Workflows and Interactions](#workflows-and-interactions)
7. [Features Summary](#features-summary)

---

## Module Structure

```
email_marketing/
├── controllers/
│   └── email_marketing.php          # Main controller (872 lines)
├── models/
│   └── email_marketing_model.php    # Data model (668 lines)
└── views/
    ├── index.php                     # Dashboard view
    ├── campaigns/
    │   ├── index.php                 # Campaign list
    │   ├── create.php                # Create campaign form
    │   ├── edit.php                  # Edit campaign form
    │   └── details.php               # Campaign details & analytics
    ├── templates/
    │   ├── index.php                 # Template list
    │   ├── create.php                # Create template form
    │   └── edit.php                  # Edit template form
    ├── smtp/
    │   ├── index.php                 # SMTP configuration list
    │   ├── create.php                # Create SMTP config form
    │   └── edit.php                  # Edit SMTP config form
    ├── recipients/
    │   └── index.php                 # Recipient management
    └── reports/
        └── index.php                 # Analytics and reports
```

---

## Controller: email_marketing.php

**File:** `/app/modules/email_marketing/controllers/email_marketing.php`

**Lines:** 872

**Class:** `Email_marketing extends MX_Controller`

### Purpose
The controller serves as the central hub for all email marketing functionality. It handles user requests, processes data, manages AJAX operations, and coordinates between views and models.

### Class Properties

```php
public $module_name;    // "Email Marketing"
public $module;         // "email_marketing"
public $module_icon;    // "fa fa-envelope"
```

### Constructor

**Method:** `__construct()`
- Loads the email_marketing_model
- Sets module configuration (name, identifier, icon)
- Performs admin permission check (only admins can access)
- Redirects unauthorized users with validation error

### Key Methods by Category

#### Dashboard (Lines 29-42)

**`index()`**
- **Purpose:** Display main dashboard with overall statistics
- **Data Retrieved:**
  - Overall statistics via `get_overall_stats()`
  - Recent activity logs (last 10) via `get_recent_logs(10)`
- **View:** `index.php`
- **Features:** Performance metrics, campaign overview, quick access cards, getting started guide

---

#### Campaign Management (Lines 49-369)

**`campaigns($page = 1)`**
- **Purpose:** List all campaigns with pagination
- **Parameters:** `$page` - Current page number (default: 1)
- **Pagination:** 20 campaigns per page
- **View:** `campaigns/index.php`

**`campaign_create()`**
- **Purpose:** Display campaign creation form
- **Dependencies:** Loads templates and SMTP configurations
- **View:** `campaigns/create.php` (Modal)

**`ajax_campaign_create()`**
- **Purpose:** Process campaign creation via AJAX
- **POST Data:**
  - `name` (required) - Campaign name
  - `template_id` (required) - Email template ID
  - `smtp_config_id` (required) - SMTP configuration ID
  - `sending_limit_hourly` (optional) - Max emails per hour
  - `sending_limit_daily` (optional) - Max emails per day
- **Validation:** Checks required fields
- **Response:** JSON status and message
- **Initial Status:** `pending`

**`campaign_edit($ids)`**
- **Purpose:** Display campaign edit form
- **Parameters:** `$ids` - Campaign unique identifier
- **View:** `campaigns/edit.php` (Modal)
- **Security:** Redirects if campaign not found

**`ajax_campaign_edit($ids)`**
- **Purpose:** Update campaign via AJAX
- **Validation:** Checks campaign existence and required fields
- **Response:** JSON status and message

**`ajax_campaign_delete()`**
- **Purpose:** Delete campaign and related data
- **POST Data:** `ids` - Campaign identifier
- **Cascading Delete:** Removes recipients and logs
- **Response:** JSON status and message

**`ajax_campaign_start()`**
- **Purpose:** Start a pending campaign
- **Validation:** 
  - Checks campaign exists
  - Verifies recipients exist (cannot start empty campaign)
- **Updates:** Sets status to `running`, records `started_at` timestamp
- **Response:** JSON status and message

**`ajax_campaign_pause()`**
- **Purpose:** Pause running campaign
- **Updates:** Changes status to `paused`
- **Response:** JSON status and message

**`ajax_campaign_resume()`**
- **Purpose:** Resume paused campaign
- **Updates:** Changes status to `running`
- **Response:** JSON status and message

**`ajax_campaign_resend_failed()`**
- **Purpose:** Reset failed emails for resending
- **Process:**
  - Resets failed recipients to `pending` status
  - Updates campaign statistics
  - Resumes completed campaigns if needed
- **Response:** Returns count of reset emails
- **Error Handling:** Returns error if no failed emails found

**`ajax_resend_single_email()`**
- **Purpose:** Resend a single failed email
- **POST Data:** `recipient_id`
- **Validation:** Only allows resending failed emails
- **Process:**
  - Resets recipient status to `pending`
  - Clears error message and sent timestamp
  - Updates campaign stats
  - Resumes campaign if completed
- **Response:** JSON status and message

**`campaign_details($ids)`**
- **Purpose:** Display comprehensive campaign details
- **Data Retrieved:**
  - Campaign info with stats
  - Recipients (last 100)
  - Activity logs (last 50)
- **Features:**
  - Real-time statistics
  - Campaign health indicator
  - Progress tracking
  - Recipient filtering
  - Action buttons for management
- **View:** `campaigns/details.php`

---

#### Template Management (Lines 375-497)

**`templates($page = 1)`**
- **Purpose:** List email templates with pagination
- **Pagination:** 20 templates per page
- **View:** `templates/index.php`

**`template_create()`**
- **Purpose:** Display template creation form
- **View:** `templates/create.php` (Modal)

**`ajax_template_create()`**
- **Purpose:** Create new email template
- **POST Data:**
  - `name` (required) - Template name
  - `subject` (required) - Email subject line
  - `body` (required) - HTML email body
  - `description` (optional) - Template description
- **Special Handling:** `body` field not XSS cleaned to preserve HTML
- **Response:** JSON status and message

**`template_edit($ids)`**
- **Purpose:** Display template edit form
- **View:** `templates/edit.php` (Modal)

**`ajax_template_edit($ids)`**
- **Purpose:** Update existing template
- **Validation:** Checks template exists and required fields
- **Response:** JSON status and message

**`ajax_template_delete()`**
- **Purpose:** Delete template
- **Security:** Prevents deletion if template is in use by active campaigns
- **Response:** JSON status with error if in use

---

#### SMTP Configuration Management (Lines 503-656)

**`smtp($page = 1)`**
- **Purpose:** List SMTP configurations with pagination
- **Pagination:** 20 configs per page
- **View:** `smtp/index.php`

**`smtp_create()`**
- **Purpose:** Display SMTP configuration form
- **View:** `smtp/create.php` (Modal)

**`ajax_smtp_create()`**
- **Purpose:** Create SMTP configuration
- **POST Data:**
  - `name` (required) - Config name
  - `host` (required) - SMTP server host
  - `port` (required) - SMTP port
  - `username` (required) - SMTP username
  - `password` (required) - SMTP password
  - `encryption` (optional) - TLS/SSL/none
  - `from_name` (optional) - Sender name
  - `from_email` (required) - Sender email
  - `reply_to` (optional) - Reply-to email
  - `is_default` (optional) - Set as default
  - `status` (optional) - Active/inactive
- **Response:** JSON status and message

**`smtp_edit($ids)`**
- **Purpose:** Display SMTP edit form
- **View:** `smtp/edit.php` (Modal)

**`ajax_smtp_edit($ids)`**
- **Purpose:** Update SMTP configuration
- **Special Handling:** Only updates password if provided (allows password changes)
- **Response:** JSON status and message

**`ajax_smtp_delete()`**
- **Purpose:** Delete SMTP configuration
- **Security:** Prevents deletion if in use by active campaigns
- **Response:** JSON status with error if in use

---

#### Recipient Management (Lines 662-772)

**`recipients($campaign_ids)`**
- **Purpose:** Display recipient management interface
- **Data:** Shows last 100 recipients
- **Features:**
  - Import from user database
  - Import from CSV file
  - View recipient status
- **View:** `recipients/index.php`

**`ajax_import_from_users()`**
- **Purpose:** Import users from database as recipients
- **Criteria:** Only imports active users with order history
- **Process:**
  - Queries users with at least 1 order
  - Validates email addresses
  - Skips duplicate emails
  - Creates custom data with user info (username, email, balance, order count)
- **Performance:** Timeout set to 300 seconds, memory limit 256M
- **Response:** Returns count of imported users
- **Error Handling:** Catches exceptions and logs errors

**`ajax_import_from_csv()`**
- **Purpose:** Import recipients from CSV file upload
- **File Format:** CSV with email,name columns
- **Upload Config:**
  - Allowed types: csv, txt
  - Max size: 5MB
  - Temporary storage path: TEMP_PATH
- **Process:**
  - Validates email addresses
  - Skips duplicates
  - Deletes file after import
- **Response:** Returns count of imported emails

---

#### Email Tracking (Lines 778-814)

**`track($token)`**
- **Purpose:** Track email opens via tracking pixel
- **Access:** Public endpoint (no authentication required)
- **Parameters:** `$token` - Unique tracking token
- **Process:**
  - Finds recipient by tracking token
  - Updates status from `sent` to `opened`
  - Records `opened_at` timestamp
  - Updates email log
  - Refreshes campaign statistics
- **Response:** Returns 1x1 transparent GIF pixel
- **Headers:** No-cache headers to ensure fresh tracking

---

#### Reports & Analytics (Lines 820-871)

**`reports()`**
- **Purpose:** Display comprehensive analytics dashboard
- **Data:**
  - Overall statistics across all campaigns
  - Campaign performance summary
  - Email delivery statistics
  - Campaign status distribution
- **View:** `reports/index.php`

**`export_campaign_report($ids)`**
- **Purpose:** Export campaign data as CSV
- **Data Included:**
  - Email address
  - Recipient name
  - Status
  - Sent timestamp
  - Opened timestamp
  - Error message
- **Format:** CSV file
- **Filename:** `campaign_{ids}_report.csv`
- **Response:** Direct CSV download

---

## Model: email_marketing_model.php

**File:** `/app/modules/email_marketing/models/email_marketing_model.php`

**Lines:** 668

**Class:** `Email_marketing_model extends MY_Model`

### Purpose
The model handles all database operations for the email marketing module. It provides methods for CRUD operations on campaigns, templates, SMTP configurations, recipients, logs, and settings.

### Protected Properties (Database Tables)

```php
protected $tb_campaigns;      // 'email_campaigns'
protected $tb_templates;      // 'email_templates'
protected $tb_smtp_configs;   // 'email_smtp_configs'
protected $tb_recipients;     // 'email_recipients'
protected $tb_logs;           // 'email_logs'
protected $tb_settings;       // 'email_settings'
```

### Key Methods by Category

#### Campaign Methods (Lines 28-212)

**`get_campaigns($limit = -1, $page = -1, $status = null)`**
- **Purpose:** Retrieve campaigns with optional filtering
- **Parameters:**
  - `$limit` - Results limit (-1 for count)
  - `$page` - Offset for pagination
  - `$status` - Filter by campaign status
- **Returns:** 
  - Count (if $limit = -1)
  - Array of campaign objects with joined template and SMTP names
- **Sorting:** Ordered by created_at DESC

**`get_campaign($ids)`**
- **Purpose:** Get single campaign by unique identifier
- **Joins:** Template and SMTP config tables
- **Returns:** Campaign object or null

**`create_campaign($data)`**
- **Purpose:** Create new campaign
- **Auto-Generated:**
  - `ids` - Unique identifier
  - `created_at` - Timestamp
  - `updated_at` - Timestamp
- **Returns:** Boolean success

**`update_campaign($ids, $data)`**
- **Purpose:** Update campaign data
- **Auto-Updated:** `updated_at` timestamp
- **Returns:** Boolean success

**`delete_campaign($ids)`**
- **Purpose:** Delete campaign and cascading data
- **Cascading Deletes:**
  - All campaign recipients
  - All campaign logs
  - Campaign record
- **Returns:** Boolean success

**`update_campaign_stats($campaign_id)`**
- **Purpose:** Recalculate and update campaign statistics
- **Calculations:**
  - Total emails count
  - Sent emails count
  - Failed emails count
  - Opened emails count
  - Bounced emails count
- **Updates:** Campaign record with fresh statistics
- **Returns:** Boolean success

**`reset_failed_recipients($campaign_id)`**
- **Purpose:** Reset failed recipients for retry
- **Process:**
  - Changes status from `failed` to `pending`
  - Clears error message
  - Resets sent_at timestamp
- **Returns:** Number of affected rows

**`get_overall_stats()`**
- **Purpose:** Calculate global email marketing statistics
- **Returns:** Object with:
  - `total_campaigns` - Total campaign count
  - `running_campaigns` - Active campaigns
  - `completed_campaigns` - Finished campaigns
  - `paused_campaigns` - Paused campaigns
  - `pending_campaigns` - Not yet started
  - `total_emails` - Sum of all emails
  - `total_sent` - Successfully sent
  - `total_failed` - Failed deliveries
  - `total_opened` - Opened emails
  - `total_bounced` - Bounced emails
  - `total_remaining` - Pending delivery
  - `open_rate` - Percentage (opened/sent)
  - `failure_rate` - Percentage (failed/total)

**`get_recent_logs($limit = 20)`**
- **Purpose:** Get recent activity across all campaigns
- **Joins:** Campaign table for campaign names
- **Sorting:** Ordered by created_at DESC
- **Returns:** Array of log objects

---

#### Template Methods (Lines 217-282)

**`get_templates($limit = -1, $page = -1)`**
- **Purpose:** Retrieve email templates
- **Filter:** Only active templates (status = 1)
- **Returns:** Count or array of template objects
- **Sorting:** Ordered by created_at DESC

**`get_template($ids)`**
- **Purpose:** Get single template by identifier
- **Returns:** Template object or null

**`create_template($data)`**
- **Purpose:** Create new email template
- **Auto-Generated:** ids, created_at, updated_at
- **Returns:** Boolean success

**`update_template($ids, $data)`**
- **Purpose:** Update template data
- **Auto-Updated:** updated_at
- **Returns:** Boolean success

**`delete_template($ids)`**
- **Purpose:** Delete template
- **Protection:** Prevents deletion if template is used by non-completed campaigns
- **Returns:** Boolean success or false if protected

---

#### SMTP Configuration Methods (Lines 287-369)

**`get_smtp_configs($limit = -1, $page = -1)`**
- **Purpose:** Retrieve SMTP configurations
- **Sorting:** Default configs first, then by created_at DESC
- **Returns:** Count or array of SMTP objects

**`get_smtp_config($ids)`**
- **Purpose:** Get single SMTP configuration
- **Returns:** SMTP object or null

**`get_default_smtp()`**
- **Purpose:** Get the default active SMTP configuration
- **Filters:** is_default = 1 AND status = 1
- **Returns:** SMTP object or null

**`create_smtp_config($data)`**
- **Purpose:** Create SMTP configuration
- **Auto-Generated:** ids, created_at, updated_at
- **Special Logic:** If is_default = 1, unsets all other defaults
- **Returns:** Boolean success

**`update_smtp_config($ids, $data)`**
- **Purpose:** Update SMTP configuration
- **Special Logic:** If is_default = 1, unsets all other defaults
- **Auto-Updated:** updated_at
- **Returns:** Boolean success

**`delete_smtp_config($ids)`**
- **Purpose:** Delete SMTP configuration
- **Protection:** Prevents deletion if used by non-completed campaigns
- **Returns:** Boolean success or false if protected

---

#### Recipient Methods (Lines 374-557)

**`get_recipients($campaign_id, $limit = -1, $page = -1, $status = null)`**
- **Purpose:** Get recipients for a campaign
- **Parameters:**
  - `$campaign_id` - Campaign ID
  - `$limit` - Results limit
  - `$page` - Offset
  - `$status` - Filter by recipient status
- **Sorting:** Ordered by created_at ASC
- **Returns:** Count or array of recipient objects

**`add_recipient($campaign_id, $email, $name = null, $user_id = null, $custom_data = null)`**
- **Purpose:** Add recipient to campaign
- **Auto-Generated:**
  - `ids` - Unique identifier
  - `tracking_token` - MD5 hash for email tracking
  - `created_at`, `updated_at` - Timestamps
- **Initial Status:** `pending`
- **Custom Data:** JSON encoded if provided
- **Returns:** Boolean success

**`import_from_users($campaign_id, $filters = [], $limit = 0)`**
- **Purpose:** Import users from database
- **Query Optimization:** Uses WHERE EXISTS for better performance
- **Selection Criteria:**
  - Active users (status = 1)
  - Has valid email address
  - Has at least 1 order
- **Parameters:**
  - `$limit` - 0 for unlimited import
  - `$filters` - Optional role filter
- **Process:**
  1. Query eligible users
  2. Validate email format
  3. Skip duplicates
  4. Get order count per user
  5. Create custom_data with user info
  6. Add recipient
- **Returns:** Count of imported users
- **Error Handling:** Logs errors and returns 0 on failure

**`import_from_csv($campaign_id, $file_path)`**
- **Purpose:** Import recipients from CSV file
- **File Format:** email,name (header row skipped)
- **Validation:**
  - Checks file exists
  - Validates email format
  - Skips duplicates
- **Returns:** Count of imported emails

**`get_next_pending_recipient($campaign_id)`**
- **Purpose:** Get next recipient to send email to
- **Filter:** status = 'pending'
- **Sorting:** Ordered by ID ASC (FIFO)
- **Limit:** 1 recipient
- **Returns:** Recipient object or null
- **Use Case:** Used by cron job for sequential sending

**`update_recipient_status($recipient_id, $status, $error_message = null)`**
- **Purpose:** Update recipient delivery status
- **Status Handling:**
  - If `sent`: Records sent_at timestamp
  - If `opened`: Records opened_at timestamp
  - If error: Stores error_message
- **Auto-Updated:** updated_at timestamp
- **Returns:** Boolean success

---

#### Log Methods (Lines 563-607)

**`add_log($campaign_id, $recipient_id, $email, $subject, $status, $error_message = null)`**
- **Purpose:** Create activity log entry
- **Auto-Generated:** ids, created_at
- **Recorded Data:**
  - Campaign and recipient IDs
  - Email and subject
  - Status
  - Error message (if applicable)
  - Sent timestamp (if status = sent)
  - IP address (from request)
  - User agent (from request)
- **Returns:** Boolean success

**`get_logs($campaign_id, $limit = -1, $page = -1)`**
- **Purpose:** Retrieve campaign activity logs
- **Sorting:** Ordered by created_at DESC (newest first)
- **Returns:** Count or array of log objects

---

#### Settings Methods (Lines 612-641)

**`get_setting($key, $default = null)`**
- **Purpose:** Retrieve module setting
- **Parameters:**
  - `$key` - Setting key
  - `$default` - Default value if not found
- **Returns:** Setting value or default

**`update_setting($key, $value)`**
- **Purpose:** Update or create setting
- **Logic:** Checks if exists, updates or inserts accordingly
- **Auto-Updated:** updated_at timestamp
- **Returns:** Boolean success

---

#### Helper Methods (Lines 647-666)

**`process_template_variables($template_body, $variables)`**
- **Purpose:** Replace template variables with actual values
- **Default Variables:**
  - `{site_name}` - Website name from options
  - `{site_url}` - Base URL
  - `{current_date}` - Current date
  - `{current_year}` - Current year
- **Custom Variables:** Merged with defaults
- **Process:** String replacement using `{variable}` syntax
- **Returns:** Processed template body
- **Use Case:** Email template rendering

---

## Views

### Dashboard Views

#### index.php (398 lines)
**Purpose:** Main email marketing dashboard

**Sections:**
1. **Overall Performance Stats** (Lines 13-111)
   - Total emails sent (with progress bar)
   - Remaining pending emails
   - Failed emails with failure rate
   - Opened emails with open rate

2. **Campaign Overview** (Lines 114-229)
   - Campaign status distribution chart
   - Running, completed, paused, pending counts
   - Quick stats sidebar

3. **Recent Activity** (Lines 233-274)
   - Last 10 email activity logs
   - Shows campaign, email, subject, status, time
   - Color-coded status badges

4. **Quick Access Cards** (Lines 277-349)
   - Links to Campaigns, Templates, SMTP Config, Reports
   - Icon-based navigation

5. **Getting Started Guide** (Lines 351-397)
   - Setup instructions
   - Cron configuration examples
   - Template variable reference

**Features:**
- Real-time statistics visualization
- Color-coded status indicators
- Progress bars for metrics
- Responsive grid layout

---

### Campaign Views

#### campaigns/index.php (252 lines)
**Purpose:** List all email campaigns

**Features:**
- Pagination (20 per page)
- Campaign status badges (color-coded)
- Progress tracking with visual bars
- Action buttons based on campaign status
- Statistics display (sent, opened, failed)

**Action Buttons:**
- View Details (all campaigns)
- Edit (pending/paused only)
- Start (pending only)
- Pause (running only)
- Resume (paused only)
- Resend Failed (if failures exist)
- Delete (non-running only)

**JavaScript Functions:**
- AJAX action handling with confirmation
- Auto-reload after successful actions
- Form validation

---

#### campaigns/create.php (85 lines)
**Purpose:** Campaign creation modal form

**Form Fields:**
1. Campaign Name (required)
2. Email Template selection (required)
3. SMTP Configuration selection (required)
4. Hourly sending limit (optional)
5. Daily sending limit (optional)

**Features:**
- Modal dialog interface
- Dynamic template/SMTP dropdown population
- Default SMTP pre-selection
- Inline help text
- AJAX form submission

---

#### campaigns/edit.php (83 lines)
**Purpose:** Campaign editing modal form

**Editable Fields:**
- Campaign name
- Email template
- SMTP configuration
- Sending limits (hourly/daily)

**Restrictions:**
- Only editable when status is pending or paused
- Pre-filled with current values

---

#### campaigns/details.php (528 lines)
**Purpose:** Comprehensive campaign dashboard

**Sections:**

1. **Statistics Cards** (Lines 20-87)
   - Total emails
   - Sent emails
   - Opened emails with rate
   - Failed emails

2. **Campaign Information** (Lines 90-236)
   - Status, template, SMTP details
   - Sending limits
   - Timestamps (created, started, completed, last sent)
   - Campaign-specific cron URL
   - **Campaign Health Indicator:**
     - Calculates health score (0-100)
     - Checks failure rate (<10%, <20%)
     - Checks open rate (<10%, <20%)
     - Detects stalled campaigns (>24h no activity)
     - Visual health bar with issues list

3. **Progress Tracking** (Lines 239-294)
   - Completion percentage
   - Remaining emails
   - Open rate
   - Manage recipients button
   - Resend failed emails button

4. **Recent Recipients Table** (Lines 298-364)
   - Shows last 100 recipients
   - Status filtering (all/pending/sent/failed/opened)
   - Individual email resend for failures
   - Color-coded status badges
   - Error message display

5. **Activity Logs** (Lines 388-442)
   - Last 50 email activities
   - Detailed timestamps
   - Status tracking
   - Error messages
   - Individual resend actions

**JavaScript Features:**
- Recipient status filtering
- AJAX resend operations (single/bulk)
- Confirmation dialogs
- Auto-refresh after actions

---

### Template Views

#### templates/index.php (141 lines)
**Purpose:** Email template management

**Table Columns:**
- Template name
- Email subject
- Description (truncated)
- Creation date
- Action buttons (Edit, Delete)

**Features:**
- Pagination support
- Delete protection (templates in use)
- Modal-based editing
- Empty state with CTA

**JavaScript:**
- AJAX delete with confirmation
- Success/error message handling
- Auto-reload after operations

---

#### templates/create.php (63 lines)
**Purpose:** Email template creation form

**Form Fields:**
1. Template Name (required)
2. Email Subject (required) - Supports variables
3. Description (optional)
4. Email Body HTML (required) - Large textarea

**Available Variables Reference:**
- `{username}` - User's name
- `{email}` - User's email
- `{balance}` - User's balance
- `{site_name}` - Website name
- `{site_url}` - Website URL
- `{current_date}` - Current date

**Features:**
- Modal interface
- HTML content support
- Variable helper guide
- AJAX submission

---

#### templates/edit.php (63 lines)
**Purpose:** Email template editing form

**Features:**
- Pre-filled form fields
- Same structure as create form
- Variable reference guide
- HTML content editing

---

### SMTP Configuration Views

#### smtp/index.php (161 lines)
**Purpose:** SMTP server configuration management

**Table Columns:**
- Configuration name
- SMTP host
- Port number
- Encryption type (TLS/SSL/None)
- From email address
- Default flag
- Active/inactive status
- Action buttons

**Features:**
- Default configuration badge
- Status indicators
- Delete protection (configs in use)
- Pagination support

---

#### smtp/create.php (97 lines)
**Purpose:** SMTP configuration creation

**Form Fields:**
1. Configuration Name (required)
2. SMTP Host (required) - e.g., smtp.gmail.com
3. Port (required) - Default: 587
4. Encryption - None/TLS/SSL (default: TLS)
5. Username (required)
6. Password (required)
7. From Name (required)
8. From Email (required)
9. Reply-To Email (optional)
10. Set as Default (checkbox)
11. Active Status (checkbox, default: checked)

**Features:**
- Predefined port defaults
- Encryption selection
- Default SMTP management
- Password security

---

#### smtp/edit.php (Similar to create)
**Purpose:** Edit existing SMTP configuration

**Special Features:**
- Pre-filled values
- Optional password update (only if provided)
- Default flag management

---

### Recipient Views

#### recipients/index.php (220 lines)
**Purpose:** Recipient management for campaigns

**Import Options:**

1. **Import from User Database** (Lines 17-36)
   - Imports active users with order history
   - Automatic filtering
   - One-click import
   - Progress indicator

2. **Import from CSV/TXT** (Lines 38-56)
   - File upload form
   - CSV format: email,name
   - Max file size: 5MB
   - Accepted formats: .csv, .txt

**Recipients Table** (Lines 60-110)
- Shows last 100 recipients
- Columns: Email, Name, Status, Sent At, Opened At
- Color-coded status badges
- Empty state message

**JavaScript Features:**
- AJAX form submission with loading states
- 60-second timeout for imports
- Error handling for timeouts
- Progress indicators
- FormData for file upload
- Auto-reload after import

---

### Report Views

#### reports/index.php (315 lines)
**Purpose:** Analytics and reporting dashboard

**Sections:**

1. **Overall Statistics** (Lines 17-81)
   - Total campaigns
   - Total emails sent
   - Global open rate
   - Global failure rate

2. **Campaign Performance Table** (Lines 84-174)
   - All campaigns listed
   - Success rate calculation
   - Open rate visualization
   - Progress bars for metrics
   - Export CSV button per campaign

3. **Email Delivery Statistics** (Lines 177-236)
   - Total emails vs. successfully sent
   - Sent emails progress
   - Pending emails progress
   - Failed emails progress
   - Opened emails progress

4. **Campaign Status Distribution** (Lines 238-286)
   - Running campaigns percentage
   - Completed campaigns percentage
   - Paused campaigns percentage
   - Pending campaigns percentage

5. **Export Options** (Lines 289-314)
   - Individual campaign CSV export
   - Features overview
   - Link to campaign details

**Calculations:**
- Success Rate = (sent - failed) / total * 100
- Open Rate = opened / sent * 100
- Visual progress bars for all metrics

---

## Database Schema

### Table: email_campaigns

**Purpose:** Store email campaign data and statistics

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `ids` - VARCHAR(32), Unique identifier
- `name` - VARCHAR(255), Campaign name
- `template_id` - INT, Foreign key to email_templates
- `smtp_config_id` - INT, Foreign key to email_smtp_configs
- `status` - ENUM('pending', 'running', 'paused', 'completed', 'cancelled')
- `total_emails` - INT, Total recipients
- `sent_emails` - INT, Successfully sent count
- `failed_emails` - INT, Failed delivery count
- `opened_emails` - INT, Email open count
- `bounced_emails` - INT, Bounced email count
- `sending_limit_hourly` - INT NULL, Max emails per hour
- `sending_limit_daily` - INT NULL, Max emails per day
- `started_at` - DATETIME NULL, Campaign start time
- `completed_at` - DATETIME NULL, Campaign completion time
- `last_sent_at` - DATETIME NULL, Last email sent time
- `created_at` - DATETIME, Creation timestamp
- `updated_at` - DATETIME, Last update timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (ids)
- INDEX (status)
- FOREIGN KEY (template_id) REFERENCES email_templates(id)
- FOREIGN KEY (smtp_config_id) REFERENCES email_smtp_configs(id)

---

### Table: email_templates

**Purpose:** Store email template designs

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `ids` - VARCHAR(32), Unique identifier
- `name` - VARCHAR(255), Template name
- `subject` - VARCHAR(500), Email subject line
- `body` - LONGTEXT, HTML email content
- `description` - TEXT NULL, Template description
- `status` - TINYINT(1), Active flag (1=active, 0=inactive)
- `created_at` - DATETIME, Creation timestamp
- `updated_at` - DATETIME, Last update timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (ids)
- INDEX (status)

---

### Table: email_smtp_configs

**Purpose:** Store SMTP server configurations

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `ids` - VARCHAR(32), Unique identifier
- `name` - VARCHAR(255), Configuration name
- `host` - VARCHAR(255), SMTP server host
- `port` - INT, SMTP port number
- `username` - VARCHAR(255), SMTP username
- `password` - VARCHAR(255), SMTP password (encrypted)
- `encryption` - ENUM('none', 'tls', 'ssl'), Encryption type
- `from_name` - VARCHAR(255), Sender name
- `from_email` - VARCHAR(255), Sender email address
- `reply_to` - VARCHAR(255) NULL, Reply-to email address
- `is_default` - TINYINT(1), Default flag (1=default, 0=not default)
- `status` - TINYINT(1), Active flag (1=active, 0=inactive)
- `created_at` - DATETIME, Creation timestamp
- `updated_at` - DATETIME, Last update timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (ids)
- INDEX (is_default)
- INDEX (status)

---

### Table: email_recipients

**Purpose:** Store campaign recipients and delivery status

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `ids` - VARCHAR(32), Unique identifier
- `campaign_id` - INT, Foreign key to email_campaigns
- `email` - VARCHAR(255), Recipient email address
- `name` - VARCHAR(255) NULL, Recipient name
- `user_id` - INT NULL, Reference to users table (if applicable)
- `custom_data` - TEXT NULL, JSON encoded custom data
- `tracking_token` - VARCHAR(64), Unique token for tracking
- `status` - ENUM('pending', 'sent', 'failed', 'opened', 'bounced')
- `sent_at` - DATETIME NULL, Email sent timestamp
- `opened_at` - DATETIME NULL, Email opened timestamp
- `error_message` - TEXT NULL, Delivery error message
- `created_at` - DATETIME, Creation timestamp
- `updated_at` - DATETIME, Last update timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (ids)
- UNIQUE KEY (tracking_token)
- INDEX (campaign_id, status)
- INDEX (campaign_id, email)
- FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE

---

### Table: email_logs

**Purpose:** Activity log for email sending operations

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `ids` - VARCHAR(32), Unique identifier
- `campaign_id` - INT, Foreign key to email_campaigns
- `recipient_id` - INT NULL, Foreign key to email_recipients
- `email` - VARCHAR(255), Recipient email
- `subject` - VARCHAR(500), Email subject
- `status` - ENUM('sent', 'failed', 'opened', 'bounced')
- `error_message` - TEXT NULL, Error details
- `sent_at` - DATETIME NULL, Send timestamp
- `opened_at` - DATETIME NULL, Open timestamp
- `ip_address` - VARCHAR(45) NULL, Request IP address
- `user_agent` - VARCHAR(500) NULL, Request user agent
- `created_at` - DATETIME, Log creation timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (ids)
- INDEX (campaign_id, status)
- INDEX (recipient_id)
- INDEX (created_at)
- FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE
- FOREIGN KEY (recipient_id) REFERENCES email_recipients(id) ON DELETE SET NULL

---

### Table: email_settings

**Purpose:** Module configuration settings

**Columns:**
- `id` - INT, Primary Key, Auto Increment
- `setting_key` - VARCHAR(100), Setting identifier
- `setting_value` - TEXT, Setting value
- `updated_at` - DATETIME, Last update timestamp

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY (setting_key)

**Example Settings:**
- `email_cron_token` - Security token for cron endpoints
- `sending_batch_size` - Number of emails to send per cron execution
- `throttle_delay` - Delay between email sends (seconds)

---

## Workflows and Interactions

### Campaign Lifecycle

```
1. CREATE CAMPAIGN
   ├─ User: campaigns/create (Modal Form)
   ├─ Controller: ajax_campaign_create()
   ├─ Model: create_campaign()
   ├─ Database: INSERT into email_campaigns
   └─ Status: 'pending'

2. ADD RECIPIENTS
   ├─ User: recipients/{campaign_ids}
   ├─ Option A: Import from Users
   │  ├─ Controller: ajax_import_from_users()
   │  ├─ Model: import_from_users()
   │  └─ Database: INSERT into email_recipients
   └─ Option B: Import from CSV
      ├─ Controller: ajax_import_from_csv()
      ├─ Model: import_from_csv()
      └─ Database: INSERT into email_recipients

3. START CAMPAIGN
   ├─ User: Click "Start" button
   ├─ Controller: ajax_campaign_start()
   ├─ Validation: Check recipients > 0
   ├─ Model: update_campaign()
   ├─ Database: UPDATE email_campaigns SET status='running'
   └─ Status: 'running'

4. SEND EMAILS (Via Cron Job)
   ├─ Cron: /cron/email_marketing?token={token}&campaign_id={id}
   ├─ For each pending recipient:
   │  ├─ Model: get_next_pending_recipient()
   │  ├─ Model: process_template_variables()
   │  ├─ Send email via SMTP
   │  ├─ Model: update_recipient_status()
   │  ├─ Model: add_log()
   │  └─ Model: update_campaign_stats()
   └─ Continue until limit reached or no pending

5. TRACK OPENS
   ├─ Email: Contains tracking pixel with token
   ├─ User: Opens email in email client
   ├─ Request: /email_marketing/track/{token}
   ├─ Controller: track()
   ├─ Model: update_recipient_status('opened')
   └─ Database: UPDATE email_recipients, email_logs

6. MONITOR PROGRESS
   ├─ User: campaign_details/{ids}
   ├─ Controller: campaign_details()
   ├─ Model: update_campaign_stats()
   ├─ Display: Real-time statistics
   └─ View: Recent recipients & logs

7. HANDLE FAILURES
   ├─ User: Click "Resend Failed"
   ├─ Controller: ajax_campaign_resend_failed()
   ├─ Model: reset_failed_recipients()
   ├─ Database: UPDATE email_recipients SET status='pending'
   └─ Cron will retry sending

8. COMPLETE CAMPAIGN
   ├─ Condition: All emails sent or failed
   ├─ Cron: Detects no pending recipients
   ├─ Model: update_campaign()
   └─ Status: 'completed'
```

### Template Variable Processing

```
1. Template Creation
   ├─ User: Creates template with {variables}
   └─ Database: Stores raw template with placeholders

2. Email Sending
   ├─ Model: get_template()
   ├─ Model: get_recipient()
   ├─ Prepare custom_data (username, email, balance, etc.)
   ├─ Model: process_template_variables()
   │  ├─ Merge default variables (site_name, site_url, etc.)
   │  ├─ Merge custom recipient data
   │  └─ String replace {variable} with actual values
   └─ Send processed email

3. Tracking Pixel Insertion
   ├─ Generate tracking_token
   ├─ Create tracking URL: /email_marketing/track/{token}
   ├─ Insert 1x1 pixel in email body
   └─ Track opens when pixel loads
```

### Import Workflow

```
Import from Users Database:
├─ Query: Active users with orders
├─ For each user:
│  ├─ Validate email format
│  ├─ Check for duplicates
│  ├─ Get order count
│  ├─ Build custom_data JSON
│  └─ Insert recipient
└─ Return count

Import from CSV:
├─ Upload file (max 5MB)
├─ Read CSV rows
├─ For each row:
│  ├─ Parse email,name
│  ├─ Validate email format
│  ├─ Check for duplicates
│  └─ Insert recipient
├─ Delete uploaded file
└─ Return count
```

### SMTP Configuration Flow

```
1. Create/Update SMTP Config
   ├─ User: Enters SMTP details
   ├─ If 'is_default' checked:
   │  ├─ Model: Unset all other defaults
   │  └─ Set this config as default
   └─ Database: Store encrypted password

2. Campaign Uses SMTP
   ├─ Campaign references smtp_config_id
   ├─ During sending:
   │  ├─ Load SMTP config
   │  ├─ Configure PHPMailer/Email library
   │  └─ Send via configured SMTP

3. Protection
   ├─ Cannot delete SMTP in use by active campaigns
   └─ Check non-completed campaigns before deletion
```

---

## Features Summary

### Core Features

1. **Campaign Management**
   - Create, edit, delete campaigns
   - Start, pause, resume campaigns
   - Set hourly/daily sending limits
   - Campaign status tracking
   - Health monitoring with issue detection

2. **Template System**
   - HTML email templates
   - Variable substitution system
   - Template reusability
   - Protection against deletion when in use

3. **SMTP Configuration**
   - Multiple SMTP server support
   - TLS/SSL encryption
   - Default SMTP selection
   - Active/inactive status
   - Protection against deletion when in use

4. **Recipient Management**
   - Import from user database (with order history filter)
   - Import from CSV files
   - Duplicate detection
   - Custom data storage
   - Status tracking per recipient

5. **Email Tracking**
   - Open tracking via pixel
   - Delivery status tracking
   - Bounce detection
   - Failure logging with error messages

6. **Advanced Features**
   - Failed email retry (bulk and individual)
   - Campaign-specific cron URLs
   - Real-time statistics
   - Progress visualization
   - Activity logging

7. **Reporting & Analytics**
   - Overall statistics dashboard
   - Per-campaign performance metrics
   - Open rate calculation
   - Failure rate analysis
   - CSV export for campaigns
   - Health scoring system

### Security Features

1. **Access Control**
   - Admin-only access
   - Permission validation in constructor

2. **AJAX Security**
   - CSRF token validation
   - `_is_ajax()` verification

3. **Data Protection**
   - Password encryption for SMTP
   - XSS prevention (except HTML templates)
   - SQL injection prevention via Active Record

4. **Deletion Protection**
   - Templates in use cannot be deleted
   - SMTP configs in use cannot be deleted
   - Cascading delete for campaign data

### Performance Optimizations

1. **Database Queries**
   - WHERE EXISTS for user imports (faster than JOIN)
   - Indexed columns for quick lookups
   - Pagination for large datasets
   - Limit clauses to prevent memory issues

2. **Resource Management**
   - 300-second timeout for imports
   - 256MB memory limit for bulk operations
   - File size limits (5MB) for uploads
   - Batch processing via cron

3. **Caching Considerations**
   - No-cache headers for tracking pixels
   - Statistics recalculation on demand

---

## Integration Points

### With Main Application

1. **User System**
   - Imports users from `USERS` table
   - Filters by order history from `ORDER` table
   - Uses `first_name`, `email`, `balance` fields

2. **Template System**
   - Uses main template builder: `$this->template->build()`
   - Integrates with site theme

3. **Helper Functions**
   - `cn()` - Clean URL generation
   - `post()` - POST data retrieval
   - `get_option()` - Site settings
   - `NOW` - Current timestamp constant
   - `ids()` - Unique ID generation
   - `get_role()` - User role checking

4. **Libraries**
   - Upload library for CSV imports
   - Email library for SMTP sending (implied)

### Cron Integration

**Endpoint:** `/cron/email_marketing`

**Parameters:**
- `token` - Security token (from email_cron_token setting)
- `campaign_id` (optional) - Specific campaign to process

**Process:**
1. Validate token
2. Get running campaigns (or specific campaign)
3. For each campaign:
   - Check sending limits
   - Get next pending recipient
   - Process template
   - Send email
   - Update status
   - Log activity
   - Update statistics
4. Mark completed if no pending

---

## Best Practices Implemented

1. **Code Organization**
   - Clear separation of concerns (MVC)
   - Logical method grouping
   - Consistent naming conventions

2. **Error Handling**
   - Try-catch blocks for imports
   - Validation before operations
   - User-friendly error messages
   - Error logging

3. **User Experience**
   - Modal forms for quick actions
   - AJAX for seamless interactions
   - Progress indicators
   - Confirmation dialogs for destructive actions
   - Empty states with CTAs

4. **Data Integrity**
   - Foreign key constraints
   - Cascading deletes
   - Duplicate prevention
   - Email validation

5. **Scalability**
   - Pagination throughout
   - Configurable limits
   - Efficient queries
   - Background processing via cron

---

## Future Enhancement Opportunities

1. **Email Testing**
   - Send test email functionality
   - Template preview
   - SMTP connection testing

2. **Advanced Scheduling**
   - Schedule campaigns for future dates
   - Recurring campaigns
   - Time zone support

3. **Segmentation**
   - Advanced user filtering
   - Custom segments
   - A/B testing

4. **Enhanced Tracking**
   - Click tracking
   - Conversion tracking
   - Geographic tracking

5. **Template Builder**
   - WYSIWYG editor
   - Pre-built templates
   - Drag-and-drop builder

6. **Bounce Handling**
   - Automatic bounce detection
   - Soft vs. hard bounce classification
   - Blacklist management

7. **Performance Reports**
   - Graphical charts
   - Trend analysis
   - Comparison reports
   - Export to PDF

---

## Conclusion

The Email Marketing module is a robust, feature-complete solution for managing email campaigns within the SMM Panel application. It follows MVC architecture principles, implements proper security measures, and provides comprehensive functionality for creating, managing, tracking, and analyzing email marketing campaigns.

**Key Strengths:**
- Well-structured codebase with clear separation of concerns
- Comprehensive feature set covering all aspects of email marketing
- Strong data validation and security measures
- Efficient database design with proper indexing
- User-friendly interface with AJAX interactions
- Detailed tracking and reporting capabilities
- Scalable architecture supporting high-volume campaigns

**Module Maturity:** Production-ready with enterprise-grade features suitable for SMM panel email marketing needs.
