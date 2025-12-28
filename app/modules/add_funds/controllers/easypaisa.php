<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class easypaisa extends MX_Controller {
    protected $tb_transaction_logs = TRANSACTION_LOGS;
    protected $tb_payments         = PAYMENTS_METHOD;
    protected $payment_type;
    protected $payment_id;
    protected $payment_fee = 0;
    protected $currency_rate_to_usd = 1;
    private $imap_host;
    private $imap_user;
    private $imap_pass;

    public function __construct($payment = "") {
        parent::__construct();
        $this->load->model('add_funds_model', 'model');
        $this->payment_type = get_class($this); // Should resolve to 'Easypaisa'

        if (!$payment) {
            $payment = $this->model->get('*', $this->tb_payments, ['type' => $this->payment_type]);
        }

        if (!$payment) {
            show_error("Payment method '{$this->payment_type}' not configured.");
        }

        $this->payment_id = $payment->id;
        $params = $payment->params;
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        $option = isset($params['option']) ? $params['option'] : [];

        $this->imap_host = $option['imap_host'] ?? '{imap.gmail.com:993/imap/ssl}INBOX';
        $this->imap_user = $option['imap_user'] ?? '';
        $this->imap_pass = $option['imap_pass'] ?? '';
        $this->payment_fee = (float) ($option['tnx_fee'] ?? 0);
        $this->currency_rate_to_usd = (float) ($option['rate_to_usd'] ?? 1);
    }

    public function index() {
        redirect(cn('add_funds'));
    }

    /**
     * Creates a payment. Single attempt at IMAP auto-verification.
     * If success -> log completed.
     * If fail -> log pending immediately (no polling).
     */
    public function create_payment($data_payment = []) {
        $amount      = (float) ($data_payment['amount'] ?? 0);
        $tx_orderID  = post('order_id') ?: session('qrtransaction_id');

        if (!$amount || !$tx_orderID) {
            return $this->validation_fail('Amount and Order ID are required.');
        }
        if (strlen($tx_orderID) < 5 || strlen($tx_orderID) > 100) {
            return $this->validation_fail('Invalid Order ID format.');
        }

        // Prevent reuse
        $exists = $this->model->get('id', $this->tb_transaction_logs, [
            'transaction_id' => $tx_orderID,
            'type' => $this->payment_type
        ]);
        if ($exists) {
            return $this->validation_fail('This Transaction ID has already been used.');
        }

        $converted = $amount / ($this->currency_rate_to_usd ?: 1);

        // Single attempt auto verification
        $auto_verified = $this->auto_verify_transaction($tx_orderID, $amount);

        if ($auto_verified) {
            // SUCCESS
            $data_log = [
                'ids'            => ids(),
                'uid'            => session('uid'),
                'type'           => $this->payment_type,
                'transaction_id' => $tx_orderID,
                'amount'         => round($converted, 4),
                'txn_fee'        => round($converted * ($this->payment_fee / 100), 4),
                'note'           => $amount,
                'status'         => 1,
                'created'        => NOW,
            ];
            $this->db->insert($this->tb_transaction_logs, $data_log);

            $user_info  = session('user_current_info');
            $user_email = $user_info['email'] ?? 'N/A';
            $this->sendWhatsAppNotification($amount, $tx_orderID, $user_email, 'completed');

            // Apply bonus & email
            require_once 'add_funds.php';
            $adder = new add_funds();
            $adder->add_funds_bonus_email((object)$data_log, $this->payment_id);

            $this->load->view('easypaisa/redirect', [
                'status'    => 'success',
                'auto'      => true,
                'amount'    => $amount,
                'txid'      => $tx_orderID,
                'converted' => $converted,
                'error_msg' => '',
                'uid'       => session('uid'),
            ]);
        } else {
            // IMMEDIATE PENDING (no verification screen)
<<<<<<< HEAD
            // Load pay token helper and generate token for pending transactions
            $this->load->helper('pay_token');
            $pay_token = generate_pay_token();
            
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            $data_log = [
                'ids'            => ids(),
                'uid'            => session('uid'),
                'type'           => $this->payment_type,
                'transaction_id' => $tx_orderID,
                'amount'         => round($converted, 4),
                'txn_fee'        => round($converted * ($this->payment_fee / 100), 4),
                'note'           => $amount,
                'status'         => 0, // Pending
<<<<<<< HEAD
                'pay_token'      => $pay_token,
=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                'created'        => NOW,
            ];
            $this->db->insert($this->tb_transaction_logs, $data_log);

            $user_info  = session('user_current_info');
            $user_email = $user_info['email'] ?? 'N/A';
<<<<<<< HEAD
            $this->sendWhatsAppNotification($amount, $tx_orderID, $user_email, 'new', $pay_token);
            
            // Send transactional email to admin about payment submission
            $this->load->library('Transactional_email');
            $this->transactional_email->send_payment_submitted_email($tx_orderID, $user_email, round($converted, 4), $this->payment_type);
=======
            $this->sendWhatsAppNotification($amount, $tx_orderID, $user_email, 'new');
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98

            $this->load->view('easypaisa/redirect', [
                'status'    => 'pending',
                'auto'      => false,
                'amount'    => $amount,
                'txid'      => $tx_orderID,
                'converted' => $converted,
                'error_msg' => '',
                'uid'       => session('uid'),
            ]);
        }
    }

    /**
     * Deprecated: No longer used with instant pending fallback.
     */
    public function poll_verification() {
        echo json_encode(['status' => 'disabled']);
    }

    /**
     * Deprecated: No longer used since we log pending immediately.
     */
    public function finalize_pending() {
        echo json_encode(['status' => 'disabled']);
    }

    private function validation_fail($msg) {
        $this->load->view('easypaisa/redirect', [
            'status'    => 'failed',
            'auto'      => false,
            'amount'    => '',
            'txid'      => '',
            'converted' => '',
            'error_msg' => $msg,
            'uid'       => session('uid'),
        ]);
        exit;
    }

    /**
     * Attempts to find a matching transaction email once.
     * Returns true if found, false otherwise.
     */
    private function auto_verify_transaction($tx_orderID, $amount) {
        $imap_host = $this->imap_host;
        $imap_user = $this->imap_user;
        $imap_pass = $this->imap_pass;

        if (empty($imap_host) || empty($imap_user) || empty($imap_pass)) return false;

        imap_timeout(IMAP_OPENTIMEOUT, 5);
        imap_timeout(IMAP_READTIMEOUT, 5);

        $imap = @imap_open($imap_host, $imap_user, $imap_pass);
        if (!$imap) return false;

        $uids  = imap_search($imap, 'ALL', SE_UID);
        $found = false;

        if ($uids !== false && count($uids) > 0) {
            $recent       = array_slice(array_reverse($uids), 0, 8);
            $needleTxID   = strtolower($tx_orderID);
            $formattedAmt = number_format($amount, 2, '.', '');
            $keywords     = ['received', 'rs', 'easypaisa', 'from', 'raast', 'account', 'trx id', 'tid', 'transaction'];

            foreach ($recent as $uid) {
                $struct = imap_fetchstructure($imap, $uid, FT_UID);
                $raw    = (!empty($struct->parts) && isset($struct->parts[0]))
                    ? imap_fetchbody($imap, $uid, 1, FT_UID)
                    : imap_body($imap, $uid, FT_UID);

                switch ($struct->encoding ?? 0) {
                    case 3:  $body = base64_decode($raw); break;
                    case 4:  $body = quoted_printable_decode($raw); break;
                    default: $body = $raw;
                }

                $body           = strtolower(trim(preg_replace('/\s+/', ' ', $body)));
                $hasAmount      = preg_match('/\brs\s*' . preg_quote($formattedAmt, '/') . '\b/i', $body);
                $hasTxnID       = strpos($body, $needleTxID) !== false;
                $keywordMatches = 0;
                foreach ($keywords as $keyword) {
                    if (strpos($body, $keyword) !== false) $keywordMatches++;
                }

                if ($hasAmount && $hasTxnID && $keywordMatches >= 3) {
                    $found = true;
                    break;
                }
            }
        }

        imap_close($imap);
        return $found;
    }

<<<<<<< HEAD
    private function sendWhatsAppNotification($amount, $transaction_id, $user_email, $type = 'new', $pay_token = null) {
=======
    private function sendWhatsAppNotification($amount, $transaction_id, $user_email, $type = 'new') {
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        try {
            $config = $this->getWhatsAppConfig();
            if (!$config) return false;

            $api_url = $config->url;
            $admin_whatsapp_number = $config->admin_phone;
            $api_key = $config->api_key;

            if (empty($api_url) || empty($admin_whatsapp_number) || empty($api_key)) return false;

<<<<<<< HEAD
            if ($type === 'new' && $pay_token) {
                // Include pay URL for pending transactions (helper already loaded)
                $pay_url = get_pay_url($pay_token);
                $message = "*🆕 New Easypaisa Payment Submission!*\n\n💰 *Amount*: PKR {$amount}\n🔢 *Transaction ID*: {$transaction_id}\n📧 *User Email*: {$user_email}\n\n🔍 Awaiting manual verification.\n\n✅ *Approve Payment:*\n{$pay_url}";
            } else {
                $message = ($type === 'new')
                    ? "*🆕 New Easypaisa Payment Submission!*\n\n💰 *Amount*: PKR {$amount}\n🔢 *Transaction ID*: {$transaction_id}\n📧 *User Email*: {$user_email}\n\n🔍 Awaiting manual verification."
                    : "*✅ Easypaisa Payment Completed!*\n\n💰 *Amount*: PKR {$amount}\n🔢 *Transaction ID*: {$transaction_id}\n📧 *User Email*: {$user_email}\n\n✨ Transaction completed successfully.";
            }
=======
            $message = ($type === 'new')
                ? "*🆕 New Easypaisa Payment Submission!*\n\n💰 *Amount*: PKR {$amount}\n🔢 *Transaction ID*: {$transaction_id}\n📧 *User Email*: {$user_email}\n\n🔍 Awaiting manual verification."
                : "*✅ Easypaisa Payment Completed!*\n\n💰 *Amount*: PKR {$amount}\n🔢 *Transaction ID*: {$transaction_id}\n📧 *User Email*: {$user_email}\n\n✨ Transaction completed successfully.";
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98

            $data = [
                "apiKey"      => $api_key,
                "phoneNumber" => $admin_whatsapp_number,
                "message"     => $message
            ];

            $ch = curl_init($api_url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_TIMEOUT        => 30
            ]);

            $response = curl_exec($ch);
            curl_close($ch);
            $responseData = json_decode($response, true);

            return $responseData['success'] ?? false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getWhatsAppConfig() {
        try {
            $query = $this->db->select('url, admin_phone, api_key')
                              ->from('whatsapp_config')
                              ->limit(1)
                              ->get();
            if ($query->num_rows() > 0) return $query->row();
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}

