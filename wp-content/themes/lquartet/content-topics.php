<div class="post">
<div class="post-img">
<a href="<?php the_permalink(); ?>">
<?php if (has_post_thumbnail()): ?><?php the_post_thumbnail('thumbnail'); ?>
</a>
</div>
  <div class="disc">
    <p class="date"><?php echo get_the_date(); ?></p></div>
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
  <div class="post-cat"><?php the_content(); ?></div>  
</div>
<?php else: ?><?php endif; ?>

