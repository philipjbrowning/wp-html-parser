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
		/* ==================================================================================================== *
		 * PRIVATE VARIABLE DECLARATIONS                                                                        *
		 * ==================================================================================================== */
		
		private $website_HTML = "";
		
		private $valid_HTML_tags = array(
		 	'a',
		 	'body',
		 	'div',
		 	'head',
		 	'script',
		 	'span',
		 	'style',
		 	'table',
		 	'tbody',
		 	'td',
		 	'tfoot',
		 	'thead',
		 	'th',
		 	'tr'
		);
		
		
		/* ==================================================================================================== *
		 * PUBLIC FUNCTION DECLARATIONS                                                                         *
		 * ==================================================================================================== */
	 	
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
		 * Gets the HTML code pulled from a URL.
		 *
		 * @return:  [string] HTML code, if matching start and end tags are found.
		 *           [bool] false, if the start tag was not found, or a matching end tag was not found.
		 */
		public function get_HTML_within_tag($tag_name, $offset=0)
		{
			if ($start = $this->get_tag_start_position($tag_name, $offset))
			{
				if ($end = $this->get_tag_end_position($tag_name, $offset))
				{
					return $this->get_the_HTML($start, $end);
				}
				return false;
			}
			return false;
		}
		// END of get_HTML_within_tag($tag_name, $offset=0)
		
		
		/**
		 * Returns the end position of a specific tag in $this->website_HTML.
		 *
		 * @return:  [int] if the tag exists within $this->website_HTML
		 *           [bool] false, if the tag does not exist within $this->website_HTML
		 */
		public function get_tag_end_position($tag_name, $html_offset=0)
		{
			if ($this->invalid_tag_name($tag_name))
			{
				return false;
			}
			return $this->get_tag_end_position_from_html($this->website_HTML, $tag_name, $html_offset);
		}
		// END of get_tag_end_position($tag_name, $html_offset=0)
		
		
		/**
		 * Returns the start position of a specific tag in $this->website_HTML.
		 *
		 * @return:  [int] if the tag exists within $this->website_HTML
		 *           [bool] false, if the tag does not exist within $this->website_HTML
		 */
		public function get_tag_start_position($tag_name, $html_offset=0)
		{
			if ($this->invalid_tag_name($tag_name))
			{
				return false;
			}
			return $this->get_tag_start_position_from_html($this->website_HTML, $tag_name, $html_offset);
		}
		// END of get_tag_start_position($tag_name, $html_offset=0)
		
		
		/**
		 * Returns the start position of the first tag with a specific class name in $this->website_HTML
		 */
		public function get_tag_start_position_with_class($tag_name, $class_value, $html_offset=0)
		{
			$current_offset = $html_offset;
			do
			{
				if ($current_position = $this->get_tag_start_position($tag_name, $current_offset))
				{
					if($this->tag_has_class($class_value, $current_position))
					{
						return $current_position;
					} else {
						$current_offset = stripos($this->website_HTML, ">", $current_position);
					}
					
				}
				else
				{
					return false;
				}
			} while ($current_offset < strlen($this->website_HTML));
			
		}
		// END of get_tag_start_position_with_class($tag_name, $class_name, $html_offset=0)
		
		
		/**
		 * Returns the start position of the first tag with a specific id name in $this->website_HTML
		 */
		public function get_tag_start_position_with_id($tag_name, $id_name, $html_offset=0)
		{
			return false;
			
			
			// 
			// while ($current_offset
		}
		// END of get_tag_start_position_with_id($tag_name, $id_name, $html_offset=0)
		
		
		/**
		 * Prints the HTML code pulled from a URL.
		 */
		public function print_all_HTML()
		{
			print_r( $this->website_HTML );
		}
		// END of print_all_HTML()
		
		
		/**
		 * Prints the HTML code within a specific tag in $website_HTML
		 */
		public function print_HTML_within_tag($tag_name, $offset=0)
		{
			print_r($this->get_HTML_within_tag($tag_name, $offset));
		}
		// END of print_HTML_within_tag($tag_name, $offset=0)
		
		
		/**
		 * Prints all the HTML code within a range in $website_HTML.
		 */
		public function print_the_HTML($from, $to)
		{
			print_r($this->get_the_HTML($from, $to));
		}
		// END of print_the_HTML($from, $to)
		
		
		/**
		 * Saves HTML code.
		 */
		public function save_HTML($new_HTML)
		{
			$this->website_HTML = (string)$new_HTML;
		}
		// END of save_HTML($new_URL)
		
		
		/**
		 * Saves HTML code within the body tags in to the $website_HTML variable as a string.
		 *
		 * @return:   [bool] true if the new HTML code was saved
		 *            [bool] false if the new HTML code was not saved
		 */
		public function save_HTML_with_URL($new_URL)
		{
			if(is_string($new_URL)) {
				$this->website_HTML = (string)file_get_contents($new_URL);
				return true;
			}
			return false;
		}
		// END of save_HTML_with_URL($new_URL)
		
		
		/* ==================================================================================================== *
		 * PRIVATE FUNCTION DECLARATIONS                                                                        *
		 * ==================================================================================================== */
		
		
		/**
		 * Returns the string value of true or false of a boolean expression.
		 * 
		 * @return:  [string] 'true' or 'false'
		 */
		private function boolString($bValue = false) {
			return ($bValue ? 'true' : 'false');
		}
		
		
		/**
		 * Returns a subset of the HTML code saved between two index values.
		 * 
		 * @return:  [string] HTML code
		 *           [bool] false, if there are no values in the range or invalid input ($to value is less than $from)
		 */
		private function get_the_HTML($from, $to)
		{
			$length = $to - $from;
			if ($length > 0) {
				return htmlentities(substr($this->website_HTML, $from, $length));
			}
			return false;
		}
		// END of get_the_HTML($from, $to)
		
		
		/**
		 * Finds the first occurnace of a tag within an HTML block.
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [bool] false, if the tag does not exist within $website_HTML. This mirrors the stripos() function.
		 */
		private function get_tag_start_position_from_html($html_block, $tag_name, $html_offset=0)
		{
			if ($this->invalid_tag_name($tag_name))
			{
				return false;
			}
			return stripos($html_block, "<".$tag_name, $html_offset);
		}
		// END of get_tag_start_position_from_html($html_block, $tag_name, $html_offset=0)
		
		
		/**
		 * Finds the end position of a tag within an HTML block.
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [bool] false if the tag does not exist within $website_HTML
		 */
		private function get_tag_end_position_from_html($html_block, $tag_name, $html_offset=0)
		{
			if ($this->invalid_tag_name($tag_name))
			{
				return false;
			}
			$tag_length = strlen($tag_name);
			$start_position = $this->get_tag_start_position_from_html($html_block, $tag_name, $html_offset);
			if ($this->get_tag_start_position_from_html($html_block, $tag_name, $html_offset))
			{ // If there is a start of the tag...
				$end_position = stripos($html_block, ">", $start_position);
				if (is_bool($end_position))
				{
					$end_position = strlen($this->website_HTML);
				}
				if (substr($html_block, ($end_position - 1), 1) == '/')
				{// If the previous space contains the character '/'...
					return $end_position + 1;
				}
				else 
				{// If the previous space does not contain the character '/'...
					$temp_start_position = $end_position;
					$temp_end_position = $end_position;
					$matching_tags = false;
					while($matching_tags == false)
					{
						$next_end_position = stripos($html_block, "</".$tag_name.">", $temp_end_position);
						$next_start_position = stripos($html_block, "<".$tag_name, $temp_start_position);
						
						if ((int)$next_end_position < (int)$next_start_position) 
						{// If there are no more start tags...
							$matching_tags = true;
							return $next_end_position + $tag_length + 3;
						} else {
							$temp_end_position = $next_end_position + $tag_length + 3;
							$temp_start_position = $next_start_position + $tag_length + 2;
						}
					}
				}
				echo '<pre><p>ERROR: No end tag for "'.$tag_name.'" was found in this code.</p></pre>';
				return false;
			}
			echo '<pre><p>ERROR: The tag "'.$tag_name.'" does not exist in this code.</p></pre>';
			return false;
		}
		// END of get_tag_end_position_from_html($html_block, $tag_name, $html_offset=0)
		
		
		/**
		 * Determines if a tag name cannot be used by this class
		 *
		 * @return:  [bool] true, if the tag CANNOT be used.
		 *           [bool] false, if the tag CAN be used.
		 */
		private function invalid_tag_name($tag_name)
		{
			$is_invalid = true;
			$i = 0;
			while ($i < count($this->valid_HTML_tags))
			{
				if ($tag_name == $this->valid_HTML_tags[$i])
				{
					return false;
				}
				$i++;
			}
			echo '<pre><p>ERROR: Invalid tag "'.$tag_name.'" name used. Only "a", "body", "div", "head", "script", ';
			echo '"span", "style", "table", "tbody", "tfoot", "thead", "td", "th", and "tr" can be used.</p></pre>';
			return true;
		}
		// END of invalid_tag_name($tag_name)
		
		
		private function tag_has_class($class_value, $start)
		{
			return $this->tag_has_attribute($class_value, "class", $start);
		}
		
		
		
		private function tag_has_id($id_value, $start)
		{
			return $this->tag_has_attribute($id_value, "id", $start);
		}
		
		
		
		/**
		 * [Description]
		 *
		 * @preconditions:  The first element $this->website_HTML[$htmloffset] = '<'.
		 * @return:         [bool] true, if there is a tag attribute with an $attribute_value.
		 *                  [bool] false, if there is no tag attribute with an $attribute_value.
		 */
		private function tag_has_attribute($attribute_value, $attribute_name, $start)
		{
			$end = stripos($this->website_HTML, ">", $start);
			$length = $end - $start + 1;
			$opening_tag = substr($this->website_HTML, $start, $length);
			if ($attribute_start = stripos($opening_tag, $attribute_name))
			{
				// This tag has an attribute of some name...
				$quote = "";
				$quote_start = "";
				$double_quote_start = stripos($opening_tag, '"', $attribute_start); // Plus 1 for the equal's sign or space
				$single_quote_start = stripos($opening_tag, "'", $attribute_start);
				if(($double_quote_start && !$single_quote_start) || ((int)$double_quote_start > (int)$single_quote_start))
				{
					$quote = '"';
					$quote_start = $double_quote_start;
				}
				else
				{
					$quote = "'";
					$quote_start = $single_quote_start;
				}
				$quote_end = stripos($opening_tag, $quote, $quote_start+1);
				$length = $quote_end - $quote_start + 1;
				$attribute_values = substr($opening_tag, $quote_start, $length);
				
				if ($attribute_value_start = stripos($attribute_values, $attribute_value))
				{
					$next_character = $attribute_values[$attribute_value_start + strlen($attribute_value)];
					if (($next_character == $quote) || ($next_character == " "))
					{// The next character should be a quote or a space. Then we have a match.
						return true;
					}
				}
				return false;
			}
			return false;
		}
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
        // Add the settings link to the plugins page
        function wp_html_parser_settings_link($links)
        { 
            $settings_link = '<a href="options-general.php?page=wp_html_parser">Settings</a>';
            array_unshift($links, $settings_link); 
            return $links; 
        }

        $plugin = plugin_basename(__FILE__); 
        add_filter("plugin_action_links_$plugin", 'wp_html_parser_settings_link');
    }
}
// END of if(class_exists('WP_HTML_Parser'))
?>