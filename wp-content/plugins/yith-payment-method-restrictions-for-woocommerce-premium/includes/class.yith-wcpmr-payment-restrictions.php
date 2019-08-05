<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPMR_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Payment_Restrictions
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Payment_Restrictions' ) ) {
    /**
     * Class YITH_Payment_Restrictions
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_Payment_Restrictions
    {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0
         */
        public $version = YITH_WCPMR_VERSION;

        /**
         * Main Instance
         *
         * @var YITH_Payment_Restrictions
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main Admin Instance
         *
         * @var YITH_Payment_Restrictions_Admin
         * @since 1.0
         */
        public $admin = null;

        /**
         * Main Frontpage Instance
         *
         * @var YITH_Payment_Restrictions_Frontend
         * @since 1.0
         */
        public $frontend = null;

        /**
         * Main Product Instance
         *
         * @var
         * @since 1.0
         */
        public $product = null;


        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {

            /* === Require Main Files === */
            $require = apply_filters('yith_wcpmr_require_class',
                array(
                    'common' => array(
                        'includes/class.yith-wcpmr-ajax.php',
                        'includes/class.yith-wcpmr-payment-restrictions-list.php',
                        'includes/functions.yith-wcpmr.php',
                        'includes/class.yith-wcpmr-payment-restrictions-functions.php',
                        'includes/class.yith-wcprm-post-types.php'
                    ),
                    'admin' => array(
                        'includes/class.yith-wcpmr-payment-restrictions-admin.php',
                    ),
                    'frontend' => array(
                        'includes/class.yith-wcpmr-payment-restrictions-frontend.php',
                    ),
                )
            );

            $this->_require($require);

            $this->init_classes();

            /* === Load Plugin Framework === */
            add_action('plugins_loaded', array($this, 'plugin_fw_loader'), 15);

            /* == Plugins Init === */
            add_action('init', array($this, 'init'));

        }

        /**
         * Main plugin Instance
         *
         * @return YITH_Payment_Restrictions Main instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }


        public function init_classes(){
            $this->ajax = YITH_WCPMR_Ajax::get_instance();
            $this->functions = YITH_Payment_Restrictions_Functions::get_instance();
            YITH_WCPMR_Post_Types::get_instance();
        }

        /**
         * Add the main classes file
         *
         * Include the admin and frontend classes
         *
         * @param $main_classes array The require classes file path
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         *
         * @return void
         * @access protected
         */
        protected function _require($main_classes)
        {
            foreach ($main_classes as $section => $classes) {
                foreach ($classes as $class) {
                    if ('common' == $section || ('frontend' == $section && !is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) || ('admin' == $section && is_admin()) && file_exists(YITH_WCPMR_PATH . $class)) {
                        require_once(YITH_WCPMR_PATH . $class);
                    }
                }
            }
        }

        /**
         * Load plugin framework
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        public function plugin_fw_loader()
        {
            if (!defined('YIT_CORE_PLUGIN')) {
                global $plugin_fw_data;
                if (!empty($plugin_fw_data)) {
                    $plugin_fw_file = array_shift($plugin_fw_data);
                    require_once($plugin_fw_file);
                }
            }
        }

        /**
         * Function init()
         *
         * Instance the admin or frontend classes
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         * @access protected
         */
        public function init()
        {
            if (is_admin()) {
                $this->admin = YITH_Payment_Restrictions_Admin::get_instance();
            }

            if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
                $this->frontend = YITH_Payment_Restrictions_Frontend::get_instance();
            }
        }

    }
}
