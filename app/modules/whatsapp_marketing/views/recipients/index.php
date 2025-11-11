<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-users"></i> Manage Recipients - <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaigns'); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaigns
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Import Options -->
<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-database"></i> Import from User Database</h3>
      </div>
      <div class="card-body">
        <p>Import all users with WhatsApp numbers from the database</p>
        <div class="alert alert-info mb-3">
          <small><strong>Note:</strong> Only active users with valid WhatsApp numbers will be imported.</small>
        </div>
        <form id="importUsersForm" action="<?php echo cn($module . '/ajax_import_recipients'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <input type="hidden" name="import_type" value="database">
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-download"></i> Import Users
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title"><i class="fe fe-upload"></i> Import from CSV/TXT File</h3>
      </div>
      <div class="card-body">
        <p>Upload a CSV or TXT file with phone numbers (format: phone,name)</p>
        <form id="importCSVForm" action="<?php echo cn($module . '/ajax_import_recipients'); ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <input type="hidden" name="import_type" value="file">
          <div class="form-group">
            <input type="file" class="form-control" name="file" accept=".csv,.txt" required>
            <small class="text-muted">
              Example: 923001234567, John Doe
            </small>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-upload"></i> Upload & Import
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Recipients List -->
<div class="row mt-3">
  <?php if(!empty($recipients)){ ?>
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Recipients List</h3>
        <div class="card-options">
          <span class="badge badge-primary">Total: <?php echo number_format($total); ?></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-vcenter card-table table-sm">
          <thead>
            <tr>
              <th>Phone Number</th>
              <th>Name</th>
              <th>Status</th>
              <th>Sent At</th>
              <th>Error Message</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            foreach ($recipients as $recipient) {
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
          <div class="empty-icon"><i class="fe fe-users"></i></div>
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
  
  // Import users form
  $('#importUsersForm').on('submit', function(e){
    e.preventDefault();
    var form = $(this);
    var formData = new FormData(this);
    
    $.ajax({
      url: form.attr('action') + '/' + form.find('input[name="campaign_ids"]').val(),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      beforeSend: function(){
        form.find('button[type="submit"]').prop('disabled', true).html('<i class="fe fe-loader"></i> Importing...');
      },
      success: function(data){
        if(data.status == 'success'){
          swal('Success!', data.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          swal('Error!', data.message, 'error');
          form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-download"></i> Import Users');
        }
      },
      error: function(){
        swal('Error!', 'An error occurred while importing users', 'error');
        form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-download"></i> Import Users');
      }
    });
  });
  
  // Import CSV form
  $('#importCSVForm').on('submit', function(e){
    e.preventDefault();
    var form = $(this);
    var formData = new FormData(this);
    
    $.ajax({
      url: form.attr('action') + '/' + form.find('input[name="campaign_ids"]').val(),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      beforeSend: function(){
        form.find('button[type="submit"]').prop('disabled', true).html('<i class="fe fe-loader"></i> Uploading...');
      },
      success: function(data){
        if(data.status == 'success'){
          swal('Success!', data.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          swal('Error!', data.message, 'error');
          form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
        }
      },
      error: function(){
        swal('Error!', 'An error occurred while uploading the file', 'error');
        form.find('button[type="submit"]').prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
      }
    });
  });
  
});
</script>
