<?php
/*
Plugin Name: YITH Payment Method Restrictions for WooCommerce Premium
Plugin URI: https://yithemes.com/themes/plugins/yith-payment-method-restrictions-for-woocommerce/
Description: <code><strong>YITH Payment Method Restrictions for WooCommerce Premium</strong></code> allows you to hide specific payment methods based on the products in cart or on user's origin.  <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
Author: YITH
Text Domain: yith-payment-method-restrictions-for-woocommerce
Version: 1.1.5
Author URI: http://yithemes.com/
WC requires at least: 3.0.0
WC tested up to: 3.6.2
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! function_exists( 'yith_wcpmr_install_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if WooCommerce is deactivated
     *
     * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function yith_wcpmr_install_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php _ex( 'YITH Payment Method Restrictions is enabled but not effective. It requires WooCommerce in order to work.', 'Alert Message: WooCommerce requires', 'yith-auctions-for-woocommerce' ); ?></p>
        </div>
        <?php
    }
}


/**
 * Check if WooCommerce is activated
 *
 * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
 * @since 1.0
 * @return void
 * @use admin_notices hooks
 */
if( ! function_exists( 'yith_wcpmr_install' ) ) {

    function yith_wcpmr_install()
    {

        if (!function_exists('WC')) {
            add_action('admin_notices', 'yith_wcpmr_install_woocommerce_admin_notice');
        } else {
            do_action('yith_wcpmr_init');
        }
    }

    add_action( 'plugins_loaded', 'yith_wcpmr_install', 11 );
}


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';                                      
}
yit_deactive_free_version( 'YITH_WCPMR_FREE_INIT', plugin_basename( __FILE__ ) );

/* === DEFINE === */
! defined( 'YITH_WCPMR_VERSION' )            && define( 'YITH_WCPMR_VERSION', '1.1.5' );
! defined( 'YITH_WCPMR_INIT' )               && define( 'YITH_WCPMR_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCPMR_SLUG' )               && define( 'YITH_WCPMR_SLUG', 'yith-payment-method-restrictions-for-woocommerce' );
! defined( 'YITH_WCPMR_SECRETKEY' )          && define( 'YITH_WCPMR_SECRETKEY', 'KCPOKg9DTFpSTs5UPbvd' );
! defined( 'YITH_WCPMR_FILE' )               && define( 'YITH_WCPMR_FILE', __FILE__ );
! defined( 'YITH_WCPMR_PATH' )               && define( 'YITH_WCPMR_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCPMR_URL' )                && define( 'YITH_WCPMR_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCPMR_ASSETS_URL' )         && define( 'YITH_WCPMR_ASSETS_URL', YITH_WCPMR_URL . 'assets/' );
! defined( 'YITH_WCPMR_TEMPLATE_PATH' )      && define( 'YITH_WCPMR_TEMPLATE_PATH', YITH_WCPMR_PATH . 'templates/' );
! defined( 'YITH_WCPMR_WC_TEMPLATE_PATH' )   && define( 'YITH_WCPMR_WC_TEMPLATE_PATH', YITH_WCPMR_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCPMR_OPTIONS_PATH' )       && define( 'YITH_WCPMR_OPTIONS_PATH', YITH_WCPMR_PATH . 'panel' );
! defined( 'YITH_WCPMR_PREMIUM' )            && define( 'YITH_WCPMR_PREMIUM', true );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCPMR_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WCPMR_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCPMR_PATH  );


function yith_wcpmr_init_premium() {
    load_plugin_textdomain( 'yith-payment-method-restrictions-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


    if ( ! function_exists( 'YITH_Payment_Restrictions' ) ) {
        /**
         * Unique access to instance of YITH_Payment_Restriction class
         *
         * @return YITH_Payment_Restrictions
         * @since 1.0.0
         */
        function YITH_Payment_Restrictions() {

            require_once( YITH_WCPMR_PATH . 'includes/class.yith-wcpmr-payment-restrictions.php' );
            if ( defined( 'YITH_WCPMR_PREMIUM' ) && file_exists( YITH_WCPMR_PATH . 'includes/class.yith-wcpmr-payment-restrictions-premium.php' ) ) {

                require_once( YITH_WCPMR_PATH . 'includes/class.yith-wcpmr-payment-restrictions-premium.php' );
                return YITH_Payment_Restrictions_Premium::instance();
            }
            return YITH_Payment_Restrictions::instance();
        }
    }

   // Let's start the game!
    YITH_Payment_Restrictions();
}

add_action( 'yith_wcpmr_init', 'yith_wcpmr_init_premium' );