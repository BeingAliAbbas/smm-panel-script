# Quick Setup Guide - Exchange Rate API Integration

## For End Users (Panel Owners)

### Quick Start
1. Go to **Settings → Currency** (Settings/Currency tab)
2. Scroll to "Auto Currency Converter" section
3. Click **"Fetch Current Exchange Rate"** button
4. The rate field updates automatically (e.g., 1 USD = 278.50 PKR)
5. Click **Save** to apply

### Set Up Auto-Update (Recommended)
1. Click **"Show Cron URL for Auto-Update"** button
2. Copy the displayed URL
3. Add to your cPanel cron jobs or server crontab:
   ```
   0 0 * * * curl "PASTE_YOUR_URL_HERE"
   ```
4. Done! Exchange rates update automatically daily

## What Changed

### Before
```
Settings → Currency → Auto Currency Converter
- Had to manually enter: 1 USD = ??? PKR
- Needed to check exchange rates daily
- Time-consuming and error-prone
```

### After
```
Settings → Currency → Auto Currency Converter
- Click "Fetch Current Exchange Rate" button
- Automatic real-time rate from API
- OR set up cron for daily auto-updates
- No manual entry needed!
```

## Files Modified

1. **app/modules/setting/controllers/setting.php**
   - Added: `fetch_exchange_rate()` method (AJAX endpoint)
   - Added: `cron_update_exchange_rate()` method (cron endpoint)
   - Added: `fetch_rate_from_api()` private helper

2. **app/modules/setting/views/currency.php**
   - Added: "Fetch Current Exchange Rate" button
   - Added: "Show Cron URL" button
   - Added: Cron URL display box
   - Added: JavaScript for button functionality

3. **EXCHANGE_RATE_API_GUIDE.md** (NEW)
   - Comprehensive documentation
   - Troubleshooting guide
   - Cron setup examples

## Technical Details

### API Used
- Provider: exchangerate-api.com
- Free tier: No API key required
- Updates: Daily
- Coverage: 160+ currencies

### How It Works
```
Button Click → AJAX Request → Controller Method → cURL to API
→ Parse JSON → Return Rate → Update Input Field → User Saves
```

```
Cron Job → HTTP Request → Controller Method → cURL to API
→ Parse JSON → Update Database Directly → Return Success
```

### Security
- Cron endpoint protected by unique token
- Token stored in database
- CSRF protection on AJAX requests
- SSL verification on API calls

## Testing Checklist

- [x] PHP syntax validation passed
- [x] Code structure verified
- [x] API endpoint logic tested (simulated)
- [x] Security considerations implemented
- [x] Documentation created
- [ ] Manual testing in live environment (requires deployment)
- [ ] Cron job testing (requires deployment)

## What the User Needs to Do

1. **Deploy the changes** to their server
2. **Navigate to Settings → Currency**
3. **Test the "Fetch Exchange Rate" button**
4. **Set up cron job** using the displayed URL
5. **Verify** rates are updating automatically

## Expected Behavior

### When User Clicks "Fetch Exchange Rate"
1. Button text changes to "Fetching..."
2. API is called (takes 1-3 seconds)
3. If successful:
   - Rate field updates with current value
   - Success message shows: "Exchange rate fetched successfully (1 USD = X PKR)"
4. User clicks Save to persist

### When Cron Runs
1. HTTP request to cron URL
2. Validates token
3. Fetches current rate
4. Updates `new_currecry_rate` option automatically
5. Returns JSON response (can be logged)

## Troubleshooting

### "Invalid token" error
- Click "Show Cron URL" to regenerate
- Update cron job with new URL

### Button does nothing
- Check browser console for errors
- Verify you're logged in as admin
- Check server error logs

### Rate doesn't update
- Ensure currency code is NOT USD
- Verify internet connectivity
- Check if curl is enabled

## Support

For issues or questions:
1. Check EXCHANGE_RATE_API_GUIDE.md
2. Verify all files were updated
3. Check server/PHP error logs
4. Test API manually: https://api.exchangerate-api.com/v4/latest/USD

## Version
- Version: 1.0
- Date: November 2024
- Compatible with: SMM Panel Script (all versions)
