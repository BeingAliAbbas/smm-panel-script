<<<<<<< HEAD

=======
<style>
    .ff {
        background: #164964;
        border: 1px solid #00ffff;
        padding: 0.5rem;
        border-radius: 3px;
        margin-bottom: 1rem;
    }

</style>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98

<?php
  $option               = get_value($payment_params, 'option');
  $min_amount           = get_value($payment_params, 'min');
  $max_amount           = get_value($payment_params, 'max');
  $type                 = get_value($payment_params, 'type');
  $tnx_fee              = get_value($option, 'tnx_fee');
  $title                = get_value($option, 'title');
  $number               = get_value($option, 'number');
  $currency_rate_to_usd = get_value($option, 'rate_to_usd');
?>

<div class="add-funds-form-content">
  <form class="form actionAddFundsForm"
        action="<?= cn('easypaisa/create_payment'); ?>"
        method="POST">
    <div class="row">
      <div class="col-md-12">

        <!-- Logo -->
        <div class="form-group text-center">
          <img src="<?=BASE?>/assets/images/payments/easypaisa.png"
               alt="EasyPaisa Logo"
               style="width: 90%;">
          <p class="p-t-10">
            <small>
              <?= sprintf(
                    lang("you_can_deposit_funds_with_paypal_they_will_be_automaticly_added_into_your_account"),
                    'EasyPaisa'
                  ) ?>
            </small>
          </p>
        </div>

<<<<<<< HEAD

  
  <div class="ep-account-body">
    <div class="ep-info-row">
      <div class="ep-info-content">
        <span class="ep-info-label"><i class="fas fa-user"></i> ACCOUNT TITLE</span>
        <h3 class="ep-info-value" id="holderName"><?= $title; ?></h3>
      </div>
      <button class="ep-copy-btn" onclick="copyToClipboard('holderName')" title="Copy">
        <i class="far fa-copy"></i>
      </button>
    </div>
    
    <div class="ep-info-row">
      <div class="ep-info-content">
        <span class="ep-info-label"><i class="fas fa-mobile-alt"></i> ACCOUNT NUMBER</span>
        <h3 class="ep-info-value" id="accountNumber"><?= $number; ?></h3>
      </div>
      <button class="ep-copy-btn" onclick="copyToClipboard('accountNumber')" title="Copy">
        <i class="far fa-copy"></i>
      </button>
    </div>
  </div>
  
  <div class="ep-account-footer">
    <i class="fas fa-info-circle"></i> Send payment to above account & enter Transaction ID below
  </div>


<style>
.ep-account-card {
  background: #ffffff;
  border-radius:  12px;
  overflow:  hidden;
  max-width:  100%;
  margin: 20px auto;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #e5e9f2;
}

.ep-account-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom:  1px solid #e5e9f2;
}

.ep-logo {
  height: 28px;
  object-fit: contain;
}

.ep-verified-badge {
  background: #00b359;
  color: #fff;
  font-size: 10px;
  font-weight:  700;
  padding: 6px 12px;
  border-radius: 20px;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

. ep-account-body {
  padding: 24px 20px;
  background: #ffffff;
}

.ep-info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  margin-bottom: 12px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid #e5e9f2;
}

.ep-info-row:last-child {
  margin-bottom: 0;
}

.ep-info-content {
  flex: 1;
}

.ep-info-label {
  display:  block;
  color: #7c8db5;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 1px;
  margin-bottom: 6px;
  text-transform: uppercase;
}

.ep-info-label i {
  margin-right: 6px;
  color: #00b359;
}

.ep-info-value {
  color: #1e293b;
  font-size: 20px;
  font-weight: 700;
  margin: 0;
  font-family: 'Consolas', 'Courier New', monospace;
  letter-spacing: 1px;
}

.ep-copy-btn {
  background: #00b359;
  border: none;
  color: #fff;
  width: 40px;
  height: 40px;
  border-radius:  10px;
  cursor: pointer;
  transition: all 0.3s ease;
  display:  flex;
  align-items:  center;
  justify-content:  center;
  font-size:  15px;
  margin-left:  15px;
}

. ep-copy-btn:hover {
  background: #00cc66;
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(0, 179, 89, 0.3);
}

.ep-copy-btn:active {
  transform: scale(0.95);
}

