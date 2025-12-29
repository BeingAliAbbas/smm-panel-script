# WhatsApp Authentication Enhancement

This enhancement adds mandatory WhatsApp number verification for Google sign-in/sign-up users, with optional OTP verification that can be controlled from the settings page.

## Features

### 1. Post-Google Login Flow (Mandatory Step)
- After successful Google sign-in/sign-up, users are redirected to a dedicated WhatsApp number setup page
- Users must enter their WhatsApp number to proceed
- Login/session is NOT fully completed until this step is completed
- Users cannot access dashboard or any protected page without finishing this step

### 2. WhatsApp OTP Verification (Configurable)
- Admin can enable or disable WhatsApp OTP verification from `/settings` under Google OAuth settings
- **If WhatsApp OTP verification is ENABLED:**
  - OTP is sent to the entered WhatsApp number
  - User must verify OTP to proceed
  - OTP expires after 10 minutes
  - Maximum 3 verification attempts allowed
  - Resend OTP functionality with 60-second cooldown
- **If WhatsApp OTP verification is DISABLED:**
  - OTP sending and verification are skipped
  - WhatsApp number is marked as verified automatically
- All other flows remain unchanged regardless of this setting

### 3. WhatsApp Number Input
- Supports WhatsApp numbers for all countries
- Country selector with country code provided
- Phone number validation ensures proper format
- Numbers are stored in standardized international format (e.g., +923001234567)

### 4. User Type Identification
- Users module clearly marks:
  - Manual signup users (marked with green user icon)
  - Google signup users (marked with blue Google icon)
- WhatsApp verification status shown for Google users (✓ verified or ⚠ not verified)
- Signup method stored in database (`signup_type` = manual / google)

### 5. Data Storage
- WhatsApp number and verification status stored in `general_users` table
- OTP verification logs stored in `whatsapp_otp_verification` table
- Verification status persists across sessions

### 6. Security & Integrity
- All validations are server-side
- Session checks prevent bypassing the WhatsApp setup step
- OTP logic does not rely on client-side trust
- International phone number format validation
- Rate limiting on OTP resend (60-second cooldown)
- Maximum attempts limit (3 attempts per OTP)
- Automatic OTP expiry (10 minutes)

## Installation

### Database Migration

Run the SQL migration file to add required tables and columns:

```bash
mysql -u username -p database_name < database/whatsapp_auth_enhancement.sql
```

Or import it via phpMyAdmin.

The migration will:
- Add `signup_type`, `whatsapp_verified`, and `whatsapp_setup_completed` columns to `general_users` table
- Create `whatsapp_otp_verification` table for OTP tracking
- Add `whatsapp_otp_verification_enabled` setting
- Add WhatsApp OTP notification template
- Update existing users with appropriate signup types

### Configuration

1. **Enable Google OAuth:**
   - Go to `Settings > Google OAuth`
   - Enable Google Login
   - Add your Google Client ID and Client Secret
   - Save settings

2. **Configure WhatsApp API:**
   - Go to `Settings > WhatsApp Notifications`
   - Configure WhatsApp API URL, API Key, and Admin Phone
   - Save API configuration

3. **Enable/Disable WhatsApp OTP Verification:**
   - Go to `Settings > Google OAuth`
   - Scroll to "WhatsApp Verification Settings"
   - Toggle "Enable WhatsApp OTP Verification"
   - Save settings

## How It Works

### For New Google Sign-Up Users:

1. User clicks "Sign in with Google" on login page
2. User authenticates with Google
3. System creates new user account with `signup_type = 'google'`
4. User is redirected to `/auth/whatsapp_setup` page
5. User selects country code and enters phone number
6. **If OTP is enabled:**
   - System sends OTP to WhatsApp number
   - User enters OTP code
   - System verifies OTP
   - Upon successful verification, user is redirected to dashboard
7. **If OTP is disabled:**
   - System saves WhatsApp number directly
   - Number is marked as verified automatically
   - User is redirected to dashboard

### For Existing Google Users:

1. User signs in with Google
2. System checks if `whatsapp_setup_completed` is false
3. If not completed, user is redirected to `/auth/whatsapp_setup` page
4. Follows same flow as new users
5. If already completed, user goes directly to dashboard

### For Manual Sign-Up Users:

- Manual signup flow remains **completely unchanged**
- Users provide WhatsApp number during signup
- `signup_type` is set to 'manual'
- WhatsApp number is marked as verified automatically
- No additional WhatsApp setup step required

## Files Modified/Created

### New Files:
- `app/libraries/Whatsapp_otp.php` - WhatsApp OTP library for generation, sending, and verification
- `themes/pergo/views/whatsapp_setup.php` - WhatsApp setup page view
- `database/whatsapp_auth_enhancement.sql` - Database migration file

### Modified Files:
- `app/modules/auth/controllers/auth.php` - Updated Google callback and added WhatsApp setup methods
- `app/modules/setting/views/google_oauth.php` - Added WhatsApp OTP verification toggle
- `app/modules/users/views/index.php` - Added signup type and verification status display

