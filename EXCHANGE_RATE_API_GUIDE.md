# Exchange Rate API Integration Guide

## Overview
This feature automatically fetches real-time exchange rates from an external API to update the currency conversion rate in the **Settings > Currency** page. This eliminates the need to manually enter the exchange rate (e.g., 1 USD to PKR) every time.

## Features
- ✅ One-click exchange rate fetching from free API
- ✅ Automatic updates via cron job
- ✅ Secure cron endpoint with token authentication
- ✅ Works with the existing auto currency converter

## How It Works

### Manual Update
1. Navigate to **Settings > Currency** in your admin panel
2. Under the "Auto Currency Converter" section, you'll see:
   - The exchange rate input field (1 USD = X [Your Currency])
   - A button: **"Fetch Current Exchange Rate"**
3. Click the button to fetch the latest exchange rate
4. The rate field will be automatically updated
5. Click **Save** to apply the new rate

### Automatic Updates via Cron

#### Step 1: Get the Cron URL
1. Navigate to **Settings > Currency**
2. Click the **"Show Cron URL for Auto-Update"** button
3. A cron URL will be displayed in a yellow alert box
4. Click **"Copy"** to copy the URL to your clipboard

#### Step 2: Set Up Cron Job
Add the following to your crontab to update the exchange rate daily at midnight:

```bash
0 0 * * * curl "YOUR_CRON_URL_HERE"
```

**Example:**
```bash
0 0 * * * curl "https://yoursite.com/setting/cron_update_exchange_rate?token=abc123xyz456"
```

**Other Cron Schedule Examples:**
- Every hour: `0 * * * * curl "URL"`
- Every 6 hours: `0 */6 * * * curl "URL"`
- Every Monday at 9 AM: `0 9 * * 1 curl "URL"`
- Twice daily (9 AM and 9 PM): `0 9,21 * * * curl "URL"`

#### Step 3: Verify Cron Job
After setting up the cron job, you can verify it's working by:
1. Checking the `new_currecry_rate` value in your database
2. Calling the cron URL manually in your browser
3. Checking your cron job logs

## API Details

### API Provider
The implementation uses [exchangerate-api.com](https://exchangerate-api.com) which:
- ✅ Is completely free (no API key required for basic usage)
- ✅ Provides real-time exchange rates for 160+ currencies
- ✅ Updates rates every 24 hours
- ✅ Has high reliability and uptime

### Supported Currencies
The API supports all major currencies including:
- PKR (Pakistani Rupee)
- USD (US Dollar)
- EUR (Euro)
- GBP (British Pound)
- INR (Indian Rupee)
- AUD (Australian Dollar)
- CAD (Canadian Dollar)
- And 150+ more

## Security

### Cron Token
- A unique random token is generated for your cron endpoint
- The token is stored in your database
- Only requests with the correct token can update the exchange rate
- This prevents unauthorized rate updates

### Token Regeneration
If you need to regenerate the cron token:
1. Delete the `exchange_rate_cron_token` from the `general_options` table
2. Click "Show Cron URL" again to generate a new token
3. Update your cron job with the new URL

## Technical Details

### How Exchange Rates are Fetched
1. The system uses the selected **Currency Code** from Settings > Currency
2. It fetches the rate from USD to your selected currency
3. Example: If your currency is PKR, it fetches: 1 USD = X PKR
4. This rate is then stored in the `new_currecry_rate` option

### Database
The exchange rate is stored in the `general_options` table as `new_currecry_rate`

### API Request Flow
```
User/Cron → Controller Method → cURL API Call → Parse JSON → Update Database
```

### Error Handling
The system handles various error cases:
- API connection failures
- Invalid currency codes
- Missing or malformed API responses
- Authentication failures (for cron)

## Integration with Auto Currency Converter

This feature works seamlessly with the existing auto currency converter:
1. Enable "Auto Currency Converter" toggle
2. The `new_currecry_rate` is used when fetching/syncing services from SMM providers
3. Service prices are automatically converted using this rate
4. Example: Provider price $1.00 × 278.50 (PKR rate) = Rs 278.50

## Troubleshooting

### Button Doesn't Work
- Check browser console for JavaScript errors
- Ensure you're logged in as admin
- Verify CSRF tokens are enabled

### Cron Job Not Updating
- Verify the cron URL is correct
- Check that the token matches
- Ensure your server can make outbound HTTP requests
- Check cron job logs for errors

### API Returns Error
- Verify your currency code is valid
- Check internet connectivity
- Try a different currency to isolate the issue
- The API might be temporarily down (rare)

### Rate Stays at 1
- Make sure your target currency is NOT USD
- If currency code is USD, the rate should remain 1
- Check that auto-convert toggle is enabled

## Alternative API Providers

If you want to use a different API provider, you can modify the `fetch_rate_from_api()` method in:
`app/modules/setting/controllers/setting.php`

**Popular alternatives:**
- [Fixer.io](https://fixer.io) - Requires API key
- [Open Exchange Rates](https://openexchangerates.org) - Free tier available
- [CurrencyLayer](https://currencylayer.com) - Free tier available
- [Exchangeratesapi.io](https://exchangeratesapi.io) - Free with limits

## Benefits

### Before This Feature
- ❌ Manual exchange rate entry required
- ❌ Risk of outdated rates
- ❌ Time-consuming to update regularly
- ❌ Potential for human error

### After This Feature
- ✅ Automatic real-time rates
- ✅ Always up-to-date
- ✅ Set and forget with cron
- ✅ No manual intervention needed

## Example Usage Scenario

**User:** SMM Panel owner in Pakistan
**Currency:** PKR (Pakistani Rupee)
**Use Case:** Syncing services from US-based providers

1. Enable auto currency converter
2. Set currency code to PKR
3. Click "Fetch Current Exchange Rate"
4. Rate is updated to current USD→PKR rate (e.g., 278.50)
5. Save settings
6. Set up daily cron job
7. Service prices are now automatically converted from USD to PKR

## Support

If you encounter any issues:
1. Check this documentation
2. Verify your settings in Settings > Currency
3. Test the API manually: `https://api.exchangerate-api.com/v4/latest/USD`
4. Check server error logs
5. Ensure curl is enabled in PHP

## Credits

Exchange Rate API Integration
Version: 1.0
Date: 2025
API Provider: exchangerate-api.com
