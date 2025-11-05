<style>
/* ===== Collapsible Header Menu Styles ===== */
.header-menu {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  background: linear-gradient(135deg, #051d2f, #063a58);
  border-bottom: 0.5px solid #04a9f4;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.header-menu.scrolled {
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.3);
}

.header-menu.collapsed {
  transform: translateY(-100%);
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 25px;
  height: 70px;
}

.header-logo {
  display: flex;
  align-items: center;
}

.header-logo img {
  max-height: 40px;
  transition: all 0.3s ease;
}

.hamburger-menu {
  display: none;
  flex-direction: column;
  cursor: pointer;
  padding: 5px;
  z-index: 1001;
}

.hamburger-menu span {
  width: 25px;
  height: 3px;
  background-color: #fff;
  margin: 3px 0;
  transition: all 0.3s ease;
  border-radius: 2px;
}

.hamburger-menu.active span:nth-child(1) {
  transform: rotate(45deg) translate(8px, 8px);
}

.hamburger-menu.active span:nth-child(2) {
  opacity: 0;
}

.hamburger-menu.active span:nth-child(3) {
  transform: rotate(-45deg) translate(7px, -6px);
}

.nav-menu {
  display: flex;
  align-items: center;
  gap: 0;
  background: #051d2f;
  padding: 0;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.4s ease, padding 0.4s ease;
}

.nav-menu.active {
  max-height: 80vh;
  overflow-y: auto;
  padding: 20px 0;
}

.nav-menu-inner {
  width: 100%;
}

.currency-switcher-menu {
  padding: 15px 25px;
  border-bottom: 1px solid #04a9f4;
  background: rgba(4, 169, 244, 0.1);
}

.currency-switcher-menu label {
  display: block;
  color: #fff;
  font-size: 12px;
  margin-bottom: 8px;
  font-weight: 500;
}

.currency-switcher-menu select {
  width: 100%;
  background: rgba(255,255,255,0.15);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 5px;
  padding: 8px 12px;
  font-size: 14px;
  cursor: pointer;
  outline: none;
}

.currency-switcher-menu select option {
  color: #000;
  background: #fff;
}

.user-info {
  padding: 15px 25px;
  border-bottom: 1px solid rgba(4, 169, 244, 0.3);
}

.user-info h4 {
  color: #fff;
  font-size: 16px;
  margin-bottom: 5px;
}

.user-info h6 {
  color: #04a9f4;
  font-size: 14px;
  margin: 0;
}

