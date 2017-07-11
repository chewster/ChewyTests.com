/**
 * For WP versions prior to 4.X, use alternative to shortcode tinyMCE button
 */
;(function($){	
	$(document).ready( function(){
		var btn = $('#fa-insert-shortcode');
		if( btn.length < 1 ){
			return;
		}
		
		btn.click( function(e){
			var id = $('#fa-slider-shortcode').val();
			if( '' == id ){
				return;
			}
			
			var shortcode = '[fa_slider id="' + id + '"]';
			send_to_editor( shortcode );			
		});		
	});	
})(jQuery);