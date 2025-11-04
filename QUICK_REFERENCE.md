# Multi-Currency Support - Quick Reference

## 🚀 Quick Start

### For Administrators

1. **Initial Setup** (One-time)
   ```sql
   -- Import the database migration
   mysql -u username -p database_name < database/multi-currency.sql
   ```

2. **Access Currency Management**
   - Login as Admin
   - Go to **Settings** → **Currencies**
   - You'll see the currency management interface

3. **Configure Currencies**
   - Review default currencies (USD, EUR, GBP, INR, PKR, AUD, CAD)
   - Update exchange rates to current values
   - Set your base currency as "Default"
   - Enable/disable currencies as needed

### For End Users

1. **Switch Currency**
   - Open the sidebar navigation
   - Look for "Currency" dropdown below your balance
   - Select your preferred currency
   - Page will reload with all amounts in new currency

2. **View Converted Amounts**
   - Balance in sidebar
   - Statistics dashboard
   - Transaction history
   - Order history
   - All amounts display in your selected currency

## 📊 Default Currencies

| Code | Name | Symbol | Default Rate |
|------|------|--------|--------------|
| PKR | Pakistani Rupee | Rs | 1.00 (Base) |
| USD | US Dollar | $ | 0.00359066 |
| EUR | Euro | € | 0.00330341 |
| GBP | British Pound | £ | 0.00283632 |
| INR | Indian Rupee | ₹ | 0.29851434 |
| AUD | Australian Dollar | A$ | 0.00545740 |
| CAD | Canadian Dollar | C$ | 0.00488309 |

*Note: These are example rates relative to PKR. Update them to current market rates.*

*Note: These are example rates. Update them to current market rates.*

## 🔧 Admin Operations

### Update Exchange Rate
1. Enter new rate in the field
2. Click "Update" button
3. Changes apply immediately

### Add New Currency
1. Fill in the form at bottom:
   - Code (e.g., JPY)
   - Name (e.g., Japanese Yen)
   - Symbol (e.g., ¥)
   - Exchange Rate (e.g., 149.50)
2. Click "Add"

### Set Default Currency
1. Click "Set as Default" next to desired currency
2. All other currencies become relative to this one
3. Update exchange rates accordingly

### Enable/Disable Currency
1. Toggle the switch in "Status" column
2. Disabled currencies don't appear in user dropdown

## 💡 Tips

### Best Practices
- ✅ Update exchange rates weekly or daily
- ✅ Set USD or your local currency as default
- ✅ Test currency switching before going live
- ✅ Keep 3-5 most relevant currencies active
- ✅ Document your exchange rate sources

### Common Issues

**Currency switcher not showing?**
- Ensure user is logged in
- Check that at least 2 currencies are active
- Clear browser cache

**Wrong conversion amounts?**
- Verify exchange rates are correct
- Check that default currency is set
- Ensure rates are relative to default currency

**Changes not saving?**
- Check database permissions
- Verify CSRF token is valid
- Check browser console for errors

## 🔄 Currency Conversion

### How It Works
```
Original Amount: Rs 100 PKR (default)
Target Currency: USD (rate: 0.00359066)
Converted: 100 × 0.00359066 = $0.36 USD
```

### Formula
```
converted_amount = original_amount × (target_rate ÷ default_rate)
```

### Example with Different Default
```
If PKR is default (rate: 1.00) and USD rate: 0.00359066
Rs 1000 to USD = 1000 × (0.00359066 ÷ 1.00) = $3.59
```

## 📝 API Integration (Optional)

### Get Currency from Session
```php
$currency = get_current_currency();
echo $currency->code;   // USD
echo $currency->symbol; // $
```

### Convert Amount
```php
$amount = 100; // in default currency
$converted = convert_currency($amount);
echo format_currency($amount, true); // $100 or converted
```

### Get All Currencies
```php
$currencies = get_active_currencies();
foreach ($currencies as $currency) {
    echo $currency->name;
}
```

## 🎯 Key Files Reference

```
database/multi-currency.sql                    - Database migration
app/modules/currencies/                        - Currency module
app/helpers/currency_helper.php               - Helper functions
app/modules/setting/views/currencies.php      - Admin UI
app/modules/blocks/views/header.php           - Sidebar switcher
MULTI_CURRENCY_GUIDE.md                       - Full documentation
validate-multicurrency.sh                     - Validation script
```

## ⚡ Quick Commands

```bash
# Validate installation
./validate-multicurrency.sh

# Check PHP syntax
php -l app/modules/currencies/controllers/currencies.php

# Import database
mysql -u user -p database < database/multi-currency.sql

# Check currencies in database
mysql -u user -p -e "SELECT * FROM currencies" database
```

## 🆘 Support Checklist

Before asking for help:
- [ ] Database migration completed
- [ ] No PHP syntax errors
- [ ] Browser cache cleared
- [ ] JavaScript console checked
- [ ] Correct user permissions
- [ ] CSRF tokens valid

---

**Version:** 1.0  
**Last Updated:** 2025  
**Compatibility:** SMM Panel Script v2.x+
