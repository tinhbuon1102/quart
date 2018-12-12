<?php
/**
 * Post Date Time Change
 * 
 * @package    Post Date Time Change
 * @subpackage Post Date Time Change Main & Management screen
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

$postdatetimechange = new PostDateTimeChange();
add_action( 'admin_notices', array( $postdatetimechange, 'custom_bulk_admin_notices') );

class PostDateTimeChange {

	private $plugin_base_url;
	private $is_add_on_activate;
	private $post_custom_types;

	/* ==================================================
	 * Construct
	 * @since	4.10
	 */
	public function __construct() {

		$plugin_base_dir = untrailingslashit(plugin_dir_path( __DIR__ ));
		$slugs = explode('/', $plugin_base_dir);
		$slug = end($slugs);
		$plugin_dir = untrailingslashit(rtrim($plugin_base_dir, $slug));

		$this->post_custom_types = array( 'post', 'page' );

		$custompost_active = FALSE;
		if( function_exists('post_date_time_change_add_on_custompost_load_textdomain') ){
			if(!class_exists('PostDateTimeChangeCustompost')){
				require_once( $plugin_dir.'/post-date-time-change-add-on-custompost/lib/PostDateTimeChangeCustompost.php' );
			}
			$custompost_active = TRUE;
			$postdatetimechangecustompost = new PostDateTimeChangeCustompost();
			$this->post_custom_types = $postdatetimechangecustompost->add_custom_post_type($this->post_custom_types);
		}
		$exif_active = FALSE;
		if( function_exists('post_date_time_change_add_on_exif_load_textdomain') ){
			if(!class_exists('PostDateTimeChangeExif')){
				require_once( $plugin_dir.'/post-date-time-change-add-on-custompost/lib/PostDateTimeChangeExif.php' );
			}
			$exif_active = TRUE;
		}
		$this->is_add_on_activate = array(
			'custompost'	=>	$custompost_active,
			'exif'			=>	$exif_active
			);

		add_filter( 'plugin_action_links', array($this, 'settings_link'), 10, 2 );
		add_action( 'admin_menu', array($this, 'add_pages') );
		add_action( 'admin_enqueue_scripts', array($this, 'load_custom_wp_admin_style') );
		foreach ( $this->post_custom_types as $custom_type ) {
			add_filter( 'manage_'.$custom_type.'_posts_columns', array($this, 'ptuc_column'), 999 );
			add_action( 'manage_'.$custom_type.'_posts_custom_column', array($this, 'ptuc_value'), 999, 2 );
		}
		add_filter( 'manage_media_columns', array($this, 'muc_column') );
		add_action( 'manage_media_custom_column', array($this, 'muc_value'), 10, 2 );
		add_action( 'admin_footer', array( $this, 'custom_bulk_admin_footer') );
		add_action( 'load-edit.php', array( $this, 'custom_bulk_action') );
		add_action( 'load-upload.php', array( $this, 'custom_bulk_action_media') );

	}

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = 'post-date-time-change/postdatetimechange.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=postdatetimechange').'">'.__( 'Settings').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	public function add_pages() {
		add_options_page( 'Post Date Time Change Options', 'Post Date Time Change', 'edit_posts', 'postdatetimechange', array($this, 'manage_page') );
	}

	/* =================================================
	 * Add Css and Script
	 * @since	1.0
	 */
	public function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen1()) {
			wp_enqueue_style( 'jquery-responsiveTabs', plugin_dir_url( __DIR__ ).'css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', plugin_dir_url( __DIR__ ).'css/style.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-responsiveTabs', plugin_dir_url( __DIR__ ).'js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'postdatetimechange-admin-js', plugin_dir_url( __DIR__ ).'js/jquery.postdatetimechange.admin.js', array('jquery') );
		}
		if ($this->is_my_plugin_screen2()) {
			$postdatetimechange_settings = get_option($this->wp_options_name());
			wp_enqueue_script( 'jquery-responsiveTabs', plugin_dir_url( __DIR__ ).'js/jquery.responsiveTabs.min.js' );
			if($postdatetimechange_settings['picker']) {
				wp_enqueue_style( 'jquery-datetimepicker', plugin_dir_url( __DIR__ ).'css/jquery.datetimepicker.css' );
			}
			wp_enqueue_script( 'jquery' );
			if($postdatetimechange_settings['picker']) {
				wp_enqueue_script( 'jquery-datetimepicker', plugin_dir_url( __DIR__ ).'js/jquery.datetimepicker.js', null, '2.3.4' );
				wp_enqueue_script( 'jquery-postdatetimechange-datetimepicker', plugin_dir_url( __DIR__ ).'js/jquery.postdatetimechange.datetimepicker.js', array('jquery') );
			}
			wp_enqueue_script( 'postdatetimechange-js', plugin_dir_url( __DIR__ ).'js/jquery.postdatetimechange.js', array('jquery') );
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	1.0
	 */
	private function is_my_plugin_screen1() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'settings_page_postdatetimechange') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	/* ==================================================
	 * For only admin style
	 * @since	1.0
	 */
	private function is_my_plugin_screen2() {
		$screen = get_current_screen();
		if ( isset($_REQUEST['post_type']) ) {
			$posttype = sanitize_text_field($_REQUEST['post_type']);
		} else {
			$posttype = 'post';
		}
		if (is_object($screen) && $screen->id == 'edit-'.$posttype) {
			return TRUE;
		} else if (is_object($screen) && $screen->id == 'upload') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Main
	 */
	public function manage_page() {

		if ( !current_user_can( 'edit_posts' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if( !empty($_POST) ) {
			$post_nonce_field = 'postdatetimechange_tabs';
			if ( isset($_POST[$post_nonce_field]) && $_POST[$post_nonce_field] ) {
				if ( check_admin_referer( 'pdtc_settings', $post_nonce_field ) ) {
					$this->options_updated();
				}
			}
		}

		$postdatetimechange_settings = get_option($this->wp_options_name());
		$scriptname = admin_url('options-general.php?page=postdatetimechange');

		$method = $postdatetimechange_settings['method'];
		$write = $postdatetimechange_settings['write'];
		$picker = $postdatetimechange_settings['picker'];

		?>
		<div class="wrap">
		<h2>Post Date Time Change</h2>

		<div id="postdatetimechange-admin-tabs">
		  <ul>
		    <li><a href="#postdatetimechange-admin-tabs-1"><?php _e('Settings'); ?></a></li>
			<li><a href="#postdatetimechange-admin-tabs-2"><?php _e('Add On', 'post-date-time-change'); ?></a></li>
			<li><a href="#postdatetimechange-admin-tabs-3"><?php _e('Donate to this plugin &#187;'); ?></a></li>
		  </ul>

		  <div id="postdatetimechange-admin-tabs-1">
			<div class="wrap">

				<form method="post" action="<?php echo $scriptname; ?>">
					<?php wp_nonce_field('pdtc_settings', 'postdatetimechange_tabs'); ?>

					<h3 style="margin: 5px; padding: 5px;"><?php _e('View'); ?></h3>
					<div style="display: block;padding:5px 5px"><input type="checkbox" name="postdatetimechange_picker" value="1" <?php checked('1', $picker); ?> />DateTimePicker</div>
					<div style="display: block;padding:5px 5px"><input type="radio" id="postdatetimechange_method" name="postdatetimechange_method" value="posted" <?php if ($method === 'posted') echo 'checked'; ?>><?php _e('Posted', 'post-date-time-change'); ?></div>
					<div style="display: block;padding:5px 5px"><input type="radio" id="postdatetimechange_method" name="postdatetimechange_method" value="modified" <?php if ($method === 'modified') echo 'checked'; ?>><?php _e('Last updated'); ?></div>
					<?php
					if ( $this->is_add_on_activate['exif'] ) {
						$postdatetimechangeexif = new PostDateTimeChangeExif();
						$postdatetimechangeexif->exif_html();
					} else {
						?>
						<div style="display: block;padding:5px 5px">
						<input type="checkbox" disabled="disabled" />
						<?php _e('Exif Shooting Date Time', 'post-date-time-change'); ?> <span style="color: red;"><?php _e('Add On is required.', 'post-date-time-change'); ?></span>
						</div>
						<?php
					}
					?>
					<h3 style="margin: 5px; padding: 5px;"><?php _e('Change'); ?></h3>
					<div style="display: block;padding:5px 5px"><input type="radio" id="postdatetimechange_write" name="postdatetimechange_write" value="date_modified" <?php if ($write === 'date_modified') echo 'checked'; ?>><?php _e('Posted And Modified', 'post-date-time-change'); ?></div>
					<div style="display: block;padding:5px 5px"><input type="radio" id="postdatetimechange_write" name="postdatetimechange_write" value="date" <?php if ($write === 'date') echo 'checked'; ?>><?php _e('Only Posted', 'post-date-time-change'); ?></div>
					<div style="display: block;padding:5px 5px"><input type="radio" id="postdatetimechange_write" name="postdatetimechange_write" value="modified" <?php if ($write === 'modified') echo 'checked'; ?>><?php _e('Only Modified', 'post-date-time-change'); ?></div>
					<h3 style="margin: 5px; padding: 5px;"><?php _e('Post Type', 'post-date-time-change'); ?></h3>
					<?php
					if ( $this->is_add_on_activate['custompost'] ) {
						$postdatetimechangecustompost = new PostDateTimeChangeCustompost();
						$postdatetimechangecustompost->custom_post_type_html();
					} else {
						?>
						<div style="display: block;padding:5px 5px">
						<?php _e('Add'); ?>: <input type="text" disabled="disabled" /> <span style="color: red;"><?php _e('Add On is required.', 'post-date-time-change'); ?></span>
						</div>
						<?php
					}
					submit_button( __('Save Changes'), 'primary', 'Submit', TRUE );
					?>
					<div style="clear:both"></div>
				</form>

			</div>
		  </div>

		  <div id="postdatetimechange-admin-tabs-2">
			<div class="wrap">
			<?php $this->addons_page(); ?>
			</div>
		  </div>

		  <div id="postdatetimechange-admin-tabs-3">
			<div class="wrap">
			<?php $this->credit(); ?>
			</div>
		  </div>

		</div>
		</div>
		<?php

	}

	/* ==================================================
	 * Update	wp_options table.
	 * @since	5.00
	 */
	function options_updated(){

		$postdatetimechange_settings = get_option($this->wp_options_name());

		if ( !empty($_POST) ) {
			if ( !empty($_POST['postdatetimechange_method']) ) {
				$postdatetimechange_settings['method'] = sanitize_text_field($_POST['postdatetimechange_method']);
			}
			if ( !empty($_POST['postdatetimechange_write']) ) {
				$postdatetimechange_settings['write'] = sanitize_text_field($_POST['postdatetimechange_write']);
			}
			if ( !empty($_POST['postdatetimechange_picker']) ) {
				$postdatetimechange_settings['picker'] = 1;
			} else {
				$postdatetimechange_settings['picker'] = FALSE;
			}
			update_option( $this->wp_options_name(), $postdatetimechange_settings );
			if( $this->is_add_on_activate['exif'] ) {
				$postdatetimechangeexif = new PostDateTimeChangeExif();
				$postdatetimechangeexif->options_updated();
			}
			if( $this->is_add_on_activate['custompost'] ) {
				$postdatetimechangecustompost = new PostDateTimeChangeCustompost();
				$postdatetimechangecustompost->options_updated();
			}
			echo '<div class="notice notice-success is-dismissible"><ul><li>'.__('Settings').' --> '.__('Changes saved.').'</li></ul></div>';
		}

	}

	/* ==================================================
	 * Credit
	 */
	private function credit() {

		$plugin_name = NULL;
		$plugin_ver_num = NULL;
		$plugin_path = plugin_dir_path( __DIR__ );
		$plugin_dir = untrailingslashit($plugin_path);
		$slugs = explode('/', $plugin_dir);
		$slug = end($slugs);
		$files = scandir($plugin_dir);
		foreach ($files as $file) {
			if($file == '.' || $file == '..' || is_dir($plugin_path.$file)){
				continue;
			} else {
				$exts = explode('.', $file);
				$ext = strtolower(end($exts));
				if ( $ext === 'php' ) {
					$plugin_datas = get_file_data( $plugin_path.$file, array('name'=>'Plugin Name', 'version' => 'Version') );
					if ( array_key_exists( "name", $plugin_datas ) && !empty($plugin_datas['name']) && array_key_exists( "version", $plugin_datas ) && !empty($plugin_datas['version']) ) {
						$plugin_name = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __('Version:').' '.$plugin_ver_num;
		$faq = __('https://wordpress.org/plugins/'.$slug.'/faq', $slug);
		$support = 'https://wordpress.org/support/plugin/'.$slug;
		$review = 'https://wordpress.org/support/view/plugin-reviews/'.$slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/'.$slug;
		$facebook = 'https://www.facebook.com/katsushikawamori/';
		$twitter = 'https://twitter.com/dodesyo312';
		$youtube = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate = __('https://shop.riverforest-wp.info/donate/', $slug);

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo $plugin_version; ?> | 
		<a style="text-decoration: none;" href="<?php echo $faq; ?>" target="_blank"><?php _e('FAQ'); ?></a> | <a style="text-decoration: none;" href="<?php echo $support; ?>" target="_blank"><?php _e('Support Forums'); ?></a> | <a style="text-decoration: none;" href="<?php echo $review; ?>" target="_blank"><?php _e('Reviews', $slug); ?></a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo $translate; ?>" target="_blank"><?php echo sprintf(__('Translations for %s'), $plugin_name); ?></a> | <a style="text-decoration: none;" href="<?php echo $facebook; ?>" target="_blank"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo $twitter; ?>" target="_blank"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo $youtube; ?>" target="_blank"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', $slug); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo $donate; ?>')"><?php _e('Donate to this plugin &#187;'); ?></button>
		</div>

		<?php

	}

	/* ==================================================
	 * Post Column
	 * @param	array	$cols
	 * @return	array	$cols
	 * @since	4.00
	 */
	public function ptuc_column( $cols ) {
		$postdatetimechange_settings = get_option($this->wp_options_name());
		if ($postdatetimechange_settings['method'] === 'modified') {
			$cols["post_date_and_time"] = __('Date and time').' '.__('Last updated');
		} else {
			$cols["post_date_and_time"] = __('Date and time').' '.__('Posted', 'post-date-time-change');
		}
		return $cols;
	}

	/* ==================================================
	 * Post Column
	 * @param	string	$column_name
	 * @param	int		$id
	 * @since	4.00
	 */
	public function ptuc_value( $column_name, $id ) {
		if ( $column_name == "post_date_and_time" ) {
			echo $this->input_date_time($id);
		}
	}

	/* ==================================================
	 * Media Library Column
	 * @param	array	$cols
	 * @return	array	$cols
	 * @since	4.00
	 */
	public function muc_column( $cols ) {

		global $pagenow;
		if($pagenow == 'upload.php') {
			$exif_date_time_columun = NULL;
			if( $this->is_add_on_activate['exif'] ) {
				$postdatetimechangeexif = new PostDateTimeChangeExif();
				$exif_date_time_columun = $postdatetimechangeexif->media_column_html();
			}
			$postdatetimechange_settings = get_option($this->wp_options_name());
			if ($postdatetimechange_settings['method'] === 'modified') {
				$cols["media_date_and_time"] = __('Date and time').' '.__('Last updated').$exif_date_time_columun;
			} else {
				$cols["media_date_and_time"] = __('Date and time').' '.__('Posted', 'post-date-time-change').$exif_date_time_columun;
			}
		}

		return $cols;

	}

	/* ==================================================
	 * Media Library Column
	 * @param	string	$column_name
	 * @param	int		$id
	 * @since	4.00
	 */
	public function muc_value( $column_name, $id ) {
		if ( $column_name == "media_date_and_time" ) {
			echo $this->input_date_time($id);
		}
	}

	/* ==================================================
	 * Input Date Time
	 * @param	int		$id
	 * @return	string	$input_html
	 * @since	4.00
	 */
	private function input_date_time($id) {

		$postdatetimechange_settings = get_option($this->wp_options_name());

		global $wpdb;
		$attachments = $wpdb->get_results($wpdb->prepare("
					SELECT	post_date, post_modified
					FROM	$wpdb->posts
					WHERE	ID = %d
					",$id
					),ARRAY_A);

		if ($postdatetimechange_settings['method'] === 'modified') {
			$date = $attachments[0]['post_modified'];
		} else {
			$date = $attachments[0]['post_date'];
		}

		if( $this->is_add_on_activate['exif'] ) {
			$postdatetimechangeexif = new PostDateTimeChangeExif();
			$date = $postdatetimechangeexif->exif_date($id, $date);
		}

		$newdate = substr( $date , 0 , strlen($date)-3 );

		$input_html = '<input type="text" id="datetimepicker-postdatetimechange'.$id.'" name="postdatetimechange_datetime['.$id.']" value="'.$newdate.'" style="width: 100%;" />';

		return $input_html;

	}

	/* ==================================================
	 * Bulk Action Select
	 * @since	4.00
	 */
	public function custom_bulk_admin_footer() {
		global $pagenow;
		if($pagenow == 'upload.php' || $pagenow == 'edit.php') {

			$now_date_time = date_i18n("Y-m-d H:i");
			$html = '<input type="text" id="datetimepicker-postdatetimechange0" name="bulk_postdatetimechange_datetime" value="'.$now_date_time.'" style="width: 100%;" />';

			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('changedatetime').text('<?php _e('Change the date and time', 'post-date-time-change')?>').appendTo("select[name='action']");
					jQuery('<option>').val('changedatetime').text('<?php _e('Change the date and time', 'post-date-time-change')?>').appendTo("select[name='action2']");
				});
				jQuery('<?php echo $html; ?>').appendTo("#post_date_and_time");
				jQuery('<?php echo $html; ?>').appendTo("#media_date_and_time");
			</script>
			<?php
		}
	}

	/* ==================================================
	 * Bulk Action for post & page
	 * @since	4.00
	 */
	public function custom_bulk_action() {

		// get the action
		$wp_list_table = _get_list_table('WP_Posts_List_Table');  
		$action = $wp_list_table->current_action();

		$allowed_actions = array("changedatetime");
		if(!in_array($action, $allowed_actions)) return;

		check_admin_referer('bulk-posts');

		if(isset($_REQUEST['post'])) {
			$post_ids = array_map('intval', $_REQUEST['post']);
		}

		if(empty($post_ids)) return;

		$sendback = remove_query_arg( array('datetimechanged', 'message', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
		if ( ! $sendback )
		$sendback = admin_url( "edit.php?post_type=$post_type" );

		$pagenum = $wp_list_table->get_pagenum();
		$sendback = add_query_arg( 'paged', $pagenum, $sendback );

		switch($action) {
			case 'changedatetime':
				$postdatetimechange_datetimes = $this->sanitize_array($_REQUEST['postdatetimechange_datetime']);
				$sendback = $this->db_update( $post_ids, $postdatetimechange_datetimes, $sendback );
				break;
			default: return;
		}

		$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
		wp_redirect($sendback);
		exit();

	}

	/* ==================================================
	 * Bulk Action for media
	 * @since	4.00
	 */
	public function custom_bulk_action_media() {

		if ( !isset( $_REQUEST['detached'] ) ) {

			// get the action
			$wp_list_table = _get_list_table('WP_Media_List_Table');  
			$action = $wp_list_table->current_action();

			$allowed_actions = array("changedatetime");
			if(!in_array($action, $allowed_actions)) return;

			check_admin_referer('bulk-media');

			if(isset($_REQUEST['media'])) {
				$post_ids = array_map('intval', $_REQUEST['media']);
			}

			if(empty($post_ids)) return;

			$sendback = remove_query_arg( array('datetimechanged', 'message', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
			if ( ! $sendback )
			$sendback = admin_url( "upload.php?post_type=$post_type" );

			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			switch($action) {
				case 'changedatetime':
					$postdatetimechange_datetimes = $this->sanitize_array($_REQUEST['postdatetimechange_datetime']);
					$sendback = $this->db_update( $post_ids, $postdatetimechange_datetimes, $sendback );
					break;
				default: return;
			}

			$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
			wp_redirect($sendback);
			exit();

		}

	}

	/* ==================================================
	 * DB Update
	 * @param	array	$post_ids
	 * @param	array	$postdatetimechange_datetimes
	 * @param	string	$sendback
	 * @return	string	$sendback
	 * @since	5.00
	 */
	private function db_update( $post_ids, $postdatetimechange_datetimes, $sendback ) {

		$postdatetimechange_settings = get_option($this->wp_options_name());
		$write = $postdatetimechange_settings['write'];

		global $wpdb;
		$messages = array();
		$datetimechanged = 0;
		foreach( $post_ids as $post_id ) {
			$postdate = $postdatetimechange_datetimes[$post_id].':00';
			$postdategmt = get_gmt_from_date($postdate);
			// Change DB Attachement post
			$message = 'success';
			if ($write === 'date') {
				$update_array = array(
								'post_date' => $postdate,
								'post_date_gmt' => $postdategmt
							);
			}else if ($write === 'modified') {
				$update_array = array(
								'post_modified' => $postdate,
								'post_modified_gmt' => $postdategmt
							);
			} else {
				$update_array = array(
								'post_date' => $postdate,
								'post_date_gmt' => $postdategmt,
								'post_modified' => $postdate,
								'post_modified_gmt' => $postdategmt
							);
			}
			$id_array= array('ID'=> $post_id);
			$wpdb->show_errors();
			$wpdb->update( $wpdb->posts, $update_array, $id_array, array('%s'), array('%d') );
			if ( $wpdb->last_error !== '' ) {
				$message = $wpdb->print_error();
			}
			unset($update_array, $id_array);
			if ( $message ) {
				$messages[$datetimechanged] = $message;
				$datetimechanged++;
			}
		}
		$sendback = add_query_arg( array('datetimechanged' => $datetimechanged, 'ids' => join(',', $post_ids), 'message' => join(',',  $messages)), $sendback );

		return $sendback;

	}

	/* ==================================================
	 * Bulk Action Message
	 * @since	4.00
	 */
	public function custom_bulk_admin_notices() {

	    global $post_type, $pagenow;

		if ( ($pagenow == 'upload.php' || $pagenow == 'edit.php') && isset($_REQUEST['datetimechanged']) && intval($_REQUEST['datetimechanged']) > 0 && isset($_REQUEST['message']) ) {
			$messages = explode(',',sanitize_text_field(urldecode($_REQUEST['message'])));
			$success_count = 0;
			foreach ( $messages as $message ) {
				if ( $message === 'success' ) {
					++$success_count;
				} else {
					echo '<div class="notice notice-error is-dismissible"><ul><li>'.$message.'</li></ul></div>';
				}
			}
			if ( $success_count > 0 ) {
				echo '<div class="notice notice-success is-dismissible"><ul><li>'.sprintf(__('%1$d updated.', 'post-date-time-change'),$success_count).'</li></ul></div>';
			}
		}

	}

	/* ==================================================
	* Sanitize Array
	* @param	array	$a
	* @return	string	$_a
	* @since	4.07
	*/
	private function sanitize_array($a) {

		$_a = array();
		foreach($a as $key=>$value) {
			if ( is_array($value) ) {
				$_a[$key] = $this->sanitize_array($value);
			} else {
				$_a[$key] = htmlspecialchars($value);
			}
		}

		return $_a;

	}

	/* ==================================================
	 * @param	none
	 * @return	html
	 * @since	1.00
	 */
	private function addons_page() {

		$plugin_dir = untrailingslashit(plugin_dir_path( __DIR__ ));
		$slugs = explode('/', $plugin_dir);
		$slug = end($slugs);
		$plugin_base_dir = untrailingslashit(str_replace($slug, '', $plugin_dir));

		?>
		<h3><?php _e('Add On', 'post-date-time-change'); ?></h3>

		<div style="width: 300px; height: 100%; margin: 10px; padding: 10px; border: #CCC 2px solid;">
			<h4>Post Date Time Change Add On Exif</h4>
			<div style="margin: 5px; padding: 5px;"><?php _e('This Add-on adds that allows you to use the Exif shooting date and time by "Post Date Time Change", if the media has Exif shooting date and time.', 'post-date-time-change'); ?></div>
			<p>
			<?php
			if ( is_dir($plugin_base_dir.'/post-date-time-change-add-on-exif') ) {
				?><div style="margin: 5px; padding: 5px;"><strong><?php
				_e('Installed', 'post-date-time-change');?> & <?php
				if ( $this->is_add_on_activate['exif'] ) {
					_e('Activated', 'post-date-time-change');
				} else {
					_e('Deactivated', 'post-date-time-change');
				}
				?></strong></div><?php
			} else {
				?>
				<div>
				<a href="<?php _e('https://shop.riverforest-wp.info/post-date-time-change-add-on-exif/', 'post-date-time-change'); ?>" target="_blank" class="page-title-action"><?php _e('BUY', 'post-date-time-change'); ?></a>
				</div>
				<?php
			}
			?>
		</div>

		<div style="width: 300px; height: 100%; margin: 10px; padding: 10px; border: #CCC 2px solid;">
			<h4>Post Date Time Change Add On Custom Post</h4>
			<div style="margin: 5px; padding: 5px;"><?php _e('This Add-on adds a custom post type that allows you to change the date and time with "Post Date Time Change".', 'post-date-time-change'); ?></div>
			<div style="margin: 5px; padding: 5px;">
			<li><?php _e('Example', 'post-date-time-change'); ?></li>
			<table>
			<tr style="background: #fff;"><td><strong>Plugins</strong></td><td><strong>Menu</strong></td><td><strong align="center">Custom Post Type</strong></td></tr>
			<tr style="background: #eee;"><td rowspan="2">WooCommerce</td><td><?php _e('Orders', 'post-date-time-change'); ?></td><td align="center">shop_order</td></tr>
			<tr style="background: #fff;"><td><?php _e('All Products', 'post-date-time-change'); ?></td><td align="center">product</td></tr>
			<tr style="background: #eee;"><td rowspan="3">bbPress</td><td><?php _e('All Forums', 'post-date-time-change'); ?></td><td align="center">forum</td></tr>
			<tr style="background: #fff;"><td><?php _e('All Topics', 'post-date-time-change'); ?></td><td align="center">topic</td></tr>
			<tr style="background: #eee;"><td><?php _e('All Replies', 'post-date-time-change'); ?></td><td align="center">reply</td></tr>
			</table>
			</div>
			<p>
			<?php
			if ( is_dir($plugin_base_dir.'/post-date-time-change-add-on-custompost') ) {
				?><div style="margin: 5px; padding: 5px;"><strong><?php
				_e('Installed', 'post-date-time-change');?> & <?php
				if ( $this->is_add_on_activate['custompost'] ) {
					_e('Activated', 'post-date-time-change');
				} else {
					_e('Deactivated', 'post-date-time-change');
				}
				?></strong></div><?php
			} else {
				?>
				<div>
				<a href="<?php _e('https://shop.riverforest-wp.info/post-date-time-change-add-on-custompost/', 'post-date-time-change'); ?>" target="_blank" class="page-title-action"><?php _e('BUY', 'post-date-time-change'); ?></a>
				</div>
				<?php
			}
			?>
		</div>
		<?php

	}

	/* ==================================================
	 * @param	none
	 * @return	string	$this->wp_options_name()
	 * @since	5.03
	 */
	private function wp_options_name() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include_once(ABSPATH . 'wp-includes/pluggable.php');
		}
		return 'postdatetimechange_settings'.'_'.get_current_user_id();
	}

}

?>