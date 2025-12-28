<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/email_marketing-responsive.css">

<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-settings"></i> Email Marketing Settings
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Queue Metrics / Observability Section -->
<div class="row">
  <div class="col-md-12">
    <h3 class="mb-3"><i class="fe fe-activity"></i> System Metrics (Observability)</h3>
  </div>
</div>

<div class="row email-marketing-stats">
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Queue Size</h6>
            <span class="h2 mb-0 text-primary"><?php echo number_format($queue_metrics->queue_size); ?></span>
            <small class="text-muted d-block">pending emails</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-inbox text-primary mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Failed Emails</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($queue_metrics->failed_count); ?></span>
            <small class="text-muted d-block">total failed</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Running Campaigns</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($queue_metrics->running_campaigns); ?></span>
            <small class="text-muted d-block">active now</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-play text-success mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Last Cron Run</h6>
            <span class="h5 mb-0"><?php echo $queue_metrics->last_cron_run; ?></span>
            <?php if($queue_metrics->last_cron_duration_sec > 0){ ?>
            <small class="text-muted d-block"><?php echo $queue_metrics->last_cron_duration_sec; ?>s duration</small>
            <?php } ?>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-clock text-info mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Last Cron Details -->
<?php if($queue_metrics->last_cron_run !== 'Never'){ ?>
<div class="row">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-terminal"></i> Last Cron Execution Details</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Sent</strong></div>
            <span class="h3 text-success"><?php echo $queue_metrics->last_cron_sent; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Failed</strong></div>
            <span class="h3 text-danger"><?php echo $queue_metrics->last_cron_failed; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Rejected (Domain)</strong></div>
            <span class="h3 text-warning"><?php echo $queue_metrics->last_cron_rejected_domain; ?></span>
          </div>
          <div class="col-md-3 text-center">
            <div class="mb-2"><strong>Duration</strong></div>
            <span class="h3 text-info"><?php echo $queue_metrics->last_cron_duration_sec; ?>s</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Cron Configuration Section -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-terminal"></i> Cron Job Configuration</h3>
      </div>
      <div class="card-body">
        <h5 class="mb-3">General Cron URL (All Campaigns)</h5>
        <p class="text-muted">This URL processes all running campaigns. Run this every minute for best performance.</p>
        
        <div class="cron-url-container mb-3">
          <input type="text" 
                 class="cron-url-input" 
                 value="<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN')); ?>" 
                 readonly 
                 onclick="this.select()">
          <div class="cron-url-buttons">
            <button type="button" class="btn btn-sm btn-secondary cron-url-copy" data-bs-toggle="tooltip" title="Copy to clipboard">
              <i class="fe fe-copy"></i> Copy
            </button>
            <button type="button" class="btn btn-sm btn-info cron-url-open" data-bs-toggle="tooltip" title="Open in new tab">
              <i class="fe fe-external-link"></i> Open
            </button>
          </div>
        </div>
        
        <div class="alert alert-info">
          <strong><i class="fe fe-info"></i> Linux Cron Setup:</strong><br>
          <code>* * * * * curl -s "<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN')); ?>" > /dev/null 2>&1</code>
          <br><br>
          <strong><i class="fe fe-info"></i> Windows Task Scheduler:</strong><br>
          <code>curl.exe "<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN')); ?>"</code>
          <br><br>
          <small class="text-muted">
            <strong>Note:</strong> For campaign-specific cron URLs, go to the campaign details page.
            <br>Running every minute ensures emails are sent promptly while respecting rate limits.
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Settings Form -->
<div class="row mt-4">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-sliders"></i> Email Settings</h3>
      </div>
      <div class="card-body">
        <form id="settingsForm" action="<?php echo cn($module . '/ajax_save_settings'); ?>" method="POST">
          
          <!-- Domain Filter -->
          <div class="form-group">
            <label><strong>Email Domain Filter</strong></label>
            <select class="form-control" name="domain_filter" id="domain_filter">
              <option value="gmail_only" <?php echo $settings['email_domain_filter'] == 'gmail_only' ? 'selected' : ''; ?>>Gmail Only (@gmail.com)</option>
              <option value="custom" <?php echo $settings['email_domain_filter'] == 'custom' ? 'selected' : ''; ?>>Custom Domains</option>
              <option value="disabled" <?php echo $settings['email_domain_filter'] == 'disabled' ? 'selected' : ''; ?>>Disabled (Allow All)</option>
            </select>
            <small class="text-muted">Choose which email domains are allowed to receive emails</small>
          </div>
          
          <!-- Custom Domains -->
          <div class="form-group" id="custom_domains_group" style="display: <?php echo $settings['email_domain_filter'] == 'custom' ? 'block' : 'none'; ?>;">
            <label><strong>Allowed Domains</strong></label>
            <input type="text" class="form-control" name="allowed_domains" id="allowed_domains" 
                   value="<?php echo htmlspecialchars($settings['email_allowed_domains']); ?>" 
                   placeholder="gmail.com, yahoo.com, outlook.com">
            <small class="text-muted">Comma-separated list of allowed domains (e.g., gmail.com, yahoo.com)</small>
          </div>
          
          <!-- Current Filter Status -->
          <div class="alert alert-info">
            <strong>Current Filter:</strong>
            <?php if($settings['email_domain_filter'] == 'gmail_only'){ ?>
              Only @gmail.com addresses are allowed
            <?php } elseif($settings['email_domain_filter'] == 'disabled'){ ?>
              All email domains are allowed
            <?php } else { ?>
              Custom domains: <?php echo htmlspecialchars($settings['email_allowed_domains']); ?>
            <?php } ?>
          </div>
          
          <!-- Open Tracking -->
          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" name="enable_open_tracking" value="1" 
                     <?php echo $settings['enable_open_tracking'] == 1 ? 'checked' : ''; ?>>
              <span class="form-check-label"><strong>Enable Open Tracking</strong></span>
            </label>
            <small class="text-muted d-block">Add tracking pixel to emails to track opens</small>
          </div>
          
          <hr>
          
          <!-- Email Validation Settings -->
          <h4 class="mb-3"><i class="fe fe-shield"></i> Email Validation (EmailListChecker API)</h4>
          
          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" name="email_validation_enabled" id="email_validation_enabled" value="1" 
                     <?php echo isset($settings['email_validation_enabled']) && $settings['email_validation_enabled'] == 1 ? 'checked' : ''; ?>>
              <span class="form-check-label"><strong>Enable Email Validation</strong></span>
            </label>
            <small class="text-muted d-block">Validate email addresses using EmailListChecker API before sending</small>
          </div>
          
          <div id="validation_settings_group" style="display: <?php echo isset($settings['email_validation_enabled']) && $settings['email_validation_enabled'] == 1 ? 'block' : 'none'; ?>;">
            <div class="form-group">
              <label><strong>API Key</strong></label>
              <input type="text" class="form-control" name="email_validation_api_key" 
                     value="<?php echo isset($settings['email_validation_api_key']) ? htmlspecialchars($settings['email_validation_api_key']) : ''; ?>" 
                     placeholder="Enter your EmailListChecker API key">
              <small class="text-muted">Your API key from EmailListChecker dashboard</small>
            </div>
            
            <div class="form-group">
              <label><strong>API Base URL</strong></label>
              <input type="text" class="form-control" name="email_validation_api_url" 
                     value="<?php echo isset($settings['email_validation_api_url']) ? htmlspecialchars($settings['email_validation_api_url']) : 'https://platform.emaillistchecker.io/api'; ?>" 
                     placeholder="https://platform.emaillistchecker.io/api">
              <small class="text-muted">API endpoint URL (default: https://platform.emaillistchecker.io/api)</small>
            </div>
            
            <div class="alert alert-warning">
              <strong>Note:</strong> When email validation is enabled, only emails verified as "deliverable" or "risky" will be sent. 
              Invalid emails will be skipped and logged for admin review.
            </div>
          </div>
          
          <hr>
          
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-save"></i> Save Settings
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Show/hide custom domains field
  $('#domain_filter').on('change', function(){
    if($(this).val() == 'custom'){
      $('#custom_domains_group').show();
    } else {
      $('#custom_domains_group').hide();
    }
  });
  
  // Show/hide email validation settings
  $('#email_validation_enabled').on('change', function(){
    if($(this).is(':checked')){
      $('#validation_settings_group').show();
    } else {
      $('#validation_settings_group').hide();
    }
  });
  
  // Handle form submission
  $('#settingsForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      beforeSend: function(){
        $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fe fe-loader"></i> Saving...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          show_message(response.message, 'error');
          $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Save Settings');
        }
      },
      error: function(){
        show_message('An error occurred', 'error');
        $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Save Settings');
      }
    });
  });
});
</script>

<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/email_marketing-responsive.css">

<!-- Include email marketing interactive features -->
<script src="<?php echo BASE; ?>assets/js/email_marketing.js"></script>
