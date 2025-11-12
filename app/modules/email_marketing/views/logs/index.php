<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fa fa-list-alt"></i> Email Logs
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
        <a href="<?php echo cn($module . '/export_logs?' . http_build_query($filters)); ?>" class="btn btn-sm btn-primary">
          <i class="fe fe-download"></i> Export to CSV
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-filter"></i> Filters</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="card-body">
        <form method="get" action="<?php echo cn($module . '/logs'); ?>" id="filter_form">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Campaign</label>
                <select name="campaign_id" class="form-control">
                  <option value="">All Campaigns</option>
                  <?php foreach($campaigns as $campaign){ ?>
                    <option value="<?php echo $campaign->id; ?>" <?php echo (isset($filters['campaign_id']) && $filters['campaign_id'] == $campaign->id) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($campaign->name); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="">All Statuses</option>
                  <option value="queued" <?php echo (isset($filters['status']) && $filters['status'] == 'queued') ? 'selected' : ''; ?>>Queued</option>
                  <option value="sent" <?php echo (isset($filters['status']) && $filters['status'] == 'sent') ? 'selected' : ''; ?>>Sent</option>
                  <option value="failed" <?php echo (isset($filters['status']) && $filters['status'] == 'failed') ? 'selected' : ''; ?>>Failed</option>
                  <option value="opened" <?php echo (isset($filters['status']) && $filters['status'] == 'opened') ? 'selected' : ''; ?>>Opened</option>
                  <option value="bounced" <?php echo (isset($filters['status']) && $filters['status'] == 'bounced') ? 'selected' : ''; ?>>Bounced</option>
                </select>
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" placeholder="Search by email..." value="<?php echo isset($filters['email']) ? htmlspecialchars($filters['email']) : ''; ?>">
              </div>
            </div>
            
            <div class="col-md-3">
              <div class="form-group">
                <label>Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?php echo isset($filters['date_from']) ? $filters['date_from'] : ''; ?>">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?php echo isset($filters['date_to']) ? $filters['date_to'] : ''; ?>">
              </div>
            </div>
            
            <div class="col-md-9">
              <div class="form-group">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary">
                  <i class="fe fe-search"></i> Apply Filters
                </button>
                <a href="<?php echo cn($module . '/logs'); ?>" class="btn btn-secondary">
                  <i class="fe fe-x"></i> Clear Filters
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Logs Table -->
<div class="row">
  <?php if(!empty($logs)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Email Logs 
          <span class="badge badge-primary"><?php echo number_format($total); ?> Total</span>
        </h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-striped">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Campaign</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Details</th>
              <th class="w-1">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($logs as $log) {
              $i++;
              
              // Status badge
              $status_class = 'secondary';
              $status_icon = 'fe-clock';
              switch($log->status){
                case 'sent':
                  $status_class = 'success';
                  $status_icon = 'fe-check';
                  break;
                case 'failed':
                  $status_class = 'danger';
                  $status_icon = 'fe-x';
                  break;
                case 'opened':
                  $status_class = 'info';
                  $status_icon = 'fe-mail';
                  break;
                case 'bounced':
                  $status_class = 'warning';
                  $status_icon = 'fe-alert-triangle';
                  break;
              }
            ?>
            <tr class="tr_<?php echo $log->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($log->campaign_name); ?></strong>
              </td>
              <td>
                <div>
                  <strong><?php echo htmlspecialchars($log->email); ?></strong>
                  <?php if($log->recipient_name){ ?>
                    <br><small class="text-muted"><?php echo htmlspecialchars($log->recipient_name); ?></small>
                  <?php } ?>
                </div>
              </td>
              <td>
                <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($log->subject); ?>">
                  <?php echo htmlspecialchars($log->subject); ?>
                </div>
              </td>
              <td>
                <span class="badge badge-<?php echo $status_class; ?>">
                  <i class="<?php echo $status_icon; ?>"></i> <?php echo ucfirst($log->status); ?>
                </span>
              </td>
              <td>
                <?php if($log->sent_at){ ?>
                  <div>
                    <?php echo date('M d, Y', strtotime($log->sent_at)); ?>
                    <br><small class="text-muted"><?php echo date('h:i A', strtotime($log->sent_at)); ?></small>
                  </div>
                <?php } else { ?>
                  <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td>
                <?php if($log->opened_at){ ?>
                  <small class="text-success">
                    <i class="fe fe-eye"></i> Opened: <?php echo date('M d, Y h:i A', strtotime($log->opened_at)); ?>
                  </small>
                <?php } ?>
                <?php if($log->error_message){ ?>
                  <div class="text-danger small" style="max-width: 250px;" title="<?php echo htmlspecialchars($log->error_message); ?>">
                    <i class="fe fe-alert-circle"></i> 
                    <span class="text-truncate d-inline-block" style="max-width: 220px;">
                      <?php echo htmlspecialchars($log->error_message); ?>
                    </span>
                  </div>
                <?php } ?>
                <?php if($log->ip_address){ ?>
                  <div class="text-muted small">
                    <i class="fe fe-globe"></i> <?php echo htmlspecialchars($log->ip_address); ?>
                  </div>
                <?php } ?>
                <div class="text-muted small">
                  <i class="fe fe-calendar"></i> Created: <?php echo date('M d, Y h:i A', strtotime($log->created_at)); ?>
                </div>
              </td>
              <td class="text-right">
                <div class="item-action dropdown">
                  <a href="javascript:void(0)" data-toggle="dropdown" class="icon"><i class="fe fe-more-vertical"></i></a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript:void(0)" 
                       class="dropdown-item actionItem" 
                       data-id="<?php echo $log->ids; ?>" 
                       data-type="delete_log">
                      <i class="dropdown-icon fe fe-trash text-danger"></i> Delete
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <?php if($total > $per_page){ ?>
      <div class="card-footer">
        <div class="row">
          <div class="col-md-6">
            <p class="m-0">
              Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total); ?> of <?php echo number_format($total); ?> entries
            </p>
          </div>
          <div class="col-md-6">
            <ul class="pagination justify-content-end mb-0">
              <?php
              $total_pages = ceil($total / $per_page);
              $current_page = $page;
              
              // Previous button
              if($current_page > 1){
                $prev_filters = $filters;
                echo '<li class="page-item"><a class="page-link" href="' . cn($module . '/logs/' . ($current_page - 1) . '?' . http_build_query($prev_filters)) . '">Previous</a></li>';
              }
              
              // Page numbers
              $start_page = max(1, $current_page - 2);
              $end_page = min($total_pages, $current_page + 2);
              
              if($start_page > 1){
                echo '<li class="page-item"><a class="page-link" href="' . cn($module . '/logs/1?' . http_build_query($filters)) . '">1</a></li>';
                if($start_page > 2){
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
              }
              
              for($p = $start_page; $p <= $end_page; $p++){
                $active = ($p == $current_page) ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . cn($module . '/logs/' . $p . '?' . http_build_query($filters)) . '">' . $p . '</a></li>';
              }
              
              if($end_page < $total_pages){
                if($end_page < $total_pages - 1){
                  echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                echo '<li class="page-item"><a class="page-link" href="' . cn($module . '/logs/' . $total_pages . '?' . http_build_query($filters)) . '">' . $total_pages . '</a></li>';
              }
              
              // Next button
              if($current_page < $total_pages){
                $next_filters = $filters;
                echo '<li class="page-item"><a class="page-link" href="' . cn($module . '/logs/' . ($current_page + 1) . '?' . http_build_query($next_filters)) . '">Next</a></li>';
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php } else { ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty-icon">
          <i class="fe fe-inbox" style="font-size: 48px; color: #ccc;"></i>
        </div>
        <p class="empty-text">No email logs found</p>
        <p class="empty-subtitle text-muted">
          <?php if(!empty($filters)){ ?>
            Try adjusting your filters or <a href="<?php echo cn($module . '/logs'); ?>">clear all filters</a>
          <?php } else { ?>
            Email logs will appear here once campaigns start sending emails
          <?php } ?>
        </p>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
$(document).ready(function(){
  // Handle delete action
  $(document).on('click', '.actionItem[data-type="delete_log"]', function(e){
    e.preventDefault();
    var ids = $(this).data('id');
    
    if(confirm('Are you sure you want to delete this log entry?')){
      $.ajax({
        url: '<?php echo cn($module . "/ajax_delete_log"); ?>',
        type: 'POST',
        dataType: 'JSON',
        data: {ids: ids},
        success: function(response){
          if(response.status == 'success'){
            show_message(response.status, response.message);
            setTimeout(function(){
              location.reload();
            }, 1000);
          } else {
            show_message('error', response.message);
          }
        }
      });
    }
  });
});
</script>
