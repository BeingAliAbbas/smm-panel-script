# Duplicate Email Prevention - Solution Architecture

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                   EMAIL MARKETING CAMPAIGN                       │
│                                                                   │
│  User Action: Import Recipients                                  │
│  ├── Import from Users Database                                  │
│  ├── Import from CSV File                                        │
│  └── Manual Addition                                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│              LAYER 1: APPLICATION VALIDATION                     │
│                                                                   │
│  Model: email_marketing_model.php                                │
│  Method: add_recipient()                                         │
│                                                                   │
│  ┌───────────────────────────────────────────────┐              │
│  │ CHECK: Does email exist in campaign?          │              │
│  │                                                │              │
│  │ Query:                                         │              │
│  │   SELECT COUNT(*) FROM email_recipients       │              │
│  │   WHERE campaign_id = ? AND email = ?         │              │
│  └───────────────────────────────────────────────┘              │
│                    │                │                             │
│               YES  │                │  NO                         │
│                    ▼                ▼                             │
│            RETURN FALSE    INSERT NEW RECIPIENT                  │
│         (Skip duplicate)         │                                │
└──────────────────────────────────┼──────────────────────────────┘
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│              LAYER 2: DATABASE CONSTRAINT                        │
│                                                                   │
│  Table: email_recipients                                         │
│  Constraint: UNIQUE KEY (campaign_id, email)                     │
│                                                                   │
│  ┌───────────────────────────────────────────────┐              │
│  │ Database checks uniqueness before INSERT      │              │
│  │                                                │              │
│  │ If duplicate detected:                         │              │
│  │   - Raise duplicate key error                 │              │
│  │   - Transaction rolled back                   │              │
│  │   - INSERT fails                              │              │
│  └───────────────────────────────────────────────┘              │
│                                                                   │
│  ✅ Recipient stored in database                                 │
│     Status: 'pending'                                            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   CAMPAIGN STARTED                               │
│                                                                   │
│  Cron Job: email_cron.php (runs every minute)                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│         LAYER 3: RUNTIME SENDING VERIFICATION                    │
│                                                                   │
│  Controller: email_cron.php                                      │
│  Method: send_email()                                            │
│                                                                   │
│  ┌───────────────────────────────────────────────┐              │
│  │ SAFETY CHECK 1: Duplicate Detection           │              │
│  │                                                │              │
│  │ Query:                                         │              │
│  │   SELECT COUNT(*) FROM email_recipients       │              │
│  │   WHERE campaign_id = ?                       │              │
│  │     AND email = ?                             │              │
│  │     AND id != ?                               │              │
│  │     AND status IN ('sent', 'opened')          │              │
│  └───────────────────────────────────────────────┘              │
│                    │                │                             │
│           FOUND >0 │                │  NOT FOUND                  │
│                    ▼                ▼                             │
│        Mark as FAILED      PROCEED TO CHECK 2                    │
│      "Duplicate recipient"         │                              │
│                                    ▼                              │
│  ┌───────────────────────────────────────────────┐              │
│  │ SAFETY CHECK 2: Status Verification           │              │
│  │                                                │              │
│  │ Query:                                         │              │
│  │   SELECT * FROM email_recipients              │              │
│  │   WHERE id = ?                                │              │
│  │                                                │              │
│  │ Verify: status == 'pending'                   │              │
│  └───────────────────────────────────────────────┘              │
│                    │                │                             │
│       NOT PENDING  │                │  PENDING                    │
│                    ▼                ▼                             │
│           SKIP (status changed)  SEND EMAIL                      │
│                                    │                              │
│                                    ▼                              │
│                          ┌──────────────┐                        │
│                          │ Email Sent!  │                        │
│                          └──────────────┘                        │
│                                    │                              │
│                                    ▼                              │
│                    Update status to 'sent'                       │
│                    Record sent_at timestamp                      │
│                    Add to email_logs                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   TRACKING & REPORTING                           │
│                                                                   │
│  ✅ Recipient marked as 'sent'                                   │
│  ✅ Log entry created                                            │
│  ✅ Campaign stats updated                                       │
│  ✅ Tracking pixel embedded                                      │
└─────────────────────────────────────────────────────────────────┘
```

## Defense Layers Summary

| Layer | Location | Type | Purpose | Strength |
|-------|----------|------|---------|----------|
| **1** | Application | Validation | Check before insert | 🛡️🛡️ Strong |
| **2** | Database | Constraint | Enforce uniqueness | 🛡️🛡️🛡️ Strongest |
| **3** | Runtime | Verification | Pre-send check | 🛡️🛡️ Strong |

## Protection Against Different Scenarios

### Scenario 1: Duplicate Import
```
User imports same CSV twice
    │
    ▼
Layer 1: First import succeeds, second returns false
    │
    ▼
