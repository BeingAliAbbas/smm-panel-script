<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_cron extends CI_Controller
{
    private $cron_token;
    private $lock_file_prefix = '/tmp/whatsapp_marketing_cron_';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('whatsapp_marketing/whatsapp_marketing_model', 'model');
        $this->cron_token = get_option('whatsapp_marketing_cron_token', '');
    }

    public function index()
    {
        // Verify cron token
        $token = $this->input->get('token');
        
        if (empty($this->cron_token) || $token !== $this->cron_token) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing cron token']);
            return;
        }

        // Get campaign ID if specified (for campaign-specific cron)
        $campaign_id = $this->input->get('campaign_id');
        
        // Get running campaigns
        $campaigns = $this->model->get_running_campaigns($campaign_id);
        
        if (empty($campaigns)) {
            echo json_encode(['status' => 'info', 'message' => 'No active WhatsApp campaign found']);
            return;
        }

        $results = [];
        
        foreach ($campaigns as $campaign) {
            $result = $this->process_campaign($campaign);
            $results[] = $result;
        }

        echo json_encode(['status' => 'success', 'results' => $results]);
    }

    private function process_campaign($campaign)
    {
        $lock_file = $this->lock_file_prefix . $campaign->id . '.lock';
        
        // Check if another cron is already processing this campaign
        if (file_exists($lock_file)) {
            $lock_time = filemtime($lock_file);
            
            // If lock file is older than 5 minutes, assume it's stale and remove it
            if (time() - $lock_time > 300) {
                unlink($lock_file);
            } else {
                return ['campaign_id' => $campaign->id, 'status' => 'skipped', 'message' => 'Campaign is being processed by another cron job'];
            }
        }

        // Create lock file
        touch($lock_file);

        try {
            // Check sending limits
            if (!$this->model->can_send_now($campaign->id, $campaign->hourly_limit, $campaign->daily_limit)) {
                unlink($lock_file);
                return ['campaign_id' => $campaign->id, 'status' => 'limited', 'message' => 'Sending limit reached'];
            }

            // Get next recipient
            $recipient = $this->model->get_next_recipient($campaign->id);
            
            if (!$recipient) {
                // No more recipients, mark campaign as completed
                $this->model->update_campaign($campaign->id, [
                    'status' => 'Completed',
                    'sending_status' => 'Stopped'
                ]);
                
                unlink($lock_file);
                return ['campaign_id' => $campaign->id, 'status' => 'completed', 'message' => 'All messages sent'];
            }

            // Get API configuration
            $api_config = $this->model->get_api_config($campaign->api_config_id);
            
            if (!$api_config) {
                unlink($lock_file);
                return ['campaign_id' => $campaign->id, 'status' => 'error', 'message' => 'API configuration not found'];
            }

            // Process message with placeholders
            $processed_message = $this->model->process_message($campaign->message, $recipient);

            // Send message via WhatsApp API
            $result = $this->send_whatsapp_message($api_config, $recipient->phone, $processed_message);

            if ($result['success']) {
                // Update recipient status
                $this->model->update_recipient_status($recipient->id, 'sent');
                
                // Log success
                $this->model->log_message($campaign->id, $recipient->id, 'sent', json_encode($result['response']));
                
                unlink($lock_file);
                return [
                    'campaign_id' => $campaign->id,
                    'status' => 'sent',
                    'message' => 'Message sent successfully',
                    'phone' => $recipient->phone
                ];
            } else {
                // Update recipient status as failed
                $this->model->update_recipient_status($recipient->id, 'failed', $result['error']);
                
                // Log failure
                $this->model->log_message($campaign->id, $recipient->id, 'failed', null, $result['error']);
                
                unlink($lock_file);
                return [
                    'campaign_id' => $campaign->id,
                    'status' => 'failed',
                    'message' => 'Message failed to send',
                    'phone' => $recipient->phone,
                    'error' => $result['error']
                ];
            }

        } catch (Exception $e) {
            unlink($lock_file);
            return ['campaign_id' => $campaign->id, 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function send_whatsapp_message($api_config, $phone, $message)
    {
        try {
            // Prepare API request
            $url = $api_config->api_url;
            
            $data = [
                'apiKey' => $api_config->api_key,
                'phoneNumber' => $phone,
                'message' => $message
            ];

            // Initialize cURL
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Execute request
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                return ['success' => false, 'error' => 'cURL Error: ' . $curl_error];
            }

            if ($http_code !== 200) {
                return ['success' => false, 'error' => 'HTTP Error: ' . $http_code . ' - ' . $response];
            }

            $response_data = json_decode($response, true);
            
            return ['success' => true, 'response' => $response_data];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
