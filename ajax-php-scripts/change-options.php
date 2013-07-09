<?php
// Include WP-Load
// include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

// Include WP HTML Parser Class
include_once( '../wp_html_parser.php' );

if (!empty($_GET)) {
	$get_variables = get_variables($_GET);
	$html = new WP_HTML_Parser;
	$options_result = $html->set_options($remove_comments, $remove_header, $remove_script, $remove_style, $remove_whitespace);
	$html->save_HTML_with_URL( $website_URL );
	echo '<pre>' . print_r( $html->get_all_HTML() ) . '</pre>';
} else {
	echo '<p>There was an error retrieving your data.</p>'; //
}
?>