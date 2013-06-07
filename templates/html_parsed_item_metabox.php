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

<table> 
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="amazon_price">Amazon Price</label>
        </th>
        <td>
            <input type="text" id="amazon_price" name="amazon_price" value="<?php echo @get_post_meta($post->ID, 'amazon_price', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="seller_new_price">Seller Price (New)</label>
        </th>
        <td>
            <input type="text" id="seller_new_price" name="seller_new_price" value="<?php echo @get_post_meta($post->ID, 'seller_new_price', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="seller_used_price">Seller Price (Used)</label>
        </th>
        <td>
            <input type="text" id="seller_used_price" name="seller_used_price" value="<?php echo @get_post_meta($post->ID, 'seller_used_price', true); ?>" />
        </td>
    <tr>                
</table>