<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Edit API Configuration: <?php echo $api->name; ?></h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<form action="<?php echo cn($module . '/ajax_api_edit/' . $api->ids); ?>" class="actionForm" method="POST">
    <div class="modal-body">
        <div class="form-group">
            <label>Configuration Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo $api->name; ?>" required>
        </div>
        <div class="form-group">
            <label>API URL *</label>
            <input type="text" name="api_url" class="form-control" value="<?php echo $api->api_url; ?>" required>
        </div>
        <div class="form-group">
            <label>API Key *</label>
            <input type="text" name="api_key" class="form-control" value="<?php echo $api->api_key; ?>" required>
        </div>
        <div class="form-check">
            <input type="checkbox" name="is_default" class="form-check-input" id="is_default" <?php echo $api->is_default ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_default">Set as Default</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="status" class="form-check-input" id="status" <?php echo $api->status ? 'checked' : ''; ?>>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Configuration</button>
    </div>
</form>
