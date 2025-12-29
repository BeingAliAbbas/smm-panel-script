<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email Bounce Detector Library
 * 
 * Detects bounced, invalid, and failed email deliveries via IMAP
 * Specifically designed for email marketing bounce handling
 * 
 * Features:
 * - Connects to SMTP inboxes via IMAP
 * - Parses bounce messages from mailer-daemon
 * - Extracts failed recipient email addresses
 * - Identifies bounce types (hard/soft/invalid)
 * - Adds to suppression list automatically
 * - Prevents duplicate processing
 */
class EmailBounceDetector
{
    protected $CI;
    protected $db;
    protected $log_file;
    protected $time_window_hours;
    protected $max_emails_per_check;
    
    // Bounce type patterns
    protected $hard_bounce_patterns = [
        '/address.*?not.*?found/i',
        '/user.*?unknown/i',
        '/no.*?such.*?user/i',
        '/recipient.*?rejected/i',
        '/mailbox.*?unavailable/i',
        '/invalid.*?recipient/i',
        '/does.*?not.*?exist/i',
        '/unknown.*?address/i',
        '/permanent.*?failure/i',
        '/550/i',  // Mailbox not found
        '/551/i',  // User not local
        '/553/i',  // Mailbox name not allowed
    ];
    
    protected $soft_bounce_patterns = [
        '/mailbox.*?full/i',
        '/quota.*?exceed/i',
        '/over.*?quota/i',
        '/inbox.*?full/i',
        '/storage.*?space/i',
        '/out.*?of.*?storage/i',
        '/temporary.*?failure/i',
        '/try.*?again.*?later/i',
        '/452/i',  // Mailbox full
        '/421/i',  // Service not available
        '/450/i',  // Mailbox busy
    ];
    
    protected $spam_patterns = [
        '/spam/i',
        '/junk/i',
        '/blocked.*?content/i',
        '/policy.*?rejection/i',
    ];
    
    public function __construct($config = [])
    {
        $this->CI =& get_instance();
        $this->CI->load->model('email_marketing/email_marketing_model', 'email_model');
        
        if (isset($this->CI->db)) {
            $this->db = $this->CI->db;
        }
        
        // Configuration
        $this->log_file = $config['log_file'] ?? APPPATH . 'logs/email_bounce_detector.log';
        $this->time_window_hours = $config['time_window_hours'] ?? 48;
        $this->max_emails_per_check = $config['max_emails_per_check'] ?? 50;
    }
    
    /**
     * Run bounce detection for all enabled SMTP configs
     */
    public function run_all()
    {
        $this->log("==== Bounce Detection Start " . date('c') . " ====");
        
        if (!$this->db) {
            $this->log("ERROR: Database not available");
            return false;
        }
        
        // Get all SMTP configs with IMAP enabled
        $this->db->where('imap_enabled', 1);
        $this->db->where('status', 1);
        $smtp_configs = $this->db->get('email_smtp_configs')->result();
        
        if (empty($smtp_configs)) {
            $this->log("INFO: No SMTP configs with IMAP enabled");
            return true;
        }
        
        $total_processed = 0;
        $total_bounces = 0;
        
        foreach ($smtp_configs as $smtp) {
            $result = $this->check_smtp_inbox($smtp);
            $total_processed += $result['processed'];
            $total_bounces += $result['bounces_found'];
        }
        
        $this->log("==== Bounce Detection Complete: Processed={$total_processed}, Bounces={$total_bounces} ====");
        
        return true;
    }
    
