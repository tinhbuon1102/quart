<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.weblineindia.com
 * @since      1.0.0
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Stickers_By_Webline
 * @subpackage Woo_Stickers_By_Webline/admin
 * @author     Weblineindia <info@weblineindia.com>
 */
class Woo_Stickers_By_Webline_Admin {

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
	private $plugin_options_key = 'wli-stickers';
	private $plugin_settings_tabs = array ();


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

		$this->load_settings ();
		$widget_ops = array (
				'classname' => 'wli_woo_stickers',
				'description' => __ ( "WLI Woocommerce Stickers", "wli_woo_stickers_widget" ) 
		);

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
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-stickers-by-webline-admin.css', array(), $this->version, 'all' );

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
		 * defined in Woo_Stickers_By_Webline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Stickers_By_Webline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-stickers-by-webline-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register settings link on plugin page.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_link($links, $file)
    {   
    	$wooStickerFile = WS_PLUGIN_FILE;    	 
        if (basename($file) == $wooStickerFile) {
        	
            $linkSettings = '<a href="' . admin_url("options-general.php?page=wli-stickers") . '">Settings</a>';
            array_unshift($links, $linkSettings);
        }
        return $links;
    }

	/**
	 * Loads settings from
	 * the database into their respective arrays.
	 * Uses
	 * array_merge to merge with default values if they're
	 * missing.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function load_settings() {
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
	 * Registers the general settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function register_general_settings() {
		$this->plugin_settings_tabs [$this->general_settings_key] = 'General';
		
		register_setting ( $this->general_settings_key, $this->general_settings_key );
		add_settings_section ( 'section_general', 'General Plugin Settings', array (
				&$this,
				'section_general_desc' 
		), $this->general_settings_key );
		
		add_settings_field ( 'enable_sticker', 'Enable Product Sticker:', array (
				&$this,
				'enable_sticker' 
		), $this->general_settings_key, 'section_general' );
		
		add_settings_field ( 'enable_sticker_list', 'Enable Sticker On Product Listing Page:', array (
				&$this,
				'enable_sticker_list' 
		), $this->general_settings_key, 'section_general' );
		
		add_settings_field ( 'enable_sticker_detail', 'Enable Sticker On Product Details Page:', array (
				&$this,
				'enable_sticker_detail' 
		), $this->general_settings_key, 'section_general' );
	}
	
	/**
	 * Registers the New Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @since 1.0.0
	 * @var No arguments passed
	 * @return void
	 * @author Weblineindia
	 */
	public function register_new_product_settings() {
		$this->plugin_settings_tabs [$this->new_product_settings_key] = 'New Products';
		
		register_setting ( $this->new_product_settings_key, $this->new_product_settings_key );
		
		add_settings_section ( 'section_new_product', 'Sticker Configurations for New Products', array (
				&$this,
				'section_new_product_desc' 
		), $this->new_product_settings_key );
		
		add_settings_field ( 'enable_new_product_sticker', 'Enable Product Sticker:', array (
				&$this,
				'enable_new_product_sticker' 
		), $this->new_product_settings_key, 'section_new_product' );
		
		add_settings_field ( 'new_product_sticker_days', 'Number of Days for New Product:', array (
		&$this,
		'new_product_sticker_days'
			), $this->new_product_settings_key, 'section_new_product' );
		

		add_settings_field ( 'new_product_position', 'Product Sticker Position:', array (
		&$this,
		'new_product_position'
			), $this->new_product_settings_key, 'section_new_product' );

		add_settings_field ( 'enable_new_product_style', 'Enable Sticker On New Product:', array (
				&$this,
				'enable_new_product_style' 
		), $this->new_product_settings_key, 'section_new_product' );
		
		add_settings_field ( 'new_product_custom_sticker', 'Add your custom sticker:', array (
				&$this,
				'new_product_custom_sticker'
		), $this->new_product_settings_key, 'section_new_product' );
	}
	
