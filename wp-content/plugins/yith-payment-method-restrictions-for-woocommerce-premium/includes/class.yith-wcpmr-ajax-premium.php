<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( !defined( 'YITH_WCPMR_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCPMR_Ajax_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
 *
 */

if ( !class_exists( 'YITH_WCPMR_Ajax_Premium' ) ) {
    /**
     * Class YITH_Payment_Restrictions_Admin_Premium
     *
     * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
     */
    class YITH_WCPMR_Ajax_Premium extends YITH_WCPMR_Ajax
    {

        /**
         * Construct
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function __construct()
        {
            add_action('wp_ajax_yith_wcpmr_save_bacs_account', array($this,'save_bacs_accounts'));
            add_action('wp_ajax_yith_wcpmr_add_bacs_account_row', array($this,'add_bacs_account_row'));
            add_action('wp_ajax_yith_wcpmr_category_search', array($this,'category_search'));
            add_action('wp_ajax_yith_wcpmr_tag_search', array($this,'tag_search'));


            parent::__construct();
        }

        /**
         * save_bacs_accounts
         *
         * Save all bank accounts
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */

        public function save_bacs_accounts() {

            $bacs_account_name      = isset( $_POST['account_name'] ) ? $_POST['account_name'] : false;
            $bacs_account_number    = isset( $_POST['account_number'] ) ? $_POST['account_number'] : false;
            $bacs_bank_name         = isset( $_POST['bank_name'] ) ? $_POST['bank_name'] : false;
            $bacs_sort_code         = isset( $_POST['sort_code'] ) ? $_POST['sort_code'] : false;
            $bacs_iban              = isset( $_POST['iban'] ) ? $_POST['iban'] : false;
            $bacs_bic               = isset( $_POST['bic'] ) ? $_POST['bic'] : false;

            $bac_accounts = array();

            $size = count( $bacs_account_name );
            for ($i = 0; $i < $size; $i++) {
                $array = array(
                    'account_name' => $bacs_account_name[$i],
                    'account_number' => $bacs_account_number[$i],
                    'bank_name'      => $bacs_bank_name[$i],
                    'sort_code'      => $bacs_sort_code[$i],
                    'iban'           => $bacs_iban[$i],
                    'bic'              => $bacs_bic[$i],
                );

                $bac_accounts['wcpmr'.$i] = $array;
            }

            update_option('yith-wcpmr-bacs-accounts',$bac_accounts);

            die();
        }
        /**
         * add_bacs_account_row
         *
         * Add new row in bank list
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0
         */
        public function add_bacs_account_row() {
            $args = array(
                'i' => $_POST['index'],
            );
            wc_get_template( 'wcpmr-bacs-account.php',$args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
            die();
        }


        /**
         * function category_search
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function category_search() {
            check_ajax_referer( 'search-categories', 'security' );

            ob_start();

            if ( version_compare( WC()->version, '2.7', '<' ) ) {
                $term = (string) wc_clean( stripslashes( $_GET['term'] ) );
            } else {
                $term = (string) wc_clean( stripslashes( $_GET['term']['term'] ) );
            }

            if ( empty( $term ) ) {
                die();
            }
            global $wpdb;
            $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_cat" and wpt.name LIKE "%'.$term.'%" ORDER BY name ASC;' );

            $found_categories = array();

            if ( $terms ) {
                foreach ( $terms as $cat ) {
                    $found_categories[$cat->term_id] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
                }
            }

            $found_categories = apply_filters( 'yith_wcpmr_json_search_categories', $found_categories );
            wp_send_json( $found_categories );
        }
        /**
         * function tag search
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public function tag_search() {
            check_ajax_referer( 'search-tags', 'security' );

            ob_start();

            if ( version_compare( WC()->version, '2.7', '<' ) ) {
                $term = (string) wc_clean( stripslashes( $_GET['term'] ) );
            } else {
                $term = (string) wc_clean( stripslashes( $_GET['term']['term'] ) );
            }

            if ( empty( $term ) ) {
                die();
            }
            global $wpdb;
            $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_tag" and wpt.name LIKE "%'.$term.'%" ORDER BY name ASC;' );

            $found_tags = array();

            if ( $terms ) {
                foreach ( $terms as $tag ) {
                    $found_tags[$tag->term_id] = ( $tag->name ) ? $tag->name : 'ID: ' . $tag->slug;
                }
            }

            $found_tags = apply_filters( 'yith_wcpmr_json_search_tags', $found_tags );
            wp_send_json( $found_tags );
        }

        /**
         * add_conditions_row
         *
         * Add new condition in the rule
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */

        public function add_conditions_row() {
            $args = array(
                'i' => $_POST['index'],
            );
            wc_get_template( 'wcpmr-conditions-row-premium.php',$args, '', YITH_WCPMR_TEMPLATE_PATH . 'metabox/' );
            die();
        }

    }
}