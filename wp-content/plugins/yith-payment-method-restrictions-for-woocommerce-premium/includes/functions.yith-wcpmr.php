<?php

/* Metabox Functions */
function yith_wcpmr_get_payment_gateway()
{
    $payment_methods = array();
    foreach (WC()->payment_gateways->payment_gateways() as $gateways) {
        $payment_methods[$gateways->id] = $gateways->title;
    }

    if(isset($payment_methods['bacs'])) {
        $payment_methods = yith_wcpmr_move_to_top($payment_methods,'bacs');
    }
    return apply_filters('yith_wcpmr_payment_gateway',$payment_methods);
}

function yith_wcpmr_restriction_type() {
    $condition = array(
        'default'                  => __('Type of restriction:','yith-payment-method-restrictions-for-woocommerce'),
        'include_or'        => __('Include at least one of','yith-payment-method-restrictions-for-woocommerce'),
        'include_and'       => __('Include all','yith-payment-method-restrictions-for-woocommerce'),
        'exclude_or'        => __('Does not contain','yith-payment-method-restrictions-for-woocommerce'),
    );

    return apply_filters('yith_wcpmr_include_exclude',$condition);
}

function yith_wcpmr_price_order() {
    $type_price = array(
        'less_than'               => __('Less than','yith-payment-method-restrictions-for-woocommerce'),
        'less_or_equal'      => __('Less than or equal to','yith-payment-method-restrictions-for-woocommerce'),
        'equal'             => __('Equal to','yith-payment-method-restrictions-for-woocommerce'),
        'greater_or_equal'      => __('Greater than or equal to','yith-payment-method-restrictions-for-woocommerce'),
        'greater_than'               => __('Greater than','yith-payment-method-restrictions-for-woocommerce'),
    );

    return apply_filters('yith_wcpmr_price_order',$type_price);
}

/* Metabox Functions */


function yith_wcpmr_get_dropdown($args = array()){
    $default_args = array(
        'id'        => '',
        'name'      => '',
        'class'     => '',
        'style'     => '',
        'options'   => array(),
        'value'     => '',
        'disabled'  => '',
        'multiple' => '',
        'echo'      => false
    );

    $args = wp_parse_args( $args, $default_args );
    extract( $args );
    /**
     * @var string $id
     * @var string $name
     * @var string $class
     * @var string $style
     * @var array  $options
     * @var string $value
     * @var bool   $echo
     * @var string $disabled
     */
    $html = "<select id='$id' name='$name' class='$class' $multiple style='$style'>";

    foreach ( $options as $option_key => $option_label ) {
        $selected = selected( $option_key == $value, true, false );
        $disabled = disabled( $option_key == $disabled,true,false );
        $html .= "<option value='$option_key' $selected $disabled >$option_label</option>";
    }

    $html .= "</select>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}

function yith_wcpmr_get_dropdown_multiple($args = array()){
    $default_args = array(
        'id'        => '',
        'name'      => '',
        'class'     => '',
        'style'     => '',
        'options'   => array(),
        'value'     => '',
        'multiple'  => 'multiple',
        'disabled'  => '',
        'echo'      => false,
        'custom-attributes' => array()
    );

    $args = wp_parse_args( $args, $default_args );
    $custom_attributes = array();
    foreach ( $args[ 'custom-attributes' ] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
    $custom_attributes = implode( ' ', $custom_attributes );
    extract( $args );
    /**
     * @var string $id
     * @var string $name
     * @var string $class
     * @var string $style
     * @var array  $options
     * @var string $value
     * @var bool   $echo
     */
    $html = "<select id='$id' name='$name' class='$class' $multiple style='$style' $custom_attributes>";
    foreach ( $options as $option_key => $option_label ) {
        $selected =  is_array($value) ? selected( in_array($option_key,$value), true, false ):'';
        $disabled = disabled( $option_key == $disabled,true,false );
        $html .= "<option value='$option_key' $selected >$option_label</option>";
    }
    $html .= "</select>";

    if ( $echo ) {
        echo $html;
    } else {
        return $html;
    }
}

function yith_wcpmr_move_to_top(&$array, $key) {
    $temp = array($key => $array[$key]);
    unset($array[$key]);
    return $array = $temp + $array;
}

function yith_wcpmr_get_rules($args) {

    $defaults = apply_filters( 'yith_wcpmr_get_rule',array(
        'posts_per_page' => -1,
        'post_type' => 'yith_wcpmr_rule',
    ));

    $params = wp_parse_args( $args, $defaults );
    $results = get_posts( $params );
    return $results;
}



function yith_wcpmr_get_rules_by_payment_method($paypment_method) {
    $args = array(
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'yith_wcpmr_payment_gateway',
                'value' => $paypment_method,
                'compare' => '='
            ),
            array(
                'key'       => 'yith_wcpmr_enable_disable_payment_restriction',
                'value'     =>'1',
                'compare'   =>'!=',
            ),
            'suppress_filters' => false,
        ) );


    return yith_wcpmr_get_rules( $args );
}
