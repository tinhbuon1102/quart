<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php

/*-----------------------------------------------------------------------------------*/
/* Start WooThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// Define the theme-specific key to be sent to PressTrends.
define( 'WOO_PRESSTRENDS_THEMEKEY', 'zdmv5lp26tfbp7jcwiw51ix9sj389e712' );

// WooFramework init
require_once ( get_template_directory() . '/functions/admin-init.php' );

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/

$includes = array(
				'includes/theme-options.php', 			// Options panel settings and custom settings
				'includes/theme-functions.php', 		// Custom theme functions
				'includes/theme-actions.php', 			// Theme actions & user defined hooks
				'includes/theme-comments.php', 			// Custom comments/pingback loop
				'includes/theme-js.php', 				// Load JavaScript via wp_enqueue_script
				'includes/sidebar-init.php', 			// Initialize widgetized areas
				'includes/theme-widgets.php',			// Theme widgets
				'includes/theme-install.php',			// Theme installation
				'includes/theme-woocommerce.php',		// WooCommerce options
				'includes/theme-plugin-integrations.php'	// Plugin integrations
				);

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'woo_includes', $includes );

foreach ( $includes as $i ) {
	locate_template( $i, true );
}

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/


add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 24;
  return $cols;
}


//管理バーを非表示
add_filter( 'show_admin_bar', '__return_false' );


add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
function wcs_woo_remove_reviews_tab($tabs) {
 unset($tabs['reviews']);
 return $tabs;
}


/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/

function wc_custom_shop_archive_title( $title ) {
    if ( is_shop() ) {
        return str_replace( __( 'Products', 'woocommerce' ), 'Products', $title );
    }

    return $title;
}
add_filter( 'wp_title', 'wc_custom_shop_archive_title' );
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_change_breadcrumb_home_text' );
function jk_change_breadcrumb_home_text( $defaults ) {
    // Change the breadcrumb home text from 'Home' to 'HOME'
	$defaults['home'] = 'HOME';
	return $defaults;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
 
function woo_custom_cart_button_text() {

 
        return __( 'ADD TO CART', 'woocommerce' );
 
}
//PCでのみ表示するコンテンツ
function if_is_pc($atts, $content = null )
{
$content = do_shortcode( $content);
    if(!wp_is_mobile())
        {
        return $content;
        }
}
add_shortcode('pc', 'if_is_pc');
//スマートフォン・タブレットでのみ表示するコンテンツ
function if_is_nopc($atts, $content = null )
{
$content = do_shortcode( $content);
    if(wp_is_mobile())
        {
        return $content;
        }
}
add_shortcode('nopc', 'if_is_nopc');
//////////////////////////////////////////WIDGET/////////////////////////////////////////////
// Creating the widget
	class wpb_widget extends WP_Widget {
	function __construct() {
	parent::__construct(
	// Base ID of your widget
	'wpb_widget',
	// Widget name will appear in UI
	__('WPBeginner Widget', 'wpb_widget_domain'),
	// Widget description
	array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'wpb_widget_domain' ), )
	);
	}
// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) 
	
	{
$taxonomies = array( 
    'product_cat'
);

$args = array(
   /* 'orderby'           => 'id', 
    'order'             => 'DESC',*/
    'hide_empty'        => true, 
    'number'            => '', 
    'fields'            => 'all', 
    'slug'              => '',
    'parent'            => 0,
    'hierarchical'      => true, 
    'child_of'          => 0,
    'childless'         => false
); 

