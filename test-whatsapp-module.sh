#!/bin/bash

# WhatsApp Marketing Module Installation Test Script

echo "========================================"
echo "WhatsApp Marketing Module Test Script"
echo "========================================"
echo ""

# Check if required files exist
echo "Checking required files..."
echo ""

FILES=(
    "app/modules/whatsapp_marketing/controllers/Whatsapp_marketing.php"
    "app/modules/whatsapp_marketing/models/Whatsapp_marketing_model.php"
    "app/modules/whatsapp_marketing/views/index.php"
    "app/modules/whatsapp_marketing/views/campaigns/index.php"
    "app/modules/whatsapp_marketing/views/campaigns/create.php"
    "app/modules/whatsapp_marketing/views/campaigns/edit.php"
    "app/modules/whatsapp_marketing/views/recipients/index.php"
    "app/modules/whatsapp_marketing/views/logs/index.php"
    "app/modules/whatsapp_marketing/views/api_configs/index.php"
    "app/modules/whatsapp_marketing/views/api_configs/create.php"
    "app/modules/whatsapp_marketing/views/api_configs/edit.php"
    "app/controllers/Whatsapp_cron.php"
    "database/whatsapp-marketing.sql"
    "WHATSAPP_MARKETING_README.md"
)

ALL_EXIST=true

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ $file"
    else
        echo "✗ $file (MISSING)"
        ALL_EXIST=false
    fi
done

echo ""

# Check PHP syntax
echo "Checking PHP syntax..."
echo ""

PHP_FILES=(
    "app/modules/whatsapp_marketing/controllers/Whatsapp_marketing.php"
    "app/modules/whatsapp_marketing/models/Whatsapp_marketing_model.php"
    "app/controllers/Whatsapp_cron.php"
)

SYNTAX_OK=true

for file in "${PHP_FILES[@]}"; do
    if php -l "$file" > /dev/null 2>&1; then
        echo "✓ $file - Syntax OK"
    else
        echo "✗ $file - Syntax Error"
        php -l "$file"
        SYNTAX_OK=false
    fi
done

echo ""

# Check routes configuration
echo "Checking routes configuration..."
echo ""

if grep -q "whatsapp_cron/run" app/config/routes.php; then
    echo "✓ WhatsApp cron routes configured"
else
    echo "✗ WhatsApp cron routes NOT found in routes.php"
fi

echo ""

# Summary
echo "========================================"
echo "Test Summary"
echo "========================================"
echo ""

if [ "$ALL_EXIST" = true ] && [ "$SYNTAX_OK" = true ]; then
    echo "✓ All tests passed!"
    echo ""
    echo "Next steps:"
    echo "1. Import database/whatsapp-marketing.sql"
    echo "2. Configure WhatsApp API in admin panel"
    echo "3. Create your first campaign"
    echo "4. Set up cron jobs"
    echo ""
    echo "See WHATSAPP_MARKETING_README.md for detailed instructions."
    exit 0
else
    echo "✗ Some tests failed. Please check the errors above."
    exit 1
fi
