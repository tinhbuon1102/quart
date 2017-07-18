<div class="list-item">
<!--<div class="post-img">
<a href="<?php //the_permalink(); ?>">
<?php //if (has_post_thumbnail()): ?><?php //the_post_thumbnail('thumbnail'); ?>
</a>
</div>-->
<h3 class="retail-name"><?php the_title(); ?></h3>
<div class="stk-info"><?php the_content(); ?></div>  
</div>
<?php else: ?><?php endif; ?>

