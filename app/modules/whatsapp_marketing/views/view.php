<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> Campaign Details: <?php echo $campaign->campaign_name; ?></h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-arrow-left"></i> Back to Campaigns
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <!-- Campaign Info -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="text-muted mb-2">Status</h6>
                <?php
                $status_colors = [
                  'pending' => 'secondary',
                  'running' => 'primary',
                  'paused' => 'warning',
                  'completed' => 'success',
                  'cancelled' => 'danger'
                ];
                $color = isset($status_colors[$campaign->status]) ? $status_colors[$campaign->status] : 'secondary';
                ?>
                <h4><span class="badge badge-<?php echo $color; ?>"><?php echo ucfirst($campaign->status); ?></span></h4>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total Recipients</h6>
                <h4><?php echo $campaign->total_recipients; ?></h4>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="text-muted mb-2">API Profile</h6>
                <h6><?php echo $campaign->profile_name; ?></h6>
              </div>
            </div>
          </div>
          
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="text-muted mb-2">Created</h6>
                <h6><?php echo date('Y-m-d H:i', strtotime($campaign->created)); ?></h6>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Statistics -->
        <h4 class="mb-3">Campaign Statistics</h4>
        <div class="row mb-4">
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Total</h6>
                <h3><?php echo $stats['total']; ?></h3>
              </div>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Pending</h6>
                <h3 class="text-secondary"><?php echo $stats['pending']; ?></h3>
              </div>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Sent</h6>
                <h3 class="text-success"><?php echo $stats['sent']; ?></h3>
              </div>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Delivered</h6>
                <h3 class="text-info"><?php echo $stats['delivered']; ?></h3>
              </div>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Failed</h6>
                <h3 class="text-danger"><?php echo $stats['failed']; ?></h3>
              </div>
            </div>
          </div>
          
          <div class="col-md-2">
            <div class="card text-center">
              <div class="card-body">
                <h6 class="text-muted">Remaining</h6>
                <h3><?php echo $campaign->total_recipients - $campaign->sent_count; ?></h3>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Message Content -->
        <div class="card mb-4">
          <div class="card-header">
            <h4 class="card-title">Message Content</h4>
          </div>
          <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($campaign->message_content)); ?></p>
          </div>
        </div>
        
        <!-- Sending Limits -->
        <?php if ($campaign->limit_per_hour || $campaign->limit_per_day): ?>
        <div class="card mb-4">
          <div class="card-header">
            <h4 class="card-title">Sending Limits</h4>
          </div>
          <div class="card-body">
            <?php if ($campaign->limit_per_hour): ?>
            <p><strong>Per Hour:</strong> <?php echo $campaign->limit_per_hour; ?> messages</p>
            <?php endif; ?>
            <?php if ($campaign->limit_per_day): ?>
            <p><strong>Per Day:</strong> <?php echo $campaign->limit_per_day; ?> messages</p>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Sample Recipients -->
        <?php if (!empty($recipients)): ?>
        <h4 class="mb-3">Sample Recipients (First 10)</h4>
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>Phone Number</th>
                <th>Username</th>
                <th>Email</th>
                <th>Balance</th>
                <th>Source</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recipients as $recipient): ?>
              <tr>
                <td><?php echo $recipient->phone_number; ?></td>
                <td><?php echo $recipient->username; ?></td>
                <td><?php echo $recipient->email; ?></td>
                <td><?php echo $recipient->balance; ?></td>
                <td><span class="badge badge-secondary"><?php echo ucfirst($recipient->source); ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
        
        <!-- Message Logs -->
        <h4 class="mb-3">Message Logs</h4>
        
        <!-- Filter -->
        <form method="get" action="<?php echo cn('whatsapp_marketing/view/' . $campaign->id); ?>" class="mb-3">
          <div class="row">
            <div class="col-md-3">
              <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="pending" <?php echo (get('status') == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="sent" <?php echo (get('status') == 'sent') ? 'selected' : ''; ?>>Sent</option>
                <option value="delivered" <?php echo (get('status') == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                <option value="failed" <?php echo (get('status') == 'failed') ? 'selected' : ''; ?>>Failed</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary">Filter</button>
              <a href="<?php echo cn('whatsapp_marketing/view/' . $campaign->id); ?>" class="btn btn-secondary">Reset</a>
            </div>
            <div class="col-md-7 text-right">
              <a href="<?php echo cn('whatsapp_marketing/export/' . $campaign->id . '/csv'); ?>" class="btn btn-success">
                <i class="fe fe-download"></i> Export CSV
              </a>
            </div>
          </div>
        </form>
        
        <?php if (!empty($messages)): ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered table-sm">
            <thead>
              <tr>
                <th>ID</th>
                <th>Phone Number</th>
                <th>Username</th>
                <th>Status</th>
                <th>Sent At</th>
                <th>Error</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($messages as $msg): ?>
              <tr>
                <td><?php echo $msg->id; ?></td>
                <td><?php echo $msg->phone_number; ?></td>
                <td><?php echo $msg->username; ?></td>
                <td>
                  <?php
                  $msg_colors = [
                    'pending' => 'secondary',
                    'sent' => 'success',
                    'delivered' => 'info',
                    'failed' => 'danger'
                  ];
                  $msg_color = isset($msg_colors[$msg->status]) ? $msg_colors[$msg->status] : 'secondary';
                  ?>
                  <span class="badge badge-<?php echo $msg_color; ?>"><?php echo ucfirst($msg->status); ?></span>
                </td>
                <td><?php echo $msg->sent_at ? date('Y-m-d H:i:s', strtotime($msg->sent_at)) : '-'; ?></td>
                <td><?php echo $msg->error_message ? '<small class="text-danger">' . $msg->error_message . '</small>' : '-'; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          <?php echo $links; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
          <i class="fe fe-info"></i> No message logs found.
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
