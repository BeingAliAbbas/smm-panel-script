<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class transactions_model extends MY_Model {
	public $tb_users;
	public $tb_categories;
	public $tb_services;
	public $tb_transaction_logs;

	public function __construct(){
		$this->tb_users 		     = USERS;
		$this->tb_categories 		 = CATEGORIES;
		$this->tb_services   		 = SERVICES;
		$this->tb_transaction_logs   = TRANSACTION_LOGS;
		parent::__construct();
	}

	function get_transaction_list($total_rows = false, $status = "", $limit = "", $start = "", $filters = []){
		$data  = array();
		if (get_role("user")) {
			$this->db->where("tl.uid", session('uid'));
			$this->db->where("tl.status", 1);
		}
		
		// Apply advanced filters for admin
		if (get_role("admin") && !empty($filters)) {
			// Filter by user email
			if (!empty($filters['user_email'])) {
				$this->db->like('u.email', $filters['user_email']);
			}
			// Filter by date range
			if (!empty($filters['date_from'])) {
				$this->db->where('tl.created >=', $filters['date_from'] . ' 00:00:00');
			}
			if (!empty($filters['date_to'])) {
				$this->db->where('tl.created <=', $filters['date_to'] . ' 23:59:59');
			}
			// Filter by payment method (validate to prevent SQL injection)
			if (!empty($filters['payment_method'])) {
				$this->db->where('tl.type', $filters['payment_method']);
			}
			// Filter by status (validate to ensure it's a valid status code)
			if (isset($filters['status']) && $filters['status'] !== '') {
				$status = (int)$filters['status'];
				if (in_array($status, [-1, 0, 1])) {
					$this->db->where('tl.status', $status);
				}
			}
			// Filter by amount range
			if (!empty($filters['amount_min'])) {
				$this->db->where('tl.amount >=', (float)$filters['amount_min']);
			}
			if (!empty($filters['amount_max'])) {
				$this->db->where('tl.amount <=', (float)$filters['amount_max']);
			}
		}
		
		if ($limit != "" && $start >= 0) {
			$this->db->limit($limit, $start);
		}
		$this->db->select("tl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_transaction_logs." tl");
		$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');
		
		// Apply sorting if specified
		if (!empty($filters['sort_by']) && !empty($filters['sort_order'])) {
			$allowed_sort = ['created', 'amount', 'status', 'type', 'uid'];
			$sort_by = in_array($filters['sort_by'], $allowed_sort) ? $filters['sort_by'] : 'id';
			$sort_order = strtoupper($filters['sort_order']) === 'ASC' ? 'ASC' : 'DESC';
			$this->db->order_by("tl.{$sort_by}", $sort_order);
		} else {
			$this->db->order_by("tl.id", 'DESC');
		}
		
		$query = $this->db->get();
		if ($total_rows) {
			$result = $query->num_rows();
			return $result;
		}else{
			$result = $query->result();
			return $result;
		}
		return false;
	}

	function get_transactions_by_search($k){
		$k = trim(htmlspecialchars($k));
		if (get_role("user")) {
			$this->db->select("tl.*, u.email");
			$this->db->from($this->tb_transaction_logs." tl");
			$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`tl`.`transaction_id` LIKE '%".$k."%' ESCAPE '!' OR `tl`.`type` LIKE '%".$k."%' ESCAPE '!')");
			}
			$this->db->where("u.id", session("uid"));
			$this->db->where("tl.status", 1);
			$this->db->order_by("tl.id", 'DESC');
			$query = $this->db->get();
			$result = $query->result();
		}else{
			$this->db->select("tl.*, u.email");
			$this->db->from($this->tb_transaction_logs." tl");
			$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');

			if ($k != "" && strlen($k) >= 2) {
				$this->db->where("(`tl`.`transaction_id` LIKE '%".$k."%' ESCAPE '!' OR `tl`.`type` LIKE '%".$k."%' ESCAPE '!' OR `u`.`email` LIKE '%".$k."%' ESCAPE '!')");
			}
			$this->db->order_by("tl.id", 'DESC');
			$query = $this->db->get();
			$result = $query->result();
		}

		return $result;
	}

	function delete_unpaid_payment($day = ""){
		if ($day == "") {
			$day = 7;
		}
		$SQL   = "DELETE FROM ".$this->tb_transaction_logs." WHERE `status` != 1 AND created < NOW() - INTERVAL ".$day." DAY";
		$query = $this->db->query($SQL);
		return $query;
	}

	// Get Count of orders by Search query
	public function get_count_items_by_search($search = []){
		$k = trim($search['k']);
		$where_like = "";
		switch ($search['type']) {
			case 1:
				#User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				# Transaction ID
				$where_like = "`tl`.`transaction_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("tl.*, u.email");
		$this->db->from($this->tb_transaction_logs." tl");
		$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');

		if ($where_like) $this->db->where($where_like);
		$this->db->order_by("tl.id", 'DESC');
		$query = $this->db->get();
		$number_row = $query->num_rows();
		return $number_row;
	}

	// Search Logs by keywork and search type
	public function search_items_by_get_method($search, $limit = "", $start = ""){
		$k = trim($search['k']);
		$where_like = "";
		switch ($search['type']) {
			case 1:
				#User Email
				$where_like = "`u`.`email` LIKE '%".$k."%' ESCAPE '!'";
				break;
			case 2:
				# Transaction ID
				$where_like = "`tl`.`transaction_id` LIKE '%".$k."%' ESCAPE '!'";
				break;
		}

		$this->db->select("tl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_transaction_logs." tl");
		$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');

		if ($where_like) $this->db->where($where_like);
		
		$this->db->order_by("tl.id", 'DESC');
		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	/**
	 * Get transaction summary statistics
	 * @param array $filters - optional filters to apply
	 * @return object
	 */
	public function get_transaction_stats($filters = []){
		// Apply filters if any
		if (!empty($filters['user_email'])) {
			$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');
			$this->db->like('u.email', $filters['user_email']);
		}
		if (!empty($filters['date_from'])) {
			$this->db->where('tl.created >=', $filters['date_from'] . ' 00:00:00');
		}
		if (!empty($filters['date_to'])) {
			$this->db->where('tl.created <=', $filters['date_to'] . ' 23:59:59');
		}
		if (!empty($filters['payment_method'])) {
			$this->db->where('tl.type', $filters['payment_method']);
		}
		
		$this->db->select('
			COUNT(*) as total_transactions,
			SUM(CASE WHEN tl.status = 0 THEN 1 ELSE 0 END) as pending_count,
			SUM(CASE WHEN tl.status = 1 THEN 1 ELSE 0 END) as completed_count,
			SUM(CASE WHEN tl.status = -1 THEN 1 ELSE 0 END) as failed_count,
			SUM(CASE WHEN tl.status = 1 THEN tl.amount - tl.txn_fee ELSE 0 END) as total_earnings,
			SUM(CASE WHEN tl.status = 0 THEN tl.amount ELSE 0 END) as pending_amount,
			SUM(CASE WHEN tl.status = 1 THEN tl.amount ELSE 0 END) as completed_amount
		');
		$this->db->from($this->tb_transaction_logs." tl");
		$query = $this->db->get();
		return $query->row();
	}

	/**
	 * Get transactions for export (no pagination)
	 * @param array $filters
	 * @return array
	 */
	public function get_transactions_for_export($filters = []){
		// Apply filters
		if (!empty($filters['user_email'])) {
			$this->db->like('u.email', $filters['user_email']);
		}
		if (!empty($filters['date_from'])) {
			$this->db->where('tl.created >=', $filters['date_from'] . ' 00:00:00');
		}
		if (!empty($filters['date_to'])) {
			$this->db->where('tl.created <=', $filters['date_to'] . ' 23:59:59');
		}
		if (!empty($filters['payment_method'])) {
			$this->db->where('tl.type', $filters['payment_method']);
		}
		if (isset($filters['status']) && $filters['status'] !== '') {
			$this->db->where('tl.status', $filters['status']);
		}
		if (!empty($filters['amount_min'])) {
			$this->db->where('tl.amount >=', $filters['amount_min']);
		}
		if (!empty($filters['amount_max'])) {
			$this->db->where('tl.amount <=', $filters['amount_max']);
		}

		$this->db->select("tl.*, u.email, u.first_name, u.last_name");
		$this->db->from($this->tb_transaction_logs." tl");
		$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');
		$this->db->order_by("tl.id", 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get transaction by IDs for detail view
	 * @param string $ids
	 * @return object|null
	 */
	public function get_transaction_details($ids){
		$this->db->select("tl.*, u.email, u.first_name, u.last_name, u.whatsapp_number");
		$this->db->from($this->tb_transaction_logs." tl");
		$this->db->join($this->tb_users." u", "u.id = tl.uid", 'left');
		$this->db->where('tl.ids', $ids);
		$query = $this->db->get();
		return $query->row();
	}
}