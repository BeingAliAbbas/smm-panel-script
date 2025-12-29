# Pre-Deployment Checklist

Use this checklist before deploying the WhatsApp verification feature to production.

## Prerequisites ✅

- [ ] Database backup completed
- [ ] Staging environment available for testing
- [ ] WhatsApp API credentials ready
- [ ] Admin panel access confirmed
- [ ] Rollback plan prepared

## Code Review ✅

- [x] All code changes reviewed
- [x] Security vulnerabilities checked
- [x] XSS protection verified
- [x] SQL injection protection verified
- [x] Table name consistency verified
- [x] Helper functions loaded properly

## Database Preparation ✅

- [ ] Backup current database
  ```bash
  mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
  ```

- [ ] Review migration script
  - [ ] Check syntax compatibility with MySQL version
  - [ ] Verify column types and defaults
  - [ ] Review index creation

- [ ] Test migration in staging
  ```bash
  mysql -u username -p database_name < database/whatsapp-verification-enhancement.sql
  ```

- [ ] Verify migration results
  ```sql
  -- Check new columns
  DESCRIBE general_users;
  
  -- Check notification template
  SELECT * FROM whatsapp_notifications WHERE event_type = 'otp_verification';
  
  -- Check existing users categorization
  SELECT signup_type, COUNT(*) FROM general_users GROUP BY signup_type;
  ```

## Configuration ✅

- [ ] Hooks enabled in `app/config/config.php`
  ```php
  $config['enable_hooks'] = TRUE;
  ```

- [ ] Hook registered in `app/config/hooks.php`
  - [ ] Whatsapp_verification_check hook configured
  - [ ] Correct file path specified

- [ ] WhatsApp API configured in admin panel
  - [ ] API URL set
  - [ ] API Key set
  - [ ] Admin phone number set

- [ ] OTP notification template enabled
  - [ ] Template exists in database
  - [ ] Status is Active/Enabled
  - [ ] Message template reviewed

## Staging Tests ✅

### Test 1: New Google User Sign-Up
- [ ] Clear browser cache/cookies
- [ ] Open site in incognito mode
- [ ] Click "Sign in with Google"
- [ ] Complete Google authentication
- [ ] **Expected**: Redirect to WhatsApp verification page
- [ ] Enter country code and phone number
- [ ] Click "Send OTP"
- [ ] **Expected**: OTP sent to WhatsApp
- [ ] Check WhatsApp for OTP
- [ ] Enter OTP in verification form
- [ ] **Expected**: Verified and redirected to dashboard
- [ ] Logout and login again
- [ ] **Expected**: Direct access to dashboard (no verification)

### Test 2: Existing Google User (Not Verified)
- [ ] Use existing Google account (no WhatsApp verified)
- [ ] Sign in with Google
- [ ] **Expected**: Redirect to WhatsApp verification page
- [ ] Complete verification flow
- [ ] **Expected**: Successful verification and dashboard access

### Test 3: Existing Google User (Already Verified)
- [ ] Use Google account with verified WhatsApp
- [ ] Sign in with Google
- [ ] **Expected**: Direct redirect to dashboard
- [ ] No verification page shown

### Test 4: Manual Sign-Up
- [ ] Go to manual registration page
- [ ] Fill all fields including WhatsApp number
- [ ] Submit registration
- [ ] **Expected**: Account created successfully
- [ ] **Expected**: Immediate dashboard access
- [ ] No additional verification required

### Test 5: Dashboard Access Protection
- [ ] Login via Google without completing verification
- [ ] Try to access `/statistics` directly
- [ ] **Expected**: Auto-redirect to `/whatsapp_verify`
- [ ] Try to access other dashboard pages
- [ ] **Expected**: Auto-redirect to `/whatsapp_verify`

### Test 6: OTP Validation
- [ ] Request OTP
- [ ] Enter wrong OTP
- [ ] **Expected**: Error message with remaining attempts
- [ ] Try 5 wrong attempts
- [ ] **Expected**: "Request new OTP" message
- [ ] Request new OTP
- [ ] Let OTP expire (wait 10+ minutes)
- [ ] Try to verify expired OTP
- [ ] **Expected**: "OTP expired" error

### Test 7: Resend OTP
- [ ] Request OTP
- [ ] Immediately click "Resend OTP"
- [ ] **Expected**: Cooldown message (wait 60 seconds)
- [ ] Wait 60 seconds
- [ ] Click "Resend OTP"
- [ ] **Expected**: New OTP sent successfully

### Test 8: Change Number
- [ ] Request OTP for a number
- [ ] Click "Change Number"
- [ ] **Expected**: Return to phone input form
- [ ] Enter different number
- [ ] **Expected**: Can send OTP to new number

## Security Verification ✅

- [ ] Cannot access dashboard without verification
  - Test direct URL access: `/statistics`, `/order/add`, etc.
  - All should redirect to `/whatsapp_verify`

- [ ] Cannot bypass verification
  - Test session manipulation
  - Test direct controller access
  - Hook should block all attempts

