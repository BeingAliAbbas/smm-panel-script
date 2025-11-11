<form class="actionForm" action="<?php echo cn($module . '/ajax_api_config_create'); ?>" data-redirect="<?php echo cn($module . '/api_configs'); ?>" method="POST">
  <div class="modal-header">
    <h4 class="modal-title"><i class="fa fa-plus"></i> Add WhatsApp API Configuration</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="form-group">
      <label>Configuration Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" placeholder="e.g. Main WhatsApp API" required>
      <small class="form-text text-muted">A descriptive name for this API configuration</small>
    </div>
    
    <div class="form-group">
      <label>API Key <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="api_key" placeholder="YOUR_API_KEY" required>
      <small class="form-text text-muted">Your WhatsApp API key</small>
    </div>
    
    <div class="form-group">
      <label>API Endpoint <span class="text-danger">*</span></label>
      <input type="url" class="form-control" name="api_endpoint" value="http://waapi.beastsmm.pk/send-message" required>
      <small class="form-text text-muted">WhatsApp API endpoint URL</small>
    </div>
    
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1">
        <label class="custom-control-label" for="is_default">Set as default configuration</label>
      </div>
    </div>
    
    <div class="form-group">
      <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" checked>
        <label class="custom-control-label" for="status">Active</label>
      </div>
    </div>
    
    <div class="alert alert-info">
      <strong><i class="fa fa-info-circle"></i> API Format:</strong>
      <pre class="mb-0 small">POST <?php echo htmlspecialchars('http://waapi.beastsmm.pk/send-message'); ?>
{
  "apiKey": "YOUR_API_KEY",
  "phoneNumber": "923XXXXXXXXX",
  "message": "Hello"
}</pre>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-save"></i> Save Configuration
    </button>
  </div>
</form>
