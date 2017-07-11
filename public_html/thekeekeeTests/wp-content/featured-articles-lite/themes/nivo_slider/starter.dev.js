;(function($){
	
	$(window).load( function(){
		var sliders = $('.fa-nivo-slider-wrapper');
		$.each( sliders, function(){
			// nivo slider defaults
			var options = {
				effect: 'random',               // Specify sets like: 'fold,fade,sliceDown'
			    slices: 15,                     // For slice animations
			    boxCols: 8,                     // For box animations
			    boxRows: 4,                     // For box animations
			    animSpeed: 500,                 // Slide transition speed
			    pauseTime: 3000,                 // How long each slide will show
			    startSlide: 0,                     // Set starting Slide (0 index)
			    directionNav: true,             // Next & Prev navigation
			    controlNav: true,                 // 1,2,3... navigation
			    controlNavThumbs: false,         // Use thumbnails for Control Nav
			    pauseOnHover: true,             // Stop animation while hovering
			    manualAdvance: true,             // Force manual transitions
			    prevText: '&laquo;',                 // Prev directionNav text
			    nextText: '&raquo;',                 // Next directionNav text
			    randomStart: false,             // Start on a random slide
			    // events
			    beforeChange: function(){},     // Triggers before a slide transition
			    afterChange: function(){},         // Triggers after a slide transition
			    slideshowEnd: function(){},     // Triggers after all slides have been shown
			    lastSlide: function(){},         // Triggers when last slide is shown
			    afterLoad: function(){}         // Triggers when slider has loaded					
			};
			// slider data			
			var data = $(this).data();
			// set the slider options into the defaults
			options.animSpeed = data.effect_duration * 1000,
			options.pauseTime = data.slide_duration * 1000;
			options.manualAdvance = !data.auto_slide;
			
			// start nivo slider
			var slider = $(this).find('.fa-nivo-slider').nivoSlider( options );
			slider.parent().removeClass('slider-loading').css({'height':'auto'});
		});
	});
})(jQuery);