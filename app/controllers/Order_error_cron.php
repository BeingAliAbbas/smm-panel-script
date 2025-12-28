<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Order Error Email Cron
 * 
 * This cron job checks for orders with errors and sends email alerts
 * to admin about failed orders
 * 
 * Route: /cron/check_order_errors
 */
class Order_error_cron extends CI_Controller {
    
    public $tb_orders;
    public $tb_users;
    public $tb_services;
    public $cron_logger;
    
    public function __construct() {
        parent::__construct();
        
        // Load database library
        $this->load->database();
        
        // Load cron logger
        $this->load->library('cron_logger');
        
        $this->tb_orders = ORDER;  // Use ORDER constant (singular)
        $this->tb_users = USERS;
        $this->tb_services = SERVICES;
    }
    
    /**
     * Default index method - redirects to check_errors
     */
    public function index() {
        $this->check_errors();
    }
    
    /**
     * Main cron entry point - checks for error orders and sends emails
     * Route: /cron/check_order_errors
     */
    public function check_errors() {
        // Start logging
        $log_id = $this->cron_logger->start('cron/check_order_errors');
        
        try {
            echo "Starting order error check...<br>";
            
            // Check if order error alerts are enabled
            if (get_option('enable_email_order_error', '0') != '1') {
                echo "Order error email alerts are disabled.<br>Successfully";
                if ($log_id) {
                    $this->cron_logger->end($log_id, 'Success', 200, 'Order error alerts disabled');
                }
                return;
            }
            
            // Get last checked timestamp
            $last_checked = get_option('order_error_email_last_checked', '');
            $current_time = date('Y-m-d H:i:s');
            
            // Get list of already notified order IDs (within last 24 hours)
            $notified_ids_json = get_option('order_error_email_notified_ids', '[]');
            $notified_ids = json_decode($notified_ids_json, true) ?: [];
            
            // Clean up old entries (older than 24 hours)
            $time_24h_ago = strtotime('-24 hours');
            $notified_ids = array_filter($notified_ids, function($timestamp) use ($time_24h_ago) {
                return $timestamp > $time_24h_ago;
            });
            
            // Get error orders that haven't been notified
            $this->db->select('orders.*, users.email as user_email, services.name as service_name')
                     ->from($this->tb_orders . ' as orders')
                     ->join($this->tb_users . ' as users', 'users.id = orders.uid', 'left')
                     ->join($this->tb_services . ' as services', 'services.id = orders.service_id', 'left')
                     ->where('orders.status', 'error')
                     ->order_by('orders.id', 'DESC')
                     ->limit(50); // Process max 50 error orders per run
            
            // Exclude already notified IDs
            if (!empty($notified_ids)) {
                $this->db->where_not_in('orders.id', array_keys($notified_ids));
            }
            
            $error_orders = $this->db->get()->result();
            
            if (empty($error_orders)) {
                echo "No new error orders to notify.<br>Successfully";
                if ($log_id) {
                    $this->cron_logger->end($log_id, 'Success', 200, 'No new error orders found');
                }
                // Update last checked time
                update_option('order_error_email_last_checked', $current_time);
                return;
            }
            
            $checked_count = 0;
            $notified_count = 0;
            
            // Load transactional email library
            $this->load->library('Transactional_email');
            
            foreach ($error_orders as $order) {
                $checked_count++;
                
                // Get error message from order note
                $error_message = !empty($order->note) ? $order->note : 'Unknown error occurred';
                
                // Send error notification email
                $result = $this->transactional_email->send_order_error_email(
                    $order->id,
                    $order->user_email ?? '',
                    $order->service_name ?? 'Unknown Service',
                    $error_message
                );
                
                if ($result) {
                    echo "Error notification sent for Order ID: {$order->id}<br>";
                    $notified_count++;
                    
                    // Mark this order as notified
                    $notified_ids[$order->id] = time();
                }
            }
            
            // Save updated notified IDs list
            update_option('order_error_email_notified_ids', json_encode($notified_ids));
            update_option('order_error_email_last_checked', $current_time);
            
            echo "Completed! Checked {$checked_count} error orders, sent {$notified_count} notifications.<br>";
            
            if ($log_id) {
                $message = "Checked {$checked_count} error orders, sent {$notified_count} email notifications";
                $this->cron_logger->end($log_id, 'Success', 200, $message);
            }
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
            if ($log_id) {
                $this->cron_logger->end($log_id, 'Failed', 500, 'Exception: ' . $e->getMessage());
            }
        }
    }
}