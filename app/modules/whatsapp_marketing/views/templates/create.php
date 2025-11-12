<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Create WhatsApp Template</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<form action="<?php echo cn($module . '/ajax_template_create'); ?>" class="actionForm" method="POST">
    <div class="modal-body">
        <div class="form-group">
            <label>Template Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Message *</label>
            <textarea name="message" class="form-control" rows="5" required placeholder="Hello {username}! Welcome to {site_name}."></textarea>
            <small class="form-text text-muted">
                Available variables: {username}, {email}, {balance}, {total_orders}, {site_name}, {site_url}, {current_date}
            </small>
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description" class="form-control">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Template</button>
    </div>
</form>
