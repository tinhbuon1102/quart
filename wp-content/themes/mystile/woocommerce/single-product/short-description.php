<?php
/**
 * Single product short description
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;
$terms = wp_get_post_terms( $post->ID, 'product_cat' );
foreach ( $terms as $term ) $categories[] = $term->slug;

if ( ! $post->post_excerpt ) {
	return;
}
?>
<div itemprop="description" class="description">
<?php if ( $product->get_sku() ) : ?>
<div class="sku"><span class="jp">品番</span>: <?php echo $product->get_sku(); ?></div>
<?php endif; ?>
<?php  $categ = $product->get_categories();
if (preg_match("/Pre Order/i", $categ) && preg_match("/OUTER/i", $categ)) {
    ?>                       
<div class="pre-note">
<ul class="pre-list"><li>3月下旬からの発送開始となります。</li>
<li>お支払いは銀行振込またはPaypalのみでお願い致します。</li>
<li>キャンセル、不良品理由以外での返品は不可となります。</li>
</ul>
</div>
<?php } 
 elseif(preg_match("/Pre Order/i", $categ)) {
    ?>                       
<div class="pre-note">
<ul class="pre-list">
<li>3月下旬からの発送開始となります。</li>
<li>お支払いは銀行振込またはPaypalのみでお願い致します。</li>
<li>キャンセル、不良品理由以外での返品は不可となります。</li>
</ul>
</div>
<?php } 
else {
	
}?>


<?php
$table = get_field( 'product_size' );

if ( $table ) { 
	ob_start();
?>
<?php
echo ('[su_tabs class="my-custom-tabs"]');
echo ('[su_tab title="アイテム説明"]');
echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
echo ('[/su_tab]');
?>
<?php echo ('[su_tab title="サイズ詳細"]');

    echo '<table border="0">';

        if ( $table['header'] ) {

            echo '<thead>';

                echo '<tr>';

                    foreach ( $table['header'] as $th ) {

                        echo '<th>';
                            echo $th['c'];
                        echo '</th>';
                    }

                echo '</tr>';

            echo '</thead>';
        }

        echo '<tbody>';

            foreach ( $table['body'] as $tr ) {

                echo '<tr>';

                    foreach ( $tr as $td ) {

                        echo '<td>';
                            echo $td['c'];
                        echo '</td>';
                    }

                echo '</tr>';
            }

        echo '</tbody>';

    echo '</table>';
	
echo ('[/su_tab]');
echo ('[/su_tabs]'); ?>
<?php } elseif ( function_exists( 'YITH_WCPSC_Frontend' ) && is_callable( array( YITH_WCPSC_Frontend(), 'get_charts_from_product_id' ) ) && !!YITH_WCPSC_Frontend()->get_charts_from_product_id( $post->ID ) ) { ?>
	<?php 
	echo ('[su_tabs class="my-custom-tabs"]');
echo ('[su_tab title="アイテム説明"]');
echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
echo ('[/su_tab]');
	echo ('[su_tab title="サイズ詳細"]');
		echo '<div class="size-chart-yith">';
		echo ('[sizecharts type="charts"]');
		echo '<p>サイズ単位はcmです。</p>';
		echo '</div>';
	echo ('[/su_tab]');
echo ('[/su_tabs]');
	?>
<?php } else { ?>
	<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
	<?php }
	
$tabs_shortcode = ob_get_contents();
ob_end_clean();
echo do_shortcode($tabs_shortcode);
?>


</div>

<?php
if (is_accessory_product($post->ID))
{
    echo '<p class="notice_info"><span>受注商品は通常商品と一緒にカートに入れることはできません。</span></p>';
}
?>