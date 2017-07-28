<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Page Template
 *
 * This template is the default page template. It is used to display content when someone is viewing a
 * singular view of a page ('page' post_type) unless another page template overrules this one.
 * @link http://codex.wordpress.org/Pages
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;
?>
   		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/component.css" />
    <style>
	.grid li
	{
		padding:0.5%;
		width:32%;
	}
	.grid li .item
	{
		width:100% !important;
	}
	@media screen and (max-width: 900px){
		.grid li
	{
		padding:0.5%;
		width:49%;
	}
		
	}
	@media screen and (max-width: 400px){
		.grid li
	{
		padding:0.5%;
		width:100%;
	}
		
	}
	</style>
       
    <div id="content" class="page col-full">
    
    	<?php woo_main_before(); ?>
    	
		<section id="main" class="col-full page"> 	
<article <?php post_class(); ?>>
				
				<header>
			    	<hgroup><h1><?php single_cat_title(); ?></h1></hgroup>
				</header>	
<section class="entry" >	
<ul class=" grid effect-2" id="ngrid">

        <?php
        	if ( have_posts() ) { $count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>                                                           
            
				
                <li>
                	<?php get_template_part('content-news'); ?>
</li>
					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	

				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            
            
            <?php
            	// Determine wether or not to display comments here, based on "Theme Options".
            	if ( isset( $woo_options['woo_comments'] ) && in_array( $woo_options['woo_comments'], array( 'page', 'both' ) ) ) {
            		//comments_template();
            	}

				} // End WHILE Loop
			} else {
		?>
			<article <?php post_class(); ?>>
            	<div class="coming"><p>COMING SOON</p></div>
            </article><!-- /.post -->
        <?php } // End IF Statement ?>  
<<<<<<< HEAD
		</ul>
</section><!-- /.entry -->
        </article><!-- /.post -->
		</section><!-- /#main -->
		
=======
</section><!-- /.entry -->
        </article><!-- /.post -->
		</section><!-- /#main -->
		</ul>
>>>>>>> 73ef1e63214652ad92c8fccddbcc2be88e7ec156
		<?php woo_main_after(); ?>

    

    </div><!-- /#content -->
		
<?php get_footer(); ?>
