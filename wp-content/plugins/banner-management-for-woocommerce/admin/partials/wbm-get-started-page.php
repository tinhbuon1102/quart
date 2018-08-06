<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once('header/plugin-header.php');
global $wpdb;
?>
    <div class="wbm-main-table res-cl">
        <h2><?php esc_html_e('Thanks For Installing '.WOO_BANNER_MANAGEMENT_PLUGIN_NAME,  'woo-banner-management'); ?></h2>
        <table class="table-outer">
            <tbody>
            <tr>
                <td class="fr-2">
                    <p class="block gettingstarted"><strong><?php esc_html_e('Getting Started',  'woo-banner-management'); ?> </strong></p>
                    <p class="block textgetting">
                        <?php esc_html_e('Banner Management for WooCommerce plugin that allows you to manage page and category wise banners in your WooCommerce store.You can easily add banner in WooCommerce stores and you can upload the banner specific for page and category. You can easily add banner in WooCommerce stores and you can upload the banner specific for page,category and welcome page.',  'woo-banner-management'); ?>
                    </p>
                    <p class="block textgetting">
                        <?php esc_html_e('Add banner to shop page.',  'woo-banner-management'); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/Getting_Started_01.png'); ?>">
                        </span>
                    </p>
                    <p class="block textgetting">
                        <?php esc_html_e('Add banner to cart page.',  'woo-banner-management'); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/Getting_Started_02.png'); ?>">
                        </span>
                    </p>
                    <p class="block textgetting">
                        <?php esc_html_e('Add banner to checkout page.',  'woo-banner-management'); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/Getting_Started_03.png'); ?>">
                        </span>
                    </p>
                    <p class="block textgetting">
                        <?php esc_html_e('Add banner to thank you page.',  'woo-banner-management'); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/Getting_Started_04.png'); ?>">
                        </span>
                    </p>
                    <p class="block textgetting">
                        <?php esc_html_e('Add banner to category page.',  'woo-banner-management'); ?>
                        <span class="gettingstarted">
                            <img src="<?php echo esc_url(WBM_PLUGIN_URL . 'admin/images/Getting_Started_05.png'); ?>">
                        </span>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php require_once('header/plugin-sidebar.php'); ?>