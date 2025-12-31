<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Platform Model
 * 
 * Manages platform filters, icons, and keywords for the order/add system.
 * Provides caching and optimized queries for better performance.
 */
class Platform_model extends MY_Model {
	
	private $tb_platforms;
	private $tb_platform_keywords;
	private $tb_category_icons;
	private $tb_platform_cache;
	private $tb_categories;
	private $cache_ttl = 3600; // 1 hour cache TTL
	
	public function __construct(){
		parent::__construct();
		$this->tb_platforms = 'platforms';
		$this->tb_platform_keywords = 'platform_keywords';
		$this->tb_category_icons = 'category_icons';
		$this->tb_platform_cache = 'platform_cache';
		$this->tb_categories = CATEGORIES;
	}
	
	/**
	 * Get all active platforms for filter bar
	 * Returns cached data if available
	 * 
	 * @return array Array of platform objects
	 */
	public function get_active_platforms(){
		// Try to get from cache first
		$cache_key = 'active_platforms';
		$cached = $this->get_from_cache($cache_key);
		if ($cached !== false) {
			return $cached;
		}
		
		// Fetch from database
		$this->db->select('id, name, slug, icon_class, icon_url, sort_order');
		$this->db->from($this->tb_platforms);
		$this->db->where('status', 1);
		$this->db->order_by('sort_order', 'ASC');
		$query = $this->db->get();
		$result = $query->result();
		
		// Store in cache
		$this->set_cache($cache_key, $result);
		
		return $result;
	}
	
	/**
	 * Get platform keywords for category detection
	 * Returns cached data if available
	 * 
	 * @return array Array of keyword objects with platform info
	 */
	public function get_platform_keywords(){
		// Try to get from cache first
		$cache_key = 'platform_keywords';
		$cached = $this->get_from_cache($cache_key);
		if ($cached !== false) {
			return $cached;
		}
		
		// Fetch from database with platform info
		$this->db->select('pk.keyword, pk.priority, p.slug as platform_slug, p.id as platform_id');
		$this->db->from($this->tb_platform_keywords . ' pk');
		$this->db->join($this->tb_platforms . ' p', 'p.id = pk.platform_id', 'inner');
		$this->db->where('p.status', 1);
		$this->db->order_by('pk.priority', 'DESC');
		$query = $this->db->get();
		$result = $query->result();
		
		// Store in cache
		$this->set_cache($cache_key, $result);
		
		return $result;
	}
	
	/**
	 * Detect platform from text using keywords
	 * 
	 * @param string $text Text to analyze (category/service name)
	 * @return string Platform slug or 'other'
	 */
	public function detect_platform($text){
		if (empty($text)) {
			return 'other';
		}
		
		$text_lower = strtolower($text);
		$keywords = $this->get_platform_keywords();
		
		foreach ($keywords as $kw) {
			if (strpos($text_lower, strtolower($kw->keyword)) !== false) {
				return $kw->platform_slug;
			}
		}
		
		return 'other';
	}
	
	/**
	 * Get icon for a category or service
	 * First checks for custom category icon, then falls back to keyword-based detection
	 * 
	 * @param int $category_id Category ID
	 * @param string $name Category/Service name for fallback
	 * @return object Object with icon_type and icon_value
	 */
	public function get_icon_for_category($category_id, $name = ''){
		// Check for custom category icon first
		$this->db->select('icon_type, icon_value');
		$this->db->from($this->tb_category_icons);
		$this->db->where('category_id', $category_id);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		
		// Fallback: detect platform from name and get its icon
		if (!empty($name)) {
			$platform_slug = $this->detect_platform($name);
			return $this->get_icon_by_platform($platform_slug);
		}
		
		return (object)['icon_type' => 'class', 'icon_value' => ''];
	}
	
