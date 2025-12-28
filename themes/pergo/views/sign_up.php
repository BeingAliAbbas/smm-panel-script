<?=Modules::run(get_theme()."/header")?>
<style>
hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
}
</style>
<<<<<<< HEAD
<style>
:root {
  --primary: #0077ff !important;
  --primary-dark: #0266d6 !important;
  --card-bg: #ffffff !important;
  --page-bg: linear-gradient(135deg, #d9edff 0%, #c6e0ff 100%) !important;
  --border: #d6dbe2 !important;
  --muted: #6c757d !important;
  --shadow: 0 1rem 2.25rem rgba(0, 0, 0, 0.12) !important;
  --radius: 14px !important;
}

.auth-signup-form {
  width: 100% !important;
  min-height: 100vh !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  padding: 1.25rem !important;
  background: var(--page-bg) !important;
}

.form-signup {
  width: 100% !important;
  max-width: 620px !important;
  border-radius: var(--radius) !important;
  background: var(--card-bg) !important;
  box-shadow: var(--shadow) !important;
  border: 1px solid rgba(0, 0, 0, 0.06) !important;
  padding: 1.25rem 1.5rem !important;
}

.form-signup h4 {
  color: #1f2b3a !important;
  font-weight: 800 !important;
  letter-spacing: 0.2px !important;
  margin-bottom: 1rem !important;
}

.form-signup .form-label {
  color: #1f2b3a !important;
  font-weight: 700 !important;
  margin-bottom: 0.25rem !important;
}

.form-signup .input-group-text {
  background-color: #eef2f6 !important;
  border-color: var(--border) !important;
  color: #1f2b3a !important;
  min-width: 44px !important;
  justify-content: center !important;
}

.form-signup .form-control,
.form-signup .form-select {
  border-color: var(--border) !important;
  height: 48px !important;
  font-size: 0.97rem !important;
  color: #1f2b3a !important;
}

.form-signup .form-control::placeholder,
.form-signup .form-select::placeholder {
  color: #9aa3ad !important;
}

.form-signup .form-control:focus,
.form-signup .form-select:focus {
  border-color: var(--primary) !important;
  box-shadow: 0 0 0 0.2rem rgba(0, 119, 255, 0.16) !important;
}

.form-signup .btn-submit {
  font-weight: 700 !important;
  height: 50px !important;
  background: var(--primary) !important;
  border: 1px solid var(--primary) !important;
  color: #fff !important;
  transition: transform 0.15s ease, box-shadow 0.2s ease !important;
  width: 100% !important;
}

.form-signup .btn-submit:hover {
  background: var(--primary-dark) !important;
  border-color: var(--primary-dark) !important;
  transform: translateY(-1px) !important;
  box-shadow: 0 0.65rem 1.25rem rgba(0, 119, 255, 0.2) !important;
}

.form-signup .btn-submit:active {
  transform: translateY(0) !important;
}

.form-signup .form-check {
  display: flex !important;
  align-items: center !important;
  gap: 0.4rem !important;
}

.form-signup .form-check-label {
  font-size: 0.92rem !important;
  color: #1f2b3a !important;
}

.form-signup a {
  color: var(--primary) !important;
  font-weight: 600 !important;
}

.form-signup a:hover {
  color: var(--primary-dark) !important;
}

/* Row spacing and gutters */
.form-signup .row.g-3 {
  margin: 0 !important;
  row-gap: 0.65rem !important;
}

.form-signup .row.g-3 > [class*="col-"] {
  padding: 0 !important;
  padding-right: 0.75rem !important;
}

.form-signup .row.g-3 > [class*="col-"]:last-child {
  padding-right: 0 !important;
}

/* Recaptcha centering */
.form-signup .g-recaptcha {
  display: flex !important;
  justify-content: center !important;
}

/* Horizontal rule spacing */
hr {
  margin-top: 1rem !important;
  margin-bottom: 1rem !important;
}

/* Tablet/Mobile adjustments */
@media (max-width: 768px) {
  .auth-signup-form {
    padding: 0.9rem !important;
  }

  .form-signup {
    max-width: 100% !important;
    padding: 1rem 1.1rem !important;
    border-radius: 12px !important;
  }

  .form-signup h4 {
    font-size: 1.2rem !important;
    text-align: center !important;
    margin-bottom: 0.9rem !important;
  }

  .form-signup .row.g-3 {
    row-gap: 0.5rem !important;
  }

  .form-signup .row.g-3 > [class*="col-"] {
    width: 100% !important;
    padding-right: 0 !important;
  }

  .form-signup .form-control,
  .form-signup .form-select {
    height: 46px !important;
    font-size: 0.95rem !important;
  }

  .form-signup .input-group-text {
    min-width: 42px !important;
  }

  .form-signup .btn-submit {
    height: 48px !important;
    font-size: 1rem !important;
  }

  .form-signup .form-check-label {
    font-size: 0.9rem !important;
    line-height: 1.3 !important;
  }
}

@media (max-width: 576px) {
  .auth-signup-form {
    padding: 0.75rem !important;
  }

  .form-signup {
    padding: 0.95rem !important;
  }

  .form-signup h4 {
    font-size: 1.1rem !important;
  }

  .form-signup .form-control,
  .form-signup .form-select {
    height: 44px !important;
    font-size: 0.94rem !important;
  }

  .form-signup .btn-submit {
    height: 46px !important;
    font-size: 0.98rem !important;
  }
}
</style>
<br>
<div class="auth-signup-form">
  <div class="form-signup p-4 my-4">
    <form class="actionForm" action="<?=cn("auth/ajax_sign_up")?>" data-redirect="<?=cn('order/add')?>" method="POST">
      <div>
        <div class="card-title text-center mb-4">
          <h4><?=lang("register_now")?></h4>
        </div>
        <div class="form-group">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="first-name" class="form-label fw-semibold small"><?=lang("first_name")?></label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fe fe-user"></i>
                </span>
                <input type="text" class="form-control form-control-lg" id="first-name" name="first_name" placeholder="<?=lang("first_name")?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <label for="last-name" class="form-label fw-semibold small"><?=lang("last_name")?></label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fe fe-user"></i>
                </span>
                <input type="text" class="form-control form-control-lg" id="last-name" name="last_name" placeholder="<?=lang("last_name")?>" required>
              </div>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label for="email" class="form-label fw-semibold small"><?=lang("Email")?></label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fe fe-mail"></i>
              </span>
              <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="<?=lang("Email")?>" required>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="whatsapp_number" class="form-label fw-semibold small">WhatsApp Number</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fab fa-whatsapp"></i>
              </span>
              <input type="text" class="form-control form-control-lg" name="whatsapp_number" id="whatsapp_number"
                     placeholder="Whatsapp Number" required pattern="^\+92\d{10}$" maxlength="13" 
                     value="" onfocus="addPrefix()" onblur="removePrefix()" oninput="validateWhatsappNumber(event)">
            </div>
          </div>
=======
<br>
<div class="auth-signup-form">
  <div class="form-signup">
    <form class="actionForm" action="<?=cn("auth/ajax_sign_up")?>" data-redirect="<?=cn('order/add')?>" method="POST">
      <div>
        <div class="card-title text-center">
          
          <h4 class=" text-white"><?=lang("register_now")?></h4>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <div class="input-icon mb-3">
                <span class="input-icon-addon">
                  <i class="fe fe-user"></i>
                </span>
                <input type="text" class="form-control" name="first_name" placeholder="<?=lang("first_name")?>"  required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-icon mb-3">
                <span class="input-icon-addon">
                  <i class="fe fe-user"></i>
                </span>
                <input type="text" class="form-control" name="last_name" placeholder="<?=lang("last_name")?>" required>
              </div>  
            </div>
          </div>

          <div class="input-icon mb-3">
            <span class="input-icon-addon">
              <i class="fe fe-mail"></i>
            </span>
            <input type="email" class="form-control" name="email" placeholder="<?=lang("Email")?>" required>
          </div>  
          <div class="input-icon mb-3">
    <span class="input-icon-addon">
        <i class="fa fa-whatsapp"></i>
    </span>
    <input type="text" class="form-control" name="whatsapp_number" id="whatsapp_number"
           placeholder="Whatsapp Number" required pattern="^\+92\d{10}$" maxlength="13" 
           value="" onfocus="addPrefix()" onblur="removePrefix()" oninput="validateWhatsappNumber(event)">
</div>


>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98

<script>
  function addPrefix() {
    var input = document.getElementById('whatsapp_number');
    // Check if the input is empty and add +92 as prefix
    if (input.value === '') {
        input.value = '+92';
    }
}

function removePrefix() {
    var input = document.getElementById('whatsapp_number');
    // If the value is only +92, clear the field to show placeholder
    if (input.value === '+92') {
        input.value = '';
    }
}

function validateWhatsappNumber(event) {
    var input = event.target;
    var value = input.value;

    // If the input contains a number after +92, validate it
    if (value.startsWith('+92') && value.length > 3) {
        // Allow digits only after +92, and enforce max length of 13
        input.value = '+92' + value.slice(3).replace(/[^0-9]/g, ''); // Remove any non-numeric characters

        // Enforce the max length of 13 (including +92)
        if (input.value.length > 13) {
            input.value = input.value.slice(0, 13);
        }
    }

    // Ensure the number after +92 follows the valid format
    if (!/^\+92\d{10}$/.test(input.value)) {
        // Optionally, you can show an error message here
        input.setCustomValidity("Please enter a valid WhatsApp number with the +92 prefix");
    } else {
        input.setCustomValidity("");
    }
}
<<<<<<< HEAD
=======

>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
</script>

          <?php
            if (get_option('enable_signup_skype_field')) {
          ?>
<<<<<<< HEAD
          <div class="mb-3">
            <label for="skype-id" class="form-label fw-semibold small"><?=lang("Skype_id")?></label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fab fa-skype"></i>
              </span>
              <input type="text" class="form-control form-control-lg" id="skype-id" name="skype_id" placeholder="<?=lang("Skype_id")?>" required>
            </div>
          </div>
          <?php } ?>
          
          <div class="mb-3">
            <label for="password" class="form-label fw-semibold small"><?=lang("Password")?></label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-key"></i>
              </span>
              <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="<?=lang("Password")?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="re-password" class="form-label fw-semibold small"><?=lang("Confirm_password")?></label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-key"></i>
              </span>
              <input type="password" class="form-control form-control-lg" id="re-password" name="re_password" placeholder="<?=lang("Confirm_password")?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="timezone" class="form-label fw-semibold small">Timezone</label>
            <select name="timezone" id="timezone" class="form-select form-select-lg">
=======
          <div class="input-icon mb-3">
            <span class="input-icon-addon">
              <i class="fa fa-skype"></i>
            </span>
            <input type="text" class="form-control" name="skype_id" placeholder="<?=lang("Skype_id")?>" required>
          </div>    
          <?php } ?>      
          <div class="input-icon mb-3">
            <span class="input-icon-addon">
              <i class="fa fa-key"></i>
            </span>
            <input type="password" class="form-control" name="password" placeholder="<?=lang("Password")?>" required>
          </div>    

          <div class="input-icon mb-3">
            <span class="input-icon-addon">
              <i class="fa fa-key"></i>
            </span>
            <input type="password" class="form-control" name="re_password" placeholder="<?=lang("Confirm_password")?>" required>
          </div>

          <div class="input-icon mb-3">
            <span class="input-icon-addon">
              <i class="fe fe-clock"></i>
            </span>
            <select  name="timezone" class="form-control square">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
              <?php $time_zones = tz_list();
                if (!empty($time_zones)) {
                  $location = get_location_info_by_ip(get_client_ip());
                  $user_timezone = $location->timezone;
                  if ($user_timezone == "" || $user_timezone == 'Unknow') {
                    $user_timezone = get_option("default_timezone", 'UTC');
                  }
                  foreach ($time_zones as $key => $time_zone) {
              ?>
              <option value="<?=$time_zone['zone']?>" <?=($user_timezone == $time_zone["zone"])? 'selected': ''?>><?=$time_zone['time']?></option>
              <?php }}?>
            </select>
          </div>
        </div>
        
        <?php 
        if(get_option("enable_affiliate") == "1"){?>
        
<<<<<<< HEAD
        <div class="mb-3">
          <label for="referral" class="form-label fw-semibold small"><?=lang("Referral_id_(optional)")?></label>
          <div class="input-group">
            <span class="input-group-text">
              <i class="far fa-handshake"></i>
            </span>
            <input type="text" class="form-control form-control-lg" id="referral" name="referral" placeholder="<?=lang("Referral_id_(optional)")?>" value="<?=session("referral")?>">
          </div>
=======
        <div class="input-icon mb-5">
            <span class="input-icon-addon">
              <i class="fa fa-handshake-o"></i>
            </span>
            <input type="text" class="form-control" name="referral" placeholder="<?=lang("Referral_id_(optional)")?>" value="<?=session("referral")?>">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        </div>
        <?php }?>
        
        <?php
          if (get_option('enable_goolge_recapcha') &&  get_option('google_capcha_site_key') != "" && get_option('google_capcha_secret_key') != "") {
        ?>
<<<<<<< HEAD
        <div class="mb-3">
=======
        <div class="form-group">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          <div class="g-recaptcha" data-sitekey="<?=get_option('google_capcha_site_key')?>"></div>
        </div>
        <?php } ?> 

