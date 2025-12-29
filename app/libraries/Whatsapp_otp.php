<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsApp OTP Library
 * 
 * Handles OTP generation, validation, and sending for WhatsApp verification
 * 
 * @package    SMM Panel
 * @subpackage Libraries
 * @category   Authentication
 */
class Whatsapp_otp {

    protected $CI;
    protected $otp_length = 6;
    protected $otp_expiry_minutes = 10;
    protected $max_attempts = 5;
    protected $resend_cooldown_seconds = 60;

    /**
     * Constructor
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->library('whatsapp_notification');
    }

    /**
     * Generate a random OTP
     * 
     * @return string
     */
    public function generate_otp() {
        return str_pad(rand(0, pow(10, $this->otp_length) - 1), $this->otp_length, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to WhatsApp number
     * 
     * @param int    $user_id       User ID
     * @param string $phone_number  WhatsApp number in international format
     * @return array Result array with status and message
     */
    public function send_otp($user_id, $phone_number) {
        // Validate phone number format (must start with +)
        if (empty($phone_number) || $phone_number[0] !== '+') {
            return array(
                'status' => 'error',
                'message' => 'Invalid phone number format. Must include country code with + prefix.'
            );
        }

        // Check cooldown period
        $user = $this->CI->db->select('whatsapp_otp_expires_at')
                             ->where('id', $user_id)
                             ->get('general_users')
                             ->row();

        if ($user && $user->whatsapp_otp_expires_at) {
            $last_sent = strtotime($user->whatsapp_otp_expires_at) - ($this->otp_expiry_minutes * 60);
            $cooldown_ends = $last_sent + $this->resend_cooldown_seconds;
            
            if (time() < $cooldown_ends) {
                $wait_seconds = $cooldown_ends - time();
                return array(
                    'status' => 'error',
                    'message' => 'Please wait ' . $wait_seconds . ' seconds before requesting a new OTP.'
                );
            }
        }

        // Generate OTP
        $otp = $this->generate_otp();
        $expires_at = date('Y-m-d H:i:s', strtotime('+' . $this->otp_expiry_minutes . ' minutes'));

        // Store OTP in database
        $data = array(
            'whatsapp_otp' => $otp,
            'whatsapp_otp_expires_at' => $expires_at,
            'whatsapp_otp_attempts' => 0,
            'whatsapp_number' => $phone_number
        );

        $this->CI->db->where('id', $user_id);
        $this->CI->db->update('users', $data);

        if ($this->CI->db->affected_rows() === 0) {
            return array(
                'status' => 'error',
                'message' => 'Failed to update user record.'
            );
        }

        // Send OTP via WhatsApp
        $variables = array(
            'otp' => $otp,
            'expiry_minutes' => $this->otp_expiry_minutes
        );

        $result = $this->CI->whatsapp_notification->send('otp_verification', $variables, $phone_number);

        if ($result === true) {
            return array(
                'status' => 'success',
                'message' => 'OTP sent successfully to your WhatsApp number.'
            );
        } else {
            // Failed to send, but we'll allow user to retry
            log_message('error', 'WhatsApp OTP: Failed to send - ' . $result);
            return array(
                'status' => 'warning',
                'message' => 'OTP generated but there was an issue sending it. Please try again or contact support if the problem persists.'
            );
        }
    }

    /**
     * Verify OTP
     * 
     * @param int    $user_id  User ID
     * @param string $otp      OTP entered by user
     * @return array Result array with status and message
     */
    public function verify_otp($user_id, $otp) {
        // Get user data
        $user = $this->CI->db->select('whatsapp_otp, whatsapp_otp_expires_at, whatsapp_otp_attempts, whatsapp_number')
                             ->where('id', $user_id)
                             ->get('general_users')
                             ->row();

        if (!$user) {
            return array(
                'status' => 'error',
                'message' => 'User not found.'
            );
        }

        // Check if OTP exists
        if (empty($user->whatsapp_otp)) {
            return array(
                'status' => 'error',
                'message' => 'No OTP found. Please request a new OTP.'
            );
        }

        // Check attempts limit
        if ($user->whatsapp_otp_attempts >= $this->max_attempts) {
            // Clear OTP
            $this->clear_otp($user_id);
            return array(
                'status' => 'error',
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.'
            );
        }

        // Check expiry
        if (strtotime($user->whatsapp_otp_expires_at) < time()) {
            // Clear expired OTP
            $this->clear_otp($user_id);
            return array(
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new OTP.'
            );
        }

        // Increment attempts
        $this->CI->db->where('id', $user_id);
        $this->CI->db->set('whatsapp_otp_attempts', 'whatsapp_otp_attempts + 1', FALSE);
        $this->CI->db->update('users');

        // Verify OTP
        if ($user->whatsapp_otp !== $otp) {
            $remaining_attempts = $this->max_attempts - ($user->whatsapp_otp_attempts + 1);
            return array(
                'status' => 'error',
                'message' => 'Invalid OTP. You have ' . $remaining_attempts . ' attempt(s) remaining.'
            );
        }

        // OTP is valid - mark as verified and clear OTP
        $update_data = array(
            'whatsapp_verified' => 1,
            'whatsapp_otp' => NULL,
            'whatsapp_otp_expires_at' => NULL,
            'whatsapp_otp_attempts' => 0
        );

        $this->CI->db->where('id', $user_id);
        $this->CI->db->update('users', $update_data);

        return array(
            'status' => 'success',
            'message' => 'WhatsApp number verified successfully!'
        );
    }

    /**
     * Clear OTP data for a user
     * 
     * @param int $user_id User ID
     * @return bool
     */
    public function clear_otp($user_id) {
        $data = array(
            'whatsapp_otp' => NULL,
            'whatsapp_otp_expires_at' => NULL,
            'whatsapp_otp_attempts' => 0
        );

        $this->CI->db->where('id', $user_id);
        $this->CI->db->update('users', $data);

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Check if user's WhatsApp is verified
     * 
     * @param int $user_id User ID
     * @return bool
     */
    public function is_verified($user_id) {
        $user = $this->CI->db->select('whatsapp_verified')
                             ->where('id', $user_id)
                             ->get('general_users')
                             ->row();

        return $user && $user->whatsapp_verified == 1;
    }

    /**
     * Validate international phone number format
     * 
     * @param string $phone Phone number
     * @return bool
     */
    public function validate_phone_number($phone) {
        // Must start with + and contain 10-15 digits
        // Format: +[country code][number]
        return preg_match('/^\+[1-9]\d{9,14}$/', $phone);
    }

    /**
     * Get supported countries for phone number selection
     * 
     * @return array Array of countries with codes
     */
    public function get_countries() {
        return array(
            array('code' => '+1', 'name' => 'United States/Canada', 'flag' => '🇺🇸'),
            array('code' => '+44', 'name' => 'United Kingdom', 'flag' => '🇬🇧'),
            array('code' => '+91', 'name' => 'India', 'flag' => '🇮🇳'),
            array('code' => '+92', 'name' => 'Pakistan', 'flag' => '🇵🇰'),
            array('code' => '+93', 'name' => 'Afghanistan', 'flag' => '🇦🇫'),
            array('code' => '+20', 'name' => 'Egypt', 'flag' => '🇪🇬'),
            array('code' => '+27', 'name' => 'South Africa', 'flag' => '🇿🇦'),
            array('code' => '+30', 'name' => 'Greece', 'flag' => '🇬🇷'),
            array('code' => '+31', 'name' => 'Netherlands', 'flag' => '🇳🇱'),
            array('code' => '+32', 'name' => 'Belgium', 'flag' => '🇧🇪'),
            array('code' => '+33', 'name' => 'France', 'flag' => '🇫🇷'),
            array('code' => '+34', 'name' => 'Spain', 'flag' => '🇪🇸'),
            array('code' => '+39', 'name' => 'Italy', 'flag' => '🇮🇹'),
            array('code' => '+41', 'name' => 'Switzerland', 'flag' => '🇨🇭'),
            array('code' => '+43', 'name' => 'Austria', 'flag' => '🇦🇹'),
            array('code' => '+45', 'name' => 'Denmark', 'flag' => '🇩🇰'),
            array('code' => '+46', 'name' => 'Sweden', 'flag' => '🇸🇪'),
            array('code' => '+47', 'name' => 'Norway', 'flag' => '🇳🇴'),
            array('code' => '+48', 'name' => 'Poland', 'flag' => '🇵🇱'),
            array('code' => '+49', 'name' => 'Germany', 'flag' => '🇩🇪'),
            array('code' => '+51', 'name' => 'Peru', 'flag' => '🇵🇪'),
            array('code' => '+52', 'name' => 'Mexico', 'flag' => '🇲🇽'),
            array('code' => '+53', 'name' => 'Cuba', 'flag' => '🇨🇺'),
            array('code' => '+54', 'name' => 'Argentina', 'flag' => '🇦🇷'),
            array('code' => '+55', 'name' => 'Brazil', 'flag' => '🇧🇷'),
            array('code' => '+56', 'name' => 'Chile', 'flag' => '🇨🇱'),
            array('code' => '+57', 'name' => 'Colombia', 'flag' => '🇨🇴'),
            array('code' => '+58', 'name' => 'Venezuela', 'flag' => '🇻🇪'),
            array('code' => '+60', 'name' => 'Malaysia', 'flag' => '🇲🇾'),
            array('code' => '+61', 'name' => 'Australia', 'flag' => '🇦🇺'),
            array('code' => '+62', 'name' => 'Indonesia', 'flag' => '🇮🇩'),
            array('code' => '+63', 'name' => 'Philippines', 'flag' => '🇵🇭'),
            array('code' => '+64', 'name' => 'New Zealand', 'flag' => '🇳🇿'),
            array('code' => '+65', 'name' => 'Singapore', 'flag' => '🇸🇬'),
            array('code' => '+66', 'name' => 'Thailand', 'flag' => '🇹🇭'),
            array('code' => '+81', 'name' => 'Japan', 'flag' => '🇯🇵'),
            array('code' => '+82', 'name' => 'South Korea', 'flag' => '🇰🇷'),
            array('code' => '+84', 'name' => 'Vietnam', 'flag' => '🇻🇳'),
            array('code' => '+86', 'name' => 'China', 'flag' => '🇨🇳'),
            array('code' => '+90', 'name' => 'Turkey', 'flag' => '🇹🇷'),
            array('code' => '+94', 'name' => 'Sri Lanka', 'flag' => '🇱🇰'),
            array('code' => '+95', 'name' => 'Myanmar', 'flag' => '🇲🇲'),
            array('code' => '+98', 'name' => 'Iran', 'flag' => '🇮🇷'),
            array('code' => '+212', 'name' => 'Morocco', 'flag' => '🇲🇦'),
            array('code' => '+213', 'name' => 'Algeria', 'flag' => '🇩🇿'),
            array('code' => '+216', 'name' => 'Tunisia', 'flag' => '🇹🇳'),
            array('code' => '+218', 'name' => 'Libya', 'flag' => '🇱🇾'),
            array('code' => '+220', 'name' => 'Gambia', 'flag' => '🇬🇲'),
            array('code' => '+221', 'name' => 'Senegal', 'flag' => '🇸🇳'),
            array('code' => '+234', 'name' => 'Nigeria', 'flag' => '🇳🇬'),
            array('code' => '+249', 'name' => 'Sudan', 'flag' => '🇸🇩'),
            array('code' => '+254', 'name' => 'Kenya', 'flag' => '🇰🇪'),
            array('code' => '+255', 'name' => 'Tanzania', 'flag' => '🇹🇿'),
            array('code' => '+256', 'name' => 'Uganda', 'flag' => '🇺🇬'),
            array('code' => '+351', 'name' => 'Portugal', 'flag' => '🇵🇹'),
            array('code' => '+352', 'name' => 'Luxembourg', 'flag' => '🇱🇺'),
            array('code' => '+353', 'name' => 'Ireland', 'flag' => '🇮🇪'),
            array('code' => '+354', 'name' => 'Iceland', 'flag' => '🇮🇸'),
            array('code' => '+355', 'name' => 'Albania', 'flag' => '🇦🇱'),
            array('code' => '+370', 'name' => 'Lithuania', 'flag' => '🇱🇹'),
            array('code' => '+371', 'name' => 'Latvia', 'flag' => '🇱🇻'),
            array('code' => '+372', 'name' => 'Estonia', 'flag' => '🇪🇪'),
            array('code' => '+380', 'name' => 'Ukraine', 'flag' => '🇺🇦'),
            array('code' => '+381', 'name' => 'Serbia', 'flag' => '🇷🇸'),
            array('code' => '+382', 'name' => 'Montenegro', 'flag' => '🇲🇪'),
            array('code' => '+385', 'name' => 'Croatia', 'flag' => '🇭🇷'),
            array('code' => '+386', 'name' => 'Slovenia', 'flag' => '🇸🇮'),
            array('code' => '+387', 'name' => 'Bosnia and Herzegovina', 'flag' => '🇧🇦'),
            array('code' => '+420', 'name' => 'Czech Republic', 'flag' => '🇨🇿'),
            array('code' => '+421', 'name' => 'Slovakia', 'flag' => '🇸🇰'),
            array('code' => '+880', 'name' => 'Bangladesh', 'flag' => '🇧🇩'),
            array('code' => '+960', 'name' => 'Maldives', 'flag' => '🇲🇻'),
            array('code' => '+961', 'name' => 'Lebanon', 'flag' => '🇱🇧'),
            array('code' => '+962', 'name' => 'Jordan', 'flag' => '🇯🇴'),
            array('code' => '+963', 'name' => 'Syria', 'flag' => '🇸🇾'),
            array('code' => '+964', 'name' => 'Iraq', 'flag' => '🇮🇶'),
            array('code' => '+965', 'name' => 'Kuwait', 'flag' => '🇰🇼'),
            array('code' => '+966', 'name' => 'Saudi Arabia', 'flag' => '🇸🇦'),
            array('code' => '+967', 'name' => 'Yemen', 'flag' => '🇾🇪'),
            array('code' => '+968', 'name' => 'Oman', 'flag' => '🇴🇲'),
            array('code' => '+971', 'name' => 'United Arab Emirates', 'flag' => '🇦🇪'),
            array('code' => '+972', 'name' => 'Israel', 'flag' => '🇮🇱'),
            array('code' => '+973', 'name' => 'Bahrain', 'flag' => '🇧🇭'),
            array('code' => '+974', 'name' => 'Qatar', 'flag' => '🇶🇦'),
            array('code' => '+975', 'name' => 'Bhutan', 'flag' => '🇧🇹'),
            array('code' => '+976', 'name' => 'Mongolia', 'flag' => '🇲🇳'),
            array('code' => '+977', 'name' => 'Nepal', 'flag' => '🇳🇵'),
        );
    }
}
