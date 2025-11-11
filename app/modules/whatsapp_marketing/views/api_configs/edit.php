<div id="main-modal-content">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="form actionForm" action="<?php echo cn('whatsapp_marketing/api_config_edit/' . $config->id) ?>" method="post" data-redirect="<?php echo cn('whatsapp_marketing/api_configs') ?>">
                <div class="modal-header bg-pantone">
                    <h4 class="modal-title text-white"><i class="fa fa-edit"></i> Edit API Configuration</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        <div class="row justify-content-md-center">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Configuration Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control square" value="<?php echo $config->name ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>API URL <span class="text-danger">*</span></label>
                                    <input type="url" name="api_url" class="form-control square" value="<?php echo $config->api_url ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>API Key <span class="text-danger">*</span></label>
                                    <input type="text" name="api_key" class="form-control square" value="<?php echo $config->api_key ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" name="is_default" value="1" class="custom-control-input" <?php echo $config->is_default ? 'checked' : '' ?>>
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Set as default configuration</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary round btn-min-width mr-1 mb-1">Update Configuration</button>
                    <button type="button" class="btn btn-secondary round btn-min-width mr-1 mb-1" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
