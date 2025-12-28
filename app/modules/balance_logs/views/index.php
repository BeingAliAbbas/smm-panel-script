<style>
/* Custom Styling for Balance Logs Card */
.balance-logs-card {
  background: #06141b;
  border: 1px solid #0d3242;
  border-radius: 14px;
  box-shadow: 0 8px 18px -8px rgba(0,0,0,.6), 0 2px 6px -2px rgba(0,0,0,.5);
  overflow: hidden;
}
/* Enable horizontal scrolling for tables on all screen sizes */
.table-responsive {
  display: block;
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}
.table-responsive > .table {
  margin-bottom: 0;
}
.balance-logs-card-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:18px 22px 14px;
  background: #003a75;
  border-bottom:1px solid #0e3b4e;
}
.balance-logs-card-title{
  margin:0;
  font-size:20px;
  font-weight:600;
  letter-spacing:.5px;
  color:#fff !important;
  text-shadow:0 1px 2px rgba(0,0,0,.65);
}
.badge-action-deduction {
  background-color: #dc3545;
}
.badge-action-addition {
  background-color: #28a745;
}
.badge-action-refund {
  background-color: #17a2b8;
}
.badge-action-manual_add {
  background-color: #007bff;
}
.badge-action-manual_deduct {
  background-color: #fd7e14;
}
.amount-positive {
  color: #28a745;
  font-weight: 600;
}
.amount-negative {
  color: #dc3545;
  font-weight: 600;
}
</style>

