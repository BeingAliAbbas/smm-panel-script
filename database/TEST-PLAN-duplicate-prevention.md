# Test Plan: Duplicate Email Prevention

## Overview
This document outlines the test scenarios to verify that duplicate email sending is properly prevented.

## Prerequisites
1. Database migration (`fix-duplicate-emails.sql`) has been applied
2. Email marketing module is configured with at least one SMTP config
3. At least one email template exists
4. Test users exist in the database

## Test Scenarios

### Scenario 1: Import Users - Prevent Duplicates
**Objective**: Verify that importing users multiple times doesn't create duplicates

**Steps**:
1. Create a new campaign
2. Import users from database (e.g., 10 users)
3. Note the total recipients count
4. Import users from database again
5. Verify total recipients count remains the same
6. Check database: `SELECT email, COUNT(*) as count FROM email_recipients WHERE campaign_id = [CAMPAIGN_ID] GROUP BY email HAVING count > 1;`

**Expected Result**: 
- Second import should report 0 imported
- No duplicates in the database
- Total recipients count unchanged

### Scenario 2: Import CSV - Prevent Duplicates
**Objective**: Verify that importing the same CSV file multiple times doesn't create duplicates

**Test Data** (save as `test-recipients.csv`):
```csv
email,name
test1@example.com,Test User 1
test2@example.com,Test User 2
test3@example.com,Test User 3
```

**Steps**:
1. Create a new campaign
2. Import the CSV file
3. Note the total recipients count (should be 3)
4. Import the same CSV file again
5. Verify total recipients count remains 3
6. Import a CSV with some duplicate and some new emails
7. Verify only new emails are added

**Expected Result**:
- First import: 3 recipients
- Second import: 0 recipients (all duplicates)
- Third import: Only new emails added

### Scenario 3: Mixed Import Sources
**Objective**: Verify that the same email from different sources doesn't create duplicates

**Steps**:
1. Create a new campaign
2. Import users from database
3. Create a CSV with some emails that match imported users
4. Import the CSV
5. Verify no duplicates exist

**Expected Result**:
- CSV import should skip emails already imported from users
- Total count should be unique emails only

### Scenario 4: Database Constraint Test
**Objective**: Verify database-level protection works

**Steps**:
1. Create a campaign with ID X
2. Try to manually insert a duplicate email via SQL:
```sql
INSERT INTO email_recipients (ids, campaign_id, email, name, status, tracking_token, created_at, updated_at)
VALUES (MD5(RAND()), X, 'test@example.com', 'Test', 'pending', MD5(RAND()), NOW(), NOW());

-- Try again with same email
INSERT INTO email_recipients (ids, campaign_id, email, name, status, tracking_token, created_at, updated_at)
VALUES (MD5(RAND()), X, 'test@example.com', 'Test 2', 'pending', MD5(RAND()), NOW(), NOW());
```

**Expected Result**:
- Second insert should fail with duplicate key error
- Error message should reference `unique_campaign_email` constraint

### Scenario 5: Email Sending - Prevent Double Send
**Objective**: Verify cron doesn't send duplicate emails

**Setup**:
Manually create duplicate entries (before applying migration):
```sql
-- Disable constraint temporarily for testing
ALTER TABLE email_recipients DROP INDEX unique_campaign_email;

-- Insert duplicates
INSERT INTO email_recipients (ids, campaign_id, email, name, status, tracking_token, created_at, updated_at)
VALUES 
(MD5('dup1'), 1, 'duplicate@test.com', 'User 1', 'pending', MD5('token1'), NOW(), NOW()),
(MD5('dup2'), 1, 'duplicate@test.com', 'User 2', 'pending', MD5('token2'), NOW(), NOW());
```

**Steps**:
1. Start the campaign
2. Run the cron job
3. Check email_logs table for sends to `duplicate@test.com`
4. Check email_recipients table statuses

**Expected Result**:
- Only one email sent to `duplicate@test.com`
- First recipient marked as 'sent'
- Second recipient marked as 'failed' with error message "Duplicate recipient - email already sent to this address in this campaign"

**Cleanup**:
```sql
-- Re-add constraint
ALTER TABLE email_recipients ADD UNIQUE KEY unique_campaign_email (campaign_id, email);
```

### Scenario 6: Race Condition Test
**Objective**: Verify concurrent cron executions don't cause double sends

**Steps**:
1. Create campaign with many recipients (100+)
2. Start the campaign
3. Run multiple cron jobs simultaneously (simulate with cURL or multiple terminals):
```bash
# Terminal 1
curl "http://yoursite.com/email_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"

# Terminal 2 (immediately)
curl "http://yoursite.com/email_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"

# Terminal 3 (immediately)
curl "http://yoursite.com/email_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID"
```

**Expected Result**:
- Rate limiting should prevent most concurrent executions
- Even if some get through, status checks prevent duplicate sends
- No email should be sent twice to the same address

### Scenario 7: Status Verification
**Objective**: Verify status is checked before sending

**Steps**:
1. Create campaign with recipient in 'pending' status
2. Start campaign
3. While cron is processing, manually update recipient status to 'sent'
4. Verify cron doesn't send to that recipient

**Expected Result**:
- Cron skips recipients whose status changed from 'pending'
- No error logged (just skipped)

## Verification Queries

### Check for duplicates in any campaign:
```sql
SELECT campaign_id, email, COUNT(*) as count 
FROM email_recipients 
GROUP BY campaign_id, email 
HAVING count > 1;
```

### Check sending logs for duplicates:
```sql
SELECT l.campaign_id, l.email, COUNT(*) as send_count
FROM email_logs l
WHERE l.status IN ('sent', 'opened')
GROUP BY l.campaign_id, l.email
HAVING send_count > 1;
```

### Verify unique constraint exists:
```sql
SHOW INDEX FROM email_recipients WHERE Key_name = 'unique_campaign_email';
```

## Performance Testing

### Large Import Test
**Objective**: Verify performance with large datasets

**Steps**:
1. Create CSV with 10,000 unique emails
2. Import to campaign
3. Try to import the same CSV again
4. Measure time for both imports

**Expected Result**:
- First import completes successfully
- Second import completes quickly (duplicate check is efficient)
- No performance degradation

## Rollback Testing

**Objective**: Verify rollback procedure works if needed

**Steps**:
1. Export current database state
2. Apply migration
3. Test functionality
4. Rollback using documented procedure
5. Verify system works as before

**Expected Result**:
- Rollback completes without errors
- System returns to previous state

## Sign-off Checklist

- [ ] All test scenarios pass
- [ ] No duplicate emails found in database
- [ ] No duplicate sends in logs
- [ ] Performance is acceptable
- [ ] Documentation is clear and accurate
- [ ] Migration script works correctly
- [ ] Rollback procedure verified
- [ ] Error handling works as expected

## Notes
- Test on a staging/development environment first
- Monitor error logs during testing
- Keep database backups before applying migration
- Test with real SMTP server if possible (use a test email domain)
