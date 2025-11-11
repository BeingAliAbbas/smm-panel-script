<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_cron extends CI_Controller {
    
    private $requiredToken;
    private $lockFile;
    
    public function __construct(){
        parent::__construct();
        $this->load->model('whatsapp_marketing/whatsapp_marketing_model', 'whatsapp_model');
        
        // Security token for cron access
        $this->requiredToken = get_option('whatsapp_cron_token', md5('whatsapp_marketing_cron_' . ENCRYPTION_KEY));
        $this->lockFile = APPPATH.'cache/whatsapp_cron_last_run.lock';
    }
    
    /**
     * Main cron entry point
     * URL: /cron/whatsapp_marketing?token=YOUR_TOKEN&campaign_id=CAMPAIGN_ID (optional)
     */
    public function run(){
        // Verify token
        $token = $this->input->get('token', true);
        if(!$token || !hash_equals($this->requiredToken, $token)){
            show_404();
            return;
        }
        
        // Get optional campaign_id for campaign-specific cron
        $campaign_id = $this->input->get('campaign_id', true);
        
        // Rate limiting - prevent running too frequently
        $lockFileKey = $campaign_id ? 'campaign_' . $campaign_id : 'all';
        $lockFile = APPPATH.'cache/whatsapp_cron_' . $lockFileKey . '.lock';
        
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
        
        // Process messages
        $result = $this->process_messages($campaign_id);
        
        $this->respond($result);
    }
    
    /**
     * Process pending WhatsApp messages
     * @param string $campaign_id Optional campaign ID to process specific campaign only
     */
    private function process_messages($campaign_id = null){
        // Get running campaigns
        $this->whatsapp_model->db->where('status', 'running');
        
        // If campaign_id specified, filter by it
        if($campaign_id){
            $this->whatsapp_model->db->where('ids', $campaign_id);
        }
        
        $campaigns = $this->whatsapp_model->db->get('whatsapp_campaigns')->result();
        
        if(empty($campaigns)){
            return [
                'status' => 'info',
                'message' => $campaign_id ? 'No active campaign found with ID: ' . $campaign_id : 'No active campaign found',
                'campaign_id' => $campaign_id,
                'campaigns_checked' => 0,
                'messages_sent' => 0,
                'time' => date('c')
            ];
        }
        
        $totalSent = 0;
        $campaignsProcessed = 0;
        
        foreach($campaigns as $campaign){
            // Check sending limits
            if(!$this->can_send_message($campaign)){
                continue;
            }
            
            // Get next pending recipient
            $recipient = $this->whatsapp_model->get_next_pending_recipient($campaign->id);
            
            if(!$recipient){
                // No more recipients - mark campaign as completed
                $this->whatsapp_model->update_campaign($campaign->ids, [
                    'status' => 'completed',
                    'completed_at' => NOW
                ]);
                $campaignsProcessed++;
                continue;
            }
            
            // Send message
            $sent = $this->send_message($campaign, $recipient);
            
            if($sent){
                $totalSent++;
                $campaignsProcessed++;
                
                // Update campaign last sent time
                $this->whatsapp_model->update_campaign($campaign->ids, [
                    'last_sent_at' => NOW
                ]);
                
                // Update campaign stats
                $this->whatsapp_model->update_campaign_stats($campaign->id);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'WhatsApp message processing completed',
            'campaign_id' => $campaign_id,
            'campaigns_checked' => count($campaigns),
            'campaigns_processed' => $campaignsProcessed,
            'messages_sent' => $totalSent,
            'time' => date('c')
        ];
    }
    
    /**
     * Check if campaign can send message based on limits
     */
    private function can_send_message($campaign){
        $now = time();
        
        // Check hourly limit
        if($campaign->sending_limit_hourly > 0){
            $hourAgo = date('Y-m-d H:i:s', $now - 3600);
            $this->whatsapp_model->db->where('campaign_id', $campaign->id);
            $this->whatsapp_model->db->where('sent_at >', $hourAgo);
            $this->whatsapp_model->db->where('status', 'sent');
            $sentLastHour = $this->whatsapp_model->db->count_all_results('whatsapp_recipients');
            
            if($sentLastHour >= $campaign->sending_limit_hourly){
                return false;
            }
        }
        
        // Check daily limit
        if($campaign->sending_limit_daily > 0){
            $dayAgo = date('Y-m-d H:i:s', $now - 86400);
            $this->whatsapp_model->db->where('campaign_id', $campaign->id);
            $this->whatsapp_model->db->where('sent_at >', $dayAgo);
            $this->whatsapp_model->db->where('status', 'sent');
            $sentLastDay = $this->whatsapp_model->db->count_all_results('whatsapp_recipients');
            
            if($sentLastDay >= $campaign->sending_limit_daily){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Send individual WhatsApp message
     */
    private function send_message($campaign, $recipient){
        try {
            // Get API config
            $this->whatsapp_model->db->where('id', $campaign->api_config_id);
            $api_config = $this->whatsapp_model->db->get('whatsapp_api_configs')->row();
            
            if(!$api_config || $api_config->status != 1){
                $this->log_failed($campaign, $recipient, 'API configuration not found or disabled');
                return false;
            }
            
            // Prepare message variables
            $variables = [];
            
            // Add custom data if available
            if($recipient->custom_data){
                $customData = json_decode($recipient->custom_data, true);
                if(is_array($customData)){
                    $variables = $customData;
                }
            }
            
            // Add default recipient data
            $variables['phone'] = $recipient->phone_number;
            $variables['phone_number'] = $recipient->phone_number;
            $variables['name'] = $recipient->name ?: 'User';
            $variables['username'] = $recipient->name ?: 'User';
            
            // If user_id exists, fetch additional data from general_users
            if($recipient->user_id){
                $this->whatsapp_model->db->where('id', $recipient->user_id);
                $user = $this->whatsapp_model->db->get('general_users')->row();
                
                if($user){
                    $variables['username'] = trim($user->first_name . ' ' . $user->last_name);
                    $variables['balance'] = $user->balance;
                    $variables['email'] = $user->email;
                }
            }
            
            // Process message with variables
            $message = $this->whatsapp_model->process_message_variables($campaign->message, $variables);
            
            // Prepare API request
            $postData = array(
                'apiKey' => $api_config->api_key,
                'phoneNumber' => $recipient->phone_number,
                'message' => $message
            );
            
            // Send API request
            $ch = curl_init($api_config->api_endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Check for curl errors
            if($curlError){
                $this->log_failed($campaign, $recipient, 'CURL Error: ' . $curlError);
                return false;
            }
            
            // Check HTTP status
            if($httpCode >= 200 && $httpCode < 300){
                // Success
                $this->whatsapp_model->update_recipient_status($recipient->id, 'sent');
                
                // Add log
                $this->whatsapp_model->add_log(
                    $campaign->id,
                    $recipient->id,
                    $recipient->phone_number,
                    $message,
                    'sent',
                    null,
                    $response
                );
                
                return true;
            } else {
                // API returned error
                $error = "HTTP $httpCode: " . substr($response, 0, 500);
                $this->log_failed($campaign, $recipient, $error, $response);
                return false;
            }
            
        } catch(Exception $e){
            $this->log_failed($campaign, $recipient, $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log failed message
     */
    private function log_failed($campaign, $recipient, $error, $api_response = null){
        // Update recipient status
        $this->whatsapp_model->update_recipient_status($recipient->id, 'failed', $error);
        
        // Add log
        $this->whatsapp_model->add_log(
            $campaign->id,
            $recipient->id,
            $recipient->phone_number,
            'Failed to send',
            'failed',
            $error,
            $api_response
        );
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
