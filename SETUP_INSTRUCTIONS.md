# WhatsApp Verification Setup Instructions

Follow these steps to enable WhatsApp verification for Google Sign-In users.

## Prerequisites

- SMM Panel installed and working
- Google OAuth configured and working
- WhatsApp API configured in admin panel
- Database access

## Step 1: Database Migration

Run the migration SQL file to add required columns and notification template:

### Option A: Via MySQL Command Line
```bash
mysql -u your_username -p your_database_name < database/whatsapp-verification-enhancement.sql
```

### Option B: Via phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Click "Import" tab
4. Choose file: `database/whatsapp-verification-enhancement.sql`
5. Click "Go" to execute

### Option C: Manual Execution
Copy and paste the SQL content from `database/whatsapp-verification-enhancement.sql` into your database query tool and execute.

## Step 2: Verify Installation

### 2.1 Check Database Changes
Verify the following columns exist in `general_users` table:
```sql
DESCRIBE general_users;
```

Look for these new columns:
- `whatsapp_verified` (TINYINT)
- `whatsapp_otp` (VARCHAR)
- `whatsapp_otp_expires_at` (DATETIME)
- `whatsapp_otp_attempts` (INT)
- `signup_type` (ENUM)

### 2.2 Check Notification Template
Verify the OTP notification template exists:
```sql
SELECT * FROM whatsapp_notifications WHERE event_type = 'otp_verification';
```

Expected result: One row with the OTP verification template.

## Step 3: Configure WhatsApp Notification

### 3.1 Access Admin Panel
1. Log into your admin panel
2. Go to Settings → WhatsApp Configuration

### 3.2 Verify Configuration
Ensure these are configured:
- ✅ WhatsApp API URL
- ✅ WhatsApp API Key
- ✅ Admin Phone Number

### 3.3 Enable OTP Notification
1. Go to WhatsApp Notifications settings
2. Find "OTP Verification" notification
3. Ensure it's **Enabled** (Status = Active)
4. Review the template message (optional)

## Step 4: Test the Flow

### Test 1: New Google User Sign-Up
1. Open your site in incognito/private window
2. Click "Sign in with Google"
3. Complete Google authentication
4. **Expected**: Redirected to WhatsApp verification page
5. Enter country code and phone number
6. Click "Send OTP"
7. **Expected**: OTP sent to WhatsApp
8. Enter the OTP code
9. **Expected**: Verified and redirected to dashboard

### Test 2: Existing Google User (No WhatsApp)
1. Use an existing Google user account without WhatsApp verified
2. Sign in with Google
3. **Expected**: Redirected to WhatsApp verification page
4. Complete verification
5. **Expected**: Redirected to dashboard

### Test 3: Existing Google User (With WhatsApp)
1. Use an existing Google user with verified WhatsApp
2. Sign in with Google
3. **Expected**: Directly redirected to dashboard (no verification page)

### Test 4: Manual Sign-Up User
1. Go to manual sign-up page
2. Complete registration with all fields including WhatsApp
3. **Expected**: Account created and immediately logged in
4. **Expected**: No additional WhatsApp verification required

### Test 5: Dashboard Access Protection
1. Log in via Google without WhatsApp verification
2. Try to directly access `/statistics` or any dashboard page
3. **Expected**: Automatically redirected to `/whatsapp_verify`

## Step 5: Verify Hooks are Enabled

Check that hooks are enabled in your configuration:

```bash
grep "enable_hooks" app/config/config.php
```

Expected output:
```php
$config['enable_hooks'] = TRUE;
```

If it shows `FALSE`, the system should have automatically enabled it. If not, manually change it to `TRUE`.

## Troubleshooting

### Issue: OTP Not Received

**Possible Causes:**
1. WhatsApp API not configured correctly
2. Phone number format incorrect
3. WhatsApp notification template disabled

**Solutions:**
1. Check WhatsApp API configuration in admin panel
2. Verify phone number format: Must be +[country code][number]
3. Enable "OTP Verification" notification template
4. Check application logs for errors

