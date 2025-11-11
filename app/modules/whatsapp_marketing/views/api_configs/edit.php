<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_api_config_edit/' . $config->ids); ?>" data-redirect="<?php echo cn($module . '/api_configs'); ?>" method="POST">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit WhatsApp API Configuration</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($config->name); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="api_key" value="<?php echo htmlspecialchars($config->api_key); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API Endpoint <span class="text-danger">*</span></label>
                    <input type="url" class="form-control square" name="api_endpoint" value="<?php echo htmlspecialchars($config->api_endpoint); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="is_default" name="is_default" value="1" <?php echo $config->is_default ? 'checked' : ''; ?>>
                      <label class="custom-control-label" for="is_default">Set as default configuration</label>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?php echo $config->status ? 'checked' : ''; ?>>
                      <label class="custom-control-label" for="status">Active</label>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Update Configuration
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
