<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp OTP Library
 * 
 * Handles OTP generation, sending, and verification for WhatsApp numbers
 * 
 * @package    SMM Panel
 * @subpackage Libraries
 * @category   Authentication
 */
class Whatsapp_otp {

    protected $CI;
    protected $otp_expiry_minutes = 10;
    protected $max_attempts = 3;
    protected $resend_cooldown_seconds = 60;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('whatsapp_notification');
    }

    /**
     * Generate and send OTP to WhatsApp number
     * 
     * @param int    $user_id         User ID
     * @param string $whatsapp_number WhatsApp number in international format
     * @return array Result with status and message
     */
    public function send_otp($user_id, $whatsapp_number) {
        // Check if OTP verification is enabled
        if (!$this->is_otp_enabled()) {
            return [
                'status' => 'error',
                'message' => 'WhatsApp OTP verification is not enabled'
            ];
        }

        // Check for recent OTP (cooldown)
        if ($this->has_recent_otp($user_id)) {
            return [
                'status' => 'error',
                'message' => 'Please wait before requesting a new OTP'
            ];
        }

        // Generate 6-digit OTP
        $otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Calculate expiry time
        $created_at = date('Y-m-d H:i:s');
        $expires_at = date('Y-m-d H:i:s', strtotime("+{$this->otp_expiry_minutes} minutes"));

        // Save OTP to database
        $data = [
            'user_id' => $user_id,
            'whatsapp_number' => $whatsapp_number,
            'otp_code' => $otp_code,
            'created_at' => $created_at,
            'expires_at' => $expires_at,
            'attempts' => 0,
            'status' => 'pending'
        ];

        $this->CI->db->insert('whatsapp_otp_verification', $data);
        $otp_id = $this->CI->db->insert_id();

        // Send OTP via WhatsApp
        $send_result = $this->_send_otp_message($whatsapp_number, $otp_code);

        if ($send_result === true) {
            return [
                'status' => 'success',
                'message' => 'OTP sent successfully to your WhatsApp number',
                'otp_id' => $otp_id,
                'expires_in' => $this->otp_expiry_minutes
            ];
        } else {
            // Delete the OTP record if sending failed
            $this->CI->db->delete('whatsapp_otp_verification', ['id' => $otp_id]);
            
            return [
                'status' => 'error',
                'message' => 'Failed to send OTP. Please try again.'
            ];
        }
    }

    /**
     * Verify OTP code
     * 
     * @param int    $user_id  User ID
     * @param string $otp_code OTP code to verify
     * @return array Result with status and message
     */
    public function verify_otp($user_id, $otp_code) {
        // Get the latest pending OTP for this user
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->where('status', 'pending');
        $this->CI->db->order_by('created_at', 'DESC');
        $this->CI->db->limit(1);
        $otp_record = $this->CI->db->get('whatsapp_otp_verification')->row();

        if (!$otp_record) {
            return [
                'status' => 'error',
                'message' => 'No OTP request found. Please request a new OTP.'
            ];
        }

        // Check if OTP has expired
        if (strtotime($otp_record->expires_at) < time()) {
            $this->CI->db->update('whatsapp_otp_verification', 
                ['status' => 'expired'], 
                ['id' => $otp_record->id]
            );
            return [
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.'
            ];
        }

        // Check max attempts
        if ($otp_record->attempts >= $this->max_attempts) {
            $this->CI->db->update('whatsapp_otp_verification', 
                ['status' => 'failed'], 
                ['id' => $otp_record->id]
            );
            return [
                'status' => 'error',
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.'
            ];
        }

        // Increment attempts
        $this->CI->db->set('attempts', 'attempts + 1', FALSE);
        $this->CI->db->where('id', $otp_record->id);
        $this->CI->db->update('whatsapp_otp_verification');

        // Verify OTP code
        if ($otp_record->otp_code === $otp_code) {
            // Mark OTP as verified
            $this->CI->db->update('whatsapp_otp_verification', 
                [
                    'status' => 'verified',
                    'verified_at' => date('Y-m-d H:i:s')
                ], 
                ['id' => $otp_record->id]
            );

            // Update user's WhatsApp verification status
            $this->CI->db->update('general_users', 
                [
                    'whatsapp_number' => $otp_record->whatsapp_number,
                    'whatsapp_verified' => 1,
                    'whatsapp_setup_completed' => 1
                ], 
                ['id' => $user_id]
            );

            return [
                'status' => 'success',
                'message' => 'WhatsApp number verified successfully!'
            ];
        } else {
            $remaining = $this->max_attempts - ($otp_record->attempts + 1);
            return [
                'status' => 'error',
                'message' => "Invalid OTP code. $remaining attempts remaining."
            ];
        }
    }

    /**
     * Skip OTP verification and mark as completed (when OTP is disabled)
     * 
     * @param int    $user_id         User ID
     * @param string $whatsapp_number WhatsApp number
     * @return array Result with status and message
     */
    public function skip_otp_verification($user_id, $whatsapp_number) {
        // Update user's WhatsApp info without requiring OTP verification
        $this->CI->db->update('general_users', 
            [
                'whatsapp_number' => $whatsapp_number,
                'whatsapp_verified' => 1, // Auto-verify when OTP is disabled
                'whatsapp_setup_completed' => 1
            ], 
            ['id' => $user_id]
        );

        return [
            'status' => 'success',
            'message' => 'WhatsApp number saved successfully!'
        ];
    }

    /**
     * Check if OTP verification is enabled in settings
     * 
     * @return bool
     */
    public function is_otp_enabled() {
        return (bool) get_option('whatsapp_otp_verification_enabled', 0);
    }

    /**
     * Check if user has a recent OTP (cooldown check)
     * 
     * @param int $user_id User ID
     * @return bool
     */
    private function has_recent_otp($user_id) {
        $cooldown_time = date('Y-m-d H:i:s', strtotime("-{$this->resend_cooldown_seconds} seconds"));
        
        $this->CI->db->where('user_id', $user_id);
        $this->CI->db->where('created_at >', $cooldown_time);
        $this->CI->db->where('status', 'pending');
        $count = $this->CI->db->count_all_results('whatsapp_otp_verification');
        
        return $count > 0;
    }

    /**
     * Send OTP message via WhatsApp
     * 
     * @param string $phone    Phone number
     * @param string $otp_code OTP code
     * @return bool|string True on success, error message on failure
     */
    private function _send_otp_message($phone, $otp_code) {
        // Check if WhatsApp notification is configured
        if (!$this->CI->whatsapp_notification->is_configured()) {
            log_message('error', 'WhatsApp OTP: WhatsApp notification not configured');
            return 'WhatsApp notification not configured';
        }

        // Prepare variables for OTP template
        $variables = [
            'otp_code' => $otp_code,
            'expiry_minutes' => $this->otp_expiry_minutes
        ];

        // Send OTP notification
        return $this->CI->whatsapp_notification->send('whatsapp_otp', $variables, $phone);
    }

    /**
     * Clean up expired OTPs (should be called periodically via cron)
     */
    public function cleanup_expired_otps() {
        $this->CI->db->where('status', 'pending');
        $this->CI->db->where('expires_at <', date('Y-m-d H:i:s'));
        $this->CI->db->update('whatsapp_otp_verification', ['status' => 'expired']);
        
        // Optionally delete old records (older than 30 days)
        $this->CI->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')));
        $this->CI->db->delete('whatsapp_otp_verification');
    }
}
