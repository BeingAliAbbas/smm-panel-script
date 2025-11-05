<div class="card content">
  <div class="card-header" style="border: 0.1px solid #05d0a0; border-radius: 3.5px 3.5px 0px 0px; background: #05d0a0;">
    <h3 class="card-title"><i class="fe fe-dollar-sign"></i> <?=lang("Multi-Currency Management")?></h3>
    <div class="card-options">
      <button class="btn btn-sm btn-primary fetch-rates-btn" style="margin-right: 10px;">
        <i class="fe fe-refresh-cw"></i> <?=lang("Fetch Latest Rates")?>
      </button>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-info">
          <i class="fe fe-info"></i> Manage multiple currencies for your SMM panel. Users can switch between currencies in the sidebar. All amounts will be converted based on exchange rates.
        </div>
        
        <div class="alert alert-warning" style="display:none;" id="rate-fetch-info">
          <i class="fe fe-clock"></i> <span id="rate-fetch-message"></span>
        </div>
        
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-vcenter card-table">
            <thead>
              <tr>
                <th><?=lang("Code")?></th>
                <th><?=lang("Name")?></th>
                <th><?=lang("Symbol")?></th>
                <th><?=lang("Exchange Rate")?></th>
                <th><?=lang("Default")?></th>
                <th><?=lang("Status")?></th>
                <th><?=lang("Actions")?></th>
              </tr>
            </thead>
            <tbody>
              <?php
                // Get currencies from database
                $currencies = get_active_currencies();
                if (!empty($currencies)) {
                  foreach ($currencies as $currency) {
              ?>
              <tr>
                <td><strong><?=$currency->code?></strong></td>
                <td><?=$currency->name?></td>
                <td><?=$currency->symbol?></td>
                <td>
                  <input type="number" step="0.00000001" class="form-control form-control-sm exchange-rate" 
                         data-id="<?=$currency->id?>" value="<?=$currency->exchange_rate?>" style="width: 150px;">
                </td>
                <td>
                  <?php if ($currency->is_default) { ?>
                    <span class="badge badge-success"><?=lang("Default")?></span>
                  <?php } else { ?>
                    <button class="btn btn-sm btn-primary set-default" data-id="<?=$currency->id?>">
                      <?=lang("Set as Default")?>
                    </button>
                  <?php } ?>
                </td>
                <td>
                  <label class="custom-switch">
                    <input type="checkbox" class="custom-switch-input toggle-status" 
                           data-id="<?=$currency->id?>" <?=$currency->status ? 'checked' : ''?>>
                    <span class="custom-switch-indicator"></span>
                  </label>
                </td>
                <td>
                  <button class="btn btn-sm btn-success update-rate" data-id="<?=$currency->id?>">
                    <i class="fe fe-check"></i> <?=lang("Update")?>
                  </button>
                </td>
              </tr>
              <?php 
                  }
                } else {
              ?>
              <tr>
                <td colspan="7" class="text-center"><?=lang("No currencies found. Please run the multi-currency.sql migration.")?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          <h5><?=lang("Add New Currency")?></h5>
          <form class="actionForm" action="<?=cn("currencies/add_currency")?>" method="POST">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label><?=lang("Code")?></label>
                  <input type="text" name="code" class="form-control" required maxlength="10" placeholder="USD">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label><?=lang("Name")?></label>
                  <input type="text" name="name" class="form-control" required maxlength="100" placeholder="US Dollar">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label><?=lang("Symbol")?></label>
                  <input type="text" name="symbol" class="form-control" required maxlength="10" placeholder="$">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label><?=lang("Exchange Rate")?></label>
                  <input type="number" step="0.00000001" name="exchange_rate" class="form-control" required value="1">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fe fe-plus"></i> <?=lang("Add")?>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Update exchange rate
  $('.update-rate').on('click', function() {
    var id = $(this).data('id');
    var rate = $('.exchange-rate[data-id="' + id + '"]').val();
    
    $.ajax({
      url: '<?=cn("currencies/update_rate")?>',
      type: 'POST',
      data: {
        id: id,
        exchange_rate: rate,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          show_message(response.message, 'success');
        } else {
          show_message(response.message, 'error');
        }
      }
    });
  });
  
  // Set as default
  $('.set-default').on('click', function() {
    var id = $(this).data('id');
    
    $.ajax({
      url: '<?=cn("currencies/set_default")?>',
      type: 'POST',
      data: {
        id: id,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          location.reload();
        } else {
          show_message(response.message, 'error');
        }
      }
    });
  });
  
  // Toggle status
  $('.toggle-status').on('change', function() {
    var id = $(this).data('id');
    var status = $(this).is(':checked') ? 1 : 0;
    
    $.ajax({
      url: '<?=cn("currencies/toggle_status")?>',
      type: 'POST',
      data: {
        id: id,
        status: status,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status != 'success') {
          show_message(response.message, 'error');
        }
      }
    });
  });
  
  // Fetch latest rates from API
  $('.fetch-rates-btn').on('click', function() {
    var btn = $(this);
    var originalHtml = btn.html();
    
    // Disable button and show loading
    btn.prop('disabled', true);
    btn.html('<i class="fe fe-loader"></i> Fetching...');
    
    $.ajax({
      url: '<?=cn("currencies/fetch_rates")?>',
      type: 'POST',
      data: {
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      dataType: 'json',
      success: function(response) {
        if (response.status == 'success') {
          show_message(response.message, 'success');
          
          // Show info message
          $('#rate-fetch-message').text('Last updated: ' + response.data.last_update + ' - ' + response.data.updated_count + ' currencies updated');
          $('#rate-fetch-info').show();
          
          // Reload page after 2 seconds to show new rates
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else {
          show_message(response.message, 'error');
        }
        
        // Re-enable button
        btn.prop('disabled', false);
        btn.html(originalHtml);
      },
      error: function() {
        show_message('Failed to fetch exchange rates. Please try again.', 'error');
        btn.prop('disabled', false);
        btn.html(originalHtml);
      }
    });
  });
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