.ep-account-footer {
  background: #f0fdf4;
  padding: 14px 20px;
  text-align: center;
  color: #64748b;
  font-size: 13px;
  border-top:  1px solid #e5e9f2;
}

. ep-account-footer i {
  color: #00b359;
  margin-right: 6px;
}

/* Responsive */
@media (max-width: 480px) {
  .ep-info-value {
    font-size: 16px;
  }
  
  .ep-account-header {
    flex-direction: column;
    gap: 12px;
    text-align: center;
  }
  
  .ep-info-row {
    padding: 14px;
  }
  
  .ep-copy-btn {
    width: 36px;
    height: 36px;
  }
}
</style>

<script>
function copyToClipboard(elementId) {
  const text = document.getElementById(elementId).innerText;
  navigator.clipboard. writeText(text).then(() => {
    const btn = event.target.closest('. ep-copy-btn');
    const originalIcon = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i>';
    btn.style.background = '#00cc66';
    
    setTimeout(() => {
      btn.innerHTML = originalIcon;
      btn.style.background = '#00b359';
    }, 1500);
  }).catch(err => {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
  });
}
</script>
=======
        <!-- Account Info -->
        <fieldset class="ff mt-1">
          <center>
            <div class="for-group text-center text-uppercase">
              <strong>EASYPAISA ACCOUNT TITLE:</strong>
              <h2>
                <span id="holderName"><?= $title; ?></span>
              </h2>
            </div>

            <div class="for-group text-center">
              <strong>EASYPAISA ACCOUNT NUMBER:</strong>
              <h2>
                <span id="accountNumber"><?= $number; ?></span>
                
              </h2>
            </div>
          </center>
        </fieldset>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98

        <!-- Amount -->
        <div class="form-group">
          <label><?= sprintf(lang("amount_usd"), 'PKR') ?></label>
          <input class="form-control square"
                 type="number"
                 name="amount"
                 min="<?= $min_amount; ?>"
                 max="<?= $max_amount; ?>"
                 placeholder="<?= $min_amount; ?>"
                 required>
        </div>

        <!-- Transaction ID -->
        <div class="form-group">
          <label>TRANSACTION ID (e.g. 0123456789)</label>
          <input class="form-control square"
                 type="number"
                 name="order_id"
                 placeholder="Enter your TRX ID"
                 required>
        </div>

        <!-- Notes -->
        <div class="form-group">
          <label><?= lang("note"); ?></label>
          <ul>
            <?php if ($tnx_fee > 0): ?>
              <li>
                <?= lang("transaction_fee"); ?>:
                <strong><?= $tnx_fee; ?>%</strong>
              </li>
            <?php endif; ?>
            <li>
              <?= lang("Minimal_payment"); ?>:
              <strong><?= $min_amount; ?> PKR</strong>
            </li>
            <?php if ($max_amount > 0): ?>
              <li>
                <?= lang("Maximal_payment"); ?>:
                <strong><?= $max_amount; ?> PKR</strong>
              </li>
            <?php endif; ?>
            <?php if ($currency_rate_to_usd > 1): ?>
              <li>
                <?= lang("currency_rate"); ?>:
                1 USD = <strong><?= $currency_rate_to_usd; ?></strong> PKR
              </li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Agreement Checkbox -->
        <div class="form-group">
          <label class="custom-control custom-checkbox">
            <input type="checkbox"
                   class="custom-control-input"
                   name="agree"
                   value="1"
                   required>
            <span class="custom-control-label text-uppercase">
              <strong>
                <?= lang("yes_i_understand_after_the_funds_added_i_will_not_ask_fraudulent_dispute_or_chargeback") ?>
              </strong>
            </span>
          </label>
        </div>

        <!-- Submit -->
        <div class="form-actions left">
          <input type="hidden" name="payment_id"
                 value="<?= $payment_id; ?>">
          <input type="hidden" name="payment_method"
                 value="<?= $type; ?>">
          <button type="submit"
                  class="btn round btn-primary btn-min-width mr-1 mb-1"
                  style="border-radius: 5px !important;
                         background-color: #04a9f4;
                         color: #fff;
                         min-width: 120px;
                         margin: 15px 5px 5px 0;">
            <?= lang("Pay"); ?>
          </button>
        </div>

      </div>
    </div>
  </form>
</div>

