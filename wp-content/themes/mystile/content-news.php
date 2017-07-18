<div class="item">
<div class="text ja">
<?php if (has_post_thumbnail()): ?>
<div class="thum">
<a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail('full'); ?>
</a>
</div>
<?php else: ?>
<div class="thum">
<a href="<?php the_permalink(); ?>">
<img src="<?php echo get_template_directory_uri(); ?>/images/news-thum.jpg" />
</a>
</div>
<?php endif; ?>
<div class="ncon">
<p class="newsline">
<span class="date"><?php the_time( 'Y.m.d' ); ?>
</span>
<span class="subcat">
<?php
$cats = get_the_category();
foreach($cats as $cat):
if($cat->parent) echo $cat->cat_name;
endforeach;
?>
</span>
<p class="newstitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
</div>
  </div><!--end of text-->
  <div class="clear"></div>
</div><!--end of items-->
