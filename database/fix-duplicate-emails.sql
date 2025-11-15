-- =====================================================
-- Fix: Prevent Duplicate Email Sending in Campaigns
-- This migration adds database constraints and cleanup
-- =====================================================

-- Step 1: Remove any existing duplicates (keep the oldest record for each email per campaign)
DELETE r1 FROM email_recipients r1
INNER JOIN email_recipients r2 
WHERE r1.campaign_id = r2.campaign_id 
  AND r1.email = r2.email 
  AND r1.id > r2.id;

-- Step 2: Add unique constraint to prevent future duplicates
-- This ensures that each email can only appear once per campaign
ALTER TABLE `email_recipients` 
ADD UNIQUE KEY `unique_campaign_email` (`campaign_id`, `email`);

-- =====================================================
-- END OF MIGRATION
-- =====================================================
