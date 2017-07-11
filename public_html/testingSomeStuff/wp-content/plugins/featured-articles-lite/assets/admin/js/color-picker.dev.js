;(function($){	
	$(document).ready(function(){		
		var picker = $('.fapro-color-picker-hex');
		$.each( picker, function( i,  item ){
			$(item).wpColorPicker({
				change: function() {
					$(item).val( $(item).wpColorPicker('color') );
				},
				clear: function() {
					picker.val( false );
				}
			});
			
		})	
				
	});	
})(jQuery);