<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Approved - SMM Panel</title>
    <meta http-equiv="refresh" content="5;url=/transactions">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .icon svg {
            width: 48px;
            height: 48px;
            stroke: white;
            stroke-width: 3;
            fill: none;
        }
        h1 {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 32px;
        }
        .details {
            background: #f9fafb;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        .detail-value {
            color: #1f2937;
            font-size: 14px;
            font-weight: 600;
            text-align: right;
        }
        .highlight {
            color: #10b981;
            font-size: 18px;
        }
        .footer {
            color: #9ca3af;
            font-size: 13px;
            margin-top: 24px;
        }
        .timestamp {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 16px;
        }
        .redirect-note {
            margin-top: 12px;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        
        <h1>Payment Approved Successfully!</h1>
        <p class="subtitle">The transaction has been processed and the user's balance has been credited.</p>
        
        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Transaction ID</span>
                <span class="detail-value"><?=htmlspecialchars($transaction_id ?? 'N/A')?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">User</span>
                <span class="detail-value"><?=htmlspecialchars($user_email ?? 'N/A')?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method</span>
                <span class="detail-value"><?=htmlspecialchars($payment_method ?? 'N/A')?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount</span>
                <span class="detail-value"><?=htmlspecialchars(number_format($amount ?? 0, 2))?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transaction Fee</span>
                <span class="detail-value"><?=htmlspecialchars(number_format($fee ?? 0, 2))?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Credited</span>
                <span class="detail-value highlight"><?=htmlspecialchars(number_format($net_amount ?? 0, 2))?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Previous Balance</span>
                <span class="detail-value"><?=htmlspecialchars(number_format($old_balance ?? 0, 2))?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">New Balance</span>
                <span class="detail-value highlight"><?=htmlspecialchars(number_format($new_balance ?? 0, 2))?></span>
            </div>
        </div>
        
        <div class="footer">
            <p>✓ Balance has been updated</p>
            <p>✓ User has been notified via WhatsApp</p>
            <p>✓ This payment link has been deactivated</p>
        </div>
        
        <div class="timestamp">
            Processed on <?=date('F j, Y \a\t g:i A')?>
        </div>

        <div class="redirect-note" aria-live="polite">
            Redirecting to transactions in <span id="countdown">5</span> second<span id="plural-s">s</span>…
        </div>
    </div>

    <script>
        (function() {
            // Countdown and redirect to /transactions after 5 seconds.
            var seconds = 5;
            var countdownEl = document.getElementById('countdown');
            var pluralEl = document.getElementById('plural-s');

            function updateText() {
                if (countdownEl) countdownEl.textContent = seconds;
                if (pluralEl) pluralEl.textContent = seconds === 1 ? '' : 's';
            }

            updateText(); // initial set

            var timer = setInterval(function() {
                seconds -= 1;
                if (seconds <= 0) {
                    clearInterval(timer);
                    // Final update (0)
                    if (countdownEl) countdownEl.textContent = '0';
                    if (pluralEl) pluralEl.textContent = 's';
                    // Redirect
                    window.location.href = BASE_URL + '/transactions';
                } else {
                    updateText();
                }
            }, 1000);
        })();
    </script>
</body>
</html>
