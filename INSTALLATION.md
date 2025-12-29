# Quick Installation Guide - WhatsApp Authentication Enhancement

## Prerequisites
- Existing SMM Panel with Google OAuth configured
- WhatsApp API configured in settings
- Database access (MySQL/MariaDB)

## Installation Steps

### Step 1: Database Migration
Run the SQL migration file:

**Option A: Command Line**
```bash
mysql -u your_username -p your_database_name < database/whatsapp_auth_enhancement.sql
```

**Option B: phpMyAdmin**
1. Open phpMyAdmin
2. Select your database
3. Click "Import"
4. Choose `database/whatsapp_auth_enhancement.sql`
5. Click "Go"

### Step 2: Verify Files
Ensure these new files are present:
- ✅ `app/libraries/Whatsapp_otp.php`
- ✅ `themes/pergo/views/whatsapp_setup.php`
- ✅ `database/whatsapp_auth_enhancement.sql`

And these files are updated:
- ✅ `app/modules/auth/controllers/auth.php`
- ✅ `app/modules/setting/views/google_oauth.php`
- ✅ `app/modules/users/views/index.php`

### Step 3: Configure WhatsApp API
1. Login to admin panel
2. Go to **Settings** > **WhatsApp Notifications**
3. Fill in:
   - API URL (your WhatsApp API endpoint)
   - API Key (your API authentication key)
   - Admin Phone Number (with country code, e.g., +923001234567)
4. Click **Save API Configuration**

### Step 4: Configure Google OAuth (if not already done)
1. Go to **Settings** > **Google OAuth**
2. Enable Google Login
3. Add your Google Client ID
4. Add your Google Client Secret
5. Copy the Authorized Redirect URI and add it to your Google Cloud Console
6. Click **Save**

### Step 5: Enable/Disable WhatsApp OTP Verification
1. Stay on **Settings** > **Google OAuth**
2. Scroll to "WhatsApp Verification Settings"
3. Toggle **Enable WhatsApp OTP Verification**:
   - **ON**: Users must verify OTP sent to WhatsApp
   - **OFF**: WhatsApp numbers accepted without OTP
4. Click **Save**

### Step 6: Test the Implementation

#### Test 1: New Google Sign-Up
1. Logout from admin
2. Go to login page
3. Click "Sign in with Google"
4. Authenticate with Google
5. You should be redirected to WhatsApp setup page
6. Enter country code and phone number
7. If OTP is enabled, verify the OTP
8. You should be redirected to dashboard

#### Test 2: Manual Sign-Up
1. Go to sign-up page
2. Fill in the form with email, password, and WhatsApp number
3. Submit the form
4. Verify you go directly to dashboard (no additional WhatsApp step)

#### Test 3: Existing Google User
1. Login with an existing Google account
2. If they haven't completed WhatsApp setup, they'll be redirected to setup page
3. Complete the setup
4. On next login, they should go directly to dashboard

### Step 7: Verify Users Module
1. Login as admin
2. Go to **Users** module
3. Check that:
   - Google users show blue Google icon
   - Manual users show green user icon
   - WhatsApp verification status is shown for Google users

## Configuration Options

### WhatsApp OTP Settings
Located in: **Settings** > **Google OAuth** > **WhatsApp Verification Settings**

**Enable WhatsApp OTP Verification** (Toggle)
- **Enabled (1)**: Users must verify OTP sent to their WhatsApp number
  - OTP expires in 10 minutes
  - Max 3 verification attempts
  - 60-second cooldown on resend
- **Disabled (0)**: WhatsApp numbers are accepted without verification
  - Number is marked as verified automatically
  - Users go directly to dashboard after entering number

### WhatsApp API Settings
Located in: **Settings** > **WhatsApp Notifications**

**Required Configuration:**
- **API URL**: Your WhatsApp API endpoint (e.g., https://api.example.com/send)
- **API Key**: Authentication key for your WhatsApp API
- **Admin Phone Number**: Admin's WhatsApp number (e.g., +923001234567)

## Troubleshooting

### Issue: OTP Not Sending
**Solution:**
1. Verify WhatsApp API is configured correctly
2. Check API URL and API Key in settings
3. Ensure WhatsApp OTP notification template exists (check `whatsapp_notifications` table)
4. Check server logs for errors

### Issue: Users Can't Access Dashboard After Google Login
**Solution:**
1. Check if they completed WhatsApp setup
2. Verify `whatsapp_setup_completed = 1` in database
3. Clear browser cache and try again

### Issue: Manual Sign-Up Not Working
**Solution:**
1. Verify database migration was successful
2. Check if `signup_type` column exists in `general_users` table
3. Ensure manual signup users have `whatsapp_setup_completed = 1`

### Issue: Database Migration Failed
**Solution:**
1. Check if columns already exist (may be safe to ignore "duplicate column" errors)
2. Manually run each SQL statement one by one
3. Verify all tables and columns exist after migration

## Database Changes Verification

Run these queries to verify the migration was successful:

```sql
-- Check if columns exist
DESCRIBE general_users;
-- Should show: signup_type, whatsapp_verified, whatsapp_setup_completed

-- Check if OTP table exists
DESCRIBE whatsapp_otp_verification;

-- Check if setting exists
SELECT * FROM general_options WHERE name = 'whatsapp_otp_verification_enabled';

-- Check if notification template exists
SELECT * FROM whatsapp_notifications WHERE event_type = 'whatsapp_otp';
```

## Default Values

After migration:
- `signup_type`: 'manual' (default for new users)
- `whatsapp_verified`: 0 (not verified)
- `whatsapp_setup_completed`: 0 (not completed)
- `whatsapp_otp_verification_enabled`: 0 (disabled by default)

For existing users:
- All marked as `whatsapp_setup_completed = 1` (backward compatibility)
- Users with WhatsApp numbers marked as `whatsapp_verified = 1`
- Google users updated to `signup_type = 'google'`
- Other users updated to `signup_type = 'manual'`

## Support & Documentation

For detailed information, see:
- **Full Documentation**: `WHATSAPP_AUTH_ENHANCEMENT.md`
- **Database Schema**: `database/whatsapp_auth_enhancement.sql`
- **API Documentation**: See "API Endpoints" section in main README

## Security Notes

- OTP codes are 6 digits (100,000 - 999,999)
- OTP expires after 10 minutes
- Maximum 3 verification attempts per OTP
- 60-second cooldown between OTP resends
- All validations are server-side
- Session checks prevent bypassing setup
- International phone number format validation

## Rollback (If Needed)

If you need to rollback:

1. **Remove new columns:**
```sql
ALTER TABLE general_users 
  DROP COLUMN signup_type,
  DROP COLUMN whatsapp_verified,
  DROP COLUMN whatsapp_setup_completed;

DROP TABLE whatsapp_otp_verification;

DELETE FROM general_options WHERE name = 'whatsapp_otp_verification_enabled';
DELETE FROM whatsapp_notifications WHERE event_type = 'whatsapp_otp';
```

2. **Restore original files** (if you have backups)

3. **Clear cache** and test

---

## Quick Checklist

- [ ] Database migration completed
- [ ] WhatsApp API configured
- [ ] Google OAuth configured
- [ ] WhatsApp OTP setting configured
- [ ] Tested new Google sign-up
- [ ] Tested manual sign-up (unchanged)
- [ ] Tested existing Google users
- [ ] Verified users module displays correctly
- [ ] Checked logs for errors

**All done? You're ready to go! 🚀**