$terms = get_terms($taxonomies, $args);
foreach ($terms as $term)
{
	$termiid=$term->term_id;
	?>
    <style type="text/css">
	.sssx::after {
    content: "+";
	font-size: 25px;
}
.cat-item
{
	list-style:none;
}
.subitem {
    font-size: 13px;
    list-style: outside none none;
    margin-left: -23px;
}
li.subitem a{color:#c9b8b5;}
	</style>
<script type="text/javascript">

function calltosee()
{
	jQuery('#wert').toggle();
}
jQuery('.dcft').click(function(){
	jQuery('#wert').toggle();
	});
	
jQuery('.dcftx').click(function(){
	jQuery('#wert').hide();
	jQuery('#wert').css( "display", "none" );
	jQuery('.sssx').removeClass('dcftx');
	jQuery('.sssx').addClass('dcft');
});
	
</script> 
    <li class="cat-item cat-item-<?php echo $termiid;?>" style="font-size:16px;">
    <a <?php if($termiid==14){?> href="javascript:void(0);" onclick="calltosee();" <?php } else{?>href="<?php echo get_term_link($term );?>"<?php } ?>>
	<?php echo $term->name;?></a><?php if($termiid==14){?><span class="sssx dcft"></span><?php }?>
<?php
$taxonomiesx = array('product_cat');									
$argsx = array(
    /*'orderby'           => 'id', 
    'order'             => 'DESC',*/
    'hide_empty'        => true, 
    'number'            => '', 
    'fields'            => 'all', 
    'slug'              => '',
    'parent'            => $termiid,
    'hierarchical'      => true, 
    'child_of'          => 0,
    'childless'         => false
); 

$termxs = get_terms($taxonomiesx, $argsx);
if (!empty($termxs))
{
				?>
                	<ul class="cute" <?php if($termiid==14){?>style="display:none;" id="wert"<?php }?>>
                    <?php 
					foreach($termxs as $termx)
					{
						$term=get_term_by('id',$term_childern,$taxonomy_name);
						echo '<li class="subitem"><a href="'.get_term_link($termx).'">'.$termx->name.'</a></li>';
$argsy = array(
   /* 'orderby'           => 'id', 
    'order'             => 'DESC',*/
    'hide_empty'        => true, 
    'number'            => '', 
    'fields'            => 'all', 
    'slug'              => '',
    'parent'            => $termx->term_id,
    'hierarchical'      => true, 
    'child_of'          => 0,
    'childless'         => false
); 
$termys = get_terms($taxonomiesx, $argsy);
if (!empty($termys))
{
?>
<ul class="cutesubb">
                    <?php 
					foreach($termys as $termy)
					{
						echo '<li class="subitem sub"><a href="'.get_term_link($termy).'">'.$termy->name.'</a></li>';

					}
					?>
</ul>
<?php
}
					} 
					?>	
                    </ul>
                    <?php } ?>
                    </li>

    <?php
	
}
	}
	
	// Widget Backend
	public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
	}
	else {
	$title = __( 'New title', 'wpb_widget_domain' );
	}
	// Widget admin form
	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<?php
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	return $instance;
	}
	} // Class wpb_widget ends here
// Register and load the widget
	function wpb_load_widget() {
	    register_widget( 'wpb_widget' );
	}
	add_action( 'widgets_init', 'wpb_load_widget' );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
register_nav_menu( 'footer-navi', 'フッターのナビゲーション' );
// Override theme default specification for product # per row
function loop_columns() {
return 3; // 3 products per row
}
add_filter('loop_shop_columns', 'loop_columns', 999);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
add_action ("woocommerce_after_shop_loop", "add_footer_menu_shop") ;
function add_footer_menu_shop(){
	$shopmenu = get_terms('product_cat', array('hide_empty' => 0, 'orderby' => 'id', 'order' => 'DESC',  'parent' =>0)); //, 'exclude' => '17,77'		
?>
<div class="primary">
<div class="mobile category-product-menu" 
<ul>
<?php foreach($shopmenu as $menu){ 
		$wsubargs = array(
		   'hierarchical' => 1,
		   'show_option_none' => '',
		   'hide_empty' => 0,
		   'parent' => $menu->term_id,
		   'taxonomy' => 'product_cat'
		);
		$wsubcats = get_categories($wsubargs);

		?>
		<li class="cat-item cat-item-<?php echo $menu->id ;?>">	
			<?php if($wsubcats) {?>
				<a href="#" ><?php echo $menu->name; ?> +</a>
				<ul class="cute submenu" style="display: none;">
			<?php
				foreach ($wsubcats as $wsc){
					$tmp = array(
					   'hierarchical' => 1,
					   'show_option_none' => '',
					   'hide_empty' => 0,
					   'parent' => $wsc->term_id,
					   'taxonomy' => 'product_cat'
					);
					$sub3menu = get_categories($tmp);
					
				?>
				<li class="subitem">
					<a href="<?php echo get_term_link( $wsc->slug, $wsc->taxonomy );?>"><?php echo $wsc->name;?></a>
					<?php if($sub3menu) {?>
						<ul class="cute">
						<?php foreach($sub3menu as $menu2){ ?>
							<li class="subitem2">
								<a href="<?php echo get_term_link( $menu2->slug, $menu2->taxonomy );?>"><?php echo $menu2->name;?></a>
							</li>
						<?php } ?>
						</ul>
					
					<?php } ?>
				</li>
			<?php } ?>
				</ul>
		<?php }else{ ?>
			<a href="<?php echo get_term_link( $menu->slug, $menu->taxonomy ); ?>"><?php echo $menu->name; ?></a>
		<?php } ?>
		</li>
<?php }?>
</ul>
</div>
</div>
<script>
jQuery(function($){
	$(".category-product-menu li.cat-item > a").click(function(event){
		event.preventDefault();
		$(this).parent().find(".submenu").toggle();
	});
});
</script>
<?php
}




