# Currency Converter Integration Guide

## Overview
This guide provides step-by-step instructions for the currency converter feature integrated into the SMM Panel Script. The currency converter allows users to convert between 20+ world currencies with live exchange rates.

## Features
- **Live Exchange Rates**: Fetches real-time exchange rates from exchangerate-api.com
- **20+ Currencies**: Support for major world currencies including USD, EUR, GBP, JPY, INR, PKR, AED, and more
- **Real-time Conversion**: Converts currency as you type
- **Rate Caching**: Caches exchange rates for 1 hour to reduce API calls
- **Fallback Rates**: Uses static fallback rates if API is unavailable
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Popular Quick Converts**: Quick-select buttons for popular currency pairs

## Files Created

### 1. Controller
**Location**: `/app/modules/currency_converter/controllers/currency_converter.php`

This controller handles:
- Rendering the currency converter page
- Processing conversion requests via AJAX
- Fetching exchange rates

**Methods**:
- `index()`: Displays the converter page
- `convert()`: Handles currency conversion requests
- `get_rates()`: Returns exchange rates for a base currency

### 2. Model
**Location**: `/app/modules/currency_converter/models/currency_converter_model.php`

This model handles:
- Currency conversion logic
- Fetching live exchange rates from API
- Caching rates in database
- Providing fallback static rates

**Methods**:
- `convert_currency($from, $to, $amount)`: Converts amount between currencies
- `get_exchange_rates($base)`: Gets exchange rates for a base currency
- `get_cached_rates($base)`: Retrieves cached rates from database
- `cache_rates($base, $rates)`: Stores rates in database cache
- `fetch_rates_from_api($base)`: Fetches live rates from API
- `get_fallback_rates($base)`: Returns static fallback rates

### 3. View
**Location**: `/app/modules/currency_converter/views/index.php`

This view provides:
- Modern, clean user interface
- Input fields for amount and currency selection
- Swap button to reverse currencies
- Real-time conversion display
- Popular currency quick-select chips
- Exchange rate information

### 4. Database Schema
**Location**: `/database/currency_converter.sql`

Creates the `currency_rates` table for caching exchange rates:
- `id`: Primary key
- `base_currency`: Base currency code (e.g., USD)
- `rates`: JSON-encoded exchange rates
- `updated_at`: Timestamp of last update

### 5. Configuration
**Modified**: `/app/config/constants.php`
- Added `CURRENCY_RATES` constant for table name

**Modified**: `/app/modules/blocks/views/header.php`
- Added navigation link to currency converter in sidebar menu

## Installation Steps

### Step 1: Database Setup
Run the SQL file to create the currency rates table:

```sql
-- Execute this in your database
source database/currency_converter.sql;
```