	/**
	 * Registers the Sale Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function register_sale_product_settings() {
		$this->plugin_settings_tabs [$this->sale_product_settings_key] = 'Products On Sale';
		
		register_setting ( $this->sale_product_settings_key, $this->sale_product_settings_key );
		add_settings_section ( 'section_sale_product', 'Sticker Configurations for Products On Sale', array (
				&$this,
				'section_sale_product_desc' 
		), $this->sale_product_settings_key );
		
		add_settings_field ( 'enable_sale_product_sticker', 'Enable Product Sticker:', array (
				&$this,
				'enable_sale_product_sticker' 
		), $this->sale_product_settings_key, 'section_sale_product' );
		
		add_settings_field ( 'sale_product_position', 'Product Sticker Position:', array (
		&$this,
		'sale_product_position'
			), $this->sale_product_settings_key, 'section_sale_product' );
		
		add_settings_field ( 'enable_sale_product_style', 'Enable Sticker On Sale Product:', array (
				&$this,
				'enable_sale_product_style' 
		), $this->sale_product_settings_key, 'section_sale_product' );
		
		add_settings_field ( 'sale_product_custom_sticker', 'Add your custom sticker:', array (
		&$this,
		'sale_product_custom_sticker'
			), $this->sale_product_settings_key, 'section_sale_product' );
	}
	/**
	 * Registers the Sold Product settings via the Settings API,
	 * appends the setting to the tabs array of the object.
	 * Tab Name will defined here.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function register_sold_product_settings() {
		$this->plugin_settings_tabs [$this->sold_product_settings_key] = 'Soldout Products';
	
		register_setting ( $this->sold_product_settings_key, $this->sold_product_settings_key );
		add_settings_section ( 'section_sold_product', 'Sticker Configurations for Soldout Products', array (
		&$this,
		'section_sold_product_desc'
				), $this->sold_product_settings_key );
	
		add_settings_field ( 'enable_sold_product_sticker', 'Enable Product Sticker:', array (
		&$this,
		'enable_sold_product_sticker'
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field ( 'sold_product_position', 'Product Sticker Position:', array (
		&$this,
		'sold_product_position'
			), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field ( 'enable_sold_product_style', 'Enable Sticker On Sold Product:', array (
		&$this,
		'enable_sold_product_style'
				), $this->sold_product_settings_key, 'section_sold_product' );
		
		add_settings_field ( 'sold_product_custom_sticker', 'Add your custom sticker:', array (
		&$this,
		'sold_product_custom_sticker'
			), $this->sold_product_settings_key, 'section_sold_product' );
	}

	/**
	 * The following methods provide descriptions
	 * for their respective sections, used as callbacks
	 * with add_settings_section
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function section_general_desc() {		
	}
	public function section_new_product_desc() {				
	}
	public function section_sale_product_desc() {		
	}
	public function section_sold_product_desc() {		
	}

	/**
	 * General Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker() {
		?>
<select id='enable_sticker'
	name="<?php echo $this->general_settings_key; ?>[enable_sticker]">
	<option value='yes'
		<?php selected( $this->general_settings['enable_sticker'], 'yes',true );?>>Yes</option>
	<option value='no'
		<?php selected( $this->general_settings['enable_sticker'], 'no',true );?>>No</option>
</select>
<p class="description">Select wether you want to enable sticker feature or not.</p>
<?php
	}
	/**
	 * General Settings :: Enable Sticker On Product Listing Page
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker_list() {
		?>
<select id='enable_sticker_list'
	name="<?php echo $this->general_settings_key; ?>[enable_sticker_list]">
	<option value='yes'
		<?php selected( $this->general_settings['enable_sticker_list'], 'yes',true );?>>Yes</option>
	<option value='no'
		<?php selected( $this->general_settings['enable_sticker_list'], 'no',true );?>>No</option>
</select>
<p class="description">Select wether you want to enable sticker feature on product listing page or not.</p>
<?php
	}
	/**
	 * General Settings :: Enable Sticker On Product Listing Page
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sticker_detail() {
		?>
<select id='enable_sticker_list'
	name="<?php echo $this->general_settings_key; ?>[enable_sticker_detail]">
	<option value='yes'
		<?php selected( $this->general_settings['enable_sticker_detail'], 'yes',true );?>>Yes</option>
	<option value='no'
		<?php selected( $this->general_settings['enable_sticker_detail'], 'no',true );?>>No</option>
</select>
<p class="description">Select wether you want to enable sticker feature on product detail page or not.</p>
<?php
	}
	
	/**
	 * New Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_new_product_sticker() {
		?>
<select id='enable_new_product_sticker'
	name="<?php echo $this->new_product_settings_key; ?>[enable_new_product_sticker]">
	<option value='yes'
		<?php selected( $this->new_product_settings['enable_new_product_sticker'], 'yes',true );?>>Yes</option>
	<option value='no'
		<?php selected( $this->new_product_settings['enable_new_product_sticker'], 'no',true );?>>No</option>
</select>
<p class="description">Control sticker display for products which are marked as NEW in wooCommerce.</p>
<?php
	}
	
	/**
	 * New Product Settings :: Days to New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_sticker_days() {
		
		?>
		<input type="text" id="new_product_sticker_days" name="<?php echo $this->new_product_settings_key;?>[new_product_sticker_days]" value="<?php echo $this->new_product_settings['new_product_sticker_days']?>" />

<p class="description">Specify the No of days before to be disaplay product as New (Default 10 days).</p>
<?php
	}
	
	/**
	 * New Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_position() {
		?>
	<select id='new_product_position'
		name="<?php echo $this->new_product_settings_key; ?>[new_product_position]">
		<option value='left'
			<?php selected( $this->new_product_settings['new_product_position'], 'left',true );?>>Left</option>
		<option value='right'
			<?php selected( $this->new_product_settings['new_product_position'], 'right',true );?>>Right</option>
	</select>
	<p class="description">Select the position of the sticker.</p>
	<?php
		}
	/**
	 * New Product Settings :: Custom Stickers for New Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function new_product_custom_sticker() {
	
		?>
		
	<?php
	if (get_bloginfo('version') >= 3.5)
		wp_enqueue_media();
	else {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('thickbox');
	}
	if ($this->new_product_settings ['new_product_custom_sticker'] == '')
	{				
		$image_url = "";
		echo '<img class="new_product_custom_sticker" width="125px" height="auto" />';
	}
	else
	{
		$image_url = $this->new_product_settings ['new_product_custom_sticker'];
		echo '<img class="new_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
	}
	
	
	echo '		<br/>
				<input type="hidden" name="'.$this->new_product_settings_key .'[new_product_custom_sticker]" id="new_product_custom_sticker" value="'.$image_url.'" />
				<button class="upload_img_btn button">Upload Image</button>
				<button class="remove_img_btn button">Remove Image</button>								
			'.$this->custom_sticker_script('new_product_custom_sticker'); ?>
	<p class="description">Add your own custom new product image instead of WooStickers default.</p>
	<?php
		}
	
	/**
	 * New Product Settings :: Display style On New Product
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_new_product_style() {
		?>
<select id='enable_new_product_style'
	name="<?php echo $this->new_product_settings_key; ?>[enable_new_product_style]">
	<option value='ribbon'
		<?php selected( $this->new_product_settings['enable_new_product_style'], 'ribbon',true );?>>Ribbon</option>
	<option value='round'
		<?php selected( $this->new_product_settings['enable_new_product_style'], 'round',true );?>>Round</option>
</select>
<p class="description">Select sticker type to show on New Products.</p>
	<?php }
	
	/**
	 * Sale Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sale_product_sticker() {
		?>
<select id='enable_sale_product_sticker'
	name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_product_sticker]">
	<option value='yes'
		<?php selected( $this->sale_product_settings['enable_sale_product_sticker'], 'yes',true );?>>Yes</option>
	<option value='no'
		<?php selected( $this->sale_product_settings['enable_sale_product_sticker'], 'no',true );?>>No</option>
</select>
<p class="description">Control sticker display for products which are marked as under sale in wooCommerce.</p>
<?php
	}
	/**
	 * Sale Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_position() {
		?>
		<select id='sale_product_position'
			name="<?php echo $this->sale_product_settings_key; ?>[sale_product_position]">
			<option value='left'
				<?php selected( $this->sale_product_settings['sale_product_position'], 'left',true );?>>Left</option>
			<option value='right'
				<?php selected( $this->sale_product_settings['sale_product_position'], 'right',true );?>>Right</option>
		</select>
		<p class="description">Select the position of the sticker.</p>
		<?php
	}
	/**
	 * Sale Product Settings :: Display style On Sale Product
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sale_product_style() {
		?>
<select id='enable_sale_product_style'
	name="<?php echo $this->sale_product_settings_key; ?>[enable_sale_product_style]">
	<option value='ribbon'
		<?php selected( $this->sale_product_settings['enable_sale_product_style'], 'ribbon',true );?>>Ribbon</option>
	<option value='round'
		<?php selected( $this->sale_product_settings['enable_sale_product_style'], 'round',true );?>>Round</option>
</select>
<p class="description">Select sticker type to show on Products under sale.</p>
<?php
	}
	/**
	 * Sale Product Settings :: Custom Stickers for Sale Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sale_product_custom_sticker() {
	
		?>
			
		<?php
		if (get_bloginfo('version') >= 3.5)
			wp_enqueue_media();
		else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('thickbox');
		}
		if ($this->sale_product_settings ['sale_product_custom_sticker'] == '' )
		{
			$image_url = "";
			echo '<img class="sale_product_custom_sticker" width="125px" height="auto" />';
		}
		else
		{
			$image_url = $this->sale_product_settings ['sale_product_custom_sticker'];
			echo '<img class="sale_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
		}
		echo '		<br/>
					<input type="hidden" name="'.$this->sale_product_settings_key .'[sale_product_custom_sticker]" id="sale_product_custom_sticker" value="'.$image_url.'" />
					<button class="upload_img_btn button">Upload Image</button>
					<button class="remove_img_btn button">Remove Image</button>								
				'.$this->custom_sticker_script('sale_product_custom_sticker'); ?>		
		<p class="description">Add your own custom sale product image instead of WooStickers default.</p>
		<?php
			}
	/**
	 * Sold Product Settings :: Enable Stickers
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function enable_sold_product_sticker() {
		?>
	<select id='enable_sold_product_sticker'
		name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_product_sticker]">
		<option value='yes'
			<?php selected( $this->sold_product_settings['enable_sold_product_sticker'], 'yes',true );?>>Yes</option>
		<option value='no'
			<?php selected( $this->sold_product_settings['enable_sold_product_sticker'], 'no',true );?>>No</option>
	</select>
	<p class="description">Control sticker display for products which are marked as under sold in wooCommerce.</p>
	<?php
		}
		/**
		 * Sold Product Settings :: Display style On Sold Product
		 *
		 * @return void
		 * @var No arguments passed
		 * @author Weblineindia
		 */
		public function enable_sold_product_style() {
			?>
	<select id='enable_sold_product_style'
		name="<?php echo $this->sold_product_settings_key; ?>[enable_sold_product_style]">
		<option value='ribbon'
			<?php selected( $this->sold_product_settings['enable_sold_product_style'], 'ribbon',true );?>>Ribbon</option>
		<option value='round'
			<?php selected( $this->sold_product_settings['enable_sold_product_style'], 'round',true );?>>Round</option>
	</select>
	<p class="description">Select sticker type to show on Products under sold.</p>
	<?php
		}
	/**
	 * Sold Product Settings :: Sticker Position
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_position() {
		?>
		<select id='sold_product_position'
			name="<?php echo $this->sold_product_settings_key; ?>[sold_product_position]">
			<option value='left'
				<?php selected( $this->sold_product_settings['sold_product_position'], 'left',true );?>>Left</option>
			<option value='right'
				<?php selected( $this->sold_product_settings['sold_product_position'], 'right',true );?>>Right</option>
		</select>
		<p class="description">Select the position of the sticker.</p>
		<?php
			}
	/**
	 * Sold Product Settings :: Custom Stickers for Sold Products
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function sold_product_custom_sticker() {
	
		?>
				
			<?php
			if (get_bloginfo('version') >= 3.5)
				wp_enqueue_media();
			else {
				wp_enqueue_style('thickbox');
				wp_enqueue_script('thickbox');
			}
			//print_r(CV_DEFAULT_IMAGE); die;	
			if ($this->sold_product_settings ['sold_product_custom_sticker'] == '')
			{
				$image_url = "";
				echo '<img class="sold_product_custom_sticker" width="125px" height="auto" />';
			}
			else
			{
				$image_url = $this->sold_product_settings ['sold_product_custom_sticker'];
				echo '<img class="sold_product_custom_sticker" src="'.$image_url.'" width="125px" height="auto" />';
			}
			echo '		<br/>
						<input type="hidden" name="'.$this->sold_product_settings_key .'[sold_product_custom_sticker]" id="sold_product_custom_sticker" value="'.$image_url.'" />
						<button class="upload_img_btn button">Upload Image</button>
						<button class="remove_img_btn button">Remove Image</button>								
					'.$this->custom_sticker_script('sold_product_custom_sticker'); ?>			
			<p class="description">Add your own custom sold product image instead of WooStickers default.</p>
			<?php
		}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menus() {

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

		add_options_page ( 'WLI Woocommerce Stickers', 'WOO Stickers', 'manage_options', $this->plugin_options_key, array (
				&$this,
				'plugin_options_page' 
		) );
	}

	public function plugin_options_page(){
		$tab = isset ( $_GET ['tab'] ) ? $_GET ['tab'] : $this->general_settings_key;
		?>
		<div class="wrap">
		<h2>WOO Stickers by Webline- Configuration Settings</h2>
		    			<?php $this->plugin_options_tabs(); ?>
		    			<form method="post" action="options.php">
		    				<?php wp_nonce_field( 'update-options' ); ?>
		    				<?php settings_fields( $tab ); ?>
		    				<?php do_settings_sections( $tab ); ?>
		    				<?php submit_button(); ?>
		    			</form>
		</div><?php
	}

	/**
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one.
	 * Provides the heading for the
	 * plugin_options_page method.
	 *
	 * @return void
	 * @var No arguments passed
	 * @author Weblineindia
	 */
	public function plugin_options_tabs() {
		$current_tab = isset ( $_GET ['tab'] ) ? $_GET ['tab'] : $this->general_settings_key;
		screen_icon ();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
		}
		echo '</h2>';
	}
	
	/**
	 *   custom_sticker_script() is used to upload using wordpress upload.
	 *
	 *  @since    			1.0.0
	 *
	 *  @return             script
	 *  @var                No arguments passed
	 *  @author             Weblineindia
	 *
	 */
	public function custom_sticker_script($obj_url) {
		return '<script type="text/javascript">
	    jQuery(document).ready(function() {
			var wordpress_ver = "'.get_bloginfo("version").'", upload_button;
			jQuery(".upload_img_btn").click(function(event) {
				upload_button = jQuery(this);
				var frame;
				jQuery(this).parent().children("img").attr("src","").show();					
				if (wordpress_ver >= "3.5") {
					event.preventDefault();
					if (frame) {
						frame.open();
						return;
					}
					frame = wp.media();
					frame.on( "select", function() {					
						// Grab the selected attachment.
						var attachment = frame.state().get("selection").first();
						frame.close();
						if (upload_button.parent().prev().children().hasClass("cat_list")) {
							upload_button.parent().prev().children().val(attachment.attributes.url);
							upload_button.parent().prev().prev().children().attr("src", attachment.attributes.url);
						}
						else
						{
							jQuery("#'.$obj_url.'").val(attachment.attributes.url);
							jQuery(".'.$obj_url.'").attr("src",attachment.attributes.url);
						}
					});
					frame.open();
				}
				else {
					tb_show("", "media-upload.php?type=image&amp;TB_iframe=true");
					return false;
				}
			});
	
			jQuery(".remove_img_btn").click(function() {
				jQuery("#'.$obj_url.'").val("");
				if(jQuery(this).parent().children("img").attr("src")!="undefined")	
				{ 
					jQuery(this).parent().children("img").attr("src","").hide();
					jQuery(this).parent().siblings(".title").children("img").attr("src"," ");
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(""); 
				}	
				else
				{
					jQuery(this).parent().children("img").attr("src","").hide();
				}						
				return false;
			});
	
			if (wordpress_ver < "3.5") {
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					if (upload_button.parent().prev().children().hasClass("cat_list")) {
						upload_button.parent().prev().children().val(imgurl);
						upload_button.parent().prev().prev().children().attr("src", imgurl);
					}
					else
					{
						jQuery("#'.$obj_url.'").val(imgurl);
						jQuery(".'.$obj_url.'").attr("src",imgurl);
					}
					tb_remove();
				}
			}
	
			jQuery(".editinline").click(function(){
			    var tax_id = jQuery(this).parents("tr").attr("id").substr(4);
			    var thumb = jQuery("#tag-"+tax_id+" .thumb img").attr("src");
				if (thumb != "") {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val(thumb);
				} else {
					jQuery(".inline-edit-col :input[name=\''.$obj_url.'\']").val("");
				}
				jQuery(".inline-edit-col .title img").attr("src",thumb);
			    return true;
			});
	    });
	</script>';
	}



}
