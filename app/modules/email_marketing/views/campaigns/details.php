<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-mail"></i> <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaigns
        </a>
        <a href="<?php echo cn($module . '/export_campaign_report/' . $campaign->ids); ?>" class="btn btn-sm btn-success">
          <i class="fe fe-download"></i> Export Report
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Campaign Stats -->
<div class="row">
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

<!-- Campaign Info -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign Information</h3>
      </div>
      <div class="card-body">
        <table class="table table-sm">
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
              <span class="badge badge-<?php echo $status_class; ?>"><?php echo ucfirst($campaign->status); ?></span>
            </td>
          </tr>
          <tr>
            <td><strong>Template:</strong></td>
            <td><?php echo htmlspecialchars($campaign->template_name); ?></td>
          </tr>
          <tr>
            <td><strong>SMTP:</strong></td>
            <td><?php echo htmlspecialchars($campaign->smtp_name); ?></td>
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
        </table>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Progress</h3>
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
            <div class="float-left"><strong><?php echo $progress; ?>% Complete</strong></div>
            <div class="float-right"><small class="text-muted"><?php echo $campaign->sent_emails; ?> / <?php echo $campaign->total_emails; ?></small></div>
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
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent Recipients -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recent Recipients (Last 100)</h3>
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
            </tr>
          </thead>
          <tbody>
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
            <tr>
              <td><?php echo htmlspecialchars($recipient->email); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($recipient->status); ?></span></td>
              <td><?php echo $recipient->sent_at ? date('M d, H:i', strtotime($recipient->sent_at)) : '-'; ?></td>
              <td><?php echo $recipient->opened_at ? date('M d, H:i', strtotime($recipient->opened_at)) : '-'; ?></td>
              <td class="text-danger small"><?php echo $recipient->error_message ? htmlspecialchars(substr($recipient->error_message, 0, 50)) . '...' : '-'; ?></td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="6" class="text-center">No recipients yet. <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>">Add recipients</a></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Recent Logs -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Activity Log (Last 50)</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Email</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Timestamp</th>
              <th>Error</th>
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
            ?>
            <tr>
              <td><?php echo htmlspecialchars($log->email); ?></td>
              <td><?php echo htmlspecialchars($log->subject); ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($log->status); ?></span></td>
              <td><?php echo date('M d, Y H:i:s', strtotime($log->created_at)); ?></td>
              <td class="text-danger small"><?php echo $log->error_message ? htmlspecialchars(substr($log->error_message, 0, 50)) . '...' : '-'; ?></td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="5" class="text-center">No activity logs yet</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
