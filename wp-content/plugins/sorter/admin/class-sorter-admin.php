<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       rr
 * @since      1.0.0
 *
 * @package    Sorter
 * @subpackage Sorter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sorter
 * @subpackage Sorter/admin
 * @author     rohit <a>
 */
class Sorter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sorter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sorter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sorter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sorter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sorter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sorter-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
 * Register a custom menu page.
 */
function wpdocs_register_my_custom_menu_page() {
    add_menu_page(
        'Sorter',
        'Sorter',
        'manage_options',
         'custompage',
        array($this, 'my_custom_menu_page')
    );
}
  function wad_get_product_id_to_use($product) {
    $product_class = get_class($product);

    if ($product_class == "WC_Product_Variation") {
        $pid = $product->variation_id;
    } else
        $pid = $product->id;

    return $pid;
}
function my_custom_menu_page(){
  global $wad_discounts;
  global $woocommerce;
  
    esc_html_e( 'Product Sorting Completed', 'textdomain' );  
		
	$loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => -1 ,"post_status"=>array("publish", "future")) );
 echo "<pre>";
 $i=0;
 $discount= new WAD_Discount(false);
// print_r($wad_discounts);
 $all_discounts = $wad_discounts;
        
	while ( $loop->have_posts() ) : $loop->the_post();
	$i++;
	//print_r($wad_discounts);
		$theid = get_the_ID();
  
$dt=strtotime(get_the_date( 'Y-m-d H:i' ));
	
		 
		update_post_meta( $theid, 'sortingdt', $dt );
	
		endwhile; wp_reset_query();
		
		foreach ($all_discounts["product"] as $discount_id => $discount_obj) {
			 $settings = $discount_obj->settings;
			 $list_products = $discount_obj->products;
			$ndt=strtotime($settings['start-date']);
		//print_r($list_products);
		$j=0;
		foreach ($list_products as $k => $v)
		{
			$j++;
			update_post_meta( $v, 'sortingdt', $ndt+$j );
		}
		
	/*	$args = array(
    'post_type' => 'product',
    'posts_per_page' => 100,
    'orderby' => 'meta_value_num',
	'meta_key' => 'sortingdt',
    'order' => 'desc'
    );
$loop = new WP_Query( $args );
if ( $loop->have_posts() ) {
    while ( $loop->have_posts() ) : $loop->the_post(); 
		echo '<br/>'.get_the_ID();
	endwhile;
}*/
       
		
		}
		
}
function cw_add_new_postmeta_orderby( $sortby ) {
   $sortby['salenew'] = __( 'Sort By Sale New', 'woocommerce' );
   return $sortby;
}

function cw_add_postmeta_ordering_args( $args_sort_cw ) {

	$cw_orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) :
	
        apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	
	switch( $cw_orderby_value ) {
		case 'salenew':
			$args_sort_cw['orderby'] = 'meta_value_num';
			$args_sort_cw['order'] = 'desc';
			$args_sort_cw['meta_key'] = 'sortingdt';
			break;
      

	  
	}

	return $args_sort_cw;
}
}
