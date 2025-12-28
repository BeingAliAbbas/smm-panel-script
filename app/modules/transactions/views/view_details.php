<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (empty($transaction)) {
	echo '<div class="alert alert-danger">Transaction not found</div>';
	return;
}

// Format currency
$current_currency = get_current_currency();
$currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol", "$");

// Format status
$status_badge = '';
switch ($transaction->status) {
	case 1:
		$status_badge = '<span class="badge bg-success">Paid</span>';
		break;
	case 0:
		$status_badge = '<span class="badge bg-warning">Pending</span>';
		break;
	case -1:
		$status_badge = '<span class="badge bg-danger">Cancelled</span>';
		break;
}

$user_name = trim(($transaction->first_name ?? '') . ' ' . ($transaction->last_name ?? ''));
?>

<form class="form-horizontal">
	<div class="modal-header bg-pantone">
		<h4 class="modal-title"><i class="fe fe-eye"></i> <?=lang('Transaction_Details')?></h4>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>
	</div>
	
	<div class="modal-body">
		<div class="row">
			<!-- Left Column -->
			<div class="col-md-6">
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Transaction_ID')?></label>
					<div class="form-control-plaintext">
						<code><?=htmlspecialchars($transaction->transaction_id ?? 'N/A')?></code>
					</div>
				</div>
				
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Status')?></label>
					<div class="form-control-plaintext">
						<?=$status_badge?>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Amount')?></label>
					<div class="form-control-plaintext">
						<strong class="text-primary"><?=$currency_symbol?><?=number_format($transaction->amount ?? 0, 2)?></strong>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Transaction_Fee')?></label>
					<div class="form-control-plaintext">
						<?=$currency_symbol?><?=number_format($transaction->txn_fee ?? 0, 2)?>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Net_Amount')?></label>
					<div class="form-control-plaintext">
						<strong class="text-success"><?=$currency_symbol?><?=number_format(($transaction->amount - $transaction->txn_fee), 2)?></strong>
					</div>
				</div>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Payment_method')?></label>
					<div class="form-control-plaintext">
						<?php if (in_array(strtolower($transaction->type), ["bonus","manual","other"])) { ?>
							<span class="badge bg-info"><?=ucfirst($transaction->type)?></span>
						<?php } else { ?>
							<img class="payment" src="<?=BASE?>/assets/images/payments/<?=strtolower($transaction->type); ?>.png" alt="<?=$transaction->type?>" style="max-height: 24px;">
							<?=ucfirst($transaction->type)?>
						<?php } ?>
					</div>
				</div>
			</div>

			<!-- Right Column -->
			<div class="col-md-6">
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('User')?></label>
					<div class="form-control-plaintext">
						<?=$user_name ? $user_name : 'N/A'?><br>
						<small class="text-muted"><?=htmlspecialchars($transaction->email ?? '')?></small><br>
						<small class="text-muted">User ID: <?=$transaction->uid ?? 'N/A'?></small>
					</div>
				</div>

				<?php if (!empty($transaction->payer_email)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Payer_Email')?></label>
					<div class="form-control-plaintext">
						<?=htmlspecialchars($transaction->payer_email)?>
					</div>
				</div>
				<?php } ?>

				<?php if (!empty($transaction->whatsapp_number)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('WhatsApp_Number')?></label>
					<div class="form-control-plaintext">
						<?=htmlspecialchars($transaction->whatsapp_number)?>
					</div>
				</div>
				<?php } ?>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Created')?></label>
					<div class="form-control-plaintext">
						<?=convert_timezone($transaction->created ?? '', 'user')?>
					</div>
				</div>

				<?php if (!empty($transaction->note)) { ?>
				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Note')?></label>
					<div class="form-control-plaintext">
						<?=htmlspecialchars($transaction->note)?>
					</div>
				</div>
				<?php } ?>

				<div class="form-group">
					<label class="font-weight-bold"><?=lang('Record_ID')?></label>
					<div class="form-control-plaintext">
						<small class="text-muted"><?=htmlspecialchars($transaction->ids ?? '')?></small>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('Close')?></button>
		<a href="<?=cn($module.'/update/'.$transaction->ids)?>" class="btn btn-primary ajaxModal">
			<i class="fe fe-edit"></i> <?=lang('Edit_Transaction')?>
		</a>
	</div>
</form>
