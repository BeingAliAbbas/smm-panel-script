<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class analytics extends MX_Controller {
	public $tb_users;
	public $tb_order;
	public $tb_services;
	public $tb_transaction_logs;
	public $module;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');

		// Check if user is admin
		if (!get_role('admin')) {
			redirect(cn('dashboard'));
		}

		$this->tb_users            = USERS;
		$this->tb_order            = ORDER;
		$this->tb_services         = SERVICES;
		$this->tb_transaction_logs = TRANSACTION_LOGS;
		$this->module              = get_class($this);
	}

	// Dashboard
	public function index(){
		$data = $this->model->get_dashboard_analytics();
		$data['module'] = $this->module;
		$this->template->build('dashboard', $data);
	}

	// Service Popularity
	public function service_popularity(){
		$data = $this->model->get_service_popularity();
		$data['module'] = $this->module;
		$this->template->build('service_popularity', $data);
	}

	// Payment History with Filters
	public function payment_history(){
		$status = $this->input->get('status');
		$date_from = $this->input->get('date_from');
		$date_to = $this->input->get('date_to');
		$payment_method = $this->input->get('payment_method');

		$data = $this->model->get_payment_history($status, $date_from, $date_to, $payment_method);
		$data['module'] = $this->module;
		$data['status_filter'] = $status;
		$data['date_from_filter'] = $date_from;
		$data['date_to_filter'] = $date_to;
		$data['payment_method_filter'] = $payment_method;
		
		$this->template->build('payment_history', $data);
	}

	// Export Orders (CSV)
	public function export_orders(){
		$status = $this->input->get('status');
		$date_from = $this->input->get('date_from');
		$date_to = $this->input->get('date_to');

		$orders = $this->model->get_orders_for_export($status, $date_from, $date_to);

		// Set headers for CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d_His') . '.csv');

		// Create output stream
		$output = fopen('php://output', 'w');

		// Add CSV headers
		fputcsv($output, ['Order ID', 'User Email', 'Service Name', 'Link', 'Quantity', 'Charge', 'Status', 'Created', 'API Order ID', 'Response']);

		// Add data rows
		foreach ($orders as $order) {
			fputcsv($output, [
				$order->ids,
				$order->user_email,
				$order->service_name,
				$order->link,
				$order->quantity,
				$order->charge,
				$order->status,
				$order->created,
				$order->api_order_id,
				$order->response
			]);
		}

		fclose($output);
		exit;
	}

	// Get analytics data via AJAX
	public function get_chart_data(){
		$type = $this->input->post('type');
		$result = [];

		switch($type){
			case 'revenue':
				$result = $this->model->get_revenue_chart_data();
				break;
			case 'orders':
				$result = $this->model->get_orders_chart_data();
				break;
			case 'users':
				$result = $this->model->get_users_chart_data();
				break;
		}

		echo json_encode($result);
	}
}
