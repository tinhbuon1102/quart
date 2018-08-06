<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
$image_url = WBM_PLUGIN_URL . 'admin/images/right_click.png';
?>
<div class="dotstore_plugin_sidebar">

    <div class="dotstore_discount_voucher">
        <span class="dotstore_discount_title"><?php esc_html_e('Discount Voucher',  'woo-banner-management'); ?></span>
        <span class="dotstore-upgrade"><?php esc_html_e('Upgrade to premium now and get',  'woo-banner-management'); ?></span>
        <strong class="dotstore-OFF"><?php esc_html_e('10% OFF',  'woo-banner-management'); ?></strong>
        <span class="dotstore-with-code"><?php esc_html_e('with code',  'woo-banner-management'); ?><b><?php esc_html_e('FLAT10',  'woo-banner-management'); ?></b></span>
        <a class="dotstore-upgrade" href="<?php echo esc_url('store.multidots.com/woocommerce-category-banner-management'); ?>" target="_blank"><?php esc_html_e('Upgrade Now!',  'woo-banner-management'); ?></a>
    </div>

    <div class="dotstore-important-link">
        <div class="video-detail important-link">
            <a href="https://www.youtube.com/watch?v=rTL2pyH16Eo" target="_blank">
                <img width="100%" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/plugin-videodemo.png'); ?>" alt="Advanced Flat Rate Shipping For WooCommerce">
            </a>
        </div>
    </div>

    <div class="dotstore-important-link">
        <h2><span class="dotstore-important-link-title"><?php esc_html_e('Important link',  'woo-banner-management'); ?></span></h2>
        <div class="video-detail important-link">
            <ul>
                <li>
                    <img src="<?php echo $image_url; ?>">
                    <a target="_blank" href="<?php echo esc_url('https://store.multidots.com/wp-content/uploads/2017/02/Banner-Management-for-WooCommerce-help-document-.pdf'); ?>"><?php esc_html_e('Plugin documentation',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img src="<?php echo $image_url; ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/dotstore-support-panel'); ?>"><?php esc_html_e('Support platform',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img src="<?php echo $image_url; ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/suggest-a-feature'); ?>"><?php esc_html_e('Suggest A Feature',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img src="<?php echo $image_url; ?>">
                    <a  target="_blank" href="<?php echo esc_url('wordpress.org/plugins/banner-management-for-woocommerce/#developers'); ?>"><?php esc_html_e('Changelog',  'woo-banner-management'); ?></a>
                </li>
            </ul>
        </div>
    </div>

    <div class="dotstore-important-link">
        <h2><span class="dotstore-important-link-title"><?php esc_html_e('OUR POPULAR PLUGINS',  'woo-banner-management'); ?></span></h2>
        <div class="video-detail important-link">
            <ul>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/advance-flat-rate2.png'); ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/advanced-flat-rate-shipping-method-for-woocommerce'); ?>"><?php esc_html_e('Advanced Flat Rate Shipping Method',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/wc-conditional-product-fees.png'); ?>">
                    <a  target="_blank" href="<?php echo esc_url('store.multidots.com/woocommerce-conditional-product-fees-checkout'); ?>"><?php esc_html_e('WooCommerce Conditional Product Fees',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/advance-menu-manager.png'); ?>">
                    <a  target="_blank" href="<?php echo esc_url('store.multidots.com/advance-menu-manager-wordpress'); ?>"><?php esc_html_e('Advance Menu Manager',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/wc-enhanced-ecommerce-analytics-integration.png'); ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/woocommerce-enhanced-ecommerce-analytics-integration-with-conversion-tracking'); ?>"><?php esc_html_e('Woo Enhanced Ecommerce Analytics Integration',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img  class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/advanced-product-size-charts.png'); ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/woocommerce-advanced-product-size-charts'); ?>"><?php esc_html_e('Advanced Product Size Charts',  'woo-banner-management'); ?></a>
                </li>
                <li>
                    <img  class="sidebar_plugin_icone" src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/wc-blocker-prevent-fake-orders'); ?>">
                    <a target="_blank" href="<?php echo esc_url('store.multidots.com/woocommerce-blocker-prevent-fake-orders-blacklist-fraud-customers'); ?>"><?php esc_html_e('WooCommerce Blocker – Prevent Fake Orders',  'woo-banner-management'); ?></a>
                </li>
            </ul>
        </div>
        <div class="view-button">
            <a class="view_button_dotstore" target="_blank" href="<?php echo esc_url('store.multidots.com/plugins'); ?>store.multidots.com/plugins"><?php esc_html_e('VIEW ALL',  'woo-banner-management'); ?></a>
        </div>
    </div>

</div>
</div>
</div>