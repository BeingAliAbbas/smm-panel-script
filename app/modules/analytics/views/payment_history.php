<style>
.filter-card {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.payment-table {
	background: white;
	border-radius: 10px;
	padding: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid">
	<div class="page-header">
		<h1 class="page-title">Payment History</h1>
		<div class="page-options">
			<a href="<?=cn('analytics')?>" class="btn btn-secondary">
				<i class="fe fe-arrow-left"></i> Back to Dashboard
			</a>
		</div>
	</div>

	<!-- Filters -->
	<div class="row">
		<div class="col-md-12">
			<div class="filter-card">
				<h4>Filter Payments</h4>
				<form method="get" action="<?=cn('analytics/payment_history')?>">
					<div class="row">
						<div class="col-md-3">
							<label>Status</label>
							<select name="status" class="form-control">
								<option value="">All</option>
								<option value="1" <?=$status_filter == '1' ? 'selected' : ''?>>Completed</option>
								<option value="0" <?=$status_filter == '0' ? 'selected' : ''?>>Pending</option>
							</select>
						</div>
						<div class="col-md-3">
							<label>Date From</label>
							<input type="date" name="date_from" class="form-control" value="<?=htmlspecialchars($date_from_filter)?>">
						</div>
						<div class="col-md-3">
							<label>Date To</label>
							<input type="date" name="date_to" class="form-control" value="<?=htmlspecialchars($date_to_filter)?>">
						</div>
						<div class="col-md-3">
							<label>Payment Method</label>
							<select name="payment_method" class="form-control">
								<option value="">All Methods</option>
								<?php if (!empty($payment_methods)) {
									foreach ($payment_methods as $method) { ?>
								<option value="<?=htmlspecialchars($method->type)?>" <?=$payment_method_filter == $method->type ? 'selected' : ''?>>
									<?=htmlspecialchars($method->type)?>
								</option>
								<?php }
								} ?>
							</select>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-md-12">
							<button type="submit" class="btn btn-primary">
								<i class="fe fe-filter"></i> Apply Filters
							</button>
							<a href="<?=cn('analytics/payment_history')?>" class="btn btn-secondary">
								<i class="fe fe-x"></i> Clear Filters
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Summary -->
	<div class="row">
		<div class="col-md-12">
			<div class="filter-card">
				<h4>Summary</h4>
				<div class="row">
					<div class="col-md-4">
						<p><strong>Total Transactions:</strong> <?=count($transactions)?></p>
					</div>
					<div class="col-md-4">
						<p><strong>Total Amount:</strong> <?=get_option('currency_symbol', '$')?><?=number_format($total_amount, 2)?></p>
					</div>
					<div class="col-md-4">
						<p><strong>Export:</strong> 
							<a href="<?=cn('analytics/export_orders')?>?<?=http_build_query($_GET)?>" class="btn btn-sm btn-success">
								<i class="fe fe-download"></i> Export CSV
							</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Transactions Table -->
	<div class="row">
		<div class="col-md-12">
			<div class="payment-table">
				<h4>Transactions (Last 100)</h4>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Transaction ID</th>
								<th>User</th>
								<th>Type</th>
								<th>Amount</th>
								<th>Fee</th>
								<th>Status</th>
								<th>Created</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($transactions)) { 
								foreach ($transactions as $txn) { ?>
							<tr>
								<td><?=htmlspecialchars($txn->transaction_id)?></td>
								<td>
									<?=htmlspecialchars($txn->user_email)?><br>
									<small><?=htmlspecialchars($txn->first_name . ' ' . $txn->last_name)?></small>
								</td>
								<td><?=htmlspecialchars($txn->type)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($txn->amount, 2)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($txn->txn_fee ?? 0, 2)?></td>
								<td>
									<?php if ($txn->status == 1) { ?>
									<span class="badge badge-success">Completed</span>
									<?php } else { ?>
									<span class="badge badge-warning">Pending</span>
									<?php } ?>
								</td>
								<td><?=date('Y-m-d H:i', strtotime($txn->created))?></td>
							</tr>
							<?php } 
							} else { ?>
							<tr>
								<td colspan="7" class="text-center">No transactions found</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