	/**
	 * Get icon by platform slug
	 * 
	 * @param string $platform_slug Platform slug
	 * @return object Object with icon_type and icon_value
	 */
	public function get_icon_by_platform($platform_slug){
		$this->db->select('icon_class, icon_url');
		$this->db->from($this->tb_platforms);
		$this->db->where('slug', $platform_slug);
		$this->db->where('status', 1);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			$platform = $query->row();
			
			// Prefer GIF/URL over Font Awesome icon
			if (!empty($platform->icon_url)) {
				return (object)['icon_type' => 'url', 'icon_value' => $platform->icon_url];
			} else if (!empty($platform->icon_class)) {
				return (object)['icon_type' => 'class', 'icon_value' => $platform->icon_class];
			}
		}
		
		return (object)['icon_type' => 'class', 'icon_value' => ''];
	}
	
	/**
	 * Get icon data for text (category/service name)
	 * 
	 * @param string $text Text to analyze
	 * @return object Object with icon_type and icon_value
	 */
	public function get_icon_by_text($text){
		if (empty($text)) {
			return (object)['icon_type' => 'class', 'icon_value' => ''];
		}
		
		$platform_slug = $this->detect_platform($text);
		return $this->get_icon_by_platform($platform_slug);
	}
	
	/**
	 * Get all platforms with their keywords (for admin)
	 * 
	 * @return array Array of platforms with keywords
	 */
	public function get_platforms_with_keywords(){
		$this->db->select('*');
		$this->db->from($this->tb_platforms);
		$this->db->order_by('sort_order', 'ASC');
		$query = $this->db->get();
		$platforms = $query->result();
		
		// Get keywords for each platform
		foreach ($platforms as $platform) {
			$this->db->select('id, keyword, priority');
			$this->db->from($this->tb_platform_keywords);
			$this->db->where('platform_id', $platform->id);
			$this->db->order_by('priority', 'DESC');
			$kw_query = $this->db->get();
			$platform->keywords = $kw_query->result();
		}
		
		return $platforms;
	}
	
	/**
	 * Save or update platform
	 * 
	 * @param array $data Platform data
	 * @return int Platform ID
	 */
	public function save_platform($data){
		// Clear cache
		$this->clear_all_cache();
		
		if (isset($data['id']) && $data['id'] > 0) {
			// Update
			$id = $data['id'];
			unset($data['id']);
			$data['changed'] = date('Y-m-d H:i:s');
			$this->db->where('id', $id);
			$this->db->update($this->tb_platforms, $data);
			return $id;
		} else {
			// Insert
			unset($data['id']);
			$data['created'] = date('Y-m-d H:i:s');
			$this->db->insert($this->tb_platforms, $data);
			return $this->db->insert_id();
		}
	}
	
	/**
	 * Delete platform
	 * 
	 * @param int $id Platform ID
	 * @return bool Success
	 */
	public function delete_platform($id){
		// Clear cache
		$this->clear_all_cache();
		
		// Delete keywords first
		$this->db->where('platform_id', $id);
		$this->db->delete($this->tb_platform_keywords);
		
		// Delete platform
		$this->db->where('id', $id);
		return $this->db->delete($this->tb_platforms);
	}
	
	/**
	 * Save platform keyword
	 * 
	 * @param array $data Keyword data
	 * @return int Keyword ID
	 */
	public function save_keyword($data){
		// Clear cache
		$this->clear_all_cache();
		
		if (isset($data['id']) && $data['id'] > 0) {
			// Update
			$id = $data['id'];
			unset($data['id']);
			$this->db->where('id', $id);
			$this->db->update($this->tb_platform_keywords, $data);
			return $id;
		} else {
			// Insert
			unset($data['id']);
			$data['created'] = date('Y-m-d H:i:s');
			$this->db->insert($this->tb_platform_keywords, $data);
			return $this->db->insert_id();
		}
	}
	
	/**
	 * Delete keyword
	 * 
	 * @param int $id Keyword ID
	 * @return bool Success
	 */
	public function delete_keyword($id){
		// Clear cache
		$this->clear_all_cache();
		
		$this->db->where('id', $id);
		return $this->db->delete($this->tb_platform_keywords);
	}
	
	/**
	 * Save category icon
	 * 
	 * @param int $category_id Category ID
	 * @param string $icon_type Icon type (class, url, gif)
	 * @param string $icon_value Icon value
	 * @return int Icon ID
	 */
	public function save_category_icon($category_id, $icon_type, $icon_value){
		// Clear cache
		$this->clear_all_cache();
		
		// Check if exists
		$this->db->where('category_id', $category_id);
		$query = $this->db->get($this->tb_category_icons);
		
		$data = [
			'category_id' => $category_id,
			'icon_type' => $icon_type,
			'icon_value' => $icon_value,
			'changed' => date('Y-m-d H:i:s')
		];
		
		if ($query->num_rows() > 0) {
			// Update
			$this->db->where('category_id', $category_id);
			$this->db->update($this->tb_category_icons, $data);
			return $query->row()->id;
		} else {
			// Insert
			$data['created'] = date('Y-m-d H:i:s');
			$this->db->insert($this->tb_category_icons, $data);
			return $this->db->insert_id();
		}
	}
	
	/**
	 * Delete category icon
	 * 
	 * @param int $category_id Category ID
	 * @return bool Success
	 */
	public function delete_category_icon($category_id){
		// Clear cache
		$this->clear_all_cache();
		
		$this->db->where('category_id', $category_id);
		return $this->db->delete($this->tb_category_icons);
	}
	
	/**
	 * Auto-detect and assign platforms to categories
	 * Uses keywords to detect platform and updates categories
	 * 
	 * @return int Number of categories updated
	 */
	public function auto_assign_platforms(){
		// Get all categories
		$this->db->select('id, name');
		$this->db->from($this->tb_categories);
		$query = $this->db->get();
		$categories = $query->result();
		
		$updated = 0;
		foreach ($categories as $category) {
			$platform_slug = $this->detect_platform($category->name);
			
			// Get platform ID
			$this->db->select('id');
			$this->db->from($this->tb_platforms);
			$this->db->where('slug', $platform_slug);
			$p_query = $this->db->get();
			
			if ($p_query->num_rows() > 0) {
				$platform_id = $p_query->row()->id;
				
				// Update category
				$this->db->where('id', $category->id);
				$this->db->update($this->tb_categories, ['platform_id' => $platform_id]);
				$updated++;
			}
		}
		
		return $updated;
	}
	
	// ===============================================
	// Cache Methods
	// ===============================================
	
	/**
	 * Get data from cache
	 * 
	 * @param string $key Cache key
	 * @return mixed Cached data or false
	 */
	private function get_from_cache($key){
		$this->db->select('cache_data');
		$this->db->from($this->tb_platform_cache);
		$this->db->where('cache_key', $key);
		$this->db->where('expires >', date('Y-m-d H:i:s'));
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			$data = $query->row()->cache_data;
			return json_decode($data);
		}
		
		return false;
	}
	
	/**
	 * Set cache data
	 * 
	 * @param string $key Cache key
	 * @param mixed $data Data to cache
	 * @return bool Success
	 */
	private function set_cache($key, $data){
		// Delete old cache
		$this->db->where('cache_key', $key);
		$this->db->delete($this->tb_platform_cache);
		
		// Insert new cache
		$cache_data = [
			'cache_key' => $key,
			'cache_data' => json_encode($data),
			'created' => date('Y-m-d H:i:s'),
			'expires' => date('Y-m-d H:i:s', time() + $this->cache_ttl)
		];
		
		return $this->db->insert($this->tb_platform_cache, $cache_data);
	}
	
	/**
	 * Clear all cache
	 * 
	 * @return bool Success
	 */
	public function clear_all_cache(){
		return $this->db->truncate($this->tb_platform_cache);
	}
	
	/**
	 * Clean expired cache entries
	 * 
	 * @return bool Success
	 */
	public function clean_expired_cache(){
		$this->db->where('expires <', date('Y-m-d H:i:s'));
		return $this->db->delete($this->tb_platform_cache);
	}
}
