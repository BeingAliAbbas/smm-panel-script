<head>
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<?php if (get_option('add_funds_text','') != '') { ?>
<div class="col-sm-12 col-sm-12">
  <div class="row">
    <div class="card">
      <div class="card-body">
        <?=get_option('add_funds_text','')?>
      </div>
    </div>
  </div>
</div>
<?php }?>

<div class="">
  <div class="accordion" id="faqAddFunds">
    <div class="card" style="background: transparent; border: none;">
      <div class="card-header" id="headingAddFunds" style="background: transparent; border: none; padding-left: 0;">
        <h2 class="mb-0">
         <button class="btn btn-link text-white d-flex align-items-center" type="button" data-toggle="collapse" data-target="#collapseAddFunds" aria-expanded="false" aria-controls="collapseAddFunds" style="font-size: 1.3rem; text-decoration: none;">
            <span class="mr-2 arrow-icon" style="transition: transform 0.3s;">
              <i class="fa fa-chevron-down"></i>
            </span>
            <i class="fa fa-question-circle mr-2"></i> How to add funds?
          </button>
        </h2>
      </div>
      <div id="collapseAddFunds" class="collapse" aria-labelledby="headingAddFunds" data-parent="#faqAddFunds">
        <div class="card-body" style="text-align: center;">
          <div style="
            display: inline-block;
            padding: 4px;
            border-radius: 24px;
            background: linear-gradient(to right, #05cbfd 0%, #203d9d 100%);
            margin-left: -26px; /* Added margin left */
          ">
            <iframe
              width="315"
              height="570"
              src="https://www.youtube.com/embed/wnCQolxg7OY?si=IHJBmRhhcZPAlbHr"
              title="YouTube Shorts"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              style="
                border-radius: 20px;
                background: #fff;
                border: none;
                display: block;
              "
            ></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<?php if ($payments): ?>
<section class="add-funds m-t-30">   
  <div class="container-fluid">
    <div class="row justify-content-md-center" id="result_ajaxSearch">
      <div class="col-md-8">
        <div class="card">
          <div class=" d-flex align-items-center justify-content-center">
            <div class="tabs-list">
              <div class="form-group">
                <label for="paymentTypeDropdown"><b>Please select a payment Method</b></label>
                <select id="paymentTypeDropdown" class="form-control">
                  <option value="">Select Payment Type</option>
                  <?php foreach ($payments as $row): ?>
                    <option value="<?php echo $row->type; ?>"><?php echo $row->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <?php foreach ($payments as $row): ?>
              <div id="<?php echo $row->type; ?>" class="tab-pane fade">
                <?php $this->load->view($row->type.'/index', ['payment_id' => $row->id, 'payment_params' => $row->params]); ?>
              </div>  
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
<section class="latest-transactions m-t-30">
  <div class="container-fluid p-3">
    <div class="row justify-content-md-center">
      <div class="col-md-8 p-0">
        <div class="transaction-card">
          <div class="transaction-card-header">
            <h5 class="transaction-card-title text-white">Last 5 Transactions</h5>
          </div>
          <div class="transaction-card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" style="border-collapse: collapse; margin: 0;">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Transaction ID</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($transactions)): ?>
                    <?php
                      $last_transactions = array_reverse(array_slice($transactions, 0, 5));
                      $i = 1;
                    ?>
                    <?php foreach ($last_transactions as $transaction): ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($transaction->transaction_id); ?></td>
                        <td>
                          <?php 
                            if ($transaction->type == 'easypaisa') {
                                echo '<img src="assets/images/payments/easypaise.png" alt="Easypaisa" class="payment-icon" />';
                            } elseif ($transaction->type == 'jazzcash') {
                                echo '<img src="assets/images/payments/jazzcash.png" alt="Jazzcash" class="payment-icon" />';
                            } else {
                                echo 'Manual';
                            }
                          ?>
                        </td>
                        <td><?php echo $currency_symbol . number_format($transaction->amount, 2); ?></td>
                        <td>
                          <span style="color: <?php echo $transaction->status == 1 ? '#05d0a1' : 'red'; ?>;">
                            <?php echo $transaction->status == 1 ? 'Paid' : 'Unpaid'; ?>
                          </span>
                        </td>
                        <td>
                          <?php
                            if (isset($transaction->created)) {
                              echo date('Y-m-d H:i:s', strtotime($transaction->created));
                            } else {
                              echo 'Date not available';
                            }
                          ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">No transactions found.</td>
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
</section>

<script>
    $(document).ready(function() {
        $('#paymentTypeDropdown').on('change', function() {
            var selectedPaymentType = $(this).val();
            $('.tab-pane').removeClass('in active show');
            $('#' + selectedPaymentType).addClass('in active show');
        });
    });
</script>
<script>
  document.getElementById('videoThumbnail').addEventListener('click', function() {
    document.getElementById('videoThumbnail').style.display = 'none';
    document.getElementById('videoPlayer').style.display = 'block';
  });
</script>
<style>

  .video-thumbnail-container {
    position: relative;
    display: inline-block;
    cursor: pointer;
    border: 2px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .video-thumbnail-container:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }
  .video-thumbnail-img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
  }
  .video-player {
    margin-top: 20px;
  }
  .embed-responsive-16by9 {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    height: 0;
    overflow: hidden;
    max-width: 100%;
    border-radius: 8px;
    background: #000;
  }
  .embed-responsive-item {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }

  /* Animated Add Funds Text Styles */
  .add-funds-animated-box {
    position: relative;
    margin: 30px 0;
    border-radius: 14px;
    overflow: hidden;
    /*box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.22);*/
  background: linear-gradient(145deg, #061d2d, #0b304a);
    color: #fff;
    padding: 32px 24px;
    z-index: 1;
    text-align: center;
  }
  .add-funds-animated-box .add-funds-animated-text {
    font-size: 1.4rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.24);
    animation: fadeInScale 1.2s cubic-bezier(0.17,0.67,0.83,0.67) both, colorPulse 2.5s infinite alternate;
    position: relative;
    z-index: 2;
  }
 .animated-gradient-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(145deg, #061d2d, #0b304a);
  opacity: 0.25;
  z-index: 1;
  animation: gradientMove 4s linear infinite;
  border: 0.5px solid #333;
  border-radius: inherit;
}
  @keyframes fadeInScale {
    0% { opacity:0; transform:scale(0.85);}
    100% { opacity:1; transform:scale(1);}
  }
  @keyframes colorPulse {
    0% { color: #fff; }
    100% { color: #0ea5e9; }
  }
  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    /*100% { background-position: 100% 50%; }*/
  }
  @media (max-width: 767px) {
    .video-thumbnail-container,
    .add-funds-animated-box { width: 100%; }
    .add-funds-animated-box { padding: 18px 5px; font-size: 1.1rem;}
    .add-funds-animated-text { font-size: 1.1rem;}
  }
</style>

give me full updated and clean code