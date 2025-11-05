<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class currencies extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('currencies_model', 'model');
	}

	/**
	 * Set user's selected currency
	 */
	public function set_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$currency_code = $this->input->post('currency_code', true);
		
		if (empty($currency_code)) {
			ms([
				'status'  => 'error',
				'message' => 'Currency code is required'
			]);
		}

		// Verify currency exists and is active
		$currency = $this->model->get_by_code($currency_code);
		
		if (!$currency) {
			ms([
				'status'  => 'error',
				'message' => 'Invalid currency code'
			]);
		}

		// Store in session
		$this->session->set_userdata('selected_currency', $currency_code);
		
		// Also store in cookie for persistence (30 days)
		$this->input->set_cookie([
			'name'   => 'selected_currency',
			'value'  => $currency_code,
			'expire' => 2592000 // 30 days
		]);

		ms([
			'status'  => 'success',
			'message' => 'Currency changed successfully',
			'data'    => [
				'code'   => $currency->code,
				'symbol' => $currency->symbol,
				'name'   => $currency->name
			]
		]);
	}

	/**
	 * Get user's selected currency or default
	 */
	public function get_selected_currency(){
		// Check session first
		$selected = $this->session->userdata('selected_currency');
		
		// If not in session, check cookie
		if (!$selected) {
			$selected = $this->input->cookie('selected_currency', true);
		}

		// If still not found, get default
		if ($selected) {
			$currency = $this->model->get_by_code($selected);
			if ($currency) {
				return $currency;
			}
		}

		return $this->model->get_default_currency();
	}

	/**
	 * Update exchange rate
	 */
	public function update_rate(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);
		$exchange_rate = $this->input->post('exchange_rate', true);

		if (!$id || !$exchange_rate) {
			ms([
				'status'  => 'error',
				'message' => 'Missing required fields'
			]);
		}

		$this->db->where('id', $id);
		$this->db->update('currencies', ['exchange_rate' => $exchange_rate]);

		ms([
			'status'  => 'success',
			'message' => 'Exchange rate updated successfully'
		]);
	}

	/**
	 * Set default currency
	 */
	public function set_default(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);

		if (!$id) {
			ms([
				'status'  => 'error',
				'message' => 'Missing currency ID'
			]);
		}

		// Unset all defaults
		$this->db->update('currencies', ['is_default' => 0]);

		// Set new default
		$this->db->where('id', $id);
		$this->db->update('currencies', ['is_default' => 1]);

		ms([
			'status'  => 'success',
			'message' => 'Default currency updated successfully'
		]);
	}

	/**
	 * Toggle currency status
	 */
	public function toggle_status(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$id = $this->input->post('id', true);
		$status = $this->input->post('status', true);

		if (!$id) {
			ms([
				'status'  => 'error',
				'message' => 'Missing currency ID'
			]);
		}

		$this->db->where('id', $id);
		$this->db->update('currencies', ['status' => $status]);

		ms([
			'status'  => 'success',
			'message' => 'Currency status updated'
		]);
	}

	/**
	 * Add new currency
	 */
	public function add_currency(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		$data = [
			'code'          => strtoupper($this->input->post('code', true)),
			'name'          => $this->input->post('name', true),
			'symbol'        => $this->input->post('symbol', true),
			'exchange_rate' => $this->input->post('exchange_rate', true),
			'status'        => 1,
			'is_default'    => 0
		];

		// Check if code already exists
		$exists = $this->db->get_where('currencies', ['code' => $data['code']])->row();
		if ($exists) {
			ms([
				'status'  => 'error',
				'message' => 'Currency code already exists'
			]);
		}

		$this->db->insert('currencies', $data);

		ms([
			'status'  => 'success',
			'message' => 'Currency added successfully'
		]);
	}

	/**
	 * Fetch latest exchange rates from API
	 * Uses exchangerate-api.com (free tier: 1,500 requests/month)
	 */
	public function fetch_rates(){
		if ($this->input->method() !== 'post') {
			ms([
				'status'  => 'error',
				'message' => 'Invalid method'
			]);
		}

		// Get the default currency (PKR)
		$default_currency = $this->model->get_default_currency();
		if (!$default_currency) {
			ms([
				'status'  => 'error',
				'message' => 'Default currency not set'
			]);
		}

		// Fetch rates from API (using PKR as base)
		$api_url = "https://api.exchangerate-api.com/v4/latest/" . $default_currency->code;
		
		// Use cURL to fetch the data
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code !== 200 || !$response) {
			ms([
				'status'  => 'error',
				'message' => 'Failed to fetch exchange rates from API'
			]);
		}

		$data = json_decode($response, true);
		
		if (!isset($data['rates']) || !is_array($data['rates'])) {
			ms([
				'status'  => 'error',
				'message' => 'Invalid API response format'
			]);
		}

		// Update rates for all currencies
		$updated_count = 0;
		$all_currencies = $this->model->get_active_currencies();
		
		foreach ($all_currencies as $currency) {
			// Skip the default currency
			if ($currency->is_default == 1) {
				continue;
			}

			// Check if rate exists in API response
			if (isset($data['rates'][$currency->code])) {
				$new_rate = $data['rates'][$currency->code];
				
				// Update the rate in database
				$this->db->where('id', $currency->id);
				$this->db->update('currencies', ['exchange_rate' => $new_rate]);
				
				$updated_count++;
			}
		}

		ms([
			'status'  => 'success',
			'message' => "Exchange rates updated successfully. {$updated_count} currencies updated.",
			'data'    => [
				'updated_count' => $updated_count,
				'last_update'   => date('Y-m-d H:i:s')
			]
		]);
	}
}
