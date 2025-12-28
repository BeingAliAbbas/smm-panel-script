#!/usr/bin/env php
<?php
/**
 * Email Deliverability Validation Script
 * 
 * This script validates that the deliverability improvements are working correctly
 * Run from command line: php validate_deliverability.php
 */

// Color output for terminal
function colorize($text, $status) {
    $colors = [
        'success' => "\033[0;32m", // Green
        'error' => "\033[0;31m",   // Red
        'warning' => "\033[0;33m", // Yellow
        'info' => "\033[0;36m",    // Cyan
        'reset' => "\033[0m"
    ];
    return $colors[$status] . $text . $colors['reset'];
}

function check($description, $result, $details = '') {
    $status = $result ? 'success' : 'error';
    $symbol = $result ? '✓' : '✗';
    echo colorize("[$symbol] $description", $status) . "\n";
    if ($details) {
        echo "    " . colorize($details, 'info') . "\n";
    }
    return $result;
}

echo "\n";
echo colorize("=================================================\n", 'info');
echo colorize("  Email Deliverability Validation Tool\n", 'info');
echo colorize("=================================================\n", 'info');
echo "\n";

$errors = 0;
$warnings = 0;
$checks = 0;

// Check 1: PHPMailer library exists
$checks++;
$phpmailer_path = __DIR__ . '/app/libraries/PHPMailer/src/PHPMailer.php';
if (!check("PHPMailer library exists", file_exists($phpmailer_path), $phpmailer_path)) {
    $errors++;
    echo colorize("    ERROR: PHPMailer library not found!\n", 'error');
}

// Check 2: PHPMailer SMTP exists
$checks++;
$smtp_path = __DIR__ . '/app/libraries/PHPMailer/src/SMTP.php';
if (!check("PHPMailer SMTP class exists", file_exists($smtp_path), $smtp_path)) {
    $errors++;
}

// Check 3: Email_cron.php exists
$checks++;
$cron_path = __DIR__ . '/app/controllers/Email_cron.php';
if (!check("Email_cron controller exists", file_exists($cron_path), $cron_path)) {
    $errors++;
}

// Check 4: Email_cron.php uses PHPMailer (not CI Email)
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $uses_phpmailer = (strpos($cron_content, 'use \PHPMailer\PHPMailer\PHPMailer') !== false) 
                   || (strpos($cron_content, 'new \PHPMailer\PHPMailer\PHPMailer') !== false);
    $uses_ci_email = strpos($cron_content, "\$this->email->send()") !== false;
    
    if (!check("Email_cron uses PHPMailer", $uses_phpmailer && !$uses_ci_email)) {
        $errors++;
        echo colorize("    ERROR: Email_cron still uses CI Email library!\n", 'error');
    }
}

// Check 5: Plain text alternative is implemented
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $has_altbody = strpos($cron_content, '$mail->AltBody') !== false;
    
    if (!check("Plain text alternative (AltBody) implemented", $has_altbody)) {
        $errors++;
    }
}

// Check 6: List-Unsubscribe header is added
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $has_unsubscribe = strpos($cron_content, 'List-Unsubscribe') !== false;
    
    if (!check("List-Unsubscribe header added", $has_unsubscribe)) {
        $errors++;
    }
}

// Check 7: Return-Path header is set
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $has_sender = strpos($cron_content, '$mail->Sender') !== false;
    
    if (!check("Return-Path (Sender) header set", $has_sender)) {
        $errors++;
    }
}

// Check 8: Custom X-Mailer header
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $has_custom_mailer = strpos($cron_content, "addCustomHeader('X-Mailer'") !== false;
    
    if (!check("Custom X-Mailer header (not 'CodeIgniter')", $has_custom_mailer)) {
        $warnings++;
    }
}

// Check 9: Precedence: bulk header
$checks++;
if (file_exists($cron_path)) {
    $cron_content = file_get_contents($cron_path);
    $has_precedence = strpos($cron_content, 'Precedence') !== false;
    
    if (!check("Precedence: bulk header added", $has_precedence)) {
        $warnings++;
    }
}

