<style>
.popularity-card {
	background: white;
	border-radius: 10px;
	padding: 20px;
	margin-bottom: 20px;
	box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.service-bar {
	background: #f0f0f0;
	border-radius: 5px;
	height: 30px;
	margin: 10px 0;
	position: relative;
	overflow: hidden;
}
.service-bar-fill {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	height: 100%;
	display: flex;
	align-items: center;
	padding: 0 10px;
	color: white;
	font-weight: bold;
	transition: width 0.5s ease;
}
</style>

<div class="container-fluid">
	<div class="page-header">
		<h1 class="page-title">Service Popularity Analytics</h1>
		<div class="page-options">
			<a href="<?=cn('analytics')?>" class="btn btn-secondary">
				<i class="fe fe-arrow-left"></i> Back to Dashboard
			</a>
		</div>
	</div>

	<!-- Category Performance -->
	<div class="row">
		<div class="col-md-12">
			<div class="popularity-card">
				<h4>Category Performance</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Category</th>
							<th>Orders</th>
							<th>Revenue</th>
							<th>Performance</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (!empty($category_performance)) {
							$max_revenue = max(array_column($category_performance, 'total_revenue'));
							foreach ($category_performance as $category) { 
								$percentage = $max_revenue > 0 ? ($category->total_revenue / $max_revenue) * 100 : 0;
						?>
						<tr>
							<td><strong><?=htmlspecialchars($category->name)?></strong></td>
							<td><?=number_format($category->order_count)?></td>
							<td><?=get_option('currency_symbol', '$')?><?=number_format($category->total_revenue ?? 0, 2)?></td>
							<td>
								<div class="service-bar">
									<div class="service-bar-fill" style="width: <?=$percentage?>%">
										<?=round($percentage)?>%
									</div>
								</div>
							</td>
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

	<!-- Top Services -->
	<div class="row">
		<div class="col-md-12">
			<div class="popularity-card">
				<h4>Top 20 Most Popular Services</h4>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Service Name</th>
							<th>Price</th>
							<th>Orders</th>
							<th>Revenue</th>
							<th>Popularity</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (!empty($popular_services)) {
							$max_orders = max(array_column($popular_services, 'order_count'));
							$rank = 1;
							foreach ($popular_services as $service) { 
								$percentage = $max_orders > 0 ? ($service->order_count / $max_orders) * 100 : 0;
						?>
						<tr>
							<td><?=$rank++?></td>
							<td><strong><?=htmlspecialchars($service->name)?></strong></td>
							<td><?=get_option('currency_symbol', '$')?><?=number_format($service->price, 2)?></td>
							<td><?=number_format($service->order_count)?></td>
							<td><?=get_option('currency_symbol', '$')?><?=number_format($service->total_revenue ?? 0, 2)?></td>
							<td>
								<div class="service-bar">
									<div class="service-bar-fill" style="width: <?=$percentage?>%">
										<?=round($percentage)?>%
									</div>
								</div>
							</td>
						</tr>
						<?php } 
						} else { ?>
						<tr>
							<td colspan="6" class="text-center">No services found</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
