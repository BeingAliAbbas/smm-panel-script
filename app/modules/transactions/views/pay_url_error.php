<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=htmlspecialchars($error_title ?? 'Error')?> - SMM Panel</title>
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
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
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
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .message {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .details {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            text-align: left;
        }
        .detail-item {
            color: #991b1b;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .detail-item:last-child {
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            display: inline-block;
            min-width: 120px;
        }
        .footer {
            color: #9ca3af;
            font-size: 13px;
            margin-top: 24px;
            line-height: 1.6;
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
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>
        
        <h1><?=htmlspecialchars($error_title ?? 'Payment Error')?></h1>
        <p class="message"><?=htmlspecialchars($error_message ?? 'An error occurred while processing this payment link.')?></p>
        
        <?php if (isset($transaction_id) || isset($used_at)): ?>
        <div class="details">
            <?php if (isset($transaction_id)): ?>
            <div class="detail-item">
                <span class="detail-label">Transaction ID:</span>
                <?=htmlspecialchars($transaction_id)?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($used_at)): ?>
            <div class="detail-item">
                <span class="detail-label">Processed At:</span>
                <?=htmlspecialchars($used_at)?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>If you believe this is an error, please contact support.</p>
            <p>Do not share payment links with anyone.</p>
        </div>
        
        <div class="timestamp">
            <?=date('F j, Y \a\t g:i A')?>
        </div>

        <div class="redirect-note" aria-live="polite">
            Redirecting to transactions in <span id="countdown">5</span> second<span id="plural-s">s</span>…
        </div>
    </div>

    <script>
        (function() {
            var seconds = 5;
            var countdownEl = document.getElementById('countdown');
            var pluralEl = document.getElementById('plural-s');

            function updateText() {
                if (countdownEl) countdownEl.textContent = seconds;
                if (pluralEl) pluralEl.textContent = seconds === 1 ? '' : 's';
            }

            updateText();

            var timer = setInterval(function() {
                seconds -= 1;
                if (seconds <= 0) {
                    clearInterval(timer);
                    if (countdownEl) countdownEl.textContent = '0';
                    if (pluralEl) pluralEl.textContent = 's';
                    window.location.href = BASE_URL + '/transactions';
                } else {
                    updateText();
                }
            }, 1000);
        })();
    </script>
</body>
</html>
