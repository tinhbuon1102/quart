<?php
$woocommerce_bacs_account = get_option('woocommerce_bacs_accounts',false);
$account_details = get_option('yith-wcpmr-bacs-accounts',false);

?>
<div class="yith_wcpmr_bank_accounts">
    <p scope="row" class="titledesc"><?php _e( 'Account details', 'yith-payment-method-restrictions-for-woocommerce' ); ?>:</p>
        <table class="widefat wc_input_table" id="bacs_accounts" cellspacing="0">
            <thead>
            <tr>
                <th class="sort">&nbsp;</th>
                <th><?php _e( 'Account name', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
                <th><?php _e( 'Account number', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
                <th><?php _e( 'Bank name', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
                <th><?php _e( 'Sort code', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
                <th><?php _e( 'IBAN', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
                <th><?php _e( 'BIC / Swift', 'yith-payment-method-restrictions-for-woocommerce' ); ?></th>
            </tr>
            </thead>
            <tbody class="yith-wcpmr-accounts">
            <?php
                if( $woocommerce_bacs_account ) {
                    foreach ( $woocommerce_bacs_account as $account ) {
                        $argument = array(
                                'bank_woo' => 'yith-wcpmr-woocommerce-bacs-account',
                        );
                        $args = wp_parse_args( $account,$argument );
                        extract($args);
                        wc_get_template( 'wcpmr-bacs-account.php',$args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
                    }
                }
                if ( $account_details ) {
                    foreach ( $account_details as $account ) {
                        $args = wp_parse_args( $account );
                        extract($args);
                        wc_get_template( 'wcpmr-bacs-account.php',$args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
                    }
                }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="7"><a href="#" class="add button"><?php _e( '+ Add account', 'yith-payment-method-restrictions-for-woocommerce' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)', 'yith-payment-method-restrictions-for-woocommerce' ); ?></a></th>
            </tr>
            </tfoot>
        </table>
    <div id="yith-wcpmr-custom-fields-tab-actions">
        <input type="submit" id="yith-wcpmr-custom-fields-tab-actions-save"
               class="button button-primary" value="<?php _e( 'Save', 'yith-payment-method-restrictions-for-woocommerce' ) ?>">
    </div>
</div>

