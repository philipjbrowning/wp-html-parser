// JavaScript Document
jQuery(document).ready(function() {
    // jQuery(#somefunction) ...
	
});





/**
 * [DESCRIPTION]
 */
function my_alert_text() {
	alert("My Alert Text");
}

/**
 * [DESCRIPTION]
 */
function change_options(settings, callback)
{
	console.log('change_options');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/change-options.php', settings, callback);
}

/**
 * [DESCRIPTION]
 */
function change_url(settings, callback)
{
	console.log('change_url');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/change-url.php', settings, callback);
}

/**
 * [DESCRIPTION]
 */
function get_attribute_name_list(settings, callback)
{
	console.log('get_attribute_name_list');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/get-attribute-name-list.php', settings, callback);
}

/**
 * Attach the URL for retrieving a list of tag names to the get_request function.
 */
function get_tag_name_list(settings, callback)
{
	console.log('get_tag_name_list');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/get-tag-name-list.php', settings, callback);
}

/**
 * [DESCRIPTION]
 */
function get_attribute_value_list(settings, callback)
{
	console.log('get_attribute_value_list');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/get-tag-value-list.php', settings, callback);
}

/**
 * [DESCRIPTION]
 */
function get_request(request_url, settings, callback)
{
	console.log('#1 [get_request]');
	jQuery(document).ready(function($) {
		console.log('#2 [jQuery(document).ready]');
		$.get(request_url, {
			list_container_attribute_name  : settings['list_container_attribute_name'],
			list_container_attribute_value : settings['list_container_attribute_value'],
			list_container_tag_name        : settings['list_container_tag_name'],
			list_item_attribute_name       : settings['list_item_attribute_name'],
			list_item_attribute_value      : settings['list_item_attribute_value'],
			list_item_tag_name             : settings['list_item_tag_name'],
			remove_comments                : settings['remove_comments'],
			remove_header                  : settings['remove_header'],
			remove_script                  : settings['remove_script'],
			remove_style                   : settings['remove_style'],
			remove_whitespace              : settings['remove_whitespace'],
			search_submit                  : settings['search_submit'],
			url_variables                  : settings['url_variables'],
			website_URL                    : settings['website_URL']
		}, function(data) {
			callback(data);
		});
	});
}

/**
 * [DESCRIPTION]
 */
function highlight_word(settings, callback)
{
	console.log('highlight_word');
	get_request('wp-content/plugins/wp_html_parser/ajax-php-scripts/highlight-word.php', settings, callback);
}

