<?php 
//get_header();
global $post;
//pr($post);

	$gallery=get_post_meta($post->ID,'gallery',true);
?>

<style>

.black_container{background:#000;width: 100%;float: left; color:#fff !important;}
.black_left {
    width: 20%;
	float:left;
}
.black_center {
    width: 35%;
	float:left;
	text-align: center;
}

.black_right {
    width: 25%;
	float:left;
	TEXT-ALIGN: CENTER;
}

.desc_title{width:20%; float:left;}


.thumnail_images ul {
    list-style: none;
}

.full_image {
    margin-top: 10px;
}

.mt30{margin-top:30px;}
.gallery-counter {
    position: static;
    border-top: 1px solid #666;
    border-bottom: 1px solid #666;
    padding: 26px 0;
    font-size: 26px;
	top: 12px;
    right: 15px;
    z-index: 2;
}

.gallery-counter--index {
    color: #fff;
}

.gallery-counter--total {
    color: #8e8e8e;
}

.arrows-controls {
    position: absolute;
    bottom: 90px;
}

.arrows-controls--button {
    display: inline-block;
    transition: background-color .2s ease-in-out 0s,border .2s ease-in-out 0s;
    border: 1px solid #8e8e8e;
    background: rgba(0,0,0,.5);
    cursor: pointer;
    width: 60px;
    height: 60px;
    text-decoration: none;
    line-height: 20px;
    color: #fff;
    font-size: 28px;
}
.arrows-controls--button:last-child {
    margin-left: 10px;
}
.social_icons_media ul {
    list-style: none;
}
.social_icons_media ul li {
    display: inline-block;
    padding-left: 13px;
}



@media only screen and (max-width: 449px){
	
	.black_center {
    width: 100%;
	float:left;
	text-align: center;
	overflow: hidden;
}
.black_left {
   
    display: none;
	
	
}

.desc_title {
    width: 100%;
    float: left;
    text-align: center;
}
.black_container{overflow: hidden;}
.pro_ad{display:none;}
.head_subname {
    font-size: 40px;
}

.gallery-counter {
    position: absolute;
    top: 10px;
    float: left;
    
    background: #000;
    width: 98.5%;
    font-size: 43px;
    
    padding: 24px 0px;
}
.full_image {
    margin-top: 10px;
    width: 100%;
    float: left;
}
.full_image img{
   
    width: 100%;
    float: left;
}
.head_subname {
    font-size: 40px;
}

.arrows-controls {
    
    display: none;
}

.black_right {
width: 100%;}

}

</style>


<div class="black_container">
	<div class="black_left">
		<div class="thumnail_images" id="thumbnail-slider">
		  <div class="inner">
			<?php if(count($gallery)>0){?>
			  <ul>
				<?php 
				$i=1;
				foreach($gallery as $gall){
						$img=wp_get_attachment_url($gall);
				?>
				<li class="internal-image"><img src="<?php echo $img;?>" alt="thumnail_images" style="width:100px;" data-value="<?php echo $i;?>"></li>
			
			<?php 
				$i++;
			} ?>
			  </ul>
			 <?php }
			?>
			</div>
		</div>
	
	</div>
	<div class="black_center">
		<div class="full_image">
			<img src="<?php echo wp_get_attachment_url($gallery[0]);?>" alt="img" style="width:85%;">
		</div>
	
	</div>
		<div class="desc_title mt30">
			<div class="head_subname">
				<span>Spring 2019 Ready-to-Wear</span>
				<h1> <?php echo $post->post_title;?> </h1>
			</div>
			<div class="gallery-counter">
				<span class="pro_desc-counter--index">Look 1</span>
				<span class="gallery-counter--total">/<?php echo count($gallery);?></span>
			</div>
			
			<div class="arrows-controls">
				<a class="up"><button class="arrows-controls--button icon-gallery_arrow_up"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/download-arrow.png" alt="social_icons"></button></a>
				<a class="down"><button class="arrows-controls--button icon-gallery_arrow_down"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/up-arrow.png" alt="social_icons"></button></a>
			</div>
			
		</div>
	
	<div class="black_right">
	
		<div class="social_icons_media mt30">
			<ul>
				<li><a href="https://instagram.com/L_QUARTET/" target="_blank"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/facebook-logo.png" alt="social_icons"></a></li>
				<li><a href="https://www.facebook.com/profile.php?id=100009309119528&amp;fref=ts" target="_blank"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/instagram-logo.png" alt="social_icons"></a></li>
				<li><a href="https://twitter.com/l_quartet" target="_blank"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/twitter-logo.png" alt="social_icons"></a></li>
			
			
			</ul>
			
		<div class="pro_ad">
				
				<img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/ad.PNG" alt="advertisment">
			</div>
		
		</div>
	
	
	
	</div>



</div>
<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="<?php echo site_url()?>/ninja-slider.css" rel="stylesheet" />
<script src="<?php echo site_url()?>/ninja-slider.js"></script>
<link href="<?php echo site_url()?>/thumbnail-slider.css" rel="stylesheet" type="text/css" />
<script src="<?php echo site_url()?>/thumbnail-slider.js"></script>

<script type='text/javascript'>

	jQuery(document).ready(function() {	
		
		jQuery('.internal-image').click(function () {
				
		   var get =  jQuery(this).attr('data-value');
		   var src= jQuery(this).children('img').attr('src');
		   
		   //var src=jQuery(this).attr('rel');
		   //var title =  jQuery(this).attr('data-title');
		   
		   jQuery('.pro_desc-counter--index').text('Look '+get);
		   
		   jQuery(".full_image > img").attr('src',src);
		   
		   //jQuery('.title-product-image').text(title);
		   
		   //jQuery("#pdp-image-wrapper > #pdp-zoom").attr('href',src);
		   //jQuery("#pdp-image-wrapper > #pdp-zoom > img").attr('src',src);

		});
		
		setInterval(function(){ 
			
			var get=jQuery("#thumbnail-slider > .inner > ul > .active > img").attr('data-value');
			
			var src=jQuery("#thumbnail-slider > .inner > ul > .active > img").attr('src');
			
			if(get>0){
				
				//var title =  jQuery("#thumbnail-slider > .inner > ul > .active  > img").attr('data-title');
				
			    jQuery('.pro_desc-counter--index').text('Look '+get);
				
				jQuery(".full_image > img").attr('src',src);
			   
			    //jQuery('.title-product-image').text(title);
				
			  // jQuery("#pdp-image-wrapper > #pdp-zoom").attr('href',src);
			  // jQuery("#pdp-image-wrapper > #pdp-zoom > img").attr('src',src);
			}
			
		}, 1000);


		jQuery( document ).ready(function() {
			jQuery('.up').on('click',function(){
				 jQuery("#thumbnail-slider-prev").click();  

			});
			jQuery('.down').on('click',function(){
				 jQuery("#thumbnail-slider-next").click();  

			});
		
	});

});
</script>

	
<?php 
//get_footer();
?>