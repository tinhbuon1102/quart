<?php
/*
Plugin Name: WooCommerce Extra Fee Options PRO
Plugin URI: http://terrytsang.com/shop/shop/woocommerce-extra-fee-option-pro/
Description: Allow you to add multiple extra fee with flexible options to WooCommerce
Version: 1.0.3
Author: Terry Tsang
Author URI: http://shop.terrytsang.com
*/

/*  
 * Copyright 2012-2014 Terry Tsang (email: terrytsang811@gmail.com)
 * License: Unlimited Sites
*/

// Define plugin name
define('wc_plugin_name_extra_fee_option_pro', 'WooCommerce Extra Fee Option PRO');

// Define plugin version
define('wc_version_extra_fee_option_pro', '1.0.3');


if(!class_exists('WooCommerce_Extra_Fee_Option_PRO')){
	class WooCommerce_Extra_Fee_Option_PRO{

		public static $plugin_prefix;
		public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		
		var $textdomain;
		var $yes_no;
		var $cost_type;
		var $max_limit_field;
		var $field_options;
		var $custom_field_name;
		var $options_extra_fee_option_pro;
		var $new_custom_fields_array;
		
		/**
		 * Gets things started by adding an action to initialize this plugin once
		 * WooCommerce is known to be active and initialized
		 */
		public function __construct(){
			load_plugin_textdomain('wc-extra-fee-option-pro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			
			self::$plugin_prefix = 'wc_extra_fee_option_pro_';
			self::$plugin_basefile = plugin_basename(__FILE__);
			self::$plugin_url = plugin_dir_url(self::$plugin_basefile);
			self::$plugin_path = trailingslashit(dirname(__FILE__));
			
			$this->textdomain = 'wc-extra-fee-option-pro';
			$this->yes_no = array(1 => 'Yes', 0 => 'No');
			$this->types = array('fixed' => 'Fixed Rate', 'percentage' => 'Cart % Fee');
			$this->max_limit_field = 100;
			$this->field_options = array('enabled', 'label', 'amount', 'type', 'taxable', 'minorder', 'cartitem_start', 'cartitem_end', 'payment', 'shipping' );
			$this->custom_field_name = 'newfield';
			
			$this->options_extra_fee_option_pro = array(
				'extra_fee_option_pro_enabled' => '',
				'extra_fee_option_pro_label' => 'Extra Fee',
				'extra_fee_option_pro_amount' => 0,
				'extra_fee_option_pro_type' => 'fixed',
				'extra_fee_option_pro_taxable' => false,
				'extra_fee_option_pro_minorder' => 0,
				'extra_fee_option_pro_cartitem_start' => '',
				'extra_fee_option_pro_cartitem_end' => '',
				'extra_fee_option_pro_payment' => '',
				'extra_fee_option_pro_shipping' => '',
			);

			$this->new_custom_fields_array = array();
			
			add_action('woocommerce_init', array(&$this, 'init'));
		}

		/**
		 * Initialize extension when WooCommerce is active
		 */
		public function init(){
			//get all saved custom extra fee options
			$this->new_custom_fields_array = $this->get_custom_extra_fee_options();
						
			//delete option
			if( isset($_GET['action']) )
			{
				if($_GET['action'] == 'delete' && isset($_GET['option']))
				{
					$option = $_GET['option'];
					$option_name = self::$plugin_prefix.$option;
			
					foreach($this->field_options as $field_option)
					{
						delete_option($option_name . '_' . $field_option);
					}
				}
			}
			
			//add menu link for the plugin (backend)
			add_action( 'admin_menu', array( &$this, 'add_menu_extra_fee_option_pro' ) );
			
			add_action('admin_init', array( &$this, 'tsang_plugin_admin_init') );
			
			//add_action( 'woocommerce_before_calculate_totals', array( &$this, 'woo_add_extra_fee_pro') );
			add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'woo_add_extra_fee_pro') );
			
			add_action( 'woocommerce_review_order_after_submit' , array( &$this,'woo_autoload_js' ) );
		}
		
		function tsang_plugin_admin_init() {
			/* Register admin stylesheet. */
			wp_register_style( 'tsangPluginStylesheet', plugins_url('css/admin.css', __FILE__) );
		}
			
		function tsang_plugin_admin_styles() {
			/*
			 * It will be called only on your plugin admin page, enqueue our stylesheet here
			*/
			wp_enqueue_style( 'tsangPluginStylesheet' );
		}
		
		function woo_autoload_js(){
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
			        $(document.body).on('change', 'input[name="payment_method"]', function() {
			                $('body').trigger('update_checkout');
			                $.ajax( $fragment_refresh );
			        });
			});
		</script>
	<?php
	}
	
	/**
	 * Set the extra fee with min order total limit
	 */
	public function woo_add_extra_fee_pro() {
		global $woocommerce;
		
		//get cart total from session
		// $total = 0;
		// $total_item = 0;
		// $session_cart = $woocommerce->session->cart;
		// if(count($session_cart) > 0) {
		// 	foreach($session_cart as $cart_product){
		// 		$total = $total + $cart_product['line_subtotal'];
		// 		$total_item = $total_item + $cart_product['quantity'];
		// 	}
		// }

		$total = $woocommerce->cart->cart_contents_total;
		$total_item = $woocommerce->cart->cart_contents_count;
		//echo 'total :'.$total.' -  items :'.$total_item;
		
		$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
		$current_gateway = '';
		$default_gateway = get_option( 'woocommerce_default_gateway' );
		
		//chosen payment method
		if ( isset( $woocommerce->session->chosen_payment_method ) && isset( $available_gateways[ $woocommerce->session->chosen_payment_method ] ) ) {
			$current_gateway = $available_gateways[ $woocommerce->session->chosen_payment_method ];
		} elseif ( isset( $available_gateways[ $default_gateway ] ) ) {
			$current_gateway = $available_gateways[ $default_gateway ];
		} else {
			$current_gateway = current( $available_gateways );
		}
		
		$available_methods = $woocommerce->shipping->load_shipping_methods();
		$current_method = '';
		$default_method = get_option( 'woocommerce_default_shipping_method' );
		
		//chosen shipping method
		if ( isset( $woocommerce->session->chosen_shipping_method ) && isset( $available_methods[ $woocommerce->session->chosen_shipping_method ] ) ) {
			$current_method = $available_methods[ $woocommerce->session->chosen_shipping_method ];
		} elseif ( isset( $available_methods[ $default_method ] ) ) {
			$current_method = $available_methods[ $default_method ];
		} else {
			$current_method = current( $available_methods );
		}

		foreach($this->new_custom_fields_array as $field_name => $field_array){

			foreach($this->field_options as $field_option){
				$$field_option = esc_attr($this->get_setting($field_name, $field_option));
			}
			
			if($enabled){
				$boolContinue = false;
				
				//check for payment
				if($payment == "" || ($payment != "" && $payment == $current_gateway->id))
				{
					//check for payment
					if($shipping == "" || ($shipping != "" && $shipping == $current_method->id))
					{
						if($cartitem_start == "" && $cartitem_end == ""){
							$boolContinue = true;
						} else {
							if(($cartitem_start != "" && $cartitem_start <= $total_item) && ($cartitem_end != "" && $cartitem_end >= $total_item)){
								$boolContinue = true;
							} 
						}
						
						//check for fee type (fixed fee or cart %)
						if($type == 'percentage'){
							$amount = ($amount/100) * $total;
						} 
						
						if($boolContinue){
							//if cart total less or equal than $min_order, add extra fee
							if($minorder > 0){
								if($total <= $minorder) {
									$woocommerce->cart->add_fee( __($label, 'woocommerce'), $amount, $taxable );
								}
							} else {
								$woocommerce->cart->add_fee( __($label, 'woocommerce'), $amount, $taxable );
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * Add a menu link to the woocommerce section menu
	 */
	function add_menu_extra_fee_option_pro() {
		$wc_page = 'woocommerce';
		$comparable_settings_page = add_submenu_page( $wc_page , __( 'Extra Fee Options PRO', $this->textdomain ), __( 'Extra Fee Options PRO', $this->textdomain ), 'manage_options', 'wc-extra-fee-option-pro', array(
				&$this,
				'settings_page_extra_fee_option_pro'
		));
		
		add_action( 'admin_print_styles-' . $comparable_settings_page, array( &$this, 'tsang_plugin_admin_styles') );
	}
	
	/**
	 * Create the settings page content
	 */
	public function settings_page_extra_fee_option_pro() {
		global $woocommerce;
	
		// If form was submitted
		if ( isset( $_POST['submitted'] ) )
		{
			check_admin_referer( $this->textdomain );

			//step 1: update existing rows
			for($i = 0; $i < $this->max_limit_field; $i++)
			{
				$currentfield = self::$plugin_prefix . 'newfield' . $i;

				foreach($this->field_options as $field_option)
				{
					$optionName = $currentfield . '_' . $field_option;
					
					if(isset($_POST[$currentfield][$field_option]))
					{
						$valueField = esc_attr($_POST[$currentfield][$field_option][0]);
						
						$option = get_option($optionName);
						
						update_option($optionName, $valueField);
					}
					
				}

			}

			//step 2: new added custom new fields
			$newfield = self::$plugin_prefix . 'newfield';
			$field_name = 'newfield';
			
			if(isset( $_POST[$newfield] ) && $_POST[$newfield] && count($_POST[$newfield]) > 0)
			{
				$total_index = count($_POST[$newfield]);
				
				if($total_index > 0)
				{
					for($i = 0; $i < $total_index; $i++)
					{
						$latest_index = $this->get_latest_newfield_index();
							
						foreach($this->field_options as $field_option)
						{
							if(isset($_POST[$newfield][$field_option][$i]))
							{
								$valueField = esc_attr($_POST[$newfield][$field_option][$i]);
						
								$option = get_option(self::$plugin_prefix . $field_name . $latest_index . '_' . $field_option);
								
								if(!$option)
								{
									update_option(self::$plugin_prefix . $field_name . $latest_index . '_' . $field_option, $valueField);
								}
							}
						}
					}
				}
			}
				
			// Show message
			echo '<div id="message" class="updated fade"><p>' . __( 'WooCommerce Extra Fee Options saved.', $this->textdomain ) . '</p></div>';
		}
	
		$actionurl = $_SERVER['REQUEST_URI'];
		$nonce = wp_create_nonce( $this->textdomain );
	
		// Configuration Page
		$this->new_custom_fields_array = $this->get_custom_extra_fee_options();
	
		?>
		<div id="icon-options-general" class="icon32"></div>
		<h3><?php _e( 'Extra Fee Options PRO', $this->textdomain); ?></h3>
		
		<form action="<?php echo $actionurl; ?>" method="post">
		<table width="98%" cellspacing="2">
		<tr>
			<td>
				<p><b>WooCommerce Extra Fee Options PRO</b> is a premium woocommerce plugin developed by <a href="http://shop.terrytsang.com" target="_blank" title="Terry Tsang - a PHP Developer and Wordpress Consultant">Terry Tsang</a>. This plugin aims to add extra fee minimum order with more options for WooCommerce.</p>
				<p align="right"><a href="http://shop.terrytsang.com" class="css3button orange" target="_blank" title="Premium &amp; Free Plugins for E-Commerce by Terry Tsang">Get more plugins</a></p>
			</td>
		</tr>
		
		<tr>
			<td valign="top">
				
				<table id="block-extra" class="wc_payment widefat" cellspacing="0">
				<tbody>
				<thead>
					<th width="5%"><?php _e( 'Enable', $this->textdomain ); ?></th>
					<th><?php _e( 'Label', $this->textdomain ); ?></th>
					<th><?php _e( 'Amount', $this->textdomain ); ?></th>
					<th><?php _e( 'Type', $this->textdomain ); ?></th>
					<th width="5%"><?php _e( 'Taxable', $this->textdomain ); ?></th>
					<th><?php _e( 'Min Order', $this->textdomain ); ?></th>
					<th><?php _e( 'Cart Items', $this->textdomain ); ?></th>
					<th><?php _e( 'Payment', $this->textdomain ); ?></th>
					<th><?php _e( 'Shipping', $this->textdomain ); ?></th>
					<th width="3%"></th>
				</thead>
				<tbody class="ui-sortable">
				<?php 
					foreach($this->new_custom_fields_array as $field_name => $field_array): 
					
					foreach($this->field_options as $field_option){
						$$field_option = esc_attr($this->get_setting($field_name, $field_option));
						//echo 'field option :'.$field_option.' - '.$$field_option.'<br>';
					}
					
					$current_field = self::$plugin_prefix . $field_name;
					
				?>
					<tr align="left">
						<td>
							<select name="<?php echo $current_field; ?>[enabled][]">
							<?php foreach($this->yes_no as $choice => $choice_name): ?>
								<?php if($enabled == $choice){ ?>
									<option value="<?php echo $choice?>" selected="selected"><?php echo $choice_name; ?></option>
								<?php } else { ?>
									<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>
								<?php } ?>
							<?php endforeach; ?>
							</select>
						</td>
						<td><input name="<?php echo $current_field; ?>[label][]" id="<?php echo $current_field; ?>[label][]" type="text" value="<?php echo $label; ?>" /></td>
						<td><input name="<?php echo $current_field; ?>[amount][]" id="<?php echo $current_field; ?>[amount][]" type="text" value="<?php echo $amount; ?>" size="5" /></td>
						<td>
							<select name="<?php echo $current_field; ?>[type][]">
							<?php foreach($this->types as $choice => $choice_name): ?>
								<?php if($type == $choice){ ?>
									<option value="<?php echo $choice?>" selected="selected"><?php echo $choice_name; ?></option>
								<?php } else { ?>
									<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>
								<?php } ?>
							<?php endforeach; ?>
							</select>
						</td>
						<td>
							<select name="<?php echo $current_field; ?>[taxable][]">
							<?php foreach($this->yes_no as $choice => $choice_name): ?>
								<?php if($taxable == $choice){ ?>
									<option value="<?php echo $choice?>" selected="selected"><?php echo $choice_name; ?></option>
								<?php } else { ?>
									<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>
								<?php } ?>
							<?php endforeach; ?>
							</select>
						</td>
						<td><input name="<?php echo $current_field; ?>[minorder][]" id="<?php echo $current_field; ?>[minorder][]" type="text" value="<?php echo $minorder; ?>" size="5" /></td>
						<td>
							<input name="<?php echo $current_field; ?>[cartitem_start][]" id="<?php echo $current_field; ?>[cartitem_start][]" type="text" value="<?php echo $cartitem_start; ?>" size="2" /> to
							<input name="<?php echo $current_field; ?>[cartitem_end][]" id="<?php echo $current_field; ?>[cartitem_end][]" type="text" value="<?php echo $cartitem_end; ?>" size="2" />
						</td>
						<td>
							<select name="<?php echo $current_field; ?>[payment][]">
								<option value="">Select</option>
								<?php
							    	foreach ( $woocommerce->payment_gateways->payment_gateways() as $gateway ) {
				
								    $default_gateway = esc_attr( get_option('woocommerce_default_gateway') );
								    if($gateway->id == $payment){
								?>
									<option value="<?php echo $gateway->id; ?>" selected="selected"><?php echo $gateway->get_title(); ?></option>
								<?php
									} else {
								?>
									<option value="<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?></option>
							    <?php
							    	}
								}
							    ?>
							</select>
						</td>
						<td>
							<select name="<?php echo $current_field; ?>[shipping][]">
								<option value="">Select</option>
								<?php
							    	foreach ( $woocommerce->shipping->load_shipping_methods() as $method ) {
				
								    $default_shipping_method = esc_attr( get_option('woocommerce_default_shipping_method') );
								    if($method->id == $shipping){
								?>
									<option value="<?php echo $method->id; ?>" selected="selected"><?php echo $method->get_title(); ?></option>
							    <?php
							   	 	} else {
								?>
									<option value="<?php echo $method->id; ?>"><?php echo $method->get_title(); ?></option>
							    <?php 
							    	}
								}
							    ?>
							</select>
						</td>
						<td><a href="?page=wc-extra-fee-option-pro&option=<?php echo $field_name; ?>&action=delete"><input id="removeField" type="button" value="X" title="remove this record"></a></td>
					</tr>
					<?php endforeach ?>
					<tr>
					<td colspan="10">
					
					<input id="addRow" type="button" value="Add New" class="button-secondary" />

					<script>
					jQuery(document).ready(function(){
						 var newRow = '\
							 <tr align="left">\
								<td>\
									<select name="wc_extra_fee_option_pro_newfield[enabled][]">\
										<?php foreach($this->yes_no as $choice => $choice_name): ?>
											<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>\
										<?php endforeach; ?>
									</select>\
								</td>\
								<td><input name="wc_extra_fee_option_pro_newfield[label][]" id="wc_extra_fee_option_pro_newfield[amount][]" type="text" value="" /></td>\
								<td><input name="wc_extra_fee_option_pro_newfield[amount][]" id="wc_extra_fee_option_pro_newfield[amount][]" type="text" value="" size="5" /></td>\
								<td>\
									<select name="wc_extra_fee_option_pro_newfield[type][]">\
										<?php foreach($this->types as $choice => $choice_name): ?>
											<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>\
										<?php endforeach; ?>
									</select>\
								</td>\
								<td>\
									<select name="wc_extra_fee_option_pro_newfield[taxable][]">\
										<?php foreach($this->yes_no as $choice => $choice_name): ?>
											<option value="<?php echo $choice?>"><?php echo $choice_name; ?></option>\
										<?php endforeach; ?>
									</select>\
								</td>\
								<td><input name="wc_extra_fee_option_pro_newfield[minorder][]" id="wc_extra_fee_option_pro_newfield[minorder][]" type="text" value="" size="5" /></td>\
								<td>\
									<input name="wc_extra_fee_option_pro_newfield[cartitem_start][]" id="wc_extra_fee_option_pro_newfield[cartitem_start][]" type="text" value="" size="2" /> to\
									<input name="wc_extra_fee_option_pro_newfield[cartitem_end][]" id="wc_extra_fee_option_pro_newfield[cartitem_end][]" type="text" value="" size="2" />\
								</td>\
								<td>\
									<select name="wc_extra_fee_option_pro_newfield[payment][]">\
										<option value="">Select</option>\
										<?php
									    	foreach ( $woocommerce->payment_gateways->payment_gateways() as $gateway ) {
						
										    	$default_gateway = esc_attr( get_option('woocommerce_default_gateway') );
										?>
										<option value="<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?></option>\
										<?php
									    	}
									    ?>\
									</select>\
								</td>\
								<td>\
									<select name="wc_extra_fee_option_pro_newfield[shipping][]">\
										<option value="">Select</option>\
										<?php
									    	foreach ( $woocommerce->shipping->load_shipping_methods() as $method ) {
						
										    	$default_shipping_method = esc_attr( get_option('woocommerce_default_shipping_method') );
										?>
										<option value="<?php echo $method->id; ?>"><?php echo $method->get_title(); ?></option>\
									    <?php	
											}
									    ?>
									</select>\
								</td>\
								<td><input id="removeRow" type="button" value="X" class="button-secondary" /></td>\
							</tr>';

						 jQuery('#addRow').click(function(){
					      jQuery('#block-extra').append(newRow);
						  reIndex();
						 })

						 jQuery('#removeRow').live('click', function(){
					      jQuery(this).closest('tr').remove();
						  reIndex();
						 })

						 function reIndex(){
						   jQuery('#block-extra').find('.index').each(function(i){
						   jQuery(this).html(i+2);
						  })
						 }

						})
						</script>

		    
					</td>
				</tr>
				</tbody>

				</table>
				
			
			</td>
		</tr>
		<tr>
			<td>
				<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', $this->textdomain); ?>" id="submitbutton" />
				<input type="hidden" name="submitted" value="1" /> 
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
			</td>
		</tr>
		</table>
		</form>
		
		
		<br />
		
	<?php
		}
		
		/**
		 * Get the setting options
		 */
		function get_options() {
			$field_name = 'newfield';
			
			foreach($this->options_extra_fee_option_pro as $field => $value)
			{
				$array_options[$field] = get_option( $field );
			}
				
			return $array_options;
		}
		
		/**
		 * Get the array for the custom fields
		 */
		public function get_custom_extra_fee_options()
		{
			$array_result = array();
		
			for($i = 0; $i < $this->max_limit_field; $i++)
			{
				$field_name = $this->custom_field_name.$i;
			
				$label = $this->get_setting($field_name, 'label');
				if($label)
				{
					$array_result[$field_name] = array();
				
					foreach($this->field_options as $field_option)
					{
						$value_field = $this->get_setting($field_name, $field_option);
							
						$array_result[$field_name][$field_option] = $value_field;
					}
				}
			}
		
			return $array_result;
		}
		
		/**
		 * Get the content for an option
		 */
		public function get_setting( $field_name, $name ) {
			return get_option( self::$plugin_prefix . $field_name . '_' . $name);
		}
		
		/**
		 * get latest index for newfield
		 */
		public function get_latest_newfield_index()
		{
			$index = 0;
		
			for($i = 0; $i < $this->max_limit_field; $i++)
			{
				$option = get_option(self::$plugin_prefix . $this->custom_field_name . $i . '_amount');
		
				if(!$option)
				{
					return $i;
				}
			}
		
			return $index;
		}

	}//end class
		
}//if class does not exist

$woocommerce_extra_fee_option_pro = new WooCommerce_Extra_Fee_Option_PRO();

?>