<div class="modal-header">
  <h4 class="modal-title"><i class="fe fe-mail"></i> Create New Campaign</h4>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

<form class="actionForm" action="<?php echo cn($module . '/ajax_campaign_create'); ?>" method="POST">
  <div class="modal-body">
    
    <div class="form-group">
      <label>Campaign Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" placeholder="Enter campaign name" required>
    </div>
    
    <div class="form-group">
      <label>Email Template <span class="text-danger">*</span></label>
      <select class="form-control" name="template_id" required>
        <option value="">Select Template</option>
        <?php if(!empty($templates)){ 
          foreach($templates as $template){
        ?>
        <option value="<?php echo $template->id; ?>"><?php echo htmlspecialchars($template->name); ?></option>
        <?php }} ?>
      </select>
      <small class="text-muted">Choose an email template for this campaign</small>
    </div>
    
    <div class="form-group">
      <label>SMTP Configuration <span class="text-danger">*</span></label>
      <select class="form-control" name="smtp_config_id" required>
        <option value="">Select SMTP</option>
        <?php if(!empty($smtp_configs)){ 
          foreach($smtp_configs as $smtp){
            if($smtp->status == 1){
        ?>
        <option value="<?php echo $smtp->id; ?>" <?php echo $smtp->is_default ? 'selected' : ''; ?>>
          <?php echo htmlspecialchars($smtp->name); ?>
          <?php echo $smtp->is_default ? ' (Default)' : ''; ?>
        </option>
        <?php }}} ?>
      </select>
      <small class="text-muted">Select SMTP server to send emails</small>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Hourly Limit</label>
          <input type="number" class="form-control" name="sending_limit_hourly" placeholder="e.g., 100" min="1">
          <small class="text-muted">Max emails per hour (leave empty for no limit)</small>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Daily Limit</label>
          <input type="number" class="form-control" name="sending_limit_daily" placeholder="e.g., 1000" min="1">
          <small class="text-muted">Max emails per day (leave empty for no limit)</small>
        </div>
      </div>
    </div>
    
    <div class="alert alert-info">
      <i class="fe fe-info"></i> After creating the campaign, you'll be able to add recipients and start sending.
    </div>
    
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fe fe-plus"></i> Create Campaign
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
        $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            window.location.href = '<?php echo cn($module . '/campaigns'); ?>';
          }, 1000);
        } else {
          show_message(response.message, 'error');
          $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-plus"></i> Create Campaign');
        }
      },
      error: function(){
        show_message('An error occurred. Please try again.', 'error');
        $form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-plus"></i> Create Campaign');
      }
    });
  });
});
</script>
