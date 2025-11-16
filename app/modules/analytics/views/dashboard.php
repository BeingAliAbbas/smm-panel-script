<style>
.analytics-card {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 10px;
	padding: 20px;
	color: white;
	margin-bottom: 20px;
	box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.analytics-card.green {
	background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.analytics-card.blue {
	background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}
.analytics-card.orange {
	background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
.analytics-card h3 {
	font-size: 2.5rem;
	margin: 10px 0;
	font-weight: bold;
}
.analytics-card p {
	margin: 0;
	opacity: 0.9;
}
.chart-container {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.table-container {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid">
	<div class="page-header">
		<h1 class="page-title">Analytics Dashboard</h1>
	</div>

	<!-- Summary Cards -->
	<div class="row">
		<div class="col-md-3">
			<div class="analytics-card">
				<p>Total Revenue</p>
				<h3><?=get_option('currency_symbol', '$')?><?=number_format($total_revenue, 2)?></h3>
			</div>
		</div>
		<div class="col-md-3">
			<div class="analytics-card green">
				<p>Total Orders</p>
				<h3><?=number_format($total_orders)?></h3>
			</div>
		</div>
		<div class="col-md-3">
			<div class="analytics-card blue">
				<p>Total Users</p>
				<h3><?=number_format($total_users)?></h3>
			</div>
		</div>
		<div class="col-md-3">
			<div class="analytics-card orange">
				<p>Pending Orders</p>
				<h3><?=number_format($pending_orders)?></h3>
			</div>
		</div>
	</div>

	<!-- This Month Stats -->
	<div class="row">
		<div class="col-md-4">
			<div class="chart-container">
				<h4>This Month</h4>
				<table class="table">
					<tr>
						<td>Revenue:</td>
						<td class="text-right"><strong><?=get_option('currency_symbol', '$')?><?=number_format($revenue_this_month, 2)?></strong></td>
					</tr>
					<tr>
						<td>Orders:</td>
						<td class="text-right"><strong><?=number_format($orders_this_month)?></strong></td>
					</tr>
					<tr>
						<td>New Users:</td>
						<td class="text-right"><strong><?=number_format($new_users_this_month)?></strong></td>
					</tr>
					<tr>
						<td>Completed:</td>
						<td class="text-right"><strong><?=number_format($completed_orders)?></strong></td>
					</tr>
				</table>
			</div>
		</div>

		<!-- Top Users -->
		<div class="col-md-8">
			<div class="table-container">
				<h4>Top 5 Users by Spending</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>User</th>
							<th>Email</th>
							<th>Orders</th>
							<th>Total Spent</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($top_users)) { 
							foreach ($top_users as $user) { ?>
						<tr>
							<td><?=htmlspecialchars($user->first_name . ' ' . $user->last_name)?></td>
							<td><?=htmlspecialchars($user->email)?></td>
							<td><?=number_format($user->order_count)?></td>
							<td><?=get_option('currency_symbol', '$')?><?=number_format($user->total_spent ?? 0, 2)?></td>
						</tr>
						<?php } 
						} else { ?>
						<tr>
							<td colspan="4" class="text-center">No data available</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Recent Orders -->
	<div class="row">
		<div class="col-md-12">
			<div class="table-container">
				<h4>Recent Orders</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Order ID</th>
							<th>User</th>
							<th>Service</th>
							<th>Quantity</th>
							<th>Charge</th>
							<th>Status</th>
							<th>Created</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($recent_orders)) { 
							foreach ($recent_orders as $order) { ?>
						<tr>
							<td><?=htmlspecialchars($order->ids)?></td>
							<td><?=htmlspecialchars($order->user_email)?></td>
							<td><?=htmlspecialchars($order->service_name)?></td>
							<td><?=number_format($order->quantity)?></td>
							<td><?=get_option('currency_symbol', '$')?><?=number_format($order->charge, 2)?></td>
							<td>
								<?php
								$badge_class = 'badge-secondary';
								if ($order->status == 'completed') $badge_class = 'badge-success';
								if ($order->status == 'pending') $badge_class = 'badge-warning';
								if ($order->status == 'error') $badge_class = 'badge-danger';
								?>
								<span class="badge <?=$badge_class?>"><?=ucfirst($order->status)?></span>
							</td>
							<td><?=date('Y-m-d H:i', strtotime($order->created))?></td>
						</tr>
						<?php } 
						} else { ?>
						<tr>
							<td colspan="7" class="text-center">No orders found</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Quick Links -->
	<div class="row">
		<div class="col-md-12">
			<div class="chart-container">
				<h4>Quick Actions</h4>
				<div class="btn-group">
					<a href="<?=cn('analytics/service_popularity')?>" class="btn btn-primary">
						<i class="fe fe-trending-up"></i> Service Popularity
					</a>
					<a href="<?=cn('analytics/payment_history')?>" class="btn btn-info">
						<i class="fe fe-credit-card"></i> Payment History
					</a>
					<a href="<?=cn('analytics/export_orders')?>" class="btn btn-success">
						<i class="fe fe-download"></i> Export Orders
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
