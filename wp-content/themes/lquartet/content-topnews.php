<li>
<a href="<?php the_permalink(); ?>">
<?php if (has_post_thumbnail()): ?><?php the_post_thumbnail('thumbnail'); ?>
</a>
<?php else: ?><?php endif; ?>
<p class="newstitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
<p class="newsdate"><?php echo get_the_date(); ?></p>
</li>
