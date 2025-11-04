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
}
