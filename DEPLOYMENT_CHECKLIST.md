# WhatsApp Verification Deployment Checklist

Use this checklist to ensure proper deployment of the WhatsApp verification feature.

## Pre-Deployment

### Backup & Preparation
- [ ] **CRITICAL**: Backup your database
- [ ] **CRITICAL**: Backup your codebase
- [ ] Review all changes in this PR
- [ ] Read IMPLEMENTATION_SUMMARY.md
- [ ] Read WHATSAPP_VERIFICATION_GUIDE.md
- [ ] Understand the authentication flows (AUTHENTICATION_FLOWS.md)

### Environment Check
- [ ] Verify you have database access
- [ ] Verify you have MySQL credentials
- [ ] Confirm WhatsApp API is configured and working
- [ ] Check WhatsApp API has sufficient credits/quota
- [ ] Verify PHP version compatibility (PHP 7.0+)
- [ ] Check CodeIgniter version compatibility

## Deployment Steps

### 1. Code Deployment
- [ ] Pull/merge the PR code to your environment
- [ ] Verify all 14 new files are present:
  - [ ] database/whatsapp_verification_migration.sql
  - [ ] app/modules/auth/controllers/whatsapp_verify.php
  - [ ] app/modules/auth/models/whatsapp_verify_model.php
  - [ ] app/hooks/Whatsapp_verification_guard.php
  - [ ] themes/pergo/views/whatsapp_setup.php
  - [ ] themes/pergo/views/whatsapp_verify_otp.php
  - [ ] themes/regular/views/whatsapp_setup.php
  - [ ] themes/regular/views/whatsapp_verify_otp.php
  - [ ] themes/monoka/views/whatsapp_setup.php
  - [ ] themes/monoka/views/whatsapp_verify_otp.php
  - [ ] Documentation files (3 files)

### 2. Database Migration
- [ ] Run: `bash install_whatsapp_verification.sh` OR
- [ ] Manually run: `mysql -u user -p database < database/whatsapp_verification_migration.sql`
- [ ] Verify tables created:
  - [ ] whatsapp_otp_verifications
  - [ ] whatsapp_otp_rate_limit
- [ ] Verify columns added to general_users:
  - [ ] google_id
  - [ ] signup_type
  - [ ] whatsapp_verified
  - [ ] whatsapp_verified_at

### 3. Configuration
- [ ] Verify `app/config/config.php` has hooks enabled: `$config['enable_hooks'] = TRUE;`
- [ ] Verify `app/config/hooks.php` has the WhatsApp verification guard registered
- [ ] Check WhatsApp API configuration in database (whatsapp_config table)
- [ ] Add OTP notification template (if not auto-added by migration)
- [ ] Clear any application cache

### 4. File Permissions
- [ ] Ensure proper file permissions on new files
- [ ] Check write permissions on app/logs/ directory
- [ ] Verify web server can read all new files

## Testing

### Functional Testing
- [ ] Test Google sign-in with NEW user
  - [ ] Verify redirects to WhatsApp setup page
  - [ ] Enter phone number and request OTP
  - [ ] Verify OTP is received on WhatsApp
  - [ ] Enter correct OTP and verify success
  - [ ] Confirm redirect to dashboard
  - [ ] Check user is marked as verified in database

- [ ] Test Google sign-in with EXISTING unverified user
  - [ ] Verify redirects to WhatsApp setup page
  - [ ] Complete verification process

- [ ] Test Google sign-in with EXISTING verified user
  - [ ] Verify direct access to dashboard (no verification needed)

- [ ] Test Manual signup
  - [ ] Fill signup form including WhatsApp number
  - [ ] Verify account created successfully
  - [ ] Confirm NO additional WhatsApp verification required
  - [ ] Check direct access to dashboard

- [ ] Test Manual login
  - [ ] Login with existing manual account
  - [ ] Verify NO WhatsApp verification required
  - [ ] Confirm direct access to dashboard

