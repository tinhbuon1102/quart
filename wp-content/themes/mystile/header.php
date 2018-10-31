<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
global $woo_options, $woocommerce;
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="<?php if ( $woo_options['woo_boxed_layout'] == 'true' ) echo 'boxed'; ?> <?php if (!class_exists('woocommerce')) echo 'woocommerce-deactivated'; ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php woo_title(''); ?></title>
<?php woo_meta(); ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); echo '?' . filemtime( get_stylesheet_directory() . '/style.css'); ?>" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/Fontsaddict.css" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700|Roboto:300,400|EB+Garamond' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/icons.css">
<?php
	wp_head();
	woo_head();
?>
<?php woo_top(); ?>
<script type="text/javascript">
	$ = jQuery.noConflict();
</script>
</head>

<body <?php body_class(); ?>>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg-symbol" style="position: absolute; width: 0; height: 0; overflow: hidden;">
	<symbol viewBox="0 0 510.04 116.41" id="svg-logo">
		<g class="nc-icon-wrapper">
         <g id="Layer_2" data-name="Layer 2">
          <g id="&#x30EC;&#x30A4;&#x30E4;&#x30FC;_1" data-name="&#x30EC;&#x30A4;&#x30E4;&#x30FC; 1">
           <path class="cls-1" fill="#231916" d="M17.45 19.61H0v86.51h48.34v-14.3H17.45V19.61z"/>
           <path class="cls-1" d="M111.94 82.85a149.53 149.53 0 0 0 1.21-20 166.12 166.12 0 0 0-.91-18.12 40.22 40.22 0 0 0-4-14.23 22.36 22.36 0 0 0-8.91-9.27Q93.51 17.9 83.7 17.9t-15.62 3.35a22.4 22.4 0 0 0-8.9 9.27 40.22 40.22 0 0 0-4 14.23 168.67 168.67 0 0 0-.91 18.12A168 168 0 0 0 55.18 81a40.06 40.06 0 0 0 4 14.17 21.28 21.28 0 0 0 8.9 9.09q5.82 3.15 15.63 3.15a44.14 44.14 0 0 0 11.51-1.33l10.3 10.29 10.3-9.33-9.33-9.04a34.54 34.54 0 0 0 5.45-15.15zm-16.66-5.14a41.42 41.42 0 0 1-1.69 9.93A11 11 0 0 1 90 93.21 10.13 10.13 0 0 1 83.71 95a10.17 10.17 0 0 1-6.24-1.76 11 11 0 0 1-3.63-5.57 41.36 41.36 0 0 1-1.7-9.93q-.42-6.12-.42-14.84t.42-14.78a42.3 42.3 0 0 1 1.7-9.94 11.1 11.1 0 0 1 3.63-5.63 10.24 10.24 0 0 1 6.24-1.76A10.19 10.19 0 0 1 90 32.52a11 11 0 0 1 3.64 5.63 42.35 42.35 0 0 1 1.69 9.94q.42 6.06.43 14.78t-.48 14.84z" fill="#231916"/>
           <path class="cls-1" d="M160.16 80a38.06 38.06 0 0 1-.43 6 13.45 13.45 0 0 1-1.57 4.73 8.64 8.64 0 0 1-3.09 3.15 11.71 11.71 0 0 1-10.12 0 8.5 8.5 0 0 1-3.15-3.15 13.66 13.66 0 0 1-1.57-4.73 38.12 38.12 0 0 1-.42-6V19.61h-17.45V80q0 8.72 2.3 14.17a20.09 20.09 0 0 0 6.18 8.42 20.93 20.93 0 0 0 8.84 3.94 54.2 54.2 0 0 0 10.3 1 48.37 48.37 0 0 0 10.3-1.09 21 21 0 0 0 8.84-4.24 21.67 21.67 0 0 0 6.18-8.54A35 35 0 0 0 177.6 80V19.61h-17.44z" fill="#231916"/>
           <path class="cls-1" d="M205.47 19.61l-22.9 86.51h18.17l4-18.3h23l4 18.3h18.17L227 19.61zm2.3 53.92l8.36-38.89h.24l8.36 38.89z" fill="#231916"/>
           <path class="cls-1" d="M316.56 33.91h18.9v18.5l-8.09-8.1L317 54.65l-6.46-6.45V19.61h-32.2q-10.9 0-17 5.52t-6.02 16.77q0 8.73 3.52 14.24t10.9 7.09v.24q-6.54 1-9.57 4.24t-3.75 10.54c-.17 1.62-.29 3.37-.37 5.27s-.16 4-.24 6.36q-.24 6.91-.73 10.54-.72 3.63-3.15 5v.73h18.9a9.38 9.38 0 0 0 1.7-4.18A47.49 47.49 0 0 0 273 97l.49-16.6a13.69 13.69 0 0 1 2.54-8c1.54-1.93 4.12-2.9 7.76-2.9h9.32v36.59h17.45V79.82l6.44-6.45 10.34 10.34 8.09-8.1v30.51h17.45V33.91h18.9v-14.3h-55.22zm-8.51 14.68l2.52 2.51 5 5-5 5-1.45 1.45L301.6 55zm-14.93 8.82h-7.27c-4.19 0-7.43-1-9.69-3s-3.39-5.32-3.39-10q0-12 12.11-12h8.24zm23.18 13.78l-.73.72-5 5-2.52 2.51L301.6 73l7.52-7.52.73-.73.72-.72 6.43-6.47.72-.73.73-.72 8.89-8.89 6.45 6.45-8.89 8.89-.73.72-.72.73-6.45 6.45zm11.07 9.61l-8.89-8.89 6.45-6.45 8.89 8.89zm8.09-7.71L326.38 64l9.08-9.08z" fill="#231916"/>
           <path class="cls-1" fill="#231916" d="M396.76 68.56h29.08v-14.3h-29.08V33.91h30.89v-14.3h-48.33v86.51h49.55v-14.3h-32.11V68.56z"/>
           <path class="cls-1" fill="#231916" d="M432.98 19.61v14.3h18.9v72.21h17.45V33.91h18.9v-14.3h-55.25z"/>
           <path class="cls-1" d="M500.07 0a9.81 9.81 0 1 1-10 9.75 9.74 9.74 0 0 1 10-9.75zm0 17.69a7.6 7.6 0 0 0 7.61-7.88 7.64 7.64 0 1 0-15.27 0 7.68 7.68 0 0 0 7.66 7.88zm-3.75-2.79V5a19.45 19.45 0 0 1 3.68-.34c1.77 0 4.34.33 4.34 2.9a2.49 2.49 0 0 1-2 2.3V10c1 .32 1.45 1.13 1.72 2.41a8.61 8.61 0 0 0 .75 2.47h-2.25a8.13 8.13 0 0 1-.75-2.47c-.38-1.55-1.24-1.55-3.27-1.55v4zm2.2-5.57c1.66 0 3.48 0 3.48-1.51 0-.91-.64-1.55-2.19-1.55a6 6 0 0 0-1.29.11z" fill="#231916"/>
          </g>
         </g>
        </g>
	</symbol>
