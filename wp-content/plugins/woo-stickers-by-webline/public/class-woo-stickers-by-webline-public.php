<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/public
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline_Public {

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

	private $general_settings_key = 'general_settings';
	private $new_product_settings_key = 'new_product_settings';
	private $sale_product_settings_key = 'sale_product_settings';
	private $sold_product_settings_key = 'sold_product_settings';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->general_settings = ( array ) get_option ( $this->general_settings_key );
		$this->new_product_settings = ( array ) get_option ( $this->new_product_settings_key );
		$this->sale_product_settings = ( array ) get_option ( $this->sale_product_settings_key );
		$this->sold_product_settings = ( array ) get_option ( $this->sold_product_settings_key );

		// Merge with defaults
		$this->general_settings = array_merge ( array (
				'enable_sticker' => 'no',
				'enable_sticker_list' => 'no',
				'enable_sticker_detail' => 'no' 
		), $this->general_settings );
		
		$this->new_product_settings = array_merge ( array (
				'enable_new_product_sticker' => 'no',
				'enable_new_product_style' => 'ribbon',
				'new_product_sticker_days' => '10',
				'new_product_position' => 'left',
				'new_product_custom_sticker' => ''
		), $this->new_product_settings );
		
		$this->sale_product_settings = array_merge ( array (
				'enable_sale_product_sticker' => 'no',
				'enable_sale_product_style' => 'ribbon',
				'sale_product_position' => 'right',
				'sale_product_custom_sticker' => '' 
		), $this->sale_product_settings );
		
		$this->sold_product_settings = array_merge ( array (
				'enable_sold_product_sticker' => 'no',
				'enable_sold_product_style' => 'ribbon',
				'sold_product_position' => 'left',
				'sold_product_custom_sticker' => ''
		), $this->sold_product_settings );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-stickers-by-webline-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-stickers-by-webline-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Call back function for show new product badge.
	 *
	 * @return void
	 * @param No arguments passed
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_new_badge() {

		if ($this->general_settings['enable_sticker'] == "yes" && $this->new_product_settings['enable_new_product_sticker'] == "yes") 		{

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))
			{
				$postdate = get_the_time ( 'Y-m-d' );
				$postdatestamp = strtotime ( $postdate );				
				$newness = (($this->new_product_settings['new_product_sticker_days']=="") ? 10 : trim($this->new_product_settings['new_product_sticker_days']));		
				$classPosition=(($this->new_product_settings['new_product_position']=='left')? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));
				$class=(($this->new_product_settings['new_product_custom_sticker'] =='') ? 
						(($this->new_product_settings['enable_new_product_style'] == "ribbon") ? 
							(($this->new_product_settings['new_product_position']=='left') ?
								" woosticker new_ribbon_left ":" woosticker new_ribbon_right ") : 
									(($this->new_product_settings['new_product_position']=='left') ?
										" woosticker new_round_left ":" woosticker new_round_right ")):"woosticker custom_sticker_image");	
	
				if ((time () - (60 * 60 * 24 * $newness)) < $postdatestamp) {
					//// If the product was published within the newness time frame display the new badge /////
					if($this->new_product_settings['new_product_custom_sticker']=='')
						echo '<span class="'. $class . $classPosition. '">' . __ ( 'New', 'woocommerce-new-badge' ) . '</span>';
					else 
						echo '<span class="'. $class . $classPosition. '" style="background-image:url('.$this->new_product_settings['new_product_custom_sticker'].');"></span>';
				}

			}
		}
			
	}
	

	/**
	 * Call back function for show sale product badge.
	 *
	 * @return string
	 * @param string $span_class_onsale_sale_woocommerce_span The span class onsale sale woocommerce span.
	 * @param string $post The post.
	 * @param string $product The product.
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_sale_badge($span_class_onsale_sale_woocommerce_span, $post, $product ) {
	
		if ($this->general_settings['enable_sticker'] == "yes" && $this->sale_product_settings['enable_sale_product_sticker'] == "yes") {

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))
			{
				global $product;

				$classSalePosition=(($this->sale_product_settings['sale_product_position']=='left') ? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));				
				
				$classSale = (($this->sale_product_settings['sale_product_custom_sticker']=='')?(($this->sale_product_settings['enable_sale_product_style'] == "ribbon") ? (($this->sale_product_settings['sale_product_position']=='left')?" woosticker onsale_ribbon_left ":" woosticker onsale_ribbon_right ") : (($this->sale_product_settings['sale_product_position']=='left')?" woosticker onsale_round_left ":" woosticker onsale_round_right ")):"woosticker custom_sticker_image");
				
				if ( $product->is_in_stock ()) {
					if($this->sale_product_settings['sale_product_custom_sticker']=='') {
						$span_class_onsale_sale_woocommerce_span =  '<span class="' . $classSale . $classSalePosition . '"> Sale </span>';
					}
					else {
						$span_class_onsale_sale_woocommerce_span =  '<span class="' . $classSale . $classSalePosition . '" style="background-image:url('.$this->sale_product_settings['sale_product_custom_sticker'].');"> Sale </span>';
					}
				}
				else {
					if($this->sold_product_settings['enable_sold_product_sticker']=="yes") {
						$span_class_onsale_sale_woocommerce_span='';
					}
				}
			}
		}
		
		return $span_class_onsale_sale_woocommerce_span;
	}

	/**
	 * Call back function for show sold product badge on list.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 * @since    1.0.0
	 */
	public function show_product_soldout_badge()
	{	
		if ($this->general_settings['enable_sticker'] == "yes" && $this->sold_product_settings['enable_sold_product_sticker'] == "yes") {

			if((!is_product() && $this->general_settings['enable_sticker_list'] == "yes" ) || (is_product() && $this->general_settings['enable_sticker_detail'] == "yes"))	{
				
				global $product;
					
				$classSoldPosition=(($this->sold_product_settings['sold_product_position']=='left') ? ((is_product())? " pos_left_detail " : " pos_left " ) : ((is_product())? " pos_right_detail " : " pos_right "));	
				
				$classSold=(($this->sold_product_settings['sold_product_custom_sticker']=='')?(($this->sold_product_settings['enable_sold_product_style'] == "ribbon") ? (($this->sold_product_settings['sold_product_position']=='left')?" woosticker soldout_ribbon_left ":" woosticker soldout_ribbon_right ") : (($this->sold_product_settings['sold_product_position']=='left')?" woosticker soldout_round_left ":" woosticker soldout_round_right ")):"woosticker custom_sticker_image");

				if($product->product_type=='variable') {

					$total_qty=0;
					
					$available_variations = $product->get_available_variations();
				   
					foreach ($available_variations as $variation) {

						if($variation['is_in_stock']==true){
							$total_qty++;
						}
						
					}
					
					if($total_qty==0){
						if($this->sold_product_settings['enable_sold_product_sticker']=="yes") {
							if($this->sold_product_settings['sold_product_custom_sticker']=='') { 
								echo '<span class="'.$classSold . $classSoldPosition .'">Sold Out</span>';
							}							
							else {
								echo '<span class="' . $classSold . $classSoldPosition . '" style="background-image:url('.$this->sold_product_settings['sold_product_custom_sticker'].');"> Sold Out </span>';
							}
						}
					}				

				}
				else {

					if (! $product->is_in_stock ()) {
						if($this->sold_product_settings['enable_sold_product_sticker']=="yes") {
							if($this->sold_product_settings['sold_product_custom_sticker']=='') { 
								echo '<span class="'.$classSold . $classSoldPosition .'">Sold Out</span>';
							}							
							else {
								echo '<span class="' . $classSold . $classSoldPosition . '" style="background-image:url('.$this->sold_product_settings['sold_product_custom_sticker'].');"> Sold Out </span>';
							}
						}
					}
				}
			}
		}
	}

}
