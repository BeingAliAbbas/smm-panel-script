#!/usr/bin/env php
<?php
/**
 * Bounce Detection Token Generator
 * 
 * This script helps generate the correct token for the bounce detection cron
 * Usage: php generate_bounce_token.php
 */

echo "========================================\n";
echo "Bounce Detection Token Generator\n";
echo "========================================\n\n";

// Check if we can read the config to get ENCRYPTION_KEY
$config_path = __DIR__ . '/app/config/config.php';

if (file_exists($config_path)) {
    echo "Reading ENCRYPTION_KEY from config...\n";
    
    // Read the config file
    $config_content = file_get_contents($config_path);
    
    // Try to extract ENCRYPTION_KEY
    if (preg_match('/define\s*\(\s*[\'"]ENCRYPTION_KEY[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/i', $config_content, $matches)) {
        $encryption_key = $matches[1];
        echo "✓ Found ENCRYPTION_KEY\n\n";
        
        // Generate the token
        $token = md5('bounce_detection_cron_' . $encryption_key);
        
        echo "========================================\n";
        echo "Your Bounce Detection Token:\n";
        echo "========================================\n";
        echo "$token\n";
        echo "========================================\n\n";
        
        echo "Use this token in your cron job:\n";
        echo "curl 'https://yourdomain.com/cron/bounce_detection?token=$token'\n\n";
        
        echo "Or test the endpoint:\n";
        echo "curl 'https://yourdomain.com/cron/bounce_detection/test'\n\n";
        
    } else {
        echo "❌ Could not find ENCRYPTION_KEY in config file\n";
        echo "Please check app/config/config.php manually\n\n";
        
        echo "To generate token manually:\n";
        echo "1. Find ENCRYPTION_KEY in app/config/config.php\n";
        echo "2. Run: php -r \"echo md5('bounce_detection_cron_' . 'YOUR_ENCRYPTION_KEY');\"\n";
    }
} else {
    echo "❌ Config file not found at: $config_path\n";
    echo "Please run this script from the root directory of the project\n\n";
    
    echo "To generate token manually:\n";
    echo "1. Find ENCRYPTION_KEY in app/config/config.php\n";
    echo "2. Run: php -r \"echo md5('bounce_detection_cron_' . 'YOUR_ENCRYPTION_KEY');\"\n";
}

echo "\n========================================\n";
echo "IMPORTANT NOTES:\n";
echo "========================================\n";
echo "1. Do NOT use 'YOUR_TOKEN' literally in the URL\n";
echo "2. The token shown above is the actual token you need\n";
echo "3. Test first: /cron/bounce_detection/test (no token needed)\n";
echo "4. Then use: /cron/bounce_detection?token=ACTUAL_TOKEN\n";
echo "========================================\n\n";
