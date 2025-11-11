<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        Campaign Logs - <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Campaigns
        </a>
        <a href="<?php echo cn($module . '/ajax_export_logs/' . $campaign->ids); ?>" class="btn btn-sm btn-primary">
          <i class="fa fa-download"></i> Export Logs
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Campaign Statistics</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <div class="text-center">
              <div class="h1 m-0"><?php echo $campaign->total_messages; ?></div>
              <div class="text-muted mb-3">Total Messages</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="h1 m-0 text-success"><?php echo $campaign->sent_messages; ?></div>
              <div class="text-muted mb-3">Sent</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="h1 m-0 text-danger"><?php echo $campaign->failed_messages; ?></div>
              <div class="text-muted mb-3">Failed</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="text-center">
              <div class="h1 m-0 text-info"><?php echo $campaign->delivered_messages; ?></div>
              <div class="text-muted mb-3">Delivered</div>
            </div>
          </div>
        </div>
        
        <div class="progress progress-sm">
          <?php 
          $progress = 0;
          if($campaign->total_messages > 0){
            $progress = round(($campaign->sent_messages / $campaign->total_messages) * 100);
          }
          ?>
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" 
            aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
            <?php echo $progress; ?>%
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <?php if(!empty($logs)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Message Logs (<?php echo $total; ?> total)</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">ID</th>
              <th>Phone Number</th>
              <th>Message Preview</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Error/Response</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            foreach ($logs as $log) {
              $status_class = 'secondary';
              switch($log->status){
                case 'sent':
                  $status_class = 'success';
                  break;
                case 'delivered':
                  $status_class = 'info';
                  break;
                case 'failed':
                  $status_class = 'danger';
                  break;
              }
            ?>
            <tr>
              <td class="w-1"><?php echo $log->id; ?></td>
              <td><?php echo htmlspecialchars($log->phone_number); ?></td>
              <td>
                <small><?php echo htmlspecialchars(substr($log->message, 0, 50)); ?>...</small>
              </td>
              <td>
                <span class="badge badge-<?php echo $status_class; ?>">
                  <?php echo ucfirst($log->status); ?>
                </span>
              </td>
              <td>
                <?php echo $log->sent_at ? date('M d, Y H:i:s', strtotime($log->sent_at)) : '-'; ?>
              </td>
              <td>
                <?php if($log->error_message){ ?>
                  <small class="text-danger" title="<?php echo htmlspecialchars($log->error_message); ?>">
                    <?php echo htmlspecialchars(substr($log->error_message, 0, 50)); ?>...
                  </small>
                <?php } elseif($log->api_response){ ?>
                  <small class="text-muted" title="<?php echo htmlspecialchars($log->api_response); ?>">
                    API Response
                  </small>
                <?php } else { ?>
                  -
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <?php if($total > $per_page){ ?>
      <div class="card-footer">
        <?php echo pagination($module . '/logs/' . $campaign->ids, $total, $per_page, $page); ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php }else{ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty">
          <div class="empty-icon"><i class="fa fa-file-text"></i></div>
          <p class="empty-title">No logs found</p>
          <p class="empty-subtitle text-muted">
            Logs will appear here once the campaign starts sending messages.
          </p>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
