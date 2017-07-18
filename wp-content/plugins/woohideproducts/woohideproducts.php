<?php
/*
 * Plugin Name: WooCommerce Hide Products
 * Plugin URI: http://codecanyon.net/user/codewoogeek
 * Description: WooCommerce Hide Shop Products can able to hide shop products based on User Role
 * Version: 4.4
 * Author: codewoogeek
 * Author URI: http://codewoogeek.com/
 */

if (!defined('ABSPATH')) {
    exit;
}

function cwg_check_woocommerce_is_active() {
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return false;
    }
}

//add_action('init', 'cwg_check_woocommerce_is_active');

add_action('init', 'cwg_avoid_header_sent_problem');

function cwg_avoid_header_sent_problem() {
    ob_start();
}

add_filter('terms_clauses', 'cwg_term_clauses', 10, 3);

function cwg_term_clauses($pieces, $taxonomy, $args) {
    if (!is_admin() && in_array('product_cat', $taxonomy)) {
        if (is_user_logged_in()) {
            $get_role = cwg_get_user_role_from_id(get_current_user_id());
            $get_settings = cwg_get_options_value_from_role($get_role);
        } else {
            $get_role = "guest";
            $get_settings = cwg_get_options_value_from_role($get_role);
        }

        if ($get_settings['type'] == 'categories') {
            $array_of_category_ids = array_filter((array) $get_settings['categoryids']);
            if (!empty($array_of_category_ids)) {
                $get_ids = cwg_get_data_from_wpml($array_of_category_ids, 'product_cat');
                $merge_data = array_unique(array_filter(array_merge($array_of_category_ids, $get_ids)));
                $array_of_category_ids = join(',', $merge_data);
                $visibility = $get_settings['visibility'];
                $append_where = $pieces['where'];
                if ($visibility == 'include') {
                    //IN
                    $append_where .= " AND t.term_id IN ($array_of_category_ids)";
                    $pieces['where'] = $append_where;
                } else {
                    //NOT IN
                    $append_where .= " AND t.term_id NOT IN ($array_of_category_ids)";
                    $pieces['where'] = $append_where;
                }
            }
        }
    }
    return $pieces;
}

function cwg_hide_woo_products($where, $query) {
    global $wpdb;

    if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {

        $product = $query->query_vars['post_type'];
        $product_category_check = isset($query->query_vars['taxonomy']) ? $query->query_vars['taxonomy'] : null;
        if ($product == 'product' || (isset($product_category_check) && ($product_category_check == 'product_cat' || $product_category_check == 'product_tag')) || is_search()) {
            if (is_user_logged_in()) {
                $get_role = cwg_get_user_role_from_id(get_current_user_id());
                $get_settings = cwg_get_options_value_from_role($get_role);
            } else {
                $get_role = "guest";
                $get_settings = cwg_get_options_value_from_role($get_role);
            }
            if ($get_settings['type'] == 'products') {
                $arrayofids = array_filter((array) $get_settings['productids']);

                if (!empty($arrayofids)) {
                    $get_ids = cwg_get_data_from_wpml($arrayofids, 'product');
                    $merge_data = array_unique(array_filter(array_merge($arrayofids, $get_ids)));
                    $arrayofids = join(',', $merge_data);
                    $visibility = $get_settings['visibility'];
                    if ($visibility == 'include') {
                        $where .= " AND `post_type`='product' AND `ID` IN ($arrayofids)";
                    } else {
                        $where .= " AND `post_type`='product' AND `ID` NOT IN ($arrayofids)";
                    }
                }
            } else {
                $array_of_category_ids = array_filter((array) $get_settings['categoryids']);
                if (!empty($array_of_category_ids)) {
                    $get_ids = cwg_get_data_from_wpml($array_of_category_ids, 'product_cat');
                    $merge_data = array_unique(array_filter(array_merge($array_of_category_ids, $get_ids)));
                    $array_of_category_ids = join(',', $merge_data);
                    $visibility = $get_settings['visibility'];

                    if ($visibility == 'include') {
                        $where .= " AND ID IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($array_of_category_ids) )";
                    } else {
                        $where .= " AND ID NOT IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ($array_of_category_ids) )";
                    }
                }
            }
        }
    }
    return $where;
}

add_filter('posts_where_paged', 'cwg_hide_woo_products', 10, 2);

function cwg_get_user_role_from_id($user_id) {
    $get_user_data = get_userdata($user_id);
    return $get_user_data->roles[0];
}

function cwg_get_options_value_from_role($role) {
    $array_structure = array(
        'type' => get_option('woo_toggle_products_by_type_' . $role) == '1' ? 'products' : 'categories',
        'visibility' => get_option('woo_include_exclude_products_' . $role) == '1' ? 'include' : 'exclude',
        'productids' => get_option('woo_select_products_' . $role),
        'categoryids' => get_option('woo_toggle_type_category_' . $role),
    );
    return $array_structure;
}

