# WhatsApp Verification Enhancement

## Overview
This enhancement adds mandatory WhatsApp number verification for users who sign in or sign up using Google OAuth. Manual registration users continue to work as before without requiring additional WhatsApp verification.

## Features Implemented

### 1. Database Changes
- Added `google_id` column to `general_users` table for storing Google OAuth ID
- Added `signup_type` column (enum: 'manual', 'google') to identify user signup method
- Added `whatsapp_verified` column to track WhatsApp verification status
- Added `whatsapp_verified_at` column to store verification timestamp
- Created `whatsapp_otp_verifications` table for OTP management
- Created `whatsapp_otp_rate_limit` table for rate limiting OTP requests

### 2. Authentication Flow Changes
- **Google Sign-In/Sign-Up**: After successful Google authentication, users are redirected to WhatsApp verification instead of directly to the dashboard
- **Manual Sign-Up**: Continues to work as before - no additional WhatsApp verification required
- **Existing Users**: Existing Google users without WhatsApp verification will be prompted to verify on next login

### 3. WhatsApp Verification Process
1. **Phone Number Input**: User selects country code and enters WhatsApp number
2. **OTP Generation**: 6-digit OTP is generated and sent to the user's WhatsApp
3. **OTP Verification**: User enters the OTP to verify their number
4. **Session Completion**: After successful verification, user gains full access to the dashboard

### 4. Security Features
- **Rate Limiting**: Maximum 3 OTP requests per 15 minutes per user
- **OTP Expiry**: OTPs expire after 5 minutes
- **Attempt Limiting**: Maximum 5 verification attempts per OTP
- **Server-Side Validation**: All verification steps validated on the server
- **Session Protection**: Users cannot access protected pages without completing verification

### 5. User Experience
- **International Support**: Country code selector with 200+ countries
- **Resend OTP**: Users can request a new OTP if not received
- **Clear Error Messages**: Helpful feedback for validation errors
- **Responsive Design**: Works on all device sizes

## Files Added/Modified

### Added Files
- `database/whatsapp_verification_migration.sql` - Database migration script
- `app/modules/auth/controllers/whatsapp_verify.php` - WhatsApp verification controller
- `app/modules/auth/models/whatsapp_verify_model.php` - Model for verification
- `app/hooks/Whatsapp_verification_guard.php` - Security guard to enforce verification
- `themes/pergo/views/whatsapp_setup.php` - WhatsApp number input page
- `themes/pergo/views/whatsapp_verify_otp.php` - OTP verification page
- `themes/regular/views/whatsapp_setup.php` - (same for regular theme)
- `themes/regular/views/whatsapp_verify_otp.php` - (same for regular theme)
- `themes/monoka/views/whatsapp_setup.php` - (same for monoka theme)
- `themes/monoka/views/whatsapp_verify_otp.php` - (same for monoka theme)

### Modified Files
- `app/modules/auth/controllers/auth.php` - Updated Google callback to redirect to WhatsApp verification
- `app/config/hooks.php` - Registered WhatsApp verification guard
- `app/config/config.php` - Enabled hooks

## Installation Instructions

### 1. Run Database Migration
Execute the SQL migration script:
```bash
mysql -u your_user -p your_database < database/whatsapp_verification_migration.sql
```

Or manually run the SQL commands from `database/whatsapp_verification_migration.sql` in your database.

### 2. Configure WhatsApp API
Ensure that your WhatsApp notification library is properly configured with:
- WhatsApp API URL
- API Key
- Admin phone number (optional)

The system uses the existing `Whatsapp_notification` library for sending OTPs.

### 3. Add OTP Notification Template
If using a custom WhatsApp notification system, add the following template with event_type `otp_verification`:
```
Your verification code is: *{otp_code}*

This code will expire in {expiry_minutes} minutes.

Do not share this code with anyone.

- {website_name}
```

### 4. Clear Cache
Clear any application cache if applicable.

## Testing Checklist

- [ ] Test Google sign-in flow for new users
- [ ] Test Google sign-in flow for existing users
- [ ] Test manual sign-up (should not require WhatsApp verification)
- [ ] Test manual login (should not require WhatsApp verification)
- [ ] Test OTP sending to various country codes
- [ ] Test OTP verification with correct code
- [ ] Test OTP verification with incorrect code
- [ ] Test OTP expiry (after 5 minutes)
- [ ] Test rate limiting (max 3 requests per 15 minutes)
- [ ] Test resend OTP functionality
- [ ] Test direct dashboard access without verification (should be blocked)
- [ ] Test session persistence after verification
- [ ] Verify database correctly stores signup_type
- [ ] Verify users module shows signup method

## API Endpoints

### POST /whatsapp_verify/ajax_send_otp
Sends OTP to WhatsApp number
- **Parameters**: `whatsapp_number`, `country_code`
- **Response**: Success/error message

### POST /whatsapp_verify/ajax_verify_otp
Verifies the OTP code
- **Parameters**: `otp_code`, `whatsapp_number`
- **Response**: Success/error message, redirects to dashboard on success

### POST /whatsapp_verify/ajax_resend_otp
Resends OTP to the same WhatsApp number
- **Parameters**: `whatsapp_number`
- **Response**: Success/error message

## Security Considerations

1. **OTP Storage**: OTPs are stored in the database temporarily and deleted after verification
2. **Rate Limiting**: Prevents brute force attacks on OTP verification
3. **Session Validation**: Verification guard runs on every request to protected routes
4. **SQL Injection Protection**: All database queries use parameterized queries
5. **XSS Protection**: All user inputs are properly sanitized

## Backward Compatibility

- Existing manual signup users continue to work without any changes
- Existing Google users will be prompted to verify WhatsApp on next login
- Manual signup users are automatically marked as WhatsApp verified
- No breaking changes to existing authentication flow

## Support

For issues or questions, please refer to the main project documentation or create an issue in the repository.
