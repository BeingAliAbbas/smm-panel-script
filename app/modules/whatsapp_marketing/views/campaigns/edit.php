<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn('whatsapp_marketing/campaign_edit/' . $campaign->id) ?>" method="post" data-redirect="<?php echo cn('whatsapp_marketing/campaigns') ?>">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit WhatsApp Campaign</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Campaign Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control square" value="<?php echo $campaign->name ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>Subject/Title</label>
                    <input type="text" name="subject" class="form-control square" value="<?php echo $campaign->subject ?>">
                  </div>
                  
                  <div class="form-group">
                    <label>Message <span class="text-danger">*</span></label>
                    <textarea name="message" class="form-control square" rows="5" required><?php echo $campaign->message ?></textarea>
                    <small class="form-text text-muted">Available placeholders: {username}, {phone}, {balance}, {site_name}, {site_url}</small>
                  </div>
                  
                  <div class="form-group">
                    <label>API Configuration <span class="text-danger">*</span></label>
                    <select name="api_config_id" class="form-control square" required>
                      <?php foreach ($api_configs as $config): ?>
                        <option value="<?php echo $config->id ?>" <?php echo $config->id == $campaign->api_config_id ? 'selected' : '' ?>>
                          <?php echo $config->name ?> <?php echo $config->is_default ? '(Default)' : '' ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label>Hourly Limit</label>
                    <input type="number" name="hourly_limit" class="form-control square" value="<?php echo $campaign->hourly_limit ?>" min="0">
                    <small class="form-text text-muted">0 = No limit</small>
                  </div>
                  
                  <div class="form-group">
                    <label>Daily Limit</label>
                    <input type="number" name="daily_limit" class="form-control square" value="<?php echo $campaign->daily_limit ?>" min="0">
                    <small class="form-text text-muted">0 = No limit</small>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">Submit</button>
            <button type="button" class="btn round btn-default btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
