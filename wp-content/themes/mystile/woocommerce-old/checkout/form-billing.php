<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */
?>
<style>
	#billing_last_name_field + .clear {display:none;}
</style>
<div class="woocommerce-billing-fields">
	<?php if ( WC()->cart->ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php _e( 'Billing Details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

		<?php if ( $checkout->enable_guest_checkout ) : ?>

			<p class="form-row form-row-wide create-account">
				<input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

			<div class="create-account">

				<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></p>

				<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<div class="clear"></div>

			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

	<?php endif; ?>
</div>

<?php 			/*AjaxZip3.zip2addr(this,'','billing_state','billing_city','billing_address_1','billing_address_2');*/
 /*<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
<script>
 jQuery(function () {
		
		jQuery("#billing_postcode").keyup(function(){
							AjaxZip3.zip2addr('billing_postcode', '', 'billing_state', 'billing_city' ); 
							setTimeout(function(){ jQuery("#billing_state").trigger('change'); },500);
							 


});

	    });
</script> */?>
<script type="text/javascript" src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
		<script type="text/javascript">
		jQuery(function(){
			var _billing_handler = function(){
				// AjaxZip3.zip2addr('billing_postcode', '', 'billing_state', 'billing_city', '', 'billing_address_1' );
				AjaxZip3.zip2addr('billing_postcode', '', 'billing_state', 'billing_city' );
				// jQuery("#billing_state").trigger("liszt:updated").trigger("chosen:updated");
				setTimeout(function(){
					jQuery("#billing_state").trigger('change');
				},500);
			};
			/*
			jQuery('#billing_postcode').change(function(){
				jQuery("#billing_state option").filter(function() {
					return !this.value || jQuery.trim(this.value).length == 0;
				}).remove();
			});
			*/
			jQuery('#billing_postcode').change(_billing_handler).keyup(_billing_handler);	
			
			var _shipping_handler = function(){
				// AjaxZip3.zip2addr('shipping_postcode', '', 'shipping_state', 'shipping_city', '', 'shipping_address_1' );
				AjaxZip3.zip2addr('shipping_postcode', '', 'shipping_state', 'shipping_city' );
				// jQuery("#shipping_state").trigger("liszt:updated").trigger("chosen:updated");
				setTimeout(function(){
					jQuery("#shipping_state").trigger('change');
				},500);
			};
			/*
			jQuery('#shipping_postcode').change(function(){
				jQuery("#shipping_state option").filter(function() {
					return !this.value || jQuery.trim(this.value).length == 0;
				}).remove();
			});
			*/
			jQuery('#shipping_postcode').change(_shipping_handler).keyup(_shipping_handler);
			
			
		});
		</script>

