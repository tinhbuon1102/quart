<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.orionorigin.com/
 * @since      0.1
 *
 * @package    Wad
 * @subpackage Wad/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wad
 * @subpackage Wad/admin
 * @author     ORION <support@orionorigin.com>
 */
class Wad_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1
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
     * @since    0.1
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Wad_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Wad_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wad-admin.css', array(), $this->version, 'all');
        wp_enqueue_style("acd-flexgrid", plugin_dir_url(__FILE__) . 'css/flexiblegs.css', array(), $this->version, 'all');
//                wp_enqueue_style( "acd-tooltip", plugin_dir_url( __FILE__ ) . 'css/tooltip.css', array(), $this->version, 'all' );
        wp_enqueue_style("o-ui", plugin_dir_url(__FILE__) . 'css/UI.css', array(), $this->version, 'all');
        wp_enqueue_style("o-datepciker", plugin_dir_url(__FILE__) . 'js/datepicker/css/datepicker.css', array(), $this->version, 'all');
        wp_enqueue_style("wad-datetimepicker", plugin_dir_url(__FILE__) . 'js/datetimepicker/jquery.datetimepicker.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wad-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script("o-admin", plugin_dir_url(__FILE__) . 'js/o-admin.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-tabs", plugin_dir_url(__FILE__) . 'js/SpryAssets/SpryTabbedPanels.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("acd-accordion", plugin_dir_url(__FILE__) . 'js/SpryAssets/SpryAccordion.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-serializejson", plugin_dir_url(__FILE__) . 'js/jquery.serializejson.min.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker", plugin_dir_url(__FILE__) . 'js/datepicker/js/datepicker.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-eye", plugin_dir_url(__FILE__) . 'js/datepicker/js/eye.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-util", plugin_dir_url(__FILE__) . 'js/datepicker/js/util.js', array('jquery'), $this->version, false);
