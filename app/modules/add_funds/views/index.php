
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    .fa-circle-check{ color: green; }

    /* Minimal payment option row */
    .pay-opt{ display:flex; align-items:center; gap:10px; padding:6px 4px; }
    .pay-icon-img{ width:22px; height:22px; border-radius:4px; object-fit:contain; display:inline-block; }
    .pay-sel{ display:inline-flex; align-items:center; gap:8px; }
    .arrow-rotate{ transform:rotate(180deg); }

    /* === Select2 Unified Fix & Style (tuned) === */
    :root{
      --sel-font-size: clamp(14px, 3.6vw, 16px);
      --sel-line-height: 1.3;
      --sel-border: #04a9f4;
      --sel-border-focus: #0390cf;
      --sel-bg: #ffffff;
      --sel-bg-alt: #f5fbfe;
      --sel-text: #000;
      --sel-placeholder: #666;
      --sel-highlight-bg: #00599e;
      --sel-highlight-text: #fff;
      --sel-selected-bg: #d3eefc;
      --sel-meta-color: #000000;
      --sel-scrollbar-thumb: #04a9f4;
      --sel-scrollbar-track: #cfe8f5;
    }
    /* Dark palette (apply by body.dark) */
    body.dark, body[data-theme="dark"]{
      --sel-bg: #151d25;
      --sel-bg-alt: #1e2a34;
      --sel-text: #e9edf0;
      --sel-placeholder: #9aa8b3;
      --sel-highlight-bg: #0d6ea8;
      --sel-highlight-text: #ffffff;
      --sel-selected-bg: #043549;
      --sel-meta-color: #7ea0b8;
      --sel-scrollbar-thumb: #0d6ea8;
      --sel-scrollbar-track: #122029;
    }

    .cat-icon{ margin-right:6px; width:18px; min-width:18px; text-align:center; font-size:15px; color:#000; line-height:1; }
    body.dark .cat-icon{ color:#58c9ff; }

    /* Container: exact width of parent */
    .select2-container,
    .select2,
    .select2-container[style]{
      width: 100% !important;
      max-width: 100% !important;
      box-sizing: border-box;
      display: block;
      font-size: var(--sel-font-size);
    }
    select.select2-hidden-accessible{ display:none!important; }

    .select2-container--default .select2-selection--single{
      position:relative; width:100%; max-width:100%; min-height:44px; height:auto; margin-top:4px;
      border:1px solid var(--sel-border); border-radius:6px; padding:8px 42px 8px 12px;
      font-size:var(--sel-font-size); line-height:var(--sel-line-height); font-weight:500;
      color:var(--sel-text); background:var(--sel-bg); display:flex; align-items:flex-start;
      box-sizing:border-box; overflow:hidden; transition:border-color .18s ease, box-shadow .18s ease;
    }
    .select2-container--default.select2-container--open .select2-selection--single,
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:focus-visible{
      border-color:var(--sel-border-focus); box-shadow:0 0 0 3px rgba(4,169,244,.25); outline:none;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered{
      flex:1 1 auto; padding:0; margin:0; white-space:normal!important; line-height:var(--sel-line-height);
      word-break:break-word; overflow-wrap:anywhere; color:var(--sel-text)!important; font-size:16px; font-weight:600;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder{
      color:var(--sel-placeholder)!important; white-space:normal; word-break:break-word; overflow-wrap:anywhere; font-size:16px; font-weight:600;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{
      position:absolute; top:50%!important; right:10px; transform:translateY(-50%); height:auto; width:22px; display:flex; align-items:center; justify-content:center; pointer-events:none;
    }

    .select2-container--open{ z-index:10001!important; }

    /* Dropdown: match trigger width; do not force parent's width */
    .select2-container--default .select2-dropdown{
      width: 10% !important;    /* allow natural sizing */
      min-width: 100%;           /* never smaller than trigger */
      border:1px solid var(--sel-border); border-radius:6px; box-sizing:border-box; background:var(--sel-bg);
      height:auto!important; margin-top:2px; overflow:hidden; animation:selFade .12s ease;
    }
    .select2-container--default.select2-container--above .select2-dropdown{ margin-top:-2px; margin-bottom:2px; }
    @keyframes selFade{ from{opacity:0; transform:translateY(-4px);} to{opacity:1; transform:translateY(0);} }

    .select2-container .select2-search--dropdown{ padding:6px 8px 4px; background:var(--sel-bg-alt); }
    .select2-container .select2-search--dropdown .select2-search__field{
      border:1px solid var(--sel-border); padding:6px 8px; border-radius:4px; width:100%!important;
      font-size:var(--sel-font-size); color:var(--sel-text); background:var(--sel-bg); outline:none; box-shadow:none;
    }
    .select2-container .select2-search--dropdown .select2-search__field:focus{
      border-color:var(--sel-border-focus); box-shadow:0 0 0 2px rgba(4,169,244,.25);
    }

    /* Results height */
    .select2-container--default .select2-results > .select2-results__options{
      max-height: 280px !important;
      overflow-y:auto; overflow-x:hidden; margin:4px 0; padding:6px 0 8px; background:var(--sel-bg-alt);
      scrollbar-width:thin; scrollbar-color:var(--sel-scrollbar-thumb) var(--sel-scrollbar-track);
    }
    .select2-container--default .select2-results__options::-webkit-scrollbar{ width:10px; }
    .select2-container--default .select2-results__options::-webkit-scrollbar-track{ background:var(--sel-scrollbar-track); border-radius:6px; }
    .select2-container--default .select2-results__options::-webkit-scrollbar-thumb{ background:var(--sel-scrollbar-thumb); border-radius:6px; }

    .select2-container--default .select2-results__option{
      border:1px solid var(--sel-border); background:var(--sel-bg); color:var(--sel-text);
      border-radius:5px; margin:4px 10px; padding:10px 10px 10px 12px; font-size:var(--sel-font-size);
      line-height:1.25; font-weight:500; white-space:normal; word-break:break-word; overflow-wrap:anywhere;
      cursor:pointer; transition:background .15s ease, color .15s ease, border-color .15s ease; min-height:44px;
      display:flex; flex-direction:column; justify-content:center;
    }
    .select2-container--default .select2-results__option[aria-selected="true"]{
      background:var(--sel-selected-bg); color:var(--sel-text); border-color:var(--sel-border);
    }
    .select2-container--default .select2-results__option.select2-results__option--highlighted{
      background:var(--sel-highlight-bg); color:var(--sel-highlight-text)!important; border-color:var(--sel-highlight-bg);
    }

    @media (max-width:680px){
      .select2-container--default .select2-selection--single{ padding:10px 44px 10px 14px; border-radius:8px; }
      .select2-container--default .select2-results__option{ margin:4px 8px; padding:12px 12px; min-height:48px; }
      .select2-container--default .select2-dropdown{ border-radius:8px; }
      .select2-container .select2-search--dropdown .select2-search__field{ font-size:clamp(15px,4.5vw,17px); }
      .select2-container--default .select2-results > .select2-results__options{ max-height: 320px !important; }
    }
    @media (max-width:380px){
      .select2-container--default .select2-results__option{ margin:3px 6px; padding:10px 10px; }
      .select2-container--default .select2-results > .select2-results__options{ max-height: 360px !important; }
    }
    .youtube-btn {
  display: inline-block;
  padding: 0.92em 2em;
  font-size: 1.22em;
  font-weight: 700;
  color: #fff !important;
  background: #e52d27;
  border-radius: 44px;
  border: none;
  text-decoration: none !important;
  box-shadow: 0 2px 12px rgba(229,45,39,.18);
  transition: background .21s cubic-bezier(.4,0,.2,1), box-shadow .19s;
  outline: none;
}
.youtube-btn:hover,
.youtube-btn:focus {
  background: #b31217;
  color: #fff;
  box-shadow: 0 4px 24px rgba(229,45,39,.27);
  text-decoration: none;
}
.youtube-btn i.fa-youtube-play {
  font-size: 1.1em;
  vertical-align: middle;
}
  </style>
</head>

<!-- Add class="dark" so the dark variables apply (fixes white box on dark pages) -->
<body class="dark">

<div class="container-fluid mt-3">
  <?php if (get_option('add_funds_text','') != ''): ?>
    <div class="row"><div class="col-sm-12">
      <div class="card"><div class="card-body"><?= get_option('add_funds_text','') ?></div></div>
    </div></div>
  <?php endif; ?>

  <?php if (get_code_part_by_position('add_funds', 'top', '') != ''): ?>
    <div class="row"><div class="col-sm-12">
      <?= get_code_part_by_position('add_funds', 'top', '') ?>
    </div></div>
  <?php endif; ?>

<!-- Red rounded YouTube button instead of accordion -->
<br>
  <div class="row mb-3">
    <div class="col-sm-12 text-center">
      <a href="https://www.youtube.com/embed/wnCQolxg7OY?si=IHJBmRhhcZPAlbHr"
         target="_blank"
         class="youtube-btn">
        <i class="fab fa-youtube-play me-2"></i> Watch Tutorial on YouTube
      </a>
    </div>
  </div>

 <section class="add-funds m-t-30">
  <div class="row justify-content-md-center" id="result_ajaxSearch">
    <div class="col-md-8">
      <div class="card p-0">

        <!-- Added header section similar to your first code -->
        <div class="card-header" style="border: 0.1px solid #003a75; border-radius: 3.5px 3.5px 0 0; background: #003a75 !important;">
          <h3 class="card-title" style="color: #ffffff !important;">Add Funds</h3>
        </div>

        <div class="d-flex align-items-center justify-content-center">
          <div class="tabs-list w-100 p-3">
            <div class="form-group mb-0 w-100">
              <label for="paymentTypeDropdown" class="mb-2"><b>Please select a payment Method</b></label>
              <select id="paymentTypeDropdown" class="form-control">
                <option value="">Loading payment methods...</option>
              </select>
              <div id="paymentMethodError" style="display:none; color: red; margin-top: 10px;"></div>
            </div>
          </div>
        </div>

     

            <div class="card-body">
              <div class="tab-content" id="paymentTabContent">
                <!-- Payment method tabs will be loaded dynamically -->
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>

  <section class="latest-transactions m-t-30">
    <div class="row justify-content-md-center">
      <div class="col-md-8 p-0">
        <div class="transaction-card">
          <div class="transaction-card-header1 p-3" style="background: #003a75; border-radius: 8px 8px 0 0;">
            <h5 class="transaction-card-title text-white">Last 5 Transactions</h5>
          </div>
          <div class="transaction-card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" style="border-collapse:collapse; margin:0;">
                <thead>
                  <tr><th>No.</th><th>Transaction ID</th><th>Payment Method</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php if (!empty($transactions)):
                  $last_transactions = array_reverse(array_slice($transactions, 0, 5));
                  $i = 1;
                  foreach ($last_transactions as $transaction): ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($transaction->transaction_id); ?></td>
                    <td>
                      <?php
                        if ($transaction->type == 'easypaisa') {
                          echo '<img src="https://ipg2.apps.net.pk/_next/static/media/13.49fc814f.png" alt="Easypaisa" class="payment-icon" />';
                        } elseif ($transaction->type == 'jazzcash') {
                          echo '<img src="https://ipg2.apps.net.pk/_next/static/media/1.593d5ab6.png" alt="JazzCash" class="payment-icon" />';
                        } else {
                          echo 'Manual';
                        }
                      ?>
                    </td>
                    <td><?php echo $currency_symbol . number_format((float)$transaction->amount, 2); ?></td>
                    <td>
                      <span style="color: <?php echo ((int)$transaction->status === 1) ? '#05d0a1' : 'red'; ?>;">
                        <?php echo ((int)$transaction->status === 1) ? 'Paid' : 'Unpaid'; ?>
                      </span>
                    </td>
                    <td><?php echo isset($transaction->created) ? date('Y-m-d H:i:s', strtotime($transaction->created)) : 'Date not available'; ?></td>
                  </tr>
                  <?php endforeach; else: ?>
                  <tr><td colspan="6" class="text-center">No transactions found.</td></tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div><!-- /table-responsive -->
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php if (get_code_part_by_position('add_funds', 'bottom', '') != ''): ?>
    <div class="row"><div class="col-sm-12">
      <?= get_code_part_by_position('add_funds', 'bottom', '') ?>
    </div></div>
  <?php endif; ?>

</div><!-- /container-fluid -->

<!-- jQuery (must be before Select2 and Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS (for collapse/accordion) -->
<script src="<?php echo BASE; ?>assets/js/vendors/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function ($) {
  $(function () {
    // Accordion arrow
    $('#collapseAddFunds')
      .on('show.bs.collapse', function(){ $(this).prev().find('.arrow-icon').addClass('arrow-rotate'); })
      .on('hide.bs.collapse', function(){ $(this).prev().find('.arrow-icon').removeClass('arrow-rotate'); });

    var $dd = $('#paymentTypeDropdown');
    if (!$dd.length || typeof $.fn.select2 !== 'function') return;

    var paymentMethods = []; // Store loaded payment methods
    var isLoading = false;
    var hasLoaded = false;
    
    // CSRF token for AJAX requests
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    // Visuals (no extra metadata)
    function getPaymentVisual(type){
      type = (type||'').toLowerCase();
      if (type === 'easypaisa') return { img:'https://ipg2.apps.net.pk/_next/static/media/13.49fc814f.png', label:'Easypaisa' };
      if (type === 'jazzcash')  return { img:'https://ipg2.apps.net.pk/_next/static/media/1.593d5ab6.png',  label:'JazzCash'  };
      if (type === 'manual')    return { icon:'fa-regular fa-clipboard',            label:'Manual'    };
      return { icon:'fa-solid fa-wallet', label: type ? (type.charAt(0).toUpperCase()+type.slice(1)) : '' };
    }

    function templateResult(opt){
      if (!opt.id) return opt.text;
      var $el = $(opt.element), vis = getPaymentVisual($el.val());
      var label = $.trim($el.text()) || vis.label || opt.text;
      var iconHtml = vis.img ? '<img class="pay-icon-img" src="'+vis.img+'" alt="">' : '<i class="'+(vis.icon||'')+'"></i>';
      return $('<div class="pay-opt">'+ iconHtml +'<span class="pay-label">'+ $('<span>').text(label).html() +'</span></div>');
    }

    function templateSelection(opt){
      if (!opt.id) return opt.text;
      var $el = $(opt.element), vis = getPaymentVisual($el.val());
      var label = $.trim($el.text()) || vis.label || opt.text;
      var iconHtml = vis.img ? '<img class="pay-icon-img" src="'+vis.img+'" alt="">' : '<i class="'+(vis.icon||'')+'"></i>';
      return $('<span class="pay-sel">'+ iconHtml +'<span class="pay-label">'+ $('<span>').text(label).html() +'</span></span>');
    }

    function activatePane(type){
      $('.tab-pane').removeClass('in active show');
      if (type) $('#'+type).addClass('in active show');
    }

    // Function to load payment method content dynamically
    function loadPaymentContent(type) {
      var $tabPane = $('#' + type);
      if ($tabPane.length === 0) {
        // Create tab pane if it doesn't exist
        $tabPane = $('<div>', {
          id: type,
          class: 'tab-pane fade'
        });
        $('#paymentTabContent').append($tabPane);
      }

      // Load content only if not already loaded
      if ($tabPane.html().trim() === '') {
        // Show loading indicator
        $tabPane.html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading payment form...</p></div>');
        
        $.ajax({
          url: '<?php echo cn("add_funds/get_payment_form"); ?>',
          type: 'POST',
          data: { 
            payment_type: type,
            [csrfName]: csrfHash
          },
          success: function(response) {
            // Check if response is empty or contains error
            if (!response || response.trim() === '') {
              $tabPane.html('<div class="alert alert-danger">Payment form is empty. Please contact support.</div>');
            } else {
              $tabPane.html(response);
            }
          },
          error: function(xhr, status, error) {
            // Provide more detailed error message
            var errorMsg = 'Failed to load payment method. ';
            if (xhr.status === 404) {
              errorMsg += 'Payment method not found.';
            } else if (xhr.status === 500) {
              errorMsg += 'Server error occurred.';
            } else if (status === 'timeout') {
              errorMsg += 'Request timed out.';
            } else {
              errorMsg += 'Please try again.';
            }
            $tabPane.html('<div class="alert alert-danger">' + errorMsg + '</div>');
            
            // Log error for debugging (in browser console)
            console.error('Payment form load error:', {
              status: xhr.status,
              statusText: xhr.statusText,
              error: error,
              type: type
            });
          }
        });
      }
    }

    // Function to load payment methods from server
    function loadPaymentMethods() {
      if (isLoading || hasLoaded) return;
      
      isLoading = true;
      $('#paymentMethodError').hide();
      
      $.ajax({
        url: '<?php echo cn("add_funds/get_payment_methods"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          isLoading = false;
          hasLoaded = true;

          if (response.status === 'success' && response.data && response.data.length > 0) {
            paymentMethods = response.data;
            
            // Clear existing options
            $dd.empty();
            $dd.append('<option value="">Select Payment Type</option>');
            
            // Add payment methods to dropdown
            $.each(response.data, function(index, payment) {
              $dd.append(
                $('<option>', {
                  value: payment.type,
                  text: payment.name,
                  'data-id': payment.id,
                  'data-params': JSON.stringify(payment.params)
                })
              );
            });

            // Initialize Select2
            initializeSelect2();
            
          } else if (response.status === 'success' && response.data && response.data.length === 0) {
            // No payment methods available
            $dd.empty();
            $dd.append('<option value="">No payment methods available</option>');
            $('#paymentMethodError').text('No payment methods available at this time.').show();
          } else {
            // Error response
            showError('Failed to load payment methods. Please try again.');
          }
        },
        error: function(xhr, status, error) {
          isLoading = false;
          showError('Failed to load payment methods. Please refresh the page and try again.');
        }
      });
    }

    function showError(message) {
      $dd.empty();
      $dd.append('<option value="">Select Payment Type</option>');
      $('#paymentMethodError').text(message).show();
    }

    function initializeSelect2() {
      // Destroy existing select2 if any
      if ($dd.data('select2')) {
        $dd.select2('destroy');
      }

      $dd.select2({
        placeholder: 'Select Payment Type',
        allowClear: false,
        width: '100%',
        minimumResultsForSearch: Infinity,
        templateResult: templateResult,
        templateSelection: templateSelection,
        escapeMarkup: function(m){ return m; },
        dropdownParent: $(document.body),
        dropdownAutoWidth: true
      })
      .on('change', function(){
        var v = $(this).val();
        if (v) {
          // Load payment content
          loadPaymentContent(v);
        }
        activatePane(v);
        try { localStorage.setItem('selectedPaymentType', v || ''); } catch(e){}
      })
      .on('select2:open', function(){
        var w = $dd.data('select2').$container.outerWidth();
        $('.select2-container--open .select2-dropdown').css('min-width', w);
      });

      // Restore selection, if any
      try {
        var prev = localStorage.getItem('selectedPaymentType');
        if (prev && $dd.find('option[value="'+prev+'"]').length) {
          $dd.val(prev).trigger('change');
        }
      } catch(e) {}
    }

    // Load payment methods on page load for better UX
    loadPaymentMethods();
  });
})(jQuery);
</script>
