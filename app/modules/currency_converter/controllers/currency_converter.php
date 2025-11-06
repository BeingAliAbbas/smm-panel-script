<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class currency_converter extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
    }

    public function index(){
        $data = array(
            "module" => get_class($this),
        );
        $this->template->build("index", $data);
    }

    public function convert(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $amount = $this->input->post('amount');

        if(empty($from) || empty($to) || empty($amount)){
            echo json_encode(array('status' => 'error', 'message' => lang('Please_fill_in_all_fields')));
            return;
        }

        $result = $this->model->convert_currency($from, $to, $amount);
        
        if($result['status'] == 'success'){
            echo json_encode($result);
        } else {
            echo json_encode(array('status' => 'error', 'message' => $result['message']));
        }
    }

    public function get_rates(){
        $base = $this->input->get('base') ?: 'USD';
        $rates = $this->model->get_exchange_rates($base);
        
        if($rates){
            echo json_encode(array('status' => 'success', 'rates' => $rates));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to fetch rates'));
        }
    }
}
