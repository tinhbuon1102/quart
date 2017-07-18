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
<?php if ( is_product() && has_term( 'pre-order', 'product_cat' ) ) { ?>                       
<div class="pre-note">
<ul class="pre-list">
<li>9月初旬からの発送開始となります。</li>
<li>お支払いは銀行振込またはPaypalのみでお願い致します。</li>
<li>キャンセル、不良品理由以外での返品は不可となります。</li>
</ul>
</div>
<?php } ?>

	<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
</div>