### OTP Testing
- [ ] Test OTP with correct code → Success
- [ ] Test OTP with incorrect code → Error message
- [ ] Test OTP after 5 minutes → Expired error
- [ ] Test OTP after 5 failed attempts → Blocked
- [ ] Test Resend OTP functionality
- [ ] Test rate limiting (3 requests per 15 min)

### Security Testing
- [ ] Try accessing dashboard without WhatsApp verification → Redirected
- [ ] Try manipulating session to bypass verification → Blocked
- [ ] Verify OTP codes are properly secured
- [ ] Check rate limiting works as expected
- [ ] Test CSRF protection on forms

### Edge Cases
- [ ] Test with invalid phone number format
- [ ] Test with various country codes
- [ ] Test with WhatsApp number that's already used
- [ ] Test resend OTP multiple times quickly
- [ ] Test changing phone number during verification
- [ ] Test browser back button behavior
- [ ] Test concurrent login attempts

### User Interface
- [ ] Test on desktop browsers (Chrome, Firefox, Safari)
- [ ] Test on mobile browsers
- [ ] Test on tablet
- [ ] Verify all themes work (pergo, regular, monoka)
- [ ] Check error messages are clear and helpful
- [ ] Verify UI is responsive

## Post-Deployment

### Monitoring
- [ ] Monitor application logs for errors
- [ ] Monitor web server error logs
- [ ] Check WhatsApp API success rate
- [ ] Monitor database for any issues
- [ ] Track user signup/login success rates

### User Communication
- [ ] Notify existing Google users about new requirement
- [ ] Update help documentation
- [ ] Prepare support team for potential questions
- [ ] Monitor support tickets for issues

### Database Verification
- [ ] Check signup_type is correctly set for all users
- [ ] Verify whatsapp_verified status is accurate
- [ ] Monitor OTP table for cleanup (old records)
- [ ] Check rate_limit table for any anomalies

### Performance
- [ ] Monitor page load times
- [ ] Check database query performance
- [ ] Verify OTP sending doesn't block page loads
- [ ] Monitor server resources

## Rollback Plan (If Needed)

If issues arise, follow these steps:

### Immediate Rollback
- [ ] Revert code changes to previous version
- [ ] Disable hooks: Set `$config['enable_hooks'] = FALSE;` in app/config/config.php
- [ ] Clear cache
- [ ] Test that normal login/signup works

### Database Rollback (If Required)
- [ ] Restore database from backup
- [ ] OR manually remove new tables:
  ```sql
  DROP TABLE IF EXISTS whatsapp_otp_verifications;
  DROP TABLE IF EXISTS whatsapp_otp_rate_limit;
  ```
- [ ] OR manually remove new columns from general_users:
  ```sql
  ALTER TABLE general_users 
  DROP COLUMN google_id,
  DROP COLUMN signup_type,
  DROP COLUMN whatsapp_verified,
  DROP COLUMN whatsapp_verified_at;
  ```

### Post-Rollback
- [ ] Verify all functionality works
- [ ] Document the issue
- [ ] Plan fixes for re-deployment

## Success Criteria

Mark deployment as successful when:
- [ ] All tests pass
- [ ] No errors in logs
- [ ] Users can successfully complete Google sign-in with WhatsApp verification
- [ ] Manual signup/login works unchanged
- [ ] WhatsApp OTPs are being sent successfully
- [ ] Rate limiting works as expected
- [ ] No user complaints or support tickets
- [ ] Database shows correct signup_type for all users
- [ ] Performance is acceptable

## Notes

- Expected deployment time: 30-60 minutes (including testing)
- Downtime required: None (feature can be deployed without downtime)
- User impact: Existing Google users will need to verify WhatsApp on next login
- Support impact: Prepare support team for questions about WhatsApp verification

## Sign-Off

Deployment completed by: ___________________
Date: ___________________
Time: ___________________

Verified by: ___________________
Date: ___________________

Issues encountered: ___________________

Rollback required: [ ] Yes  [ ] No

---

**IMPORTANT REMINDER**: Always backup your database before running migrations!
