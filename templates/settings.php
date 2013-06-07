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
	<h2>WP Plugin Template</h2>
    <form method="post" action="../../html-parser/templates/options.php">
		<?php @settings_fields('wp_html_parser-group'); ?>
		<?php @do_settings_fields('wp_html_parser-group'); ?>
    	<table class="form-table">
        	<tr valign="top">
        		<th scope="row"><label for="setting_a">Setting A</label></th>
            	<td><input type="text" name="setting_a" id="setting_a" value="<?php echo get_option('setting_a'); ?>" /></td>
        	</tr>
        	<tr valign="top">
        		<th scope="row"><label for="setting_b">Setting B</label></th>
            	<td><input type="text" name="setting_b" id="setting_b" value="<?php echo get_option('setting_b'); ?>" /></td>
        	</tr>
        </table>
		<?php @submit_button(); ?>
    </form>
</div> <!-- End of .wrap -->
<!-- =========================================================================================== -->