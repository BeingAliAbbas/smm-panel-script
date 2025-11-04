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
}