add_filter( 'wc_order_statuses', 'custom_wc_order_statuses_add_paid_status' );
function custom_wc_order_statuses_add_paid_status( $order_statuses ) {
	$order_statuses['wc-paidorder'] = _x( 'Paid', 'Order status', 'woocommerce' );
	return $order_statuses;
}

add_action( 'init', 'register_custom_post_status_add_paid_status', 10 );
function register_custom_post_status_add_paid_status() {
	register_post_status( 'wc-paidorder', array(
		'label'                     => _x( 'Paid', 'Order status', 'woocommerce' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>', 'woocommerce' )
	) );

}

add_action('admin_head', 'cusom_paidorder_font_icon');

function cusom_paidorder_font_icon() {
  echo '<style>
			.widefat .column-order_status mark.paidorder:after{
				font-family:WooCommerce;
				speak:none;
				font-weight:400;
				font-variant:normal;
				text-transform:none;
				line-height:1;
				-webkit-font-smoothing:antialiased;
				margin:0;
				text-indent:0;
				position:absolute;
				top:0;
				left:0;
				width:100%;
				height:100%;
				text-align:center;
			}

			.widefat .column-order_status mark.paidorder:after{
				content:"\e012";
				color:#006600;
			}
  </style>';
}

add_action ('woocommerce_order_status_paidorder', 'order_status_check_change_status_to_paid');

function order_status_check_change_status_to_paid($order_id) { 

  
 	global $woocommerce;
	$mailer = $woocommerce->mailer();
	
    $order = new WC_Order($order_id);
	
	$user_email =  $order->billing_email;
 
    $mailer->send( $user_email, sprintf( __( 'LQUARTET ONLINE STORE - ').__('Thank you for your payment' ), $order->get_order_number() ) , get_content_html_custom( $order ) );
 
		/*$items = $order->get_items();
		foreach ($items as $item) {
			$product = get_product($item['product_id']);
			if(product_available_for_quote($product)){
				$order->update_status('quote');
			}
		}*/
	
	
}
function get_content_html_custom($order) {
    ob_start();
    woocommerce_get_template( 'emails/customer-paidorder-order.php', array(
        'order'         => $order,
        'email_heading' => '',
		'sent_to_admin' => false,
               'plain_text'    => false
    ) );
    return ob_get_clean();
}

add_filter( 'woocommerce_my_account_my_address_formatted_address', 'custom_my_account_my_address_formatted_address', 10, 3 );
function custom_my_account_my_address_formatted_address( $fields, $customer_id, $type ) {
	if ( $type == 'billing' ) {
		$fields['last_name_phontic'] = get_user_meta( $customer_id, 'billing_last_name_phontic', true );
		$fields['first_name_phontic'] = get_user_meta( $customer_id, 'billing_first_name_phontic', true );
	}
	elseif ( $type == 'shipping' ) {
		$fields['last_name_phontic'] = get_user_meta( $customer_id, 'shipping_last_name_phontic', true );
		$fields['first_name_phontic'] = get_user_meta( $customer_id, 'shipping_first_name_phontic', true );
	}

	return $fields;
}

add_filter( 'woocommerce_billing_fields', 'custom_billing_address_to_edit', 10, 1 );
function custom_billing_address_to_edit( $address ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars['edit-address'] ) && $wp_query->query_vars['edit-address'] != 'billing' ) {
		return $address;
	}
	
	$newAddress  = array();
	
	$billing_last_name = $address['billing_last_name'];
	unset($address['billing_last_name']);
	
	foreach ($address as $key_address => $billing_address)
	{
		if ($key_address == 'billing_first_name')
		{
			$newAddress['billing_last_name'] = $billing_last_name;
			$newAddress['billing_first_name'] = $billing_address;
			$newAddress['billing_last_name']['class'] = array( 'form-row-first' );
			$newAddress['billing_first_name']['class'] = array( 'form-row-last' );
			
			unset($newAddress['billing_last_name']['clear']);
			$newAddress['billing_first_name']['clear'] = 1;
			
			if ( ! isset( $newAddress['billing_last_name_phontic'] ) ) {
				$newAddress['billing_last_name_phontic'] = array(
						'label'       => __('姓(ふりがな)', 'woocommerce'),
						'placeholder' => _x( '姓(ふりがな)', 'placeholder', 'woocommerce' ),
						'required'    => true, //change to false if you do not need this field to be required
						'class'       => array( 'form-row-first' ),
						'value'       => get_user_meta( get_current_user_id(), 'billing_last_name_phontic', true )
				);
			}
			
			if ( ! isset( $newAddress['billing_first_name_phontic'] ) ) {
				$newAddress['billing_first_name_phontic'] = array(
						'label'       => __( '名(ふりがな)', 'woocommerce' ),
						'placeholder' => _x( '名(ふりがな)', 'placeholder', 'woocommerce' ),
						'required'    => true, //change to false if you do not need this field to be required
						'class'       => array( 'form-row-last' ),
						'value'       => get_user_meta( get_current_user_id(), 'billing_first_name_phontic', true )
				);
			}
		}
		else
		{
			$newAddress[$key_address] = $billing_address;
		}
	}
	
	return $newAddress;
}

