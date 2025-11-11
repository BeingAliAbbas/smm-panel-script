<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-users"></i> Recipients for: <?php echo $campaign->name ?></h4>
                    <a href="<?php echo cn('whatsapp_marketing/campaigns') ?>" class="btn btn-light round pull-right">Back to Campaigns</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success round" id="importUsersBtn">
                                <i class="fa fa-database"></i> Import from Users (with orders)
                            </button>
                            <button type="button" class="btn btn-info round" data-toggle="modal" data-target="#csvModal">
                                <i class="fa fa-upload"></i> Import from CSV
                            </button>
                            <a href="<?php echo cn('whatsapp_marketing/delete_all_recipients/' . $campaign->id) ?>" class="btn btn-danger round" onclick="return confirm('Delete all recipients?')">
                                <i class="fa fa-trash"></i> Delete All Recipients
                            </a>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Only users with at least 1 order will be imported. Phone numbers automatically sanitized (+ symbol removed).
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recipients)): ?>
                                    <?php foreach ($recipients as $recipient): ?>
                                        <tr>
                                            <td><?php echo $recipient->id ?></td>
                                            <td><?php echo $recipient->name ?></td>
                                            <td><?php echo $recipient->phone ?></td>
                                            <td><span class="badge badge-<?php echo $recipient->status == 'sent' ? 'success' : ($recipient->status == 'failed' ? 'danger' : 'secondary') ?>"><?php echo $recipient->status ?></span></td>
                                            <td><?php echo $recipient->sent_at ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recipients found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="csvModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?php echo cn('whatsapp_marketing/import_from_csv') ?>" method="post">
                <div class="modal-header bg-pantone">
                    <h4 class="modal-title text-white">Import from CSV</h4>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Format: phone,name (one per line)</p>
                    <p>Example:<br>+923001234567,John Doe<br>923009876543,Jane Smith</p>
                    <input type="hidden" name="campaign_id" value="<?php echo $campaign->id ?>">
                    <textarea name="csv_data" class="form-control" rows="10" placeholder="+923001234567,John Doe"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary round">Import</button>
                    <button type="button" class="btn btn-secondary round" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#importUsersBtn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
        
        $.ajax({
            url: '<?php echo cn('whatsapp_marketing/import_from_users') ?>',
            type: 'POST',
            data: {
                campaign_id: <?php echo $campaign->id ?>,
                <?php echo $this->security->get_csrf_token_name() ?>: '<?php echo $this->security->get_csrf_hash() ?>'
            },
            dataType: 'json',
            timeout: 60000,
            success: function(response) {
                if (response.status == 'success') {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    toastr.error(response.message || 'Import failed');
                    btn.prop('disabled', false).html('<i class="fa fa-database"></i> Import from Users (with orders)');
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Import failed';
                if (status === 'timeout') {
                    errorMsg = 'Import timed out. Try again or reduce the number of users.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
                btn.prop('disabled', false).html('<i class="fa fa-database"></i> Import from Users (with orders)');
            }
        });
    });
});
</script>
