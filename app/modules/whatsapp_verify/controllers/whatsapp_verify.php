<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_verify extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('whatsapp_otp');
        $this->load->database();
        
        // Check if user is logged in
        if (!session('uid')) {
            redirect(cn('auth/login'));
        }
    }

    /**
     * Main verification page - shows phone input or OTP input based on state
     */
    public function index() {
        $user_id = session('uid');
        
        // Check if already verified
        if ($this->whatsapp_otp->is_verified($user_id)) {
            redirect(cn('statistics'));
            return;
        }

        // Get user data
        $user = $this->db->select('whatsapp_number, whatsapp_otp_expires_at')
                         ->where('id', $user_id)
                         ->get('general_users')
                         ->row();

        $data = array(
            'has_pending_otp' => !empty($user->whatsapp_otp_expires_at) && strtotime($user->whatsapp_otp_expires_at) > time(),
            'phone_number' => $user->whatsapp_number ?? '',
            'countries' => $this->whatsapp_otp->get_countries()
        );

        $this->template->set_layout('blank_page');
        $this->template->build('whatsapp_verify/verify', $data);
    }

    /**
     * AJAX: Send OTP to phone number
     */
    public function ajax_send_otp() {
        $user_id = session('uid');
        $country_code = post('country_code');
        $phone_number = post('phone_number');

        // Validate input
        if (empty($country_code) || empty($phone_number)) {
            ms(array(
                'status' => 'error',
                'message' => 'Please enter your phone number.'
            ));
            return;
        }

        // Remove any spaces, dashes, or other characters
        $phone_number = preg_replace('/[^0-9]/', '', $phone_number);
        
        // Construct full international number
        $full_number = $country_code . $phone_number;

        // Validate format
        if (!$this->whatsapp_otp->validate_phone_number($full_number)) {
            ms(array(
                'status' => 'error',
                'message' => 'Invalid phone number format. Please check and try again.'
            ));
            return;
        }

        // Send OTP
        $result = $this->whatsapp_otp->send_otp($user_id, $full_number);
        
        ms($result);
    }

    /**
     * AJAX: Verify OTP
     */
    public function ajax_verify_otp() {
        $user_id = session('uid');
        $otp = post('otp');

        // Validate input
        if (empty($otp)) {
            ms(array(
                'status' => 'error',
                'message' => 'Please enter the OTP.'
            ));
            return;
        }

        // Verify OTP
        $result = $this->whatsapp_otp->verify_otp($user_id, $otp);
        
        if ($result['status'] === 'success') {
            // Mark session as verified
            set_session('whatsapp_verified', true);
            
            // Return success with redirect URL
            $result['redirect'] = cn('statistics');
        }

        ms($result);
    }

    /**
     * AJAX: Resend OTP
     */
    public function ajax_resend_otp() {
        $user_id = session('uid');

        // Get current phone number
        $user = $this->db->select('whatsapp_number')
                         ->where('id', $user_id)
                         ->get('general_users')
                         ->row();

        if (empty($user->whatsapp_number)) {
            ms(array(
                'status' => 'error',
                'message' => 'No phone number found. Please enter your number first.'
            ));
            return;
        }

        // Resend OTP
        $result = $this->whatsapp_otp->send_otp($user_id, $user->whatsapp_number);
        
        ms($result);
    }

    /**
     * Change phone number (restart verification)
     */
    public function ajax_change_number() {
        $user_id = session('uid');

        // Clear current OTP
        $this->whatsapp_otp->clear_otp($user_id);

        ms(array(
            'status' => 'success',
            'message' => 'You can now enter a new phone number.'
        ));
    }
}
