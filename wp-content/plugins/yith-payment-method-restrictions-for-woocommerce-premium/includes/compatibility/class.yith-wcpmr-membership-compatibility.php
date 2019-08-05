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
 * @class      YITH_WCPMR_Membership_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCPMR_Membership_Compatibility' ) ) {

    class YITH_WCPMR_Membership_Compatibility
    {

        public function __construct()
        {
            add_filter('yith_wcmr_type_of_restrictions',array($this,'add_membership_restriction'));
            add_action('yith_wcpmr_conditions_row',array($this,'add_membership_conditions_row'),10,2);
            add_filter('yith_wcpmr_save_metabox',array($this,'save_membership_options'),10,4);
            add_filter('yith_wcpmr_is_payment_method_disabled',array($this,'is_payment_method_disabled_for_membership_rule'),10,4);
        }

        /**
         * add_membership_restriction
         *
         * Add membership on restriction type
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */

        public function add_membership_restriction($restrictions) {

            $restrictions['membership'] = 'Membership';

            return $restrictions;

        }
        /**
         * add_membership_conditions_row
         *
         * Add membership conditions on restriction type
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function add_membership_conditions_row($args,$i) {

            ?>

                <div class="yith-wcpmr-select2 yith-wcpmr-select2-membership yith-wcpmr-row">
                    <?php
                        $plan_ids = YITH_WCMBS_Manager()->get_plans( array( 'fields' => 'name' ) );
                        $plans = array();
                        foreach($plan_ids as $plan){
                            $plans[$plan->ID] = $plan->post_title;
                        }
                        $class = 'yith-wcpmr-select yith-wcpmr yith-wcpmr-membership-search yith-wcpmr-li';
                        $class = isset ($args['membership']) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class;
                        echo yith_wcpmr_get_dropdown_multiple(array(
                            'name' => 'yith-wcpmr-rule[conditions][' . $i . '][membership][]',
                            'id' => '',
                            'style' => isset($args['membership']) ? '' : 'display: none;',
                            'class' => $class,
                            'options' => $plans,
                            'multiple' => 'multiple',
                            'value' => isset($args['membership']) ? $args['membership'] : '',
                            'custom-attributes' => array(
                                'data-type' => 'membership',
                            ),
                        ));
                    ?>
                </div>

            <?php
        }

        public function save_membership_options($list_condition,$i,$type_restriction,$conditions) {

            if( 'membership' == $type_restriction ) {

                $list_condition[$i]['restriction_by'] = $conditions[$i]['restriction_by'];
                $list_condition[$i]['membership'] = (isset($conditions[$i]['membership'])) ? $conditions[$i]['membership'] : '';
            }


            return $list_condition;

        }

        public function is_payment_method_disabled_for_membership_rule($status,$type_restriction,$condition,$gateway) {

            if( 'membership' == $type_restriction ) {

                $user = wp_get_current_user();
                $memberships = $condition['membership'];
                switch( $condition['restriction_by'] ){

                    case 'include_or':

                        if( ! empty( $memberships  ) ){
                            $found = false;
                            foreach( (array) $memberships as $plan_id ){
                                if( yith_wcmbs_user_has_membership($user->ID,$plan_id) ){
                                    $found = true;
                                    break;
                                }
                            }

                            if( ! $found ){
                                return false;
                            }
                        }
                        break;

                    case 'include_and':

                        if( ! empty( $memberships ) ){
                            foreach( (array) $memberships as $plan_id ){
                                if( ! yith_wcmbs_user_has_membership( $user->ID, $plan_id ) ){
                                    return false;
                                    break;
                                }
                            }
                        }

                        break;

                    case 'exclude_or':

                        if( ! empty( $memberships ) ){
                            foreach( (array) $memberships as $plan_id ){
                                if( yith_wcmbs_user_has_membership( $user->ID, $plan_id ) ){
                                    return false;
                                    break;
                                }
                            }
                        }

                        break;
                    default :
                        return false;
                }
                return true;

            }

            return $status;

        }

    }
}

return new YITH_WCPMR_Membership_Compatibility();