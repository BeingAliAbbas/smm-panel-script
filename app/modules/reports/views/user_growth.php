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
		<h1 class="page-title">User Growth Report</h1>
		<div class="page-options">
			<a href="<?=cn('analytics')?>" class="btn btn-secondary">
				<i class="fe fe-arrow-left"></i> Back
			</a>
		</div>
	</div>

	<!-- Summary -->
	<div class="row">
		<div class="col-md-6">
			<div class="report-card">
				<h4>Total Users</h4>
				<h2><?=number_format($total_users)?></h2>
			</div>
		</div>
		<div class="col-md-6">
			<div class="report-card">
				<h4>Active Users (Last 30 Days)</h4>
				<h2><?=number_format($active_users_30d)?></h2>
			</div>
		</div>
	</div>

	<!-- Growth Chart -->
	<div class="row">
		<div class="col-md-12">
			<div class="report-card">
				<h4>User Growth - Last 12 Months</h4>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Month</th>
								<th>New Users</th>
								<th>Active Users</th>
								<th>Engagement Rate</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($growth_data)) { 
								foreach ($growth_data as $row) { 
									$engagement = $row['new_users'] > 0 ? ($row['active_users'] / $row['new_users']) * 100 : 0;
							?>
							<tr>
								<td><strong><?=htmlspecialchars($row['month'])?></strong></td>
								<td><?=number_format($row['new_users'])?></td>
								<td><?=number_format($row['active_users'])?></td>
								<td>
									<div class="progress" style="height: 25px;">
										<div class="progress-bar bg-success" role="progressbar" style="width: <?=min($engagement, 100)?>%">
											<?=round($engagement, 2)?>%
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
	</div>
</div>
