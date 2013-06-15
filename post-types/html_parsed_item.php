<?php
/**
 * @package WP HTML Parser
 */

/**
 * Copyright 2013 Philip Browning (email : pbrowning@scs.howard.edu)
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the
 * GNU General Public License, version 2, as published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

if(!class_exists('HTML_Parsed_Item'))
{
	/**
	 * A PostTypeTemplate class that provides 3 additional meta fields
	 */
	class HTML_Parsed_Item
	{
		 const POST_TYPE = "HTML Parsed Item";
		 private $_meta = array(
		 	'ASIN',
		 	'amazon_price',
			'amazon_old_price_old',
			'category_URL',
			'comments_count',
			'comments_URL',
			'product_URL',
		 	'marketplace_price_new',
		 	'marketplace_price_used',
			'rating',
			'thumbnail_URL'
		);
		
		/**
		 *
		 */
		public function __construct()
		{
			// Register actions
			add_action('init', array(&$this, 'init'));
			add_action('admin_init', array(&$this, 'admin_init'));
		} // END of __construct()
		
		
		/**
		 * Hook inth the WordPress init action hook.
		 */
		public function init()
		{
			// Initialize Post Type
			$this->create_HTML_Parsed_Item();
			add_action('save_post', array(&$this, 'save_post'));
		} // END of init()
		
		
		/**
		 * Create the post type
		 */
		public function create_HTML_Parsed_Item()
		{
			register_post_type(self::POST_TYPE,
    			array(
    				'labels' => array(
    					'name' => __(sprintf('%ss', ucwords(str_replace("_", " ", self::POST_TYPE)))),
    					'singular_name' => __(ucwords(str_replace("_", " ", self::POST_TYPE)))
    				),
    				'public' => true,
    				'has_archive' => true,
    				'description' => __("This is a sample post type meant only to illustrate a preferred structure of plugin development"),
    				'supports' => array(
    					'title', 'editor', 'excerpt', 
    				),
    			)
    		);
		} // END of create_HTML_Parsed_Item()
		
		
		/**
    	 * Save the metaboxes for this custom post type
    	 */
    	public function save_post($post_id)
    	{
            // verify if this is an auto save routine. 
            // If it is our form has not been submitted, so we dont want to do anything
            if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            {
                return;
            }
            
    		if(isset($_POST['post_type']) == self::POST_TYPE && current_user_can('edit_post', $post_id))
    		{
    			foreach($this->_meta as $field_name)
    			{
    				// Update the post's meta field
    				update_post_meta($post_id, $field_name, $_POST[$field_name]);
    			}
    		}
    		else
    		{
    			return;
    		} 
    	} // END of save_post($post_id)
		
		
		/**
    	 * Hook into the WordPress admin_init action hook.
    	 */
    	public function admin_init()
    	{			
    		// Add metaboxes
    		add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
    	}
		// END of admin_init()


    	/**
    	 * Hook into the WordPress add_meta_boxes action hook.
    	 */
    	public function add_meta_boxes()
    	{
    		// Add this metabox to every selected post
    		add_meta_box( 
    			sprintf('html-parser_%s_section', self::POST_TYPE),
    			sprintf('%s Information', ucwords(str_replace("_", " ", self::POST_TYPE))),
    			array(&$this, 'add_inner_meta_boxes'),
    			self::POST_TYPE
    	    );					
    	}
		// END of add_meta_boxes()
		
		
		/**
		 * Called off of the add meta box.
		 */		
		public function add_inner_meta_boxes($post)
		{		
			// Render the job order metabox
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), str_replace(" ", "_", strtolower(self::POST_TYPE))));			
		}
		// END of add_inner_meta_boxes($post)
	}
	// END of class HTML_Parsed_Item 
}
// END of if(!class_exists('HTML_Parsed_Item'))