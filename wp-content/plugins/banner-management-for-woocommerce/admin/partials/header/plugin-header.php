<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$plugin_name = WOO_BANNER_MANAGEMENT_PLUGIN_NAME;
$plugin_version = WBM_PLUGIN_VERSION;
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <header class="dots-header">
            <div class="dots-logo-main">
                <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/banner-management-for-woocommerce.png'); ?>">
            </div>
            <div class="dots-header-right">
                <div class="logo-detail">
                    <strong><?php esc_html_e($plugin_name,  'woo-banner-management'); ?></strong>
                    <span><?php esc_html_e('Free Version',  'woo-banner-management'); ?> <?php echo $plugin_version; ?></span>
                </div>

                <div class="button-dots">
                    <span class="upgrade_pro_image">
                        <a target="_blank" href="<?php echo esc_url('store.multidots.com/advanced-flat-rate-shipping-method-for-woocommerce'); ?>">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/upgrade_new.png'); ?>">
                        </a>
                    </span>
                    <span class="support_dotstore_image">
                        <a target="_blank" href="<?php echo esc_url('store.multidots.com/dotstore-support-panel'); ?>" >
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/support_new.png'); ?>">
                        </a>
                    </span>
                </div>
            </div>

            <?php
            $wbm_getting_started ='';
            $wbm_setting = isset($_GET['page']) && $_GET['page'] == 'banner-setting' ? 'active' : '';
            $wbm_add = isset($_GET['page']) && $_GET['page'] == 'wbm-add-new' ? 'active' : '';
            if (!empty($_GET['page']) && ($_GET['page'] == 'wbm-get-started')) {
                $wbm_getting_started = 'active';

            }
            $premium_version = isset($_GET['page']) && $_GET['page'] == 'wbm-premium' ? 'active' : '';
            $wbm_information = isset($_GET['page']) && $_GET['page'] == 'wbm-information' ? 'active' : '';

            if (isset($_GET['page']) && $_GET['page'] == 'wbm-information' || isset($_GET['page']) && $_GET['page'] == 'wbm-get-started') {
                $wbm_about = 'active';
            } else {
                $wbm_about = '';
            }
            if (!empty($_REQUEST['action'])) {
                if ($_REQUEST['action'] == 'add' || $_REQUEST['action'] == 'edit') {
                    $wbm_add = 'active';
                }
            }
            ?>
            <div class="dots-menu-main">
                <nav>
                    <ul>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr($wbm_setting); ?>"  href="<?php echo esc_url(home_url('/wp-admin/admin.php?page=banner-setting')); ?>"><?php esc_html_e('Settings',  'woo-banner-management'); ?></a>
                        </li>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr($premium_version); ?>"  href="<?php echo esc_url(home_url('/wp-admin/admin.php?page=wbm-premium')); ?>"><?php esc_html_e('Premium Version',  'woo-banner-management'); ?></a>
                        </li>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr($wbm_about); ?>"  href="<?php echo esc_url(home_url('/wp-admin/admin.php?page=wbm-get-started')); ?>"><?php esc_html_e('About Plugin',  'woo-banner-management'); ?></a>
                            <ul class="sub-menu">
                                <li><a  class="dotstore_plugin <?php echo esc_attr($wbm_getting_started); ?>" href="<?php echo esc_url(home_url('/wp-admin/admin.php?page=wbm-get-started')); ?>"><?php esc_html_e('Getting Started',  'woo-banner-management'); ?></a></li>
                                <li><a class="dotstore_plugin <?php echo esc_attr($wbm_information); ?>" href="<?php echo esc_url(home_url('/wp-admin/admin.php?page=wbm-information')); ?>"><?php esc_html_e('Quick info',  'woo-banner-management'); ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dotstore_plugin"  href="#"><?php esc_html_e('Dotstore',  'woo-banner-management'); ?></a>
                            <ul class="sub-menu">
                                <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/go/flatrate-pro-new-interface-woo-plugins'); ?>"><?php esc_html_e('WooCommerce Plugins',  'woo-banner-management'); ?></a></li>
                                <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/go/flatrate-pro-new-interface-wp-plugins'); ?>"><?php esc_html_e('Wordpress Plugins',  'woo-banner-management'); ?></a></li><br>
                                <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/go/flatrate-pro-new-interface-wp-free-plugins'); ?>"><?php esc_html_e('Free Plugins',  'woo-banner-management'); ?></a></li>
                                <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/go/flatrate-pro-new-interface-free-theme'); ?>"><?php esc_html_e('Free Themes',  'woo-banner-management'); ?></a></li>
                                <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/go/flatrate-pro-new-interface-dotstore-support'); ?>"><?php esc_html_e('Contact Support',  'woo-banner-management'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>