</svg>
<div id="wrapper">
    <?php woo_header_before(); ?>

	<header id="header" class="col-full">

<div id="header-nav-wrapper-desktop">

	    <hgroup>
<h3 class="nav-toggle"><a href="#navigation"><mark class="websymbols">&#178;</mark> <span><?php _e('Navigation', 'woothemes'); ?></span></a></h3>
	    	
			    <a id="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr( get_bloginfo( 'description' ) ); ?>">
			    	<span class="svg-wrapper"><svg class="svg" width="320" height="73" viewBox="0 0 320 73"><use href="#svg-logo" xlink:href="#svg-logo"/></svg></span>
			    </a>
		

			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
			
<div id="top" class="mobile" style="float: right; width: 120px;">

			
<nav class="col-full" role="navigation" id="utility-nav-desktop">

		<style>.top-nav-mobile{display:block !important;} #mobileMenu_top-nav {display:none !important;}</style>
<?php if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl top-nav-mobile', 'theme_location' => 'top-menu' ) ); ?>
			<?php } ?>
			
			<?php 
			
				if ( class_exists( 'woocommerce' ) ) {
					echo '<ul class="nav wc-nav">					
					<li style="display:none"  class="cart"><a id= "festi-cart" class="cart-parent" title="View your shopping cart" href="">
		<span>'.__('CHECKOUT','woothemes').'
	<span class="amount">¥'.$woocommerce->cart->get_cart_total().'</span><span class="contents">'.$woocommerce->cart->cart_contents_count.'</span>	</span>
	</a>
	</li><li class="checkout">';
	?>
                    <a href="<?php echo $woocommerce->cart->get_checkout_url();?>"><?php echo __('CHECKOUT','woothemes').'</a></li>';
					
					echo '</ul>';
				}
			?>
              	
		</nav>
</div><!--/top-->
		</hgroup>

        <?php woo_nav_before(); ?>

		<nav id="navigation" class="col-full" role="navigation">

			<?php
			if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
				wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fr', 'theme_location' => 'primary-menu' ) );
			} else {
			?>
	        <ul id="main-nav" class="nav fl">
				<?php if ( is_page() ) $highlight = 'page_item'; else $highlight = 'page_item current_page_item'; ?>
				<li class="<?php echo $highlight; ?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
				<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
			</ul><!-- /#nav -->
	        <?php } ?>

		</nav><!-- /#navigation -->

		<?php woo_nav_after(); ?>
<div id="social-nav" class="pc">
<ul class="social-icons icon-flat list-unstyled list-inline"> 
	      <li> <a href="https://www.facebook.com/profile.php?id=100009309119528&fref=ts" target="_blank"><i class="fa fa-facebook"></i></a></li>  
	      <li> <a href="https://twitter.com/l_quartet" target="_blank"><i class="fa fa-twitter"></i></a></li>   
	      <li> <a href="https://instagram.com/L_QUARTET/" target="_blank"><i class="fa fa-instagram"></i></a></li> 
	  	</ul>
</div>
<div id="top" class="pc">
<nav class="col-full" role="navigation" id="utility-nav-desktop">
<style>.fl{float:right;}</style>
			<?php if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
			<?php } ?>
			
			<?php 
			
				if ( class_exists( 'woocommerce' ) ) {
                        ?>
					<ul class="nav wc-nav">	

<!--<li style="list-style: none;">
<a id="festi-cart" class="festi-cart         festi-cart-customize           festi-cart-menu           festi-cart-hover"  href=""></a>    
</li>-->
<li class="checkout">
	

                    <a href="<?php echo $woocommerce->cart->get_checkout_url();?>">

<?php echo __('CHECKOUT','woothemes').'</a></li>';


					
					echo '</ul>';
				}
			?>
            		
				<?php
if ( is_user_logged_in() ) {
	echo '<ul class="nav wc-nav"><li id="menu-item-25" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-25"><a href="http://l-quartet.com/my-account">MY ACCOUNT</a></li><ul>';
} else {
	echo '<ul class="nav wc-nav"><li id="menu-item-25" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-25"><a href="http://l-quartet.com/my-account">LOGIN</a></li></ul>';
}
?>
		</nav>
</div><!--/top-->
</div><!-- /#header navigation -->

	</header><!-- /#header -->

	<?php woo_content_before(); ?>