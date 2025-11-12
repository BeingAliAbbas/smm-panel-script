<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Create WhatsApp Campaign</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<form action="<?php echo cn($module . '/ajax_campaign_create'); ?>" class="actionForm" method="POST">
    <div class="modal-body">
        <div class="form-group">
            <label>Campaign Name *</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Message Template *</label>
            <select name="template_id" class="form-control" required>
                <option value="">-- Select Template --</option>
                <?php foreach($templates as $template): ?>
                <option value="<?php echo $template->id; ?>"><?php echo $template->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>API Configuration *</label>
            <select name="api_config_id" class="form-control" required>
                <option value="">-- Select API Config --</option>
                <?php foreach($api_configs as $api): ?>
                <option value="<?php echo $api->id; ?>"><?php echo $api->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Hourly Limit</label>
                    <input type="number" name="sending_limit_hourly" class="form-control" placeholder="100">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Daily Limit</label>
                    <input type="number" name="sending_limit_daily" class="form-control" placeholder="1000">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Campaign</button>
    </div>
</form>
