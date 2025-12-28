<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notification Helper
 * 
 * Helper functions for managing user notifications
 */

if (!function_exists('create_credit_notification')) {
    /**
     * Create a credit notification for a user
     * 
     * @param int $user_id - User ID
     * @param float $amount - Amount credited
     * @param string $payment_method - Payment method used (optional)
     * @param string $transaction_id - Transaction ID (optional)
     * @return bool - True on success, false on failure
     */
    function create_credit_notification($user_id, $amount, $payment_method = '', $transaction_id = '') {
        $CI =& get_instance();
        $CI->load->model('notifications/notifications_model', 'notifications_model');
        
        return $CI->notifications_model->create_credit_notification($user_id, $amount, $payment_method, $transaction_id);
    }
}

if (!function_exists('get_unread_credit_notifications')) {
    /**
     * Get unread credit notifications for a user
     * 
     * @param int $user_id - User ID
     * @return array - Array of unread notifications
     */
    function get_unread_credit_notifications($user_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        $CI->db->select('*');
        $CI->db->from(USER_NOTIFICATIONS);
        $CI->db->where('user_id', (int)$user_id);
        $CI->db->where('type', 'credit');
        $CI->db->where('is_seen', 0);
        $CI->db->order_by('created_at', 'ASC');
        
        $query = $CI->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result();
        }
        
        return array();
    }
}

if (!function_exists('mark_notification_as_seen')) {
    /**
     * Mark a notification as seen
     * 
     * @param int $notification_id - Notification ID
     * @param int $user_id - User ID (for security check)
     * @return bool - True on success, false on failure
     */
    function mark_notification_as_seen($notification_id, $user_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        // First, verify that this notification belongs to this user
        $CI->db->select('id');
        $CI->db->from(USER_NOTIFICATIONS);
        $CI->db->where('id', (int)$notification_id);
        $CI->db->where('user_id', (int)$user_id);
        $query = $CI->db->get();
        
        if ($query->num_rows() == 0) {
            log_message('error', 'Attempted to mark notification ' . $notification_id . ' as seen by unauthorized user ' . $user_id);
            return false;
        }
        
        // Update the notification
        $data = array(
            'is_seen' => 1,
            'seen_at' => NOW,
        );
        
        $CI->db->where('id', (int)$notification_id);
        $CI->db->where('user_id', (int)$user_id);
        $result = $CI->db->update(USER_NOTIFICATIONS, $data);
        
        if ($result) {
            log_message('info', 'Notification ' . $notification_id . ' marked as seen by user ' . $user_id);
            return true;
        }
        
        log_message('error', 'Failed to mark notification ' . $notification_id . ' as seen by user ' . $user_id);
        return false;
    }
}

if (!function_exists('has_unread_credit_notifications')) {
    /**
     * Check if user has unread credit notifications
     * 
     * @param int $user_id - User ID
     * @return bool - True if has unread notifications, false otherwise
     */
    function has_unread_credit_notifications($user_id) {
        $CI =& get_instance();
        $CI->load->database();
        
        $CI->db->select('COUNT(*) as count');
        $CI->db->from(USER_NOTIFICATIONS);
        $CI->db->where('user_id', (int)$user_id);
        $CI->db->where('type', 'credit');
        $CI->db->where('is_seen', 0);
        
        $query = $CI->db->get();
        $result = $query->row();
        
        return ($result && $result->count > 0);
    }
}
