<?php

?>

<div class="yith-wcpmr-conditions-row">
    <div class="yith-wcpmr-type-of-conditions">
        <div class="yith-wcpmr-restriction-by">

            <?php do_action('yith_wcpmr_before_conditions_row'); ?>

            <div class="yith-wcpmr-type-restriction yith-wcpmr-row">
                 <input type="text" name="yith-wcpmr-rule[conditions][<?php echo $i?>'][type_restriction]" value="product" readonly>
            </div>

            <div class="yith-wcpmr-restriction-by yith-wcpmr-select2 yith-wcpmr-row">
                <?php echo yith_wcpmr_get_dropdown(array(
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][restriction_by]',
                    'id' => '',
                    'style' => '',
                    'class' => isset($restriction_by) ? ' yith-wcpmr-rule yith-wcpmr-restriction-type yith-wcpmr-select yith-wcpmr yith-wcpmr-li yith-wcpmr-rule-set' : 'yith-wcpmr-rule yith-wcpmr-restriction-type yith-wcpmr-select yith-wcpmr',
                    'options' => yith_wcpmr_restriction_type(),
                    'disabled' => 'default',
                    'value' => isset($restriction_by) ? $restriction_by : '',
                )); ?>
            </div>

            <div data-type="yith-products" class="yith-wcpmr-select2 yith-wcpmr-select2-product yith-wcpmr-row">
                <?php
                $data_selected = array();

                if (!empty($products_selected)) {
                    $products = is_array($products_selected) ? $products_selected : explode(',', $products_selected);
                    if ($products) {
                        foreach ($products as $product_id) {
                            $product = wc_get_product($product_id);
                            $data_selected[$product_id] = $product->get_formatted_name();
                        }
                    }
                }
                $class = version_compare(WC()->version, '3.0.0', '>=') ? 'wc-product-search yith-wcpmr-information yith-wcpmr-product-search yith-wcpmr-select yith-wcpmr yith-wcpmr-li' : 'wc-product-search yith-wcpmr-product-search yith-wcpmr-select yith-wcpmr yith-wcpmr-li';
                $class = isset($products_selected) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class.' yith-wcpmr-hide-rule-set';
                $search_products_array = array(
                    'type' => '',
                    'class' => $class,
                    'id' => 'yith_wcpmr_product_selector',
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][products_selected]',
                    'data-placeholder' => esc_attr__('Search for a product&hellip;', 'yith-google-product-feed-for-woocommerce'),
                    'data-allow_clear' => false,
                    'data-selected' => $data_selected,
                    'data-multiple' => true,
                    'data-action' => 'woocommerce_json_search_products',
                    'value' => empty($products_selected) ? '' : $products_selected,
                    'style' => '',
                    'custom-attributes' => array(
                        'data-type' => 'product'
                    ),
                );
                yit_add_select2_fields($search_products_array,$i);


                ?>
            </div>

            <?php do_action('yith_wcpmr_conditions_row'); ?>


            <div class="yith-wcpmr-delete-condition yith-wcpmr-row">
                <input type="button" class="button-secondary yith-wcpmr-delete-condition" value="<?php _e('Delete', 'yith-payment-method-restrictions-for-woocommerce');?>">
            </div>
        </div>
    </div>
</div>


