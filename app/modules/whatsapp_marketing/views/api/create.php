<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Add WhatsApp API Configuration</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<form action="<?php echo cn($module . '/ajax_api_create'); ?>" class="actionForm" method="POST">
    <div class="modal-body">
        <div class="form-group">
            <label>Configuration Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>API URL *</label>
            <input type="text" name="api_url" class="form-control" value="http://waapi.beastsmm.pk/send-message" required>
        </div>
        <div class="form-group">
            <label>API Key *</label>
            <input type="text" name="api_key" class="form-control" required>
        </div>
        <div class="form-check">
            <input type="checkbox" name="is_default" class="form-check-input" id="is_default">
            <label class="form-check-label" for="is_default">Set as Default</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="status" class="form-check-input" id="status" checked>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Configuration</button>
    </div>
</form>
