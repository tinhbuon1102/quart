<?php
/**
 * Functions
 *
 * @author  Yithemes
 * @package YITH WooCommerce Bulk Product Editing
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

if ( !function_exists( 'yith_wcbep_get_template' ) ) {
    function yith_wcbep_get_template( $template, $args = array() ) {
        extract( $args );
        include( YITH_WCBEP_TEMPLATE_PATH . '/' . $template );
    }
}

if ( !function_exists( 'yith_wcbep_strContains' ) ) {
    function yith_wcbep_strContains( $haystack, $needle ) {
        return stripos( $haystack, $needle ) !== false;
    }
}

if ( !function_exists( 'yith_wcbep_strStartsWith' ) ) {
    function yith_wcbep_strStartsWith( $haystack, $needle ) {
        return $needle === "" || strirpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
    }
}

if ( !function_exists( 'yith_wcbep_strEndsWith' ) ) {
    function yith_wcbep_strEndsWith( $haystack, $needle ) {
        return $needle === "" || ( ( $temp = strlen( $haystack ) - strlen( $needle ) ) >= 0 && stripos( $haystack, $needle, $temp ) !== false );
    }
}

if ( !function_exists( 'yith_wcbep_posts_filter_where' ) ) {
    function yith_wcbep_posts_filter_where( $where = '' ) {
        $f_title_sel       = !empty( $_REQUEST[ 'f_title_select' ] ) ? $_REQUEST[ 'f_title_select' ] : 'cont';
        $f_title_val       = isset( $_REQUEST[ 'f_title_value' ] ) ? $_REQUEST[ 'f_title_value' ] : '';
        $f_description_sel = !empty( $_REQUEST[ 'f_description_select' ] ) ? $_REQUEST[ 'f_description_select' ] : 'cont';
        $f_description_val = isset( $_REQUEST[ 'f_description_value' ] ) ? $_REQUEST[ 'f_description_value' ] : '';

        // Filter Title
        if ( isset( $f_title_val ) && strlen( $f_title_val ) > 0 ) {
            $compare = 'LIKE';
            $value   = '%' . $f_title_val . '%';
            switch ( $f_title_sel ) {
                case 'cont':
                    $compare = 'LIKE';
                    break;
                case 'notcont':
                    $compare = 'NOT LIKE';
                    break;
                case 'starts':
                    $compare = 'REGEXP';
                    $value   = '^' . $f_title_val;
                    break;
                case 'ends':
                    $compare = 'REGEXP';
                    $value   = $f_title_val . '$';
                    break;
                case 'regex':
                    $compare = 'REGEXP';
                    $value   = $f_title_val;
                    break;
            }

            $where .= " AND post_title {$compare} '{$value}'";
        }

        // Filter Description
        if ( isset( $f_description_val ) && strlen( $f_description_val ) > 0 ) {
            $compare = 'LIKE';
            $value   = '%' . $f_description_val . '%';
            switch ( $f_description_sel ) {
                case 'cont':
                    $compare = 'LIKE';
                    break;
                case 'notcont':
                    $compare = 'NOT LIKE';
                    break;
                case 'starts':
                    $compare = 'REGEXP';
                    $value   = '^' . $f_description_val;
                    break;
                case 'ends':
                    $compare = 'REGEXP';
                    $value   = $f_description_val . '$';
                    break;
                case 'regex':
                    $compare = 'REGEXP';
                    $value   = $f_description_val;
                    break;
            }

            $where .= " AND post_content {$compare} '{$value}'";
        }

        return $where;
    }
}

if ( !function_exists( 'yith_wcbep_get_terms' ) ) {
    function yith_wcbep_get_terms( $args = array() ) {
        global $wp_version;

        if ( version_compare( '4.5.0', $wp_version, '>=' ) ) {
            return get_terms( $args );
        } else {
            $taxonomy = isset( $args[ 'taxonomy' ] ) ? $args[ 'taxonomy' ] : '';
            if ( isset( $args[ 'taxonomy' ] ) )
                unset( $args[ 'taxonomy' ] );

            return get_terms( $taxonomy, $args );
        }
    }
}

if ( !function_exists( 'yith_wcbep_get_wc_product_types' ) ) {
    function yith_wcbep_get_wc_product_types() {
        $terms         = yith_wcbep_get_terms( array( 'taxonomy' => 'product_type' ) );
        $product_types = array();
        foreach ( $terms as $term ) {
            $name = sanitize_title( $term->name );
            switch ( $name ) {
                case 'grouped' :
                    $label = __( 'Grouped product', 'woocommerce' );
                    break;
                case 'external' :
                    $label = __( 'External/Affiliate product', 'woocommerce' );
                    break;
                case 'variable' :
                    $label = __( 'Variable product', 'woocommerce' );
                    break;
                case 'simple' :
                    $label = __( 'Simple product', 'woocommerce' );
                    break;
                default :
                    $label = ucfirst( $term->name );
                    break;
            }
            $product_types[ $name ] = $label;
        }

        return $product_types;
    }
}
if ( !function_exists( 'yith_wcbep_get_labels' ) ) {
    function yith_wcbep_get_labels() {
        return apply_filters(
            'yith_wcbep_labels', array(
                                   'ID'                 => __( 'ID', 'yith-woocommerce-bulk-product-editing' ),
                                   'title'              => __( 'Title', 'woocommerce' ),
                                   'slug'               => __( 'Slug', 'woocommerce' ),
                                   'image'              => __( 'Image', 'woocommerce' ),
                                   'image_gallery'      => __( 'Product gallery', 'woocommerce' ),
                                   'description'        => __( 'Description', 'woocommerce' ),
                                   'shortdesc'          => __( 'Short description', 'woocommerce' ),
                                   'regular_price'      => __( 'Regular price', 'woocommerce' ),
                                   'sale_price'         => __( 'Sale price', 'woocommerce' ),
                                   'purchase_note'      => __( 'Purchase note', 'woocommerce' ),
                                   'categories'         => __( 'Categories', 'woocommerce' ),
                                   'tags'               => __( 'Tags', 'woocommerce' ),
                                   'sku'                => __( 'SKU', 'woocommerce' ),
                                   'weight'             => __( 'Weight', 'woocommerce' ),
                                   'height'             => __( 'Height', 'woocommerce' ),
                                   'width'              => __( 'Width', 'woocommerce' ),
                                   'length'             => __( 'Length', 'woocommerce' ),
                                   'stock_quantity'     => __( 'Stock qty', 'woocommerce' ),
                                   'download_limit'     => __( 'Download limit', 'woocommerce' ),
                                   'download_expiry'    => __( 'Download expiry', 'woocommerce' ),
                                   'downloadable_files' => __( 'Downloadable files', 'woocommerce' ),
                                   'menu_order'         => __( 'Menu order', 'woocommerce' ),
                                   'stock_status'       => __( 'Stock status', 'woocommerce' ),
                                   'manage_stock'       => __( 'Manage stock', 'woocommerce' ),
                                   'sold_individually'  => __( 'Sold individually', 'woocommerce' ),
                                   'featured'           => __( 'Featured', 'woocommerce' ),
                                   'virtual'            => __( 'Virtual', 'woocommerce' ),
                                   'downloadable'       => __( 'Downloadable', 'woocommerce' ),
                                   'enable_reviews'     => __( 'Enable reviews', 'woocommerce' ),
                                   'tax_status'         => __( 'Tax status', 'woocommerce' ),
                                   'tax_class'          => __( 'Tax class', 'woocommerce' ),
                                   'allow_backorders'   => __( 'Allow backorders?', 'woocommerce' ),
                                   'shipping_class'     => __( 'Shipping class', 'woocommerce' ),
                                   'status'             => __( 'Status', 'woocommerce' ),
                                   'visibility'         => __( 'Catalog visibility', 'yith-woocommerce-bulk-product-editing' ),
                                   'download_type'      => __( 'Download Type', 'yith-woocommerce-bulk-product-editing' ),
                                   'prod_type'          => __( 'Product Type', 'woocommerce' ),
                                   'date'               => __( 'Date', 'yith-woocommerce-bulk-product-editing' ),
                                   'sale_price_from'    => __( 'Sale price from', 'yith-woocommerce-bulk-product-editing' ),
                                   'sale_price_to'      => __( 'Sale price to', 'yith-woocommerce-bulk-product-editing' ),
                                   'button_text'        => __( 'Button text', 'woocommerce' ),
                                   'product_url'        => __( 'Product URL', 'woocommerce' ),
                                   'up_sells'           => __( 'Upsells', 'woocommerce' ),
                                   'cross_sells'        => __( 'Cross-sells', 'woocommerce' ),
                               )
        );
    }
}

if ( !function_exists( 'yith_wcbep_get_label' ) ) {
    function yith_wcbep_get_label( $key ) {
        $labels = yith_wcbep_get_labels();
        $label  = isset( $labels[ $key ] ) ? $labels[ $key ] : '';

        return apply_filters( 'yith_wcbep_get_label', $label, $key );
    }
}