<<<<<<< HEAD
        <div class="mb-3">
          <div class="form-check">
            <input type="checkbox" name="terms" class="form-check-input" id="terms-check" required />
            <label class="form-check-label" for="terms-check">
              <?=lang("i_agree_the")?> <a href="<?=cn('terms')?>" class="text-decoration-none"><?=lang("terms__policy")?></a>
            </label>
          </div>
        </div>

        <div class="d-grid gap-2 mb-3">
          <button type="submit" class="btn btn-primary btn-lg btn-submit"><?=lang("create_new_account")?></button>
        </div>
      </div>
    </form>
    <div class="text-center text-muted small">
      <?=lang("already_have_account")?> <a href="<?=cn('auth/login')?>" class="text-decoration-none fw-semibold"><?=lang("Login")?></a>
=======
        <div class="form-group">
          <label class="custom-control custom-checkbox">
            <input type="checkbox" name="terms" class="custom-control-input" />
            <span class="custom-control-label"><?=lang("i_agree_the")?> <a href="<?=cn('terms')?>" class="text-white"><?=lang("terms__policy")?></a></span>
          </label>
        </div>

        <div class="form-footer">
          <button type="submit" class="btn btn-pill btn-2 btn-block btn-submit btn-gradient"><?=lang("create_new_account")?></button>
        </div>
      </div>
    </form>
    <div class="text-center text-muted m-t-20">
      <?=lang("already_have_account")?> <a href="<?=cn('auth/login')?>" class="btn-sign-up"><?=lang("Login")?></a>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
    </div>
  </div>
