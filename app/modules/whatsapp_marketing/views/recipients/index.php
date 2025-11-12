<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-users"></i> Recipients for: <?php echo $campaign->name; ?></h3>
        </div>
        <div class="card-body">
            <div class="btn-group mb-3">
                <button class="btn btn-primary" onclick="importFromUsers('<?php echo $campaign->ids; ?>')">Import from Users</button>
                <button class="btn btn-info" onclick="showCSVUpload('<?php echo $campaign->ids; ?>')">Import from CSV</button>
            </div>
            <?php if(!empty($recipients)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Phone Number</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recipients as $recipient): ?>
                    <tr>
                        <td><?php echo $recipient->phone_number; ?></td>
                        <td><?php echo $recipient->name; ?></td>
                        <td><span class="badge badge-<?php echo $recipient->status == 'sent' ? 'success' : ($recipient->status == 'failed' ? 'danger' : 'secondary'); ?>"><?php echo $recipient->status; ?></span></td>
                        <td><?php echo $recipient->sent_at; ?></td>
                        <td><?php echo $recipient->error_message; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center">No recipients added yet. Use the buttons above to import recipients.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function importFromUsers(campaignIds) {
    if(confirm('Import all users with phone numbers?')) {
        $.ajax({
            url: '<?php echo cn($module . '/ajax_import_from_users'); ?>',
            type: 'POST',
            data: {campaign_ids: campaignIds},
            success: function(response) {
                alert(response.message);
                location.reload();
            }
        });
    }
}
</script>