//        wp_enqueue_script("o-datepicker-layout", plugin_dir_url(__FILE__) . 'js/datepicker/js/layout.js', array('jquery'), $this->version, false);
        wp_enqueue_script("wad-datetimepicker", plugin_dir_url(__FILE__) . 'js/datetimepicker/build/jquery.datetimepicker.full.min.js', array('jquery'), $this->version, false);
    }

    /**
     * Initialize the plugin sessions
     */
    function init_sessions() {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION["active_discounts"]))
            $_SESSION["active_discounts"] = array();
        if (!isset($_SESSION["social_data"]))
            $_SESSION["social_data"] = array();
    }

    /**
     * Builds all the plugin menu and submenu
     */
    public function add_wad_menu() {
        $parent_slug = "edit.php?post_type=o-discount";
        add_submenu_page($parent_slug, __('Products Lists', 'wad'), __('Products Lists', 'wad'), 'manage_product_terms', 'edit.php?post_type=o-list', false);
        add_submenu_page($parent_slug, __('Settings', 'wad'), __('Settings', 'wad'), 'manage_product_terms', 'wad-manage-settings', array($this, 'get_wad_settings_page'));
//        add_submenu_page($parent_slug, __('Get Started', 'wad'), __('Get Started', 'wad'), 'manage_product_terms', 'wad-about', array($this, "get_about_page"));
    }

    public function get_wad_settings_page() {
        if ((isset($_POST["wad-options"]) && !empty($_POST["wad-options"]))) {
            $_POST["wad-options"]["social-desc"]=stripslashes(wp_filter_post_kses(addslashes($_POST["wad-options"]["social-desc"])));
            update_option("wad-options", $_POST["wad-options"]);
            //echo stripslashes(wp_filter_post_kses(addslashes($_POST["wad-options"]["social-desc"])));
        }
        wad_remove_transients();
        ?>
        <div class="o-wrap cf">
            <h1><?php _e("Woocommerce All Discounts Settings", "wad"); ?></h1>
            <form method="POST" action="" class="mg-top">
                <div class="postbox" id="wad-options-container">
                    <?php
                    $begin = array(
                        'type' => 'sectionbegin',
                        'id' => 'wad-datasource-container',
                        'table' => 'options',
                    );
                    $facebook_app_id = array(
                        'title' => __('App ID', 'wad'),
                        'name' => 'wad-options[facebook-app-id]',
                        'type' => 'text',
                        'desc' => __('Facebook App ID', 'wad'),
                        'default' => '',
                    );

                    $facebook_app_secret = array(
                        'title' => __('App Secret', 'wad'),
                        'name' => 'wad-options[facebook-app-secret]',
                        'type' => 'text',
                        'desc' => __('Facebook App Secret', 'wad'),
                        'default' => '',
                    );

                    $facebook = array(
                        'title' => __('Facebook', 'wad'),
                        'desc' => __('Facebook APP settings', 'wad'),
                        'type' => 'groupedfields',
                        'fields' => array($facebook_app_id, $facebook_app_secret),
                    );

                    $instagram_app_id = array(
                        'title' => __('Client ID', 'wad'),
                        'name' => 'wad-options[instagram-app-id]',
                        'type' => 'text',
                        'desc' => __('Client ID', 'wad'),
                        'default' => '',
                    );

                    $instagram_app_secret = array(
                        'title' => __('Client Secret', 'wad'),
                        'name' => 'wad-options[instagram-app-secret]',
                        'type' => 'text',
                        'desc' => __('Client Secret', 'wad'),
                        'default' => '',
                    );

                    $instagram = array(
                        'title' => __('Instagram', 'wad'),
                        'desc' => __('Instagram Client settings', 'wad'),
                        'type' => 'groupedfields',
                        'fields' => array($instagram_app_id, $instagram_app_secret),
                    );

                    $twitter_app_id = array(
                        'title' => __('Twitter Consumer Key', 'wad'),
                        'name' => 'wad-options[twitter-app-id]',
                        'type' => 'text',
                        'desc' => __('Twitter Client ID', 'wad'),
                        'default' => '',
                    );

                    $twitter_app_secret = array(
                        'title' => __('Twitter Consumer Secret', 'wad'),
                        'name' => 'wad-options[twitter-app-secret]',
                        'type' => 'text',
                        'desc' => __('Twitter Client Secret', 'wad'),
                        'default' => '',
                    );

//                    $twitter = array(
//                        'title' => __('Twitter', 'wad'),
//                        'desc' => __('Twitter Client settings', 'wad'),
//                        'type' => 'groupedfields',
//                        'fields' => array($twitter_app_id, $twitter_app_secret),
//                    );

                    $mailchimp_api_key_admin = array(
                        'title' => __('Mailchimp API KEY', 'wad'),
                        'name' => 'wad-options[mailchimp-api-key]',
                        'type' => 'text',
                        'desc' => __('Used when a MailChimp based discount need to be set. <a href="http://kb.mailchimp.com/accounts/management/about-api-keys" target="blank">How to find my API Key?</a>', 'wad'),
                        'default' => '',
                    );
                    $sendinblue_api_key_admin = array(
                        'title' => __('SendinBlue API KEY', 'wad'),
                        'name' => 'wad-options[sendinblue-api-key]',
                        'type' => 'text',
                        'desc' => __('Used when a SendinBlue based discount need to be set.<a href="https://my.sendinblue.com/advanced/apikey" target="blank">How to find my API Key?</a>', 'wad'),
                        'default' => '',
                    );

                    $social_description = array(
                        'title' => __('Social buttons description', 'wad'),
                        'name' => 'wad-options[social-desc]',
                        'id' => 'social-desc-editor',
                        'type' => 'texteditor',
                        'desc' => __('Description of the social buttons on the cart page to help the customer understand what to do', 'wad'),
                        'default' => '',
                    );

                    $envato_username = array(
                        'title' => __('Envato Username', 'wad'),
                        'name' => 'wad-options[envato-username]',
                        'type' => 'text',
                        'desc' => __('Your envato username', 'wad'),
                        'default' => '',
                    );

                    $envato_api_key = array(
                        'title' => __('Secret API Key', 'wad'),
                        'name' => 'wad-options[envato-api-key]',
                        'type' => 'text',
                        'desc' => __('You can find your secret api key by following the instructions <a href="https://www.youtube.com/watch?v=KnwumvnWAIM" target="blank">here</a>.', 'wad'),
                        'default' => '',
                    );

                    $envato_purchase_code = array(
                        'title' => __('Purchase Code', 'wad'),
                        'name' => 'wad-options[purchase-code]',
                        'type' => 'text',
                        'desc' => __('You can find your purchase code by following the instructions <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-can-I-find-my-Purchase-Code-" target="blank">here</a>.', 'wad'),
                        'default' => '',
                    );

                    $enable_cache = array(
                        'title' => __('Cache discounts', 'wad'),
                        'name' => 'wad-options[enable-cache]',
                        'type' => 'select',
                        'options' => array(0 => "No", 1 => "Yes"),
                        'desc' => __('whether or not to store the discounts in the cache to increase the pages load speed. Cache is valid for 12hours', 'wad'),
                        'default' => '',
                    );
        
                     $include_taxes = array(
                        'title' => __('Include shipping in taxes', 'wad'),
                        'name' => 'wad-options[inc-shipping-in-taxes]',
                        'type' => 'select',
                        'options' => array('No' => "No", 'Yes' => "Yes"),
                        'desc' => __('Wether or not to consider shipping as part of taxes', 'wad'),
                        'default' => 'Yes',
                    );
        
                    $disable_coupons = array(
                        'title' => __('Disable coupons', 'wad'),
                        'name' => 'wad-options[disable-coupons]',
                        'type' => 'select',
                        'options' => array(0 => "No", 1 => "Yes"),
                        'desc' => __('whether or not to disable the coupons usage when a cart discount is active.', 'wad'),
                        'default' => '',
                    );
                    $display_cart_discounts_individually = array(
                        'title' => __('Display cart discounts individually', 'wad'),
                        'name' => 'wad-options[individual-cart-discounts]',
                        'type' => 'select',
                        'options' => array(0 => "No", 1 => "Yes"),
                        'desc' => __('whether or not to display each cart discount individually on cart pages.', 'wad'),
                        'default' => 1,
                    );
                    $completed_order_statuses = array(
                        'title' => __('Completed Orders Statuses', 'wad'),
                        'name' => 'wad-options[completed-order-statuses]',
                        'type' => 'multiselect',
                        'options' => wc_get_order_statuses(),
                        'desc' => __('List of order statuses considered as completed (used when manipulating previous orders in the discounts conditions).', 'wad'),
                        'default' => '',
                    );

                    $end = array('type' => 'sectionend');
                    $settings = array(
                        $begin,
                        $facebook,
//                $twitter,
                        $instagram,
                        $mailchimp_api_key_admin,
                        $sendinblue_api_key_admin,
                        $social_description,
                        $disable_coupons,
                        $display_cart_discounts_individually,
                        $enable_cache,
                        $include_taxes,
                        $envato_username,
                        $envato_api_key,
                        $envato_purchase_code,
                        $completed_order_statuses,
                        $end
                    );
                    echo o_admin_fields($settings);
                    ?>
                </div>
                <input type="submit" class="button button-primary button-large" value="<?php _e("Save", "wad"); ?>">
            </form>
        </div>
        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
    }

    /**
     * Builds the about page
     */
    function get_about_page() {
        $wpc_logo = WAD_URL . 'admin/images/wpc.jpg';
        $img1 = WAD_URL . 'admin/images/install-demo-package.jpg';
        $img2 = WAD_URL . 'admin/images/set-basic-settings.jpg';
        $img3 = WAD_URL . 'admin/images/create-customizable-product.jpg';
        $img4 = WAD_URL . 'admin/images/manage-templates.jpg';
        ?>
        <div id='wad-about-page'>
            <div class="about-heading">
                <div>
                    <H2><?php echo __("Welcome to WooCommerce All Discounts", "wad") . " " . WAD_VERSION; ?></H2>
                    <H4><?php printf(__("Thanks for installing! WooCommerce All Discounts %s is more powerful, stable and secure than ever before. We hope you enjoy using it.", "wad"), WAD_VERSION); ?></H4>
                </div>
                <div class="about-logo">
                    <img src="<?php echo $wpc_logo; ?>" />
                </div>
            </div>
            <div class="about-button">
                <div><a href="<?php echo admin_url('edit.php?post_type=o-discount&page=wad-manage-settings'); ?>" class="button">Settings</a></div>
                <div><a href="<?php echo WAD_URL . 'user_manual.pdf'; ?>" class="button"><?php _e("User Manual", "wad"); ?></a></div>
            </div>

            <div id="TabbedPanels1" class="TabbedPanels">
                <ul class="TabbedPanelsTabGroup ">
                    <li class="TabbedPanelsTab " tabindex="4"><span><?php _e('Getting Started', 'wad'); ?></span> </li>
                    <li class="TabbedPanelsTab" tabindex="5"><span><?php _e('Changelog', 'wad'); ?> </span></li>
                    <li class="TabbedPanelsTab" tabindex="6"><span><?php _e('Follow Us', 'wad'); ?></span></li>
                </ul>

                <div class="TabbedPanelsContentGroup">
                    <div class="TabbedPanelsContent">
                        <div class='wpc-grid wpc-grid-pad'>
                            <div class="wpc-col-3-12">
                                <div class="product-container">
                                    <a href="https://www.youtube.com/watch?v=AlSMCIoOLRA" target="blank">
                                        <div class="img-container"><img src="<?php echo $img1; ?>"></div>
                                        <div class="img-title"><?php _e('How to install the demo package?', 'wad'); ?></div>
                                    </a>
                                </div>
                            </div>
                            <div class="wpc-col-3-12">
                                <div class="product-container">
                                    <a href="https://www.youtube.com/watch?v=NTvIvhJHueU" target="blank">
                                        <div class="img-container"><img src="<?php echo $img2; ?>"></div>
                                        <div class="img-title"><?php _e('How to set the basic settings?', 'wad'); ?></div>
                                    </a>
                                </div>
                            </div>
                            <div class="wpc-col-3-12">
                                <div class="product-container">
                                    <a href="https://www.youtube.com/watch?v=FDnM7hjepqo" target="blank">
                                        <div class="img-container"><img src="<?php echo $img3; ?>"></div>
                                        <div class="img-title"><?php _e('How to create a customizable product?', 'wad'); ?></div>
                                    </a>
                                </div>
                            </div>
                            <div class="wpc-col-3-12">
                                <div class="product-container">
                                    <a href="https://www.youtube.com/watch?v=_hoANHYazI4" target="blank">
                                        <div class="img-container"><img src="<?php echo $img4; ?>"></div>
                                        <div class="img-title"><?php _e('How to manage your designs templates?', 'wad'); ?></div>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="TabbedPanelsContent">
                        <div class='wpc-grid wpc-grid-pad'>
                            <?php
                            $file_path = WAD_DIR . "/changelog.txt";
                            $myfile = fopen($file_path, "r") or die(__("Unable to open file!", "wad"));
                            while (!feof($myfile)) {
                                $line_of_text = fgets($myfile);
                                if (strpos($line_of_text, 'Version') !== false)
                                    print '<b>' . $line_of_text . "</b><BR>";
                                else
                                    print $line_of_text . "<BR>";
                            }
                            fclose($myfile);
                            ?>
                        </div>
                    </div>
                    <div class="TabbedPanelsContent">
                        <div class="wpc-grid wpc-grid-pad follow-us">
                            <div class="wpc-col-6-12 ">
                                <h3>Why?</h3>
                                <ul class="follow-us-list">
                                    <li>
                                        <a href="#">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/love.png"></span>
                                            <span>
                                                <h4 class="title"> Show us some love of course!</h4>
                                                You like our product and you tried it. Cool! Then give us some boost by sharing it with friends or making interesting comments on our pages!
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/update.png"></span>
                                            <span>
                                                <h4 class="title"> Receive regular updates from us on our products.</h4>
                                                This is the best way to enjoy the full of the news features added to our plugins. 
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/features.png"></span>
                                            <span>
                                                <h4 class="title"> Suggest new features for the products you're interested in.</h4>
                                                One of our products arouses your interest but it’s not exactly what you want. If only some features can be added… You know what? Actually it’s possible! Just leave your suggestion and we’ll do our best! 
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/bug.png"></span>
                                            <span>
                                                <h4 class="title"> Become a beta tester for our pre releases.</h4>
                                                For each couple of feature up-coming we need beta tester to improve the final product we are about to propose. So if you want to be part of this, freely apply here.
                                            </span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="#">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/free.png"></span>
                                            <span>
                                                <h4 class="title"> Access our freebies collection anytime.</h4>
                                                Find the coolest free collection of our plugins and make the most of it!
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div> 
                            <div id="separator"></div>
                            <div class="wpc-col-6-12 ">
                                <h3>How?</h3>
                                <div class="follow-us-text">
                                    <div>
                                        Easy!! Just access our social networks pages and follow/like us. Yeah just like that :).
                                    </div>

                                    <div class="btn-container">
                                        <a href="http://twitter.com/OrionOrigin" target="blank" style="display: inline-block;">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/twitter.png"></span>
                                        </a>
                                        <a href="https://www.facebook.com/OrionOrigin" target="blank" style="display: inline-block;">
                                            <span class="rs-ico"><img src="<?php echo WAD_URL; ?>/admin/images/facebook-about.png"></span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div> 
                    </div>

                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Runs the new version check and upgrade process
     * @return \WAD_Updater
     */
    function get_updater() {
//        do_action('wad_before_init_updater');
        require_once( WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-wad-updater.php' );
        $updater = new WAD_Updater();
        $updater->init();
        require_once( WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'updaters' . DIRECTORY_SEPARATOR . 'class-wad-updating-manager.php' );
        $updater->setUpdateManager(new WAD_Updating_Manager(WAD_VERSION, $updater->versionUrl(), WAD_MAIN_FILE));
//        do_action('wad_after_init_updater');
        return $updater;
    }

}
