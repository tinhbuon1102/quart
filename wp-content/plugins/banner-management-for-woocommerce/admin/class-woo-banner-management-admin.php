<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://multidots.com
 * @since      1.0.0
 *
 * @package    Woo_Banner_Management
 * @subpackage Woo_Banner_Management/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Banner_Management
 * @subpackage Woo_Banner_Management/admin
 * @author     Multidots <info@multidots.com>
 */
class Woo_Banner_Management_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Banner_Management_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Banner_Management_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

            if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == "banner-setting" || $_GET['page'] == 'wbm-premium' || $_GET['page'] == 'wbm-get-started' || $_GET['page'] == 'wbm-information') || isset($_GET['taxonomy']) && !empty($_GET['taxonomy']) && ($_GET['taxonomy']=='product_cat')) {
                wp_enqueue_style('thickbox');
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-banner-management-admin.css', array('wp-jquery-ui-dialog'), $this->version, 'all');
                wp_enqueue_style('image-upload-category-css', plugin_dir_url(__FILE__) . 'css/woo-image-upload.css', array(), $this->version, 'all');
                wp_enqueue_style('wp-pointer');
                wp_enqueue_style($this->plugin_name . '-choose-css', plugin_dir_url(__FILE__) . 'css/chosen.min.css', array(), $this->version, 'all');
                wp_enqueue_style($this->plugin_name . '-jquery-ui-css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
                wp_enqueue_style($this->plugin_name . 'font-awesome', plugin_dir_url(__FILE__) . 'css/font-awesome.min.css', array(), $this->version, 'all');
                wp_enqueue_style($this->plugin_name . 'main-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), 'all');
                wp_enqueue_style($this->plugin_name . 'media-css', plugin_dir_url(__FILE__) . 'css/media.css', array(), 'all');
            }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Banner_Management_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Banner_Management_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (isset($_GET['page']) && !empty($_GET['page']) && ($_GET['page'] == "banner-setting" || $_GET['page'] == 'wbm-premium' || $_GET['page'] == 'wbm-get-started' || $_GET['page'] == 'wbm-information') || isset($_GET['taxonomy']) && !empty($_GET['taxonomy']) && ($_GET['taxonomy']=='product_cat')) {
                wp_enqueue_script('jquery-ui');
                wp_enqueue_script('jquery-ui-accordion');
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script('wp-pointer');
                wp_enqueue_media();
                wp_enqueue_script('thickbox');
                wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-banner-management-admin.js', array('jquery'), $this->version, false);
                wp_enqueue_script($this->plugin_name . '-choose-js', plugin_dir_url(__FILE__) . 'js/chosen.jquery.min.js', array('jquery', 'jquery-ui-datepicker'), $this->version, false);
                wp_enqueue_script($this->plugin_name . '-tablesorter-js', plugin_dir_url(__FILE__) . 'js/jquery.tablesorter.js', array('jquery'), $this->version, false);
                wp_enqueue_script('wbm-admin', plugin_dir_url(__FILE__) . 'js/wbm-admin.js', array('jquery'), $this->version,false);
            }
       
    }

    public function dot_store_menu_banner_management() {
        global $GLOBALS;
        if (empty($GLOBALS['admin_page_hooks']['dots_store'])) {
            add_menu_page(
                'DotStore Plugins', __('DotStore Plugins'), 'manage_option', 'dots_store', array($this, 'dot_store_menu_page'), WBM_PLUGIN_URL . 'admin/images/menu-icon.png', 25
            );
        }
        add_submenu_page('dots_store', __('Woo Banner Management', 'banner-setting'), __('Woo Banner Management', 'banner-setting'), 'manage_options', 'banner-setting', array($this, 'my_custom_submenu_page_callback'));
        add_submenu_page('dots_store', 'Get Started', 'Get Started', 'manage_options', 'wbm-get-started', array($this, 'wbm_get_started_page'));
        add_submenu_page('dots_store', 'Premium Version', 'Premium Version', 'manage_options', 'wbm-premium', array($this, 'premium_version_wbm_page'));
        add_submenu_page('dots_store', 'Introduction', 'Introduction', 'manage_options', 'wbm-information', array($this, 'wbm_information_page'));
    }

    public function dot_store_menu_page() {

    }

    public function wbm_information_page() {
        require_once('partials/wbm-information-page.php');
    }

    public function premium_version_wbm_page() {
        require_once('partials/wbm-premium-version-page.php');
    }

    public function wbm_get_started_page() {
        require_once('partials/wbm-get-started-page.php');
    }

    /**
     *  Set custom menu in woocommerce-benner-managment plugin
     */
    public function wbm_crea_custom_menu() {
        $wbm_page = 'woocommerce';
        $wbm_settings_page = add_submenu_page($wbm_page, __('Banner Management', 'banner-setting'), __('Banner Management', 'banner-setting'), 'manage_options', 'banner-setting', array(&$this, 'my_custom_submenu_page_callback'));
    }

    //custom call wbm setting page
    public function my_custom_submenu_page_callback() {
        wp_enqueue_media();
        $wbm_shop_page_stored_results_serialize_benner_src = '';
        $wbm_shop_page_stored_results_serialize_benner_link = '';
        $wbm_shop_page_stored_results_serialize_benner_enable_status = '';
        $wbm_shop_page_stored_results_serialize_benner_open_new_tab = '';

        $wbm_cart_page_stored_results_serialize_benner_src = '';
        $wbm_cart_page_stored_results_serialize_benner_link = '';
        $wbm_cart_page_stored_results_serialize_benner_enable_status = '';
        $wbm_cart_page_stored_results_serialize_benner_open_new_tab = '';

        $wbm_checkout_page_stored_results_serialize_benner_src = '';
        $wbm_checkout_page_stored_results_serialize_benner_link = '';
        $wbm_checkout_page_stored_results_serialize_benner_enable_status = '';
        $wbm_checkout_page_stored_results_serialize_benner_open_new_tab = '';

        $wbm_thankyou_page_stored_results_serialize_benner_src = '';
        $wbm_thankyou_page_stored_results_serialize_benner_link = '';
        $wbm_thankyou_page_stored_results_serialize_benner_enable_status = '';
        $wbm_thankyou_page_stored_results_serialize_benner_open_new_tab = '';


        $wbm_shop_page_stored_results = get_option('wbm_shop_page_stored_data', '');
        $wbm_cart_page_stored_results = get_option('wbm_cart_page_stored_data', '');
        $wbm_checkout_page_stored_results = get_option('wbm_checkout_page_stored_data', '');
        $wbm_thankyou_page_stored_results = get_option('wbm_thankyou_page_stored_data', '');

        // get shop page stored data 
        if (isset($wbm_shop_page_stored_results) && !empty($wbm_shop_page_stored_results)) {
            $wbm_shop_page_stored_results_serialize = maybe_unserialize($wbm_shop_page_stored_results);
            if (!empty($wbm_shop_page_stored_results_serialize)) {
                $wbm_shop_page_stored_results_serialize_benner_src = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_image_src']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_image_src'] : '';
                $wbm_shop_page_stored_results_serialize_benner_link = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_link_src']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_link_src'] : '';
                $wbm_shop_page_stored_results_serialize_benner_enable_status = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_enable_status']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_enable_status'] : '';
                $wbm_shop_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_shop_page_stored_results_serialize['shop_page_benner_open_new_tab']) ? $wbm_shop_page_stored_results_serialize['shop_page_benner_open_new_tab'] : '';
            }
        }
        //get cart setting page stored data
        if (isset($wbm_cart_page_stored_results) && !empty($wbm_cart_page_stored_results)) {
            $wbm_cart_page_stored_results_serialize = maybe_unserialize($wbm_cart_page_stored_results);
            if (!empty($wbm_cart_page_stored_results_serialize)) {
                $wbm_cart_page_stored_results_serialize_benner_src = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_image_src']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_image_src'] : '';
                $wbm_cart_page_stored_results_serialize_benner_link = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_link_src']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_link_src'] : '';
                $wbm_cart_page_stored_results_serialize_benner_enable_status = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_enable_status']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_enable_status'] : '';
                $wbm_cart_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_cart_page_stored_results_serialize['cart_page_benner_open_new_tab']) ? $wbm_cart_page_stored_results_serialize['cart_page_benner_open_new_tab'] : '';
            }
        }

        //get checkout setting page stored data
        if (isset($wbm_checkout_page_stored_results) && !empty($wbm_checkout_page_stored_results)) {
            $wbm_checkout_page_stored_results_serialize = maybe_unserialize($wbm_checkout_page_stored_results);
            if (!empty($wbm_checkout_page_stored_results_serialize)) {
                $wbm_checkout_page_stored_results_serialize_benner_src = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_image_src']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_image_src'] : '';
                $wbm_checkout_page_stored_results_serialize_benner_link = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_link_src']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_link_src'] : '';
                $wbm_checkout_page_stored_results_serialize_benner_enable_status = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_enable_status']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_enable_status'] : '';
                $wbm_checkout_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_benner_open_new_tab']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_benner_open_new_tab'] : '';
            }
        }

        //get thank you setting page stored data
        if (isset($wbm_thankyou_page_stored_results) && !empty($wbm_thankyou_page_stored_results)) {
            $wbm_thankyou_page_stored_results_serialize = maybe_unserialize($wbm_thankyou_page_stored_results);
            if (!empty($wbm_thankyou_page_stored_results_serialize)) {
                $wbm_thankyou_page_stored_results_serialize_benner_src = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_image_src']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_image_src'] : '';
                $wbm_thankyou_page_stored_results_serialize_benner_link = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_link_src']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_link_src'] : '';
                $wbm_thankyou_page_stored_results_serialize_benner_enable_status = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_enable_status']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_enable_status'] : '';
                $wbm_thankyou_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_benner_open_new_tab']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_benner_open_new_tab'] : '';
            }
        }

        require_once('partials/header/plugin-header.php');
        global $woocommerce; ?>
        <div class="wbm-main-table res-cl">
            <h2><?php esc_html_e('Banner Management Settings', 'woo-banner-management'); ?> </h2>
               <?php wp_nonce_field( 'category-ajax-nonce', 'category-ajax-nonce_field' ); ?>
            <div class="accordion">
                <table class="form-table table-outer product-fee-table">
                    <tbody>
                    <tr valign="top">
                        <td class="forminp mdtooltip"><div class="accordion-section">
                                <?php
                                $setting_enable_or_color = "red";
                                if ($wbm_shop_page_stored_results_serialize_benner_enable_status === 'on') {
                                    $setting_enable_or_not = " ( Enable ) ";
                                    $setting_enable_or_color = "green";
                                } else {
                                    $setting_enable_or_not = " ( Disable ) ";
                                    $setting_enable_or_color = "red";
                                }
                                ?>
                                <a class="accordion-section-title" href="#wbm-enable-banner-for-shpe-page"> <?php esc_html_e('Banner for shop page', 'woo-banner-management'); ?>   <span id="shop_page_status_enable_or_disable" class="shop_page_status_enable_or_disable" style="color:<?php echo esc_attr($setting_enable_or_color) ?>"><?php echo esc_attr($setting_enable_or_not); ?></span></a>
                                <div id="wbm-enable-banner-for-shpe-page" class="accordion-section-content">
                                    <table class="form-table" id="form-table-wbm-shop-page">
                                        <tbody>
                                        <tr>
                                            <th scope="row"><label class="wbm_leble_setting_css" for="wbm_enable_shop"><?php esc_html_e('Enable/Disable', 'woo-banner-management'); ?> </label></th>
                                            <td><input type="checkbox" value="on" id="wbm_shop_setting_enable" class="wbm_shop_setting_enable_or_not" <?php
                                                if ($wbm_shop_page_stored_results_serialize_benner_enable_status === 'on') {
                                                    echo " checked ";
                                                }
                                                ?>></td>
                                            <?php
                                            $shop_page_url_results = "#";
                                            $shop_page_url = get_permalink(wc_get_page_id('shop'));
                                            if (!empty($shop_page_url)) {
                                                $shop_page_url_results = $shop_page_url;
                                            }
                                            if ($wbm_shop_page_stored_results_serialize_benner_enable_status === 'on') {
                                                $shop_page_preview_content = '<strong>Preview:</strong> <a href=' . esc_url($shop_page_url_results) . '>Click here</a>';
                                            } else {
                                                $shop_page_preview_content = '';
                                            }
                                            ?>
                                            <input type="hidden" id="shop_page_hidden_url" value="<?php echo esc_attr($shop_page_url_results); ?>">
                                            <td><span class="Preview_link_for_shop_page"><?php echo wp_kses_post($shop_page_preview_content); ?></span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                    $display_option = 'block';
                                    if ($wbm_shop_page_stored_results_serialize_benner_enable_status != 'on') {
                                        $display_option = 'none';
                                    }
                                    ?>
                                    <div class="wbm_shop_page_enable_open_div" style="display:<?php echo esc_attr($display_option); ?>">
                                        <fieldset>
                                            <table class="form-table">
                                                <tbody>
                                                <tr>
                                                    <th scope="row"><label  class="wbm_leble_setting_css" for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label></th>
                                                    <td><a class='wbm_shop_page_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>  <a class='wbm_shop_page_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"></th>
                                                    <?php
                                                    if ($wbm_shop_page_stored_results_serialize_benner_src == '') {
                                                        $shop_page_benner_css = "none";
                                                    } else {
                                                        $shop_page_benner_css = "block";
                                                    }
                                                    ?>
                                                    <td><img class="wbm_shop_page_cat_banner_img_admin" style="display:<?php echo esc_attr($shop_page_benner_css); ?>" src="<?php echo esc_url($wbm_shop_page_stored_results_serialize_benner_src); ?>" /></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Banner Image Link', 'woo-banner-management'); ?></label></th>
                                                    <td><input type="url" id="shop_page_banner_image_link" name='term_meta[banner_link]' value='<?php echo esc_url($wbm_shop_page_stored_results_serialize_benner_link); ?>' /><p><label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click on the banner.', 'woo-banner-management'); ?></em></label></p>	</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Enable Open New Tab Link ', 'woo-banner-management'); ?></label></th>
                                                    <td><input type="checkbox" value="open" id="wbm_shop_open_new_tab" class="wbm_shop_open_new_tab_or_not" <?php
                                                        if ($wbm_shop_page_stored_results_serialize_benner_open_new_tab === 'open') {
                                                            echo " checked ";
                                                        }
                                                        ?>>
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </fieldset>
                                    </div>
                                </div><!--end .accordion-section-content-->
                            </div><!--end .accordion-section--></td>
                    </tr>
                    <tr>
                        <td class="forminp mdtooltip">
                            <div class="accordion-section">
                                <?php
                                $setting_enable_or_color_cart = "red";
                                $setting_enable_or_not_cart = " ( Disable ) ";
                                if ($wbm_cart_page_stored_results_serialize_benner_enable_status === 'on') {
                                    $setting_enable_or_not_cart = " ( Enable ) ";
                                    $setting_enable_or_color_cart = "green";
                                } else {
                                    $setting_enable_or_not_cart = " ( Disable ) ";
                                    $setting_enable_or_color = "red";
                                }
                                ?>
                                <a class="accordion-section-title" href="#wbm-enable-banner-for-cart-page"><?php esc_html_e('Banner for cart page', 'woo-banner-management'); ?> <span  id="cart_page_status_enable_or_disable"class="shop_page_status_enable_or_disable" style="color:<?php echo esc_attr($setting_enable_or_color_cart); ?>"> <?php echo esc_attr($setting_enable_or_not_cart); ?></span></a>
                                <div id="wbm-enable-banner-for-cart-page" class="accordion-section-content">
                                    <div class="woocommerce-banner-managment-cart-setting-admin">

                                        <table class="form-table">
                                            <tbody>
                                            <tr>
                                                <th scope="row"><label class="wbm_leble_setting_css"  for="wbm_enable_shop"><?php esc_html_e('Enable/Disable', 'woo-banner-management'); ?></label></th>
                                                <td><input type="checkbox" value="on" id="wbm_shop_setting_cart_enable" class="wbm_shop_setting_cart_enable_or_not" <?php
                                                    if ($wbm_cart_page_stored_results_serialize_benner_enable_status === 'on') {
                                                        echo " checked ";
                                                    }
                                                    ?>></td>
                                                <?php
                                                $cart_url_results = "#";
                                                $cart_url = wc_get_cart_url();
                                                if (!empty($cart_url)) {
                                                    $cart_url_results = $cart_url;
                                                }
                                                if ($wbm_cart_page_stored_results_serialize_benner_enable_status === 'on') {
                                                    $cart_page_preview_url = '<strong>Preview:</strong> <a href=' . esc_url($cart_url_results) . '>Click here</a>';
                                                } else {
                                                    $cart_page_preview_url = "";
                                                }
                                                ?>
                                                <input type="hidden" id="cart_page_hidden_url" value="<?php echo esc_url($cart_url_results); ?>">
                                                <td><span class="Preview_link_for_cart_page"><?php echo wp_kses_post($cart_page_preview_url); ?></span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <?php
                                        $display_option_cart = 'block';
                                        if ($wbm_cart_page_stored_results_serialize_benner_enable_status != 'on') {
                                            $display_option_cart = 'none';
                                        }
                                        ?>
                                        <div class="wbm-cart-upload-image-html" style="display:<?php echo esc_attr($display_option_cart); ?>">
                                            <fieldset>
                                                <table class="form-table">
                                                    <tbody>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label></th>
                                                        <td><a class='wbm_cart_page_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>  <a class='wbm_cart_page_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <?php
                                                        if ($wbm_cart_page_stored_results_serialize_benner_src == '') {
                                                            $cart_page_image_css = "none";
                                                        } else {
                                                            $cart_page_image_css = "block";
                                                        }
                                                        ?>
                                                        <td><img class="wbm_cart_page_cat_banner_img_admin" style="display:<?php echo esc_attr($cart_page_image_css); ?>" src="<?php echo esc_url($wbm_cart_page_stored_results_serialize_benner_src); ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Banner Image Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="url" id="shop_cart_banner_image_link" name='term_meta[banner_link]' value='<?php echo esc_url($wbm_cart_page_stored_results_serialize_benner_link); ?>' /><p><label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click on the banner.', 'woo-banner-management'); ?></em></label></p>	</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Enable Open New Tab Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="checkbox" value="open" id="wbm_cart_open_new_tab" class="wbm_cart_open_new_tab_or_not" <?php
                                                            if ($wbm_cart_page_stored_results_serialize_benner_open_new_tab === 'open') {
                                                                echo " checked ";
                                                            }
                                                            ?>>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div><!--end .accordion-section-content-->
                            </div><!--end .accordion-section-->
                        </td>
                    </tr>
                    <tr>
                        <td class="forminp mdtooltip">
                            <div class="accordion-section">
                                <?php
                                $setting_enable_or_color_checkout = "red";
                                $setting_enable_or_not_checkout = " ( Disable ) ";
                                if ($wbm_checkout_page_stored_results_serialize_benner_enable_status === 'on') {
                                    $setting_enable_or_not_checkout = " ( Enable ) ";
                                    $setting_enable_or_color_checkout = "green";
                                } else {
                                    $setting_enable_or_not_checkout = " ( Disable ) ";
                                    $setting_enable_or_color_checkout = "red";
                                }
                                ?>
                                <a class="accordion-section-title" href="#wbm-enable-banner-for-checkout-page"><?php esc_html_e('Banner for checkout page ', 'woo-banner-management'); ?><span id="checkout_page_status_enable_or_disable" class="shop_page_status_enable_or_disable" style="color:<?php echo esc_attr($setting_enable_or_color_checkout); ?>"><?php echo esc_attr($setting_enable_or_not_checkout); ?> </span></a>
                                <div id="wbm-enable-banner-for-checkout-page" class="accordion-section-content">
                                    <div class="woocommerce-banner-managment-checkout-setting-admin">
                                        <table class="form-table">

                                            <tbody>
                                            <tr>
                                                <th scope="row"><label class="wbm_leble_setting_css" for="wbm_enable_shop"><?php esc_html_e('Enable/Disable ', 'woo-banner-management'); ?></label></th>
                                                <td><input type="checkbox" value="on" id="wbm_shop_setting_checkout_enable" class="wbm_shop_setting_checkout_enable_or_not" <?php
                                                    if ($wbm_checkout_page_stored_results_serialize_benner_enable_status === 'on') {
                                                        echo " checked ";
                                                    }
                                                    ?>></td>
                                                <?php
                                                $CheckOut_url_real = "#";
                                                $CheckOut_url = wc_get_checkout_url();
                                                if (!empty($CheckOut_url)) {
                                                    $CheckOut_url_real = $CheckOut_url;
                                                }
                                                if ($wbm_checkout_page_stored_results_serialize_benner_enable_status === 'on') {
                                                    $check_out_preview_content = '<strong>Preview :</strong> <a href=' . esc_url($CheckOut_url_real) . '>Click here</a>';
                                                } else {
                                                    $check_out_preview_content = "";
                                                }
                                                ?>

                                                <input type="hidden" id="checkout_page_hidden_url" value="<?php echo esc_url($CheckOut_url_real); ?>">
                                                <td><span class="Preview_link_for_checkout_page"><?php echo wp_kses_post($check_out_preview_content); ?></span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <?php
                                        $display_option_checkout = 'block';
                                        if ($wbm_checkout_page_stored_results_serialize_benner_enable_status != 'on') {
                                            $display_option_checkout = 'none';
                                        }
                                        ?>
                                        <div class="wbm-checkout-upload-image-html" style="display:<?php echo esc_attr($display_option_checkout); ?>">
                                            <fieldset>
                                                <table class="form-table">
                                                    <tbody>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label></th>
                                                        <td><a class='wbm_checkout_page_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>  <a class='wbm_checkout_page_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <?php
                                                        if ($wbm_checkout_page_stored_results_serialize_benner_src == '') {
                                                            $checkout_banner_image_css = "none";
                                                        } else {
                                                            $checkout_banner_image_css = "block";
                                                        }
                                                        ?>
                                                        <td><img class="wbm_checkout_page_banner_img_admin" style="display:<?php echo esc_attr($checkout_banner_image_css); ?>" src="<?php echo esc_url($wbm_checkout_page_stored_results_serialize_benner_src); ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Banner Image Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="url" id="shop_checkout_banner_image_link" name='term_meta[banner_link]' value='<?php echo esc_attr($wbm_checkout_page_stored_results_serialize_benner_link); ?>' /><p><label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click on the banner.', 'woo-banner-management'); ?></em></label></p></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Enable Open New Tab Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="checkbox" value="open" id="wbm_checkout_open_new_tab" class="wbm_checkout_open_new_tab_or_not" <?php
                                                            if ($wbm_checkout_page_stored_results_serialize_benner_open_new_tab === 'open') {
                                                                echo " checked ";
                                                            }
                                                            ?>>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div><!--end .accordion-section-content-->
                            </div><!--end .accordion-section-->
                        </td>
                    </tr>
                    <tr>
                        <td class="forminp mdtooltip">
                            <div class="accordion-section">
                                <?php
                                $setting_enable_or_color_thankyou = "red";
                                $setting_enable_or_not_thankyou = " ( Disable ) ";
                                if ($wbm_thankyou_page_stored_results_serialize_benner_enable_status === 'on') {
                                    $setting_enable_or_not_thankyou = " ( Enable ) ";
                                    $setting_enable_or_color_thankyou = "green";
                                } else {
                                    $setting_enable_or_not_thankyou = " ( Disable ) ";
                                    $setting_enable_or_color_thankyou = "red";
                                }
                                ?>
                                <a class="accordion-section-title" href="#wbm-enable-banner-for-thankyou-page"><?php esc_html_e('Banner for thank you page ', 'woo-banner-management'); ?><span id="thankyou_page_status_enable_or_disable" class="shop_page_status_enable_or_disable" style="color:<?php echo esc_attr($setting_enable_or_color_thankyou); ?>"><?php echo esc_attr($setting_enable_or_not_thankyou); ?></span></a>
                                <div id="wbm-enable-banner-for-thankyou-page" class="accordion-section-content">
                                    <div class="woocommerce-banner-managment-thank-you-setting-admin">
                                        <table class="form-table">
                                            <tbody>
                                            <tr>
                                                <th scope="row"><label class="wbm_leble_setting_css"  for="wbm_enable_shop"><?php esc_html_e('Enable/Disable', 'woo-banner-management'); ?></label></th>
                                                <td><input type="checkbox" value="on" id="wbm_shop_setting_thank_you_page_enable" class="wbm_shop_setting_thank_you_page_enable_or_not" <?php
                                                    if ($wbm_thankyou_page_stored_results_serialize_benner_enable_status === 'on') {
                                                        echo " checked ";
                                                    }
                                                    ?>></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <?php
                                        $display_option_checkout = 'block';
                                        if ($wbm_thankyou_page_stored_results_serialize_benner_enable_status != 'on') {
                                            $display_option_checkout = 'none';
                                        }
                                        ?>
                                        <div class="wbm-thank-you-page-upload-image-html" style="display:<?php echo esc_attr($display_option_checkout); ?>">
                                            <fieldset>
                                                <table class="form-table">
                                                    <tbody>
                                                    <tr>
                                                        <th scope="row"><label class="wbm_leble_setting_css"  for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label></th>
                                                        <td><a class='wbm_thank_you_page_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>  <a class='wbm_checkout_page_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"></th>
                                                        <?php
                                                        if ($wbm_thankyou_page_stored_results_serialize_benner_src == '') {
                                                            $thankyou_page_image_css = "none";
                                                        } else {
                                                            $thankyou_page_image_css = "block";
                                                        }
                                                        ?>
                                                        <td><img class="wbm_thank_you_page_banner_img_admin" style="display:<?php echo esc_attr($thankyou_page_image_css); ?>"src="<?php echo esc_url($wbm_thankyou_page_stored_results_serialize_benner_src); ?>" /></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Banner Image Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="url" id="shop_thank_you_page_banner_image_link" name='term_meta[banner_link]' value='<?php echo esc_attr($wbm_thankyou_page_stored_results_serialize_benner_link); ?>' /><p><label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click  on the banner.', 'woo-banner-management'); ?></em></label></p>	</td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><label  class="wbm_leble_setting_css" for="banner_link"><?php esc_html_e('Enable Open New Tab Link', 'woo-banner-management'); ?></label></th>
                                                        <td><input type="checkbox" value="open" id="wbm_thankyou_open_new_tab" class="wbm_thankyou_open_new_tab_or_not" <?php
                                                            if ($wbm_thankyou_page_stored_results_serialize_benner_open_new_tab === 'open') {
                                                                echo " checked ";
                                                            }
                                                            ?>>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div><!--end .accordion-section-content-->
                            </div><!--end .accordion-section-->
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="sub-title">
                <h2><?php esc_html_e('Category specific banner', 'woo-banner-management'); ?></h2>
            </div>
            <table id="tbl-product-fee" class="tbl_product_fee table-outer tap-cas form-table product-fee-table">
                <tbody>
                <tr>
                    <td class="forminp mdtooltip">
                        <div class="category_based_settings">
                            <p><?php esc_html_e('You can upload custom banner at the top of your product category pages. Easily update the image through your product category edit page.', 'woo-banner-management'); ?></p> </br>
                            <img  class="preview_category_page_image" src="<?php echo esc_url(WooCommerce_Banner_Management_Url .'admin/assets/images/category_setting_image.png'); ?>">
                            <p><strong><?php esc_html_e('Go to category page', 'woo-banner-management'); ?></strong> <a target="_blank" href="<?php echo esc_url(site_url() . '/wp-admin/edit-tags.php?taxonomy=product_cat&post_type=product'); ?>"><?php esc_html_e('click here', 'woo-banner-management'); ?></a></p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="button" name="save_wbmshop" id="save_wbm_shop_page_setting" class="button button-primary" value="<?php esc_html_e('Save Changes', 'woo-banner-management'); ?>">
        </div>

        <?php

        require_once('partials/header/plugin-sidebar.php');
    }

    /**
     *
     *  set the custom html for category add fiel
     *
     */
    function wbm_product_add_taxonomy_custom_fields($tag) {
        ?>
        <script>
            jQuery(document).ajaxComplete(function(event, request, options) {
                if (request && 4 === request.readyState && 200 === request.status
                    && options.data && 0 <= options.data.indexOf('action=add-tag')) {

                    var res = wpAjax.parseAjaxResponse(request.responseXML, 'ajax-response');
                    if (!res || res.errors) {
                        return;
                    }
                    // Clear Thumbnail fields on submit
                    jQuery('#add_cat_banner_img_admin').find('img').attr('src', '<?php echo esc_js(wc_placeholder_img_src()); ?>');
                    jQuery('#product_cat_thumbnail_id').val('');
                    jQuery('.remove_image_button').hide();
                    // Clear Display type field on submit
                    jQuery('#display_type').val('');
                    jQuery('.add_cat_banner_img_admin').css('display', 'none');
                    jQuery('.auto_display_banner').val('');
                    jQuery('.term_meta_link_add').val('');
                    jQuery('.term_meta_link_add').val('');
                    $('input:checkbox[id=auto_display_banner_create_new]').css('input[type=checkbox]:checked:before', 'content:\f147;');

                    return;
                }
            });
        </script>
        <tr class="form-field mdwbm_banner_url_form_field">
            <th scope="row" valign="top">
                <label for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label>
            </th>
            <td>
                <fieldset>
                    <a class='mdwbm_add_cat_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>
                    <a class='mdwbm_add_cat_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a>
                </fieldset>
                <fieldset>
                    <img class="add_cat_banner_img_admin" style="display:none" src="" />
                </fieldset>
                <input type="hidden" class='mdwbm_image' name='term_meta[banner_url_id]' value='' />
            </td>
        </tr>
        <tr class="form-field banner_link_form_field">
            <th scope="row" valign="top">
                <label for="banner_link"><?php esc_html_e('Banner image link', 'woo-banner-management'); ?></label>
            </th>
            <td>
                <fieldset>
                    <input type="url" name='term_meta[banner_link]' class="term_meta_link_add" value='' />
                    <label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click on the banner.', 'woo-banner-management'); ?></em></label>
                </fieldset>
            </td>
        </tr>
        <tr class="form-field auto_display_banner">
            <th scope="row" valign="top">
                <label for="auto_display_banner"><?php esc_html_e('Automatically insert banner above main content', 'woo-banner-management'); ?></label>
            </th>
            <td>
                <fieldset>
                    <input name="term_meta[auto_display_banner]" type="checkbox" checked value="on" class="auto_display_banner" id="auto_display_banner_create_new"/>
                    <label class="auto_display_banner_label" for="auto_display_banner"><em></em></label>
                </fieldset>
            </td>
        </tr>
        <?php
    }

    /**
     * 	Set the custom html for category edit field
     *
     */
    function WBM_product_cat_taxonomy_custom_fields($tag) {
        $t_id = $tag->term_id;
        $term_meta = get_option("taxonomy_term_$t_id");
        // Get banner image
        if (isset($term_meta['banner_url_id']) and $term_meta['banner_url_id'] != '') {
            $banner_id = $term_meta['banner_url_id'];
        } else {
            $banner_id = null;
        }

        // Get banner link 
        if (isset($term_meta['banner_link']) and $term_meta['banner_link'] != '')
            $banner_link = $term_meta['banner_link'];
        else
            $banner_link = null;

        if ((isset($term_meta['auto_display_banner']) && $term_meta['auto_display_banner'] == 'on') || !isset($term_meta['auto_display_banner'])) {
            $auto_display_banner = true;
        } else {
            $auto_display_banner = false;
        }
        wp_nonce_field(basename(__FILE__), 'tax_banner_nonce');
        ?>
        <tr class="form-field mdwbm_banner_url_form_field">
            <th scope="row" valign="top">
                <label for="banner_url"><?php esc_html_e('Banner Image', 'woo-banner-management'); ?></label>
            </th>
            <td>
                <fieldset>
                    <a class='mdwbm_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'><?php esc_html_e('Upload File', 'woo-banner-management'); ?></a>
                    <a class='mdwbm_remove_file button'><?php esc_html_e('Remove File', 'woo-banner-management'); ?></a>
                </fieldset>

                <fieldset>
                    <?php
                    if ($banner_id == '') {
                        $category_banner_css = "none";
                    } else {
                        $category_banner_css = "block";
                    }
                    ?>
                    <img class="cat_banner_img_admin" style="display:<?php echo esc_attr($category_banner_css); ?>"src="<?php if ($banner_id != null) echo esc_url(wp_get_attachment_url($banner_id)); ?>" />
                </fieldset>

                <input type="hidden" class='mdwbm_image' name='term_meta[banner_url_id]' value='<?php if ($banner_id != null) echo esc_attr($banner_id); ?>' />
            </td>
        </tr>
        <tr class="form-field banner_link_form_field">
            <th scope="row" valign="top">
                <label for="banner_link"><?php esc_html_e('Banner image link'); ?></label>
            </th>
            <td>
                <fieldset>
                    <input type="url" name='term_meta[banner_link]' value='<?php if ($banner_link != null) echo esc_url($banner_link); ?>' />
                    <label class="banner_link_label" for="banner_link"><em><?php esc_html_e('Where users will be directed if they click on the banner.', 'woo-banner-management'); ?></em></label>
                </fieldset>
            </td>
        </tr>
        <tr class="form-field auto_display_banner">
            <th scope="row" valign="top">
                <label for="auto_display_banner"><?php esc_html_e('Automatically insert banner above main content', 'woo-banner-management'); ?></label>
            </th>
            <td>
                <fieldset>
                    <input name="term_meta[auto_display_banner]" type="checkbox" value="on" class="auto_display_banner" <?php if ($auto_display_banner) echo " checked "; ?>/>
                    <label class="auto_display_banner_label" for="auto_display_banner"><em></em></label>
                </fieldset>
            </td>
        </tr>

        <?php
    }

    /**
     * Save the Woocommerce-Banner-Managment Category Data
     *
     * @param  $term_id
     */
    function WBM_product_cat_save_taxonomy_custom_fields($term_id) {
        
        
        if (isset($_POST['term_meta'])) {
            // verify meta box nonce
            if (!isset($_POST['tax_banner_nonce']) || !wp_verify_nonce($_POST['tax_banner_nonce'], basename(__FILE__))) {
                return;
            }
        
            $t_id = $term_id;
            $term_meta = get_option("taxonomy_term_$t_id");
            $posted_term_meta = array_map( 'sanitize_text_field', wp_unslash( $_POST['term_meta'] ) );

            if (!isset($posted_term_meta['auto_display_banner']))
                $posted_term_meta['auto_display_banner'] = 'off';

            $cat_keys = array_keys($posted_term_meta);

            foreach ($cat_keys as $key) {
                if (isset($posted_term_meta[$key])) {
                    $term_meta[$key] = $posted_term_meta[$key];
                }
            }
            //save the option array  
            update_option("taxonomy_term_$t_id", $term_meta);
        }
    }

    /**
     * Save WBM shop page setting
     *
     */
    public function wbm_save_shop_page_banner_data() {
        
        // verify nonce
        check_ajax_referer( 'category-ajax-nonce', 'security', false );

        $shop_page_banner_image_results = !empty($_POST['shop_page_banner_image_results']) ? sanitize_text_field(wp_unslash($_POST['shop_page_banner_image_results'])) : '';
        $shop_page_banner_link_results = !empty($_POST['shop_page_banner_link_results']) ? sanitize_text_field(wp_unslash($_POST['shop_page_banner_link_results'])) : '';
        $shop_page_banner_enable_or_not_results = !empty($_POST['shop_page_banner_enable_or_not_results']) ? sanitize_text_field(wp_unslash($_POST['shop_page_banner_enable_or_not_results'])) : '';
        $shop_page_benner_open_new_tab_results = !empty($_POST['shop_page_benner_open_new_tab_results']) ? sanitize_text_field(wp_unslash($_POST['shop_page_benner_open_new_tab_results'])) : '';

        $cart_page_banner_image_results = !empty($_POST['cart_page_banner_image_results']) ? sanitize_text_field(wp_unslash($_POST['cart_page_banner_image_results'])) : '';
        $cart_page_banner_link_results = !empty($_POST['cart_page_banner_link_results']) ? sanitize_text_field(wp_unslash($_POST['cart_page_banner_link_results'])) : '';
        $cart_page_banner_enable_or_not_results = !empty($_POST['cart_page_banner_enable_or_not_results']) ? sanitize_text_field(wp_unslash($_POST['cart_page_banner_enable_or_not_results'])) : '';
        $cart_page_benner_open_new_tab_results = !empty($_POST['cart_page_benner_open_new_tab_results']) ? sanitize_text_field(wp_unslash($_POST['cart_page_benner_open_new_tab_results'])) : '';

        $checkout_page_banner_image_results = !empty($_POST['checkout_page_banner_image_results']) ? sanitize_text_field(wp_unslash($_POST['checkout_page_banner_image_results'])) : '';
        $checkout_page_banner_link_results = !empty($_POST['checkout_page_banner_link_results']) ? sanitize_text_field(wp_unslash($_POST['checkout_page_banner_link_results'])) : '';
        $checkout_page_banner_enable_or_not_results = !empty($_POST['checkout_page_banner_enable_or_not_results']) ? sanitize_text_field(wp_unslash($_POST['checkout_page_banner_enable_or_not_results'])) : '';
        $checkout_page_benner_open_new_tab_results = !empty($_POST['checkout_page_benner_open_new_tab_results']) ? sanitize_text_field(wp_unslash($_POST['checkout_page_benner_open_new_tab_results'])) : '';

        $thankyou_page_banner_image_results = !empty($_POST['thankyou_page_banner_image_results']) ? sanitize_text_field(wp_unslash($_POST['thankyou_page_banner_image_results'])) : '';
        $thankyou_page_banner_link_results = !empty($_POST['thankyou_page_banner_link_results']) ? sanitize_text_field(wp_unslash($_POST['thankyou_page_banner_link_results'])) : '';
        $thankyou_page_banner_enable_or_not_results = !empty($_POST['thankyou_page_banner_enable_or_not_results']) ? sanitize_text_field(wp_unslash($_POST['thankyou_page_banner_enable_or_not_results'])) : '';
        $thankyou_page_benner_open_new_tab_results = !empty($_POST['thankyou_page_benner_open_new_tab_results']) ? sanitize_text_field(wp_unslash($_POST['thankyou_page_benner_open_new_tab_results'])) : '';

        $shop_page_data_stored_array = array(
            'shop_page_banner_image_src' => $shop_page_banner_image_results,
            'shop_page_banner_link_src' => $shop_page_banner_link_results,
            'shop_page_banner_enable_status' => $shop_page_banner_enable_or_not_results,
            'shop_page_benner_open_new_tab' => $shop_page_benner_open_new_tab_results,
        );

        $cart_page_data_stored_array = array(
            'cart_page_banner_image_src' => $cart_page_banner_image_results,
            'cart_page_banner_link_src' => $cart_page_banner_link_results,
            'cart_page_banner_enable_status' => $cart_page_banner_enable_or_not_results,
            'cart_page_benner_open_new_tab' => $cart_page_benner_open_new_tab_results,
        );

        $checkout_page_data_stored_array = array(
            'checkout_page_banner_image_src' => $checkout_page_banner_image_results,
            'checkout_page_banner_link_src' => $checkout_page_banner_link_results,
            'checkout_page_banner_enable_status' => $checkout_page_banner_enable_or_not_results,
            'checkout_page_benner_open_new_tab' => $checkout_page_benner_open_new_tab_results,
        );

        $thankyou_page_data_stored_array = array(
            'thankyou_page_banner_image_src' => $thankyou_page_banner_image_results,
            'thankyou_page_banner_link_src' => $thankyou_page_banner_link_results,
            'thankyou_page_banner_enable_status' => $thankyou_page_banner_enable_or_not_results,
            'thankyou_page_benner_open_new_tab' => $thankyou_page_benner_open_new_tab_results,
        );

        update_option('wbm_shop_page_stored_data', $shop_page_data_stored_array);
        update_option('wbm_cart_page_stored_data', $cart_page_data_stored_array);
        update_option('wbm_checkout_page_stored_data', $checkout_page_data_stored_array);
        update_option('wbm_thankyou_page_stored_data', $thankyou_page_data_stored_array);
        die();
        /* update_option('',) */
    }

    /**
     * Show Category Banner In Category Page
     *
     */
    public function WBM_show_category_banner() {
        global $woocommerce;
        global $wp_query;

        // Make sure this is a product category page
        if (is_product_category()) {
            $cat_id = $wp_query->queried_object->term_id;

            $term_options = get_option("taxonomy_term_$cat_id");

            if ((isset($term_options['auto_display_banner']) && $term_options['auto_display_banner'] == 'on') || !isset($term_options['auto_display_banner'])) {
                // Get the banner image id
                if ($term_options['banner_url_id'] != '')
                    $url = esc_url(wp_get_attachment_url($term_options['banner_url_id']));

                // Exit if the image url doesn't exist
                if (!isset($url) or $url == false)
                    return;

                // Get the banner link if it exists
                if ($term_options['banner_link'] != '')
                    $link = $term_options['banner_link'];

                // Print Output
                if (isset($link)) {
                    if ($link == '') {
                        echo "<a>";
                    } else {
                        echo "<a href='" . esc_url($link) . "' target='_blank'>";
                    }
                }
                if ($url != false) {
                    echo "<img src='" . esc_url($url) . "' class='wbm_category_banner_image' />";
                }
                if (isset($link)) {
                    echo "</a>";
                }
            }
        }
    }

    /**
     * Function For display the banner image in shop page
     *
     *
     */
    public function wbm_show_shop_page_banner() {
        global $woocommerce, $wp_query, $wpdb;

        $wbm_shop_page_stored_results_serialize_benner_src = '';
        $wbm_shop_page_stored_results_serialize_benner_link = '';
        $wbm_shop_page_stored_results_serialize_benner_enable_status = '';
        $wbm_shop_page_stored_results_serialize_benner_open_new_tab = '';
        $alt_tag_value = '';

        $wbm_shop_page_stored_results = get_option('wbm_shop_page_stored_data', '');
        if (isset($wbm_shop_page_stored_results) && !empty($wbm_shop_page_stored_results)) {
            $wbm_shop_page_stored_results_serialize = maybe_unserialize($wbm_shop_page_stored_results);
            if (!empty($wbm_shop_page_stored_results_serialize)) {
                $wbm_shop_page_stored_results_serialize_benner_src = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_image_src']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_image_src'] : '';
                $wbm_shop_page_stored_results_serialize_benner_link = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_link_src']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_link_src'] : '';
                $wbm_shop_page_stored_results_serialize_benner_enable_status = !empty($wbm_shop_page_stored_results_serialize['shop_page_banner_enable_status']) ? $wbm_shop_page_stored_results_serialize['shop_page_banner_enable_status'] : '';
                $wbm_shop_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_shop_page_stored_results_serialize['shop_page_benner_open_new_tab']) ? $wbm_shop_page_stored_results_serialize['shop_page_benner_open_new_tab'] : '';

                $wbm_shop_page_stored_results_serialize_benner_alt = array_reverse(explode('/', $wbm_shop_page_stored_results_serialize_benner_src));
                if (!empty($wbm_shop_page_stored_results_serialize_benner_src)) {
                    if (!empty($wbm_shop_page_stored_results_serialize_benner_alt)) {
                        $wbm_shop_page_stored_results_serialize_benner_alt_results = array_reverse(explode('.', $wbm_shop_page_stored_results_serialize_benner_src));
                        if (!empty($wbm_shop_page_stored_results_serialize_benner_alt_results)) {
                            $wbm_shop_page_stored_results_serialize_benner_alt_results = array_reverse(explode('/', $wbm_shop_page_stored_results_serialize_benner_alt_results[1]));
                            $alt_tag_value = $wbm_shop_page_stored_results_serialize_benner_alt_results[0];
                        }
                    }
                }
            }
        }

        if (is_shop()) {
            if (!empty($wbm_shop_page_stored_results_serialize_benner_open_new_tab) && $wbm_shop_page_stored_results_serialize_benner_open_new_tab === 'open') {
                $test="_blank";
            }
            else
            {
                $test="_self";
            }
            ?>
            <?php if (!empty($wbm_shop_page_stored_results_serialize_benner_enable_status) && $wbm_shop_page_stored_results_serialize_benner_enable_status === 'on') {
                ?>
                <div class="wbm_banner_image">
                    <?php
                    if ($wbm_shop_page_stored_results_serialize_benner_link == '') {
                        $alt_tag_css_shop_page_fornt = 'style="cursor:default"';
                    } else {
                        $alt_tag_css_shop_page_fornt = 'style=cursor:pointer href=' . esc_url($wbm_shop_page_stored_results_serialize_benner_link) . ' target='. esc_attr($test) .'';
                    }
                    ?>
                    <a <?php echo esc_attr($alt_tag_css_shop_page_fornt); ?>>
                        <p><img src="<?php echo esc_url($wbm_shop_page_stored_results_serialize_benner_src); ?>" class="category_banner_image" alt="<?php echo esc_attr($alt_tag_value); ?>"></p>
                    </a>
                </div>
                <?php
            }
        }
    }

    /**
     * Function For display banner image in cart page
     *
     */
    public function wbm_show_cart_page_banner() {
        $wbm_cart_page_stored_results_serialize_benner_src = '';
        $wbm_cart_page_stored_results_serialize_benner_link = '';
        $wbm_cart_page_stored_results_serialize_benner_enable_status = '';
        $wbm_cart_page_stored_results_serialize_benner_open_new_tab = '';
        $alt_tag_value = '';

        $wbm_cart_page_stored_results = get_option('wbm_cart_page_stored_data', '');
        if (isset($wbm_cart_page_stored_results) && !empty($wbm_cart_page_stored_results)) {
            $wbm_cart_page_stored_results_serialize = maybe_unserialize($wbm_cart_page_stored_results);
            if (!empty($wbm_cart_page_stored_results_serialize)) {
                $wbm_cart_page_stored_results_serialize_benner_src = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_image_src']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_image_src'] : '';
                $wbm_cart_page_stored_results_serialize_benner_link = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_link_src']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_link_src'] : '';
                $wbm_cart_page_stored_results_serialize_benner_enable_status = !empty($wbm_cart_page_stored_results_serialize['cart_page_banner_enable_status']) ? $wbm_cart_page_stored_results_serialize['cart_page_banner_enable_status'] : '';
                $wbm_cart_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_cart_page_stored_results_serialize['cart_page_benner_open_new_tab']) ? $wbm_cart_page_stored_results_serialize['cart_page_benner_open_new_tab'] : '';

                $wbm_cart_page_stored_results_serialize_benner_alt = array_reverse(explode('/', $wbm_cart_page_stored_results_serialize_benner_src));
                if (!empty($wbm_cart_page_stored_results_serialize_benner_src)) {
                    if (!empty($wbm_cart_page_stored_results_serialize_benner_alt)) {
                        $wbm_cart_page_stored_results_serialize_benner_alt_results = array_reverse(explode('.', $wbm_cart_page_stored_results_serialize_benner_src));
                        if (!empty($wbm_cart_page_stored_results_serialize_benner_alt_results)) {
                            $wbm_cart_page_stored_results_serialize_benner_alt_results = array_reverse(explode('/', $wbm_cart_page_stored_results_serialize_benner_alt_results[1]));
                            $alt_tag_value = $wbm_cart_page_stored_results_serialize_benner_alt_results[0];
                        }
                    }
                }
            }
        }
        if (!empty($wbm_cart_page_stored_results_serialize_benner_open_new_tab) && $wbm_cart_page_stored_results_serialize_benner_open_new_tab === 'open') {
            $test="_blank";
        }
        else
        {
            $test="_self";
        }
        if (!empty($wbm_cart_page_stored_results_serialize_benner_enable_status) && $wbm_cart_page_stored_results_serialize_benner_enable_status === 'on') {
            ?>
            <div class="wbm_banner_image">
                <?php
                if ($wbm_cart_page_stored_results_serialize_benner_link == '') {
                    $alt_tag_css_cart_page_fornt = 'style=cursor:default';
                } else {
                    $alt_tag_css_cart_page_fornt = 'style=cursor:pointer href=' . esc_url($wbm_cart_page_stored_results_serialize_benner_link) . ' target='. esc_attr($test) .'';
                }
                ?>
                <a <?php echo esc_attr($alt_tag_css_cart_page_fornt); ?>>
                    <p><img src="<?php echo esc_url($wbm_cart_page_stored_results_serialize_benner_src); ?>" class="cart_banner_image" alt="<?php echo esc_attr($alt_tag_value); ?>"></p>
                </a>
            </div>
            <?php
        }
    }

    /**
     * Function For display banner image in check out page
     *
     */
    public function wbm_show_checkout_page_banner() {

        $wbm_checkout_page_stored_results_serialize_benner_src = '';
        $wbm_checkout_page_stored_results_serialize_benner_link = '';
        $wbm_checkout_page_stored_results_serialize_benner_enable_status = '';
        $wbm_checkout_page_stored_results_serialize_benner_open_new_tab = '';
        $alt_tag_value = '';

        $wbm_checkout_page_stored_results = get_option('wbm_checkout_page_stored_data', '');

        if (isset($wbm_checkout_page_stored_results) && !empty($wbm_checkout_page_stored_results)) {
            $wbm_checkout_page_stored_results_serialize = maybe_unserialize($wbm_checkout_page_stored_results);
            if (!empty($wbm_checkout_page_stored_results_serialize)) {
                $wbm_checkout_page_stored_results_serialize_benner_src = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_image_src']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_image_src'] : '';
                $wbm_checkout_page_stored_results_serialize_benner_link = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_link_src']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_link_src'] : '';
                $wbm_checkout_page_stored_results_serialize_benner_enable_status = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_banner_enable_status']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_banner_enable_status'] : '';

                $wbm_checkout_page_stored_results_serialize_benner_open_new_tab = !empty($wbm_checkout_page_stored_results_serialize['checkout_page_benner_open_new_tab']) ? $wbm_checkout_page_stored_results_serialize['checkout_page_benner_open_new_tab'] : '';

                $wbm_checkout_page_stored_results_serialize_benner_alt = array_reverse(explode('/', $wbm_checkout_page_stored_results_serialize_benner_src));
                if (!empty($wbm_checkout_page_stored_results_serialize_benner_src)) {
                    if (!empty($wbm_checkout_page_stored_results_serialize_benner_alt)) {
                        $wbm_checkout_page_stored_results_serialize_benner_alt_results = array_reverse(explode('.', $wbm_checkout_page_stored_results_serialize_benner_src));
                        if (!empty($wbm_checkout_page_stored_results_serialize_benner_alt_results)) {
                            $wbm_checkout_page_stored_results_serialize_benner_alt_results = array_reverse(explode('/', $wbm_checkout_page_stored_results_serialize_benner_alt_results[1]));
                            $alt_tag_value = $wbm_checkout_page_stored_results_serialize_benner_alt_results[0];
                        }
                    }
                }
            }
        }
        if (!empty($wbm_checkout_page_stored_results_serialize_benner_open_new_tab) && $wbm_checkout_page_stored_results_serialize_benner_open_new_tab === 'open') {
            $test="_blank";
        }
        else
        {
            $test="_self";
        }
        if (!empty($wbm_checkout_page_stored_results_serialize_benner_enable_status) && $wbm_checkout_page_stored_results_serialize_benner_enable_status === 'on') {
            ?>
            <div class="wbm_banner_image">
                <?php
                if ($wbm_checkout_page_stored_results_serialize_benner_link == '') {
                    $alt_tag_css_checkout_page_fornt = 'style="cursor:default"';
                } else {
                    $alt_tag_css_checkout_page_fornt = 'style=cursor:pointer href=' . esc_url($wbm_checkout_page_stored_results_serialize_benner_link) . ' target='. esc_attr($test) .'';
                }
                ?>
                <a <?php echo esc_attr($alt_tag_css_checkout_page_fornt); ?>>
                    <p><img src="<?php echo esc_url($wbm_checkout_page_stored_results_serialize_benner_src); ?>" class="checkout_banner_image" alt="<?php echo esc_attr($alt_tag_value); ?>"></p>
                </a>
            </div>
            <?php
        }
    }

    /**
     * Function For display banner image in Thank you page
     *
     */
    public function wbm_show_thankyou_page_banner() {

        $wbm_thankyou_page_stored_results_serialize_benner_src = '';
        $wbm_thankyou_page_stored_results_serialize_benner_link = '';
        $wbm_thankyou_page_stored_results_serialize_benner_enable_status = '';
        $alt_tag_value = '';

        $wbm_thankyou_page_stored_results = get_option('wbm_thankyou_page_stored_data', '');

        if (isset($wbm_thankyou_page_stored_results) && !empty($wbm_thankyou_page_stored_results)) {
            $wbm_thankyou_page_stored_results_serialize = maybe_unserialize($wbm_thankyou_page_stored_results);
            if (!empty($wbm_thankyou_page_stored_results_serialize)) {
                $wbm_thankyou_page_stored_results_serialize_benner_src = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_image_src']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_image_src'] : '';
                $wbm_thankyou_page_stored_results_serialize_benner_link = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_link_src']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_link_src'] : '';
                $wbm_thankyou_page_stored_results_serialize_benner_enable_status = !empty($wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_enable_status']) ? $wbm_thankyou_page_stored_results_serialize['thankyou_page_banner_enable_status'] : '';


                $wbm_thankyou_page_stored_results_serialize_benner_alt = array_reverse(explode('/', $wbm_thankyou_page_stored_results_serialize_benner_src));
                if (!empty($wbm_thankyou_page_stored_results_serialize_benner_src)) {
                    if (!empty($wbm_thankyou_page_stored_results_serialize_benner_alt)) {
                        $wbm_thankyou_page_stored_results_serialize_benner_alt_results = array_reverse(explode('.', $wbm_thankyou_page_stored_results_serialize_benner_src));
                        if (!empty($wbm_thankyou_page_stored_results_serialize_benner_alt_results)) {
                            $wbm_thankyou_page_stored_results_serialize_benner_alt_results = array_reverse(explode('/', $wbm_thankyou_page_stored_results_serialize_benner_alt_results[1]));
                            $alt_tag_value = $wbm_thankyou_page_stored_results_serialize_benner_alt_results[0];
                        }
                    }
                }
            }
        }

        if (!empty($wbm_thankyou_page_stored_results_serialize_benner_enable_status) && $wbm_thankyou_page_stored_results_serialize_benner_enable_status === 'on') {
            ?>
            <div class="wbm_banner_image">
                <?php
                if ($wbm_thankyou_page_stored_results_serialize_benner_link == '') {
                    $alt_tag_css_thankyou_page_fornt = 'style=cursor:default';
                } else {
                    $alt_tag_css_thankyou_page_fornt = 'style=cursor:pointer href=' . esc_url($wbm_thankyou_page_stored_results_serialize_benner_link) . ' target="_blank"';
                }
                ?>
                <a <?php echo esc_attr($alt_tag_css_thankyou_page_fornt); ?>>
                    <p><img src="<?php echo esc_url($wbm_thankyou_page_stored_results_serialize_benner_src); ?>" class="checkout_banner_image" alt="<?php echo esc_attr($alt_tag_value); ?>"></p>
                </a>
            </div>
            <?php
        }
    }



    // function for welcome screen page 

    public function welcome_benner_mamagement_for_woocommerce_screen_do_activation_redirect() {

        if (!get_transient('_benner_management_for_woocommerce_welcome_screen')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('_benner_management_for_woocommerce_welcome_screen');

        // if activating from network, or bulk
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        // Redirect to extra cost welcome  page
        wp_safe_redirect(add_query_arg(array('page' => 'wbm-get-started'), admin_url('admin.php')));
    }

    public function welcome_pages_screen_benner_mamagement_for_woocommerce() {
        add_dashboard_page(
            'Banner Management for WooCommerce Dashboard', 'Banner Management for WooCommerce Dashboard', 'read', 'banner-management-for-woocommerce', array($this, 'welcome_screen_content_banner_management_for_woocommerce'));
    }

    public function welcome_screen_benner_mamagement_for_woocommerce_remove_menus() {
        remove_submenu_page('index.php', 'banner-management-for-woocommerce');
        remove_submenu_page('dots_store', 'wbm-information');
        remove_submenu_page('dots_store', 'wbm-premium');
        remove_submenu_page('dots_store', 'wbm-add-new');
        remove_submenu_page('dots_store', 'wbm-edit-fee');
        remove_submenu_page('dots_store', 'wbm-get-started');
    }

   // function for admin notice print

    public function benner_mamagement_for_woocommerce_pointers_footer() {
        $admin_pointers = benner_mamagement_for_woocommerce_admin_pointers();
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            (function($) {
                <?php
                foreach ($admin_pointers as $pointer => $array) {
                if ($array['active']) {
                ?>
                $('<?php echo $array['anchor_id']; ?>').pointer({
                    content: '<?php echo $array['content']; ?>',
                    position: {
                        edge: '<?php echo $array['edge']; ?>',
                        align: '<?php echo $array['align']; ?>'
                    },
                    close: function() {
                        $.post(ajaxurl, {
                            pointer: '<?php echo $pointer; ?>',
                            action: 'dismiss-wp-pointer'
                        });
                    }
                }).pointer('open');
                <?php
                }
                }
                ?>
            })(jQuery);
            /* ]]> */
        </script>
        <?php
    }

}

