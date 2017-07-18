<?php
/*
Plugin Name: Woocommerce Mailchimp Discount
Plugin URI: http://magnigenie.com
Description: The plugin allows you to offer discounts to the users if they subscribe to your mailing list.
Version: 2.1
Author: Magnigenie
Author URI: http://magnigenie.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wcmd
*/

// No direct file access
! defined( 'ABSPATH' ) AND exit;

define('WCMD_FILE', __FILE__);
define('WCMD_PATH', plugin_dir_path(__FILE__));
define('WCMD_BASE', plugin_basename(__FILE__));

add_action('plugins_loaded', 'wcmd_load_textdomain');

function wcmd_load_textdomain() {
	load_plugin_textdomain( 'wcmd', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );
}

require WCMD_PATH . '/includes/class-mailchimp.php';
require WCMD_PATH . '/includes/class-wcmd.php';

new wcmd();