<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="<?php echo $module_icon; ?>"></i> 
          <?php echo isset($config) ? 'Edit' : 'Create'; ?> API Configuration
        </h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing/api_config'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-arrow-left"></i> Back to API Configurations
          </a>
        </div>
      </div>
      
      <form method="post" action="<?php echo isset($config) ? cn('whatsapp_marketing/api_config_edit/' . $config->id) : cn('whatsapp_marketing/api_config_create'); ?>">
        <div class="card-body">
          <div class="form-group">
            <label>Profile Name <span class="text-danger">*</span></label>
            <input type="text" name="profile_name" class="form-control" required 
                   value="<?php echo isset($config) ? $config->profile_name : ''; ?>" 
                   placeholder="e.g., Main WhatsApp API">
            <small class="form-text text-muted">A friendly name to identify this API configuration</small>
          </div>
          
          <div class="form-group">
            <label>API Endpoint <span class="text-danger">*</span></label>
            <input type="url" name="api_endpoint" class="form-control" required 
                   value="<?php echo isset($config) ? $config->api_endpoint : 'http://waapi.beastsmm.pk/send-message'; ?>" 
                   placeholder="http://waapi.beastsmm.pk/send-message">
            <small class="form-text text-muted">The WhatsApp API endpoint URL</small>
          </div>
          
          <div class="form-group">
            <label>API Key <span class="text-danger">*</span></label>
            <input type="text" name="api_key" class="form-control" required 
                   value="<?php echo isset($config) ? $config->api_key : ''; ?>" 
                   placeholder="Enter your API key">
            <small class="form-text text-muted">Your WhatsApp API authentication key</small>
          </div>
          
          <div class="alert alert-info">
            <strong>API Request Format:</strong><br>
            <pre><code>{
  "apiKey": "YOUR_API_KEY",
  "phoneNumber": "923XXXXXXXXX",
  "message": "Hello"
}</code></pre>
          </div>
          
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        </div>
        
        <div class="card-footer text-right">
          <a href="<?php echo cn('whatsapp_marketing/api_config'); ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-save"></i> <?php echo isset($config) ? 'Update' : 'Create'; ?> Configuration
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
