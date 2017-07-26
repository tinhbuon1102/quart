<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post, $product;

?>
<?php if ( $product->is_on_sale() ) : ?>

	<?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . __( 'Sale!', 'woocommerce' ) . '</span>', $post, $product ); ?>

<?php endif; ?>

<?php $pbl_date = get_the_date( 'Y-m-d' );
		 if(strtotime(date('Y-m-d')) < strtotime(date( "Y-m-d", strtotime( $pbl_date." +15 day" ) )))
			     {
					 ?>
					 <span class="newlabel">New</span>
					 <?php
				 }				 
 ?>
 <?php if(get_field( "show_stock_label" )==1){ ?>
					 <span class="restocklabel">Restock</span>
 <?php } ?>
<h1 itemprop="name" class="product_title entry-title "><?php the_title(); ?></h1>
