<?php
/*
Plugin Name: Post Date Time Change
Plugin URI: https://wordpress.org/plugins/post-date-time-change/
Version: 5.04
Description: Collectively change the date and time of each article of post or page or media library.
Author: Katsushi Kawamori
Author URI: https://riverforest-wp.info/
Text Domain: post-date-time-change
Domain Path: /languages
*/

/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

	add_action( 'plugins_loaded', 'post_date_time_change_load_textdomain' );
	function post_date_time_change_load_textdomain() {
		load_plugin_textdomain('post-date-time-change');
	}

	if(!class_exists('PostDateTimeChangeRegist')) require_once( dirname(__FILE__).'/lib/PostDateTimeChangeRegist.php' );
	if(!class_exists('PostDateTimeChange')) require_once( dirname(__FILE__).'/lib/PostDateTimeChange.php' );

?>