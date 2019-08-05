<?php
$payment_restriction_rule = get_post_meta( $post->ID, 'yith_wcpmr_payment_restriction', true );
$gateway                  = get_post_meta( $post->ID, 'yith_wcpmr_payment_gateway', true );
?>

<div class="yith-wcpmr-rule-payment-restriction">

    <div class="yith-wcpmr-payment-gateway yith-wcpmr-restriction">
        <label for="yith-wcpmr-payment-gateway-label"><?php _e( 'Payment method gateway:', 'yith-payment-method-restrictions-for-woocommerce' ); ?></label>
        <?php echo yith_wcpmr_get_dropdown( array(
            'name'     => 'payment_gateway',
            'id'       => 'yith-wcpmr-rule-gateway',
            'class'    => 'yith-wcpmr-rule yith-wcpmr-select yith-wcpmr-li yith-wcpmr-rule-set yith-wcpmr-rule-gateway',
            'options'  => yith_wcpmr_get_payment_gateway(),
            'value'   => isset( $gateway ) ? $gateway : '',
        ) ); ?>
    </div>

    <div class="yith-wcpmr-conditions">
        <label for="yith-wcpmr-conditions-label"><?php _e( 'Conditions:', 'yith-payment-method-restrictions-for-woocommerce' ); ?></label>
        <div id="yith-wcpmr-conditions-list" class="yith-wcpmr-condtions-list">
            <?php
            if ( !empty( $payment_restriction_rule[ 'conditions' ] ) ) {
                $i = 0;
                foreach ( $payment_restriction_rule[ 'conditions' ] as $conditions ) {
                    $default_args = array(
                        'i' => $i
                    );
                    $args         = wp_parse_args( $conditions, $default_args );
                    wc_get_template( 'wcpmr-conditions-row.php', $args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
                    $i++;
                }
            }

            ?>

        </div>
        <input id="yith-wcpmr-new-condition" type="button" class="button-secondary yith-wcpmr-new-condition" value="<?php _e( '+ Add new condition', 'yith-payment-method-restrictions-for-woocommerce' ); ?>">
    </div>
</div>
