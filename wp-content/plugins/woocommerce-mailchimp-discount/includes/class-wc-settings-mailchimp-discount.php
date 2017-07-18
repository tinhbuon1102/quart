<?php
/**
 * WooCommerce Signup Discount Settings
 *
 * @author 		Magnigenie
 * @category 	Admin
 * @version     1.8.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (  class_exists( 'WC_Settings_Page' ) ) :

/**
 * WC_Settings_Accounts
 */
class WC_Settings_Mailchimp_Discount extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'mailchimp_discount';
		$this->label = __( 'Mailchimp Discount', 'wcmd' );
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );

		if( isset($_GET['tab']) && $_GET['tab'] == $this->id )
			add_action( 'admin_footer', array( $this, 'wcmd_add_scripts') );

		add_action( 'woocommerce_admin_field_wcmd_wpeditor', array( $this, 'wcmd_display_editor' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option_wcmd_email', array( $this, 'wcmd_save_editor_val' ), 10, 3 );
		add_filter( 'woocommerce_admin_settings_sanitize_option_wcmd_popup_text', array( $this, 'wcmd_save_editor_val' ), 10, 3 );

		add_action( 'woocommerce_admin_field_wcmd_uploader', array( $this, 'wcmd_display_uploader' ) );
		add_action( 'woocommerce_admin_field_search_products', array( $this, 'wcmd_search_products' ) );
		add_action( 'woocommerce_admin_field_exclude_products', array( $this, 'wcmd_exclude_products' ) );
	}


	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		$wcmd_products = get_option( 'wcmd_products' );
		$products = array();
		if ( is_array( $wcmd_products ) ) {
			foreach ( $wcmd_products as $product_id ) {
				$product = wc_get_product( $product_id );
				$products[$product_id] = wp_kses_post( $product->get_formatted_name() );
			}
		}
		$wcmd_exclude_products = get_option( 'wcmd_exclude_products' );
		$products_exclude = array();
		if ( is_array( $wcmd_exclude_products ) ) {
			foreach ( $wcmd_exclude_products as $product_id ) {
				$product = wc_get_product( $product_id );
				$products_exclude[$product_id] = wp_kses_post( $product->get_formatted_name() );
			}
		}
		$categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		$cats = array();

		if ( function_exists('icl_object_id') )
			$wcmd_email_body = __( 'Email content will be sent to the users when they register on the site. <a href="#" class="wcmd-help">Click here</a> to see the list of variables you can use for <b>Email body and Email subject.</b></br>It Looks like you are using WPML, you can create your preferred language message by <a href="#" class="wcmd-help">Click here</a>', 'wcmd' );
		else
			$wcmd_email_body = __( 'Email content will be sent to the users when they register on the site. <a href="#" class="wcmd-help">Click here</a> to see the list of variables you can use for <b>Email body and Email subject.</b>', 'wcmd' );

		if ( $categories ) foreach ( $categories as $cat ) $cats[$cat->term_id] = esc_html( $cat->name );
		return apply_filters( 'woocommerce_' . $this->id . '_settings', array(

			array(	'title' => __( 'Mailchimp Discount Settings', 'wcmd' ), 'type' => 'title','desc' => '', 'id' => 'signup_discount_title' ),
      	array(
					'title' 			=> __( 'Enable', 'wcmd' ),
					'desc' 			=> __( 'Enable mailchimp discount plugin.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_enabled',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Disable Discount', 'wcmd' ),
					'desc' 			=> __( 'Disable discount for mailchimp sign ups and use it for normal mailchimp signups.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_disable_discount',
					'default' 			=> 'no'
				),
				array(
					'title' 	=> __( "Mailchimp API Key", 'wcmd' ),
					'type' 		=> 'text',
					'desc' 		  => __( 'Enter your mailchimp api key. To find your API Key <a href="http://kb.mailchimp.com/accounts/management/about-api-keys" target="_blank">click here</a>', 'wcmd' ),
					'id'		=> 'wcmd_api_key',
					'default' 	=> '',
					'custom_attributes' => array( 'required' => 'required' )
				),
				array(
					'title' 	=> __( "Mailchimp list id", 'wcmd' ),
					'type' 		=> 'text',
					'desc' 		  => __( 'Enter the mailchimp list id you want to use for subscription. To find your List id <a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank">click here</a>', 'wcmd' ),
					'id'		=> 'wcmd_list_id',
					'default' 	=> '',
					'custom_attributes' => array( 'required' => 'required' )
				),
        array(
					'title' 			=> __( 'Disply on home only', 'wcmd' ),
					'desc' 			=> __( 'Display the popup only when the user visits the homepage.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_home',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Add Signup Source', 'wcmd' ),
					'desc' 			=> __( 'Add SOURCE merge tag for each signup to track the signups', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_source',
					'default' 			=> 'yes'
				),
				array(
					'title' 			=> __( 'Signup Source', 'wcmd' ),
					'desc' 			=> __( 'This will be the signup source which can be shown in the mailchimp admin to check from where the user has been made signup', 'wcmd' ),
					'type' 				=> 'text',
					'id'				=> 'wcmd_source_link',
					'default' 			=> 'WooCommerce Mailchimp Discount',
					'css'		=> 'width:350px',
				),
        array(
					'title' 			=> __( 'Double optin', 'wcmd' ),
					'desc' 			=> __( 'In order to use double optin feature you need add a webhook with <strong>callback url</strong> as <strong>'. site_url('?mc_discount=1') . '</strong>. If you want to know how you can setup the webhook then <a href="http://magnigenie.com/how-to-create-mailchimp-webhooks/" target="_blank">follow this link</a>.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_double_optin',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Send welcome', 'wcmd' ),
					'desc' 			=> __( 'Send welcome message to subscribed users.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_welcome',
					'default' 			=> 'yes'
				),
        array(
					'title' 			=> __( 'Restrict Email', 'wcmd' ),
					'desc' 			=> __( 'Allow discount if the purchase is made for the same email id user registered on mailchimp.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_restrict',
					'default' 			=> 'yes'
				),
        array(
					'title' 			=> __( 'Require user to be logged in to apply coupon', 'wcmd' ),
					'desc' 			=> __( 'If you are using restrict email then you can use this option to require users to be logged in to apply coupon.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_loggedin',
					'default' 			=> 'yes'
				),
        array(
					'title' 	=> __( 'Display Fields', 'wcmd' ),
					'desc' 		=> '',
					'type' 		=> 'radio',
					'options'	=>	array( 'email' => 'Only Email', 'email_name' => 'First name and Email', 'email_name_all' => 'First name,Last name and Email' ),
					'id'		=> 'wcmd_fields',
					'default' 	=> 'email'
				),
        array(
					'title' 	=> __( 'Test E-Mail', 'wcmd' ),
					'desc' 		=> __( 'This email would be excluded from the internal tracking so that you can unsubscribe on mailchimp and test multiple times.', 'wcmd' ),
					'type' 		=> 'text',
					'id'		=> 'wcmd_test_mail',
					'default' 	=> get_option( 'admin_email' ),
					'css'		=> 'width:300px',
				),
				array(
					'title' 	=> __( "Discount Type", 'wcmd' ),
					'type' 		=> 'select',
					'id'		=> 'wcmd_dis_type',
					'options' 	=> wc_get_coupon_types(),
					'default' 	=> 'percent'
				),
				array(
					'title' 	  => __( 'Coupon prefix', 'wcmd' ),
					'desc' 		  => __( 'Enter a coupon prefix which would be added before the actual generated coupon code. Leave empty for no prefix.', 'wcmd' ),
					'id' 		  => 'wcmd_prefix',
					'type' 		  => 'text',
					'default'	  => '',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Coupon code length', 'wcmd' ),
					'desc' 		  => __( 'Enter a length for the coupon code. Note: the prefix is not counted in coupon code length.', 'wcmd' ),
					'id' 		  => 'wcmd_code_length',
					'type' 		  => 'number',
					'default'	  => '12',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Discount Amount', 'wcmd' ),
					'desc' 		  => __( 'Enter a coupon discount amount', 'wcmd' ),
					'id' 		  => 'wcmd_amount',
					'type' 		  => 'text',
					'default'	  => '10',
					'desc_tip'	  =>  true
				),
        array(
					'title' 			=> __( 'Allow free shipping', 'wcmd' ),
					'desc' 			=> __( 'Check this box if the coupon grants free shipping. The <a href="'.admin_url('admin.php?page=wc-settings&amp;tab=shipping&amp;section=WC_Shipping_Free_Shipping').'">free shipping method</a> must be enabled with the "must use coupon" setting.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_shipping',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Exclude on sale items', 'wcmd' ),
					'desc' 			=> __( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_sale',
					'default' 			=> 'no'
				),
				array(
					'title' 	  => __( 'Products', 'wc_mailchimp_discount' ),
					'desc' 		  => __( 'Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted.', 'wc_mailchimp_discount' ),
					'id' 		  => 'wcmd_products',
					'type'    => 'search_products',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Exclude products', 'wc_mailchimp_discount' ),
					'desc' 		  => __( 'Products which must not be in the cart to use this coupon or, for "Product Discounts", which products are not discounted.', 'wc_mailchimp_discount' ),
					'id' 		  => 'wcmd_exclude_products',
					'type'    => 'exclude_products',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Categories', 'wcmd' ),
					'desc' 		  => __( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'wcmd' ),
					'id' 		  => 'wcmd_categories',
					'type' 		  => 'multiselect',
					'class'		  => 'chosen_select',
					'css'		  => 'width:300px',
					'default'	  => '',
					'options'     => $cats,
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Exclude Categories', 'wcmd' ),
					'desc' 		  => __( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'wcmd' ),
					'id' 		  => 'wcmd_exclude_categories',
					'type' 		  => 'multiselect',
					'class'		  => 'chosen_select',
					'css'		  => 'width:300px',
					'default'	  => '',
					'options'     => $cats,
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Coupon Validity (in days)', 'wcmd' ),
					'desc' 		  => __( 'Enter number of days the coupon will active from the date of registration of the user. Leave blank for no limit.', 'wcmd' ),
					'id' 		  => 'wcmd_days',
					'type' 		  => 'number',
					'css'		  => 'width:100px',
					'default'	  => '',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Coupon expiry date format', 'wcmd' ),
					'desc' 		  => __( 'Enter the date format for the coupon expiry date which would be mailed to the user. <a href="http://php.net/manual/en/function.date.php" target="_blank">Click here</a> to know about the available types', 'wcmd' ),
					'id' 		  => 'wcmd_date_format',
					'type' 		  => 'text',
					'css'		  => 'width:100px',
					'default'	  => 'jS F Y',
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Minimum Purchase', 'wcmd' ),
					'desc' 		  => __( 'Minimum purchase subtotal in order to be able to use the coupon. Leave blank for no limit', 'wcmd' ),
					'id' 		  => 'wcmd_min_purchase',
					'type' 		  => 'text',
					'default'	  => '',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Maximum Purchase', 'wcmd' ),
					'desc' 		  => __( 'Maximum purchase subtotal in order to be able to use the coupon. Leave blank for no limit', 'wcmd' ),
					'id' 		  => 'wcmd_max_purchase',
					'type' 		  => 'text',
					'default'	  => '',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Email From Name', 'wcmd' ),
					'desc' 		  => __( 'Enter the name which will appear on the emails.', 'wcmd' ),
					'id' 		  => 'wcmd_email_name',
					'type' 		  => 'text',
					'css'		  => 'width:300px',
					'default'	  => get_bloginfo('name'),
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'From Email', 'wcmd' ),
					'desc' 		  => __( 'Enter the email from which the emails will be sent.', 'wcmd' ),
					'id' 		  => 'wcmd_email_id',
					'type' 		  => 'text',
					'css'		  => 'width:300px',
					'default'	  => get_bloginfo('admin_email'),
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Email Subject', 'wcmd' ),
					'desc' 		  => __( 'This will be email subject for the emails that will be sent to the users.', 'wcmd' ),
					'id' 		  => 'wcmd_email_sub',
					'type' 		  => 'text',
					'css'		  => 'width:100%',
					'default'	  => 'Congrats, you got a discount for signing up to our Newsletter',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Email Body', 'wcmd' ),
					'desc' 		  => $wcmd_email_body,
					'id' 		  => 'wcmd_email',
					'type' 		  => 'wcmd_wpeditor',
					'default'	  => '<p>Hi There,</p><p>Thanks for signing up for our Newsletter. As a registration bonus we present you with a 10% of discount on all your orders. The coupon code to redeem the discount is <h3>{COUPONCODE}</h3></p><p>The coupon will expire on {COUPONEXPIRY} so make sure to get the benefits while you still have time.</p><p>Regards</p>',
					'desc_tip'	  =>  true
				),
        array(
					'title' 			=> __( 'Disable popup', 'wcmd' ),
					'desc' 			=> __( 'Disable popup and instead you can use [wcmd] shortcode. <a href="#" class="wcmd-help">Click here</a>  to see the details', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_disable_popup',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Button/Link trigger for popup', 'wcmd' ),
					'desc' 			=> __( 'Use button/link click to trigger the popup. <i>When using this feature make sure your button/link has a class attribute of wcmd-trigger.</i> Example:<code>&lt;a href="#" class="wcmd-trigger"&gt;Open Popup&lt;/a&gt;</code>', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_btn_trigger',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Open popup only on button/link click', 'wcmd' ),
					'desc' 			=> __( 'Enable this option if you want the popup to appear only on button/link click. This will disable automatic popup open feature.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_only_btn',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Exit intent', 'wcmd' ),
					'desc' 			=> __( 'Display popup based on exit intent', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_exit_intent',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Close popup on overlay click', 'wcmd' ),
					'desc' 			=> __( 'Close the popup when people click outside the popup?', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_overlay_click',
					'default' 			=> 'no'
				),
				array(
					'title' 	  => __( 'Popup cookie length(days)', 'wcmd' ),
					'id' 		  => 'wcmd_cookie_length',
					'type' 		  => 'number',
					'desc' 		  => __( 'Enter the value for number of days the site should remember the visitor.', 'wcmd' ),
					'default'	  => '30',
					'css' 		  => 'width: 60px;',
					'custom_attributes' => array( 'min' => '1', 'step' => '1' ),
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Popup Background Image', 'wcmd' ),
					'id' 		  => 'wcmd_pop_bg',
					'type' 		  => 'wcmd_uploader',
					'default'	  => '',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Popup Background Color', 'wcmd' ),
					'id' 		  => 'wcmd_pop_bgcolor',
					'type' 		  => 'color',
					'default'	  => '#2b2f3e',
					'css' 		  => 'width: 125px;',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Popup close button Color', 'wcmd' ),
					'id' 		  => 'wcmd_close_color',
					'type' 		  => 'color',
					'default'	  => '#fff',
					'css' 		  => 'width: 125px;',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Popup Overlay Color', 'wcmd' ),
					'id' 		  => 'wcmd_pop_overlay',
					'type' 		  => 'color',
					'default'	  => '#2e3865',
					'css' 		  => 'width: 125px;',
					'desc_tip'	  =>  true
				),
				array(
					'title' 	  => __( 'Popup header text color', 'wcmd' ),
					'id' 		  => 'wcmd_header_color',
					'type' 		  => 'color',
					'default'	  => '#000000',
					'css' 		  => 'width: 125px;',
					'desc_tip'	  =>  true
				),
                array(
					'title'   => __( 'Popup overlay opacity', 'wcmd' ),
					'desc' 	  => __( 'Enter a value for the opacity value of the popup background.', 'wcmd' ),
					'type' 	  => 'number',
					'id'	  => 'wcmd_overlay_opacity',
					'css' 	  => 'width: 125px;',
					'default' => '0.8',
					'custom_attributes' => array( 'max' => '1', 'min' => '0.2', 'step' => '0.1' )
				),
				array(
					'title' 	  => __( 'Popup Height (px)', 'wcmd' ),
					'id' 		  => 'wcmd_popup_height',
					'type' 		  => 'number',
					'css' 		  => 'width: 125px;',
					'default'	  => '0',
					'desc' 		  => __( 'Enter a height for the popup. Put 0 for auto height', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Popup Width (px)', 'wcmd' ),
					'id' 		  => 'wcmd_popup_width',
					'type' 		  => 'number',
					'css' 		  => 'width: 125px;',
					'default'	  => '540',
					'desc' 		  => __( 'Enter a width for the popup. Put 0 for auto width', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Popup Content Top Position (px)', 'wcmd' ),
					'id' 		  => 'wcmd_content_top',
					'type' 		  => 'number',
					'css'		  => 'width: 125px;',
					'default'	  => '0',
					'desc' 		  => __( 'Enter number of pixel for the popup content from the top. ', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Popup Content Left Position (px)', 'wcmd' ),
					'id' 		  => 'wcmd_content_left',
					'type' 		  => 'number',
					'css'		  => 'width: 125px;',
					'default'	  => '0',
					'desc' 		  => __( 'Enter number of pixel for the popup content from the left. ', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	=> __( "Popup Animation effect", 'wcmd' ),
					'type' 		=> 'select',
					'id'		=> 'wcmd_popup_animation',
					'options' 	=> array(
						'mfp-with-fade' => __( 'Fade In', 'wcmd' ),
						'mfp-tada' => __( 'Tada', 'wcmd' ),
						'mfp-shake' => __( 'Shake', 'wcmd' ),
						'mfp-zoom-out' => __( 'Zoom Out', 'wcmd' ),
						'mfp-zoom-in' => __( 'Zoom In', 'wcmd' ),
						'mfp-3d-unfold' => __( '3D Unfold', 'wcmd' ),
						'mfp-3d-sign' => __( '3D Sign', 'wcmd' ),
						'mfp-move-from-top' => __( 'Move From Top', 'wcmd' ),
						'mfp-move-horizontal' => __( 'Move Horizontal', 'wcmd' ),
						'mfp-slide-right' => __( 'Slide Right', 'wcmd' ),
						'mfp-newspaper' => __( 'Newspaper', 'wcmd' ),
          			),
					'default' 	=> 'mfp-with-fade'
				),
        array(
					'title' 			=> __( 'Close hinge effect', 'wcmd' ),
					'desc' 			=> __( 'Enable hinge effect when closing the modal.', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_hinge',
					'default' 			=> 'no'
				),
        array(
					'title' 			=> __( 'Disable popup on mobile', 'wcmd' ),
					'desc' 			=> __( 'Disable popup on mobile devices', 'wcmd' ),
					'type' 				=> 'checkbox',
					'id'				=> 'wcmd_disable_mobile_popup',
					'default' 			=> 'no'
				),
				array(
					'title' 	  => __( 'Popup header text', 'wcmd' ),
					'id' 		  => 'wcmd_pop_header',
					'type' 		  => 'textarea',
					'css'		  => 'width: 350px;',
					'default'	  => '',
					'desc' 		  => __( 'Enter the text that would appear on the top of the popup.', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Popup Text', 'wcmd' ),
					'desc' 		  => __( 'Popup text will be shown when a new user visits the site. Use <b>{WCMD_FORM}</b> to place the form inside the popup.', 'wcmd' ),
					'id' 		  => 'wcmd_popup_text',
					'type' 		  => 'wcmd_wpeditor',
					'default'	  => '<p style="text-align: center;"><span style="color: #33d5aa;">ENTER YOUR EMAIL AND GET</span></p><h1 style="text-align: center; margin: 0; font-size: 55px;"><span style="color: #cccccc;">10% OFF</span></h1>{WCMD_FORM}<p style="text-align: center;"><span style="color: #999999;">Be the first to know about our exclusive items, New catalogs and special promotions.</span></p>',
					'desc_tip'	  =>  true
				),

				array(
					'title' 	  => __( 'Display popup after(seconds)', 'wcmd' ),
					'id' 		  => 'wcmd_dis_seconds',
					'type' 		  => 'number',
					'css' 		  => 'width: 45px;',
					'default'	  => '3',
					'desc' 		  => __( 'Enter a value for the number of seconds after which the popup will be displayed.', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Automatically close popup after(seconds)', 'wcmd' ),
					'id' 		  => 'wcmd_close_seconds',
					'type' 		  => 'number',
					'css' 		  => 'width: 45px;',
					'default'	  => '0',
					'desc' 		  => __( 'Enter a value if you want to close the popup automatically when a user successfully subscribes to your mailchimp list. Enter 0 if you don\'t want to close the popup automatically', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array(
					'title' 	  => __( 'Submit button text', 'wcmd' ),
					'desc' 		  => '',
					'id' 		  => 'wcmd_btn_text',
					'type' 		  => 'text',
					'default'	  => __( 'SUBSCRIBE', 'wcmd' ),
				),
				array(
					'title' 	  => __( 'Submit button color', 'wcmd' ),
					'desc' 		  => '',
					'id' 		  => 'wcmd_btn_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#33d5aa',
				),
				array(
					'title' 	  => __( 'Submit button hover color', 'wcmd' ),
					'desc' 		  => '',
					'id' 		  => 'wcmd_btn_hover',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#21b990',
				),
				array(
					'title' 	  => __( 'Submit button text color', 'wcmd' ),
					'desc' 		  => '',
					'id' 		  => 'wcmd_btn_txt_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#2b2f3e',
				),
				array(
					'title' 	  => __( 'Success message text color', 'wcmd' ),
					'desc' 		  => 'This will be the text color for the success message',
					'id' 		  => 'wcmd_success_txt_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#21b990',
				),
				array(
					'title' 	  => __( 'Success message background color', 'wcmd' ),
					'desc' 		  => 'This will be the background color for the success message',
					'id' 		  => 'wcmd_success_bg_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#FFFFFF',
				),				
				array(
					'title' 	  => __( 'Error message text color', 'wcmd' ),
					'desc' 		  => 'This will be the text color for the error message',
					'id' 		  => 'wcmd_error_txt_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#de0b0b',
				),
				array(
					'title' 	  => __( 'Error message background color', 'wcmd' ),
					'desc' 		  => 'This will be the background color for the error message',
					'id' 		  => 'wcmd_error_bg_color',
					'type' 		  => 'color',
					'css' 		  => 'width: 125px;',
					'default'	  => '#FFFFFF',
				),												
				array(
					'title' 	  => __( 'Form width (in px)', 'wcmd' ),
					'desc' 		  => __( 'Enter the subscription form width. Enter 0 for auto width.', 'wcmd' ),
					'id' 		  => 'wcmd_form_width',
					'type' 		  => 'number',
					'css' 		  => 'width: 105px;',
					'default'	  => '500',
				),
				array(
					'title' 	  => __( 'Form alignment', 'wcmd' ),
					'desc' 		  => '',
					'id' 		  => 'wcmd_form_alignment',
					'type' 		  => 'select',
					'options'	  => array( 'left' => 'Left', 'right' => 'Right', 'none' => 'Center'),
					'default'	  => 'none',
				),
				array(
					'title' 	  => __( 'Success message', 'wcmd' ),
					'id' 		  => 'wcmd_success_msg',
					'type' 		  => 'textarea',
					'css' 		  => 'width: 350px;',
					'default'	  => __( 'Thank you for subscribing! Check your mail for coupon code!', 'wcmd' ),
					'desc' 		  => __( 'Enter success message which will appear when user successfully subscribes to your mailchimp list. Use {COUPONCODE} variable for the generated coupon code. Remember this variable would work only in single option', 'wcmd' ),
					'desc_tip'	  =>  false
				),
				array( 'type' => 'sectionend', 'id' => 'simple_wcmd_options'),

		)); // End pages settings
	}

	/**
	* Output wordpress editor for email body condent.
	*
	* @param array $value array of settings variables.
	* @return null displays the editor.
	*
	*/
	public function wcmd_display_editor( $value ) {
		$option_value = WC_Admin_Settings::get_option( $value['id'], $value['default'] ); ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<?php echo $value['desc']; ?>
				<?php wp_editor( $option_value, esc_attr( $value['id'] ) ); ?>
			</td>
		</tr>
	<?php
	}

	/**
	* Saves the content fpr wp_editor.
	*
	* @return null saves the value of the option.
	*
	*/
	public function wcmd_save_editor_val( $value, $option, $raw_value ) {
		update_option( $option['id'], $raw_value  );
	}

	/**
	* Output wordpress file uploader.
	*
	* @param array $value array of settings variables.
	* @return null displays the editor.
	*
	*/
	public function wcmd_display_uploader( $value ) {
		$option_value = WC_Admin_Settings::get_option( $value['id'], $value['default'] ); ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
				<div class="uploader">
					<input value="<?php echo $option_value; ?>" id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>" type="text" />
					<input id="wcmd_button" class="button" type="button" value="Upload" />
					<div class="wcmd_image">
						<?php if($option_value != '') {
							echo '<img src="'.$option_value.'" style="width: 100px;" alt="">';
							} ?>
					</div>
				</div>
			</td>
		</tr>
	<?php
	}


	/**
	* Product ids
	*/
	public function wcmd_search_products() {
		?>
		<tr valign="top" class="search-products">
			<th><?php _e( 'Products', 'woocommerce' ); ?></th>
			<td>
				<input type="hidden" class="wcmd wc-product-search" data-multiple="true" style="width: 50%;" name="wcmd_products" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="wcmd_ajax_products" data-selected="<?php
					$product_ids = array_filter( array_map( 'absint', explode( ',', get_option( 'wcmd_products' ) ) ) );
					$json_ids    = array();

					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
						}
					}
					echo esc_attr( json_encode( $json_ids ) );
				?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
			</td>
		</tr>

	<?php
	}

	/**
	* Exclude Product Ids
	*/
	public function wcmd_exclude_products() {
		?>
		<tr valign="top" class="search-products">
			<th><?php _e( 'Exclude Products', 'woocommerce' ); ?></th>
			<td>
				<input type="hidden" class="wcmd wc-product-search" data-multiple="true" style="width: 50%;" name="wcmd_exclude_products" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="wcmd_ajax_products" data-selected="<?php
					$product_ids = array_filter( array_map( 'absint', explode( ',', get_option( 'wcmd_exclude_products' ) ) ) );
					$json_ids    = array();

					foreach ( $product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
						}
					}

					echo esc_attr( json_encode( $json_ids ) );
				?>" value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
			</td>
		</tr>

	<?php
	}

	/**
	* Add the required js needed for the plugin to display the list of products using ajax.
	*
	* @return null outputs the scripts on the footer.
	*
	*/
	public function wcmd_add_scripts() {
	?>
		<script type="text/javascript">
			jQuery(function($){
			// Ajax product search box
			$( ':input.wcmd.wc-product-search' ).each( function() {
				var select2_args = {
					allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
					placeholder: $( this ).data( 'placeholder' ),
					minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
					escapeMarkup: function( m ) {
						return m;
					},
					ajax: {
						url:            '<?php echo admin_url('admin-ajax.php'); ?>',
						dataType:    'json',
						quietMillis: 250,
						data: function( term ) {
							return {
								term:     term,
								action:   'wcmd_ajax_products',
								security: '<?php echo wp_create_nonce( "wcmd-search-products" ); ?>',
								exclude:  $( this ).data( 'exclude' ),
								include:  $( this ).data( 'include' ),
								limit:    $( this ).data( 'limit' )
							};
						},
						results: function( data ) {
							var terms = [];
							if ( data ) {
								$.each( data, function( id, text ) {
									terms.push( { id: id, text: text } );
								});
							}
							return {
								results: terms
							};
						},
						cache: true
					}
				};

				if ( $( this ).data( 'multiple' ) === true ) {
					select2_args.multiple = true;
					select2_args.initSelection = function( element, callback ) {
						var data     = $.parseJSON( element.attr( 'data-selected' ) );
						var selected = [];

						$( element.val().split( ',' ) ).each( function( i, val ) {
							selected.push({
								id: val,
								text: data[ val ]
							});
						});
						return callback( selected );
					};
					select2_args.formatSelection = function( data ) {
						return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
					};
				} else {
					select2_args.multiple = false;
					select2_args.initSelection = function( element, callback ) {
						var data = {
							id: element.val(),
							text: element.attr( 'data-selected' )
						};
						return callback( data );
					};
				}

				//select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

				$( this ).select2( select2_args ).addClass( 'enhanced' );
			});


				jQuery('.wcmd-help').click(function(){
					jQuery('#contextual-help-link').click();
				});
				jQuery('#tab-panel-wcmd_help input').click(function(){
					jQuery(this).select();
				});

				// Image uploader js
				var _custom_media = true;

					jQuery('#wcmd_button').click(function(e) {
						_orig_send_attachment = wp.media.editor.send.attachment;
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = jQuery(this);
						var input_file = button.parent().find('input[type="text"]');
						_custom_media = true;
						wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								input_file.val(attachment.url);
								button.parent().find('.wcmd_image').html('<img src="'+attachment.url+'" width="100px;">');
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});

				jQuery('.add_media').on('click', function(){
					_custom_media = false;
				});
			});
		</script>
	<?php
	}
}
return new WC_Settings_Mailchimp_Discount();

endif;