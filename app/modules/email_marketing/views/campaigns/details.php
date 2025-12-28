<<<<<<< HEAD

<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/email_marketing-responsive.css">

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-mail"></i> <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
<<<<<<< HEAD
      <div class="page-subtitle campaign-controls">
=======
      <div class="page-subtitle">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaigns
        </a>
        <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-sm btn-success">
          <i class="fe fe-download"></i> Export Report
        </a>
<<<<<<< HEAD
        <?php if($campaign->status == 'running' || $campaign->status == 'paused'){ ?>
        <button type="button" 
                class="btn btn-sm campaign-toggle-status campaign-toggle-btn <?php echo ($campaign->status == 'running') ? 'btn-warning' : 'btn-success'; ?>"
                data-campaign-id="<?php echo $campaign->ids; ?>"
                data-current-status="<?php echo $campaign->status; ?>"
                data-endpoint="<?php echo cn($module . '/ajax_campaign_' . ($campaign->status == 'running' ? 'pause' : 'resume')); ?>"
                data-bs-toggle="tooltip"
                title="<?php echo ($campaign->status == 'running') ? 'Pause this campaign' : 'Resume this campaign'; ?>">
          <i class="fe fe-<?php echo ($campaign->status == 'running') ? 'pause' : 'play'; ?> me-1"></i><?php echo ($campaign->status == 'running') ? 'Pause Campaign' : 'Run Campaign'; ?>
        </button>
        <?php } ?>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
      </div>
    </div>
  </div>
</div>

<!-- Campaign Stats -->
<<<<<<< HEAD
<div class="row email-marketing-stats">
=======
<div class="row">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
  <div class="col-lg-3 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Total Emails</h6>
            <span class="h2 mb-0"><?php echo number_format($campaign->total_emails); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-mail text-muted mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Sent</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($campaign->sent_emails); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-check-circle text-success mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Opened</h6>
            <span class="h2 mb-0 text-info"><?php echo number_format($campaign->opened_emails); ?></span>
            <?php if($campaign->sent_emails > 0){ ?>
            <small class="text-muted">(<?php echo round(($campaign->opened_emails / $campaign->sent_emails) * 100, 1); ?>%)</small>
            <?php } ?>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-eye text-info mb-0"></span>
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
            <h6 class="text-uppercase text-muted mb-2">Failed</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($campaign->failed_emails); ?></span>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<<<<<<< HEAD
