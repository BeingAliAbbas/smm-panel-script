<link rel="stylesheet" href="<?=BASE?>assets/css/modern-header.css">

<!-- Modern Responsive Header -->
<header class="modern-header">
  <div class="container">
    <div class="header-content">
      <h1 class="site-title">
        <a href="<?=cn('order/add')?>">
          <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" alt="website-logo">
        </a>
      </h1>
      
      <!-- Desktop Navigation -->
      <nav>
        <?php
        session_start();
        $user_id = $_SESSION['uid'];

        if (!function_exists('get_role')) {
            function get_role($role) {
                $user_roles = $_SESSION['roles'];
                return in_array($role, $user_roles);
            }
        }
        ?>
        
        <?php if (get_role('admin')): ?>
          <a href="<?= cn('statistics') ?>" class="<?=(segment(1) == 'statistics') ? 'active' : ''?>"><?= lang("Dashboard") ?></a>
        <?php endif; ?>
        
        <a href="<?=cn('order/add')?>" class="<?=(segment(1) == 'order' && segment(2) == 'add') ? 'active' : ''?>"><?= lang("New_order") ?></a>
        <a href="<?=cn('order/log')?>" class="<?=(segment(1) == 'order' && segment(2) == 'log') ? 'active' : ''?>"><?=lang("Orders")?></a>
        <a href="<?=cn('services')?>" class="<?=(segment(1) == 'services') ? 'active' : ''?>"><?=lang('Services')?></a>
        
        <?php if (get_role("user") || get_role("admin")) { ?>
          <a href="<?=cn('add_funds')?>" class="<?=(segment(1) == 'add_funds') ? 'active' : ''?>"><?=lang("Add_funds")?></a>
        <?php } ?>
        
        <a href="<?=cn('tickets')?>" class="<?=(segment(1) == 'tickets') ? 'active' : ''?>">
          <?=lang("Tickets")?>
          <?php if(isset($total_unread_tickets) && $total_unread_tickets > 0): ?>
            <span class="badge badge-info"><?=$total_unread_tickets?></span>
          <?php endif; ?>
        </a>
        
        <a href="<?=cn('profile')?>" class="<?=(segment(1) == 'profile') ? 'active' : ''?>"><?=lang("Account")?></a>
        
        <?php if (session('uid_tmp')) { ?>
          <a href="<?=cn("blocks/back_to_admin")?>" title="<?=lang('Back_to_Admin')?>" class="ajaxBackToAdmin">
            <i class="fe fe-log-out"></i> <?=lang('Back_to_Admin')?>
          </a>
        <?php } ?>
        
        <a href="<?=cn("auth/logout")?>"><?=lang("Sign_Out")?></a>
      </nav>

      <!-- Mobile Menu Button -->
      <button class="menu-btn" id="menuBtn">☰</button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <!-- User Info -->
    <div class="user-info-mobile">
      <h4><?=lang("Hi")?>, <span class="text-uppercase"><?php _echo(get_field(USERS, ["id" => session('uid')], 'first_name'))?></span></h4>
      <h6>
        <?php
          if (!get_role("admin")) {
            $balance = get_field(USERS, ["id" => session('uid')], 'balance');

            switch (get_option('currency_decimal_separator', 'dot')) {
              case 'dot':
                $decimalpoint = '.';
                break;
              case 'comma':
                $decimalpoint = ',';
                break;
              default:
                $decimalpoint = '';
                break;
            } 

            switch (get_option('currency_thousand_separator', 'comma')) {
              case 'dot':
                $separator = '.';
                break;
              case 'comma':
                $separator = ',';
                break;
              case 'space':
                $separator = ' ';
                break;
              default:
                $separator = '';
                break;
            }
            if (empty($balance) || $balance == 0) {
              $balance = 0.0000;
            }else{
              $balance = convert_currency($balance);
              $balance = currency_format($balance, get_option('currency_decimal', 2), $decimalpoint, $separator);
            }
            
            $current_currency = get_current_currency();
            $currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol',"$");
        ?>
        <?=lang("Balance")?>: <span id="balanceDisplay"><?=$currency_symbol?><?=$balance?></span>
        <?php }else{?> 
          <?=lang("Admin_account")?>
        <?php }?> 
      </h6>
    </div>

    <!-- Currency Switcher -->
    <div class="currency-switcher-mobile">
      <label for="currencySelectorMobile"><?=lang("Currency")?></label>
      <select id="currencySelectorMobile" class="form-control" aria-label="<?=lang('Select_currency')?>">
        <?php
          $current_currency = get_current_currency();
          $currencies = get_active_currencies();
          if (!empty($currencies)) {
            foreach ($currencies as $currency) {
        ?>
        <option value="<?=$currency->code?>" <?=($current_currency && $current_currency->code == $currency->code) ? 'selected' : ''?>>
          <?=$currency->symbol?> - <?=$currency->code?>
        </option>
        <?php
            }
          }
        ?>
      </select>
    </div>

    <!-- Mobile Navigation Links -->
    <nav>
      <?php if (get_role('admin')): ?>
        <a href="<?= cn('statistics') ?>" class="<?=(segment(1) == 'statistics') ? 'active' : ''?>"><?= lang("Dashboard") ?></a>
      <?php endif; ?>

      <a href="<?=cn('order/add')?>" class="<?=(segment(1) == 'order' && segment(2) == 'add') ? 'active' : ''?>"><?= lang("New_order") ?></a>
      <a href="<?=cn('order/log')?>" class="<?=(segment(1) == 'order' && segment(2) == 'log') ? 'active' : ''?>"><?=lang("Orders")?></a>
      <a href="<?=cn('refill/log')?>" class="<?=(segment(1) == 'order' && segment(2) == 'refill') ? 'active' : ''?>"><?=lang("Refill")?></a>
      
      <?php if (get_role("admin") || get_role("supporter")) { ?>
        <a href="<?=cn('category')?>" class="<?=(segment(1) == 'category') ? 'active' : ''?>"><?=lang("Category")?></a>
      <?php } ?>

      <a href="<?=cn('services')?>" class="<?=(segment(1) == 'services') ? 'active' : ''?>"><?=lang('Services')?></a>
      
      <?php if (get_role("user") || get_role("admin")) { ?>
        <a href="<?=cn('add_funds')?>" class="<?=(segment(1) == 'add_funds') ? 'active' : ''?>"><?=lang("Add_funds")?></a>
      <?php } ?>
      
      <?php if (get_option('enable_api_tab') && !get_role("admin")) { ?>      
        <a href="<?=cn('api/docs')?>" class="<?=(segment(2) == 'docs') ? 'active' : ''?>"><?=lang("API")?></a>
      <?php } ?>
      
      <a href="<?=cn('tickets')?>" class="<?=(segment(1) == 'tickets') ? 'active' : ''?>">
        <?=lang("Tickets")?>
        <?php if(isset($total_unread_tickets) && $total_unread_tickets > 0): ?>
          <span class="badge badge-info"><?=$total_unread_tickets?></span>
        <?php endif; ?>
      </a>
      
      <?php if(get_option("enable_affiliate") == "1"){ ?>
        <a href="<?=cn('affiliate')?>" class="<?=(segment(1) == 'affiliate') ? 'active' : ''?>"><?=lang("Affiliate")?></a>
      <?php } ?>
      
      <?php if(get_option("is_childpanel_status") == "1"){ ?>
        <a href="<?=cn('childpanel/add')?>" class="<?=(segment(1) == 'childpanel') ? 'active' : ''?>"><?=lang("Child_Panel")?></a>
      <?php } ?>
      
      <a href="<?=cn('transactions')?>" class="<?=(segment(1) == 'transactions') ? 'active' : ''?>"><?=lang("Transaction_logs")?></a>
      <a href="<?=cn('balance_logs')?>" class="<?=(segment(1) == 'balance_logs') ? 'active' : ''?>"><?=lang("Balance_Logs")?></a>
      
      <?php if(get_role("admin") || get_role("supporter")){ ?>
        <a href="<?=cn('users')?>" class="<?=(segment(1) == 'users') ? 'active' : ''?>"><?=lang("Users")?></a>
        <a href="<?= cn('whatsapp_listed_updated.php') ?>" class="<?=(segment(1) == 'whatsapp_listed_updated') ? 'active' : ''?>">WA Number Updates</a>
        <a href="<?=cn('subscribers')?>" class="<?=(segment(1) == 'subscribers') ? 'active' : ''?>"><?php echo lang("subscribers"); ?></a>
        <a href="<?=cn('user_mail_logs')?>" class="<?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs') ? 'active' : ''?>"><?=lang("User_Mail_Logs")?></a>
        <a href="<?=cn('user_logs')?>" class="<?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs') ? 'active' : ''?>"><?=lang("user_activity_logs")?></a>
        <a href="<?=cn('user_block_ip')?>" class="<?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs') ? 'active' : ''?>"><?=lang("banned_ip_address")?></a>
        <a href="<?=cn('setting')?>" class="<?=(segment(1) == 'setting') ? 'active' : ''?>"><?=lang("System_Settings")?></a>
        <a href="<?=cn('api_provider')?>" class="<?=(segment(1) == 'api_provider') ? 'active' : ''?>"><?=lang("Services_Providers")?></a>
        <a href="<?=cn('payments')?>" class="<?=(segment(1) == 'payments') ? 'active' : ''?>"><?=lang("Payments")?></a>
        <a href="<?=cn('payments_bonuses')?>" class="<?=(segment(1) == 'payments_bonuses') ? 'active' : ''?>"><?=lang("Payments_Bonuses")?></a>
      <?php } ?>
      
      <?php if(get_role("admin")){ ?>
        <a href="<?=cn('news')?>" class="<?=(segment(1) == 'news') ? 'active' : ''?>"><?=lang("Announcement")?></a>
        <a href="<?=cn('faqs')?>" class="<?=(segment(1) == 'faqs') ? 'active' : ''?>">FAQs</a>
        <a href="<?=cn('language')?>" class="<?=(segment(1) == 'language') ? 'active' : ''?>"><?=lang("Language")?></a>
        <a href="https://codewithali.online" target="_blank"><?=lang("Modules_&_Scripts")?></a>
      <?php } ?>
      
      <a href="<?=cn('profile')?>" class="<?=(segment(1) == 'profile') ? 'active' : ''?>"><?=lang("Account")?></a>
      
      <?php if (session('uid_tmp')) { ?>
        <a href="<?=cn("blocks/back_to_admin")?>" class="ajaxBackToAdmin"><?=lang('Back_to_Admin')?></a>
      <?php } ?>
      
      <a href="<?=cn("auth/logout")?>"><?=lang("Sign_Out")?></a>
    </nav>
  </div>
