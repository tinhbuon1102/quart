<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.orionorigin.com/
 * @since      0.1
 *
 * @package    Wad
 * @subpackage Wad/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wad
 * @subpackage Wad/public
 * @author     ORION <support@orionorigin.com>
 */
class Wad_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wad-public.css', array(), $this->version, 'all');
        wp_enqueue_style("o-tooltip", WAD_URL . 'public/css/tooltip.min.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1
     */
    public function enqueue_scripts() {

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
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wad-public.js', array('jquery'), $this->version, false);
        wp_enqueue_script("o-tooltip", WAD_URL . 'public/js/tooltip.min.js', array('jquery'), $this->version, false);
    }

    function init_globals() {
        global $wad_discounts;
        global $wad_settings;
        global $mailchimp_user_lists;
        global $sendinblue_user_lists;
        global $wad_free_gifts;
        global $wad_cart_discounts;
        global $wad_user_role;
        global $wad_user_groups;
        global $wad_ignore_product_prices_calculations;
        global $wad_cart_subtotal_inc_taxes;
        global $wad_reviewed_products_by_customer;
        $wad_cart_discounts=0;
        $wad_cart_subtotal_inc_taxes=false;
        $wad_reviewed_products_by_customer=false;
        $wad_ignore_product_prices_calculations=false;
        $wad_free_gifts=array();
        $wad_user_groups=false;
        $wad_settings = get_option("wad-options");
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;
        $wad_user_role=wad_get_user_role();

        $mailchimp_user_lists = $this->get_mailchimp_user_lists($email);
        
        
        $sendinblue_user_lists = $this->get_sendinblue_user_lists($email);
        $cache_enabled=  get_proper_value($wad_settings, "enable-cache", 0);
        if($cache_enabled)
        {
            $cached_discounts = get_transient( 'orion_wad_discounts_transient' );
            if($cached_discounts)
            {
                $wad_discounts=$cached_discounts;
                return;
            }
        }
        
        $all_discounts = wad_get_active_discounts(true);
        foreach ($all_discounts as $discount_type => $discounts) {
            $wad_discounts[$discount_type] = array();
            foreach ($discounts as $discount_id) {
                $wad_discounts[$discount_type][$discount_id] = new WAD_Discount($discount_id);
            }
        }

        if($cache_enabled)
            set_transient( 'orion_wad_discounts_transient', $wad_discounts, 12 * HOUR_IN_SECONDS );
    }

    function get_mailchimp_user_lists($email) {
        global $wad_settings;
        $list = array();
        
        $api_key_mc = get_proper_value($wad_settings, "mailchimp-api-key", false);
        if (isset($api_key_mc) AND ! empty($api_key_mc)) {
            $MailChimp = new \Drewm\MailChimp($api_key_mc);
            $DATA_MC = array("email" => array("email" => $email));
            $user_lists = $MailChimp->call("/helper/lists-for-email", $DATA_MC);
            if (empty($user_lists)) 
                return $list;
            foreach ($user_lists as $to_list) {
                if (isset($to_list['id']) AND ! empty($to_list['id'])) {
                    $list[] = $to_list['id'];
                }
            }
        }
        return $list;
    }
    
    function get_sendinblue_user_lists($email) {
        global $wad_settings;
        $list = array();
        
        $api_key_sb = get_proper_value($wad_settings, "sendinblue-api-key", false);
        if (isset($api_key_sb) AND ! empty($api_key_sb)) {
            $mailin = new Mailin('https://api.sendinblue.com/v2.0', $api_key_sb);
            $DATA_SB = array("email" => $email);
            $content = $mailin->get_user($DATA_SB);
            if (isset($content['data']['listid']) AND !empty($content['data']['listid'])) {
            $list = $content['data']['listid'];
        }
            
        }
        return $list;
    }

    public function process_social_login() {
        if (isset($_GET["social-login-wad"])) {
            $login_type = $_GET["social-login-wad"];
            $allowed_logins = array("facebook", "instagram");
            if (in_array($login_type, $allowed_logins)) {
                if(!class_exists("Hybrid_Auth"))
                    require_once( WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'hybridauth' . DIRECTORY_SEPARATOR . 'Hybrid' . DIRECTORY_SEPARATOR . 'Auth.php' );
                $config = WAD_DIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'hybridauth' . DIRECTORY_SEPARATOR . 'config.php';
                try {
                    $hybridauth = new Hybrid_Auth($config);

                    $adapter = $hybridauth->authenticate($login_type);
//                    $adapter= $hybridauth->logoutAllProviders();
//                    return;
                    if ($login_type == "facebook") {
                        $_SESSION["social_data"]["facebook"]["likes"] = $adapter->api()->api('/me/likes?limit=999');
                        //Les liens sont devenus dépréciés à partir de la v2.4. Pas trouvé de remplacement
                        //Risque d'affecté la vérification si l'user a partagé un produit
//                        $_SESSION["social_data"]["facebook"]["links"] = $adapter->api()->api('/me/links?limit=999');
                    } else if ($login_type == "instagram") {
                        $_SESSION["social_data"]["instagram"]["likes"] = $adapter->api()->api('users/self/follows/');
                    }
                } catch (Exception $e) {
                    die("<b>got an error!</b> " . $e->getMessage());
                }
            }
        }
    }

}
