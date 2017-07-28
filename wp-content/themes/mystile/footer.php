<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Footer Template
 *
 * Here we setup all logic and XHTML that is required for the footer section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
	global $woo_options;
	
	echo '<div class="footer-wrap">';

	$total = 4;
	if ( isset( $woo_options['woo_footer_sidebars'] ) && ( $woo_options['woo_footer_sidebars'] != '' ) ) {
		$total = $woo_options['woo_footer_sidebars'];
	}

	if ( ( woo_active_sidebar( 'footer-1' ) ||
		   woo_active_sidebar( 'footer-2' ) ||
		   woo_active_sidebar( 'footer-3' ) ||
		   woo_active_sidebar( 'footer-4' ) ) && $total > 0 ) {

?>
	<?php woo_footer_before(); ?>
	
		<section id="footer-widgets" class="col-full col-<?php echo $total; ?> fix">
	
			<?php $i = 0; while ( $i < $total ) { $i++; ?>
				<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>
	
			<div class="block footer-widget-<?php echo $i; ?>">
	        	<?php woo_sidebar( 'footer-' . $i ); ?>
			</div>
	
		        <?php } ?>
			<?php } // End WHILE Loop ?>
	
		</section><!-- /#footer-widgets  -->
	<?php } // End IF Statement ?>
		<footer id="footer" class="col-full">
	
			<div class="col-left">
 <!-- Footer Navigation -->
  
  <!-- /Footer Navigation -->
			<?php if( isset( $woo_options['woo_footer_left'] ) && $woo_options['woo_footer_left'] == 'true' ) {
	
					echo stripslashes( $woo_options['woo_footer_left_text'] );
	
			} else { ?>
			<nav>
    <?php wp_nav_menu(array('theme_location' => 'footer-navi', 'container' => '', 'menu_class' => '', 'items_wrap' => '<ul id="fnav">%3$s</ul>')); ?>
  </nav>
			<?php } ?>
			</div>
	
			<div class="col-right">
	        <?php if( isset( $woo_options['woo_footer_right'] ) && $woo_options['woo_footer_right'] == 'true' ) {
	
	        	echo stripslashes( $woo_options['woo_footer_right_text'] );
	
			} else { ?>
				<ul id="utility">
           <li class="utility-social-icons">
<a href="https://instagram.com/L_QUARTET/" target="_blank"><i class="icon-instagram"></i></a>
<a href="https://www.facebook.com/profile.php?id=100009309119528&fref=ts" target="_blank"><i class="icon-facebook"></i></a>
<a href="https://twitter.com/l_quartet" target="_blank"><i class="icon-twitter"></i></a>
</li>
                                </ul>
			<?php } ?>
			</div>
	
		</footer><!-- /#footer  -->
	<div id="copyright"><p><?php bloginfo(); ?> &copy; <?php echo date( 'Y' ); ?>. <?php _e( 'All Rights Reserved.', 'woothemes' ); ?></p></div>
	</div><!-- / footer-wrap -->

</div><!-- /#wrapper -->
<?php wp_footer(); ?>
<script type="text/javascript">

</script>
<script>
jQuery(function($){
	$(".single_add_to_cart_button").click(function(event){
		if($('form.variations_form.cart select option:selected').val() == ""){
		  event.preventDefault();
		  var html = "<p class = 'required_text' >商品のオプションを選択してください</p>" ;
		  $('form.variations_form.cart select').addClass("required_option").after(html);
		}
	});
});
  </script>
  		<script src="<?php echo get_template_directory_uri(); ?>/js/grid/modernizr.custom.js"></script>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/masonry.pkgd.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/grid/imagesloaded.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/grid/classie.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/grid/AnimOnScroll.js"></script>
		<script>
			new AnimOnScroll( document.getElementById( 'ngrid' ), {
				minDuration : 0.4,
				maxDuration : 0.7,
				viewportFactor : 0.2
			} );
		</script>
