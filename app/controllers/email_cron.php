<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('email_marketing/email_marketing_model', 'email_model');
        $this->load->library('cron_logger');
        $this->load->library('EmailListChecker');
        
        // Load PHPMailer for better deliverability
        require_once APPPATH . 'libraries/PHPMailer/src/PHPMailer.php';
        require_once APPPATH . 'libraries/PHPMailer/src/SMTP.php';
        require_once APPPATH . 'libraries/PHPMailer/src/Exception.php';
        
        // Security token for cron access
        $this->requiredToken = get_option('email_cron_token', md5('email_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/email_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /cron/email_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
     */
    public function run(){
        $log_id = $this->cron_logger->start('cron/email_marketing');
        // Verify token
        $token = $this->input->get('token', true);
        if(!$token || !hash_equals($this->requiredToken, $token)){
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid or missing token');
            show_404();
            return;
        }
        
        // Get optional campaign_id for campaign-specific cron
        $campaign_id = $this->input->get('campaign_id', true);
        
        // Rate limiting - prevent running too frequently
        $lockFileKey = $campaign_id ? 'campaign_' . $campaign_id : 'all';
        $lockFile = APPPATH.'cache/email_cron_' . $lockFileKey . '.lock';
        
        $minInterval = 60; // 60 seconds minimum between runs
        if(file_exists($lockFile)){
            $lastRun = (int)@file_get_contents($lockFile);
            $now = time();
            if($lastRun && ($now - $lastRun) < $minInterval){
                $this->respond([
                    'status' => 'rate_limited',
                    'message' => 'Cron is rate limited. Please wait.',
                    'retry_after_sec' => $minInterval - ($now - $lastRun),
                    'campaign_id' => $campaign_id,
                    'time' => date('c')
                ]);
                return;
            }
        }
        
        // Update lock file
        @file_put_contents($lockFile, time());
        
        // Process emails
        $result = $this->process_emails($campaign_id);
        // Log the result
        $status = ($result['status'] == 'success' || $result['status'] == 'info') ? 'Success' : 'Failed';
        $response_code = ($status == 'Success') ? 200 : 500;
        $message = $result['message'] . ' (Sent: ' . $result['emails_sent'] . ')';
        $this->cron_logger->end($log_id, $status, $response_code, $message);
        
        $this->respond($result);
    }
    
    /**
     * Process pending emails
     * @param string $campaign_id Optional campaign ID to process specific campaign only
     */
    private function process_emails($campaign_id = null){
        // Get running campaigns
        $this->email_model->db->where('status', 'running');
        
        // If campaign_id specified, filter by it
        if($campaign_id){
            $this->email_model->db->where('ids', $campaign_id);
        }
        
        $campaigns = $this->email_model->db->get('email_campaigns')->result();
        
        if(empty($campaigns)){
            return [
                'status' => 'info',
                'message' => $campaign_id ? 'No active campaign found with ID: ' . $campaign_id : 'No active campaign found',
                'campaign_id' => $campaign_id,
                'campaigns_checked' => 0,
                'emails_sent' => 0,
                'time' => date('c')
            ];
        }
        
        $totalSent = 0;
        $campaignsProcessed = 0;
        
        foreach($campaigns as $campaign){
            // Check sending limits
            if(!$this->can_send_email($campaign)){
                continue;
            }
            
            // Get next pending recipient
            $recipient = $this->email_model->get_next_pending_recipient($campaign->id);
            
            if(!$recipient){
                // No more recipients - mark campaign as completed
                $this->email_model->update_campaign($campaign->ids, [
                    'status' => 'completed',
                    'completed_at' => NOW
                ]);
                $campaignsProcessed++;
                continue;
            }
            
            // Send email
            $sent = $this->send_email($campaign, $recipient);
            
            if($sent){
                $totalSent++;
                $campaignsProcessed++;
                
                // Update campaign last sent time
                $this->email_model->update_campaign($campaign->ids, [
                    'last_sent_at' => NOW
                ]);
                
                // Update campaign stats
                $this->email_model->update_campaign_stats($campaign->id);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'Email processing completed',
            'campaign_id' => $campaign_id,
            'campaigns_checked' => count($campaigns),
            'campaigns_processed' => $campaignsProcessed,
            'emails_sent' => $totalSent,
            'time' => date('c')
        ];
    }
    
    /**
     * Check if campaign can send email based on limits
     */
    private function can_send_email($campaign){
        $now = time();
        
        // Check hourly limit
        if($campaign->sending_limit_hourly > 0){
            $hourAgo = date('Y-m-d H:i:s', $now - 3600);
            $this->email_model->db->where('campaign_id', $campaign->id);
            $this->email_model->db->where('sent_at >', $hourAgo);
            $this->email_model->db->where('status', 'sent');
            $sentLastHour = $this->email_model->db->count_all_results('email_recipients');
            
            if($sentLastHour >= $campaign->sending_limit_hourly){
                return false;
            }
        }
        
        // Check daily limit
        if($campaign->sending_limit_daily > 0){
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);
            $this->email_model->db->where('campaign_id', $campaign->id);
            $this->email_model->db->where('sent_at >', $dayAgo);
            $this->email_model->db->where('status', 'sent');
            $sentLastDay = $this->email_model->db->count_all_results('email_recipients');
            
            if($sentLastDay >= $campaign->sending_limit_daily){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Send individual email using PHPMailer for better deliverability
     */
    private function send_email($campaign, $recipient){
        $smtp_config_id = null; // Initialize to track which SMTP is being used
        $validation_start_time = microtime(true);
        
        try {
            // ========================================
            // STEP 0: CHECK SUPPRESSION LIST
            // ========================================
            
            // Check if email is in suppression list (bounced/blocked emails)
            if($this->email_model->is_email_suppressed($recipient->email)){
                $this->log_failed($campaign, $recipient, 'Email is in suppression list (bounced/blocked)', null);
                
                // Mark recipient as suppressed if not already marked
                $this->email_model->db->where('id', $recipient->id);
                $this->email_model->db->update('email_recipients', [
                    'is_suppressed' => 1,
                    'suppression_reason' => 'Found in suppression list',
                    'status' => 'bounced',
                    'updated_at' => NOW
                ]);
                
                return false;
            }
            
            // ========================================
            // STEP 1: EMAIL VALIDATION
            // ========================================
            
            if($this->emaillistchecker->is_enabled()){
                if($recipient->validation_status == 'pending' || empty($recipient->validation_status)){
                    // Validate email
                    $validation_result = $this->emaillistchecker->validate_single_email($recipient->email);

                    // Update recipient with validation result
                    $this->email_model->db->where('id', $recipient->id);
                    $this->email_model->db->update('email_recipients', [
                        'validation_status' => $validation_result['valid'] ? 'valid' : 'invalid',
                        'validation_result' => $validation_result['result'],
                        'validation_reason' => $validation_result['reason'],
                        'validation_score' => $validation_result['score'],
                        'validation_checked_at' => NOW,
                        'validation_error' => $validation_result['error'],
                        'updated_at' => NOW
                    ]);

                    // Use the fresh validation result instead of old $recipient object
                    if(!$validation_result['valid']){
                        $error_msg = 'Email validation failed: ' . ($validation_result['reason'] ?? 'Invalid email');
                        $this->log_failed(
                            $campaign, 
                            $recipient, 
                            $error_msg, 
                            null, 
                            'invalid', 
                            $validation_result['result'], 
                            $validation_result['reason']
                        );
                        return false;
                    }
                    
                    // Refresh recipient object
                    $this->email_model->db->where('id', $recipient->id);
                    $recipient = $this->email_model->db->get('email_recipients')->row();
                }
                
                // Skip if email is invalid
                if($recipient->validation_status == 'invalid'){
                    $error_msg = 'Email validation failed: ' . ($recipient->validation_reason ?: 'Invalid email');
                    $this->log_failed($campaign, $recipient, $error_msg, null, 'invalid', $recipient->validation_result, $recipient->validation_reason);
                    return false;
                }
            }
            
            // ========================================
            // STEP 2: PREPARE EMAIL
            // ========================================
            
            // Get template
            $this->email_model->db->where('id', $campaign->template_id);
            $template = $this->email_model->db->get('email_templates')->row();
            
            if(!$template){
                $this->log_failed($campaign, $recipient, 'Template not found', null);
                return false;
            }
            
            // Get SMTP config with rotation support
            $smtp_config_id = $this->get_next_smtp_config($campaign);
            
            if(!$smtp_config_id){
                $this->log_failed($campaign, $recipient, 'No SMTP configuration available', null);
                return false;
            }
            
            // Get SMTP config
            $this->email_model->db->where('id', $smtp_config_id);
            $smtp = $this->email_model->db->get('email_smtp_configs')->row();
            
            if(!$smtp || $smtp->status != 1){
                $this->log_failed($campaign, $recipient, 'SMTP configuration not found or disabled', $smtp_config_id);
                return false;
            }
            
            // Prepare template variables
            $variables = [];
            
            // Add custom data if available
            if($recipient->custom_data){
                $customData = json_decode($recipient->custom_data, true);
                if(is_array($customData)){
                    $variables = $customData;
                }
            }
            
            // Add default recipient data
            $variables['email'] = $recipient->email;
            $variables['name'] = $recipient->name ?: 'User';
            $variables['username'] = $recipient->name ?: 'User';
            
            // Add tracking link
            $trackingUrl = base_url('email_marketing/track/' . $recipient->tracking_token);
            $variables['tracking_pixel'] = '<img src="' . $trackingUrl . '" width="1" height="1" />';
            
            // Process template
            $subject = $this->email_model->process_template_variables($template->subject, $variables);
            $body = $this->email_model->process_template_variables($template->body, $variables);
            
            // Calculate spam risk score
            $spam_risk_score = $this->email_model->calculate_spam_risk_score($subject, $body);
            
            // Add tracking pixel to body if enabled
            if($this->email_model->get_setting('enable_open_tracking', 1) == 1){
                $body .= $variables['tracking_pixel'];
            }
            
            // Create plain text version (strip HTML tags)
            $altBody = strip_tags($body);
            $altBody = html_entity_decode($altBody, ENT_QUOTES, 'UTF-8');
            $altBody = preg_replace('/\s+/', ' ', $altBody); // Clean up whitespace
            $altBody = trim($altBody);
            
            // ========================================
            // STEP 3: SEND EMAIL WITH PHPMAILER
            // ========================================
            
            $send_start_time = microtime(true);
            
            // Use PHPMailer for better deliverability
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $smtp->host;
            $mail->SMTPAuth = true;
            $mail->Username = $smtp->username;
            $mail->Password = $smtp->password;
            
            // Set encryption
            if($smtp->encryption == 'ssl'){
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = $smtp->port ?: 465;
            } elseif($smtp->encryption == 'tls'){
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $smtp->port ?: 587;
            } else {
                $mail->SMTPSecure = '';
                $mail->Port = $smtp->port ?: 25;
            }
            
            // Recipients
            $mail->setFrom($smtp->from_email, $smtp->from_name);
            $mail->addAddress($recipient->email, $recipient->name ?: '');
            
            // Reply-To
            if($smtp->reply_to){
                $mail->addReplyTo($smtp->reply_to, $smtp->from_name);
            }
            
            // Return-Path (important for deliverability)
            $mail->Sender = $smtp->from_email;
            
            // Custom headers for better deliverability
            $mail->addCustomHeader('X-Mailer', $smtp->from_name . ' Mailer');
            $mail->addCustomHeader('X-Priority', '3'); // Normal priority
            $mail->addCustomHeader('X-MSMail-Priority', 'Normal');
            $mail->addCustomHeader('Importance', 'Normal');
            
            // List-Unsubscribe header (important for bulk email)
            $unsubscribe_url = base_url('email_marketing/unsubscribe/' . $recipient->tracking_token);
            $mail->addCustomHeader('List-Unsubscribe', '<' . $unsubscribe_url . '>');
            $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            
            // Precedence header for bulk emails
            $mail->addCustomHeader('Precedence', 'bulk');
            
            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody; // Plain text alternative
            
            // Send email
            if($mail->send()){
                $send_time_ms = (microtime(true) - $send_start_time) * 1000;
                
                // Update recipient status
                $this->email_model->update_recipient_status($recipient->id, 'sent');
                
                // Add log with SMTP config ID, validation info, and deliverability tracking
                $this->email_model->db->insert('email_logs', [
                    'ids' => ids(),
                    'campaign_id' => (int)$campaign->id,
                    'recipient_id' => (int)$recipient->id,
                    'smtp_config_id' => ($smtp_config_id !== null) ? (int)$smtp_config_id : null,
                    'email' => $recipient->email,
                    'subject' => $subject,
                    'status' => 'sent',
                    'error_message' => null,
                    'validation_status' => $recipient->validation_status ?? null,
                    'validation_result' => $recipient->validation_result ?? null,
                    'validation_reason' => $recipient->validation_reason ?? null,
                    'spam_risk_score' => round($spam_risk_score, 2), // Add spam risk score
                    'has_plain_text' => 1, // We now always add plain text alternative
                    'has_unsubscribe' => 1, // We now always add List-Unsubscribe header
                    'deliverability_status' => 'inbox', // Assume inbox delivery by default
                    'time_taken_ms' => (float)$send_time_ms,
                    'sent_at' => NOW,
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                    'created_at' => NOW
                ]);
                
                return true;
            } else {
                // Get error
                $error = $mail->ErrorInfo;
                $this->log_failed($campaign, $recipient, $error, $smtp_config_id);
                return false;
            }
            
        } catch(\PHPMailer\PHPMailer\Exception $e){
            $this->log_failed($campaign, $recipient, 'PHPMailer Error: ' . $e->getMessage(), $smtp_config_id);
            return false;
        } catch(Exception $e){
            $this->log_failed($campaign, $recipient, $e->getMessage(), $smtp_config_id);
            return false;
        }
    }
    
    /**
     * Log failed email
     */
    private function log_failed($campaign, $recipient, $error, $smtp_config_id = null, $validation_status = null, $validation_result = null, $validation_reason = null){
        // Update recipient status
        $this->email_model->update_recipient_status($recipient->id, 'failed', $error);
        
        // Add log with SMTP config ID and validation info
        $this->email_model->db->insert('email_logs', [
            'ids' => ids(),
            'campaign_id' => (int)$campaign->id,
            'recipient_id' => (int)$recipient->id,
            'smtp_config_id' => ($smtp_config_id !== null) ? (int)$smtp_config_id : null,
            'email' => $recipient->email,
            'subject' => 'Failed',
            'status' => 'failed',
            'error_message' => $error,
            'validation_status' => $validation_status ?? ($recipient->validation_status ?? null),
            'validation_result' => $validation_result ?? ($recipient->validation_result ?? null),
            'validation_reason' => $validation_reason ?? ($recipient->validation_reason ?? null),
            'sent_at' => null,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => NOW
        ]);
    }
    
    /**
     * Get next SMTP config ID using rotation
     * Implements round-robin rotation for multi-SMTP campaigns
     * Falls back to single smtp_config_id for backward compatibility
     * 
     * @param object $campaign Campaign object
     * @return int|null SMTP config ID or null if none available
     */
    private function get_next_smtp_config($campaign){
        // Check if campaign has multiple SMTPs configured (rotation mode)
        if(!empty($campaign->smtp_config_ids)){
            $smtp_ids = json_decode($campaign->smtp_config_ids, true);
            
            // Handle JSON decode errors
            if(json_last_error() !== JSON_ERROR_NONE){
                log_message('error', sprintf(
                    'Email Marketing: Failed to decode smtp_config_ids for campaign %d: %s',
                    $campaign->id,
                    json_last_error_msg()
                ));
                // Fall through to single SMTP fallback
            } elseif(is_array($smtp_ids) && count($smtp_ids) > 0){
                // Get current rotation index
                $current_index = (int)$campaign->smtp_rotation_index;
                
                // Ensure index is within bounds
                if($current_index < 0 || $current_index >= count($smtp_ids)){
                    $current_index = 0;
                }
                
                // Get SMTP ID at current index
                $smtp_id = (int)$smtp_ids[$current_index];
                
                // Calculate next index (round-robin)
                $next_index = ($current_index + 1) % count($smtp_ids);
                
                // Update rotation index in database for next email
                // Note: In high-concurrency scenarios, multiple cron instances may read 
                // the same index. This is acceptable as it only affects SMTP distribution
                // and doesn't cause functional errors. For strict rotation, consider
                // implementing a locking mechanism or atomic increment.
                $this->email_model->update_campaign_rotation_index($campaign->id, $next_index);
                
                return $smtp_id;
            }
        }
        
        // Fallback to single SMTP config (backward compatibility)
        return !empty($campaign->smtp_config_id) ? (int)$campaign->smtp_config_id : null;
    }
    
    /**
     * JSON response
     */
    private function respond($data){
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}