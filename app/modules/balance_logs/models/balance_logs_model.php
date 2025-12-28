<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class balance_logs_model extends MY_Model {
	public $tb_users;
	public $tb_balance_logs;

	public function __construct(){
		$this->tb_users        = USERS;
		$this->tb_balance_logs = BALANCE_LOGS;
		parent::__construct();
	}

	/**
	 * Get balance logs list with pagination
	 * @param bool $total_rows - if true, return count; if false, return data
	 * @param string $status - filter by status (not used currently)
	 * @param int $limit - number of records to fetch
	 * @param int $start - offset for pagination
	 * @param array $filters - advanced filters for admin
	 * @return mixed
	 */
	function get_balance_logs_list($total_rows = false, $status = "", $limit = "", $start = "", $filters = []){
		// For regular users, show only their logs
		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}
		
		// Apply advanced filters for admin
		if (get_role("admin") && !empty($filters)) {
			// Filter by user email
			if (!empty($filters['user_email'])) {
				$this->db->like('u.email', $filters['user_email']);
			}
			// Filter by action type (validate to prevent SQL injection)
			if (!empty($filters['action_type'])) {
				$allowed_actions = ['deduction', 'addition', 'refund', 'manual_add', 'manual_deduct'];
				if (in_array($filters['action_type'], $allowed_actions)) {
					$this->db->where('bl.action_type', $filters['action_type']);
				}
			}
			// Filter by related type (module/source)
			if (!empty($filters['related_type'])) {
				$this->db->like('bl.related_type', $filters['related_type']);
			}
			// Filter by date range
			if (!empty($filters['date_from'])) {
				$this->db->where('bl.created >=', $filters['date_from'] . ' 00:00:00');
			}
			if (!empty($filters['date_to'])) {
				$this->db->where('bl.created <=', $filters['date_to'] . ' 23:59:59');
			}
			// Filter by amount range
			if (!empty($filters['amount_min'])) {
				$this->db->where('bl.amount >=', (float)$filters['amount_min']);
			}
			if (!empty($filters['amount_max'])) {
				$this->db->where('bl.amount <=', (float)$filters['amount_max']);
			}
		}
		
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		
		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');
		
		// Apply sorting if specified
		if (!empty($filters['sort_by']) && !empty($filters['sort_order'])) {
			$allowed_sort = ['created', 'amount', 'action_type', 'uid'];
			$sort_by = in_array($filters['sort_by'], $allowed_sort) ? $filters['sort_by'] : 'id';
			$sort_order = strtoupper($filters['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
			$this->db->order_by("bl.{$sort_by}", $sort_order);
		} else {
			$this->db->order_by("bl.id", 'DESC');
		}
		
		$query = $this->db->get();
		
		if ($total_rows) {
			return $query->num_rows();
		} else {
			return $query->result();
		}
	}

	/**
	 * Search balance logs
	 * @param string $k - search keyword
	 * @return array
	 */
	function get_balance_logs_by_search($k){
		$k = trim(htmlspecialchars($k));
		
		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if ($k != "" && strlen($k) >= 2) {
			if (get_role("user")) {
				$this->db->where("(`bl`.`description` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`related_id` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`action_type` LIKE '%".$k."%' ESCAPE '!')");
				$this->db->where("u.id", session("uid"));
			} else {
				$this->db->where("(`bl`.`description` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`related_id` LIKE '%".$k."%' ESCAPE '!' OR `bl`.`action_type` LIKE '%".$k."%' ESCAPE '!' OR `u`.`email` LIKE '%".$k."%' ESCAPE '!')");
			}
		} else {
			if (get_role("user")) {
				$this->db->where("u.id", session("uid"));
			}
		}
		
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	/**
	 * Get count of items by search
	 * @param array $search
	 * @return int
	 */
	public function get_count_items_by_search($search = []){
		$k = trim($search['k']);
		$where_like = "";
		
		switch ($search['type']) {
			case 1:
				// User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				// Related ID (Order ID, Transaction ID, etc.)
				$where_like = "`bl`.`related_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 3:
				// Action Type
				$where_like = "`bl`.`action_type` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("bl.*, u.email");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}

		if ($where_like) $this->db->where($where_like);
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}

	/**
	 * Search logs by keyword and search type
	 * @param array $search
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function search_items_by_get_method($search, $limit = "", $start = ""){
		$k = trim($search['k']);
		$where_like = "";
		
		switch ($search['type']) {
			case 1:
				// User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				// Related ID
				$where_like = "`bl`.`related_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 3:
				// Action Type
				$where_like = "`bl`.`action_type` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');

		if (get_role("user")) {
			$this->db->where("bl.uid", session('uid'));
		}

		if ($where_like) $this->db->where($where_like);
		
		$this->db->order_by("bl.id", 'DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	/**
	 * Get balance logs summary statistics
	 * @param array $filters - optional filters to apply
	 * @return object
	 */
	public function get_balance_stats($filters = []){
		// Apply filters if any
		if (!empty($filters['user_email'])) {
			$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');
			$this->db->like('u.email', $filters['user_email']);
		}
		if (!empty($filters['action_type'])) {
			$this->db->where('bl.action_type', $filters['action_type']);
		}
		if (!empty($filters['related_type'])) {
			$this->db->like('bl.related_type', $filters['related_type']);
		}
		if (!empty($filters['date_from'])) {
			$this->db->where('bl.created >=', $filters['date_from'] . ' 00:00:00');
		}
		if (!empty($filters['date_to'])) {
			$this->db->where('bl.created <=', $filters['date_to'] . ' 23:59:59');
		}
		
		$this->db->select('
			COUNT(*) as total_logs,
			SUM(CASE WHEN bl.action_type IN ("addition", "manual_add", "refund") THEN bl.amount ELSE 0 END) as total_credited,
			SUM(CASE WHEN bl.action_type IN ("deduction", "manual_deduct") THEN bl.amount ELSE 0 END) as total_debited,
			COUNT(CASE WHEN bl.action_type IN ("addition", "manual_add", "refund") THEN 1 END) as credit_count,
			COUNT(CASE WHEN bl.action_type IN ("deduction", "manual_deduct") THEN 1 END) as debit_count
		');
		$this->db->from($this->tb_balance_logs." bl");
		$query = $this->db->get();
		$stats = $query->row();
		
		// Calculate net change
		if ($stats) {
			$stats->net_change = $stats->total_credited - $stats->total_debited;
		}
		
		return $stats;
	}

	/**
	 * Get balance logs for export (no pagination)
	 * @param array $filters
	 * @return array
	 */
	public function get_balance_logs_for_export($filters = []){
		// Apply filters
		if (!empty($filters['user_email'])) {
			$this->db->like('u.email', $filters['user_email']);
		}
		if (!empty($filters['action_type'])) {
			$this->db->where('bl.action_type', $filters['action_type']);
		}
		if (!empty($filters['related_type'])) {
			$this->db->like('bl.related_type', $filters['related_type']);
		}
		if (!empty($filters['date_from'])) {
			$this->db->where('bl.created >=', $filters['date_from'] . ' 00:00:00');
		}
		if (!empty($filters['date_to'])) {
			$this->db->where('bl.created <=', $filters['date_to'] . ' 23:59:59');
		}
		if (!empty($filters['amount_min'])) {
			$this->db->where('bl.amount >=', $filters['amount_min']);
		}
		if (!empty($filters['amount_max'])) {
			$this->db->where('bl.amount <=', $filters['amount_max']);
		}

		$this->db->select("bl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');
		$this->db->order_by("bl.id", 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get balance log details by IDs
	 * @param string $ids
	 * @return object|null
	 */
	public function get_balance_log_details($ids){
		$this->db->select("bl.*, u.email, u.first_name, u.last_name, u.whatsapp_number");
		$this->db->from($this->tb_balance_logs." bl");
		$this->db->join($this->tb_users." u", "u.id = bl.uid", 'left');
		$this->db->where('bl.ids', $ids);
		$query = $this->db->get();
		return $query->row();
	}
}
