<?php
$payment_restriction_rule = get_post_meta( $post->ID, 'yith_wcpmr_payment_restriction', true );
$gateway                  = get_post_meta( $post->ID, 'yith_wcpmr_payment_gateway', true );
$enable_disable           = get_post_meta( $post->ID, 'yith_wcpmr_enable_disable_payment_restriction', true );
$radio_button             = get_post_meta( $post->ID, 'yith_wcpmr_select_radio_button', true );
?>

<div class="yith-wcpmr-rule-payment-restriction">
    <div class="yith-wcpmr-enable-disable-payment-restriction yith-wcpmr-restriction">
        <label for="yith-wcpmr-rule-enable-disable-payment-restriction"><?php _e( 'Disable:', 'yith-payment-method-restrictions-for-woocommerce' ); ?></label>
        <input type="checkbox" id="yith-wcpmr-rule-enable-disable-payment-restriction" name="enable_disable" value="1" <?php checked( $enable_disable, 1 ) ?> />
        <span class="desc inline"><?php echo __( 'Check this option to disable payment restriction rule', 'yith-payment-method-restrictions-for-woocommerce' ) ?></span>
    </div>
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
    <div id="yith-wcpmr-bacs-payment-restriction-container" class="yith-wcpmr-bacs-payment-restriction">
        <div class="yith-wcpmr-bacs-radio-button">
            <label for="yith-wcpmr-payment-bacs-radio"><?php _e( 'What to do with this payment gateway?', 'yith-payment-method-restrictions-for-woocommerce') ?></label><br/><br/>
            <input type="radio" name="yith_wcpmr_checked_account" value="remove_payment_method" checked="checked" <?php echo ( ( $radio_button == "remove_payment_method" ) ? 'checked="checked"': '' ); ?> ><?php _e( 'Remove payment method', 'yith-payment-method-restrictions-for-woocommerce' ) ?>
            <input type="radio" name="yith_wcpmr_checked_account" value="change_bacs_account" <?php echo ( ( $radio_button == "change_bacs_account"
            ) ? 'checked="checked"': '' ); ?>><?php _e( 'Change BACS account', 'yith-payment-method-restrictions-for-woocommerce' ) ?>
        </div>
        <div id="yith-wcpmr-bacs-account-select" class="yith-wcpmr-restriction">
            <?php
            $bank_transfer_account = yith_wcpmr_get_bank_transfer_account();
            if( empty($bank_transfer_account) ) {

            } else{
                ?>
                <label for="yith-wcpmr-bacs-label"><?php _e( 'Select bank transfer account:', 'yith-payment-method-restrictions-for-woocommerce' ); ?></label>

                <?php echo yith_wcpmr_get_dropdown_multiple( array(
                    'name'     => 'yith-wcpmr-rule[banks_account][]',
                    'id'       => 'yith-wcpmr-bacs',
                    'style'    => '',
                    'multiple' => 'multiple',
                    'class'    => 'yith-wcpmr-rule yith-wcpmr-select yith-wcpmr-bacs',
                    'options'  => $bank_transfer_account,
                    'value'    => isset( $payment_restriction_rule[ 'banks_account' ] ) ? $payment_restriction_rule[ 'banks_account' ] : '',
                ) ); ?>

            <?php } ?>

        </div>
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
                    wc_get_template( 'wcpmr-conditions-row-premium.php', $args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
                    $i++;
                }
            }
            ?>

        </div>
        <input id="yith-wcpmr-new-condition" type="button" class="button-secondary yith-wcpmr-new-condition" value="<?php _e( '+ Add new condition', 'yith-payment-method-restrictions-for-woocommerce' ); ?>">
    </div>


    <div class="yith-wcpmr-message">
        <label for="yith-wcpmr-restriction-message"><?php _e( 'Message:', 'yith-payment-method-restrictions-for-woocommerce' ); ?></label>
        <p>
            <textarea id="yith-wcpmr-rule-message" name="yith-wcpmr-rule[message]" rows="5" cols="50"><?php echo isset( $payment_restriction_rule[ 'message' ] ) ? $payment_restriction_rule[ 'message' ] : '' ?></textarea>
            <span class="desc inline"><?php _e( 'Enter here the reason why the payment gateway is disabled',
                    'yith-payment-method-restrictions-for-woocommerce' ); ?></span>
        </p>

    </div>

</div>
