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
 * @class      YITH_Payment_Restrictions_Functions_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Payment_Restrictions_Functions_Premium' ) ) {
    /**
     * Class YITH_Payment_Restrictions_Functions_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions_Functions_Premium extends YITH_Payment_Restrictions_Functions
    {
        /**
         * Single instance of the class
         *
         * @var \YITH_Payment_Restrictions_Functions_Premium
         * @since 1.0.0
         */
        protected static $instance;

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
                $payment_restriction_rule = get_post_meta($rule->ID,'yith_wcpmr_payment_restriction',true);
                $conditions = $payment_restriction_rule['conditions'];
                $remove_payment_method = true;
                foreach ( $conditions as $condition ) {
                    if ( $remove_payment_method == false ) {
                        //return false;
                        break;
                    }
                    switch($condition['type_restriction']) {

                        case 'price' :
                            $remove_payment_method = $this->restriction_by_price($condition['restriction_by_price'],$condition['price'],$order_cart );

                            break;
                        case 'category' :

                            $remove_payment_method = $this->restriction_by_categories($condition['restriction_by'],$condition['categories_selected'],$order_cart );
                            break;
                        case 'tag' :
                            $remove_payment_method = $this->restriction_by_tags($condition['restriction_by'],$condition['tags_selected'],$order_cart);

                            break;
                        case 'product' :
                            $remove_payment_method = $this->restriction_by_products($condition['restriction_by'],$condition['products_selected'],$order_cart );

                            break;
                        case 'geolocalization' :

                            $remove_payment_method = $this->restriction_by_geolocalization($condition['restriction_by'],$condition['geolocalization']);
                            break;

                        case 'role' :
                            $remove_payment_method = $this->restriction_by_role($condition['restriction_by'],$condition['role']);
                            break;

                        default :
                            $remove_payment_method = apply_filters('yith_wcpmr_is_payment_method_disabled',false,$condition['type_restriction'],$condition,$gateway);
                            break;
                    }
                }
                if ( $remove_payment_method )  {
                    if( 'bacs' == $gateway ) {
                        $radio_button_select = get_post_meta($rule->ID,'yith_wcpmr_select_radio_button',true);
                        if( 'remove_payment_method' != $radio_button_select ) {
                            return $rule->ID;
                        } else {
                            return $payment_restriction_rule;
                        }
                    }else {
                        return $payment_restriction_rule;
                    }

                }
            }
            return false;
        }

        /**
         * Restriction by price
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_price( $restriction_by, $threshold, $order_cart ) {


            if ( (is_checkout() && !is_wc_endpoint_url( 'order-pay' )) || apply_filters('yith_wcpmr_restriction_by',false) ) {

                $cart_total = (  isset( $order_cart['cart_total'] ) ) ? $order_cart['cart_total'] : ( isset( WC()->cart ) ) ? WC()->cart->total :'';

            }else {

                global $wp;
                $order_id = $wp->query_vars['order-pay'];
                $order = wc_get_order( $order_id );

                if( $order ) {

                    $cart_total = $order->get_total();

                } else {
                    $cart_total = '';
                }

            }

             if(!$cart_total) {
                 return false;
             }

            switch( $restriction_by ){
                case 'less_than':
                    if( ! ( $cart_total < $threshold ) ){
                        return  false;
                    }
                    break;
                case 'less_or_equal':
                    if( ! ( $cart_total <= $threshold ) ){
                        return false;
                    }
                    break;
                case 'equal':
                    if( ! ( $cart_total == $threshold ) ){
                        return false;
                    }
                    break;
                case 'greater_or_equal':
                    if( ! ( $cart_total >= $threshold ) ){
                        return false;
                    }
                    break;
                case 'greater_than':
                    if( ! ( $cart_total > $threshold ) ){
                        return  false;
                    }
                    break;
                default :
                    return false;
                    break;
            }
            return true;
        }
        /**
         * Restriction by categories
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_categories( $restriction_by, $selected_cats, $order_cart ) {

            if ( ( is_checkout() && !is_wc_endpoint_url( 'order-pay' ) ) || apply_filters('yith_wcpmr_restriction_by',false)) {

                $item_cart = ( isset( $order_cart['items'] ) ) ? $order_cart['items']  : ( isset( WC()->cart ) ?  WC()->cart->get_cart() : '' ) ;

            }else {

                global $wp;
                $order_id = $wp->query_vars['order-pay'];
                $order = wc_get_order( $order_id );

                if( $order ) {

                    $item_cart = $order->get_items();

                } else {
                    $item_cart ='';
                }

            }

            if(!$item_cart) {
                return true;
            }
            $cats_in_cart = array();

            foreach ( $item_cart as $cart_item_key => $cart_item ) {

                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                $item_terms = get_the_terms( $product_id, 'product_cat' );

                if( ! empty( $item_terms ) ){
                    foreach( $item_terms as $term ){
                        if( ! in_array( $term->term_id, $cats_in_cart ) ){
                            $cats_in_cart[] = $term->term_id;
                        }
                    }
                }
            }
            switch ( $restriction_by ) {
                case 'include_or' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_cats as $cat ){
                            if( in_array( $cat, $cats_in_cart ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
                        return  false;
                    }
                    break;

                case 'include_and' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        foreach( (array) $selected_cats as $cat ){
                            if( ! in_array( $cat, $cats_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
                        return false;
                    }
                    break;

                case 'exclude_or' :
                    if( ! empty( $selected_cats ) && ! empty( $cats_in_cart ) ){
                        foreach( (array) $selected_cats as $cat ){
                            if( in_array( $cat, $cats_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_cats ) ){
                        return false;
                    }
                    break;

                default :
                    return false;
                    break;
            }
            return true;
        }

        /**
         * Restriction by tag
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */

        function restriction_by_tags( $restriction_by,$selected_tags, $order_cart ) {

            if ( ( is_checkout() && !is_wc_endpoint_url( 'order-pay' ) ) || apply_filters('yith_wcpmr_restriction_by',false) ) {

                $item_cart = ( isset( $order_cart['items'] ) ) ? $order_cart['items']  : ( isset( WC()->cart ) ?  WC()->cart->get_cart() : '' ) ;

            }else {

                global $wp;
                $order_id = $wp->query_vars['order-pay'];
                $order = wc_get_order( $order_id );

                if( $order ) {

                    $item_cart = $order->get_items();

                } else {
                    $item_cart ='';
                }

            }

            if(!$item_cart) {
                return true;
            }

            $tags_in_cart = array();
            foreach ( $item_cart as $cart_item_key => $cart_item ) {

                $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                $item_terms = get_the_terms( $product_id, 'product_tag' );
                if( ! empty( $item_terms ) ){
                    foreach( $item_terms as $term ){
                        if( ! in_array( $term->term_id, $tags_in_cart ) ){
                            $tags_in_cart[] = $term->term_id;
                        }
                    }
                }
            }
            switch ( $restriction_by ) {
                case 'include_or' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        $found = false;
                        foreach( (array) $selected_tags as $tag ){
                            if( in_array( $tag, $selected_tags ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return  false;
                    }
                    break;

                case 'include_and' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        foreach( (array) $selected_tags as $tag ){
                            if( ! in_array( $tag, $tags_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return false;
                    }
                    break;

                case 'exclude_or' :
                    if( ! empty( $selected_tags ) && ! empty( $tags_in_cart ) ){
                        foreach( (array) $selected_tags as $tag ){
                            if( in_array( $tag, $tags_in_cart ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_tags ) ){
                        return false;
                    }
                    break;

                default :
                    return false;
                    break;
            }
            return true;
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

            if ( ( is_checkout() && !is_wc_endpoint_url( 'order-pay' ) ) || apply_filters('yith_wcpmr_restriction_by',false) ) {

                $item_cart = ( isset( $order_cart['items'] ) ) ? $order_cart['items']  : ( isset( WC()->cart ) ?  WC()->cart->get_cart() : '' ) ;

            }else {

                global $wp;
                $order_id = $wp->query_vars['order-pay'];
                $order = wc_get_order( $order_id );

                if( $order ) {

                    $item_cart = $order->get_items();

                } else {
                    $item_cart ='';
                }

            }
            
            if(!$item_cart) {
                return true;
            }

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
                    return apply_filters('yith_wcpmr_default_restriction_by_product',false,$restriction_by,$selected_products,$products_in_cart);
            }
            return true;
        }

        /**
         * Restriction by geolocalization
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_geolocalization($restriction_by,$countries) {

            $customer = yith_get_country_customer();
            $country = $customer['country'];
            if( empty( $countries ) ){
                return false;
            }

            switch( $restriction_by ){
                case 'include_or':
                case 'include_and':

                    if( ! in_array( $country, $countries ) ){
                        return false;
                        break;
                    }

                    break;
                case 'exclude_or':

                    if( in_array( $country, $countries ) ){
                        return false;
                        break;
                    }

                    break;
                default :
                    return false;
            }
            return true;
        }
        /**
         * Restriction by role
         *
         * Return false if the restriction is not met
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return boolean
         */
        function restriction_by_role( $restriction_by,$selected_roles ) {
            $user = wp_get_current_user();
            $user_roles = $user->roles;
            switch( $restriction_by ){
                case 'include_or':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        $found = false;
                        foreach( (array) $selected_roles as $role ){
                            if( in_array( $role, $user_roles ) ){
                                $found = true;
                                break;
                            }
                        }

                        if( ! $found ){
                            return false;
                        }
                    }
                    elseif( ! empty( $selected_roles ) && !in_array('yith_guest',$selected_roles) ){
                        return false;
                    }

                    break;
                case 'include_and':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        foreach( (array) $selected_roles as $role ){
                            if( ! in_array( $role, $user_roles ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_roles ) && !in_array('yith_guest',$selected_roles) ){
                        return false;
                    }

                    break;
                case 'exclude_or':

                    if( ! empty( $selected_roles ) && ! empty( $user_roles ) ){
                        foreach( (array) $selected_roles as $role ){
                            if( in_array( $role, $user_roles ) ){
                                return false;
                                break;
                            }
                        }
                    }
                    elseif( ! empty( $selected_roles ) && in_array('yith_guest',$selected_roles) ){
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