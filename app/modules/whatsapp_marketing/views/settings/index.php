<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/whatsapp_marketing-responsive.css">

<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-settings"></i> WhatsApp Marketing Settings
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

<div class="row whatsapp-marketing-stats">
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Queue Size</h6>
            <span class="h2 mb-0 text-primary"><?php echo number_format($queue_metrics->queue_size); ?></span>
            <small class="text-muted d-block">pending messages</small>
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
            <h6 class="text-uppercase text-muted mb-2">Failed Messages</h6>
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
            <div class="mb-2"><strong>Rejected (Phone)</strong></div>
            <span class="h3 text-warning"><?php echo $queue_metrics->last_cron_rejected_phone; ?></span>
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
                 value="<?php echo base_url('cron/whatsapp_marketing?token=' . get_option('whatsapp_cron_token', 'YOUR_TOKEN')); ?>" 
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
          <code>* * * * * curl -s "<?php echo base_url('cron/whatsapp_marketing?token=' . get_option('whatsapp_cron_token', 'YOUR_TOKEN')); ?>" > /dev/null 2>&1</code>
          <br><br>
          <strong><i class="fe fe-info"></i> Windows Task Scheduler:</strong><br>
          <code>curl.exe "<?php echo base_url('cron/whatsapp_marketing?token=' . get_option('whatsapp_cron_token', 'YOUR_TOKEN')); ?>"</code>
          <br><br>
          <small class="text-muted">
            <strong>Note:</strong> For campaign-specific cron URLs, go to the campaign details page.
            <br>Running every minute ensures messages are sent promptly while respecting rate limits.
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
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-sliders"></i> WhatsApp Settings</h3>
      </div>
      <div class="card-body">
        <form id="settingsForm" action="<?php echo cn($module . '/ajax_save_settings'); ?>" method="POST">
          
          <!-- Phone Filter -->
          <div class="form-group">
            <label><strong>Phone Number Filter</strong></label>
            <select class="form-control" name="phone_filter" id="phone_filter">
              <option value="disabled" <?php echo $settings['phone_filter'] == 'disabled' ? 'selected' : ''; ?>>Disabled (Allow All)</option>
              <option value="country_code" <?php echo $settings['phone_filter'] == 'country_code' ? 'selected' : ''; ?>>Filter by Country Code</option>
            </select>
            <small class="text-muted">Choose which phone numbers are allowed to receive messages</small>
          </div>
          
          <!-- Custom Country Codes -->
          <div class="form-group" id="country_codes_group" style="display: <?php echo $settings['phone_filter'] == 'country_code' ? 'block' : 'none'; ?>;">
            <label><strong>Allowed Country Codes</strong></label>
            <input type="text" class="form-control" name="allowed_country_codes" id="allowed_country_codes" 
                   value="<?php echo htmlspecialchars($settings['allowed_country_codes']); ?>" 
                   placeholder="+1, +44, +92">
            <small class="text-muted">Comma-separated list of allowed country codes (e.g., +1, +44, +92)</small>
          </div>
          
          <!-- Current Filter Status -->
          <div class="alert alert-info">
            <strong>Current Filter:</strong>
            <?php if($settings['phone_filter'] == 'disabled'){ ?>
              All phone numbers are allowed
            <?php } else { ?>
              Country codes: <?php echo htmlspecialchars($settings['allowed_country_codes']); ?>
            <?php } ?>
          </div>
          
          <!-- Read Tracking -->
          <div class="form-group">
            <label class="form-check">
              <input type="checkbox" class="form-check-input" name="enable_read_tracking" value="1" 
                     <?php echo $settings['enable_read_tracking'] == 1 ? 'checked' : ''; ?>>
              <span class="form-check-label"><strong>Enable Read Tracking</strong></span>
            </label>
            <small class="text-muted d-block">Track when messages are read (requires WhatsApp Business API support)</small>
          </div>
          
          <!-- Message Retry Settings -->
          <div class="form-group">
            <label><strong>Failed Message Retry Attempts</strong></label>
            <input type="number" class="form-control" name="retry_attempts" 
                   value="<?php echo isset($settings['retry_attempts']) ? $settings['retry_attempts'] : 3; ?>" 
                   min="0" max="10">
            <small class="text-muted">Number of times to retry failed messages (0 = no retry)</small>
          </div>
          
          <hr>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fe fe-save"></i> Save Settings
            </button>
          </div>
          
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Show/hide custom country codes based on filter selection
document.getElementById('phone_filter').addEventListener('change', function() {
    var customGroup = document.getElementById('country_codes_group');
    if(this.value === 'country_code') {
        customGroup.style.display = 'block';
    } else {
        customGroup.style.display = 'none';
    }
});

// Copy cron URL functionality
document.querySelectorAll('.cron-url-copy').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = this.closest('.cron-url-container').querySelector('.cron-url-input');
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        
        // Visual feedback
        var originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fe fe-check"></i> Copied!';
        this.classList.add('copy-btn-success');
        
        setTimeout(() => {
            this.innerHTML = originalHTML;
            this.classList.remove('copy-btn-success');
        }, 2000);
    });
});

// Open cron URL in new tab
document.querySelectorAll('.cron-url-open').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = this.closest('.cron-url-container').querySelector('.cron-url-input');
        window.open(input.value, '_blank');
    });
});

// Handle settings form submission
$(document).ready(function() {
    $('#settingsForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'JSON',
            success: function(response) {
                if(response.status == 'success') {
                    show_message(response.message, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    show_message(response.message, 'error');
                }
            },
            error: function() {
                show_message('An error occurred. Please try again.', 'error');
            }
        });
    });
});
</script>
