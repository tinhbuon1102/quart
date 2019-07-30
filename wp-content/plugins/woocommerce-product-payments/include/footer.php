<?php

function softsdev_product_payments_footer_text($text) {
    if (isset($_GET['page']) && strpos(plugin_basename(wp_unslash($_GET['page'])), 'softsdev-product-payments') === 0) {
        $text = sprintf('If you enjoy using <strong>Woocommerce Payments Gateway per Product</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. A <strong style="text-decoration: underline;">huge</strong> thank you in advance!', 'https://wordpress.org/support/view/plugin-reviews/woocommerce-product-payments');
    }
    return $text;
}

function softdev_product_payments_update_footer($text) {
    if (isset($_GET['page']) && strpos(plugin_basename(wp_unslash($_GET['page'])), 'softsdev-product-payments') === 0) {
        $text = 'Version 2.5.7';
    }
    return $text;
}

?>