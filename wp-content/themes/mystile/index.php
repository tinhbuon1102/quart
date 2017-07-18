<?php
// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?><?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;
	
?>

    <?php if ( $woo_options[ 'woo_homepage_banner' ] == "true" ) { ?>
    	
    	<div class="homepage-banner">
    		<?php
				if ( $woo_options[ 'woo_homepage_banner' ] == "true" ) { $banner = $woo_options['woo_homepage_banner_path']; }
				if ( $woo_options[ 'woo_homepage_banner' ] == "true" && is_ssl() ) { $banner = preg_replace("/^http:/", "https:", $woo_options['woo_homepage_banner_path']); }
			?>
			    <img src="<?php echo $banner; ?>" alt="" />
    		<h1><span><?php echo $woo_options['woo_homepage_banner_headline']; ?></span></h1>
    		<div class="description"><?php echo wpautop($woo_options['woo_homepage_banner_standfirst']); ?></div>
    	</div>
    	
    <?php } ?>
    
    <div id="content" class="col-full <?php if ( $woo_options[ 'woo_homepage_banner' ] == "true" ) echo 'with-banner'; ?> <?php if ( $woo_options[ 'woo_homepage_sidebar' ] == "false" ) echo 'no-sidebar'; ?>">
    <div id="homepage-slider-container" class="pc">
<?php// wowslider(10); ?>
<?php 
    echo do_shortcode("[metaslider id=1733]"); 
?>
</div><!--/slider container-->
<div id="homepage-slider-container" class="mb">
<?php 
    echo do_shortcode("[metaslider id=182]"); 
?>
</div><!--/slider container-->
<?php if(!wp_is_mobile()): ?>
<div class="sp-none"><?php 
    echo do_shortcode("[instagram-feed id=1439034892]"); 
?></div>
<?php endif; ?>
<div class="pc-none" style="display:none!important;">
<section id="recent">

    <h1>NEW ARRIVAL</h1>

    <ul class="products">

        <?php
            $args = array( 'post_type' => 'product', 'stock' => 1, 'posts_per_page' => 4, 'orderby' =>'date','order' => 'DESC' );
            $loop = new WP_Query( $args );
            while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

                    <li class="span3">    

                        <a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

                            <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="65px" height="115px" />'; ?>

                            <h3><?php the_title(); ?></h3>

                        	   <span class="price"><?php echo $product->get_price_html(); ?></span>

                        </a>

                       
                    </li><!-- /span3 -->
        <?php endwhile; ?>
        <?php wp_reset_query(); ?>

    </ul><!-- /row-fluid -->
</section><!-- /recent -->
</div>
    	<?php woo_main_before(); ?>
    
		<section id="main" class="col-left">  
		
		<?php mystile_homepage_content(); ?>		
		
		<?php woo_loop_before(); ?>
		
		<?php if ( $woo_options[ 'woo_homepage_blog' ] == "true" ) { 
			$postsperpage = $woo_options['woo_homepage_blog_perpage'];
		?>
		
		<?php
			
			$the_query = new WP_Query( array( 'posts_per_page' => $postsperpage ) );
			
        	if ( have_posts() ) : $count = 0;
        ?>
        
			<?php /* Start the Loop */ ?>
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); $count++; ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to overload this in a child theme then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php 
				endwhile; 
				// Reset Post Data
				wp_reset_postdata();
			?>
			
			

		<?php else : ?>
        
            <article <?php post_class(); ?>>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </article><!-- /.post -->
        
        <?php endif; ?>
        
        <?php } // End query to see if the blog should be displayed ?>
        
        <?php woo_loop_after(); ?>
		                
		</section><!-- /#main -->
		
		<?php woo_main_after(); ?>

        <?php if ( $woo_options[ 'woo_homepage_sidebar' ] == "true" ) get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>