- [ ] OTP security working
  - [ ] Expires after 10 minutes
  - [ ] Blocked after 5 wrong attempts
  - [ ] 60-second cooldown enforced
  - [ ] Cleared after verification

- [ ] XSS protection verified
  - [ ] User inputs properly escaped
  - [ ] No script injection possible

- [ ] SQL injection protection
  - [ ] Parameterized queries used
  - [ ] No raw SQL with user input

## Performance Testing ✅

- [ ] Page load times acceptable
  - [ ] Verification page: < 2 seconds
  - [ ] Dashboard after verification: < 3 seconds

- [ ] OTP delivery time
  - [ ] WhatsApp receives OTP: < 10 seconds
  - [ ] No timeout issues

- [ ] Database queries optimized
  - [ ] Indexes working
  - [ ] No slow queries in logs

- [ ] Hook overhead minimal
  - [ ] No noticeable delay on page loads
  - [ ] Excluded controllers work properly

## Error Handling ✅

- [ ] WhatsApp API failure handled gracefully
  - [ ] User sees friendly error message
  - [ ] Can retry sending OTP
  - [ ] Logged for admin review

- [ ] Invalid phone number handled
  - [ ] Clear validation error shown
  - [ ] User can correct and retry

- [ ] Network issues handled
  - [ ] Timeout doesn't break flow
  - [ ] User can retry operation

- [ ] Database errors handled
  - [ ] Proper error messages
  - [ ] No sensitive data exposed

## Documentation Review ✅

- [x] WHATSAPP_VERIFICATION_README.md - Complete
- [x] SETUP_INSTRUCTIONS.md - Complete
- [x] WHATSAPP_FLOW_DIAGRAM.md - Complete
- [x] IMPLEMENTATION_SUMMARY.md - Complete
- [ ] Admin documentation updated (if applicable)
- [ ] User guide updated (if applicable)

## Monitoring Setup ✅

- [ ] Application logging enabled
  - [ ] OTP requests logged
  - [ ] Verification attempts logged
  - [ ] Failures logged

- [ ] WhatsApp API monitoring
  - [ ] Track delivery success rate
  - [ ] Monitor API costs
  - [ ] Alert on high failure rate

- [ ] User metrics tracking
  - [ ] Google sign-ups count
  - [ ] Verification success rate
  - [ ] Average verification time

## Rollback Plan ✅

**If issues occur, rollback procedure:**

1. **Disable Hook (Immediate)**
   ```php
   // In app/config/config.php
   $config['enable_hooks'] = FALSE;
   ```

2. **Disable Verification (Alternative)**
   ```php
   // Comment out hook in app/config/hooks.php
   // $hook['post_controller_constructor'][] = array(...);
   ```

3. **Database Rollback (If Needed)**
   ```sql
   -- Restore from backup
   mysql -u username -p database_name < backup_YYYYMMDD.sql
   ```

4. **Code Rollback (Last Resort)**
   ```bash
   git revert <commit-hash>
   git push origin main
   ```

## Post-Deployment Monitoring ✅

**First Hour:**
- [ ] Monitor application logs every 10 minutes
- [ ] Check WhatsApp API success rate
- [ ] Watch for error spikes
- [ ] Verify first few Google sign-ups

**First Day:**
- [ ] Review all OTP requests
- [ ] Check verification success rate
- [ ] Monitor user feedback
- [ ] Track any support tickets

**First Week:**
- [ ] Analyze metrics
- [ ] Review costs (WhatsApp API)
- [ ] Gather user feedback
- [ ] Adjust settings if needed

## Communication Plan ✅

- [ ] Notify team of deployment time
- [ ] Inform support team of new feature
- [ ] Prepare user announcement (if needed)
- [ ] Document any known issues
- [ ] Establish escalation path

## Final Checks ✅

- [ ] All code committed and pushed
- [ ] PR approved and ready to merge
- [ ] Deployment time scheduled
- [ ] Team members available
- [ ] Backup verified and accessible
- [ ] Rollback plan documented and ready

---

## Deployment Day Checklist

### Pre-Deployment (15 minutes before)
- [ ] Final backup completed
- [ ] Team notified and ready
- [ ] Support team briefed
- [ ] Monitoring tools ready

### During Deployment (30 minutes)
- [ ] Site in maintenance mode (optional)
- [ ] Pull latest code
- [ ] Run database migration
- [ ] Verify hooks enabled
- [ ] Clear cache/sessions
- [ ] Test one complete flow
- [ ] Site out of maintenance mode

### Post-Deployment (1 hour)
- [ ] Monitor logs continuously
- [ ] Test multiple user flows
- [ ] Check WhatsApp deliveries
- [ ] Verify no errors in logs
- [ ] Confirm metrics tracking

---

**Sign-off:**
- [ ] Tech Lead approval: ________________
- [ ] QA approval: ________________
- [ ] Product Owner approval: ________________
- [ ] Deployment Date: ________________
- [ ] Deployment Time: ________________

---

**Status:** Ready for Production ✅
**Last Updated:** 2025-12-29
**Version:** 1.0.0
