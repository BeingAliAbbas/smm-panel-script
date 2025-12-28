<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transactions Controller
 *
 * Features:
 *  - List / paginate transactions
 *  - Update transaction (admin)
 *  - Add manual funds directly from Transactions module (admin/supporter)
 *  - WhatsApp notification on successful status change (pending -> paid)
 *  - Search
 *
 * NOTE:
 *  - Make sure ms() helper (JSON response) and ids(), NOW constants exist (already in your codebase).
 *  - Ensure you add a trigger button in the transactions index view to open the add_funds_manual modal:
 *      <a href="<?=cn($module.'/add_funds_manual')?>" class="btn btn-outline-info btn-sm ajaxModal">
 *          <i class="fe fe-dollar-sign me-1"></i><?=lang('Add_Funds')?>
 *      </a>
 */
class transactions extends MX_Controller {

	public $module;
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_transaction_logs;
	public $columns;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');

		$this->module                = get_class($this);
		$this->tb_users              = USERS;
		$this->tb_categories         = CATEGORIES;
		$this->tb_services           = SERVICES;
		$this->tb_transaction_logs   = TRANSACTION_LOGS;

		// Columns (admin vs normal user)
		$this->columns = array(
			"uid"            => lang('User'),
			"transaction_id" => lang('Transaction_ID'),
			"type"           => lang('Payment_method'),
			"amount"         => lang('Amount_includes_fee'),
			"txn_fee"        => 'Transaction fee',
			"note"           => 'Note',
			"created"        => lang('Created'),
			"status"         => lang('Status'),
		);

