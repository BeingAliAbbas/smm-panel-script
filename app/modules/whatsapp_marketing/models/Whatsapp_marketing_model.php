<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing_model extends MY_Model {
    
    protected $tb_campaigns;
    protected $tb_api_configs;
    protected $tb_recipients;
    protected $tb_logs;
    protected $tb_settings;
    
    public function __construct(){
        parent::__construct();
        
        // Define table names
        $this->tb_campaigns = 'whatsapp_campaigns';
        $this->tb_api_configs = 'whatsapp_api_configs';
        $this->tb_recipients = 'whatsapp_recipients';
        $this->tb_logs = 'whatsapp_logs';
        $this->tb_settings = 'whatsapp_settings';
    }
    
    // ========================================
    // CAMPAIGN METHODS
    // ========================================
    
    public function get_campaigns($limit = -1, $page = -1, $status = null) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('c.*, a.name as api_name');
        }
        
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_api_configs . ' a', 'c.api_config_id = a.id', 'left');
        
        if ($status !== null) {
            $this->db->where('c.status', $status);
        }
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('c.created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_campaign($ids) {
        $this->db->select('c.*, a.name as api_name');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_api_configs . ' a', 'c.api_config_id = a.id', 'left');
        $this->db->where('c.ids', $ids);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_campaign($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        return $this->db->insert($this->tb_campaigns, $data);
    }
    
    public function update_campaign($ids, $data) {
        $data['updated_at'] = NOW;
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_campaigns, $data);
    }
    
    public function delete_campaign($ids) {
        // Get campaign to delete related data
        $campaign = $this->get_campaign($ids);
        if ($campaign) {
            // Delete recipients
            $this->db->where('campaign_id', $campaign->id);
            $this->db->delete($this->tb_recipients);
            
            // Delete logs
            $this->db->where('campaign_id', $campaign->id);
            $this->db->delete($this->tb_logs);
            
            // Delete campaign
            $this->db->where('ids', $ids);
            return $this->db->delete($this->tb_campaigns);
        }
        return false;
    }
    
    public function update_campaign_stats($campaign_id) {
        $this->db->select("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered
        ");
        $this->db->from($this->tb_recipients);
        $this->db->where('campaign_id', $campaign_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $stats = $query->row();
            $this->db->where('id', $campaign_id);
            $this->db->update($this->tb_campaigns, [
                'total_messages' => $stats->total,
                'sent_messages' => $stats->sent,
                'failed_messages' => $stats->failed,
                'delivered_messages' => $stats->delivered,
                'updated_at' => NOW
            ]);
            return true;
        }
        return false;
    }
    
    // ========================================
    // API CONFIG METHODS
    // ========================================
    
    public function get_api_configs($limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_api_configs);
        $this->db->where('status', 1);
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_api_config($ids) {
        $this->db->where('ids', $ids);
        $query = $this->db->get($this->tb_api_configs);
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function create_api_config($data) {
        $data['ids'] = ids();
        $data['created_at'] = NOW;
        $data['updated_at'] = NOW;
        
        return $this->db->insert($this->tb_api_configs, $data);
    }
    
    public function update_api_config($ids, $data) {
        $data['updated_at'] = NOW;
        $this->db->where('ids', $ids);
        return $this->db->update($this->tb_api_configs, $data);
    }
    
    public function delete_api_config($ids) {
        $this->db->where('ids', $ids);
        return $this->db->delete($this->tb_api_configs);
    }
    
    // ========================================
    // RECIPIENT METHODS
    // ========================================
    
    public function add_recipients($campaign_id, $recipients) {
        if (empty($recipients)) {
            return false;
        }
        
        $inserted = 0;
        foreach ($recipients as $recipient) {
            $data = array(
                'ids' => ids(),
                'campaign_id' => $campaign_id,
                'phone_number' => $recipient['phone_number'],
                'name' => isset($recipient['name']) ? $recipient['name'] : null,
                'user_id' => isset($recipient['user_id']) ? $recipient['user_id'] : null,
                'custom_data' => isset($recipient['custom_data']) ? json_encode($recipient['custom_data']) : null,
                'status' => 'pending',
                'created_at' => NOW,
                'updated_at' => NOW
            );
            
            if ($this->db->insert($this->tb_recipients, $data)) {
                $inserted++;
            }
        }
        
        return $inserted;
    }
    
    public function get_recipients($campaign_id, $limit = -1, $page = -1, $status = null) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_recipients);
        $this->db->where('campaign_id', $campaign_id);
        
        if ($status !== null) {
            $this->db->where('status', $status);
        }
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('created_at', 'ASC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    public function get_next_pending_recipient($campaign_id) {
        $this->db->where('campaign_id', $campaign_id);
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get($this->tb_recipients);
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    public function update_recipient_status($recipient_id, $status, $error = null) {
        $data = array(
            'status' => $status,
            'updated_at' => NOW
        );
        
        if ($status == 'sent') {
            $data['sent_at'] = NOW;
        } elseif ($status == 'delivered') {
            $data['delivered_at'] = NOW;
        }
        
        if ($error) {
            $data['error_message'] = $error;
        }
        
        $this->db->where('id', $recipient_id);
        return $this->db->update($this->tb_recipients, $data);
    }
    
    public function import_from_general_users($campaign_id) {
        // Fetch all users with WhatsApp numbers
        $this->db->select('id, first_name, last_name, whatsapp_number, balance');
        $this->db->from('general_users');
        $this->db->where('whatsapp_number IS NOT NULL');
        $this->db->where('whatsapp_number !=', '');
        $this->db->where('status', 1);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return 0;
        }
        
        $recipients = [];
        foreach ($query->result() as $user) {
            // Sanitize phone number (remove + symbol)
            $phone = $this->sanitize_phone_number($user->whatsapp_number);
            if (empty($phone)) {
                continue;
            }
            
            $recipients[] = array(
                'phone_number' => $phone,
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'user_id' => $user->id,
                'custom_data' => array(
                    'username' => trim($user->first_name . ' ' . $user->last_name),
                    'balance' => $user->balance
                )
            );
        }
        
        // Remove duplicates by phone number
        $recipients = $this->remove_duplicate_recipients($recipients);
        
        return $this->add_recipients($campaign_id, $recipients);
    }
    
    public function import_from_csv($campaign_id, $file_path) {
        if (!file_exists($file_path)) {
            return array('success' => false, 'message' => 'File not found');
        }
        
        $recipients = [];
        $row = 0;
        
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                
                // Skip header row
                if ($row == 1 && (strtolower($data[0]) == 'phone' || strtolower($data[0]) == 'phone_number')) {
                    continue;
                }
                
                // Expecting format: phone_number, name (optional)
                $phone = isset($data[0]) ? $this->sanitize_phone_number($data[0]) : '';
                if (empty($phone)) {
                    continue;
                }
                
                $recipients[] = array(
                    'phone_number' => $phone,
                    'name' => isset($data[1]) ? trim($data[1]) : null,
                    'user_id' => null,
                    'custom_data' => null
                );
            }
            fclose($handle);
        }
        
        if (empty($recipients)) {
            return array('success' => false, 'message' => 'No valid phone numbers found');
        }
        
        // Remove duplicates
        $recipients = $this->remove_duplicate_recipients($recipients);
        
        $inserted = $this->add_recipients($campaign_id, $recipients);
        
        return array(
            'success' => true,
            'message' => $inserted . ' recipients added successfully',
            'count' => $inserted
        );
    }
    
    // ========================================
    // LOG METHODS
    // ========================================
    
    public function add_log($campaign_id, $recipient_id, $phone_number, $message, $status, $error = null, $api_response = null) {
        $data = array(
            'ids' => ids(),
            'campaign_id' => $campaign_id,
            'recipient_id' => $recipient_id,
            'phone_number' => $phone_number,
            'message' => $message,
            'status' => $status,
            'error_message' => $error,
            'api_response' => $api_response,
            'created_at' => NOW
        );
        
        if ($status == 'sent' || $status == 'delivered') {
            $data['sent_at'] = NOW;
        }
        
        if ($status == 'delivered') {
            $data['delivered_at'] = NOW;
        }
        
        return $this->db->insert($this->tb_logs, $data);
    }
    
    public function get_logs($campaign_id, $limit = -1, $page = -1) {
        if ($limit == -1) {
            $this->db->select('count(*) as sum');
        } else {
            $this->db->select('*');
        }
        
        $this->db->from($this->tb_logs);
        $this->db->where('campaign_id', $campaign_id);
        
        if ($limit != -1) {
            $this->db->limit($limit, $page);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            if ($limit == -1) {
                return $query->row()->sum;
            } else {
                return $query->result();
            }
        }
        
        return ($limit == -1) ? 0 : [];
    }
    
    // ========================================
    // SETTINGS METHODS
    // ========================================
    
    public function get_setting($key, $default = null) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->tb_settings);
        
        if ($query->num_rows() > 0) {
            return $query->row()->setting_value;
        }
        
        return $default;
    }
    
    public function set_setting($key, $value) {
        $this->db->where('setting_key', $key);
        $query = $this->db->get($this->tb_settings);
        
        if ($query->num_rows() > 0) {
            $this->db->where('setting_key', $key);
            return $this->db->update($this->tb_settings, array('setting_value' => $value));
        } else {
            return $this->db->insert($this->tb_settings, array(
                'setting_key' => $key,
                'setting_value' => $value
            ));
        }
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    public function sanitize_phone_number($phone) {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + symbol as per requirement
        $phone = str_replace('+', '', $phone);
        
        // Basic validation - phone should be numeric and between 10-15 digits
        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            return '';
        }
        
        return $phone;
    }
    
    public function remove_duplicate_recipients($recipients) {
        $unique = [];
        $seen = [];
        
        foreach ($recipients as $recipient) {
            $phone = $recipient['phone_number'];
            if (!isset($seen[$phone])) {
                $unique[] = $recipient;
                $seen[$phone] = true;
            }
        }
        
        return $unique;
    }
    
    public function process_message_variables($message, $variables) {
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        return $message;
    }
}
