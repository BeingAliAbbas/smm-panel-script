<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailListChecker {

    private $ci;
    private $api_base_url;
    private $api_key;
    private $enabled;
    private $default_timeout = 30;
    private $max_retries = 1; // number of retries on transient errors (0 = none)

    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->model('email_marketing/email_marketing_model');

        // Load configuration from settings
        $base = $this->ci->email_marketing_model->get_setting('email_validation_api_url', 'https://platform.emaillistchecker.io/api');
        $this->api_base_url = rtrim($base, '/');
        $this->api_key = $this->ci->email_marketing_model->get_setting('email_validation_api_key', '');
        $this->enabled = (bool)$this->ci->email_marketing_model->get_setting('email_validation_enabled', 0);
    }

    /**
     * Check if email validation is enabled and configured
     * @return bool
     */
    public function is_enabled() {
        return $this->enabled && !empty($this->api_key);
    }

    /**
     * Build full URL for API path safely (avoids double segments)
     * @param string $path e.g. 'v1/verify' or 'bulk-upload'
     * @return string
     */
    private function build_url($path) {
        $path = ltrim($path, '/');

        // If base already contains '/v1' and path also starts with 'v1/', remove duplicate
        if (strpos($this->api_base_url, '/v1') !== false && strpos($path, 'v1/') === 0) {
            $path = preg_replace('#^v1/#', '', $path);
        }

        return $this->api_base_url . '/' . $path;
    }

    /**
     * Common header builder
     * @return array
     */
    private function headers() {
        return [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    }

    /**
     * Generic HTTP request helper with basic retry logic
     * @param string $url
     * @param array|null $payload
     * @param int $timeout
     * @return array [response_body (string|null), http_code (int), curl_error (string|null)]
     */
    private function request($url, $payload = null, $timeout = null) {
        $timeout = $timeout ?? $this->default_timeout;
        $attempt = 0;
        $lastError = null;
        $lastResponse = null;
        $lastHttpCode = 0;

        do {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers());
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout + 10); // safe buffer
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

            if ($payload !== null) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            }

            $response = curl_exec($ch);
            $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            $lastError = $curl_error ?: null;
            $lastResponse = $response;
            $lastHttpCode = $http_code;

            // If success (200) return immediately
            if (empty($curl_error) && $http_code >= 200 && $http_code < 300) {
                break;
            }

            // If rate limited or transient network error, retry
            $should_retry = false;
            if (!empty($curl_error)) {
                $should_retry = true;
            } elseif ($http_code === 429 || ($http_code >= 500 && $http_code < 600)) {
                $should_retry = true;
            }

            $attempt++;
            if ($should_retry && $attempt <= $this->max_retries) {
                // exponential backoff (simple)
                usleep(500000 * $attempt); // 0.5s, 1s, ...
                continue;
            } else {
                break;
            }

        } while ($attempt <= $this->max_retries);

        return [$lastResponse, $lastHttpCode, $lastError];
    }

    /**
     * Validate a single email address
     *
     * Pass only when API returns reason === 'VALID'
     *
     * @param string $email
     * @param int|null $timeout
     * @param bool $smtp_check
     * @return array Validation result with keys: valid, result, reason, score, error, disposable, role, free, domain, spam_trap, smtp_provider, mx_records, mx_found, verification_result
     */
    public function validate_single_email($email, $timeout = null, $smtp_check = true) {
        // If validation is disabled, return valid by default
        if (!$this->is_enabled()) {
            return [
                'valid' => true,
                'result' => 'deliverable',
                'reason' => 'VALIDATION_DISABLED',
                'score' => 1.0,
                'error' => null,
                'disposable' => false,
                'role' => false,
                'free' => false,
                'domain' => null,
                'spam_trap' => false,
                'smtp_provider' => null,
                'mx_records' => [],
                'mx_found' => false,
                'verification_result' => null
            ];
        }

        // Basic email format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'result' => 'undeliverable',
                'reason' => 'INVALID_FORMAT',
                'score' => 0.0,
                'error' => 'Invalid email format',
                'disposable' => false,
                'role' => false,
                'free' => false,
                'domain' => null,
                'spam_trap' => false,
                'smtp_provider' => null,
                'mx_records' => [],
                'mx_found' => false,
                'verification_result' => null
            ];
        }

        try {
            $url = $this->build_url('v1/verify');

            $payload = [
                'email' => $email,
                'timeout' => $timeout ?? $this->default_timeout,
                'smtp_check' => $smtp_check
            ];

            list($response, $http_code, $curl_error) = $this->request($url, $payload, $timeout);

            if ($curl_error) {
                log_message('error', 'EmailListChecker API Error: ' . $curl_error . ' | URL: ' . $url);
                return [
                    'valid' => false,
                    'result' => 'unknown',
                    'reason' => 'API_ERROR',
                    'score' => 0.0,
                    'error' => 'API request failed: ' . $curl_error,
                    'disposable' => false,
                    'role' => false,
                    'free' => false,
                    'domain' => null,
                    'spam_trap' => false,
                    'smtp_provider' => null,
                    'mx_records' => [],
                    'mx_found' => false,
                    'verification_result' => null
                ];
            }

            $decoded = json_decode($response, true);

            if (!is_array($decoded)) {
                log_message('error', 'EmailListChecker API returned non-JSON response. HTTP: ' . $http_code . ' | Body: ' . ($response ?? '[empty]'));
                return [
                    'valid' => false,
                    'result' => 'unknown',
                    'reason' => 'INVALID_API_RESPONSE',
                    'score' => 0.0,
                    'error' => 'Invalid API response',
                    'disposable' => false,
                    'role' => false,
                    'free' => false,
                    'domain' => null,
                    'spam_trap' => false,
                    'smtp_provider' => null,
                    'mx_records' => [],
                    'mx_found' => false,
                    'verification_result' => null
                ];
            }

            // If API indicates failure
            if (!isset($decoded['success']) || !$decoded['success']) {
                $error_msg = isset($decoded['message']) ? $decoded['message'] : 'Unknown API error';
                log_message('error', 'EmailListChecker API Error (HTTP ' . $http_code . '): ' . $error_msg);
                return [
                    'valid' => false,
                    'result' => 'unknown',
                    'reason' => 'API_ERROR',
                    'score' => 0.0,
                    'error' => $error_msg,
                    'disposable' => false,
                    'role' => false,
                    'free' => false,
                    'domain' => null,
                    'spam_trap' => false,
                    'smtp_provider' => null,
                    'mx_records' => [],
                    'mx_found' => false,
                    'verification_result' => null
                ];
            }

            $data = $decoded['data'] ?? [];

            // Pass only when reason === 'VALID'
            $is_valid = isset($data['reason']) && strtoupper($data['reason']) === 'VALID';

            return [
                'valid' => $is_valid,
                'result' => $data['result'] ?? 'unknown',
                'reason' => $data['reason'] ?? 'UNKNOWN',
                'score' => isset($data['score']) ? (float)$data['score'] : 0.0,
                'error' => null,
                'disposable' => isset($data['disposable']) ? (bool)$data['disposable'] : false,
                'role' => isset($data['role']) ? (bool)$data['role'] : false,
                'free' => isset($data['free']) ? (bool)$data['free'] : false,
                'domain' => $data['domain'] ?? null,
                'spam_trap' => isset($data['spam_trap']) ? (bool)$data['spam_trap'] : false,
                'smtp_provider' => $data['smtp_provider'] ?? null,
                'mx_records' => $data['mx_records'] ?? [],
                'mx_found' => isset($data['mx_found']) ? (bool)$data['mx_found'] : false,
                'verification_result' => isset($data['verification_result']) ? $data['verification_result'] : null
            ];

        } catch (Exception $e) {
            log_message('error', 'EmailListChecker Exception: ' . $e->getMessage());
            return [
                'valid' => false,
                'result' => 'unknown',
                'reason' => 'EXCEPTION',
                'score' => 0.0,
                'error' => $e->getMessage(),
                'disposable' => false,
                'role' => false,
                'free' => false,
                'domain' => null,
                'spam_trap' => false,
                'smtp_provider' => null,
                'mx_records' => [],
                'mx_found' => false,
                'verification_result' => null
            ];
        }
    }

    /**
     * Upload emails for bulk validation
     *
     * @param array $emails
     * @param string|null $list_name
     * @return array
     */
    public function bulk_upload($emails, $list_name = null) {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'list_id' => null,
                'error' => 'Email validation is not enabled'
            ];
        }

        if (empty($emails) || !is_array($emails)) {
            return [
                'success' => false,
                'list_id' => null,
                'error' => 'No emails provided'
            ];
        }

        try {
            // keep original endpoint style but under v1 if needed
            $url = $this->build_url('bulk-upload');

            $data = [
                'emails' => array_values($emails),
                'list_name' => $list_name ?? 'Campaign ' . date('Y-m-d H:i:s')
            ];

            list($response, $http_code, $curl_error) = $this->request($url, $data, 60);

            if ($curl_error) {
                log_message('error', 'EmailListChecker Bulk Upload Error: ' . $curl_error);
                return [
                    'success' => false,
                    'list_id' => null,
                    'error' => 'API request failed: ' . $curl_error
                ];
            }

            $result = json_decode($response, true);

            if (!is_array($result) || !isset($result['success']) || !$result['success']) {
                $error_msg = isset($result['message']) ? $result['message'] : 'Unknown API error';
                log_message('error', 'EmailListChecker Bulk Upload Error (HTTP ' . $http_code . '): ' . $error_msg);
                return [
                    'success' => false,
                    'list_id' => null,
                    'error' => $error_msg
                ];
            }

            return [
                'success' => true,
                'list_id' => $result['data']['list_id'] ?? null,
                'error' => null
            ];

        } catch (Exception $e) {
            log_message('error', 'EmailListChecker Bulk Upload Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'list_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check bulk validation progress
     *
     * @param string $list_id
     * @return array
     */
    public function check_bulk_progress($list_id) {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'status' => 'error',
                'error' => 'Email validation is not enabled'
            ];
        }

        try {
            $url = $this->build_url('bulk-verification/' . urlencode($list_id) . '/progress');

            list($response, $http_code, $curl_error) = $this->request($url, null, 30);

            if ($curl_error) {
                log_message('error', 'EmailListChecker Progress Check Error: ' . $curl_error);
                return [
                    'success' => false,
                    'status' => 'error',
                    'error' => 'API request failed: ' . $curl_error
                ];
            }

            $result = json_decode($response, true);

            if (!is_array($result) || !isset($result['success']) || !$result['success']) {
                $error_msg = isset($result['message']) ? $result['message'] : 'Unknown API error';
                return [
                    'success' => false,
                    'status' => 'error',
                    'error' => $error_msg
                ];
            }

            $data = $result['data'] ?? [];

            return [
                'success' => true,
                'status' => $data['status'] ?? 'unknown',
                'progress' => $data['progress'] ?? 0,
                'total' => $data['total'] ?? 0,
                'verified' => $data['verified'] ?? 0,
                'error' => null
            ];

        } catch (Exception $e) {
            log_message('error', 'EmailListChecker Progress Check Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Download verified emails from bulk validation
     *
     * @param string $list_id
     * @return array
     */
    public function download_bulk_results($list_id) {
        if (!$this->is_enabled()) {
            return [
                'success' => false,
                'emails' => [],
                'error' => 'Email validation is not enabled'
            ];
        }

        try {
            $url = $this->build_url('bulk-verification/' . urlencode($list_id) . '/download');

            list($response, $http_code, $curl_error) = $this->request($url, null, 60);

            if ($curl_error) {
                log_message('error', 'EmailListChecker Download Error: ' . $curl_error);
                return [
                    'success' => false,
                    'emails' => [],
                    'error' => 'API request failed: ' . $curl_error
                ];
            }

            $result = json_decode($response, true);

            if (!is_array($result) || !isset($result['success']) || !$result['success']) {
                $error_msg = isset($result['message']) ? $result['message'] : 'Unknown API error';
                return [
                    'success' => false,
                    'emails' => [],
                    'error' => $error_msg
                ];
            }

            return [
                'success' => true,
                'emails' => $result['data']['emails'] ?? [],
                'error' => null
            ];

        } catch (Exception $e) {
            log_message('error', 'EmailListChecker Download Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'emails' => [],
                'error' => $e->getMessage()
            ];
        }
    }
}
