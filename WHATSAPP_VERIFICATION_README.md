# WhatsApp Verification Enhancement

This enhancement adds mandatory WhatsApp verification for Google Sign-In users.

## Features

### 1. Post-Google Login WhatsApp Verification
- After successful Google authentication, users are redirected to WhatsApp verification page
- Login session is not completed until WhatsApp number is verified
- Users cannot access dashboard or protected pages without verification

### 2. International Phone Number Support
- Support for WhatsApp numbers from 80+ countries
- Country selector with country codes and flags
- Proper validation for international phone format (+[country code][number])
- Standardized storage in international format

### 3. Secure OTP Verification
- 6-digit OTP sent via WhatsApp
- 10-minute expiry time for OTP
- Maximum 5 verification attempts per OTP
- 60-second cooldown between OTP requests
- Server-side validation (no client-side trust)

### 4. User Type Identification
- `signup_type` field distinguishes manual vs Google users
- Manual signup users: `signup_type = 'manual'`
- Google signup users: `signup_type = 'google'`
- Existing users automatically categorized

### 5. Security & Data Integrity
- Hook-based protection prevents bypassing verification
- WhatsApp verification required only for Google users
- Manual signup users remain unaffected
- Database indexes for performance
- Proper session management

## Database Changes

The migration file `database/whatsapp-verification-enhancement.sql` adds:

```sql
-- New columns in general_users table
- whatsapp_verified: TINYINT(1) - Verification status
- whatsapp_otp: VARCHAR(10) - Temporary OTP storage
- whatsapp_otp_expires_at: DATETIME - OTP expiration
- whatsapp_otp_attempts: INT(11) - Attempt counter
- signup_type: ENUM('manual', 'google') - User signup method

-- New notification template
- otp_verification: WhatsApp OTP message template
```

## Installation

### 1. Run Database Migration

Execute the SQL migration:

```bash
mysql -u your_user -p your_database < database/whatsapp-verification-enhancement.sql
```

Or import via phpMyAdmin/database management tool.

### 2. Verify Hooks are Enabled

The system automatically enables hooks in `app/config/config.php`:
```php
$config['enable_hooks'] = TRUE;
```

### 3. Configure WhatsApp API

Ensure WhatsApp notification system is configured in your admin panel:
- Settings > WhatsApp Configuration
- Add API URL and API Key
- Enable "OTP Verification" notification template

## Files Added/Modified

### New Files
```
app/libraries/Whatsapp_otp.php              - OTP generation and validation
app/hooks/Whatsapp_verification_check.php   - Security hook for access control
app/modules/whatsapp_verify/controllers/    - Verification controller
app/modules/whatsapp_verify/views/          - Verification UI
database/whatsapp-verification-enhancement.sql - Database migration
```

### Modified Files
```
app/config/config.php                       - Enable hooks
app/config/hooks.php                        - Register verification hook
app/modules/auth/controllers/auth.php      - Updated Google callback & signup
```

## User Flow

### Google Sign-In (New User)
1. User clicks "Sign in with Google"
2. Google authentication succeeds
3. User account created with `signup_type = 'google'` and `whatsapp_verified = 0`
4. User redirected to `/whatsapp_verify`
5. User enters country code and phone number
6. OTP sent to WhatsApp
7. User enters OTP for verification
8. On success: `whatsapp_verified = 1`, redirect to dashboard
9. On failure: Error message with remaining attempts

### Google Sign-In (Existing User)
1. User clicks "Sign in with Google"
2. Google authentication succeeds
3. System checks `whatsapp_verified` status
4. If not verified: Redirect to `/whatsapp_verify`
5. If verified: Redirect to dashboard

### Manual Sign-Up
1. User completes manual registration form (with WhatsApp field)
2. Account created with:
   - `signup_type = 'manual'`
   - `whatsapp_verified = 1` (pre-verified)
3. User can immediately access dashboard
4. No additional WhatsApp verification required

