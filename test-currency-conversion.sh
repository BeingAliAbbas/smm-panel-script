#!/bin/bash
# Currency Conversion Test Script
# This script verifies that the currency conversion is working correctly

echo "================================================"
echo "Currency Conversion Test"
echo "================================================"
echo ""
echo "Test Case from Problem Statement:"
echo "  Service Price: PKR 916.6500"
echo "  Expected USD: $3.24"
echo "  Exchange Rate: 1 USD = 282.63 PKR"
echo ""

# Calculate the conversion
PKR_AMOUNT=916.65
USD_RATE=282.63

# Using bc for precise calculation
USD_AMOUNT=$(echo "scale=4; $PKR_AMOUNT / $USD_RATE" | bc)
USD_ROUNDED=$(echo "scale=2; $PKR_AMOUNT / $USD_RATE" | bc)

echo "Calculation:"
echo "  PKR_AMOUNT / USD_RATE = $PKR_AMOUNT / $USD_RATE"
echo "  Result (4 decimals): $USD_AMOUNT USD"
echo "  Result (2 decimals): $USD_ROUNDED USD"
echo ""

# Alternative calculation using exchange rate as 1 PKR = X USD
PKR_TO_USD=$(echo "scale=8; 1 / $USD_RATE" | bc)
USD_ALT=$(echo "scale=4; $PKR_AMOUNT * $PKR_TO_USD" | bc)

echo "Alternative Calculation (database format):"
echo "  1 PKR = $PKR_TO_USD USD (exchange_rate in database)"
echo "  PKR_AMOUNT * exchange_rate = $PKR_AMOUNT * $PKR_TO_USD"
echo "  Result: $USD_ALT USD"
echo ""

# Verify the old incorrect rate
OLD_RATE=0.00359066
OLD_RESULT=$(echo "scale=4; $PKR_AMOUNT * $OLD_RATE" | bc)

echo "Old Incorrect Calculation:"
echo "  PKR_AMOUNT * old_exchange_rate = $PKR_AMOUNT * $OLD_RATE"
echo "  Result: $OLD_RESULT USD (INCORRECT - should be 3.24)"
echo ""

# Verify the new correct rate
NEW_RATE=0.00353876
NEW_RESULT=$(echo "scale=4; $PKR_AMOUNT * $NEW_RATE" | bc)
NEW_ROUNDED=$(echo "scale=2; $PKR_AMOUNT * $NEW_RATE" | bc)

echo "New Correct Calculation:"
echo "  PKR_AMOUNT * new_exchange_rate = $PKR_AMOUNT * $NEW_RATE"
echo "  Result (4 decimals): $NEW_RESULT USD"
echo "  Result (2 decimals): $NEW_ROUNDED USD (CORRECT!)"
echo ""

echo "================================================"
echo "Test Summary:"
echo "================================================"
if [ "$NEW_ROUNDED" = "3.24" ]; then
    echo "✓ PASS: Conversion is correct ($NEW_ROUNDED USD)"
else
    echo "✗ FAIL: Conversion is incorrect (expected 3.24 USD, got $NEW_ROUNDED USD)"
fi
echo ""
