<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pay Token Helper
 * 
 * Generates and validates secure one-time-use payment tokens for transactions
 */

if (!function_exists('generate_pay_token')) {
    /**
     * Generate a secure, unique pay token for a transaction
     * Uses cryptographically secure random bytes
     * 
     * @return string 64-character hexadecimal token
     */
    function generate_pay_token() {
        // Generate 32 random bytes, convert to hexadecimal (64 chars)
        return bin2hex(random_bytes(32));
    }
}

if (!function_exists('get_pay_url')) {
    /**
     * Get the full pay URL for a given token
     * 
     * @param string $token The pay token
     * @return string Full URL to approve payment
     */
    function get_pay_url($token) {
        $CI = &get_instance();
        $base = rtrim($CI->config->site_url(), '/');
        return $base . '/transactions/pay/' . $token;
    }
}