<?php
/**
 * @package WP HTML Parser
 */
 
/**
 * Plugin Name:  WP HTML Parser
 * Plugin URI:   https://github.com/philipjbrowning/wp-html-parser
 * Description:  A WordPress plugin that enables users to parse any website and display products on this website, knowing a little HTML code.
 * Version:      1.0
 * Author:       Philip Browning
 * Last Update:  June 5, 2013
 * URI:          http://www.scs.howard.edu
 * References:   http://www.yaconiello.com/blog/how-to-write-wordpress-plugin/#sthash.lZDxqDtZ.dpbs
 * License:      GPL2
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

if(!class_exists('WP_HTML_Parser'))
{
	class WP_HTML_Parser
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
            require_once(sprintf("%s/settings.php", dirname(__FILE__)));
            $WP_HTML_Parser_Settings = new WP_HTML_Parser_Settings();
			
			// Register custom post types
            require_once(sprintf("%s/post-types/html_parsed_item.php", dirname(__FILE__)));
            $HTML_Parsed_Item = new HTML_Parsed_Item();
		}
		// END of __construct()
		
		
		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		}
		// END of activate()
		
		
		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		}
		// END of deactivate()
		
		
		/**
		 * [Description]
		 */
		public function html_parser_settings_page()
		{
			// Restrict access only to admin
			if(!current_user_can('manage_options'))
			{
				wp_die(___('You do not have sufficient permissions to access this page.'));
			}
			
			// Render the settings template
			include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
		}
		// END of html_parser_settings_page()
	}
	// END class WP_HTML_Parser	
}
// END if(!class_exists('WP_HTML_Parser'))


if(class_exists('WP_HTML_Parser'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_HTML_Parser', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_HTML_Parser', 'deactivate'));
	
	// instantiate the plugin class
	$wp_html_parser = new WP_HTML_Parser();
	
	if(isset($wp_html_parser))
	{
		// Add the settings link to the plugin page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-genera.php?page=wp_html_parser">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		
		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links$plugin", 'plugin_settings_link');
	}
}
// END of if(class_exists('WP_HTML_Parser'))
?>