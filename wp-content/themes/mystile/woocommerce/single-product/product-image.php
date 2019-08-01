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
$attachment_ids = $product->get_gallery_attachment_ids();

?>
<?php 
function wp_get_attachment( $attachment_id ) {

	$attachment = get_post( $attachment_id );
	return array(
		'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink( $attachment->ID ),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	);
}
?>
	<?php
	
		if ( has_post_thumbnail() ) {
			$thumb_id = get_post_thumbnail_id( get_the_ID() );
			$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
			$firstimage       = wp_get_attachment_url( $attachment_ids[0]);
			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $loop->post->ID ), 'full' );
			$Cap       = get_post_meta( $thumb_id );
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
<?php if($image_caption=="mobile") { ?>
    <link href="https://l-quartet.com/zoom/pdp-style.css" rel="stylesheet" type="text/css"  media="all"  />
 <!-- may be appended here or inside the 'head' section -->
    <script type="text/javascript" src="https://l-quartet.com/zoom/mediatypechecker.js"></script>
    <style>
	 @media (min-width: 768px) and (max-width: 1800px){
              
	   #pdp-mobile-slider-container{ display:none}
	   
	 }
	 
	  @media (min-width: 300px) and (max-width: 767px){
      	   #pdp-thumb-alt-wrapper{ display:none}
	   #pdp-mobile-slider-container{ display:block}
	   
	 }
	</style>
<!--OLD IMAGE CODE-->
<div id="pdp-image-wrapper" style="background-image:url(<?php echo $firstimage ?>); ">
				 <a id="pdp-tooltip">CLICK TO ZOOM</a>
				 <a  href="<?php echo $firstimage ?>" class ='cloud-zoom' id='pdp-zoom' rel="position: 'inside', adjustX: 0, adjustY:0, smoothMove:3, showTitle:false">
					 <img src="https://l-quartet.com/zoom/img_spacer0696.png" class="image" id="pdp-placeholder-img"  />
				 </a>
</div>
<?php do_action( 'woocommerce_product_thumbnails' ); ?>
<div id="pdp-zoom-tooltip-area" title="Click to Zoom"></div>
	<!-- End Desktop Image Viewer -->
<div style="clear:both"></div>
<!--/OLD IMAGE CODE-->
<?php } else { ?>
<div class="pro-image-wrap">
<div class="main-feature-image"><img src="<?php echo $featured_image[0] ?>" class="image-product" id="largeImage"  /></div>
<?php do_action( 'woocommerce_product_thumbnails' ); ?>
</div>
<?php } ?>
    <!-- Start Mobile Image Viewer -->
<div id="pdp-mobile-slider-container">
   
 <style>

.diy-slideshow{
  position: relative;
  display: block;
  overflow: hidden;
}
figure{
  position: absolute;
  opacity: 0;
  transition: 1s opacity;
  width:100%;
  margin:0;
}

figure.show{
  opacity: 1;
  position: static;
  transition: 1s opacity;
}
.next, .prev{
  color: #fff;
  position: absolute;
  background: rgba(0,0,0, .6);
  top: 50%;
  z-index: 1;
  font-size: 2em;
  margin-top: -.75em;
  opacity: .3;
  user-select: none;
}
.next:hover, .prev:hover{
  cursor: pointer;
  opacity: 1;
}
.next{
  right: 0;
  padding: 10px 5px 15px 10px;
  border-top-left-radius: 3px;
  border-bottom-left-radius: 3px;
}
.prev{
  left: 0;
  padding: 10px 10px 15px 5px;
  border-top-right-radius: 3px;
  border-bottom-right-radius: 3px;
}

</style>
<div class="diy-slideshow">
<!--<div style="text-align:right">フリックして下さい</div>-->
<figure class="show"><img src="<?php echo $image_link ?>" width="100%" /></figure>
<?php    

      $attachment_ids = $product->get_gallery_attachment_ids();
       if ( $attachment_ids ) {
		   
		   foreach ( $attachment_ids as $attachment_id ) {
			   $thumb_link = wp_get_attachment_url( $attachment_id);
                $img_info =  wp_get_attachment( $attachment_id );
}
			?>
    		
       <?php if($img_info['caption']=="mobile"){ ?>
	<figure ><img src="<?php echo $thumb_link ?>" width="100%" /></figure>	
	<?php } ?>
<?php
		if ( empty($img_info['caption']) && $attachment_ids && $product->get_image_id() ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$full_url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
					echo '<figure ><img src="'.$full_url.'" /></figure>'; // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
				}
			}
		?>
        
     <?php   
	 
	   }

	?>
  <span class="prev">&laquo;</span>
  <span class="next">&raquo;</span>
</div>  


	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.4/jquery.touchSwipe.js"></script>
    <script src="https://l-quartet.com/zoom/simple-slideshow-jquery.js"></script>
      

      
  
        
     
      
        
    	
	</div><!--/pdp-mobile-slider-container-->

	
	<!-- End Mobile Image Viewer -->
    
<?php if($image_caption=="mobile") { ?>
<script>
		$(document).ready(function(){
			initPDPZoom();
	  });
		
	</script>
<script src="https://l-quartet.com/zoom/main.min0696.js" type="text/javascript"></script>
<?php } ?>
<?php

		} else {

		}
	?>