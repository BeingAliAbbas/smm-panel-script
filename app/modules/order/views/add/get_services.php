<?php
$current_currency = get_current_currency();
$currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol', "$");
$decimal_places  = get_option('currency_decimal', 2);
?>

<div class="form-group service-select-wrapper" data-wrapper="service">
  <label for="dropdownservices"><?= lang("order_service") ?></label>
  <select
      id="dropdownservices"
      name="service_id"
      class="form-control square ajaxChangeService service-select"
      data-url="<?= cn($module . "/get_service/") ?>"
      aria-label="<?= lang('order_service') ?>"
      aria-describedby="serviceHelpText">
    <?php if (!empty($services)): ?>
      <?php foreach ($services as $service):
        // Base price or custom override
        $service_price = $service->price;
        if (get_role('user') && isset($custom_rates[$service->id])) {
          $service_price = $custom_rates[$service->id]['service_price'];
        }
        // Convert price to selected currency
        $converted_price = convert_currency($service_price);
        $price_per_1k = currency_format($converted_price, $decimal_places);
        $safe_name    = htmlspecialchars($service->name, ENT_QUOTES, 'UTF-8');
<<<<<<< HEAD
        $safe_name_with_id = (int)$service->id . " - " . $safe_name;
=======
        $label_full   = "ID: {$service->id} {$service->name} - {$currency_symbol}{$price_per_1k}";
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
      ?>
        <option
          value="<?= (int)$service->id ?>"
          data-dripfeed="<?= (int)$service->dripfeed ?>"
          data-min="<?= (int)$service->minimum ?>"
          data-max="<?= (int)$service->maximum ?>"
          data-rate="<?= $price_per_1k ?>"
          data-price-raw="<?= htmlspecialchars($converted_price, ENT_QUOTES, 'UTF-8') ?>"
<<<<<<< HEAD
          data-name="<?= $safe_name_with_id ?>"
          data-fullname="<?= $safe_name_with_id ?>"
          title="<?= htmlspecialchars($safe_name_with_id, ENT_QUOTES, 'UTF-8') ?>"
        ><?= htmlspecialchars($safe_name_with_id, ENT_QUOTES, 'UTF-8') ?></option>
=======
          data-name="<?= $safe_name ?>"
          data-fullname="<?= $safe_name ?>"
          title="<?= htmlspecialchars($label_full, ENT_QUOTES, 'UTF-8') ?>"
        ><?= htmlspecialchars($label_full, ENT_QUOTES, 'UTF-8') ?></option>
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</div>



