<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Get Quick Stats for Dashboard
 */
if (!function_exists('get_quick_stats')) {
	function get_quick_stats() {
		$CI =& get_instance();
		$CI->load->database();
		
		$stats = [];
		
		// Today's stats
		$today = date('Y-m-d');
		
		// Orders Today
		$stats['orders_today'] = $CI->db->from(ORDER)
			->where('DATE(created)', $today)
			->count_all_results();
		
		// Revenue Today
		$stats['revenue_today'] = $CI->db->select_sum('charge')
			->from(ORDER)
			->where('DATE(created)', $today)
			->where('status !=', 'canceled')
			->get()->row()->charge ?? 0;
		
		// New Users Today
		$stats['new_users_today'] = $CI->db->from(USERS)
			->where('role', 'user')
			->where('DATE(created)', $today)
			->count_all_results();
		
		// Pending Orders
		$stats['pending_orders'] = $CI->db->from(ORDER)
			->where('status', 'pending')
			->count_all_results();
		
		// Failed Orders (Error status)
		$stats['failed_orders'] = $CI->db->from(ORDER)
			->where('status', 'error')
			->count_all_results();
		
		// Pending Payments
		$stats['pending_payments'] = $CI->db->from(TRANSACTION_LOGS)
			->where('status', 0)
			->count_all_results();
		
		// This Week Stats
		$week_start = date('Y-m-d', strtotime('monday this week'));
		$week_end = date('Y-m-d', strtotime('sunday this week'));
		
		$stats['orders_this_week'] = $CI->db->from(ORDER)
			->where('DATE(created) >=', $week_start)
			->where('DATE(created) <=', $week_end)
			->count_all_results();
		
		$stats['revenue_this_week'] = $CI->db->select_sum('charge')
			->from(ORDER)
			->where('DATE(created) >=', $week_start)
			->where('DATE(created) <=', $week_end)
			->where('status !=', 'canceled')
			->get()->row()->charge ?? 0;
		
		return $stats;
	}
}

/**
 * Get System Health Status
 */
if (!function_exists('get_system_health')) {
	function get_system_health() {
		$CI =& get_instance();
		$CI->load->database();
		
		$health = [
			'status' => 'healthy',
			'issues' => []
		];
		
		// Check for high error rate
		$total_orders = $CI->db->from(ORDER)
			->where('created >=', date('Y-m-d', strtotime('-24 hours')))
			->count_all_results();
		
		$error_orders = $CI->db->from(ORDER)
			->where('status', 'error')
			->where('created >=', date('Y-m-d', strtotime('-24 hours')))
			->count_all_results();
		
		if ($total_orders > 0) {
			$error_rate = ($error_orders / $total_orders) * 100;
			if ($error_rate > 20) {
				$health['status'] = 'warning';
				$health['issues'][] = "High error rate: " . round($error_rate, 2) . "%";
			}
		}
		
		// Check for pending payments older than 7 days
		$old_pending = $CI->db->from(TRANSACTION_LOGS)
			->where('status', 0)
			->where('created <', date('Y-m-d', strtotime('-7 days')))
			->count_all_results();
		
		if ($old_pending > 0) {
			$health['status'] = 'warning';
			$health['issues'][] = "$old_pending pending payments older than 7 days";
		}
		
		// Check database size (if applicable)
		// Add more health checks as needed
		
		return $health;
	}
}

/**
 * Format currency with symbol
 */
if (!function_exists('format_currency')) {
	function format_currency($amount, $decimals = 2) {
		$symbol = get_option('currency_symbol', '$');
		return $symbol . number_format($amount, $decimals);
	}
}

/**
 * Get percentage change
 */
if (!function_exists('get_percentage_change')) {
	function get_percentage_change($current, $previous) {
		if ($previous == 0) {
			return $current > 0 ? 100 : 0;
		}
		return (($current - $previous) / $previous) * 100;
	}
}

/**
 * Get trend icon
 */
if (!function_exists('get_trend_icon')) {
	function get_trend_icon($percentage) {
		if ($percentage > 0) {
			return '<i class="fe fe-trending-up text-success"></i>';
		} elseif ($percentage < 0) {
			return '<i class="fe fe-trending-down text-danger"></i>';
		}
		return '<i class="fe fe-minus text-muted"></i>';
	}
}
