<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container-fluid">
  
  <!-- Page Header -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
              <i class="fas fa-exclamation-triangle"></i> Bounce Detection Logs
            </h4>
            <button class="btn btn-primary" id="runBounceDetection">
              <i class="fas fa-sync"></i> Run Bounce Detection Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Statistics Cards -->
  <div class="row">
    <div class="col-md-4 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Total Bounces</h5>
          <h2 class="text-danger"><?php echo number_format($stats->total_bounces); ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Hard Bounces</h5>
          <h2 class="text-danger"><?php echo number_format($stats->hard_bounces); ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-6">
      <div class="card">
        <div class="card-body">
          <h5 class="text-muted">Soft Bounces</h5>
          <h2 class="text-warning"><?php echo number_format($stats->soft_bounces); ?></h2>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Main Content -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Bounce Detection Logs</h4>
          <p class="text-muted small mb-0">Automatically detected bounce emails from IMAP monitoring</p>
        </div>
        
        <div class="card-body">
          <?php if(empty($bounce_logs)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No bounce logs found yet. Enable IMAP bounce detection in SMTP configurations and run the bounce detection cron job.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Bounced Email</th>
                    <th>Bounce Type</th>
                    <th>Reason</th>
                    <th>SMTP Code</th>
                    <th>SMTP Config</th>
                    <th>Detected At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($bounce_logs as $log): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars($log->bounced_email); ?></strong>
                      </td>
                      <td>
                        <?php
                        $badge_class = 'secondary';
                        if($log->bounce_type == 'hard') $badge_class = 'danger';
                        elseif($log->bounce_type == 'soft') $badge_class = 'warning';
                        elseif($log->bounce_type == 'complaint') $badge_class = 'info';
                        ?>
                        <span class="badge bg-<?php echo $badge_class; ?>">
                          <?php echo ucfirst($log->bounce_type); ?>
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">
                          <?php echo htmlspecialchars($log->bounce_reason ?: 'Unknown'); ?>
                        </small>
                      </td>
                      <td>
                        <?php if($log->bounce_code): ?>
                          <code><?php echo htmlspecialchars($log->bounce_code); ?></code>
                        <?php else: ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <small><?php echo htmlspecialchars($log->smtp_name ?: 'Unknown'); ?></small>
                      </td>
                      <td>
                        <small><?php echo date('Y-m-d H:i:s', strtotime($log->detected_at)); ?></small>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-info view-details" 
                                data-bounce-id="<?php echo $log->id; ?>"
                                data-email="<?php echo htmlspecialchars($log->bounced_email, ENT_QUOTES); ?>"
                                data-type="<?php echo htmlspecialchars($log->bounce_type, ENT_QUOTES); ?>"
                                data-reason="<?php echo htmlspecialchars($log->bounce_reason ?: 'Unknown', ENT_QUOTES); ?>"
                                data-code="<?php echo htmlspecialchars($log->bounce_code ?: '', ENT_QUOTES); ?>"
                                data-smtp="<?php echo htmlspecialchars($log->smtp_name ?: 'Unknown', ENT_QUOTES); ?>"
                                data-detected="<?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($log->detected_at)), ENT_QUOTES); ?>"
                                data-details="<?php echo htmlspecialchars($log->parsed_details ?: '', ENT_QUOTES); ?>">
                          <i class="fas fa-eye"></i> View
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
                        <a class="page-link" href="<?php echo cn($module . '/bounce_logs/' . $i); ?>"><?php echo $i; ?></a>
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

<!-- Bounce Details Modal -->
<div class="modal fade" id="bounceDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bounce Details</h5>
        <button type="button" class="btn-close" data-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="bounceDetailsContent"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Run bounce detection manually
  $('#runBounceDetection').on('click', function() {
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Running...');
    
    $.post('<?php echo cn($module . '/ajax_run_bounce_detection'); ?>', {}, function(response) {
      if(response.status == 'success') {
        alert(response.message);
        location.reload();
      } else {
        alert('Error: ' + response.message);
        $btn.prop('disabled', false).html('<i class="fas fa-sync"></i> Run Bounce Detection Now');
      }
    }, 'json').fail(function() {
      alert('Failed to run bounce detection. Please try again.');
      $btn.prop('disabled', false).html('<i class="fas fa-sync"></i> Run Bounce Detection Now');
    });
  });
  
  // View bounce details
  $('.view-details').on('click', function() {
    var $btn = $(this);
    var log = {
      email: $btn.data('email'),
      type: $btn.data('type'),
      reason: $btn.data('reason'),
      code: $btn.data('code'),
      smtp: $btn.data('smtp'),
      detected: $btn.data('detected'),
      details: $btn.data('details')
    };
    
    var html = '<dl class="row">';
    html += '<dt class="col-sm-4">Email:</dt><dd class="col-sm-8"><strong>' + escapeHtml(log.email) + '</strong></dd>';
    html += '<dt class="col-sm-4">Bounce Type:</dt><dd class="col-sm-8"><span class="badge bg-' + 
            (log.type == 'hard' ? 'danger' : (log.type == 'soft' ? 'warning' : 'info')) + '">' + 
            escapeHtml(log.type.toUpperCase()) + '</span></dd>';
    html += '<dt class="col-sm-4">Reason:</dt><dd class="col-sm-8">' + escapeHtml(log.reason || 'Unknown') + '</dd>';
    if(log.code) {
      html += '<dt class="col-sm-4">SMTP Code:</dt><dd class="col-sm-8"><code>' + escapeHtml(log.code) + '</code></dd>';
    }
    html += '<dt class="col-sm-4">SMTP Config:</dt><dd class="col-sm-8">' + escapeHtml(log.smtp || 'Unknown') + '</dd>';
    html += '<dt class="col-sm-4">Detected At:</dt><dd class="col-sm-8">' + escapeHtml(log.detected) + '</dd>';
    
    if(log.details) {
      try {
        var details = JSON.parse(log.details);
        html += '<dt class="col-sm-4">Subject:</dt><dd class="col-sm-8">' + escapeHtml(details.subject || '-') + '</dd>';
        html += '<dt class="col-sm-4">From:</dt><dd class="col-sm-8">' + escapeHtml(details.from || '-') + '</dd>';
      } catch(e) {}
    }
    
    html += '</dl>';
    
    $('#bounceDetailsContent').html(html);
    $('#bounceDetailsModal').modal('show');
  });
  
  // Helper function to escape HTML
  function escapeHtml(text) {
    if(!text) return '';
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
  }
});
</script>
