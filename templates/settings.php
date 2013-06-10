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
?>
<!-- =========================================================================================== -->
<!-- Settings page for the HTML Parser plugin -->
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br/>
	</div>
	<h2>WP HTML Parser Settings</h2>
    <form method="post" action="options.php">
		<?php @settings_fields('wp_html_parser-group'); ?>
		<?php @do_settings_fields('wp_html_parser-group'); ?>
        <h3>Information Source</h3>
    	<table class="form-table">
        	<tr valign="top">
        		<th scope="row"><label for="source_title">Title</label></th>
            	<td><p id="source_title" class="description"><?php echo get_option('source_title'); ?></p></td>
        	</tr>
        	<tr valign="top">
        		<th scope="row"><label for="source_URL">URL</label></th>
            	<td><input type="text" name="source_URL" id="source_URL" class="regular-text code" value="<?php echo get_option('source_URL'); ?>" /></td>
        	</tr>
            <tr valign="top">
        		<th scope="row"><label for="search_keyword">Search Keyword</label></th>
            	<td><input type="text" name="search_keyword" id="search_keyword" class="regular-text code" value="<?php echo get_option('search_keyword'); ?>" /></td>
        	</tr>
        </table>
		<?php @submit_button(); ?>
    </form>
</div> <!-- End of .wrap -->
<!-- =========================================================================================== -->