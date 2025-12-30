<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * IMAP Bounce Detector Library
 * 
 * Connects to IMAP mailbox and detects bounced emails
 * Parses bounce messages to extract recipient email addresses
 * and categorize bounce types
 */
class ImapBounceDetector {
    
    private $CI;
    private $connection;
    private $smtp_config;
    
    // Configuration constants
    const MAX_BOUNCE_MESSAGE_SIZE = 10000; // Maximum size for stored bounce messages
    const IMAP_RETRY_ATTEMPTS = 3; // Number of connection retry attempts
    
    // Bounce patterns for detection
    private $bounce_patterns = [
        'hard_bounce' => [
            'user unknown',
            'mailbox not found',
            'address not found',
            'no such user',
            'invalid recipient',
            'recipient address rejected',
            'user doesn\'t exist',
            '550 5.1.1',
            '550 5.7.1',
            'permanent failure',
            'does not exist',
            'unknown user'
        ],
        'soft_bounce' => [
            'mailbox full',
            'inbox full',
            'quota exceeded',
            'over quota',
            'out of storage',
            '452 4.2.2',
            '452 4.3.1',
            'temporary failure',
            'try again later',
            'deferred'
        ],
        'complaint' => [
            'spam',
            'abuse',
            'complaint',
            'blocked'
        ]
    ];
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('email_marketing/email_marketing_model', 'email_model');
    }
    
    /**
     * Connect to IMAP server
     * 
     * @param object $smtp_config SMTP configuration with IMAP settings
     * @return bool Success status
     */
    public function connect($smtp_config) {
        try {
            $this->smtp_config = $smtp_config;
            
            // Validate IMAP settings
            if (!$smtp_config->imap_enabled || empty($smtp_config->imap_host)) {
                log_message('error', 'IMAP Bounce Detector: IMAP not enabled or host not configured');
                return false;
            }
            
            // Build connection string
            $connection_string = $this->build_connection_string($smtp_config);
            
            // Clear any previous errors
            imap_errors();
            imap_alerts();
            
            // Attempt connection
            $this->connection = imap_open(
                $connection_string,
                $smtp_config->imap_username ?: $smtp_config->username,
                $smtp_config->imap_password ?: $smtp_config->password,
                0,
                self::IMAP_RETRY_ATTEMPTS
            );
            
            if (!$this->connection) {
                $errors = imap_errors();
                $error = $errors ? implode('; ', $errors) : 'Unknown IMAP error';
                
                log_message('error', sprintf(
                    'IMAP Bounce Detector: Connection failed for %s: %s',
                    $smtp_config->imap_host,
                    $error
                ));
                
                // Update last error in database
                $this->CI->email_model->db->where('id', $smtp_config->id);
                $this->CI->email_model->db->update('email_smtp_configs', [
                    'imap_last_error' => $error,
                    'updated_at' => NOW
                ]);
                
                return false;
            }
            
            // Update last check time
            $this->CI->email_model->db->where('id', $smtp_config->id);
            $this->CI->email_model->db->update('email_smtp_configs', [
                'imap_last_check' => NOW,
                'imap_last_error' => null,
                'updated_at' => NOW
            ]);
            
            return true;
            
        } catch (Exception $e) {
            log_message('error', 'IMAP Bounce Detector: Connection exception - ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build IMAP connection string
     */
    private function build_connection_string($smtp_config) {
        $host = $smtp_config->imap_host;
        $port = $smtp_config->imap_port ?: 993;
        
        // Build flags based on encryption
        $flags = [];
        if ($smtp_config->imap_encryption == 'ssl') {
            $flags[] = 'ssl';
        } elseif ($smtp_config->imap_encryption == 'tls') {
            $flags[] = 'tls';
        }
        
        // Check if SSL certificate validation should be disabled
        // For production, this should be enabled (remove novalidate-cert)
        // For testing or self-signed certs, keep it disabled
        $validate_cert = $this->CI->email_model->get_setting('imap_validate_ssl_cert', 0);
        if (!$validate_cert) {
            $flags[] = 'novalidate-cert';
        }
        
        $flag_string = implode('/', $flags);
        
        return sprintf('{%s:%d/imap%s}INBOX', $host, $port, $flag_string ? '/' . $flag_string : '');
    }
    
    /**
     * Check for bounce emails
     * 
     * @param int $max_emails Maximum emails to process per run
     * @return array Processing results
     */
    public function check_bounces($max_emails = 50) {
        if (!$this->connection) {
            return [
                'success' => false,
                'error' => 'Not connected to IMAP server'
            ];
        }
        
        try {
            $processed = 0;
            $bounces_found = 0;
            $emails_suppressed = 0;
            
            // Search for emails from mailer daemon
            $search_criteria = 'FROM "mailer-daemon" UNSEEN';
            $emails = imap_search($this->connection, $search_criteria);
            
            // Also search for Mail Delivery Subsystem
            if (!$emails) {
                $search_criteria = 'FROM "Mail Delivery Subsystem" UNSEEN';
                $emails = imap_search($this->connection, $search_criteria);
            }
            
            if (!$emails) {
                // No bounce emails found
                return [
                    'success' => true,
                    'processed' => 0,
                    'bounces_found' => 0,
                    'emails_suppressed' => 0,
                    'message' => 'No new bounce emails found'
                ];
            }
            
            // Limit emails to process
            $emails = array_slice($emails, 0, $max_emails);
            
            foreach ($emails as $email_id) {
                $result = $this->process_bounce_email($email_id);
                
                if ($result['success']) {
                    $processed++;
                    
                    if ($result['bounce_detected']) {
                        $bounces_found++;
                        
                        if ($result['email_suppressed']) {
                            $emails_suppressed++;
                        }
                    }
                    
                    // Mark email as seen
                    imap_setflag_full($this->connection, $email_id, '\\Seen');
                }
            }
            
            return [
                'success' => true,
                'processed' => $processed,
                'bounces_found' => $bounces_found,
                'emails_suppressed' => $emails_suppressed,
                'message' => sprintf(
                    'Processed %d emails, found %d bounces, suppressed %d addresses',
                    $processed,
                    $bounces_found,
                    $emails_suppressed
                )
            ];
            
        } catch (Exception $e) {
            log_message('error', 'IMAP Bounce Detector: Error checking bounces - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Process individual bounce email
     */
    private function process_bounce_email($email_id) {
        try {
            // Get email structure
            $structure = imap_fetchstructure($this->connection, $email_id);
            $header = imap_headerinfo($this->connection, $email_id);
            $body = imap_body($this->connection, $email_id);
            
            // Get full email content for parsing
            $full_message = imap_fetchbody($this->connection, $email_id, '');
            
            // Parse bounce email
            $bounce_data = $this->parse_bounce_email($header, $body, $full_message);
            
            if (!$bounce_data['is_bounce']) {
                return [
                    'success' => true,
                    'bounce_detected' => false
                ];
            }
            
            // Extract bounced email addresses
            $bounced_emails = $this->extract_email_addresses($bounce_data);
            
            if (empty($bounced_emails)) {
                log_message('debug', 'IMAP Bounce Detector: Bounce email found but no recipient extracted');
                return [
                    'success' => true,
                    'bounce_detected' => true,
                    'email_suppressed' => false
                ];
            }
            
            $suppressed = false;
            
            // Process each bounced email
            foreach ($bounced_emails as $bounced_email) {
                // Log bounce
                $bounce_log_id = $this->log_bounce($bounced_email, $bounce_data, $full_message);
                
                // Add to suppression list if auto-suppress is enabled
                if ($this->CI->email_model->get_setting('imap_auto_suppress_bounces', 1) == 1) {
                    $this->add_to_suppression_list($bounced_email, $bounce_data, $bounce_log_id);
                    $suppressed = true;
                }
            }
            
            return [
                'success' => true,
                'bounce_detected' => true,
                'email_suppressed' => $suppressed,
                'bounced_emails' => $bounced_emails
            ];
            
        } catch (Exception $e) {
            log_message('error', 'IMAP Bounce Detector: Error processing email - ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Parse bounce email to extract information
     */
    private function parse_bounce_email($header, $body, $full_message) {
        $bounce_data = [
            'is_bounce' => false,
            'bounce_type' => 'unknown',
            'bounce_reason' => 'Unknown',
            'bounce_code' => null,
            'subject' => $header->subject ?? '',
            'from' => $header->fromaddress ?? '',
            'date' => $header->date ?? '',
            'body' => $body
        ];
        
        // Check if it's a bounce email
        $from_lower = strtolower($bounce_data['from']);
        $subject_lower = strtolower($bounce_data['subject']);
        $body_lower = strtolower($body);
        
        // Check sender
        if (strpos($from_lower, 'mailer-daemon') !== false ||
            strpos($from_lower, 'mail delivery') !== false ||
            strpos($from_lower, 'postmaster') !== false) {
            $bounce_data['is_bounce'] = true;
        }
        
        // Check subject
        if (strpos($subject_lower, 'delivery') !== false ||
            strpos($subject_lower, 'failure') !== false ||
            strpos($subject_lower, 'undelivered') !== false ||
            strpos($subject_lower, 'returned mail') !== false) {
            $bounce_data['is_bounce'] = true;
        }
        
        if (!$bounce_data['is_bounce']) {
            return $bounce_data;
        }
        
        // Categorize bounce type
        $combined_text = $body_lower . ' ' . $subject_lower;
        
        // Check for hard bounce
        foreach ($this->bounce_patterns['hard_bounce'] as $pattern) {
            if (strpos($combined_text, strtolower($pattern)) !== false) {
                $bounce_data['bounce_type'] = 'hard';
                $bounce_data['bounce_reason'] = 'Hard Bounce - ' . ucwords($pattern);
                break;
            }
        }
        
        // Check for soft bounce
        if ($bounce_data['bounce_type'] == 'unknown') {
            foreach ($this->bounce_patterns['soft_bounce'] as $pattern) {
                if (strpos($combined_text, strtolower($pattern)) !== false) {
                    $bounce_data['bounce_type'] = 'soft';
                    $bounce_data['bounce_reason'] = 'Soft Bounce - ' . ucwords($pattern);
                    break;
                }
            }
        }
        
        // Check for complaint
        if ($bounce_data['bounce_type'] == 'unknown') {
            foreach ($this->bounce_patterns['complaint'] as $pattern) {
                if (strpos($combined_text, strtolower($pattern)) !== false) {
                    $bounce_data['bounce_type'] = 'complaint';
                    $bounce_data['bounce_reason'] = 'Complaint - ' . ucwords($pattern);
                    break;
                }
            }
        }
        
        // Extract SMTP error code if present
        if (preg_match('/(\d{3}\s+\d\.\d\.\d)/', $body, $matches)) {
            $bounce_data['bounce_code'] = $matches[1];
        }
        
        return $bounce_data;
    }
    
    /**
     * Extract email addresses from bounce message
     */
    private function extract_email_addresses($bounce_data) {
        $emails = [];
        $body = $bounce_data['body'];
        
        // Common patterns for extracting recipient email
        $patterns = [
            '/(?:to|recipient|address):\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/i',
            '/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/i' // Generic email pattern
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $body, $matches)) {
                foreach ($matches[1] as $email) {
                    $email = strtolower(trim($email));
                    
                    // Validate email
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        // Skip mailer-daemon and system addresses
                        if (strpos($email, 'mailer-daemon') === false &&
                            strpos($email, 'postmaster') === false &&
                            strpos($email, 'noreply') === false) {
                            $emails[] = $email;
                        }
                    }
                }
            }
        }
        
        return array_unique($emails);
    }
    
    /**
     * Log bounce to database
     */
    private function log_bounce($email, $bounce_data, $raw_message) {
        $data = [
            'ids' => ids(),
            'smtp_config_id' => $this->smtp_config->id,
            'bounced_email' => $email,
            'bounce_type' => $bounce_data['bounce_type'],
            'bounce_reason' => $bounce_data['bounce_reason'],
            'bounce_code' => $bounce_data['bounce_code'],
            'raw_bounce_message' => substr($raw_message, 0, self::MAX_BOUNCE_MESSAGE_SIZE),
            'parsed_details' => json_encode([
                'subject' => $bounce_data['subject'],
                'from' => $bounce_data['from'],
                'date' => $bounce_data['date']
            ]),
            'detected_at' => NOW,
            'processed' => 1,
            'created_at' => NOW
        ];
        
        $this->CI->email_model->db->insert('email_bounce_logs', $data);
        return $this->CI->email_model->db->insert_id();
    }
    
    /**
     * Add email to suppression list
     */
    private function add_to_suppression_list($email, $bounce_data, $bounce_log_id) {
        // Check if email already exists in suppression list
        $this->CI->email_model->db->where('email', $email);
        $existing = $this->CI->email_model->db->get('email_suppression_list')->row();
        
        if ($existing) {
            // Update existing record
            $this->CI->email_model->db->where('email', $email);
            $this->CI->email_model->db->update('email_suppression_list', [
                'bounce_count' => $existing->bounce_count + 1,
                'last_bounce_date' => NOW,
                'bounce_log_id' => $bounce_log_id,
                'reason_detail' => $bounce_data['bounce_reason'],
                'updated_at' => NOW
            ]);
        } else {
            // Insert new record
            $data = [
                'ids' => ids(),
                'email' => $email,
                'reason' => $bounce_data['bounce_type'] == 'complaint' ? 'complaint' : 'bounced',
                'reason_detail' => $bounce_data['bounce_reason'],
                'bounce_count' => 1,
                'first_bounce_date' => NOW,
                'last_bounce_date' => NOW,
                'smtp_config_id' => $this->smtp_config->id,
                'bounce_log_id' => $bounce_log_id,
                'added_by' => 'auto',
                'status' => 'active',
                'created_at' => NOW,
                'updated_at' => NOW
            ];
            
            $this->CI->email_model->db->insert('email_suppression_list', $data);
        }
        
        // Update all pending recipients with this email
        $this->mark_recipients_as_suppressed($email, $bounce_data['bounce_reason']);
    }
    
    /**
     * Mark recipients as suppressed
     */
    private function mark_recipients_as_suppressed($email, $reason) {
        $this->CI->email_model->db->where('email', $email);
        $this->CI->email_model->db->where_in('status', ['pending', 'failed']);
        $this->CI->email_model->db->update('email_recipients', [
            'is_suppressed' => 1,
            'suppression_reason' => $reason,
            'status' => 'bounced',
            'updated_at' => NOW
        ]);
    }
    
    /**
     * Close IMAP connection
     */
    public function disconnect() {
        if ($this->connection) {
            imap_close($this->connection);
            $this->connection = null;
        }
    }
    
    /**
     * Destructor - ensure connection is closed
     */
    public function __destruct() {
        $this->disconnect();
    }
}
