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
 * @class      YITH_Payment_Restrictions_Admin_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_Payment_Restrictions_Admin_Premium' ) ) {
    /**
     * Class YITH_Payment_Restrictions_Admin_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions_Admin_Premium extends YITH_Payment_Restrictions_Admin
    {

        /**
         * Single instance of the class
         *
         * @var \YITH_Payment_Restrictions_Admin_Premium
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
            /* === Register Panel Settings === */
            $this->show_premium_landing = false;
            add_filter( 'yith_wcpmr_admin_tabs', array( $this,'yith_wcpmr_admin_tabs'));
            add_action( 'yith_wcpmr_bacs_account_tab', array( $this,'payment_method_restriction_bacs_account_tab' ) );
            add_filter( 'yith_wcpmr_template_restriction', array( $this,'add_template_restriction' ));

            /* Register plugin to licence/update system */
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));

            parent::__construct();
        }

        public function yith_wcpmr_admin_tabs($tabs) {
            $tabs['bacs-account'] =  __('BACS account', 'yith-payment-method-restrictions-for-woocommerce');
            if(current_user_can('manage_options')) {
                $tabs['general-settings'] = __('General Settings', 'yith-payment-method-restrictions-for-woocommerce');
            }

            return $tabs;
        }

        /**
         * Enqueue Scripts
         *
         * Register and enqueue scripts for Admin
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */

        public function enqueue_scripts()
        {
            global $post;
            wp_register_style('yith_wcpmr_admincss', YITH_WCPMR_ASSETS_URL . 'css/wcpmr-admin.css', YITH_WCPMR_VERSION);
            wp_register_script('yith_wcpmr_admin', YITH_WCPMR_ASSETS_URL . 'js/wcpmr-admin-premium.js', array('jquery', 'jquery-ui-sortable', 'wc-enhanced-select'), YITH_WCPMR_VERSION, true);

            wp_localize_script('yith_wcpmr_admin', 'yith_wcpmr_admin', apply_filters('yith_wcpmr_admin_localize', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'before_3_0' => version_compare( WC()->version, '3.0', '<' ) ? true : false,
                'search_categories_nonce' => wp_create_nonce( 'search-categories' ),
                'search_tags_nonce'       => wp_create_nonce( 'search-tags' ),
            )));
            if ( is_admin() && ( is_page('yith_wcpmr_payment_restriction_panel') || isset( $post ) && 'yith_wcpmr_rule' == $post->post_type ) ) {
                wp_enqueue_script('yith_wcpmr_admin');
                wp_enqueue_style('woocommerce_admin_styles');
                wp_enqueue_style('yith_wcpmr_admincss');
            }

            do_action('yith_wcpmr_enqueue_scripts');

        }



        /**
         *
         * Bacs account tab
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return void
         */
        public function payment_method_restriction_bacs_account_tab() {
            wc_get_template('admin/bacs-account-tab.php', array(), '', YITH_WCPMR_TEMPLATE_PATH);
        }

        /**
         *
         * Add template premium
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         * @return $template
         */
        public function add_template_restriction($template) {

           $template = defined( 'YITH_WCPMR_PREMIUM' ) ? 'wcpmr-template-restriction-premium.php' : $template;

           return $template;
        }


        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YITH_WCPMR_PATH . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WCPMR_PATH . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register(YITH_WCPMR_INIT, YITH_WCPMR_SECRETKEY, YITH_WCPMR_SLUG);

        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCPMR_PATH . '/plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCPMR_SLUG, YITH_WCPMR_INIT);
        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.0.8
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCPMR_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.0.8
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;
        }


    }
}