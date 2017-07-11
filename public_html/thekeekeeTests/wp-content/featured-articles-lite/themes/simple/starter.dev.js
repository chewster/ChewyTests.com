;(function($){	
	$(document).ready( function(){
		$('.fa_slider_simple').FeaturedArticles({
			slide_selector 	: '.fa_slide',
			nav_prev 		: '.go-back',
			nav_next		: '.go-forward',
			nav_elem		: '.main-nav .fa-nav',			
			effect	: false,
			// events
			load	: load,
			before	: before,
			after	: after,
			resize	: resize,
			stop	: stop,
			start	: start
		});		
	});
	
	var resizeDuration = 100;
	
	var load = function(){
		var options = this.settings(),
			self = this;
		this.progressBar = $(this).find('.progress-bar');
		this.mouseOver;
		
		// height resize
		if( $(this).data('theme_opt_auto_resize') ){			
			this.sliderHeight = $(this).height();
			var h 			= $( this.slides()[0] ).find(options.content_container).outerHeight() + 100;
				setHeight 	= h > this.sliderHeight ? h : this.sliderHeight;
				slide		= this.slides()[0];
			
			$(slide).css({
				'height' : setHeight
			});	
			self.center_img( slide );
			
			$(this)
				.css({
					'max-height':'none',
					'height' : this.sliderHeight
				})
				.animate({
					'height' : setHeight
					},{ 
					queue: false, 
					duration:resizeDuration , 
					complete: function(){
						/*
						$(this).css({
							'max-height':setHeight
						});
						*/						
					}
				});// end animate
		}// end height resize
	}
	
	var before = function(d){
		var options = this.settings(),
			self = this;
		if( typeof this.progressBar !== 'undefined' ){
			this.progressBar.stop().css({'width':0});
		}
		// height resize
		if( $(this).data('theme_opt_auto_resize') ){
			var h 		  = $( d.next ).find(options.content_container).outerHeight() + 100,
				setHeight = h > this.sliderHeight ? h : this.sliderHeight;
			
			$(d.next).css({
				height : setHeight
			});	
			self.center_img( d.next );	
						
			$(this)
				.css({
					'max-height':'none'
				})
				.animate({
					'height' : setHeight
					},{ 
					queue: false, 
					duration:resizeDuration , 
					complete: function(){
						$(this).css({'max-height':setHeight});
					}
				});// end animate
		}
		// end height resize
	}
	
	var resize = function(){		
		var self = this,
			options = this.settings();		
		// height resize
		if( $(this).data('theme_opt_auto_resize') ){
			var h = $( this.get_current() ).find(options.content_container).outerHeight() + 100;
			this.sliderHeight = $(this).height();;
			
			var setHeight = h > this.sliderHeight ? h : this.sliderHeight;
			
			$( this.get_current() ).css({
				height: setHeight
			});
			self.center_img( self.get_current() );
			
			$(this)
				.css({
					'max-height':'none',
					'height':this.sliderHeight
				})
				.animate({
					'height' : setHeight
					},{ 
					queue: false, 
					duration:resizeDuration , 
					complete: function(){
						$(this).css({'max-height':setHeight});
					}
				});
		}
		// end height resize		
	}
	
	var after = function(){
		var options 	= this.settings(),
			self 		= this,
			duration 	= options.slide_duration;
		
		//self.center_current_img();
		
		if( this.mouseOver || this.stopped || !options.auto_slide ){
			return;			
		}
		
		if( typeof this.progressBar !== 'undefined' ){
			this.progressBar.css({width:0}).animate(
				{'width' : '100%'},
				{duration: duration, queue:false, complete: function(){
					$(this).css({'width':0});
				}
			});
		}	
	}
	
	var stop = function(){
		if( typeof this.progressBar !== 'undefined' ){
			this.progressBar.stop().css({'width':0});
		}	
		this.mouseOver = true;			
	}
	
	var start = function(){
		this.mouseOver = false;
		if( this.animating() ){
			return;
		}
		
		var options 	= this.settings(),
			duration 	= options.slide_duration;		
		
		if( typeof this.progressBar !== 'undefined' ){
			this.progressBar.css({width:0}).animate(
				{'width' : '100%'},
				{duration: duration, queue:false, complete: function(){
					$(this).css({'width':0});
				}
			});
		}	
	}
	
})(jQuery);	