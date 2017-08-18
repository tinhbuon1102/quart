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
<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet">
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
<div id="wrapper">
    <?php woo_header_before(); ?>

	<header id="header" class="col-full">

<div id="header-nav-wrapper-desktop">

	    <hgroup>
<h3 class="nav-toggle"><a href="#navigation"><mark class="websymbols">&#178;</mark> <span><?php _e('Navigation', 'woothemes'); ?></span></a></h3>
	    	 <?php
			    $logo = esc_url( get_template_directory_uri() . '/images/logo.png' );
				if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] != '' ) { $logo = $woo_options['woo_logo']; }
				if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] != '' && is_ssl() ) { $logo = preg_replace("/^http:/", "https:", $woo_options['woo_logo']); }
			?>
			<?php if ( ! isset( $woo_options['woo_texttitle'] ) || $woo_options['woo_texttitle'] != 'true' ) { ?>
			    <a id="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr( get_bloginfo( 'description' ) ); ?>">
			    	<img src="<?php echo $logo; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
			    </a>
		    <?php } ?>

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