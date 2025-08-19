<?php
// Database connection
$host = 'localhost';
$user = 'beastsmm_ali';
$pass = 'ra6efcTo[4z#';
$db = 'beastsmm_ali';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch current WhatsApp API settings
$query = "SELECT * FROM whatsapp_config WHERE id = 1";
$result = $conn->query($query);
$whatsapp_settings = $result->fetch_assoc();

// Handle form submission for WhatsApp API settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url']) && isset($_POST['api_key']) && isset($_POST['admin_phone'])) {
    $url = $conn->real_escape_string($_POST['url']);
    $api_key = $conn->real_escape_string($_POST['api_key']);
    $admin_phone = $conn->real_escape_string($_POST['admin_phone']);

    // Update or insert settings in the database
    if ($whatsapp_settings) {
        $update_query = "UPDATE whatsapp_config SET url = '$url', api_key = '$api_key', admin_phone = '$admin_phone' WHERE id = 1";
        $conn->query($update_query);
    } else {
        $insert_query = "INSERT INTO whatsapp_config (url, api_key, admin_phone) VALUES ('$url', '$api_key', '$admin_phone')";
        $conn->query($insert_query);
    }

    // Redirect to the same page to show updated values
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<div class="card content">
  <div class="">
    <h3 class="card-title"><i class="fa fa-question-circle"></i> <?=lang("Modules")?></h3>
  </div>
  <div class="card-body">
    
    <!-- WhatsApp Number Section -->
    <form class="actionForm" action="<?=cn("$module/ajax_whatsapp_settings")?>" method="POST">
      <div class="row">
        <div class="col-md-12 col-lg-12">
          <h5 class="text-info"><i class="fe fe-link"></i> <?=lang("Whatsapp_Number_Settings")?></h5>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label"><?=lang("Whatsapp_Number")?></label>
                <input class="form-control" name="whatsapp_number" value="<?=get_option('whatsapp_number')?>">
              </div>
            </div>
          </div>
        </div>   

        <div class="col-md-8">
          <div class="form-footer">
            <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
          </div>
        </div>
      </div>
    </form>
    
    <br><hr><br>

     <!-- WhatsApp API Settings Section -->
<h5 class="text-info"><i class="fe fe-link"></i> <?= lang("Whatsapp_API_Settings") ?></h5>
<form class="actionForm" action="" method="POST" data-redirect="<?php echo get_current_url(); ?>">
  <div class="row">
    <div class="col-md-4">
      <div class="form-group">
        <label class="form-label"><?= lang("url") ?></label>
        <input class="form-control" name="url" value="<?= htmlspecialchars($whatsapp_settings['url'] ?? '') ?>" required>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="form-label"><?= lang("api_key") ?></label>
        <input class="form-control" name="api_key" value="<?= htmlspecialchars($whatsapp_settings['api_key'] ?? '') ?>" required>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <label class="form-label"><?= lang("admin_phone") ?></label>
        <input class="form-control" name="admin_phone" value="<?= htmlspecialchars($whatsapp_settings['admin_phone'] ?? '') ?>" required>
      </div>
    </div>
  </div>
  <div class="form-footer">
    <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?= lang("Save") ?></button>
  </div>
</form>

    <br><hr><br>

    <!-- Refill Expiry Days Section -->
    <h5 class="text-info"><i class="fe fe-link"></i> <?= lang("refill_expiry_days") ?></h5>
    <form class="actionForm" action="<?=cn("$module/ajax_general_settings")?>" method="POST" data-redirect="<?php echo get_current_url(); ?>">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label><?= lang("disable_refill_option_from_and_order_after_X_days") ?></label>
            <select name="refill_expiry_days" class="form-control square">
              <?php for ($i = 1; $i <= 90; $i++) { ?>
                <option value="<?= $i ?>" <?= (get_option('refill_expiry_days', 30) == $i) ? 'selected' : '' ?>> <?= $i ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>    

      <div class="form-footer">
        <button class="btn btn-primary btn-min-width btn-lg text-uppercase"><?=lang("Save")?></button>
      </div>
    </form> 
  </div>  
</div>

<script>
  $(document).ready(function() {
    plugin_editor('.plugin_editor', {height: 200, toolbar: 'code'});
  });
</script>
