#!/bin/bash

# WhatsApp Verification Feature Installation Script
# This script helps install and configure the WhatsApp verification feature

echo "=========================================="
echo "WhatsApp Verification Feature Installer"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the correct directory
if [ ! -f "database/whatsapp_verification_migration.sql" ]; then
    echo -e "${RED}Error: Migration file not found!${NC}"
    echo "Please run this script from the project root directory."
    exit 1
fi

echo "Step 1: Database Migration"
echo "-------------------------"
echo -e "${YELLOW}Important: This will modify your database!${NC}"
echo "Please ensure you have:"
echo "  1. Backed up your database"
echo "  2. MySQL credentials ready"
echo ""
read -p "Do you want to continue? (yes/no): " continue_install

if [ "$continue_install" != "yes" ]; then
    echo "Installation cancelled."
    exit 0
fi

echo ""
read -p "Enter MySQL username: " mysql_user
read -sp "Enter MySQL password: " mysql_pass
echo ""
read -p "Enter database name: " mysql_db

echo ""
echo "Running database migration..."

# Run the migration
mysql -u "$mysql_user" -p"$mysql_pass" "$mysql_db" < database/whatsapp_verification_migration.sql

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database migration completed successfully!${NC}"
else
    echo -e "${RED}✗ Database migration failed!${NC}"
    echo "Please check your credentials and try again."
    exit 1
fi

echo ""
echo "Step 2: Verification"
echo "-------------------"
echo "Checking installed tables and columns..."

# Verify tables exist
tables_check=$(mysql -u "$mysql_user" -p"$mysql_pass" "$mysql_db" -e "SHOW TABLES LIKE 'whatsapp_otp%';" 2>/dev/null | wc -l)

if [ $tables_check -ge 2 ]; then
    echo -e "${GREEN}✓ WhatsApp OTP tables created successfully${NC}"
else
    echo -e "${YELLOW}⚠ Warning: Some tables may not have been created${NC}"
fi

# Check for new columns
columns_check=$(mysql -u "$mysql_user" -p"$mysql_pass" "$mysql_db" -e "DESCRIBE general_users google_id;" 2>/dev/null | wc -l)

if [ $columns_check -ge 1 ]; then
    echo -e "${GREEN}✓ New columns added to general_users table${NC}"
else
    echo -e "${YELLOW}⚠ Warning: Some columns may not have been added${NC}"
fi

echo ""
echo "Step 3: Configuration Check"
echo "---------------------------"

# Check if hooks are enabled
hooks_enabled=$(grep "enable_hooks.*TRUE" app/config/config.php 2>/dev/null)

if [ ! -z "$hooks_enabled" ]; then
    echo -e "${GREEN}✓ Hooks are enabled${NC}"
else
    echo -e "${RED}✗ Hooks are not enabled${NC}"
    echo "  Please verify app/config/config.php has: \$config['enable_hooks'] = TRUE;"
fi

# Check if WhatsApp notification library exists
if [ -f "app/libraries/Whatsapp_notification.php" ]; then
    echo -e "${GREEN}✓ WhatsApp notification library found${NC}"
else
    echo -e "${YELLOW}⚠ Warning: WhatsApp notification library not found${NC}"
    echo "  Please ensure WhatsApp API is properly configured"
fi

echo ""
echo "Step 4: Post-Installation Tasks"
echo "-------------------------------"
echo ""
echo "Please complete the following tasks:"
echo ""
echo "1. Configure WhatsApp API"
echo "   - Set WhatsApp API URL in database (whatsapp_config table)"
echo "   - Set API Key"
echo "   - Set Admin phone number (optional)"
echo ""
echo "2. Test the implementation"
echo "   - Try Google sign-in with a test account"
echo "   - Verify WhatsApp OTP is sent"
echo "   - Complete OTP verification"
echo "   - Test manual signup (should work unchanged)"
echo ""
echo "3. Clear application cache (if applicable)"
echo ""
echo "4. Monitor logs for any errors"
echo "   - Check app/logs/ directory"
echo "   - Check web server error logs"
echo ""
echo "=========================================="
echo -e "${GREEN}Installation Complete!${NC}"
echo "=========================================="
echo ""
echo "Documentation:"
echo "  - IMPLEMENTATION_SUMMARY.md - Overview and testing"
echo "  - WHATSAPP_VERIFICATION_GUIDE.md - Detailed guide"
echo "  - AUTHENTICATION_FLOWS.md - Flow diagrams"
echo ""
echo "For support or issues, please refer to the documentation."
echo ""
