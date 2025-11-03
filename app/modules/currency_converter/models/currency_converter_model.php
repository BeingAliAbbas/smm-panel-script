<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class currency_converter_model extends CI_Model {

    protected $tb_main;

    public function __construct(){
        parent::__construct();
        $this->tb_main = CURRENCY_RATES;
    }

    public function convert_currency($from, $to, $amount){
        // Get exchange rates
        $rates = $this->get_exchange_rates($from);
        
        if(!$rates){
            return array('status' => 'error', 'message' => 'Failed to fetch exchange rates');
        }

        // If converting to the same currency
        if($from == $to){
            return array(
                'status' => 'success',
                'from' => $from,
                'to' => $to,
                'amount' => floatval($amount),
                'result' => floatval($amount),
                'rate' => 1.0
            );
        }

        // Check if the target currency exists in rates
        if(!isset($rates[$to])){
            return array('status' => 'error', 'message' => 'Invalid currency conversion');
        }

        $rate = $rates[$to];
        $result = floatval($amount) * floatval($rate);

        return array(
            'status' => 'success',
            'from' => $from,
            'to' => $to,
            'amount' => floatval($amount),
            'result' => round($result, 2),
            'rate' => $rate
        );
    }

    public function get_exchange_rates($base = 'USD'){
        // Check if we have cached rates (less than 1 hour old)
        $cached_rates = $this->get_cached_rates($base);
        if($cached_rates){
            return $cached_rates;
        }

        // Fetch from API
        $rates = $this->fetch_rates_from_api($base);
        
        if($rates){
            // Cache the rates
            $this->cache_rates($base, $rates);
            return $rates;
        }

        return false;
    }

    private function get_cached_rates($base){
        // Try to get from database cache if table exists
        if(!$this->db->table_exists('currency_rates')){
            return false;
        }

        $this->db->where('base_currency', $base);
        $this->db->where('updated_at >', date('Y-m-d H:i:s', strtotime('-1 hour')));
        $query = $this->db->get('currency_rates');
        
        if($query->num_rows() > 0){
            $row = $query->row();
            return json_decode($row->rates, true);
        }
        
        return false;
    }

    private function cache_rates($base, $rates){
        // Cache rates in database if table exists
        if(!$this->db->table_exists('currency_rates')){
            return false;
        }

        $data = array(
            'base_currency' => $base,
            'rates' => json_encode($rates),
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Check if record exists
        $this->db->where('base_currency', $base);
        $query = $this->db->get('currency_rates');
        
        if($query->num_rows() > 0){
            $this->db->where('base_currency', $base);
            $this->db->update('currency_rates', $data);
        } else {
            $this->db->insert('currency_rates', $data);
        }
    }

    private function fetch_rates_from_api($base){
        // Using exchangerate-api.com free API
        $api_url = "https://api.exchangerate-api.com/v4/latest/{$base}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($http_code == 200 && $response){
            $data = json_decode($response, true);
            if(isset($data['rates'])){
                return $data['rates'];
            }
        }
        
        // Fallback to static rates if API fails
        return $this->get_fallback_rates($base);
    }

    private function get_fallback_rates($base){
        // Static fallback rates (approximate values)
        $static_rates = array(
            'USD' => array(
                'EUR' => 0.92,
                'GBP' => 0.79,
                'JPY' => 149.50,
                'AUD' => 1.53,
                'CAD' => 1.36,
                'CHF' => 0.88,
                'CNY' => 7.24,
                'INR' => 83.12,
                'PKR' => 278.50,
                'SAR' => 3.75,
                'AED' => 3.67,
                'BRL' => 4.97,
                'MXN' => 17.10,
                'SGD' => 1.34,
                'NZD' => 1.64,
                'ZAR' => 18.75,
                'KRW' => 1315.50,
                'TRY' => 28.50,
                'RUB' => 92.50,
                'USD' => 1.00
            )
        );

        if($base == 'USD'){
            return $static_rates['USD'];
        }

        // For other bases, calculate from USD
        if(isset($static_rates['USD'][$base])){
            $base_rate = $static_rates['USD'][$base];
            $converted_rates = array();
            foreach($static_rates['USD'] as $currency => $rate){
                $converted_rates[$currency] = $rate / $base_rate;
            }
            return $converted_rates;
        }

        return false;
    }
}
