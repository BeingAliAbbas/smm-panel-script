<style>
.report-card {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.summary-box {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	text-align: center;
}
.summary-box h3 {
	font-size: 2rem;
	margin: 10px 0;
}
</style>

<div class="container-fluid">
	<div class="page-header">
		<h1 class="page-title">Revenue Report</h1>
		<div class="page-options">
			<a href="<?=cn('analytics')?>" class="btn btn-secondary">
				<i class="fe fe-arrow-left"></i> Back
			</a>
		</div>
	</div>

	<!-- Filters -->
	<div class="row">
		<div class="col-md-12">
			<div class="report-card">
				<h4>Report Options</h4>
				<form method="get" action="<?=cn('reports/revenue')?>">
					<div class="row">
						<div class="col-md-4">
							<label>Period</label>
							<select name="period" class="form-control">
								<option value="monthly" <?=$period == 'monthly' ? 'selected' : ''?>>Monthly</option>
								<option value="quarterly" <?=$period == 'quarterly' ? 'selected' : ''?>>Quarterly</option>
							</select>
						</div>
						<div class="col-md-4">
							<label>Year</label>
							<select name="year" class="form-control">
								<?php for ($y = date('Y'); $y >= 2020; $y--) { ?>
								<option value="<?=$y?>" <?=$year == $y ? 'selected' : ''?>><?=$y?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-4">
							<label>&nbsp;</label><br>
							<button type="submit" class="btn btn-primary">
								<i class="fe fe-refresh-cw"></i> Generate Report
							</button>
							<a href="<?=cn('reports/export_revenue')?>?period=<?=$period?>&year=<?=$year?>" class="btn btn-success">
								<i class="fe fe-download"></i> Export CSV
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Summary -->
	<div class="row">
		<div class="col-md-4">
			<div class="summary-box">
				<p>Total Revenue</p>
				<h3><?=get_option('currency_symbol', '$')?><?=number_format($total_revenue, 2)?></h3>
			</div>
		</div>
		<div class="col-md-4">
			<div class="summary-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
				<p>Total Orders</p>
				<h3><?=number_format($total_orders)?></h3>
			</div>
		</div>
		<div class="col-md-4">
			<div class="summary-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
				<p>Avg Order Value</p>
				<h3><?=get_option('currency_symbol', '$')?><?=number_format($avg_order_value, 2)?></h3>
			</div>
		</div>
	</div>

	<!-- Report Data -->
	<div class="row">
		<div class="col-md-12">
			<div class="report-card">
				<h4>Revenue Breakdown</h4>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Period</th>
								<th>Orders</th>
								<th>Revenue</th>
								<th>Avg Order Value</th>
								<th>Growth</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$prev_revenue = 0;
							if (!empty($report_data)) { 
								foreach ($report_data as $index => $row) { 
									$growth = 0;
									if ($prev_revenue > 0) {
										$growth = (($row['revenue'] - $prev_revenue) / $prev_revenue) * 100;
									}
									$growth_class = $growth >= 0 ? 'text-success' : 'text-danger';
									$growth_icon = $growth >= 0 ? 'fe-trending-up' : 'fe-trending-down';
							?>
							<tr>
								<td><strong><?=htmlspecialchars($row['period'])?></strong></td>
								<td><?=number_format($row['orders'])?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($row['revenue'], 2)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($row['avg_order_value'], 2)?></td>
								<td class="<?=$growth_class?>">
									<?php if ($index > 0) { ?>
									<i class="fe <?=$growth_icon?>"></i> <?=abs(round($growth, 2))?>%
									<?php } else { ?>
									-
									<?php } ?>
								</td>
							</tr>
							<?php 
									$prev_revenue = $row['revenue'];
								} 
							} else { ?>
							<tr>
								<td colspan="5" class="text-center">No data available</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