add_filter( 'woocommerce_shipping_fields', 'custom_shipping_address_to_edit', 10, 1 );
function custom_shipping_address_to_edit( $address ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars['edit-address'] ) && $wp_query->query_vars['edit-address'] != 'shipping' ) {
		return $address;
	}

	$newAddress  = array();

	$shipping_last_name = $address['shipping_last_name'];
	unset($address['shipping_last_name']);

	foreach ($address as $key_address => $shipping_address)
	{
		if ($key_address == 'shipping_first_name')
		{
			$newAddress['shipping_last_name'] = $shipping_last_name;
			$newAddress['shipping_first_name'] = $shipping_address;
			$newAddress['shipping_last_name']['class'] = array( 'form-row-first' );
			$newAddress['shipping_first_name']['class'] = array( 'form-row-last' );
				
			unset($newAddress['shipping_last_name']['clear']);
			$newAddress['shipping_first_name']['clear'] = 1;
				
			if ( ! isset( $newAddress['shipping_last_name_phontic'] ) ) {
				$newAddress['shipping_last_name_phontic'] = array(
						'label'       => __('姓(ふりがな)', 'woocommerce'),
						'placeholder' => _x( '姓(ふりがな)', 'placeholder', 'woocommerce' ),
						'required'    => true, //change to false if you do not need this field to be required
						'class'       => array( 'form-row-first' ),
						'value'       => get_user_meta( get_current_user_id(), 'shipping_last_name_phontic', true )
				);
			}
				
			if ( ! isset( $newAddress['shipping_first_name_phontic'] ) ) {
				$newAddress['shipping_first_name_phontic'] = array(
						'label'       => __( '名(ふりがな)', 'woocommerce' ),
						'placeholder' => _x( '名(ふりがな)', 'placeholder', 'woocommerce' ),
						'required'    => true, //change to false if you do not need this field to be required
						'class'       => array( 'form-row-last' ),
						'value'       => get_user_meta( get_current_user_id(), 'shipping_first_name_phontic', true )
				);
			}
		}
		else
		{
			$newAddress[$key_address] = $shipping_address;
		}
	}

	return $newAddress;
}