<script>
jQuery(function($){	
	$('#news-grid').masonry({
    itemSelector: '.item',
    isAnimated: true,
    isFitWidth: true,
    isResizable: true
	});
		
});
</script>
<!--<script src="<?php echo get_template_directory_uri(); ?>/js/GammaGallery/js/jquery.history.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/GammaGallery/js/js-url.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/GammaGallery/js/jquerypp.custom.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/js/GammaGallery/js/gamma.js"></script>
		<script type="text/javascript">
			
			$(function() {

				var GammaSettings = {
						// order is important!
						viewport : [ {
							width : 1200,
							columns : 5
						}, {
							width : 900,
							columns : 4
						}, {
							width : 500,
							columns : 3
						}, { 
							width : 320,
							columns : 2
						}, { 
							width : 0,
							columns : 2
						} ]
				};

				Gamma.init( GammaSettings, fncallback );


				// Example how to add more items (just a dummy):

				var page = 0,
					items = ['<li><div data-alt="img03" data-description="<h3>Sky high</h3>" data-max-width="1800" data-max-height="1350"><div data-src="images/xxxlarge/3.jpg" data-min-width="1300"></div><div data-src="images/xxlarge/3.jpg" data-min-width="1000"></div><div data-src="images/xlarge/3.jpg" data-min-width="700"></div><div data-src="images/large/3.jpg" data-min-width="300"></div><div data-src="images/medium/3.jpg" data-min-width="200"></div><div data-src="images/small/3.jpg" data-min-width="140"></div><div data-src="images/xsmall/3.jpg"></div><noscript><img src="images/xsmall/3.jpg" alt="img03"/></noscript></div></li><li><div data-alt="img03" data-description="<h3>Sky high</h3>" data-max-width="1800" data-max-height="1350"><div data-src="images/xxxlarge/3.jpg" data-min-width="1300"></div><div data-src="images/xxlarge/3.jpg" data-min-width="1000"></div><div data-src="images/xlarge/3.jpg" data-min-width="700"></div><div data-src="images/large/3.jpg" data-min-width="300"></div><div data-src="images/medium/3.jpg" data-min-width="200"></div><div data-src="images/small/3.jpg" data-min-width="140"></div><div data-src="images/xsmall/3.jpg"></div><noscript><img src="images/xsmall/3.jpg" alt="img03"/></noscript></div></li><li><div data-alt="img03" data-description="<h3>Sky high</h3>" data-max-width="1800" data-max-height="1350"><div data-src="images/xxxlarge/3.jpg" data-min-width="1300"></div><div data-src="images/xxlarge/3.jpg" data-min-width="1000"></div><div data-src="images/xlarge/3.jpg" data-min-width="700"></div><div data-src="images/large/3.jpg" data-min-width="300"></div><div data-src="images/medium/3.jpg" data-min-width="200"></div><div data-src="images/small/3.jpg" data-min-width="140"></div><div data-src="images/xsmall/3.jpg"></div><noscript><img src="images/xsmall/3.jpg" alt="img03"/></noscript></div></li><li><div data-alt="img03" data-description="<h3>Sky high</h3>" data-max-width="1800" data-max-height="1350"><div data-src="images/xxxlarge/3.jpg" data-min-width="1300"></div><div data-src="images/xxlarge/3.jpg" data-min-width="1000"></div><div data-src="images/xlarge/3.jpg" data-min-width="700"></div><div data-src="images/large/3.jpg" data-min-width="300"></div><div data-src="images/medium/3.jpg" data-min-width="200"></div><div data-src="images/small/3.jpg" data-min-width="140"></div><div data-src="images/xsmall/3.jpg"></div><noscript><img src="images/xsmall/3.jpg" alt="img03"/></noscript></div></li><li><div data-alt="img03" data-description="<h3>Sky high</h3>" data-max-width="1800" data-max-height="1350"><div data-src="images/xxxlarge/3.jpg" data-min-width="1300"></div><div data-src="images/xxlarge/3.jpg" data-min-width="1000"></div><div data-src="images/xlarge/3.jpg" data-min-width="700"></div><div data-src="images/large/3.jpg" data-min-width="300"></div><div data-src="images/medium/3.jpg" data-min-width="200"></div><div data-src="images/small/3.jpg" data-min-width="140"></div><div data-src="images/xsmall/3.jpg"></div><noscript><img src="images/xsmall/3.jpg" alt="img03"/></noscript></div></li>']

				function fncallback() {

					$( '#loadmore' ).show().on( 'click', function() {

						++page;
						var newitems = items[page-1]
						if( page <= 1 ) {
							
							Gamma.add( $( newitems ) );

						}
						if( page === 1 ) {

							$( this ).remove();

						}

					} );

				}

			});

		</script>-->
<script type="text/javascript">
jQuery(function(){
	jQuery("a img,#main-nav li a").hover(function(){
		jQuery(this).stop().animate({"opacity":"1"});
	},function(){
		jQuery(this).stop().animate({"opacity":"1"});
	});
});
</script>
<style>
#order_review > table > div.blockUI.blockOverlay ,#payment > div.blockUI.blockOverlay{
	position:inherit!important
}
</style
<?php woo_foot(); ?>
  

</body>
</html>
