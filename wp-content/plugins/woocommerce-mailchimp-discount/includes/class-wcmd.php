<?php

class wcmd {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */

    private $wcmd_enabled;

    public function __construct() {

        $this->wcmd_enabled = get_option( 'wcmd_enabled' );


        //Check if woocommerce plugin is installed.
        add_action( 'admin_notices', array( $this, 'check_required_plugins' ) );


        //Add setting link for the admin settings
        add_filter( "plugin_action_links_".WCMD_BASE, array( $this, 'wcmd_settings_link' ) );

        //Add backend settings
        add_filter( 'woocommerce_get_settings_pages', array( $this, 'wcmd_settings_class' ) );

        //Add shortcode support on the widgets
        add_filter( 'widget_text', 'do_shortcode' );

        //Add help tab for displaying the use for the variables in email
        add_action( "current_screen", array( $this, 'add_tabs' ), 50 );

        //Add shortcode for mailchimp discount.
        add_shortcode( 'wc_mailchimp_discount', array( $this, 'wcmd_shortcode' ) );

        add_action( 'admin_enqueue_scripts',  array( $this, 'wcmd_enque_admin_scripts' ) );

        if( $this->wcmd_enabled == 'yes' ) {

            //Add css and js files for the popup
            add_action( 'wp_enqueue_scripts',  array( $this, 'wcmd_enque_scripts' ) );

            //show popup in the store frontend
            $cookie =  ( isset( $_COOKIE['wcmd'] ) && $_COOKIE['wcmd'] == 'yes' ) ? 'yes' : 'no';
            if( get_option( 'wcmd_disable_popup' ) != 'yes' && $cookie == 'no' || get_option( 'wcmd_btn_trigger' ) == 'yes' )
                add_action( 'wp_footer', array( $this, 'wcmd_display_popup') );

            if( get_option('wcmd_restrict') == 'yes' && get_option('wcmd_loggedin') == 'yes' )
                add_filter('woocommerce_coupon_is_valid', array( $this,'validate_coupon' ), 10, 2);

            //Mailchimp user registration.
            add_action( 'wp_ajax_wcmd_subscribe', array( $this, 'wcmd_subscribe' ) );
            add_action( 'wp_ajax_nopriv_wcmd_subscribe', array( $this, 'wcmd_subscribe' ) );
        }


        add_shortcode( 'wcmd', array( $this, 'wcmd_lang_func' ) );

        add_action( 'wp_ajax_wcmd_ajax_products', array( $this, 'wcmd_ajax_products' ) );
    }


    public function wcmd_enque_admin_scripts() {
      if( is_admin() && isset($_GET['tab']) && $_GET['tab'] == 'mailchimp_discount' ) {
        wp_enqueue_style( 'select2-style', plugins_url( 'assets/css/select2.css', WCMD_FILE ) );
        wp_enqueue_script( 'wcmd-enhanced-select', plugins_url( 'assets/js/select2.min.js', WCMD_FILE ) , array( 'jquery' ), '1.0.0', true );
      
        wp_localize_script( 'wcmd-enhanced-select', 'wcmd_enhanced_select_params', array(
            'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
            'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
            'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
            'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
            'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
            'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
            'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
            'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
            'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
            'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
            'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
            'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
        ) ); 
      } 
    }

    public function wcmd_lang_func( $atts, $content = "" ) {
        $a = shortcode_atts( array(
            'lang' => ''
        ), $atts );

        if( !function_exists('icl_object_id') )
          $current_lang = substr(get_bloginfo('language'), 0, 2);
        else
          $current_lang = ICL_LANGUAGE_CODE;

        if( !empty( $a['lang'] ) && $current_lang == $a['lang'] )
            return $content;
        else
            return;
    }