add_filter( 'woocommerce_order_formatted_billing_address' , 'woo_custom_order_formatted_billing_address',10,2 );
function woo_custom_order_formatted_billing_address($address, $args) {
	$address = array(
			'last_name'		=> $args->billing_last_name,
			'first_name'	=> $args->billing_first_name,
			'last_name_phontic'		=> $args->billing_last_name_phontic,
			'first_name_phontic'	=> $args->billing_first_name_phontic,
			'company'		=> $args->billing_company,
			'postcode'		=> $args->billing_postcode,
			'state'			=> $args->billing_state,
			'city'			=> $args->billing_city,
			'address_1'		=> $args->billing_address_1,
			'address_2'		=> $args->billing_address_2,
			'country'		=> $args->billing_country
	);
	return $address;
}

add_filter( 'woocommerce_order_formatted_shipping_address' , 'woo_custom_order_formatted_shipping_address',10,2 );
function woo_custom_order_formatted_shipping_address($address, $args) {
	$address = array(
			'last_name'		=> $args->shipping_last_name,
			'first_name'	=> $args->shipping_first_name,
			'last_name_phontic'		=> $args->shipping_last_name_phontic,
			'first_name_phontic'	=> $args->shipping_first_name_phontic,
			'company'		=> $args->shipping_company,
			'postcode'		=> $args->shipping_postcode,
			'state'			=> $args->shipping_state,
			'city'			=> $args->shipping_city,
			'address_1'		=> $args->shipping_address_1,
			'address_2'		=> $args->shipping_address_2,
			'country'		=> $args->shipping_country
	);
	return $address;
}

add_filter( 'woocommerce_formatted_address_replacements', 'custom_formatted_address_replacements', 10, 2 );
function custom_formatted_address_replacements( $address, $args ) {
	$address['{last_name_phontic}'] = '';
	$address['{first_name_phontic}'] = '';

	if ( ! empty( $args['last_name_phontic'] ) ) {
		$address['{last_name_phontic}'] = $args['last_name_phontic'];
	}

	if ( ! empty( $args['first_name_phontic'] ) ) {
		$address['{first_name_phontic}'] = $args['first_name_phontic'];
	}
	return $address;
}

add_filter( 'woocommerce_localisation_address_formats', 'custom_localisation_address_format' );
function custom_localisation_address_format( $formats ) {
	$formats['JP'] .= "{last_name_phontic} {first_name_phontic}";
	return $formats;
}

add_filter( 'woocommerce_admin_billing_fields', 'custom_admin_billing_fields' );
function custom_admin_billing_fields( $fields ) {
$fields = array(
		'last_name' => array(
			'label' => __( 'Last Name', 'woocommerce' ),
			'show'  => false
		),
		'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
		),
		'last_name_phontic' => array(
				'label' => __('姓(ふりがな)', 'woocommerce'),
				'show'  => false
		),
		'first_name_phontic' => array(
				'label' => __( '名(ふりがな)', 'woocommerce' ),
				'show'  => false
		),
		'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
		),
		'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
		),
		'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
		),
		'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
		),
		'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
		),
		'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
		),
		'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
		),
		'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
		),
		'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
		),
);
return $fields;
}

add_filter( 'woocommerce_admin_shipping_fields', 'custom_admin_shipping_fields' );
function custom_admin_shipping_fields( $fields ) {
	$fields = array(
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name_phontic' => array(
					'label' => __('姓(ふりがな)', 'woocommerce'),
					'show'  => false
			),
			'first_name_phontic' => array(
					'label' => __( '名(ふりがな)', 'woocommerce' ),
					'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
	);
	return $fields;
}

add_filter( 'woocommerce_found_customer_details', 'custom_found_customer_details' );
function custom_found_customer_details( $customer_data ) {
	$customer_data['billing_last_name_phontic'] = get_user_meta( $_POST['user_id'], 'billing_last_name_phontic', true );
	$customer_data['billing_first_name_phontic'] = get_user_meta( $_POST['user_id'], 'billing_first_name_phontic', true );
	
	$customer_data['shipping_last_name_phontic'] = get_user_meta( $_POST['user_id'], 'shipping_last_name_phontic', true );
	$customer_data['shipping_first_name_phontic'] = get_user_meta( $_POST['user_id'], 'shipping_first_name_phontic', true );

	return $customer_data;
}

