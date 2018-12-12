<?php
// Exit if accessed directly
!defined( 'YITH_WCBEP' ) && exit();


$tab = array(
    'settings' => array(
        'general-options' => array(
            'title' => __( 'General Options', 'yith-woocommerce-bulk-product-editing' ),
            'type'  => 'title',
            'desc'  => '',
        ),

        'round-prices' => array(
            'id'        => 'yith-wcbep-round-prices',
            'name'      => __( 'Round Prices', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the prices will be rounded when bulk editing.' )
        ),

        'use-regex-on-search' => array(
            'id'        => 'yith-wcbep-use-regex-on-search',
            'name'      => __( 'Use Regular Expressions', 'yith-woocommerce-bulk-product-editing' ),
            'type'      => 'yith-field',
            'yith-type' => 'onoff',
            'default'   => 'no',
            'desc'      => __( 'If enabled, the plugin uses Regular Expressions when searching for texts.' )
        ),

        'general-options-end' => array(
            'type' => 'sectionend'
        )
    )
);

return apply_filters( 'yith_wcbep_panel_settings_options', $tab );