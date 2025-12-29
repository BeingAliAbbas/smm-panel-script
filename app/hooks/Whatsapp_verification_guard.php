<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp Verification Guard
 * 
 * This hook checks if the user has verified their WhatsApp number
 * after Google sign-in. It prevents access to protected pages until
 * the verification is complete.
 */
class Whatsapp_verification_guard {

    public function check_whatsapp_verification() {
        $CI =& get_instance();
        
        // Get current segment/route
        $segment1 = $CI->uri->segment(1);
        $segment2 = $CI->uri->segment(2);
        
        // List of allowed routes that don't require WhatsApp verification
        $allowed_routes = array(
            'auth',
            'whatsapp_verify',
            'assets',
            'themes',
            'public'
        );
        
        // Skip check for allowed routes
        if (in_array($segment1, $allowed_routes)) {
            return;
        }
        
        // Skip check if user is not logged in
        if (!session('uid')) {
            return;
        }
        
        // Load database if not already loaded
        if (!isset($CI->db)) {
            $CI->load->database();
        }
        
        // Check if user exists and get WhatsApp verification status
        $CI->db->select('whatsapp_verified, signup_type');
        $CI->db->from('general_users');
        $CI->db->where('id', session('uid'));
        $user = $CI->db->get()->row();
        
        if (!$user) {
            // User not found, log them out
            unset_session('uid');
            unset_session('user_current_info');
            redirect(cn('auth/login'));
            return;
        }
        
        // Only check WhatsApp verification for Google sign-up users
        if ($user->signup_type === 'google' && (!$user->whatsapp_verified || $user->whatsapp_verified != 1)) {
            // User hasn't verified WhatsApp, redirect to verification
            redirect(cn('whatsapp_verify'));
        }
    }
}
