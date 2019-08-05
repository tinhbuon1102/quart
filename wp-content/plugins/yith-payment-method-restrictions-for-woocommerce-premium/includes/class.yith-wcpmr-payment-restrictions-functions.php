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
 * @class      YITH_Payment_Restrictions_Functions
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Payment_Restrictions_Functions' ) ) {
    /**
     * Class YITH_Payment_Restrictions_Functions
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions_Functions
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

        }

        /**
         * Is payment method disabled
         *
         * Return if the payment gateways will be disabled
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function is_payment_method_disabled( $gateway,$rules,$order_cart = '' ) {

            foreach ( $rules as $rule ) {
                $payment_restriction_rule = get_post_meta($rule->ID, 'yith_wcpmr_payment_restriction', true);
                $conditions = $payment_restriction_rule['conditions'];

                $remove_payment_method = true;
                foreach ($conditions as $condition) {
                    if ($remove_payment_method == false) {
                        return false;
                    }

                    $remove_payment_method = $this->restriction_by_products($condition['restriction_by'], $condition['products_selected'], $order_cart);
                }

                if ($remove_payment_method) {
                    return $payment_restriction_rule;

                } else {
                    return false;
                }
            }
        }
        /**
         * Restriction by product
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_products( $restriction_by,$selected_products,$order_cart ) {
            $item_cart = ($order_cart) ? $order_cart['items'] : WC()->cart->get_cart();

            $products_in_cart = array();
            foreach ( $item_cart as $cart_item_key => $cart_item ) {
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $products_in_cart[] = $product_id;
            }

            switch( $restriction_by ){
                case 'include_or':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_products as $product ){
                            if( in_array( $product, $products_in_cart ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        return false;
                    }

                    break;
                case 'include_and':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        foreach( (array) $selected_products as $product ){
                            if( ! in_array( $product, $products_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        return false;
                    }

                    break;
                case 'exclude_or':

                    if( ! empty( $selected_products ) && ! empty( $products_in_cart ) ){
                        foreach( (array) $selected_products as $product ){
                            if( in_array( $product, $products_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_products ) ){
                        return false;
                    }

                    break;
                default :
                    return false;
            }
            return true;
        }
    }
}