<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h2 class="page-header-title"><i class="fa fa-whatsapp"></i> WhatsApp Marketing Dashboard</h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fa fa-bullhorn"></i> Campaigns</h4>
                    <p class="card-text">Create and manage WhatsApp campaigns</p>
                    <a href="<?php echo cn('whatsapp_marketing/campaigns') ?>" class="btn btn-primary round">View Campaigns</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fa fa-cog"></i> API Configuration</h4>
                    <p class="card-text">Configure WhatsApp API endpoints</p>
                    <a href="<?php echo cn('whatsapp_marketing/api_configs') ?>" class="btn btn-primary round">API Configs</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fa fa-users"></i> Recipients</h4>
                    <p class="card-text">Manage campaign recipients</p>
                    <a href="<?php echo cn('whatsapp_marketing/campaigns') ?>" class="btn btn-primary round">Select Campaign</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><i class="fa fa-bar-chart"></i> Reports</h4>
                    <p class="card-text">View campaign statistics and export</p>
                    <a href="<?php echo cn('whatsapp_marketing/reports') ?>" class="btn btn-primary round">View Reports</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-info-circle"></i> Cron Setup</h4>
                </div>
                <div class="card-body">
                    <p><strong>To enable automated WhatsApp sending:</strong></p>
                    <ol>
                        <li>Add the following cron job to your server</li>
                        <li>Set it to run every 1-5 minutes</li>
                    </ol>
                    <p><strong>Cron Command (All Campaigns):</strong></p>
                    <pre class="bg-light p-3">*/5 * * * * curl "<?php echo base_url('cron/whatsapp_marketing?token=YOUR_TOKEN') ?>"</pre>
                    <p><strong>Cron Command (Specific Campaign):</strong></p>
                    <pre class="bg-light p-3">*/5 * * * * curl "<?php echo base_url('cron/whatsapp_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID') ?>"</pre>
                    <p class="text-muted"><small>Note: Replace YOUR_TOKEN with your actual cron token (configured in settings) and CAMPAIGN_ID with specific campaign ID</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
