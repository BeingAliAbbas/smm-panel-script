<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifications Model
 */
class notifications_model extends MY_Model {

	public $tb_users;
	public $tb_notifications;

	public function __construct(){
		parent::__construct();
		$this->tb_users         = USERS;
		$this->tb_notifications = USER_NOTIFICATIONS;
	}

	/**
	 * Get unread notifications for a user
	 */
	public function get_unread_notifications($user_id, $type = 'credit') {
		$this->db->select('*');
		$this->db->from($this->tb_notifications);
		$this->db->where('user_id', (int)$user_id);
		$this->db->where('type', $type);
		$this->db->where('is_seen', 0);
		$this->db->order_by('created_at', 'ASC');
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		
		return array();
	}

	/**
	 * Mark notification as seen
	 */
	public function mark_as_seen($notification_id, $user_id) {
		// First verify that this notification belongs to this user
		$this->db->select('id');
		$this->db->from($this->tb_notifications);
		$this->db->where('id', (int)$notification_id);
		$this->db->where('user_id', (int)$user_id);
		$query = $this->db->get();
		
		if ($query->num_rows() == 0) {
			return false;
		}
		
		// Update the notification
		$data = array(
			'is_seen' => 1,
			'seen_at' => NOW,
		);
		
		$this->db->where('id', (int)$notification_id);
		$this->db->where('user_id', (int)$user_id);
		return $this->db->update($this->tb_notifications, $data);
	}

	/**
	 * Create a credit notification
	 */
	public function create_credit_notification($user_id, $amount, $payment_method = '', $transaction_id = '') {
		// Format amount
		$formatted_amount = number_format($amount, 2);
		
		// Build title and message
		$title = "Balance Credited Successfully!";
		$message = "Your account has been credited with {$formatted_amount}";
		
		if ($payment_method) {
			$message .= " via {$payment_method}";
		}
		
		if ($transaction_id && $transaction_id !== 'empty') {
			$message .= " (Transaction ID: {$transaction_id})";
		}
		
		$message .= ". Your balance has been updated and is now available for use.";
		
		// Prepare data
		$data = array(
			'ids'        => ids(),
			'user_id'    => (int)$user_id,
			'type'       => 'credit',
			'title'      => $title,
			'message'    => $message,
			'amount'     => round((float)$amount, 4),
			'is_seen'    => 0,
			'created_at' => NOW,
			'seen_at'    => NULL,
		);
		
		return $this->db->insert($this->tb_notifications, $data);
	}
}
