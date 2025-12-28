<?php
/**
 * Sitemap.xml serving script
 * This file serves the sitemap from the database
 */

// Load CodeIgniter
require_once(__DIR__ . '/index.php');

// Get CodeIgniter instance
$CI = &get_instance();

// Check if sitemap table exists
if (!$CI->db->table_exists('sitemaps')) {
    header("HTTP/1.0 404 Not Found");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<error>Sitemap not found. Please run database migration.</error>';
    exit;
}

// Get latest sitemap
$sitemap = $CI->db->order_by('id', 'DESC')->limit(1)->get('sitemaps')->row();

if (!$sitemap || empty($sitemap->content)) {
    header("HTTP/1.0 404 Not Found");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<error>No sitemap available. Please generate or upload a sitemap.</error>';
    exit;
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
