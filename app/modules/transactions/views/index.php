<?php if (get_option('transactions_text','') != '') { ?>
<div class="col-sm-12 col-sm-12">
  <div class="row">
    <div class="card">
      <div class="card-body">
        <?=get_option('transactions_text','')?>
      </div>
    </div>
  </div>
</div>
<?php }?>

<?php if (get_code_part_by_position('transactions', 'top', '') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part_by_position('transactions', 'top', '')?>
    </div>
  </div>
</div>
<?php }?>

<?php if (get_role('admin') && !empty($stats)) { ?>
<!-- Admin Dashboard Summary Boxes -->
<div class="col-12">
  <div class="row">
    <div class="col-sm-6 col-lg-3">
      <div class="card card-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-auto">
              <span class="bg-primary text-white avatar">
                <i class="fe fe-activity"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Total_Transactions')?></div>
              <div class="text-muted"><?=$stats->total_transactions ?? 0?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-auto">
              <span class="bg-warning text-white avatar">
                <i class="fe fe-clock"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Pending')?></div>
              <div class="text-muted"><?=$stats->pending_count ?? 0?> (<?=get_option('currency_symbol', '$')?><?=number_format($stats->pending_amount ?? 0, 2)?>)</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-auto">
              <span class="bg-success text-white avatar">
                <i class="fe fe-check-circle"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Completed')?></div>
              <div class="text-muted"><?=$stats->completed_count ?? 0?> (<?=get_option('currency_symbol', '$')?><?=number_format($stats->completed_amount ?? 0, 2)?>)</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card card-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-auto">
              <span class="bg-info text-white avatar">
                <i class="fe fe-dollar-sign"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Total_Earnings')?></div>
              <div class="text-muted"><?=get_option('currency_symbol', '$')?><?=number_format($stats->total_earnings ?? 0, 2)?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Admin Filters -->
<div class="col-12">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><?=lang('Filters')?></h3>
      <div class="card-options">
        <a href="#" class="btn btn-sm btn-primary" onclick="$('#filterForm').toggle(); return false;">
          <i class="fe fe-filter"></i> <?=lang('Toggle_Filters')?>
        </a>
      </div>
    </div>
    <div class="card-body" id="filterForm" style="display: <?=!empty($filters) && array_filter($filters) ? 'block' : 'none'?>;">
      <form method="get" action="<?=cn($module)?>">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('User_Email')?></label>
              <input type="text" name="user_email" class="form-control" value="<?=htmlspecialchars($filters['user_email'] ?? '')?>" placeholder="<?=lang('User_Email')?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Date_From')?></label>
              <input type="date" name="date_from" class="form-control" value="<?=htmlspecialchars($filters['date_from'] ?? '')?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Date_To')?></label>
              <input type="date" name="date_to" class="form-control" value="<?=htmlspecialchars($filters['date_to'] ?? '')?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Payment_method')?></label>
              <input type="text" name="payment_method" class="form-control" value="<?=htmlspecialchars($filters['payment_method'] ?? '')?>" placeholder="e.g. PayPal, Stripe">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Status')?></label>
              <select name="status" class="form-control">
                <option value=""><?=lang('All')?></option>
                <option value="1" <?=isset($filters['status']) && $filters['status'] == '1' ? 'selected' : ''?>><?=lang('Paid')?></option>
                <option value="0" <?=isset($filters['status']) && $filters['status'] == '0' ? 'selected' : ''?>><?=lang('Pending')?></option>
                <option value="-1" <?=isset($filters['status']) && $filters['status'] == '-1' ? 'selected' : ''?>><?=lang('Cancelled')?></option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Amount_Min')?></label>
              <input type="number" step="0.01" name="amount_min" class="form-control" value="<?=htmlspecialchars($filters['amount_min'] ?? '')?>" placeholder="Min">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Amount_Max')?></label>
              <input type="number" step="0.01" name="amount_max" class="form-control" value="<?=htmlspecialchars($filters['amount_max'] ?? '')?>" placeholder="Max">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Sort_By')?></label>
              <select name="sort_by" class="form-control">
                <option value="created" <?=isset($filters['sort_by']) && $filters['sort_by'] == 'created' ? 'selected' : ''?>><?=lang('Date')?></option>
                <option value="amount" <?=isset($filters['sort_by']) && $filters['sort_by'] == 'amount' ? 'selected' : ''?>><?=lang('Amount')?></option>
                <option value="status" <?=isset($filters['sort_by']) && $filters['sort_by'] == 'status' ? 'selected' : ''?>><?=lang('Status')?></option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Sort_Order')?></label>
              <select name="sort_order" class="form-control">
                <option value="DESC" <?=isset($filters['sort_order']) && $filters['sort_order'] == 'DESC' ? 'selected' : ''?>><?=lang('Descending')?></option>
                <option value="ASC" <?=isset($filters['sort_order']) && $filters['sort_order'] == 'ASC' ? 'selected' : ''?>><?=lang('Ascending')?></option>
              </select>
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-group mb-0">
              <button type="submit" class="btn btn-primary me-2">
                <i class="fe fe-search"></i> <?=lang('Apply_Filters')?>
              </button>
              <a href="<?=cn($module)?>" class="btn btn-secondary">
                <i class="fe fe-x"></i> <?=lang('Clear')?>
              </a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

<style>
/* ---------- Custom Styling Add Funds Button + Card Header (matches dark theme) ---------- */
.transaction-card {
  background: #06141b;
  border: 1px solid #003a75;
  border-radius: 14px;
  box-shadow: 0 8px 18px -8px rgba(0,0,0,.6), 0 2px 6px -2px rgba(0,0,0,.5);
  overflow: hidden;
}
.transaction-card-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 22px 14px;
  background: #003a75 !important;
  border-bottom:1px solid #003a75;
}
.transaction-card-title{
  margin:0;
  font-size:20px;
  font-weight:600;
  letter-spacing:.5px;
  color:#e9f6ff;
  text-shadow:0 1px 2px rgba(0,0,0,.65);
}
.btn-add-funds{
  --c1:#00b4ff;
  --c2:#16d2ff;
  background:linear-gradient(90deg,var(--c1),var(--c2));
  color:#fff !important;
  font-weight:600;
  font-size:14px;
  padding:9px 20px;
  border-radius:9px;
  border:1px solid #22c3ff;
  display:inline-flex;
  align-items:center;
  gap:8px;
  text-decoration:none;
  position:relative;
  overflow:hidden;
  line-height:1.1;
  box-shadow:0 4px 12px -3px rgba(0,180,255,.45),0 2px 4px -2px rgba(0,0,0,.55);
  transition:background .35s,transform .25s,box-shadow .35s;
}
.btn-add-funds:before{
  content:"";
  position:absolute;
  top:0;left:-40%;
  width:40%;height:100%;
  background:linear-gradient(100deg,rgba(255,255,255,.28),rgba(255,255,255,0));
  transform:skewX(-20deg);
  transition: left .6s;
}
.btn-add-funds:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 26px -10px rgba(0,180,255,.6),0 6px 12px -6px rgba(0,0,0,.6);
  background:linear-gradient(90deg,#19c3ff,#47ddff);
  text-decoration:none;
}
.btn-add-funds:hover:before{
  left:110%;
}
.btn-add-funds i{
  font-size:16px;
  display:inline-block;
}
@media (max-width:680px){
  .transaction-card-header{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }
  .btn-add-funds{
    width:100%;
    justify-content:center;
  }
}
</style>