    /**
    *
    * Add necessary js and css files for the popup
    *
    */
    public function wcmd_enque_scripts() {
        if( get_option( 'wcmd_disable_discount') != 'yes' && get_option( 'wcmd_double_optin') == 'yes' && isset( $_GET['mc_discount'] ) && isset( $_POST['type'] ) && $_POST['type'] == 'subscribe' ) {
          if( isset($_POST['data']['merges']['WCMDLANG']) && !empty($_POST['data']['merges']['WCMDLANG']) ) {
            $current_lang = $_POST['data']['merges']['WCMDLANG'];
          }
          else {
            if( !function_exists('icl_object_id') )
              $current_lang = substr(get_bloginfo('language'), 0, 2);
            else
              $current_lang = ICL_LANGUAGE_CODE;
          }
          $this->wcmd_send_coupons( $_POST['data']['email'], $current_lang );
        }
        $overlay_color = get_option( 'wcmd_pop_overlay' );
        list($r, $g, $b) = sscanf($overlay_color, "#%02x%02x%02x");
        $rgb_color = 'rgba('.$r.','.$g.','.$b.','.get_option( 'wcmd_overlay_opacity' ).')';
        $height = get_option( 'wcmd_popup_height' ) == 0 ? 'auto' : get_option( 'wcmd_popup_height' ) . 'px';
        $width = get_option( 'wcmd_popup_width' ) == 0 ? 'auto' : get_option( 'wcmd_popup_width' ) . 'px';
        $bg = get_option( 'wcmd_pop_bg' ) ==  '' ? get_option('wcmd_pop_bgcolor') : 'url(' . get_option( 'wcmd_pop_bg' ) . ')';
        $top_pixel = get_option('wcmd_content_top') . 'px';
        $left_pixel = get_option('wcmd_content_left') . 'px';
        $form_width = get_option( 'wcmd_form_width' ) == 0 ? 'auto' : get_option( 'wcmd_form_width' ) . 'px';
        $close_color = get_option( 'wcmd_close_color' );
        if( $close_color == '' )
            $close_color = '#fff';
        $css  = '#wcmd_modal{ min-height:' . $height . ';background:' . $bg . ';max-width:' . $width . ';}';
        $css .= '#wcmd_modal .mfp-close{ color:' .$close_color .' !important; }';
        $css .= '#wcmd-form{float:' . get_option( 'wcmd_form_alignment' ) . '; max-width:' . $form_width . ';}';
        $css .= '.wcmd-title{ color:' . get_option( 'wcmd_header_color' ) . ';}';
        $css .= '.wcmd_text{ top:' . $top_pixel . ';left:' . $left_pixel . ';}';
        $css .= '.wcmd-btn{ background:' . get_option( 'wcmd_btn_color' ) . ';color:' . get_option( 'wcmd_btn_txt_color' ) . ';}';
        $css .= '.wcmd-btn:hover{ background:' . get_option( 'wcmd_btn_hover' ) . ';}';
        $css .= '#wcmd-form label{ color:' . get_option( 'wcmd_label_color' ) . ';}';
        $css .= '#wcmd-form .wcmd-confirm{ background:' . get_option( 'wcmd_checkbox_color' ) . ';}';
        $css .= '.wcmd-form .wcmd-validation.success{ background: '.get_option('wcmd_success_bg_color').'; color: '.get_option('wcmd_success_txt_color').'; border: 1px solid '.get_option('wcmd_success_bg_color').'; }';
        $css .= '.wcmd-form .wcmd-validation.error{ background: '.get_option('wcmd_error_bg_color').'; color: '.get_option('wcmd_error_txt_color').'; border: 1px solid '.get_option('wcmd_error_bg_color').' }';

        //Add custombox css
        wp_enqueue_style( 'wcmd-custombox-stylesheet', plugins_url( 'assets/css/magnific-popup.css', WCMD_FILE ));

        //Add our customized css
        wp_add_inline_style( 'wcmd-custombox-stylesheet', $css );

        //Custombox js script
        wp_enqueue_script( 'wcmd-custombox', plugins_url( 'assets/js/jquery.magnific-popup.min.js', WCMD_FILE ) , array( 'jquery' ), '1.0.0', true);
        wp_enqueue_script('custom-script', plugins_url( 'assets/js/custom.js', WCMD_FILE ), array( 'jquery', 'wcmd-custombox' ), '1.0.0', true );

        wp_localize_script('custom-script', 'wcmd', array(
          'double_optin'   => get_option('wcmd_double_optin'),
          'effect' => get_option('wcmd_popup_animation'),
          'width'  => get_option( 'wcmd_popup_width' ),
          'overlayColor'   => $rgb_color,
          'delay' => get_option( 'wcmd_dis_seconds'),
          'success' => do_shortcode( get_option( 'wcmd_success_msg' ) ),
          'cookie_length' => get_option( 'wcmd_cookie_length' ),
          'wcmd_popup'  => get_option( 'wcmd_disable_popup' ),
          'valid_email' => __( '有効なメールアドレスを入力してください。' ),
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'exit_intent' => get_option( 'wcmd_exit_intent' ),
          'hinge' => get_option( 'wcmd_hinge' ),
          'overlay_click' => get_option( 'wcmd_overlay_click' ),
          'btn_trigger' => get_option( 'wcmd_btn_trigger' ),
          'only_btn' => get_option( 'wcmd_only_btn' ),
          'close_time' => get_option( 'wcmd_close_seconds' ),
          'wcmd_home'  => get_option( 'wcmd_home' ),
          'disable_popup_on_mobile' => get_option('wcmd_disable_mobile_popup'),
          'is_home' => is_front_page()
        ));

    }

