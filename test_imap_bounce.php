#!/usr/bin/env php
<?php
/**
 * IMAP Bounce Detection Test Script
 * 
 * This script helps test IMAP connection and bounce detection functionality
 * Run from command line: php test_imap_bounce.php
 */

// Configuration - Update these with your test SMTP/IMAP details
$config = [
    'imap_host' => 'imap.gmail.com',
    'imap_port' => 993,
    'imap_encryption' => 'ssl',
    'imap_username' => 'your-email@gmail.com',  // Update this
    'imap_password' => 'your-app-password',      // Update this with App Password
];

echo "========================================\n";
echo "IMAP Bounce Detection Test Script\n";
echo "========================================\n\n";

// Check if IMAP extension is loaded
if (!extension_loaded('imap')) {
    echo "❌ ERROR: PHP IMAP extension is not installed!\n";
    echo "\nTo install:\n";
    echo "Ubuntu/Debian: sudo apt-get install php-imap && sudo systemctl restart apache2\n";
    echo "CentOS/RHEL: sudo yum install php-imap && sudo systemctl restart httpd\n";
    exit(1);
}

echo "✓ PHP IMAP extension is loaded\n\n";

// Validate configuration
if ($config['imap_username'] === 'your-email@gmail.com' || 
    $config['imap_password'] === 'your-app-password') {
    echo "❌ ERROR: Please update the configuration in this script with your IMAP credentials!\n";
    echo "\nFor Gmail:\n";
    echo "1. Go to Google Account Settings → Security\n";
    echo "2. Enable 2-Step Verification\n";
    echo "3. Generate App Password for 'Mail'\n";
    echo "4. Use the generated password in this script\n";
    exit(1);
}

// Build connection string
$flags = [];
if ($config['imap_encryption'] == 'ssl') {
    $flags[] = 'ssl';
} elseif ($config['imap_encryption'] == 'tls') {
    $flags[] = 'tls';
}
$flags[] = 'novalidate-cert'; // For self-signed certs

$flag_string = implode('/', $flags);
$connection_string = sprintf(
    '{%s:%d/imap%s}INBOX',
    $config['imap_host'],
    $config['imap_port'],
    $flag_string ? '/' . $flag_string : ''
);

echo "Connection String: $connection_string\n\n";
echo "Attempting to connect to IMAP server...\n";

// Attempt connection
$connection = @imap_open(
    $connection_string,
    $config['imap_username'],
    $config['imap_password']
);

if (!$connection) {
    $error = imap_last_error();
    echo "❌ IMAP Connection FAILED!\n";
    echo "Error: $error\n\n";
    echo "Common issues:\n";
    echo "1. Incorrect credentials\n";
    echo "2. IMAP not enabled in email account\n";
    echo "3. For Gmail: Need to use App Password, not regular password\n";
    echo "4. Firewall blocking port 993\n";
    exit(1);
}

echo "✓ IMAP Connection SUCCESSFUL!\n\n";

// Get mailbox info
$mailbox_info = imap_check($connection);
echo "Mailbox Information:\n";
echo "- Messages: " . $mailbox_info->Nmsgs . "\n";
echo "- Recent: " . $mailbox_info->Recent . "\n";
echo "- Unread: " . $mailbox_info->Unread . "\n";
echo "- Date: " . $mailbox_info->Date . "\n\n";

// Search for bounce emails
echo "Searching for bounce emails...\n";

$search_criteria = 'FROM "mailer-daemon"';
$bounce_emails = imap_search($connection, $search_criteria);

if (!$bounce_emails) {
    echo "No bounce emails from 'mailer-daemon' found.\n";
    
    // Try alternate search
    $search_criteria = 'FROM "Mail Delivery Subsystem"';
    $bounce_emails = imap_search($connection, $search_criteria);
    
    if (!$bounce_emails) {
        echo "No bounce emails from 'Mail Delivery Subsystem' found either.\n\n";
        echo "This is normal if you haven't sent any emails that bounced.\n";
        echo "To test:\n";
        echo "1. Send an email to an invalid address (e.g., invalid@nonexistentdomain12345.com)\n";
        echo "2. Wait for bounce notification\n";
        echo "3. Run this script again\n";
    }
}

if ($bounce_emails) {
    echo "✓ Found " . count($bounce_emails) . " bounce email(s)!\n\n";
    
    // Display first 5 bounce emails
    $display_count = min(5, count($bounce_emails));
    echo "Displaying first $display_count bounce email(s):\n";
    echo str_repeat("=", 60) . "\n";
    
    for ($i = 0; $i < $display_count; $i++) {
        $email_id = $bounce_emails[$i];
        $header = imap_headerinfo($connection, $email_id);
        $body = imap_body($connection, $email_id);
        
        echo "\nBounce Email #" . ($i + 1) . ":\n";
        echo "- From: " . $header->fromaddress . "\n";
        echo "- Subject: " . $header->subject . "\n";
        echo "- Date: " . $header->date . "\n";
        echo "- Preview: " . substr(strip_tags($body), 0, 200) . "...\n";
        
        // Try to extract bounced email address
        $patterns = [
            '/(?:to|recipient|address):\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/i',
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i'
        ];
        
        $extracted_emails = [];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $body, $matches)) {
                foreach ($matches[1] as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) &&
                        strpos($email, 'mailer-daemon') === false &&
                        strpos($email, 'postmaster') === false) {
                        $extracted_emails[] = $email;
                    }
                }
            }
        }
        
        if (!empty($extracted_emails)) {
            echo "- Bounced Email(s): " . implode(', ', array_unique($extracted_emails)) . "\n";
        }
        
        echo str_repeat("-", 60) . "\n";
    }
}

// Close connection
imap_close($connection);

echo "\n========================================\n";
echo "Test completed successfully!\n";
echo "========================================\n\n";

echo "Next Steps:\n";
echo "1. If connection worked, update your SMTP configuration in admin panel\n";
echo "2. Enable IMAP Bounce Detection for the SMTP config\n";
echo "3. Set up cron job to run bounce detection automatically\n";
echo "4. Or run manually from Email Marketing → Bounce Logs\n\n";

echo "Cron Job Example:\n";
echo "*/30 * * * * curl -X GET \"https://yourdomain.com/cron/bounce_detection?token=YOUR_TOKEN\"\n\n";
