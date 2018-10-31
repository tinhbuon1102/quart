<?php 
//get_header();
global $post, $woo_options, $woocommerce;
//global $post;
//pr($post);

	$gallery=get_post_meta($post->ID,'gallery',true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php if ( $woo_options['woo_boxed_layout'] == 'true' ) echo 'boxed'; ?> <?php if (!class_exists('woocommerce')) echo 'woocommerce-deactivated'; ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php woo_title(''); ?></title>
<?php woo_meta(); ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />  
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
<?php
	//wp_head();
?>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/icons.css">
<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700|Roboto:300,400|EB+Garamond' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/lookbook.css">
</head>
<body id="looksingle" data-tmpdir="<?php echo esc_url(get_template_directory_uri()); ?>/" <?php body_class(); ?>>
<div class="black_container">
	<div class="black_left">
		<div class="slider-nav">
			<?php 					
				$id=1;
				foreach($gallery as $gall){
					$img=wp_get_attachment_image_src($gall,'thumbnail');
			?>
				<div><img class="navbarImage" src="<?php echo $img[0];?>" data-count="<?php echo $id; ?>" alt="thumnail_images"  ></div>
			 <?php 
				$id++;
				} ?>
		</div>		
	</div>
	
	
	
	
	<div class="black_center">
		<div class="look--center-block--wrapper">
			<div class="look_image_center_wrapper"><div class="slider-for">
			<?php 
					$i=1;
					foreach($gallery as $gall){
						$img=wp_get_attachment_url($gall);
				?>
					<div class="full_image look_image_center_container"><img class="look--center--image" src="<?php echo $img;?>" alt="thumnail_images" style="" ></div>
				 <?php } ?>
				</div></div>
			<div class="look--center--details-container">
				<div class="look--center--details-content">
					<div class="slide_title">
						<h1>
							<span class="header--subtitle"><?php the_title_attribute(); ?></span>
							<span class="header--title">Collection</span>
						</h1>
					</div>
					<div class="slide-counter display_small">
						<span class="slide-counter--index">Look 1</span>
						<span class="slide-counter--total">/<?php echo count($gallery);?></span>
					</div>
				</div>
				<div class="arrows-controls">
			<a class="up">
				<button class="arrows-controls--button icon-gallery_arrow_up_1">
					<img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/download-arrow.png" alt="social_icons">
				</button>
			</a>
			<a class="down">
				<button class="arrows-controls--button icon-gallery_arrow_down">
					<img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/up-arrow.png" alt="social_icons">
				</button>
			</a>
		</div>
			</div><!--/.container-->
		</div>
		<div class="lookbook--nav display_desktop"><a href="<?php echo esc_url( home_url( '/lookbook/' ) ); ?>" class="lookbook--close icon-X"><i class="icon icon-simple-remove"></i></a></div>
	</div>
	
	
	<!--<div class="black_sub-content">
		<div class="look--close"></div>
	</div>-->
	<div class="slide-counter display_xsmall">
						<span class="slide-counter--index">Look 1</span>
						<span class="slide-counter--total">/<?php echo count($gallery);?></span>
					</div>
	<div class="lookbook--nav display_mobile"><a href="<?php echo esc_url( home_url( '/lookbook/' ) ); ?>" class="lookbook--close icon-X"><i class="icon icon-simple-remove"></i></a></div>
	



</div>


<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

<?php //wp_footer(); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/lookbook.js"></script>

	</body></html>
