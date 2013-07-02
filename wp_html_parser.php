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
		/* ========================================================================================= *
		 * SUMMARY OF THE WP_HTML_PARSER CLASS                                                       *
		 * ========================================================================================= *
		
			----------------
			Public Varibales
			----------------
			
			(string) website_HTML;
			(array) valid_HTML_tags;
			
			----------------
			Public Functions
			----------------
			
			__construct()
			activate()
			deactivate()
			
			get_attribute_value_of_tag( $tag_name, $attribute_name, $html_offset=0 )
			get_HTML_within_tag( $tag_name, $html_offset=0 )
			get_tag_end_position( $tag_name, $html_offset=0 )
			get_tag_start_position( $tag_name, $html_offset=0 )
			get_tag_start_position_with_attribute_name_and_value( $tag_name, $attribute_name, $attribute_value, $html_offset=0 )
			
			print_all_HTML()
			print_HTML_within_tag( $tag_name, $offset=0)
			print_HTML_content_within_tag( $tag_name, $offset=0 )
			print_the_HTML( $from, $to )
			
			save_HTML( $new_HTML )
			save_HTML_with_URL( $new_URL )
			
			-----------------
			Private Functions
			-----------------
			
			boolString( $bValue = false ) {
			get_the_HTML( $from, $to )
			get_tag_start_position_from_html( $html_block, $tag_name, $html_offset=0 )
			get_tag_end_position_from_html( $html_block, $tag_name, $html_offset=0 )
			
			is_valid_tag_name( $tag_name )
			
			tag_has_attribute_name( $attribute_name, $start )
			tag_has_attribute_name_and_value( $attribute_name, $attribute_value, $start )
		
		 * ==================================================================================================== *
		 * PRIVATE VARIABLE DECLARATIONS                                                                        *
		 * ==================================================================================================== */
		
		const MIN_TAG_LENGTH = 2;
		
		private $options = array(
			'remove_comments' => true,
			'remove_header' => true,
			'remove_script' => true,
			'remove_style' => true // CSS
		);
		
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
			'title',
		 	'tr'
		);
		
		private $website_HTML = "";
		
		
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
		 * [FUNCTION DESCRIPTION]
		 */
		public function get_all_tag_names()
		{
			$offset = 0;
			echo "<pre>";
			$count = 0;
			do
			{
				$tag_start = stripos($this->website_HTML, '<', $offset);
				if (($this->website_HTML[$tag_start + 1] !== '/') && ($tag_start !== false))
				{
					echo "<p>tag_start = $tag_start</p>";
					$tag_ending_space = stripos($this->website_HTML, ' ', $tag_start);
					echo "<p>tag_ending_space = $tag_ending_space</p>";
					$tag_ending_gt = stripos($this->website_HTML, '>', $tag_start);
					echo "<p>tag_ending_gt = $tag_ending_gt</p>";
					$tag_ending_slash = stripos($this->website_HTML, '/', $tag_start);
					echo "<p>tag_ending_slash = $tag_ending_slash</p>";
					$tag_ending = $this->minimum_position($tag_ending_space, $tag_ending_gt);
					$tag_ending = $this->minimum_position($tag_ending, $tag_ending_slash);
					echo "<p>tag_ending = $tag_ending</p>";
					$tag_name = substr($this->website_HTML, $tag_start + 1, $tag_ending - $tag_start); // LOGIC ERROR HERE WITH ">" and "/" --------------
					echo "<p>tag_name = $tag_name</p>";
				}
				$offset = $tag_start + 1;
			} while ($count++ < 5); // ($tag_start !== false);
			echo "</pre>";
		}
		
		
		/**
		 * [FUNCTION DESCRIPTION]
		 */
		public function get_attribute_value_of_tag( $tag_name, $attribute_name, $html_offset=0 )
		{
			if($this->HTML_content_is_saved())
			{
				if ($this->is_valid_tag_name($tag_name))
				{
					echo "<p>---------------------------------------</p>";
					echo "<p>get_attribute_value_of_tag START</p>";
					echo "<p> tag_name: ".$tag_name."</p>";
					echo "<p> attribute_name: ".$attribute_name."</p>";
					echo "<p> html_offset: ".$html_offset."</p>";
					$start_position = $this->get_tag_start_position_from_html($this->website_HTML, $tag_name, $html_offset);
					if (is_numeric($start_position))
					{
						$attribute_start = $this->tag_has_attribute_name( $attribute_name, $html_offset );
						if (!is_wp_error($attribute_start))
						{
							// MORE CODE
							
							
							
							return $attribute_start;
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
					// NEED TO EDIT -------------------------------------------------------------------------------------------------------------- FIX BELOW
					/*
					if ($start = $this->get_tag_start_position($tag_name, $html_offset))
					{
						if($attribute_start = $this->tag_has_attribute_name( $attribute_name, $html_offset ))
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
							$quote_end = stripos($opening_tag, $quote, $quote_start+1);
							$length = $quote_end - $quote_start + 1;
							$attribute_values = substr($opening_tag, $quote_start, $length);
							return $attribute_values;
						}
						echo '<p><u>ERROR</u>: No attribute with name '.$attribute_name.' found within '.$tag_name.'</p>';
						return false;
					}
					echo '<p><u>ERROR</u>: No tag with name '.$tag_name.' found.</p>';
					return false;
					// NEED TO EDIT -------------------------------------------------------------------------------------------------------------- FIX ABOVE
				*/
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
		
		/**
		 * Gets the HTML code pulled from a URL.
		 *
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
					if ($current_position = $this->get_tag_start_position($tag_name, $current_offset))
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
		 * Prints the HTML code within a specific tag in $website_HTML
		 */
		public function print_HTML_content_within_tag($tag_name, $offset=0)
		{
			print_r($this->get_HTML_content_within_tag($tag_name, $offset));
		}
		// END of print_HTML_content_within_tag($tag_name, $offset=0)
		
		
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
			$this->remove_header_comments_style_and_script_tags();
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
				$contents = @file_get_contents($new_URL);
				if ($contents === false) {
					return new WP_Error('invalid_URL', 'ERROR: The function save_HTML_with_URL() could not open the URL.');
				} else {
					$this->website_HTML = $contents;
					$this->remove_header_comments_style_and_script_tags();// Return WP_Error???
					return true;
				}
			}
			return new WP_Error('invalid_URL', 'ERROR: The function save_HTML_with_URL() requries string input for the URL.');
		}
		// END of save_HTML_with_URL($new_URL)
		
		
		/**
		 * [DESCRIPTION]
		 */
		public function set_options($remove_comments, $remove_header, $remove_script, $remove_style)
		{
			$return_wp_error = false;
			if (is_bool($remove_comments)) {
				$this->options['remove_comments'] = $remove_comments;
			} else {
				$return_wp_error = true;
			}
			if (is_bool($remove_header)) {
				$this->options['remove_header'] = $remove_header;
			} else {
				$return_wp_error = true;
			}
			if (is_bool($remove_script)) {
				$this->options['remove_script'] = $remove_script;
			} else {
				$return_wp_error = true;
			}
			if (is_bool($remove_style)) {
				$this->options['remove_style'] = $remove_style;
			} else {
				$return_wp_error = true;
			}
			if ($return_wp_error) {
				return new WP_Error('set_options_error', 'ERROR: All options values must be true or false.');
			}
			return true;
		}
		// END of set_options($remove_comments, $remove_header, $remove_script, $website_URL)
		
		
		/* ==================================================================================================== *
		 * PRIVATE FUNCTION DECLARATIONS                                                                        *
		 * ==================================================================================================== */
		
		
		/**
		 * Returns the string value of true or false of a boolean expression.
		 * 
		 * @return:  [string] 'true' or 'false'
		 */
		private function boolString( $bValue = false ) {
			return ($bValue ? 'true' : 'false');
		}
		// END of boolString( $bValue = false )
		
		
		
		/**
		 * Finds an end tag denoted by "/>".
		 *
		 * @return:  [int] position of the tag, if the tag exists within $website_HTML
		 *           [bool] false, if the tag does not exist within $website_HTML. This mirrors the stripos() function.
		 */
		private function get_next_short_close( $html_block, $tag_name, $html_offset=0 )
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
		 * Returns a subset of the HTML code saved between two index values.
		 * 
		 * @return:  [string] HTML code
		 *           [bool] false, if there are no values in the range or invalid input ($to value is less than $from)
		 */
		private function get_the_HTML( $from, $to )
		{
			$length = $to - $from;
			if ($length > 0) {
				return substr($this->website_HTML, $from, $length);
			}
			return false;
		}
		// END of get_the_HTML($from, $to)
		
		
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
					$next_end_position = $this->minimum_position($next_end_position_1, $next_end_position_2);
					$editing_position = $this->minimum_position($next_start_position, $next_end_position);
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
					$start_position = $editing_position;
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
		 * Determines if a tag name cannot be used by this class.
		 *
		 * @return:  [bool] true, if the tag CAN be used.
		 *           [bool] false, if the tag CANNOT be used.
		 */
		private function is_valid_tag_name( $tag_name )
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
		 * @return:  [int] minimum value. If one value = false, while the other has a value, it returns the other's value.
		 *           [bool] false, if both values are false
		 */
		private function minimum_position($a, $b)
		{
			if (is_bool($a) && !is_bool($b))
			{
				return $b;
			}
			elseif  (is_bool($b) && !is_bool($a))
			{
				return $a;
			}
			return min($a, $b);
		}
		
		
		/**
		 * [DESCRIPTION]
		 */
		private function remove_all_comments()
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
		public function remove_header_comments_style_and_script_tags()
		{
			if ($this->options['remove_header'] === true)
			{
				// Crop all HTML outside the body tags
				$body_start = $this->get_tag_start_position( "body" );
				$body_end = $this->get_tag_end_position( "body" );
				$body_start = stripos($this->website_HTML, ">", $body_start) + 1;
				$this->website_HTML = $this->get_the_HTML($body_start, $body_end - 7);
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
		}
		// END of remove_header_comments_style_and_script_tags()
		
		
		/**
		 * [DESCRIPTION]
		 */
		private function str_remove( $from, $to )
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
		private function tag_has_attribute_name_and_value( $attribute_name, $attribute_value, $start )
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
		// END of tag_has_attribute_name_and_value( $attribute_name, $attribute_value, $start )
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