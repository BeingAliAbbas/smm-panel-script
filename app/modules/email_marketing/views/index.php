<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?>
      </h1>
      <p class="text-muted">Manage your email marketing campaigns, templates, and SMTP configurations</p>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="row row-cards">
      
      <!-- Campaigns Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-blue mr-3">
              <i class="fe fe-mail"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/campaigns'); ?>" class="text-inherit">Campaigns</a></h4>
              <small class="text-muted">Manage email campaigns</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Templates Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-green mr-3">
              <i class="fe fe-file-text"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/templates'); ?>" class="text-inherit">Templates</a></h4>
              <small class="text-muted">Email templates</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- SMTP Config Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-orange mr-3">
              <i class="fe fe-settings"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/smtp'); ?>" class="text-inherit">SMTP Config</a></h4>
              <small class="text-muted">SMTP settings</small>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Reports Card -->
      <div class="col-sm-6 col-lg-3">
        <div class="card p-3">
          <div class="d-flex align-items-center">
            <span class="stamp stamp-md bg-purple mr-3">
              <i class="fe fe-bar-chart-2"></i>
            </span>
            <div>
              <h4 class="m-0"><a href="<?php echo cn($module . '/reports'); ?>" class="text-inherit">Reports</a></h4>
              <small class="text-muted">Analytics & Reports</small>
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
        <h3 class="card-title"><i class="fe fe-info"></i> Getting Started</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Quick Start Guide</h4>
            <ol class="mb-3">
              <li><strong>Configure SMTP:</strong> Add at least one SMTP configuration in <a href="<?php echo cn($module . '/smtp'); ?>">SMTP Config</a></li>
              <li><strong>Create Template:</strong> Design your email template in <a href="<?php echo cn($module . '/templates'); ?>">Templates</a></li>
              <li><strong>Create Campaign:</strong> Set up a new campaign in <a href="<?php echo cn($module . '/campaigns'); ?>">Campaigns</a></li>
              <li><strong>Add Recipients:</strong> Import users or upload CSV file</li>
              <li><strong>Start Campaign:</strong> Click "Start Sending" to begin</li>
            </ol>
          </div>
          <div class="col-md-6">
            <h4>Cron Setup</h4>
            <p>To enable automatic email sending, add this cron job to your server:</p>
            <div class="alert alert-info">
              <code>* * * * * curl "<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN')); ?>"</code>
            </div>
            <p><small class="text-muted">This runs every minute. Emails are sent one at a time based on campaign limits.</small></p>
            
            <h5 class="mt-3">Template Variables</h5>
            <p><small>Use these variables in your email templates:</small></p>
            <ul class="small">
              <li><code>{username}</code> - User's name</li>
              <li><code>{email}</code> - User's email</li>
              <li><code>{balance}</code> - User's balance</li>
              <li><code>{site_name}</code> - Website name</li>
              <li><code>{site_url}</code> - Website URL</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
