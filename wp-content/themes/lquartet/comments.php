<!-- comment area -->
<div id="comment-area">
	<?php 
	if(have_comments()): // コメントがあったら
	?>

		<h3 id="comments">Comment</h2>
	
		<ol class="commets-list">
		<?php wp_list_comments('avatar_size=55'); //コメント一覧を表示 ?>
		</ol>
		
		<div class="comment-page-link">
				<?php paginate_comments_links(); //コメントが多い場合、ページャーを表示 ?>
		</div>
	<?php
	endif;
	
	// ここからコメントフォーム
	$args = array(
		'title_reply' => 'Leave a Reply',
		'label_submit' => 'Submit Comment'
	);
	comment_form($args);
	?>
</div>
<!-- /comment area -->