    /**
     * Check specific SMTP config inbox for bounces
     */
    public function check_smtp_inbox($smtp)
    {
        $this->log("Checking inbox for SMTP config: {$smtp->name} (ID: {$smtp->id})");
        
        $processed = 0;
        $bounces_found = 0;
        
        try {
            // Build IMAP connection string
            $encryption = $smtp->imap_encryption === 'ssl' ? '/imap/ssl' : 
                         ($smtp->imap_encryption === 'tls' ? '/imap/tls' : '/imap');
            $imap_string = "{{$smtp->imap_host}:{$smtp->imap_port}{$encryption}}INBOX";
            
            // Connect to IMAP
            $inbox = @imap_open(
                $imap_string,
                $smtp->imap_username,
                $smtp->imap_password
            );
            
            if (!$inbox) {
                $error = imap_last_error();
                $this->log("ERROR: IMAP connection failed for {$smtp->name}: {$error}");
                return ['processed' => 0, 'bounces_found' => 0];
            }
            
            // Search for recent emails
            $since_date = date('d-M-Y', strtotime("-{$this->time_window_hours} hours"));
            $criteria = 'SINCE "' . $since_date . '"';
            
            // Also search for mailer-daemon sender
            $criteria .= ' FROM "mailer-daemon"';
            
            $uids = imap_search($inbox, $criteria, SE_UID);
            
            if (!$uids) {
                $this->log("INFO: No bounce emails found in last {$this->time_window_hours} hours for {$smtp->name}");
                imap_close($inbox);
                
                // Update last check time
                $this->update_last_check_time($smtp->id);
                
                return ['processed' => 0, 'bounces_found' => 0];
            }
            
            // Sort newest first
            rsort($uids);
            
            // Limit processing
            $uids = array_slice($uids, 0, $this->max_emails_per_check);
            
            $this->log("Found " . count($uids) . " potential bounce emails");
            
            foreach ($uids as $uid) {
                $processed++;
                
                // Get message number
                $msgno = imap_msgno($inbox, $uid);
                if (!$msgno) {
                    $this->log("WARN: Could not get message number for UID {$uid}");
                    continue;
                }
                
                // Fetch email
                $header = @imap_headerinfo($inbox, $msgno);
                $subject = isset($header->subject) ? imap_utf8($header->subject) : '';
                $from = isset($header->from[0]) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : '';
                
                // Get body
                $body = $this->fetch_message_body($inbox, $uid);
                
                // Parse bounce
                $bounce_info = $this->parse_bounce_message($subject, $body, $from);
                
                if ($bounce_info) {
                    $bounces_found++;
                    
                    // Add to suppression list
                    $this->add_to_suppression_list($bounce_info, $smtp->id, $uid);
                    
                    // Mark as processed
                    $this->mark_as_processed($inbox, $uid, $smtp->imap_processed_folder);
                } else {
                    $this->log("INFO: Email UID {$uid} is not a valid bounce message");
                }
            }
            
            imap_close($inbox);
            
            // Update last check time
            $this->update_last_check_time($smtp->id);
            
        } catch (Exception $e) {
            $this->log("ERROR: Exception in check_smtp_inbox: " . $e->getMessage());
            return ['processed' => $processed, 'bounces_found' => $bounces_found];
        }
        
        return ['processed' => $processed, 'bounces_found' => $bounces_found];
    }
    
    /**
     * Parse bounce message and extract information
     */
    protected function parse_bounce_message($subject, $body, $from)
    {
        // Check if from mailer-daemon or similar
        if (!preg_match('/(mailer-daemon|postmaster|mail.*delivery.*subsystem)/i', $from)) {
            return false;
        }
        
        $text = $this->normalize_text($subject . "\n" . $body);
        
        // Extract email addresses
        $emails = $this->extract_email_addresses($text);
        
        if (empty($emails)) {
            $this->log("WARN: Could not extract email address from bounce message");
            return false;
        }
        
        // Determine bounce type
        $bounce_type = $this->determine_bounce_type($text);
        
        // Extract bounce reason
        $bounce_reason = $this->extract_bounce_reason($text, $bounce_type);
        
        // Extract SMTP code
        $bounce_code = $this->extract_smtp_code($text);
        
        return [
            'emails' => $emails,
            'bounce_type' => $bounce_type,
            'bounce_reason' => $bounce_reason,
            'bounce_code' => $bounce_code,
            'raw_message' => substr($text, 0, 1000) // Store first 1000 chars
        ];
    }
    
    /**
     * Extract email addresses from bounce message
     */
    protected function extract_email_addresses($text)
    {
        $emails = [];
        
        // Pattern to match email addresses
        preg_match_all('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', $text, $matches);
        
        if (!empty($matches[0])) {
            foreach ($matches[0] as $email) {
                $email = strtolower(trim($email));
                
                // Skip mailer-daemon and system addresses
                if (preg_match('/(mailer-daemon|postmaster|noreply|no-reply)/i', $email)) {
                    continue;
                }
                
                // Validate email
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                }
            }
        }
        
