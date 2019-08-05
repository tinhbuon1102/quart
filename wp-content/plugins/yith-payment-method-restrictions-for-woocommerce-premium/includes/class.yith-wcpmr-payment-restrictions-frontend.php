<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCPMR_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Payment_Restrictions_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Payment_Restrictions_Frontend' ) ) {



    /**
     * Class YITH_Payment_Restrictions_Frontend
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions_Frontend
    {
        /**
         * Single instance of the class
         *
         * @var \YITH_Payment_Restrictions_Frontend
         * @since 1.0.0
         */
        protected static $instance;

        public static function get_instance()
        {
            $self = __CLASS__ . (class_exists(__CLASS__ . '_Premium') ? '_Premium' : '');

            if (is_null($self::$instance)) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct()
        {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_filter('woocommerce_available_payment_gateways',array($this,'available_payment_gateways'));
        }

        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Frontend
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function enqueue_scripts()
        {
            do_action('yith_wcpmr_enqueue_fontend_scripts');

        }

        /**
         * Available Payment Gateways
         *
         * Return the payment gateways available based on restriction rule
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function available_payment_gateways($available_gateways) {

            if( is_checkout() ) {
                $functions = YITH_Payment_Restrictions()->functions;

                foreach ($available_gateways as $gateway => $value) {

                    $rules = yith_wcpmr_get_rules_by_payment_method($gateway);
                    if ($rules) {
                        $payment_restriction_rule = $functions->is_payment_method_disabled($gateway, $rules);
                        if (is_array($payment_restriction_rule)) {
                            unset($available_gateways[$gateway]);
                        }
                    }
                }
            }
            return $available_gateways;
        }
    }
}