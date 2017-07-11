/**
 * 
 */
;(function($){
	
	$(document).ready(function(){
		sliderTheme();
		contentSwitch();
		mixedContentSelect();
		images();
		editSlides();
		categoriesSelect();
		displayCategoriesSelect();
		displayPostsSelect();
		togglers();
		expirationDate();
		removeDefaultSliderImage();
	})
	
	var removeDefaultSliderImage = function(){		
		$(document).on('click', '#fa-remove-default-slider-image', function(e){
			e.preventDefault();
			var post_id = $(this).data('post_id'),
				d = faEditSlider.rem_slider_default_img;
			
			var data = {
				'post_id' 	: post_id,
				'action' 	: d.action
			};
			data[ d.nonce.name ] = d.nonce.nonce;
			$.ajax({
				'url' : ajaxurl,
				'data' : data,
				'dataType' : 'json',
				'type' : 'POST',
				'success' : function( json ){
					if( json.success ){
						$('#fa-slides-default-image').html( json.data );
					}
				}						
			});			
		})		
	}
	
	var expirationDate = function(){
		
		$('.edit-exp_timestamp').click(function(e){
			e.preventDefault();
			$('#timestamp_exp_div').show('fast');
			$(this).hide();
		})
		
		$('.save-exp-timestamp, .cancel-exp-timestamp').click(function(e){
			e.preventDefault();
			$('#timestamp_exp_div').hide('fast');
			$('.edit-exp_timestamp').show();
		})
		
	}
	
	var togglers = function(){
		var t = $('.fa-horizontal-tabs input.toggler');
		$.each( t, function(){
			var toggle 	= $(this).data('toggle'),
				elems 	= $('.fa-horizontal-tabs .' + toggle),
				show	= $(this).data('show'),
				hide	= $(this).data('hide'),
				showElems = false,
				hideElems = false;
			
			if( show ){
				showElems = $('.fa-horizontal-tabs .' + show);
			}
			if( hide ){
				hideElems = $('.fa-horizontal-tabs .' + hide);
			}	
			
			$(this).click(function(e){
				
				var action = $(this).data('action') ? $(this).data('action') : 'show';
				
				if( $(this).is(':checked') ){
					
					if( 'show' == action ){
						$(elems).show(800);
					}else{
						$(elems).hide(500);
					}
										
					var togglers = $(elems).find('input.toggler');
					if( togglers && 'show' == action ){
						$.each( togglers, function(){
							if( $(this).is(':checked') ){
								$(this).trigger('click');
							}	
						});
					}
					
					if( showElems ){
						$(showElems).show();
					}
					if( hideElems ){
						$(hideElems).hide();
					}
				}else{
					if( 'show' == action ){					
						$(elems).hide(500);
					}else{
						$(elems).show(800);						
						var togglers = $(elems).find('input.toggler');
						if( togglers ){
							$.each( togglers, function(){
								if( $(this).is(':checked') ){
									var selector = $(this).data('toggle');
									$('.fa-horizontal-tabs .' + selector).hide();
								}	
							});
						}
						
					}	
				};
			});
			
		});		
	}
	
	var sliderTheme = function(){
		var themes 		= $('#fa-registered-themes .fa-theme'),
			selects 	= $('#fa-registered-themes .fa-select'),
			customize 	= $('#fa-registered-themes .fa-customize'),
			input 		= $('#fa_active_theme'),
			optionals	= $('.fa-horizontal-tabs tr.optional');
		
		$(selects).click(function(e){
			e.preventDefault();
			var theme = $(this).data('theme'),
				el = $( '#fa-theme-' + theme );
			
			// hide layout variations fields
			var layout_vars = $('.layout-color-variations');
			$(layout_vars).hide();
			$('#layout-classes-' + theme).show();
			
			// iterate all optional fields
			$.each( optionals, function(i, o){
				var disable 	= $(this).data('theme_disable').split(','),
					enable		= $(this).data('theme_enable').split(','),
					togglers 	= $(this).find('.toggler');
				// if enabled, show the options
				if( -1 != $.inArray( theme, enable ) ){
					var e = $(this).find('.fa-optional-field-enabled'),
						d = $(this).find('.fa-optional-field-disabled');
					// hide message and show option field
					$(e).show();
					$(d).hide();
					// run the togglers
					$.each( togglers, function(){
						var toggle 	= $(this).data('toggle'),
							elems 	= $('.fa-horizontal-tabs .' + toggle);
						if( $(this).is(':checked') ){
							$(elems).show();
						}else{
							$(elems).hide();
						}						
					});					
				}
				// if disabled, hide option and show message
				if( -1 != $.inArray( theme, disable ) ){
					var d = $(this).find('.fa-optional-field-enabled'),
						e = $(this).find('.fa-optional-field-disabled');
					$(e).show();
					$(d).hide();
					// show all togglers
					$.each( togglers, function(){
						var toggle 	= $(this).data('toggle'),
							elems 	= $('.fa-horizontal-tabs .' + toggle);
						$(elems).show();												
					});
				}				
			});
			
			
			$(input).val( theme );
			$(themes).removeClass('active');
			$(el).addClass('active');
			
			$(customize).hide();
			$(selects).show();			
			
			$(el).find('.fa-customize').show();
			$(this).hide();
			/**
			 * Theme controlled settings container all have class .theme-settings .THEME_NAME
			 * Hide all specific theme settings and show only the ones for the selected theme
			 */
			$('.theme-settings').hide();
			$('.theme-settings.' + theme ).show();
			
			//$('.theme-js-settings, .theme-layout-settings').hide();
			//$('#theme-js-settings-' + $(this).data('theme') ).show();
			//$('#theme-layout-settings-' + $(this).data('theme') ).show();			
		})
		
		// add color to slider preview
		$.each(customize, function(){
			var u;
			$(this).click( function(e){
				
				e.preventDefault();
				
				var $form 			= $('form#post'),
					$previewField 	= $('input#wp-preview'),
					$contentField	= $('input#wp-content');
					target 			= $(this).attr('target');
				
				$previewField.val('dopreview');
				$contentField.val('last saved @ ' + ( new Date() ) );
				$form.attr({'target':target}).submit().attr({'target':''});
				$previewField.val('');
				$contentField.val('');
				
				/* Uncomment for old functionality
				var color = $(this).parent().find('select').val() || '-1';
				u = $(this).attr('href');
				$(this).attr('href', u + '&color=' + color );
				//*/
				
				
				
			}).mouseup(function(e){
				$(this).attr('href', u);
			})			
		});
		
		// tabs
		if(typeof(Storage)!=="undefined"){
			var data = {
				active : sessionStorage['fapro_theme_tab_active'],
				activate : function(event, ui){
					sessionStorage['fapro_theme_tab_active'] = ui.newTab.index();
				},
				create: function(event, ui){}
			};
		}else{
			var data = {};
		};
		// start the tabs
		$('#fa-themes-tabs').tabs(data);		
	}
	
	var contentSwitch = function(){
		var chk = $("input[name='slides[type]']");
		
		$('#fa-slider-content-tabs').tabs();
		
		$(chk).click(function(){
			var self = this;
			$.each(chk, function(){
				var panel = $(this).data('panel');
				if( self === this ){
					$('#' + panel).show();
				}else{
					$('#' + panel).hide();
				}
				$('#fa-slider-content-tabs').tabs('option', 'active', 0);
			});
		});
	}
	
	var editSlides = function(){
		var iframe,
			triggers = $('.fapro-modal-trigger.fa-slide-edit'),
			container = $('#fa-selected-posts'),
			postId;
		
		if( triggers.length > 0 ){
			$.each( triggers, function(i, trigger){
				
				var m = $(trigger).FA_Modal({
					'hide_action'	: true,
					'close_txt'	 	: faEditSlider.messages.close_modal,
					'title_txt'	  	: $(this).data('modal_title') || faEditSlider.messages.title_edit_post,
					'closeOnAction' : false,
					'iframeLoaded' 	: function( data ){	
						iframe = $(data.iframe).contents();
						
						postId = $(iframe).find('#post_ID').val();
						
						$(iframe).find('#delete-action a.submitdelete').click(function(e){
							var post_id = $(iframe).find('#post_ID').val();							
							if( $(this).data('type') == 'mixed' ){
								$('#fa-selected-posts').find('#post-' + post_id).remove();							
							}else{
								$('#fa-selected-images').find('#attachment-' + post_id).remove();	
							}
							m.close();
						})												
					},
					'doAction' 		: function(){
						
					},
					'onClose'		: function( info ){
						var slide_id 	= $(this).data('slide_id'),
							slider_id 	= $(this).data('slider_id'),
							is_new		= false;
						
						if( 'new-custom-slide' == slide_id ){
							slide_id = postId;
							is_new = true;
						}
						
						var data = {
							'post_id' 	: slide_id,
							'slider_id' : slider_id,	
							'action' 	: 	( $(this).data('type') == 'mixed' ? 
												faEditSlider.assign_slide_ajax_action :
												faEditSlider.assign_image_ajax_action
											)
						};
						
						if( $(this).data('type') == 'mixed' ){
							data[ faEditSlider.assign_slide_wp_nonce.name ] = faEditSlider.assign_slide_wp_nonce.nonce;
						}else{
							data[ faEditSlider.assign_image_nonce.name ] = faEditSlider.assign_image_nonce.nonce;
							data['images'] = slide_id;
							container = $('#fa-selected-images');
						}
						
						var id_prefix = $(this).data('type') == 'mixed' ? '#post-' : '#attachment-';
						
						$.ajax({
							'url' : ajaxurl,
							'data' : data,
							'dataType' : 'json',
							'type' : 'POST',
							'success' : function( json ){
								if( json.success ){
									var el = $( container ).find( id_prefix + slide_id );
									if( el.length > 0 ){
										$(el).replaceWith( json.data );
										editSlides();
									}else if( is_new ){
										$(container).append( json.data );
										editSlides();
										is_new = false;
									}
								}
							}						
						});
					}
				});
			})			
		}		
	}
	
	// Used in media-gallery.dev.js to activate the edit modal when adding new images to slider
	window.fa_edit_slides = function(){
		editSlides();
	}
	
	var images = function(){
		// make slides sortable
		$('#fa-selected-images').sortable({
			cancel : 'a',
			containment: 'parent',
			cursor : 'move',
			distance : 10,
			items : '.fa-slide-image',
			opacity : .9,
			revert : false,
			tolerance : 'pointer',
			serialize: {key:'attachment-'},
			update: function(event, ui){
				
			}			
		});
		
		// remove slides functionality
		$('#fa-selected-images').on('click', '.fa-image-remove', function(e){
			e.preventDefault();
			$(this).parent().remove();
			$('#fa-selected-images').sortable('refresh');
		})		
	}
	
	var mixedContentSelect = function(){
		var container = $('#fa-selected-posts'),
			iframe;
		
		// make slides sortable
		$('#fa-selected-posts').sortable({
			cancel : 'a',
			containment: 'parent',
			cursor : 'move',
			distance : 10,
			items : '.fa-slide',
			opacity : .9,
			revert : false,
			tolerance : 'pointer',
			serialize: {key:'post-id-'},
			update: function(event, ui){
				
			}			
		});
		
		// remove slides functionality
		$('#fa-selected-posts').on('click', '.fa-slide-remove', function(e){
			e.preventDefault();
			$(this).parent().remove();
			$('#fa-selected-posts').sortable('refresh');
		})
		
		
		// start modal functionality
		$('#fa-select-mixed-content').FA_Modal({
			'hide_action' : true,
			'close_txt'	  : faEditSlider.messages.close_modal,
			'title_txt'	  : faEditSlider.messages.title_slides,
			'iframeLoaded' 	: function( data ){	
				iframe = $(data.iframe).contents();
				checkPosts();
				selectPosts();
			},
			'doAction' 		: function(){
				// not used
			},
			'onClose'		: function(){
				$(container).find('p.description').remove();
			}
		});
		
		var checkPosts = function(){
			var existing = $( container ).find('.fa-slide');
			if( existing.length < 1 ){
				return;				
			}
			$.each( existing, function(){
				var post_id = $(this).data('post_id');
				iframe.find('#cb-select-' + post_id).attr('checked', 'checked');
			});
		}
		
		var selectPosts = function(){
			var chk			= iframe.find("input[type='checkbox'][name^='select_posts']"),
				selectAll 	= iframe.find("input[type='checkbox'][id^='cb-select-all']");
			
			$(chk).change( function(){
				var post_id 	= $(this).data('post_id'),
					checked		= $(this).is(':checked');
				
				if( checked ){
					var data = {
						'post_id' 	: post_id,
						'slider_id' : $('#post_ID').val(),	
						'action' 	: faEditSlider.assign_slide_ajax_action						 
					};
					
					data[ faEditSlider.assign_slide_wp_nonce.name ] = faEditSlider.assign_slide_wp_nonce.nonce;
					
					$.ajax({
						'url' : ajaxurl,
						'data' : data,
						'dataType' : 'json',
						'type' : 'POST',
						'success' : function( json ){
							if( json.success ){
								var el = $( container ).find( '#post-' + post_id );
								if( 0 == el.length ){
									$(container).append( json.data );
									editSlides();
								}
							}
						}						
					});				
				}else{					
					$( container ).find( '#post-' + post_id ).remove();
					$('#fa-selected-posts').sortable('refresh');				
				}				
			});
			
			$(chk).click( function(e){
				if ( e.shiftKey ) {
					setTimeout( function(){
						$(chk).trigger('change');					
					}, 1000 );
				}
			});
			
			$(selectAll).change( function(){
				setTimeout( function(){
					$(chk).trigger('change');					
				}, 1000 );				
			});	
			
		}		
	}
	
	var categoriesSelect = function(){
		
		var container = $('#fa-selected-categories'),
			iframe;
		
		$(document).on('click', '.fa_remove_tag', function(e){
			e.preventDefault();
			var taxonomy = $(this).parent().data('taxonomy');
			$(this).parent().remove();
			
			var children = $('#fa-tax-' + taxonomy).children('.fa-term');
			if( children.length == 0 ){
				$('#fa-tax-' + taxonomy).hide();
			}
			
			var sel = $(container).find("[id^='fa_term_']");
			if( sel.length == 0 ){
				$('#fa-all-categories').show();
			}else{
				$('#fa-all-categories').hide();
			}
		})
		
		$(document).on('mouseenter', '.fa_remove_tag', function(){
			$(this).parent().addClass('remove');
		}).on('mouseleave', '.fa_remove_tag', function(){
			$(this).parent().removeClass('remove');
		})
		
		$('#fa-content-posts-categories').FA_Modal({
			'hide_action'	: true,
			'close_txt'		: faEditSlider.messages.close_modal,
			'title_txt'	  	: faEditSlider.messages.title_categories,
			'iframeLoaded' 	: function( data ){	
				iframe = $(data.iframe).contents();
				checkCategories();
				selectCategories( data );
			},
			'doAction' 		: function(){
				// not used
			},
			'onClose'		: function(){
				var sel = $(container).find("[id^='fa_term_']");
				if( sel.length == 0 ){
					$('#fa-all-categories').show();
				}else{
					$('#fa-all-categories').hide();
				}
			}
		});
		
		var checkCategories = function(){
			var existing = $( container ).find('.fa-term');
			if( existing.length < 1 ){
				return;				
			}
			
			$.each( existing, function(){
				var term_id = $(this).data('term_id');
				iframe.find('#cb-select-' + term_id).attr('checked', 'checked');
			});			
		}
		
		var selectCategories = function( data ){
			var chk			= iframe.find("input[type='checkbox'][name^='select_tags']"),
				selectAll 	= iframe.find("input[type='checkbox'][id^='cb-select-all']");
			
			$('#fa-all-categories').hide();
			
			$(chk).change( function(){
				var post_type 	= $(this).data( 'post_type' ),
					taxonomy 	= $(this).data( 'taxonomy' ),
					term_id		= $(this).data( 'term_id' ),
					name		= iframe.find('#fa-name-' + term_id).html(),
					checked		= $(this).is(':checked');
				
				if( checked ){
					var el = $( container ).find( '#fa_term_' + term_id );
					if( el.length == 0 ){					
						$( container ).find('#fa-tax-' + taxonomy).show().append( catTemplate( name, taxonomy, term_id ) );
					}	
				}else{					
					$( container ).find( '#fa_term_' + term_id ).remove();
					var elems = $( container ).find( '#fa-tax-' + taxonomy ).children('.fa-term');
					if( elems.length == 0 ){
						$( container ).find( '#fa-tax-' + taxonomy ).hide();
					}					
				}				
			});
			
			$(selectAll).change( function(){
				$(chk).trigger('change');	
			});			
		}
		
		var catTemplate = function( name, taxonomy, term_id ){
			var out = '<span class="fa-term" data-term_id="' + term_id + '" data-taxonomy="' + taxonomy + '" id="fa_term_' + term_id + '">';
			out += '<a href="#" class="fa_remove_tag"><i class="dashicons dashicons-dismiss"></i></a> ' + name;
			out += '<input type="hidden" name="slides[tags][' + taxonomy + '][]" value="' + term_id + '" />';
			out += '</span>';
			return out;
		}		
	}
	
	var displayCategoriesSelect = function(){
		var container = $('#fa-selected-display-categories'),
			iframe;
		
		$(document).on('click', '.fa_remove_display_tag', function(e){
			e.preventDefault();
			var taxonomy = $(this).parent().data('taxonomy');
			$(this).parent().remove();
			
			var children = $('#fa-display-tax-' + taxonomy).children('.fa-term');
			if( children.length == 0 ){
				$('#fa-display-tax-' + taxonomy).hide();
			}
			
			var sel = $(container).find("[id^='fa_display_term_']");
			if( sel.length == 0 ){
				$('#fa-all-display-categories').show();
			}else{
				$('#fa-all-display-categories').hide();
			}
		})
		
		$(document).on('mouseenter', '.fa_remove_display_tag', function(){
			$(this).parent().addClass('remove');
		}).on('mouseleave', '.fa_remove_display_tag', function(){
			$(this).parent().removeClass('remove');
		})
		
		$('#fa-display-posts-categories').FA_Modal({
			'hide_action'	: true,
			'close_txt'		: faEditSlider.messages.close_modal,
			'title_txt'	  	: faEditSlider.messages.title_categories,
			'iframeLoaded' 	: function( data ){	
				iframe = $(data.iframe).contents();
				checkCategories();
				selectCategories( data );
			},
			'doAction' 		: function(){
				// not used
			},
			'onClose'		: function(){
				var sel = $(container).find("[id^='fa_display_term_']");
				if( sel.length == 0 ){
					$('#fa-all-display-categories').show();
				}else{
					$('#fa-all-display-categories').hide();
				}
			}
		});
		
		var checkCategories = function(){
			var existing = $( container ).find('.fa-term');
			if( existing.length < 1 ){
				return;				
			}
			
			$.each( existing, function(){
				var term_id = $(this).data('term_id');
				iframe.find('#cb-select-' + term_id).attr('checked', 'checked');
			});			
		}
		
		var selectCategories = function( data ){
			var chk			= iframe.find("input[type='checkbox'][name^='select_tags']"),
				selectAll 	= iframe.find("input[type='checkbox'][id^='cb-select-all']");
			
			$('#fa-all-display-categories').hide();
			
			$(chk).change( function(){
				var post_type 	= $(this).data( 'post_type' ),
					taxonomy 	= $(this).data( 'taxonomy' ),
					term_id		= $(this).data( 'term_id' ),
					name		= iframe.find('#fa-name-' + term_id).html(),
					checked		= $(this).is(':checked');
				
				if( checked ){
					var el = $( container ).find( '#fa_display_term_' + term_id );
					if( el.length == 0 ){					
						$( container ).find('#fa-display-tax-' + taxonomy).show().append( catTemplate( name, taxonomy, term_id ) );
					}	
				}else{					
					$( container ).find( '#fa_display_term_' + term_id ).remove();
					var elems = $( container ).find( '#fa-display-tax-' + taxonomy ).children('.fa-term');
					if( elems.length == 0 ){
						$( container ).find( '#fa-display-tax-' + taxonomy ).hide();
					}					
				}				
			});
			
			$(selectAll).change( function(){
				$(chk).trigger('change');	
			});			
		}
		
		var catTemplate = function( name, taxonomy, term_id ){
			var out = '<span class="fa-term" data-term_id="' + term_id + '" data-taxonomy="' + taxonomy + '" id="fa_display_term_' + term_id + '">';
			out += '<a href="#" class="fa_remove_display_tag"><i class="dashicons dashicons-dismiss"></i></a> ' + name;
			out += '<input type="hidden" name="display[tax][' + taxonomy + '][]" value="' + term_id + '" />';
			out += '</span>';
			return out;
		}		
	}
	
	var displayPostsSelect = function(){
		var container = $('#fa-selected-display-posts'),
			iframe;
		
		$(document).on('click', '.fa_remove_display_post', function(e){
			e.preventDefault();
			var post_type = $(this).parent().data('post_type');
			$(this).parent().remove();
			
			var children = $('#fa-display-posts-' + post_type).children('.fa-post');
			if( children.length == 0 ){
				$('#fa-display-posts-' + post_type).hide();
			}
			
			var sel = $(container).find("[id^='fa_display_post_']");
			if( sel.length == 0 ){
				$('#fa-all-display-posts').show();
			}else{
				$('#fa-all-display-posts').hide();
			}
		})
		
		$(document).on('mouseenter', '.fa_remove_display_post', function(){
			$(this).parent().addClass('remove');
		}).on('mouseleave', '.fa_remove_display_post', function(){
			$(this).parent().removeClass('remove');
		})
		
		$('#fa-display-posts').FA_Modal({
			'hide_action'	: true,
			'close_txt'		: faEditSlider.messages.close_modal,
			'title_txt'	  	: faEditSlider.messages.title_categories,
			'iframeLoaded' 	: function( data ){	
				iframe = $(data.iframe).contents();
				checkPosts();
				selectPosts( data );
			},
			'doAction' 		: function(){
				// not used
			},
			'onClose'		: function(){
				var sel = $(container).find("[id^='fa_display_post_']");
				if( sel.length == 0 ){
					$('#fa-all-display-posts').show();
				}else{
					$('#fa-all-display-posts').hide();
				}
			}
		});
		
		var checkPosts = function(){
			var existing = $( container ).find('.fa-post');
			if( existing.length < 1 ){
				return;				
			}
			
			$.each( existing, function(){
				var post_id = $(this).data('post_id');
				iframe.find('#cb-select-' + post_id).attr('checked', 'checked');
			});			
		}
		
		var selectPosts = function( data ){
			var chk			= iframe.find("input[type='checkbox'][name^='select_posts']"),
				selectAll 	= iframe.find("input[type='checkbox'][id^='cb-select-all']");
			
			$('#fa-all-display-posts').hide();
			
			$(chk).change( function(){
				var post_type 	= $(this).data( 'post_type' ),
					post_id		= $(this).data( 'post_id' ),
					name		= iframe.find('#fa-name-' + post_id).html(),
					checked		= $(this).is(':checked');
				
				if( checked ){
					var el = $( container ).find( '#fa_display_post_' + post_id );
					if( el.length == 0 ){					
						$( container ).find('#fa-display-posts-' + post_type).show().append( postTemplate( name, post_type, post_id ) );
					}	
				}else{					
					$( container ).find( '#fa_display_post_' + post_id ).remove();
					var elems = $( container ).find( '#fa-display-posts-' + post_type ).children('.fa-post');
					if( elems.length == 0 ){
						$( container ).find( '#fa-display-posts-' + post_type ).hide();
					}					
				}				
			});
			
			$(selectAll).change( function(){
				$(chk).trigger('change');	
			});			
		}
		
		var postTemplate = function( name, post_type, post_id ){
			var out = '<span class="fa-post" data-post_id="' + post_id + '" data-post_type="' + post_type + '" id="fa_display_post_' + post_id + '">';
			out += '<a href="#" class="fa_remove_display_post"><i class="dashicons dashicons-dismiss"></i></a> ' + name;
			out += '<input type="hidden" name="display[posts][' + post_type + '][]" value="' + post_id + '" />';
			out += '</span>';
			return out;
		}		
	}
	
})(jQuery);