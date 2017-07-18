<?php
/**
 * Booster for WooCommerce - Settings - Products XML
 *
 * @version 2.8.0
 * @since   2.8.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$product_cats_options = wcj_get_terms( 'product_cat' );
$product_tags_options = wcj_get_terms( 'product_tag' );
$products_options     = wcj_get_products();
$settings = array(
	array(
		'title'    => __( 'Options', 'woocommerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_products_xml_options',
	),
	array(
		'title'    => __( 'Total Files', 'woocommerce-jetpack' ),
		'id'       => 'wcj_products_xml_total_files',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc_tip' => __( 'Press Save changes after you change this number.', 'woocommerce-jetpack' ),
		'desc'     => apply_filters( 'booster_get_message', '', 'desc' ),
		'custom_attributes' => is_array( apply_filters( 'booster_get_message', '', 'readonly' ) ) ?
			apply_filters( 'booster_get_message', '', 'readonly' ) : array( 'step' => '1', 'min'  => '1', ),
	),
	array(
		'title'    => __( 'Advanced: Block Size', 'woocommerce-jetpack' ),
		'desc_tip' => __( 'If you have large number of products you may want to modify block size for WP_Query call. Leave default value if not sure.', 'woocommerce-jetpack' ),
		'id'       => 'wcj_products_xml_block_size',
		'default'  => 256,
		'type'     => 'number',
		'custom_attributes' => array( 'step' => '1', 'min'  => '1', ),
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_products_xml_options',
	),
);
for ( $i = 1; $i <= apply_filters( 'booster_get_option', 1, get_option( 'wcj_products_xml_total_files', 1 ) ); $i++ ) {
	$products_xml_cron_desc = '';
	if ( $this->is_enabled() ) {
		if ( '' != get_option( 'wcj_create_products_xml_cron_time_' . $i, '' ) ) {
			$scheduled_time_diff = get_option( 'wcj_create_products_xml_cron_time_' . $i, '' ) - time();
			if ( $scheduled_time_diff > 0 ) {
				$products_xml_cron_desc = '<em>' . sprintf( __( '%s seconds till next update.', 'woocommerce-jetpack' ), $scheduled_time_diff ) . '</em>';
			}
		}
		$products_xml_cron_desc .= '<br><a href="' . add_query_arg( 'wcj_create_products_xml', $i ) . '">' . __( 'Create Now', 'woocommerce-jetpack' ) . '</a>';
	}
	$products_time_file_created_desc = '';
	if ( '' != get_option( 'wcj_products_time_file_created_' . $i, '' ) ) {
		$products_time_file_created_desc = sprintf(
			__( 'Recent file was created on %s', 'woocommerce-jetpack' ),
			date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), get_option( 'wcj_products_time_file_created_' . $i, '' ) )
		);
	}
	$default_file_name = ( ( 1 == $i ) ? 'products.xml' : 'products_' . $i . '.xml' );
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'XML File', 'woocommerce-jetpack' ) . ' #' . $i,
			'type'     => 'title',
			'desc'     => $products_time_file_created_desc,
			'id'       => 'wcj_products_xml_options_' . $i,
		),
		array(
			'title'    => __( 'Enabled', 'woocommerce-jetpack' ),
			'desc'     => __( 'Enable', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_enabled_' . $i,
			'default'  => 'yes',
			'type'     => 'checkbox',
		),
		array(
			'title'    => __( 'XML Header', 'woocommerce-jetpack' ),
			'desc'     => __( 'You can use shortcodes here. For example [wcj_current_datetime].', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_header_' . $i,
			'default'  => '<?xml version = "1.0" encoding = "utf-8" ?>' . PHP_EOL . '<root>' . PHP_EOL,
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:150px;',
		),
		array(
			'title'    => __( 'XML Item', 'woocommerce-jetpack' ),
			'desc'     => sprintf(
				__( 'You can use shortcodes here. Please take a look at <a target="_blank" href="%s">Booster\'s products shortcodes</a>.', 'woocommerce-jetpack' ),
				'http://booster.io/category/shortcodes/products-shortcodes/'
			),
			'id'       => 'wcj_products_xml_item_' . $i,
			'default'  =>
				'<item>' . PHP_EOL .
					"\t" . '<name>[wcj_product_title strip_tags="yes"]</name>' . PHP_EOL .
					"\t" . '<link>[wcj_product_url strip_tags="yes"]</link>' . PHP_EOL .
					"\t" . '<price>[wcj_product_price hide_currency="yes" strip_tags="yes"]</price>' . PHP_EOL .
					"\t" . '<image>[wcj_product_image_url image_size="full" strip_tags="yes"]</image>' . PHP_EOL .
					"\t" . '<category_full>[wcj_product_categories_names strip_tags="yes"]</category_full>' . PHP_EOL .
					"\t" . '<category_link>[wcj_product_categories_urls strip_tags="yes"]</category_link>' . PHP_EOL .
				'</item>' . PHP_EOL,
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:300px;',
		),
		array(
			'title'    => __( 'XML Footer', 'woocommerce-jetpack' ),
			'desc'     => __( 'You can use shortcodes here.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_footer_' . $i,
			'default'  => '</root>',
			'type'     => 'custom_textarea',
			'css'      => 'width:66%;min-width:300px;min-height:150px;',
		),
		array(
			'title'    => __( 'XML File Path and Name', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'Path on server:', 'woocommerce-jetpack' ) . ' ' . ABSPATH . get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ),
			'desc'     => __( 'URL:', 'woocommerce-jetpack' ) . ' ' . '<a target="_blank" href="' . site_url() . '/' . get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ) . '">' . site_url() . '/' . get_option( 'wcj_products_xml_file_path_' . $i, $default_file_name ) . '</a>', // todo
			'id'       => 'wcj_products_xml_file_path_' . $i,
			'default'  => $default_file_name,
			'type'     => 'text',
			'css'      => 'width:66%;min-width:300px;',
		),
		array(
			'title'    => __( 'Update Period', 'woocommerce-jetpack' ),
			'desc'     => $products_xml_cron_desc,
			'id'       => 'wcj_create_products_xml_period_' . $i,
			'default'  => 'weekly',
			'type'     => 'select',
			'options'  => array(
				'minutely'   => __( 'Update Every Minute', 'woocommerce-jetpack' ),
				'hourly'     => __( 'Update Hourly', 'woocommerce-jetpack' ),
				'twicedaily' => __( 'Update Twice Daily', 'woocommerce-jetpack' ),
				'daily'      => __( 'Update Daily', 'woocommerce-jetpack' ),
				'weekly'     => __( 'Update Weekly', 'woocommerce-jetpack' ),
			),
			'desc_tip' => __( 'Possible update periods are: every minute, hourly, twice daily, daily and weekly.', 'woocommerce-jetpack' ) . ' ' . apply_filters( 'booster_get_message', '', 'desc_no_link' ),
			'custom_attributes' => apply_filters( 'booster_get_message', '', 'disabled' ),
		),
		array(
			'title'    => __( 'Products to Include', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To include selected products only, enter products here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_products_incl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $products_options,
		),
		array(
			'title'    => __( 'Products to Exclude', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To exclude selected products, enter products here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_products_excl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $products_options,
		),
		array(
			'title'    => __( 'Categories to Include', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To include products from selected categories only, enter categories here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_cats_incl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'title'    => __( 'Categories to Exclude', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To exclude products from selected categories, enter categories here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_cats_excl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'title'    => __( 'Tags to Include', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To include products from selected tags only, enter tags here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_tags_incl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'title'    => __( 'Tags to Exclude', 'woocommerce-jetpack' ),
			'desc_tip' => __( 'To exclude products from selected tags, enter tags here. Leave blank to include all products.', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_tags_excl_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'title'    => __( 'Products Scope', 'woocommerce-jetpack' ),
			'id'       => 'wcj_products_xml_scope_' . $i,
			'default'  => 'all',
			'type'     => 'select',
			'options'  => array(
				'all'               => __( 'All products', 'woocommerce-jetpack' ),
				'sale_only'         => __( 'Only products that are on sale', 'woocommerce-jetpack' ),
				'not_sale_only'     => __( 'Only products that are not on sale', 'woocommerce-jetpack' ),
				'featured_only'     => __( 'Only products that are featured', 'woocommerce-jetpack' ),
				'not_featured_only' => __( 'Only products that are not featured', 'woocommerce-jetpack' ),
			),
		),
		array(
			'type'     => 'sectionend',
			'id'       => 'wcj_products_xml_options_' . $i,
		),
	) );
}
return $settings;
