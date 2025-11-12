<?php
/**
 * WhatsApp Marketing System - Test Script
 * 
 * This script tests the WhatsApp API integration
 * Run this from command line: php test-whatsapp-api.php
 */

// Configuration - Update these values
$apiUrl = "http://waapi.beastsmm.pk/send-message";
$apiKey = "123456";  // Update with your actual API key
$testPhoneNumber = "923483469617";  // Update with your test phone number
$testMessage = "Hello! This is a test message from WhatsApp Marketing System.";

echo "WhatsApp Marketing System - API Test\n";
echo "=====================================\n\n";

echo "Configuration:\n";
echo "  API URL: $apiUrl\n";
echo "  API Key: " . substr($apiKey, 0, 3) . "***\n";
echo "  Test Phone: $testPhoneNumber\n\n";

// Prepare data
$data = [
    "apiKey" => $apiKey,
    "phoneNumber" => $testPhoneNumber,
    "message" => $testMessage
];

echo "Sending test message...\n";

// Initialize cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_errno($ch)) {
    echo "\n❌ Request Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit(1);
}

curl_close($ch);

echo "\nHTTP Status Code: $httpCode\n";
echo "Response:\n";
echo "--------\n";
echo $response . "\n";
echo "--------\n\n";

// Check HTTP response code
if ($httpCode >= 200 && $httpCode < 300) {
    echo "✅ SUCCESS! Message sent successfully.\n";
    echo "\nThe WhatsApp API is working correctly!\n";
    echo "You can now use the WhatsApp Marketing System.\n";
    exit(0);
} else {
    echo "❌ FAILED! HTTP Error $httpCode\n";
    echo "\nPlease check:\n";
    echo "1. Your API key is correct\n";
    echo "2. The phone number is valid\n";
    echo "3. The API endpoint is accessible\n";
    exit(1);
}
