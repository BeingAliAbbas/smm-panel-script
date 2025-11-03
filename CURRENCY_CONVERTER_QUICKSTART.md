# Quick Start Guide - Currency Converter

## For End Users

### Accessing the Currency Converter
1. Log into your SMM panel account
2. Look for "Currency Converter" in the sidebar menu (with 💵 icon)
3. Click to open the converter page

### How to Convert Currency

#### Method 1: Manual Entry
1. Enter the amount you want to convert (e.g., 100)
2. Select the source currency from the first dropdown (e.g., USD)
3. Select the target currency from the second dropdown (e.g., EUR)
4. The conversion happens automatically as you type!

#### Method 2: Quick Conversion
1. Click any of the "Popular Conversions" chips at the bottom
2. For example, click "USD to EUR" 
3. The currencies are automatically selected
4. Enter your amount to see the conversion

#### Method 3: Swap Currencies
1. After selecting currencies, click the circular swap button (⟲) in the center
2. This will reverse your source and destination currencies
3. Perfect for checking reverse conversions!

### Understanding the Results
- **Top Number**: The converted amount in the target currency
- **Exchange Rate**: Shows the current rate (e.g., "1 USD = 0.92 EUR")
- **Note**: Exchange rates update regularly and may vary slightly

## For Developers/Admins

### Database Setup (Optional but Recommended)
```sql
-- Run this in your MySQL database
CREATE TABLE IF NOT EXISTS `currency_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_currency` varchar(3) NOT NULL,
  `rates` text NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_currency` (`base_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Or simply import the provided SQL file:
```bash
mysql -u your_username -p your_database < database/currency_converter.sql
```

### File Structure
```
app/modules/currency_converter/
├── controllers/
│   └── currency_converter.php    # Main controller
├── models/
│   └── currency_converter_model.php    # Business logic & API integration
└── views/
    └── index.php    # User interface
```

### API Integration
- **Provider**: exchangerate-api.com (free tier)
- **Cache Duration**: 1 hour
- **Fallback**: Static rates if API unavailable
- **No API Key Required**: Uses free public endpoint

### Customization Examples

#### Change Theme Color
Edit `app/modules/currency_converter/views/index.php`:
```css
/* Change from blue (#467fcf) to your color */
.convert-button button {
    background: #your-color;
}
```

#### Add More Currencies
1. Add to view dropdowns:
```html
<option value="NEW">NEW - New Currency</option>
```

2. Add to model fallback rates:
```php
'NEW' => 1.50,  // Your conversion rate from USD
```

#### Change Cache Duration
Edit `app/modules/currency_converter/models/currency_converter_model.php`:
```php
// Change from 1 hour to your desired duration
$this->db->where('updated_at >', date('Y-m-d H:i:s', strtotime('-2 hours')));
```

### Troubleshooting

**Problem**: "Failed to fetch exchange rates"
- **Solution**: Check internet connection or use fallback rates

**Problem**: Converter not appearing in menu
- **Solution**: Clear cache, verify header.php was updated

**Problem**: Conversion not working
- **Solution**: Check browser console for JavaScript errors

### Support URLs
- Direct access: `https://yourdomain.com/currency_converter`
- Or use: `cn('currency_converter')` in PHP code

## Supported Currencies

| Americas | Europe | Asia | Middle East | Others |
|----------|--------|------|-------------|---------|
| USD 🇺🇸 | EUR 🇪🇺 | JPY 🇯🇵 | SAR 🇸🇦 | AUD 🇦🇺 |
| CAD 🇨🇦 | GBP 🇬🇧 | CNY 🇨🇳 | AED 🇦🇪 | NZD 🇳🇿 |
| BRL 🇧🇷 | CHF 🇨🇭 | INR 🇮🇳 | | ZAR 🇿🇦 |
| MXN 🇲🇽 | TRY 🇹🇷 | PKR 🇵🇰 | | |
| | RUB 🇷🇺 | SGD 🇸🇬 | | |
| | | KRW 🇰🇷 | | |

## Tips & Tricks

1. **Quick Swaps**: Use the swap button to quickly check reverse conversions
2. **Auto-Convert**: No need to click convert - it happens as you type!
3. **Bookmarkable**: Save the URL for quick access
4. **Mobile Ready**: Works perfectly on phones and tablets
5. **Real-time**: Rates update automatically every hour

## Security Note
All conversions are processed server-side with proper validation and CSRF protection.

---

**Need Help?** See the full documentation in `CURRENCY_CONVERTER_INTEGRATION.md`
