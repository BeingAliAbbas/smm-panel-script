<?=Modules::run(get_theme()."/header")?>  

<section class="banner">
  <div class="container">
    <div class="row justify-content-center">
      
      <!-- Single main column for both: signin code part + login form -->
      <div class="col-lg-6 col-md-8 col-sm-10 col-12" data-aos="fade-down" data-aos-easing="ease-in" data-aos-delay="300">

        <!-- Auto-fetched signin code part (welcome box, inline CSS, etc.) -->
        <?php if (get_code_part('signin','') != '') { ?>
          <div class="mb-4">
            <?=get_code_part('signin','')?>
          </div>
        <?php }?>

        <!-- Login form -->
        <div class="form-login">
          <form class="actionForm" action="<?=cn("auth/ajax_sign_in")?>" data-redirect="<?=cn('order/add')?>" method="POST">
            <div>
              <div class="form-group">
                <?php
                  if (isset($_COOKIE["cookie_email"])) {
                    $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
                  }

                  if (isset($_COOKIE["cookie_pass"])) {
                    $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
                  }
                ?>
                <!-- Email Input with Label -->
                <div class="mb-3">
                  <label for="email-input" class="form-label fw-semibold">
                    <?=lang("Email")?>
                  </label>
                  <input type="email" class="form-control form-control-lg" id="email-input" name="email"
                         placeholder="<?=lang("Email")?>"
                         value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : ""?>" required>
                </div>

                <!-- Password Input with Label -->
                <div class="mb-3">
                  <label for="password-input" class="form-label fw-semibold">
                    <?=lang("Password")?>
                  </label>
                  <input type="password" class="form-control form-control-lg" id="password-input" name="password"
                         placeholder="<?=lang("Password")?>"
                         value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" required>
                </div>
              </div>

              <!-- Remember / Forgot row -->
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="form-check">
                  <input type="checkbox" name="remember" class="form-check-input" id="remember-check" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
                  <label class="form-check-label" for="remember-check">
                    <?=lang("remember_me")?>
                  </label>
                </div>

                <div>
                  <a href="<?=cn("auth/forgot_password")?>" class="text-decoration-none small">
                    <?=lang("forgot_password")?>
                  </a>
                </div>
              </div>

              <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary btn-lg btn-submit">
                  <?=lang("Login")?>
                </button>
              </div>
            </div>
          </form>

          <?php if(get_option('enable_google_login') && get_option('google_client_id') && get_option('google_client_secret')){ ?>
          <!-- Divider -->
          <div class="position-relative my-4">
            <hr class="text-muted">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted fw-medium">OR</span>
          </div>

          <!-- Google Login Button -->
          <div class="d-grid gap-2 mb-3">
            <a href="<?=cn('auth/google')?>" class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2">
              <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
              </svg>
              Sign in with Google
            </a>
          </div>
          <?php }; ?>

          <?php if(!get_option('disable_signup_page')){ ?>
          <div class="text-center text-muted small">
            <?=lang("dont_have_account_yet")?> 
            <a href="<?=cn('auth/signup')?>" class="text-decoration-none fw-semibold"><?=lang("Sign_Up")?></a>
          </div>
          <?php }; ?>
        </div>
      </div>
      <!-- /single main column -->

    </div>
  </div>
</section>

<?=Modules::run(get_theme()."/footer")?>