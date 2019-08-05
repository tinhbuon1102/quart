<?php

function yith_wcpmr_get_type_of_restrictions() {
    $type_restrictions = array(
        'default'           => __('Restriction by:','yith-payment-method-restrictions-for-woocommerce'),
        'category'          => __('Category','yith-payment-method-restrictions-for-woocommerce'),
        'tag'               => __('Tag','yith-payment-method-restrictions-for-woocommerce'),
        'product'           => __('Product','yith-payment-method-restrictions-for-woocommerce'),
        'price'             => __('Price','yith-payment-method-restrictions-for-woocommerce'),
        'geolocalization'   => __('Geolocalization','yith-payment-method-restrictions-for-woocommerce'),
        'role'              => __('Role','yith-payment-method-restrictions-for-woocommerce'),
    );

    return apply_filters('yith_wcmr_type_of_restrictions',$type_restrictions);
}

function yith_wcpmr_get_bank_transfer_account() {
    $woocommerce_bacs_account = get_option('woocommerce_bacs_accounts',array());
    $woo_banks = array();
    $i = 0;
    foreach ( $woocommerce_bacs_account as $account ) {
        $account_bacs = implode(' ',$account);
        if(isset($account_bacs) && !empty($account_bacs)) {
            $woo_banks['woo'.$i] = $account_bacs;
        }
        $i++;
    }

    $bacs_account =  get_option('yith-wcpmr-bacs-accounts',array());
    $banks = array();
    $j = 0;
    foreach ( $bacs_account as $account ) {
        $account_bacs = implode(' ',$account);
        if(isset($account_bacs)) {
            $banks['wcpmr'.$j] = $account_bacs;
        }
        $j++;
    }
    $bacs_accounts = array_merge($woo_banks,$banks);
    return $bacs_accounts;
}

function yith_wcpmr_get_current_ip(){
    $result = false;
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    $result = $ip;
                }
            }
        }
    }
    return apply_filters('yith_wcpmr_customer_ip', $result);
}

function yith_get_country_customer()
{
    $ip_address = yith_wcpmr_get_current_ip();
    $geolocation = WC_Geolocation::geolocate_ip($ip_address);

    if( empty($geolocation['country']) ) {
        $ip_external = WC_Geolocation::get_external_ip_address();
        $geolocation = WC_Geolocation::geolocate_ip($ip_external);
    }

    return $geolocation;

}

function yith_wcpmr_get_user_roles() {
    $roles_user = wp_roles()->roles;
    $role = array();
    foreach($roles_user as $roles=>$rol){
        $role[$roles] = $rol['name'];
    }
    $role['yith_guest'] = __('Guest','yith-payment-method-restrictions-for-woocommerce');
    return apply_filters('yith_wcdppm_get_user_roles',$role);
}