</div><br>



<?=Modules::run(get_theme()."/footer", false)?>
<<<<<<< HEAD
=======
<style>
 /* Main Container */
.auth-signup-form {
  width: 100%;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
  background: rgba(0, 0, 0, 0.7);
}

/* Sign-Up Form Container */
.form-signup {
  width: 100%;
  max-width: 500px;
  border-radius: 8px;
  padding: 20px;
  background: #061d2b;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
  transition: all 0.3s ease;
  margin: 30px auto;
}

/* Site Logo */
.form-signup .site-logo {
  padding-bottom: 15px;
  text-align: center;
}

.form-signup .site-logo img {
  max-height: 50px;
  width: auto;
}

/* Section Title */
.form-signup h4 {
  color: #ffffff;
  font-weight: bold;
  margin-bottom: 15px;
  text-align: center;
  font-size: 18px;
}

/* Input Fields */
.form-signup input[type="text"],
.form-signup input[type="password"],
.form-signup input[type="email"],
.form-signup input[type="tel"],
.form-signup select {
  width: 100%;
  height: 40px;
  border: 1px solid #04a9f4;
  border-radius: 6px;
  padding: 0 12px;
  background: transparent;
  color: #ffffff;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s ease;
}

.form-signup input::placeholder {
  color: #b1b1b1;
  font-size: 13px;
}