<?php if (get_role('admin') || get_role('supporter')) { ?>
<!-- Admin Dashboard Summary Widgets -->
<?php if (!empty($stats)) { ?>
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
              <div class="font-weight-medium"><?=lang('Total_Logs')?></div>
              <div class="text-muted"><?=$stats->total_logs ?? 0?></div>
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
                <i class="fe fe-arrow-up"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Total_Credited')?></div>
              <div class="text-muted text-success">+<?=get_option('currency_symbol', '$')?><?=number_format($stats->total_credited ?? 0, 2)?> (<?=$stats->credit_count ?? 0?>)</div>
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
              <span class="bg-danger text-white avatar">
                <i class="fe fe-arrow-down"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Total_Debited')?></div>
              <div class="text-muted text-danger">-<?=get_option('currency_symbol', '$')?><?=number_format($stats->total_debited ?? 0, 2)?> (<?=$stats->debit_count ?? 0?>)</div>
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
                <i class="fe fe-trending-<?=$stats->net_change >= 0 ? 'up' : 'down'?>"></i>
              </span>
            </div>
            <div class="col">
              <div class="font-weight-medium"><?=lang('Net_Change')?></div>
              <div class="text-muted <?=$stats->net_change >= 0 ? 'text-success' : 'text-danger'?>"><?=$stats->net_change >= 0 ? '+' : ''?><?=get_option('currency_symbol', '$')?><?=number_format($stats->net_change ?? 0, 2)?></div>
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
              <label><?=lang('Action_Type')?></label>
              <select name="action_type" class="form-control">
                <option value=""><?=lang('All')?></option>
                <option value="addition" <?=isset($filters['action_type']) && $filters['action_type'] == 'addition' ? 'selected' : ''?>><?=lang('Addition')?></option>
                <option value="deduction" <?=isset($filters['action_type']) && $filters['action_type'] == 'deduction' ? 'selected' : ''?>><?=lang('Deduction')?></option>
                <option value="refund" <?=isset($filters['action_type']) && $filters['action_type'] == 'refund' ? 'selected' : ''?>><?=lang('Refund')?></option>
                <option value="manual_add" <?=isset($filters['action_type']) && $filters['action_type'] == 'manual_add' ? 'selected' : ''?>><?=lang('Manual_Add')?></option>
                <option value="manual_deduct" <?=isset($filters['action_type']) && $filters['action_type'] == 'manual_deduct' ? 'selected' : ''?>><?=lang('Manual_Deduct')?></option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?=lang('Related_Type')?></label>
              <input type="text" name="related_type" class="form-control" value="<?=htmlspecialchars($filters['related_type'] ?? '')?>" placeholder="e.g. order, transaction">
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
                <option value="action_type" <?=isset($filters['sort_by']) && $filters['sort_by'] == 'action_type' ? 'selected' : ''?>><?=lang('Action_Type')?></option>
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
<?php } ?>


<div class="page-header d-md-none">
  <h1 class="page-title">
    <i class="fe fe-activity" aria-hidden="true"> </i> 
    <?=lang("Balance_Logs")?>
  </h1>
  <?php if (get_role('admin') || get_role('supporter')): ?>
  <div class="page-options">
    <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-info">
      <i class="fe fe-activity"></i> <?=lang('View_Cron_Logs')?>
    </a>
  </div>
  <?php endif; ?>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if (!empty($balance_logs)) { ?>

<div class="col-md-12 col-xl-12">
  <div class="balance-logs-card">
    <div class="balance-logs-card-header">
      <h3 class="balance-logs-card-title"><?= lang('Balance_Change_History') ?></h3>
      
      <div class="d-flex align-items-center gap-2">
        <?php if (get_role('admin') || get_role('supporter')): ?>
          <?php
            // Build export URL with current filters
            $export_params = array_filter($filters ?? []);
            $export_url = cn($module.'/export_csv'.(count($export_params) ? '?'.http_build_query($export_params) : ''));
          ?>
          <a href="<?=$export_url?>" class="btn btn-success btn-sm">
            <i class="fe fe-download"></i> <?=lang('Export_CSV')?>
          </a>

          <a href="<?=cn($module.'/view_execution_logs')?>" class="btn btn-info btn-sm">
            <i class="fe fe-activity"></i> <?=lang('View_Cron_Logs')?>
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
            <?php if (get_role("admin") || get_role("supporter")) { ?>
              <th class="text-center"><?=lang('Action')?></th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if (!empty($balance_logs)) {
              $i = 0;
              $current_currency = get_current_currency();
              $currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol", '$');
              foreach ($balance_logs as $key => $row) {
                $i++;
                // Use helper functions for cleaner code
                $is_positive = is_balance_positive_action($row->action_type);
                $amount_class = $is_positive ? 'amount-positive' : 'amount-negative';
                $amount_prefix = $is_positive ? '+' : '-';
          ?>
          <tr class="tr_<?=$row->ids?>">
            <td class="text-center"><?=$i?></td>
            
            <?php if (get_role("admin") || get_role("supporter")) { ?>
            <td>
              <div class="title">
                <?php 
                  $user_name = trim($row->first_name . ' ' . $row->last_name);
                  echo $user_name ? $user_name : $row->email;
                ?>
              </div>
              <small class="text-muted">ID: <?=$row->uid?></small><br>
              <small class="text-muted"><?=$row->email?></small>
            </td>
            <?php } ?>
            
            <td>
              <?php
                $action_display = format_balance_action_display($row->action_type);
                $badge_class = get_balance_action_class($row->action_type);
              ?>
              <span class="badge <?=$badge_class?>"><?=$action_display?></span>
            </td>
            
            <td class="<?=$amount_class?>">
              <?=$amount_prefix . $currency_symbol . currency_format(convert_currency($row->amount), get_option('currency_decimal', 2))?>
            </td>
            
            <td>
              <?=$currency_symbol . currency_format(convert_currency($row->balance_before), get_option('currency_decimal', 2))?>
            </td>
            
            <td>
              <?=$currency_symbol . currency_format(convert_currency($row->balance_after), get_option('currency_decimal', 2))?>
            </td>
            
            <td><?=htmlspecialchars($row->description)?></td>
            
            <?php if (get_role("admin") || get_role("supporter")) { ?>
            <td>
              <?=$row->related_id ? htmlspecialchars($row->related_id) : '-'?>
            </td>
            
            <td>
              <?=$row->related_type ? htmlspecialchars($row->related_type) : '-'?>
            </td>
            <?php } ?>
            
            <td><?=convert_timezone($row->created, 'user')?></td>
            
            <?php if (get_role("admin") || get_role("supporter")) { ?>
            <td class="text-center">
              <a href="<?=cn($module.'/view_details/'.$row->ids)?>" class="btn btn-sm btn-info ajaxModal" title="<?=lang('View_Details')?>">
                <i class="fe fe-eye"></i>
              </a>
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
</div>