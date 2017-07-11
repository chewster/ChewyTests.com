/**
 * 
 */
;(function($){
	$.fn.FeaturedArticles = function( options ){
		// support mutltiple elements
       	if ( this.length > 1 ){
            this.each(function() { 
				$(this).FeaturedArticles( options );				
			});
			return this;
        }
		
       	if( this.length < 1 ){
       		return;
       	}
       	
       	/**
       	 * The plugin defaults
       	 */
       	var defaults = {
       		cycle : true, // when true, side navigation will restart when reaching the end
       		effect_duration : 900, // duration of transition effect
       		slide_duration	: 5000, // duration of slide when auto_slide=true
       		auto_slide		: false, // slide automatically
       		click_stop		: false, //
       		position_in		: 'left', 	// position slides enter from
			distance_in		: 0, 		// distance slides enter from
			position_out	: 'right', 	// position slides exit to
			distance_out	: 0, 		// distance slides exit to	
			animate_opacity : true,
			event			: 'click', // mouse event
			effect			: false, // some sliders may use slide effects ( see get_effect() )
			/* Responsive */
			width 		: 0,
			height 		: 0,
			fullwidth 	: false,
			height_resize : true, // allow height resize for full width sliders
			font_size	: 100, // percentual font size
			is_mobile	: false,
			/* Video */
			video_container : 'div.fa-video',
			play_video		: '.play-video',
			image_container	: '.fa-image-container', // must be specified when slide image covers full background ( full_image : true )
			content_container : '.fa_slide_content', // must be specified when aplying effect ( param effect )
			_image			: '.main-image', // the image selector (shouldn't have to be specified since it's implemented by the PHP script)
       		full_image		: false, // when true, the script will do image resizing to cover the full slider
			/* Selectors */
       		nav_prev 		: '.prev', // previous slide navigation element selector	
       		nav_next		: '.next', // next slide navigation element selector
       		nav_elem		: '.nav', // single slide navigation element selector
       		slide_selector 	: '.slide', // slide element selector
       		/* Events */
       		begin	: function(){}, // triggered first
       		load	: function(){}, // triggered after all script is processed
       		stop	: function(){}, // triggered when autoslide is stopped
       		start	: function(){}, // triggered when autoslide starts 
       		before	: function(){}, // triggered before the slide change animation begins
       		after	: function(){}, // triggered after the slide change animation is done
       		change	: function(){},
       		video_status : function(){}, // returns the status of the video as it changes during playback
       		resize	: function(){}
       	};
       	
       	if (!$.support.transition){
       		$.fn.transition = $.fn.animate;
       	}
       	       	
       	// durations are in seconds, we need them in milliseconds
       	var data = $(this).data();
       	data.slide_duration *= 1000;
       	data.effect_duration *= 1000;
       	
       	// various variables
       	var self 	= this,
       		slides, // store found slides
       		navs, // navigation elements
       		current, // store current slide key
       		animating, // an animation is running while this is true
       		options = $.extend( {}, defaults, data, options ), // plugin options
       		cycle = options.cycle, // stores cycle option
       		timer,// stores the timer for auto slide
       		auto = options.auto_slide,
       		pause = false,
       		modal_open = false,
       		touch = ("ontouchstart" in window) || window.DocumentTouch && document instanceof DocumentTouch,
       		nav_event = (touch) ? "touchend" : "click",
       		video_status; 
       	
       	/**
       	 * Start the script
       	 */
       	var init = function(){
       		// if animate opacity is off and no effect is specified, make opacity run by default
       		if( !options.animate_opacity && !options.effect ){
       			options.animate_opacity = true;
       		}
       		
       		var ratio = options.width / options.height,
			width = options.fullwidth ? '100%' : options.width;		
			$(self).css({
				'width' : width
			});
			// get the slides
       		slides 	= $(self).find( options.slide_selector );
       		// set the aspect
       		set_aspect();       		
       		/**
       		 * Slider begin event
       		 */
       		options.begin.call( self, options );
       		
       		if( 0 == options.effect_duration ){
       			options.effect_duration = 10; // set to 10 milliseconds if animation is 0
       		}
       		
       		// set navigation
       		navigation();
       		// set current slide
       		set_current(0);     
       		// prepare slides
       		slides_prepare();
       		
       		// if less than 2 slides, no need for a slider
       		if( slides.length > 1 ){
       			// start autoslide (if enabled)
           		set_timer( true ); 
       		}       		
       		
       		// change aspect on window resize (part of the responsive functionality)
       		$(window).resize( on_resize );
       		
       		/**
       		 * Slider load event
       		 */
       		options.load.call( self, options );
       		
       		if( touch ){       			
       			$(self).swiperight(function(){
       				goto_prev();
       			}).swipeleft(function(){
       				goto_next();
       			});
       		}
       		
       		// for preloaded sliders
       		$(self).removeClass('slider-loading').children().show();
       		
       		return self;
       	}
       	
       	var on_resize = function(){
       		set_aspect();
       		/**
			 * Resize event callback
			 */
			options.resize.call( self, slides );
       	}
       	
       	/**
       	 * Calculate height and font size from a set of given dimensions.
       	 */ 
		var set_aspect = function(){
			var ratio 		= options.width / options.height,
				currWidth 	= $(self).width(),
				resizeRatio = ( currWidth / options.width ) * 100;
			
			if( currWidth > options.width ){
				if( !options.height_resize ){
					resizeRatio = 100;
				}
			}	
			// don't allow font size over 100% in size
			var	font_size 	= options.font_size * resizeRatio / 100;
			if( font_size > 100 ){
				font_size = 100;
			}
			
			var css = {
				'font-size' : font_size + '%',
				'height'	: ( currWidth / ratio )
			};	
			if( !options.height_resize ){
				css['max-height'] = options.height;
				
			}
			$(self).css(css);			
			
			
			// set images to cover the whole background
			//if( options.full_image && slides ){
				$.each( slides, function(){
					var img = $(this).find( options.image_container +' '+ options._image );
					center_image( img );					
				});
			//}
			
			// process current slide first
			var current = get_current();
			if( current ){
				var img 	= $(current).data( 'orig_img' ),
					imgs 	= $(current).data( 'img_pieces' );
				
				if( img ){
					img.show();
				}
				if( imgs ){
					$.each(imgs, function(){
						$(this).remove();
					});
					$(current).removeData('img_pieces');
				}
				
				// if animating, change current slide z-index to cover all
				if( animating ){
					$(current).css({
						'z-index' : 2
					});
				}
				
			}
			// process all slides
			$.each( slides, function( i ){
				var img 	= $(this).data( 'orig_img' ),
					imgs 	= $(this).data( 'img_pieces' );
				if( img ){
					img.show();
				}
				if( imgs ){
					$.each(imgs, function(){
						$(this).remove();
					});
					$(this).removeData('img_pieces');
				}
			});			
		}
       	
		/**
		 * Centers images inside their container, both vertically and horizontally
		 */
		var center_image = function( img ){
			var	w = img.data('width'),
				h = img.data('height'),
				image_prop = w/h,
				s_w = $(self).width(),
				s_h = $(self).height(),
				slider_prop = s_w / s_h;
			
			if( !options.full_image ){
				s_w = img.parents( options.image_container ).width();
				s_h = img.parents( options.image_container ).height();	
				slider_prop = s_w / s_h;
			}
			
			if( image_prop > slider_prop ){				
				$(img).css({
					'width' : 'auto',
					'max-width' : 'none',
					'height' : '100%',
					'max-height' : '100%'							
				});
				
				var img_width = $(img).width();
				
				if( 0 == img_width ){
					$(img).load( function(){
						$(img).css({
							'margin-left' : - ( ( $(img).width()  -  $(self).width() )/2 ),
							'margin-top' : 0
						});
					});
				}else{
					$(img).css({
						'margin-left' : - ( ( img_width  -  s_w )/2 ),
						'margin-top' : 0
					});
				}				
			}else{				
				$(img).css({
					'width' : '100%',
					'max-width' : '100%',
					'height' : 'auto',
					'max-height' : 'none'
				});
				
				var img_height = $(img).height();
				
				if( 0 == img_height ){
					$(img).load( function(){
						$(img).css({
							'margin-top' : - ( ( $(img).height()  -  $(self).height() )/2 ),
							'margin-left' : 0
						});
					});
				}else{
					$(img).css({
						'margin-top' : - ( ( img_height  -  s_h )/2 ),
						'margin-left' : 0
					});
				}
			}			
		}
		
       	/**
       	 * Sets up the navigation, both prev/next and individual elements
       	 */
       	var navigation = function(){
       		// previous slide nav element
       		if( options.nav_prev ){
       			var nav_prev = $(self).find( options.nav_prev );
       			nav_prev.bind( nav_event, function(e){
       				e.preventDefault();
       				if( options.click_stop ){
       					stop_auto_slide();
       				}
       				goto_prev();
       			});       			
       		}
       		// next slide nav element
       		if( options.nav_next ){
       			var nav_next = $(self).find( options.nav_next );
       			nav_next.bind( nav_event, function(e){
       				e.preventDefault();
       				if( options.click_stop ){
       					stop_auto_slide();
       				}
       				goto_next();
       			});
       		}
       		
       		if( options.nav_elem ){
       			navs = $(self).find( options.nav_elem );
       			$.each( navs, function( i ){
       				$(this).bind( nav_event, function(e){
       					e.preventDefault();
       					if( options.click_stop ){
           					stop_auto_slide();
           				}
       					goto_index( i );
       				});
       			})       			
       		}
       	}
       	
       	/**
       	 * Navigation next slide function
       	 */
       	var goto_next = function(){
       		var index = get_np_index( 1 );
       		goto_index( index );
       	}
       	
       	/**
       	 * Navigation prev slide function
       	 */
       	var goto_prev = function(){
       		var index = get_np_index( -1 );
       		goto_index( index );
       		
       	}
       	
       	/**
       	 * Set up the slides CSS and other on initialization
       	 */
       	var slides_prepare = function(){
       		var styles = {
				'position'	: 'absolute',
				'top'		: 0,
				'left'		: 0,
				'z-index'	: 1
			};
			var visible = {
				'z-index' : 2
			}
			
			if( options.effect_duration > 0 ){
				if( options.animate_opacity ){
					styles.opacity = 0;
					visible.opacity = 1;
				}
				visible.filter = 1;
			}			
			slides.css( styles );
			$( get_current() ).css( visible );
       		
			/**
			 * Load assets
			 */
			$.each( slides, function( i, slide ){
				
				// preload images
				var img_container = $(this).find( options.image_container );
				if( img_container.length > 0 ){
					var data = $(img_container).data();						
					var	img = $('<img />',{
						'src' 	: data.image,
						'class'	: data.image_class,
						'data-width' : data.width,
						'data-height': data.height
					});					
					$(img).load(function(){
						var preloader = $(img_container).find('img');
						$(this).insertAfter( $(preloader) );
						$(preloader).remove();
						center_image( $(this) );					
					});															
				}
				
				// prepare video player
				var player_trigger 	= $(this).find( options.play_video ),
					open_video		= $(player_trigger).data('open_video'),
					player_container = $(this).find( options.video_container );
				
				if( player_container.length < 1 ){
					return;
				}
				
				var	image	= $(this).find( options.image_container ),
					data	= $(player_container).data(),
					width	= data.width,
					height;
				
				//*				
				// calculate the video width based on height
				switch( data.aspect ){
					case '16x9':
					default:
						height = (width *  9) / 16;
					break;
					case '4x3':
						height = (width *  3) / 4;
					break;
				}
				
				// begin dialog *****************************
				if( open_video && 'modal' == open_video ){
					var dialog = $(slide).find( options.video_container ).dialog({
						autoOpen 	: false,
						width 		: width,
						height		: height,
						maxWidht	: width,
						maxHeight	: height,
						draggable	: false,
						closeOnEscape	: true,
						resizable	: false,
						modal		: true,
						dialogClass	: 'fa-video-modal',
						close		: function(){
							// pause video when closing modal
							$(slide).data( 'player' ).pause();
							modal_open = false;
							set_timer();
						},
						open : function( event, ui ){
							// play video
							if( !$(slide).data('player') ){	
								var player = $(this).FA_VideoPlayer({
									stateChange	: function( status ){
										//check if video is playing
										if( i == current ){
											//
											// Video status change event
											//
											options.video_status.call( self, status );
											if( 1 == status ){
												if( !options.is_mobile ){
													this.play();
												}
											}
										}
										set_video_status( status );
										if( 4 == status ){
											dialog.dialog('close');
										}								
									}
								});
								// store the player on slide
								$(slide).data( 'player', player );
								
							}else{							
								// video is loaded, just play it
								$(slide).data( 'player' ).resizePlayer();
								$(slide).data( 'player' ).play();
							}
							modal_open = true;
						}
					});				
				}
				// end dialog *******************************
				//*/
				$(this).find( options.play_video ).bind( nav_event, function(event){
					event.preventDefault();
					
					stop_auto_slide();
					
					//*
					if( open_video && 'modal' == open_video ){
						dialog.dialog('open');
						return;
					}
					//*/
					
					$(player_container).show();
					$(image).hide();
					
					// play video
					if( !$(slide).data('player') ){						
						var player = $(player_container).FA_VideoPlayer({
							stateChange	: function( status ){
								//check if video is playing
								if( i == current ){
									/**
									 * Video status change event
									 */
									options.video_status.call( self, status );
									if( 1 == status ){
										if( !options.is_mobile ){
											this.play();
										}
									}
								}
								set_video_status( status );
								if( 4 == status ){
									player.hide();
									image.show();
								}								
							}
						});
						// store the player on slide
						$(slide).data( 'player', player );
						
					}else{
						// video is loaded, just play it
						$(slide).data( 'player' ).play();
					}					
				});// click				
			});// each			
       	}
       	
       	/**
       	 * Set up auto sliding if enabled
       	 */
       	var set_timer = function( first ){
       		if( !auto || pause || 2 == get_video_status() || modal_open ){
       			return;
       		}
       		clear_timer();
       		var duration = options.slide_duration;       		
       		timer = setTimeout( goto_next , duration );
       		
       		/**
       		 * Start slider event
       		 */
       		options.start.call( self );
       		
       		// set timer remove when hovering slider
       		if( first ){
       			$(self)
       				.mouseenter(function(){
       					pause = true;
       					clear_timer();
       				})
       				.mouseleave(function(){
       					pause = false;
       					set_timer();
       				});
       		}       		
       	}
       	
       	/**
       	 * Loads the slide specified by index
       	 */
       	var goto_index = function( index ){
       		// index can be returned false if cycle is off and the end is reached
       		// in this case, do nothing;
       		if( ( !index && 0 !== index ) || animating || index == current ){
       			if( false === index ){
       				stop_auto_slide();
       			}       			
       			return;
       		}
       		
       		stop_video();       		
       		animating = true;
       		
       		/**
       		 * Before animation event
       		 */
       		var event_data = {
       			current : get_current(),
       			next	: get_slide( index ),
       			current_index : current,
       			next_index	  : index
       		};
       		options.before.call( self, event_data );
       		
       		// if effects are enabled, apply the effect on current slide
       		var slide_effect = apply_slide_effect();
       		
       		// set animations
       		var curr_styles = animation_styles( 'current' ),
       			next_styles	= animation_styles( 'next' ),
       			current_slide 	= get_current(),
       			next_slide		= get_slide( index );
       		
       		// center next slide image
       		var img = $(next_slide).find( options.image_container +' '+ options._image );
       		if( img.length > 0 ){
       			center_image( img );
       		}
       		
       		// previous slide animation
       		$( current_slide ).css( curr_styles.css ).transition( 
       			curr_styles.animation, 
       			options.effect_duration, 
       			function(){
       				$( this ).css({ 'z-index': 1 });       				
       			}       			
       		);
       		// next slide animation
       		$( next_slide ).css( next_styles.css ).transition( 
       			next_styles.animation,
       			options.effect_duration,
       			function(){
           			animating = false;
           			set_timer();
           			$( this ).css({ 'z-index': 2 });
           			if( slide_effect ){
           				reset_effect( event_data.current );
           			}           			
           			/**
           			 * After animation event
           			 */
           			options.after.call( self, event_data );
           		}
       		);
       		
       		// add class "current" on slide
       		$(slides[current]).removeClass('current');
       		$(slides[index]).addClass('current');
       		// add class "active" on corresponding nav element
       		$( navs[ current ] ).removeClass('active');
			$( navs[ index ] ).addClass('active');
       		
			// finally, set the next index
       		set_current( index );
       		
       		options.change.call(self, event_data);
       	}
       	
       	/**
       	 * Applies slide effect on current slide if set
       	 */
       	var apply_slide_effect = function(){
       		var slide 	= get_current(),
       			e 		= $(slide).data('effect') || options.effect;
       		// if effect isn't set on slide or options, stop here
       		if( !e || !get_effect( e ) ){
       			return false;
       		}
       		
       		var pieces = $(slide).data('img_pieces');
       		if( !pieces ){
       			var img = $(slide).find( '.fa_slide_image.main-image' );
       			if( img.length < 1 ){
       				return false;
       			}
       			// create the image pieces
       			pieces = prepare_effect( img, effect );
       			if( !pieces ){
       				return false;
       			}       			
       			// store on slide
       			$(slide).data('img_pieces', pieces);
       			$(slide).data('orig_img', img);
       			// insert pieces
       			$.each( imgs, function(){
    				$(this).insertBefore( $(img) );
    			});
    			$(img).hide();       			
       		}
       		
       		var duration 	= options.effect_duration / pieces.length,
       			effect		= get_effect( e );
       		
       		$.each( pieces, function(index){
       			if( !effect.delayed ){
       				var dur = options.effect_duration;
       			}else{
       				if( effect.reversed ){
       					var i = pieces.length - index;
       				}else{
       					var i = 0 == index ? 1 : index;
       				}
       				var dur = i * duration;       				
       			}
       			
       			$(this).transition(
       				effect.animation,
       				dur,
       				function(){
       					// animation complete callback
       				}
       			);       			
       		});// $.each 
       		
       		$( slide ).find( options.content_container ).transition(
       			{opacity:0},
       			( options.effect_duration / 2 )
       		) 
       		return true;
       	}
       	
       	/**
       	 * Reset the effect when animation done
       	 */
       	var reset_effect = function( slide ){
       		// reset the clipped images
    		var img = $( slide ).data('img_pieces');
    		if( img ){
    			$.each( img, function(){
    				$(this).css({
    					opacity	: 1,					
    					scale	: 1,
    					rotate	: 0,
    					skewX	: 0,
    					skewY	: 0,
    					x		: 0,
    					y		: 0
    				});
    			});
    		}
    		// reset the content
    		$( slide ).find( options.content_container ).css({
    			opacity:1
    		});
       	}
       	
       	/**
       	 * Creates the image clippings for the effect
       	 */
       	var prepare_effect = function( img, effect ){
       		var e = get_effect( effect ),
				cols = e.cols,
				rows = e.rows
				w	 = $(img).width(),
				h 	 = $(img).height(), 
				pieceW	= w / cols;
				pieceH	= h / rows,
				imgs = [];
		
			for( var x = 0; x < cols; x++ ){
				for( var y = 0; y < rows; y++ ){
					var top 	= y * pieceH,
						right 	= ( x + 1 ) * pieceW,
						bottom 	= ( y + 1 ) * pieceH,
						left 	= x * pieceW,
						originY = top + ( pieceH / 2 ),
						originX = left + ( pieceW / 2 );
					
					var c = $(img).clone().css({
						clip : 'rect(' + top + 'px, ' + right + 'px, ' + bottom + 'px, ' + left + 'px)',
						transformOrigin : originX + 'px ' + originY + 'px'
					}).removeClass('main-image');
					imgs.push( c );
				}			
			}		
			return imgs;
       	}
       	
       	/*********
       	 * Helpers
       	 *********/
       	
       	/**
       	 * Returns the current active slide
       	 */
       	var get_current = function(){
       		if( !current && 0 !== current ){
       			return false;
       		}
       		return slides[ current ];
       	}
       	
       	/**
       	 * Returns the element of a given slide index
       	 */
       	var get_slide = function( index ){
       		return slides[ index ];
       	}
       	
       	/**
       	 * Set the current slide index
       	 */
       	var set_current = function( index ){
       		if( current ){
       			$( navs[ current ] ).removeClass('active');
           		$(slides[current]).removeClass('current');
       		}
       		
       		$( navs[ index ] ).addClass('active');
       		$(slides[index]).addClass('current');
       		
       		current = index;
       	}
       	
       	/**
       	 * Get the next or previous slide index
       	 * 
       	 * @param int direction - 1 = get next slide index; -1 = get previous slide index
       	 */
       	var get_np_index = function( direction ){
       		var total = slides.length,
       			index = current + direction;
       		
       		if( index < 0 ){
       			index = cycle ? ( total - 1 ) : 0;
       		}else if( index >= total ){
       			index = cycle ? 0 : total - 1;
       		}
       		
       		if( current == index ){
       			return false;
       		}
       		
       		return index;
       	}
       	
       	/**
       	 * Clear the timer
       	 */
       	var clear_timer = function(){
       		if( timer ){
	       		clearTimeout( timer );
	       		timer = false;
	       		/**
	           	 * Stop slider event
	           	 */
	           	options.stop.call( self );  
       		}
       	}
       	
       	/**
       	 * Stops auto sliding
       	 */
       	var stop_auto_slide = function(){
       		clear_timer();
       		auto = false;
       	}
       	
       	/**
       	 * Store the video status
       	 */
       	var set_video_status = function( status ){
       		video_status = status;
       		if( 2 != status && 1 != status ){
       			auto = options.auto_slide;
       			set_timer();
       		}else{
       			stop_auto_slide();
       		}
       	}
       	
       	/**
       	 * Stops the current video
       	 */
       	var stop_video = function(){
       		var slide = get_current();
       		if( slide ){
       			var player = $(slide).data('player');
       			if( player ){
       				player.pause();
       			}
       		}
       	}
       	
       	/**
       	 * Get video status
       	 */
       	var get_video_status = function(){
       		return video_status;       		
       	}
       	
       	/**
       	 * Effects
       	 */
       	var get_effect = function( effect ){
       		var effects = {
   				squares : {
   					cols 		: 20,
   					rows		: 5,
   					animation 	: {opacity : 0, scale : 0, y : -60, x : -60},
   					delayed		: true,
   					reversed	: true
   				},
   				zipper : {
   					cols 		: 50,
   					rows 		: 1,
   					animation	: {opacity: 0, scale: 0},
   					delayed		: true
   				},
   				ripple : {
   					cols 		: 50,
   					rows 		: 1,
   					animation	: {opacity: 0, scale: -2},
   					delayed		: true
   				},
   				fade : {
   					cols 		: 50,
   					rows		: 1,
   					animation	: {opacity: 0},
   					delayed		: true
   				},
   				simple_squares : {
   					cols		: 20,
   					rows		: 5,
   					animation 	: { opacity: .5, scale: 0 },
   					delayed		: false
   				},
   				// new
   				flip : {
   					cols	: 50,
   					rows	: 1,
   					animation : { rotate:360, opacity:0 },
   					delayed : true,
   					reversed : false
   				}, 
   				wave : {
   					cols	: 30,
   					rows	: 3,
   					animation : { scale:3, opacity:0 },
   					delayed : true,
   					reversed : false
   				},
   				horizontal_slices : {
   					cols	: 1,
   					rows	: 10,
   					animation : { x : -$(self).width() },
   					delayed : true,
   					reversed : true
   				},
   				vertical_slices : {
   					cols	: 10,
   					rows	: 1,
   					animation : { y : $(self).height() },
   					delayed : true,
   					reversed : false
   				},  
   				
   				// @todo - remove this
   				test : {
   					cols	: 1,
   					rows	: 1,
   					animation : { x: $(this).width() },
   					delayed : true,
   					reversed : false
   				}
   			};
       		
       		var e = effect ? effect : options.effect;
       		return effects[e];
       	}
       	
       	/**
       	 * Slide animation styles
       	 */
       	var animation_styles = function( which ){
       		
       		var styles = {
       			'css' : {
       				'z-index' 	: 1,
       				'display' 	: 'block',
       				'visibility': 'visible'
       				
       			},
       			'animation' : {}
       		};
       		
       		switch( which ){
       			case 'current':
       				// css
       				styles.css['z-index'] = 2;
       				if( options.animate_opacity ){
       					styles.animation.opacity = 0;
       				}
       				switch( options.position_out ){
	    				case 'top':
	    				case 'left':
	    					styles.css[ options.position_out ] = 0;
	    					styles.animation[ options.position_out ] = -options.distance_out;    					
	    				break;	
	    				case 'bottom':
	    					styles.css.top = 0;
	    					styles.animation.top = options.distance_out;	    					
	    				break;
	    				case 'right':
	    					styles.css.left = 0;
	    					styles.animation.left = options.distance_out;
	    				break;	
	    			}
       				
       			break;
       			case 'next':
       				// reset out styles
       				switch( options.position_out ){
       					case 'top':
       					case 'left':
       						styles.css[ options.position_out ] = 0;
       					break;
       					case 'bottom':
       						styles.css.top = 0;
       					break;	
       					case 'right':
       						styles.css.left = 0;
       					break;	
       				}
       				
       				switch( options.position_in ){
	    				case 'top':
	    				case 'left':
	    					styles.animation[ options.position_in ] 	= 0;
	    					styles.css[ options.position_in ] 		= -options.distance_in;
	    				break;	
	    				case 'bottom':
	    					styles.animation.top 	= 0;
	    					styles.css.top 			= options.distance_in;
	    				break;
	    				case 'right':
	    					styles.animation.left 	= 0;
	    					styles.css.left 		= options.distance_in;
	    				break;	
	    			}
       				
       				if( options.animate_opacity ){
       					styles.animation.opacity = 1;
       				}
       			break;       		
       		} 
       		
       		return styles;
       	}
       	
       	/**
       	 * Returns true if a slider change animation is running
       	 */
       	this.animating = function(){
       		return animating;
       	}
       	
       	/**
       	 * Returns the plugin options
       	 */
       	this.settings = function(){
       		return options;
       	}
       	
       	/**
       	 * Returns all slides
       	 */
       	this.slides = function(){
       		return slides;
       	}
       	
       	/**
       	 * Return current slide
       	 */
       	this.get_current = function(){
       		return get_current();
       	}
       	
       	this.get_current_index = function(){
       		return current;       		
       	}
       	
       	this.goto_index = function( index ){
       		goto_index( index );
       	}
       	
       	this.get_navs = function(){
       		return navs;
       	}
       	
    	this.center_img = function( el ){
       		var img = $( el ).find( options.image_container +' '+ options._image );
       		center_image( img );
       	}
       	
       	return init();
	}	
})(jQuery);