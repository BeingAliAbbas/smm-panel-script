<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-bar-chart"></i> WhatsApp Campaign Reports</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Total</th>
                                    <th>Sent</th>
                                    <th>Delivered</th>
                                    <th>Failed</th>
                                    <th>Remaining</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($campaigns)): ?>
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <tr>
                                            <td><?php echo $campaign->name ?></td>
                                            <td><?php echo $campaign->stats['total'] ?></td>
                                            <td><?php echo $campaign->stats['sent'] ?></td>
                                            <td><?php echo $campaign->stats['delivered'] ?></td>
                                            <td><?php echo $campaign->stats['failed'] ?></td>
                                            <td><?php echo $campaign->stats['remaining'] ?></td>
                                            <td>
                                                <a href="<?php echo cn('whatsapp_marketing/campaign_details/' . $campaign->id) ?>" class="btn btn-sm btn-info round">View Details</a>
                                                <a href="<?php echo cn('whatsapp_marketing/export_report/' . $campaign->id) ?>" class="btn btn-sm btn-success round">Export CSV</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No campaigns found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
