<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-bullhorn"></i> Campaign Details: <?php echo $campaign->name ?></h4>
                    <a href="<?php echo cn('whatsapp_marketing/campaigns') ?>" class="btn btn-light round pull-right">Back to Campaigns</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <h5>Total Recipients</h5>
                            <h3><?php echo $campaign->stats['total'] ?></h3>
                        </div>
                        <div class="col-md-3">
                            <h5>Sent</h5>
                            <h3 class="text-success"><?php echo $campaign->stats['sent'] ?></h3>
                        </div>
                        <div class="col-md-3">
                            <h5>Failed</h5>
                            <h3 class="text-danger"><?php echo $campaign->stats['failed'] ?></h3>
                        </div>
                        <div class="col-md-3">
                            <h5>Remaining</h5>
                            <h3 class="text-info"><?php echo $campaign->stats['remaining'] ?></h3>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Status:</strong> <span class="badge badge-info"><?php echo $campaign->status ?></span></p>
                            <p><strong>Sending Status:</strong> <span class="badge badge-<?php echo $campaign->sending_status == 'Started' ? 'success' : 'secondary' ?>"><?php echo $campaign->sending_status ?></span></p>
                            <p><strong>Message:</strong></p>
                            <pre class="bg-light p-3"><?php echo htmlspecialchars($campaign->message) ?></pre>
                            <p><strong>Cron URL for this campaign:</strong></p>
                            <pre class="bg-light p-3"><?php echo base_url('cron/whatsapp_marketing?token=YOUR_TOKEN&campaign_id=' . $campaign->id) ?></pre>
                        </div>
                    </div>
                    <hr>
                    <h5>Actions</h5>
                    <a href="<?php echo cn('whatsapp_marketing/recipients/' . $campaign->id) ?>" class="btn btn-info round">Manage Recipients</a>
                    <?php if ($campaign->sending_status != 'Started'): ?>
                        <a href="<?php echo cn('whatsapp_marketing/campaign_start/' . $campaign->id) ?>" class="btn btn-success round" onclick="return confirm('Start sending?')">Start Sending</a>
                    <?php else: ?>
                        <a href="<?php echo cn('whatsapp_marketing/campaign_pause/' . $campaign->id) ?>" class="btn btn-warning round" onclick="return confirm('Pause sending?')">Pause Sending</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
