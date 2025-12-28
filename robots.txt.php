<?php
/**
 * Robots.txt serving script
 * This file serves the robots.txt from database options
 */

// Load CodeIgniter
require_once(__DIR__ . '/index.php');

// Get CodeIgniter instance
$CI = &get_instance();

// Load helper for get_option
if (!function_exists('get_option')) {
    $CI->load->helper('common');
}

// Get robots.txt content from options
$robots_txt = get_option('seo_robots_txt', '');

if (empty($robots_txt)) {
    // Default robots.txt
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $robots_txt = "User-agent: *\nDisallow: /app/\nDisallow: /install/\nAllow: /\n\nSitemap: " . $base_url . "/sitemap.xml";
}

// Set proper headers
header('Content-Type: text/plain; charset=utf-8');

// Output robots.txt content
echo $robots_txt;
exit;