</header>

<script>
// Mobile menu toggle
const menuBtn = document.getElementById('menuBtn');
const mobileMenu = document.getElementById('mobileMenu');

if (menuBtn && mobileMenu) {
  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
  });
}

// Currency switcher handler for mobile
$(document).ready(function() {
  $('#currencySelectorMobile').on('change', function() {
    var selectedCurrency = $(this).val();

    // Show loading overlay
    if ($('#page-overlay').length) {
      $('#page-overlay').addClass('visible incoming');
    }

    $.ajax({
      url: '<?=cn("currencies/set_currency")?>',
      type: 'POST',
      data: {
        currency_code: selectedCurrency,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success' || response.success === true) {
          setTimeout(function(){ 
            location.reload(); 
          }, 500);
        } else {
          alert(response.message || 'Failed to change currency');
          if ($('#page-overlay').length) {
            $('#page-overlay').removeClass('visible incoming');
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('An error occurred while changing currency. Please try again.');
        if ($('#page-overlay').length) {
          $('#page-overlay').removeClass('visible incoming');
        }
      }
    });
  });
});
</script>

<?php
if (get_option("enable_news_announcement") == 1) {
?>
  <a href="<?=cn("news/ajax_notification")?>" style="position: fixed; bottom: 8px; right: 8px; font-size: 20px; padding-top: 3px; text-align: center; z-index: 10000000;" data-toggle="tooltip" data-placement="bottom" title="News & Announcement" class="ajaxModal text-white">
    <div class="bell-fix">
      <i class="fa fa-bell"></i>
      <div class="test">
        <span class="nav-unread <?=(isset($_COOKIE["news_annoucement"]) && $_COOKIE["news_annoucement"] == "clicked") ? "" : "change_color"?>"></span>
      </div>
    </div>
  </a>
<?php }?>

<?php
if (get_option("enable_news_announcement") == 1) {
?>
<a href="https://wa.me/<?=get_option('whatsapp_number')?>/?text=Hello, I have a question" 
   style="position: fixed; bottom: 8px; left: 8px; font-size: 24px; padding-top: 3px; text-align: center; z-index: 10000000;" 
   title="Chat" class="text-white">
  <div class="custom-whatsapp-button">
    <img src="<?php echo BASE; ?>assets/images/whatsapp.png" alt="WhatsApp" class="custom-whatsapp-icon">
  </div>
</a>

<style>
  .custom-whatsapp-button {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 40px;
    margin-top: 10px;
  }

  .custom-whatsapp-icon {
    width: 100%;
    height: 100%;
  }
</style>
<?php }?>