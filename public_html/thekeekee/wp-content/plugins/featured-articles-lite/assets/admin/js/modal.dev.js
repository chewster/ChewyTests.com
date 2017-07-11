;(function($){
	$.fn.FA_Modal = function( options ){
		
		if( 0 == this.length ){
			return false;
		}
		
		// support multiple elements
       	if (this.length > 1){
       		this.each(function() { 
				return $(this).FA_Modal( options );				
			});       		
       	}
       	
       	var defaults = {
       		iframeLoaded : function(){},
       		onClose		 : function(){},
       		doAction	 : function(){},
       		hide_action	 : false,
       		close_txt	 : false,
       		title_txt 	 : false,
       		closeOnAction : true
       	}
       	
       	var self = this,
       		options = $.extend({}, defaults, options),
       		url,
       		target,
       		content,
       		cancel,
       		close,
       		doAction,
       		ifr,
       		loadOverlay;
       	
       	var init = function(){
       		url			= $(self).attr('href');
       		target  	= $(self).data('target');
			content		= $( '#' + target ).find('.media-frame-content'),
			title		= $( '#' + target ).find('.media-frame-title h1'),
			cancel		= $( '#' + target ).find('.fapro-cancel-action'),
			close		= $( '#' + target ).find('.media-modal-close'),
			doAction 	= $( '#' + target ).find('.fapro-make-action'),
			loadCount	= 0;
			loadOverlay = $('<div />',{
				'class' : 'fa_overlay'
			});
			
			$(self).click( function(e){
				e.preventDefault();
				
				if( options.hide_action ){
					$(doAction).hide();
				}else{
					$(doAction).show();
				}				
				if( options.close_txt ){
					$(cancel).html( options.close_txt );
				}				
				if( options.title_txt ){
					$(title).html( options.title_txt );
				}else{
					$(title).html( title.data('title') );
				}
				
				$( '#' + target ).show();
    			
    			$(content).empty();//.addClass('loading');
    			loadOverlay.appendTo( $(content) );
    			
    			ifr = $('<iframe/>',{
    				'src' : url,
    				'width' : '100%',
    				'height' : '100%',
    				'scrolling' : 'yes'
    			}).appendTo( content );
    			
    			ifr.load(function(){
    				//$(content).removeClass('loading');
    				loadOverlay.hide();
    				
    				var f = $(ifr).contents().find('form');
    				if( f.length > 0 ){
    					$(f).submit(function(){
    						loadOverlay.show();
    					});
    				}
    				
    				options.iframeLoaded.call(self, {'iframe' : ifr, 'count' : loadCount});
    				loadCount++;
    			})
    			
    			// remove previous click events
    			$(cancel).unbind('click');
    			$(cancel).click(function(e){
	    			e.preventDefault();  	
	    			$(content).empty();
	    			$( '#' + target ).hide();
	    			options.onClose.call(self, {'iframe' : ifr, 'count' : loadCount});
	    			loadCount = 0;
	    		})
	    		
	    		// remove previous click events
	    		$(close).unbind('click');
	    		$(close).click(function(e){
	    			e.preventDefault();
	    			$(content).empty();
	    			$( '#' + target ).hide();
	    			options.onClose.call(self, {'iframe' : ifr, 'count' : loadCount});
	    			loadCount = 0;
	    		})
				
	    		$(doAction).unbind('click');
	    		$(doAction).click(function(e){
	    			e.preventDefault();
	    			options.doAction.call(self, {'iframe' : ifr, 'count' : loadCount});
	    			
	    			if( !options.closeOnAction ){
	    				return;
	    			}
	    				
	    			$(content).empty();
	    			$( '#' + target ).hide();
	    			options.onClose.call(self, {'iframe' : ifr, 'count' : loadCount});
	    			loadCount = 0;
	    		})
			
			})
			
       		return self;
       	}
		
       	this.close = function(){
       		$( '#' + target ).hide();
			options.onClose.call(self, {'iframe' : ifr, 'count' : loadCount});
			loadCount = 0;
       	}
       	
		return init();	
	}    	    	
    
}(jQuery));