add_filter( 'woocommerce_customer_meta_fields', 'custom_customer_meta_fields' );
function custom_customer_meta_fields( $fields ) {
	$fields['billing']['fields']['billing_last_name_phontic'] = array(
			'label'       => __('姓(ふりがな)', 'woocommerce')
	);

	$fields['billing']['fields']['billing_first_name_phontic'] = array(
			'label'       => __( '名(ふりがな)', 'woocommerce' )
	);

	$fields['shipping']['fields']['shipping_last_name_phontic'] = array(
		'label'       => __('姓(ふりがな)', 'woocommerce')
	);
	
	$fields['shipping']['fields']['shipping_first_name_phontic'] = array(
			'label'       => __( '名(ふりがな)', 'woocommerce' )
	);

	return $fields;
}

add_action('admin_footer','posts_status_color');
function posts_status_color(){
?>
	<style> 
	._billing_last_name_field, ._shipping_last_name_field {float: left !important;}
	._billing_first_name_field, ._shipping_first_name_field {float: right !important;}
	</style> 
<?php 
}

add_action( 'init', 'customise_wootheme' );
function customise_wootheme() {
    add_filter( 'woo_pagination', '__return_false' );
}	


function woocommerce_paginations() {
		global $wp_query;
			$total = $wp_query->max_num_pages;
			// only bother with the rest if we have more than 1 page!
			if ( $total > 1 )  {
				 // get the current page
				 if ( !$current_page = get_query_var('paged') )
					  $current_page = 1;
				 // structure of "format" depends on whether we're using pretty permalinks
				 $options = get_option('woocommerce_permalinks');
				 if( get_option('permalink_structure') ) {
					 $format = '&paged=%#%';
				 } else {
					 $format = 'page/%#%/';
				 }
				 
				 $load_more = true;
				 if ($load_more == true)
				 {
				 	echo '
					<div id="load_more_wraper">
						<button class="button">'. __('Load More', 'woocommerce') .'</button>
				 	</div>';
				 }
				 
				 echo "<nav class='pagination woo-pagination'>";
				 echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
				 	'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
				 	'format'       => '',
				 	'add_args'     => '',
				 	'current'      => max( 1, get_query_var( 'paged' ) ),
				 	'total'        => $wp_query->max_num_pages,
				 	'prev_text'    => '',
				 	'next_text'    => '',
				 	'type'         => 'list',
				 	'end_size'     => 4,
				 	'mid_size'     => 3
				 ) ) );
				 echo "</nav>";
			}		
	}
add_action( 'woocommerce_after_shop_loop', 'woocommerce_paginations', 1);
add_action( 'woocommerce_before_shop_loop_item_title', 'wc_ninja_outofstock_notify_on_archives', 10 );
function wc_ninja_outofstock_notify_on_archives() {
    global $product;
    //if ( ! $product->managing_stock() && ! $product->is_in_stock() ) {
        echo '<span class=" woosticker soldout_round_right  pos_right " style="display:none;">Sold Out</span>';
    //}
}

function hide_plugin_order_by_product ()
{
	global $wp_list_table;
	$hidearr = array(
		'woocommerce-advanced-free-shipping/woocommerce-advanced-free-shipping.php',
		'woocommerce-products-filter/index.php',
	);
	$active_plugins = get_option('active_plugins');

	$myplugins = $wp_list_table->items;
	foreach ( $myplugins as $key => $val )
	{
		if ( in_array($key, $hidearr) && in_array($key, $active_plugins))
		{
			unset($wp_list_table->items[$key]);
		}
	}
}
add_action('pre_current_active_plugins', 'hide_plugin_order_by_product');

function pr($data)
{
	echo '<pre>'; print_r($data); echo'</pre>';
}