## API Endpoints

### WhatsApp Verification Module

```
GET  /whatsapp_verify              - Show verification page
POST /whatsapp_verify/ajax_send_otp      - Send OTP to phone
POST /whatsapp_verify/ajax_verify_otp    - Verify OTP code
POST /whatsapp_verify/ajax_resend_otp    - Resend OTP
POST /whatsapp_verify/ajax_change_number - Change phone number
```

## Security Features

### 1. Hook-Based Protection
```php
// Automatically redirects unverified Google users
post_controller_constructor -> Whatsapp_verification_check->check()
```

### 2. OTP Security
- Temporary storage (cleared after verification/expiry)
- Attempt limits (max 5 attempts)
- Time limits (10 minutes expiry)
- Cooldown periods (60 seconds between requests)

### 3. Excluded Controllers
The following controllers bypass verification check:
- `auth` - Authentication flows
- `whatsapp_verify` - Verification itself
- `api_access` - API endpoints
- `cron` - Background tasks

## Configuration

### OTP Settings (in Whatsapp_otp library)
```php
$otp_length = 6;                      // OTP digit length
$otp_expiry_minutes = 10;            // OTP validity period
$max_attempts = 5;                   // Max verification attempts
$resend_cooldown_seconds = 60;       // Cooldown between requests
```

### WhatsApp Message Template
Template variables for `otp_verification`:
- `{otp}` - The verification code
- `{expiry_minutes}` - Expiration time
- `{website_name}` - Site name (auto-added)
- `{currency_symbol}` - Currency (auto-added)

## Testing Checklist

- [ ] New Google user sign-up redirects to WhatsApp verification
- [ ] Existing Google user without verification redirects to verification
- [ ] Existing Google user with verification goes to dashboard
- [ ] Manual signup users access dashboard immediately
- [ ] OTP is sent successfully to WhatsApp
- [ ] OTP verification works correctly
- [ ] Invalid OTP shows error with remaining attempts
- [ ] Expired OTP requires new OTP
- [ ] Max attempts reached requires new OTP
- [ ] Resend OTP respects cooldown period
- [ ] Change number clears current OTP
- [ ] Cannot access dashboard without verification
- [ ] Verification hook blocks unauthorized access
- [ ] Logout works correctly

## Troubleshooting

### OTP Not Received
1. Check WhatsApp API configuration
2. Verify phone number format (+[country][number])
3. Check WhatsApp notification logs
4. Verify notification template is enabled

### Users Stuck in Verification Loop
1. Check database: user's `whatsapp_verified` field
2. Verify hook is working: check `enable_hooks` in config
3. Check for errors in application logs

### Manual Users Being Asked to Verify
1. Check user's `signup_type` field (should be 'manual')
2. Check `whatsapp_verified` field (should be 1)
3. Run migration to update existing users

## Support for Existing Users

The migration automatically handles existing users:
```sql
-- Existing users without google_id marked as manual
UPDATE general_users SET signup_type = 'manual' 
WHERE signup_type IS NULL AND google_id IS NULL;

-- Existing users with google_id marked as google
UPDATE general_users SET signup_type = 'google' 
WHERE google_id IS NOT NULL AND google_id != '';

-- Users with WhatsApp number marked as verified
UPDATE general_users SET whatsapp_verified = 1 
WHERE whatsapp_number IS NOT NULL AND whatsapp_number != '';
```

## Performance

- Database indexes added for `whatsapp_verified` and `signup_type`
- Hook runs on every controller load (minimal overhead)
- OTP cleanup happens automatically on verification/expiry
- No additional queries for manual users

## Future Enhancements

Potential improvements:
- SMS fallback if WhatsApp fails
- Configurable OTP length and expiry
- Admin panel to manage unverified users
- Bulk WhatsApp verification for existing users
- WhatsApp verification reminder emails
- Multiple phone number support per user

## License

Part of SmartPanel SMM Reseller Tool
