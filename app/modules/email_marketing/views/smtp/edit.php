<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_smtp_edit/' . $smtp->ids); ?>" data-redirect="<?php echo cn($module . '/smtp'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit SMTP Configuration</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($smtp->name); ?>" placeholder="e.g., Gmail SMTP" required>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-8">
                      <div class="form-group">
                        <label>SMTP Host <span class="text-danger">*</span></label>
                        <input type="text" class="form-control square" name="host" value="<?php echo htmlspecialchars($smtp->host); ?>" placeholder="e.g., smtp.gmail.com" required>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Port <span class="text-danger">*</span></label>
                        <input type="number" class="form-control square" name="port" value="<?php echo $smtp->port; ?>" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label>Encryption</label>
                    <select class="form-control square" name="encryption">
                      <option value="none" <?php echo ($smtp->encryption == 'none') ? 'selected' : ''; ?>>None</option>
                      <option value="tls" <?php echo ($smtp->encryption == 'tls') ? 'selected' : ''; ?>>TLS</option>
                      <option value="ssl" <?php echo ($smtp->encryption == 'ssl') ? 'selected' : ''; ?>>SSL</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="username" value="<?php echo htmlspecialchars($smtp->username); ?>" placeholder="SMTP username" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control square" name="password" placeholder="Leave empty to keep current password">
                    <small class="text-muted">Leave blank to keep existing password</small>
                  </div>
                  
                  <div class="form-group">
                    <label>From Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="from_name" value="<?php echo htmlspecialchars($smtp->from_name); ?>" placeholder="e.g., SMM Panel" required>
                  </div>
                  
                  <div class="form-group">
                    <label>From Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control square" name="from_email" value="<?php echo htmlspecialchars($smtp->from_email); ?>" placeholder="e.g., noreply@example.com" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Reply-To Email</label>
                    <input type="email" class="form-control square" name="reply_to" value="<?php echo htmlspecialchars($smtp->reply_to); ?>" placeholder="e.g., support@example.com">
                  </div>
                  
                  <div class="form-group">
                    <label class="form-check">
                      <input type="checkbox" class="form-check-input" name="is_default" value="1" <?php echo $smtp->is_default ? 'checked' : ''; ?>>
                      <span class="form-check-label">Set as default SMTP</span>
                    </label>
                  </div>
                  
                  <hr class="my-4">
                  
                  <h5 class="mb-3">
                    <i class="fas fa-inbox"></i> IMAP Configuration (for Bounce Detection)
                  </h5>
                  
                  <div class="form-group">
                    <label class="form-check">
                      <input type="checkbox" class="form-check-input" name="imap_enabled" id="imap_enabled" value="1" <?php echo !empty($smtp->imap_enabled) ? 'checked' : ''; ?>>
                      <span class="form-check-label">Enable IMAP bounce detection</span>
                    </label>
                    <small class="form-text text-muted">
                      Automatically detect bounced emails by monitoring inbox
                    </small>
                  </div>
                  
                  <div id="imap_fields" style="display:<?php echo !empty($smtp->imap_enabled) ? 'block' : 'none'; ?>;">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group">
                          <label>IMAP Host</label>
                          <input type="text" class="form-control square" name="imap_host" value="<?php echo htmlspecialchars($smtp->imap_host ?? ''); ?>" placeholder="e.g., imap.gmail.com">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>IMAP Port</label>
                          <input type="number" class="form-control square" name="imap_port" value="<?php echo $smtp->imap_port ?? 993; ?>">
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label>IMAP Encryption</label>
                      <select class="form-control square" name="imap_encryption">
                        <option value="ssl" <?php echo (isset($smtp->imap_encryption) && $smtp->imap_encryption == 'ssl') ? 'selected' : ''; ?>>SSL</option>
                        <option value="tls" <?php echo (isset($smtp->imap_encryption) && $smtp->imap_encryption == 'tls') ? 'selected' : ''; ?>>TLS</option>
                        <option value="none" <?php echo (isset($smtp->imap_encryption) && $smtp->imap_encryption == 'none') ? 'selected' : ''; ?>>None</option>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label>IMAP Username</label>
                      <input type="text" class="form-control square" name="imap_username" value="<?php echo htmlspecialchars($smtp->imap_username ?? ''); ?>" placeholder="Leave empty to use SMTP username">
                      <small class="form-text text-muted">Usually same as SMTP username</small>
                    </div>
                    
                    <div class="form-group">
                      <label>IMAP Password</label>
                      <input type="password" class="form-control square" name="imap_password" placeholder="Leave empty to use SMTP password">
                      <small class="form-text text-muted">Leave blank to keep existing password</small>
                    </div>
                    
                    <?php if(!empty($smtp->imap_last_check)){ ?>
                    <div class="alert alert-info">
                      <i class="fas fa-info-circle"></i>
                      Last IMAP check: <?php echo date('Y-m-d H:i:s', strtotime($smtp->imap_last_check)); ?>
                    </div>
                    <?php } ?>
                    
                    <div class="form-group">
                      <button type="button" class="btn btn-sm btn-info" id="test_imap">
                        <i class="fas fa-check-circle"></i> Test IMAP Connection
                      </button>
                    </div>
                  </div>
                  
                  <hr class="my-4">
                  
                  <div class="form-group">
                    <label class="form-check">
                      <input type="checkbox" class="form-check-input" name="status" value="1" <?php echo $smtp->status ? 'checked' : ''; ?>>
                      <span class="form-check-label">Active</span>
                    </label>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn round btn-primary btn-min-width me-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Toggle IMAP fields
  $('#imap_enabled').change(function() {
    if($(this).is(':checked')) {
      $('#imap_fields').slideDown();
    } else {
      $('#imap_fields').slideUp();
    }
  });
  
  // Test IMAP connection
  $('#test_imap').click(function() {
    var $btn = $(this);
    $btn.prop('disabled', true);
    var original_html = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Testing...');
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_test_imap'); ?>',
      type: 'POST',
      dataType: 'JSON',
      data: { smtp_ids: '<?php echo $smtp->ids; ?>' },
      success: function(response) {
        if(response.status == 'success') {
          show_message('success', response.message);
        } else {
          show_message('error', response.message);
        }
        $btn.prop('disabled', false);
        $btn.html(original_html);
      },
      error: function() {
        show_message('error', 'Failed to test IMAP connection');
        $btn.prop('disabled', false);
        $btn.html(original_html);
      }
    });
  });
});
</script>
