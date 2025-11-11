<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        Recipients - <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fa fa-arrow-left"></i> Back to Campaigns
        </a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Import Recipients</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <form id="formImportDatabase" class="importRecipientsForm" data-campaign-ids="<?php echo $campaign->ids; ?>" data-type="database">
              <h4><i class="fa fa-database"></i> Import from Database</h4>
              <p class="text-muted">Import all users with WhatsApp numbers from general_users table</p>
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-download"></i> Import from Database
              </button>
            </form>
          </div>
          
          <div class="col-md-6">
            <form id="formImportFile" class="importRecipientsForm" data-campaign-ids="<?php echo $campaign->ids; ?>" data-type="file" enctype="multipart/form-data">
              <h4><i class="fa fa-file"></i> Import from CSV/TXT</h4>
              <p class="text-muted">Upload a CSV or TXT file with phone numbers</p>
              <div class="form-group">
                <input type="file" class="form-control-file" name="file" accept=".csv,.txt" required>
                <small class="form-text text-muted">
                  Format: phone_number, name (optional)<br>
                  Example: 923001234567, John Doe
                </small>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-upload"></i> Upload & Import
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <?php if(!empty($recipients)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recipients List (<?php echo $total; ?> total)</h3>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table">
          <thead>
            <tr>
              <th class="w-1">No.</th>
              <th>Phone Number</th>
              <th>Name</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Error Message</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $i = ($page - 1) * $per_page;
            foreach ($recipients as $recipient) {
              $i++;
              
              $status_class = 'secondary';
              switch($recipient->status){
                case 'sent':
                  $status_class = 'success';
                  break;
                case 'delivered':
                  $status_class = 'info';
                  break;
                case 'failed':
                  $status_class = 'danger';
                  break;
              }
            ?>
            <tr>
              <td class="w-1"><?php echo $i; ?></td>
              <td><?php echo htmlspecialchars($recipient->phone_number); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td>
                <span class="badge badge-<?php echo $status_class; ?>">
                  <?php echo ucfirst($recipient->status); ?>
                </span>
              </td>
              <td>
                <?php echo $recipient->sent_at ? date('M d, Y H:i', strtotime($recipient->sent_at)) : '-'; ?>
              </td>
              <td>
                <?php if($recipient->error_message){ ?>
                  <small class="text-danger"><?php echo htmlspecialchars(substr($recipient->error_message, 0, 100)); ?></small>
                <?php } else { ?>
                  -
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      
      <?php if($total > $per_page){ ?>
      <div class="card-footer">
        <?php echo pagination($module . '/recipients/' . $campaign->ids, $total, $per_page, $page); ?>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php }else{ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-body text-center">
        <div class="empty">
          <div class="empty-icon"><i class="fa fa-users"></i></div>
          <p class="empty-title">No recipients found</p>
          <p class="empty-subtitle text-muted">
            Import recipients using one of the methods above.
          </p>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>
</div>

<script>
$(document).ready(function(){
  
  // Import recipients form
  $('.importRecipientsForm').on('submit', function(e){
    e.preventDefault();
    
    var form = $(this);
    var campaignIds = form.data('campaign-ids');
    var importType = form.data('type');
    var formData = new FormData(this);
    formData.append('import_type', importType);
    
    $.ajax({
      url: '<?php echo cn($module . '/ajax_import_recipients'); ?>/' + campaignIds,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(data){
        if(data.status == 'success'){
          swal('Success!', data.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          swal('Error!', data.message, 'error');
        }
      },
      error: function(){
        swal('Error!', 'An error occurred while importing recipients', 'error');
      }
    });
  });
  
});
</script>