function cwg_get_data_from_wpml($ids, $type) {
    $get_all_ids = array();
    //run only when wpml is enabled
    if (function_exists('icl_get_languages')) {
        $all_languages = icl_get_languages();
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $each_id) {
                foreach ($all_languages as $lang => $row) {
                    $get_all_ids[] = icl_object_id($each_id, $type, false, $lang);
                }
            }
        }
    }
    return $get_all_ids;
}

add_filter('woocommerce_get_related_product_tag_terms', 'cwg_hide_related_products_based_on_tag', 10, 2);

function cwg_hide_related_products_based_on_tag($term_ids, $product_id) {

    if (is_user_logged_in()) {
        $get_role = cwg_get_user_role_from_id(get_current_user_id());
        $get_settings = cwg_get_options_value_from_role($get_role);
    } else {
        $get_role = "guest";
        $get_settings = cwg_get_options_value_from_role($get_role);
    }

    if ($get_settings['type'] == 'products') {
        $arrayofids = array_filter((array) $get_settings['productids']);
        if (!empty($arrayofids)) {
            $get_ids = cwg_get_data_from_wpml($arrayofids, 'product');
            $merge_data = array_unique(array_filter(array_merge($arrayofids, $get_ids)));
            $visibility = $get_settings['visibility'];

            $object_ids = $merge_data;
            $taxonomies = 'product_tag';
            $args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids');
            $data = wp_get_object_terms($object_ids, $taxonomies, $args);

            if ($visibility == 'include') {
                $term_ids = array_intersect($term_ids, $data);
            } else {
                $term_ids = array_diff($term_ids, $data);
            }
        }
    } else {
        $array_of_category_ids = array_filter((array) $get_settings['categoryids']);
        if (!empty($array_of_category_ids)) {
            $get_ids = cwg_get_data_from_wpml($array_of_category_ids, 'product_cat');
            $merge_data = array_unique(array_filter(array_merge($array_of_category_ids, $get_ids)));

            $visibility = $get_settings['visibility'];

            $product_ids = new WP_Query(array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'fields' => 'ids',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $merge_data,
                        'operator' => 'IN',
                    )
                ),
            ));

            $object_ids = $product_ids;

            $taxonomies = 'product_tag';
            $args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids');
            $data = wp_get_object_terms($object_ids, $taxonomies, $args);
            if ($visibility == 'include') {
                $term_ids = array_intersect($term_ids, $data);
            } else {
                $term_ids = array_diff($term_ids, $data);
            }
        }
    }
    return $term_ids;
}

add_filter('woocommerce_get_related_product_cat_terms', 'cwg_hide_related_products_based_on_category', 10, 2);

function cwg_hide_related_products_based_on_category($term_ids, $product_id) {


    if (is_user_logged_in()) {
        $get_role = cwg_get_user_role_from_id(get_current_user_id());
        $get_settings = cwg_get_options_value_from_role($get_role);
    } else {
        $get_role = "guest";
        $get_settings = cwg_get_options_value_from_role($get_role);
    }

    if ($get_settings['type'] == 'products') {
        $arrayofids = array_filter((array) $get_settings['productids']);
        if (!empty($arrayofids)) {
            $get_ids = cwg_get_data_from_wpml($arrayofids, 'product');
            $merge_data = array_unique(array_filter(array_merge($arrayofids, $get_ids)));
            $visibility = $get_settings['visibility'];

            $object_ids = $merge_data;
            $taxonomies = 'product_cat';
            $args = array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids');
            $data = wp_get_object_terms($object_ids, $taxonomies, $args);

            if ($visibility == 'include') {
                $term_ids = array_intersect($term_ids, $data);
            } else {
                $term_ids = array_diff($term_ids, $data);
            }
        }
    } else {
        $array_of_category_ids = array_filter((array) $get_settings['categoryids']);
        if (!empty($array_of_category_ids)) {
            $get_ids = cwg_get_data_from_wpml($array_of_category_ids, 'product_cat');
            $merge_data = array_unique(array_filter(array_merge($array_of_category_ids, $get_ids)));

            $visibility = $get_settings['visibility'];

            if ($visibility == 'include') {
                $term_ids = array_intersect($term_ids, $merge_data);
            } else {
                $term_ids = array_diff($term_ids, $merge_data);
            }
        }
    }

    return $term_ids;
}