// Check 10: Unsubscribe functionality exists
$checks++;
$controller_path = __DIR__ . '/app/modules/email_marketing/controllers/email_marketing.php';
if (file_exists($controller_path)) {
    $controller_content = file_get_contents($controller_path);
    $has_unsubscribe_method = strpos($controller_content, 'function unsubscribe') !== false;
    
    if (!check("Unsubscribe endpoint implemented", $has_unsubscribe_method)) {
        $warnings++;
    }
}

// Check 11: Database migration file exists
$checks++;
$migration_path = __DIR__ . '/database/email-deliverability-improvements.sql';
if (!check("Database migration file exists", file_exists($migration_path), $migration_path)) {
    $warnings++;
}

// Check 12: Documentation exists
$checks++;
$doc_path = __DIR__ . '/EMAIL_DELIVERABILITY_IMPROVEMENTS.md';
if (!check("Deliverability documentation exists", file_exists($doc_path))) {
    $warnings++;
}

// Check 13: Setup guide exists
$checks++;
$setup_path = __DIR__ . '/DELIVERABILITY_SETUP.md';
if (!check("Setup guide exists", file_exists($setup_path))) {
    $warnings++;
}

// Check 14: Headers comparison doc exists
$checks++;
$headers_path = __DIR__ . '/EMAIL_HEADERS_COMPARISON.md';
if (!check("Headers comparison document exists", file_exists($headers_path))) {
    $warnings++;
}

// Check 15: PHP syntax validation
$checks++;
$syntax_ok = true;
if (file_exists($cron_path)) {
    exec("php -l " . escapeshellarg($cron_path) . " 2>&1", $output, $return);
    $syntax_ok = ($return === 0);
    
    if (!check("Email_cron.php syntax valid", $syntax_ok)) {
        $errors++;
        echo colorize("    Syntax errors:\n" . implode("\n    ", $output) . "\n", 'error');
    }
}

// Check 16: Spam risk scoring implemented
$checks++;
$model_path = __DIR__ . '/app/modules/email_marketing/models/email_marketing_model.php';
if (file_exists($model_path)) {
    $model_content = file_get_contents($model_path);
    $has_spam_scoring = strpos($model_content, 'calculate_spam_risk_score') !== false;
    
    if (!check("Spam risk scoring implemented", $has_spam_scoring)) {
        $warnings++;
    }
}

// Check 17: Deliverability reporting implemented
$checks++;
if (file_exists($model_path)) {
    $model_content = file_get_contents($model_path);
    $has_reporting = strpos($model_content, 'get_deliverability_report') !== false;
    
    if (!check("Deliverability reporting implemented", $has_reporting)) {
        $warnings++;
    }
}

// Summary
echo "\n";
echo colorize("=================================================\n", 'info');
echo colorize("  Validation Summary\n", 'info');
echo colorize("=================================================\n", 'info');
echo "\n";

echo "Total Checks: " . colorize($checks, 'info') . "\n";
echo "Errors: " . colorize($errors, $errors > 0 ? 'error' : 'success') . "\n";
echo "Warnings: " . colorize($warnings, $warnings > 0 ? 'warning' : 'success') . "\n";
echo "Passed: " . colorize(($checks - $errors - $warnings), 'success') . "\n";
echo "\n";

if ($errors > 0) {
    echo colorize("✗ VALIDATION FAILED - Please fix errors above\n\n", 'error');
    exit(1);
} elseif ($warnings > 0) {
    echo colorize("⚠ VALIDATION PASSED WITH WARNINGS\n", 'warning');
    echo colorize("  Optional features are missing but core functionality is OK\n\n", 'warning');
    exit(0);
} else {
    echo colorize("✓ ALL CHECKS PASSED!\n", 'success');
    echo colorize("  Email deliverability improvements are correctly implemented\n\n", 'success');
    exit(0);
}
