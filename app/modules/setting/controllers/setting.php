<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class setting extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        $this->load->model('language/language_model', 'sub_model');
    }

    public function index($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Load WhatsApp API settings from whatsapp_config (single-row pattern)
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,  // may be null if not created yet
        ];

        $this->template->build('index', $data);
    }

    public function get_content($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Also supply API settings here if partial loads happen via AJAX tab switching
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,
        ];
        $this->template->build('index', $data);
    }

    /**
     * Generic settings saver (existing logic).
     * Saves POST keys as options, including whatsapp_number.
     */
    public function ajax_general_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $data              = $this->input->post(NULL, true);
        $default_home_page = $this->input->post("default_home_page", true);

        if (is_array($data)) {
            foreach ($data as $key => $value) {

                if (in_array($key, ['csrf_token_name','csrf_test_name'], true)) {
                    continue;
                }

                if (in_array($key, ['embed_javascript', 'embed_head_javascript', 'manual_payment_content'])) {
                    $value = htmlspecialchars(@$_POST[$key], ENT_QUOTES);
                }

                if (in_array($key, ['midtrans_payment_channels', 'coinpayments_acceptance', 'freekassa_acceptance'], true)) {
                    $value = json_encode($value);
                }

                if ($key === 'new_currecry_rate') {
                    $value = (double)$value;
                    if ($value <= 0) $value = 1;
                }

                if ($key === 'whatsapp_number') {
                    $value = trim($value);
                    $normalized = preg_replace('/[\s\-\(\)]+/', '', $value);
                    if ($normalized !== '' && !preg_match('/^\+?[0-9]{6,20}$/', $normalized)) {
                        ms([
                            'status'  => 'error',
                            'message' => 'Invalid WhatsApp number format'
                        ]);
                    }
                    $value = $normalized;
                }

                update_option($key, $value);
            }
        }

        if ($default_home_page != "") {
            $theme_file_path = APPPATH."../themes/config.json";
            if (is_writable(dirname($theme_file_path))) {
                if ($theme_file = @fopen($theme_file_path, "w")) {
                    $txt = '{ "theme" : "'.$default_home_page.'" }';
                    fwrite($theme_file, $txt);
                    fclose($theme_file);
                }
            }
        }

        ms([
            "status"  => "success",
            "message" => lang('Update_successfully')
        ]);
    }

    /**
     * Save WhatsApp API settings (url, api_key, admin_phone) to whatsapp_config table.
     * Table schema expected:
     * id (INT PK, usually 1), url VARCHAR, api_key VARCHAR, admin_phone VARCHAR
     */
    public function ajax_whatsapp_api_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $url         = trim($this->input->post('url', true));
        $api_key     = trim($this->input->post('api_key', true));
        $admin_phone = trim($this->input->post('admin_phone', true));

        // Basic validation (adjust as needed)
        if ($url === '' || $api_key === '' || $admin_phone === '') {
            ms([
                'status'  => 'error',
                'message' => 'All fields are required'
            ]);
        }

        // Normalize admin phone (optional)
        $normalized_phone = preg_replace('/[\s\-\(\)]+/', '', $admin_phone);
        if (!preg_match('/^\+?[0-9]{6,20}$/', $normalized_phone)) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid admin phone format'
            ]);
        }

        // Ensure single row pattern
        $existing = $this->db->get('whatsapp_config')->row();
        $data = [
            'url'         => $url,
            'api_key'     => $api_key,
            'admin_phone' => $normalized_phone,
        ];

        if ($existing) {
            $this->db->where('id', $existing->id)->update('whatsapp_config', $data);
        } else {
            // Force id=1 (optional) or let auto-increment
            $this->db->insert('whatsapp_config', $data);
        }

        if ($this->db->affected_rows() >= 0) {
            ms([
                'status'  => 'success',
                'message' => lang('Update_successfully'),
                'data'    => $data
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'No changes detected'
            ]);
        }
    }

    /**
     * Fetch current exchange rate from API (USD to target currency)
     * Called via AJAX from the currency settings page
     */
    public function fetch_exchange_rate() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $target_currency = $this->input->post('target_currency', true);
        
        if (empty($target_currency)) {
            $target_currency = get_option('currency_code', 'PKR');
        }

        // Use USD as base currency for the API
        $base_currency = 'USD';
        
        if ($target_currency === 'USD') {
            ms([
                'status'  => 'error',
                'message' => 'Exchange rate is not needed when target currency is USD'
            ]);
        }

        // Fetch exchange rate from API
        $result = $this->fetch_rate_from_api($base_currency, $target_currency);
        
        if ($result['status'] === 'success') {
            ms([
                'status'  => 'success',
                'message' => 'Exchange rate fetched successfully',
                'data'    => [
                    'rate' => $result['rate'],
                    'base' => $base_currency,
                    'target' => $target_currency
                ]
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Generate secure cron token for exchange rate updates
     * Called via AJAX from the currency settings page
     */
    public function generate_cron_token() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // Check if token already exists
        $existing_token = get_option('exchange_rate_cron_token', '');
        
        if ($existing_token) {
            // Return existing token
            ms([
                'status'  => 'success',
                'token'   => $existing_token
            ]);
        }

        // Generate a cryptographically secure random token
        if (function_exists('random_bytes')) {
            $token = bin2hex(random_bytes(32));
        } else {
            // Fallback for older PHP versions
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }

        // Save the token
        update_option('exchange_rate_cron_token', $token);

        ms([
            'status'  => 'success',
            'token'   => $token
        ]);
    }

    /**
     * Cron-accessible endpoint to automatically update exchange rate
     * Access via: yoursite.com/setting/cron_update_exchange_rate?token=xxx
     */
    public function cron_update_exchange_rate() {
        // Check token for security
        $token = $this->input->get('token', true);
        $expected_token = get_option('exchange_rate_cron_token', '');
        
        // Use hash_equals to prevent timing attacks
        if (empty($expected_token) || !hash_equals($expected_token, (string)$token)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid or missing token'
            ]);
            return;
        }

        $target_currency = get_option('currency_code', 'PKR');
        $base_currency = 'USD';
        
        if ($target_currency === 'USD') {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Exchange rate is not needed when target currency is USD'
            ]);
            return;
        }

        // Fetch exchange rate from API
        $result = $this->fetch_rate_from_api($base_currency, $target_currency);
        
        if ($result['status'] === 'success') {
            // Update the option
            update_option('new_currecry_rate', $result['rate']);
            
            echo json_encode([
                'status'  => 'success',
                'message' => 'Exchange rate updated successfully',
                'data'    => [
                    'rate' => $result['rate'],
                    'base' => $base_currency,
                    'target' => $target_currency,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ]);
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Helper method to fetch exchange rate from API
     * Uses exchangerate-api.com (free, no API key required)
     */
    private function fetch_rate_from_api($base_currency, $target_currency) {
        // Use exchangerate-api.com (free tier, no API key required)
        $api_url = "https://api.exchangerate-api.com/v4/latest/{$base_currency}";
        
        // Fetch exchange rates using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SMM-Panel-Script/1.0');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($http_code !== 200 || !$response) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch exchange rates from API' . ($curl_error ? ': ' . $curl_error : '')
            ];
        }
        
        $data = json_decode($response, true);
        
        if (!isset($data['rates']) || empty($data['rates'])) {
            return [
                'status' => 'error',
                'message' => 'Invalid API response'
            ];
        }
        
        if (!isset($data['rates'][$target_currency])) {
            return [
                'status' => 'error',
                'message' => "Exchange rate for {$target_currency} not found in API response"
            ];
        }
        
        $rate = $data['rates'][$target_currency];
        
        return [
            'status' => 'success',
            'rate' => $rate
        ];
    }
}