Or manually run:
```sql
CREATE TABLE IF NOT EXISTS `currency_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_currency` varchar(3) NOT NULL,
  `rates` text NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_currency` (`base_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Note**: The currency converter will work without this table using fallback rates, but creating it enables rate caching and reduces API calls.

### Step 2: Verify File Permissions
Ensure the web server has read access to all currency converter files:
```bash
chmod 644 app/modules/currency_converter/controllers/currency_converter.php
chmod 644 app/modules/currency_converter/models/currency_converter_model.php
chmod 644 app/modules/currency_converter/views/index.php
```

### Step 3: Access the Converter
The currency converter is now accessible at:
```
https://yourdomain.com/currency_converter
```

Or use the CodeIgniter URL helper:
```php
cn('currency_converter')
```

### Step 4: Navigation
A link to the currency converter has been added to the sidebar navigation menu. Users can access it by clicking "Currency Converter" in the main menu.

## Supported Currencies

The currency converter supports the following currencies:

| Code | Currency Name |
|------|---------------|
| USD  | US Dollar |
| EUR  | Euro |
| GBP  | British Pound |
| JPY  | Japanese Yen |
| AUD  | Australian Dollar |
| CAD  | Canadian Dollar |
| CHF  | Swiss Franc |
| CNY  | Chinese Yuan |
| INR  | Indian Rupee |
| PKR  | Pakistani Rupee |
| SAR  | Saudi Riyal |
| AED  | UAE Dirham |
| BRL  | Brazilian Real |
| MXN  | Mexican Peso |
| SGD  | Singapore Dollar |
| NZD  | New Zealand Dollar |
| ZAR  | South African Rand |
| KRW  | South Korean Won |
| TRY  | Turkish Lira |
| RUB  | Russian Ruble |

## API Integration

### Exchange Rate API
The converter uses the free tier of exchangerate-api.com:
- **Endpoint**: `https://api.exchangerate-api.com/v4/latest/{base}`
- **Rate Limit**: No strict limit on free tier
- **Caching**: Rates are cached for 1 hour to minimize API calls

### Fallback System
If the API is unavailable, the system automatically uses static fallback rates stored in the model. These rates are approximate values and should be updated periodically.

## Customization

### Adding More Currencies
To add more currencies, edit both:
1. The view (`views/index.php`) - Add option to both `fromCurrency` and `toCurrency` select elements
2. The model (`models/currency_converter_model.php`) - Add fallback rate in `get_fallback_rates()` method

### Styling
All styles are contained in the view file (`views/index.php`). You can customize:
- Colors (currently using theme color `#467fcf`)
- Card layout and spacing
- Button styles
- Responsive breakpoints

### Changing API Provider
To use a different exchange rate API:
1. Modify the `fetch_rates_from_api()` method in the model
2. Update the API URL and response parsing logic
3. Ensure the response format matches expected structure

## Usage Examples

### Basic Conversion
1. Enter amount (e.g., 100)
2. Select source currency (e.g., USD)
3. Select target currency (e.g., EUR)
4. Click "Convert" or wait for auto-conversion
5. View result with exchange rate

### Quick Conversion
Click any of the popular currency pair chips (e.g., "USD to EUR") for instant conversion.

### Swap Currencies
Click the swap button (⟲) to reverse the source and target currencies.

## Troubleshooting

### Issue: "Failed to fetch exchange rates"
**Solution**: 
- Check internet connectivity
- Verify API endpoint is accessible
- System will automatically use fallback rates

### Issue: Navigation link not appearing
**Solution**:
- Clear browser cache
- Check if header.php was modified correctly
- Verify user has appropriate permissions

### Issue: Conversion not working
**Solution**:
- Check browser console for JavaScript errors
- Verify AJAX endpoint is accessible
- Ensure CodeIgniter routes are configured correctly

### Issue: Database error
**Solution**:
- Verify currency_rates table exists
- Check database permissions
- The converter will work without the table using fallback rates

## Security Considerations

1. **CSRF Protection**: All POST requests include CSRF tokens (if enabled in your installation)
2. **Input Validation**: Amount and currency codes are validated before processing
3. **SQL Injection Prevention**: Uses CodeIgniter's query builder for safe database operations
4. **XSS Protection**: User input is sanitized before display

## Performance

### Optimization Tips
1. **Rate Caching**: Database caching reduces API calls by storing rates for 1 hour
2. **Static Fallback**: Fallback rates eliminate dependency on external API
3. **AJAX Requests**: Only conversion data is transferred, not full page reloads
4. **Minimal Dependencies**: Uses native JavaScript, no heavy libraries required

### Cache Management
To clear the currency rates cache:
```sql
DELETE FROM currency_rates WHERE updated_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);
```

Or manually:
```sql
TRUNCATE TABLE currency_rates;
```

## Future Enhancements

Potential improvements for future versions:
1. Historical exchange rate charts
2. Multiple currency conversions at once
3. Favorite currency pairs
4. Email/SMS rate alerts
5. Currency conversion calculator widget
6. Admin panel to manage API settings
7. Support for cryptocurrency conversions

## Support

For issues or questions:
1. Check this documentation first
2. Review the troubleshooting section
3. Check browser console for errors
4. Verify all files are in place and have correct permissions

## Credits

- **Exchange Rate API**: exchangerate-api.com
- **Icons**: Feather Icons (already included in SMM Panel)
- **Framework**: CodeIgniter 3.x

## License

This currency converter module follows the same license as the SMM Panel Script.

---

**Last Updated**: November 2025
**Version**: 1.0.0
