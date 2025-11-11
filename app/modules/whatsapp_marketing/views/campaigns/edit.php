<form class="actionForm" action="<?php echo cn($module . '/ajax_campaign_edit/' . $campaign->ids); ?>" data-redirect="<?php echo cn($module . '/campaigns'); ?>" method="POST">
  <div class="modal-header">
    <h4 class="modal-title"><i class="fa fa-edit"></i> Edit WhatsApp Campaign</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <div class="form-group">
      <label>Campaign Name <span class="text-danger">*</span></label>
      <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($campaign->name); ?>" required>
    </div>
    
    <div class="form-group">
      <label>Message <span class="text-danger">*</span></label>
      <textarea class="form-control" name="message" rows="5" required><?php echo htmlspecialchars($campaign->message); ?></textarea>
      <small class="form-text text-muted">
        Use placeholders: {username}, {phone}, {balance}, {email}
      </small>
    </div>
    
    <div class="form-group">
      <label>WhatsApp API Configuration <span class="text-danger">*</span></label>
      <select class="form-control" name="api_config_id" required>
        <?php if(!empty($api_configs)){ 
          foreach($api_configs as $config){ ?>
            <option value="<?php echo $config->id; ?>" <?php echo $config->id == $campaign->api_config_id ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($config->name); ?>
            </option>
          <?php }
        } ?>
      </select>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Hourly Limit</label>
          <input type="number" class="form-control" name="sending_limit_hourly" value="<?php echo $campaign->sending_limit_hourly; ?>" min="0">
          <small class="form-text text-muted">Max messages per hour (0 = unlimited)</small>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="form-group">
          <label>Daily Limit</label>
          <input type="number" class="form-control" name="sending_limit_daily" value="<?php echo $campaign->sending_limit_daily; ?>" min="0">
          <small class="form-text text-muted">Max messages per day (0 = unlimited)</small>
        </div>
      </div>
    </div>
    
    <?php if($campaign->status == 'running'){ ?>
    <div class="alert alert-warning">
      <strong><i class="fa fa-exclamation-triangle"></i> Warning:</strong>
      This campaign is currently running. Changes will take effect immediately.
    </div>
    <?php } ?>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">
      <i class="fa fa-save"></i> Update Campaign
    </button>
  </div>
</form>
