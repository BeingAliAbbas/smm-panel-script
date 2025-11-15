# Email Logs Page - Feature Documentation

## Overview
A comprehensive, dedicated email logs page with advanced filtering, pagination, and export capabilities.

## Access
- **URL**: `/email_marketing/logs`
- **Dashboard Link**: Quick Access section or "View All Logs" button in Recent Activity

## Features

### 1. Advanced Filtering
Filter logs by multiple criteria:
- **Campaign**: Select specific campaign from dropdown
- **Status**: Filter by email status (queued, sent, failed, opened, bounced)
- **Email**: Search by recipient email address
- **Date Range**: Filter by date from/to

### 2. Pagination
- **50 logs per page**
- Full pagination controls with:
  - Previous/Next buttons
  - Page numbers (with ellipsis for large ranges)
  - Shows current range (e.g., "Showing 1 to 50 of 234 entries")

### 3. Detailed Log Information
Each log entry displays:
- **Campaign Name**: Which campaign sent the email
- **Email & Recipient**: Email address and recipient name
- **Subject**: Email subject line (truncated if long)
- **Status**: Color-coded badge (green for sent, red for failed, blue for opened, etc.)
- **Sent At**: Date and time email was sent
- **Additional Details**:
  - Opened timestamp (if email was opened)
  - Error message (for failed emails)
  - IP address of sender
  - Creation timestamp

### 4. Actions
- **Delete**: Remove individual log entries
- **Export to CSV**: Download filtered logs with all details

### 5. Responsive Design
- Works on desktop, tablet, and mobile
- Table scrolls horizontally on small screens
- Filters collapse on mobile for better UX

## Color-Coded Status Badges

| Status | Color | Icon | Meaning |
|--------|-------|------|---------|
| Queued | Gray | Clock | Email is queued for sending |
| Sent | Green | Check | Email successfully sent |
| Failed | Red | X | Email failed to send |
| Opened | Blue | Mail | Recipient opened the email |
| Bounced | Yellow | Alert Triangle | Email bounced back |

## CSV Export Format
Exported CSV includes:
1. Campaign
2. Email
3. Recipient
4. Subject
5. Status
6. Sent At
7. Opened At
8. Error Message
9. IP Address
10. Created At

## Use Cases

### 1. Troubleshooting Failed Emails
- Filter by status = "failed"
- Review error messages
- Identify patterns (SMTP issues, invalid emails, etc.)

### 2. Campaign Performance Analysis
- Select specific campaign
- See delivery and open rates
- Export for external analysis

### 3. Compliance & Audit Trail
- Export logs for specific date range
- Maintain records of all email communications
- Verify email delivery for specific recipients

### 4. Duplicate Prevention Verification
- Search for specific email address
- Verify no duplicate sends within campaign
- Check error messages for "Duplicate recipient" entries

## Technical Implementation

### Model Method
```php
get_all_logs($limit, $page, $filters)
```
- Supports filtering by campaign_id, status, email, date_from, date_to
- Joins with campaigns and recipients tables for full details
- Returns count when limit = -1 for pagination

### Controller Methods
1. `logs($page)` - Main page with filtering
2. `ajax_delete_log()` - AJAX endpoint for deletion
3. `export_logs()` - CSV export with filters

### Database Query
Efficient query with:
- LEFT JOINs for campaign and recipient names
- WHERE clauses for all filters
- ORDER BY created_at DESC (newest first)
- LIMIT/OFFSET for pagination

## Performance
- Indexed columns used in WHERE clauses
- Pagination prevents loading all logs at once
- Export limited to 10,000 records to prevent timeouts
- AJAX deletion for smooth UX

## Navigation
- **Dashboard** → "Email Logs" card in Quick Access
- **Dashboard** → "View All Logs" button in Recent Activity
- **Direct URL**: `/email_marketing/logs`

## Screenshots

### Main Logs Page
Shows table with all logs, status badges, and pagination controls

### Filters Section
Collapsible filter panel with campaign, status, email, and date filters

### Empty State
Clean empty state when no logs match filters, with helpful message

## Integration with Duplicate Prevention
The logs page shows entries marked as "Duplicate recipient" in the error message, making it easy to:
- Verify duplicate prevention is working
- See which duplicate attempts were blocked
- Monitor the effectiveness of the multi-layer protection system
