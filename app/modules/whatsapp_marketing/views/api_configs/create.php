<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn('whatsapp_marketing/api_config_create') ?>" method="post" data-redirect="<?php echo cn('whatsapp_marketing/api_configs') ?>">
          <div class="modal-header bg-pantone">
            <h4 class="modal-title"><i class="fa fa-cog"></i> Create API Configuration</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control square" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API URL <span class="text-danger">*</span></label>
                    <input type="url" name="api_url" class="form-control square" required placeholder="http://waapi.beastsmm.pk/send-message">
                  </div>
                  
                  <div class="form-group">
                    <label>API Key <span class="text-danger">*</span></label>
                    <input type="text" name="api_key" class="form-control square" required>
                  </div>
                  
                  <div class="form-group">
                    <label class="custom-control custom-checkbox">
                      <input type="checkbox" name="is_default" value="1" class="custom-control-input">
                      <span class="custom-control-label">Set as default configuration</span>
                    </label>
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
