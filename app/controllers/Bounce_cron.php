<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bounce Detection Cron Controller
 * 
 * Runs IMAP bounce detection for configured SMTP accounts
 * Can be triggered via cron job or manually from admin panel
 */
class Bounce_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('ImapBounceDetector');
        $this->load->library('cron_logger');
        
        // Security token for cron access
        $this->requiredToken = get_option('bounce_cron_token', md5('bounce_detection_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/bounce_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /cron/bounce_detection?token=YOUR_TOKEN&smtp_id=SMTP_ID (optional)
     */
    public function run(){
        // Start logging
        $log_id = $this->cron_logger->start('cron/bounce_detection');
        
        // Verify token
        $token = $this->input->get('token', true);
        
        // Debug: Check if we have a token
        if(!$token){
            $error_response = [
                'status' => 'error',
                'message' => 'No token provided. Please add ?token=YOUR_ACTUAL_TOKEN to the URL',
                'example' => base_url('cron/bounce_detection?token=YOUR_ACTUAL_TOKEN'),
                'help' => 'The token is generated from your ENCRYPTION_KEY. Check your settings or use the token from admin panel.',
                'time' => date('c')
            ];
            $this->cron_logger->end($log_id, 'Failed', 401, 'No token provided');
            $this->respond($error_response);
            return;
        }
        
        // Check if token matches
        if(!hash_equals($this->requiredToken, $token)){
            $error_response = [
                'status' => 'error',
                'message' => 'Invalid token provided. The token does not match.',
                'provided_token_length' => strlen($token),
                'expected_token_length' => strlen($this->requiredToken),
                'help' => 'Make sure you are using the correct token from your system settings. Do not use "YOUR_TOKEN" literally.',
                'time' => date('c')
            ];
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid token');
            $this->respond($error_response);
            return;
        }
        
        // Get optional smtp_id for specific SMTP checking
        $smtp_id = $this->input->get('smtp_id', true);
        
        // Rate limiting - prevent running too frequently
        $minInterval = 300; // 5 minutes minimum between runs (bounces don't need to be instant)
        if(file_exists($this->lockFile)){
            $lastRun = (int)@file_get_contents($this->lockFile);
            $now = time();
            if($lastRun && ($now - $lastRun) < $minInterval){
                $result = [
                    'status' => 'rate_limited',
                    'message' => 'Bounce detection is rate limited. Please wait.',
                    'retry_after_sec' => $minInterval - ($now - $lastRun),
                    'time' => date('c')
                ];
                $this->cron_logger->end($log_id, 'Rate Limited', 429, $result['message']);
                $this->respond($result);
                return;
            }
        }
        
        // Update lock file
        @file_put_contents($this->lockFile, time());
        
        // Process bounce detection
        $result = $this->process_bounce_detection($smtp_id);
        
        // Log the result
        $status = ($result['status'] == 'success') ? 'Success' : 'Failed';
        $response_code = ($status == 'Success') ? 200 : 500;
        $message = $result['message'] . ' (Bounces: ' . ($result['total_bounces'] ?? 0) . ')';
        $this->cron_logger->end($log_id, $status, $response_code, $message);
        
        // Update last global check time
        $this->email_model->update_setting('imap_last_global_check', date('Y-m-d H:i:s'));
        
        $this->respond($result);
    }
    
    /**
     * Process bounce detection for SMTP accounts
     */
    private function process_bounce_detection($smtp_id = null){
        try {
            // Get SMTP configs with IMAP enabled
            $this->email_model->db->where('status', 1);
            $this->email_model->db->where('imap_enabled', 1);
            
            // If specific SMTP ID provided, filter by it
            if($smtp_id){
                $this->email_model->db->where('id', $smtp_id);
            }
            
            $smtp_configs = $this->email_model->db->get('email_smtp_configs')->result();
            
            if(empty($smtp_configs)){
                return [
                    'status' => 'info',
                    'message' => 'No SMTP configurations with IMAP enabled found',
                    'smtp_checked' => 0,
                    'total_bounces' => 0,
                    'total_suppressed' => 0,
                    'time' => date('c')
                ];
            }
            
            $total_bounces = 0;
            $total_suppressed = 0;
            $smtp_checked = 0;
            $results = [];
            
            // Get max emails per check from settings
            $max_emails = (int)$this->email_model->get_setting('imap_max_emails_per_check', 50);
            
            foreach($smtp_configs as $smtp){
                $smtp_result = [
                    'smtp_id' => $smtp->id,
                    'smtp_name' => $smtp->name,
                    'success' => false
                ];
                
                // Connect to IMAP
                if(!$this->imapbouncedetector->connect($smtp)){
                    $smtp_result['error'] = 'Failed to connect to IMAP server';
                    $results[] = $smtp_result;
                    continue;
                }
                
                // Check for bounces
                $bounce_result = $this->imapbouncedetector->check_bounces($max_emails);
                
                // Disconnect
                $this->imapbouncedetector->disconnect();
                
                if($bounce_result['success']){
                    $smtp_checked++;
                    $total_bounces += $bounce_result['bounces_found'];
                    $total_suppressed += $bounce_result['emails_suppressed'];
                    
                    $smtp_result['success'] = true;
                    $smtp_result['processed'] = $bounce_result['processed'];
                    $smtp_result['bounces_found'] = $bounce_result['bounces_found'];
                    $smtp_result['emails_suppressed'] = $bounce_result['emails_suppressed'];
                } else {
                    $smtp_result['error'] = $bounce_result['error'] ?? 'Unknown error';
                }
                
                $results[] = $smtp_result;
            }
            
            return [
                'status' => 'success',
                'message' => sprintf(
                    'Bounce detection completed. Checked %d SMTP config(s), found %d bounce(s), suppressed %d email(s)',
                    $smtp_checked,
                    $total_bounces,
                    $total_suppressed
                ),
                'smtp_checked' => $smtp_checked,
                'total_bounces' => $total_bounces,
                'total_suppressed' => $total_suppressed,
                'results' => $results,
                'time' => date('c')
            ];
            
        } catch(Exception $e){
            log_message('error', 'Bounce Cron: Error - ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error during bounce detection: ' . $e->getMessage(),
                'time' => date('c')
            ];
        }
    }
    
    /**
     * JSON response
     */
    private function respond($data){
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Test endpoint to verify the cron is accessible
     * URL: /cron/bounce_detection/test
     * No authentication required - for testing only
     */
    public function test(){
        $response = [
            'status' => 'success',
            'message' => 'Bounce detection cron endpoint is accessible',
            'controller' => 'Bounce_cron',
            'method' => 'test',
            'url' => current_url(),
            'instructions' => [
                'step1' => 'This test endpoint confirms the route is working',
                'step2' => 'To use the actual cron, you need a valid token',
                'step3' => 'Get your token from the admin panel or use: md5("bounce_detection_cron_" . ENCRYPTION_KEY)',
                'step4' => 'Then call: ' . base_url('cron/bounce_detection?token=YOUR_ACTUAL_TOKEN')
            ],
            'token_help' => [
                'note' => 'Do NOT use "YOUR_TOKEN" literally - it must be the actual generated token',
                'format' => 'The token is a 32-character MD5 hash',
                'generation' => 'Token = md5("bounce_detection_cron_" . ENCRYPTION_KEY)'
            ],
            'time' => date('c')
        ];
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response, JSON_PRETTY_PRINT));
    }
}
