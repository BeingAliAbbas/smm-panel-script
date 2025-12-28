
<<<<<<< HEAD
<?php if (get_code_part_by_position('signin', 'top', '') != '') { ?>
<div class="row">
  <div class="col-sm-12">
    <?=get_code_part_by_position('signin', 'top', '')?>
  </div>
</div>
<?php }?>

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
<div class="row h-100 align-items-center auth-form">
  <div class="col-md-6 col-login mx-auto ">
    <form class="card actionForm" action="<?=cn("auth/ajax_sign_in")?>" data-redirect="<?=cn('statistics')?>" method="POST">
      <div class="card-body ">
        <div class="card-title text-center">
          <div class="site-logo mb-2">
            <a href="<?=cn()?>">
              <img src="<?=get_option('website_logo', BASE."assets/images/logo.png")?>" alt="website-logo" style="max-height: 50px;">
            </a>
          </div>
          <h5><?=lang("login_to_your_account")?></h5>
        </div>
        <div class="form-group">
          <?php

            if (isset($_COOKIE["cookie_email"])) {
              $cookie_email = encrypt_decode($_COOKIE["cookie_email"]);
            }

            if (isset($_COOKIE["cookie_pass"])) {
              $cookie_pass = encrypt_decode($_COOKIE["cookie_pass"]);
            }

          ?>
          <div class="input-icon mb-5">
            <span class="input-icon-addon">
              <i class="fe fe-mail"></i>
            </span>
            <input type="email" class="form-control" name="email" placeholder="<?=lang("Email")?>" value="<?=(isset($cookie_email) && $cookie_email != "") ? $cookie_email : ""?>" required>
          </div>    
                
          <div class="input-icon mb-5">
            <span class="input-icon-addon">
<<<<<<< HEAD
              <i class="fas fa-key"></i>
=======
              <i class="fa fa-key"></i>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            </span>
            <input type="password" class="form-control" name="password" placeholder="<?=lang("Password")?>" value="<?=(isset($cookie_pass) && $cookie_pass != "") ? $cookie_pass : ""?>" required>
          </div>   
        </div>

        <div class="form-group">
<<<<<<< HEAD
          <label class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
            <span class="form-check-label"><?=lang("remember_me")?></span>
            <a href="<?=cn("auth/forgot_password")?>" class="float-end small"><?=lang("forgot_password")?></a>
=======
          <label class="custom-control custom-checkbox">
            <input type="checkbox" name="remember" class="custom-control-input" <?=(isset($cookie_email) && $cookie_email != "") ? "checked" : ""?>>
            <span class="custom-control-label"><?=lang("remember_me")?></span>
            <a href="<?=cn("auth/forgot_password")?>" class="float-right small"><?=lang("forgot_password")?></a>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </label>
        </div>

        <div class="form-footer">
          <button type="submit" class="btn btn-primary btn-block"><?=lang("Login")?></button>
        </div>
      </div>
    </form>
    <div class="text-center text-muted">
      <?=lang("dont_have_account_yet")?> <a href="<?=cn('auth/signup')?>"><?=lang("Sign_Up")?></a>
    </div>
  </div>
</div>
<<<<<<< HEAD

<?php if (get_code_part_by_position('signin', 'bottom', '') != '') { ?>
<div class="row">
  <div class="col-sm-12">
    <?=get_code_part_by_position('signin', 'bottom', '')?>
  </div>
</div>
<?php }?>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
