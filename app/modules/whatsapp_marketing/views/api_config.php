<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="<?php echo $module_icon; ?>"></i> WhatsApp API Configuration</h3>
        <div class="card-options">
          <a href="<?php echo cn('whatsapp_marketing'); ?>" class="btn btn-secondary btn-sm">
            <i class="fe fe-arrow-left"></i> Back to Campaigns
          </a>
          <a href="<?php echo cn('whatsapp_marketing/api_config_create'); ?>" class="btn btn-primary btn-sm">
            <i class="fe fe-plus"></i> Add New Profile
          </a>
        </div>
      </div>
      
      <div class="card-body">
        <?php if (!empty($configs)): ?>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th width="5%">ID</th>
                <th width="20%">Profile Name</th>
                <th width="35%">API Endpoint</th>
                <th width="20%">API Key</th>
                <th width="10%">Status</th>
                <th width="10%">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($configs as $config): ?>
              <tr>
                <td><?php echo $config->id; ?></td>
                <td><strong><?php echo $config->profile_name; ?></strong></td>
                <td><small><?php echo $config->api_endpoint; ?></small></td>
                <td><code><?php echo substr($config->api_key, 0, 20) . '...'; ?></code></td>
                <td>
                  <span class="badge badge-<?php echo ($config->status == 1) ? 'success' : 'danger'; ?>">
                    <?php echo ($config->status == 1) ? 'Active' : 'Inactive'; ?>
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <a href="<?php echo cn('whatsapp_marketing/api_config_edit/' . $config->id); ?>" class="btn btn-secondary" title="Edit">
                      <i class="fe fe-edit"></i>
                    </a>
                    <a href="javascript:void(0)" 
                       data-url="<?php echo cn('whatsapp_marketing/api_config_delete/' . $config->id); ?>" 
                       class="btn btn-danger ajaxDelete" 
                       title="Delete">
                      <i class="fe fe-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          <?php echo $links; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
          <i class="fe fe-info"></i> No API configurations found. <a href="<?php echo cn('whatsapp_marketing/api_config_create'); ?>">Create your first API profile</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.ajaxDelete').on('click', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    if (confirm('Are you sure you want to delete this API configuration?')) {
      window.location.href = url;
    }
  });
});
</script>
