<?=Modules::run(get_theme()."/header")?>  

<section class="banner">
  <div class="container">
    <div class="row justify-content-center">
      
      <!-- WhatsApp Setup Column -->
      <div class="col-lg-6 col-md-8 col-sm-10 col-12" data-aos="fade-down" data-aos-easing="ease-in" data-aos-delay="300">

        <!-- Welcome Message -->
        <div class="mb-4 text-center">
          <h2 class="mb-3">
            <i class="fab fa-whatsapp text-success"></i> WhatsApp Setup
          </h2>
          <p class="text-muted">
            To complete your registration, please provide your WhatsApp number
          </p>
        </div>

        <!-- Setup Form -->
        <div class="form-login">
          <form id="whatsappSetupForm" class="actionForm" action="<?=cn("auth/ajax_whatsapp_setup")?>" data-redirect="<?=cn('statistics')?>" method="POST">
            
            <!-- WhatsApp Number Input -->
            <div class="mb-3">
              <label for="country-code" class="form-label fw-semibold">
                Country Code
              </label>
              <select class="form-control form-control-lg" id="country-code" name="country_code" required>
                <option value="" disabled selected>Select Country</option>
                <option value="+93">Afghanistan (+93)</option>
                <option value="+355">Albania (+355)</option>
                <option value="+213">Algeria (+213)</option>
                <option value="+1">United States (+1)</option>
                <option value="+44">United Kingdom (+44)</option>
                <option value="+91">India (+91)</option>
                <option value="+92" selected>Pakistan (+92)</option>
                <option value="+880">Bangladesh (+880)</option>
                <option value="+86">China (+86)</option>
                <option value="+81">Japan (+81)</option>
                <option value="+82">South Korea (+82)</option>
                <option value="+49">Germany (+49)</option>
                <option value="+33">France (+33)</option>
                <option value="+39">Italy (+39)</option>
                <option value="+7">Russia (+7)</option>
                <option value="+55">Brazil (+55)</option>
                <option value="+52">Mexico (+52)</option>
                <option value="+61">Australia (+61)</option>
                <option value="+27">South Africa (+27)</option>
                <option value="+234">Nigeria (+234)</option>
                <option value="+20">Egypt (+20)</option>
                <option value="+966">Saudi Arabia (+966)</option>
                <option value="+971">UAE (+971)</option>
                <option value="+90">Turkey (+90)</option>
                <option value="+62">Indonesia (+62)</option>
                <option value="+60">Malaysia (+60)</option>
                <option value="+63">Philippines (+63)</option>
                <option value="+66">Thailand (+66)</option>
                <option value="+84">Vietnam (+84)</option>
                <option value="+65">Singapore (+65)</option>
                <option value="+94">Sri Lanka (+94)</option>
                <option value="+977">Nepal (+977)</option>
                <option value="+98">Iran (+98)</option>
                <option value="+964">Iraq (+964)</option>
                <option value="+962">Jordan (+962)</option>
                <option value="+961">Lebanon (+961)</option>
                <option value="+968">Oman (+968)</option>
                <option value="+974">Qatar (+974)</option>
                <option value="+965">Kuwait (+965)</option>
                <option value="+973">Bahrain (+973)</option>
                <option value="+967">Yemen (+967)</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="phone-number" class="form-label fw-semibold">
                Phone Number
              </label>
              <input type="tel" class="form-control form-control-lg" id="phone-number" name="phone_number"
                     placeholder="Enter phone number without country code" pattern="[0-9]+" required>
              <small class="text-muted">Enter your phone number without the country code</small>
            </div>

            <!-- OTP Section (Hidden by default, shown after number submission) -->
            <div id="otp-section" style="display: none;">
              <div class="alert alert-info">
                <i class="fe fe-info"></i> An OTP has been sent to your WhatsApp number. Please enter it below.
              </div>
              
              <div class="mb-3">
                <label for="otp-code" class="form-label fw-semibold">
                  Enter OTP
                </label>
                <input type="text" class="form-control form-control-lg" id="otp-code" name="otp_code"
                       placeholder="Enter 6-digit OTP" pattern="[0-9]{6}" maxlength="6">
                <small class="text-muted">
                  OTP will expire in <span id="otp-timer">10:00</span> minutes
                </small>
              </div>

              <div class="mb-3 text-center">
                <button type="button" id="resend-otp-btn" class="btn btn-link" disabled>
                  Resend OTP (<span id="resend-timer">60</span>s)
                </button>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 mb-3">
              <button type="submit" id="submit-btn" class="btn btn-primary btn-lg btn-submit">
                Continue
              </button>
            </div>

            <div class="text-center text-muted small">
              <p>
                <i class="fe fe-info"></i> This step is required to complete your registration
              </p>
            </div>

          </form>
        </div>

      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function() {
    var otpEnabled = <?=get_option('whatsapp_otp_verification_enabled', 0) ? 'true' : 'false'?>;
    var otpSent = false;
    var otpTimer = null;
    var resendTimer = null;
    var otpExpiryMinutes = 10;
    var resendCooldown = 60;

    // Handle form submission
    $('#whatsappSetupForm').on('submit', function(e) {
        e.preventDefault();
        
        var countryCode = $('#country-code').val();
        var phoneNumber = $('#phone-number').val();
        var otpCode = $('#otp-code').val();

        // Validate inputs
        if (!countryCode || !phoneNumber) {
            show_toast('error', 'Please fill in all required fields');
            return;
        }

        // If OTP is not enabled, submit directly
        if (!otpEnabled) {
            submitWhatsAppSetup();
            return;
        }

        // If OTP not sent yet, send OTP first
        if (!otpSent) {
            sendOTP();
            return;
        }

        // If OTP sent, verify it
        if (!otpCode || otpCode.length !== 6) {
            show_toast('error', 'Please enter a valid 6-digit OTP');
            return;
        }

        verifyOTP();
    });

    // Send OTP
    function sendOTP() {
        var countryCode = $('#country-code').val();
        var phoneNumber = $('#phone-number').val();
        var fullNumber = countryCode + phoneNumber;

        $.ajax({
            url: '<?=cn("auth/ajax_send_otp")?>',
            type: 'POST',
            data: {
                whatsapp_number: fullNumber,
                '<?=$this->security->get_csrf_token_name()?>': '<?=$this->security->get_csrf_hash()?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending OTP...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    otpSent = true;
                    $('#otp-section').slideDown();
                    $('#submit-btn').html('Verify OTP');
                    $('#phone-number').prop('readonly', true);
                    $('#country-code').prop('disabled', true);
                    show_toast('success', response.message);
                    startOTPTimer();
                    startResendTimer();
                } else {
                    show_toast('error', response.message || 'Failed to send OTP');
                    $('#submit-btn').prop('disabled', false).html('Continue');
                }
            },
            error: function() {
                show_toast('error', 'An error occurred. Please try again.');
                $('#submit-btn').prop('disabled', false).html('Continue');
            }
        });
    }

    // Verify OTP
    function verifyOTP() {
        var otpCode = $('#otp-code').val();

        $.ajax({
            url: '<?=cn("auth/ajax_verify_otp")?>',
            type: 'POST',
            data: {
                otp_code: otpCode,
                '<?=$this->security->get_csrf_token_name()?>': '<?=$this->security->get_csrf_hash()?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    show_toast('success', response.message);
                    setTimeout(function() {
                        window.location.href = '<?=cn("statistics")?>';
                    }, 1000);
                } else {
                    show_toast('error', response.message || 'Invalid OTP');
                    $('#submit-btn').prop('disabled', false).html('Verify OTP');
                }
            },
            error: function() {
                show_toast('error', 'An error occurred. Please try again.');
                $('#submit-btn').prop('disabled', false).html('Verify OTP');
            }
        });
    }

    // Submit without OTP (when OTP is disabled)
    function submitWhatsAppSetup() {
        var countryCode = $('#country-code').val();
        var phoneNumber = $('#phone-number').val();
        var fullNumber = countryCode + phoneNumber;

        $.ajax({
            url: '<?=cn("auth/ajax_whatsapp_setup")?>',
            type: 'POST',
            data: {
                whatsapp_number: fullNumber,
                '<?=$this->security->get_csrf_token_name()?>': '<?=$this->security->get_csrf_hash()?>'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#submit-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            },
            success: function(response) {
                if (response.status === 'success') {
                    show_toast('success', response.message);
                    setTimeout(function() {
                        window.location.href = '<?=cn("statistics")?>';
                    }, 1000);
                } else {
                    show_toast('error', response.message || 'Failed to save WhatsApp number');
                    $('#submit-btn').prop('disabled', false).html('Continue');
                }
            },
            error: function() {
                show_toast('error', 'An error occurred. Please try again.');
                $('#submit-btn').prop('disabled', false).html('Continue');
            }
        });
    }

    // Resend OTP
    $('#resend-otp-btn').on('click', function() {
        sendOTP();
    });

    // OTP expiry timer
    function startOTPTimer() {
        var timeLeft = otpExpiryMinutes * 60; // in seconds
        
        otpTimer = setInterval(function() {
            timeLeft--;
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            $('#otp-timer').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
            
            if (timeLeft <= 0) {
                clearInterval(otpTimer);
                show_toast('error', 'OTP has expired. Please request a new one.');
                $('#otp-code').prop('disabled', true);
                $('#submit-btn').prop('disabled', true);
            }
        }, 1000);
    }

    // Resend cooldown timer
    function startResendTimer() {
        var timeLeft = resendCooldown;
        $('#resend-otp-btn').prop('disabled', true);
        
        resendTimer = setInterval(function() {
            timeLeft--;
            $('#resend-timer').text(timeLeft);
            
            if (timeLeft <= 0) {
                clearInterval(resendTimer);
                $('#resend-otp-btn').prop('disabled', false).html('Resend OTP');
            }
        }, 1000);
    }

    // Toast notification helper
    function show_toast(type, message) {
        // Use existing notification system or implement basic alert
        if (typeof notify !== 'undefined') {
            notify(type, message);
        } else {
            alert(message);
        }
    }
});
</script>

<?=Modules::run(get_theme()."/footer")?>