.form-signup input:focus {
  border-color: #0082be;
}

/* Submit Button */
.form-signup .btn-submit {
  width: 100%;
  height: 45px;
  font-size: 15px;
  font-weight: bold;
  color: #ffffff;
  background: #04a9f4;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2);
  outline: none;
}

.form-signup .btn-submit:hover {
  background: #0082be;
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0, 0, 0, 0.3);
}

.form-signup .btn-submit:active {
  transform: translateY(1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Checkbox */
.form-signup .custom-control {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.form-signup .custom-control input {
  margin-right: 8px;
}

.form-signup .custom-control-label {
  color: #ffffff;
  font-size: 13px;
}

.form-signup .custom-control-label a {
  color: #04a9f4;
  text-decoration: none;
}

.form-signup .custom-control-label a:hover {
  text-decoration: underline;
}

/* Google reCAPTCHA */
.form-signup .g-recaptcha {
  margin-bottom: 15px;
}

/* Already Have Account Link */
.form-signup .signup-link {
  text-align: center;
  margin-top: 15px;
  color: #ffffff;
  font-size: 13px;
}

.form-signup .signup-link a {
  color: #04a9f4;
  text-decoration: none;
  font-weight: 600;
}

.form-signup .signup-link a:hover {
  text-decoration: underline;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .form-signup {
    padding: 15px;
    margin: 20px;
  }

  .form-signup h4 {
    font-size: 16px;
  }

  .form-signup input,
  .form-signup .btn-submit {
    height: 40px;
    font-size: 13px;
  }

  .auth-signup-form {
    padding: 5px;
  }
}

@media (max-width: 480px) {
  .form-signup {
    max-width: 100%;
    padding: 10px;
  }

  .form-signup h4 {
    font-size: 15px;
  }

  .form-signup input,
  .form-signup .btn-submit {
    height: 35px;
    font-size: 12px;
  }

  .form-signup .signup-link {
    font-size: 12px;
  }
}


</style>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