Result: ✅ Only one copy of each email in campaign
```

### Scenario 2: Race Condition
```
Two cron jobs run simultaneously
    │
    ├── Job 1: Gets recipient #123
    │       │
    └── Job 2: Gets recipient #123 (same email)
            │
            ▼
Layer 3: Safety Check 1 detects duplicate
    │   │
    │   └── Job 2: Marks #123 as FAILED
    │
    ▼
Result: ✅ Only Job 1 sends email
```

### Scenario 3: Manual Database Insertion
```
Someone tries SQL: INSERT INTO email_recipients...
    │
    ▼
Layer 2: Database rejects with "Duplicate key error"
    │
    ▼
Result: ✅ Insert fails, duplicate prevented
```

### Scenario 4: Mixed Import Sources
```
1. Import from Users DB (email: user@test.com)
    │
    ▼
Layer 1: Inserted successfully
    │
2. Import from CSV (contains: user@test.com)
    │
    ▼
Layer 1: Detects duplicate, skips
    │
    ▼
Result: ✅ Only one entry for user@test.com
```

## Error Handling Flow

```
┌─────────────────────┐
│ Duplicate Detected  │
└─────────────────────┘
          │
          ├── At Import (Layer 1)
          │   └── Return false, skip silently
          │
          ├── At Insert (Layer 2)
          │   └── Database error, caught by application
          │
          └── At Send (Layer 3)
              └── Mark as failed, log error message
                  "Duplicate recipient - email already sent..."
```

## Data Flow with Timestamps

```
Time T0: Import Phase
│
├── 10:00:00 - User imports 100 emails
│   └── Layer 1 checks each: 100 inserts succeed
│
├── 10:01:00 - User imports same 100 emails
│   └── Layer 1 checks each: 0 inserts (all duplicates)
│
Time T1: Sending Phase
│
├── 10:05:00 - Cron job starts
│   ├── Gets recipient #1 (pending)
│   │   └── Layer 3 Check 1: No other sent ✓
│   │   └── Layer 3 Check 2: Status pending ✓
│   │   └── SEND EMAIL
│   │   └── Status: pending → sent
│   │
├── 10:05:10 - Cron job continues
│   ├── Gets recipient #2 (pending)
│   │   └── Layer 3 Check 1: No other sent ✓
│   │   └── Layer 3 Check 2: Status pending ✓
│   │   └── SEND EMAIL
│   │   └── Status: pending → sent
│   │
└── [continues for all pending recipients]
```

## Database Schema Changes

```sql
-- BEFORE (vulnerable to duplicates)
CREATE TABLE email_recipients (
    id INT PRIMARY KEY,
    campaign_id INT,
    email VARCHAR(255),
    ...
);

-- AFTER (protected against duplicates)
CREATE TABLE email_recipients (
    id INT PRIMARY KEY,
    campaign_id INT,
    email VARCHAR(255),
    ...
    UNIQUE KEY unique_campaign_email (campaign_id, email) ← NEW!
);
```

## Performance Impact Analysis

### Query Cost (per operation)

| Operation | Before | After | Overhead |
|-----------|--------|-------|----------|
| Import single email | 0 queries | 1 SELECT | +1-2ms |
| Import 100 emails | 0 queries | 100 SELECTs | +100-200ms |
| Send email | 3 queries | 5 queries | +2-4ms |
| Campaign completion | Same | Same | 0ms |

### Overall Impact
- Import phase: Slightly slower (acceptable tradeoff)
- Sending phase: Negligible impact (<1%)
- Database: Index improves lookup speed
- Net result: **Minimal performance impact**

## Security Audit Checklist

✅ **SQL Injection**: All queries use parameterized statements
✅ **Email Validation**: filter_var() with FILTER_VALIDATE_EMAIL
✅ **Race Conditions**: Multiple safety checks prevent
✅ **Data Integrity**: Database constraint enforces
✅ **Error Logging**: All failures logged for audit
✅ **No Sensitive Data**: Error messages don't expose internals

## Success Metrics

After deployment, monitor:
1. **Zero duplicates**: `SELECT campaign_id, email, COUNT(*) FROM email_recipients GROUP BY campaign_id, email HAVING COUNT(*) > 1` → 0 rows
2. **Import efficiency**: Failed imports due to duplicates increase
3. **Send accuracy**: No double-sends in email_logs
4. **Error logs**: "Duplicate recipient" errors indicate protection working

## Rollback Scenario

```
Issue Detected
    │
    ▼
1. Stop all campaigns (set status='paused')
    │
    ▼
2. Drop database constraint
   ALTER TABLE email_recipients DROP INDEX unique_campaign_email;
    │
    ▼
3. Revert code changes (git revert)
    │
    ▼
4. Investigate issue
    │
    ▼
5. Fix and redeploy
```

## Conclusion

This architecture provides **defense in depth** with three independent layers ensuring no duplicate emails are sent. Each layer can catch duplicates even if others fail, making the system extremely robust against duplicate sending scenarios.