add_filter( 'woocommerce_get_catalog_ordering_args', 'mystile_woocommerce_get_catalog_ordering_args', 10000, 1 );
function mystile_woocommerce_get_catalog_ordering_args($args) {
	if((strpos($_SERVER['REDIRECT_URL'], '/shop') !== false || $_REQUEST['action'] == 'woof_draw_products') && !$_GET['orderby']){
		$args['orderby']  = 'menu_order title';
		$args['order']    = 'ASC';
		$args['meta_key'] = '';
	}
	return $args;
}

add_filter( 'post_limits', 'mystile_modify_request_perpage', 1000, 2 );
function mystile_modify_request_perpage( $limit, $query ) {
	if ( is_admin() && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] && $_GET['orderby'] == 'menu_order title' ) {
		$limit = "LIMIT 0, 20000000";
	}
	return $limit;
}

add_filter( 'edit_posts_per_page', 'mystile_modify_edit_products_per_page', 1000, 2 );
function mystile_modify_edit_products_per_page( $per_page, $post_type ) {
	if ( is_admin() && 'product' == $post_type && $_GET['orderby'] == 'menu_order title' ) {
		$per_page = 20000000;
	}
	return $per_page;
}


add_action('wp_loaded', 'init_action_wp_loaded');
function init_action_wp_loaded()
{
	if (isset($_REQUEST['private_schedule']))
	{
		every_one_minutes_event_func();
	}
}

add_filter( 'cron_schedules', 'isa_add_every_one_minutes' );
function isa_add_every_one_minutes( $schedules ) {
	$schedules['every_one_minutes'] = array(
		'interval'  => 60,
		'display'   => __( 'Every 1 Minute', 'mystile' )
	);
	return $schedules;
}

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'isa_add_every_one_minutes' ) ) {
	wp_schedule_event( time(), 'every_one_minutes', 'isa_add_every_one_minutes' );
}

// Hook into that action that'll fire every one minutes
add_action( 'isa_add_every_one_minutes', 'every_one_minutes_event_func' );
function every_one_minutes_event_func() {
	global $wpdb;
	$schedule_privates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s AND meta_value <= NOW()", 'private_schedule' ) );
	if (!empty($schedule_privates))
	{
		foreach ($schedule_privates as $schedule_private)
		{
			wp_update_post(array(
				'ID'    =>  $schedule_private->post_id,
				'post_status'   =>  'private'
			));
			delete_post_meta($schedule_private->post_id, 'private_schedule');
		}
	}
}

add_action( 'save_post', 'save_post_meta_schedule_private', 10, 2 );
function save_post_meta_schedule_private($post_id, $post)
{
	if ( isset( $_POST['private_time'] ))
	{
		$private_date = $_POST['private_time']['aa'] . '-' . $_POST['private_time']['mm'] . '-' . $_POST['private_time']['jj'];
		$private_date .=  ' ' . $_POST['private_time']['hh'] . ':' . $_POST['private_time']['mn'];
		update_post_meta($post->ID, 'private_schedule', $private_date);
	}
}


add_action( 'post_submitbox_misc_actions', 'add_schedule_private_product', 10, 1 );
function add_schedule_private_product($post)
{
	global $action;
	
	if ($post->post_type != 'product')
		return '';
	
	/* translators: Publish box date format, see https://secure.php.net/date */
	$datef = __( 'M j, Y @ H:i' );
	if ( 0 != $post->ID ) {
		$private_schedule = get_post_meta($post->ID, 'private_schedule', true);
		if ('private' == $post->post_status ) {
			/* translators: Post date information. 1: Date on which the post was published */
			$stamp = __('Already Privated', 'mystile');
		}
		elseif ( !$private_schedule) {
			$stamp = __('No private schedule');
		}
		elseif ( $private_schedule) {
			$stamp = __('Schedule for Private: <b>%1$s</b>');
		}
		$date = date_i18n( $datef, strtotime( $private_schedule) );
	} 
?>
<div class="misc-pub-section curtime misc-pub-curtime">
	<span id="private_time">
	<?php printf($stamp, $date); ?></span>
	<a href="#edit_private_time" id="private_time_edit" class="edit-private_time hide-if-no-js" role="button"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span></a>
	<fieldset id="private_timediv" class="hide-if-js">
	<legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
	<?php touch_private_time( ( $action === 'edit'), 1, 1, 1  ); ?>
	</fieldset>
	<script>
		jQuery('body').on('click', '#private_time_edit', function(){
			jQuery('#private_timediv').slideToggle('slow', function(){
				if (jQuery('#private_timediv').is(':hidden'))
				{
					jQuery('#private_timediv').find('input, select').attr('disabled', true);
				}
				else {
					jQuery('#private_timediv').find('input, select').removeAttr('disabled');
				}
			});
		});
	</script>
</div><?php // /misc-pub-section ?>
<?php
}

