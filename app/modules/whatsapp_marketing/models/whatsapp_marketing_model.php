<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class whatsapp_marketing_model extends MY_Model {

    protected $tb_campaigns = 'whatsapp_campaigns';
    protected $tb_api_configs = 'whatsapp_api_configs';
    protected $tb_recipients = 'whatsapp_recipients';
    protected $tb_messages = 'whatsapp_messages';
    protected $tb_users = USERS;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get campaigns list
     */
    public function get_campaigns($count = false, $limit = null, $offset = 0, $filters = []) {
        $this->db->select('c.*, a.profile_name, a.api_endpoint');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_api_configs . ' a', 'c.api_config_id = a.id', 'left');
        
        if (!empty($filters['status'])) {
            $this->db->where('c.status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $this->db->like('c.campaign_name', $filters['search']);
        }
        
        $this->db->order_by('c.id', 'DESC');
        
        if ($count) {
            return $this->db->count_all_results();
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get campaign by ID
     */
    public function get_campaign($id) {
        $this->db->select('c.*, a.profile_name, a.api_endpoint, a.api_key');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_api_configs . ' a', 'c.api_config_id = a.id', 'left');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get campaign by IDS
     */
    public function get_campaign_by_ids($ids) {
        $this->db->where('ids', $ids);
        $query = $this->db->get($this->tb_campaigns);
        return $query->row();
    }

    /**
     * Create campaign
     */
    public function create_campaign($data) {
        $data['ids'] = ids();
        $data['created'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tb_campaigns, $data);
    }

    /**
     * Update campaign
     */
    public function update_campaign($id, $data) {
        $data['changed'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->tb_campaigns, $data);
    }

    /**
     * Delete campaign
     */
    public function delete_campaign($id) {
        // Delete campaign messages
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->tb_messages);
        
        // Delete campaign recipients
        $this->db->where('campaign_id', $id);
        $this->db->delete($this->tb_recipients);
        
        // Delete campaign
        $this->db->where('id', $id);
        return $this->db->delete($this->tb_campaigns);
    }

    /**
     * Get API configs
     */
    public function get_api_configs($count = false, $limit = null, $offset = 0) {
        $this->db->select('*');
        $this->db->from($this->tb_api_configs);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        
        if ($count) {
            return $this->db->count_all_results();
        }
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get API config by ID
     */
    public function get_api_config($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->tb_api_configs);
        return $query->row();
    }

    /**
     * Create API config
     */
    public function create_api_config($data) {
        $data['ids'] = ids();
        $data['created'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tb_api_configs, $data);
    }

    /**
     * Update API config
     */
    public function update_api_config($id, $data) {
        $data['changed'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->tb_api_configs, $data);
    }

    /**
     * Delete API config
     */
    public function delete_api_config($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->tb_api_configs);
    }

    /**
     * Get users with WhatsApp numbers
     */
    public function get_users_with_whatsapp($limit = null, $offset = 0) {
        $this->db->select('id, first_name, last_name, email, whatsapp_number, balance');
        $this->db->from($this->tb_users);
        $this->db->where('whatsapp_number IS NOT NULL');
        $this->db->where('whatsapp_number !=', '');
        $this->db->where('status', 1);
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Count users with WhatsApp numbers
     */
    public function count_users_with_whatsapp() {
        $this->db->from($this->tb_users);
        $this->db->where('whatsapp_number IS NOT NULL');
        $this->db->where('whatsapp_number !=', '');
        $this->db->where('status', 1);
        return $this->db->count_all_results();
    }

    /**
     * Add recipients to campaign
     */
    public function add_recipients($campaign_id, $recipients) {
        if (empty($recipients)) {
            return false;
        }
        
        foreach ($recipients as $recipient) {
            $data = [
                'ids' => ids(),
                'campaign_id' => $campaign_id,
                'user_id' => isset($recipient['user_id']) ? $recipient['user_id'] : null,
                'phone_number' => $recipient['phone_number'],
                'username' => isset($recipient['username']) ? $recipient['username'] : '',
                'email' => isset($recipient['email']) ? $recipient['email'] : '',
                'balance' => isset($recipient['balance']) ? $recipient['balance'] : 0,
                'source' => $recipient['source'],
                'created' => date('Y-m-d H:i:s')
            ];
            $this->db->insert($this->tb_recipients, $data);
        }
        
        return true;
    }

    /**
     * Get campaign recipients
     */
    public function get_recipients($campaign_id, $limit = null, $offset = 0) {
        $this->db->select('*');
        $this->db->from($this->tb_recipients);
        $this->db->where('campaign_id', $campaign_id);
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Count campaign recipients
     */
    public function count_recipients($campaign_id) {
        $this->db->where('campaign_id', $campaign_id);
        return $this->db->count_all_results($this->tb_recipients);
    }

    /**
     * Get pending messages for cron
     */
    public function get_pending_messages($campaign_id, $limit = 1) {
        $this->db->select('m.*, r.username, r.email, r.balance');
        $this->db->from($this->tb_messages . ' m');
        $this->db->join($this->tb_recipients . ' r', 'm.recipient_id = r.id', 'left');
        $this->db->where('m.campaign_id', $campaign_id);
        $this->db->where('m.status', 'pending');
        $this->db->order_by('m.id', 'ASC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Create message log
     */
    public function create_message($data) {
        $data['ids'] = ids();
        $data['created'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tb_messages, $data);
    }

    /**
     * Update message status
     */
    public function update_message($id, $data) {
        $data['changed'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->tb_messages, $data);
    }

    /**
     * Get campaign messages
     */
    public function get_messages($campaign_id, $filters = [], $limit = null, $offset = 0) {
        $this->db->select('m.*, r.username, r.email');
        $this->db->from($this->tb_messages . ' m');
        $this->db->join($this->tb_recipients . ' r', 'm.recipient_id = r.id', 'left');
        $this->db->where('m.campaign_id', $campaign_id);
        
        if (!empty($filters['status'])) {
            $this->db->where('m.status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $this->db->where('m.created >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('m.created <=', $filters['date_to']);
        }
        
        $this->db->order_by('m.id', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Count messages
     */
    public function count_messages($campaign_id, $filters = []) {
        $this->db->from($this->tb_messages);
        $this->db->where('campaign_id', $campaign_id);
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $this->db->where('created >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('created <=', $filters['date_to']);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Get campaign statistics
     */
    public function get_campaign_stats($campaign_id) {
        $stats = [
            'total' => 0,
            'sent' => 0,
            'delivered' => 0,
            'failed' => 0,
            'pending' => 0
        ];
        
        $this->db->select('status, COUNT(*) as count');
        $this->db->from($this->tb_messages);
        $this->db->where('campaign_id', $campaign_id);
        $this->db->group_by('status');
        
        $query = $this->db->get();
        $results = $query->result();
        
        foreach ($results as $row) {
            $stats[$row->status] = $row->count;
            $stats['total'] += $row->count;
        }
        
        return $stats;
    }

    /**
     * Get active campaigns for cron
     */
    public function get_active_campaigns() {
        $this->db->select('c.*, a.api_key, a.api_endpoint');
        $this->db->from($this->tb_campaigns . ' c');
        $this->db->join($this->tb_api_configs . ' a', 'c.api_config_id = a.id', 'left');
        $this->db->where('c.status', 'running');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Prepare messages for campaign
     */
    public function prepare_campaign_messages($campaign_id) {
        $campaign = $this->get_campaign($campaign_id);
        if (!$campaign) {
            return false;
        }
        
        $recipients = $this->get_recipients($campaign_id);
        
        foreach ($recipients as $recipient) {
            // Replace placeholders in message
            $message = $campaign->message_content;
            $message = str_replace('{username}', $recipient->username, $message);
            $message = str_replace('{phone}', $recipient->phone_number, $message);
            $message = str_replace('{balance}', $recipient->balance, $message);
            $message = str_replace('{email}', $recipient->email, $message);
            
            // Create message record
            $this->create_message([
                'campaign_id' => $campaign_id,
                'recipient_id' => $recipient->id,
                'phone_number' => $recipient->phone_number,
                'message_content' => $message,
                'status' => 'pending'
            ]);
        }
        
        return true;
    }
}
