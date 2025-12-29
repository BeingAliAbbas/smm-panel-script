<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Verification Check Hook
 * 
 * This hook ensures that Google sign-in users must verify their WhatsApp
 * before accessing any protected pages
 */
class Whatsapp_verification_check {

    protected $CI;
    protected $excluded_controllers = array(
        'auth',
        'whatsapp_verify',
        'api_access',
        'cron'
    );

    public function __construct() {
        $this->CI =& get_instance();
    }

    /**
     * Check if user needs WhatsApp verification
     */
    public function check() {
        // Get current controller
        $controller = $this->CI->router->fetch_class();
        
        // Skip check for excluded controllers
        if (in_array($controller, $this->excluded_controllers)) {
            return;
        }

        // Check if user is logged in
        $user_id = $this->CI->session->userdata('uid');
        if (!$user_id) {
            return; // Not logged in, let auth controller handle it
        }

        // Load database if not loaded
        if (!isset($this->CI->db)) {
            $this->CI->load->database();
        }

        // Get user data
        $user = $this->CI->db->select('whatsapp_verified, signup_type')
                             ->where('id', $user_id)
                             ->get('general_users')
                             ->row();

        // If user not found, logout
        if (!$user) {
            $this->CI->session->unset_userdata('uid');
            redirect(cn('auth/login'));
            return;
        }

        // Check if WhatsApp verification is required (only for Google users)
        if ($user->signup_type === 'google' && !$user->whatsapp_verified) {
            // Redirect to WhatsApp verification page
            redirect(cn('whatsapp_verify'));
        }
    }
}
