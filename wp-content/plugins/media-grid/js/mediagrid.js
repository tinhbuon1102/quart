(function($) {
	$mg_sel_grid 		= false; // set displayed item's grid id
	mg_mobile_mode 		= false; // mobile mode flag
	var lb_is_shown 	= false; // lightbox shown flag
	var lb_switch_dir 	= false; // which sense lightbox is switching (prev/next)
	var video_h_ratio 	= 0.562; // video aspect ratio
	
	var grids_width		= jQuery.makeArray(); // array used to register grid size changes
	var mg_grid_pag 	= jQuery.makeArray();
	mg_grid_filter 		= jQuery.makeArray();
	mg_filters_defer	= jQuery.makeArray(); // setTimeout objects to defer filters
	mg_slider_autoplay 	= jQuery.makeArray();
	var items_cache		= jQuery.makeArray(); // avoid fetching again same item
	
	var mg_deeplinked	= false; // flag to know whether to use history.replaceState
	var mg_hashless_url	= false; // page URL without eventual hashes
	var mg_url_hash		= ''; // URL hashtag
	
	// body/html style vars
	var mg_html_style = ''; 
	var mg_body_style = '';

	// CSS3 loader code
	mg_loader =
	'<div class="mg_loader">'+
		'<div class="mgl_1"></div><div class="mgl_2"></div><div class="mgl_3"></div><div class="mgl_4"></div>'+
	'</div>';

	// event for touch devices that are not webkit
	var mg_generic_touch_event = (!("ontouchstart" in document.documentElement) || navigator.userAgent.match(/(iPad|iPhone|iPod)/g)) ? '' : ' touchstart';
	

	// first init
	jQuery(document).ready(function() {
		
		// items pagination - initial setup
		jQuery('.mg_container').each(function() {
			var gid = this.id.substr(8);

			mg_grid_pag[ this.id ] = (jQuery('#mgp_'+gid).length) ? jQuery('#mgp_'+gid).attr('init-pag') : 1;
			mg_pag_class(this.id);
		});
		
		// image switch and append lightbox code
		mg_item_img_switch(true);
		mg_append_lightbox();
		
		jQuery('.mg_container').each(function() {
			mg_size_boxes(this.id, false);
		});
		
		mg_apply_deeplinks(true);
	});

	
	// Grid handling for AJAX pages
	mg_async_init = function(grid_id, pag) {
		var mg_cont_id = 'mg_grid_'+ grid_id;
		
		mg_grid_pag[ mg_cont_id ] = (typeof(pag) == 'undefined') ? 1 : pag;
		mg_pag_class(mg_cont_id);
		
		mg_item_img_switch(true);
		mg_size_boxes(mg_cont_id, false);

		mg_display_grid(mg_cont_id);

		if(!jQuery('#mg_lb_wrap').length) {
			mg_append_lightbox();
		}
	};


	// append the lightbox code to the website
	mg_append_lightbox = function() {
		if(typeof(mg_lightbox_mode) != 'undefined') {

			// deeplinked lightbox - stop here
			if(jQuery('#mg_deeplinked_lb').length) {
				$mg_lb_contents = jQuery('#mg_lb_contents');
				lb_is_shown = true;
				return true;
			}


			/// remove existing one
			if(jQuery('#mg_lb_wrap').length) {
				jQuery('#mg_lb_wrap, #mg_lb_background').remove();
			}

			// touchswipe class
			var ts_class = (mg_lb_touchswipe) ? 'class="mg_touchswipe"' : '';

			jQuery('body').append(''+
			'<div id="mg_lb_wrap" '+ts_class+'>'+
				'<div id="mg_lb_loader">'+ mg_loader + '</div>' +
				'<div id="mg_lb_contents" class="mg_lb_pre_show_next"></div>'+
				'<div id="mg_lb_scroll_helper" class="'+ mg_lightbox_mode +'"></div>'+
			'</div>'+
			'<div id="mg_lb_background" class="'+ mg_lightbox_mode +'"></div>');

			$mg_lb_contents = jQuery('#mg_lb_contents');
		}
	};


	// image manager for mobile mode
	mg_item_img_switch = function(first_init) {
		var safe_mg_mobile = (typeof(mg_mobile) == 'undefined') ? 800 : mg_mobile;

		// mg_mobile_mode
		if(jQuery(window).width() < safe_mg_mobile && (!mg_mobile_mode || typeof(first_init) != 'undefined')) {
			jQuery('.mg_box:not(.mg_pag_hide) .thumb').each(function() {
                jQuery(this).attr('src', jQuery(this).attr('mobileurl'));
            });

			mg_mobile_mode = true;
			jQuery('.mg_grid_wrap').addClass('mg_mobile_mode');
			jQuery('.mg_grid_wrap').trigger('mg_mobile_mode_switch');
			return true;
		}

		// standard
		if(jQuery(window).width() >= safe_mg_mobile && (mg_mobile_mode || typeof(first_init) != 'undefined')) {
			jQuery('.mg_box:not(.mg_pag_hide) .thumb').each(function() {
                jQuery(this).attr('src', jQuery(this).attr('fullurl'));
            });

			mg_mobile_mode = false;
			jQuery('.mg_grid_wrap').removeClass('mg_mobile_mode');
			jQuery('.mg_grid_wrap').trigger('mg_mobile_mode_switch');
			return true;
		}
	};
	
	
	// mobile mode on window resize
	jQuery(window).resize(function() {
		if(typeof(mg_is_resizing) != 'undefined') {clearTimeout(mg_is_resizing);}

		var mg_is_resizing = setTimeout(function() {
			mg_item_img_switch();
		}, 50);
	});
	

	// get cell width
	mg_get_w_size = function(box_id, mg_wrap_w) {
		var size = (mg_mobile_mode) ?  jQuery(box_id).attr('mgi_mw') : jQuery(box_id).attr('mgi_w');
		var wsize = Math.round(mg_wrap_w * parseFloat(size.replace(',', '.')) );

		// max width control
		var cols = Math.round( 1 / size );
		if( (wsize * cols) > mg_wrap_w ) {
			wsize = wsize - 1;
		}

		return wsize;
	};


	// get cell height
	mg_get_h_size = function(box_id, mg_wrap_w, mg_box_w) {
		var hsize = 0;
		var size = (mg_mobile_mode) ? jQuery(box_id).attr('mgi_mh') : jQuery(box_id).attr('mgi_h');

		// standard fractions
		if(size != 'auto') {
			hsize = Math.round(mg_wrap_w * parseFloat(size.replace(',', '.')) );

			// max width control - to follow width algorithm
			var cols = Math.round( 1 / size );
			if( (hsize * cols) > mg_wrap_w) {
				hsize = hsize - 1;
			}
		}

		// "auto" height calculation
		else {
			var add_space = (mg_boxMargin * 2) + (mg_boxBorder * 2) + (mg_imgPadding * 2);

			// if inline text - set to auto
			if(jQuery(box_id).hasClass('mg_inl_text')) {
				hsize = 'auto';
			}

			// image aspect ratio
			else {
				var ratio = parseFloat( jQuery(box_id).attr('ratio') );
				var img_w = mg_box_w - add_space;

				hsize = Math.round(img_w * ratio) + add_space;
			}
		}

		return hsize;
	};


	// size boxes
	mg_size_boxes = function(cont_id, is_resizing) {
		if( jQuery('#'+cont_id).attr('rel') == 'auto' || mg_mobile_mode) {
			var mg_wrap_w = jQuery('#'+cont_id).width();
			var use_rel_width = false;
		} else {
			var mg_wrap_w = parseInt(jQuery('#'+cont_id).attr('rel'));
			var use_rel_width = true;
		}

		var tot_elem = jQuery('#'+cont_id+' .mg_box').not('.mg_pag_hide').length;
		jQuery('#'+cont_id+' .mg_box').not('.mg_pag_hide').each(function(i) {
			var mg_box_id = '#' + jQuery(this).attr('id');

			// size boxes
			var mg_box_w = mg_get_w_size(mg_box_id, mg_wrap_w);
			var mg_box_h = mg_get_h_size(mg_box_id, mg_wrap_w, mg_box_w);

			// apply only if under relative width
			if(use_rel_width) {
				jQuery(this).css('width', mg_box_w);
			}

			// height - calculate the title under and adjust img_wrap
			if( jQuery(this).find('.mg_title_under').length ) {
				var tit_under_h = jQuery(this).find('.mg_title_under').outerHeight(true);

				jQuery(this).find('.img_wrap').css('height', (mg_box_h - mg_boxMargin * 2));
				jQuery(this).css('height', mg_box_h + tit_under_h);
			}
			else  {
				jQuery(this).css('height', mg_box_h);
			}

			// overlays control
			if( parseInt(mg_box_w) < 100 || parseInt(mg_box_h) < 100 ) { jQuery(this).find('.cell_type').hide(); }
			else {jQuery(this).find('.cell_type').show();}

			if( parseInt(mg_box_w) < 65 || parseInt(mg_box_h) < 65 ) { jQuery(this).find('.cell_more').hide(); }
			else {jQuery(this).find('.cell_more').show();}


			// orientation class & masonerize after sizing
			if(i == (tot_elem - 1)) {
				jQuery('#'+cont_id+' .mg_box').each(function() {
                    if(jQuery(this).find('.img_wrap').width() >= jQuery(this).find('.img_wrap').height()) {
						jQuery(this).addClass('mg_landscape').removeClass('mg_portrait');	
					} else {
						jQuery(this).removeClass('mg_landscape').addClass('mg_portrait');		
					}
                });
				
				
				if(typeof(is_resizing) == 'undefined' || !is_resizing) {
					mg_masonerize(cont_id);
				}
				else {
					setTimeout(function() {
						jQuery('#' + cont_id).isotope('layout');
					}, 710);
				}
			}
		});
	};

	
	// custom action on grid width size change - persistent interval
	jQuery(document).ready(function() {
		setInterval(function() {
			jQuery('.mg_container').each(function() {
                var gid = jQuery(this).attr('id');
				var new_w = Math.round(jQuery(this).width());
				
				if(typeof(grids_width[gid]) == 'undefined') {
					grids_width[gid] = new_w;	
					return true;
				}
				
				// trigger only if size is different
				if(grids_width[gid] != new_w) {
					jQuery(this).trigger('mg_resize_grid', [gid]);	
					
					grids_width[gid] = new_w;
				}
            });
		}, 200);
	});
	
	// standard MG operations
	jQuery(document).delegate('.mg_container', 'mg_resize_grid', function(e, grid_id) {
		mg_responsive_txt(grid_id);				
		mg_size_boxes(grid_id, true);
			
		// inline players - resize to adjust tools size
		setTimeout(function() {
			mg_adjust_inl_player_size();
		}, 800);
	});
	
	
	// resize items after mobile mode switch
	jQuery(document).delegate('.mg_grid_wrap', 'mg_mobile_mode_switch', function() {
		jQuery('.mg_container').each(function() {
        	var gid = jQuery(this).attr('id');
			jQuery(this).trigger('mg_resize_grid', [gid]);		
		});
	});
	

	// masonry init
	mg_masonerize = function(cont_id) {
		var gid  = cont_id.substr(8);
		var $gid = jQuery('#' + cont_id);
		
		$gid.isotope({
			percentPosition: true,
			isResizeBound: false,
			resize: false,
			masonry: {
				columnWidth: 1
			},
			containerClass: 'mg_isotope',
			itemClass : 'mg_isotope-item',
			itemSelector: '.mg_box:not(.mg_pag_hide)',
			transitionDuration: '0.7s'
		});
		
		// be sure container class is added on any isotope version
		$gid.addClass('mg_isotope');
		
		
		// check for default/deeplinked filter
		var sel_filter = jQuery('#mgf_'+gid+' .mg_cats_selected').attr('rel');
		if(typeof(sel_filter) != 'undefined' && sel_filter != '*') {
			mg_filter_grid(cont_id, 'cat', sel_filter);
		} 
			
		// check for deeplinked search
		if(jQuery('#mgs_'+gid).length && jQuery('#mgs_'+gid+' input').val()) {
			mg_filter_grid(cont_id, 'search', jQuery('#mgs_'+gid+' input').val());
		}

		mg_display_grid(cont_id);
	};


	// grid display
	mg_display_grid = function(grid_id) {
		mg_responsive_txt();
		var $subj = jQuery('#'+grid_id+' .mg_box').not('.mg_pag_hide, .mg_inl_slider, .mg_inl_text');

		// is filtering?
		var filter_class = ''; 
		filter_class += (typeof(mg_grid_filter[grid_id]) != 'undefined' && typeof(mg_grid_filter[grid_id]['cat']) != 'undefined') ? ', .mgc_'+mg_grid_filter[grid_id]['cat'] : '';

		// fallback for IE
		if( navigator.appVersion.indexOf("MSIE 8.") != -1 || navigator.appVersion.indexOf("MSIE 9.") != -1 ) {
			mg_ie_fallback(grid_id);
		}
	
		// if no items have a featured image
		if(!$subj.find('img').length) {
			
			// selectors in case of selected filter
			if(filter_class && mg_filters_behav == 'standard') {
				// first filtered
				jQuery('#'+grid_id+' .mg_box'+filter_class).not('.mg_pag_hide').mg_display_boxes(grid_id);
				jQuery('#'+grid_id+' .mg_box').not('.mg_pag_hide'+filter_class).mg_display_boxes(grid_id);
			} else {
				jQuery('#'+grid_id+' .mg_box').not('.mg_pag_hide').mg_display_boxes(grid_id);
			}
		}

		else {
			var $images = $subj.find('img');
			
			// flag if items don't have featured image - only for logged users
			if(jQuery('body').hasClass('logged-in')) {
				$images.each(function() {
					if(jQuery(this).parents('.mg_box').hasClass('mg_no_feat_img') || this.src.indexOf('creation failed') != -1) {
						jQuery('#'+grid_id).prepend('<div class="mg_error_mess">One or more items don\'t have a featered image.<br/>Also check for thumbnails creation problems on your server</div>');
						return false;
					}
                });	
			}
			
			$images.lcweb_lazyload({
				allLoaded: function(url_arr, width_arr, height_arr) {
					
					// selectors in case of selected filter
					if(filter_class && mg_filters_behav == 'standard') {
						// first filtered
						jQuery('#'+grid_id+' .mg_box'+filter_class).not('.mg_pag_hide').mg_display_boxes(grid_id);
						jQuery('#'+grid_id+' .mg_box').not('.mg_pag_hide'+filter_class).mg_display_boxes(grid_id);
					} else {
						jQuery('#'+grid_id+' .mg_box').not('.mg_pag_hide').mg_display_boxes(grid_id);
					}
				}
			});
		}
	};


	// show boxes - to call after allLoaded
	jQuery.fn.mg_display_boxes = function(grid_id) {
		var a = 0;
		
		this.each(function(i, v) {
			var $subj = jQuery(this);
			var delay = (mg_delayed_fx) ? 170 : 0;

			setTimeout(function() {
				if( navigator.appVersion.indexOf("MSIE 8.") != -1 || navigator.appVersion.indexOf("MSIE 9.") != -1 ) {
					$subj.find('.mg_shadow_div').fadeTo(450, 1);
				}
				$subj.removeClass('mg_pre_show').addClass('mg_shown');
				
				// keburns effects - init
				$subj.mg_item_img_to_kenburns();
				
				
				// inline slider - init
				if( $subj.hasClass('mg_inl_slider') ) {
					var sid = $subj.find('.mg_inl_slider_wrap').attr('id');
					mg_inl_slider_init(sid);
				}
				
				// inline video - init and eventually autoplay
				if( $subj.hasClass('mg_inl_video') && $subj.find('.mg_sh_inl_video').length ) {
					var pid = '#' + $subj.find('.mg_sh_inl_video').attr('id');
					mg_video_player(pid, true);
					
					var inl_player = true; 
				}
				
				
				// webkit fix for inline vimeo/youtube fullscreen mode + avoid bounce back on self-hosted fullscreen mode
				if( $subj.hasClass('mg_inl_video') && !$subj.find('.mg_sh_inl_video').length) {
					if(navigator.userAgent.indexOf('Chrome/') != -1 || navigator.appVersion.indexOf("Safari/") != -1) {
						setTimeout(function() {
							$subj.find('.mg_shadow_div').css('transform', 'none').css('animation', 'none').css('-webkit-transform', 'none').css('-webkit-animation', 'none').css('opacity', 1);				
						}, 350);
					}	
				}
		

				// inline audio - init and show
				if( $subj.hasClass('mg_inl_audio') && $subj.hasClass('mg_item_no_ol') && $subj.find('.mg_inl_audio_player').length ) {
					setTimeout(function() {
						var pid = '#' + $subj.find('.mg_inl_audio_player').attr('id');
						mg_initSlide_inl_audio(pid);
					}, 350);
						
					var inl_player = true; 
				}
				
				// fix inline player's progressbar when everything has been shown
				if(typeof(inl_player) != 'undefined') {
					setTimeout(function() {
						var player_id = '#' + $subj.find('.mg_inl_audio_player, .mg_sh_inl_video').attr('id');
						mg_adjust_inl_player_size(player_id);
					}, 400);
				}
				
			}, (delay * a));
			
			a++;
		});
		jQuery('#'+grid_id).parents('.mg_grid_wrap').find('.mg_loader').fadeOut('fast', function() {
			jQuery(this).remove();
		});
		
		mg_filter_no_results( jQuery('#'+grid_id) );
	};


	// IE transitions fallback
	mg_ie_fallback = function(grid_id) {
		if( !jQuery('#'+grid_id+' .mgom_layer').length ) { // not for overlay manager
			jQuery('.mg_box .overlays').children().hide();

			jQuery('.mg_box .img_wrap').hover(
				function() {
					jQuery(this).find('.overlays').children().hide();
					jQuery(this).find('.overlays').children().not('.cell_more').fadeIn(250);
					jQuery(this).find('.overlays .cell_more').fadeIn(150);
				}
			);
		}
	};

	///////////////////////////////////////////////////////////


	// open item trigger
	jQuery(document).ready(function() {
		jQuery(document).delegate('.mg_closed:not(.mg_disabled)', 'click', function(e){
			// elements to ignore -> mgom socials
			var $e = jQuery(e.target);
			if(!lb_is_shown && !$e.hasClass('mgom_fb') && !$e.hasClass('mgom_tw') && !$e.hasClass('mgom_pt') && !$e.hasClass('mgom_gp') && !$e.hasClass('mg_quick_edit_btn')) {
				var $subj = jQuery(this);
				
				var pid = $subj.attr('rel').substr(4);
				$mg_sel_grid = $subj.parents('.mg_container');

				// open
				mg_open_item(pid);
			}
		});
	});

	// OPEN ITEM
	mg_open_item = function(item_id, deeplinked_lb) {

		// backup html/body inline CSS
		mg_html_style = jQuery('html').attr('style');
		mg_body_style = jQuery('body').attr('style');

		jQuery('#mg_lb_wrap').show();
		jQuery('html').css('overflow', 'hidden');
		jQuery('body').css('overflow', 'visible');

		if( jQuery('body').height() > jQuery(window).height() && !mg_is_mobile() ) {
			jQuery('html').css('margin-right', 16);
		}

		if(mg_is_mobile()) { 
			$mg_lb_contents.delay(20).trigger('click');
		}

		// open only if is not deeplinked
		if(typeof(deeplinked_lb) == 'undefined') {
			setTimeout(function() {
				jQuery('#mg_lb_loader, #mg_lb_background').addClass('mg_lb_shown');
				mg_get_item_content(item_id);
			}, 50);
		}
	};


	// get item content
	mg_get_item_content = function(pid, on_item_switch) {
		$mg_lb_contents.removeClass('mg_lb_shown');
		var gid = $mg_sel_grid.attr('id').substr(8);

		// set attributes to know related grid and item ID
		jQuery('#mg_lb_wrap').attr('item-id', pid).attr('grid-id', gid);

		// set deeplink
		if(jQuery.inArray('item', mg_deeplinked_elems) !== -1) {
			var item_title = jQuery('.mgi_'+pid+' .thumb').attr('alt');
			mg_set_deeplink('mgi_'+gid, pid, item_title);
			var static_cache_id = window.location.href;
		}
		else {
			var static_cache_id = false;
		}

		// get prev and next items ID to compose nav arrows
		var curr_item_pag = $mg_sel_grid.find('.mgi_'+pid).attr('mg_pag');
		var nav_arr = [];
		var curr_pos = 0;

		$mg_sel_grid.find('.mg_closed.mg_pag_'+curr_item_pag).not('.mg_disabled, .isotope-hidden').each(function(i, el) {
			var item_id = jQuery(this).attr('rel').substr(4);

			nav_arr.push(item_id);
			if(item_id == pid) {curr_pos = i;}
		});
		
		// nav - prev item
		var prev_id = (curr_pos != 0) ? nav_arr[(curr_pos - 1)] : 0;
		
		// nav - next item
		var next_id = (curr_pos != (nav_arr.length - 1)) ? nav_arr[(curr_pos + 1)] : 0;
		

		// check in static cache
		if(static_cache_id && typeof(items_cache[static_cache_id]) != 'undefined') {
			var delay = (typeof(on_item_switch) == 'undefined') ? 320 : 0; // avoid lightbox to be faster than background on initial load
			
			setTimeout(function() {
				fill_lightbox( items_cache[static_cache_id] );	
			}, delay);
		}
		
		// perform ajax call
		else {
			var cur_url = location.href;
			var data = {
				mg_lb	: 'mg_lb_content',
				pid		: pid,
				prev_id : prev_id,
				next_id : next_id
			};
			mg_get_item_ajax = jQuery.post(cur_url, data, function(response) {
				
				if(static_cache_id) {
					items_cache[static_cache_id] = response;
				}
				
				fill_lightbox(response);
			});
		}

		return true;
	};
	
	
	// POPULATE LIGHTBOX AND SHOW BOX
	var fill_lightbox = function(lb_contents) {
		if(!lb_switch_dir) {lb_switch_dir = 'next';}
		$mg_lb_contents.html(lb_contents).attr('class', 'mg_lb_pre_show_'+lb_switch_dir);

		// older IE iframe bg fix
		if(mg_is_old_IE() && jQuery('#mg_lb_contents .mg_item_featured iframe').length) {
			jQuery('#mg_lb_contents .mg_item_featured iframe').attr('allowTransparency', 'true');
		}

		// init self-hosted videos without poster
		if(jQuery('.mg_item_featured .mg_me_player_wrap.mg_self-hosted-video').length && !jQuery('.mg_item_featured .mg_me_player_wrap.mg_self-hosted-video > img').length) {
			mg_video_player('#mg_lb_video_wrap');
		}
		
		// show with a little delay to be smoother
		setTimeout(function() {
			jQuery('#mg_lb_loader').removeClass('mg_lb_shown');
			$mg_lb_contents.attr('class', 'mg_lb_shown').focus();
			
			lb_is_shown = true;
			lb_switch_dir = false;
		}, 50);
	};
	

	// switch item - arrow click
	jQuery(document).ready(function() {
		jQuery(document).delegate('.mg_nav_active > *', 'click'+mg_generic_touch_event, function(){
			lb_switch_dir = (jQuery(this).parents('.mg_nav_active').hasClass('mg_nav_next')) ? 'next' : 'prev';
			
			var pid = jQuery(this).parents('.mg_nav_active').attr('rel');
			mg_switch_item_act(pid);
		});
	});

	// switch item - keyboards events
	jQuery(document).keydown(function(e){
		if(lb_is_shown) {

			// prev
			if (e.keyCode == 37 && jQuery('.mg_nav_prev.mg_nav_active').length) {
				var pid = jQuery('.mg_nav_prev.mg_nav_active').attr('rel');
				lb_switch_dir = 'prev';
				mg_switch_item_act(pid);
			}

			// next
			if (e.keyCode == 39 && jQuery('.mg_nav_next.mg_nav_active').length) {
				var pid = jQuery('.mg_nav_next.mg_nav_active').attr('rel');
				lb_switch_dir = 'next';
				mg_switch_item_act(pid);
			}
		}
	});


	// switch item - touchSwipe events
	jQuery(document).ready(function() {
		if(typeof(mg_lb_touchswipe) != 'undefined' && mg_lb_touchswipe) {

			jQuery('#mg_lb_contents').swipe({
				swipeRight: function() { // prev
					if (jQuery('.mg_nav_prev.mg_nav_active').length) {
						var pid = jQuery('.mg_nav_prev.mg_nav_active').attr('rel');
						mg_switch_item_act(pid);
					}
				}, 
				swipeLeft: function() { // next
					if (jQuery('.mg_nav_next.mg_nav_active').length) {
						var pid = jQuery('.mg_nav_next.mg_nav_active').attr('rel');
						mg_switch_item_act(pid);
					}
				},
				threshold: 150,
				allowPageScroll: 'vertical'
			});
		}
	});


	// SWITCH ITEM IN LIGHTBOX
	mg_switch_item_act = function(pid) {
		jQuery('#mg_lb_loader').addClass('mg_lb_shown');
		$mg_lb_contents.attr('class', 'mg_lb_switching_'+lb_switch_dir);
		
		jQuery('#mg_lb_top_nav, .mg_side_nav, #mg_top_close').fadeOut(350, function() {
			jQuery(this).remove();
		});

		// wait CSS3 transitions
		setTimeout(function() {
			mg_unload_lb_scripts();
			$mg_lb_contents.empty();
			mg_get_item_content(pid);
			
			lb_is_shown = false;
		}, 500);


	};


	// CLOSE LIGHTBOX
	mg_close_lightbox = function() {
		mg_unload_lb_scripts();
		if(typeof(mg_get_item_ajax) != 'undefined') {mg_get_item_ajax.abort();}
		
		if(typeof(mg_lb_realtime_actions_intval) != 'undefined') {
			clearInterval(mg_lb_realtime_actions_intval);	
		}

		jQuery('#mg_lb_loader').removeClass('mg_lb_shown');
		$mg_lb_contents.attr('class', 'mg_closing_lb');
		
		jQuery('#mg_lb_background').delay(120).removeClass('mg_lb_shown');
		jQuery('#mg_lb_top_nav, .mg_side_nav, #mg_top_close').fadeOut(350, function() {
			jQuery(this).remove();
		});
		
		setTimeout(function() {
			jQuery('#mg_lb_wrap').hide();
			$mg_lb_contents.empty();
			jQuery('#mg_lb_background.google_crawler').fadeOut();

			// restore html/body inline CSS
			if(typeof(mg_html_style) != 'undefined') {jQuery('html').attr('style', mg_html_style);}
			else {jQuery('html').removeAttr('style');}

			if(typeof(mg_body_style) != 'undefined') {jQuery('body').attr('style', mg_body_style);}
			else {jQuery('body').removeAttr('style');}

			if(typeof(mg_scroll_helper_h) != 'undefined') {
				clearTimeout(mg_scroll_helper_h);
			}
			jQuery('#mg_lb_scroll_helper').removeAttr('style');
			
			$mg_lb_contents.attr('class', 'mg_lb_pre_show_next');
			lb_is_shown = false;
		}, 500); // wait for CSS transitions

		mg_remove_deeplink('mgi_'+ $mg_sel_grid.attr('id').substr(8) );
	};

	jQuery(document).ready(function() {
		jQuery(document).delegate('#mg_lb_background.mg_classic_lb, #mg_lb_scroll_helper.mg_classic_lb, .mg_close_lb', 'click'+mg_generic_touch_event, function(){
			mg_close_lightbox();
		});
	});

	jQuery(document).keydown(function(e){
		if( jQuery('#mg_lb_contents .mg_close_lb').length && e.keyCode == 27 ) { // escape key pressed
			mg_close_lightbox();
		}
	});


	// unload lightbox scripts
	var mg_unload_lb_scripts = function() {
		
		// stop persistent actions
		if(typeof(mg_lb_realtime_actions_intval) != 'undefined') {
			clearInterval(mg_lb_realtime_actions_intval);	
		}
	};


	// lightbox images lazyload
	mg_lb_lazyload = function() {
		$ll_img = jQuery('.mg_item_featured > div > img, #mg_lb_video_wrap img');
		if( $ll_img.length ) {
			mg_lb_lazyloaded = false;
			$ll_img.fadeTo(0, 0);
			
			$ll_img.lcweb_lazyload({
				allLoaded: function(url_arr, width_arr, height_arr) {
					mg_lb_lazyloaded = {
						urls 	: url_arr,
						widths	: width_arr,
						heights : height_arr	
					};
					
					$ll_img.fadeTo(300, 1);
					jQuery('.mg_item_featured .mg_loader').fadeOut('fast');
					jQuery('.mg_item_featured').mg_item_img_to_kenburns();
					
					if(jQuery('#mg_lb_feat_img_wrap').length) {
						jQuery('#mg_lb_feat_img_wrap').fadeTo(300, 1);	
					}
					
					// for video poster
					if( jQuery('#mg_ifp_ol').length )  {
						jQuery('#mg_ifp_ol').delay(300).fadeIn(300);
						setInterval(function() {
							jQuery('#mg_lb_video_wrap > img').css('display', 'block'); // fix for poster image click
						}, 200);
					}

					// for self-hosted video
					if( jQuery('.mg_item_featured .mg_self-hosted-video').length )  {
						jQuery('#mg_lb_video_wrap').fadeTo(0, 0);
						mg_video_player('#mg_lb_video_wrap');
						jQuery('#mg_lb_video_wrap').fadeTo(300, 1);
					}

					// for mp3 player
					if( jQuery('.mg_item_featured .mg_lb_audio_player').length )  {

						var player_id = '#' + jQuery('.mg_lb_audio_player').attr('id');
						mg_audio_player(player_id);

						jQuery('.mg_item_featured .mg_lb_audio_player').fadeIn();
					}
				}
			});
		}
	};


	// lightbox persistent interval actions
	mg_lb_realtime_actions = function() {
		if(typeof(mg_lb_realtime_actions_intval) != 'undefined') {
			clearInterval(mg_lb_realtime_actions_intval);	
		}
		mg_lb_realtime_actions_intval = setInterval(function() {
			var $feat = jQuery('.mg_item_featured');
			
			// wrap class reporting if contents are higher than page
			if(jQuery('#mg_lb_contents').outerHeight(true) > jQuery(window).height()) {
				if(!jQuery('#mg_lb_wrap').hasClass('mg_lb_has_scroll')) {
					jQuery('#mg_lb_wrap').addClass('mg_lb_has_scroll');
				}
			} else {
				if(jQuery('#mg_lb_wrap').hasClass('mg_lb_has_scroll')) {
					jQuery('#mg_lb_wrap').removeClass('mg_lb_has_scroll');
				}
			}
			
			
			// video - prior checks and height calculation
			if(jQuery('.mg_lb_video').length) {
				if( jQuery('.mg_item_featured .mg_video_iframe').length ) {	// iframe
					var $video_subj = jQuery('#mg_lb_video_wrap, #mg_lb_video_wrap .mg_video_iframe');
				}
				else { // self-hosted
					var $video_subj = jQuery('.mg_item_featured .mg_self-hosted-video .mejs-container, .mg_item_featured .mg_self-hosted-video video');
				}
				
				var new_video_h = Math.ceil($feat.width() * video_h_ratio);
			}
			
			/////////

			// fill side-layout space if lightbox is smaller than screen's height 
			if(jQuery('.mg_lb_feat_match_txt').length && jQuery(window).width() > 860) {
				var txt_h = jQuery('.mg_item_content').outerHeight();
					
				// single image and audio
				if(typeof(mg_lb_lazyloaded) != 'undefined' && mg_lb_lazyloaded && !jQuery('.mg_galleria_slider_wrap').length) {
					var player_h = (jQuery('.mg_lb_audio').length) ? jQuery('.mg_lb_audio_player').outerHeight(true) : 0;	
				  
					// calculate what would be original height
					var real_img_h = Math.round((mg_lb_lazyloaded.heights[0] * $feat.width()) / mg_lb_lazyloaded.widths[0]);

					if((real_img_h + player_h) < txt_h && $feat.height() != txt_h) {
						$feat.addClass('mg_lb_feat_matched');
						$feat.find('img').css('height', (txt_h - player_h)).addClass('mg_lb_img_fill');	
					} 
					else if(real_img_h > txt_h) {
						$feat.removeClass('mg_lb_feat_matched');
						$feat.find('img').removeAttr('style').removeClass('mg_lb_img_fill');
					}
				}
			
				// video
				if(jQuery('.mg_lb_video').length) {
					if(new_video_h < txt_h) {new_video_h = txt_h;}
					
					if($video_subj.height() != new_video_h) {
						if(jQuery('.mg_item_featured .mg_video_iframe').length) {
							$video_subj.attr('height', new_video_h);
						} else {
							$video_subj.css('height', new_video_h).css('max-height', new_video_h).css('min-height', new_video_h);
						}	
					}
				}
				
				// slider 
				if(jQuery('.mg_galleria_slider_wrap').length) {
					var new_slider_h = txt_h - parseInt( jQuery('.mg_galleria_slider_wrap').css('padding-bottom'));
				}
				
			}
				
			// normal sizing
			else {
				
				// single image and audio
				if(typeof(mg_lb_lazyloaded) != 'undefined' && mg_lb_lazyloaded && $feat.hasClass('mg_lb_feat_matched')) {
					$feat.removeClass('mg_lb_feat_matched');
					$feat.find('img').removeAttr('style').removeClass('mg_lb_img_fill');	
				}
				
				// video
				if(jQuery('.mg_lb_video').length) {
					if($video_subj.height() != new_video_h) {
						if($video_subj.is('div')) {
							$video_subj.css('height', new_video_h).css('max-height', new_video_h).css('min-height', new_video_h);
						} else {
							$video_subj.attr('height', new_video_h);
						}
					}
				}
				
				// slider 
				if(jQuery('.mg_galleria_slider_wrap').length) {
					var slider_id = '#'+ jQuery('.mg_galleria_slider_wrap').attr('id');
					var new_slider_h = (jQuery('.mg_galleria_responsive').length) ? Math.ceil(jQuery('.mg_galleria_responsive').width() * mg_galleria_height(slider_id)) : mg_galleria_height(slider_id); 
				}
			}
			
			//////////
			
			// slider resizing
			if(typeof(mg_lb_slider) != 'undefined' && typeof(new_slider_h) != 'undefined') {
				if(
					typeof(mg_galleria_h) == 'undefined' ||
					mg_galleria_h != new_slider_h || 
					jQuery('.mg_galleria_slider_wrap').width() != jQuery('.galleria-stage').width()
				) { 
					if(typeof(mg_slider_is_resizing) == 'undefined' || !mg_slider_is_resizing)  {
						mg_galleria_h = new_slider_h; 
						resize_galleria(new_slider_h);
					}
				}
			}
			
			// hook for customizations
			jQuery('#mg_lb_wrap').trigger('mg_lb_realtime_actions');
		}, 20);
	};


	//////////////////////////////////////////////////////////////////////////
	
	// paginate items
	jQuery(document).ready(function() {
		
		// next
		jQuery(document).delegate('.mg_next_page:not(.mg_pag_disabled)', 'click'+mg_generic_touch_event, function(e){
			mg_do_pagination('next', this);
		});

		// prev
		jQuery(document).delegate('.mg_prev_page:not(.mg_pag_disabled)', 'click'+mg_generic_touch_event, function(e){
			mg_do_pagination('prev', this);
		});
	});
	
	
	// trick to move to first page
	var mavo_to_pag_1 = function(gid, prev_btn_obj) {
		mg_grid_pag[ 'mg_grid_' + gid ] = 2;	
		mg_do_pagination(gid, prev_btn_obj);
	};
	
	
	// perform pagination
	mg_do_pagination = function(direction, pag_btn_obj) {
		var gid = jQuery(pag_btn_obj).parents('.mg_pag_wrap').attr('id').substr(4);
		var $grid_wrap = jQuery('#mg_wrap_'+gid);

		var tot_pag = parseInt(jQuery('#mgp_'+gid).attr('tot-pag'));
		var curr_pag =  parseInt(mg_grid_pag[ 'mg_grid_' + gid ]);
		
		// hide "no items" message
		$grid_wrap.find('.mg_no_results').remove();
		
		// ignore in these cases
		if(
			(direction == 'next' && curr_pag >= tot_pag) ||
			(direction == 'prev' && curr_pag <= 1) ||
			$grid_wrap.hasClass('mg_is_paginating')
		) {
			return false;	
		}

		var new_pag = (direction == 'next') ? curr_pag + 1 : curr_pag - 1;
		
		// set/remove deeplink
		if(jQuery.inArray('page', mg_deeplinked_elems) !== -1) {
			if(new_pag == 1) {
				mg_remove_deeplink('mgp_'+gid);
			} else {
				mg_set_deeplink('mgp_'+gid, new_pag);
			}
		}
		
		// manage disabled class
		if(new_pag == 1) {
			jQuery('#mgp_'+gid+' .mg_prev_page').addClass('mg_pag_disabled');
		} else {
			jQuery('#mgp_'+gid+' .mg_prev_page').removeClass('mg_pag_disabled');
		}
		
		if(new_pag == tot_pag) {
			jQuery('#mgp_'+gid+' .mg_next_page').addClass('mg_pag_disabled');
		} else {
			jQuery('#mgp_'+gid+' .mg_next_page').removeClass('mg_pag_disabled');
		}
		
		// manage current pag number if displayed
		if(jQuery('#mgp_'+gid+' .mg_nav_mid span').length) {
			jQuery('#mgp_'+gid+' .mg_nav_mid span').text(new_pag);	
		}
		
		// pause players
		mg_pause_inl_players('mg_grid_'+gid, true);
		
		// hide items and show loader
		setTimeout(function() {
			$grid_wrap.addClass('mg_is_paginating');
			$grid_wrap.find('.mgf_search_form input, .mg_mobile_filter_dd').attr('disabled', 'disabled');
		}, 200);
		
		setTimeout(function() {
			if(!$grid_wrap.find('.mg_loader').length) {
				$grid_wrap.find('.mg_container').prepend(mg_loader);
			}
		}, 300);

		// once items are hidden - elaborate
		setTimeout(function() {
			$grid_wrap.find('.mg_container').isotope('destroy').removeClass('mg_isotope');
			jQuery('#mg_grid_'+gid+' .mg_box').removeClass('mg_shown isotope-hidden');
			$grid_wrap.find('.mg_container').css('height', 200);
			
			$grid_wrap.removeClass('mg_is_paginating');
			$grid_wrap.find('.mgf_search_form input, .mg_mobile_filter_dd').removeAttr('disabled');
			
			mg_grid_pag[ gid ] = new_pag;
			
			// webkit fix for inline videos fullscreen mode - remove custom CSS
			jQuery('#mg_grid_'+gid+' .mg_inl_video .mg_shadow_div').removeAttr('style');
			
			mg_async_init(gid, new_pag);
			$grid_wrap.trigger('mg_did_pag', [gid]);
		}, 650);	
	};
	
	
	// manage pag hide class
	var mg_pag_class = function(grid_id) {
		var curr_pag = mg_grid_pag[grid_id];

		jQuery('#'+grid_id+' .mg_box').removeClass('mg_pag_hide');
		jQuery('#'+grid_id+' .mg_box').not('.mg_pag_' + curr_pag).addClass('mg_pag_hide');
	};
	

	//////////////////////////////////////////////////////////////////////////

	
	// FILTERING HUB
	mg_filter_grid = function(grid_id, filter_subj, filter_val) {
		if(typeof(mg_grid_filter[grid_id]) == 'undefined') {mg_grid_filter[grid_id] = {};}	
		
		// avoid applying twice the same filter
		if(typeof(mg_grid_filter[grid_id][filter_subj]) != 'undefined' && mg_grid_filter[grid_id][filter_subj] == filter_val) {
			return false;	
		}
		
		mg_grid_filter[grid_id][filter_subj] = filter_val;
			
		// apply - deferring
		if(typeof(mg_filters_defer[grid_id]) != 'undefined') {
			clearTimeout(mg_filters_defer[grid_id]);	
		}
		mg_filters_defer[grid_id] = setTimeout(function() {
			mg_apply_grid_filters(grid_id);
		}, 10); 
		
	};
	var mg_apply_grid_filters = function(grid_id) {
		$gid = jQuery('#'+ grid_id);

		// wrap up filters
		var filters = '';
		jQuery.each(mg_grid_filter[grid_id], function(i, v) {
			if(i == 'cat') {
				filters += (v == '*') ? '*' : '.mgc_' + v;
			}
			else if(i == 'search' && v && v.length > 2) {
				filters += '.mg_search_res'; 	
			}
		});
		
		// clean
		if(!filters) {filters = '*';}
		else {
			if(filters.indexOf('*') !== -1) {filters = filters.replace(/\*/g, '');}	
			if(!filters) {filters = '*';}
		}

		// apply
		if(mg_filters_behav == 'standard') {
			$gid.isotope({ filter: filters });
		} else {
			$gid.mg_custom_iso_filter({ filter: filters });
		}
		
		// be sure isotope-hidden class is added
		$gid.find('.mg_box:not(.mg_pag_hide)').removeClass('isotope-hidden');
		if(filters != '*') {
			$gid.find('.mg_box:not(.mg_pag_hide)').not(filters).addClass('isotope-hidden');
		}
		
		// if no item left - show message
		mg_filter_no_results($gid);
		
		// pause hidden inline players
		if(filters != '*') {
			mg_pause_inl_players(grid_id);
		}
	};


	// items filter - buttons
	jQuery(document).ready(function() {
		jQuery(document).delegate('.mg_filter a', 'click', function(e) {
			e.preventDefault();

			var gid = jQuery(this).parents('.mg_filter').attr('id').substr(4);
			var sel = jQuery(this).attr('rel');
			var cont_id = 'mg_grid_' + gid;
			
			// if is paginating - stop
			if(jQuery('#mg_wrap_'+gid).hasClass('mg_is_paginating') ) {return false;}
			
			// set/clear deeplink
			if(!jQuery(this).hasClass('mg_def_filter')) {
				
				if(jQuery.inArray('category', mg_deeplinked_elems) !== -1) {
					var txt = (sel == '*') ? '' : jQuery(this).text();
					mg_set_deeplink('mgc_'+gid, sel, txt); 
				}
			} else {
				mg_remove_deeplink('mgc_'+gid);
			}

			// button selection manag
			jQuery('#mgf_'+gid+' a').removeClass('mg_cats_selected');
			jQuery(this).addClass('mg_cats_selected');

			// perform
			mg_filter_grid(cont_id, 'cat', sel);
			

			// if is there a dropdown filter - select option
			if( jQuery('#mgmf_'+gid).length) {
				jQuery('#mgmf_'+gid+' option').removeAttr('selected');

				if(jQuery(this).attr('rel') !== '*') {
					jQuery('#mgmf_'+gid+' option[value='+ jQuery(this).attr('rel') +']').attr('selected', 'selected');
				}
			}
		});
	});

	// items filter - mobile dropdown
	jQuery(document).ready(function() {
		jQuery(document).delegate('.mg_mobile_filter_dd', 'change', function(e) {
			var gid = jQuery(this).parents('.mg_mobile_filter').attr('id').substr(5);

			// simulate regular filter's click
			var btn_to_sel = (jQuery(this).val() == '*') ? '.mgf_all' : '.mgf_id_' + jQuery(this).val();
			jQuery('#mgf_'+gid+' '+btn_to_sel).trigger('click');
		});
	});
	
	
	// searching
	jQuery(document).delegate('.mgf_search_form i', 'click'+mg_generic_touch_event, function() {
		var gid = jQuery(this).parent().attr('id').substr(4);
		var grid_id = 'mg_grid_' + gid;
		var val = jQuery(this).parent().find('input').val();
		
		// reset
		jQuery('#'+ grid_id +' .mg_box').removeClass('mg_search_res');	
		
		if(!val || val.length > 2) {
			if(val) {
				var src_arr = val.toLowerCase().split(' ');
				var matching = jQuery.makeArray();

				// cyle and check each searched term 
				jQuery('#' + grid_id+' .mg_box').not('.mg_spacer').each(function() {
					var src_attr = jQuery(this).attr('mg-search').toLowerCase();
					var rel = jQuery(this).attr('rel');
					
					jQuery.each(src_arr, function(i, word) {						
						if( src_attr.indexOf(word) !== -1 ) {
							matching.push( rel );
							return false;	
						}
					});
				});

				// add class to matched elements
				jQuery.each(matching, function(i, v) {
					jQuery('#' + grid_id+' .mg_box[rel='+ v +']').addClass('mg_search_res');
				});
			}	
		}
		
		// deeplink set/remove
		if(jQuery.inArray('search', mg_deeplinked_elems) !== -1) {
			if(val.length > 2) {
				mg_set_deeplink('mgs_'+gid, val);
			} else {
				mg_remove_deeplink('mgs_'+gid);
			}
		}
		
		mg_filter_grid(grid_id, 'search', val);
	});
	
	
	// search on key pressing
	jQuery(document).delegate('.mgf_search_form input', 'keyup', function() { 
		if(typeof(mg_search_keyup_defer) != 'undefined') {clearTimeout(mg_search_keyup_defer);}
		var $subj = jQuery(this).parent();
		
		mg_search_keyup_defer = setTimeout(function() {
			$subj.find('i').trigger('click');
		}, 50);
	});
	
	
	// avoid field submit
	jQuery(document).ready(function(e) {
		jQuery('.mgf_search_form').submit(function(e) {
			e.preventDefault();

			jQuery(this).find('i').trigger('click');
		});
	});
		
	
	// custom filtering behavior
	jQuery.fn.mg_custom_iso_filter = function( options ) {
		options = jQuery.extend({
			filter: '*',
			hiddenStyle: { opacity: 0.2 },
			visibleStyle: { opacity: 1 }
		}, options );

		this.each( function() {
			var $items = jQuery(this).children();
			var $visible = $items.filter( options.filter );
			var $hidden = $items.not( options.filter );

			$visible.clearQueue().animate( options.visibleStyle, 300 ).removeClass('mg_disabled');
			$hidden.clearQueue().animate( options.hiddenStyle, 300 ).addClass('mg_disabled');
		});
	};
	
	
	// shown items count - toggle "no results" box
	var mg_filter_no_results = function($grid_obj) {
		if(!$grid_obj.find('.mg_box:not(.mg_pag_hide)').not('.isotope-hidden').length) {
			if(!$grid_obj.find('.mg_no_results').length) {
				
				$grid_obj.css('min-height', 55);
				$grid_obj.append('<div class="mg_no_results">'+ mg_no_results_txt +'</div>');
				$grid_obj.find('.mg_no_results').fadeTo(400, 1);
			}
		} 
		else {
			$grid_obj.css('min-height', 0);
			$grid_obj.find('.mg_no_results').remove();	
		}
	};
	

	////////////////////////////////////////////
	
	
	// link items + text under - fix
	jQuery(document).delegate('.mg_link .mg_title_under', 'click', function(e) {
		e.preventDefault();

		var $subj = jQuery(this).parents('.mg_link').find('.mg_link_elem');
		window.open($subj.attr('href'), $subj.attr('target'));
	});


	// video poster - handle click
	jQuery(document).ready(function() {
		// grid item
		jQuery(document).delegate('.mg_inl_video:not(.mg_disabled)', 'click'+mg_generic_touch_event, function(e){
			if(jQuery(this).find('.thumb').length) {
				
				// self-hosted 
				if(jQuery(this).find('.mg_sh_inl_video').length) {
					var pid = '#' + jQuery(this).find('.mg_sh_inl_video').attr('id');
					
					jQuery(this).find('.img_wrap .thumb, .img_wrap .overlays').not('iframe').fadeOut(350, function() {
						jQuery(this).parents('.img_wrap').find('.mg_sh_inl_video').css('z-index', 10);
						jQuery(this).remove();
						
						var player_obj = mg_player_objects[pid];
						player_obj.play();
					});
				}
				else {
					var autop = jQuery(this).find('iframe').attr('autoplay-url');
					jQuery(this).find('iframe').attr('src', autop);
		
					jQuery(this).find('.img_wrap iframe').show();
					jQuery(this).find('.img_wrap > div *').not('iframe').fadeOut(350, function() {
						jQuery(this).parents('.img_wrap').find('iframe').css('z-index', 10);
						jQuery(this).remove();
					});
				}
			}
		});

		// lightbox
		jQuery(document).delegate('#mg_lb_video_poster, #mg_ifp_ol', 'click'+mg_generic_touch_event, function(e){
			var autop = jQuery('#mg_lb_video_poster').attr('autoplay-url');
			if(typeof(autop) != 'undefined') {
				jQuery('#mg_lb_video_wrap').find('iframe').attr('src', autop);
			}

			jQuery('#mg_ifp_ol').fadeOut(120);
			jQuery('#mg_lb_video_poster').fadeOut(400);
		});
	});


	// touch devices hover effects
	if( mg_is_touch_device() ) {
		jQuery('.mg_box').bind('touchstart', function() { jQuery(this).addClass('mg_touch_on'); });
		jQuery('.mg_box').bind('touchend', function() { jQuery(this).removeClass('mg_touch_on'); });
	}


	/////////////////////////////////////


	// get URL query vars and returns them into an associative array
	var get_url_qvars = function() {
		mg_hashless_url = window.location.href;
		
		if(mg_hashless_url.indexOf('#') !== -1) {
			var hash_arr = mg_hashless_url.split('#');
			mg_hashless_url = hash_arr[0];
			mg_url_hash = '#' + hash_arr[1];
		}
		
		// detect
		var qvars = {};
		var raw = mg_hashless_url.slice(mg_hashless_url.indexOf('?') + 1).split('&');
		
		jQuery.each(raw, function(i, v) {
			var arr = v.split('=');
			qvars[arr[0]] = arr[1];
		});	
		
		return qvars;
	};
	

	/*
	 * Global function to set media grid deeplinks
	 *
	 * subj (string) - attribute name
	 * val (int) - deeplink value (cat ID - item ID - etc)
	 * txt (string) - optional value to attach a text to value 
	 */
	mg_set_deeplink = function(subj, val, txt) {
		if(!mg_deeplinked_elems.length) {return false;}
		
		var qvars = get_url_qvars(); // get query vars and set clean URL + eventual hash 

		// setup deeplink part
		var true_val = (typeof(txt) != 'undefined' && txt) ? val +'/'+ encodeURIComponent(txt) : val;
		var dl_part = subj +'='+ true_val + mg_url_hash;
		
		
		// if URL doesn't have attributes
		if(mg_hashless_url.indexOf('?') === -1) {
			history.pushState(null, null, mg_hashless_url +'?'+ dl_part);
		}
		else {

			// if new deeplink already exists
			if(typeof(qvars[subj]) != 'undefined' && qvars[subj] == true_val) {
				return true;	
			}
			
			// re-compose URL
			var new_url = mg_hashless_url.slice(0, mg_hashless_url.indexOf('?') + 1);

			// (if found) discard attribute to be set
			var a = 0;
			var has_other_qvars = false;
			var this_attr_exists = false;
			
			jQuery.each(qvars, function(i, v) {
				if(typeof(i) == 'undefined') {return;}
				if(a > 0) {new_url += '&';}
				
				if(i != subj) {
					new_url += (v) ? i+'='+v : i; 
					
					has_other_qvars = true;
					a++;	
				}
				else {
					this_attr_exists = true;	
				}
			});
				
			if(has_other_qvars) {new_url += '&';}		
			new_url += dl_part;


			if(mg_deeplinked && this_attr_exists && !mg_full_deeplinking) { 
				history.replaceState(null, null, new_url);
			} else {
				history.pushState(null, null, new_url);	
				mg_deeplinked = true;
			}
		}
	};


	// apply deeplink to page grids
	mg_apply_deeplinks = function(on_init) {
		// at the moment - no actions on init
		if(on_init) {return false;}
		
		var qvars = get_url_qvars();
		
		jQuery.each(qvars, function(subj, val) {
			if(typeof(val) == 'undefined') {return;}
			var gid = subj.substr(4);
			
			// clean texts from deeplinked val
			var raw_val = val.split('/');
			val = raw_val[0]; 
			
			
			// item deeplink - not on first init
			if(subj.indexOf('mgi_') !== -1) {
				
				// check item existence
				if(!jQuery('#mg_grid_'+ gid +' .mg_closed.mgi_'+ val).length) {return;}
				
				// if lightbox is already opened
				if(jQuery('.mg_item_content').length) {

					// grid item is already shown?
					if(jQuery('#mg_lb_wrap').attr('item-id') == val && jQuery('#mg_lb_wrap').attr('grid-id') == gid) {return;}

					// unload lightbox
					$mg_sel_grid = jQuery('#mg_grid_'+gid);
					jQuery('#mg_lb_loader').addClass('mg_lb_shown');
					mg_get_item_content(val);
				}
				
				else {
					// simulate click on item
					jQuery('#mg_grid_'+ gid +' .mgi_'+ val).trigger('click');
				}
			}
			
			// category deeplink - not on first init
			if(subj.indexOf('mgc_') !== -1) {
				var $f_subj = (val == '*') ? jQuery('#mgf_'+ gid +' .mgf_all') : jQuery('#mgf_'+ gid +' .mgf_id_'+ val);
				
				// check filter existence
				if(!$f_subj.not('.mg_cats_selected').length) {return;}
				$f_subj.trigger('click');
			}
			
			// pagination deeplink - not on first init
			if(subj.indexOf('mgp_') !== -1 && jQuery('#mgp_'+gid).length) {
				if(typeof(mg_grid_pag['mg_grid_' + gid ]) == 'undefined' || mg_grid_pag['mg_grid_' + gid ] == val) {return;}
				
				var subj = (mg_grid_pag['mg_grid_' + gid ] > val) ? '.mg_prev_page' : '.mg_next_page'; 
				jQuery('#mgp_'+gid+' '+subj).not('.mg_pag_disabled').trigger('click');
			}
			
			// search deeplink
			if(subj.indexOf('mgs_') !== -1) {
				if(typeof(on_init) == 'undefined') {
					jQuery('#mgs_'+ gid+' input').val(decodeURIComponent(val)).submit();
				} else {
					setTimeout(function() {
						jQuery('#mgs_'+ gid+' input').submit();
					}, 10);	
				}
			}
		});
		
				
		// step back from opened lightbox
		if(mg_hashless_url.indexOf('mgi_') === -1 && jQuery('.mg_item_content').length) {
			jQuery('.mg_close_lb').trigger('click');	
		}	
		
		// step back for each grid
		jQuery('.mg_grid_wrap').each(function() {
			var gid = jQuery(this).attr('id').substr(8);

			// from category deeplink
			var $mgc = jQuery(this).find('.mg_cats_selected');
			if(mg_hashless_url.indexOf('mgc_'+gid) === -1 && $mgc.length && !$mgc.hasClass('mg_def_filter')) {
				jQuery(this).find('.mg_def_filter').trigger('click');	
			}
			
			// from pagination
			if(mg_hashless_url.indexOf('mgp_'+gid) === -1 && jQuery('#mgp_'+gid).length && jQuery('#mgs_'+ gid+' input').val()) {
				mavo_to_pag_1(gid, jQuery('#mgp_'+gid+' .mg_prev_page'));
			}
			
			// from search
			if(mg_hashless_url.indexOf('mgs_'+gid) === -1 && jQuery('#mgs_'+gid).length && jQuery('#mgs_'+ gid+' input').val()) {
				jQuery('#mgs_'+ gid+' input').val('').submit();
			}
		});
	};
	
	
	// remove deeplink
	mg_remove_deeplink = function(subj) {
		var qvars = get_url_qvars();
		if(typeof(qvars[subj]) == 'undefined') {return false;}
		
		// discard attribute to be removed
		var parts = [];
		jQuery.each(qvars, function(i, v) {
			if(typeof(i) != 'undefined' && i && i != subj) {
				var val = (v) ? i+'='+v : i;
				parts.push(val);	
			}
		});
		
		var qm = (parts.length) ? '?' : '';	
		var new_url = mg_hashless_url.slice(0, mg_hashless_url.indexOf('?')) + qm + parts.join('&') + mg_url_hash;

		history.pushState(null, null, new_url);	
		
		if(mg_hashless_url.indexOf('mgi_') === -1 && mg_hashless_url.indexOf('mgc_') === -1 && mg_hashless_url.indexOf('mgp_') === -1 && mg_hashless_url.indexOf('mgs_') === -1) {
			mg_deeplinked = false;
		}	
	};
	
	
	// detect URL changes
	window.onpopstate = function(e) {
		mg_apply_deeplinks();
		
		if(mg_hashless_url.indexOf('mgi_') === -1 && mg_hashless_url.indexOf('mgc_') === -1 && mg_hashless_url.indexOf('mgp_') === -1 && mg_hashless_url.indexOf('mgs_') === -1) {
			mg_deeplinked = false;
		}
	};
	
	

	/////////////////////////////////////
	// galleria slider functions

	// manage slider initial appearance
	mg_galleria_show = function(sid) {
		setTimeout(function() {
			if( jQuery(sid+' .galleria-stage').length) {
				jQuery(sid).removeClass('mg_show_loader');
				jQuery(sid+' .galleria-container').fadeTo(400, 1);
			} else {
				mg_galleria_show(sid);
			}
		}, 50);
	};


	// manage the slider proportions on resize
	mg_galleria_height = function(sid) {
		if( jQuery(sid).hasClass('mg_galleria_responsive')) {
			return parseFloat( jQuery(sid).attr('asp-ratio') );
		} else {
			return parseInt(jQuery(sid).attr('slider-h'));
		}
	};


	var resize_galleria = function(new_h) {
		mg_slider_is_resizing = setTimeout(function() {
			jQuery('.mg_galleria_slider_wrap, .galleria-container').css('min-height', new_h);
			
			setTimeout(function() {
				mg_lb_slider.resize();	
			}, 500);
			
			mg_slider_is_resizing = false;
		}, 20);
	};

	
	/////////////////////////////////////
	// initialize inline sliders 
	mg_inl_slider_init = function(sid) {
		jQuery('#'+sid).lc_micro_slider({
			slide_fx 		: mg_inl_slider_fx,
			touchswipe		: mg_inl_slider_touch,
			autoplay		: (jQuery('#'+sid).hasClass('mg_autoplay_slider')) ? true : false,
			animation_time	: mg_inl_slider_fx_time,
			slideshow_time	: mg_inl_slider_intval,
			pause_on_hover	: mg_inl_slider_pause_on_h,
			loader_code		: mg_loader,
			debug			: false
		});
    };
	
	
	// turns item's image into a ken burns slider
	jQuery.fn.mg_item_img_to_kenburns = function() {
		this.find('.mg_kenburnsed_item').lc_micro_slider({
			slideshow_time	: mg_kenburns_timing,
			pause_on_hover	: false,
			loader_code		: mg_loader,
			debug			: false
		});
	};

	
	//// ken burns effect
	// catch event	
	jQuery(document).ready(function() {
		jQuery('body').delegate('.mg_kenburns_slider', 'lcms_initial_slide_shown lcms_new_active_slide', function(e, slide_index) {	
			var $subj = jQuery(this).find('.lcms_slide[rel='+slide_index+'] .lcms_bg');
			var time = jQuery(this).data('lcms_settings').slideshow_time;

			$subj.css('transition-duration', (time / 1000)+'s');	
			mg_lcms_apply_kenburns_css($subj, time);
		});
	});
	
	
	// apply css for kenburns
	var mg_lcms_apply_kenburns_css = function($subj, time) {
		if(!$subj.length) {return false;}
		
		vert_prop = mg_lcms_kenburns_size_prop('vert');
		horiz_prop = mg_lcms_kenburns_size_prop('horiz');
		var props = {};	
			
		if($subj.hasClass('mg_lcms_kb_zoomed')) {
			props['top']	= '0';
			props['right'] 	= '0';
			props['bottom'] = '0';
			props['left'] 	= '0';
				
			$subj.removeClass('mg_lcms_kb_zoomed');
		}
		else {
			props[ vert_prop ] 	= '-25%';
			props[ horiz_prop ] = '-25%';

			$subj.addClass('mg_lcms_kb_zoomed');
		}
		
		props['background-position'] = mg_lcms_kenburns_bgpos_prop() +' '+ mg_lcms_kenburns_bgpos_prop();
		$subj.css(props);
		
		setTimeout(function() {
			mg_lcms_apply_kenburns_css($subj, time, vert_prop, horiz_prop);
		}, time);
	};
	
	// get random value for random direction
	var mg_lcms_kenburns_size_prop = function(direction) {
	   var vals = (direction == 'horiz') ? ["left", "right"] : ["top", "bottom"];
	   return vals[Math.floor(Math.random() * vals.length)];
	};
	
	var mg_lcms_kenburns_bgpos_prop = function() {
	   var vals = ['0%', '100%'];
	   return vals[Math.floor(Math.random() * vals.length)];
	};
	

	/////////////////////////////////////
	// Initialize Galleria
	mg_galleria_init = function(sid, inline_slider) {
		Galleria.run(sid, {
			theme				: 'mediagrid',
			height				: (jQuery('.mg_lb_feat_match_txt').length && jQuery(window).width() > 860) ? jQuery('.mg_item_content').outerHeight() : mg_galleria_height(sid),
			swipe				: false,
			thumbnails			: true,
			transition			: mg_galleria_fx,
			fullscreenDoubleTap	: false,
			responsive			: false,
			wait				: true,

			initialTransition	: 'flash',
			transitionSpeed		: mg_galleria_fx_time,
			imageCrop			: mg_galleria_img_crop,
			extend				: function() {
				mg_lb_slider = this;
				jQuery(sid+' .galleria-loader').append(mg_loader);

				if(typeof(mg_slider_autoplay[sid]) != 'undefined' && mg_slider_autoplay[sid]) {
					jQuery(sid+' .galleria-mg-play').addClass('galleria-mg-pause');
					mg_lb_slider.play(mg_galleria_interval);
				}

				// play-pause
				jQuery(sid+' .galleria-mg-play').click(function() {
					jQuery(this).toggleClass('galleria-mg-pause');
					mg_lb_slider.playToggle(mg_galleria_interval);
				});

				// thumbs navigator toggle
				jQuery(sid+' .galleria-mg-toggle-thumb').click(function() {
					var $mg_slider_wrap = jQuery(this).parents('.mg_galleria_slider_wrap');


					if( $mg_slider_wrap.hasClass('galleria-mg-show-thumbs') || $mg_slider_wrap.hasClass('mg_galleria_slider_show_thumbs') ) {
						$mg_slider_wrap.stop().animate({'padding-bottom' : '0px'}, 400);
						$mg_slider_wrap.find('.galleria-thumbnails-container').stop().animate({'bottom' : '10px', 'opacity' : 0}, 400);

						$mg_slider_wrap.removeClass('galleria-mg-show-thumbs');
						if( $mg_slider_wrap.hasClass('mg_galleria_slider_show_thumbs') ) {
							$mg_slider_wrap.removeClass('mg_galleria_slider_show_thumbs');
						}
					}
					else {
						$mg_slider_wrap.stop().animate({'padding-bottom' : '56px'}, 400);
						$mg_slider_wrap.find('.galleria-thumbnails-container').stop().animate({'bottom' : '-60px', 'opacity' : 1}, 400);

						$mg_slider_wrap.addClass('galleria-mg-show-thumbs');
					}
				});
			}
		});
	};
	
	
	// hide caption if play a slider video
	jQuery(document).ready(function() {
		jQuery('body').delegate('.mg_galleria_slider_wrap .galleria-images', 'click', function(e) {
			setTimeout(function() {
				if( jQuery('.mg_galleria_slider_wrap .galleria-image:first-child .galleria-frame').length) {
					jQuery('.mg_galleria_slider_wrap .galleria-stage .galleria-info-text').slideUp();	
				}
			}, 500);
		});
	});


	/////////////////////////////////////
	// mediaelement audio/video player functions

	// init video player
	mg_video_player = function(player_id, is_inline) {
		if(!jQuery(player_id).length) {return false;}
		
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_video_player(player_id, is_inline);
			}, 50);
			return false;
		}
		
		if(typeof(mg_player_objects) == 'undefined') {
			mg_player_objects = jQuery.makeArray(); // array of player objects
		}
		
		if(typeof(is_inline) == 'undefined') {
			var features = ['playpause','current','progress','duration','volume','fullscreen'];
		} else {
			var features = ['playpause','current','progress','volume','fullscreen'];
		}
		
		var player_obj = new MediaElementPlayer(player_id+' video',{
			audioVolume: 'vertical',
			startVolume: 1,
			features: features
		});
		
		mg_player_objects[player_id] = player_obj;
		
		// autoplay
		if(jQuery(player_id).hasClass('mg_video_autoplay')) {
			if(typeof(is_inline) == 'undefined') {
				player_obj.play();
			} 
			else {
				setTimeout(function() {
					if(!jQuery(player_id).parents('.mg_box').hasClass('isotope-hidden')) {
						var delay = setInterval(function() {
							if(jQuery(player_id).parents('.mg_box').hasClass('mg_shown')) {
								player_obj.play();	
								clearInterval(delay);
							}
						}, 50);
					}
				}, 100);
			}
		}
	};


	// store player playlist and the currently played track - init player
	mg_audio_player = function(player_id, is_inline) {
		if(typeof(mg_audio_tracklists) == 'undefined') {
			mg_audio_tracklists = jQuery.makeArray(); // array of tracklists
			mg_player_objects = jQuery.makeArray(); // array of player objects
			mg_audio_is_playing = jQuery.makeArray(); // which track is playing for each player
		}
		
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_audio_player(player_id, is_inline);
			}, 50);
			return false;
		}
		
		// if has multiple tracks
		if(jQuery(player_id).find('source').length > 1) {

			mg_audio_tracklists[player_id] = jQuery.makeArray();
			jQuery(player_id).find('source').each(function(i, v) {
                mg_audio_tracklists[player_id].push( jQuery(this).attr('src') );
            });

			if(typeof(is_inline) == 'undefined') {
				var features = ['mg_prev','playpause','mg_next','current','progress','duration','mg_loop','volume','mg_tracklist'];
			} else {
				var features = ['mg_prev','playpause','mg_next','current','progress','mg_loop','volume','mg_tracklist'];
			}

			var success_function = function (player, domObject) {
				player.addEventListener('ended', function (e) {
					var player_id = '#' + jQuery(this).parents('.mg_me_player_wrap').attr('id');
					mg_audio_go_to(player_id, 'next', true);
				}, false);
			};
		}
		else {
			var features = ['playpause','current','progress','duration','mg_loop','volume'];
			var success_function = function() {};
		}


		// init
		var player_obj = new MediaElementPlayer(player_id+' audio',{
			audioVolume: 'vertical',
			startVolume: 1,
			features: features,
			success: success_function,
			alwaysShowControls: true
		});

		mg_player_objects[player_id] = player_obj;
		mg_audio_is_playing[player_id] = 0;

		// autoplay
		if(jQuery(player_id).hasClass('mg_audio_autoplay')) {
			player_obj.play();
		}
	};


	// go to track - prev / next / track_num
	mg_audio_go_to = function(player_id, direction, autonext) {
		var t_list = mg_audio_tracklists[player_id];
		var curr = mg_audio_is_playing[player_id];

		if(direction == 'prev') {
			var track_num = (!curr) ? (t_list.length - 1) : (curr - 1);
			var track_url = t_list[track_num];
			mg_audio_is_playing[player_id] = track_num;
		}
		else if(direction == 'next') {
			// if hasn't tracklist and loop is disabled, stop
			if(typeof(autonext) != 'undefined' && !jQuery(player_id+' .mejs-mg-loop-on').length) {
				return false;
			}

			var track_num = (curr == (t_list.length - 1)) ? 0 : (curr + 1);
			var track_url = t_list[track_num];
			mg_audio_is_playing[player_id] = track_num;
		}
		else {
			var track_url = t_list[(direction - 1)];
			mg_audio_is_playing[player_id] = (direction - 1);
		}

		// set player to that url
		var $subj = mg_player_objects[player_id];
		$subj.pause();
		$subj.setSrc(track_url);
		$subj.play();

		// set tracklist current track
		jQuery(player_id +' .mg_audio_tracklist li').removeClass('mg_current_track');
		jQuery(player_id +' .mg_audio_tracklist li[rel='+ (mg_audio_is_playing[player_id] + 1) +']').addClass('mg_current_track');
	};
	
	
	// init and slideUp inline player
	var mg_initSlide_inl_audio = function(player_id, autoplay) {
		mg_audio_player(player_id, true);
		jQuery(player_id).animate({bottom: 0}, 550);
		
		setTimeout(function() {
			jQuery(player_id).parents('.img_wrap').css('overflow', 'visible');
			jQuery(player_id).parents('.img_wrap').children().css('overflow', 'visible');
			
			mg_check_inl_audio_icons_vis();
			
			if(typeof(autoplay) != 'undefined') {
				var player_obj = mg_player_objects[player_id];
				player_obj.play();		
			}
		}, 550);
	};
	

	// add custom mediaelement buttons
	jQuery(document).ready(function(e) {
		mg_mediael_add_custom_functions();
	});
	
	var mg_mediael_add_custom_functions = function() {
		
		// wait until mediaelement script is loaded
		if(typeof(MediaElementPlayer) != 'function') {
			setTimeout(function() {
				mg_mediael_add_custom_functions();
			}, 50);
			return false;
		}
		
		
		// prev
		MediaElementPlayer.prototype.buildmg_prev = function(player, controls, layers, media) {
			var prev = jQuery('<div class="mejs-button mejs-mg-prev" title="previous track"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			// add a click toggle event
			.click(function() {
				var player_id = '#' + jQuery('#'+player.id).parent().attr('id');
				mg_audio_go_to(player_id, 'prev');
			});
		}

		// next
		MediaElementPlayer.prototype.buildmg_next = function(player, controls, layers, media) {
			var prev = jQuery('<div class="mejs-button mejs-mg-next" title="previous track"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			// add a click toggle event
			.click(function() {
				var player_id = '#' + jQuery('#'+player.id).parent().attr('id');
				mg_audio_go_to(player_id, 'next');
			});
		}

		// tracklist toggle
		MediaElementPlayer.prototype.buildmg_tracklist = function(player, controls, layers, media) {
			var tracklist =
			jQuery('<div class="mejs-button mejs-mg-tracklist-button ' +
				((jQuery('#'+player.id).parent().hasClass('mg_show_tracklist')) ? 'mejs-mg-tracklist-on' : 'mejs-mg-tracklist-off') + '" title="'+
				((jQuery('#'+player.id).parent().hasClass('mg_show_tracklist')) ? 'hide' : 'show') +' tracklist"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			// add a click toggle event
			.click(function() {
				if (jQuery('#'+player.id).find('.mejs-mg-tracklist-on').length) {
					jQuery('#'+player.id).parent().find('.mg_audio_tracklist').slideUp(300);
					tracklist.removeClass('mejs-mg-tracklist-on').addClass('mejs-mg-tracklist-off').attr('title', 'show tracklist');
				} else {
					jQuery('#'+player.id).parent().find('.mg_audio_tracklist').slideDown(300);
					tracklist.removeClass('mejs-mg-tracklist-off').addClass('mejs-mg-tracklist-on').attr('title', 'hide tracklist');
				}
			});
		}

		// loop toggle
		MediaElementPlayer.prototype.buildmg_loop = function(player, controls, layers, media) {
			var loop =
			jQuery('<div class="mejs-button mejs-mg-loop-button ' +
				((player.options.loop) ? 'mejs-mg-loop-on' : 'mejs-mg-loop-off') + '" title="'+
				((player.options.loop) ? 'disable' : 'enable') +' loop"><button type="button"></button></div>')
			// append it to the toolbar
			.appendTo(controls)
			// add a click toggle event
			.click(function() {
				player.options.loop = !player.options.loop;
				if (player.options.loop) {
					loop.removeClass('mejs-mg-loop-off').addClass('mejs-mg-loop-on').attr('title', 'disable loop');
				} else {
					loop.removeClass('mejs-mg-loop-on').addClass('mejs-mg-loop-off').attr('title', 'enable loop');
				}
			});
		}
	};


	// change track clicking on tracklist
	jQuery(document).ready(function(e) {
        jQuery('body').delegate('.mg_audio_tracklist li:not(.mg_current_track)', 'click'+mg_generic_touch_event, function() {
			var player_id = '#' + jQuery(this).parents('.mg_me_player_wrap').attr('id');
			var num = jQuery(this).attr('rel');

			mg_audio_go_to(player_id, num);
		});
    });


	// show&play inline audio on overlay click
	jQuery(document).ready(function(e) {
        jQuery('body').delegate('.mg_box.mg_inl_audio .overlays', 'click'+mg_generic_touch_event, function() {
			var $subj = jQuery(this).parents('.mg_box');
			jQuery(this).fadeOut(350);
			
			setTimeout(function() {
				$subj.find('.overlays').remove();
				$subj.addClass('mg_item_no_ol');
			}, 350);
			
			// slideup player or show soundcloud iframe
			if($subj.find('.mg_inl_audio_player').length) {
				var player_id = '#' + $subj.find('.mg_inl_audio_player').attr('id');
				mg_initSlide_inl_audio(player_id, true);
			} 
			else {
				$subj.find('.thumb').fadeOut(350);
				
				var sc_url = $subj.find('.mg_soundcloud_embed').attr('lazy-src');
				$subj.find('.mg_soundcloud_embed').attr('src', sc_url).removeAttr('lazy-src');
				
				setTimeout(function() {
					$subj.find('.thumb').remove();
					$subj.find('.mg_soundcloud_embed').css('z-index', 10);
				},350);
			}
		});
	});

	
	// pause inline players
	mg_pause_inl_players = function(grid_id, is_paginating) {
		if(typeof(is_paginating) != 'undefined') {var $subj =  jQuery('#'+ grid_id+' .mg_box');}
		else {var $subj = (typeof(grid_id) == 'undefined') ? jQuery('.mg_container .isotope-hidden') : jQuery('#'+ grid_id+' .isotope-hidden');}
		
		$subj.find('.mg_sh_inl_video, .mg_inl_audio_player').each(function() {
			if( typeof(mg_player_objects) != 'undefined' && typeof( mg_player_objects[ '#' + this.id ] ) != 'undefined') {
				var $subj = mg_player_objects[ '#' + this.id ];
				$subj.pause();
			}
		});	
	};

	
	// adjust players size
	var mg_adjust_inl_player_size = function(item_id) {
		var $subj = (typeof(item_id) != 'undefined') ? jQuery(item_id) : jQuery('.mg_inl_audio_player, .mg_sh_inl_video');
		mg_check_inl_audio_icons_vis();
		
		$subj.each(function() {
			if(typeof(mg_player_objects) != 'undefined' && typeof(mg_player_objects[ '#' + this.id ]) != 'undefined') {
				
				var player = mg_player_objects[ '#' + this.id ];
				player.setControlsSize();
			}
		});	
	};
	
	
	// hide audio player commands in tiny items
	var mg_check_inl_audio_icons_vis = function() {
		jQuery('.mg_inl_audio').not('.mg_pag_hide').each(function() {
			if( jQuery(this).find('.img_wrap').width() >= 195) {
				jQuery(this).find('.img_wrap > div').css('overflow', 'visible');	
			} else {
				jQuery(this).find('.img_wrap > div').css('overflow', 'hidden');	
			}
		});
	};
	

	/////////////////////////////////////
	// utilities

	function mg_responsive_txt(gid) {
		var selector = (typeof(gid) != 'undefined') ? '#'+gid+' ' : '';
		var $subj = jQuery(selector + '.mg_inl_txt_td').not('.mg_inl_txt_no_resize').find('p, b, div, span, strong, em, i, h6, h5, h4, h3, h2, h1');

		// setup original text sizes and reset
		$subj.each(function() {
			if(typeof( jQuery(this).attr('orig-size') ) == 'undefined') {
				jQuery(this).attr('orig-size', jQuery(this).css('font-size'));
				jQuery(this).attr('orig-lheight', jQuery(this).css('line-height'));
			}

			// reset
			jQuery(this).removeClass('mg_min_reached mg_inl_txt_top_margin_fix mg_inl_txt_btm_margin_fix mg_inl_txt_top_padding_fix mg_inl_txt_btm_padding_fix');
			jQuery(this).css('font-size', jQuery(this).attr('orig-size'));
			jQuery(this).css('line-height', jQuery(this).attr('orig-lheight'));
        });

		jQuery(selector + '.mg_inl_txt_td').each(function() {
			// not for auto-height
			if(
				(!mg_mobile_mode && !jQuery(this).parents('.mg_box').hasClass('rowauto')) ||
				(mg_mobile_mode && !jQuery(this).parents('.mg_box').hasClass('m_rowauto'))
			) {
				var max_height = jQuery(this).parents('.img_wrap').height();

				if(max_height < jQuery(this).outerHeight()) {
					var a = 0;
					while( max_height < jQuery(this).outerHeight()) {
						if(a == 0) {
							// check and eventually reduce big margins and paddings at first
							$subj.each(function(i, v) {
								if( parseInt(jQuery(this).css('margin-top')) > 10 ) {jQuery(this).addClass('mg_inl_txt_top_margin_fix');}
								if( parseInt(jQuery(this).css('margin-bottom')) > 10 ) {jQuery(this).addClass('mg_inl_txt_btm_margin_fix');}

								if( parseInt(jQuery(this).css('padding-top')) > 10 ) {jQuery(this).addClass('mg_inl_txt_top_padding_fix');}
								if( parseInt(jQuery(this).css('padding-bottom')) > 10 ) {jQuery(this).addClass('mg_inl_txt_btm_padding_fix');}
							});
						}
						else {
							$subj.each(function(i, v) {
								var new_size = parseFloat( jQuery(this).css('font-size')) - 1;
								if(new_size < 12) {new_size = 12;}

								var new_lheight = parseInt( jQuery(this).css('line-height')) - 1;
								if(new_lheight < 15) {new_lheight = 15;}

								jQuery(this).css('font-size', new_size).css('line-height', new_lheight+'px');

								if(new_size == 12 && new_lheight == 15) { // resizing limits
									jQuery(this).addClass('mg_min_reached');
								}
							});

							// if any element has reached min size
							if( jQuery(selector + '.mg_inl_txt_td .mg_min_reached').length ==  $subj.length) {
								return false;
							}
						}

						a++;
					}
				}
			}
        });
	};



	// check for touch device
	function mg_is_touch_device() {
		return !!('ontouchstart' in window);
	};


	// check if the browser is IE8 or older
	function mg_is_old_IE() {
		if( navigator.appVersion.indexOf("MSIE 8.") != -1 ) {return true;}
		else {return false;}
	};

	// check if mobile browser
	function mg_is_mobile() {
		if( /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent) )
		{ return true;}
		else { return false; }
	};

})(jQuery);


