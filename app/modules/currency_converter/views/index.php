<style>
.currency-converter-container {
    max-width: 800px;
    margin: 0 auto;
}

.converter-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
    margin-top: 30px;
}

.converter-title {
    font-size: 28px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
}

.converter-subtitle {
    color: #666;
    text-align: center;
    margin-bottom: 30px;
    font-size: 14px;
}

.currency-input-group {
    margin-bottom: 20px;
}

.currency-input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #555;
}

.currency-input-group .input-wrapper {
    position: relative;
    display: flex;
    gap: 10px;
}

.currency-input-group input[type="number"] {
    flex: 2;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.currency-input-group input[type="number"]:focus {
    outline: none;
    border-color: #467fcf;
}

.currency-input-group select {
    flex: 1;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
    background-color: #fff;
    cursor: pointer;
    transition: border-color 0.3s;
}

.currency-input-group select:focus {
    outline: none;
    border-color: #467fcf;
}

.swap-button {
    text-align: center;
    margin: 20px 0;
}

.swap-button button {
    background: #467fcf;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.swap-button button:hover {
    background: #3867d6;
    transform: rotate(180deg);
}

.convert-button {
    text-align: center;
    margin: 30px 0 20px 0;
}

.convert-button button {
    background: #467fcf;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 12px 40px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.convert-button button:hover {
    background: #3867d6;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

.result-section {
    margin-top: 30px;
    padding: 25px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #467fcf;
    display: none;
}

.result-section.show {
    display: block;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.result-amount {
    font-size: 32px;
    font-weight: 700;
    color: #467fcf;
    margin: 10px 0;
}

.result-details {
    color: #666;
    font-size: 14px;
    margin-top: 10px;
}

.exchange-rate-info {
    margin-top: 30px;
    padding: 15px;
    background: #fff3cd;
    border-radius: 5px;
    border-left: 4px solid #ffc107;
}

.exchange-rate-info p {
    margin: 0;
    color: #856404;
    font-size: 13px;
}

.popular-currencies {
    margin-top: 30px;
}

.popular-currencies h4 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
}

.currency-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.currency-chip {
    padding: 8px 15px;
    background: #e9ecef;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.3s;
    border: 1px solid transparent;
}

.currency-chip:hover {
    background: #467fcf;
    color: #fff;
    transform: translateY(-2px);
}

.loading {
    text-align: center;
    color: #467fcf;
    display: none;
}

.loading.show {
    display: block;
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .converter-card {
        padding: 20px;
    }
    
    .currency-input-group .input-wrapper {
        flex-direction: column;
    }
    
    .result-amount {
        font-size: 24px;
    }
}
</style>

<div class="currency-converter-container">
    <div class="converter-card">
        <h1 class="converter-title">
            <i class="fe fe-dollar-sign"></i> Currency Converter
        </h1>
        <p class="converter-subtitle">Convert between different world currencies with live exchange rates</p>

        <form id="converterForm">
            <div class="currency-input-group">
                <label for="fromAmount">Amount</label>
                <div class="input-wrapper">
                    <input type="number" id="fromAmount" name="amount" placeholder="Enter amount" value="1" min="0" step="0.01" required>
                    <select id="fromCurrency" name="from" required>
                        <option value="USD" selected>USD - US Dollar</option>
                        <option value="EUR">EUR - Euro</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="JPY">JPY - Japanese Yen</option>
                        <option value="AUD">AUD - Australian Dollar</option>
                        <option value="CAD">CAD - Canadian Dollar</option>
                        <option value="CHF">CHF - Swiss Franc</option>
                        <option value="CNY">CNY - Chinese Yuan</option>
                        <option value="INR">INR - Indian Rupee</option>
                        <option value="PKR">PKR - Pakistani Rupee</option>
                        <option value="SAR">SAR - Saudi Riyal</option>
                        <option value="AED">AED - UAE Dirham</option>
                        <option value="BRL">BRL - Brazilian Real</option>
                        <option value="MXN">MXN - Mexican Peso</option>
                        <option value="SGD">SGD - Singapore Dollar</option>
                        <option value="NZD">NZD - New Zealand Dollar</option>
                        <option value="ZAR">ZAR - South African Rand</option>
                        <option value="KRW">KRW - South Korean Won</option>
                        <option value="TRY">TRY - Turkish Lira</option>
                        <option value="RUB">RUB - Russian Ruble</option>
                    </select>
                </div>
            </div>

            <div class="swap-button">
                <button type="button" id="swapButton" title="Swap currencies">
                    <i class="fe fe-repeat"></i>
                </button>
            </div>

            <div class="currency-input-group">
                <label for="toCurrency">To</label>
                <div class="input-wrapper">
                    <input type="number" id="toAmount" placeholder="Converted amount" readonly>
                    <select id="toCurrency" name="to" required>
                        <option value="EUR">EUR - Euro</option>
                        <option value="USD">USD - US Dollar</option>
                        <option value="GBP">GBP - British Pound</option>
                        <option value="JPY">JPY - Japanese Yen</option>
                        <option value="AUD">AUD - Australian Dollar</option>
                        <option value="CAD">CAD - Canadian Dollar</option>
                        <option value="CHF">CHF - Swiss Franc</option>
                        <option value="CNY">CNY - Chinese Yuan</option>
                        <option value="INR">INR - Indian Rupee</option>
                        <option value="PKR">PKR - Pakistani Rupee</option>
                        <option value="SAR">SAR - Saudi Riyal</option>
                        <option value="AED">AED - UAE Dirham</option>
                        <option value="BRL">BRL - Brazilian Real</option>
                        <option value="MXN">MXN - Mexican Peso</option>
                        <option value="SGD">SGD - Singapore Dollar</option>
                        <option value="NZD">NZD - New Zealand Dollar</option>
                        <option value="ZAR">ZAR - South African Rand</option>
                        <option value="KRW">KRW - South Korean Won</option>
                        <option value="TRY">TRY - Turkish Lira</option>
                        <option value="RUB">RUB - Russian Ruble</option>
                    </select>
                </div>
            </div>

            <div class="convert-button">
                <button type="submit" id="convertBtn">
                    <i class="fe fe-arrow-right"></i> Convert
                </button>
            </div>

            <div class="loading" id="loading">
                <i class="fe fe-loader spin"></i> Converting...
            </div>
        </form>

        <div class="result-section" id="resultSection">
            <h3>Conversion Result</h3>
            <div class="result-amount" id="resultAmount">0.00</div>
            <div class="result-details" id="resultDetails">
                Exchange Rate: <strong id="exchangeRate">1.00</strong>
            </div>
        </div>

        <div class="exchange-rate-info">
            <p><i class="fe fe-info"></i> <strong>Note:</strong> Exchange rates are updated regularly. Actual rates may vary slightly at the time of transaction.</p>
        </div>

        <div class="popular-currencies">
            <h4>Popular Conversions</h4>
            <div class="currency-chips">
                <span class="currency-chip" data-from="USD" data-to="EUR">USD to EUR</span>
                <span class="currency-chip" data-from="USD" data-to="GBP">USD to GBP</span>
                <span class="currency-chip" data-from="USD" data-to="INR">USD to INR</span>
                <span class="currency-chip" data-from="EUR" data-to="USD">EUR to USD</span>
                <span class="currency-chip" data-from="GBP" data-to="USD">GBP to USD</span>
                <span class="currency-chip" data-from="USD" data-to="PKR">USD to PKR</span>
                <span class="currency-chip" data-from="EUR" data-to="GBP">EUR to GBP</span>
                <span class="currency-chip" data-from="USD" data-to="AED">USD to AED</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('converterForm');
    const swapButton = document.getElementById('swapButton');
    const fromAmount = document.getElementById('fromAmount');
    const toAmount = document.getElementById('toAmount');
    const fromCurrency = document.getElementById('fromCurrency');
    const toCurrency = document.getElementById('toCurrency');
    const resultSection = document.getElementById('resultSection');
    const resultAmount = document.getElementById('resultAmount');
    const resultDetails = document.getElementById('resultDetails');
    const exchangeRate = document.getElementById('exchangeRate');
    const loading = document.getElementById('loading');
    const currencyChips = document.querySelectorAll('.currency-chip');

    // Swap currencies
    swapButton.addEventListener('click', function() {
        const tempCurrency = fromCurrency.value;
        fromCurrency.value = toCurrency.value;
        toCurrency.value = tempCurrency;
        
        if (fromAmount.value) {
            convertCurrency();
        }
    });

    // Auto-convert on amount or currency change
    fromAmount.addEventListener('input', function() {
        if (this.value) {
            convertCurrency();
        }
    });

    fromCurrency.addEventListener('change', function() {
        if (fromAmount.value) {
            convertCurrency();
        }
    });

    toCurrency.addEventListener('change', function() {
        if (fromAmount.value) {
            convertCurrency();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        convertCurrency();
    });

    // Popular currency chips
    currencyChips.forEach(chip => {
        chip.addEventListener('click', function() {
            fromCurrency.value = this.dataset.from;
            toCurrency.value = this.dataset.to;
            if (fromAmount.value) {
                convertCurrency();
            }
        });
    });

    // Convert currency function
    function convertCurrency() {
        const amount = fromAmount.value;
        const from = fromCurrency.value;
        const to = toCurrency.value;

        if (!amount || amount <= 0) {
            return;
        }

        loading.classList.add('show');
        resultSection.classList.remove('show');

        const formData = new FormData();
        formData.append('amount', amount);
        formData.append('from', from);
        formData.append('to', to);

        fetch('<?=cn("currency_converter/convert")?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loading.classList.remove('show');
            
            if (data.status === 'success') {
                toAmount.value = data.result.toFixed(2);
                resultAmount.textContent = data.to + ' ' + data.result.toFixed(2);
                exchangeRate.textContent = '1 ' + data.from + ' = ' + data.rate.toFixed(4) + ' ' + data.to;
                resultSection.classList.add('show');
            } else {
                alert(data.message || 'Conversion failed. Please try again.');
            }
        })
        .catch(error => {
            loading.classList.remove('show');
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

    // Initial conversion if amount is already set
    if (fromAmount.value) {
        convertCurrency();
    }
});
</script>