function cwg_hide_products_upsells_crosssells($product_ids, $object) {
    global $wpdb;

    /* below is for product check */
    // array_diff means selected ignore ids will be hidden
    // array_intersect selected ids will be shown
    //    $ignore = array(50);
    // $product_ids = array_intersect($product_ids, $ignore);


    if (is_user_logged_in()) {
        $get_role = cwg_get_user_role_from_id(get_current_user_id());
        $get_settings = cwg_get_options_value_from_role($get_role);
    } else {
        $get_role = "guest";
        $get_settings = cwg_get_options_value_from_role($get_role);
    }

    if ($get_settings['type'] == 'products') {
        $arrayofids = array_filter((array) $get_settings['productids']);
        if (!empty($arrayofids)) {
            $get_ids = cwg_get_data_from_wpml($arrayofids, 'product');
            $merge_data = array_unique(array_filter(array_merge($arrayofids, $get_ids)));
            $visibility = $get_settings['visibility'];
            if ($visibility == 'include') {
                $sellids = $product_ids;
                $product_ids = array_intersect($sellids, $merge_data);
            } else {
                $sellids = $product_ids;
                $product_ids = array_diff($sellids, $merge_data);
            }
        }
    } else {
        $array_of_category_ids = array_filter((array) $get_settings['categoryids']);
        if (!empty($array_of_category_ids)) {
            $get_ids = cwg_get_data_from_wpml($array_of_category_ids, 'product_cat');
            $merge_data = array_unique(array_filter(array_merge($array_of_category_ids, $get_ids)));
            $array_of_category_ids = join(',', $merge_data);
            $array_of_product_ids = join(',', $product_ids);
            $query = $wpdb->get_results($wpdb->prepare("SELECT GROUP_CONCAT(object_id) AS product_ids FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (%s) AND object_id IN (%s)", $array_of_category_ids, $array_of_product_ids), 'ARRAY_A');


            $visibility = $get_settings['visibility'];
            if ($query && !empty($query)) {
                $get_category_matched_sell_ids = $query['product_ids'];
                $explode_data = explode(',', $get_category_matched_sell_ids);
                if ($visibility == 'include') {
                    $product_ids = array_intersect($product_ids, $explode_data);
                } else {
                    $product_ids = array_diff($product_ids, $explode_data);
                }
            }
        }
    }
    return $product_ids;
}

add_filter('woocommerce_product_get_upsell_ids', 'cwg_hide_products_upsells_crosssells', 10, 2);
add_filter('woocommerce_product_get_crosssell_ids', 'cwg_hide_products_upsells_crosssells', 10, 2);

add_filter('woocommerce_product_upsell_ids', 'cwg_hide_products_upsells_crosssells', 10, 2);
add_filter('woocommerce_product_crosssell_ids', 'cwg_hide_products_upsells_crosssells', 10, 2);

function cwg_common_function_to_show_hide($data) {
    ob_start();
    ?>
    var alter_data = jQuery('#woo_toggle_products_by_type_<?php
    echo $data;
    ?>').val();
    if(alter_data==='1') {
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().show();

    }else {

    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().show();
    }
    jQuery('#woo_toggle_products_by_type_<?php
    echo $data;
    ?>').change(function(){
    var current_data = jQuery(this).val();
    if(current_data==='1') {
    // Hide the Category alone here
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().show();

    }else {
    // Hide the Products and Category Checkbox  (for category it is useless)
    jQuery('#woo_select_products_<?php
    echo $data;
    ?>').parent().parent().hide();
    jQuery('#woo_toggle_type_category_<?php
    echo $data;
    ?>').parent().parent().show();

    }
    });
    <?php
    return ob_get_clean();
}

function cwg_load_script_to_admin() {
    global $woocommerce;
//var_dump($woocommerce->version);
    if (isset($_GET['tab'])) {
        if ($_GET['tab'] == 'woocommerce_hide_products') {
            ?>
            <script type="text/javascript">
                jQuery(function () {
            <?php
            global $wp_roles;
            if (!isset($wp_roles)) {
                $wp_roles = new WP_Roles();
            }
            $getdata = $wp_roles->get_names();
            if ((float) $woocommerce->version > "2.2.0") {

                $k = 0;
                foreach ($getdata as $data => $key) {

                    if ($k == 0) {
                        ?>
                                //                                jQuery('#woo_select_products_guest').select2();
                                jQuery('#woo_toggle_type_category_guest').select2();

                        <?php
                        echo cwg_common_function_to_show_hide('guest');
                    }

                    echo cwg_common_function_to_show_hide($data);
                    ?>


                            //                            jQuery('#woo_select_products_<?php
                    echo $data;
                    ?>//').select2();
                            jQuery('#woo_toggle_type_category_<?php
                    echo $data;
                    ?>').select2();
                    <?php
                    $k++;
                }
            } else {
                $k = 0;
                foreach ($getdata as $data => $key) {
                    if ($k == 0) {
                        ?>
                                //jQuery('#woo_select_products_guest').chosen();
                                jQuery('#woo_toggle_type_category_guest').chosen();

                        <?php
                        echo cwg_common_function_to_show_hide('guest');
                    }
                    echo cwg_common_function_to_show_hide($data);
                    ?>
                            //                            jQuery('#woo_select_products_<?php
                    echo $data;
                    ?>//').chosen();
                            jQuery('#woo_toggle_type_category_<?php
                    echo $data;
                    ?>').chosen();
                    <?php
                    $k++;
                }
            }
            ?>
                });
            </script>
            <?php
        }
    }
}

add_action('admin_head', 'cwg_load_script_to_admin');
include_once('inc/class-admin-settings.php');
include('updates/github.php');
