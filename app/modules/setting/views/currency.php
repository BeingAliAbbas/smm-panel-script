
    <div class="card content">
      <div class="card-header" style="border: 0.1px solid #05d0a0; border-radius: 3.5px 3.5px 0px 0px; background: #05d0a0;">
        <h3 class="card-title"><i class="fe fe-dollar-sign"></i> <?=lang("currency_setting")?></h3>
      </div>
      <div class="card-body">
        <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
          <div class="row">
            <div class="col-md-12 col-lg-12">

              <h5 class="text-info"><i class="fe fe-link"></i> <?=lang("currency_setting")?></h5>
              <div class="form-group">
                <label class="form-label"><?=lang("currency_code")?></label>
                <small><?=lang("the_paypal_payments_only_supports_these_currencies")?></small>
                <select  name="currency_code" class="form-control square">
                  <?php 
                    $currency_codes = currency_codes();
                    if(!empty($currency_codes)){
                      foreach ($currency_codes as $key => $row) {
                  ?>
                  <option value="<?=$key?>" <?=(get_option("currency_code", "USD") == $key)? 'selected': ''?>> <?=$key." - ".$row?></option>
                  <?php }}else{?>
                  <option value="USD" selected> USD - United States dollar</option>
                  <?php }?>
                </select>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?=lang("currency_symbol")?></label>
                    <input class="form-control" name="currency_symbol" value="<?=get_option('currency_symbol',"$")?>">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?=lang("thousand_separator")?></label>
                    <select  name="currency_thousand_separator" class="form-control square">
                      <option value="dot" <?=(get_option('currency_thousand_separator', 'comma') == 'dot')? 'selected': ''?>> <?=lang("Dot")?></option>
                      <option value="comma" <?=(get_option('currency_thousand_separator', 'comma') == 'comma')? 'selected': ''?>> <?=lang("Comma")?></option>
                      <option value="space" <?=(get_option('currency_thousand_separator', 'comma') == 'space')? 'selected': ''?>> <?=lang("Space")?></option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?=lang("decimal_separator")?></label>
                    <select  name="currency_decimal_separator" class="form-control square">
                      <option value="dot" <?=(get_option('currency_decimal_separator', 'dot') == 'dot')? 'selected': ''?>> <?=lang("Dot")?></option>
                      <option value="comma" <?=(get_option('currency_decimal_separator', 'dot') == 'comma')? 'selected': ''?>> <?=lang("Comma")?></option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label><?=lang("currency_decimal_places")?></label>
                    <select  name="currency_decimal" class="form-control square">
                      <option value="0" <?=(get_option('currency_decimal', 2) == 0)? 'selected': ''?>> 0</option>
                      <option value="1" <?=(get_option('currency_decimal', 2) == 1)? 'selected': ''?>> 0.0</option>
                      <option value="2" <?=(get_option('currency_decimal', 2) == 2)? 'selected': ''?>> 0.00</option>
                      <option value="3" <?=(get_option('currency_decimal', 2) == 3)? 'selected': ''?>> 0.000</option>
                      <option value="4" <?=(get_option('currency_decimal', 2) == 4)? 'selected': ''?>> 0.0000</option>
                    </select>
                  </div>
                </div>

              </div>
              
              <h5 class="text-info"><i class="fe fe-link"></i> <?=lang("price_percentage_increase")?></h5>
              <div class="row">

                <div class="col-md-4">
                  <div class="form-group">
                    <label><?=lang("use_for_sync_and_bulk_add_services")?></label>
                    <select name="default_price_percentage_increase" class="form-control square">
                      <?php
                        for ($i = 0; $i <= 1000; $i++) {
                      ?>
                      <option value="<?=$i?>" <?=(get_option("default_price_percentage_increase", 30) == $i)? "selected" : ''?>><?=$i?>%</option>
                      <?php } ?>
                    </select>
                  </div>
                </div>  
                <div class="col-md-4">
                  <div class="form-group">
                    <label><?=sprintf(lang('auto_rounding_to_X_decimal_places'), "X")?></label>
                    <select name="auto_rounding_x_decimal_places" class="form-control square">
                      <?php
                        for ($i = 1; $i <= 4; $i++) {
                      ?>
                      <option value="<?=$i?>" <?=(get_option("auto_rounding_x_decimal_places", 2) == $i)? "selected" : ''?>><?=$i?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>   

              </div>

              <h5 class="text-info"><i class="fe fe-link"></i> <?=lang("auto_currency_converter")?></h5>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="custom-switch">
                      <input type="hidden" name="is_auto_currency_convert" value="0">
                      <input type="checkbox" name="is_auto_currency_convert" class="custom-switch-input" <?=(get_option("is_auto_currency_convert", 0) == 1) ? "checked" : ""?> value="1">
                      <span class="custom-switch-indicator"></span>
                      <span class="custom-switch-description"><?=lang("Active")?></span>
                    </label>
                  </div>
                  <div class="form-group">
                    <label class="form-label"><?=lang("currency_rate")?>
                      <small><?=lang("applying_when_you_fetch_sync_all_services_from_smm_providers")?></small></span>
                    </label>
                    <div class="input-group">
                      <span class="input-group-prepend">
                        <span class="input-group-text">1 USD =</span>
                      </span>
                      <input type="text" class="form-control text-right" name="new_currecry_rate" id="new_currecry_rate" value="<?=get_option('new_currecry_rate', 1)?>">
                      <span class="input-group-append">
                        <span class="input-group-text"><?=get_option("currency_code", "USD")?></span>
                      </span>
                    </div>
                    <small class="text-muted"><span class="text-danger">*</span> <?=lang("if_you_dont_want_to_change_currency_rate_then_leave_this_currency_rate_field_to_1")?></small>
                  </div>
                  
                  <div class="form-group">
                    <button type="button" class="btn btn-success" id="fetchExchangeRateBtn">
                      <i class="fe fe-download"></i> Fetch Current Exchange Rate (USD to <?=get_option("currency_code", "USD")?>)
                    </button>
                    <button type="button" class="btn btn-info ml-2" id="showCronUrlBtn">
                      <i class="fe fe-link"></i> Show Cron URL for Auto-Update
                    </button>
                  </div>
                  
                  <div class="alert alert-warning d-none" id="cronUrlBox">
                    <strong>Cron URL for Automatic Exchange Rate Updates:</strong><br>
                    <code id="cronUrlText"></code>
                    <button type="button" class="btn btn-sm btn-primary ml-2" id="copyCronUrlBtn">
                      <i class="fe fe-copy"></i> Copy
                    </button>
                    <br><small class="text-muted mt-2 d-block">Add this URL to your cron job to automatically update the exchange rate daily. Example: <code>0 0 * * * curl "URL"</code></small>
                  </div>
                </div>
              </div>
              
            </div> 
            <div class="col-md-8">
              <div class="form-footer">
                <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

<script>
$(document).ready(function() {
  // Fetch current exchange rate from API
  $('#fetchExchangeRateBtn').on('click', function() {
    var btn = $(this);
    var targetCurrency = '<?=get_option("currency_code", "USD")?>';
    
    if (targetCurrency === 'USD') {
      show_message('Exchange rate is not needed when target currency is USD', 'error');
      return;
    }
    
    btn.prop('disabled', true).html('<i class="fe fe-loader"></i> Fetching...');
    
    $.ajax({
      url: '<?=cn("setting/fetch_exchange_rate")?>',
      type: 'POST',
      data: {
        target_currency: targetCurrency,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        btn.prop('disabled', false).html('<i class="fe fe-download"></i> Fetch Current Exchange Rate (USD to ' + targetCurrency + ')');
        
        if (response.status == 'success') {
          // Update the rate field
          $('#new_currecry_rate').val(response.data.rate);
          show_message(response.message + ' (1 USD = ' + response.data.rate + ' ' + targetCurrency + ')', 'success');
        } else {
          show_message(response.message, 'error');
        }
      },
      error: function() {
        btn.prop('disabled', false).html('<i class="fe fe-download"></i> Fetch Current Exchange Rate (USD to ' + targetCurrency + ')');
        show_message('Failed to fetch exchange rate', 'error');
      }
    });
  });
  
  // Show cron URL
  $('#showCronUrlBtn').on('click', function() {
    var cronBox = $('#cronUrlBox');
    var baseUrl = '<?=base_url()?>';
    var targetCurrency = '<?=get_option("currency_code", "USD")?>';
    
    // Generate or get the cron token
    var token = '<?=get_option("exchange_rate_cron_token", "")?>';
    if (!token) {
      // Generate a random token if it doesn't exist
      token = generateRandomToken();
      // Save it via AJAX
      $.ajax({
        url: '<?=cn("setting/ajax_general_settings")?>',
        type: 'POST',
        data: {
          exchange_rate_cron_token: token,
          <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
        },
        dataType: 'json'
      });
    }
    
    var cronUrl = baseUrl + 'setting/cron_update_exchange_rate?token=' + token;
    
    $('#cronUrlText').text(cronUrl);
    cronBox.removeClass('d-none');
  });
  
  // Copy cron URL
  $('#copyCronUrlBtn').on('click', function() {
    var cronUrl = $('#cronUrlText').text();
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(cronUrl).then(function() {
        show_message('Cron URL copied to clipboard!', 'success');
      }).catch(function() {
        copyToClipboardFallback(cronUrl);
      });
    } else {
      copyToClipboardFallback(cronUrl);
    }
  });
  
  // Fallback function for older browsers
  function copyToClipboardFallback(text) {
    var tempInput = $('<input>');
    $('body').append(tempInput);
    tempInput.val(text).select();
    try {
      document.execCommand('copy');
      show_message('Cron URL copied to clipboard!', 'success');
    } catch (err) {
      show_message('Failed to copy. Please copy manually.', 'error');
    }
    tempInput.remove();
  }
  
  // Generate random token
  function generateRandomToken() {
    return Math.random().toString(36).substring(2) + Math.random().toString(36).substring(2);
  }
});

function show_message(message, type) {
  $.toast({
    heading: type == 'success' ? 'Success' : 'Error',
    text: message,
    position: 'top-right',
    loaderBg: type == 'success' ? '#5ba035' : '#c9302c',
    icon: type,
    hideAfter: 3000
  });
}
</script>
