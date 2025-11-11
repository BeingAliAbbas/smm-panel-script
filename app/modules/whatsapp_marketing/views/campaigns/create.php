<form class="actionForm" action="<?php echo cn($module . '/ajax_campaign_create'); ?>" data-redirect="<?php echo cn($module . '/campaigns'); ?>" method="POST">
  <div class="modal-header">
    <h4 class="modal-title"><i class="fa fa-plus"></i> Create WhatsApp Campaign</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="form-group">
      <label>Campaign Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" placeholder="Enter campaign name" required>
      <small class="form-text text-muted">Give your campaign a descriptive name</small>
    </div>
    
    <div class="form-group">
      <label>Message <span class="text-danger">*</span></label>
      <textarea class="form-control" name="message" rows="5" placeholder="Enter your WhatsApp message" required></textarea>
      <small class="form-text text-muted">
        Use placeholders: {username}, {phone}, {balance}, {email}
      </small>
    </div>
    
    <div class="form-group">
      <label>WhatsApp API Configuration <span class="text-danger">*</span></label>
      <select class="form-control" name="api_config_id" required>
        <option value="">-- Select API Configuration --</option>
        <?php if(!empty($api_configs)){ 
          foreach($api_configs as $config){ ?>
            <option value="<?php echo $config->id; ?>" <?php echo $config->is_default ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($config->name); ?>
              <?php echo $config->is_default ? ' (Default)' : ''; ?>
            </option>
          <?php }
        } ?>
      </select>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Hourly Limit</label>
          <input type="number" class="form-control" name="sending_limit_hourly" placeholder="e.g. 100" min="0">
          <small class="form-text text-muted">Max messages per hour (0 = unlimited)</small>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">
          <label>Daily Limit</label>
          <input type="number" class="form-control" name="sending_limit_daily" placeholder="e.g. 1000" min="0">
          <small class="form-text text-muted">Max messages per day (0 = unlimited)</small>
        </div>
      </div>
    </div>
    
    <div class="alert alert-info">
      <strong><i class="fa fa-info-circle"></i> Next Steps:</strong>
      <ol class="mb-0 pl-3">
        <li>After creating the campaign, add recipients from the Recipients tab</li>
        <li>Click "Start Sending" to begin the campaign</li>
        <li>Setup campaign-specific cron job for automated sending</li>
      </ol>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-save"></i> Create Campaign
    </button>
  </div>
</form>
