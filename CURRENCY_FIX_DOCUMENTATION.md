# Currency Conversion Fix Documentation

## Overview
This document explains the currency conversion bug fixes and the new API integration for automatic exchange rate updates.

## Problem Statement
The currency conversion was showing incorrect values. For example:
- **Incorrect**: PKR 916.6500 → USD 3.2914
- **Correct**: PKR 916.6500 → USD 3.24 (with rate 1 USD = 282.63 PKR)

### Root Cause
The exchange rates stored in the database were outdated or incorrect:
- Old USD rate: 0.00359066 (equivalent to 1 USD = 278.47 PKR)
- Correct USD rate: 0.00353876 (equivalent to 1 USD = 282.63 PKR)

## Solution Implemented

### 1. Fixed Exchange Rates
Updated the default exchange rates in `database/multi-currency.sql`:
```sql
-- Corrected USD exchange rate
('USD', 'US Dollar', '$', 0.00353876, 0, 1),
```

### 2. API Integration
Added automatic exchange rate fetching from `exchangerate-api.com`:

#### New Controller Methods
- `fetch_rates()` - Fetches latest exchange rates from API
- `cron_fetch_rates()` - Cron-friendly endpoint for scheduled updates

#### Features
1. **Fetch Latest Rates Button**: Admin can click to update all currency rates instantly
2. **Cron URL**: Automated scheduled updates via cron job
3. **Copy to Clipboard**: Easy copying of cron URL

### 3. Database Update Script
For existing installations, run `database/update-currency-rates.sql`:
```bash
mysql -u your_user -p your_database < database/update-currency-rates.sql
```

## How to Use

### Manual Update via Admin Panel
1. Navigate to **Settings → Currencies** (or `/setting/currencies`)
2. Click the **"Fetch Latest Rates"** button
3. Rates will be automatically updated from the API
4. Page will reload showing the new rates

### Automated Updates via Cron
1. Navigate to **Settings → Currencies**
2. Click **"Show Cron URL"** button
3. Copy the displayed URL
4. Set up a cron job (recommended: daily at midnight):
   ```bash
   0 0 * * * curl -s "https://yoursite.com/currencies/cron_fetch_rates" > /dev/null 2>&1
   ```

### Security for Cron Endpoint
To add token-based authentication:
1. Add a setting option `currency_cron_token` with a random secure token
2. The cron URL will automatically include this token
3. Example: `https://yoursite.com/currencies/cron_fetch_rates?token=YOUR_SECRET_TOKEN`

## Conversion Formula

The conversion formula used is:
```php
$converted = $amount * ($target->exchange_rate / $default->exchange_rate);
```

### Example Calculation
- Base currency: PKR (exchange_rate = 1.0)
- Target currency: USD (exchange_rate = 0.00353876)
- Amount: 916.65 PKR

```
916.65 × (0.00353876 / 1.0) = 916.65 × 0.00353876 = 3.2435 USD ≈ 3.24 USD
```

## Exchange Rate API

### API Provider
We use `exchangerate-api.com` which provides:
- Free tier with no API key required
- Daily updates
- 200+ currencies
- High reliability

### API Format
Request: `https://api.exchangerate-api.com/v4/latest/PKR`

Response:
```json
{
  "base": "PKR",
  "date": "2024-01-15",
  "rates": {
    "PKR": 1,
    "USD": 0.00353876,
    "EUR": 0.00325000,
    "GBP": 0.00280000,
    ...
  }
}
```

### Alternative API Providers
If you prefer a different API, modify the `fetch_rates()` method in:
`app/modules/currencies/controllers/currencies.php`

Popular alternatives:
- **fixer.io** (requires API key)
- **openexchangerates.org** (requires API key)
- **currencylayer.com** (requires API key)
- **exchangeratesapi.io** (requires API key)

## Testing the Fix

### Verify Correct Conversion
1. Navigate to **New Order** page
2. Select a service priced at PKR 916.6500
3. Switch currency to USD in the sidebar
4. The price should now show as **$3.24** (not $3.2914)

### Test Calculation
To verify the calculation manually:
```
PKR Amount / Exchange Rate = USD Amount
916.65 PKR / 282.63 = 3.2435 USD ≈ 3.24 USD (rounded to 2 decimals)
```

## Files Modified

1. **app/modules/currencies/controllers/currencies.php**
   - Added `fetch_rates()` method
   - Added `cron_fetch_rates()` method

2. **app/modules/setting/views/currencies.php**
   - Added "Fetch Latest Rates" button
   - Added "Show Cron URL" button
   - Added JavaScript handlers

3. **database/multi-currency.sql**
   - Updated default exchange rates

4. **database/update-currency-rates.sql** (NEW)
   - SQL script for updating existing installations

## Troubleshooting

### Issue: "Failed to fetch exchange rates from API"
**Solution**: 
- Check internet connectivity
- Verify the API endpoint is accessible
- Check if cURL is enabled on the server
- Try alternative API providers

### Issue: Currency conversion still showing old rates
**Solution**:
- Clear browser cache
- Reload the page
- Verify exchange rates in database
- Click "Fetch Latest Rates" button

### Issue: Cron job not working
**Solution**:
- Verify cron job syntax
- Check cron job logs
- Test the URL manually in browser
- Verify token (if using authentication)

## Future Enhancements

Potential improvements:
1. Multiple API provider support (fallback)
2. Currency rate history tracking
3. Admin notification on rate updates
4. Custom rate markup/discount per currency
5. Scheduled auto-updates without cron (using WordPress-style system)

## Support

For issues or questions:
1. Check this documentation
2. Review the code comments
3. Test with the API endpoint manually
4. Contact support with specific error messages
