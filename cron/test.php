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


// Testing a cron job to send an email to me in a certain time interval
$to = "pbrowning@scs.howard.edu";
$from = "info@ilife.mobi";
$subject = 'Testing Cron on ' . date('D, j M, Y');
$message = '<h2>Cron is working, remove test cron job now.</h2>';
$headers = "From: $from\n";
$headers .= "MIME_Version: 1.0\n";
$headers .= "Content-type: text/html; c harset=iso-8859-1\n";
mail($to, $subject, $message, $headers);

/**
 * URI:         http://www.amazon.com/s/ref=nb_sb_noss
 * Parameters:  url = search-alias%3Daps
 *              field-keywords = bike
 */
/**
 * URI:         http://www.amazon.com/s/ref=sr_pg_2
 * Parameters:  rh = i%3Aaps%2Ck%3Abike
 *              page = 2
 *              keywords = bike
 *              ie = UTF8
 *              qid = 1370970761
 */
/**
 * URI:         http://www.amazon.com/s/ref=sr_pg_1
 *              rh = i%3Aaps%2Ck%3Abike
 *              keywords = bike
 *              ie = UTF8
 *              qid = 1370971213
 */
?>