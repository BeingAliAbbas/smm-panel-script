<!-- Include responsive CSS -->
<link rel="stylesheet" href="<?php echo BASE; ?>assets/css/whatsapp_marketing-responsive.css">

<div class="row justify-content-md-center">
  <div class="col-md-12">
    <div class="page-header">
      <h1 class="page-title">
        <i class="fe fe-users"></i> Manage Recipients - <?php echo htmlspecialchars($campaign->name); ?>
      </h1>
      <div class="page-subtitle">
        <a href="<?php echo cn($module . '/campaign_details/' . $campaign->ids); ?>" class="btn btn-sm btn-secondary">
          <i class="fe fe-arrow-left"></i> Back to Campaign
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Import Options Row 1: Database Imports -->
<div class="row">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-database"></i> Import Active Users (With Orders)</h3>
      </div>
      <div class="card-body">
        <p>Import active users who have placed at least 1 order</p>
        <div class="alert alert-info mb-3">
          <small><strong>Note:</strong> Only users with order history will be imported to ensure better targeting.</small>
        </div>
        <form id="importUsersForm" action="<?php echo cn($module . '/ajax_import_from_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-download"></i> Import Active Users
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-users"></i> Import All Users (From Database)</h3>
      </div>
      <div class="card-body">
        <p>Import ALL registered users from the database</p>
        <div class="alert alert-warning mb-3">
          <small><strong>Note:</strong> This will import all users regardless of order history. Use for broader campaigns.</small>
        </div>
        <form id="importAllUsersForm" action="<?php echo cn($module . '/ajax_import_all_users'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <button type="submit" class="btn btn-warning">
            <i class="fe fe-download"></i> Import All Users
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Import Options Row 2: CSV and Manual Input -->
<div class="row">
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-upload"></i> Import from CSV/TXT File</h3>
      </div>
      <div class="card-body">
        <p>Upload a CSV file with phone numbers (format: phone_number,name)</p>
        <form id="importCSVForm" action="<?php echo cn($module . '/ajax_import_from_csv'); ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group">
            <input type="file" class="form-control" name="csv_file" accept=".csv,.txt" required>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fe fe-upload"></i> Upload & Import
          </button>
        </form>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;"><i class="fe fe-plus-circle"></i> Add Manual Phone Number</h3>
      </div>
      <div class="card-body">
        <p>Add individual phone numbers one by one</p>
        <form id="addManualPhoneForm" action="<?php echo cn($module . '/ajax_add_manual_phone'); ?>" method="POST">
          <input type="hidden" name="campaign_ids" value="<?php echo $campaign->ids; ?>">
          <div class="form-group">
            <label>Phone Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="phone" placeholder="+1234567890" required>
          </div>
          <div class="form-group">
            <label>Name (Optional)</label>
            <input type="text" class="form-control" name="name" placeholder="John Doe">
          </div>
          <button type="submit" class="btn btn-success">
            <i class="fe fe-plus"></i> Add Phone Number
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Recipients List -->
<div class="row mt-3">
  <div class="col-md-12">
    <div class="card p-0 content">
      <div class="card-header">
        <h3 class="card-title" style="color:#fff !important;">Recipients List (Showing last 100)</h3>
        <div class="card-options">
          <span class="badge bg-primary">Total: <?php echo number_format($campaign->total_messages); ?></span>
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
              <th>Error</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($recipients)){ 
              foreach($recipients as $recipient){
                $status_badge = 'secondary';
                switch($recipient->status){
                  case 'sent': $status_badge = 'success'; break;
                  case 'failed': $status_badge = 'danger'; break;
                }
            ?>
            <tr>
              <td><?php echo htmlspecialchars($recipient->phone_number); ?></td>
              <td><?php echo htmlspecialchars($recipient->name ?: '-'); ?></td>
              <td><span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($recipient->status); ?></span></td>
              <td><?php echo $recipient->sent_at ? date('M d, H:i', strtotime($recipient->sent_at)) : '-'; ?></td>
              <td><?php echo htmlspecialchars($recipient->error_message ?: '-'); ?></td>
            </tr>
            <?php }} else { ?>
            <tr>
              <td colspan="5" class="text-center">
                <p class="text-muted">No recipients added yet. Import users or upload a CSV file above.</p>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Define toast notification helper
  function showToast(message, type) {
    if (typeof $.toast === 'function') {
      $.toast({
        heading: type == 'success' ? 'Success' : 'Error',
        text: message,
        position: 'top-right',
        loaderBg: type == 'success' ? '#5ba035' : '#c9302c',
        icon: type,
        hideAfter: 3500
      });
    } else {
      alert(message);
    }
  }

  // Handle import from users
  $('#importUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var $button = $form.find('button[type="submit"]');
    var formData = $form.serialize();
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: formData,
      timeout: 60000,
      beforeSend: function(){
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          showToast(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          showToast(response.message, 'error');
          $button.prop('disabled', false).html('<i class="fe fe-download"></i> Import Users');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while importing users.';
        
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The import may be taking too long. Please check if users have been imported or try again.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.responseText) {
          try {
            var response = JSON.parse(xhr.responseText);
            if (response.message) {
              errorMsg = response.message;
            }
          } catch(e) {
            errorMsg = 'Error: ' + xhr.status + ' - ' + error;
          }
        }
        
        showToast(errorMsg, 'error');
        $button.prop('disabled', false).html('<i class="fe fe-download"></i> Import Users');
      }
    });
  });
  
  // Handle import all users
  $('#importAllUsersForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      timeout: 60000, // 60 seconds timeout
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importing...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
        }
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import All Users');
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while importing users.';
        if (status === 'timeout') {
          errorMsg = 'Request timed out. The server may still be processing. Please check back in a moment.';
        }
        show_message(errorMsg, 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-download"></i> Import All Users');
      }
    });
  });
  
  // Handle manual phone addition
  $('#addManualPhoneForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var formData = $form.serializeArray();
    
    // Add CSRF token if it exists
    var csrfToken = $('input[name="csrf_test_name"]').val();
    if (csrfToken) {
      formData.push({name: 'csrf_test_name', value: csrfToken});
    }
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: $.param(formData),
      beforeSend: function(){
        $form.find('button').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
      },
      success: function(response){
        if(response.status == 'success'){
          show_message(response.message, 'success');
          $form[0].reset(); // Clear form
          setTimeout(function(){
            location.reload();
          }, 500);
        } else {
          show_message(response.message, 'error');
        }
        $form.find('button').prop('disabled', false).html('<i class="fe fe-plus"></i> Add Phone Number');
      },
      error: function(){
        show_message('An error occurred while adding phone number.', 'error');
        $form.find('button').prop('disabled', false).html('<i class="fe fe-plus"></i> Add Phone Number');
      }
    });
  });
  
  // Handle CSV import
  $('#importCSVForm').on('submit', function(e){
    e.preventDefault();
    var $form = $(this);
    var $button = $form.find('button[type="submit"]');
    var formData = new FormData($form[0]);
    
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      dataType: 'json',
      data: formData,
      processData: false,
      contentType: false,
      timeout: 60000,
      beforeSend: function(){
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
      },
      success: function(response){
        if(response.status == 'success'){
          showToast(response.message, 'success');
          setTimeout(function(){
            location.reload();
          }, 1000);
        } else {
          showToast(response.message, 'error');
          $button.prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
        }
      },
      error: function(xhr, status, error){
        var errorMsg = 'An error occurred while uploading CSV.';
        
        if (status === 'timeout') {
          errorMsg = 'Request timed out. Please try with a smaller file.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        
        showToast(errorMsg, 'error');
        $button.prop('disabled', false).html('<i class="fe fe-upload"></i> Upload & Import');
      }
    });
  });
});
</script>
