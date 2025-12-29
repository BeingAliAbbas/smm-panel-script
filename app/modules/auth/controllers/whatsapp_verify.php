<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_verify extends MX_Controller {
	public $tb_users;
	public $tb_otp_verifications;
	public $tb_rate_limit;

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		$this->tb_users = USERS;
		$this->tb_otp_verifications = 'whatsapp_otp_verifications';
		$this->tb_rate_limit = 'whatsapp_otp_rate_limit';

		// Check if user is logged in but not verified
		if(!session("uid")){
			redirect(cn("auth/login"));
		}

		// Check if user already verified
		$user = $this->model->get("whatsapp_verified", $this->tb_users, ['id' => session('uid')]);
		if($user && $user->whatsapp_verified == 1 && segment(2) != 'ajax_send_otp' && segment(2) != 'ajax_verify_otp' && segment(2) != 'ajax_resend_otp'){
			// User already verified, redirect to dashboard
			redirect(cn("statistics"));
		}
	}

	public function index(){
		$this->setup_phone();
	}

	public function setup_phone(){
		$this->lang->load('../../../../themes/'.get_theme().'/language/english/'.get_theme());
		$data = array();
		$this->template->set_layout('blank_page');
		$this->template->build('../../../themes/'.get_theme().'/views/whatsapp_setup', $data);
	}

	public function verify_otp(){
		$this->lang->load('../../../../themes/'.get_theme().'/language/english/'.get_theme());
		$data = array(
			'whatsapp_number' => get('number')
		);
		$this->template->set_layout('blank_page');
		$this->template->build('../../../themes/'.get_theme().'/views/whatsapp_verify_otp', $data);
	}

	public function ajax_send_otp(){
		$whatsapp_number = post('whatsapp_number');
		$country_code = post('country_code');
		$user_id = session('uid');

		// Validate inputs
		if(empty($whatsapp_number) || empty($country_code)){
			ms(array(
				'status' => 'error',
				'message' => 'Please provide WhatsApp number and country code'
			));
		}

		// Format phone number (remove spaces, dashes, etc.)
		$whatsapp_number = preg_replace('/[^0-9]/', '', $whatsapp_number);
		
		// Combine country code and number
		$full_number = $country_code . $whatsapp_number;

		// Validate phone number format (basic validation)
		if(strlen($full_number) < 10 || strlen($full_number) > 15){
			ms(array(
				'status' => 'error',
				'message' => 'Invalid phone number format'
			));
		}

		// Check rate limiting
		if($this->_is_rate_limited($user_id)){
			ms(array(
				'status' => 'error',
				'message' => 'Too many OTP requests. Please try again in 15 minutes.'
			));
		}

		// Generate 6-digit OTP
		$otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
		$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

		// Store OTP in database
		$otp_data = array(
			'user_id' => $user_id,
			'whatsapp_number' => $full_number,
			'otp_code' => $otp_code,
			'expires_at' => $expires_at,
			'created_at' => NOW,
			'verified' => 0,
			'attempts' => 0
		);

		// Delete old unverified OTPs for this user
		$this->db->delete($this->tb_otp_verifications, array('user_id' => $user_id, 'verified' => 0));

		// Insert new OTP
		if(!$this->db->insert($this->tb_otp_verifications, $otp_data)){
			ms(array(
				'status' => 'error',
				'message' => 'Failed to generate OTP. Please try again.'
			));
		}

		// Update rate limiting
		$this->_update_rate_limit($user_id);

		// Send OTP via WhatsApp
		$this->load->library('whatsapp_notification');
		
		if($this->whatsapp_notification->is_configured()){
			$variables = array(
				'otp_code' => $otp_code,
				'expiry_minutes' => '5'
			);
			
			$result = $this->whatsapp_notification->send('otp_verification', $variables, $full_number);
			
			if($result !== true){
				log_message('error', 'Failed to send WhatsApp OTP: ' . $result);
				// Continue even if WhatsApp fails - user can still verify manually
			}
		}

		ms(array(
			'status' => 'success',
			'message' => 'OTP sent to your WhatsApp number',
			'data' => array(
				'whatsapp_number' => $full_number
			)
		));
	}

	public function ajax_resend_otp(){
		$whatsapp_number = post('whatsapp_number');
		$user_id = session('uid');

		if(empty($whatsapp_number)){
			ms(array(
				'status' => 'error',
				'message' => 'WhatsApp number is required'
			));
		}

		// Check rate limiting
		if($this->_is_rate_limited($user_id)){
			ms(array(
				'status' => 'error',
				'message' => 'Too many OTP requests. Please try again in 15 minutes.'
			));
		}

		// Generate new OTP
		$otp_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
		$expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));

		// Delete old unverified OTPs for this user
		$this->db->delete($this->tb_otp_verifications, array('user_id' => $user_id, 'verified' => 0));

		// Insert new OTP
		$otp_data = array(
			'user_id' => $user_id,
			'whatsapp_number' => $whatsapp_number,
			'otp_code' => $otp_code,
			'expires_at' => $expires_at,
			'created_at' => NOW,
			'verified' => 0,
			'attempts' => 0
		);

		if(!$this->db->insert($this->tb_otp_verifications, $otp_data)){
			ms(array(
				'status' => 'error',
				'message' => 'Failed to generate OTP. Please try again.'
			));
		}

		// Update rate limiting
		$this->_update_rate_limit($user_id);

		// Send OTP via WhatsApp
		$this->load->library('whatsapp_notification');
		
		if($this->whatsapp_notification->is_configured()){
			$variables = array(
				'otp_code' => $otp_code,
				'expiry_minutes' => '5'
			);
			
			$result = $this->whatsapp_notification->send('otp_verification', $variables, $whatsapp_number);
			
			if($result !== true){
				log_message('error', 'Failed to send WhatsApp OTP: ' . $result);
			}
		}

		ms(array(
			'status' => 'success',
			'message' => 'New OTP sent to your WhatsApp number'
		));
	}

	public function ajax_verify_otp(){
		$otp_code = post('otp_code');
		$whatsapp_number = post('whatsapp_number');
		$user_id = session('uid');

		// Validate inputs
		if(empty($otp_code) || empty($whatsapp_number)){
			ms(array(
				'status' => 'error',
				'message' => 'OTP code and WhatsApp number are required'
			));
		}

		// Get OTP record
		$otp_record = $this->model->get('*', $this->tb_otp_verifications, [
			'user_id' => $user_id,
			'whatsapp_number' => $whatsapp_number,
			'verified' => 0
		]);

		if(empty($otp_record)){
			ms(array(
				'status' => 'error',
				'message' => 'Invalid OTP or OTP expired. Please request a new one.'
			));
		}

		// Check if OTP expired
		if(strtotime($otp_record->expires_at) < time()){
			ms(array(
				'status' => 'error',
				'message' => 'OTP has expired. Please request a new one.'
			));
		}

		// Check attempts (max 5 attempts)
		if($otp_record->attempts >= 5){
			ms(array(
				'status' => 'error',
				'message' => 'Too many failed attempts. Please request a new OTP.'
			));
		}

		// Verify OTP
		if($otp_record->otp_code !== $otp_code){
			// Increment attempts
			$this->db->update($this->tb_otp_verifications, 
				['attempts' => $otp_record->attempts + 1], 
				['id' => $otp_record->id]
			);

			ms(array(
				'status' => 'error',
				'message' => 'Invalid OTP code. Please try again.'
			));
		}

		// OTP is valid, mark as verified
		$this->db->update($this->tb_otp_verifications, [
			'verified' => 1,
			'verified_at' => NOW
		], ['id' => $otp_record->id]);

		// Update user record
		$this->db->update($this->tb_users, [
			'whatsapp_number' => $whatsapp_number,
			'whatsapp_verified' => 1,
			'whatsapp_verified_at' => NOW,
			'changed' => NOW
		], ['id' => $user_id]);

		// Clear rate limiting for this user
		$this->db->delete($this->tb_rate_limit, ['identifier' => 'user_' . $user_id]);

		ms(array(
			'status' => 'success',
			'message' => 'WhatsApp number verified successfully!'
		));
	}

	private function _is_rate_limited($user_id){
		$identifier = 'user_' . $user_id;
		$rate_limit = $this->model->get('*', $this->tb_rate_limit, ['identifier' => $identifier]);

		if(empty($rate_limit)){
			return false;
		}

		// Check if within 15 minutes window
		$time_diff = time() - strtotime($rate_limit->first_request_at);
		
		if($time_diff > 900){ // 15 minutes = 900 seconds
			// Reset the rate limit
			$this->db->delete($this->tb_rate_limit, ['identifier' => $identifier]);
			return false;
		}

		// Check if exceeded 3 requests
		if($rate_limit->request_count >= 3){
			return true;
		}

		return false;
	}

	private function _update_rate_limit($user_id){
		$identifier = 'user_' . $user_id;
		$rate_limit = $this->model->get('*', $this->tb_rate_limit, ['identifier' => $identifier]);

		if(empty($rate_limit)){
			// Create new rate limit record
			$this->db->insert($this->tb_rate_limit, [
				'identifier' => $identifier,
				'request_count' => 1,
				'first_request_at' => NOW,
				'last_request_at' => NOW
			]);
		} else {
			// Update existing record
			$time_diff = time() - strtotime($rate_limit->first_request_at);
			
			if($time_diff > 900){ // Reset after 15 minutes
				$this->db->update($this->tb_rate_limit, [
					'request_count' => 1,
					'first_request_at' => NOW,
					'last_request_at' => NOW
				], ['identifier' => $identifier]);
			} else {
				$this->db->update($this->tb_rate_limit, [
					'request_count' => $rate_limit->request_count + 1,
					'last_request_at' => NOW
				], ['identifier' => $identifier]);
			}
		}
	}
}
