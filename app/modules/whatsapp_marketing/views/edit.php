<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> Edit Campaign</h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-arrow-left"></i> Back to Campaigns
          </a>
        </div>
      </div>
      
      <form method="post" action="<?php echo cn('whatsapp_marketing/edit/' . $campaign->id); ?>">
        <div class="card-body">
          <div class="alert alert-info">
            <i class="fe fe-info"></i> Note: You cannot change recipients once a campaign is created. Recipients: <strong><?php echo $recipients_count; ?></strong>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Campaign Name <span class="text-danger">*</span></label>
                <input type="text" name="campaign_name" class="form-control" required value="<?php echo $campaign->campaign_name; ?>">
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>API Profile <span class="text-danger">*</span></label>
                <select name="api_config_id" class="form-control" required>
                  <option value="">Select API Profile</option>
                  <?php if (!empty($api_configs)): ?>
                    <?php foreach ($api_configs as $config): ?>
                      <option value="<?php echo $config->id; ?>" <?php echo ($config->id == $campaign->api_config_id) ? 'selected' : ''; ?>>
                        <?php echo $config->profile_name; ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label>Message Content <span class="text-danger">*</span></label>
            <textarea name="message_content" class="form-control" rows="6" required><?php echo $campaign->message_content; ?></textarea>
            <small class="form-text text-muted">
              <strong>Available Placeholders:</strong> 
              {username}, {phone}, {balance}, {email}
            </small>
          </div>
          
          <hr>
          
          <h4>Sending Limits</h4>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Messages Per Hour</label>
                <input type="number" name="limit_per_hour" class="form-control" value="<?php echo $campaign->limit_per_hour; ?>" placeholder="Leave empty for no limit">
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label>Messages Per Day</label>
                <input type="number" name="limit_per_day" class="form-control" value="<?php echo $campaign->limit_per_day; ?>" placeholder="Leave empty for no limit">
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="retry_failed" name="retry_failed" value="1" <?php echo ($campaign->retry_failed) ? 'checked' : ''; ?>>
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
            <i class="fe fe-save"></i> Update Campaign
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
