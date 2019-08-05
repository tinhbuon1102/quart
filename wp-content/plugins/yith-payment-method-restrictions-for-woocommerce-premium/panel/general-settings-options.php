<?php
return array(

    'general-settings' => apply_filters( 'yith_wcpmr_settings_options', array(

            //////////////////////////////////////////////////////

            'settings_tab_payment_restriction_options_start'    => array(
                'type' => 'sectionstart',
                'id'   => 'yith_wcpmr_settings_tab_payment_restriction_start'
            ),

            'settings_tab_payment_restriction_options_title'    => array(
                'title' => _x( 'General settings', 'Panel: page title', 'yith-payment-method-restrictions-for-woocommerce' ),
                'type'  => 'title',
                'desc'  => '',
                'id'    => 'yith_wcpmr_settings_tab_payment_restriction_title'
            ),
            'settings_tab_payment_restriction_show_name' => array(
                'title'   => _x( 'Show Payment Restriction option for shop manager', 'Admin option: Show Payment Restriction option for shop managers', 'yith-payment-method-restrictions-for-woocommerce' ),
                'type'    => 'checkbox',
                'desc'    => _x( 'Check this option to manage payment restriction option for shop manager role', 'Admin option description: Check this option to manage payment restriction option for shop manager role', 'yith-payment-method-restrictions-for-woocommerce' ),
                'id'      => 'yith_wcpmr_settings_tab_payment_restriction_allow_shop_manager',
                'default' => 'no'
            ),

            'settings_tab_payment_restriction_options_end'      => array(
                'type' => 'sectionend',
                'id'   => 'yyith_wcpmr_settings_tab_payment_restriction_end'
            ),

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        )
    )
);
