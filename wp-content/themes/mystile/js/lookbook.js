jQuery(document).ready(function($) {	
	    //var stylesheet_directory_uri = "<?php echo get_stylesheet_directory_uri(); ?>";
	    var whtLogo = $('body#looksingle').attr('data-tmpdir') + 'images/logo-wht.png';
	    var whtLogoImg = '<img src="' + whtLogo + '" class="logo--image">';
	    //var wp_temp_uri = tmp_path.temp_uri;
	    //var wp_home_url = tmp_path.home_url;
	    //var homeLink = '<a class="logo_link" href="'+wp_home_url+'"></a>';
	var homeLink = '<a class="logo_link" href="http://lquartet.xsrv.jp"></a>';
		$('.black_container').prepend('<div class="logo"></div>');
	    $(homeLink).prependTo('div.logo');
	    $(whtLogoImg).prependTo('.logo_link');
		$('.slider-for').slick({
			initialSlide:0,
			infinite: true,
			touchThreshold:6,
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			fade: true,  	
			dots:false,
			asNavFor: '.slider-nav'
		});
		
		$('.slider-nav').slick({
			infinite: true,  
			centerMode: false,  
			prevArrow:'<div class="nav-controllers allow_up"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/up-arrow.png" alt="arrow_up"></div>',
			nextArrow:'<div class="nav-controllers allow_down"><img src="http://lquartet.xsrv.jp/wp-content/uploads/2018/download-arrow.png" alt="arrow_down"></div>',	
			vertical:true,  
			asNavFor: '.slider-for',
			dots: false,	
			verticalSwiping:true,
			slidesToShow: 4,
			slidesToScroll: 1,
			focusOnSelect: true,
		});
		
		$('.slider-nav').on('afterChange', function(event, slick, currentSlide){	
			counter=currentSlide+1;
			$('.slide-counter--index').text('Look '+ counter);
		});

		$('a.down').on('click',function(e){
			e.preventDefault();
			$('.slider-nav').slick('slickPrev');
		});
		$('a.up').on('click',function(e){
			e.preventDefault();
			$('.slider-nav').slick('slickNext');
			
		})
		
		var scale=function(){
			var ele=$('.slider-nav').find('.slick-list');
			$(ele).addClass('h-90');
		}
		
		scale();
	
	var ImgW = $('.slick-active > .look--center--image').width();
	$('.look_image_center_wrapper').css('width', ImgW + 'px');
	//$('.full_image.look_image_center_container').css('width', ImgW + 'px');
	$(window).on('load resize', function() {
		var ImgW = $('.slick-active > .look--center--image').width();
		if ($(window).width() < 992) {
			$('.look_image_center_wrapper').css('width', $(window).width() + 'px');
			$('.full_image.look_image_center_container').css('width', $(window).width() + 'px');
			$('div.black_left').hide();
		} else {
			$('.look_image_center_wrapper').css('width', ImgW + 'px');
			$('.full_image.look_image_center_container').css('width', ImgW + 'px');
			$('div.black_left').show();
		}
	});
	
	
	
});