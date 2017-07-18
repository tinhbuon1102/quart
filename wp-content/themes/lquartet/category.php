<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>
<div id="contents">
<h2><?php single_cat_title(); ?></h2>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
<?php get_template_part('content'); ?><?php endwhile; endif; ?>
<?php wp_pagenavi(); ?>
</div>
<?php get_footer(); ?>​