<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing_model extends MY_Model
{
    protected $tb_whatsapp_campaigns = TABLE_WHATSAPP_CAMPAIGNS;
    protected $tb_whatsapp_recipients = TABLE_WHATSAPP_RECIPIENTS;
    protected $tb_whatsapp_logs = TABLE_WHATSAPP_LOGS;
    protected $tb_whatsapp_api_configs = TABLE_WHATSAPP_API_CONFIGS;
    protected $tb_whatsapp_settings = TABLE_WHATSAPP_SETTINGS;
    protected $tb_users = 'general_users';
    protected $tb_orders = 'orders';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ===========================
    // Campaign Management
    // ===========================

    public function get_campaigns($filters = [])
    {
        $this->db->select('c.*');
        $this->db->from($this->tb_whatsapp_campaigns . ' c');
        
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('c.name', $filters['search']);
            $this->db->or_like('c.subject', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('c.id', 'DESC');
        
        $query = $this->db->get();
        $campaigns = $query->result();
        
        // Get statistics for each campaign
        foreach ($campaigns as &$campaign) {
            $campaign->stats = $this->get_campaign_stats($campaign->id);
        }
        
        return $campaigns;
    }

    public function get_campaign($id)
    {
        $this->db->where('id', $id);
        $campaign = $this->db->get($this->tb_whatsapp_campaigns)->row();
        
        if ($campaign) {
            $campaign->stats = $this->get_campaign_stats($id);
            $campaign->api_config = $this->get_api_config($campaign->api_config_id);
        }
        
        return $campaign;
    }

    public function get_campaign_stats($campaign_id)
    {
        // Total recipients
        $this->db->where('campaign_id', $campaign_id);
        $total = $this->db->count_all_results($this->tb_whatsapp_recipients);
        
        // Sent
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'sent');
        $sent = $this->db->count_all_results($this->tb_whatsapp_recipients);
        
        // Failed
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'failed');
        $failed = $this->db->count_all_results($this->tb_whatsapp_recipients);
        
        // Delivered
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'delivered');
        $delivered = $this->db->count_all_results($this->tb_whatsapp_recipients);
        
        // Pending
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'pending');
        $pending = $this->db->count_all_results($this->tb_whatsapp_recipients);
        
        return [
            'total' => $total,
            'sent' => $sent,
            'delivered' => $delivered,
            'failed' => $failed,
            'pending' => $pending,
            'remaining' => $pending
        ];
    }

    public function create_campaign($data)
    {
        $insert_data = [
            'name' => $data['name'],
            'subject' => $data['subject'] ?? '',
            'message' => $data['message'],
            'api_config_id' => $data['api_config_id'],
            'status' => 'Pending',
            'sending_status' => 'Stopped',
            'hourly_limit' => $data['hourly_limit'] ?? 0,
            'daily_limit' => $data['daily_limit'] ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert($this->tb_whatsapp_campaigns, $insert_data);
        return $this->db->insert_id();
    }

    public function update_campaign($id, $data)
    {
        $update_data = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if (isset($data['name'])) $update_data['name'] = $data['name'];
        if (isset($data['subject'])) $update_data['subject'] = $data['subject'];
        if (isset($data['message'])) $update_data['message'] = $data['message'];
        if (isset($data['api_config_id'])) $update_data['api_config_id'] = $data['api_config_id'];
        if (isset($data['status'])) $update_data['status'] = $data['status'];
        if (isset($data['sending_status'])) $update_data['sending_status'] = $data['sending_status'];
        if (isset($data['hourly_limit'])) $update_data['hourly_limit'] = $data['hourly_limit'];
        if (isset($data['daily_limit'])) $update_data['daily_limit'] = $data['daily_limit'];
        
        $this->db->where('id', $id);
        return $this->db->update($this->tb_whatsapp_campaigns, $update_data);
    }

    public function delete_campaign($id)
    {
        // Delete recipients
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->tb_whatsapp_recipients);
        
        // Delete logs
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->tb_whatsapp_logs);
        
        // Delete campaign
        $this->db->where('id', $id);
        return $this->db->delete($this->tb_whatsapp_campaigns);
    }

    // ===========================
    // API Configuration Management
    // ===========================

    public function get_api_configs()
    {
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->tb_whatsapp_api_configs)->result();
    }

    public function get_api_config($id)
    {
        if (empty($id)) {
            // Get default config
            $this->db->where('is_default', 1);
            $config = $this->db->get($this->tb_whatsapp_api_configs)->row();
            
            if (!$config) {
                // Get first config
                $this->db->order_by('id', 'ASC');
                $config = $this->db->get($this->tb_whatsapp_api_configs, 1)->row();
            }
            
            return $config;
        }
        
        $this->db->where('id', $id);
        return $this->db->get($this->tb_whatsapp_api_configs)->row();
    }

    public function get_default_api_config()
    {
        $this->db->where('is_default', 1);
        $config = $this->db->get($this->tb_whatsapp_api_configs)->row();
        
        if (!$config) {
            $this->db->order_by('id', 'ASC');
            $config = $this->db->get($this->tb_whatsapp_api_configs, 1)->row();
        }
        
        return $config;
    }

    public function create_api_config($data)
    {
        // If this is set as default, unset others
        if (!empty($data['is_default'])) {
            $this->db->set('is_default', 0);
            $this->db->update($this->tb_whatsapp_api_configs);
        }
        
        $insert_data = [
            'name' => $data['name'],
            'api_url' => $data['api_url'],
            'api_key' => $data['api_key'],
            'is_default' => !empty($data['is_default']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->insert($this->tb_whatsapp_api_configs, $insert_data);
        return $this->db->insert_id();
    }

    public function update_api_config($id, $data)
    {
        // If this is set as default, unset others
        if (!empty($data['is_default'])) {
            $this->db->where('id !=', $id);
            $this->db->set('is_default', 0);
            $this->db->update($this->tb_whatsapp_api_configs);
        }
        
        $update_data = [];
        
        if (isset($data['name'])) $update_data['name'] = $data['name'];
        if (isset($data['api_url'])) $update_data['api_url'] = $data['api_url'];
        if (isset($data['api_key'])) $update_data['api_key'] = $data['api_key'];
        if (isset($data['is_default'])) $update_data['is_default'] = !empty($data['is_default']) ? 1 : 0;
        
        $this->db->where('id', $id);
        return $this->db->update($this->tb_whatsapp_api_configs, $update_data);
    }

    public function delete_api_config($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->tb_whatsapp_api_configs);
    }

    // ===========================
    // Recipient Management
    // ===========================

    public function get_recipients($campaign_id, $filters = [])
    {
        $this->db->where('campaign_id', $campaign_id);
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('phone', $filters['search']);
            $this->db->or_like('name', $filters['search']);
            $this->db->group_end();
        }
        
        $this->db->order_by('id', 'ASC');
        return $this->db->get($this->tb_whatsapp_recipients)->result();
    }

    public function import_from_users($campaign_id, $filters = [], $limit = 1000)
    {
        try {
            set_time_limit(120);
            
            // Get users with at least 1 order and valid WhatsApp number
            $this->db->select('u.id, u.first_name, u.whatsapp_number, u.balance');
            $this->db->from($this->tb_users . ' u');
            $this->db->where('u.whatsapp_number IS NOT NULL');
            $this->db->where('u.whatsapp_number !=', '');
            $this->db->where('EXISTS (SELECT 1 FROM ' . $this->tb_orders . ' o WHERE o.uid = u.id LIMIT 1)', NULL, FALSE);
            $this->db->limit($limit);
            
            $users = $this->db->get()->result();
            
            if ($this->db->error()['code'] != 0) {
                log_message('error', 'WhatsApp Marketing - Import query error: ' . $this->db->error()['message']);
                return 0;
            }
            
            $imported = 0;
            
            foreach ($users as $user) {
                // Validate phone number
                if (empty($user->whatsapp_number) || !filter_var($user->whatsapp_number, FILTER_SANITIZE_STRING)) {
                    continue;
                }
                
                // Check if already imported
                $this->db->where('campaign_id', $campaign_id);
                $this->db->where('phone', $this->sanitize_phone($user->whatsapp_number));
                $exists = $this->db->count_all_results($this->tb_whatsapp_recipients);
                
                if ($exists > 0) {
                    continue;
                }
                
                // Add recipient
                $recipient_data = [
                    'campaign_id' => $campaign_id,
                    'phone' => $this->sanitize_phone($user->whatsapp_number),
                    'name' => $user->first_name ? $user->first_name : 'User',
                    'status' => 'pending',
                    'custom_data' => json_encode([
                        'balance' => $user->balance ? $user->balance : 0,
                        'user_id' => $user->id
                    ]),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $this->db->insert($this->tb_whatsapp_recipients, $recipient_data);
                $imported++;
            }
            
            return $imported;
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Marketing - Import error: ' . $e->getMessage());
            return 0;
        }
    }

    public function import_from_csv($campaign_id, $csv_data)
    {
        $imported = 0;
        $lines = explode("\n", $csv_data);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = str_getcsv($line);
            $phone = isset($parts[0]) ? trim($parts[0]) : '';
            $name = isset($parts[1]) ? trim($parts[1]) : 'Unknown';
            
            if (empty($phone)) continue;
            
            // Sanitize phone
            $phone = $this->sanitize_phone($phone);
            
            // Check if already imported
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where('phone', $phone);
            $exists = $this->db->count_all_results($this->tb_whatsapp_recipients);
            
            if ($exists > 0) continue;
            
            // Add recipient
            $recipient_data = [
                'campaign_id' => $campaign_id,
                'phone' => $phone,
                'name' => $name,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert($this->tb_whatsapp_recipients, $recipient_data);
            $imported++;
        }
        
        return $imported;
    }

    public function delete_all_recipients($campaign_id)
    {
        $this->db->where('campaign_id', $campaign_id);
        return $this->db->delete($this->tb_whatsapp_recipients);
    }

    // ===========================
    // Message Sending
    // ===========================

    public function get_next_recipient($campaign_id)
    {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        
        return $this->db->get($this->tb_whatsapp_recipients)->row();
    }

    public function update_recipient_status($recipient_id, $status, $error_message = null)
    {
        $update_data = [
            'status' => $status,
            'sent_at' => date('Y-m-d H:i:s')
        ];
        
        if ($error_message) {
            $update_data['error_message'] = $error_message;
        }
        
        $this->db->where('id', $recipient_id);
        return $this->db->update($this->tb_whatsapp_recipients, $update_data);
    }

    public function process_message($message, $recipient)
    {
        // Get custom data
        $custom_data = !empty($recipient->custom_data) ? json_decode($recipient->custom_data, true) : [];
        
        // Get site settings
        $site_name = get_option('site_name', 'Our Site');
        $site_url = base_url();
        
        // Replace placeholders
        $replacements = [
            '{username}' => $recipient->name,
            '{phone}' => $recipient->phone,
            '{balance}' => isset($custom_data['balance']) ? $custom_data['balance'] : '0',
            '{site_name}' => $site_name,
            '{site_url}' => $site_url
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    public function sanitize_phone($phone)
    {
        // Remove + symbol and any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return $phone;
    }

    // ===========================
    // Logging
    // ===========================

    public function log_message($campaign_id, $recipient_id, $status, $response = null, $error = null)
    {
        $log_data = [
            'campaign_id' => $campaign_id,
            'recipient_id' => $recipient_id,
            'status' => $status,
            'response' => $response,
            'error_message' => $error,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->tb_whatsapp_logs, $log_data);
    }

    public function get_logs($campaign_id, $limit = 100)
    {
        $this->db->select('l.*, r.phone, r.name');
        $this->db->from($this->tb_whatsapp_logs . ' l');
        $this->db->join($this->tb_whatsapp_recipients . ' r', 'r.id = l.recipient_id', 'left');
        $this->db->where('l.campaign_id', $campaign_id);
        $this->db->order_by('l.id', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    // ===========================
    // Cron & Sending Limits
    // ===========================

    public function can_send_now($campaign_id, $hourly_limit, $daily_limit)
    {
        $now = time();
        $one_hour_ago = date('Y-m-d H:i:s', $now - 3600);
        $one_day_ago = date('Y-m-d H:i:s', $now - 86400);
        
        // Check hourly limit
        if ($hourly_limit > 0) {
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where('sent_at >=', $one_hour_ago);
            $this->db->where('status', 'sent');
            $sent_last_hour = $this->db->count_all_results($this->tb_whatsapp_recipients);
            
            if ($sent_last_hour >= $hourly_limit) {
                return false;
            }
        }
        
        // Check daily limit
        if ($daily_limit > 0) {
            $this->db->where('campaign_id', $campaign_id);
            $this->db->where('sent_at >=', $one_day_ago);
            $this->db->where('status', 'sent');
            $sent_last_day = $this->db->count_all_results($this->tb_whatsapp_recipients);
            
            if ($sent_last_day >= $daily_limit) {
                return false;
            }
        }
        
        return true;
    }

    public function get_running_campaigns($campaign_id = null)
    {
        $this->db->where('sending_status', 'Started');
        
        if ($campaign_id) {
            $this->db->where('id', $campaign_id);
        }
        
        return $this->db->get($this->tb_whatsapp_campaigns)->result();
    }

    // ===========================
    // Reports
    // ===========================

    public function export_campaign_report($campaign_id)
    {
        $this->db->select('r.phone, r.name, r.status, r.sent_at, r.error_message');
        $this->db->from($this->tb_whatsapp_recipients . ' r');
        $this->db->where('r.campaign_id', $campaign_id);
        $this->db->order_by('r.id', 'ASC');
        
        return $this->db->get()->result_array();
    }
}
