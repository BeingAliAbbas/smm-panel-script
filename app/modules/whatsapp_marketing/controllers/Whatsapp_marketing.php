<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing extends MX_Controller {
    
    public $module_name;
    public $module;
    public $module_icon;
    
    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Config Module
        $this->module_name = 'WhatsApp Marketing';
        $this->module = get_class($this);
        $this->module_icon = "fa fa-whatsapp";
        
        // Check if user is admin
        if (!get_role("admin")) {
            _validation('error', "Permission Denied!");
        }
    }
    
    // ========================================
    // MAIN DASHBOARD
    // ========================================
    
    public function index(){
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon
        );
        $this->template->build("index", $data);
    }
    
    // ========================================
    // CAMPAIGNS
    // ========================================
    
    public function campaigns($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $campaigns = $this->model->get_campaigns($per_page, $offset);
        $total = $this->model->get_campaigns();
        
        $data = array(
            "module" => $this->module,
            "campaigns" => $campaigns,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("campaigns/index", $data);
    }
    
    public function campaign_create(){
        $api_configs = $this->model->get_api_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "api_configs" => $api_configs
        );
        $this->load->view('campaigns/create', $data);
    }
    
    public function ajax_campaign_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $message = post("message");
        $api_config_id = post("api_config_id");
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        // Validation
        if(empty($name) || empty($message)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        if(empty($api_config_id)){
            ms(array(
                "status" => "error",
                "message" => "Please select WhatsApp API configuration"
            ));
        }
        
        $campaign_data = array(
            'name' => $name,
            'message' => $message,
            'api_config_id' => $api_config_id,
            'status' => 'pending',
            'sending_limit_hourly' => $sending_limit_hourly ? (int)$sending_limit_hourly : null,
            'sending_limit_daily' => $sending_limit_daily ? (int)$sending_limit_daily : null
        );
        
        if($this->model->create_campaign($campaign_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign created successfully"
            ));
        }
    }
    
    public function campaign_edit($ids = ""){
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $api_configs = $this->model->get_api_configs(1000, 0);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "api_configs" => $api_configs
        );
        $this->load->view('campaigns/edit', $data);
    }
    
    public function ajax_campaign_edit($ids = ""){
        _is_ajax($this->module);
        
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $name = post("name");
        $message = post("message");
        $api_config_id = post("api_config_id");
        $sending_limit_hourly = post("sending_limit_hourly");
        $sending_limit_daily = post("sending_limit_daily");
        
        if(empty($name) || empty($message)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'message' => $message,
            'api_config_id' => $api_config_id,
            'sending_limit_hourly' => $sending_limit_hourly ? (int)$sending_limit_hourly : null,
            'sending_limit_daily' => $sending_limit_daily ? (int)$sending_limit_daily : null
        );
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign updated successfully"
            ));
        }
    }
    
    public function ajax_campaign_delete($ids = ""){
        _is_ajax($this->module);
        
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        if($this->model->delete_campaign($ids)){
            ms(array(
                "status" => "success",
                "message" => "Campaign deleted successfully"
            ));
        }
    }
    
    public function ajax_campaign_status($ids = ""){
        _is_ajax($this->module);
        
        $campaign = $this->model->get_campaign($ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $action = post("action");
        
        $update_data = array();
        switch($action){
            case 'start':
                if($campaign->status != 'pending' && $campaign->status != 'paused'){
                    ms(array(
                        "status" => "error",
                        "message" => "Can only start pending or paused campaigns"
                    ));
                }
                $update_data['status'] = 'running';
                $update_data['started_at'] = NOW;
                break;
                
            case 'pause':
                if($campaign->status != 'running'){
                    ms(array(
                        "status" => "error",
                        "message" => "Can only pause running campaigns"
                    ));
                }
                $update_data['status'] = 'paused';
                break;
                
            case 'resume':
                if($campaign->status != 'paused'){
                    ms(array(
                        "status" => "error",
                        "message" => "Can only resume paused campaigns"
                    ));
                }
                $update_data['status'] = 'running';
                break;
                
            default:
                ms(array(
                    "status" => "error",
                    "message" => "Invalid action"
                ));
        }
        
        if($this->model->update_campaign($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "Campaign status updated successfully"
            ));
        }
    }
    
    // ========================================
    // RECIPIENTS
    // ========================================
    
    public function recipients($campaign_ids = "", $page = 1){
        $campaign = $this->model->get_campaign($campaign_ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $page = max(1, (int)$page);
        $per_page = 50;
        $offset = ($page - 1) * $per_page;
        
        $recipients = $this->model->get_recipients($campaign->id, $per_page, $offset);
        $total = $this->model->get_recipients($campaign->id);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "recipients" => $recipients,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("recipients/index", $data);
    }
    
    public function ajax_import_recipients($campaign_ids = ""){
        _is_ajax($this->module);
        
        $campaign = $this->model->get_campaign($campaign_ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        $import_type = post("import_type");
        
        if($import_type == 'database'){
            // Import from general_users
            $count = $this->model->import_from_general_users($campaign->id);
            
            if($count > 0){
                // Update campaign stats
                $this->model->update_campaign_stats($campaign->id);
                
                ms(array(
                    "status" => "success",
                    "message" => "$count recipients imported successfully"
                ));
            } else {
                ms(array(
                    "status" => "error",
                    "message" => "No valid phone numbers found in database"
                ));
            }
            
        } elseif($import_type == 'file'){
            // Import from CSV/TXT file
            if(empty($_FILES['file']['name'])){
                ms(array(
                    "status" => "error",
                    "message" => "Please select a file"
                ));
            }
            
            $allowed_types = array('csv', 'txt');
            $file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            
            if(!in_array(strtolower($file_ext), $allowed_types)){
                ms(array(
                    "status" => "error",
                    "message" => "Only CSV and TXT files are allowed"
                ));
            }
            
            // Upload file
            $upload_path = APPPATH . 'cache/whatsapp_imports/';
            if(!is_dir($upload_path)){
                mkdir($upload_path, 0755, true);
            }
            
            $file_name = 'import_' . time() . '_' . uniqid() . '.' . $file_ext;
            $file_path = $upload_path . $file_name;
            
            if(move_uploaded_file($_FILES['file']['tmp_name'], $file_path)){
                $result = $this->model->import_from_csv($campaign->id, $file_path);
                
                // Delete uploaded file
                @unlink($file_path);
                
                if($result['success']){
                    // Update campaign stats
                    $this->model->update_campaign_stats($campaign->id);
                    
                    ms(array(
                        "status" => "success",
                        "message" => $result['message']
                    ));
                } else {
                    ms(array(
                        "status" => "error",
                        "message" => $result['message']
                    ));
                }
            } else {
                ms(array(
                    "status" => "error",
                    "message" => "Failed to upload file"
                ));
            }
        } else {
            ms(array(
                "status" => "error",
                "message" => "Invalid import type"
            ));
        }
    }
    
    // ========================================
    // LOGS & REPORTS
    // ========================================
    
    public function logs($campaign_ids = "", $page = 1){
        $campaign = $this->model->get_campaign($campaign_ids);
        if(!$campaign){
            redirect(cn($this->module . "/campaigns"));
        }
        
        $page = max(1, (int)$page);
        $per_page = 50;
        $offset = ($page - 1) * $per_page;
        
        $logs = $this->model->get_logs($campaign->id, $per_page, $offset);
        $total = $this->model->get_logs($campaign->id);
        
        $data = array(
            "module" => $this->module,
            "campaign" => $campaign,
            "logs" => $logs,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("logs/index", $data);
    }
    
    public function ajax_export_logs($campaign_ids = ""){
        $campaign = $this->model->get_campaign($campaign_ids);
        if(!$campaign){
            ms(array(
                "status" => "error",
                "message" => "Campaign not found"
            ));
        }
        
        // Get all logs
        $logs = $this->model->get_logs($campaign->id, 10000, 0);
        
        if(empty($logs)){
            ms(array(
                "status" => "error",
                "message" => "No logs found"
            ));
        }
        
        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="whatsapp_logs_' . $campaign->ids . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, array('ID', 'Phone Number', 'Message', 'Status', 'Error', 'Sent At', 'Created At'));
        
        // Data rows
        foreach($logs as $log){
            fputcsv($output, array(
                $log->id,
                $log->phone_number,
                $log->message,
                $log->status,
                $log->error_message,
                $log->sent_at,
                $log->created_at
            ));
        }
        
        fclose($output);
        exit;
    }
    
    // ========================================
    // API CONFIGURATIONS
    // ========================================
    
    public function api_configs($page = 1){
        $page = max(1, (int)$page);
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $configs = $this->model->get_api_configs($per_page, $offset);
        $total = $this->model->get_api_configs();
        
        $data = array(
            "module" => $this->module,
            "configs" => $configs,
            "total" => $total,
            "page" => $page,
            "per_page" => $per_page
        );
        $this->template->build("api_configs/index", $data);
    }
    
    public function api_config_create(){
        $data = array(
            "module" => $this->module
        );
        $this->load->view('api_configs/create', $data);
    }
    
    public function ajax_api_config_create(){
        _is_ajax($this->module);
        
        $name = post("name");
        $api_key = post("api_key");
        $api_endpoint = post("api_endpoint");
        $is_default = post("is_default") ? 1 : 0;
        $status = post("status") ? 1 : 0;
        
        if(empty($name) || empty($api_key) || empty($api_endpoint)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $config_data = array(
            'name' => $name,
            'api_key' => $api_key,
            'api_endpoint' => $api_endpoint,
            'is_default' => $is_default,
            'status' => $status
        );
        
        if($this->model->create_api_config($config_data)){
            ms(array(
                "status" => "success",
                "message" => "API configuration created successfully"
            ));
        }
    }
    
    public function api_config_edit($ids = ""){
        $config = $this->model->get_api_config($ids);
        if(!$config){
            redirect(cn($this->module . "/api_configs"));
        }
        
        $data = array(
            "module" => $this->module,
            "config" => $config
        );
        $this->load->view('api_configs/edit', $data);
    }
    
    public function ajax_api_config_edit($ids = ""){
        _is_ajax($this->module);
        
        $config = $this->model->get_api_config($ids);
        if(!$config){
            ms(array(
                "status" => "error",
                "message" => "API configuration not found"
            ));
        }
        
        $name = post("name");
        $api_key = post("api_key");
        $api_endpoint = post("api_endpoint");
        $is_default = post("is_default") ? 1 : 0;
        $status = post("status") ? 1 : 0;
        
        if(empty($name) || empty($api_key) || empty($api_endpoint)){
            ms(array(
                "status" => "error",
                "message" => lang("please_fill_in_the_required_fields")
            ));
        }
        
        $update_data = array(
            'name' => $name,
            'api_key' => $api_key,
            'api_endpoint' => $api_endpoint,
            'is_default' => $is_default,
            'status' => $status
        );
        
        if($this->model->update_api_config($ids, $update_data)){
            ms(array(
                "status" => "success",
                "message" => "API configuration updated successfully"
            ));
        }
    }
    
    public function ajax_api_config_delete($ids = ""){
        _is_ajax($this->module);
        
        $config = $this->model->get_api_config($ids);
        if(!$config){
            ms(array(
                "status" => "error",
                "message" => "API configuration not found"
            ));
        }
        
        if($this->model->delete_api_config($ids)){
            ms(array(
                "status" => "success",
                "message" => "API configuration deleted successfully"
            ));
        }
    }
}
