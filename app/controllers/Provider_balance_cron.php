<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Provider Balance Monitor Cron
 * 
 * This cron job checks provider balances and sends email alerts
 * when balance falls below configured threshold
 * 
 * Route: /cron/check_provider_balance
 */
class Provider_balance_cron extends CI_Controller {
    
    public $tb_api_providers;
    public $cron_logger;
    
    public function __construct() {
        parent::__construct();
        
        // Load database library
        $this->load->database();
        
        // Load cron logger
        $this->load->library('cron_logger');
        
        $this->tb_api_providers = API_PROVIDERS;
    }
    
    /**
     * Default index method - redirects to check_balances
     */
    public function index() {
        $this->check_balances();
    }
    
    /**
     * Main cron entry point - checks all provider balances
     * Route: /cron/check_provider_balance
     */
    public function check_balances() {
        // Start logging
        $log_id = $this->cron_logger->start('cron/check_provider_balance');
        
        try {
            echo "Starting provider balance check...<br>";
            
            // Check if low balance alerts are enabled
            if (get_option('enable_email_low_provider_balance', '0') != '1') {
                echo "Low provider balance alerts are disabled.<br>Successfully";
                if ($log_id) {
                    $this->cron_logger->end($log_id, 'Success', 200, 'Low balance alerts disabled');
                }
                return;
            }
            
            // Get configured threshold
            $threshold = (float)get_option('provider_balance_threshold', 100);
            
            // Get all active providers
            $providers = $this->db->select('id, name, balance, currency_code')
                                  ->where('status', 1)
                                  ->where('balance IS NOT NULL', null, false)
                                  ->get($this->tb_api_providers)
                                  ->result();
            
            if (empty($providers)) {
                echo "No active providers found.<br>Successfully";
                if ($log_id) {
                    $this->cron_logger->end($log_id, 'Success', 200, 'No providers to check');
                }
                return;
            }
            
            $checked_count = 0;
            $alert_count = 0;
            
            // Load transactional email library
            $this->load->library('Transactional_email');
            
            foreach ($providers as $provider) {
                $checked_count++;
                
                // Check if balance is below threshold
                if ($provider->balance < $threshold) {
                    // Send alert email
                    $result = $this->transactional_email->send_low_balance_alert(
                        $provider->name,
                        $provider->balance . ' ' . ($provider->currency_code ?? ''),
                        $threshold,
                        $provider->id
                    );
                    
                    if ($result) {
                        echo "Alert sent for provider: {$provider->name} (Balance: {$provider->balance})<br>";
                        $alert_count++;
                    }
                }
            }
            
            echo "Completed! Checked {$checked_count} providers, sent {$alert_count} alerts.<br>";
            
            if ($log_id) {
                $message = "Checked {$checked_count} providers, sent {$alert_count} low balance alerts";
                $this->cron_logger->end($log_id, 'Success', 200, $message);
            }
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
            if ($log_id) {
                $this->cron_logger->end($log_id, 'Failed', 500, 'Exception: ' . $e->getMessage());
            }
        }
    }
}
