<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class setting extends MX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
        $this->load->model('language/language_model', 'sub_model');
    }

    public function index($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Load WhatsApp API settings from whatsapp_config (single-row pattern)
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,  // may be null if not created yet
        ];

        $this->template->build('index', $data);
    }

    public function get_content($tab = ""){
        $path              = APPPATH.'./modules/setting/views/';
        $path_integrations = APPPATH.'./modules/setting/views/integrations/';
        $tabs = array_merge(
            get_name_of_files_in_dir($path, ['.php']),
            get_name_of_files_in_dir($path_integrations, ['.php'])
        );
        if (($idx = array_search('index', $tabs, true)) !== false) {
            unset($tabs[$idx]);
        }

        if ($tab == "") {
            $tab = "website_setting";
        }
        if (!in_array($tab, $tabs)) {
            redirect(cn('setting'));
        }

        // Also supply API settings here if partial loads happen via AJAX tab switching
        $whatsapp_api = $this->db->get('whatsapp_config')->row();
        $data = [
            "module"       => get_class($this),
            "tab"          => $tab,
            "whatsapp_api" => $whatsapp_api,
        ];
        $this->template->build('index', $data);
    }

    /**
     * Generic settings saver (existing logic).
     * Saves POST keys as options, including whatsapp_number.
     */
    public function ajax_general_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $data              = $this->input->post(NULL, true);
        $default_home_page = $this->input->post("default_home_page", true);

        if (is_array($data)) {
            foreach ($data as $key => $value) {

                if (in_array($key, ['csrf_token_name','csrf_test_name'], true)) {
                    continue;
                }

<<<<<<< HEAD
                if (in_array($key, ['embed_javascript', 'embed_head_javascript', 'manual_payment_content', 'seo_header_code', 'seo_footer_code'])) {
                    $value = isset($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES) : '';
=======
                if (in_array($key, ['embed_javascript', 'embed_head_javascript', 'manual_payment_content'])) {
                    $value = htmlspecialchars(@$_POST[$key], ENT_QUOTES);
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                }

                if (in_array($key, ['midtrans_payment_channels', 'coinpayments_acceptance', 'freekassa_acceptance'], true)) {
                    $value = json_encode($value);
                }

                if ($key === 'new_currecry_rate') {
                    $value = (double)$value;
                    if ($value <= 0) $value = 1;
                }

                if ($key === 'whatsapp_number') {
                    $value = trim($value);
                    $normalized = preg_replace('/[\s\-\(\)]+/', '', $value);
                    if ($normalized !== '' && !preg_match('/^\+?[0-9]{6,20}$/', $normalized)) {
                        ms([
                            'status'  => 'error',
                            'message' => 'Invalid WhatsApp number format'
                        ]);
                    }
                    $value = $normalized;
                }

<<<<<<< HEAD
                // Validate logo and favicon URLs
                if (in_array($key, ['website_logo', 'website_logo_white', 'website_favicon'], true)) {
                    $value = trim($value);
                    // Basic URL/path validation - ensure it's not empty and is a valid format
                    if (!empty($value)) {
                        // Check for potentially dangerous content
                        if (preg_match('/<script|javascript:|onerror=|onclick=/i', $value)) {
                            ms([
                                'status'  => 'error',
                                'message' => 'Invalid URL format detected for ' . $key
                            ]);
                        }
                    }
                }

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
                update_option($key, $value);
            }
        }

        if ($default_home_page != "") {
            $theme_file_path = APPPATH."../themes/config.json";
            if (is_writable(dirname($theme_file_path))) {
                if ($theme_file = @fopen($theme_file_path, "w")) {
                    $txt = '{ "theme" : "'.$default_home_page.'" }';
                    fwrite($theme_file, $txt);
                    fclose($theme_file);
                }
            }
        }

        ms([
            "status"  => "success",
            "message" => lang('Update_successfully')
        ]);
    }

    /**
     * Save WhatsApp API settings (url, api_key, admin_phone) to whatsapp_config table.
     * Table schema expected:
     * id (INT PK, usually 1), url VARCHAR, api_key VARCHAR, admin_phone VARCHAR
     */
    public function ajax_whatsapp_api_settings() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $url         = trim($this->input->post('url', true));
        $api_key     = trim($this->input->post('api_key', true));
        $admin_phone = trim($this->input->post('admin_phone', true));

        // Basic validation (adjust as needed)
        if ($url === '' || $api_key === '' || $admin_phone === '') {
            ms([
                'status'  => 'error',
                'message' => 'All fields are required'
            ]);
        }

        // Normalize admin phone (optional)
        $normalized_phone = preg_replace('/[\s\-\(\)]+/', '', $admin_phone);
        if (!preg_match('/^\+?[0-9]{6,20}$/', $normalized_phone)) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid admin phone format'
            ]);
        }

