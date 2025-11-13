<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> Create WhatsApp Campaign</h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-arrow-left"></i> Back to Campaigns
          </a>
        </div>
      </div>
      
      <form method="post" action="<?php echo cn('whatsapp_marketing/create'); ?>" enctype="multipart/form-data">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Campaign Name <span class="text-danger">*</span></label>
                <input type="text" name="campaign_name" class="form-control" required placeholder="Enter campaign name">
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>API Profile <span class="text-danger">*</span></label>
                <select name="api_config_id" class="form-control" required>
                  <option value="">Select API Profile</option>
                  <?php if (!empty($api_configs)): ?>
                    <?php foreach ($api_configs as $config): ?>
                      <option value="<?php echo $config->id; ?>"><?php echo $config->profile_name; ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <small class="form-text text-muted">
                  Don't have API profile? <a href="<?php echo cn('whatsapp_marketing/api_config_create'); ?>" target="_blank">Create one here</a>
                </small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label>Message Content <span class="text-danger">*</span></label>
            <textarea name="message_content" class="form-control" rows="6" required placeholder="Enter your message here..."></textarea>
            <small class="form-text text-muted">
              <strong>Available Placeholders:</strong> 
              {username}, {phone}, {balance}, {email}
              <br>
              <strong>Example:</strong> Hello {username}, your current balance is {balance} PKR. Stay tuned for more updates!
            </small>
          </div>
          
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Recipients Source <span class="text-danger">*</span></label>
                <select name="recipient_source" id="recipient_source" class="form-control" required>
                  <option value="database">Database Users (<?php echo $users_count; ?> users with WhatsApp)</option>
                  <option value="import">Import from File (CSV/TXT)</option>
                </select>
              </div>
            </div>
          </div>
          
          <div id="import_file_section" style="display: none;">
            <div class="form-group">
              <label>Upload File (CSV/TXT)</label>
              <input type="file" name="import_file" class="form-control" accept=".csv,.txt">
              <small class="form-text text-muted">
                <strong>Format:</strong> One phone number per line or CSV format: phone,name,email<br>
                <strong>Note:</strong> The "+" sign will be automatically removed from phone numbers.
              </small>
            </div>
          </div>
          
          <hr>
          
          <h4>Sending Limits</h4>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Messages Per Hour</label>
                <input type="number" name="limit_per_hour" class="form-control" placeholder="Leave empty for no limit">
                <small class="form-text text-muted">Maximum messages to send per hour</small>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>Messages Per Day</label>
                <input type="number" name="limit_per_day" class="form-control" placeholder="Leave empty for no limit">
                <small class="form-text text-muted">Maximum messages to send per day</small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="retry_failed" name="retry_failed" value="1" checked>
              <label class="custom-control-label" for="retry_failed">
                Retry Failed Messages (up to 3 attempts)
              </label>
            </div>
          </div>
          
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        </div>
        
        <div class="card-footer text-right">
          <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-save"></i> Create Campaign
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#recipient_source').on('change', function() {
    if ($(this).val() == 'import') {
      $('#import_file_section').slideDown();
    } else {
      $('#import_file_section').slideUp();
    }
  });
});
</script>
