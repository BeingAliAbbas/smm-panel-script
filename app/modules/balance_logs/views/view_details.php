<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (empty($balance_log)) {
	echo '<div class="alert alert-danger">Balance log not found</div>';
	return;
}

// Load balance_logs helper for formatting functions (format_balance_action_display, get_balance_action_class, is_balance_positive_action)
$this->load->helper('balance_logs');

// Format currency
$current_currency = get_current_currency();
$currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol", "$");

// Format action type
$action_display = format_balance_action_display($balance_log->action_type);
$badge_class = get_balance_action_class($balance_log->action_type);
$is_positive = is_balance_positive_action($balance_log->action_type);
$amount_class = $is_positive ? 'text-success' : 'text-danger';
$amount_prefix = $is_positive ? '+' : '-';

$user_name = trim(($balance_log->first_name ?? '') . ' ' . ($balance_log->last_name ?? ''));
?>

<form class="form-horizontal">
	<div class="modal-header bg-pantone">
		<h4 class="modal-title"><i class="fe fe-eye"></i> <?=lang('Balance_Log_Details')?></h4>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
	</div>
	
	<div class="modal-body">
		<div class="row">
			<!-- Left Column -->
			<div class="col-md-6">
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Action_Type')?></label>
					<div class="form-control-plaintext">
						<span class="badge <?=$badge_class?>"><?=$action_display?></span>
					</div>
				</div>
				
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Amount')?></label>
					<div class="form-control-plaintext">
						<strong class="<?=$amount_class?>"><?=$amount_prefix?><?=$currency_symbol?><?=number_format($balance_log->amount ?? 0, 2)?></strong>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Balance_Before')?></label>
					<div class="form-control-plaintext">
						<?=$currency_symbol?><?=number_format($balance_log->balance_before ?? 0, 2)?>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Balance_After')?></label>
					<div class="form-control-plaintext">
						<?=$currency_symbol?><?=number_format($balance_log->balance_after ?? 0, 2)?>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Net_Change')?></label>
					<div class="form-control-plaintext">
						<strong class="<?=$is_positive ? 'text-success' : 'text-danger'?>">
							<?=$is_positive ? '+' : '-'?><?=$currency_symbol?><?=number_format(abs($balance_log->balance_after - $balance_log->balance_before), 2)?>
						</strong>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Created')?></label>
					<div class="form-control-plaintext">
						<?=convert_timezone($balance_log->created ?? '', 'user')?>
					</div>
				</div>
			</div>

			<!-- Right Column -->
			<div class="col-md-6">
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('User')?></label>
					<div class="form-control-plaintext">
						<?=$user_name ? $user_name : 'N/A'?><br>
						<small class="text-muted"><?=htmlspecialchars($balance_log->email ?? '')?></small><br>
						<small class="text-muted">User ID: <?=$balance_log->uid ?? 'N/A'?></small>
					</div>
				</div>

				<?php if (!empty($balance_log->whatsapp_number)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('WhatsApp_Number')?></label>
					<div class="form-control-plaintext">
						<?=htmlspecialchars($balance_log->whatsapp_number)?>
					</div>
				</div>
				<?php } ?>

				<?php if (!empty($balance_log->description)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Description')?></label>
					<div class="form-control-plaintext">
						<?=htmlspecialchars($balance_log->description)?>
					</div>
				</div>
				<?php } ?>

				<?php if (!empty($balance_log->related_id)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Related_ID')?></label>
					<div class="form-control-plaintext">
						<code><?=htmlspecialchars($balance_log->related_id)?></code>
					</div>
				</div>
				<?php } ?>

				<?php if (!empty($balance_log->related_type)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Related_Type')?></label>
					<div class="form-control-plaintext">
						<span class="badge bg-secondary"><?=htmlspecialchars($balance_log->related_type)?></span>
					</div>
				</div>
				<?php } ?>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Record_ID')?></label>
					<div class="form-control-plaintext">
						<small class="text-muted"><?=htmlspecialchars($balance_log->ids ?? '')?></small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('Close')?></button>
	</div>
</form>
