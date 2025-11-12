<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-header">
    <h5 class="modal-title">Edit Campaign: <?php echo $campaign->name; ?></h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<form action="<?php echo cn($module . '/ajax_campaign_edit/' . $campaign->ids); ?>" class="actionForm" method="POST">
    <div class="modal-body">
        <div class="form-group">
            <label>Campaign Name *</label>
            <input type="text" name="name" class="form-control" value="<?php echo $campaign->name; ?>" required>
        </div>
        <div class="form-group">
            <label>Message Template *</label>
            <select name="template_id" class="form-control" required>
                <?php foreach($templates as $template): ?>
                <option value="<?php echo $template->id; ?>" <?php echo $template->id == $campaign->template_id ? 'selected' : ''; ?>><?php echo $template->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>API Configuration *</label>
            <select name="api_config_id" class="form-control" required>
                <?php foreach($api_configs as $api): ?>
                <option value="<?php echo $api->id; ?>" <?php echo $api->id == $campaign->api_config_id ? 'selected' : ''; ?>><?php echo $api->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Hourly Limit</label>
                    <input type="number" name="sending_limit_hourly" class="form-control" value="<?php echo $campaign->sending_limit_hourly; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Daily Limit</label>
                    <input type="number" name="sending_limit_daily" class="form-control" value="<?php echo $campaign->sending_limit_daily; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Campaign</button>
    </div>
</form>
