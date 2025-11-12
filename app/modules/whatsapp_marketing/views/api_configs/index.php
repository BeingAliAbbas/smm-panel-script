<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <a href="<?php echo cn($module . '/api_config_create'); ?>" class="ajaxModal">
          <span class="add-new" data-toggle="tooltip" data-placement="bottom" title="Add New API Configuration">
            <i class="fa fa-plus-square text-primary" aria-hidden="true"></i>
          </span>
        </a>
        WhatsApp API Configurations
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module); ?>" class="btn btn-sm btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <?php if(!empty($configs)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">API Configuration List</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Configuration Name</th>
              <th>API Endpoint</th>
              <th>Default</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($configs as $config) {
              $i++;
            ?>
            <tr>
              <td class="w-1"><?php echo $i; ?></td>
              <td>
                <strong><?php echo htmlspecialchars($config->name); ?></strong>
              </td>
              <td>
                <small class="text-muted"><?php echo htmlspecialchars($config->api_endpoint); ?></small>
              </td>
              <td>
                <?php if($config->is_default){ ?>
                  <span class="badge badge-success">Default</span>
                <?php } else { ?>
                  -
                <?php } ?>
              </td>
              <td>
                <?php if($config->status){ ?>
                  <span class="badge badge-success">Active</span>
                <?php } else { ?>
                  <span class="badge badge-secondary">Inactive</span>
                <?php } ?>
              </td>
              <td>
                <small class="text-muted"><?php echo date('M d, Y', strtotime($config->created_at)); ?></small>
              </td>
              <td>
                <div class="item-action dropdown">
                  <a href="javascript:void(0)" data-toggle="dropdown" class="icon">
                    <i class="fa fa-ellipsis-v"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?php echo cn($module . '/api_config_edit/' . $config->ids); ?>" class="dropdown-item ajaxModal">
                      <i class="dropdown-icon fa fa-edit"></i> Edit
                    </a>
                    <a href="javascript:void(0)" class="dropdown-item actionApiConfigDelete" data-ids="<?php echo $config->ids; ?>">
                      <i class="dropdown-icon fa fa-trash text-danger"></i> Delete
                    </a>
                  </div>
                </div>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <?php if($total > $per_page){ ?>
      <div class="card-footer">
        <?php echo pagination($module . '/api_configs', $total, $per_page, $page); ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php }else{ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty">
          <div class="empty-icon"><i class="fa fa-cog"></i></div>
          <p class="empty-title">No API configurations found</p>
          <p class="empty-subtitle text-muted">
            Add your WhatsApp API configuration to start sending messages.
          </p>
          <div class="empty-action">
            <a href="<?php echo cn($module . '/api_config_create'); ?>" class="btn btn-primary ajaxModal">
              <i class="fa fa-plus"></i> Add API Configuration
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
$(document).ready(function(){
  
  // Delete API config
  $(document).on('click', '.actionApiConfigDelete', function(e){
    e.preventDefault();
    var ids = $(this).data('ids');
    
    swal({
      title: 'Delete API Configuration?',
      text: "Are you sure you want to delete this API configuration?",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: '<?php echo cn($module . '/ajax_api_config_delete'); ?>/' + ids,
          type: 'POST',
          dataType: 'json',
          success: function(data){
            if(data.status == 'success'){
              swal('Deleted!', data.message, 'success');
              setTimeout(function(){
                location.reload();
              }, 1000);
            } else {
              swal('Error!', data.message, 'error');
            }
          }
        });
      }
    });
  });
  
});
</script>
