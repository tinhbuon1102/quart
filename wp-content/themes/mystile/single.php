<?php
$post = $wp_query -> post;
if (in_category('8')) {
	include (TEMPLATEPATH . '/single-collection.php');
}elseif ( in_category('6') ) {
include(TEMPLATEPATH.'/single-collection.php');
}else {
	include (TEMPLATEPATH . '/single-common.php');
}
?>