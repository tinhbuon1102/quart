<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

?>
    
	<link href="http://l-quartet.com/zoom/pdp-style.css" rel="stylesheet" type="text/css"  media="all"  />    
	<!--<script src="http://l-quartet.com/zoom/jquery-1.9.0.min0696.js" type="text/javascript"></script>-->
	
	<script src="http://l-quartet.com/zoom/option_selection-73ded8016ade315cc3a3efa49f24bfb9.js" type="text/javascript"></script>
	
	
<div  id="pdp" class="dark"  style="margin:0px 0px 0px 300px;  width: 100%;">

	<?php
		if ( has_post_thumbnail() ) {

			$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
			$image       	= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title'	=> $image_title,
				'alt'	=> $image_title
				) );

			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}
			?>
             <div id="pdp-image-wrapper" style="background-image:url(<?php echo $image_link ?>);">
		            <a id="pdp-tooltip">CLICK TO ZOOM</a>
        
        
             <a href="<?php echo $image_link ?>" class ='cloud-zoom' id='pdp-zoom' rel="position: 'inside', adjustX: 0, adjustY:0, smoothMove:3, showTitle:false">
          <img src="http://l-quartet.com/zoom/img_spacer0696.png" class="image" id="pdp-placeholder-img" />
        </a>
            <?php

			// echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="cloud-zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_caption, $image ), $post->ID );

		} else {

			// echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

		}
	?>
   
</div>
	<?php do_action( 'woocommerce_product_thumbnails' ); ?>



	<div id="pdp-zoom-tooltip-area" title="Click to Zoom"></div>
	<!-- End Desktop Image Viewer -->
	<script type='text/javascript'>
	/* <![CDATA[ */	
		jQuery(document).ready(function() {	
			initPDPZoom();
		
		}); 
	/* ]]> */
	</script>



</div>


	<script src="http://l-quartet.com/zoom/main.min0696.js?10710391271777737739" type="text/javascript"></script>
