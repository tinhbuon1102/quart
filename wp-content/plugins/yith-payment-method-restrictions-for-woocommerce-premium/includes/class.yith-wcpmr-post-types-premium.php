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
 * @class      YITH_WCPMR_Post_Types_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCPMR_Post_Types_Premium' ) ) {
    /**
     * Class YITH_WCPMR_Post_Types_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCPMR_Post_Types_Premium extends YITH_WCPMR_Post_Types
    {

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {

            parent::__construct();
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
            if( !isset( $_POST['yith-wcpmr-rule'] ) ){
                return;
            }
            $rule = $_POST['yith-wcpmr-rule'];
            $enable_disable = isset($_POST['enable_disable']) ? $_POST['enable_disable'] : false ;
            $gateway = $_POST['payment_gateway'];
            $banks_account = isset($rule['banks_account']) ? $rule['banks_account']: false;
            $conditions = isset($rule['conditions']) ? $rule['conditions']: '';
            $radio_button = $_POST['yith_wcpmr_checked_account'];
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

                        case 'role' :
                            $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                            $list_condition[$i]['role'] = (isset($conditions[$i]['role'])) ? $conditions[$i]['role'] : '';

                        default:
                            $list_condition = apply_filters('yith_wcpmr_save_metabox',$list_condition,$i,$type_restriction,$conditions);

                    }

                }
            }

            $type_restriction_rule = array(
                'gateway' => $gateway,
                'conditions' => $list_condition,
                'banks_account' => $banks_account,
                'message' => $rule['message'],
            );

            update_post_meta( $post_id, 'yith_wcpmr_payment_gateway' ,$gateway );
            update_post_meta( $post_id, 'yith_wcpmr_enable_disable_payment_restriction',$enable_disable );
            update_post_meta( $post_id, 'yith_wcpmr_rule_banks_account',$banks_account);
            update_post_meta( $post_id, 'yith_wcpmr_payment_restriction', $type_restriction_rule );
            update_post_meta( $post_id, 'yith_wcpmr_select_radio_button',$radio_button );
        }
    }
}