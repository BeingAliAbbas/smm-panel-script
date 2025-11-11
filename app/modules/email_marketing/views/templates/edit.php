<div class="modal-header">
  <h4 class="modal-title"><i class="fe fe-edit"></i> Edit Email Template</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

<form class="actionForm" action="<?php echo cn($module . '/ajax_template_edit/' . $template->ids); ?>" method="POST">
  <div class="modal-body">
    
    <div class="form-group">
      <label>Template Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($template->name); ?>" placeholder="e.g., Welcome Email" required>
    </div>
    
    <div class="form-group">
      <label>Email Subject <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="subject" value="<?php echo htmlspecialchars($template->subject); ?>" placeholder="e.g., Welcome to {site_name}!" required>
      <small class="text-muted">You can use variables like {username}, {email}, {site_name}</small>
    </div>
    
    <div class="form-group">
      <label>Description</label>
      <textarea class="form-control" name="description" rows="2" placeholder="Brief description of this template"><?php echo htmlspecialchars($template->description); ?></textarea>
    </div>
    
    <div class="form-group">
      <label>Email Body (HTML) <span class="text-danger">*</span></label>
      <textarea class="form-control" name="body" id="email_body" rows="15" required><?php echo htmlspecialchars($template->body); ?></textarea>
      <small class="text-muted">HTML content of the email. Use variables: {username}, {email}, {balance}, {site_name}, {site_url}</small>
    </div>
    
    <div class="alert alert-info">
      <strong>Available Variables:</strong>
      <ul class="mb-0">
        <li><code>{username}</code> - User's name</li>
        <li><code>{email}</code> - User's email address</li>
        <li><code>{balance}</code> - User's balance</li>
        <li><code>{site_name}</code> - Website name</li>
        <li><code>{site_url}</code> - Website URL</li>
        <li><code>{current_date}</code> - Current date</li>
      </ul>
    </div>
    
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fe fe-save"></i> Update Template
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
          $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Update Template');
        }
      },
      error: function(){
        show_message('An error occurred. Please try again.', 'error');
        $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-save"></i> Update Template');
      }
    });
  });
});
</script>
