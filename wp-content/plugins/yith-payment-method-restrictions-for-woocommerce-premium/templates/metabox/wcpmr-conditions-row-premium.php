<?php

?>
<div class="yith-wcpmr-conditions-row">
    <div class="yith-wcpmr-type-of-conditions">
        <div class="yith-wcpmr-restriction-by">

            <?php do_action('yith_wcpmr_before_conditions_row'); ?>

            <div class="yith-wcpmr-type-restriction yith-wcpmr-row">
                <?php echo yith_wcpmr_get_dropdown(array(
                    'name'      =>  'yith-wcpmr-rule[conditions]['.$i.'][type_restriction]',
                    'id'        =>  '',
                    'class'     =>  isset($type_restriction) ? ' yith-wcpmr-rule yith-wcpmr-get-type-restriction yith-wcpmr-select yith-wcpmr-li yith-wcpmr-rule-set' : 'yith-wcpmr-rule yith-wcpmr-get-type-restriction yith-wcpmr-select',
                    'options'   =>  yith_wcpmr_get_type_of_restrictions(),
                    'value'     =>  isset($type_restriction) ? $type_restriction : '',
                )); ?>
            </div>

            <div class="yith-wcpmr-restriction-by yith-wcpmr-select2 yith-wcpmr-row">
                <?php echo yith_wcpmr_get_dropdown(array(
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][restriction_by]',
                    'id' => '',
                    'style' => isset($restriction_by) ? '' : 'display: none;',
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
                    'style' => 'display:none;',
                    'custom-attributes' => array(
                        'data-type' => 'product'
                    ),
                );
                yit_add_select2_fields($search_products_array,$i);


                ?>
            </div>

            <div class="yith-wcpmr-restriction-by-price yith-wcpmr-select2 yith-wcpmr-row">
                <?php echo yith_wcpmr_get_dropdown(array(
                    'name'      =>  'yith-wcpmr-rule[conditions]['.$i.'][restriction_by_price]',
                    'id'        =>  '',
                    'style'     =>  isset($restriction_by_price) ? '' : 'display: none;',
                    'class'     =>  isset($restriction_by_price) ? 'yith-wcpmr-rule yith-wcpmr-rule-price yith-wcpmr-select yith-wcpmr yith-wcpmr-li yith-wcpmr-rule-set' : 'yith-wcpmr-rule yith-wcpmr-rule-price yith-wcpmr-select yith-wcpmr yith-wcpmr-li',
                    'options'   =>   yith_wcpmr_price_order(),
                    'value'     =>   isset($restriction_by_price) ? $restriction_by_price : '',
                )); ?>
            </div>

            <div class="yith-wcpmr-input-price yith-wcpmr-row">
                <?php
                $class = 'yith-wcpmr-input-price yith-wcpmr-price yith-wcpmr yith-wcpmr-li';
                $class = isset($price) ? $class . ' yith-wcpmr-rule-set' : $class ;
                ?>
                <input type="text" class="<?php echo $class ?> " name="yith-wcpmr-rule[conditions][<?php echo $i ?>][price]" value="<?php echo isset($price) ? $price : '' ?>" style="display:none" >
            </div>


            <div data-type="yith-categories" class="yith-wcpmr-select2 yith-wcpmr-select2-categories yith-wcpmr-row">
                <?php

                $data_selected = array();
                if (!empty($categories_selected)) {
                    $categories = is_array($categories_selected) ? $categories_selected : explode(',', $categories_selected);
                    if ($categories) {
                        foreach ($categories as $category_id) {
                            $term = get_term_by('id', $category_id, 'product_cat', 'ARRAY_A');
                            $data_selected[$category_id] = $term['name'];
                        }
                    }
                }
                $class = version_compare(WC()->version, '3.0.0', '>=') ? 'yith-wcpmr-category-search yith-wcpmr-information yith-wcpmr-categories yith-wcpmr-select yith-wcpmr yith-wcpmr-li' : 'yith-wcpmr-category-search yith-wcpmr-categories yith-wcpmr-select yith-wcpmr yith-wcpmr-li';

                $class = isset($categories_selected) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class;

                $search_cat_array = array(
                    'type' => '',
                    'class' => $class,
                    'id' => 'yith_wcpmr_category_selector',
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][categories_selected]',
                    'data-placeholder' => esc_attr__('Search for a category&hellip;', 'yith-payment-method-restrictions-for-woocommerce'),
                    'data-allow_clear' => false,
                    'data-selected' => $data_selected,
                    'data-multiple' => true,
                    'data-action' => '',
                    'value' => empty($categories_selected) ? '' : $categories_selected,
                    'style' => 'display: none;',
                    'custom-attributes' => array(
                        'data-type' => 'category'
                    ),
                );
                yit_add_select2_fields($search_cat_array);

                ?>
            </div>

            <div data-type="yith-tags" class="yith-wcpmr-select2 yith-wcpmr-select2-tags yith-wcpmr-row">
                <?php

                $data_selected = array();
                if (!empty($tags_selected)) {
                    $tags = is_array($tags_selected) ? $tags_selected : explode(',', $feed['tags_selected']);
                    if ($tags) {
                        foreach ($tags as $tag_id) {
                            $term = get_term_by('id', $tag_id, 'product_tag', 'ARRAY_A');
                            $data_selected[$tag_id] = $term['name'];
                        }
                    }
                }

                $class = version_compare(WC()->version, '3.0.0', '>=') ? ' yith-wcpmr-tags-search yith-wcpmr-information yith-wcpmr-tags yith-wcpmr-select yith-wcpmr yith-wcpmr-li' : 'yith-wcmr-tags-search yith-wcpmr-tags yith-wcpmr-select yith-wcpmr yith-wcpmr-li';
                $class = isset($tags_selected) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class;

                $search_tag_array = array(
                    'type' => '',
                    'class' => $class,
                    'id' => 'yith_wcpmr_tags_selector',
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][tags_selected]',
                    'data-placeholder' => esc_attr__('Search for a tag&hellip;', 'yith-payment-method-restrictions-for-woocommerce'),
                    'data-allow_clear' => false,
                    'data-selected' => $data_selected,
                    'data-multiple' => true,
                    'data-action' => '',
                    'value' => empty($tags_selected) ? '' : $tags_selected,
                    'style' => 'display: none;',
                    'custom-attributes' => array(
                        'data-type' => 'tag'
                    ),
                );

                yit_add_select2_fields($search_tag_array);
                ?>
            </div>



            <div class="yith-wcpmr-select2 yith-wcpmr-select2-geolocalization yith-wcpmr-row">
                <?php
                $country = WC()->countries->countries;
                $class = 'yith-wcpmr-select yith-wcpmr yith-wcpmr-geolocalization-search yith-wcpmr-li';
                $class = isset ($geolocalization) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class;
                echo yith_wcpmr_get_dropdown_multiple(array(
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][geolocalization][]',
                    'id' => '',
                    'style' => isset($geolocalization) ? '' : 'display: none;',
                    'class' => $class,
                    'options' => $country,
                    'multiple' => 'multiple',
                    'value' => isset($geolocalization) ? $geolocalization : '',
                    'custom-attributes' => array(
                        'data-type' => 'geolocalization',
                    ),
                ));
                ?>
            </div>

            <div class="yith-wcpmr-select2 yith-wcpmr-select2-role yith-wcpmr-row">
                <?php
                $role_option = yith_wcpmr_get_user_roles();
                $class = 'yith-wcpmr-select yith-wcpmr yith-wcpmr-role-search yith-wcpmr-li';
                $class = isset ($role) ? $class.' yith-wcpmr-rule-set yith-wcpmr-selector2' : $class;
                echo yith_wcpmr_get_dropdown_multiple(array(
                    'name' => 'yith-wcpmr-rule[conditions][' . $i . '][role][]',
                    'id' => '',
                    'style' => isset($role) ? '' : 'display: none;',
                    'class' => $class,
                    'options' => $role_option,
                    'multiple' => 'multiple',
                    'value' => isset($role) ? $role : '',
                    'custom-attributes' => array(
                        'data-type' => 'role',
                    ),
                ));
                ?>
            </div>

            <?php do_action('yith_wcpmr_conditions_row',$args,$i);  ?>


            <div class="yith-wcpmr-delete-condition yith-wcpmr-row">
                <input type="button" class="button-secondary yith-wcpmr-delete-condition" value="<?php _e('Delete', 'yith-payment-method-restrictions-for-woocommerce');?>">
            </div>
        </div>
    </div>
</div>


