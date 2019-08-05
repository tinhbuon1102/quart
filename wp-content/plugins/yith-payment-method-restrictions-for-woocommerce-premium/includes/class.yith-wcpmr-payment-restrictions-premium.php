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
 * @class      YITH_Payment_Restrictions_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Payment_Restrictions_Premium' ) ) {
	/**
	 * Class YITH_Payment_Restrictions_Premium
	 *
	 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
	 */
	class YITH_Payment_Restrictions_Premium extends YITH_Payment_Restrictions {
        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct(){
			add_filter( 'yith_wcpmr_require_class', array( $this, 'load_premium_classes' ) );

			parent::__construct();
		}
		

		/**
		 * Main Init classes
		 *
		 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
		 */
		public function init_classes(){
			$this->ajax = YITH_WCPMR_Ajax::get_instance();
            $this->functions = YITH_Payment_Restrictions_Functions::get_instance();
            $this->compatibility = YITH_WCPMR_Compatibility::get_instance();

            YITH_WCPMR_Post_Types::get_instance();
        }


		/**
		 * Add premium files to Require array
		 *
		 * @param $require The require files array
		 *
		 * @return Array
		 * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
		 * @since 1.0
		 *
		 */
		public function load_premium_classes( $require ){
			$frontend = array(
				'includes/class.yith-wcpmr-payment-restrictions-frontend-premium.php',
			);
			$common = array(
                'includes/class.yith-wcpmr-ajax-premium.php',
                'includes/functions.yith-wcpmr-premium.php',
                'includes/class.yith-wcpmr-post-types-premium.php',
                'includes/class.yith-wcpmr-payment-restrictions-functions-premium.php',
                'includes/compatibility/class.yith-wcpmr-compatibility.php'
            );
			$admin = array(
				'includes/class.yith-wcpmr-payment-restrictions-admin-premium.php',
            );
			$require['admin']   	= array_merge($require['admin'],$admin);
			$require['frontend']  	= array_merge($require['frontend'],$frontend);
			$require['common']    	= array_merge($require['common'],$common);

			return $require;
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
				$this->admin = YITH_Payment_Restrictions_Admin_Premium::get_instance();
			}

			if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
				$this->frontend = YITH_Payment_Restrictions_Frontend_Premium::get_instance();
			}
		}
		
    }
}