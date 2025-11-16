<style>
.report-card {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid">
	<div class="page-header">
		<h1 class="page-title">Service Performance Report</h1>
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
				<h4>Filter by Date Range</h4>
				<form method="get" action="<?=cn('reports/service_performance')?>">
					<div class="row">
						<div class="col-md-4">
							<label>Date From</label>
							<input type="date" name="date_from" class="form-control" value="<?=htmlspecialchars($date_from)?>">
						</div>
						<div class="col-md-4">
							<label>Date To</label>
							<input type="date" name="date_to" class="form-control" value="<?=htmlspecialchars($date_to)?>">
						</div>
						<div class="col-md-4">
							<label>&nbsp;</label><br>
							<button type="submit" class="btn btn-primary">
								<i class="fe fe-filter"></i> Apply Filter
							</button>
							<a href="<?=cn('reports/service_performance')?>" class="btn btn-secondary">
								<i class="fe fe-x"></i> Clear
							</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Performance Data -->
	<div class="row">
		<div class="col-md-12">
			<div class="report-card">
				<h4>Top 50 Services Performance</h4>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Service Name</th>
								<th>Category</th>
								<th>Price</th>
								<th>Orders</th>
								<th>Revenue</th>
								<th>Avg Value</th>
								<th>Success Rate</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if (!empty($performance_data)) { 
								$rank = 1;
								foreach ($performance_data as $service) { 
									$success_class = $service->success_rate >= 90 ? 'success' : ($service->success_rate >= 70 ? 'warning' : 'danger');
							?>
							<tr>
								<td><?=$rank++?></td>
								<td><strong><?=htmlspecialchars($service->name)?></strong></td>
								<td><?=htmlspecialchars($service->category_name)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($service->price, 2)?></td>
								<td><?=number_format($service->order_count)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($service->total_revenue ?? 0, 2)?></td>
								<td><?=get_option('currency_symbol', '$')?><?=number_format($service->avg_order_value ?? 0, 2)?></td>
								<td>
									<div class="progress" style="height: 25px;">
										<div class="progress-bar bg-<?=$success_class?>" role="progressbar" style="width: <?=$service->success_rate?>%">
											<?=$service->success_rate?>%
										</div>
									</div>
								</td>
							</tr>
							<?php } 
							} else { ?>
							<tr>
								<td colspan="8" class="text-center">No data available</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
