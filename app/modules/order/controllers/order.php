<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 
class order extends MX_Controller {
	public $tb_users;
	public $tb_users_price;
	public $tb_order;
	public $tb_categories;
	public $tb_services;
	public $tb_api_providers;
	public $module;
	public $module_name;
	public $module_icon;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');

		//Config Module
		$this->tb_users               = USERS;
		$this->tb_users_price         = USERS_PRICE;
		$this->tb_order               = ORDER;
		$this->tb_categories          = CATEGORIES;
		$this->tb_services            = SERVICES;
		$this->tb_api_providers       = API_PROVIDERS;
		$this->module                 = get_class($this);
		$this->module_name            = 'Order';
		$this->module_icon            = "fa ft-users";

		$this->columns = array(
			"order_id"                  => lang("order_id"),
			"order_basic_details"       => lang("order_basic_details"),
			"created"                   => lang("Created"),
			"status"                    => lang("Status"),
		);
		
		if (get_role("admin") || get_role("supporter")) {
			$this->columns = array(
				"order_id"                  => lang("order_id"),
				"api_order_id"              => lang("api_orderid"),
				"uid"                       => lang("User"),
				"order_basic_details"       => lang("order_basic_details"),
				"profit"                   => lang("Profit"),
				"created"                   => lang("Created"),
				"status"                    => lang("Status"),
				"response"                  => lang("API_Response"),
				"action"                    => lang("Action"),
			);
		}

	}

	// ADD - Refactored to pass data through controller
	public function add(){
		$this->load->model("services/services_model", 'services_model');
		$categories = $this->services_model->get_active_categories();
		
		// Get dashboard data from model
		$dashboard_data = $this->model->get_dashboard_data(session('uid'));
		
		// Get user role
		$user_role = $this->model->get_user_role(session('uid'));
		
		// Get WhatsApp status
		$whatsapp_data = $this->model->get_whatsapp_data(session('uid'));
		
		// Get current currency
		$current_currency = get_current_currency();
		$currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol', "$");
		
		$data = array(
			"module"                  => get_class($this),
			'categories'              => $categories,
			'services'                => "",
			'dashboard_data'          => $dashboard_data,
			'user_role'               => $user_role,
			'whatsapp_number_exists'  => $whatsapp_data['exists'],
			'currency_symbol'         => $currency_symbol,
		);
		$this->template->build('add/add', $data);
	}

	// Get Services by cate ID
	public function get_services($id = ""){
		$check_category = $this->model->check_record("id", $this->tb_categories, $id, false, false);
		if ($check_category) {
			$services    = $this->model->get_services_by_cate($id);
			$this->load->model('users/users_model');
			$data = array(
				"module"   		=> get_class($this),
				"services" 		=> $services,
				"custom_rates"  => $this->users_model->get_custom_rates(),
			);
			$this->load->view('add/get_services', $data);
		}		
	}
	
	// Get Service Detail by ID
	public function get_service($id = ""){
		$check_service    = $this->model->get_service_item($id);
		$data = array(
			"module"   		=> get_class($this),
			"service" 		=> $check_service,
			
		);
		$this->load->view('add/get_service', $data);
	}
	
	public function ajax_add_order(){
		$api_provider_id    = post("api_provider_id");
		$api_service_id 	= post("api_service_id");
		$service_id 		= post("service_id");
		$cate_id 		    = post("category_id");
		$quantity 		    = post("quantity");
		$link 		        = post("link");
		$runs               = post("runs");
		$interval           = post("interval");
		$is_drip_feed       = (post("is_drip_feed") == "on") ? 1 : 0;
		$agree 		        = (post("agree") == "on") ? 1 : 0;
		$service_type 	    = post("service_type");
		
		if ($cate_id == "") {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_a_category")
			));
		}	

		if ($service_id == "") {
			ms(array(
				"status"  => "error",
				"message" => lang("please_choose_a_service")
			));
		}

		$check_category = $this->model->check_record("*", $this->tb_categories, $cate_id, false, true);
		$check_service  = $this->model->check_record("*", $this->tb_services, $service_id, false, true);
		if (empty($check_category)) {
			ms(array(
				"status"  => "error",
				"message" => lang("category_does_not_exists")
			));
		}

		if (empty($check_service)) {
			ms(array(
				"status"  => "error",
				"message" => lang("service_does_not_exists")
			));
		}
		
		$check_category_name = $this->model->get("ids, name", $this->tb_categories, "id = '{$cate_id}'");
		if(!empty($check_category_name)){
			$category_name = $check_category_name->name;
			if (strpos($category_name, 'Refill') == true) {
				if (strpos($category_name, 'No Refill') == false) {
					// echo 'true';
					// die;
					$is_refill = "yes";
				}else{
					$is_refill = "no";
				}
	
			}else{
				$is_refill = "yes";
			}
		}else{
			$is_refill = "no";
		}
		
		/*----------  Add all order without quantity  ----------*/
		if ($service_type == "subscriptions") {
			$this->add_order_subscriptions($_POST);
			exit();
		}

		if ($link == "") {
			ms(array(
				"status"  => "error",
				"message" => lang("invalid_link")
			));
		}
		$link = strip_tags($link);
		
		switch ($service_type) {

			case 'custom_comments':
				$comments = strip_tags($_POST['comments']);
				if ($comments == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("comments_field_is_required")
					));
				}
				$quantity = count(explode("\n", $comments));
				break;

			case 'mentions_custom_list':
				$usernames_custom = post("usernames_custom");
				if ($usernames_custom == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("username_field_is_required")
					));
				}
				$quantity = count(explode("\n", $usernames_custom));
				break;

			case 'package':
				$quantity = 1;
				break;

			case 'custom_comments_package':
				$comments = strip_tags($_POST['comments_custom_package']);
				if ($comments == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("comments_field_is_required")
					));
				}
				$quantity = 1;
				break;
		}

		if ($quantity == "") {
			ms(array(
				"status"  => "error",
				"message" => lang("quantity_is_required")
			));
		}
		
		/*----------  Check dripfeed  ----------*/
		if ($is_drip_feed) {

			if ($runs == "") {
				ms(array(
					"status"  => "error",
					"message" => lang("runs_is_required")
				));
			}
			
			if ($interval == "") {
				ms(array(
					"status"  => "error",
					"message" => lang("interval_time_is_required")
				));
			}

			if ($interval > 60) {
				ms(array(
					"status"  => "error",
					"message" => lang("interval_time_must_to_be_less_than_or_equal_to_60_minutes")
				));
			}
			$total_quantity = $runs * $quantity;
		}else{
			$total_quantity = $quantity;
		}
		
		/*----------  Check quantity  ----------*/
		$min          = $check_service->min;
		$max          = $check_service->max;
		$price        = get_user_price(session('uid'), $check_service);

		if ($service_type == "package" || $service_type == "custom_comments_package") {
			$total_charge = $price;
		}else{
			$total_charge = ($price*$total_quantity)/1000;
		}
		
		if ($total_quantity <= 0 || ($total_quantity < $min) || $quantity < $min) {
			ms(array(
				"status"  => "error",
				"message" => lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount")
			));
		}	

		if ($total_quantity > $max) {
			ms(array(
				"status"  => "error",
				"message" => lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount")
			));
		}
		/*----------  Get balance ----------*/
		$user = $this->model->get("balance", $this->tb_users, ['id' => session('uid')]);

		/*----------  Get Formal Charge and profit  ----------*/
		$formal_charge = ($check_service->original_price * $total_charge) / $check_service->price;
		$profit        = $total_charge - $formal_charge;
		/*----------  Collect data import to database  ----------*/
		$data = array(
			"ids" 	        	=> ids(),
			"uid" 	        	=> session("uid"),
			"cate_id" 	    	=> $cate_id,
			"service_id" 		=> $service_id,
			"service_type" 		=> $service_type,
			"link" 	        	=> $link,
			"quantity" 	    	=> $total_quantity,
			"charge" 	    	=> $total_charge,
			"formal_charge" 	=> $formal_charge,
			"profit" 	    	=> $profit,
			"api_provider_id"  	=> $api_provider_id,
			"api_service_id"  	=> $api_service_id,
			"is_drip_feed"  	=> $is_drip_feed,
			"is_refill"         => $is_refill,
			"status"			=> 'pending',
			"changed" 	    	=> NOW,
			"created" 	    	=> NOW,
		);
		/*----------  get the different required paramenter for each service type  ----------*/
		switch ($service_type) {

			case 'mentions_with_hashtags':
				$hashtags  = post("hashtags");
				$usernames = post("usernames");
				$usernames = strip_tags($usernames);

				if ($usernames == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("username_field_is_required")
					));
				}

				if ($hashtags == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("hashtag_field_is_required")
					));
				}
				$data["usernames"] = $usernames;
				$data["hashtags"]  = $hashtags;
				break;

			case 'mentions_hashtag':
				$hashtag = post("hashtag");
				if ($hashtag == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("hashtag_field_is_required")
					));
				}
				$data["hashtag"] = $hashtag;
				break;	

			case 'comment_likes':
				$username = post("username");
				$username = strip_tags($username);
				if ($username == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("username_field_is_required")
					));
				}

				$data["username"] = $username;
				break;	
							
			case 'mentions_user_followers':
				$username = post("username");
				$username = strip_tags($username);

				if ($username == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("username_field_is_required")
					));
				}

				$data["username"] = $username;
				break;		

			case 'mentions_media_likers':
				$media_url = post("media_url");

				if ($media_url == "" || !filter_var($media_url, FILTER_VALIDATE_URL)) {
				    ms(array(
						"status"  => "error",
						"message" => lang("invalid_link")
					));
				}
				$data["media"] = $media_url;
				break;

			case 'custom_comments':
				$data["comments"] = json_encode($comments);
				break;

			case 'custom_comments_package':
				$data["comments"] = json_encode($comments);
				break;

			case 'mentions_custom_list':
				$data["usernames"] = json_encode($usernames_custom);
				break;

		}
		// Check agree
		if (!$agree) {
			ms(array(
				"status"  => "error",
				"message" => lang("you_must_confirm_to_the_conditions_before_place_order")
			));
		}
		// check balance
		if ($user->balance != 0 && $user->balance < $total_charge || $user->balance == 0) {
			ms(array(
				"status"  => "error",
				"message" => lang("not_enough_funds_on_balance")
			));
		}

		// Check for duplicate orders (same link in pending/processing/inprogress status, regardless of service)
		$existing_order = $this->model->check_duplicate_order(session('uid'), $link);
		if ($existing_order) {
			// Sanitize order data before sending to frontend
			ms(array(
				"status" => "error",
				"message" => lang("An order with the same link is already in progress"),
				"order_exists" => true,
				"existing_order_id" => (int)$existing_order->id,
				"existing_order_status" => htmlspecialchars($existing_order->status, ENT_QUOTES, 'UTF-8'),
				"existing_order_created" => htmlspecialchars($existing_order->created, ENT_QUOTES, 'UTF-8')
			));
		}

		if ($is_drip_feed) {
			$data['runs'] = $runs;
			$data['interval'] = $interval;
			$data['dripfeed_quantity'] = $quantity;
			$data['status'] = 'inprogress';
		}

		if (!empty($api_provider_id) && !empty($api_service_id)) {
			$data['api_order_id'] = -1;
		}
		$this->save_order($this->tb_order, $data, $user->balance, $total_charge);
	}

	private function add_order_subscriptions($post){
		$api_provider_id    = $post["api_provider_id"];
		$api_service_id 	= $post["api_service_id"];
		$service_id 		= $post["service_id"];
		$cate_id 		    = $post["category_id"];
		$agree 		        = (isset($post['agree']) && $post["agree"] == "on") ? 1 : 0;
		$service_type 	    = $post["service_type"];
		$link 		        = $post["link"];
		$link               = strip_tags($link);

		/*----------  check service   ----------*/
		$check_service  = $this->model->check_record("*", $this->tb_services, $service_id, false, true);

		/*----------  Collect data import to database  ----------*/
		$data = array(
			"ids" 	        	=> ids(),
			"uid" 	        	=> session("uid"),
			"cate_id" 	    	=> $cate_id,
			"service_id" 		=> $service_id,
			"service_type" 		=> $service_type,
			"api_provider_id"  	=> $api_provider_id,
			"api_service_id"  	=> $api_service_id,
			"sub_status"  	    => 'Active',
			"status"  	        => 'pending',
			"changed" 	    	=> NOW,
			"created" 	    	=> NOW,
		);

		switch ($service_type) {
			case 'subscriptions':
				$username = $post["sub_username"];
				$posts    = (int)$post["sub_posts"];
				$min      = (int)$post["sub_min"];
				$max      = (int)$post["sub_max"];
				$delay    = (int)$post["sub_delay"];
				$expiry   = $post["sub_expiry"];

				if ($username == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("username_field_is_required")
					));
				}

				if ($min == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount")
					));
				}

				if ($min < $check_service->min) {
					ms(array(
						"status"  => "error",
						"message" => lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount")
					));
				}

				if ($max < $min) {
					ms(array(
						"status"  => "error",
						"message" => lang("min_cannot_be_higher_than_max")
					));
				}

				if ($max > $check_service->max) {
					ms(array(
						"status"  => "error",
						"message" => lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount")
					));
				}
				
				if (!in_array($delay, array(0, 5, 10, 15, 30, 60, 90))) {
					ms(array(
						"status"  => "error",
						"message" => lang("incorrect_delay")
					));
				}

				if ($posts <=  0 || $posts == "") {
					ms(array(
						"status"  => "error",
						"message" => lang("new_posts_future_posts_must_to_be_greater_than_or__equal_to_1")
					));
				}

				// Check agree
				if (!$agree) {
					ms(array(
						"status"  => "error",
						"message" => lang("you_must_confirm_to_the_conditions_before_place_order")
					));
				}
				// calculate total charge
				$charge = ($max * $posts * $check_service->price) / 1000;
				
				// check balance
				$current_balance = $this->model->check_record("balance", $this->tb_users, session('uid'), false, true);
				if (($current_balance->balance != 0 && $current_balance->balance < $charge) || $current_balance->balance == 0) {
					ms(array(
						"status"  => "error",
						"message" => lang("not_enough_funds_on_balance")
					));
				}
				if ($expiry != "") {
					$expiry = str_replace('/', '-', $expiry);
					$expiry = date("Y-m-d", strtotime($expiry));
				}else{
					$expiry = "";
				}	
				
				$data["username"]     = $username;
				$data["sub_posts"]    = ($posts == "")? -1: $posts;
				$data["sub_min"]      = $min;
				$data["sub_max"]      = $max;
				$data["sub_delay"]    = $delay;
				$data["sub_expiry"]   = $expiry;

				if (!empty($api_provider_id) && !empty($api_service_id)) {
					$data['api_order_id'] = -1;
				}
				
				$this->save_order($this->tb_order, $data);
				break;
		}

	}
			