        // Remove duplicates
        return array_unique($emails);
    }
    
    /**
     * Determine bounce type (hard, soft, spam, etc.)
     */
    protected function determine_bounce_type($text)
    {
        // Check for hard bounce
        foreach ($this->hard_bounce_patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return 'hard';
            }
        }
        
        // Check for soft bounce
        foreach ($this->soft_bounce_patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return 'soft';
            }
        }
        
        // Check for spam
        foreach ($this->spam_patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return 'spam_complaint';
            }
        }
        
        // Default to hard if unsure
        return 'hard';
    }
    
    /**
     * Extract specific bounce reason
     */
    protected function extract_bounce_reason($text, $bounce_type)
    {
        $reasons = [
            'hard' => [
                '/address.*?not.*?found/i' => 'Address not found',
                '/user.*?unknown/i' => 'User unknown',
                '/no.*?such.*?user/i' => 'No such user',
                '/mailbox.*?unavailable/i' => 'Mailbox unavailable',
                '/invalid.*?recipient/i' => 'Invalid recipient',
            ],
            'soft' => [
                '/mailbox.*?full/i' => 'Mailbox full',
                '/quota.*?exceed/i' => 'Quota exceeded',
                '/over.*?quota/i' => 'Over quota',
                '/temporary.*?failure/i' => 'Temporary failure',
            ]
        ];
        
        $type_reasons = $reasons[$bounce_type] ?? [];
        
        foreach ($type_reasons as $pattern => $reason) {
            if (preg_match($pattern, $text)) {
                return $reason;
            }
        }
        
        return ucfirst($bounce_type) . ' bounce';
    }
    
    /**
     * Extract SMTP error code
     */
    protected function extract_smtp_code($text)
    {
        // Common SMTP codes
        if (preg_match('/\b(4[0-9]{2}|5[0-9]{2})\b/', $text, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Add email to suppression list
     */
    protected function add_to_suppression_list($bounce_info, $smtp_config_id, $message_uid)
    {
        foreach ($bounce_info['emails'] as $email) {
            // Check if already in suppression list
            $existing = $this->db->get_where('email_bounces', ['email' => $email])->row();
            
            if ($existing) {
                // Update existing record
                $this->db->where('id', $existing->id);
                $this->db->update('email_bounces', [
                    'retry_count' => $existing->retry_count + 1,
                    'bounce_type' => $bounce_info['bounce_type'], // Update to latest
                    'bounce_reason' => $bounce_info['bounce_reason'],
                    'bounce_code' => $bounce_info['bounce_code'],
                    'last_bounce_at' => NOW,
                    'updated_at' => NOW
                ]);
                
                $action = 'updated_existing';
                $bounce_id = $existing->id;
                
                $this->log("Updated bounce record for {$email} (Retry count: " . ($existing->retry_count + 1) . ")");
            } else {
                // Calculate expiry for soft bounces
                $expires_at = null;
                $status = 'active';
                
                if ($bounce_info['bounce_type'] === 'soft') {
                    $retry_hours = $this->CI->email_model->get_setting('bounce_soft_retry_hours', 24);
                    $expires_at = date('Y-m-d H:i:s', strtotime("+{$retry_hours} hours"));
                    $status = 'temporary';
                }
                
                // Insert new record
                $data = [
                    'ids' => ids(),
                    'email' => $email,
                    'smtp_config_id' => $smtp_config_id,
                    'bounce_type' => $bounce_info['bounce_type'],
                    'bounce_reason' => $bounce_info['bounce_reason'],
                    'bounce_code' => $bounce_info['bounce_code'],
                    'raw_bounce_message' => $bounce_info['raw_message'],
                    'source' => 'imap',
                    'status' => $status,
                    'expires_at' => $expires_at,
                    'retry_count' => 1,
                    'suppressed_at' => NOW,
                    'last_bounce_at' => NOW,
                    'created_at' => NOW,
                    'updated_at' => NOW
                ];
                
                $this->db->insert('email_bounces', $data);
                $bounce_id = $this->db->insert_id();
                
                $action = 'added_to_suppression';
                
                $this->log("Added {$email} to suppression list (Type: {$bounce_info['bounce_type']}, Reason: {$bounce_info['bounce_reason']})");
            }
            
            // Log the detection
            $this->log_bounce_detection($smtp_config_id, $bounce_id, $email, $bounce_info, $message_uid, $action);
            
            // Update any pending recipients in campaigns
            $this->suppress_pending_recipients($email, $bounce_info);
        }
    }
    
    /**
     * Log bounce detection
     */
    protected function log_bounce_detection($smtp_config_id, $bounce_id, $email, $bounce_info, $message_uid, $action)
    {
        $this->db->insert('email_bounce_logs', [
            'ids' => ids(),
            'smtp_config_id' => $smtp_config_id,
            'bounce_id' => $bounce_id,
            'email' => $email,
            'bounce_type' => $bounce_info['bounce_type'],
            'bounce_reason' => $bounce_info['bounce_reason'],
            'action_taken' => $action,
            'message_uid' => $message_uid,
            'processed' => 1,
            'created_at' => NOW
        ]);
    }
    
    /**
     * Suppress pending recipients in all campaigns
     */
    protected function suppress_pending_recipients($email, $bounce_info)
    {
        // Update pending recipients
        $this->db->where('email', $email);
        $this->db->where('status', 'pending');
        $this->db->update('email_recipients', [
            'is_suppressed' => 1,
            'suppression_reason' => $bounce_info['bounce_reason'],
            'status' => 'bounced',
            'updated_at' => NOW
        ]);
        
        $affected = $this->db->affected_rows();
        
        if ($affected > 0) {
            $this->log("Suppressed {$affected} pending recipient(s) for {$email}");
        }
    }
    
    /**
     * Fetch message body
     */
    protected function fetch_message_body($inbox, $uid)
    {
        $structure = @imap_fetchstructure($inbox, $uid, FT_UID);
        
        if (!$structure) {
            $raw = @imap_body($inbox, $uid, FT_UID);
            return $this->decode_body($raw, 0);
        }
        
        if (empty($structure->parts)) {
            $raw = @imap_body($inbox, $uid, FT_UID);
            return $this->decode_body($raw, $structure->encoding ?? 0);
        }
        
        $parts_collected = [];
        $this->collect_parts($inbox, $uid, $structure, '', $parts_collected);
        
        if (!empty($parts_collected['text/plain'])) {
            return implode("\n", $parts_collected['text/plain']);
        }
        
        if (!empty($parts_collected['text/html'])) {
            return strip_tags(implode("\n", $parts_collected['text/html']));
        }
        
        return '';
    }
    
    /**
     * Collect message parts
     */
    protected function collect_parts($inbox, $uid, $structure, $prefix, &$store)
    {
        if (!empty($structure->parts)) {
            $i = 1;
            foreach ($structure->parts as $part) {
                $part_num = $prefix === '' ? (string)$i : $prefix . '.' . $i;
                $this->collect_parts($inbox, $uid, $part, $part_num, $store);
                $i++;
            }
        } else {
            $type = $this->mime_type($structure);
            if (in_array($type, ['text/plain', 'text/html'])) {
                $raw = @imap_fetchbody($inbox, $uid, $prefix ?: '1', FT_UID);
                $decoded = $this->decode_body($raw, $structure->encoding ?? 0);
                $store[$type][] = $decoded;
            }
        }
    }
    
    /**
     * Get MIME type
     */
    protected function mime_type($structure)
    {
        $primary = [0=>'text', 1=>'multipart', 2=>'message', 3=>'application', 4=>'audio', 5=>'image', 6=>'video', 7=>'other'];
        $p = $primary[$structure->type] ?? 'other';
        $s = isset($structure->subtype) ? strtolower($structure->subtype) : 'plain';
        return $p . '/' . $s;
    }
    
    /**
     * Decode body based on encoding
     */
    protected function decode_body($raw, $encoding)
    {
        if ($raw === false || $raw === null) return '';
        
        switch ($encoding) {
            case 3: return base64_decode($raw);
            case 4: return quoted_printable_decode($raw);
            default: return $raw;
        }
    }
    
    /**
     * Normalize text
     */
    protected function normalize_text($text)
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    /**
     * Mark message as processed
     */
    protected function mark_as_processed($inbox, $uid, $processed_folder)
    {
        @imap_setflag_full($inbox, $uid, "\\Seen", ST_UID);
        
        if ($processed_folder) {
            // Try to move to processed folder
            @imap_mail_move($inbox, $uid, $processed_folder, CP_UID);
            @imap_expunge($inbox);
        }
    }
    
    /**
     * Update last check time
     */
    protected function update_last_check_time($smtp_config_id)
    {
        $this->db->where('id', $smtp_config_id);
        $this->db->update('email_smtp_configs', [
            'imap_last_check' => NOW
        ]);
    }
    
    /**
     * Check if email is suppressed
     */
    public function is_suppressed($email)
    {
        $this->db->where('email', $email);
        $this->db->where('status', 'active');
        $bounce = $this->db->get('email_bounces')->row();
        
        if (!$bounce) {
            return false;
        }
        
        // Check if temporary bounce has expired
        if ($bounce->status === 'temporary' && $bounce->expires_at) {
            if (strtotime($bounce->expires_at) < time()) {
                // Expired, remove from suppression
                $this->db->where('id', $bounce->id);
                $this->db->update('email_bounces', [
                    'status' => 'removed',
                    'updated_at' => NOW
                ]);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Log message
     */
    protected function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[{$timestamp}] {$message}\n";
        
        @file_put_contents($this->log_file, $log_entry, FILE_APPEND);
        
        // Also log to CodeIgniter log
        if (function_exists('log_message')) {
            log_message('info', 'EmailBounceDetector: ' . $message);
        }
    }
}
