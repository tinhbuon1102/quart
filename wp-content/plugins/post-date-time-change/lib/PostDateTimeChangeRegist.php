<?php
/**
 * Post Date Time Change
 * 
 * @package    Post Date Time Change
 * @subpackage Post Date Time Change Regist registered in the database
    Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$postdatetimechangeregist = new PostDateTimeChangeRegist();

class PostDateTimeChangeRegist {

	/* ==================================================
	 * Construct
	 * @since	5.00
	 */
	public function __construct() {

		add_action( 'admin_init', array($this, 'register_settings') );

	}

	/* ==================================================
	 * Settings register
	 * @since	4.02
	 */
	public function register_settings(){

		$wp_options_name = 'postdatetimechange_settings'.'_'.get_current_user_id();
		$postdatetimechange_settings = get_option($wp_options_name);

		$method = 'posted';
		$write = 'date_modified';
		$picker = 1;

		// Before Ver 5.00
		if ( get_option('postdatetimechange_method') ) {
			$method = get_option('postdatetimechange_method');
			delete_option('postdatetimechange_method'); 
		}
		if ( get_option('postdatetimechange_write') ) {
			$write = get_option('postdatetimechange_write');
			delete_option('postdatetimechange_write'); 
		}
		if ( get_option('postdatetimechange_picker') ) {
			$picker = get_option('postdatetimechange_picker');
			delete_option('postdatetimechange_picker'); 
		}
		if ( get_option('postdatetimechange_exif') ) {
			delete_option('postdatetimechange_exif'); 
		}

		if ( !get_option($wp_options_name) ) {
			$postdatetimechange_tbl = array(
							'method' => $method,
							'write' => $write,
							'picker' => $picker
						);
			update_option( $wp_options_name, $postdatetimechange_tbl );
		}

	}

}

?>