<div class="page-header d-md-none">
  <h1 class="page-title">
    <i class="fe fe-calendar" aria-hidden="true"> </i> 
    <?=lang("Transaction_logs")?>
  </h1>
</div>
<div class="row" id="result_ajaxSearch">
  <?php if (!empty($transactions)) { ?>

<div class="col-md-12 col-xl-12">
  <div class="transaction-card">
    <div class="transaction-card-header">
      <h3 class="transaction-card-title"><?= lang('Lists') ?></h3>

      <div class="d-flex gap-2">
        <?php if (get_role('admin')): ?>
          <?php
            // Build export URL with current filters
            $export_params = array_filter($filters ?? []);
            $export_url = cn($module.'/export_csv'.(count($export_params) ? '?'.http_build_query($export_params) : ''));
          ?>
          <a href="<?=$export_url?>" class="btn btn-success btn-sm">
            <i class="fe fe-download"></i>
            <span><?=lang('Export_CSV')?></span>
          </a>
        <?php endif; ?>

        <?php if (get_role('admin') || get_role('supporter')): ?>
          <a href="<?=cn($module.'/add_funds_manual')?>" class="ajaxModal btn-add-funds">
            <i class="fe fe-plus"></i>
            <span><?=lang('Add_Funds')?></span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered table-outline table-vcenter card-table">
        <thead>
          <tr>
            <th class="text-center w-1"><?=lang('No_')?></th>
            <?php if (!empty($columns)) {
              foreach ($columns as $key => $row) { ?>
                <th><?=$row?></th>
            <?php }} ?>
            <?php if (get_role("admin")) { ?>
              <th class="text-center"><?=lang('Action')?></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if (!empty($transactions)) {
              $i = 0;
              // Multi-currency support setup
              $current_currency = get_current_currency();
              $currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol", "$");
              $decimal_places = get_option('currency_decimal', 2);
              $decimalpoint = get_option('currency_decimal_separator', 'dot') == 'comma' ? ',' : '.';
              $separator = get_option('currency_thousand_separator', 'space') == 'space' ? ' ' : (get_option('currency_thousand_separator', 'comma') == 'comma' ? ',' : '.');
              
              foreach ($transactions as $key => $row) {
                $i++;
          ?>
          <tr class="tr_<?=$row->ids?>">
            <td><?=$i?></td>
            <?php if (get_role("admin")) { ?>
            <td>
              <div class="title"><?=get_field('general_users', ["id" => $row->uid], "email")?></div>
              <?php if ($row->payer_email) { ?>
                <small class="text-muted">Payer Email: <?=$row->payer_email?></small>
              <?php } ?>
            </td>
            <td>
              <?php
                switch ($row->transaction_id) {
                  case 'empty':
                    if ($row->type == 'manual') {
                      echo lang($row->transaction_id);
                    } else {
                      echo lang($row->transaction_id) . " " . lang("transaction_id_was_sent_to_your_email");
                    }
                    break;
                  default:
                    echo $row->transaction_id;
                    break;
                }
              ?>
            </td>
            <?php } ?>
            <td class="">
              <?php if (in_array(strtolower($row->type), ["bonus","manual","other"])) {
                echo ucfirst($row->type);
              } else { ?>
                <img class="payment" src="<?=BASE?>/assets/images/payments/<?=strtolower($row->type); ?>.png" alt="<?=$row->type?> icon">
              <?php } ?>
            </td>
            <td><?= $currency_symbol . currency_format(convert_currency($row->amount), $decimal_places, $decimalpoint, $separator) ?></td>
            <td><?= $currency_symbol . currency_format(convert_currency($row->txn_fee), $decimal_places, $decimalpoint, $separator) ?></td>
            <?php if (get_role("admin")) { ?>
              <td><?=$row->note;?></td>
            <?php } ?>
            <td><?=convert_timezone($row->created, 'user')?></td>
            <td>
              <?php
                switch ($row->status) {
                  case 1:
                    echo '<span class="badge badge-default">'.lang('Paid').'</span>';
                    break;
                  case 0:
                    echo '<span class="badge bg-warning text-dark">'.lang("waiting_for_buyer_funds").'</span>';
                    break;
                  case -1:
                    echo '<span class="badge bg-danger">'.lang('cancelled_timed_out').'</span>';
                    break;
                }
              ?>
            </td>
            <?php if (get_role("admin")) { ?>
            <td class="text-center">
              <a href="<?=cn("$module/update/".$row->ids)?>" class="btn btn-sm btn-primary ajaxModal me-1" title="<?=lang('Edit')?>">
                <i class="fe fe-edit"></i>
              </a>
              <?php if ($row->status == 0 && !empty($row->pay_token) && $row->pay_token_used == 0) { ?>
              <a href="<?=cn("transactions/pay/".$row->pay_token)?>" 
                 class="btn btn-sm btn-success quick-pay-btn" 
                 title="Quick Pay Approval"
                 data-transaction-id="<?=htmlspecialchars($row->transaction_id)?>"
                 data-amount="<?=htmlspecialchars($row->amount)?>"
                 onclick="return confirmQuickPay(event, this);">
                <i class="fe fe-check-circle"></i>
              </a>
              <?php } ?>
            </td>
            <?php } ?>
          </tr>
          <?php }} ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="col-md-12">
  <div class="float-end">
    <?=$links?>
  </div>
</div>

<?php } else {
  echo Modules::run("blocks/empty_data");
} ?>

<?php if (get_code_part_by_position('transactions', 'bottom', '') != '') { ?>
<div class="col-sm-12">
  <div class="row">
    <div class="col-sm-12">
      <?=get_code_part_by_position('transactions', 'bottom', '')?>
    </div>
  </div>
</div>
<?php }?>

</div>

<script>
function confirmQuickPay(event, element) {
  event.preventDefault();
  
  const transactionId = element.getAttribute('data-transaction-id');
  const amount = element.getAttribute('data-amount');
  const payUrl = element.getAttribute('href');
  
  // Use SweetAlert2 if available, otherwise use native confirm
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Approve Payment?',
      html: `
        <div style="text-align: left; padding: 10px;">
          <p><strong>Transaction ID:</strong> ${transactionId}</p>
          <p><strong>Amount:</strong> ${amount}</p>
          <p style="margin-top: 15px;">This will immediately approve the payment and credit the user's account.</p>
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fe fe-check"></i> Yes, Approve Payment',
      cancelButtonText: '<i class="fe fe-x"></i> Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = payUrl;
      }
    });
  } else {
    // Fallback to native confirm
    const confirmed = confirm(
      `Approve Payment?\n\n` +
      `Transaction ID: ${transactionId}\n` +
      `Amount: ${amount}\n\n` +
      `This will immediately approve the payment and credit the user's account.`
    );
    
    if (confirmed) {
      window.location.href = payUrl;
    }
  }
  
  return false;
}
</script>

<style>
.quick-pay-btn {
  position: relative;
  overflow: hidden;
}

.quick-pay-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.quick-pay-btn i {
  font-size: 14px;
}
</style>