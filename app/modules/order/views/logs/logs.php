<?php if (get_option('orders_text','') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="card">
      <div class="card-body">
        <?=get_option('orders_text','')?>
      </div>
    </div>
  </div>
</div>
<?php }?>
<<<<<<< HEAD

<?php if (get_code_part_by_position('orders', 'top', '') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part_by_position('orders', 'top', '')?>
    </div>
  </div>
</div>
<?php }?>

<div class="container-fluid">
  <div class="search-box m-r-30 d-none d-lg-block">
      <?php
      if ( allowed_search_bar(segment(1)) || allowed_search_bar(segment(1)) ) {
=======
<div class="container-fluid">
  <div class="search-box m-r-30 d-none d-lg-block">
      <?php
      if ( allowed_search_bar(segment(1)) || allowed_search_bar(segment(2)) ) {
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        echo Modules::run("blocks/search_box");
      }
      ?>
  </div>
  <br>
  <div class="page-options d-flex">
      <ul class="list-inline mb-0 order_btn_group">
          <li class="list-inline-item">
              <a class="nav-link btn <?=($order_status == 'all') ? 'btn-info active1' : ''?>" href="<?=cn($module."/log/all")?>"><?=lang('All')?></a>
          </li>
          <?php 
          $status_array = order_status_array();
          if (!empty($status_array)) {
              foreach ($status_array as $row_status) {
                  if ((get_role('user')) && in_array($row_status, ['error'])) {
                      continue;
                  }
          ?>
          <li class="list-inline-item">
              <a class="nav-link btn <?=($order_status == $row_status) ? 'btn-info active1' : ''?>" href="<?=cn($module."/log/".$row_status)?>">
                  <?=order_status_title($row_status)?>
                  <?php
                      if (in_array($row_status, ['error']) && isset($number_error_orders)) {
<<<<<<< HEAD
                          echo '<span class="badge bg-danger badge-error-orders">'.$number_error_orders.'</span>';
=======
                          echo '<span class="badge badge-danger badge-error-orders">'.$number_error_orders.'</span>';
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                      }
                  ?>
              </a>
          </li>
          <?php }}?>
      </ul>
  </div>

<?php if ($order_status == 'all' && get_role("admin")): ?>
  <!-- Total Profit Card -->
  <div id="total-profit-card" class="card summary-card">
<<<<<<< HEAD
    <h4><?=lang("Total Profit")?> <small style="font-size:1rem; color: #000000ff;">(100 orders)</small></h4>
=======
    <h4><?=lang("Total Profit")?> <small style="font-size:1rem; color:#bfc9d1;">(100 orders)</small></h4>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
    <p id="total-profit-value" class="summary-value" style="color: #27ae60; font-size:2rem; font-weight:bold; margin:0;"></p>
  </div>

  <!-- Total Sell Card -->
  <div id="total-sell-card" class="card summary-card">
<<<<<<< HEAD
    <h4><?=lang("Total Sell")?> <small style="font-size:1rem; color: #000;">(100 orders)</small></h4>
=======
    <h4><?=lang("Total Sell")?> <small style="font-size:1rem; color:#bfc9d1;">(100 orders)</small></h4>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
    <p id="total-sell-value" class="summary-value" style="color: #f39c12; font-size:2rem; font-weight:bold; margin:0;"></p>
  </div>

  <!-- Profit Today Card -->
  <div id="profit-today-card" class="card summary-card">
<<<<<<< HEAD
    <h4><?=lang("Profit Today")?> <small style="font-size:1rem; color:#000;">(100 orders)</small></h4>
=======
    <h4><?=lang("Profit Today")?> <small style="font-size:1rem; color:#bfc9d1;">(100 orders)</small></h4>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
    <p id="profit-today-value" class="summary-value" style="color: #2980b9; font-size:2rem; font-weight:bold; margin:0;"></p>
  </div>
<?php endif; ?>

<<<<<<< HEAD
<?php if ($order_status == 'error' && get_role("admin") && isset($total_provider_price)): ?>
  <!-- Total Provider Price Card for Error Orders -->
  <div id="total-provider-price-card" class="card summary-card">
    <h4><?=lang("Total Provider Price")?> <small style="font-size:1rem; color: #000;">(Error Orders)</small></h4>
    <p id="total-provider-price-value" class="summary-value" style="color: #e74c3c; font-size:2rem; font-weight:bold; margin:0;">
      $<?= number_format($total_provider_price, 4) ?>
    </p>
  </div>
<?php endif; ?>

<?php 
// Include API Provider Balance Card (shown on all order status pages for admin)
if (get_role("admin")) {
  include(APPPATH . 'modules/order/views/logs/api_provider_balance_card.php');
}
?>

  <br><br>
  <?php if ($order_status == 'error') { ?>
    <div class="dropdown d-inline-block">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
=======
  <br><br>
  <?php if ($order_status == 'error') { ?>
    <div class="dropdown d-inline-block">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        Bulk Actions
      </button>
      <div class="dropdown-menu" aria-labelledby="bulkActionDropdown">
        <a class="dropdown-item" href="<?=cn("$module/change_status/resend_order/bulk")?>">Resend All</a>
        <a class="dropdown-item" href="<?=cn("$module/change_status/cancel_order/bulk")?>">Cancel All</a>
      </div>
    </div>
  <?php } ?>

  <div class="row" id="result_ajaxSearch">
    <?php if(!empty($order_logs)){ ?>
      <div class="col-md-12">
        <div>
<<<<<<< HEAD
          <div class="card-header" style="border: 0.1px solid #003a75; border-radius: 3.5px 3.5px 0px 0px; background: #003a75 !important;">
              <h3 class="card-title" style="color: #ffffffff !important;"><?=lang("Your Orders")?></h3>
              <div class="card-options">
                  <?php if (get_role("admin")) { ?>
                  <div class="dropdown">
                      <button type="button" class="btn btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" style="background-color: #06324e; color: #fff; border: none;">
                          <i class="fas fa-clone me-2"></i> Copy Options
                      </button>
                      <div class="dropdown-menu" style="background-color: #051d2f!important; border: 1px solid #04a9f4;">
                          <a class="dropdown-item" href="#" onclick="copyAllApiOrderIds()" style="background-color: #051d2f!important; color: #fff !important;">
                              <i class="fas fa-copy me-2"></i> Copy All API Order IDs
=======
          <div class="card-header bg-gradient text-white">
              <h3 class="card-title"><?=lang("Your Orders")?></h3>
              <div class="card-options">
                  <?php if (get_role("admin")) { ?>
                  <div class="dropdown">
                      <button type="button" class="btn btn-outline-info dropdown-toggle" data-toggle="dropdown" style="background-color: #06324e; color: #fff; border: none;">
                          <i class="fa fa-clone mr-2"></i> Copy Options
                      </button>
                      <div class="dropdown-menu" style="background-color: #051d2f!important; border: 1px solid #04a9f4;">
                          <a class="dropdown-item" href="#" onclick="copyAllApiOrderIds()" style="background-color: #051d2f!important; color: #fff !important;">
                              <i class="fa fa-copy mr-2"></i> Copy All API Order IDs
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                          </a>
                      </div>
                  </div>
                  <?php } ?>
              </div>
          </div>
<<<<<<< HEAD
          <div class="table-responsive" style="border-radius: 0px;">
=======
          <div class="table-responsive">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            <table class="table table-hover table-bordered table-vcenter card-table">
              <thead style="color: #fff;">
                <tr>
                  <?php if (!empty($columns)) {
                    foreach ($columns as $key => $row) {
                  ?>
                  <th><?=$row?></th>
                  <?php }}?>
                </tr>
              </thead>
              <tbody>
                <?php
                $current_currency = get_current_currency();
                $currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol","");
                $decimal_places = get_option('currency_decimal', 2);
                $decimalpoint = get_option('currency_decimal_separator', 'dot') == 'comma' ? ',' : '.';
                $separator = get_option('currency_thousand_separator', 'space') == 'space' ? ' ' : (get_option('currency_thousand_separator', 'comma') == 'comma' ? ',' : '.');
                $usd_to_pkr_rate = 280; // Example conversion rate

                $total_profit = 0;
                $total_sell = 0;
                $profit_today = 0;
                $today = date('Y-m-d'); // Get today's date

                foreach ($order_logs as $key => $row) {
                  $profit = 0;
                  $provider_charge_in_pkr = isset($row->formal_charge) ? $row->formal_charge * $usd_to_pkr_rate : 0;

                  if (get_role("admin") && is_numeric($row->charge) && is_numeric($row->formal_charge) && $row->charge > 0 && $row->formal_charge > 0) {
                      $profit = $row->charge - $provider_charge_in_pkr;
                      $total_profit += $profit;
                  }
                  if (is_numeric($row->charge) && $row->charge > 0) {
                      $total_sell += $row->charge;
                  }
                  // Today's profit
                  if (get_role("admin") && !empty($row->created) && substr($row->created,0,10) == $today && is_numeric($row->charge) && is_numeric($row->formal_charge) && $row->charge > 0 && $row->formal_charge > 0) {
                      $profit_today += $profit;
                  }
                ?>
                <!-- Table Row -->
                <tr style="color: #fff;" class="tr_<?=$row->ids?>">
                  <!-- Order ID with copy button -->
                  <td style="color: #fff;">
                    <span id="orderId_<?=$row->id?>"><?=$row->id?></span>
                    <button onclick="copyToClipboard('orderId_<?=$row->id?>')" style="background:none; border:none; cursor:pointer;">
<<<<<<< HEAD
                      <i class="fas fa-copy" style="color: #2ecc71; margin-left: 8px;"></i>
=======
                      <i class="fa fa-copy" style="color: #2ecc71; margin-left: 8px;"></i>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    </button>
                  </td>
                  <?php if (get_role("admin") || get_role("supporter")) { ?>
                  <td class="text-center" style="color: #fff;">
                    <span id="apiOrderId_<?=$row->id?>">
                        <?= ($row->api_order_id == 0 || $row->api_order_id == -1) ? "" : $row->api_order_id ?>
                    </span>
                    <?php if ($row->api_order_id != 0 && $row->api_order_id != -1): ?>
                      <button onclick="copyToClipboard('apiOrderId_<?=$row->id?>')" style="background:none; border:none; cursor:pointer;">
<<<<<<< HEAD
                        <i class="fas fa-copy" style="color: #2ecc71; margin-left: 8px;"></i>
=======
                        <i class="fa fa-copy" style="color: #2ecc71; margin-left: 8px;"></i>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                      </button>
                    <?php endif; ?>
                  </td>
                  <td style="color: #fff;"><?=$row->user_email?></td>
                  <?php } ?>
                  <!-- Service Info -->
                  <td>
                    <div class="title" style="color: #fff; font-size: 13px; font-weight: 400;">
                      <h6><?=$row->service_id." - ".$row->service_name?></h6>
                    </div>
                    <div style="margin-left:5px !important">
                      <small>
                        <ul style="color: #fff; margin:0px">
                          <?php if (get_role("admin")) { ?>
                          <li style="color: #fff;">
                            <?php echo lang("Type")?>: 
                            <?php
                              if (!empty($row->api_service_id)) {
                                if ($row->type == 'api') {
                                  echo $row->api_name." (ID".$row->api_service_id. ")" . ' <span class="badge badge-default">API</span>';
                                } else {
                                  echo $row->api_name." (ID".$row->api_service_id. ")";
                                }
                              } else {
                                echo lang("Manual");
                              }
                            ?>
                          </li>
                          <?php } ?>
                          <li style="color: #fff;"><?=lang("Link")?>:
                            <?php
                              if (filter_var($row->link, FILTER_VALIDATE_URL)) {
                                echo '<a class="text-blue" href="'.$row->link.'" target="_blank">'.truncate_string($row->link, 60).'</a>'; 
                              } else {
                                echo truncate_string($row->link, 60);
                              }
                            ?>
                          </li> 
                          <li><?=lang("Quantity")?>: <?=$row->quantity?></li>
                          <li><?=lang("Charge")?>: <?= $currency_symbol . currency_format(convert_currency($row->charge), $decimal_places, $decimalpoint, $separator) ?></li>
                          <?php if (get_role("admin")): ?>
                          <li><?=lang("Provider Charge")?>: 
                            <small style="color: #3498db; font-size: 12px;">
                              <?= (isset($provider_charge_in_pkr) && $provider_charge_in_pkr > 0) 
                                  ? '$' . $row->formal_charge . ' (' . $currency_symbol . currency_format(convert_currency($provider_charge_in_pkr), $decimal_places, $decimalpoint, $separator) . ')' 
                                  : lang("No charge available"); ?>
                            </small>
                          </li>
                          <?php endif; ?>
                          <li><?=lang("Start_counter")?>: <?=(!empty($row->start_counter)) ? $row->start_counter : lang("N/A")?></li>
                          <li><?=lang("Remains")?>: <?=(!empty($row->remains)) ? $row->remains : lang("N/A")?></li>
                          <?php
                            $mention_list = get_list_custom_mention($row);
                            if ($mention_list && $mention_list->exists_list) {
                          ?>
                          <li>
                            <a href="<?=cn($module.'/ajax_show_list_custom_mention/'.$row->ids)?>" class="btn btn-gray btn-sm ajaxModal btn-show-custom-mention"><?=$mention_list->title?></a>
                          </li>
                          <?php } ?>
                        </ul>
                      </small>
                    </div>
                  </td>
                  <?php if (get_role("admin")): ?>
                  <td><?= $currency_symbol . currency_format(convert_currency($profit), $decimal_places, $decimalpoint, $separator); ?></td>
                  <?php endif; ?>
                  <td style="color: #fff;"><?=convert_timezone($row->created, "user")?></td>
<<<<<<< HEAD
                  <td class="order-status-cell">
=======
                  <td>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    <?php
                    $order_status = $row->status;
                    if (!get_role('admin') && in_array($order_status, ['fail', 'error'])) {
                        $order_status = 'processing';
                    }
                    $btn_background = ($order_status == "pending" || $order_status == "processing") ? "btn-info"
                                  : ($order_status == "inprogress" ? "btn-orange"
                                  : ($order_status == "completed" ? "btn-blue" : "btn-danger"));
                    ?>
                    <span class="btn round btn-sm <?=$btn_background?>"><?=order_status_title($order_status)?></span>
                    <?php if ($order_status == "completed"): ?>
                      <?php
                      $user_whatsapp = $this->db->select('whatsapp_number')->from('general_users')->where('id', $row->uid)->get()->row();
                      $notification_status = $this->db->select('is_notified')->from('order_notifications')->where('order_id', $row->id)->get()->row();
                      $is_notified = isset($notification_status->is_notified) ? $notification_status->is_notified : 0;
                      if ($user_whatsapp && $user_whatsapp->whatsapp_number):
                        $whatsapp_number = ltrim($user_whatsapp->whatsapp_number, '+');
                      ?>
                        <?php if (get_role('admin')): ?>
                          <?php if (!$is_notified): ?>
                            <a href="<?=base_url("order/send_whatsapp_notification?whatsapp_number={$whatsapp_number}&order_id={$row->id}&service_name=" . urlencode($row->service_name))?>" 
                            class="btn btn-sm btn-primary mt-2" id="notify-btn-<?=$row->id?>">
                              <i class="fab fa-whatsapp"></i> Notify User
                            </a>
                          <?php else: ?>
                            <span class="btn btn-sm btn-success mt-2">Notification Sent</span>
                          <?php endif; ?>
                        <?php endif; ?>
                      <?php endif; ?>
                    <?php endif; ?>
<<<<<<< HEAD
                    
                    <?php
                    // Show cancel button for non-admin users in the status column
                    if (!get_role("admin") && !get_role("supporter")) {
                      // Show cancel button for cancellable orders (pending, processing, inprogress)
                      // Only show if order is less than 10 minutes old
                      if (in_array($row->status, ['pending', 'processing', 'inprogress'])) {
                        // Calculate if order is within 10 minutes
                        $order_created_timestamp = strtotime($row->created);
                        $current_timestamp = time();
                        $time_diff_minutes = ($current_timestamp - $order_created_timestamp) / 60;
                        
                        if ($time_diff_minutes <= 10) {
                          // Check if cancel request was already sent
                          $cancel_requested = !empty($row->note) && strpos($row->note, 'Cancel request sent') !== false;
                          
                          if ($cancel_requested) {
                            // Show disabled "Cancel Requested" button
                    ?>
                            <br>
                            <button 
                              class="btn btn-secondary btn-sm mt-2" 
                              disabled
                            >
                              <i class="fe fe-clock"></i> Cancel Requested
                            </button>
                    <?php
                          } else {
                            // Show active cancel button
                    ?>
                            <br>
                            <button 
                              class="btn btn-danger btn-sm cancel-order-btn mt-2" 
                              data-order-ids="<?=htmlspecialchars($row->ids, ENT_QUOTES, 'UTF-8')?>"
                              data-order-id="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8')?>"
                              id="cancel-btn-<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8')?>"
                            >
                              <i class="fe fe-x-circle"></i> Cancel Order
                            </button>
                    <?php 
                          }
                        }
                      }
                    }
                    ?>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                  </td>
                  <?php if (get_role("admin") || get_role("supporter")) { ?>
                  <td class="text-red"><?=!empty($row->note)? $row->note : ""?></td>
                  <td class="text-center">
                    <a href="<?=cn("$module/log_update/".$row->ids)?>" class="ajaxModal">
                      <i class="btn btn-info fe fe-edit"> <?=lang('Edit')?></i>
                    </a>
                    <br><br>
                    <?php
                    if (get_role('admin') && $row->status == 'error') {
                    ?>
                      <!-- Single order resend as anchor (no JS, follows original) -->
                      <a href="<?=cn("$module/change_status/resend_order/".$row->ids)?>" class="">
                        <i class="btn btn-success fe fe-send"> Resend</i>
                      </a>
                      <br><br>
                    <?php } ?>
<<<<<<< HEAD
                    
                    <?php
                    // Show cancel button for cancellable orders (pending, processing, inprogress)
                    // Only show if order is less than 10 minutes old
                    if (in_array($row->status, ['pending', 'processing', 'inprogress'])) {
                      // Calculate if order is within 10 minutes
                      $order_created_timestamp = strtotime($row->created);
                      $current_timestamp = time();
                      $time_diff_minutes = ($current_timestamp - $order_created_timestamp) / 60;
                      
                      if ($time_diff_minutes <= 10) {
                        // Check if cancel request was already sent
                        $cancel_requested = !empty($row->note) && strpos($row->note, 'Cancel request sent') !== false;
                        
                        if ($cancel_requested) {
                          // Show disabled "Cancel Requested" button
                    ?>
                          <button 
                            class="btn btn-secondary btn-sm" 
                            disabled
                          >
                            <i class="fe fe-clock"></i> Cancel Requested
                          </button>
                    <?php
                        } else {
                          // Show active cancel button
                    ?>
                          <button 
                            class="btn btn-danger btn-sm cancel-order-btn" 
                            data-order-ids="<?=htmlspecialchars($row->ids, ENT_QUOTES, 'UTF-8')?>"
                            data-order-id="<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8')?>"
                            id="cancel-btn-<?=htmlspecialchars($row->id, ENT_QUOTES, 'UTF-8')?>"
                          >
                            <i class="fe fe-x-circle"></i> Cancel Order
                          </button>
                    <?php 
                        }
                      }
                    } 
                    ?>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                  </td>
                  <?php } ?>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-12">
<<<<<<< HEAD
        <div class="float-end">
=======
        <div class="float-right">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          <?=$links?>
        </div>
      </div>
    <?php } else {
      echo Modules::run("blocks/empty_data");
    }?>
  </div>
</div>

<<<<<<< HEAD
<?php if (get_code_part_by_position('orders', 'bottom', '') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part_by_position('orders', 'bottom', '')?>
    </div>
  </div>
</div>
<?php }?>

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
<!-- Scripts -->
<script>
$(document).ready(function() {
  $('.sidebar-toggle').click(function() {
    $('.sidebar').toggleClass('active');
  });

  // Display profit cards
  let totalProfit = <?=json_encode(round($total_profit, 1))?>;
  let totalSell = <?=json_encode(round($total_sell, 1))?>;
  let profitToday = <?=json_encode(round($profit_today, 1))?>;

  $('#total-profit-value').text(totalProfit + ' PKR');
  $('#total-sell-value').text(totalSell + ' PKR');
  $('#profit-today-value').text(profitToday + ' PKR');

  // No JS required for single order resend (uses anchor)
<<<<<<< HEAD

  // Handle cancel order button click
  $('.cancel-order-btn').click(function() {
    var button = $(this);
    var orderIds = button.data('order-ids');
    var orderId = button.data('order-id');
    
    // Confirm cancellation
    if (!confirm('Are you sure you want to cancel this order?')) {
      return;
    }
    
    // Disable button and show loading state
    button.prop('disabled', true);
    var originalHtml = button.html();
    button.html('<i class="fa fa-spinner fa-spin"></i> Requesting...');
    button.css('background-color', '#ffa500');
    
    // Send AJAX request to cancel order
    $.ajax({
      url: '<?=cn("order/ajax_cancel_order")?>',
      type: 'POST',
      dataType: 'json',
      data: {
        order_ids: orderIds,
        <?=$this->security->get_csrf_token_name()?>: '<?=$this->security->get_csrf_hash()?>'
      },
      success: function(response) {
        if (response.status === 'success') {
          // Show success message
          alert(response.message || 'Cancellation request sent!');
          
          // Update button to show cancel requested state (NOT the order status)
          button.html('<i class="fe fe-clock"></i> Cancel Requested');
          button.removeClass('btn-danger').addClass('btn-secondary');
          button.css('background-color', '#6c757d');
          button.prop('disabled', true);
          
          // Do NOT update order status - it will be updated automatically by provider
          // Just reload after 2 seconds
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else {
          // Show error message
          alert(response.message || 'Failed to cancel order. Please try again.');
          
          // Re-enable button
          button.prop('disabled', false);
          button.html(originalHtml);
          button.css('background-color', '#dc3545');
        }
      },
      error: function(xhr, status, error) {
        // Show error message
        alert('An error occurred while cancelling the order. Please try again.');
        
        // Re-enable button
        button.prop('disabled', false);
        button.html(originalHtml);
        button.css('background-color', '#dc3545');
      }
    });
  });
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
});

// Copy single order id to clipboard
function copyToClipboard(elementId) {
  var textToCopy = document.getElementById(elementId).innerText;
  var tempInput = document.createElement("textarea");
  tempInput.value = textToCopy;
  document.body.appendChild(tempInput);
  tempInput.select();
  tempInput.setSelectionRange(0, 99999);
  document.execCommand("copy");
  document.body.removeChild(tempInput);
  alert("Order ID copied: " + textToCopy);
}

// Copy all API order IDs to clipboard
function copyAllApiOrderIds() {
  let apiOrderIds = [];
  document.querySelectorAll('[id^="apiOrderId_"]').forEach(function(element) {
    if (element.textContent.trim() !== "") {
      apiOrderIds.push(element.textContent.trim());
    }
  });
  if (apiOrderIds.length > 0) {
    let formattedText = 'order ids\n' + apiOrderIds.join(',\n');
    copyTextToClipboard(formattedText);
    alert("API Order IDs copied to clipboard!");
  } else {
    alert("No API Order IDs found to copy.");
  }
}
function copyTextToClipboard(text) {
  const textarea = document.createElement('textarea');
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);
}
</script>

<style>
.summary-card {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  text-align: center;
}
.summary-value {
  font-size: 18px;
  font-weight: bold;
}
<<<<<<< HEAD
li {
  color: #000 !important; /* black text */
}

/* Cancel order button styles */
.cancel-order-btn {
  margin-top: 10px;
  transition: all 0.3s ease;
}

.cancel-order-btn:hover:not(:disabled) {
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.cancel-order-btn:disabled {
  cursor: not-allowed;
  opacity: 0.7;
}

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
</style>