function touch_private_time( $edit = 1, $for_post = 1, $tab_index = 0, $multi = 0 ) {
	global $wp_locale;
	$post = get_post();
	
	$tab_index_attribute = '';
	if ( (int) $tab_index > 0 ) $tab_index_attribute = " tabindex=\"$tab_index\"";
	
	$time_adj = current_time('timestamp');
	$post_date = get_post_meta($post->ID, 'private_schedule', true);
	$post_date = $post_date ? $post_date : date('Y-m-d H:i:s');
	
	$jj = ($edit) ? mysql2date('d', $post_date, false) : gmdate('d', $time_adj);
	$mm = ($edit) ? mysql2date('m', $post_date, false) : gmdate('m', $time_adj);
	$aa = ($edit) ? mysql2date('Y', $post_date, false) : gmdate('Y', $time_adj);
	$hh = ($edit) ? mysql2date('H', $post_date, false) : gmdate('H', $time_adj);
	$mn = ($edit) ? mysql2date('i', $post_date, false) : gmdate('i', $time_adj);
	$ss = ($edit) ? mysql2date('s', $post_date, false) : gmdate('s', $time_adj);
	
	$cur_jj = gmdate('d', $time_adj);
	$cur_mm = gmdate('m', $time_adj);
	$cur_aa = gmdate('Y', $time_adj);
	$cur_hh = gmdate('H', $time_adj);
	$cur_mn = gmdate('i', $time_adj);
	
	$month = '<label><span class="screen-reader-text">' . __('Month') . '</span><select ' . ($multi ? '' : 'id="mm_private" ') . 'name="private_time[mm]"' . $tab_index_attribute . " disabled>\n";
	for ( $i = 1; $i < 13; $i = $i + 1 )
	{
		$monthnum = zeroise($i, 2);
		$monthtext = $wp_locale->get_month_abbrev($wp_locale->get_month($i));
		$month .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected($monthnum, $mm, false) . '>';
		/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
		$month .= sprintf(__('%1$s-%2$s'), $monthnum, $monthtext) . "</option>\n";
	}
	$month .= '</select></label>';
	
	$day = '<label><span class="screen-reader-text">' . __('Day') . '</span><input type="text" ' . ($multi ? '' : 'id="jj_private" ') . 'name="private_time[jj]" value="' . $jj . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" disabled/></label>';
	$year = '<label><span class="screen-reader-text">' . __('Year') . '</span><input type="text" ' . ($multi ? '' : 'id="aa_private" ') . 'name="private_time[aa]" value="' . $aa . '" size="4" maxlength="4"' . $tab_index_attribute . ' autocomplete="off" disabled/></label>';
	$hour = '<label><span class="screen-reader-text">' . __('Hour') . '</span><input type="text" ' . ($multi ? '' : 'id="hh_private" ') . 'name="private_time[hh]" value="' . $hh . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" disabled/></label>';
	$minute = '<label><span class="screen-reader-text">' . __('Minute') . '</span><input type="text" ' . ($multi ? '' : 'id="mn_private" ') . 'name="private_time[mn]" value="' . $mn . '" size="2" maxlength="2"' . $tab_index_attribute . ' autocomplete="off" disabled/></label>';
	
	echo '<div class="timestamp-wrap">';
	/* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
	printf(__('%1$s %2$s, %3$s @ %4$s:%5$s'), $month, $day, $year, $hour, $minute);
	
	echo '</div><input type="hidden" id="ss_private" name="private_time[ss]" value="' . $ss . '" disabled/>';
}