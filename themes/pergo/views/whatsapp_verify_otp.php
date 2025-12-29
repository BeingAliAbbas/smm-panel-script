<?=Modules::run(get_theme()."/header")?>  

<section class="banner">
  <div class="container">
    <div class="row justify-content-center">
      
      <!-- WhatsApp OTP Verification Column -->
      <div class="col-lg-6 col-md-8 col-sm-10 col-12" data-aos="fade-down" data-aos-easing="ease-in" data-aos-delay="300">

        <!-- OTP Verification Form -->
        <div class="form-login">
          <div class="text-center mb-4">
            <h2 class="fw-bold">Enter OTP</h2>
            <p class="text-muted">We've sent a 6-digit code to<br><strong><?=htmlspecialchars($whatsapp_number)?></strong></p>
          </div>

          <form class="actionForm" id="otp-verify-form" action="<?=cn("whatsapp_verify/ajax_verify_otp")?>" data-redirect="<?=cn('statistics')?>" method="POST">
            <input type="hidden" name="whatsapp_number" value="<?=htmlspecialchars($whatsapp_number)?>">
            
            <div>
              <!-- OTP Input -->
              <div class="mb-3">
                <label for="otp-input" class="form-label fw-semibold">
                  Verification Code
                </label>
                <input type="text" class="form-control form-control-lg text-center" id="otp-input" name="otp_code"
                       placeholder="000000" maxlength="6" pattern="[0-9]{6}" required
                       style="letter-spacing: 0.5em; font-size: 1.5rem;">
                <small class="form-text text-muted">
                  Enter the 6-digit code sent to your WhatsApp
                </small>
              </div>

              <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg btn-submit">
                  Verify OTP
                </button>
              </div>
            </div>
          </form>

          <div class="text-center">
            <p class="text-muted small mb-2">
              Didn't receive the code?
            </p>
            <button type="button" class="btn btn-link" id="resend-otp-btn">
              Resend OTP
            </button>
            <p class="text-muted small mt-3">
              <a href="<?=cn('whatsapp_verify')?>" class="text-decoration-none">
                Change Phone Number
              </a>
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function(){
    // OTP Verification Form
    $('#otp-verify-form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('.btn-submit');
        var submitText = submitBtn.html();
        var redirect = form.data('redirect');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function(){
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Verifying...');
            },
            success: function(response){
                if(response.status == 'success'){
                    show_message(response.status, response.message);
                    setTimeout(function(){
                        window.location.href = redirect;
                    }, 1000);
                } else {
                    show_message(response.status, response.message);
                    submitBtn.prop('disabled', false).html(submitText);
                }
            },
            error: function(){
                show_message('error', 'An error occurred. Please try again.');
                submitBtn.prop('disabled', false).html(submitText);
            }
        });
    });

    // Resend OTP
    $('#resend-otp-btn').on('click', function(){
        var btn = $(this);
        var btnText = btn.html();
        var whatsappNumber = $('input[name="whatsapp_number"]').val();
        
        $.ajax({
            url: '<?=cn("whatsapp_verify/ajax_resend_otp")?>',
            type: 'POST',
            dataType: 'json',
            data: {whatsapp_number: whatsappNumber},
            beforeSend: function(){
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
            },
            success: function(response){
                show_message(response.status, response.message);
                if(response.status == 'success'){
                    // Disable button for 60 seconds
                    var countdown = 60;
                    var interval = setInterval(function(){
                        countdown--;
                        btn.html('Resend OTP (' + countdown + 's)');
                        if(countdown <= 0){
                            clearInterval(interval);
                            btn.prop('disabled', false).html(btnText);
                        }
                    }, 1000);
                } else {
                    btn.prop('disabled', false).html(btnText);
                }
            },
            error: function(){
                show_message('error', 'An error occurred. Please try again.');
                btn.prop('disabled', false).html(btnText);
            }
        });
    });

    // Auto-format OTP input (only numbers)
    $('#otp-input').on('input', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>

<?=Modules::run(get_theme()."/footer")?>
