<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> <?php echo $module_name; ?></h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing/create'); ?>" class="btn btn-primary btn-sm">
            <i class="fe fe-plus"></i> Create Campaign
          </a>
          <a href="<?php echo cn('whatsapp_marketing/api_config'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-settings"></i> API Settings
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <!-- Filter form -->
        <form method="get" action="<?php echo cn('whatsapp_marketing'); ?>" class="mb-3">
          <div class="row">
            <div class="col-md-3">
              <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="pending" <?php echo (get('status') == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="running" <?php echo (get('status') == 'running') ? 'selected' : ''; ?>>Running</option>
                <option value="paused" <?php echo (get('status') == 'paused') ? 'selected' : ''; ?>>Paused</option>
                <option value="completed" <?php echo (get('status') == 'completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo (get('status') == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
              </select>
            </div>
            <div class="col-md-4">
              <input type="text" name="search" class="form-control" placeholder="Search campaign name..." value="<?php echo get('search'); ?>">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-primary">Filter</button>
              <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary">Reset</a>
            </div>
          </div>
        </form>

        <?php if (!empty($campaigns)): ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th width="5%">ID</th>
                <th width="20%">Campaign Name</th>
                <th width="15%">API Profile</th>
                <th width="10%">Status</th>
                <th width="15%">Statistics</th>
                <th width="10%">Created</th>
                <th width="25%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($campaigns as $campaign): ?>
              <tr>
                <td><?php echo $campaign->id; ?></td>
                <td>
                  <strong><?php echo $campaign->campaign_name; ?></strong><br>
                  <small class="text-muted">Recipients: <?php echo $campaign->total_recipients; ?></small>
                </td>
                <td><?php echo $campaign->profile_name; ?></td>
                <td>
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
                  <span class="badge badge-<?php echo $color; ?>"><?php echo ucfirst($campaign->status); ?></span>
                </td>
                <td>
                  <small>
                    Total: <?php echo $campaign->stats['total']; ?><br>
                    Sent: <span class="text-success"><?php echo $campaign->stats['sent']; ?></span><br>
                    Delivered: <span class="text-info"><?php echo $campaign->stats['delivered']; ?></span><br>
                    Failed: <span class="text-danger"><?php echo $campaign->stats['failed']; ?></span><br>
                    Remaining: <?php echo $campaign->remaining; ?>
                  </small>
                </td>
                <td>
                  <small><?php echo date('Y-m-d H:i', strtotime($campaign->created)); ?></small>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="<?php echo cn('whatsapp_marketing/view/' . $campaign->id); ?>" class="btn btn-info" title="View Details">
                      <i class="fe fe-eye"></i>
                    </a>
                    
                    <?php if ($campaign->status == 'pending'): ?>
                    <a href="<?php echo cn('whatsapp_marketing/start/' . $campaign->id); ?>" class="btn btn-success ajaxStart" title="Start Campaign">
                      <i class="fe fe-play"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($campaign->status == 'running'): ?>
                    <a href="<?php echo cn('whatsapp_marketing/pause/' . $campaign->id); ?>" class="btn btn-warning ajaxPause" title="Pause Campaign">
                      <i class="fe fe-pause"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($campaign->status == 'paused'): ?>
                    <a href="<?php echo cn('whatsapp_marketing/resume/' . $campaign->id); ?>" class="btn btn-primary ajaxResume" title="Resume Campaign">
                      <i class="fe fe-play"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($campaign->status != 'running'): ?>
                    <a href="<?php echo cn('whatsapp_marketing/edit/' . $campaign->id); ?>" class="btn btn-secondary" title="Edit Campaign">
                      <i class="fe fe-edit"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       data-url="<?php echo cn('whatsapp_marketing/delete/' . $campaign->ids); ?>" 
                       class="btn btn-danger ajaxDeleteItem" 
                       title="Delete Campaign">
                      <i class="fe fe-trash"></i>
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?php echo cn('whatsapp_marketing/export/' . $campaign->id . '/csv'); ?>" class="btn btn-success" title="Export CSV">
                      <i class="fe fe-download"></i>
                    </a>
                  </div>
                </td>
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
          <i class="fe fe-info"></i> No campaigns found. <a href="<?php echo cn('whatsapp_marketing/create'); ?>">Create your first campaign</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.ajaxStart, .ajaxPause, .ajaxResume').on('click', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    if (confirm('Are you sure you want to perform this action?')) {
      window.location.href = url;
    }
  });
  
  $('.ajaxDeleteItem').on('click', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    if (confirm('Are you sure you want to delete this campaign? All data will be lost.')) {
      window.location.href = url;
    }
  });
});
</script>
