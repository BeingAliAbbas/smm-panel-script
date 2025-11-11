<div class="modal-header">
  <h4 class="modal-title"><i class="fe fe-edit"></i> Edit SMTP Configuration</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

<form class="actionForm" action="<?php echo cn($module . '/ajax_smtp_edit/' . $smtp->ids); ?>" method="POST">
  <div class="modal-body">
    
    <div class="form-group">
      <label>Configuration Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($smtp->name); ?>" placeholder="e.g., Gmail SMTP" required>
    </div>
    
    <div class="row">
      <div class="col-md-8">
        <div class="form-group">
          <label>SMTP Host <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="host" value="<?php echo htmlspecialchars($smtp->host); ?>" placeholder="e.g., smtp.gmail.com" required>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Port <span class="text-danger">*</span></label>
          <input type="number" class="form-control" name="port" value="<?php echo $smtp->port; ?>" required>
        </div>
      </div>
    </div>
    
    <div class="form-group">
      <label>Encryption</label>
      <select class="form-control" name="encryption">
        <option value="none" <?php echo ($smtp->encryption == 'none') ? 'selected' : ''; ?>>None</option>
        <option value="tls" <?php echo ($smtp->encryption == 'tls') ? 'selected' : ''; ?>>TLS</option>
        <option value="ssl" <?php echo ($smtp->encryption == 'ssl') ? 'selected' : ''; ?>>SSL</option>
      </select>
    </div>
    
    <div class="form-group">
      <label>Username <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($smtp->username); ?>" placeholder="SMTP username" required>
    </div>
    
    <div class="form-group">
      <label>Password</label>
      <input type="password" class="form-control" name="password" placeholder="Leave empty to keep current password">
      <small class="text-muted">Leave blank to keep existing password</small>
    </div>
    
    <div class="form-group">
      <label>From Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="from_name" value="<?php echo htmlspecialchars($smtp->from_name); ?>" placeholder="e.g., SMM Panel" required>
    </div>
    
    <div class="form-group">
      <label>From Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" name="from_email" value="<?php echo htmlspecialchars($smtp->from_email); ?>" placeholder="e.g., noreply@example.com" required>
    </div>
    
    <div class="form-group">
      <label>Reply-To Email</label>
      <input type="email" class="form-control" name="reply_to" value="<?php echo htmlspecialchars($smtp->reply_to); ?>" placeholder="e.g., support@example.com">
    </div>
    
    <div class="form-group">
      <label class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="is_default" value="1" <?php echo $smtp->is_default ? 'checked' : ''; ?>>
        <span class="custom-control-label">Set as default SMTP</span>
      </label>
    </div>
    
    <div class="form-group">
      <label class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="status" value="1" <?php echo $smtp->status ? 'checked' : ''; ?>>
        <span class="custom-control-label">Active</span>
      </label>
    </div>
    
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fe fe-save"></i> Update Configuration
    </button>
  </div>
</form>

<script>
$(document).ready(function(){
  $('.actionForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serialize();
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: formData,
      beforeSend: function(){
        $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            window.location.reload();
          }, 1000);
        } else {
          show_message(response.message, 'error');
          $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Update Configuration');
        }
      },
      error: function(){
        show_message('An error occurred. Please try again.', 'error');
        $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Update Configuration');
      }
    });
  });
});
</script>
