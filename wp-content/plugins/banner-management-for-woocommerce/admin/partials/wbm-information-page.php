<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once('header/plugin-header.php');
global $wpdb;
?>

<div class="wbm-main-table res-cl">
    <h2><?php esc_html_e('Quick info', 'woo-banner-management'); ?></h2>
    <table class="table-outer">
        <tbody>
            <tr>
                <td class="fr-1"><?php esc_html_e('Product Type', 'woo-banner-management'); ?></td>
                <td class="fr-2"><?php esc_html_e('WooCommerce Plugin', 'woo-banner-management'); ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Product Name', 'woo-banner-management'); ?></td>
                <td class="fr-2"><?php esc_html_e($plugin_name, 'woo-banner-management'); ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Installed Version', 'woo-banner-management'); ?></td>
                <td class="fr-2"><?php esc_html_e('Free Version', 'woo-banner-management'); ?> <?php echo $plugin_version; ?></td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('License & Terms of use', 'woo-banner-management'); ?></td>
                <td class="fr-2">
                    <a target="_blank"  href="<?php echo esc_url('http://t.signauxdeux.com/e1t/c/5/f18dQhb0SmZ58dDMPbW2n0x6l2B9nMJW7sM9dn7dK_MMdBzM2-04?t=https%3A%2F%2Fstore.multidots.com%2Fterms-conditions%2F&si=4973901068632064&pi=61378fda-f5e5-4125-c521-28a4597b13d6'); ?>">
                    <?php esc_html_e('Click here', 'woo-banner-management'); ?></a>
                    <?php esc_html_e('to view license and terms of use.', 'woo-banner-management'); ?>
                </td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Help & Support', 'woo-banner-management'); ?></td>
                <td class="fr-2 wbm-information">
                    <ul>
                        <li><a target="_blank" href="<?php echo site_url('wp-admin/admin.php?page=wbm-get-started'); ?>"><?php esc_html_e('Quick Start', 'woo-banner-management'); ?></a></li>
                        <li><a target="_blank" href="<?php echo esc_url('https://store.multidots.com/wp-content/uploads/2017/02/Banner-Management-for-WooCommerce-help-document-.pdf'); ?>"><?php esc_html_e('Guide Documentation', 'woo-banner-management'); ?></a></li>
                        <li><a target="_blank" href="<?php echo esc_url('http://t.signauxdeux.com/e1t/c/5/f18dQhb0SmZ58dDMPbW2n0x6l2B9nMJW7sM9dn7dK_MMdBzM2-04?t=https%3A%2F%2Fstore.multidots.com%2Fdotstore-support-panel%2F&si=4973901068632064&pi=61378fda-f5e5-4125-c521-28a4597b13d6'); ?>"><?php esc_html_e('Support Forum', 'woo-banner-management'); ?></a></li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td class="fr-1"><?php esc_html_e('Localization', 'woo-banner-management'); ?></td>
                <td class="fr-2"><?php esc_html_e('English', 'woo-banner-management'); ?>, <?php esc_html_e('Spanish', 'woo-banner-management'); ?></td>
            </tr>

        </tbody>
    </table>
</div>
<?php require_once('header/plugin-sidebar.php'); ?>