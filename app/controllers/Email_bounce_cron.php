<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Bounce Detection Cron Controller
 * 
 * Automated bounce detection via IMAP
 * URL: /cron/email_bounce?token=YOUR_TOKEN
 */
class Email_bounce_cron extends CI_Controller
{
    private $requiredToken;
    private $lockFile;
    private $minInterval;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('EmailBounceDetector');
        $this->load->library('cron_logger');
        
        // Security token for cron access
        $this->requiredToken = get_option('email_bounce_cron_token', md5('email_bounce_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH . 'cache/email_bounce_cron.lock';
        
        // Minimum interval between runs (in seconds)
        $bounce_interval = $this->email_model->get_setting('bounce_check_interval_minutes', 30);
        $this->minInterval = (int)$bounce_interval * 60;
    }
    
    /**
     * Main cron entry point
     * URL: /cron/email_bounce?token=YOUR_TOKEN
     */
    public function run()
    {
        $log_id = $this->cron_logger->start('cron/email_bounce');
        
        // Verify token
        $token = $this->input->get('token', true);
        if (!$token || !hash_equals($this->requiredToken, $token)) {
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid or missing token');
            show_404();
            return;
        }
        
        // Rate limiting - prevent running too frequently
        if (file_exists($this->lockFile)) {
            $lastRun = (int)@file_get_contents($this->lockFile);
            $now = time();
            if ($lastRun && ($now - $lastRun) < $this->minInterval) {
                $retry_after = $this->minInterval - ($now - $lastRun);
                $this->respond([
                    'status' => 'rate_limited',
                    'message' => 'Bounce detection is rate limited. Please wait.',
                    'retry_after_sec' => $retry_after,
                    'time' => date('c')
                ]);
                return;
            }
        }
        
        // Update lock file
        @file_put_contents($this->lockFile, time());
        
        // Check if bounce detection is enabled
        $enabled = $this->email_model->get_setting('bounce_detection_enabled', 1);
        if (!$enabled) {
            $this->cron_logger->end($log_id, 'Info', 200, 'Bounce detection is disabled');
            $this->respond([
                'status' => 'info',
                'message' => 'Bounce detection is disabled in settings',
                'time' => date('c')
            ]);
            return;
        }
        
        // Run bounce detection
        try {
            $start_time = microtime(true);
            
            $success = $this->emailbouncedetector->run_all();
            
            $duration = round((microtime(true) - $start_time) * 1000, 2);
            
            // Get statistics
            $stats = $this->get_bounce_statistics();
            
            $message = "Bounce detection completed in {$duration}ms. Total suppressed: {$stats['total_suppressed']}";
            
            $this->cron_logger->end($log_id, 'Success', 200, $message);
            
            $this->respond([
                'status' => 'success',
                'message' => 'Bounce detection completed',
                'duration_ms' => $duration,
                'statistics' => $stats,
                'time' => date('c')
            ]);
            
        } catch (Exception $e) {
            $error_message = 'Error: ' . $e->getMessage();
            $this->cron_logger->end($log_id, 'Failed', 500, $error_message);
            
            $this->respond([
                'status' => 'error',
                'message' => $error_message,
                'time' => date('c')
            ]);
        }
    }
    
    /**
     * Get bounce statistics
     */
    private function get_bounce_statistics()
    {
        // Total suppressed emails
        $this->db->where('status', 'active');
        $total_suppressed = $this->db->count_all_results('email_bounces');
        
        // Hard bounces
        $this->db->where('status', 'active');
        $this->db->where('bounce_type', 'hard');
        $hard_bounces = $this->db->count_all_results('email_bounces');
        
        // Soft bounces
        $this->db->where('status', 'active');
        $this->db->where('bounce_type', 'soft');
        $soft_bounces = $this->db->count_all_results('email_bounces');
        
        // Recent bounces (last 24 hours)
        $this->db->where('created_at >', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $recent_bounces = $this->db->count_all_results('email_bounces');
        
        return [
            'total_suppressed' => $total_suppressed,
            'hard_bounces' => $hard_bounces,
            'soft_bounces' => $soft_bounces,
            'recent_24h' => $recent_bounces
        ];
    }
    
    /**
     * JSON response
     */
    private function respond($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