### Issue: Users Stuck in Verification Loop

**Possible Causes:**
1. Database not updated correctly
2. Hooks not enabled
3. Session issues

**Solutions:**
1. Verify database columns exist: `whatsapp_verified`, `signup_type`
2. Ensure `enable_hooks = TRUE` in `app/config/config.php`
3. Clear browser cache and cookies
4. Check application error logs

### Issue: Manual Users Being Asked to Verify

**Possible Causes:**
1. Migration didn't run correctly
2. `signup_type` not set for existing users

**Solutions:**
1. Run this SQL to fix existing manual users:
```sql
UPDATE general_users 
SET signup_type = 'manual', whatsapp_verified = 1 
WHERE google_id IS NULL OR google_id = '';
```

### Issue: Google Users Not Being Asked to Verify

**Possible Causes:**
1. `whatsapp_verified` already set to 1
2. Hook not working

**Solutions:**
1. Check user's record in database:
```sql
SELECT id, email, signup_type, whatsapp_verified 
FROM general_users 
WHERE email = 'user@example.com';
```
2. Verify hooks are enabled
3. Check hook file exists: `app/hooks/Whatsapp_verification_check.php`

### Issue: "No Phone Number" Error When Resending

**Possible Causes:**
1. Phone number not saved in database
2. User changed number but didn't resend

**Solutions:**
1. Have user click "Change" to enter number again
2. Clear OTP data:
```sql
UPDATE general_users 
SET whatsapp_otp = NULL, whatsapp_otp_expires_at = NULL 
WHERE id = [user_id];
```

## Step 6: Production Deployment

### Before Deploying
- [ ] Test all flows in staging environment
- [ ] Backup database
- [ ] Ensure WhatsApp API has sufficient capacity
- [ ] Prepare rollback plan

### During Deployment
1. Put site in maintenance mode (optional)
2. Run database migration
3. Deploy code changes
4. Verify hooks are enabled
5. Test one Google login flow
6. Take site out of maintenance mode

### After Deployment
- [ ] Monitor application logs for errors
- [ ] Check WhatsApp API usage/costs
- [ ] Monitor user feedback
- [ ] Track successful verifications vs failures

## Optional: Customize OTP Settings

To change OTP behavior, edit `app/libraries/Whatsapp_otp.php`:

```php
protected $otp_length = 6;              // Change OTP digit count
protected $otp_expiry_minutes = 10;     // Change expiry time
protected $max_attempts = 5;            // Change attempt limit
protected $resend_cooldown_seconds = 60; // Change cooldown period
```

## Optional: Customize UI

The verification page can be customized by editing:
- `app/modules/whatsapp_verify/views/verify.php`

You can change:
- Colors and styling
- Text content
- Layout
- Country list

## Support

For issues or questions:
1. Check `WHATSAPP_VERIFICATION_README.md` for detailed documentation
2. Review application logs in `app/logs/`
3. Check WhatsApp API logs
4. Contact support with error details

## Rollback Instructions

If you need to rollback this feature:

1. **Disable the hook** in `app/config/hooks.php`:
```php
// Comment out or remove:
// $hook['post_controller_constructor'][] = array(...);
```

2. **Or disable hooks entirely** in `app/config/config.php`:
```php
$config['enable_hooks'] = FALSE;
```

3. **(Optional) Remove database columns** - Only if you're sure:
```sql
ALTER TABLE general_users 
DROP COLUMN whatsapp_verified,
DROP COLUMN whatsapp_otp,
DROP COLUMN whatsapp_otp_expires_at,
DROP COLUMN whatsapp_otp_attempts,
DROP COLUMN signup_type;
```

**Note:** Removing columns will lose verification status data. Keep the columns if you might re-enable the feature.

## Next Steps

After successful setup:
1. ✅ Monitor first few Google sign-ups
2. ✅ Collect user feedback
3. ✅ Adjust OTP settings if needed
4. ✅ Consider adding admin dashboard for unverified users
5. ✅ Plan for bulk verification campaigns if needed

---

Last Updated: 2025-12-29
Version: 1.0
