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
 * @class      YITH_WCPMR_Ajax
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_WCPMR_Ajax' ) ) {

    class YITH_WCPMR_Ajax {

        /**
         * Main Instance
         *
         * @var YITH_WCPMR_Ajax
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCPMR_Ajax instance
         * @author Carlos Rodríguez <carlos.rodr
         * iguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function __construct()
        {
            add_action('wp_ajax_yith_wcpmr_add_condition_row', array($this,'add_conditions_row'));
        }

        /**
         * add_conditions_row
         *
         * Add new condition in the rule
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function add_conditions_row() {
            $args = array(
                'i' => $_POST['index'],
            );
            wc_get_template( 'wcpmr-conditions-row.php',$args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
            die();
        }

    }

}