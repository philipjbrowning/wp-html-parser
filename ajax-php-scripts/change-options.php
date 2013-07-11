<?php
// Include WP-Load
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

if (!empty($_GET)) {
	$get_variables = get_variables($_GET);
	$html = new WP_HTML_Parser;
	$options_result = $html->set_options( $get_variables['options'] );
	// print_r ( $options_result );
	// print_r ( "False = " . false );
	$save_result = $html->save_HTML_with_URL( $get_variables['website_URL'] );
	if (!is_wp_error($save_result))
	{
		echo '<pre>';
		print_r( $html->get_all_HTML() );
		echo '</pre>';
	}
	else // It is an error
	{
		echo '<p>WP Error</p>';
	}
} else {
	echo '<p>There was an error retrieving your data.</p>'; //
}
?>