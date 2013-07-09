<?php
function get_variables()
{
	if (!empty($_GET)) {
		// Save Data Options
		$options = array (
			'remove_comments'         => false,
			'remove_header'           => false,
			'remove_script'           => false,
			'remove_style'            => false,
			'remove_whitespace'       => false
		);
		
		if (isset($_GET['remove_comments']))
		{
			$options['remove_comments'] = true;
		}
		if (isset($_GET['remove_header']))
		{
			$options['remove_header'] = true;
		}
		if (isset($_GET['remove_script']))
		{
			$options['remove_script'] = true;
		}
		if (isset($_GET['remove_style']))
		{
			$options['remove_style'] = true;
		}
		if (isset($_GET['remove_whitespace']))
		{
			$options['remove_whitespace'] = true;
		}
		
		//
		$settings = array (
			'list_container_tag_name' => false,
			'list_item_tag_name'      => false,
			'options'                 => $options,
			'website_URL'             => $_GET['website_URL']
		);
		if (isset($_GET['list_container_tag_name']))
		{
			$settings['list_container_tag_name'] = $_GET['list_container_tag_name'];
		}
		if (isset($_GET['list_item_tag_name']))
		{
			$settings['list_item_tag_name'] = $_GET['list_item_tag_name'];
		}
		
		return $settings;
	}
	else
	{
		return false;
	}
}