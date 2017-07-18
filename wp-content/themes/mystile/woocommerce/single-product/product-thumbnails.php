<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();

if ( $attachment_ids ) {
	$loop 		= 0;
	$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
	?>
	<div id="pdp-thumb-alt-wrapper" ><?php

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );

			if ( $loop == 0 || $loop % $columns == 0 )
				$classes[] = 'first';

			if ( ( $loop + 1 ) % $columns == 0 )
				$classes[] = 'last';

			$image_link = wp_get_attachment_url( $attachment_id );

			if ( ! $image_link )
				continue;

			$image       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$thumb_link = wp_get_attachment_url( $attachment_id);
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );
			$zoom_img_caption =  wp_get_attachment( $attachment_id );
			?>
            
            
             <?php if($zoom_img_caption['caption']=="zoom"){ ?>
            
            <a style="float:right; padding:5px;" href="<?php echo $image_link ?>" title="<?php echo $image_title ?>" rel="useZoom: 'pdp-zoom'" class="pdp-image-alt pdp-image-alt-hide ">
							<img width="75px" height="75px" src="<?php echo $zoom_img_caption['description']; ?>" rel="<?php echo $image_link ?>" alt="<?php echo $image_title ?>"/>
						</a>
                        <a style="float:right; padding:5px;" href="<?php echo $image_link ?>" title="<?php echo $image_title ?>" rel="useZoom: 'pdp-zoom'" class="pdp-image-alt pdp-image-alt-hide "></a>
                        
			<?php } ?>	
						
            <?php

			// echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" data-rel="prettyPhoto[product-gallery]">%s</a>', $image_link, $image_class, $image_title, $image ), $attachment_id, $post->ID, $image_class );
            
            

			$loop++;
		}

	?></div>
	<?php 
}