.nav-links {
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-link {
  display: block;
  padding: 12px 25px;
  color: #fff;
  text-decoration: none;
  transition: all 0.2s ease;
  border-bottom: 0.3px solid rgba(0, 0, 0, 0.2);
}

.nav-link:hover {
  background: #04a9f4;
  color: #fff;
  text-decoration: none;
}

.nav-link.active {
  background: rgba(4, 169, 244, 0.2);
  border-left: 3px solid #04a9f4;
}

.nav-link i {
  margin-right: 10px;
  width: 20px;
  display: inline-block;
}

.nav-item {
  display: flex;
  align-items: center;
}

.back-to-admin {
  position: absolute;
  right: 80px;
  top: 50%;
  transform: translateY(-50%);
  color: #fff;
  font-size: 20px;
  z-index: 1002;
}

/* Desktop Styles */
@media (min-width: 992px) {
  .hamburger-menu {
    display: none !important;
  }

  .nav-menu {
    max-height: none !important;
    overflow: visible !important;
    padding: 0 !important;
    display: block !important;
  }

  .nav-menu-inner {
    display: flex;
    align-items: flex-start;
  }

  .currency-switcher-menu {
    flex: 0 0 250px;
    border-bottom: none;
    border-right: 1px solid #04a9f4;
    padding: 20px;
  }

  .user-info {
    flex: 0 0 250px;
    border-bottom: none;
    border-right: 1px solid rgba(4, 169, 244, 0.3);
    padding: 20px;
  }

  .nav-links {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }

  .nav-link {
    border-bottom: 0.3px solid rgba(0, 0, 0, 0.2);
    border-right: 0.3px solid rgba(0, 0, 0, 0.1);
  }
}

/* Mobile Styles */
@media (max-width: 991px) {
  .hamburger-menu {
    display: flex;
  }

  .header-top {
    padding: 15px 20px;
  }
}

/* Body padding for fixed header */
body {
  padding-top: 70px;
}
</style>

<!-- Collapsible Header Menu -->
<div class="header-menu" id="headerMenu">
  <div class="header-top">
    <div class="header-logo">
      <a href="<?=cn('order/add')?>">
        <img src="<?=get_option('website_logo_white', BASE."assets/images/logo-white.png")?>" alt="website-logo">
      </a>
    </div>

    <?php if (session('uid_tmp')): ?>
      <div class="back-to-admin">
        <a href="<?=cn("blocks/back_to_admin")?>" data-toggle="tooltip" data-placement="bottom" 
           title="<?=lang('Back_to_Admin')?>" class="text-white ajaxBackToAdmin">
          <i class="fe fe-log-out"></i>
        </a>
      </div>
    <?php endif; ?>

    <div class="hamburger-menu" id="hamburgerMenu">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div class="nav-menu" id="navMenu">
    <div class="nav-menu-inner">
      <!-- Currency Switcher at the top -->
      <div class="currency-switcher-menu">
        <label><?=lang("Currency")?></label>
        <select id="currencySelector" class="form-control">
          <?php
            $current_currency = get_current_currency();
            $currencies = get_active_currencies();
            if (!empty($currencies)) {
              foreach ($currencies as $currency) {
          ?>
          <option value="<?=$currency->code?>" <?=($current_currency && $current_currency->code == $currency->code) ? 'selected' : ''?>>
            <?=$currency->code?> - <?=$currency->symbol?>
          </option>
          <?php 
              }
            }
          ?>
        </select>
      </div>

      <!-- User Info -->
      <div class="user-info">
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
              } else {
                $balance = convert_currency($balance);
                $balance = currency_format($balance, get_option('currency_decimal', 2), $decimalpoint, $separator);
              }

              $current_currency = get_current_currency();
              $currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol',"$");
          ?>
            <?=lang("Balance")?>: <span id="balanceDisplay"><?=$currency_symbol?><?=$balance?></span>
          <?php } else { ?>
            <?=lang("Admin_account")?>
          <?php } ?>
        </h6>
      </div>

      <!-- Navigation Links -->
      <ul class="nav-links">
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
          <li>
            <a href="<?=cn('statistics')?>" class="nav-link <?=(segment(1) == 'statistics') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fe fe-bar-chart-2"></i><?=lang("Dashboard")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="<?=cn('order/add')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'add') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fe fe-shopping-cart"></i><?=lang("New_order")?>
            </div>
          </a>
        </li>

        <li>
          <a href="<?=cn('order/log')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'log') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fa fa-shopping-cart"></i><?=lang("Orders")?>
            </div>
          </a>
        </li>

        <li>
          <a href="<?=cn('refill/log')?>" class="nav-link <?=(segment(1) == 'order' && segment(2) == 'refill') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fa fa-recycle"></i><?=lang("Refill")?>
            </div>
          </a>
        </li>

        <?php if (get_role("admin") || get_role("supporter")): ?>
          <li>
            <a href="<?=cn('category')?>" class="nav-link <?=(segment(1) == 'category') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-table"></i><?=lang("Category")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="<?=cn('services')?>" class="nav-link <?=(segment(1) == 'services') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fe fe-list"></i><?=lang('Services')?>
            </div>
          </a>
        </li>

        <?php if (get_role("user") || get_role("admin")): ?>
          <li>
            <a href="<?=cn('add_funds')?>" class="nav-link <?=(segment(1) == 'add_funds') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-money"></i><?=lang("Add_funds")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (get_option('enable_api_tab') && !get_role("admin")): ?>
          <li>
            <a href="<?=cn('api/docs')?>" class="nav-link <?=(segment(2) == 'docs') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fe fe-share-2"></i><?=lang("API")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="<?=cn('tickets')?>" class="nav-link <?=(segment(1) == 'tickets') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fa fa-comments-o"></i><?=lang("Tickets")?>&nbsp;&nbsp;<span class="badge badge-info"><?=$total_unread_tickets?></span>
            </div>
          </a>
        </li>

        <?php if (get_option("enable_affiliate") == "1"): ?>
          <li>
            <a href="<?=cn('affiliate')?>" class="nav-link <?=(segment(1) == 'affiliate') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-money"></i><?=lang("Affiliate")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (get_option("is_childpanel_status") == "1"): ?>
          <li>
            <a href="<?=cn('childpanel/add')?>" class="nav-link <?=(segment(1) == 'childpanel') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-child"></i><?=lang("Child_Panel")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="<?=cn('transactions')?>" class="nav-link <?=(segment(1) == 'transactions') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fe fe-calendar"></i><?=lang("Transaction_logs")?>
            </div>
          </a>
        </li>

        <?php if (get_role("admin") || get_role("supporter")): ?>
          <li>
            <a href="<?=cn('users')?>" class="nav-link <?=(segment(1) == 'users') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fe fe-users"></i><?=lang("Users")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('subscribers')?>" class="nav-link <?=(segment(1) == 'subscribers') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-user-circle-o"></i><?php echo lang("subscribers"); ?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('user_mail_logs')?>" class="nav-link <?=(segment(1) == 'user' && segment(2) == 'mail' && segment(3) == 'logs') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-envelope"></i><?=lang("User_Mail_Logs")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('user_logs')?>" class="nav-link">
              <div class="nav-item">
                <i class="fa fa-sort"></i><?=lang("user_activity_logs")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('user_block_ip')?>" class="nav-link">
              <div class="nav-item">
                <i class="fa fa-ban"></i><?=lang("banned_ip_address")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('setting')?>" class="nav-link <?=(segment(1) == 'setting') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-cog"></i><?=lang("System_Settings")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('api_provider')?>" class="nav-link <?=(segment(1) == 'api_provider') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-share-alt"></i><?=lang("Services_Providers")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('payments')?>" class="nav-link <?=(segment(1) == 'payments') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-credit-card"></i><?=lang("Payments")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('payments_bonuses')?>" class="nav-link <?=(segment(1) == 'payments_bonuses') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-money"></i><?=lang("Payments_Bonuses")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (get_role("admin")): ?>
          <li>
            <a href="<?=cn('news')?>" class="nav-link <?=(segment(1) == 'news') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-bell"></i><?=lang("Announcement")?>
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('faqs')?>" class="nav-link <?=(segment(1) == 'faqs') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-book"></i>FAQs
              </div>
            </a>
          </li>

          <li>
            <a href="<?=cn('language')?>" class="nav-link <?=(segment(1) == 'language') ? 'active' : ''?>">
              <div class="nav-item">
                <i class="fa fa-language"></i><?=lang("Language")?>
              </div>
            </a>
          </li>

          <li>
            <a href="https://codewithali.online" target="_blank" class="nav-link">
              <div class="nav-item">
                <i class="fa fa-diamond"></i><?=lang("Modules_&_Scripts")?>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <li>
          <a href="<?=cn('profile')?>" class="nav-link <?=(segment(1) == 'profile') ? 'active' : ''?>">
            <div class="nav-item">
              <i class="fa fa-user"></i><?=lang("Account")?>
            </div>
          </a>
        </li>

        <li>
          <a href="<?=cn("auth/logout")?>" class="nav-link">
            <div class="nav-item">
              <i class="fa fa-power-off"></i><?=lang("Sign_Out")?>
            </div>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<script>
