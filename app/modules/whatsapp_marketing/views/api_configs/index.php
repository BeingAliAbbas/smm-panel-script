<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-pantone">
                    <h4 class="card-title text-white"><i class="fa fa-cog"></i> WhatsApp API Configurations</h4>
                    <button type="button" class="btn btn-light round pull-right" data-toggle="modal" data-target="#apiModal" onclick="load_modal_content('<?php echo cn('whatsapp_marketing/api_config_create') ?>')">
                        <i class="fa fa-plus"></i> New API Config
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>API URL</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($configs)): ?>
                                    <?php foreach ($configs as $config): ?>
                                        <tr>
                                            <td><?php echo $config->id ?></td>
                                            <td><?php echo $config->name ?></td>
                                            <td><?php echo $config->api_url ?></td>
                                            <td><?php echo $config->is_default ? '<span class="badge badge-success">Yes</span>' : 'No' ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary round" data-toggle="modal" data-target="#apiModal" onclick="load_modal_content('<?php echo cn('whatsapp_marketing/api_config_edit/' . $config->id) ?>')"><i class="fa fa-edit"></i></button>
                                                <a href="<?php echo cn('whatsapp_marketing/api_config_delete/' . $config->id) ?>" class="btn btn-sm btn-danger round" onclick="return confirm('Delete this configuration?')"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No API configurations found</td>
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

<div class="modal fade" id="apiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="api-modal-content"></div>
    </div>
</div>
