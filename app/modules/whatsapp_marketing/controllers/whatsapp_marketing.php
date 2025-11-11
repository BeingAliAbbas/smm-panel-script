<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class whatsapp_marketing extends MX_Controller {

    public $module_name;
    public $module_icon;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('whatsapp_marketing_model', 'model');
        $this->module_name = 'WhatsApp Marketing';
        $this->module_icon = 'fa fa-whatsapp';
        
        // Check if user is admin
        if (!get_role('admin')) {
            redirect(cn());
        }
    }

    /**
     * Main campaigns list page
     */
    public function index() {
        $page = (int)get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        $limit = 20;
        
        $filters = [];
        if (get('status')) {
            $filters['status'] = get('status');
        }
        if (get('search')) {
            $filters['search'] = get('search');
        }
        
        $campaigns = $this->model->get_campaigns(false, $limit, $page * $limit, $filters);
        $total = $this->model->get_campaigns(true, null, 0, $filters);
        
        // Get stats for each campaign
        foreach ($campaigns as &$campaign) {
            $stats = $this->model->get_campaign_stats($campaign->id);
            $campaign->stats = $stats;
            $campaign->remaining = $campaign->total_recipients - $campaign->sent_count;
        }
        
        $config = [
            'base_url' => cn('whatsapp_marketing'),
            'total_rows' => $total,
            'per_page' => $limit,
            'use_page_numbers' => true,
            'prev_link' => '<i class="fe fe-chevron-left"></i>',
            'first_link' => '<i class="fe fe-chevrons-left"></i>',
            'next_link' => '<i class="fe fe-chevron-right"></i>',
            'last_link' => '<i class="fe fe-chevrons-right"></i>',
        ];
        $this->pagination->initialize($config);
        $links = $this->pagination->create_links();
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'campaigns' => $campaigns,
            'links' => $links
        ];
        
        $this->template->build('index', $data);
    }

    /**
     * Create new campaign
     */
    public function create() {
        if ($this->input->post()) {
            $this->_save_campaign();
            return;
        }
        
        $api_configs = $this->model->get_api_configs();
        $users_count = $this->model->count_users_with_whatsapp();
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'api_configs' => $api_configs,
            'users_count' => $users_count
        ];
        
        $this->template->build('create', $data);
    }

    /**
     * Edit campaign
     */
    public function edit($id = null) {
        if (!$id) {
            redirect(cn('whatsapp_marketing'));
        }
        
        $campaign = $this->model->get_campaign($id);
        if (!$campaign) {
            redirect(cn('whatsapp_marketing'));
        }
        
        if ($this->input->post()) {
            $this->_save_campaign($id);
            return;
        }
        
        $api_configs = $this->model->get_api_configs();
        $recipients_count = $this->model->count_recipients($id);
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'campaign' => $campaign,
            'api_configs' => $api_configs,
            'recipients_count' => $recipients_count
        ];
        
        $this->template->build('edit', $data);
    }

    /**
     * Save campaign (create or update)
     */
    private function _save_campaign($id = null) {
        $campaign_name = $this->input->post('campaign_name');
        $message_content = $this->input->post('message_content');
        $api_config_id = $this->input->post('api_config_id');
        $recipient_source = $this->input->post('recipient_source');
        $limit_per_hour = $this->input->post('limit_per_hour');
        $limit_per_day = $this->input->post('limit_per_day');
        $retry_failed = $this->input->post('retry_failed') ? 1 : 0;
        
        if (empty($campaign_name) || empty($message_content) || empty($api_config_id)) {
            ms([
                'status' => 'error',
                'message' => 'Please fill all required fields'
            ]);
        }
        
        $campaign_data = [
            'campaign_name' => $campaign_name,
            'message_content' => $message_content,
            'api_config_id' => $api_config_id,
            'limit_per_hour' => $limit_per_hour ? $limit_per_hour : null,
            'limit_per_day' => $limit_per_day ? $limit_per_day : null,
            'retry_failed' => $retry_failed,
        ];
        
        if ($id) {
            // Update existing campaign
            $this->model->update_campaign($id, $campaign_data);
            $campaign_id = $id;
            $message = 'Campaign updated successfully';
        } else {
            // Create new campaign
            $this->model->create_campaign($campaign_data);
            $campaign_id = $this->db->insert_id();
            $message = 'Campaign created successfully';
            
            // Add recipients
            $recipients = [];
            
            if ($recipient_source == 'database') {
                // Get all users with WhatsApp numbers
                $users = $this->model->get_users_with_whatsapp();
                foreach ($users as $user) {
                    // Remove + sign from phone number
                    $phone = str_replace('+', '', $user->whatsapp_number);
                    
                    $recipients[] = [
                        'user_id' => $user->id,
                        'phone_number' => $phone,
                        'username' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'balance' => $user->balance,
                        'source' => 'database'
                    ];
                }
            } elseif ($recipient_source == 'import') {
                // Handle file upload
                if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
                    $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
                    $lines = explode("\n", $file_content);
                    
                    $seen_numbers = [];
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;
                        
                        // Remove + sign and any non-numeric characters except commas
                        $parts = str_getcsv($line);
                        $phone = preg_replace('/[^0-9]/', '', $parts[0]);
                        
                        if (!empty($phone) && !in_array($phone, $seen_numbers)) {
                            $seen_numbers[] = $phone;
                            $recipients[] = [
                                'phone_number' => $phone,
                                'username' => isset($parts[1]) ? $parts[1] : '',
                                'email' => isset($parts[2]) ? $parts[2] : '',
                                'balance' => 0,
                                'source' => 'import'
                            ];
                        }
                    }
                }
            }
            
            // Update total recipients count
            $total_recipients = count($recipients);
            $this->model->update_campaign($campaign_id, ['total_recipients' => $total_recipients]);
            
            // Add recipients to database
            if (!empty($recipients)) {
                $this->model->add_recipients($campaign_id, $recipients);
            }
        }
        
        ms([
            'status' => 'success',
            'message' => $message,
            'redirect' => cn('whatsapp_marketing')
        ]);
    }

    /**
     * View campaign details
     */
    public function view($id = null) {
        if (!$id) {
            redirect(cn('whatsapp_marketing'));
        }
        
        $campaign = $this->model->get_campaign($id);
        if (!$campaign) {
            redirect(cn('whatsapp_marketing'));
        }
        
        $stats = $this->model->get_campaign_stats($id);
        $recipients = $this->model->get_recipients($id, 10);
        
        $page = (int)get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        $limit = 50;
        
        $filters = [];
        if (get('status')) {
            $filters['status'] = get('status');
        }
        
        $messages = $this->model->get_messages($id, $filters, $limit, $page * $limit);
        $total_messages = $this->model->count_messages($id, $filters);
        
        $config = [
            'base_url' => cn('whatsapp_marketing/view/' . $id),
            'total_rows' => $total_messages,
            'per_page' => $limit,
            'use_page_numbers' => true,
            'prev_link' => '<i class="fe fe-chevron-left"></i>',
            'first_link' => '<i class="fe fe-chevrons-left"></i>',
            'next_link' => '<i class="fe fe-chevron-right"></i>',
            'last_link' => '<i class="fe fe-chevrons-right"></i>',
        ];
        $this->pagination->initialize($config);
        $links = $this->pagination->create_links();
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => $this->module_name,
            'module_icon' => $this->module_icon,
            'campaign' => $campaign,
            'stats' => $stats,
            'recipients' => $recipients,
            'messages' => $messages,
            'links' => $links
        ];
        
        $this->template->build('view', $data);
    }

    /**
     * Start campaign
     */
    public function start($id = null) {
        if (!$id) {
            ms(['status' => 'error', 'message' => 'Invalid campaign ID']);
        }
        
        $campaign = $this->model->get_campaign($id);
        if (!$campaign) {
            ms(['status' => 'error', 'message' => 'Campaign not found']);
        }
        
        // Prepare messages if not already done
        $existing_messages = $this->model->count_messages($id, []);
        if ($existing_messages == 0) {
            $this->model->prepare_campaign_messages($id);
        }
        
        // Update campaign status to running
        $this->model->update_campaign($id, [
            'status' => 'running',
            'started_at' => date('Y-m-d H:i:s')
        ]);
        
        ms([
            'status' => 'success',
            'message' => 'Campaign started successfully',
            'redirect' => cn('whatsapp_marketing')
        ]);
    }

    /**
     * Pause campaign
     */
    public function pause($id = null) {
        if (!$id) {
            ms(['status' => 'error', 'message' => 'Invalid campaign ID']);
        }
        
        $this->model->update_campaign($id, ['status' => 'paused']);
        
        ms([
            'status' => 'success',
            'message' => 'Campaign paused successfully',
            'redirect' => cn('whatsapp_marketing')
        ]);
    }

    /**
     * Resume campaign
     */
    public function resume($id = null) {
        if (!$id) {
            ms(['status' => 'error', 'message' => 'Invalid campaign ID']);
        }
        
        $this->model->update_campaign($id, ['status' => 'running']);
        
        ms([
            'status' => 'success',
            'message' => 'Campaign resumed successfully',
            'redirect' => cn('whatsapp_marketing')
        ]);
    }

    /**
     * Delete campaign
     */
    public function delete($ids = null) {
        if (!$ids) {
            ms(['status' => 'error', 'message' => 'Invalid campaign ID']);
        }
        
        $campaign = $this->model->get_campaign_by_ids($ids);
        if (!$campaign) {
            ms(['status' => 'error', 'message' => 'Campaign not found']);
        }
        
        $this->model->delete_campaign($campaign->id);
        
        ms([
            'status' => 'success',
            'message' => 'Campaign deleted successfully',
            'redirect' => cn('whatsapp_marketing')
        ]);
    }

    /**
     * API Configuration Management
     */
    public function api_config() {
        $page = (int)get("p");
        $page = ($page > 0) ? ($page - 1) : 0;
        $limit = 20;
        
        $configs = $this->model->get_api_configs(false, $limit, $page * $limit);
        $total = $this->model->get_api_configs(true);
        
        $config = [
            'base_url' => cn('whatsapp_marketing/api_config'),
            'total_rows' => $total,
            'per_page' => $limit,
            'use_page_numbers' => true,
            'prev_link' => '<i class="fe fe-chevron-left"></i>',
            'first_link' => '<i class="fe fe-chevrons-left"></i>',
            'next_link' => '<i class="fe fe-chevron-right"></i>',
            'last_link' => '<i class="fe fe-chevrons-right"></i>',
        ];
        $this->pagination->initialize($config);
        $links = $this->pagination->create_links();
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => 'WhatsApp API Configuration',
            'module_icon' => $this->module_icon,
            'configs' => $configs,
            'links' => $links
        ];
        
        $this->template->build('api_config', $data);
    }

    /**
     * Create API config
     */
    public function api_config_create() {
        if ($this->input->post()) {
            $profile_name = $this->input->post('profile_name');
            $api_endpoint = $this->input->post('api_endpoint');
            $api_key = $this->input->post('api_key');
            
            if (empty($profile_name) || empty($api_key)) {
                ms(['status' => 'error', 'message' => 'Please fill all required fields']);
            }
            
            $this->model->create_api_config([
                'profile_name' => $profile_name,
                'api_endpoint' => $api_endpoint ? $api_endpoint : 'http://waapi.beastsmm.pk/send-message',
                'api_key' => $api_key,
                'status' => 1
            ]);
            
            ms([
                'status' => 'success',
                'message' => 'API configuration created successfully',
                'redirect' => cn('whatsapp_marketing/api_config')
            ]);
        }
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => 'Create API Configuration',
            'module_icon' => $this->module_icon
        ];
        
        $this->template->build('api_config_form', $data);
    }

    /**
     * Edit API config
     */
    public function api_config_edit($id = null) {
        if (!$id) {
            redirect(cn('whatsapp_marketing/api_config'));
        }
        
        $config = $this->model->get_api_config($id);
        if (!$config) {
            redirect(cn('whatsapp_marketing/api_config'));
        }
        
        if ($this->input->post()) {
            $profile_name = $this->input->post('profile_name');
            $api_endpoint = $this->input->post('api_endpoint');
            $api_key = $this->input->post('api_key');
            
            if (empty($profile_name) || empty($api_key)) {
                ms(['status' => 'error', 'message' => 'Please fill all required fields']);
            }
            
            $this->model->update_api_config($id, [
                'profile_name' => $profile_name,
                'api_endpoint' => $api_endpoint,
                'api_key' => $api_key
            ]);
            
            ms([
                'status' => 'success',
                'message' => 'API configuration updated successfully',
                'redirect' => cn('whatsapp_marketing/api_config')
            ]);
        }
        
        $data = [
            'module' => 'whatsapp_marketing',
            'module_name' => 'Edit API Configuration',
            'module_icon' => $this->module_icon,
            'config' => $config
        ];
        
        $this->template->build('api_config_form', $data);
    }

    /**
     * Delete API config
     */
    public function api_config_delete($id = null) {
        if (!$id) {
            ms(['status' => 'error', 'message' => 'Invalid configuration ID']);
        }
        
        $this->model->delete_api_config($id);
        
        ms([
            'status' => 'success',
            'message' => 'API configuration deleted successfully',
            'redirect' => cn('whatsapp_marketing/api_config')
        ]);
    }

    /**
     * Cron job to send messages
     */
    public function cron() {
        // Get active campaigns
        $campaigns = $this->model->get_active_campaigns();
        
        if (empty($campaigns)) {
            echo json_encode([
                'status' => 'info',
                'message' => 'No active campaign found.'
            ]);
            return;
        }
        
        $sent_count = 0;
        $failed_count = 0;
        
        foreach ($campaigns as $campaign) {
            // Check rate limits
            $can_send = $this->_check_rate_limits($campaign);
            if (!$can_send) {
                continue;
            }
            
            // Get next pending message
            $messages = $this->model->get_pending_messages($campaign->id, 1);
            
            if (empty($messages)) {
                // No more pending messages, mark campaign as completed
                $this->model->update_campaign($campaign->id, [
                    'status' => 'completed',
                    'completed_at' => date('Y-m-d H:i:s')
                ]);
                continue;
            }
            
            foreach ($messages as $message) {
                // Send message via WhatsApp API
                $result = $this->_send_whatsapp_message(
                    $campaign->api_endpoint,
                    $campaign->api_key,
                    $message->phone_number,
                    $message->message_content
                );
                
                if ($result['status'] == 'success') {
                    // Update message status
                    $this->model->update_message($message->id, [
                        'status' => 'sent',
                        'sent_at' => date('Y-m-d H:i:s'),
                        'api_response' => json_encode($result)
                    ]);
                    
                    // Update campaign counters
                    $this->model->update_campaign($campaign->id, [
                        'sent_count' => $campaign->sent_count + 1
                    ]);
                    
                    $sent_count++;
                } else {
                    // Update message as failed
                    $retry_count = $message->retry_count + 1;
                    $status = ($retry_count >= $campaign->max_retries) ? 'failed' : 'pending';
                    
                    $this->model->update_message($message->id, [
                        'status' => $status,
                        'retry_count' => $retry_count,
                        'error_message' => $result['message'],
                        'api_response' => json_encode($result)
                    ]);
                    
                    if ($status == 'failed') {
                        $this->model->update_campaign($campaign->id, [
                            'failed_count' => $campaign->failed_count + 1
                        ]);
                        $failed_count++;
                    }
                }
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => "Sent: {$sent_count}, Failed: {$failed_count}"
        ]);
    }

    /**
     * Check rate limits for campaign
     */
    private function _check_rate_limits($campaign) {
        // If no limits set, allow sending
        if (!$campaign->limit_per_hour && !$campaign->limit_per_day) {
            return true;
        }
        
        $now = date('Y-m-d H:i:s');
        
        // Check hourly limit
        if ($campaign->limit_per_hour) {
            $hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
            $sent_last_hour = $this->model->count_messages($campaign->id, [
                'status' => 'sent',
                'date_from' => $hour_ago,
                'date_to' => $now
            ]);
            
            if ($sent_last_hour >= $campaign->limit_per_hour) {
                return false;
            }
        }
        
        // Check daily limit
        if ($campaign->limit_per_day) {
            $day_start = date('Y-m-d 00:00:00');
            $sent_today = $this->model->count_messages($campaign->id, [
                'status' => 'sent',
                'date_from' => $day_start,
                'date_to' => $now
            ]);
            
            if ($sent_today >= $campaign->limit_per_day) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Send WhatsApp message via API
     */
    private function _send_whatsapp_message($api_endpoint, $api_key, $phone_number, $message) {
        try {
            $data = [
                'apiKey' => $api_key,
                'phoneNumber' => $phone_number,
                'message' => $message
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_endpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code == 200) {
                return [
                    'status' => 'success',
                    'message' => 'Message sent successfully',
                    'response' => $response
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'API returned error: ' . $http_code,
                    'response' => $response
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export campaign logs
     */
    public function export($id = null, $format = 'csv') {
        if (!$id) {
            redirect(cn('whatsapp_marketing'));
        }
        
        $campaign = $this->model->get_campaign($id);
        if (!$campaign) {
            redirect(cn('whatsapp_marketing'));
        }
        
        $messages = $this->model->get_messages($id);
        
        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="campaign_' . $id . '_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Phone Number', 'Username', 'Email', 'Status', 'Sent At', 'Error Message']);
            
            foreach ($messages as $msg) {
                fputcsv($output, [
                    $msg->id,
                    $msg->phone_number,
                    $msg->username,
                    $msg->email,
                    $msg->status,
                    $msg->sent_at,
                    $msg->error_message
                ]);
            }
            
            fclose($output);
        }
    }
}