/////////////////////////////////////
// Image preloader v1.01
(function($) {
	$.fn.lcweb_lazyload = function(lzl_callbacks) {
		lzl_callbacks = jQuery.extend({
			oneLoaded: function() {},
			allLoaded: function() {}
		}, lzl_callbacks);

		var lzl_loaded = 0,
			lzl_url_array = [],
			lzl_width_array = [],
			lzl_height_array = [],
			lzl_img_obj = this;

		var check_complete = function() {
			if(lzl_url_array.length == lzl_loaded) {
				lzl_callbacks.allLoaded.call(this, lzl_url_array, lzl_width_array, lzl_height_array);
			}
		};

		var lzl_load = function() {
			jQuery.map(lzl_img_obj, function(n, i){
                lzl_url_array.push( $(n).attr('src') );
            });

			jQuery.each(lzl_url_array, function(i, v) {
				if( jQuery.trim(v) == '' ) {console.log('empty img url - ' + (i+1) );}

				$('<img />').bind("load.lcweb_lazyload",function(){
					if(this.width == 0 || this.height == 0) {


						setTimeout(function() {
							lzl_width_array[i] = this.width;
							lzl_height_array[i] = this.height;

							lzl_loaded++;
							check_complete();
						}, 70);
					}
					else {
						lzl_width_array[i] = this.width;
						lzl_height_array[i] = this.height;
						lzl_loaded++;
						check_complete();
					}
				}).attr('src',  v);
			});
		};

		return lzl_load();
	};
})(jQuery);