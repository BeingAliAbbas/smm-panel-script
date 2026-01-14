<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends MY_Model {

	public function __construct(){
		parent::__construct();
	}

	// Revenue Report
	public function get_revenue_report($period = 'monthly', $year = null){
		$data = [];
		$year = $year ?: date('Y');
		
		if ($period == 'monthly') {
			// Monthly report for the year
			for ($month = 1; $month <= 12; $month++) {
				$period_label = date('F', mktime(0, 0, 0, $month, 1));
				
				$revenue = $this->db->select_sum('charge')
					->from(ORDER)
					->where('YEAR(created)', $year)
					->where('MONTH(created)', $month)
					->where('status !=', 'canceled')
					->get()->row()->charge ?? 0;
				
				$orders = $this->db->from(ORDER)
					->where('YEAR(created)', $year)
					->where('MONTH(created)', $month)
					->where('status !=', 'canceled')
					->count_all_results();
				
				$avg_order = $orders > 0 ? $revenue / $orders : 0;
				
				$data['report_data'][] = [
					'period' => $period_label,
					'revenue' => round($revenue, 2),
					'orders' => $orders,
					'avg_order_value' => round($avg_order, 2)
				];
			}
		} elseif ($period == 'quarterly') {
			// Quarterly report
			$quarters = [
				'Q1' => [1, 2, 3],
				'Q2' => [4, 5, 6],
				'Q3' => [7, 8, 9],
				'Q4' => [10, 11, 12]
			];
			
			foreach ($quarters as $q => $months) {
				$revenue = 0;
				$orders = 0;
				
				foreach ($months as $month) {
					$rev = $this->db->select_sum('charge')
						->from(ORDER)
						->where('YEAR(created)', $year)
						->where('MONTH(created)', $month)
						->where('status !=', 'canceled')
						->get()->row()->charge ?? 0;
					
					$ord = $this->db->from(ORDER)
						->where('YEAR(created)', $year)
						->where('MONTH(created)', $month)
						->where('status !=', 'canceled')
						->count_all_results();
					
					$revenue += $rev;
					$orders += $ord;
				}
				
				$avg_order = $orders > 0 ? $revenue / $orders : 0;
				
				$data['report_data'][] = [
					'period' => $q . ' ' . $year,
					'revenue' => round($revenue, 2),
					'orders' => $orders,
					'avg_order_value' => round($avg_order, 2)
				];
			}
		}
		
		// Calculate totals
		$total_revenue = array_sum(array_column($data['report_data'], 'revenue'));
		$total_orders = array_sum(array_column($data['report_data'], 'orders'));
		
		$data['total_revenue'] = $total_revenue;
		$data['total_orders'] = $total_orders;
		$data['avg_order_value'] = $total_orders > 0 ? $total_revenue / $total_orders : 0;
		
		return $data;
	}

	// User Growth Report
	public function get_user_growth_report(){
		$data = [];
		
		// Last 12 months user growth
		for ($i = 11; $i >= 0; $i--) {
			$month = date('Y-m', strtotime("-$i months"));
			$month_label = date('M Y', strtotime("-$i months"));
			
			$new_users = $this->db->from(USERS)
				->where('role', 'user')
				->where('DATE_FORMAT(created, "%Y-%m")', $month)
				->count_all_results();
			
			$active_users = $this->db->select('COUNT(DISTINCT uid) as count')
				->from(ORDER)
				->where('DATE_FORMAT(created, "%Y-%m")', $month)
				->get()->row()->count ?? 0;
			
			$data['growth_data'][] = [
				'month' => $month_label,
				'new_users' => $new_users,
				'active_users' => $active_users
			];
		}
		
		// Total users
		$data['total_users'] = $this->db->from(USERS)->where('role', 'user')->count_all_results();
		
		// Active users (placed order in last 30 days)
		$data['active_users_30d'] = $this->db->select('COUNT(DISTINCT uid) as count')
			->from(ORDER)
			->where('created >=', date('Y-m-d', strtotime('-30 days')))
			->get()->row()->count ?? 0;
		
		return $data;
	}

	// Service Performance Report
	public function get_service_performance_report($date_from = null, $date_to = null){
		$data = [];
		
		$this->db->select('s.id, s.name, s.price, c.name as category_name, 
			COUNT(o.id) as order_count, 
			SUM(o.charge) as total_revenue,
			SUM(CASE WHEN o.status = "completed" THEN 1 ELSE 0 END) as completed_orders,
			SUM(CASE WHEN o.status = "error" THEN 1 ELSE 0 END) as failed_orders,
			AVG(o.charge) as avg_order_value')
			->from(SERVICES . ' s')
			->join(ORDER . ' o', 's.id = o.service_id', 'left')
			->join(CATEGORIES . ' c', 's.cate_id = c.id', 'left')
			->where('o.status !=', 'canceled');
		
		if ($date_from) {
			$this->db->where('DATE(o.created) >=', $date_from);
		}
		
		if ($date_to) {
			$this->db->where('DATE(o.created) <=', $date_to);
		}
		
		$data['performance_data'] = $this->db->group_by('s.id')
			->order_by('total_revenue', 'DESC')
			->limit(50)
			->get()->result();
		
		// Calculate success rate for each service
		foreach ($data['performance_data'] as &$service) {
			$total = $service->order_count;
			$completed = $service->completed_orders;
			$service->success_rate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
		}
		
		return $data;
	}
}
