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
 * Last Update:  July 8, 2013
 * Author URI:   https://github.com/philipjbrowning
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
		// Include JQuery scripts for form submission
		
		
		/* ==================================================================================================== *
		 * PRIVATE VARIABLE DECLARATIONS                                                                        *
		 * ==================================================================================================== */
		
		const MIN_TAG_LENGTH = 2;
		
		private $options = array(
			'container_tag_name' => '',
			'container_attribute_name' => '',
			'container_attribute_value' => '',
			'item_tag_name' => '',
			'item_attribute_name' => '',
			'item_attribute_value' => '',
			'remove_comments' => true,
			'remove_header' => true,
			'remove_script' => true,
			'remove_style' => true, // CSS
			'remove_whitespace' => true,
		);
		
		private $url_variables = false; // Array of $_GET variable names and values;
		
		private $valid_HTML_tags = array( // OBSOLETE
		 	'a',
		 	'body',
		 	'div',
			'h3',
		 	'head',
			'img',
			'li',
		 	'script',
		 	'span',
		 	'style',
		 	'table',
		 	'tbody',
		 	'td',
		 	'tfoot',
		 	'thead',
		 	'th',
			'title',
		 	'tr'
		);
		
		private $website_HTML = false;
		
		private $website_URL = false;
		
		
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
			global $wpdb;
			$query = "CREATE TABLE ".$wpdb->prefix."searches (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				search_author bigint(20) unsigned NOT NULL DEFAULT '0',
				search_date datetime NOT NULL,
				search_date_gmt datetime NOT NULL,
				search_title text NOT NULL,
				website_url text NOT NULL,
				website_html longtext NOT NULL,
				remove_comments enum('Y','N') NOT NULL DEFAULT 'Y',
				remove_header enum('Y','N') NOT NULL DEFAULT 'Y',
				remove_script enum('Y','N') NOT NULL DEFAULT 'Y',
				remove_style enum('Y','N') NOT NULL DEFAULT 'Y',
				remove_whitespace enum('Y','N') NOT NULL DEFAULT 'Y',
				container_tag_name varchar(20) DEFAULT NULL,
				container_attribute_name varchar(20) DEFAULT NULL,
				container_attribute_value varchar(20) DEFAULT NULL,
				item_tag_name varchar(20) DEFAULT NULL,
				item_attribute_name varchar(20) DEFAULT NULL,
				item_attribute_value varchar(20) DEFAULT NULL,
				PRIMARY KEY (ID)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			
			$result = $wpdb->query('query');
			
			// Create searchmeta table also
		}
		// END of activate()
		
		
		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			global $wpdb;
			$wpdb->query('DROP TABLE IF EXISTS '.$wpdb->prefix.'searches');
		}
		// END of deactivate()
		
		
		/**
		 * [FUNCTION DESCRIPTION]
		 */
		public function get_all_HTML()
		{
			if($this->HTML_content_is_saved())
			{
				return htmlentities($this->website_HTML);
			}
			return new WP_Error('no_HTML_content', 'ERROR: The function get_all_HTML() found no saved HTML content.');
		}
		
		
		/**
		 * Returns an array of the distinct attributes in a tag sorted alphabetically.
		 *
		 * @parameters:  [string] The name of the tag
		 * @return:      [???]
		 */
		public function get_all_attributes_within_tag( $tag_name )
		{
			// CODING NOW ----
		}
		
		
		/**
		 * Returns the names of all the tags in the HTML code sorted alphabetically.
		 *
		 * @return:  [array] tag names, if tag names are found.
		 *           [bool] false, if no tag names are found.
		 */
		public function get_all_tag_names()
		{
			$offset = 0;
			$tag_name_found = false;
			$tag_names = array();
			do
			{
				$tag_info = $this->get_next_tag_name($offset);
				if ($tag_info !== false)
				{
					$tag_name_found = true;
					array_push($tag_names, $tag_info['name']);
					$offset = $tag_info['position'] + 1;
				}
			} while ($tag_info !== false);
			if ($tag_name_found === true)
			{
				$tag_names = array_unique($tag_names);
				asort(&$tag_names);
				return $tag_names;
			} else {
				return false;
			}
		}
		// END of get_all_tag_names()
		
		
		/**
		 * [FUNCTION DESCRIPTION]
		 */
		public function get_attribute_value_of_tag( $tag_name, $attribute_name, $html_offset=0 )
		{
			if($this->HTML_content_is_saved())
			{
				if ($this->is_valid_tag_name($tag_name))
				{
					$start_position = $this->get_tag_start_position_from_html($this->website_HTML, $tag_name, $html_offset);
					if (is_numeric($start_position))
					{
						$attribute_start = $this->tag_has_attribute_name( $attribute_name, $start_position ) + $start_position;
						if (!is_wp_error($attribute_start))
						{
							// This tag has an attribute of some name...
							$quote = "";
							$quote_start = "";
							$double_quote_start = stripos($this->website_HTML, '"', $attribute_start); // Plus 1 for the equal's sign or space
							$single_quote_start = stripos($this->website_HTML, "'", $attribute_start);
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
							$quote_end = stripos($this->website_HTML, $quote, $quote_start+1);
							$length = $quote_end - $quote_start + 1;
							$attribute_values = substr($this->website_HTML, $quote_start+1, $length-2); // +1 and -2 to remove quotes
							return $attribute_values;
						}
						return $attribute_start; // WP_Error
					}
					$error_data = array(
						"function_name"  => 'get_tag_end_position',
						"html_offset"    => $html_offset,
						"start_position" => $start_position,
						"tag_name"       => $tag_name
					);
					return new WP_Error('no_start_position', __('There is no "'.$tag_name.'" tag in the HTML.'), $error_data);	
				}
				$error_data = array(
					"attribute_name" => $attribute_name,
					"function_name"  => 'get_attribute_value_of_tag',
					"html_offset"    => $html_offset,
					"tag_name"       => $tag_name
				);
				return new WP_Error('invalid_tag_name', __('This plugin does not allow the parsing of a "'.$tag_name.'" tag.'), $error_data);
			}
			$error_data = array(
				"function_name"  => 'get_attribute_value_of_tag',
				"html_offset"    => $html_offset,
				"tag_name"       => $tag_name
			);
			return new WP_Error('no_HTML_content', __('The function get_attribute_value_of_tag() found no saved HTML content.'), $error_data);
		}
		// END of get_attribute_value_of_tag( $tag_name, $attribute_name, $html_offset=0 )
		
		
		/**
		 * Gets the HTML code pulled from a URL.
		 *
		 * @parameters:  [string] tag_name
		 *               [int] offset to start seraching for the tag_name
		 * @return:  [string] HTML code, if matching start and end tags are found.
		 *           [bool] false, if the start tag was not found, or a matching end tag was not found.
		 */
		public function get_HTML_within_tag( $tag_name, $html_offset=0 )
		{
			if($this->HTML_content_is_saved())
			{
				if ($this->is_valid_tag_name($tag_name))
				{
					// NEED TO EDIT -------------------------------------------------------------------------------------------------------------- FIX BELOW
					if ($start = $this->get_tag_start_position($tag_name, $html_offset))
					{
						if ($end = $this->get_tag_end_position($tag_name, $html_offset))
						{
							return $this->get_the_HTML($start, $end);
						}
						echo '<p><u>ERROR</u>: The function get_HTML_within_tag() found no end tag.</p>';
						return false;
					}
					echo '<p><u>ERROR</u>: The function get_HTML_within_tag() found no start tag.</p>';
					return false;
					// NEED TO EDIT -------------------------------------------------------------------------------------------------------------- FIX ABOVE
				} else {
					$error_data = array(
						"function_name"  => 'get_HTML_within_tag',
						"html_offset"    => $html_offset,
						"tag_name"       => $tag_name
					);
					return new WP_Error('invalid_tag_name', __('This plugin does not allow the parsing of a "'.$tag_name.'" tag.'), $error_data);
				}
			}
			$error_data = array(
				"function_name"  => 'get_HTML_within_tag',
				"html_offset"    => $html_offset,
				"tag_name"       => $tag_name
			);
			return new WP_Error('no_HTML_content', __('The function get_HTML_within_tag() found no saved HTML content.'), $error_data);
		}
		// END of get_HTML_within_tag($tag_name, $offset=0)
		
		
		/**
		 * [Description]
		 *
		 * @return:  [string]
		 *           [bool] false
		 */
		public function get_HTML_content_within_tag( $tag_name, $html_offset=0 )
		{
			$html_block = $this->get_HTML_within_tag($tag_name, $html_offset);
			if (is_wp_error($html_block))
			{
				return $html_block; // WP_Error Class
			}
			else
			{
				// NEED TO EDIT ------------------------------------------------------------------------------------------------------------------ FIX BELOW
				$start = stripos($html_block, ">") + 1;
				$end = strripos($html_block , "</".$tag_name);
				$length = $end - $start;
				return substr($html_block, $start, $length);
				// NEED TO EDIT ------------------------------------------------------------------------------------------------------------------ FIX ABOVE
			}
		}
		
		
		/**
		 * Finds an end tag denoted by "/>".
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [bool] false, if the tag does not exist within $website_HTML. This mirrors the stripos() function.
		 */
		public function get_next_short_close( $html_block, $tag_name, $html_offset=0 )
		{
			$possible_end_position = stripos($html_block, "/>", $html_offset);
			if ($possible_end_position !== false)
			{
				$tag_html = substr($html_block, $html_offset, $possible_end_position - $html_offset + 2);
				$start_tag_position = strripos($tag_html, "<");
				$tag_html = substr($tag_html, $start_tag_position, $possible_end_position - $start_tag_position + 2);
				$space_char_position = stripos($tag_html, " ");
				if (substr($tag_html, 1, $space_char_position - 1) == $tag_name)
				{
					return $possible_end_position;
				}
				return $this->get_next_short_close( $html_block, $tag_name, $possible_end_position + 2 );
			}
			return false;
		}
		
		
		/**
		 * [FUNCTION DESCRIPTION]
		 * @parameters: [array] tag_names
		 * @return:
		 */
		public function get_next_tag_name( $offset=0 )
		{
			do
			{
				$tag_start = stripos($this->website_HTML, '<', $offset);
				if (($this->website_HTML[$tag_start + 1] !== '/') && ($tag_start !== false))
				{
					$tag_ending_space = stripos($this->website_HTML, ' ', $tag_start);
					$tag_ending_gt = stripos($this->website_HTML, '>', $tag_start);
					$tag_ending_slash = stripos($this->website_HTML, '/', $tag_start);
					$tag_ending_newline = stripos($this->website_HTML, "\n", $tag_start);
					$tag_ending = $this->minimum_position(array($tag_ending_space, $tag_ending_gt, $tag_ending_slash, $tag_ending_newline));
					$tag_name = trim(substr($this->website_HTML, $tag_start + 1, $tag_ending - $tag_start - 1));
					return array("position" => $tag_start, "name" => $tag_name);
				}
				$offset = $tag_start + 1;
			} while ($tag_start !== false);
			return false; // No tag name found.
		}
		// END of get_next_tag_name( $offset=0 )
		
		
		/**
		 * [FUNCTION DESCRIPTION]
		 */
		public function get_raw_HTML()
		{
			if($this->HTML_content_is_saved())
			{
				return $this->website_HTML;
			}
			return new WP_Error('no_HTML_content', 'ERROR: The function get_raw_HTML() found no saved HTML content.');
		}
		
		
		/**
		 * Returns the end position of a specific tag in $this->website_HTML.
		 *
		 * @return:  [int] if the tag exists within $this->website_HTML
		 *           [bool] false, if the tag does not exist within $this->website_HTML
		 */
		public function get_tag_end_position( $tag_name, $html_offset=0 )
		{
			if($this->HTML_content_is_saved())
			{
				if ($this->is_valid_tag_name($tag_name))
				{
					return $this->get_tag_end_position_from_html($this->website_HTML, $tag_name, $html_offset);
				}
				$error_data = array(
					"function_name"  => 'get_tag_end_position',
					"html_offset"    => $html_offset,
					"tag_name"       => $tag_name
				);
				return new WP_Error('invalid_tag_name', __('This plugin does not allow the parsing of a "'.$tag_name.'" tag.'), $error_data);
			}
			$error_data = array(
				"function_name"  => 'get_tag_end_position',
				"html_offset"    => $html_offset,
				"tag_name"       => $tag_name
			);
			return new WP_Error('no_HTML_content', __('The function get_tag_end_position() found no saved HTML content.'), $error_data);
		}
		// END of get_tag_end_position($tag_name, $html_offset=0)
		
		
		/**
		 * Finds the end position of a tag within an HTML block.
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [WP_Error] false if the tag does not exist within $website_HTML
		 *           [WP_Error] if there is no start position
		 */
		public function get_tag_end_position_from_html( $html_block, $tag_name, $html_offset=0 )
		{
			$start_position = $this->get_tag_start_position_from_html( $html_block, $tag_name, $html_offset );
			if ($start_position !== false)
			{
				$tag_stack_count = 1;
				while (($tag_stack_count > 0) && ($start_position !== false))
				{
					$tag_length = strlen($tag_name);
					$next_start_position = $this->get_tag_start_position_from_html( $html_block, $tag_name, $start_position + $tag_length );
					$next_end_position_1 = stripos($html_block, "</".$tag_name.">", $start_position);
					$next_end_position_2 = $this->get_next_short_close( $html_block, $tag_name, $start_position );
					$next_end_position = $this->minimum_position(array($next_end_position_1, $next_end_position_2));
					$editing_position = $this->minimum_position(array($next_start_position, $next_end_position));
					$is_opening_tag = true;
					if ($editing_position == $next_end_position)
					{
						$is_opening_tag = false;
					}
					if ($is_opening_tag)
					{
						$tag_stack_count++;
					} else {
						$tag_stack_count--;
					}
					if ($tag_stack_count == 0)
					{
						return $editing_position + $tag_length + 3;
					}
					$start_position = $editing_position + $tag_length;
				}
				$error_data = array(
					"html_block"      => $html_block,
					"html_offset"     => $html_offset,
					"start_position"  => $start_position,
					"tag_name"        => $tag_name,
					"tag_stack_count" => $tag_stack_count
				);
				return new WP_Error('invalid_html', __('There was no corresponding closing tag for '.$tag_name.' in the html code.'), $error_data);
			}
			$error_data = array(
				"html_block"     => $html_block,      //  Example
				"html_offset"    => $html_offset,     //  -------
				"start_position" => $start_position,  //  div id="first">
				"tag_name"       => $tag_name         //
			);
			return new WP_Error('invalid_tag_start', __('There must be an opening '.$tag_name.' tag in the html code.'), $error_data);
		}
		// END of get_tag_end_position_from_html( $html_block, $tag_name, $html_offset=0 )
		
		
		/**
		 * Returns the start position of a specific tag in $this->website_HTML.
		 *
		 * @return:  [int] if the tag exists within $this->website_HTML
		 *           [WP_Error] no_start_position, if there is no tag found within the HTML code
		 *           [WP_Error] if the tag name is invalid
		 */
		public function get_tag_start_position($tag_name, $html_offset=0)
		{
			if($this->HTML_content_is_saved())
			{
				if ($this->is_valid_tag_name($tag_name))
				{
					$start_position = $this->get_tag_start_position_from_html($this->website_HTML, $tag_name, $html_offset);
					if (is_numeric($start_position))
					{
						return $start_position;
					}
					$error_data = array(
						"function_name"  => 'get_tag_end_position',
						"html_offset"    => $html_offset,
						"start_position" => $start_position,
						"tag_name"       => $tag_name
					);
					return new WP_Error('no_start_position', __('There is no "'.$tag_name.'" tag in the HTML.'), $error_data);	
				}
				$error_data = array(
					"function_name" => 'get_tag_end_position',
					"html_offset"   => $html_offset,
					"tag_name"      => $tag_name
				);
				return new WP_Error('invalid_tag_name', __('This plugin does not allow the parsing of a "'.$tag_name.'" tag.'), $error_data);
			}
			$error_data = array(
				"function_name"  => 'get_tag_start_position',
				"html_offset"    => $html_offset,
				"tag_name"       => $tag_name
			);
			return new WP_Error('no_HTML_content', __('The function get_tag_start_position() found no saved HTML content.'), $error_data);
		}
		// END of get_tag_start_position($tag_name, $html_offset=0)
		
		
		/**
		 * Finds the first occurnace of a tag within an HTML block.
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [bool] false, if the tag does not exist within $website_HTML. This mirrors the stripos() function.
		 */
		public function get_tag_start_position_from_html( $html_block, $tag_name, $html_offset=0 )
		{
			return stripos($html_block, "<".$tag_name, $html_offset);
		}
		// END of get_tag_start_position_from_html( $html_block, $tag_name, $html_offset=0 )
		
		
		/**
		 * [Description]
		 */
		public function get_tag_start_position_with_attribute_name_and_value($tag_name, $attribute_name, $attribute_value, $html_offset=0)
		{
			if ($this->is_valid_tag_name($tag_name))
			{
				// NEED TO EDIT ------------------------------------------------------------------------------------------------------------------ FIX BELOW
				$current_offset = $html_offset;
				do
				{
					$current_position = $this->get_tag_start_position_from_html( $this->website_HTML, $tag_name, $current_offset );
					if ($current_position !== false)
					{
						if($this->tag_has_attribute_name_and_value($attribute_name, $attribute_value, $current_position))
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
				// NEED TO EDIT ------------------------------------------------------------------------------------------------------------------ FIX ABOVE
			} else {
				$error_data = array(
					"function_name"  => 'get_tag_end_position',
					"html_offset"    => $html_offset,
					"tag_name"       => $tag_name
				);
				return new WP_Error('invalid_tag_name', __('This plugin does not allow the parsing of a "'.$tag_name.'" tag.'), $error_data);
			}
		}
		// END of get_tag_start_position_with_attribute_name_and_value($tag_name, $attribute_name, $attribute_value, $html_offset=0)
		
		
		/**
		 * Returns a subset of the HTML code saved between two index values.
		 * 
		 * @return:  [string] HTML code
		 *           [bool] false, if there are no values in the range or invalid input ($to value is less than $from)
		 */
		public function get_the_HTML( $from, $to )
		{
			$length = $to - $from;
			if ($length > 0) {
				return substr($this->website_HTML, $from, $length);
			}
			return false;
		}
		// END of get_the_HTML($from, $to)
		
		
		/**
		 * [DESCRIPTION]
		 * @return:  [array]
		 *           [bool] false,
		 */
		public function get_url_variables()
		{
			return $this->url_variables;
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function HTML_content_is_saved()
		{
			if (strlen($this->website_HTML) > 0)
			{
				return true;
			}
			return false;
		}
		
		
		/**
		 * Determines if a tag name cannot be used by this class.
		 *
		 * @return:  [bool] true, if the tag CAN be used.
		 *           [bool] false, if the tag CANNOT be used.
		 */
		public function is_valid_tag_name( $tag_name )
		{
			$is_valid = false;
			$i = 0;
			while ($i < count($this->valid_HTML_tags))
			{
				if ($tag_name == $this->valid_HTML_tags[$i])
				{
					return true;
				}
				$i++;
			}
			return false;
		}
		// END of is_valid_tag_name( $tag_name )
		
		
		/**
		 * Find the minimum value of a character position in a string, accouting for values of false.
		 *
		 * @parameters: [int array]
		 * @return:     [int] minimum value. If one value = false, while the other has a value, it returns the other's value.
		 *              [bool] false, if both values are false
		 */
		public function minimum_position( $positions )
		{
			$num_positions = count($positions);
			if (is_array($positions) && $num_positions > 1)
			{
				$i = 0;
				$min_value = $positions[$i];
				while ($i < $num_positions)
				{
					if (is_bool($min_value) && !is_bool($positions[$i]))
					{
						$min_value = $positions[$i];
					}
					elseif (!is_bool($min_value) && !is_bool($positions[$i]))
					{
						$min_value = min($min_value, $positions[$i]);
					}
					$i++;
				}
				return $min_value;
			} 
			elseif (is_array($positions) && $num_positions == 1)
			{
				return $position[0];
			}
			elseif (is_numeric($positions) && $positions >= 0)
			{
				return $position;
			}
			else
			{
				return false;
			}
		}
		// END of minimum_position( $positions )
		
		
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
		public function print_HTML_content_within_tag($tag_name, $offset=0)
		{
			print_r($this->get_HTML_content_within_tag($tag_name, $offset));
		}
		// END of print_HTML_content_within_tag($tag_name, $offset=0)
		
		
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
		 * [DESCRIPTION]
		 */
		public function remove_all_comments()
		{
			do
			{
				$tag_start = $this->get_tag_start_position_from_html( $this->website_HTML, "!--" );
				if ($tag_start !== false)
				{
					$tag_end = stripos($this->website_HTML, "-->", $tag_start);
					if ($tag_end !== false)
					{
						$this->website_HTML = $this->str_remove( $tag_start, $tag_end + 3);
					}
				}
			} while ($tag_start !== false && $tag_end !== false);
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function remove_all_tags( $tag_name )
		{
			do
			{
				$tag_start = $this->get_tag_start_position_from_html( $this->website_HTML, $tag_name );
				if ($tag_start !== false)
				{
					$tag_end = $this->get_tag_end_position_from_html( $this->website_HTML, $tag_name );
					if ($tag_end !== false)
					{
						$this->website_HTML = $this->str_remove( $tag_start, $tag_end );
					}
				}
			} while ($tag_start !== false && $tag_end !== false);
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function remove_unwanted_data()
		{
			if ($this->options['remove_header'] === true)
			{
				// Crop all HTML outside the body tags
				$body_start = $this->get_tag_start_position( "body" );
				$body_end = $this->get_tag_end_position( "body" );
				if (is_numeric($body_start) && is_numeric($body_end)) {
					$body_start = stripos($this->website_HTML, ">", $body_start) + 1;
					$this->website_HTML = $this->get_the_HTML($body_start, $body_end - 7);
				}
			}
			if ($this->options['remove_comments'] === true)
			{
				$this->remove_all_comments(); 
			}
			if ($this->options['remove_script'] === true)
			{
				$this->remove_all_tags('script');
			}
			if ($this->options['remove_style'] === true)
			{
				$this->remove_all_tags('style');
			}
			if ($this->options['remove_whitespace'] === true)
			{
				$this->remove_whitespace();
			}
		}
		// END of remove_header_comments_style_and_script_tags()
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function remove_whitespace()
		{
			// Trim whitespace from the beginning and end
			$this->website_HTML = trim($this->website_HTML);
			
			// Trim whitespace from the middle
			$offset = 0;
			do
			{
				$end_position = strpos($this->website_HTML, "\n", $offset);
				if ($end_position !== false)
				{
					if ($end_position == $offset)
					{
						$this->website_HTML = $this->str_remove( $offset - 1, $end_position + 1 );
					} 
					else
					{
						$line = substr($this->website_HTML, $offset, $end_position - $offset);
						$all_spaces = true;
						$i = $offset;
						while(($all_spaces === true) && ($i<$end_position))
						{
							if ($this->website_HTML[$i] !== ' ')
							{
								$all_spaces = false;
							}
							$i++;
						}
						if ($all_spaces === true)
						{
							$this->website_HTML = $this->str_remove( $offset - 1, $end_position + 1 );
						}
						else // Move to the next line
						{
							$offset = $end_position + 1;
						}
					}
				}
			} while ($end_position !== false);
		}
		// END of remove_whitespace()
		
		
		/**
		 * Saves HTML code.
		 */
		public function save_HTML($new_HTML)
		{
			$this->website_HTML = (string)$new_HTML;
			$this->remove_unwanted_data();
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
			$this->website_URL = $new_URL;
			if(is_string($new_URL)) {
				$this->set_url_variables($new_URL);
				$contents = @file_get_contents($new_URL);
				if ($contents === false) {
					return new WP_Error('invalid_URL', 'ERROR: The function save_HTML_with_URL() could not open the URL.');
				} else {
					$this->website_HTML = $contents;
					$this->remove_unwanted_data();// Return WP_Error???
					return true;
				}
			}
			return new WP_Error('invalid_URL', 'ERROR: The function save_HTML_with_URL() requries string input for the URL.');
		}
		// END of save_HTML_with_URL($new_URL)
		
		
		public function save_to_database( $search_author )
		{
			if (!is_numeric($search_author))
			{
				$search_author = 0;
			}
			
			$query = "INSERT INTO ".$wpdb->prefix."searches (
				ID,
				search_author,
				search_date,
				search_date_gmt,
				search_title,
				website_url,
				website_html,
				remove_comments,
				remove_header,
				remove_script,
				remove_style,
				remove_whitespace,
				container_tag_name,
				container_attribute_name,
				container_attribute_value,
				item_tag_name,
				item_attribute_name,
				item_attribute_value
			) VALUES (
				null,
				" . $search_author . ",
				" . date('Y-m-d H:i:s') . ",
				" . gmdate('Y-m-d H:i:s') . ",
				TITLE,
				" . $this->website_URL . ",
				" . $this->website_HTML . ",
				" . y_or_n($this->options['remove_comments']) . ",
				" . y_or_n($this->options['remove_header']) . ",
				" . y_or_n($this->options['remove_script']) . ",
				" . y_or_n($this->options['remove_style']) . ",
				" . y_or_n($this->options['remove_whitespace']) . ",
				" . $this->options['container_tag_name'] . ",
				" . $this->options['container_attribute_name'] . ",
				" . $this->options['container_attribute_value'] . ",
				" . $this->options['item_tag_name'] . ",
				" . $this->options['item_attribute_name'] . ",
				" . $this->options['item_attribute_value'] . "
			)";
			
			$result = $wpdb->query( $query );
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function set_url_variables( $new_URL )
		{
			$var_start = stripos($this->website_URL, '?');
			if ($var_start !== false)
			{
				$website_URL_vars = substr($this->website_URL, $var_start + 1);
				$url_variables = array();
				do
				{
					$equal_char = stripos($website_URL_vars, '=');
					if ($equal_char !== false)
					{
						$var_name = substr($website_URL_vars, 0, $equal_char);
						$website_URL_vars = substr($website_URL_vars, $equal_char + 1);
						$ampersand_char = stripos($website_URL_vars, '&');
						if ($ampersand_char !== false)
						{
							$var_value = substr($website_URL_vars, 0, $ampersand_char);
							$website_URL_vars = substr($website_URL_vars, $ampersand_char + 1);
							$url_variables[$var_name] = rawurldecode($var_value);
						} elseif (strlen($website_URL_vars) > 0) {
							// Last variable
							$url_variables[$var_name] = rawurldecode($website_URL_vars);
						}
					}
				} while ($equal_char !== false && $ampersand_char !== false);
			}
		}
		// END of set_url_variables( $new_URL )
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function set_options( $options )
		{
			$return_wp_error = false;
			if (is_bool($options['remove_comments'])) {
				$this->options['remove_comments'] = $options['remove_comments'];
			} else {
				$return_wp_error = true;
			}
			if (is_bool($options['remove_header'])) {
				$this->options['remove_header'] = $options['remove_header'];
			} else {
				$return_wp_error = true;
			}
			if (is_bool($options['remove_script'])) {
				$this->options['remove_script'] = $options['remove_script'];
			} else {
				$return_wp_error = true;
			}
			if (is_bool($options['remove_style'])) {
				$this->options['remove_style'] = $options['remove_style'];
			} else {
				$return_wp_error = true;
			}
			if (is_bool($options['remove_whitespace'])) {
				$this->options['remove_whitespace'] = $options['remove_whitespace'];
			} else {
				$return_wp_error = true;
			}
			if ($return_wp_error) {
				return new WP_Error('set_options_error', 'ERROR: All options values must be true or false.');
			}
			return true;
		}
		// END of set_options( $options )
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function str_crop( $from, $to )
		{
			$this->website_HTML = $this->get_the_HTML($from, $to);
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function str_remove( $from, $to )
		{
			$text_before = "";
			if ($from > 0 )
			{
				$text_before = substr($this->website_HTML, 0, $from);
			}
			$text_after = substr($this->website_HTML, $to, strlen($this->website_HTML) - $to);
			return $text_before . $text_after;
		}
		
		
		/**
		 * Determines if there is a tag with an attibute within $this->website-HTML starting from an offset $tag_start.
		 *
		 * @preconditions:  The first element $this->website_HTML[$tag_start] = '<'.
		 * @return:         [int] position, if there is a tag $attribute_name.
		 *                  [bool] false, if there is no tag $attribute_name.
		 *                  [WP_Error], if there are no start and or ending tag characters
		 */
		public function tag_has_attribute_name( $attribute_name, $tag_start )
		{
			if($this->website_HTML[$tag_start] == '<')
			{
				$tag_end = stripos($this->website_HTML, ">", $tag_start);
				if ($tag_end !== false)
				{
					$opening_tag = substr($this->website_HTML, $tag_start, $tag_end - $tag_start + 1);
					return stripos($opening_tag, $attribute_name);
				}
				$error_data = array(
					"attribute_name" => $attribute_name,
					"tag_end"        => $tag_end,
					"tag_start"      => $tag_start
				);
				return new WP_Error('invalid_tag_end', __('There is no ending tag character ">" after the start tag character "<" was found.'), $error_data);
			}
			$error_data = array(
					"attribute_name" => $attribute_name,
					"tag_start"      => $tag_start
				);
			return new WP_Error('invalid_tag_start', __('The first element must be "<".'), $error_data);
		}
		// END of tag_has_attribute_name( $attribute_name, $tag_start )
		
		
		/**
		 * Determines if there is a tag with an attibute name and value or not within $this->website-HTML starting from an offset $start.
		 *
		 * @preconditions:  The first element $this->website_HTML[$start] = '<'.
		 * @return:         [bool] true, if there is a tag $attribute_name with $attribute_value.
		 *                  [bool] false, if there is no tag $attribute_name with $attribute_value.
		 */
		public function tag_has_attribute_name_and_value( $attribute_name, $attribute_value, $start )
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
					$prev_character = substr($attribute_values, $attribute_value_start - 1, 1);
					$next_character = substr($attribute_values, $attribute_value_start + strlen($attribute_value), 1);
					if ((($prev_character == $quote) || ($prev_character == " ")) && (($next_character == $quote) || ($next_character == " ")))
					{// The previous and next character should be a quote or a space. Then we have a match.
						return true;
					}
				}
				return false;
			}
			return false;
		}
		// END of tag_has_attribute_name_and_value( $attribute_name, $attribute_value, $start )
		
		public function y_or_n( $bool )
		{
			return $bool ? 'Y' : 'N';
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
		
		// Add JQuery functionality for AJAX
		function add_wp_html_parser_scripts() {
			// Use JQuery already in WordPress
			wp_enqueue_script( 'jquery' );
			
			// Register scripts
			wp_register_script( 'wp-html-parser-js', plugins_url( '/js/jquery.js' , __FILE__ ), array( 'jquery' ), '', true); 
			
			// Enqueue scripts
			wp_enqueue_script( 'wp-html-parser-js' );
		}
		add_action( 'wp_enqueue_scripts', 'add_wp_html_parser_scripts' );
		
		// Include Helper PHP Functions
		include_once('ajax-php-scripts/get-variables-function.php');
    }
}
// END of if(class_exists('WP_HTML_Parser'))
?>