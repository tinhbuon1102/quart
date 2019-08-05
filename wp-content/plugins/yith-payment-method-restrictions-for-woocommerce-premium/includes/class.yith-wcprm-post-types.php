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
 * @class      YITH_WCPMR_Post_Types
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCPMR_Post_Types' ) ) {
    /**
     * YITH Payment Method Restrictions for WooCommerce Premium Post Types
     *
     * @since 1.0.0
     */
    class YITH_WCPMR_Post_Types
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_Payment_Restrictions_Admin
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_Payment_Restrictions_Admin
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }


        /**
         * Payment Restriction Post Type
         *
         * @var string
         * @static
         */
        public static $rule = 'yith_wcpmr_rule';

        /**
         * Hook in methods.
         */
        public function __construct() {
            add_action( 'init', array($this, 'register_post_types' ));
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ),10,2 );
            add_action( 'save_post', array( $this, 'save_metabox' ), 10, 1 );
            add_action( 'edit_form_advanced', array( $this, 'add_return_to_list_button' ) );
        }

        /**
         * Register core post types.
         */
        public function register_post_types() {
            if ( post_type_exists( self::$rule ) ) {
                return;
            }

            do_action( 'yith_wcpmr_register_post_type' );

            /*  PAYMENT METHOD RESTRICTIONS  */

            $labels = array(
                'name'               => __( 'Payment Method Restrictions', 'yith-payment-method-restrictions-for-woocommerce' ),
                'singular_name'      => __( 'Payment Method Restrictions', 'yith-payment-method-restrictions-for-woocommerce' ),
                'add_new'            => __( 'Add new rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'add_new_item'       => __( 'Add New Rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'edit'               => __( 'Edit', 'yith-payment-method-restrictions-for-woocommerce' ),
                'edit_item'          => __( 'Edit Rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'new_item'           => __( 'New Rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'view'               => __( 'View Rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'view_item'          => __( 'View Rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                'search_items'       => __( 'Search Rules', 'yith-payment-method-restrictions-for-woocommerce' ),
                'not_found'          => __( 'No Rules found', 'yith-payment-method-restrictions-for-woocommerce' ),
                'not_found_in_trash' => __( 'No Rules found in trash', 'yith-payment-method-restrictions-for-woocommerce' ),
                'parent'             => __( 'Parent Rules', 'yith-payment-method-restrictions-for-woocommerce' ),
                'menu_name'          => _x( 'YITH Rules', 'Admin menu name', 'yith-payment-method-restrictions-for-woocommerce' ),
                'all_items'          => __( 'All YITH Rules', 'yith-payment-method-restrictions-for-woocommerce' ),
            );

            $payment_method_restrictions_args = array(
                'label'               => __( 'Payment Method Restrictions', 'yith-payment-method-restrictions-for-woocommerce' ),
                'labels'              => $labels,
                'description'         => __( 'This is where rules are stored.', 'yith-payment-method-restrictions-for-woocommerce' ),
                'public'              => true,
                'show_ui'             => true,
                'capability_type'     => 'product',
                'map_meta_cap'        => true,
                'publicly_queryable'  => false,
                'exclude_from_search' => true,
                'show_in_menu'        => false,
                'hierarchical'        => false,
                'show_in_nav_menus'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'supports'            => array( 'title' ),
                'has_archive'         => false,
                'menu_icon'           => 'dashicons-edit',
            );

            register_post_type( self::$rule, apply_filters( 'yith_wcpmr_register_post_type_payment_method_restriction', $payment_method_restrictions_args ) );

        }
        /**
         * Add style metabox custom post type.
         */
        public function add_meta_boxes( $post_type, $post ) {

            if ( $post_type && self::$rule  == $post_type ) {
                add_meta_box( 'wcpmr-rule-metabox',
                    __( 'Payment method restriction rule', 'yith-payment-method-restrictions-for-woocommerce' ),
                    array( $this, 'rule_metabox_content' ), self::$rule, 'normal', 'core'
                );
            }


        }

        /**
         * save_metabox
         *
         * Save post type data
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function save_metabox($post_id) {

            $rule = $_POST['yith-wcpmr-rule'];
            $gateway = $_POST['payment_gateway'];
            $conditions = isset($rule['conditions']) ? $rule['conditions']: '';
            $list_condition = array();

            for ( $i = 0; $i < count($conditions); $i++ ) {

                $type_restriction = isset($conditions[$i]['type_restriction']) ? $conditions[$i]['type_restriction'] : '' ;

                if( '' != $type_restriction ) {
                    $list_condition[$i]['type_restriction'] = $type_restriction;

                    switch ( $type_restriction ) {
                        case 'price' :
                            $list_condition[$i]['restriction_by_price'] = $conditions[$i]['restriction_by_price'];
                            $list_condition[$i]['price'] = $conditions[$i]['price'];
                            break;

                        case 'geolocalization' :
                            $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                            $list_condition[$i]['geolocalization'] = (isset($conditions[$i]['geolocalization'])) ? $conditions[$i]['geolocalization'] : '';
                            break;
                        case 'product' :
                            $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                            $list_condition[$i]['products_selected'] = (isset($conditions[$i]['products_selected'])) ? $conditions[$i]['products_selected'] : '';

                            break;
                        case 'category' :
                            $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                            $list_condition[$i]['categories_selected'] = (isset($conditions[$i]['categories_selected'])) ? $conditions[$i]['categories_selected'] : '';
                            break;

                        case 'tag' :
                            $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                            $list_condition[$i]['tags_selected'] = (isset($conditions[$i]['tags_selected'])) ? $conditions[$i]['tags_selected'] : '';
                            break;
                    }

                }
            }


            $type_restriction_rule = array(
                'gateway' => $gateway,
                'conditions' => $list_condition,
            );
            update_post_meta( $post_id, 'yith_wcpmr_payment_gateway' ,$gateway );
            update_post_meta( $post_id, 'yith_wcpmr_payment_restriction', $type_restriction_rule );
        }


        /**
         * Add content in metabox.
         */
        public function rule_metabox_content($post) {
            if ( ! $post ) {
                return;
            }
            wc_get_template( apply_filters('yith_wcpmr_template_restriction','wcpmr-template-restriction.php'),
                array( 'post' => $post ),
                '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
        }

        /**
         * Add content in metabox.
         */
        public function add_return_to_list_button() {
            global $post;

            if ( isset( $post ) && self::$rule === $post->post_type ) {
                $admin_url = admin_url( 'admin.php' );
                $params = array(
                    'page' => 'yith_wcpmr_payment_restriction_panel',
                    'tab' => 'settings'
                );

                $list_url = apply_filters( 'yith_wcpmr_rule_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
                $button = sprintf( '<a class="button-secondary" href="%s">%s</a>', $list_url,
                    __( 'Back to rules',
                        'yith-payment-method-restrictions-for-woocommerce' ) );
                echo $button;
            }
        }
    }
}