# Multi-Currency Support - Installation and Usage Guide

## Overview
This implementation adds comprehensive multi-currency support to the SMM Panel script, allowing users to switch between different currencies and view all amounts in their preferred currency.

## Features
- ✅ Multiple currency support with real-time conversion
- ✅ Currency switcher in sidebar navigation
- ✅ Session and cookie-based currency persistence
- ✅ Admin interface for managing currencies
- ✅ Automatic conversion in all views (statistics, transactions, orders)
- ✅ Exchange rate management
- ✅ Default currency configuration

## Installation Steps

### 1. Database Migration
Run the SQL migration to create the currencies table and insert default currencies:

```bash
mysql -u [username] -p [database_name] < database/multi-currency.sql
```

Or manually execute the SQL via phpMyAdmin or your database management tool.

### 2. Verify Installation
After running the migration, you should have:
- A new `currencies` table in your database
- 7 default currencies (PKR, USD, EUR, GBP, INR, AUD, CAD)
- PKR set as the default currency

### 3. Configure Currencies
1. Login as admin
2. Navigate to **Settings > Currencies** (new tab)
3. Review and update exchange rates as needed
4. Set your preferred default currency
5. Enable/disable currencies as required

## Usage

### For End Users
1. Look for the **Currency** dropdown in the sidebar (below the balance)
2. Select your preferred currency
3. The page will reload and all amounts will be displayed in the selected currency
4. Your selection is remembered via session and cookie (30 days)

### For Administrators
1. Navigate to **Settings > Currencies**
2. Manage currencies:
   - **Update Exchange Rates**: Enter new rates and click "Update"
   - **Set Default**: Choose which currency is the base for conversions
   - **Toggle Status**: Enable/disable currencies
   - **Add New**: Add additional currencies as needed

## How It Works

### Currency Conversion
- All amounts are stored in the default currency (usually USD)
- When a user selects a different currency, amounts are converted using the exchange rate
- Formula: `converted_amount = original_amount × (target_rate ÷ default_rate)`

### Helper Functions
New helper functions are available:

```php
// Get current selected currency
$currency = get_current_currency();

// Convert amount to current currency
$converted = convert_currency($amount);

// Format amount with currency symbol
$formatted = format_currency($amount, $convert = true);

// Get all active currencies
$currencies = get_active_currencies();
```

## File Changes Summary

### New Files
- `database/multi-currency.sql` - Database migration
- `app/modules/currencies/models/currencies_model.php` - Currency model
- `app/modules/currencies/controllers/currencies.php` - Currency controller
- `app/modules/setting/views/currencies.php` - Admin UI for currency management

### Modified Files
- `app/helpers/currency_helper.php` - Added multi-currency helper functions
- `app/language/english/common_lang.php` - Added "Currency" translation
- `app/modules/blocks/views/header.php` - Added currency switcher to sidebar
- `app/modules/statistics/views/index.php` - Updated to use currency conversion
- `app/modules/statistics/views/last_5_transactions.php` - Updated amounts
- `app/modules/transactions/views/index.php` - Updated amounts
- `app/modules/transactions/views/ajax_search.php` - Updated amounts
- `app/modules/order/views/logs/logs.php` - Updated amounts
- `app/modules/order/views/logs/ajax_search.php` - Updated amounts

## Exchange Rate Management

### Updating Exchange Rates
Exchange rates should be updated regularly to reflect current market rates. You can:

1. **Manual Update**: 
   - Go to Settings > Currencies
   - Enter new rates
   - Click "Update" for each currency

2. **Recommended Services for Rates**:
   - Use APIs like exchangerate-api.io, fixer.io, or openexchangerates.org
   - Update rates daily or weekly depending on your needs

### Setting Default Currency
The default currency is the base for all conversions. To change it:
1. Go to Settings > Currencies
2. Click "Set as Default" next to your preferred currency
3. Update exchange rates relative to the new default

## Troubleshooting

### Currency Switcher Not Appearing
- Ensure you're logged in
- Check that the header.php file was updated correctly
- Verify that currencies exist in the database

### Conversion Not Working
- Check that exchange rates are set correctly (not 0)
- Verify the default currency is set
- Check browser console for JavaScript errors

### Amounts Not Converting
- Ensure helper functions are loaded
- Check that `get_current_currency()` returns a valid currency object
- Verify the view files use `convert_currency()` function

## API Considerations

If you have an API, you may want to:
1. Allow API requests to specify currency via parameter
2. Return amounts in both default and requested currencies
3. Document currency handling in API documentation

## Performance Notes

- Currency conversion is lightweight (simple multiplication)
- Currency selection is cached in session/cookie
- No impact on database queries (read-only for currency data)

## Security

- All currency inputs are sanitized
- CSRF protection on all currency management actions
- Only admins can manage currencies
- Users can only select from active currencies

## Future Enhancements

Potential improvements:
- Automatic exchange rate updates via API
- Historical exchange rate tracking
- Currency-specific number formatting
- Multi-currency support in payment gateways
- Reports with currency conversion history

## Support

If you encounter any issues:
1. Check the database migration ran successfully
2. Verify file permissions are correct
3. Check server error logs
4. Clear browser cache and cookies
5. Test with different browsers

## Credits

Multi-currency support implementation for SMM Panel Script
Version: 1.0
Date: 2025
