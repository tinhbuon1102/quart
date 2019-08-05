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
 * @class      YITH_Auctions_Frontend
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */
if ( !class_exists( 'YITH_Payment_Restrictions_Frontend_Premium' ) ) {
    /**
     * Class YITH_Payment_Restrictions_Frontend_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions_Frontend_Premium extends YITH_Payment_Restrictions_Frontend
    {
        /**
         * Single instance of the class
         *
         * @var \YITH_Payment_Restrictions_Frontend
         * @since 1.0.0
         */
        protected static $instance;
        private $cart;

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        protected function __construct()
        {

            add_filter('woocommerce_bacs_accounts',array($this,'change_bacs_accounts'));
            add_action('woocommerce_thankyou_bacs',array($this,'thankyou_bacs'));
            add_action('woocommerce_checkout_update_order_meta',array($this,'create_order_meta'),10,2);
            parent::__construct();
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
        public function available_payment_gateways($available_gateways)
        {
            if( is_checkout() ) {

                $functions = YITH_Payment_Restrictions()->functions;

                foreach ($available_gateways as $gateway => $value) {

                    $rules = yith_wcpmr_get_rules_by_payment_method($gateway);
                    if ($rules) {
                        $payment_restriction_rule = $functions->is_payment_method_disabled($gateway, $rules);
                        if (is_array($payment_restriction_rule)) {
                            if (version_compare(WC()->version, '3.0.2', '<=')) {

                                if (!wc_has_notice((string)$payment_restriction_rule['message'], 'notice') && is_checkout() && (defined('DOING_AJAX') && DOING_AJAX)) {
                                    if (apply_filters('yith_wcgpf_print_notices', true, $payment_restriction_rule['message'])) {
                                        wc_add_notice($payment_restriction_rule['message'], 'notice');
                                    }
                                }
                            } else {
                                if (function_exists('wc_has_notice')) {
                                    if (!wc_has_notice((string)$payment_restriction_rule['message'], 'notice') && is_checkout() && (defined('DOING_AJAX') && DOING_AJAX)) {
                                        if (apply_filters('yith_wcgpf_print_notices', true, $payment_restriction_rule['message'])) {
                                            wc_add_notice($payment_restriction_rule['message'], 'notice');
                                        }
                                    }
                                }
                            }
                            unset($available_gateways[$gateway]);
                        }
                    }
                }
            }
            return $available_gateways;

        }

        /**
         * Change bacs account
         *
         * if bacs is the payment method restriction, change by default woocommerce bacs to payment restriction bacs rule
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function change_bacs_accounts($account_details) {
            $functions = YITH_Payment_Restrictions()->functions;
            $rules = yith_wcpmr_get_rules_by_payment_method('bacs');
            if ($rules) {
                $unset_rule = $functions->is_payment_method_disabled('bacs', $rules,$this->cart);
                if (!empty($unset_rule)) {

                    $bacs_account = get_post_meta($unset_rule, 'yith_wcpmr_rule_banks_account', true);
                    $bacs_accounts_tab = get_option('yith-wcpmr-bacs-accounts', array());
                    $bacs_accounts_tab_woo = get_option('woocommerce_bacs_accounts', array());

                    $bacs_accounts_tab_woo_formated = array();
                    foreach ($bacs_accounts_tab_woo as $key => $value) {
                        $bacs_accounts_tab_woo_formated['woo' . $key] = $value;
                    }

                    $mix_accounts = array_merge($bacs_accounts_tab_woo_formated, $bacs_accounts_tab);

                    $account = array();
                    if (!empty ($bacs_account)) {
                        foreach ($bacs_account as $bacs) {
                            $account[] = $mix_accounts[$bacs];
                        }

                        return $account;
                    }
                }
            }
            return $account_details;
        }
        /**
         * create_order_meta
         *
         * Save the array in the order
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function create_order_meta($order_id,$data) {
            $order = wc_get_order( $order_id );
            $cart = WC()->cart;

            $cart = array(
                'cart_total' => WC()->cart->total,
                'items'      => WC()->cart->get_cart(),
            );
            yit_save_prop($order,'yith_wcpmr_order_cart',$cart);
        }
        /**
         * thankyou_bacs
         *
         * get the array saved into the order
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */
        public function thankyou_bacs($order_id) {
            $order = wc_get_order($order_id);
            $this->cart = yit_get_prop($order,'yith_wcpmr_order_cart',true);
            yit_delete_prop($order,'yith_wcpmr_oder_cart');
        }
    }
}