		if (!get_role("admin")) {
			$this->columns = array(
				"type"     => lang('Payment_method'),
				"amount"   => lang('Amount_includes_fee'),
				"txn_fee"  => 'Transaction fee',
				"created"  => lang('Created'),
				"status"   => lang('Status'),
			);
		}
	}

	/**
	 * Index - transaction list
	 */
	public function index(){
		// Delete unpaid over 2 days
		$this->model->delete_unpaid_payment(2);

		$page            = (int)get("p");
		$page            = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page  = get_option("default_limit_per_page", 10);

		// Advanced filters for admin
		$filters = [];
		if (get_role('admin')) {
			$filters['user_email']     = get('user_email');
			$filters['date_from']      = get('date_from');
			$filters['date_to']        = get('date_to');
			$filters['payment_method'] = get('payment_method');
			$filters['status']         = get('status');
			$filters['amount_min']     = get('amount_min');
			$filters['amount_max']     = get('amount_max');
			$filters['sort_by']        = get('sort_by');
			$filters['sort_order']     = get('sort_order');
		}

		$query           = array_filter($filters); // Remove empty values
		$query_string    = (!empty($query)) ? "?".http_build_query($query) : "";

		$config = array(
			'base_url'         => cn(get_class($this).$query_string),
			'total_rows'       => $this->model->get_transaction_list(true, "all", "", "", $filters),
			'per_page'         => $limit_per_page,
			'use_page_numbers' => true,
			'prev_link'        => '<i class="fe fe-chevron-left"></i>',
			'first_link'       => '<i class="fe fe-chevrons-left"></i>',
			'next_link'        => '<i class="fe fe-chevron-right"></i>',
			'last_link'        => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links        = $this->pagination->create_links();
		$transactions = $this->model->get_transaction_list(false, "all", $limit_per_page, $page * $limit_per_page, $filters);

		// Get statistics for admin dashboard
		$stats = null;
		if (get_role('admin')) {
			$stats = $this->model->get_transaction_stats($filters);
		}

		$data = array(
			"module"       => $this->module,
			"columns"      => $this->columns,
			"transactions" => $transactions,
			"links"        => $links,
			"stats"        => $stats,
			"filters"      => $filters,
		);

		$this->template->build('index', $data);
	}

	/**
	 * Edit transaction modal
	 */
	public function update($ids = ""){
		if (!get_role('admin')) {
			redirect(cn());
		}
		$transaction = $this->model->get("*", $this->tb_transaction_logs, ['ids' => $ids]);
		$data = array(
			"module"       => $this->module,
			"transaction"  => $transaction,
		);
		$this->load->view('update', $data);
	}

	/**
	 * Update transaction (status / note / transaction id / method)
	 */
	public function ajax_update($ids = "") {
		if (!get_role('admin')) {
			ms(array('status' => 'error', 'message' => 'Permission denied'));
		}

		$uid            = (int)post("uid");
		$posted_ids     = trim(post("ids"));
		$note           = post("note");
		$transaction_id = trim(post("transaction_id"));
		$payment_method = trim(post("payment_method"));
		$status         = (int)post("status");

		if (!$posted_ids) {
			ms(array("status" => "error", "message" => 'Missing transaction reference'));
		}
		if (!$uid) {
			ms(array("status" => "error", "message" => 'User ID missing'));
		}
		if ($transaction_id == "") {
			ms(array("status" => "error", "message" => 'Transaction ID is required'));
		}
		if ($payment_method == "") {
			ms(array("status" => "error", "message" => 'Payment method is required'));
		}

		// Fetch by unique ids (more reliable) then confirm matching fields
		$check_item = $this->model->get("*", $this->tb_transaction_logs, ['ids' => $posted_ids]);
		if (empty($check_item) || (int)$check_item->uid !== $uid) {
			ms(array("status" => "error", "message" => 'Transaction does not exist'));
		}

		// Previous status
		$prev_status = (int)$check_item->status;

		// Prepare update data
		$update_data = array(
			'transaction_id' => $transaction_id,
			'type'           => $payment_method,
			'note'           => $note,
			'status'         => $status
		);

		$this->db->update($this->tb_transaction_logs, $update_data, ['ids' => $posted_ids]);

		if ($this->db->affected_rows() > 0) {
			// If moved from pending (0) to paid (1) credit the amount (net of fee) to user
			if ($status == 1 && $prev_status == 0) {
				$user_balance_obj = $this->model->get("balance", $this->tb_users, ['id' => $check_item->uid]);
				$current_balance  = $user_balance_obj ? $user_balance_obj->balance : 0;
				$new_balance      = $current_balance + ($check_item->amount - $check_item->txn_fee);
				$balance_update_result = $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => $check_item->uid]);

				// Only proceed with logging and notifications if balance update succeeded
				if ($balance_update_result) {
					// Log balance change
					$this->load->helper('balance_logs');
					log_payment_addition($check_item->uid, $transaction_id, ($check_item->amount - $check_item->txn_fee), $current_balance, $new_balance, $payment_method);

					// Send WhatsApp notification
					$this->send_transaction_success_whatsapp($check_item->uid, $transaction_id, $check_item->amount);

					// Create credit notification for in-panel popup
					$this->load->helper('notification');
					create_credit_notification($check_item->uid, ($check_item->amount - $check_item->txn_fee), $payment_method, $transaction_id);
					
					// Send transactional email to user about payment approval
					$this->load->library('Transactional_email');
					$this->transactional_email->send_payment_approved_email($check_item->uid, $transaction_id, ($check_item->amount - $check_item->txn_fee), $payment_method);
				}
			}

			ms(array("status" => "success", "message" => lang("Updated successfully")));
		} else {
			ms(array("status" => "info", "message" => 'No changes detected / already up to date'));
		}
	}

	/**
	 * Modal for adding manual funds (user looked up by email inside the modal)
	 */
	public function add_funds_manual() {
		if (!(get_role('admin') || get_role('supporter'))) {
			redirect(cn());
		}
		$payments_defaut = $this->model->fetch('type, name', PAYMENTS_METHOD, ['status' => 1]);

		$data = [
			'module'          => $this->module,
			'payments_defaut' => $payments_defaut,
		];
		$this->load->view('add_funds_manual', $data);
	}

	/**
	 * AJAX: Add manual funds (admin/supporter)
	 *
	 * POST fields:
	 *  - email
	 *  - funds
	 *  - payment_method
	 *  - transaction_id (optional)
	 *  - txt_note (optional)
	 *  - txt_fee (optional)
	 */
	public function ajax_add_funds_manual() {
		if (!(get_role('admin') || get_role('supporter'))) {
			ms(['status' => 'error', 'message' => 'Permission denied']);
		}

		$email          = trim(post('email'));
		$funds_raw      = post('funds');
		$funds          = (double)$funds_raw;
		$payment_method = trim(post('payment_method'));
		$transaction_id = trim(post('transaction_id'));
		$note           = trim(post('txt_note'));
		$fee_raw        = trim(post('txt_fee'));
		$fee            = ($fee_raw === '' ? 0 : (double)$fee_raw);

		if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			ms(['status' => 'error', 'message' => 'Valid user email is required']);
		}
		if ($funds <= 0) {
			ms(['status' => 'error', 'message' => 'Amount must be greater than zero']);
		}
		if ($payment_method == '') {
			ms(['status' => 'error', 'message' => 'Please choose a payment method']);
		}

		$user = $this->model->get('id, email, balance', $this->tb_users, ['email' => $email]);
		if (!$user) {
			ms(['status' => 'error', 'message' => 'User not found']);
		}

		if ($transaction_id == '') {
			$transaction_id = 'empty';
		}

		// Update user balance
		$old_balance = $user->balance;
		$new_balance = $user->balance + $funds;
		$balance_update_result = $this->db->update($this->tb_users, ['balance' => $new_balance], ['id' => $user->id]);

		// Only proceed with logging and notifications if balance update succeeded
		if ($balance_update_result) {
			// Log transaction
			$data_log = [
				'ids'            => ids(),
				'uid'            => $user->id,
				'type'           => $payment_method,
				'transaction_id' => $transaction_id,
				'amount'         => round($funds, 4),
				'txn_fee'        => round($fee, 4),
				'note'           => ($note != '') ? $note : $funds,
				'status'         => 1, // immediate credit
				'created'        => NOW,
			];
			$this->db->insert($this->tb_transaction_logs, $data_log);

			// Log balance change
			$this->load->helper('balance_logs');
			log_manual_funds($user->id, $funds, $old_balance, $new_balance, $note, $transaction_id);

			// Create credit notification for in-panel popup
			$this->load->helper('notification');
			create_credit_notification($user->id, $funds, $payment_method, $transaction_id);

			ms([
				'status'  => 'success',
				'message' => 'Funds added successfully',
			]);
		} else {
			// Log the failure for debugging
			log_message('error', 'Failed to update balance for user ID: ' . $user->id . ' - Attempted to add: ' . $funds);
			
			ms([
				'status'  => 'error',
				'message' => 'Failed to update user balance. Please try again or contact support if the issue persists.',
			]);
		}
	}

	/**
	 * Delete transaction (by ids)
	 */
	public function ajax_delete_item($ids = ""){
		if (!get_role('admin')) {
			ms(['status' => 'error', 'message' => 'Permission denied']);
		}
		$this->model->delete($this->tb_transaction_logs, $ids, false);
		ms(['status' => 'success', 'message' => 'Deleted']);
	}

	/**
	 * Export transactions to CSV
	 */
	public function export_csv(){
		if (!get_role('admin')) {
			redirect(cn($this->module));
		}

		// Get filters from query string
		$filters = [];
		$filters['user_email']     = get('user_email');
		$filters['date_from']      = get('date_from');
		$filters['date_to']        = get('date_to');
		$filters['payment_method'] = get('payment_method');
		$filters['status']         = get('status');
		$filters['amount_min']     = get('amount_min');
		$filters['amount_max']     = get('amount_max');

		$transactions = $this->model->get_transactions_for_export($filters);

		// Set headers for CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=transactions_'.date('Y-m-d_H-i-s').'.csv');

		// Create output stream
		$output = fopen('php://output', 'w');

		// Add BOM for Excel UTF-8 support
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

		// Add CSV headers
		fputcsv($output, array(
			'ID',
			'User Email',
			'User Name',
			'Transaction ID',
			'Payment Method',
			'Amount',
			'Transaction Fee',
			'Net Amount',
			'Status',
			'Note',
			'Created',
		));

		// Add data rows
		foreach ($transactions as $row) {
			$status_text = '';
			switch ($row->status) {
				case 1:
					$status_text = 'Paid';
					break;
				case 0:
					$status_text = 'Pending';
					break;
				case -1:
					$status_text = 'Cancelled';
					break;
			}

			$user_name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));

			fputcsv($output, array(
				$row->id,
				$row->email ?? '',
				$user_name,
				$row->transaction_id ?? '',
				$row->type ?? '',
				$row->amount ?? 0,
				$row->txn_fee ?? 0,
				($row->amount - $row->txn_fee),
				$status_text,
				$row->note ?? '',
				$row->created ?? '',
			));
		}

		fclose($output);
		exit;
	}

	/**
	 * View transaction details (modal)
	 */
	public function view_details($ids = ""){
		if (!get_role('admin')) {
			redirect(cn());
		}
		$transaction = $this->model->get_transaction_details($ids);
		$data = array(
			"module"      => $this->module,
			"transaction" => $transaction,
		);
		$this->load->view('view_details', $data);
	}

	/**
	 * Search page
	 */
	public function search(){
		if (!get_role('admin')) {
			redirect(cn($this->module));
		}
		$k              = htmlspecialchars(get('query'));
		$search_type    = (int)get('search_type');
		$data_search    = ['k' => $k, 'type' => $search_type];

		$page           = (int)get("p");
		$page           = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);

		$query          = ['query' => $k, 'search_type' => $search_type];
		$query_string   = "?".http_build_query($query);

		$config = array(
			'base_url'         => cn($this->module."/search".$query_string),
			'total_rows'       => $this->model->get_count_items_by_search($data_search),
			'per_page'         => $limit_per_page,
			'use_page_numbers' => true,
			'prev_link'        => '<i class="fe fe-chevron-left"></i>',
			'first_link'       => '<i class="fe fe-chevrons-left"></i>',
			'next_link'        => '<i class="fe fe-chevron-right"></i>',
			'last_link'        => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links        = $this->pagination->create_links();
		$transactions = $this->model->search_items_by_get_method($data_search, $limit_per_page, $page * $limit_per_page);

		$data = array(
			"module"       => $this->module,
			"columns"      => $this->columns,
			"transactions" => $transactions,
			"links"        => $links,
		);

		$this->template->build('index', $data);
	}

	/* ----------------------------------------------------------------------
	 * INTERNAL HELPERS
	 * -------------------------------------------------------------------- */

	/**
	 * Send WhatsApp message when a transaction becomes Paid.
	 * @param int    $user_id
	 * @param string $transaction_id
	 * @param mixed  $amount
	 */
	private function send_transaction_success_whatsapp($user_id, $transaction_id, $amount) {
		// Check if WhatsApp module is loaded and messaging is enabled
		$this->load->model('whatsapp/whatsapp_model', 'whatsapp_model');
		
		// Silently skip if messaging is globally disabled
		if (!$this->whatsapp_model->is_messaging_enabled()) {
			return;
		}

		$user_info    = $this->model->get("*", $this->tb_users, ['id' => $user_id]);
		if (!$user_info) return;

		$phone_number = $user_info->whatsapp_number ?? '';
		$first_name   = $user_info->first_name ?? 'Customer';
		$last_name    = $user_info->last_name ?? '';
		$full_name    = trim($first_name.' '.$last_name);

		$tx = $this->model->get("*", $this->tb_transaction_logs, [
			'uid'            => $user_id,
			'transaction_id' => $transaction_id
		]);
		$payment_method = $tx ? $tx->type : 'N/A';

		$phone_number = ltrim($phone_number, '+'); // basic cleanup

	$message =
"*Payment Received Successfully ✅*\n\n"
."Amount: *{$amount} PKR*\n"
."Transaction ID: `{$transaction_id}`\n"
."Payment Method: {$payment_method}\n\n"
."➡️ Place your order here: beastsmm.pk/order/add";

		$this->send_whatsapp_message($phone_number, $message);
	}

	/**
	 * Low-level WhatsApp message sender
	 */
	private function send_whatsapp_message($phoneNumber, $message) {
		$phoneNumber = preg_replace('/\D/', '', $phoneNumber);
		if ($phoneNumber == '') return;

		// Check if WhatsApp module is loaded, if not load it
		if (!isset($this->whatsapp_model)) {
			$this->load->model('whatsapp/whatsapp_model', 'whatsapp_model');
		}

		// Use the WhatsApp model's send_message method which checks the global toggle
		$result = $this->whatsapp_model->send_message($phoneNumber, $message);
		
		// If skipped due to global toggle being disabled, just return silently
		if (isset($result['skipped']) && $result['skipped']) {
			return;
		}
		
		// Handle errors if any
		if (!$result['success']) {
			log_message('error', 'WhatsApp send failed: ' . ($result['error'] ?? 'Unknown error'));
		}
	}

	/* ----------------------------------------------------------------------
	 * PAY URL SYSTEM - One-Click Payment Approval
	 * -------------------------------------------------------------------- */

	/**
	 * Handle payment approval via unique token URL
	 * URL format: /transactions/pay/{token}
	 * 
	 * This allows admin to approve payments by simply visiting the link
	 * sent via WhatsApp, without logging into the panel.
	 * 
	 * Security features:
	 * - Admin-only access (session-based authorization)
	 * - Token is cryptographically secure (64-char hex)
	 * - One-time use only (tracked via pay_token_used flag)
	 * - All access attempts are logged with IP, user agent, timestamp
	 * - Validates transaction is still in pending state
	 * - Server-side validation only, no client-side trust
	 */
	public function pay($token = '') {
		// Strict admin-only access check
		// Non-admin users and unauthenticated visitors get no output
		if (!get_role('admin')) {
			// Log unauthorized access attempt for security monitoring
			$this->log_pay_url_access(0, $token ?: 'empty', false, 'Unauthorized access - non-admin user');
			
			// Silent exit - no error messages, no redirects, no output
			return;
		}
		
		// Load helper for pay token functions
		$this->load->helper('pay_token');
		
		// Basic validation
		if (empty($token) || strlen($token) !== 64 || !ctype_xdigit($token)) {
			$this->log_pay_url_access(0, $token, false, 'Invalid token format');
			$this->load->view('pay_url_error', [
				'error_title' => 'Invalid Payment Link',
				'error_message' => 'This payment link is invalid or malformed.',
			]);
			return;
		}

		// Find transaction by token with row locking to prevent race conditions
		$this->db->select('*');
		$this->db->from($this->tb_transaction_logs);
		$this->db->where('pay_token', $token);
		$query = $this->db->get();
		$transaction = $query->row();
		
		if (!$transaction) {
			$this->log_pay_url_access(0, $token, false, 'Token not found');
			$this->load->view('pay_url_error', [
				'error_title' => 'Payment Link Not Found',
				'error_message' => 'This payment link does not exist or has expired.',
			]);
			return;
		}

		// Check if token was already used (strict comparison)
		if ((int)$transaction->pay_token_used === 1) {
			$this->log_pay_url_access($transaction->id, $token, false, 'Token already used');
			$this->load->view('pay_url_error', [
				'error_title' => 'Payment Already Processed',
				'error_message' => 'This payment has already been approved and processed.',
				'transaction_id' => $transaction->transaction_id,
				'used_at' => $transaction->pay_token_used_at,
			]);
			return;
		}

		// Check if transaction is still pending (strict comparison)
		if ((int)$transaction->status !== 0) {
			$status_text = (int)$transaction->status === 1 ? 'already paid' : 'cancelled';
			$this->log_pay_url_access($transaction->id, $token, false, "Transaction {$status_text}");
			$this->load->view('pay_url_error', [
				'error_title' => 'Payment Cannot Be Processed',
				'error_message' => "This transaction is {$status_text} and cannot be processed via this link.",
				'transaction_id' => $transaction->transaction_id,
			]);
			return;
		}

		// Get user information
		$user = $this->model->get('id, email, balance, first_name, last_name', $this->tb_users, ['id' => $transaction->uid]);
		if (!$user) {
			$this->log_pay_url_access($transaction->id, $token, false, 'User not found');
			$this->load->view('pay_url_error', [
				'error_title' => 'User Not Found',
				'error_message' => 'The user associated with this transaction could not be found.',
			]);
			return;
		}

		// All validation passed - process the payment
		$current_balance = $user->balance;
		$net_amount = $transaction->amount - $transaction->txn_fee;
		$new_balance = $current_balance + $net_amount;

		// Start transaction
		$this->db->trans_start();

		// Update user balance
		$this->db->update($this->tb_users, ['balance' => $new_balance], ['id' => $user->id]);

		// Mark transaction as paid and token as used
		$this->db->update($this->tb_transaction_logs, [
			'status' => 1,
			'pay_token_used' => 1,
			'pay_token_used_at' => NOW,
			'pay_token_used_by_ip' => $this->input->ip_address(),
		], ['id' => $transaction->id]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			// Transaction failed
			$this->log_pay_url_access($transaction->id, $token, false, 'Database transaction failed');
			$this->load->view('pay_url_error', [
				'error_title' => 'Processing Failed',
				'error_message' => 'An error occurred while processing the payment. Please try again or contact support.',
			]);
			return;
		}

		// Log successful payment approval
		$this->log_pay_url_access($transaction->id, $token, true, 'Payment approved successfully');

		// Log balance change
		$this->load->helper('balance_logs');
		log_payment_addition(
			$user->id, 
			$transaction->transaction_id, 
			$net_amount, 
			$current_balance, 
			$new_balance, 
			$transaction->type
		);

		// Create in-panel notification
		$this->load->helper('notification');
		create_credit_notification(
			$user->id, 
			$net_amount, 
			$transaction->type, 
			$transaction->transaction_id
		);

		// Send WhatsApp success notification to user
		$this->send_transaction_success_whatsapp(
			$user->id, 
			$transaction->transaction_id, 
			$transaction->amount
		);

		// Show success page
		$this->load->view('pay_url_success', [
			'transaction_id' => $transaction->transaction_id,
			'amount' => $transaction->amount,
			'net_amount' => $net_amount,
			'fee' => $transaction->txn_fee,
			'user_email' => $user->email,
			'user_name' => trim($user->first_name . ' ' . $user->last_name),
			'payment_method' => $transaction->type,
			'old_balance' => $current_balance,
			'new_balance' => $new_balance,
		]);
	}

	/**
	 * Log pay URL access attempt
	 * 
	 * @param int $transaction_id Transaction ID (0 if not found)
	 * @param string $token The pay token used
	 * @param bool $success Whether the access was successful
	 * @param string $error_message Error message if unsuccessful
	 */
	private function log_pay_url_access($transaction_id, $token, $success, $error_message = '') {
		$log_data = [
			'transaction_id' => $transaction_id,
			'pay_token' => $token,
			'access_ip' => $this->input->ip_address(),
			'user_agent' => $this->input->user_agent(),
			'success' => $success ? 1 : 0,
			'error_message' => $error_message,
			'accessed_at' => NOW,
		];

		// Check if table exists
		if ($this->db->table_exists('general_pay_url_logs')) {
			$this->db->insert('general_pay_url_logs', $log_data);
		} else {
			// Fallback to logging if table doesn't exist
			log_message('info', 'Pay URL access: ' . json_encode($log_data));
		}
	}

}