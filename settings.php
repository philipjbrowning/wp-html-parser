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

if(!class_exists('WP_HTML_Parser_Settings'))
{
	class WP_HTML_Parser_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Register actions
			add_action('admin_init', array(&$this, 'admin_init'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
		}
		// END public function __construct
		
		
		/**
		 * Hook into the WordPress admin_init action hook.
		 */
		public function admin_init()
		{
			// Set up the settings for this plugin
			$this->init_settings();
			
			// Possibly do additional admin_init tasks
		}
		// END of admin_init()
		
		
		/**
		 * [Description]
		 */
		public function admin_menu()
		{
			// CODE
			add_options_page('WP HTML Parser Settings', 'WP HTML Parser', 'manage_options', 'wp_html_parser', array(&$this, 'html_parser_settings_page'));
		}
		// END of admin_menu()
		
		
		/**
		 * [Description]
		 */
		public function init_settings()
		{
			register_setting('wp_html_parser-group', 'setting_a');
			register_setting('wp_html_parser-group', 'setting_b');
		}
		// END of init_settings()
	}
	
}

?>