<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?>
      </h1>
      <p class="text-muted">Manage your WhatsApp marketing campaigns, API configurations, and recipients</p>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="row row-cards">
      
      <!-- Campaigns Card -->
      <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-green mr-3">
              <i class="fa fa-paper-plane"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/campaigns'); ?>" class="text-inherit">Campaigns</a></h4>
              <small class="text-muted">Manage WhatsApp campaigns</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- API Config Card -->
      <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-blue mr-3">
              <i class="fa fa-cog"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/api_configs'); ?>" class="text-inherit">API Configuration</a></h4>
              <small class="text-muted">WhatsApp API settings</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Reports Card -->
      <div class="col-sm-6 col-lg-4">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-purple mr-3">
              <i class="fa fa-bar-chart"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/campaigns'); ?>" class="text-inherit">Reports</a></h4>
              <small class="text-muted">Analytics & Logs</small>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fa fa-info-circle"></i> Getting Started</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Quick Start Guide</h4>
            <ol class="mb-3">
              <li><strong>Configure API:</strong> Add your WhatsApp API configuration in <a href="<?php echo cn($module . '/api_configs'); ?>">API Configuration</a></li>
              <li><strong>Create Campaign:</strong> Set up a new campaign in <a href="<?php echo cn($module . '/campaigns'); ?>">Campaigns</a></li>
              <li><strong>Add Recipients:</strong> Import users from database or upload CSV/TXT file</li>
              <li><strong>Start Campaign:</strong> Click "Start Sending" to begin</li>
              <li><strong>Monitor Progress:</strong> View logs and statistics in real-time</li>
            </ol>
          </div>
          <div class="col-md-6">
            <h4>Cron Setup</h4>
            <p><strong>Campaign-Specific Cron (Recommended)</strong></p>
            <div class="alert alert-success">
              <code>* * * * * curl "<?php echo base_url('whatsapp_cron/run?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID'); ?>"</code>
            </div>
            <p><small class="text-muted">Each campaign should have its own cron job. Get campaign-specific URL from campaign details page.</small></p>
            
            <p class="mt-3"><strong>Process All Running Campaigns</strong></p>
            <div class="alert alert-info">
              <code>* * * * * curl "<?php echo base_url('whatsapp_cron/run?token=' . get_option('whatsapp_cron_token', md5('whatsapp_marketing_cron_' . ENCRYPTION_KEY))); ?>"</code>
            </div>
            <p><small class="text-muted">This processes all running campaigns together.</small></p>
            
            <h5 class="mt-3">Message Placeholders</h5>
            <p><small>Use these variables in your messages:</small></p>
            <ul class="small">
              <li><code>{username}</code> - User's name</li>
              <li><code>{phone}</code> - User's phone number</li>
              <li><code>{balance}</code> - User's balance</li>
              <li><code>{email}</code> - User's email</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