<!-- Validation Stats (if email validation is enabled) -->
<?php 
$validation_enabled = get_option('email_validation_enabled', 0);
if($validation_enabled && ($campaign->validated_emails > 0 || $campaign->invalid_emails > 0)){ 
?>
<div class="row">
  <div class="col-md-12">
    <h4 class="mb-3"><i class="fe fe-shield"></i> Email Validation Statistics</h4>
  </div>
</div>
<div class="row">
  <div class="col-lg-4 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Validated</h6>
            <span class="h2 mb-0 text-success"><?php echo number_format($campaign->validated_emails ?? 0); ?></span>
            <small class="text-muted d-block">valid emails</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-check-circle text-success mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Invalid</h6>
            <span class="h2 mb-0 text-danger"><?php echo number_format($campaign->invalid_emails ?? 0); ?></span>
            <small class="text-muted d-block">rejected emails</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-x-circle text-danger mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 col-sm-6">
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col">
            <h6 class="text-uppercase text-muted mb-2">Skipped</h6>
            <span class="h2 mb-0 text-warning"><?php echo number_format($campaign->validation_skipped ?? 0); ?></span>
            <small class="text-muted d-block">validation skipped</small>
          </div>
          <div class="col-auto">
            <span class="h2 fe fe-alert-circle text-warning mb-0"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Campaign Info -->
<div class="row">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Campaign Information</h3>
      </div>
      <div class="card-body">
        <table class="table table-sm campaign-info-table">
=======
<!-- Campaign Info -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign Information</h3>
      </div>
      <div class="card-body">
        <table class="table table-sm">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          <tr>
            <td class="w-50"><strong>Status:</strong></td>
            <td>
              <?php
              $status_class = 'secondary';
              switch($campaign->status){
                case 'running': $status_class = 'success'; break;
                case 'completed': $status_class = 'info'; break;
                case 'paused': $status_class = 'warning'; break;
                case 'cancelled': $status_class = 'danger'; break;
              }
              ?>
<<<<<<< HEAD
              <span class="badge badge-<?php echo $status_class; ?> campaign-status-badge"><?php echo ucfirst($campaign->status); ?></span>
=======
              <span class="badge badge-<?php echo $status_class; ?>"><?php echo ucfirst($campaign->status); ?></span>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            </td>
          </tr>
          <tr>
            <td><strong>Template:</strong></td>
            <td><?php echo htmlspecialchars($campaign->template_name); ?></td>
          </tr>
          <tr>
<<<<<<< HEAD
            <td><strong>SMTP Server(s):</strong></td>
            <td>
              <?php 
              // Get SMTP names from IDs
              $smtp_names = array();
              $selected_smtp_ids = array();
              
              // Parse SMTP IDs from JSON
              if(!empty($campaign->smtp_config_ids)){
                $selected_smtp_ids = json_decode($campaign->smtp_config_ids, true);
                if(!is_array($selected_smtp_ids)){
                  $selected_smtp_ids = array();
                }
              }
              
              // Fallback to single smtp_config_id if smtp_config_ids is empty
              if(empty($selected_smtp_ids) && !empty($campaign->smtp_config_id)){
                $selected_smtp_ids = array($campaign->smtp_config_id);
              }
              
              // Get SMTP names
              if(!empty($smtp_configs) && !empty($selected_smtp_ids)){
                foreach($smtp_configs as $smtp){
                  if(in_array($smtp->id, $selected_smtp_ids)){
                    $smtp_names[] = htmlspecialchars($smtp->name);
                  }
                }
              }
              
              if(!empty($smtp_names)){
                if(count($smtp_names) > 1){
                  echo '<span class="badge bg-info me-1">Round-Robin</span>';
                }
                echo implode(', ', $smtp_names);
                if(count($smtp_names) > 1){
                  echo '<br><small class="text-muted">Emails are rotated between ' . count($smtp_names) . ' SMTP servers</small>';
                }
              } else {
                echo htmlspecialchars(isset($campaign->smtp_name) ? $campaign->smtp_name : 'Not set');
              }
              ?>
            </td>
=======
            <td><strong>SMTP:</strong></td>
            <td><?php echo htmlspecialchars($campaign->smtp_name); ?></td>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </tr>
          <tr>
            <td><strong>Hourly Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_hourly ?: 'No limit'; ?></td>
          </tr>
          <tr>
            <td><strong>Daily Limit:</strong></td>
            <td><?php echo $campaign->sending_limit_daily ?: 'No limit'; ?></td>
          </tr>
          <tr>
            <td><strong>Created:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->created_at)); ?></td>
          </tr>
          <?php if($campaign->started_at){ ?>
          <tr>
            <td><strong>Started:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->started_at)); ?></td>
          </tr>
          <?php } ?>
          <?php if($campaign->completed_at){ ?>
          <tr>
            <td><strong>Completed:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->completed_at)); ?></td>
          </tr>
          <?php } ?>
          <?php if($campaign->last_sent_at){ ?>
          <tr>
            <td><strong>Last Sent:</strong></td>
            <td><?php echo date('M d, Y H:i', strtotime($campaign->last_sent_at)); ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><strong>Campaign Cron URL:</strong></td>
            <td>
<<<<<<< HEAD
              <div class="cron-url-container">
                <input type="text" 
                       class="cron-url-input" 
                       value="<?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN') . '&campaign_id=' . $campaign->ids); ?>" 
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
              <small class="text-muted d-block mt-2">
                <strong>Linux cron example:</strong><br>
                <code>* * * * * curl -s "<?php echo base_url('cron/email_marketing?token=YOUR_TOKEN&campaign_id=' . $campaign->ids); ?>" &gt; /dev/null 2&gt;&1</code>
              </small>
=======
              <small class="text-muted">
                <code><?php echo base_url('cron/email_marketing?token=' . get_option('email_cron_token', 'YOUR_TOKEN') . '&campaign_id=' . $campaign->ids); ?></code>
              </small>
              <br>
              <small class="text-info">Use this URL for campaign-specific cron job</small>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            </td>
          </tr>
        </table>
        
        <!-- Campaign Health Indicator -->
        <div class="mt-4">
          <h5>Campaign Health</h5>
          <?php 
          $health_score = 100;
          $health_class = 'success';
          $health_issues = array();
          
          // Check failure rate
          if($campaign->total_emails > 0){
            $failure_rate = ($campaign->failed_emails / $campaign->total_emails) * 100;
            if($failure_rate > 20){
              $health_score -= 30;
              $health_issues[] = 'High failure rate (' . round($failure_rate, 1) . '%)';
            } elseif($failure_rate > 10){
              $health_score -= 15;
              $health_issues[] = 'Moderate failure rate (' . round($failure_rate, 1) . '%)';
            }
          }
          
          // Check open rate
          if($campaign->sent_emails > 0){
            $open_rate = ($campaign->opened_emails / $campaign->sent_emails) * 100;
            if($open_rate < 10){
              $health_score -= 20;
              $health_issues[] = 'Low open rate (' . round($open_rate, 1) . '%)';
            } elseif($open_rate < 20){
              $health_score -= 10;
              $health_issues[] = 'Below average open rate (' . round($open_rate, 1) . '%)';
            }
          }
          
          // Check if campaign is stalled
          if($campaign->status == 'running' && $campaign->last_sent_at){
            $hours_since_last = (strtotime(NOW) - strtotime($campaign->last_sent_at)) / 3600;
            if($hours_since_last > 24){
              $health_score -= 25;
              $health_issues[] = 'No emails sent in last 24 hours';
            }
          }
          
          // Set health class based on score
          if($health_score >= 80){
            $health_class = 'success';
          } elseif($health_score >= 60){
            $health_class = 'warning';
          } else {
            $health_class = 'danger';
          }
          ?>
          
          <div class="progress mb-2" style="height: 25px;">
            <div class="progress-bar bg-<?php echo $health_class; ?>" role="progressbar" style="width: <?php echo $health_score; ?>%" aria-valuenow="<?php echo $health_score; ?>" aria-valuemin="0" aria-valuemax="100">
              <strong><?php echo $health_score; ?>%</strong>
            </div>
          </div>
          
          <?php if(!empty($health_issues)){ ?>
          <div class="alert alert-<?php echo $health_class; ?> mb-0">
            <strong>Issues Detected:</strong>
<<<<<<< HEAD
            <ul class="mb-0 ps-3">
=======
            <ul class="mb-0 pl-3">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
              <?php foreach($health_issues as $issue){ ?>
              <li><?php echo $issue; ?></li>
              <?php } ?>
            </ul>
          </div>
          <?php } else { ?>
          <div class="alert alert-success mb-0">
            <i class="fe fe-check-circle"></i> Campaign is performing well!
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
<<<<<<< HEAD
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Progress</h3>
=======
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Progress</h3>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
      </div>
      <div class="card-body">
        <?php 
        $progress = 0;
        if($campaign->total_emails > 0){
          $progress = round(($campaign->sent_emails / $campaign->total_emails) * 100);
        }
        $remaining = $campaign->total_emails - $campaign->sent_emails;
        ?>
        <div class="mb-3">
          <div class="clearfix mb-2">
<<<<<<< HEAD
            <div class="float-start"><strong><?php echo $progress; ?>% Complete</strong></div>
            <div class="float-end"><small class="text-muted"><?php echo $campaign->sent_emails; ?> / <?php echo $campaign->total_emails; ?></small></div>
=======
            <div class="float-left"><strong><?php echo $progress; ?>% Complete</strong></div>
            <div class="float-right"><small class="text-muted"><?php echo $campaign->sent_emails; ?> / <?php echo $campaign->total_emails; ?></small></div>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
          <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%"></div>
          </div>
        </div>
        
        <div class="row text-center">
          <div class="col">
            <div class="text-muted">Remaining</div>
            <div class="h4"><?php echo number_format($remaining); ?></div>
          </div>
          <div class="col">
            <div class="text-muted">Open Rate</div>
            <div class="h4">
              <?php 
              if($campaign->sent_emails > 0){
                echo round(($campaign->opened_emails / $campaign->sent_emails) * 100, 1) . '%';
              } else {
                echo '0%';
              }
              ?>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="btn btn-primary btn-block">
            <i class="fe fe-users"></i> Manage Recipients
          </a>
          
          <?php if($campaign->failed_emails > 0){ ?>
          <button class="btn btn-warning btn-block mt-2 actionCampaignResendFailed" data-ids="<?php echo $campaign->ids; ?>">
            <i class="fe fe-refresh-cw"></i> Resend Failed Emails (<?php echo $campaign->failed_emails; ?>)
          </button>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Recipients -->
<div class="row mt-3">
  <div class="col-md-12">
<<<<<<< HEAD
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Recent Recipients (Last 100)</h3>
=======
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Recipients (Last 100)</h3>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        <div class="card-options">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-secondary filter-recipients active" data-filter="all">All</button>
            <button type="button" class="btn btn-secondary filter-recipients" data-filter="pending">Pending</button>
            <button type="button" class="btn btn-secondary filter-recipients" data-filter="sent">Sent</button>
            <button type="button" class="btn btn-secondary filter-recipients" data-filter="failed">Failed</button>
            <button type="button" class="btn btn-secondary filter-recipients" data-filter="opened">Opened</button>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Email</th>
              <th>Name</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Opened At</th>
              <th>Error</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="recipients-table-body">
            <?php if(!empty($recipients)){ 
              foreach($recipients as $recipient){
                $status_badge = 'secondary';
                switch($recipient->status){
                  case 'sent': $status_badge = 'success'; break;
                  case 'opened': $status_badge = 'info'; break;
                  case 'failed': $status_badge = 'danger'; break;
                  case 'bounced': $status_badge = 'warning'; break;
                }
            ?>
            <tr class="recipient-row" data-status="<?php echo $recipient->status; ?>">
              <td><?php echo htmlspecialchars($recipient->email); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($recipient->status); ?></span></td>
              <td><?php echo $recipient->sent_at ? date('M d, H:i', strtotime($recipient->sent_at)) : '-'; ?></td>
              <td><?php echo $recipient->opened_at ? date('M d, H:i', strtotime($recipient->opened_at)) : '-'; ?></td>
              <td class="text-danger small"><?php echo $recipient->error_message ? htmlspecialchars(substr($recipient->error_message, 0, 50)) . (strlen($recipient->error_message) > 50 ? '...' : '') : '-'; ?></td>
              <td>
                <?php if($recipient->status == 'failed'){ ?>
                <button class="btn btn-sm btn-warning actionResendSingleEmail" data-recipient-id="<?php echo $recipient->id; ?>" title="Resend this email">
                  <i class="fe fe-refresh-cw"></i>
                </button>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="7" class="text-center">No recipients yet. <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>">Add recipients</a></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Filter recipients by status
  $('.filter-recipients').on('click', function(){
    var filter = $(this).data('filter');
    
    // Update active button
    $('.filter-recipients').removeClass('active');
    $(this).addClass('active');
    
    // Filter rows
    if(filter === 'all'){
      $('.recipient-row').show();
    } else {
      $('.recipient-row').hide();
      $('.recipient-row[data-status="' + filter + '"]').show();
    }
  });
});
</script>

<!-- Recent Logs -->
<div class="row mt-3">
  <div class="col-md-12">
<<<<<<< HEAD
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Activity Log (Last 50)</h3>
=======
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Activity Log (Last 50)</h3>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Email</th>
              <th>Subject</th>
<<<<<<< HEAD
              <th>SMTP Used</th>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
              <th>Status</th>
              <th>Timestamp</th>
              <th>Error</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($logs)){ 
              foreach($logs as $log){
                $status_badge = 'secondary';
                switch($log->status){
                  case 'sent': $status_badge = 'success'; break;
                  case 'opened': $status_badge = 'info'; break;
                  case 'failed': $status_badge = 'danger'; break;
                }
<<<<<<< HEAD
                
                // Get SMTP name from log (now comes directly from JOIN)
                $smtp_name_used = '-';
                if(!empty($log->smtp_name)){
                  $smtp_name_used = htmlspecialchars($log->smtp_name);
                } elseif(!empty($log->smtp_config_id) && !empty($smtp_configs)){
                  // Fallback to old method if JOIN didn't work
                  foreach($smtp_configs as $smtp){
                    if($smtp->id == $log->smtp_config_id){
                      $smtp_name_used = htmlspecialchars($smtp->name);
                      break;
                    }
                  }
                }
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            ?>
            <tr>
              <td><?php echo htmlspecialchars($log->email); ?></td>
              <td><?php echo htmlspecialchars($log->subject); ?></td>
<<<<<<< HEAD
              <td><small class="text-info"><strong><?php echo $smtp_name_used; ?></strong><?php if(!empty($log->smtp_config_id)) echo ' (ID: '.$log->smtp_config_id.')'; ?></small></td>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($log->status); ?></span></td>
              <td><?php echo date('M d, Y H:i:s', strtotime($log->created_at)); ?></td>
              <td class="text-danger small"><?php echo $log->error_message ? htmlspecialchars(substr($log->error_message, 0, 50)) . '...' : '-'; ?></td>
              <td>
                <?php if($log->status == 'failed'){ ?>
                <button class="btn btn-sm btn-warning actionResendSingleEmail" data-recipient-id="<?php echo $log->recipient_id; ?>" title="Resend this email">
                  <i class="fe fe-refresh-cw"></i>
                </button>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
            </tr>
            <?php }} else { ?>
            <tr>
<<<<<<< HEAD
              <td colspan="7" class="text-center">No activity logs yet</td>
=======
              <td colspan="6" class="text-center">No activity logs yet</td>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Handle resend failed emails for campaign
  $(document).on('click', '.actionCampaignResendFailed', function(e){
    e.preventDefault();
    var ids = $(this).data('ids');
    
    if(!confirm('Are you sure you want to resend all failed emails for this campaign?')){
      return;
    }
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_campaign_resend_failed'); ?>',
      type: 'POST',
      dataType: 'JSON',
      data: {
        ids: ids
      },
      success: function(data){
        if(data.status == 'success'){
          _notif({
            message: data.message,
            type: data.status
          });
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          _notif({
            message: data.message,
            type: data.status
          });
        }
      }
    });
  });
  
  // Handle resend single email
  $(document).on('click', '.actionResendSingleEmail', function(e){
    e.preventDefault();
    var recipient_id = $(this).data('recipient-id');
    var $btn = $(this);
    
    if(!confirm('Are you sure you want to resend this email?')){
      return;
    }
    
    $btn.prop('disabled', true);
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_resend_single_email'); ?>',
      type: 'POST',
      dataType: 'JSON',
      data: {
        recipient_id: recipient_id
      },
      success: function(data){
        $btn.prop('disabled', false);
        if(data.status == 'success'){
          _notif({
            message: data.message,
            type: data.status
          });
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          _notif({
            message: data.message,
            type: data.status
          });
        }
      },
      error: function(){
        $btn.prop('disabled', false);
        _notif({
          message: 'An error occurred',
          type: 'error'
        });
      }
    });
  });
});
</script>
<<<<<<< HEAD

<!-- Include email marketing interactive features -->
<script src="<?php echo BASE; ?>assets/js/email_marketing.js"></script>
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
