<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-bullhorn"></i> WhatsApp Campaigns</h4>
                    <button type="button" class="btn btn-light round pull-right" data-toggle="modal" data-target="#campaignModal" onclick="load_modal_content('<?php echo cn('whatsapp_marketing/campaign_create') ?>')">
                        <i class="fa fa-plus"></i> New Campaign
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Sending</th>
                                    <th>Total</th>
                                    <th>Sent</th>
                                    <th>Failed</th>
                                    <th>Remaining</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($campaigns)): ?>
                                    <?php foreach ($campaigns as $campaign): ?>
                                        <tr>
                                            <td><?php echo $campaign->id ?></td>
                                            <td><?php echo $campaign->name ?></td>
                                            <td><span class="badge badge-info"><?php echo $campaign->status ?></span></td>
                                            <td><span class="badge badge-<?php echo $campaign->sending_status == 'Started' ? 'success' : 'secondary' ?>"><?php echo $campaign->sending_status ?></span></td>
                                            <td><?php echo $campaign->stats['total'] ?></td>
                                            <td><?php echo $campaign->stats['sent'] ?></td>
                                            <td><?php echo $campaign->stats['failed'] ?></td>
                                            <td><?php echo $campaign->stats['remaining'] ?></td>
                                            <td>
                                                <a href="<?php echo cn('whatsapp_marketing/campaign_details/' . $campaign->id) ?>" class="btn btn-sm btn-info round" title="Details"><i class="fa fa-eye"></i></a>
                                                <button type="button" class="btn btn-sm btn-primary round" data-toggle="modal" data-target="#campaignModal" onclick="load_modal_content('<?php echo cn('whatsapp_marketing/campaign_edit/' . $campaign->id) ?>')" title="Edit"><i class="fa fa-edit"></i></button>
                                                <?php if ($campaign->sending_status == 'Stopped' || $campaign->sending_status == 'Paused'): ?>
                                                    <a href="<?php echo cn('whatsapp_marketing/campaign_start/' . $campaign->id) ?>" class="btn btn-sm btn-success round" onclick="return confirm('Start this campaign?')" title="Start"><i class="fa fa-play"></i></a>
                                                <?php endif; ?>
                                                <?php if ($campaign->sending_status == 'Started'): ?>
                                                    <a href="<?php echo cn('whatsapp_marketing/campaign_pause/' . $campaign->id) ?>" class="btn btn-sm btn-warning round" onclick="return confirm('Pause this campaign?')" title="Pause"><i class="fa fa-pause"></i></a>
                                                <?php endif; ?>
                                                <a href="<?php echo cn('whatsapp_marketing/campaign_delete/' . $campaign->id) ?>" class="btn btn-sm btn-danger round" onclick="return confirm('Delete this campaign and all its data?')" title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No campaigns found</td>
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

<div class="modal fade" id="campaignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="campaign-modal-content"></div>
    </div>
</div>