/*---------- Insert data to order ----------*/
private function save_order($table, $data_orders, $user_balance = "", $total_charge = "") {
    $this->db->insert($table, $data_orders);
    $order_id = $this->db->insert_id();
    if ($this->db->affected_rows() > 0) {

        if ($data_orders["service_type"] != "subscriptions") {
            $new_balance = $user_balance - $total_charge;
            $new_balance = ($new_balance > 0) ? $new_balance : 0;
            $this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);
            
            // Log balance deduction for order
            $this->load->helper('balance_logs');
            log_order_deduction(session("uid"), $order_id, $total_charge, $user_balance, $new_balance);
        }

        /*---------- Send Admin WhatsApp Notification ----------*/
        // Load WhatsApp notification library
        $this->load->library('whatsapp_notification');

        // Check if configured
        if ($this->whatsapp_notification->is_configured()) {
            // Fetch user details
            $user = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'");
            $user_email = isset($user->email) ? $user->email : '';

            // Format link
            $formatted_link = '';
            if (!empty($data_orders['link'])) {
                $formatted_link = filter_var($data_orders['link'], FILTER_VALIDATE_URL) ? 
                                preg_replace('#^https?://#', '', $data_orders['link']) : 
                                truncate_string($data_orders['link'], 60);
            }

            // Prepare variables for order placed notification
            $variables = array(
                'order_id' => $order_id,
                'total_charge' => $total_charge,
                'quantity' => $data_orders['quantity'],
                'link' => $formatted_link,
                'user_email' => $user_email
            );

            // Send order placed notification to admin
            $result = $this->whatsapp_notification->send('order_placed', $variables);

            // Log the response
            if ($result === true) {
                error_log("Admin WhatsApp notification sent successfully for order #" . $order_id);
            } else {
                error_log("Failed to send admin WhatsApp notification: " . $result);
            }
        } else {
            error_log("WhatsApp API not configured.");
        }
        
        /*---------- Send Transactional Email for New Order ----------*/
        $this->load->library('Transactional_email');
        $user = $this->model->get("email", $this->tb_users, "id = '" . session('uid') . "'");
        $user_email = isset($user->email) ? $user->email : '';
        $service = $this->model->get("name", $this->tb_services, "id = '" . $data_orders['service_id'] . "'");
        $service_name = isset($service->name) ? $service->name : 'Unknown Service';
        $quantity = isset($data_orders['quantity']) ? $data_orders['quantity'] : 0;
        $this->transactional_email->send_new_order_email($order_id, $user_email, $service_name, $total_charge, $quantity);
        
        /*---------- Send Order notification email to Admin ----------*/
        if (get_option("is_order_notice_email", '')) {
            $user_email = $this->model->get("email", $this->tb_users, "id = '".session('uid')."'")->email;

            $subject = getEmailTemplate("order_success")->subject;
            $subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);
            $email_content = getEmailTemplate("order_success")->content;
            $email_content = str_replace("{{user_email}}", $user_email, $email_content);
            $email_content = str_replace("{{order_id}}", $order_id, $email_content);
            $email_content = str_replace("{{currency_symbol}}", get_option("currency_symbol",""), $email_content);
            $email_content = str_replace("{{total_charge}}", $total_charge, $email_content);
            $email_content = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $email_content);

            $admin_id = $this->model->get("id", $this->tb_users, "role = 'admin'","id","ASC")->id;
            if ($admin_id == "") {
                $admin_id = 1;
            }
            $check_send_email_issue = $this->model->send_email($subject, $email_content, $admin_id, false);
            if ($check_send_email_issue) {
                ms(array(
                    "status" => "error",
                    "message" => $check_send_email_issue,
                ));
            }
        }

        // Get currency info for modal
        $current_currency = get_current_currency();
        $currency_symbol = $current_currency ? $current_currency->symbol : get_option('currency_symbol', "$");
        
        // Format charge with proper decimal places and currency conversion
        $decimal_places = get_option('currency_decimal', 2);
        $decimalpoint = get_option('currency_decimal_separator', 'dot') == 'comma' ? ',' : '.';
        $separator = get_option('currency_thousand_separator', 'space') == 'space' ? ' ' : (get_option('currency_thousand_separator', 'comma') == 'comma' ? ',' : '.');
        $formatted_charge = currency_format(convert_currency($total_charge), $decimal_places, $decimalpoint, $separator);
        
        // Calculate estimated completion time (rough estimate)
        // This is a rough estimate based on quantity - can be customized per service type
        $estimated_minutes = ceil($quantity / 1000) * 30; // Roughly 30 minutes per 1000 units
        if ($estimated_minutes < 30) {
            $estimated_minutes = 30; // Minimum 30 minutes
        } elseif ($estimated_minutes > 1440) {
            $estimated_minutes = 1440; // Maximum 24 hours
        }
        
        // Format estimated time
        if ($estimated_minutes < 60) {
            $estimated_time = $estimated_minutes . " minutes";
        } elseif ($estimated_minutes < 1440) {
            $hours = floor($estimated_minutes / 60);
            $estimated_time = $hours . " hour" . ($hours > 1 ? "s" : "");
        } else {
            $days = floor($estimated_minutes / 1440);
            $estimated_time = $days . " day" . ($days > 1 ? "s" : "");
        }
        
        // Prepare order confirmation data for modal
        ms(array(
            "status"  => "success",
            "message" => lang("place_order_successfully"),
            "show_confirmation_modal" => true,
            "order_details" => array(
                "order_id" => $order_id,
                "service_name" => $service_name,
                "link" => htmlspecialchars($data_orders['link'], ENT_QUOTES, 'UTF-8'),
                "quantity" => $quantity,
                "charge" => $formatted_charge,
                "currency_symbol" => $currency_symbol,
                "status" => ucfirst($data_orders['status']),
                "estimated_time" => $estimated_time
            )
        ));
    } else {
        ms(array(
            "status"  => "error",
            "message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")
        ));
    }
}


	// MASS ORDER
	public function ajax_mass_order(){
		$mass_order 		= post("mass_order");
		$agree 		        = (post("agree") == "on") ? 1 : 0;
		
		if (!$agree) {
			ms(array(
				"status"  => "error",
				"message" => lang("you_must_confirm_to_the_conditions_before_place_order")
			));
		}

		if ($mass_order == "") {
			ms(array(
				"status"  => "error",
				"message" => lang("field_cannot_be_blank")
			));
		}

		/*----------  get balance   ----------*/
		$user = $this->model->get("balance", $this->tb_users, ['id' => session('uid')]);
		
		if ($user->balance == 0) {
			ms(array(
				"status"  => "error",
				"message" => lang("you_do_not_have_enough_funds_to_place_order")
			));
		}
		$total_order  = 0;
		$total_errors = 0;
		$sum_charge = 0;
		$error_details = array();
		$orders = array();
		if (is_array($mass_order)) {
			foreach ($mass_order as $key => $row) {
				$order = explode("|", $row);

				// check format
				$order_count = count($order);
				if ($order_count > 3  || $order_count <= 2) {
					$error_details[$row] = lang("invalid_format_place_order");
					continue;
				}
				$service_id = $order[0];
				$quantity   = $order[1];
				$link       = $order[2];

				// check service id
				$check_service = $this->model->check_record("*", $this->tb_services, $service_id, false, true);
				if (empty($check_service)) {
					$error_details[$row] = lang("service_id_does_not_exists");
					continue;
				}

				// check quantity and balance
				$min          = $check_service->min;
				$max          = $check_service->max;
				$price        = get_user_price(session('uid'), $check_service);
				$charge       = (double)$price*($quantity/1000);

				if ($quantity <= 0 || $quantity < $min) {
					$error_details[$row] = lang("quantity_must_to_be_greater_than_or_equal_to_minimum_amount");
					continue;
				}	
						
				if ($quantity > $max) {
					$error_details[$row] = lang("quantity_must_to_be_less_than_or_equal_to_maximum_amount");
					continue;
				}

				// Order charge to .00 decimal points
                $charge = number_format($charge, 2, '.', '');

                /*----------  Get Formal Charge and profit  ----------*/
				$formal_charge = ($check_service->original_price * $charge) / $check_service->price;
				$profit        = $charge - $formal_charge;

				// every thing is ok
				$orders[] = array(
					"ids" 	            => ids(),
					"uid" 	            => session("uid"),
					"cate_id"           => $check_service->cate_id,
					"service_id"        => $service_id,
					"link" 	            => $link,
					"quantity" 	        => $quantity,
					"charge" 	        => $charge,
					"formal_charge" 	=> $formal_charge,
					"profit" 	        => $profit,
					"api_provider_id"  	=> $check_service->api_provider_id,
					"api_service_id"  	=> $check_service->api_service_id,
					"api_order_id"  	=> (!empty($check_service->api_provider_id) && !empty($check_service->api_service_id)) ? -1 : 0,
					"status"			=> 'pending',
					"changed" 	        => NOW,
					"created" 	        => NOW,
				);
				$sum_charge += $charge;
			}

			// check sum_charge and balance
			if ($sum_charge > $user->balance) {
				ms(array(
					"status"  => "error",
					"message" => lang("not_enough_funds_on_balance")
				));
			}
			if (!empty($orders)) {
				$this->db->insert_batch($this->tb_order, $orders);
				$new_balance = $user->balance - $sum_charge;
				$this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => session("uid")]);
			}
		}
		if (!empty($error_details)) {
			$this->load->view("add/mass_order_notification", ["error_details" => $error_details]);
		}else{
			ms(array(
				"status"  => "success",
				"message" => lang("place_order_successfully")
			));
		}

	}

	/*----------  Logs  ----------*/
	public function log($order_status = ""){
		if ($order_status == "") {
			$order_status = "all";
		}

		$number_error_orders = 0;
		$total_provider_price = 0;
		if (get_role('user') && in_array($order_status, ['fail', 'error'])) {
          redirect(cn('order/log/all'));
        }

        if (get_role('admin')) {
        	$number_error_orders = $this->model->get_count_orders('error');
        	// Only calculate total provider price for error orders (optimization)
        	if ($order_status == 'error') {
        		$total_provider_price = $this->model->get_total_provider_price($order_status);
        	}
        }

		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		$query = array();
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}

		$config = array(
			'base_url'           => cn(get_class($this)."/log/".$order_status.$query_string),
			'total_rows'         => $this->model->get_order_logs_list(true, $order_status),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$order_logs = $this->model->get_order_logs_list(false, $order_status, $limit_per_page, $page * $limit_per_page);
		$data = array(
			"module"                        => get_class($this),
			"columns"                       => $this->columns,
			"order_logs"                    => $order_logs,
			"order_status"                  => $order_status,
			"links"                         => $links,
			"number_error_orders"           => $number_error_orders,
			"total_provider_price"          => $total_provider_price,
		);
		$this->template->build('logs/logs', $data);
	}
	
	/**
	 * Download all orders as HTML file
	 */
	public function download_html($order_status = ""){
		// Validate session
		if (!session('uid')) {
			redirect(cn('auth/login'));
			return;
		}
		
		if ($order_status == "") {
			$order_status = "all";
		}
		
		// Security: Users can only download their own orders
		if (get_role('user') && in_array($order_status, ['fail', 'error'])) {
			redirect(cn('order/log/all'));
			return;
		}
		
		// Get all orders (without pagination)
		// Note: Limited to 10,000 orders to prevent memory issues
		// For larger datasets, consider implementing pagination or chunked downloads
		$max_orders = get_option('max_orders_download', 10000);
		$order_logs = $this->model->get_order_logs_list(false, $order_status, $max_orders, 0);
		
		if (empty($order_logs)) {
			redirect(cn($this->module.'/log/'.$order_status));
			return;
		}
		
		// Get user information
		$user = $this->model->get("*", $this->tb_users, ['id' => session('uid')]);
		
		// Get website information
		$website_name = get_option('website_name', 'SMM Panel');
		$website_logo = get_option('website_logo', BASE."assets/images/logo.png");
		
		// Get currency settings
		$current_currency = get_current_currency();
		$currency_symbol = $current_currency ? $current_currency->symbol : get_option("currency_symbol","$");
		$decimal_places = get_option('currency_decimal', 2);
		$decimalpoint = get_option('currency_decimal_separator', 'dot') == 'comma' ? ',' : '.';
		$separator = get_option('currency_thousand_separator', 'space') == 'space' ? ' ' : (get_option('currency_thousand_separator', 'comma') == 'comma' ? ',' : '.');
		
		// Generate HTML content
		$html = $this->generate_html_template($order_logs, $user, $website_name, $website_logo, $currency_symbol, $decimal_places, $decimalpoint, $separator, $order_status);
		
		// Set filename
		$filename = 'orders_' . $order_status . '_' . date('Y-m-d_H-i-s') . '.html';
		
		// Clear any output buffers to prevent header issues
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		// Force download with proper headers
		$this->output
			->set_status_header(200)
			->set_content_type('text/html', 'utf-8')
			->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
			->set_header('Content-Length: ' . strlen($html))
			->set_header('Cache-Control: no-cache, no-store, must-revalidate')
			->set_header('Pragma: no-cache')
			->set_header('Expires: 0')
			->set_output($html);
	}
	
	/**
	 * Generate HTML template for orders download
	 */
	private function generate_html_template($order_logs, $user, $website_name, $website_logo, $currency_symbol, $decimal_places, $decimalpoint, $separator, $order_status){
		$download_datetime = date('F d, Y h:i A');
		$user_email = $user ? $user->email : 'N/A';
		$user_role = $user ? ucfirst($user->role) : 'N/A';
		
		// Panel branding colors
		$primary_color = '#003a75';
		$success_color = '#27ae60';
		$warning_color = '#f39c12';
		$danger_color = '#e74c3c';
		$text_color = '#2c3e50';
		$border_color = '#bdc3c7';
		
		$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($website_name) . ' - Orders Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: ' . $text_color . ';
            background: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 3px solid ' . $primary_color . ';
            margin-bottom: 30px;
        }
        
        .logo {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            color: ' . $primary_color . ';
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .info-item {
            padding: 10px;
        }
        
        .info-item label {
            font-weight: 600;
            color: ' . $primary_color . ';
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item span {
            font-size: 16px;
            color: ' . $text_color . ';
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        thead {
            background: ' . $primary_color . ';
            color: white;
        }
        
        th {
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid ' . $border_color . ';
            font-size: 14px;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-inprogress { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-partial { background: #f8d7da; color: #721c24; }
        .status-canceled { background: #e2e3e5; color: #383d41; }
        .status-error { background: #f8d7da; color: #721c24; }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid ' . $border_color . ';
            text-align: center;
            color: #7f8c8d;
            font-size: 13px;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="' . htmlspecialchars($website_logo) . '" alt="' . htmlspecialchars($website_name) . '" class="logo">
            <h1>' . htmlspecialchars($website_name) . '</h1>
            <p>Orders Report - ' . ucfirst($order_status) . '</p>
        </div>
        
        <div class="info-section">
            <div class="info-item">
                <label>Downloaded By:</label>
                <span>' . htmlspecialchars($user_email) . '</span>
            </div>
            <div class="info-item">
                <label>User Role:</label>
                <span>' . htmlspecialchars($user_role) . '</span>
            </div>
            <div class="info-item">
                <label>Download Date:</label>
                <span>' . $download_datetime . '</span>
            </div>
            <div class="info-item">
                <label>Total Orders:</label>
                <span>' . count($order_logs) . '</span>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>';
		
		// Admin columns
		if (get_role("admin") || get_role("supporter")) {
			$html .= '
                        <th>API Order ID</th>
                        <th>User Email</th>';
		}
		
		$html .= '
                        <th>Service</th>
                        <th>Link/Username</th>
                        <th>Quantity</th>
                        <th>Start Count</th>
                        <th>Remains</th>
                        <th>Charge</th>';
		
		if (get_role("admin") || get_role("supporter")) {
			$html .= '
                        <th>Profit</th>';
		}
		
		$html .= '
                        <th>Created</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>';
		
		foreach ($order_logs as $row) {
			$charge_display = $currency_symbol . number_format($row->charge, $decimal_places, $decimalpoint, $separator);
			
			// Calculate profit for admin
			// Note: Both charge and formal_charge should be in the same currency for accurate profit calculation
			$profit_display = '';
			if (get_role("admin") || get_role("supporter")) {
				if (is_numeric($row->charge) && is_numeric($row->formal_charge) && $row->charge > 0 && $row->formal_charge > 0) {
					// Calculate profit (assuming both values are in same currency)
					$profit = $row->charge - $row->formal_charge;
					$profit_display = $currency_symbol . number_format($profit, $decimal_places, $decimalpoint, $separator);
				} else {
					$profit_display = $currency_symbol . '0' . $decimalpoint . '00';
				}
			}
			
			// Status badge class
			$status_class = 'status-' . str_replace(' ', '-', strtolower($row->status));
			
			$html .= '
                    <tr>
                        <td>' . htmlspecialchars($row->id) . '</td>';
			
			if (get_role("admin") || get_role("supporter")) {
				$api_order_id = ($row->api_order_id == 0 || $row->api_order_id == -1) ? '-' : htmlspecialchars($row->api_order_id);
				$html .= '
                        <td>' . $api_order_id . '</td>
                        <td>' . htmlspecialchars($row->user_email) . '</td>';
			}
			
			$html .= '
                        <td>' . htmlspecialchars($row->service_id . ' - ' . $row->service_name) . '</td>
                        <td style="word-break: break-all;">' . htmlspecialchars($row->link) . '</td>
                        <td>' . number_format($row->quantity, 0, $decimalpoint, $separator) . '</td>
                        <td>' . number_format($row->start_counter, 0, $decimalpoint, $separator) . '</td>
                        <td>' . number_format($row->remain, 0, $decimalpoint, $separator) . '</td>
                        <td>' . $charge_display . '</td>';
			
			if (get_role("admin") || get_role("supporter")) {
				$html .= '
                        <td>' . $profit_display . '</td>';
			}
			
			$html .= '
                        <td>' . htmlspecialchars(date('M d, Y H:i', strtotime($row->created))) . '</td>
                        <td><span class="status-badge ' . $status_class . '">' . htmlspecialchars(ucfirst($row->status)) . '</span></td>
                    </tr>';
		}
		
		$html .= '
                </tbody>
            </table>
        </div>
        
        <div class="footer">
            <p><strong>' . htmlspecialchars($website_name) . '</strong></p>
            <p>This is a computer-generated document. No signature is required.</p>
            <p>Downloaded on ' . $download_datetime . '</p>
        </div>
    </div>
</body>
</html>';
		
		return $html;
	}

	/*----------  Order details for Dripfeed and Subscription  ----------*/
	public function log_details($id = ""){
		$orders = $this->model->get_log_details($id);
		if (!empty($orders)) {
			$data = array(
				"module"     => get_class($this),
				"columns"    => $this->columns,
				"order_logs" => $orders,
			);
			$this->template->build("logs/ajax_search", $data);
		}else{
			redirect(cn('dripfeed'));
		}
	}

	public function log_update($ids = ""){
		$order = $this->model->get("*", $this->tb_order, ['ids' => $ids]);
		if (!$order) {
			redirect(cn($this->module.'/log'));
		}
	
		if (in_array($order->status, ['pending', 'processing', 'inprogress'])) {
			$order_status_array = ['pending', 'processing', 'inprogress', 'completed', 'partial', 'canceled', 'error'];
		}
		
		if (in_array($order->status, ['canceled'])) {
			$order_status_array = ['canceled'];
		}
	
		if (in_array($order->status, ['completed'])) {
			$order_status_array = ['completed', 'canceled', 'partial'];
		}
	
		if (in_array($order->status, ['partial'])) {
			$order_status_array = ['canceled', 'partial'];
		}
	
		if (in_array($order->status, ['error'])) {
			$order_status_array = ['canceled', 'error', 'partial', 'completed'];
		}
	
		$data = array(
			"module"                => get_class($this),
			"order"                 => $order,
			"order_status_array"    => $order_status_array,
		);
		$this->load->view('logs/update', $data);
	}
	

		public function ajax_logs_update($ids = ""){
    if (!get_role('admin')) _validation('error', "Permission Denied!");
    
    $link           = post("link");
    $start_counter  = post("start_counter");
    $remains        = post("remains");
    $status         = post("status");

    if($link == ""){
        ms(array(
            "status"  => "error",
            "message" => lang("link_is_required")
        ));
    }

    if(!is_numeric($start_counter) && $start_counter != ""){
        ms(array(
            "status"  => "error",
            "message" => lang("start_counter_is_a_number_format")
        ));
    }

    if(!is_numeric($remains) && $remains != ""){
        ms(array(
            "status"  => "error",
            "message" => lang("start_counter_is_a_number_format")
        ));
    }

    $data = array(
        "link"              => $link,
        "status"            => $status,
        "start_counter"     => $start_counter,
        "remains"           => $remains,
        "changed"           => NOW,
    );
    
    // Set completed_at timestamp when status is changed to 'completed'
    if ($status == 'completed') {
        $data['completed_at'] = NOW;
    }

    // ✅ FIX: Now fetching BOTH id (numeric) and ids (hash)
    $check_item = $this->model->get("id, ids, cate_id, service_id, service_type, api_provider_id, api_service_id, charge, uid, quantity, status, formal_charge, profit", $this->tb_order, "ids = '{$ids}'");
    
    if(!empty($check_item)){
        /*----------  If status = refund  ----------*/
        if ($status == "refunded" || $status == "partial" || $status == "canceled") {
            $charge = $check_item->charge;
            $charge_back = 0;
            $real_charge = 0;
            $formal_charge = 0;
            $profit = 0;

            if ($status == "partial") {
                $charge_back = ($charge * $remains) / $check_item->quantity;
                $real_charge = $charge - $charge_back;

                $formal_charge = $check_item->formal_charge * (1 - ($remains / $check_item->quantity ));
                $profit = $check_item->profit * (1 - ($remains / $check_item->quantity ));
            }

            $user = $this->model->get("id, balance, whatsapp_number", $this->tb_users, ["id"=> $check_item->uid]);
            if (!empty($user) && !in_array($check_item->status, array('partial', 'cancelled', 'refunded'))) {
                $balance = $user->balance;
                $refund_amount = $charge - $real_charge;
                $new_balance = $balance + $refund_amount;
                $this->db->update($this->tb_users, ["balance" => $new_balance], ["id"=> $check_item->uid]);
                
                // ✅ FIX: Log balance refund with NUMERIC id (not hash ids)
                if ($refund_amount > 0) {
                    $this->load->helper('balance_logs');
                    log_refund(
                        $check_item->uid,           // user_id
                        $check_item->id,            // ✅ NOW USING numeric id (2100) instead of ids (hash)
                        $refund_amount,             // amount
                        $balance,                   // old_balance
                        $new_balance,               // new_balance
                        $status                     // reason
                    );
                }

                // Send WhatsApp notification for order cancellation or partial completion
                $this->load->library('whatsapp_notification');
                if ($this->whatsapp_notification->is_configured() && !empty($user->whatsapp_number)) {
                    // Get service name
                    $service = $this->model->get("name", SERVICES, ["id" => $check_item->service_id]);
                    $service_name = isset($service->name) ? $service->name : 'Unknown Service';

                    if ($status == "canceled" || $status == "refunded") {
                        // Send order cancelled notification
                        $variables = array(
                            'order_id' => $check_item->id,
                            'refund_amount' => number_format($refund_amount, 2),
                            'service_name' => $service_name,
                            'new_balance' => number_format($new_balance, 2)
                        );
                        $this->whatsapp_notification->send('order_cancelled', $variables, $user->whatsapp_number);
                    } elseif ($status == "partial") {
                        // Send order partial notification
                        $delivered_quantity = $check_item->quantity - $remains;
                        $variables = array(
                            'order_id' => $check_item->id,
                            'service_name' => $service_name,
                            'delivered_quantity' => $delivered_quantity,
                            'ordered_quantity' => $check_item->quantity,
                            'refund_amount' => number_format($refund_amount, 2),
                            'new_balance' => number_format($new_balance, 2)
                        );
                        $this->whatsapp_notification->send('order_partial', $variables, $user->whatsapp_number);
                    }
                }
            }
            $data['charge'] = $real_charge;
            $data['formal_charge'] = $formal_charge;
            $data['profit'] = $profit;
        }

        $this->db->update($this->tb_order, $data, array("ids" => $check_item->ids));
        
        ms(array(
            "status"  => "success",
            "message" => lang("Update_successfully")
        ));
    }else{
        ms(array(
            "status"  => "error",
            "message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")
        ));
    }
}
	public function send_whatsapp_notification() {
		// Check for required parameters
		$whatsapp_number = $this->input->get('whatsapp_number');
		$order_id = $this->input->get('order_id');
		$service_name = urldecode($this->input->get('service_name'));
	
		if (!$whatsapp_number || !$order_id || !$service_name) {
			$this->session->set_flashdata('error', 'Invalid request parameters.');
			redirect('order/log');
		}
	
		try {
			// Fetch additional order details
			$order = $this->model->get("*", $this->tb_order, ['id' => $order_id]);
	
			if (!$order) {
				$this->session->set_flashdata('error', 'Order details not found.');
				redirect('order/log');
			}
	
			// Get WhatsApp API configuration
			$whatsapp_config = $this->model->get("url, api_key, admin_phone", "whatsapp_config", []);
			if (empty($whatsapp_config) || empty($whatsapp_config->url) || empty($whatsapp_config->api_key)) {
				$this->session->set_flashdata('error', 'WhatsApp API URL or API key not configured.');
				redirect('order/log');
			}
	
			// Get API URL, API key, and admin phone from config
			$api_url = $whatsapp_config->url;
			$api_key = $whatsapp_config->api_key;
	
			// Format charge to 2 decimal places
			$formatted_charge = number_format($order->charge, 2);
	
			$formatted_link = filter_var($order->link, FILTER_VALIDATE_URL) ? 
				preg_replace('#^https?://#', '', $order->link) : 
				truncate_string($order->link, 60);
	
			// Prepare message with additional details
			$message = "*✅ Order Completed Successfully!*\n\n"
				. "🔢 *Order ID*: {$order_id}\n"
				. "🛍️ *Service*: {$service_name}\n"
				. "💰 *Total Charge*: ${formatted_charge}\n"
				. "🔗 *Order Link*: {$formatted_link}\n\n"
				. "Thank you for using our services! Your order has been completed successfully.";
	
			// Prepare the data
			$data = [
				"apiKey" => $api_key,  // Include API key for validation
				"phoneNumber" => $whatsapp_number,
				"message" => $message
			];
	
			// Initialize cURL
			$ch = curl_init($api_url);
	
			curl_setopt_array($ch, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_TIMEOUT => 30
			]);
	
			$response = curl_exec($ch);
	
			if (curl_errno($ch)) {
				$this->session->set_flashdata('error', 'Failed to send WhatsApp notification: ' . curl_error($ch));
				curl_close($ch);
				redirect('order/log');
			}
	
			curl_close($ch);
	
			$responseData = json_decode($response, true);
	
			// Update notification status
			$this->db->where('order_id', $order_id);
			$update_result = $this->db->update('order_notifications', [
				'is_notified' => 1,
				'created_at' => date('Y-m-d H:i:s')
			]);
	
			if ($this->db->affected_rows() == 0) {
				$insert_result = $this->db->insert('order_notifications', [
					'order_id' => $order_id,
					'is_notified' => 1,
					'created_at' => date('Y-m-d H:i:s')
				]);
	
				if (!$insert_result) {
					log_message('error', 'Failed to insert notification status: ' . $this->db->error()['message']);
					$this->session->set_flashdata('error', 'Failed to update notification status.');
					redirect('order/log');
				}
			}
	
			if (isset($responseData['status']) && $responseData['status'] == 'success') {
				$this->session->set_flashdata('success', 'WhatsApp notification sent successfully!');
			} else {
				$this->session->set_flashdata('error', 'Failed to send WhatsApp notification.');
			}
	
		} catch (Exception $e) {
			log_message('error', 'Exception in send_whatsapp_notification: ' . $e->getMessage());
			$this->session->set_flashdata('error', 'Error: ' . $e->getMessage());
		}
	
		redirect('order/log/completed');
	}
	

    public function refill_order($ids = ""){
		$refill_status	= "yes";
		// $status 		= "processing";

		$data = array(
			"refill_status" => $refill_status,
			"changed" 		=> NOW,
		);

		$this->db->update($this->tb_order, $data, array("ids" => $ids));

		$check_item_refill = $this->model->get("id, api_order_id, uid, status", $this->tb_order, "ids = '{$ids}'");
		if(!empty($check_item_refill)){
			// if ($status == "completed") {			
			// }
			$check_item_user = $this->model->get("id, first_name, last_name, email", $this->tb_users, "id = '{$check_item_refill->uid}'");
			if(!empty($check_item_refill)){


						$subject = get_option('email_new_refill_subject', '');
						$subject = str_replace("{{website_name}}", get_option("website_name", "SmartPanel"), $subject);

						$email_content = get_option('email_new_refill_content', '');
						$email_content = str_replace("{{user_firstname}}", $check_item_user->first_name, $email_content);
						$email_content = str_replace("{{user_lastname}}", $check_item_user->last_name, $email_content);
						$email_content = str_replace("{{user_email}}", $check_item_user->email, $email_content);
						$email_content = str_replace("{{order_id}}", $check_item_refill->id, $email_content);
						$email_content = str_replace("{{api_order_id}}", $check_item_refill->api_order_id, $email_content);

						$admin_id = $this->model->get("id", $this->tb_users, "role = 'admin'","id","ASC")->id;
						if ($admin_id == "") {
							$admin_id = 1;
						}
						
						$check_send_email_issue = $this->model->send_email( $subject, $email_content, $admin_id, false);
						if($check_send_email_issue){
							ms(array(
								"status" => "error",
								"message" => $check_send_email_issue,
							));
						}
						
			}
			ms(array(
				"status"  => "success",
				"message" => lang("Refill_successfully")
			));
		}else{
			ms(array(
				"status"  => "error",
				"message" => lang("There_was_an_error_processing_your_request_Please_try_again_later")
			));
		}
	}

	// function Search Data
	public function search(){
		$k           = get('query');
		$k           = htmlspecialchars($k);
		$search_type = (int)get('search_type');
		$data_search = ['k' => $k, 'type' => $search_type];
		$page        = (int)get("p");
		$page        = ($page > 0) ? ($page - 1) : 0;
		$limit_per_page = get_option("default_limit_per_page", 10);
		$query = ['query' => $k, 'search_type' => $search_type];
		$query_string = "";
		if(!empty($query)){
			$query_string = "?".http_build_query($query);
		}
		$config = array(
			'base_url'           => cn(get_class($this)."/search".$query_string),
			'total_rows'         => $this->model->get_count_orders_by_search($data_search),
			'per_page'           => $limit_per_page,
			'use_page_numbers'   => true,
			'prev_link'          => '<i class="fe fe-chevron-left"></i>',
			'first_link'         => '<i class="fe fe-chevrons-left"></i>',
			'next_link'          => '<i class="fe fe-chevron-right"></i>',
			'last_link'          => '<i class="fe fe-chevrons-right"></i>',
		);
		$this->pagination->initialize($config);
		$links = $this->pagination->create_links();

		$order_logs = $this->model->search_logs_by_get_method($data_search, $limit_per_page, $page * $limit_per_page);
		$data = array(
			"module"       => get_class($this),
			"columns"      => $this->columns,
			"order_logs"   => $order_logs,
			"order_status" => '',
			"links"        => $links,
		);
		$this->template->build('logs/logs', $data);
	}

	public function ajax_order_by($status = ""){
		if (!empty($status) && $status !="" ) {
			$order_logs = $this->model->get_order_logs_list(false, $status);
			$data = array(
				"module"     => get_class($this),
				"columns"    => $this->columns,
				"order_logs" => $order_logs,
			);
			$this->load->view("logs/ajax_search", $data);
		}
	}

	public function ajax_show_list_custom_mention($ids = ''){
		if (!get_role('admin')) _validation('error', "Permission Denied!");

		$order    = $this->model->get("*", $this->tb_order, ['ids' => $ids]);
		if (!empty($order)) {
			$mentions = get_list_custom_mention($order);
            if($mentions->exists_list){
				$data = array(
					"module"   		=> get_class($this),
					"title" 	    => $mentions->title,
					"list" 	        => $mentions->list,
				);
				$this->load->view('logs/show_list_custom_mention', $data);
			}
		}
	}

	/**
	 * Cancel Order - Send cancellation request to API provider
	 * Uses provider's API configuration from database
	 * Just sends the request - status will be updated by provider's cron/API
	 */
	public function ajax_cancel_order() {
		// Check if user has permission (admin or order owner)
		$order_ids = post('order_ids');
		
		if (empty($order_ids)) {
			ms(array(
				"status" => "error",
				"message" => lang("Invalid order ID")
			));
		}

		// Get order details (including note field to check for previous cancel requests)
		$order = $this->model->get(
			"id, ids, uid, service_id, api_provider_id, api_order_id, api_service_id, status, charge, quantity, formal_charge, profit, created, note", 
			$this->tb_order, 
			['ids' => $order_ids]
		);

		if (empty($order)) {
			ms(array(
				"status" => "error",
				"message" => lang("Order not found")
			));
		}

		// Check permission - admin or order owner
		if (!get_role('admin') && $order->uid != session('uid')) {
			ms(array(
				"status" => "error",
				"message" => lang("Permission Denied!")
			));
		}

		// Check if order can be cancelled (only pending, processing, inprogress)
		if (!in_array($order->status, ['pending', 'processing', 'inprogress'])) {
			ms(array(
				"status" => "error",
				"message" => lang("This order cannot be cancelled. Only pending, processing or in-progress orders can be cancelled.")
			));
		}

		// Check if cancel request was already sent
		if (!empty($order->note) && strpos($order->note, 'Cancel request sent') !== false) {
			ms(array(
				"status" => "error",
				"message" => lang("Cancel request has already been sent for this order.")
			));
		}

		// Check if order has been sent to API provider
		// Check for empty or missing API order ID (handles both 0, -1, empty string, null)
		if (empty($order->api_provider_id) || empty($order->api_order_id) || $order->api_order_id == 0 || $order->api_order_id == -1) {
			// Order not sent to API yet, cancel it immediately locally
			$this->cancel_order_locally($order);
			ms(array(
				"status" => "success",
				"message" => lang("Order cancelled successfully")
			));
		}

		// Get API provider details
		$provider = $this->model->get(
			"id, ids, name, url, key", 
			$this->tb_api_providers, 
			['id' => $order->api_provider_id]
		);

		if (empty($provider)) {
			ms(array(
				"status" => "error",
				"message" => lang("API provider configuration not found")
			));
		}

		// Send cancellation request to provider API
		$cancel_result = $this->send_cancel_request_to_provider($provider, $order->api_order_id);

		if ($cancel_result['success']) {
			// Save cancel request to database for persistence
			$this->db->update($this->tb_order, [
				'note' => 'Cancel request sent at ' . date('Y-m-d H:i:s'),
				'changed' => NOW
			], ["ids" => $order->ids]);
			
			ms(array(
				"status" => "success",
				"message" => lang("Cancellation request sent successfully.")
			));
		} else {
			ms(array(
				"status" => "error",
				"message" => $cancel_result['message']
			));
		}
	}

	/**
	 * Send cancel request to API provider
	 * @param object $provider Provider details
	 * @param int $api_order_id API order ID
	 * @return array ['success' => bool, 'message' => string]
	 */
	private function send_cancel_request_to_provider($provider, $api_order_id) {
		// Prepare cancel request data (following the test script format)
		$post_data = array(
			'key'    => $provider->key,
			'action' => 'cancel',
			'orders' => $api_order_id
		);

		// Send request using cURL
		$ch = curl_init($provider->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$response = curl_exec($ch);
		$curl_error = curl_errno($ch);
		$curl_error_msg = curl_error($ch);
		curl_close($ch);

		// Handle cURL errors
		if ($curl_error) {
			return array(
				'success' => false,
				'message' => 'Failed to connect to API provider: ' . $curl_error_msg
			);
		}

		// Parse response
		$result = json_decode($response, true);

		// Log the raw response for debugging
		log_message('info', 'API cancel response for order ' . $api_order_id . ': ' . $response);

		// Check for JSON decode error
		if (json_last_error() !== JSON_ERROR_NONE) {
			log_message('error', 'Failed to parse API response as JSON: ' . json_last_error_msg());
			return array(
				'success' => false,
				'message' => 'Invalid response from provider (not valid JSON)'
			);
		}

		// Handle array response format: [0 => ['order' => xxx, 'cancel' => yyy]]
		if (isset($result[0]) && is_array($result[0])) {
			$response_data = $result[0];
			
			// Check if cancel field exists
			if (isset($response_data['cancel'])) {
				// If cancel is an array with error, it's a failure
				if (is_array($response_data['cancel']) && isset($response_data['cancel']['error'])) {
					return array(
						'success' => false,
						'message' => 'Provider error: ' . $response_data['cancel']['error']
					);
				}
				
				// If cancel is a number/string, it's successful (the cancel ID)
				if (!is_array($response_data['cancel'])) {
					return array(
						'success' => true,
						'message' => 'Order cancelled via API provider (Cancel ID: ' . $response_data['cancel'] . ')'
					);
				}
			}
		}

		// Check for top-level error field (alternative format)
		if (isset($result['error']) && !empty($result['error'])) {
			return array(
				'success' => false,
				'message' => 'Provider error: ' . $result['error']
			);
		}

		// Check for simple cancel field at top level
		if (isset($result['cancel']) && !is_array($result['cancel']) && !empty($result['cancel'])) {
			return array(
				'success' => true,
				'message' => 'Order cancelled via API provider'
			);
		}

		// Unknown response format
		log_message('error', 'Unknown API cancel response format: ' . json_encode($result));
		return array(
			'success' => false,
			'message' => 'Unable to parse provider response format'
		);
	}

	/**
	 * Cancel order locally and process refund
	 * @param object $order Order details
	 * @param string $note Optional note for cancellation
	 * @return bool True on success, false on failure
	 */
	private function cancel_order_locally($order, $note = 'Cancelled by user') {
		// Get user details
		$user = $this->model->get("id, balance, whatsapp_number", $this->tb_users, ["id" => $order->uid]);
		
		if (empty($user)) {
			return false;
		}

		// Skip if order is already canceled, refunded, or completed
		if (in_array($order->status, ['canceled', 'refunded', 'completed'])) {
			log_message('info', "Order {$order->id} skipped - already in final status: {$order->status}");
			return false;
		}

		// Calculate refund amount (full charge)
		$refund_amount = $order->charge;
		$new_balance = $user->balance + $refund_amount;

		// Update user balance
		$this->db->update($this->tb_users, ["balance" => $new_balance], ["id" => $order->uid]);

		// Log balance refund
		$this->load->helper('balance_logs');
		if ($refund_amount > 0) {
			log_refund(
				$order->uid,
				$order->id,
				$refund_amount,
				$user->balance,
				$new_balance
			);
		}

		// Update order status
		$this->db->update($this->tb_order, [
			'status' => 'canceled',
			'note' => $note,
			'charge' => 0,
			'formal_charge' => 0,
			'profit' => 0,
			'changed' => NOW
		], ["ids" => $order->ids]);

		// Send WhatsApp notification if configured
		$this->load->library('whatsapp_notification');
		if ($this->whatsapp_notification->is_configured() && !empty($user->whatsapp_number)) {
			// Get service name
			$service = $this->model->get("name", SERVICES, ["id" => $order->service_id]);
			$service_name = isset($service->name) ? $service->name : 'Unknown Service';

			$variables = array(
				'order_id' => $order->id,
				'refund_amount' => number_format($refund_amount, 2),
				'service_name' => $service_name,
				'new_balance' => number_format($new_balance, 2)
			);
			$this->whatsapp_notification->send('order_cancelled', $variables, $user->whatsapp_number);
		}

		log_message('info', "Order {$order->id} cancelled successfully. Refund: {$refund_amount}, New balance: {$new_balance}");
		return true;
	}

	public function change_status($status = "", $ids = "") {
		if (!get_role('admin')) {
			_validation('error', "Permission Denied!");
		}
	
		// Check if it's a bulk action
		if ($ids == "bulk") {
			// Fetch all orders in 'error' status - need full order details for cancellation
			$orders = $this->model->get_bulk_orders_full('error');
			if (!empty($orders)) {
				$cancelled_count = 0;
				$skipped_count = 0;
				$total_refunded = 0;
				
				foreach ($orders as $order) {
					switch ($status) {
						case 'resend_order':
							$data = [
								'status'       => 'pending',
								'note'         => 'Resent',
								'api_order_id' => -1,
							];
							$this->db->update($this->tb_order, $data, ['ids' => $order->ids]);
							break;
	
						case 'cancel_order':
							// Use the shared cancel_order_locally method for proper refund handling
							$result = $this->cancel_order_locally($order, 'Bulk cancelled by admin');
							if ($result) {
								$cancelled_count++;
								$total_refunded += $order->charge;
							} else {
								$skipped_count++;
							}
							break;
					}
				}
				
				// Show success message with statistics for cancel operations
				if ($status == 'cancel_order') {
					$current_currency = get_current_currency();
					$currency_symbol = ($current_currency && isset($current_currency->symbol)) ? $current_currency->symbol : (get_option('currency_symbol', '$') ?: '$');
					
					$message = "Bulk cancellation completed. Cancelled: {$cancelled_count}, Skipped: {$skipped_count}";
					if ($total_refunded > 0) {
						$message .= ", Total refunded: " . $currency_symbol . number_format($total_refunded, 2);
					}
					$this->session->set_flashdata('success', $message);
				}
				
				redirect(cn('order/log/error'));
			} else {
				_validation('error', "No orders found in error status!");
			}
		}
	
		// Handle single order actions
		if (!is_string($ids)) {
			redirect(cn('order/log'));
		}
	
		$check_item = $this->model->get("ids", $this->tb_order, ['ids' => $ids]);
		if ($check_item) {
			switch ($status) {
				case 'resend_order':
					$data = [
						'status'       => 'pending',
						'note'         => 'Resent',
						'api_order_id' => -1,
					];
					$this->db->update($this->tb_order, $data, ['ids' => $check_item->ids]);
					redirect(cn('order/log/error'));
					break;
	
				default:
					# code...
					break;
			}
		}
		redirect(cn('order/log'));
	}
	
	
	// Delete
	public function ajax_log_delete_item($ids = ""){
		$this->model->delete($this->tb_order, $ids, false);
	}

	/**
	 * UPDATE WHATSAPP NUMBER - NOW GOES THROUGH MODEL
	 */
	public function update_whatsapp_number() {
		// Check if user is logged in
		if (!session('uid')) {
			$this->session->set_flashdata('message', 'User not logged in.');
			$this->session->set_flashdata('message_type', 'danger');
			redirect(cn('order/add'));
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST' && $this->input->post('whatsapp_number')) {
			$whatsapp_number = trim($this->input->post('whatsapp_number'));
			$user_id = session('uid');

			// Validate WhatsApp number format
			if (!preg_match('/^\+?[0-9]{10,15}$/', $whatsapp_number)) {
				$this->session->set_flashdata('message', 'Invalid WhatsApp number format.');
				$this->session->set_flashdata('message_type', 'danger');
				redirect(cn('order/add'));
			}

			// Call model to update WhatsApp number
			$update_result = $this->model->update_user_whatsapp_number($user_id, $whatsapp_number);

			if ($update_result) {
				$this->session->set_flashdata('message', 'Thanks, WhatsApp number updated successfully.');
				$this->session->set_flashdata('message_type', 'success');
			} else {
				$this->session->set_flashdata('message', 'Failed to update WhatsApp number.');
				$this->session->set_flashdata('message_type', 'danger');
			}
		}

		redirect(cn('order/add'));
	}

	/**
	 * Get API providers list for balance card
	 * Returns JSON with providers data including LIVE balance fetched from API
	 */
	public function get_api_providers_balance(){
		// Only admin can access this
		if (!get_role('admin')) {
			echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
			return;
		}

		$this->load->model('api_provider/api_provider_model');
		$providers = $this->api_provider_model->get_api_lists(true); // Get only active providers

		$result = [];
		if (!empty($providers)) {
			foreach ($providers as $provider) {
				// Fetch LIVE balance from API provider using direct cURL
				$live_balance = $provider->balance;
				$live_currency = $provider->currency_code;
				
				$data_post = ['key' => $provider->key, 'action' => 'balance'];
				$data_connect = $this->connect_api_direct($provider->url, $data_post);
				
				if ($data_connect !== false) {
					$data_connect = json_decode($data_connect);
					if (!empty($data_connect) && isset($data_connect->balance)) {
						$live_balance = $data_connect->balance;
						if (isset($data_connect->currency)) {
							$live_currency = $data_connect->currency;
						}
						
						// Update database with fresh balance
						$update_data = [
							'balance' => $live_balance,
							'currency_code' => $live_currency,
							'changed' => NOW,
						];
						$this->db->update(API_PROVIDERS, $update_data, ['ids' => $provider->ids]);
					}
				}
				
				$result[] = [
					'id' => $provider->id,
					'ids' => $provider->ids,
					'name' => htmlspecialchars($provider->name, ENT_QUOTES),
					'balance' => $live_balance,
					'currency_code' => $live_currency,
				];
			}
		}

		echo json_encode(['status' => 'success', 'data' => $result]);
	}

	/**
	 * Refresh balance for a specific API provider
	 * Returns updated balance
	 */
	public function refresh_provider_balance(){
		// Only admin can access this
		if (!get_role('admin')) {
			echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
			return;
		}

		$provider_ids = $this->input->post('provider_ids');
		if (empty($provider_ids)) {
			echo json_encode(['status' => 'error', 'message' => 'Provider ID is required']);
			return;
		}

		$this->load->model('api_provider/api_provider_model');
		$provider = $this->api_provider_model->get("id, ids, name, url, key, balance, currency_code", API_PROVIDERS, ["ids" => $provider_ids]);

		if (empty($provider)) {
			echo json_encode(['status' => 'error', 'message' => 'Provider not found']);
			return;
		}

		// Call API to get latest balance using direct cURL
		$data_post = ['key' => $provider->key, 'action' => 'balance'];
		$data_connect = $this->connect_api_direct($provider->url, $data_post);
		
		if ($data_connect === false) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to connect to API provider. Please check API configuration.']);
			return;
		}

		$data_connect = json_decode($data_connect);

		if (empty($data_connect) || !isset($data_connect->balance)) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid API response. Balance information not found.']);
			return;
		}

		// Update balance via model
		$update_data = [
			'balance' => $data_connect->balance,
			'currency_code' => isset($data_connect->currency) ? $data_connect->currency : $provider->currency_code,
			'changed' => NOW,
		];
		$this->db->update(API_PROVIDERS, $update_data, ['ids' => $provider_ids]);

		echo json_encode([
			'status' => 'success',
			'balance' => $data_connect->balance,
			'currency_code' => isset($data_connect->currency) ? $data_connect->currency : $provider->currency_code,
		]);
	}

	/**
	 * Direct cURL connection to API provider (without license check)
	 * Used to fetch live balance from provider APIs
	 */
	private function connect_api_direct($url, $post = array()){
		$_post = [];
		if (is_array($post)) {
			foreach ($post as $name => $value) {
				$_post[] = $name.'='.urlencode($value);
			}
		}
		
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if (is_array($post)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
		}
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (SmartPanel API Sync)');
		
		$result = curl_exec($ch);
		
		if (curl_errno($ch) != 0 && empty($result)) {
			$result = false;
		}
		
		curl_close($ch);
		return $result;
	}
}