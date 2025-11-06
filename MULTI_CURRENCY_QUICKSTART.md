# Multi-Currency Support - Quick Start Guide

## Overview
This SMM panel now supports multiple currencies with automatic exchange rate updates. Users can view prices in their preferred currency while all backend operations remain in the default currency (PKR).

## Problem Solved
**Before:** PKR 916.65 showed as USD 3.2914 ❌  
**After:** PKR 916.65 shows as USD 3.24 ✅  

## Quick Setup

### For New Installations
```bash
# Run the multi-currency SQL file
mysql -u username -p database_name < database/multi-currency.sql
```

### For Existing Installations
```bash
# Update exchange rates in existing database
mysql -u username -p database_name < database/update-currency-rates.sql
```

## Features

### 1. Manual Rate Updates
1. Login as Admin
2. Navigate to **Settings → Currencies**
3. Click **"Fetch Latest Rates"** button
4. Rates update automatically from API

### 2. Automated Updates (Cron Job)
1. Navigate to **Settings → Currencies**
2. Click **"Show Cron URL"**
3. Copy the URL
4. Add to your crontab:
   ```bash
   # Update currency rates daily at 1:00 AM
   0 1 * * * curl -s "YOUR_CRON_URL_HERE" > /dev/null 2>&1
   ```

### 3. User Currency Selection
Users can switch currencies from:
- **Sidebar** in desktop view
- **Header** in mobile view

## How It Works

### Currency Conversion Formula
```
Converted Amount = Original Amount × (Target Rate / Base Rate)
```

**Example:**
- Original: 916.65 PKR
- Target: USD
- Rate: 0.00353876 (1 PKR = 0.00353876 USD)
- Result: 916.65 × 0.00353876 = 3.24 USD ✓

### Display vs Storage
| Aspect | Currency Used |
|--------|---------------|
| **Price Display** | User's selected currency |
| **Database Storage** | Default currency (PKR) |
| **Order Calculation** | Default currency (PKR) |
| **Balance Management** | Default currency (PKR) |
| **API Transactions** | Default currency (PKR) |

This ensures:
- ✓ Consistency in financial records
- ✓ No rounding errors in transactions
- ✓ Accurate balance calculations
- ✓ Proper profit tracking

## Supported Currencies

### Default Currencies (Included)
- PKR - Pakistani Rupee (Default/Base)
- USD - US Dollar
- EUR - Euro
- GBP - British Pound
- INR - Indian Rupee
- AUD - Australian Dollar
- CAD - Canadian Dollar

### Adding More Currencies
1. Go to **Settings → Currencies**
2. Scroll to "Add New Currency" section
3. Fill in:
   - **Code**: 3-letter ISO code (e.g., AED)
   - **Name**: Full name (e.g., UAE Dirham)
   - **Symbol**: Currency symbol (e.g., د.إ)
   - **Exchange Rate**: Rate relative to PKR
4. Click **Add**

## API Integration

### Default API Provider
**exchangerate-api.com**
- ✓ Free tier (no API key required)
- ✓ 200+ currencies supported
- ✓ Daily updates
- ✓ High reliability

### Rate Update Process
1. API is called with base currency (PKR)
2. Rates are fetched for all currencies
3. Database is updated with new rates
4. Users see updated prices immediately

### Alternative API Providers
You can modify the API in:  
`app/modules/currencies/controllers/currencies.php`

Popular alternatives:
- **fixer.io** (requires API key)
- **openexchangerates.org** (requires API key)
- **currencylayer.com** (requires API key)

## Testing

### Verify Conversion
Run the test script:
```bash
./test-currency-conversion.sh
```

Expected output:
```
✓ PASS: Conversion is correct (3.24 USD)
```

### Manual Testing
1. Create a service priced at PKR 916.65
2. Switch to USD in currency selector
3. Verify price shows as $3.24

## Troubleshooting

### Issue: Rates Not Updating
**Solution:**
```bash
# Check cURL is available
curl --version

# Test API manually
curl "https://api.exchangerate-api.com/v4/latest/PKR"

# Check database connection
mysql -u username -p -e "SELECT * FROM currencies;"
```

### Issue: Wrong Currency Showing
**Solution:**
1. Clear browser cache
2. Logout and login again
3. Check cookie settings
4. Verify currency is active in database

### Issue: Cron Not Working
**Solution:**
```bash
# Verify cron service is running
service cron status

# Check cron logs
grep CRON /var/log/syslog

# Test URL manually
curl "YOUR_CRON_URL_HERE"
```

## Security

### Cron Token Authentication
Add a security token for cron access:

1. In database, add option:
   ```sql
   INSERT INTO general_options (name, value) 
   VALUES ('currency_cron_token', 'YOUR_RANDOM_TOKEN_HERE');
   ```

2. Cron URL will automatically include token:
   ```
   https://yoursite.com/currencies/cron_fetch_rates?token=YOUR_RANDOM_TOKEN_HERE
   ```

### SSL Certificate
Ensure your server has valid SSL certificates for API calls:
```bash
# Check SSL
openssl s_client -connect api.exchangerate-api.com:443
```

## File Changes

### Modified Files
- `app/modules/currencies/controllers/currencies.php`
- `app/modules/setting/views/currencies.php`
- `database/multi-currency.sql`

### New Files
- `database/update-currency-rates.sql`
- `CURRENCY_FIX_DOCUMENTATION.md`
- `test-currency-conversion.sh`

## Support & Documentation

### Full Documentation
See `CURRENCY_FIX_DOCUMENTATION.md` for complete technical details.

### Need Help?
1. Check the troubleshooting section above
2. Review the full documentation
3. Test with the provided test script
4. Verify database schema is correct

## Version History

### v1.0.0 - Currency Fix & API Integration
- ✓ Fixed exchange rate calculations
- ✓ Added API integration for automatic updates
- ✓ Added admin UI for rate management
- ✓ Added cron support for scheduled updates
- ✓ Improved security (SSL verification)
- ✓ Modernized code (Clipboard API)

---

**Last Updated:** 2024-01-15  
**Author:** Currency Module Team  
**License:** Same as main project
