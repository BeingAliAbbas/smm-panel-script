#!/bin/bash

# Multi-Currency Implementation Validation Script
# Run this script to verify the multi-currency implementation

echo "======================================"
echo "Multi-Currency Implementation Checker"
echo "======================================"
echo ""

# Check if database migration file exists
echo "1. Checking database migration file..."
if [ -f "database/multi-currency.sql" ]; then
    echo "   ✓ Migration file exists"
else
    echo "   ✗ Migration file NOT found!"
    exit 1
fi

# Check if currencies module exists
echo ""
echo "2. Checking currencies module..."
if [ -d "app/modules/currencies" ]; then
    echo "   ✓ Currencies module directory exists"
    
    if [ -f "app/modules/currencies/models/currencies_model.php" ]; then
        echo "   ✓ Currency model exists"
    else
        echo "   ✗ Currency model NOT found!"
    fi
    
    if [ -f "app/modules/currencies/controllers/currencies.php" ]; then
        echo "   ✓ Currency controller exists"
    else
        echo "   ✗ Currency controller NOT found!"
    fi
else
    echo "   ✗ Currencies module NOT found!"
fi

# Check if helper functions were added
echo ""
echo "3. Checking helper functions..."
if grep -q "get_current_currency" app/helpers/currency_helper.php; then
    echo "   ✓ get_current_currency() function added"
else
    echo "   ✗ get_current_currency() NOT found!"
fi

if grep -q "convert_currency" app/helpers/currency_helper.php; then
    echo "   ✓ convert_currency() function added"
else
    echo "   ✗ convert_currency() NOT found!"
fi

if grep -q "format_currency" app/helpers/currency_helper.php; then
    echo "   ✓ format_currency() function added"
else
    echo "   ✗ format_currency() NOT found!"
fi

# Check if header was updated
echo ""
echo "4. Checking sidebar currency switcher..."
if grep -q "currencySelector" app/modules/blocks/views/header.php; then
    echo "   ✓ Currency selector added to header"
else
    echo "   ✗ Currency selector NOT found in header!"
fi

# Check if admin UI exists
echo ""
echo "5. Checking admin management UI..."
if [ -f "app/modules/setting/views/currencies.php" ]; then
    echo "   ✓ Currency management UI exists"
else
    echo "   ✗ Currency management UI NOT found!"
fi

# Check if key views were updated
echo ""
echo "6. Checking view updates..."

files_to_check=(
    "app/modules/statistics/views/index.php"
    "app/modules/transactions/views/index.php"
    "app/modules/order/views/logs/ajax_search.php"
)

for file in "${files_to_check[@]}"; do
    if grep -q "convert_currency" "$file"; then
        echo "   ✓ $file updated"
    else
        echo "   ✗ $file NOT updated!"
    fi
done

# Check language file
echo ""
echo "7. Checking language translations..."
if grep -q "Currency" app/language/english/common_lang.php; then
    echo "   ✓ Language strings added"
else
    echo "   ✗ Language strings NOT found!"
fi

echo ""
echo "======================================"
echo "Validation Complete!"
echo "======================================"
echo ""
echo "Next steps:"
echo "1. Run the database migration: database/multi-currency.sql"
echo "2. Login as admin and navigate to Settings > Currencies"
echo "3. Configure exchange rates"
echo "4. Test currency switching in the sidebar"
echo ""
