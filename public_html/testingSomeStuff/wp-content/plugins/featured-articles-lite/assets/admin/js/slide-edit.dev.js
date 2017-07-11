;(function($){
	
	$(document).ready(function(){
		removeSlideImage();
	});
	
	var removeSlideImage = function(){
		$(document).on('click', '#fa-remove-slide-image', function(e){
			e.preventDefault();
			var data = {
				'action' : faEditSlide.remove_image_action,	
				'post_id' : $('#post_ID').val() 	
			};
			data[ faEditSlide.remove_image_nonce.name ] = faEditSlide.remove_image_nonce.nonce;
			$.ajax({
				'url' : ajaxurl,
				'data' : data,
				'dataType' : 'json',
				'type' : 'POST',
				'success' : function( json ){
					if( json.success ){
						$( '#fa-selected-images' ).empty().append( json.data );
						return;
					}					
				},
				'error': function(){
					
				}
			});
		})
		
	}
	
})(jQuery);