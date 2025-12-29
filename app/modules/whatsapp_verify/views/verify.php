<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Verification - <?=htmlspecialchars(get_option('website_name', 'SMM Panel'), ENT_QUOTES, 'UTF-8')?></title>
    <link rel="stylesheet" href="<?=BASE?>assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=BASE?>assets/css/auth.css">
    <style>
        .verify-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .verify-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        .verify-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }
        .phone-input-group {
            display: flex;
            gap: 10px;
        }
        .country-select {
            flex: 0 0 140px;
        }
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        .otp-input:focus {
            border-color: #667eea;
            outline: none;
        }
        .alert {
            margin-top: 15px;
        }
        .countdown {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="verify-card">
            <div class="verify-icon">
                <span>📱</span>
            </div>
            
            <h2 class="text-center mb-3">WhatsApp Verification Required</h2>
            <p class="text-center text-muted mb-4">
                Please verify your WhatsApp number to complete your registration and access your dashboard.
            </p>

            <div id="alert-container"></div>

            <!-- Phone Number Input Form -->
            <div id="phone-input-section" <?=($has_pending_otp ? 'style="display:none;"' : '')?>>
                <form id="phone-form">
                    <div class="mb-3">
                        <label class="form-label">Select Country</label>
                        <select class="form-select country-select" id="country-code" name="country_code" required>
                            <option value="">Choose...</option>
                            <?php foreach($countries as $country): ?>
                                <option value="<?=$country['code']?>" <?=($country['code'] === '+92' ? 'selected' : '')?>>
                                    <?=$country['flag']?> <?=$country['name']?> (<?=$country['code']?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">WhatsApp Number</label>
                        <div class="phone-input-group">
                            <input type="text" class="form-control" id="country-display" readonly value="+92" style="flex: 0 0 80px;">
                            <input type="text" class="form-control" id="phone-number" name="phone_number" 
                                   placeholder="3001234567" required pattern="[0-9]+" maxlength="15">
                        </div>
                        <small class="text-muted">Enter your number without country code</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="send-otp-btn">
                        <span id="send-otp-text">Send OTP</span>
                        <span id="send-otp-loader" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-2"></span>Sending...
                        </span>
                    </button>
                </form>
            </div>

            <!-- OTP Verification Form -->
            <div id="otp-input-section" <?=(!$has_pending_otp ? 'style="display:none;"' : '')?>>
                <p class="text-center mb-3">
                    Enter the 6-digit code sent to<br>
                    <strong id="display-phone"><?=htmlspecialchars($phone_number ?? '')?></strong>
                    <button type="button" class="btn btn-link btn-sm p-0" id="change-number-btn">Change</button>
                </p>

                <form id="otp-form">
                    <div class="otp-inputs">
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                        <input type="text" class="form-control otp-input" maxlength="1" pattern="[0-9]" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="verify-otp-btn">
                        <span id="verify-otp-text">Verify OTP</span>
                        <span id="verify-otp-loader" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-2"></span>Verifying...
                        </span>
                    </button>
                </form>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-link" id="resend-otp-btn">
                        Resend OTP
                    </button>
                    <div class="countdown" id="resend-countdown" style="display:none;">
                        Resend available in <strong id="countdown-timer">60</strong> seconds
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?=cn('auth/logout')?>" class="text-muted small">Logout</a>
            </div>
        </div>
    </div>

    <script src="<?=BASE?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?=BASE?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let countdownInterval;

            // Update country code display when country changes
            $('#country-code').change(function() {
                $('#country-display').val($(this).val());
            });

            // Phone form submission
            $('#phone-form').submit(function(e) {
                e.preventDefault();
                
                $('#send-otp-text').hide();
                $('#send-otp-loader').show();
                $('#send-otp-btn').prop('disabled', true);

                $.ajax({
                    url: '<?=cn("whatsapp_verify/ajax_send_otp")?>',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.status, response.message);
                        
                        if (response.status === 'success') {
                            // Show OTP input section
                            $('#phone-input-section').hide();
                            $('#otp-input-section').show();
                            $('#display-phone').text($('#country-display').val() + $('#phone-number').val());
                            startCountdown(60);
                        }
                    },
                    error: function() {
                        showAlert('error', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $('#send-otp-text').show();
                        $('#send-otp-loader').hide();
                        $('#send-otp-btn').prop('disabled', false);
                    }
                });
            });

            // OTP input auto-focus
            $('.otp-input').on('input', function() {
                if ($(this).val().length === 1) {
                    $(this).next('.otp-input').focus();
                }
            });

            $('.otp-input').on('keydown', function(e) {
                if (e.key === 'Backspace' && $(this).val() === '') {
                    $(this).prev('.otp-input').focus();
                }
            });

            // OTP form submission
            $('#otp-form').submit(function(e) {
                e.preventDefault();
                
                let otp = '';
                $('.otp-input').each(function() {
                    otp += $(this).val();
                });

                if (otp.length !== 6) {
                    showAlert('error', 'Please enter the complete 6-digit OTP.');
                    return;
                }

                $('#verify-otp-text').hide();
                $('#verify-otp-loader').show();
                $('#verify-otp-btn').prop('disabled', true);

                $.ajax({
                    url: '<?=cn("whatsapp_verify/ajax_verify_otp")?>',
                    method: 'POST',
                    data: { otp: otp },
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.status, response.message);
                        
                        if (response.status === 'success' && response.redirect) {
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1500);
                        }
                    },
                    error: function() {
                        showAlert('error', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $('#verify-otp-text').show();
                        $('#verify-otp-loader').hide();
                        $('#verify-otp-btn').prop('disabled', false);
                    }
                });
            });

            // Resend OTP
            $('#resend-otp-btn').click(function() {
                $(this).prop('disabled', true);
                
                $.ajax({
                    url: '<?=cn("whatsapp_verify/ajax_resend_otp")?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.status, response.message);
                        
                        if (response.status === 'success') {
                            startCountdown(60);
                        }
                    },
                    error: function() {
                        showAlert('error', 'An error occurred. Please try again.');
                    },
                    complete: function() {
                        $('#resend-otp-btn').prop('disabled', false);
                    }
                });
            });

            // Change number
            $('#change-number-btn').click(function() {
                $.ajax({
                    url: '<?=cn("whatsapp_verify/ajax_change_number")?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        $('#otp-input-section').hide();
                        $('#phone-input-section').show();
                        $('.otp-input').val('');
                        showAlert('info', 'Please enter a new phone number.');
                    }
                });
            });

            // Countdown timer
            function startCountdown(seconds) {
                clearInterval(countdownInterval);
                $('#resend-otp-btn').hide();
                $('#resend-countdown').show();
                
                let remaining = seconds;
                $('#countdown-timer').text(remaining);
                
                countdownInterval = setInterval(function() {
                    remaining--;
                    $('#countdown-timer').text(remaining);
                    
                    if (remaining <= 0) {
                        clearInterval(countdownInterval);
                        $('#resend-countdown').hide();
                        $('#resend-otp-btn').show();
                    }
                }, 1000);
            }

            // Show alert
            function showAlert(type, message) {
                let alertClass = 'alert-info';
                if (type === 'error') alertClass = 'alert-danger';
                else if (type === 'success') alertClass = 'alert-success';
                else if (type === 'warning') alertClass = 'alert-warning';

                let alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                    '</div>');

                $('#alert-container').html(alert);
                
                setTimeout(function() {
                    alert.fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }

            // Start countdown if OTP section is visible
            <?php if ($has_pending_otp): ?>
            startCountdown(60);
            <?php endif; ?>
        });
    </script>
</body>
</html>