// Collapsible Header Menu JavaScript
(function() {
  const headerMenu = document.getElementById('headerMenu');
  const hamburgerMenu = document.getElementById('hamburgerMenu');
  const navMenu = document.getElementById('navMenu');
  let lastScrollTop = 0;
  let isMenuOpen = false;

  // Hamburger menu toggle
  hamburgerMenu.addEventListener('click', function() {
    isMenuOpen = !isMenuOpen;
    hamburgerMenu.classList.toggle('active');
    navMenu.classList.toggle('active');
  });

  // Close menu when clicking on a link (mobile)
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth < 992 && isMenuOpen) {
        isMenuOpen = false;
        hamburgerMenu.classList.remove('active');
        navMenu.classList.remove('active');
      }
    });
  });

  // Header collapse on scroll
  let scrollTimeout;
  window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(function() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollTop > lastScrollTop && scrollTop > 100) {
        // Scrolling down
        headerMenu.classList.add('collapsed');
        if (isMenuOpen && window.innerWidth < 992) {
          isMenuOpen = false;
          hamburgerMenu.classList.remove('active');
          navMenu.classList.remove('active');
        }
      } else {
        // Scrolling up
        headerMenu.classList.remove('collapsed');
      }
      
      if (scrollTop > 50) {
        headerMenu.classList.add('scrolled');
      } else {
        headerMenu.classList.remove('scrolled');
      }
      
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, 100);
  });

  // Auto-expand menu on desktop
  function handleResize() {
    if (window.innerWidth >= 992) {
      navMenu.classList.add('active');
      hamburgerMenu.classList.remove('active');
      isMenuOpen = false;
    } else {
      navMenu.classList.remove('active');
    }
  }

  window.addEventListener('resize', handleResize);
  handleResize(); // Initial check
})();

// Currency switcher handler
$(document).ready(function() {
  $('#currencySelector').on('change', function() {
    var selectedCurrency = $(this).val();
    
    // Show loading overlay
    $('#page-overlay').addClass('visible incoming');
    
    // Send AJAX request to update currency
    $.ajax({
      url: '<?=cn("currencies/set_currency")?>',
      type: 'POST',
      data: {
        currency_code: selectedCurrency,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          // Reload the page to reflect new currency
          location.reload();
        } else {
          alert(response.message || 'Failed to change currency');
          $('#page-overlay').removeClass('visible incoming');
        }
      },
      error: function() {
        alert('An error occurred while changing currency');
        $('#page-overlay').removeClass('visible incoming');
      }
    });
  });
});
</script>

<!-- News Announcement Button -->
<?php if (get_option("enable_news_announcement") == 1): ?>
  <a href="<?=cn("news/ajax_notification")?>" style="position: fixed; bottom: 8px; right: 8px; font-size: 20px; padding-top: 3px; text-align: center; z-index: 10000000;" 
     data-toggle="tooltip" data-placement="bottom" title="News & Announcement" class="ajaxModal text-white">
    <div class="bell-fix">
      <i class="fa fa-bell"></i>
      <div class="test">
        <span class="nav-unread <?=(isset($_COOKIE["news_annoucement"]) && $_COOKIE["news_annoucement"] == "clicked") ? "" : "change_color"?>"></span>
      </div>
    </div>
  </a>
<?php endif; ?>

<!-- WhatsApp Button -->
<?php if (get_option("enable_news_announcement") == 1): ?>
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
<?php endif; ?>
