<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics_model extends MY_Model {

	public function __construct(){
		parent::__construct();
	}

	// Get Dashboard Analytics Data
	public function get_dashboard_analytics(){
		$data = [];

		// Total Revenue
		$data['total_revenue'] = $this->db->select_sum('charge')
			->from(ORDER)
			->where('status !=', 'canceled')
			->get()->row()->charge ?? 0;

		// Total Orders
		$data['total_orders'] = $this->db->from(ORDER)->count_all_results();

		// Total Users
		$data['total_users'] = $this->db->from(USERS)->where('role', 'user')->count_all_results();

		// Pending Orders
		$data['pending_orders'] = $this->db->from(ORDER)->where('status', 'pending')->count_all_results();

		// Completed Orders
		$data['completed_orders'] = $this->db->from(ORDER)->where('status', 'completed')->count_all_results();

		// Revenue This Month
		$data['revenue_this_month'] = $this->db->select_sum('charge')
			->from(ORDER)
			->where('status !=', 'canceled')
			->where('MONTH(created)', date('m'))
			->where('YEAR(created)', date('Y'))
			->get()->row()->charge ?? 0;

		// Orders This Month
		$data['orders_this_month'] = $this->db->from(ORDER)
			->where('MONTH(created)', date('m'))
			->where('YEAR(created)', date('Y'))
			->count_all_results();

		// New Users This Month
		$data['new_users_this_month'] = $this->db->from(USERS)
			->where('role', 'user')
			->where('MONTH(created)', date('m'))
			->where('YEAR(created)', date('Y'))
			->count_all_results();

		// Top 5 Users by Spending
		$data['top_users'] = $this->db->select('u.id, u.first_name, u.last_name, u.email, SUM(o.charge) as total_spent, COUNT(o.id) as order_count')
			->from(USERS . ' u')
			->join(ORDER . ' o', 'u.id = o.uid', 'left')
			->where('u.role', 'user')
			->where('o.status !=', 'canceled')
			->group_by('u.id')
			->order_by('total_spent', 'DESC')
			->limit(5)
			->get()->result();

		// Recent Orders
		$data['recent_orders'] = $this->db->select('o.*, u.email as user_email, s.name as service_name')
			->from(ORDER . ' o')
			->join(USERS . ' u', 'u.id = o.uid', 'left')
			->join(SERVICES . ' s', 's.id = o.service_id', 'left')
			->order_by('o.created', 'DESC')
			->limit(10)
			->get()->result();

		return $data;
	}

	// Get Service Popularity Data
	public function get_service_popularity(){
		$data = [];

		// Most Popular Services (by order count)
		$data['popular_services'] = $this->db->select('s.id, s.name, s.price, COUNT(o.id) as order_count, SUM(o.charge) as total_revenue')
			->from(SERVICES . ' s')
			->join(ORDER . ' o', 's.id = o.service_id', 'left')
			->where('o.status !=', 'canceled')
			->group_by('s.id')
			->order_by('order_count', 'DESC')
			->limit(20)
			->get()->result();

		// Service Categories Performance
		$data['category_performance'] = $this->db->select('c.id, c.name, COUNT(o.id) as order_count, SUM(o.charge) as total_revenue')
			->from(CATEGORIES . ' c')
			->join(SERVICES . ' s', 'c.id = s.cate_id', 'left')
			->join(ORDER . ' o', 's.id = o.service_id', 'left')
			->where('o.status !=', 'canceled')
			->group_by('c.id')
			->order_by('total_revenue', 'DESC')
			->get()->result();

		return $data;
	}

	// Get Payment History with Filters
	public function get_payment_history($status = null, $date_from = null, $date_to = null, $payment_method = null){
		$data = [];

		$this->db->select('t.*, u.email as user_email, u.first_name, u.last_name')
			->from(TRANSACTION_LOGS . ' t')
			->join(USERS . ' u', 'u.id = t.uid', 'left');

		if ($status !== null && $status !== '') {
			$this->db->where('t.status', $status);
		}

		if ($date_from) {
			$this->db->where('DATE(t.created) >=', $date_from);
		}

		if ($date_to) {
			$this->db->where('DATE(t.created) <=', $date_to);
		}

		if ($payment_method) {
			$this->db->where('t.type', $payment_method);
		}

		$data['transactions'] = $this->db->order_by('t.created', 'DESC')
			->limit(100)
			->get()->result();

		// Get available payment methods
		$data['payment_methods'] = $this->db->select('DISTINCT type')
			->from(TRANSACTION_LOGS)
			->where('type IS NOT NULL')
			->get()->result();

		// Summary stats
		$this->db->select_sum('amount');
		if ($status !== null && $status !== '') {
			$this->db->where('status', $status);
		}
		if ($date_from) {
			$this->db->where('DATE(created) >=', $date_from);
		}
		if ($date_to) {
			$this->db->where('DATE(created) <=', $date_to);
		}
		if ($payment_method) {
			$this->db->where('type', $payment_method);
		}
		$data['total_amount'] = $this->db->from(TRANSACTION_LOGS)->get()->row()->amount ?? 0;

		return $data;
	}

	// Get Orders for Export
	public function get_orders_for_export($status = null, $date_from = null, $date_to = null){
		$this->db->select('o.*, u.email as user_email, s.name as service_name')
			->from(ORDER . ' o')
			->join(USERS . ' u', 'u.id = o.uid', 'left')
			->join(SERVICES . ' s', 's.id = o.service_id', 'left');

		if ($status) {
			$this->db->where('o.status', $status);
		}

		if ($date_from) {
			$this->db->where('DATE(o.created) >=', $date_from);
		}

		if ($date_to) {
			$this->db->where('DATE(o.created) <=', $date_to);
		}

		return $this->db->order_by('o.created', 'DESC')
			->limit(10000)
			->get()->result();
	}

	// Revenue Chart Data (Last 30 days)
	public function get_revenue_chart_data(){
		$data = [];
		for ($i = 29; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-$i days"));
			$revenue = $this->db->select_sum('charge')
				->from(ORDER)
				->where('DATE(created)', $date)
				->where('status !=', 'canceled')
				->get()->row()->charge ?? 0;
			
			$data[] = [
				'date' => $date,
				'revenue' => round($revenue, 2)
			];
		}
		return $data;
	}

	// Orders Chart Data (Last 30 days)
	public function get_orders_chart_data(){
		$data = [];
		for ($i = 29; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-$i days"));
			$count = $this->db->from(ORDER)
				->where('DATE(created)', $date)
				->count_all_results();
			
			$data[] = [
				'date' => $date,
				'count' => $count
			];
		}
		return $data;
	}

	// Users Chart Data (Last 30 days)
	public function get_users_chart_data(){
		$data = [];
		for ($i = 29; $i >= 0; $i--) {
			$date = date('Y-m-d', strtotime("-$i days"));
			$count = $this->db->from(USERS)
				->where('role', 'user')
				->where('DATE(created)', $date)
				->count_all_results();
			
			$data[] = [
				'date' => $date,
				'count' => $count
			];
		}
		return $data;
	}
}
