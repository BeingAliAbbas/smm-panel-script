<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Transactional Email Library
 * 
 * Handles all transactional email notifications for orders, payments, and system events
 * Independent of the email_marketing module
 */
class Transactional_email {
    
    protected $CI;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('Phpmailer_lib');
        $this->CI->load->helper('email');
    }
    
    /**
     * Check if a specific email notification type is enabled
     */
    public function is_enabled($notification_type) {
        $option_name = 'enable_email_' . $notification_type;
        return get_option($option_name, '0') == '1';
    }
    
    /**
     * Get admin notification email address
     */
    public function get_admin_email() {
        $admin_email = get_option('admin_notification_email', '');
        if (empty($admin_email)) {
            // Fallback to first admin user email
            $admin = $this->CI->db->select('email')
                                  ->where('role', 'admin')
                                  ->order_by('id', 'ASC')
                                  ->limit(1)
                                  ->get(USERS)
                                  ->row();
            if ($admin) {
                $admin_email = $admin->email;
            }
        }
        return $admin_email;
    }
    
    /**
     * Send email to admin about new order
     */
    public function send_new_order_email($order_id, $user_email, $service_name, $total_charge, $quantity) {
        if (!$this->is_enabled('new_order')) {
            return true; // Not enabled, skip silently
        }
        
        $admin_email = $this->get_admin_email();
        if (empty($admin_email)) {
            $this->log_error('new_order', 'Admin email not configured');
            return true; // Don't block the process
        }
        
        $subject = get_option('website_name', 'SMM Panel') . ' - New Order Placed (#' . $order_id . ')';
        
        $currency_symbol = get_option('currency_symbol', '$');
        $content = "
            <h2>New Order Notification</h2>
            <p>A new order has been placed on your platform.</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Order ID:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$order_id}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>User Email:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$user_email}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Service:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$service_name}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Quantity:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$quantity}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Total Charge:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$currency_symbol}{$total_charge}</td>
                </tr>
            </table>
            <p><a href='" . cn('order') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px;'>View Orders</a></p>
        ";
        
        return $this->send_email_template($admin_email, 'Admin', $subject, $content);
    }
    
    /**
     * Send email to admin about order error
     */
    public function send_order_error_email($order_id, $user_email, $service_name, $error_message) {
        if (!$this->is_enabled('order_error')) {
            return true;
        }
        
        $admin_email = $this->get_admin_email();
        if (empty($admin_email)) {
            $this->log_error('order_error', 'Admin email not configured');
            return true;
        }
        
        $subject = get_option('website_name', 'SMM Panel') . ' - Order Error (#' . $order_id . ')';
        
        $content = "
            <h2>Order Error Notification</h2>
            <p>An order has encountered an error.</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Order ID:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$order_id}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>User Email:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$user_email}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Service:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$service_name}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd; color: #dc3545;'><strong>Error:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd; color: #dc3545;'>" . htmlspecialchars($error_message) . "</td>
                </tr>
            </table>
            <p><a href='" . cn('order') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 4px;'>View Orders</a></p>
        ";
        
        return $this->send_email_template($admin_email, 'Admin', $subject, $content);
    }
    
    /**
     * Send email to admin when payment is submitted
     */
    public function send_payment_submitted_email($transaction_id, $user_email, $amount, $payment_method) {
        if (!$this->is_enabled('payment_submitted')) {
            return true;
        }
        
        $admin_email = $this->get_admin_email();
        if (empty($admin_email)) {
            $this->log_error('payment_submitted', 'Admin email not configured');
            return true;
        }
        
        $subject = get_option('website_name', 'SMM Panel') . ' - New Payment Submission';
        
        $currency_symbol = get_option('currency_symbol', '$');
        $content = "
            <h2>Payment Submitted</h2>
            <p>A user has submitted a payment request.</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Transaction ID:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$transaction_id}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>User Email:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$user_email}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Amount:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$currency_symbol}{$amount}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Payment Method:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$payment_method}</td>
                </tr>
            </table>
            <p><a href='" . cn('transactions') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #28a745; color: #fff; text-decoration: none; border-radius: 4px;'>View Transactions</a></p>
        ";
        
        return $this->send_email_template($admin_email, 'Admin', $subject, $content);
    }
    
    /**
     * Send email to user when payment is approved
     */
    public function send_payment_approved_email($user_id, $transaction_id, $amount, $payment_method) {
        if (!$this->is_enabled('payment_approved')) {
            return true;
        }
        
        $user = $this->CI->db->select('email, first_name')->where('id', $user_id)->get(USERS)->row();
        if (!$user) {
            $this->log_error('payment_approved', 'User not found: ' . $user_id);
            return true;
        }
        
        $subject = get_option('website_name', 'SMM Panel') . ' - Payment Approved';
        
        $currency_symbol = get_option('currency_symbol', '$');
        $content = "
            <h2>Payment Approved!</h2>
            <p>Dear {$user->first_name},</p>
            <p>Your payment has been approved and your balance has been credited.</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Transaction ID:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$transaction_id}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Amount Credited:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$currency_symbol}{$amount}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Payment Method:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$payment_method}</td>
                </tr>
            </table>
            <p>You can now use your balance to place orders on our platform.</p>
            <p><a href='" . cn('add_funds') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px;'>View Balance</a></p>
        ";
        
        return $this->send_email_template($user->email, $user->first_name, $subject, $content);
    }
    
    /**
     * Send email to admin about low provider balance
     */
    public function send_low_balance_alert($provider_name, $current_balance, $threshold, $provider_id) {
        if (!$this->is_enabled('low_provider_balance')) {
            return true;
        }
        
        // Check cooldown to prevent spam
        $last_sent_key = 'low_balance_last_sent_' . $provider_id;
        $last_sent = get_option($last_sent_key, '0');
        $cooldown_hours = 24; // Send maximum once per 24 hours
        
        if ((time() - intval($last_sent)) < ($cooldown_hours * 3600)) {
            return true; // Within cooldown period, skip
        }
        
        $admin_email = $this->get_admin_email();
        if (empty($admin_email)) {
            $this->log_error('low_provider_balance', 'Admin email not configured');
            return true;
        }
        
        $subject = get_option('website_name', 'SMM Panel') . ' - Low Provider Balance Alert';
        
        $content = "
            <h2 style='color: #dc3545;'>⚠️ Low Balance Alert</h2>
            <p>A provider balance has fallen below the configured threshold.</p>
            <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Provider:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$provider_name}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Current Balance:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd; color: #dc3545;'>{$current_balance}</td>
                </tr>
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'><strong>Threshold:</strong></td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>{$threshold}</td>
                </tr>
            </table>
            <p><strong>Action Required:</strong> Please add funds to this provider to avoid service interruption.</p>
            <p><a href='" . cn('api_provider') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #dc3545; color: #fff; text-decoration: none; border-radius: 4px;'>Manage Providers</a></p>
        ";
        
        $result = $this->send_email_template($admin_email, 'Admin', $subject, $content);
        
        // Update last sent timestamp if successful
        if ($result) {
            update_option($last_sent_key, time());
        }
        
        return $result;
    }
    
    /**
     * Core method to send email using PHPMailer
     * Public so it can be used for test emails
     */
    public function send_email_template($to_email, $to_name, $subject, $content) {
        try {
            // Parse merge fields and wrap in modern template
            $html_content = parse_merge_fields($content, [], true);
            
            $mail = new PHPMailer(true);
            $mail->CharSet = "utf-8";
            $mail->WordWrap = 78;
            $mail->Encoding = 'quoted-printable';
            
            // SMTP Configuration
            $email_protocol_type = get_option("email_protocol_type", "");
            $smtp_server = get_option("smtp_server", "");
            $smtp_port = get_option("smtp_port", "");
            $smtp_username = get_option("smtp_username", "");
            $smtp_password = get_option("smtp_password", "");
            $smtp_encryption = get_option("smtp_encryption", "");
            
            if ($email_protocol_type == "smtp" && $smtp_server != "" && $smtp_port != "" && $smtp_username != "" && $smtp_password != "") {
                $mail->isSMTP();
                $mail->SMTPDebug = 0;
                $mail->Host = $smtp_server;
                $mail->SMTPAuth = true; // Enable authentication
                $mail->Username = $smtp_username;
                $mail->Password = $smtp_password;
                $mail->SMTPSecure = $smtp_encryption;
                $mail->Port = $smtp_port;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
            } else {
                $mail->isSendmail();
            }
            
            // Email settings
            $email_from = get_option('email_from', '') ? get_option('email_from', '') : "no-reply@smmpanel.com";
            $email_name = get_option('email_name', '') ? get_option('email_name', '') : get_option('website_name', 'SMM Panel');
            
            $mail->setFrom($email_from, $email_name);
            $mail->addAddress($to_email, $to_name);
            $mail->addReplyTo($email_from, $email_name);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->MsgHTML($html_content);
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            $this->log_error('email_send', 'Failed to send email: ' . $mail->ErrorInfo);
            return false; // Return false to indicate failure (but don't throw exception)
        }
    }
    
    /**
     * Log email errors silently for debugging
     */
    private function log_error($type, $message) {
        $log_message = '[' . date('Y-m-d H:i:s') . '] Transactional Email Error [' . $type . ']: ' . $message;
        error_log($log_message);
        
        // Also try to log to CI log file if available
        if (function_exists('log_message')) {
            log_message('error', $log_message);
        }
    }
}
