<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  
  <!-- Page Header -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">
            <i class="fas fa-ban"></i> Email Suppression List
          </h4>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Statistics Cards -->
  <div class="row">
    <div class="col-md-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Total Suppressed</h5>
          <h2 class="text-primary"><?php echo number_format($stats->total_suppressed); ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Bounced</h5>
          <h2 class="text-danger"><?php echo number_format($stats->bounced); ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Invalid</h5>
          <h2 class="text-warning"><?php echo number_format($stats->invalid); ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Complaints</h5>
          <h2 class="text-info"><?php echo number_format($stats->complaints); ?></h2>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Suppression List</h4>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addSuppressionModal">
              <i class="fas fa-plus"></i> Add Email
            </button>
          </div>
        </div>
        
        <div class="card-body">
          <?php if(empty($suppression_list)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No suppressed emails found. Emails will be automatically added when bounces are detected via IMAP.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Email</th>
                    <th>Reason</th>
                    <th>Details</th>
                    <th>Bounce Count</th>
                    <th>First/Last Bounce</th>
                    <th>Added By</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($suppression_list as $item): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($item->email); ?></strong>
                      </td>
                      <td>
                        <?php
                        $badge_class = 'secondary';
                        if($item->reason == 'bounced') $badge_class = 'danger';
                        elseif($item->reason == 'invalid') $badge_class = 'warning';
                        elseif($item->reason == 'complaint') $badge_class = 'info';
                        elseif($item->reason == 'manual') $badge_class = 'primary';
                        ?>
                        <span class="badge bg-<?php echo $badge_class; ?>">
                          <?php echo ucfirst($item->reason); ?>
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($item->reason_detail ?: '-'); ?>
                        </small>
                      </td>
                      <td>
                        <span class="badge bg-secondary"><?php echo $item->bounce_count; ?></span>
                      </td>
                      <td>
                        <small>
                          <?php if($item->first_bounce_date): ?>
                            <strong>First:</strong> <?php echo date('Y-m-d H:i', strtotime($item->first_bounce_date)); ?><br>
                          <?php endif; ?>
                          <?php if($item->last_bounce_date): ?>
                            <strong>Last:</strong> <?php echo date('Y-m-d H:i', strtotime($item->last_bounce_date)); ?>
                          <?php endif; ?>
                        </small>
                      </td>
                      <td>
                        <span class="badge bg-<?php echo $item->added_by == 'auto' ? 'success' : 'primary'; ?>">
                          <?php echo ucfirst($item->added_by); ?>
                        </span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-danger remove-suppression" data-ids="<?php echo $item->ids; ?>">
                          <i class="fas fa-trash"></i> Remove
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <?php if($total > $per_page): ?>
              <div class="mt-3">
                <?php
                $total_pages = ceil($total / $per_page);
                ?>
                <nav>
                  <ul class="pagination">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                      <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo cn($module . '/suppression_list/' . $i); ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>
                  </ul>
                </nav>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  
</div>

<!-- Add Suppression Modal -->
<div class="modal fade" id="addSuppressionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="actionForm" action="<?php echo cn($module . '/ajax_add_to_suppression'); ?>" method="POST" data-redirect="<?php echo cn($module . '/suppression_list'); ?>">
        <div class="modal-header">
          <h5 class="modal-title">Add Email to Suppression List</h5>
          <button type="button" class="btn-close" data-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" required placeholder="user@example.com">
          </div>
          <div class="form-group">
            <label>Reason</label>
            <select class="form-control" name="reason">
              <option value="manual">Manual Suppression</option>
              <option value="bounced">Bounced</option>
              <option value="invalid">Invalid</option>
              <option value="complaint">Complaint</option>
              <option value="unsubscribed">Unsubscribed</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notes (Optional)</label>
            <textarea class="form-control" name="notes" rows="3" placeholder="Add any notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add to Suppression List</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Remove from suppression list
  $('.remove-suppression').on('click', function() {
    var ids = $(this).data('ids');
    
    if(confirm('Are you sure you want to remove this email from suppression list?')) {
      $.post('<?php echo cn($module . '/ajax_remove_from_suppression'); ?>', {
        ids: ids
      }, function(response) {
        if(response.status == 'success') {
          location.reload();
        } else {
          alert(response.message);
        }
      }, 'json');
    }
  });
});
</script>
