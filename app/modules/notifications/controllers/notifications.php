<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifications Controller
 * 
 * Handles user notification operations
 */
class notifications extends MX_Controller {

	public $tb_notifications;
	public $tb_users;

	public function __construct(){
		parent::__construct();
		$this->load->model('notifications_model', 'model');
		$this->tb_notifications = USER_NOTIFICATIONS;
		$this->tb_users         = USERS;
	}

	/**
	 * Get notification modal view (for inclusion in template)
	 */
	public function get_notification_modal() {
		return $this->load->view('notification_modal', [], true);
	}

	/**
	 * Get unread notifications for the current user (AJAX)
	 */
	public function ajax_get_unread() {
		$user_id = $this->check_authentication();

		// Get unread credit notifications
		$notifications = $this->model->get_unread_notifications($user_id, 'credit');

		if (empty($notifications)) {
			ms(['status' => 'success', 'has_notifications' => false, 'notifications' => []]);
		}

		// Return only the first unread notification
		$notification = $notifications[0];
		
		ms([
			'status'           => 'success',
			'has_notifications'=> true,
			'notification'     => [
				'id'         => $notification->id,
				'title'      => $notification->title,
				'message'    => $notification->message,
				'amount'     => number_format($notification->amount, 2),
				'created_at' => $notification->created_at,
			]
		]);
	}

	/**
	 * Mark notification as seen (AJAX)
	 */
	public function ajax_mark_seen() {
		$user_id = $this->check_authentication();

		$notification_id = (int)post('notification_id');
		
		if (!$notification_id) {
			ms(['status' => 'error', 'message' => 'Notification ID is required']);
		}

		// Mark as seen (with user verification)
		$result = $this->model->mark_as_seen($notification_id, $user_id);

		if ($result) {
			// Check if there are more unread notifications
			$remaining = $this->model->get_unread_notifications($user_id, 'credit');
			
			ms([
				'status'       => 'success',
				'message'      => 'Notification marked as seen',
				'has_more'     => !empty($remaining),
			]);
		} else {
			ms(['status' => 'error', 'message' => 'Failed to mark notification as seen']);
		}
	}

	/**
	 * Check if user is authenticated and return user ID
	 * 
	 * @return int User ID
	 */
	private function check_authentication() {
		$user_id = session('uid');
		if (!$user_id) {
			ms(['status' => 'error', 'message' => 'Not authenticated']);
		}
		return $user_id;
	}
}
