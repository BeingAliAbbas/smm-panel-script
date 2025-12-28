<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div id="main-modal-content">
  <div class="modal-right">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form class="form actionForm" action="<?php echo cn($module . '/ajax_api_edit/' . $api->ids); ?>" data-redirect="<?php echo cn($module . '/api'); ?>" method="POST">
          <div class="modal-header bg-pantone">
<<<<<<< HEAD
            <h4 class="modal-title"><i class="fas fa-edit"></i> Edit API Configuration: <?php echo htmlspecialchars($api->name); ?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
=======
            <h4 class="modal-title"><i class="fa fa-edit"></i> Edit API Configuration: <?php echo htmlspecialchars($api->name); ?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            </button>
          </div>
          <div class="modal-body">
            <div class="form-body">
              <div class="row justify-content-md-center">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  
                  <div class="form-group">
                    <label>Configuration Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="name" value="<?php echo htmlspecialchars($api->name); ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label>API URL <span class="text-danger">*</span></label>
                    <input type="url" class="form-control square" name="api_url" value="<?php echo htmlspecialchars($api->api_url); ?>" required>
                    <small class="text-muted">Full URL to your WhatsApp API endpoint</small>
                  </div>
                  
                  <div class="form-group">
                    <label>API Key <span class="text-danger">*</span></label>
                    <input type="text" class="form-control square" name="api_key" value="<?php echo htmlspecialchars($api->api_key); ?>" required>
                    <small class="text-muted">Authentication key for your WhatsApp API</small>
                  </div>
                  
                  <div class="form-group">
<<<<<<< HEAD
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" name="is_default" id="is_default" <?php echo $api->is_default ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="is_default">Set as Default Configuration</label>
=======
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="is_default" id="is_default" <?php echo $api->is_default ? 'checked' : ''; ?>>
                      <label class="custom-control-label" for="is_default">Set as Default Configuration</label>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    </div>
                  </div>
                  
                  <div class="form-group">
<<<<<<< HEAD
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" name="status" id="status" <?php echo $api->status ? 'checked' : ''; ?>>
                      <label class="form-check-label" for="status">Active</label>
=======
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="status" id="status" <?php echo $api->status ? 'checked' : ''; ?>>
                      <label class="custom-control-label" for="status">Active</label>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
<<<<<<< HEAD
            <button type="submit" class="btn round btn-primary btn-min-width me-1 mb-1">Update</button>
            <button type="button" class="btn round btn-default btn-min-width me-1 mb-1" data-bs-dismiss="modal">Cancel</button>
=======
            <button type="submit" class="btn round btn-primary btn-min-width mr-1 mb-1">Update</button>
            <button type="button" class="btn round btn-default btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
