<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/email_marketing-responsive.css">

<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fas fa-ban text-danger"></i> Bounce & Suppression List
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Dashboard
        </a>
        <a href="javascript:void(0)" class="btn btn-sm btn-primary actionItem" 
           data-action="<?php echo cn($module . '/ajax_run_bounce_detection'); ?>"
           data-bs-toggle="tooltip" 
           title="Manually run bounce detection">
          <i class="fe fe-refresh-cw"></i> Run Bounce Detection
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row">
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h6 class="mb-0">Total Suppressed</h6>
          <h2 class="display-4 text-danger"><?php echo $stats['total_suppressed']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h6 class="mb-0">Hard Bounces</h6>
          <h2 class="display-4 text-warning"><?php echo $stats['hard_bounces']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h6 class="mb-0">Soft Bounces</h6>
          <h2 class="display-4 text-info"><?php echo $stats['soft_bounces']; ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <div class="text-center">
          <h6 class="mb-0">Invalid Emails</h6>
          <h2 class="display-4 text-secondary"><?php echo $stats['invalid']; ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row" id="result_ajaxSearch">
  <?php if(!empty($bounces)){ ?>
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Bounced & Suppressed Emails</h3>
        <div class="card-options">
          <a href="#" class="card-options-collapse" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Email</th>
              <th>Bounce Type</th>
              <th>Reason</th>
              <th>Code</th>
              <th>SMTP Config</th>
              <th>Retry Count</th>
              <th>Status</th>
              <th>Last Bounce</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($bounces as $bounce) {
              $i++;
              
              // Badge colors for bounce types
              $type_badge = [
                'hard' => 'danger',
                'soft' => 'warning',
                'invalid' => 'secondary',
                'spam_complaint' => 'dark',
                'unsubscribe' => 'info',
                'manual' => 'primary'
              ];
              $badge_color = $type_badge[$bounce->bounce_type] ?? 'secondary';
              
              // Status badge
              $status_badge = [
                'active' => 'danger',
                'temporary' => 'warning',
                'removed' => 'success'
              ];
              $status_color = $status_badge[$bounce->status] ?? 'secondary';
            ?>
            <tr class="tr_<?php echo $bounce->id; ?>">
              <td class="w-1"><?php echo $i; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($bounce->email); ?></strong>
              </td>
              <td>
                <span class="badge bg-<?php echo $badge_color; ?>">
                  <?php echo strtoupper(str_replace('_', ' ', $bounce->bounce_type)); ?>
                </span>
              </td>
              <td>
                <small><?php echo htmlspecialchars(substr($bounce->bounce_reason, 0, 50)); ?></small>
              </td>
              <td>
                <?php if($bounce->bounce_code){ ?>
                <span class="badge bg-secondary"><?php echo $bounce->bounce_code; ?></span>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td>
                <?php if($bounce->smtp_name){ ?>
                <small><?php echo htmlspecialchars($bounce->smtp_name); ?></small>
                <?php } else { ?>
                <span class="text-muted">-</span>
                <?php } ?>
              </td>
              <td>
                <span class="badge bg-<?php echo $bounce->retry_count > 3 ? 'danger' : 'secondary'; ?>">
                  <?php echo $bounce->retry_count; ?>
                </span>
              </td>
              <td>
                <span class="badge bg-<?php echo $status_color; ?>">
                  <?php echo strtoupper($bounce->status); ?>
                </span>
                <?php if($bounce->status == 'temporary' && $bounce->expires_at){ ?>
                <br><small class="text-muted">Expires: <?php echo date('Y-m-d H:i', strtotime($bounce->expires_at)); ?></small>
                <?php } ?>
              </td>
              <td>
                <small><?php echo date('Y-m-d H:i', strtotime($bounce->last_bounce_at)); ?></small>
              </td>
              <td>
                <?php if($bounce->status == 'active' || $bounce->status == 'temporary'){ ?>
                <a href="javascript:void(0)" 
                  class="btn btn-sm btn-icon btn-success actionItem" 
                  data-id="<?php echo $bounce->ids; ?>" 
                  data-action="<?php echo cn($module . '/ajax_remove_bounce'); ?>" 
                  data-bs-toggle="tooltip" 
                  title="Remove from suppression list" 
                  data-confirm="Are you sure you want to remove this email from suppression list?">
                  <i class="fe fe-check"></i>
                </a>
                <?php } else { ?>
                <span class="text-muted">Already removed</span>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <?php if($total > $per_page){ ?>
    <div class="card-footer">
      <div class="row">
        <div class="col">
          <div class="dataTables_info">
            Showing <?php echo (($page - 1) * $per_page) + 1; ?> to 
            <?php echo min($page * $per_page, $total); ?> of <?php echo $total; ?> entries
          </div>
        </div>
        <div class="col text-end">
          <div class="dataTables_paginate">
            <?php 
            $total_pages = ceil($total / $per_page);
            for($p = 1; $p <= $total_pages; $p++){ 
              if($p == $page){
            ?>
            <a href="<?php echo cn($module . '/bounces/' . $p); ?>" 
               class="btn btn-sm btn-primary"><?php echo $p; ?></a>
            <?php } else { ?>
            <a href="<?php echo cn($module . '/bounces/' . $p); ?>" 
               class="btn btn-sm btn-secondary"><?php echo $p; ?></a>
            <?php } 
            } ?>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty">
          <div class="empty-icon">
            <i class="fas fa-check-circle fa-4x text-success"></i>
          </div>
          <p class="empty-title">No Bounced Emails</p>
          <p class="empty-subtitle text-muted">
            Your suppression list is empty. All emails are clean!
          </p>
          <div class="empty-action">
            <a href="<?php echo cn($module); ?>" class="btn btn-primary">
              <i class="fe fe-arrow-left"></i> Back to Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
$(document).ready(function() {
  // Handle action items (run bounce detection, remove bounce)
  $('.actionItem').on('click', function(e) {
    e.preventDefault();
    
    var $this = $(this);
    var action = $this.data('action');
    var id = $this.data('id');
    var confirm_msg = $this.data('confirm');
    
    if (confirm_msg && !confirm(confirm_msg)) {
      return false;
    }
    
    // Show loading
    $this.prop('disabled', true);
    var original_html = $this.html();
    $this.html('<i class="fa fa-spinner fa-spin"></i>');
    
    $.ajax({
      url: action,
      type: 'POST',
      dataType: 'JSON',
      data: { ids: id },
      success: function(response) {
        if (response.status == 'success') {
          show_message('success', response.message);
          setTimeout(function() {
            location.reload();
          }, 1500);
        } else {
          show_message('error', response.message);
          $this.prop('disabled', false);
          $this.html(original_html);
        }
      },
      error: function() {
        show_message('error', 'An error occurred');
        $this.prop('disabled', false);
        $this.html(original_html);
      }
    });
  });
});
</script>
