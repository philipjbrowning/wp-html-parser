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
            <label for="ASIN">ASIN</label>
        </th>
        <td>
            <input type="text" id="ASIN" name="ASIN" value="<?php echo @get_post_meta($post->ID, 'ASIN', true); ?>" />
        </td>
    <tr> 
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
            <label for="amazon_old_price_old">Old Amazon Price</label>
        </th>
        <td>
            <input type="text" id="amazon_old_price_old" name="amazon_old_price_old" value="<?php echo @get_post_meta($post->ID, 'amazon_old_price_old', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="category_URL">Category URL</label>
        </th>
        <td>
            <input type="text" id="category_URL" name="category_URL" value="<?php echo @get_post_meta($post->ID, 'category_URL', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="comments_count">Number of Comments</label>
        </th>
        <td>
            <input type="text" id="comments_count" name="comments_count" value="<?php echo @get_post_meta($post->ID, 'comments_count', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="comments_URL">Comments URL</label>
        </th>
        <td>
            <input type="text" id="comments_URL" name="comments_URL" value="<?php echo @get_post_meta($post->ID, 'comments_URL', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="marketplace_price_new">Marketplace Price (New)</label>
        </th>
        <td>
            <input type="text" id="marketplace_price_new" name="marketplace_price_new" value="<?php echo @get_post_meta($post->ID, 'marketplace_price_new', true); ?>" />
        </td>
    <tr>
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="marketplace_price_used">Marketplace Price (Used)</label>
        </th>
        <td>
            <input type="text" id="marketplace_price_used" name="marketplace_price_used" value="<?php echo @get_post_meta($post->ID, 'marketplace_price_used', true); ?>" />
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
<?php
/*
			'product_URL',
			'rating',
			'thumbnail_URL
*/
?>