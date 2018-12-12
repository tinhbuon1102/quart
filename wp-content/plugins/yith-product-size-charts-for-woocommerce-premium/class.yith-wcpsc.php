<?php
/**
 * Main class
 *
 * @author  Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPSC' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPSC' ) ) {
    /**
     * YITH Product Size Charts for WooCommerce
     *
     * @since 1.0.0
     */
    class YITH_WCPSC {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPSC
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return YITH_WCPSC
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @return mixed| YITH_WCPSC_Admin | YITH_WCPSC_Frontend
         * @since 1.0.0
         */
        protected function __construct() {
            // Load Plugin Framework
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

            if ( is_admin() && !defined( 'DOING_AJAX' ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( !isset( $_REQUEST[ 'context' ] ) || ( isset( $_REQUEST[ 'context' ] ) && $_REQUEST[ 'context' ] !== 'frontend' ) ) ) ) {
                YITH_WCPSC_Admin();
            }
            
            YITH_WCPSC_Frontend();
        }


        /**
         * Load Plugin Framework
         *
         * @since  1.0
         * @access public
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function plugin_fw_loader() {
            if ( !defined( 'YIT_CORE_PLUGIN' ) ) {
                global $plugin_fw_data;
                if ( !empty( $plugin_fw_data ) ) {
                    $plugin_fw_file = array_shift( $plugin_fw_data );
                    require_once( $plugin_fw_file );
                }
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCPSC class
 *
 * @return YITH_WCPSC|YITH_WCPSC_Premium
 * @since 1.0.0
 */
function YITH_WCPSC() {
    return YITH_WCPSC::get_instance();
}