<div class="item">
<?php //if (has_post_thumbnail()): ?>
<!--<div class="thum">
<a href="<?php //the_permalink(); ?>">
<?php //the_post_thumbnail('full'); ?>
</a>
</div>-->
<?php //else: ?>
<!--<div class="thum">
<a href="<?php //the_permalink(); ?>">
<img src="<?php //echo get_template_directory_uri(); ?>/images/news-thum.jpg" />
</a>
</div>-->
<?php //endif; ?>
<h3 class="retail-name"><?php the_title(); ?></h3>
<div class="stk-info"><?php the_content(); ?></div> 
  <div class="clear"></div>
</div><!--end of items-->
