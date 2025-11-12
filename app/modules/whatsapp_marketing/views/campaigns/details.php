<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-info-circle"></i> Campaign Details: <?php echo $campaign->name; ?></h3>
            <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-secondary float-right">Back to Campaigns</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Status:</strong> <span class="badge badge-<?php echo $campaign->status == 'running' ? 'success' : 'warning'; ?>"><?php echo ucfirst($campaign->status); ?></span></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Total Messages:</strong> <?php echo $campaign->total_messages; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Sent:</strong> <?php echo $campaign->sent_messages; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Failed:</strong> <?php echo $campaign->failed_messages; ?></p>
                </div>
            </div>
            <hr>
            <h4>Recent Messages</h4>
            <?php if(!empty($logs)): ?>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($logs as $log): ?>
                    <tr>
                        <td><?php echo $log->phone_number; ?></td>
                        <td><span class="badge badge-<?php echo $log->status == 'sent' ? 'success' : 'danger'; ?>"><?php echo $log->status; ?></span></td>
                        <td><?php echo $log->created_at; ?></td>
                        <td><?php echo $log->error_message; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No logs yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
