;(function($){
	// Uploading files
	var file_frame;
	var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
	var set_to_post_id = 0; // Set this
	 
	$(document).on('click', '.fa_upload_image_button', function( event ){
		var self = this;
		event.preventDefault();
		
		// If the media frame already exists on element, reopen it.
		if( $(self).data('file_frame') ){
			file_frame = $(self).data('file_frame');
			// Set the post ID to what we want
			file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			// Open frame
			file_frame.open();
			return;
		}else{
			wp.media.model.settings.post.id = set_to_post_id;
		}
		
		// If the media frame already exists, reopen it.
		//if ( file_frame ) {
			// Set the post ID to what we want
		//	file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			// Open frame
		//	file_frame.open();
		//	return;
		//} else {
			// Set the wp.media post id so the uploader grabs the ID we want when initialised
			//wp.media.model.settings.post.id = set_to_post_id;
		//}
		
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			frame: 'select',
			library:{  
				type:'image'
			},
			title: $( this ).data( 'title' ),
			button: {
				text: $( this ).data( 'text' ),
			},
			multiple: $( this ).data( 'multiple' ) // Set to true to allow multiple files to be selected
		});
		$(self).data('file_frame', file_frame);
		
		var update_elem = $(this).data('update');
		
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').toJSON();
		 
			if( attachment.length > 0 ){
				var ids = [];
				$.each( attachment, function(){
					ids.push( this.id );
				});
				
				var data = {
					'action' 	: $(self).data('ajax_action') || faEditSlider.assign_image_ajax_action,
					'post_id' 	: $('#post_ID').val(),
					'images' 	: ids
				};
				
				data[ faEditSlider.assign_image_nonce.name ] = faEditSlider.assign_image_nonce.nonce;
				
				$.ajax({
					'url' 		: ajaxurl,
					'data' 		: data,
					'dataType' 	: 'json',
					'type' 		: 'POST',
					'success' : function( json ){
						if( json.success ){
							$(self).trigger( 'ajaxload', json.data );
							if( $(self).data('append') ){
								$( update_elem ).empty().append( json.data );
								if( typeof fa_edit_slides !== 'undefined' && $.isFunction( fa_edit_slides ) ){
									fa_edit_slides();
								}
							}	
						}
					}
				});
				
			}
			
			$('.wp-full-overlay').css({'z-index':'500000'});
			
			// Do something with attachment.id and/or attachment.url here
			// Restore the main post ID
			wp.media.model.settings.post.id = wp_media_post_id;
		});
		
		file_frame.on( 'escape', function(){
			$('.wp-full-overlay').css({'z-index':'500000'});
		});
		
		// select media elements in slider
		file_frame.on( 'open', function() {
			var selection = file_frame.state().get('selection');
			$('.wp-full-overlay').css({'z-index':'auto'});
			
			var s = $( update_elem ).find('.fa-slide-image');
			if( s.length > 0 ){
				$.each( s, function(){
					var id = $(this).data('post_id'); 
					var attachment = wp.media.attachment(id);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );					
				});
			}						
		});
				
		// Finally, open the modal
		file_frame.open();
	});
	
	// Restore the main ID when the add media button is pressed
	jQuery('a.add_media').on('click', function() {
		wp.media.model.settings.post.id = wp_media_post_id;
	});
	
	
})(jQuery);