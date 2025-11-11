<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp_marketing extends MX_Controller
{
    public $module_name;
    public $module;
    public $module_icon;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        
        // Config Module
        $this->module_name = 'WhatsApp Marketing';
        $this->module = strtolower(get_class($this));
        $this->module_icon = "fa fa-whatsapp";
        
        // Check if user is admin
        if (!get_role("admin")) {
            _validation('error', "Permission Denied!");
        }
    }

    public function index()
    {
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon
        );
        $this->template->build("index", $data);
    }

    // ===========================
    // Campaigns
    // ===========================

    public function campaigns()
    {
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'campaigns' => $this->model->get_campaigns()
        );
        $this->template->build("campaigns/index", $data);
    }

    public function campaign_create()
    {
        if ($this->input->post()) {
            try {
                $campaign_id = $this->model->create_campaign($this->input->post());
                
                _msgbox_redirect(lang('Created successfully'), cn($this->module . '/campaigns'));
            } catch (Exception $e) {
                _msgbox_error($e->getMessage());
            }
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'api_configs' => $this->model->get_api_configs()
        );
        $this->template->build("campaigns/create", $data);
    }

    public function campaign_edit($id)
    {
        $campaign = $this->model->get_campaign($id);
        
        if (!$campaign) {
            _msgbox_redirect(lang('Campaign not found'), cn($this->module . '/campaigns'));
        }
        
        if ($this->input->post()) {
            try {
                $this->model->update_campaign($id, $this->input->post());
                
                _msgbox_redirect(lang('Updated successfully'), cn($this->module . '/campaigns'));
            } catch (Exception $e) {
                _msgbox_error($e->getMessage());
            }
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'campaign' => $campaign,
            'api_configs' => $this->model->get_api_configs()
        );
        $this->template->build("campaigns/edit", $data);
    }

    public function campaign_details($id)
    {
        $campaign = $this->model->get_campaign($id);
        
        if (!$campaign) {
            _msgbox_redirect(lang('Campaign not found'), cn($this->module . '/campaigns'));
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'campaign' => $campaign,
            'recipients' => $this->model->get_recipients($id),
            'logs' => $this->model->get_logs($id, 50)
        );
        $this->template->build("campaigns/details", $data);
    }

    public function campaign_delete($id)
    {
        $this->model->delete_campaign($id);
        _msgbox_redirect(lang('Deleted successfully'), cn($this->module . '/campaigns'));
    }

    public function campaign_start($id)
    {
        $this->model->update_campaign($id, ['sending_status' => 'Started']);
        _msgbox_redirect(lang('Campaign started'), cn($this->module . '/campaigns'));
    }

    public function campaign_pause($id)
    {
        $this->model->update_campaign($id, ['sending_status' => 'Paused']);
        _msgbox_redirect(lang('Campaign paused'), cn($this->module . '/campaigns'));
    }

    public function campaign_resume($id)
    {
        $this->model->update_campaign($id, ['sending_status' => 'Started']);
        _msgbox_redirect(lang('Campaign resumed'), cn($this->module . '/campaigns'));
    }

    // ===========================
    // API Configs
    // ===========================

    public function api_configs()
    {
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'configs' => $this->model->get_api_configs()
        );
        $this->template->build("api_configs/index", $data);
    }

    public function api_config_create()
    {
        if ($this->input->post()) {
            try {
                $this->model->create_api_config($this->input->post());
                
                _msgbox_redirect(lang('Created successfully'), cn($this->module . '/api_configs'));
            } catch (Exception $e) {
                _msgbox_error($e->getMessage());
            }
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon
        );
        $this->template->build("api_configs/create", $data);
    }

    public function api_config_edit($id)
    {
        $config = $this->model->get_api_config($id);
        
        if (!$config) {
            _msgbox_redirect(lang('Configuration not found'), cn($this->module . '/api_configs'));
        }
        
        if ($this->input->post()) {
            try {
                $this->model->update_api_config($id, $this->input->post());
                
                _msgbox_redirect(lang('Updated successfully'), cn($this->module . '/api_configs'));
            } catch (Exception $e) {
                _msgbox_error($e->getMessage());
            }
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'config' => $config
        );
        $this->template->build("api_configs/edit", $data);
    }

    public function api_config_delete($id)
    {
        $this->model->delete_api_config($id);
        _msgbox_redirect(lang('Deleted successfully'), cn($this->module . '/api_configs'));
    }

    // ===========================
    // Recipients
    // ===========================

    public function recipients($campaign_id)
    {
        $campaign = $this->model->get_campaign($campaign_id);
        
        if (!$campaign) {
            _msgbox_redirect(lang('Campaign not found'), cn($this->module . '/campaigns'));
        }
        
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'campaign' => $campaign,
            'recipients' => $this->model->get_recipients($campaign_id)
        );
        $this->template->build("recipients/index", $data);
    }

    public function import_from_users()
    {
        try {
            set_time_limit(120);
            
            $campaign_id = $this->input->post('campaign_id');
            
            if (!$campaign_id) {
                echo json_encode(['status' => 'error', 'message' => 'Campaign ID is required']);
                return;
            }
            
            $imported = $this->model->import_from_users($campaign_id);
            
            if ($imported > 0) {
                echo json_encode(['status' => 'success', 'message' => "Imported {$imported} users with order history"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No users found with order history or all users already imported']);
            }
        } catch (Exception $e) {
            log_message('error', 'WhatsApp Marketing - Import error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function import_from_csv()
    {
        $campaign_id = $this->input->post('campaign_id');
        $csv_data = $this->input->post('csv_data');
        
        if (!$campaign_id || !$csv_data) {
            _msgbox_error('Campaign ID and CSV data are required');
            return;
        }
        
        $imported = $this->model->import_from_csv($campaign_id, $csv_data);
        
        _msgbox_redirect("Imported {$imported} recipients", site_url($this->module . '/recipients/' . $campaign_id));
    }

    public function delete_all_recipients($campaign_id)
    {
        $this->model->delete_all_recipients($campaign_id);
        _msgbox_redirect(lang('All recipients deleted'), site_url($this->module . '/recipients/' . $campaign_id));
    }

    // ===========================
    // Reports
    // ===========================

    public function reports()
    {
        $data = array(
            "module" => $this->module,
            "module_name" => $this->module_name,
            "module_icon" => $this->module_icon,
            'campaigns' => $this->model->get_campaigns()
        );
        $this->template->build("reports/index", $data);
    }

    public function export_report($campaign_id)
    {
        $campaign = $this->model->get_campaign($campaign_id);
        
        if (!$campaign) {
            _msgbox_redirect(lang('Campaign not found'), cn($this->module . '/reports'));
        }
        
        $report_data = $this->model->export_campaign_report($campaign_id);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="whatsapp_campaign_' . $campaign_id . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, ['Phone', 'Name', 'Status', 'Sent At', 'Error Message']);
        
        // Add rows
        foreach ($report_data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
