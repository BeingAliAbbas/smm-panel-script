<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3><i class="fa fa-list"></i> WhatsApp Campaigns</h3>
            <a href="<?php echo cn($module . '/campaign_create'); ?>" class="btn btn-primary float-right ajaxModal">
                <i class="fa fa-plus"></i> Create Campaign
            </a>
        </div>
        <div class="card-body">
            <?php if(!empty($campaigns)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>Messages</th>
                        <th>Progress</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($campaigns as $campaign): ?>
                    <tr>
                        <td><?php echo $campaign->name; ?></td>
                        <td><?php echo $campaign->template_name; ?></td>
                        <td><span class="badge badge-<?php echo $campaign->status == 'running' ? 'success' : ($campaign->status == 'completed' ? 'primary' : 'warning'); ?>"><?php echo ucfirst($campaign->status); ?></span></td>
                        <td><?php echo $campaign->sent_messages; ?> / <?php echo $campaign->total_messages; ?></td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $campaign->total_messages > 0 ? round(($campaign->sent_messages / $campaign->total_messages) * 100) : 0; ?>%"></div>
                            </div>
                        </td>
                        <td><?php echo $campaign->created_at; ?></td>
                        <td>
                            <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="btn btn-sm btn-info">Details</a>
                            <a href="<?php echo cn($module . '/recipients/' . $campaign->ids); ?>" class="btn btn-sm btn-primary">Recipients</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-center">No campaigns found. <a href="<?php echo cn($module . '/campaign_create'); ?>" class="ajaxModal">Create your first campaign</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
