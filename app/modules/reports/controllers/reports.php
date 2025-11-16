<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class reports extends MX_Controller {
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

	// Revenue Report
	public function revenue(){
		$period = $this->input->get('period') ?: 'monthly';
		$year = $this->input->get('year') ?: date('Y');
		
		$data = $this->model->get_revenue_report($period, $year);
		$data['module'] = $this->module;
		$data['period'] = $period;
		$data['year'] = $year;
		
		$this->template->build('revenue', $data);
	}

	// User Growth Report
	public function user_growth(){
		$data = $this->model->get_user_growth_report();
		$data['module'] = $this->module;
		
		$this->template->build('user_growth', $data);
	}

	// Service Performance Report
	public function service_performance(){
		$date_from = $this->input->get('date_from');
		$date_to = $this->input->get('date_to');
		
		$data = $this->model->get_service_performance_report($date_from, $date_to);
		$data['module'] = $this->module;
		$data['date_from'] = $date_from;
		$data['date_to'] = $date_to;
		
		$this->template->build('service_performance', $data);
	}

	// Export Revenue Report
	public function export_revenue(){
		$period = $this->input->get('period') ?: 'monthly';
		$year = $this->input->get('year') ?: date('Y');
		
		$data = $this->model->get_revenue_report($period, $year);
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=revenue_report_' . $year . '_' . $period . '.csv');
		
		$output = fopen('php://output', 'w');
		fputcsv($output, ['Period', 'Orders', 'Revenue', 'Average Order Value']);
		
		foreach ($data['report_data'] as $row) {
			fputcsv($output, [
				$row['period'],
				$row['orders'],
				$row['revenue'],
				$row['avg_order_value']
			]);
		}
		
		fclose($output);
		exit;
	}

	// Generate PDF Report (if needed)
	public function generate_pdf(){
		$this->load->library('Pdf');
		
		$period = $this->input->get('period') ?: 'monthly';
		$year = $this->input->get('year') ?: date('Y');
		
		$data = $this->model->get_revenue_report($period, $year);
		
		// Create PDF content
		$html = '<h1>Revenue Report - ' . $year . '</h1>';
		$html .= '<table border="1" cellpadding="5">';
		$html .= '<tr><th>Period</th><th>Orders</th><th>Revenue</th><th>Avg Order Value</th></tr>';
		
		foreach ($data['report_data'] as $row) {
			$html .= '<tr>';
			$html .= '<td>' . $row['period'] . '</td>';
			$html .= '<td>' . $row['orders'] . '</td>';
			$html .= '<td>' . get_option('currency_symbol', '$') . number_format($row['revenue'], 2) . '</td>';
			$html .= '<td>' . get_option('currency_symbol', '$') . number_format($row['avg_order_value'], 2) . '</td>';
			$html .= '</tr>';
		}
		
		$html .= '</table>';
		
		$this->pdf->loadHtml($html);
		$this->pdf->render();
		$this->pdf->stream("revenue_report_$year.pdf");
	}
}