<<<<<<< HEAD
        // Check if table exists
        if (!$this->db->table_exists('whatsapp_config')) {
            ms([
                'status'  => 'error',
                'message' => 'WhatsApp config table not found. Please check your database setup.'
            ]);
        }

=======
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
        // Ensure single row pattern
        $existing = $this->db->get('whatsapp_config')->row();
        $data = [
            'url'         => $url,
            'api_key'     => $api_key,
            'admin_phone' => $normalized_phone,
        ];

        if ($existing) {
            $this->db->where('id', $existing->id)->update('whatsapp_config', $data);
        } else {
            // Force id=1 (optional) or let auto-increment
            $this->db->insert('whatsapp_config', $data);
        }

<<<<<<< HEAD
        ms([
            'status'  => 'success',
            'message' => lang('Update_successfully'),
            'data'    => $data
        ]);
    }

    /**
     * Save WhatsApp notification settings
     */
    public function ajax_whatsapp_notifications() {
        // Check if it's a POST request
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        $notification_status = $this->input->post('notification_status', true);
        $notification_template = $this->input->post('notification_template', true);

        if (!is_array($notification_status)) {
            $notification_status = array();
        }
        if (!is_array($notification_template)) {
            $notification_template = array();
        }

        // Check if table exists first
        if (!$this->db->table_exists('whatsapp_notifications')) {
            ms([
                'status'  => 'error',
                'message' => 'WhatsApp notifications table not found. Please run the database migration: /database/whatsapp-notifications.sql'
            ]);
        }

        // Get all notifications from database
        $all_notifications = $this->db->order_by('id', 'ASC')->get('whatsapp_notifications')->result();

        if (empty($all_notifications)) {
            ms([
                'status'  => 'error',
                'message' => 'No notification templates found. Please run the database migration.'
            ]);
        }

        $updated_count = 0;
        $total_count = count($all_notifications);

        foreach ($all_notifications as $notification) {
            $event_type = $notification->event_type;
            
            // Update status (1 if checked, 0 if not)
            $status = isset($notification_status[$event_type]) ? 1 : 0;
            
            // Update template if provided
            $template = isset($notification_template[$event_type]) ? trim($notification_template[$event_type]) : $notification->template;

            // Update in database
            $update_data = array(
                'status' => $status,
                'template' => $template
            );

            $this->db->where('event_type', $event_type);
            $this->db->update('whatsapp_notifications', $update_data);

            // Count as updated even if no rows changed (same data)
            $updated_count++;
        }

        ms([
            'status'  => 'success',
            'message' => lang('Update_successfully') . " ({$updated_count}/{$total_count} notifications processed)"
        ]);
    }

    /**
     * Redirect to the new code_parts module.
     * The code parts functionality has been moved to a dedicated module.
     * @deprecated Use code_parts module instead
     */
    public function ajax_code_parts() {
        // Redirect to the new code_parts module
        redirect(cn('code_parts'));
    }

    /**
     * Handle direct access to code_parts tab - redirect to the new module.
     * @deprecated Use code_parts module instead
     */
    public function code_parts() {
        redirect(cn('code_parts'));
    }

    /**
     * Upload and validate custom sitemap.xml file
     */
    public function ajax_sitemap_upload() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // Check if sitemap table exists
        if (!$this->db->table_exists('sitemaps')) {
            ms([
                'status'  => 'error',
                'message' => 'Sitemap table not found. Please run database migration: /database/advanced-seo-settings.sql'
            ]);
        }

        // Check if file was uploaded
        if (empty($_FILES['sitemap_file']['name'])) {
            ms([
                'status'  => 'error',
                'message' => 'No file uploaded'
            ]);
        }

        $validate = $this->input->post('validate_sitemap', true);

        // Configure upload
        $public_dir = APPPATH . '../public/';
        $upload_path = realpath($public_dir);
        
        // Validate path is within expected boundaries
        if ($upload_path === false || strpos($upload_path, realpath(APPPATH . '../')) !== 0) {
            ms([
                'status'  => 'error',
                'message' => 'Invalid upload directory'
            ]);
        }
        
        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'xml';
        $config['max_size']      = 10240; // 10MB
        $config['file_name']     = 'sitemap_temp_' . time() . '.xml';
        $config['overwrite']     = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('sitemap_file')) {
            ms([
                'status'  => 'error',
                'message' => $this->upload->display_errors('', '')
            ]);
        }

        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];
        $content = file_get_contents($file_path);

        // Validate if requested
        if ($validate == 1) {
            // Disable external entities to prevent XXE attacks
            $previous_value = libxml_disable_entity_loader(true);
            libxml_use_internal_errors(true);
            
            $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_NOCDATA);
            
            // Restore previous setting
            libxml_disable_entity_loader($previous_value);
            
            if ($xml === false) {
                @unlink($file_path);
                ms([
                    'status'  => 'error',
                    'message' => 'Invalid XML format. Please check your sitemap file.'
                ]);
            }

            // Check if it's a valid sitemap
            $namespaces = $xml->getNamespaces(true);
            if (!isset($namespaces['']) || $namespaces[''] !== 'http://www.sitemaps.org/schemas/sitemap/0.9') {
                @unlink($file_path);
                ms([
                    'status'  => 'error',
                    'message' => 'Invalid sitemap format. Missing proper sitemap namespace.'
                ]);
            }

            // Count URLs
            $urls_count = count($xml->url);
        } else {
            // Simple URL count without full validation
            $urls_count = substr_count($content, '<url>');
        }

        // Store in database
        $data = [
            'filename'      => 'sitemap.xml',
            'content'       => $content,
            'is_custom'     => 1,
            'urls_count'    => $urls_count,
            'file_size'     => strlen($content),
            'last_modified' => date('Y-m-d H:i:s')
        ];

        // Check if sitemap exists, update or insert
        $existing = $this->db->get('sitemaps')->row();
        if ($existing) {
            $this->db->where('id', $existing->id)->update('sitemaps', $data);
        } else {
            $this->db->insert('sitemaps', $data);
        }

        // Clean up temp file
        @unlink($file_path);

        ms([
            'status'  => 'success',
            'message' => 'Sitemap uploaded successfully! Found ' . $urls_count . ' URLs.'
        ]);
    }

    /**
     * Auto-generate sitemap.xml based on site pages
     */
    public function ajax_sitemap_generate() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // Check if sitemap table exists
        if (!$this->db->table_exists('sitemaps')) {
            ms([
                'status'  => 'error',
                'message' => 'Sitemap table not found. Please run database migration: /database/advanced-seo-settings.sql'
            ]);
        }

        $changefreq = $this->input->post('changefreq', true) ?: 'daily';
        $priority = $this->input->post('priority', true) ?: '0.8';
        $include_custom_pages = $this->input->post('include_custom_pages', true);
        $include_services = $this->input->post('include_services', true);

        // Save settings for future use
        update_option('seo_sitemap_changefreq', $changefreq);
        update_option('seo_sitemap_priority', $priority);

        $urls = [];
        $base_url = rtrim(PATH, '/');

        // Add home page
        $urls[] = [
            'loc'        => $base_url . '/',
            'lastmod'    => date('c'),
            'changefreq' => 'daily',
            'priority'   => '1.0'
        ];

        // Add custom pages if enabled
        if ($include_custom_pages == 1 && $this->db->table_exists('general_custom_page')) {
            $custom_pages = $this->db->where('status', 1)->get('general_custom_page')->result();
            foreach ($custom_pages as $page) {
                $urls[] = [
                    'loc'        => $base_url . '/' . (isset($page->slug) ? $page->slug : 'page/' . $page->ids),
                    'lastmod'    => isset($page->changed) ? date('c', strtotime($page->changed)) : date('c'),
                    'changefreq' => $changefreq,
                    'priority'   => $priority
                ];
            }
        }

        // Add services if enabled
        if ($include_services == 1 && $this->db->table_exists('general_services')) {
            $services = $this->db->where('status', 1)->limit(100)->get('general_services')->result();
            foreach ($services as $service) {
                $urls[] = [
                    'loc'        => $base_url . '/services',
                    'lastmod'    => isset($service->changed) ? date('c', strtotime($service->changed)) : date('c'),
                    'changefreq' => $changefreq,
                    'priority'   => '0.7'
                ];
            }
        }

        // Add common pages
        $common_pages = ['login', 'signup', 'services', 'api', 'faq'];
        foreach ($common_pages as $page) {
            $urls[] = [
                'loc'        => $base_url . '/' . $page,
                'lastmod'    => date('c'),
                'changefreq' => 'weekly',
                'priority'   => '0.6'
            ];
        }

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        // Store in database
        $data = [
            'filename'      => 'sitemap.xml',
            'content'       => $xml,
            'is_custom'     => 0,
            'urls_count'    => count($urls),
            'file_size'     => strlen($xml),
            'last_modified' => date('Y-m-d H:i:s')
        ];

        // Check if sitemap exists, update or insert
        $existing = $this->db->get('sitemaps')->row();
        if ($existing) {
            $this->db->where('id', $existing->id)->update('sitemaps', $data);
        } else {
            $this->db->insert('sitemaps', $data);
        }

        ms([
            'status'  => 'success',
            'message' => 'Sitemap generated successfully! Added ' . count($urls) . ' URLs.'
        ]);
    }

    /**
     * Save robots.txt content
     */
    public function ajax_robots_txt() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }

        // Get raw POST data for robots.txt (we need to preserve formatting)
        $robots_txt = isset($_POST['seo_robots_txt']) ? trim($_POST['seo_robots_txt']) : '';
        
        if (empty($robots_txt)) {
            ms([
                'status'  => 'error',
                'message' => 'Robots.txt content cannot be empty'
            ]);
        }

        // Whitelist approach - validate against allowed robots.txt directives
        $lines = explode("\n", $robots_txt);
        $valid_directives = [
            'user-agent:', 'disallow:', 'allow:', 'crawl-delay:', 
            'sitemap:', 'host:', 'request-rate:', 'visit-time:',
            'comment:', '#'
        ];
        
        foreach ($lines as $line) {
            $line = trim(strtolower($line));
            
            // Skip empty lines and comments
            if (empty($line) || substr($line, 0, 1) === '#') {
                continue;
            }
            
            // Check if line starts with a valid directive
            $is_valid = false;
            foreach ($valid_directives as $directive) {
                if (strpos($line, $directive) === 0) {
                    $is_valid = true;
                    break;
                }
            }
            
            // Also check for dangerous patterns
            $dangerous_patterns = ['<?php', '<?', '<script', 'javascript:', 'onerror=', 'onclick='];
            foreach ($dangerous_patterns as $pattern) {
                if (strpos($line, strtolower($pattern)) !== false) {
                    ms([
                        'status'  => 'error',
                        'message' => 'Invalid content detected in robots.txt. Scripts and code execution are not allowed.'
                    ]);
                }
            }
            
            if (!$is_valid) {
                ms([
                    'status'  => 'error',
                    'message' => 'Invalid robots.txt directive found: ' . htmlspecialchars(substr($line, 0, 50))
                ]);
            }
        }

        // Save to options (store as-is for proper formatting)
        update_option('seo_robots_txt', $robots_txt);

        ms([
            'status'  => 'success',
            'message' => 'Robots.txt saved successfully!'
        ]);
    }

    /**
     * Serve sitemap.xml content
     * This method can be called via routing or directly
     */
    public function serve_sitemap() {
        // Check if sitemap table exists
        if (!$this->db->table_exists('sitemaps')) {
            show_404();
            return;
        }

        // Get latest sitemap
        $sitemap = $this->db->order_by('id', 'DESC')->limit(1)->get('sitemaps')->row();

        if (!$sitemap || empty($sitemap->content)) {
            show_404();
            return;
        }

        // Set proper headers
        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex');
        
        if ($sitemap->last_modified) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($sitemap->last_modified)) . ' GMT');
        }

        // Output sitemap content
        echo $sitemap->content;
        exit;
    }

    /**
     * Serve robots.txt content
     */
    public function serve_robots() {
        $robots_txt = get_option('seo_robots_txt', '');
        
        if (empty($robots_txt)) {
            // Default robots.txt
            $robots_txt = "User-agent: *\nDisallow: /app/\nDisallow: /install/\nAllow: /\n\nSitemap: " . PATH . "sitemap.xml";
        }

        // Set proper headers
        header('Content-Type: text/plain; charset=utf-8');
        
        // Output robots.txt content
        echo $robots_txt;
        exit;
    }
    
    /**
     * Send test email to verify email notification configuration
     */
    public function ajax_send_test_email() {
        if ($this->input->method() !== 'post') {
            ms([
                'status'  => 'error',
                'message' => 'Invalid method'
            ]);
        }
        
        $admin_email = trim($this->input->post('admin_email', true));
        
        // If no email provided, get from option or admin user
        if (empty($admin_email)) {
            $admin_email = get_option('admin_notification_email', '');
            if (empty($admin_email)) {
                $admin = $this->db->select('email')
                                  ->where('role', 'admin')
                                  ->order_by('id', 'ASC')
                                  ->limit(1)
                                  ->get(USERS)
                                  ->row();
                if ($admin) {
                    $admin_email = $admin->email;
                }
            }
        }
        
        if (empty($admin_email)) {
            ms([
                'status'  => 'error',
                'message' => lang('Please_provide_an_admin_email_address')
            ]);
        }
        
        try {
            $this->load->library('Transactional_email');
            
            $subject = get_option('website_name', 'SMM Panel') . ' - Test Email';
            $content = "
                <h2>Test Email</h2>
                <p>This is a test email to verify your transactional email configuration.</p>
                <p>If you received this email, your email notifications are configured correctly!</p>
                <table style='border-collapse: collapse; width: 100%; max-width: 600px; margin-top: 20px;'>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ddd;'><strong>Test Time:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ddd;'>" . date('Y-m-d H:i:s') . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border: 1px solid #ddd;'><strong>Website:</strong></td>
                        <td style='padding: 8px; border: 1px solid #ddd;'>" . get_option('website_name', 'SMM Panel') . "</td>
                    </tr>
                </table>
            ";
            
            // Send test email using public method
            $result = $this->transactional_email->send_email_template($admin_email, 'Admin', $subject, $content);
            
            if ($result) {
                ms([
                    'status'  => 'success',
                    'message' => lang('Test_email_sent_successfully_to') . ' ' . $admin_email
                ]);
            } else {
                ms([
                    'status'  => 'error',
                    'message' => lang('Failed_to_send_test_email_Check_SMTP_configuration')
                ]);
            }
        } catch (Exception $e) {
            ms([
                'status'  => 'error',
                'message' => lang('Error') . ': ' . $e->getMessage()
=======
        if ($this->db->affected_rows() >= 0) {
            ms([
                'status'  => 'success',
                'message' => lang('Update_successfully'),
                'data'    => $data
            ]);
        } else {
            ms([
                'status'  => 'error',
                'message' => 'No changes detected'
>>>>>>> dd720c81418616f5ea5455fb1a7b66ce0090eb98
            ]);
        }
    }
}