## Database Schema

### `general_users` table (new columns):
```sql
signup_type VARCHAR(20) DEFAULT 'manual' -- 'manual' or 'google'
whatsapp_verified TINYINT(1) DEFAULT 0 -- WhatsApp number verification status
whatsapp_setup_completed TINYINT(1) DEFAULT 0 -- Whether WhatsApp setup step completed
```

### `whatsapp_otp_verification` table (new):
```sql
id INT(11) PRIMARY KEY AUTO_INCREMENT
user_id INT(11) NOT NULL
whatsapp_number VARCHAR(20) NOT NULL
otp_code VARCHAR(6) NOT NULL
created_at DATETIME NOT NULL
expires_at DATETIME NOT NULL
verified_at DATETIME
attempts INT(11) DEFAULT 0
status ENUM('pending', 'verified', 'expired', 'failed')
```

### `general_options` table (new setting):
```sql
whatsapp_otp_verification_enabled = '0' or '1'
```

### `whatsapp_notifications` table (new template):
```sql
event_type = 'whatsapp_otp'
```

## API Endpoints

### `POST /auth/ajax_whatsapp_setup`
Saves WhatsApp number without OTP verification (when OTP is disabled)

**Parameters:**
- `whatsapp_number` (string, required) - Full WhatsApp number in international format

**Response:**
```json
{
  "status": "success",
  "message": "WhatsApp number saved successfully!"
}
```

### `POST /auth/ajax_send_otp`
Sends OTP to WhatsApp number

**Parameters:**
- `whatsapp_number` (string, required) - Full WhatsApp number in international format

**Response:**
```json
{
  "status": "success",
  "message": "OTP sent successfully to your WhatsApp number",
  "otp_id": 123,
  "expires_in": 10
}
```

### `POST /auth/ajax_verify_otp`
Verifies OTP code

**Parameters:**
- `otp_code` (string, required) - 6-digit OTP code

**Response:**
```json
{
  "status": "success",
  "message": "WhatsApp number verified successfully!"
}
```

## Settings

All settings are managed in the admin panel:

### Google OAuth Settings (`/settings` > Google OAuth)
- **Enable Google Login** - Turn Google sign-in on/off
- **Google Client ID** - OAuth 2.0 Client ID
- **Google Client Secret** - OAuth 2.0 Client Secret
- **Enable WhatsApp OTP Verification** - Toggle OTP verification on/off

### WhatsApp Notifications (`/settings` > WhatsApp Notifications)
- **API URL** - WhatsApp API endpoint
- **API Key** - WhatsApp API authentication key
- **Admin Phone Number** - Admin's WhatsApp number for notifications
- **WhatsApp OTP Template** - Message template for OTP (auto-created)

## Testing

### Test Scenarios:

1. **New Google Sign-Up with OTP Enabled:**
   - Enable WhatsApp OTP verification
   - Sign up with a new Google account
   - Verify WhatsApp setup page appears
   - Enter phone number and verify OTP works
   - Confirm user can access dashboard after verification

2. **New Google Sign-Up with OTP Disabled:**
   - Disable WhatsApp OTP verification
   - Sign up with a new Google account
   - Verify WhatsApp setup page appears
   - Enter phone number (no OTP required)
   - Confirm user can access dashboard immediately

3. **Existing Google User Without WhatsApp:**
   - Create a Google user (or use existing)
   - Set `whatsapp_setup_completed = 0`
   - Sign in with Google
   - Verify redirect to WhatsApp setup page

4. **Manual Sign-Up:**
   - Sign up manually with email/password
   - Verify process works exactly as before
   - Confirm no WhatsApp setup page appears

5. **Session Security:**
   - Try to access dashboard URL directly without completing WhatsApp setup
   - Verify redirect to WhatsApp setup page

6. **OTP Security:**
   - Test OTP expiry after 10 minutes
   - Test maximum 3 attempts limit
   - Test resend cooldown (60 seconds)

## Troubleshooting

### OTP Not Sending
- Check WhatsApp API configuration in settings
- Verify API URL and API Key are correct
- Check WhatsApp notification template exists (event_type = 'whatsapp_otp')
- Check server logs for errors

### Users Stuck on WhatsApp Setup Page
- Check if `whatsapp_setup_completed` column exists
- Verify user session is active
- Check if WhatsApp OTP library is loaded correctly

### Manual Signup Not Working
- Verify manual signup users have `signup_type = 'manual'`
- Check if `whatsapp_setup_completed = 1` for manual users
- Verify no redirect logic is interfering

## Support

For issues or questions:
1. Check the logs in `app/logs/`
2. Verify database migrations were applied correctly
3. Check WhatsApp API configuration
4. Review Google OAuth configuration

## Notes

- Manual signup flow is **completely unchanged** and works independently
- Google users cannot bypass WhatsApp setup - it's mandatory
- OTP verification can be toggled on/off anytime from settings
- All existing users are marked as having completed setup (backward compatibility)
- The system supports international phone numbers for all countries