    /**
    *
    * Check if woocommerce is installed and activated and if not
    * activated then deactivate woocommerce mailchimp discount.
    *
    */
    public function check_required_plugins() {

        //Check if woocommerce is installed and activated
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>

            <div id="message" class="error">
                <p>WooCommerce Mailchimp Discount requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="<?php echo admin_url('/plugin-install.php?tab=search&amp;type=term&amp;s=WooCommerce'); ?>" target="">WooCommerce</a> first.</p>
            </div>

            <?php
            deactivate_plugins( '/woocommerce-mailchimp-discount/woocommerce-mailchimp-discount.php' );
        }

    }

    /**
     * Add new link for the settings under plugin links
     *
     * @param array   $links an array of existing links.
     * @return array of links  along with mailchimp discount settings link.
     *
     */
    public function wcmd_settings_link($links) {
        $settings_link = '<a href="'.admin_url('admin.php?page=wc-settings&tab=mailchimp_discount').'">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Add new admin setting page for woocommerce mailchimp discount settings.
     *
     * @param array   $settings an array of existing setting pages.
     * @return array of setting pages along with mailchimp discount settings page.
     *
     */
    public function wcmd_settings_class( $settings ) {
        $settings[] = include 'class-wc-settings-mailchimp-discount.php';
        return $settings;
    }


    /**
     * Output the html for the popup.
     *
     * @param void
     * @return outputs the html for the popup
     *
     */
    public function wcmd_display_popup() {
      $wcmd_title = do_shortcode( get_option( 'wcmd_pop_header') );
      $fields = get_option( 'wcmd_fields' );
      $pop_text = wpautop( stripslashes( get_option('wcmd_popup_text') ) );
      $pop_text = do_shortcode( $pop_text );
    ?>
      <div id="wcmd_modal" class="mfp-with-anim mfp-hide">
        <?php if( $wcmd_title != '' ) echo '<h4 class="wcmd-title">' . $wcmd_title . '</h4>'; ?>
        <div class="wcmd_content">
            <div class="wcmd-loading"></div>
            <div class="wcmd_text">
            <?php
                $form = '<form class="wcmd-form wcmd_' . $fields . '">';
                $form .= '<div class="wcmd-fields">';
                if( $fields == 'email_name' || $fields == 'email_name_all' )
                    $form .= '<input type="text" placeholder="'. __('Enter first name', 'wcmd' ) .'" name="wcmd_fname" class="wcmd_fname">';
                if( $fields == 'email_name_all' )
                    $form .= '<input type="text" placeholder="'. __('Enter last name', 'wcmd' ) .'" name="wcmd_lname" class="wcmd_lname">';
                $form .='<input type="text" placeholder="'. __('メールアドレスを入力', 'wcmd' ) .'" name="wcmd_email" class="wcmd_email">';
                $form .= '</div><div class="wcmd-btn-cont">';
                $form .= '<button class="wcmd-btn">' . get_option( 'wcmd_btn_text' ) . '</button>';
                $form .= '</div><div class="wcmd-clear"></div><div class="wcmd-validation"></div></form>';
                $form .= '<div class="wcmd-clear"></div>';
                //Replace the from code and add the form html.
                echo str_replace( '{WCMD_FORM}', $form, $pop_text );

            ?>
            </div>
        </div>
      </div>
    <?php
    }

    /**
     * Hook our function to send the emails to users when they signup for newsletter
     *
     * @param string $email Email Id for the newly registered user.
     *
     */
    public function wcmd_send_coupons( $email, $language ) {

        global $woocommerce;
        $code_length = get_option( 'wcmd_code_length' );
        $emails = get_option( 'wcmd_mails', array() );


        //If user is already subscribed in past and trying to register again after unsubscribe.
        if( is_array( $emails ) && in_array( $email, $emails ) )
            return;

        if( $code_length == '' )
            $code_length = 12;
        $prefix = get_option( 'wcmd_prefix' );
        $code = $prefix . strtoupper( substr( str_shuffle( md5( time() ) ), 0, $code_length ) );
        $type = get_option( 'wcmd_dis_type' );
        $amount = get_option( 'wcmd_amount' );
        $product_ids = get_option( 'wcmd_products' );
        $allowed_products = '';
        $excluded_products = '';
        if ( is_array( $product_ids ) ) {
            foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                $allowed_products .= '<a href="'.$product->get_permalink().'">'.$product->get_title().'</a>,';
            }
            $allowed_products = rtrim( $allowed_products, ',' );
            $product_ids = implode( ',', $product_ids );
        }

        $exclude_product_ids = get_option( 'wcmd_exclude_products' );
        if ( is_array( $exclude_product_ids ) ) {
            foreach ( $exclude_product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                $excluded_products .= '<a href="'.$product->get_permalink().'">'.$product->get_title().'</a>,';
            }
            $excluded_products = rtrim( $excluded_products, ',' );
            $exclude_product_ids = implode( ',', $exclude_product_ids );
        }


        $product_categories = get_option( 'wcmd_categories' );
        $allowed_cats = '';
        $excluded_cats = '';
        if ( is_array( $product_categories ) ) {
            foreach ( $product_categories as $cat_id ) {
                $cat = get_term_by( 'id', $cat_id, 'product_cat' );
                $allowed_cats .= '<a href="'.get_term_link( $cat->slug, 'product_cat' ).'">'.$cat->name.'</a>,';
            }
            $allowed_cats = rtrim( $allowed_cats, ',' );
        }
        else
            $product_categories = array();


        $exclude_product_categories = get_option( 'wcmd_exclude_categories' );
        if ( is_array( $exclude_product_categories ) ) {
            foreach ( $exclude_product_categories as $cat_id ) {
                $cat = get_term_by( 'id', $cat_id, 'product_cat' );
                $excluded_cats .= '<a href="'.get_term_link( $cat->slug, 'product_cat' ).'">'.$cat->name.'</a>,';
            }
            $excluded_cats = rtrim( $excluded_cats, ',' );
        }
        else
            $exclude_product_categories = array();

        $days = get_option( 'wcmd_days' );
        $date = '';
        $expire = '';
        $format = get_option( 'wcmd_date_format' ) == '' ? 'jS F Y' : get_option( 'wcmd_date_format' );
        if ( $days ) {
            $date = date( 'Y-m-d', strtotime( '+'.$days.' days' ) );
            $expire = date_i18n( $format, strtotime( '+'.$days.' days' ) );
        }
        $free_shipping = get_option( 'wcmd_shipping' );
        $exclude_sale_items = get_option( 'wcmd_sale' );
        $minimum_amount = get_option( 'wcmd_min_purchase' );
        $maximum_amount = get_option( 'wcmd_max_purchase' );
        $customer_email = '';
        if ( get_option( 'wcmd_restrict' ) == 'yes' )
            $customer_email = $email;

        //Add a new coupon when user registers
        $coupon = array(
            'post_title' => $code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'     => 'shop_coupon'
        );
        $coupon_id = wp_insert_post( $coupon );

        //Add coupon meta data
        update_post_meta( $coupon_id, 'discount_type', $type );
        update_post_meta( $coupon_id, 'coupon_amount', $amount );
        update_post_meta( $coupon_id, 'individual_use', 'yes' );
        update_post_meta( $coupon_id, 'product_ids', $product_ids );
        update_post_meta( $coupon_id, 'exclude_product_ids', $exclude_product_ids );
        update_post_meta( $coupon_id, 'usage_limit', '1' );
        update_post_meta( $coupon_id, 'usage_limit_per_user', '1' );
        update_post_meta( $coupon_id, 'limit_usage_to_x_items', '' );
        update_post_meta( $coupon_id, 'expiry_date', $date );
        update_post_meta( $coupon_id, 'apply_before_tax', 'no' );
        update_post_meta( $coupon_id, 'free_shipping', $free_shipping );
        update_post_meta( $coupon_id, 'exclude_sale_items', $exclude_sale_items );
        update_post_meta( $coupon_id, 'product_categories', $product_categories );
        update_post_meta( $coupon_id, 'exclude_product_categories', $exclude_product_categories );
        update_post_meta( $coupon_id, 'minimum_amount', $minimum_amount );
        update_post_meta( $coupon_id, 'maximum_amount', $maximum_amount );
        update_post_meta( $coupon_id, 'customer_email', $customer_email );

        $search = array( '{COUPONCODE}', '{COUPONEXPIRY}', '{ALLOWEDCATEGORIES}', '{EXCLUDEDCATEGORIES}', '{ALLOWEDPRODUCTS}', '{EXCLUDEDPRODUCTS}' );
        $replace = array( $code, $expire, $allowed_cats, $excluded_cats, $allowed_products, $excluded_products );
        $subject = str_replace( $search, $replace, get_option( 'wcmd_email_sub' ) );
        $subject = do_shortcode( $subject );
        $body = str_replace( $search, $replace, get_option( 'wcmd_email' ) );
        $body = stripslashes( $body );
        $body = do_shortcode( $body );

        add_filter( 'wp_mail_content_type', array( $this, 'mail_content_type' ) );
        add_filter( 'wp_mail_from', array( $this, 'mail_from' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );
        $headers = array('Content-Type: text/html; charset=UTF-8');

        if ( version_compare( $woocommerce->version, '2.3',  ">=" ) ) {
            $mailer = WC()->mailer();
            $mailer->send( $email, $subject, $mailer->wrap_message( $subject, $body ), $headers, '' );
        }
        else
            wp_mail( $email, $subject, wpautop( $body ), $headers );

        remove_filter( 'wp_mail_content_type', array( $this, 'mail_content_type' ) );
        remove_filter( 'wp_mail_from', array( $this, 'mail_from' ) );
        remove_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ) );
        if( $email != get_option( 'wcmd_test_mail' ) ){
            $emails[] = $email;
            update_option( 'wcmd_mails', $emails );
        }

        return $code;
    }


    /**
     * This function is used to check whether merge field exists or not
     *  If merge fiel not exists then it will create the field
     * @param string $merge_var merge var that would be created.
     * @param string $merge_field merge field name
     * @param boolean $public the field should be public or not.
     *
     */
    public function check_merge_field($merge_var, $merge_field, $public) {
      if( !empty($merge_field) ) {
        $apiKey = get_option( 'wcmd_api_key' );
        $listId = get_option( 'wcmd_list_id' );
        $mailchimp = new MGMailChimp( $apiKey );
        $check_vars = $mailchimp->get("/lists/{$listId}/merge-fields");
        
        if( count($check_vars) > 0 ) {
          $tags_array = array();
          foreach( $check_vars as $vars_result ) {
            foreach( $vars_result as $key => $vals ) {
              array_push($tags_array, $vals['tag']);
            }
          }

          if( !in_array($merge_var, $tags_array) ) {
            $mailchimp->post("/lists/{$listId}/merge-fields",
              array(
                "tag" => $merge_var,
                "required" => true,
                "name" => $merge_field,
                "type" => "text",
                "default_value" => "",
                "public" => $public,
              )
            );
          }
        }
      }
    }


    public function wcmd_subscribe() {
        $email = $_POST['email'];
        $fname = isset( $_POST['fname'] ) ? $_POST['fname'] : '';
        $lname = isset( $_POST['lname'] ) ? $_POST['lname'] : '';
        $apiKey = get_option( 'wcmd_api_key' );
        $listId = get_option( 'wcmd_list_id' );
        $welcome = get_option( 'wcmd_welcome' ) == 'yes' ? true : false;
        $merge_vars = array( 'FNAME'=> $fname, 'LNAME'=> $lname );
        $source = get_option( 'wcmd_source' );

        if( !function_exists('icl_object_id') )
          $current_language = substr(get_bloginfo('language'), 0, 2);
        else
          $current_language = ICL_LANGUAGE_CODE;


        $optin = get_option( 'wcmd_double_optin' ) == 'yes' ? 'pending' : 'subscribed';
        $check_merge_vars = get_option('check_wcmd_merge_vars');

        if( $optin == 'pending' && function_exists('icl_object_id') && $check_merge_vars !== 'yes' ) {
          if( $check_merge_vars !== 'yes' ) {
            $this->check_merge_field('WCMDLANG', 'wcmdlang', false);
            update_option('check_wcmd_merge_vars', 'yes');
          }
        }

        if( get_option('wcmd_source') == 'yes' ) {
          $signup_source_link = get_option('wcmd_source_link');
          if( empty($signup_source_link) ) {
            $signup_source_link = 'WooCommerce Mailchimp Discount';
          }


          //check whether the merge fields for signup has been created or not
          if( get_option('wcmd_source_merge_vars') !== 'yes' ) {
            $this->check_merge_field('SOURCE', 'Signup Source', true);
            update_option('wcmd_source_merge_vars', 'yes');
          }
        }


        if( !empty( $apiKey ) && !empty( $listId ) ) {
          $MailChimp = new MGMailChimp( $apiKey );
          if( get_option('wcmd_source') == 'yes' ) {
            $result = $MailChimp->post("lists/{$listId}/members", [
              'email_address' => $email,
              'status'        => $optin,
              'merge_fields'  => Array( 'FNAME' => $fname, 'LNAME' => $lname, 'WCMDLANG' => $current_language, 'SOURCE' => $signup_source_link ),
              'language'      => $current_language
              ]);
          }

          else {
            $result = $MailChimp->post("lists/{$listId}/members", [
              'email_address' => $email,
              'status'        => $optin,
              'merge_fields'  => Array( 'FNAME' => $fname, 'LNAME' => $lname, 'WCMDLANG' => $current_language ),
              'language'      => $current_language
            ]);
          }

          if( array_key_exists('title', $result) && $result['title'] == 'Member Exists') {
            $subscriber_hash = md5(strtolower($email));
            $result = $MailChimp->put("lists/{$listId}/members/{$subscriber_hash}", [
              'email_address' => $email,
              'status'        => $optin,
              'merge_fields'  => Array( 'FNAME' => $fname, 'LNAME' => $lname, 'WCMDLANG' => $current_language ),
              'language'      => $current_language
              ]);
            $result['status'] = 'error';
          }
          else {
            $result['status'] = 'success';
          }

          if( $result['status'] == 'error'  )
            $result['error'] = $email . __( ' はすでに購読者として登録されています。', 'wcmd' );
            if( $optin == 'subscribed' && get_option( 'wcmd_disable_discount') != 'yes' && ( isset( $result['status'] ) && $result['status'] !='error' ) ) {
                $coupon_code = $this->wcmd_send_coupons( $email, $current_language );
              if( !empty($coupon_code) ) {
                $result['coupon_code'] = $coupon_code;
              }
            }
            echo json_encode($result);
        }
        else {
            echo json_encode( array( 'status' => 'error', 'error' => __( 'Please setup mailchimp api key and list id.', 'wcmd' ) ) );
        }
        exit;
    }

    /**
    *
    * Set default email from address set from the admin.
    *
    * @return string $from_email email address from which the email should be sent.
    *
    */
    public function mail_from() {
        $from_email = get_option( 'wcmd_email_id' );
        return $from_email;
    }

    /**
    *
    * Set default email from name set from the admin.
    *
    * @return string $from_name name  from which the email should be sent.
    *
    */
    public function mail_from_name() {
        $from_name = get_option( 'wcmd_email_name' );
        return $from_name;
    }

    /**
    *
    * Set email content type
    *
    * @return string content type for the email to be sent.
    *
    */
    public function mail_content_type() {
        return "text/html";
    }

  /**
    *
    * Our own custom method to verify the coupon for specific email address
    * as the one with woocommerce core doesn't work always.
    *
    * @param $valid boolean validation status.
    * @param $item list of values for the submitted coupon
    *
    * @return boolean status for coupon validation.
    *
    */
    public function validate_coupon( $valid, $item ) {
        if( is_array( $item->customer_email ) ) {
            global $current_user;
            wp_get_current_user();
            if( !is_user_logged_in() && $item->customer_email[0] != '' && $item->customer_email[0] != $current_user->user_email  ){
                add_filter('woocommerce_coupon_error', array($this,'custom_error'), 10, 3);
                return false;
            }
            else {
                if( $item->customer_email[0] != '' && $item->customer_email[0] != $current_user->user_email ){
                    add_filter('woocommerce_coupon_error', array($this,'custom_error'), 10, 3);
                    return false;
                }
            }
        }
        return $valid;
    }

    /**
    *
    * Custom error message for coupon validation.
    *
    * @param string $err default error message.
    * @param string $errcode error code for the error
    * @param array of values for the applied coupon
    *
    * @return string error message.
    *
    */
    public function custom_error( $err, $errcode, $val ) {
        if( !is_user_logged_in() )
            return __( 'Please login to apply this coupon.', 'wcmd' );
        else
            return __( 'This coupon is assigned to some other user, Please verify !', 'wcmd' );
    }

    /**
    *
    * Output products for the ajax search on admin.
    *
    * @return json matched products
    *
    */
    public function wcmd_ajax_products() {
        global $wpdb;
        $post_types = array( 'product' );
        ob_start();

        if ( empty( $term ) ) {
            $term = wc_clean( stripslashes( $_GET['term'] ) );
        } else {
            $term = wc_clean( $term );
        }

        if ( empty( $term ) ) {
            die();
        }

        $like_term = '%' . $wpdb->esc_like( $term ) . '%';

        if ( is_numeric( $term ) ) {
            $query = $wpdb->prepare( "
                SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
                WHERE posts.post_status = 'publish'
                AND (
                    posts.post_parent = %s
                    OR posts.ID = %s
                    OR posts.post_title LIKE %s
                    OR (
                        postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
                    )
                )
            ", $term, $term, $term, $like_term );
        } else {
            $query = $wpdb->prepare( "
                SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
                WHERE posts.post_status = 'publish'
                AND (
                    posts.post_title LIKE %s
                    or posts.post_content LIKE %s
                    OR (
                        postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
                    )
                )
            ", $like_term, $like_term, $like_term );
        }

        $query .= " AND posts.post_type IN ('" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "')";

        if ( ! empty( $_GET['exclude'] ) ) {
            $query .= " AND posts.ID NOT IN (" . implode( ',', array_map( 'intval', explode( ',', $_GET['exclude'] ) ) ) . ")";
        }

        if ( ! empty( $_GET['include'] ) ) {
            $query .= " AND posts.ID IN (" . implode( ',', array_map( 'intval', explode( ',', $_GET['include'] ) ) ) . ")";
        }

        if ( ! empty( $_GET['limit'] ) ) {
            $query .= " LIMIT " . intval( $_GET['limit'] );
        }

        $posts          = array_unique( $wpdb->get_col( $query ) );
        $found_products = array();

        if ( ! empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $product = wc_get_product( $post );

                if ( ! current_user_can( 'read_product', $post ) ) {
                    continue;
                }

                if ( ! $product || ( $product->is_type( 'variation' ) && empty( $product->parent ) ) ) {
                    continue;
                }

                $found_products[ $post ] = rawurldecode( $product->get_formatted_name() );
            }
        }

        wp_send_json( $found_products );
    }
    public function wcmd_shortcode( $atts ) {
        $options = shortcode_atts( array(
            'width' => '100%',
            'align' => '',
            'btn_width' => 'auto',
            'btn_align' => 'center',
            'top_text'  => '',
            'top_text_color' => '#000',
            'layout'    => 'vertical'
        ), $atts );
        extract( $options );

        if( $align == 'center' )
            $align = 'margin:0 auto;';
        else if( $align == 'left' || $align == 'right' )
            $align = 'float:' . $align . ';';

        $fields = get_option( 'wcmd_fields' );
        $form = '<div class="wcmd-form-wrapper wcmd-' . $layout . '" style="width:' . $width . '; ' . $align . '">';
        $form .= '<div class="wcmd-loading"></div>';
        if( $top_text != '' )
            $form .= '<div class="wcmd-top-title" style="color:' . $top_text_color . '">' . $top_text . '</div>';
        $form .= '<div class="wcmd_content">';
        $form .='<div class="wcmd_text">';
        $form .= '<form class="wcmd-form wcmd_' . $fields . '">';
        $form .= '<div class="validation-wrap"><span class="wcmd-validation"></span></div><div class="wcmd-fields">';
        if( $fields == 'email_name' || $fields == 'email_name_all' )
            $form .= '<input type="text" placeholder="'. __('Enter first name', 'wcmd' ) .'" name="wcmd_fname" class="wcmd_fname">';
        if( $fields == 'email_name_all' )
            $form .= '<input type="text" placeholder="'. __('Enter last name', 'wcmd' ) .'" name="wcmd_lname" class="wcmd_lname">';
        $form .='<input type="text" placeholder="'. __('Enter your email', 'wcmd' ) .'" name="wcmd_email" class="wcmd_email">';
        $form .= '</div><div class="wcmd-btn-cont" style="text-align:' . $btn_align . '">';
        $form .= '<button class="wcmd-btn" style="width:' . $btn_width . '">' . get_option( 'wcmd_btn_text' ) . '</button>';
        $form .= '</div><div class="wcmd-clear"></div></form>';
        $form .= '<div class="wcmd-clear"></div>';
        $form .='</div></div></div>';

        return $form;
    }

    /**
     * Add Contextual help tab
     */
    public function add_tabs() {
        $screen = get_current_screen();

        if ( $screen->id != 'woocommerce_page_wc-settings' )
            return;
        $screen->add_help_tab( array(
                'id'        => 'wcmd_wpml',
                'title'     => __( 'WPML ShortCodes ', 'wcmd' ),
                'content'   =>

                '<p>' . __( 'You can use [wcmd] shortcode to translate the contents of email body, email subject, popup text, popup header text and success message. Please find the list of variables.', 'woocommerce' ) . '</p>' .

                '<table class="widefat">
                    <tr>
                        <th>Variable</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td>lang</td>
                        <td>Language code of the content</td>
                    </tr>
                </table>' .
                '<p>' . __( 'Here some examples below:</br>[wcmd lang="en"]English Content[/wcmd]</br>[wcmd lang="fr"]French Content[/wcmd]', 'woocommerce' ) . '</p>'
                ));

        $screen->add_help_tab( array(
                'id'        => 'wcmd_help',
                'title'     => __( 'Mailchimp Discount ', 'wcmd' ),
                'content'   =>

                '<p>' . __( 'Thanks for purchasing the plugin. Please find the list of variables you can use for email body and email subject.', 'woocommerce' ) . '</p>' .

                '<table class="widefat">
                    <tr>
                        <th>Variable</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td><input readonly value="{COUPONCODE}"></td>
                        <td>The coupon code which the user will use to reedem his discount. Make sure you have added this in email content otherwise the user can\'t get the discount.</td>
                    </tr>
                    <tr>
                        <td><input readonly value="{COUPONEXPIRY}"></td>
                        <td>It will output the coupon expiry date if you have entered a value for coupon validity.</td>
                    </tr>
                    <tr>
                        <td><input readonly size="26" value="{ALLOWEDCATEGORIES}"></td>
                        <td>It will display the list of categories with their link on which the discount is applicable. Make sure you have selected some categories otherwise it will output nothing.</td>
                    </tr>
                    <tr>
                        <td><input readonly size="26" value="{EXCLUDEDCATEGORIES}"></td>
                        <td>It will display the list of categories with their link on which the discount is not applicable. Make sure you have selected some categories otherwise it will output nothing.</td>
                    </tr>
                    <tr>
                        <td><input readonly size="26" value="{ALLOWEDPRODUCTS}"></td>
                        <td>It will display the list of products with their link on which the discount is applicable. Make sure you have selected some products otherwise it will output nothing.</td>
                    </tr>
                    <tr>
                        <td><input readonly size="26" value="{EXCLUDEDPRODUCTS}"></td>
                        <td>It will display the list of products with their link on which the discount is not applicable. Make sure you have selected some products otherwise it will output nothing.</td>
                    </tr>
                </table>'));

        $screen->add_help_tab( array(
                'id'        => 'wcmd_help_shortcode',
                'title'     => __( 'Mailchimp Discount Shortcode', 'wcmd' ),
                'content'   => '<p>' . __( 'You can use <i>[wc_mailchimp_discount]</i> shortcode to use the mailchimp discout form on your page/post/widget etc.<br>Please find the list of variables you can use with shortcode.' ) . '</p>'.

                '<table class="widefat">
                    <tr>
                        <th>Variable</th>
                        <th>Description</th>
                    </tr>
                    <tr>
                        <td><input readonly size="10" value="width"></td>
                        <td>Define a width for the signup form. <br>Possible values: 100px, 100%, 500px etc. <br>Usage: [wc_mailchimp_discount width="400px"]</td>
                    </tr>
                    <tr>
                        <td><input readonly size="10" value="align"></td>
                        <td>Set the alignment for the signup form. <br> Possible values: left,right and center.<br>Usage: [wc_mailchimp_discount align="center"]</td>
                    </tr>
                    <tr>
                        <td><input readonly size="10" value="btn_width"></td>
                        <td>Set width for the subscribe button.<br> Possible values: 100px, 429px, 100%, 69% etc.<br>Usage: [wc_mailchimp_discount btn_width="300px"]</td>
                    </tr>
                    <tr>
                        <td><input readonly size="10" value="btn_align"></td>
                        <td>Set the alignment for the subscribe button. <br> Possible values: left,right and center.<br>Usage: [wc_mailchimp_discount btn_align="right"]</td>
                    </tr>
                    <tr>
                        <td><input readonly size="10" value="top_text"></td>
                        <td>Define a text that would appear on top of the form.<br>Usage: [wc_mailchimp_discount top_text="Subscribe to our newsletter and win discount"]</td>
                    </tr>
                    <tr>
                        <td><input readonly size="16" value="top_text_color"></td>
                        <td>Set a text color for the top text.<br>Usage: [wc_mailchimp_discount top_text_color="#ffcc00"]</td>
                    </tr>
                </table>'.
                '<p>' . 'You can combine any of the shortcode variables and create different type of forms. Check some examples below:<br>'.
                '[wc_mailchimp_discount width="400px" align="center" btn_width="100%" texttop_text_top="Signup for newsletter" top_text_color="#333333"]</b>'
            ) );
    }

}