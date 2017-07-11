/**
 * 
 */
;(function(){
	
	tinymce.PluginManager.add('fa_slider', function( editor, url ){
		/**
		 * Put a visual representation on shortcodes
		 */
		function replaceFAShortcodes( content ) {
			return content.replace( /\[fa_slider([^\]]*)\]/g, function( match ) {
				var r = html( 'fa-slider', match );
				return r;
			});
		}
		
		function html( cls, data ){
			data = window.encodeURIComponent( data );
			return '<img src="' + tinymce.Env.transparentSrc + '" class="fa_pro mceItem ' + cls + '" ' + 
			'data-fa_slider="' + data + '" data-mce-resize="false" />';			
		}
		
		/**
		 * Restore the shortcodes
		 */
		function restoreFAShortcodes( content ) {
			function getAttr( str, name ) {
				name = new RegExp( name + '=\"([^\"]+)\"' ).exec( str );
				return name ? window.decodeURIComponent( name[1] ) : '';
			}

			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data = getAttr( image, 'data-fa_slider' );

				if ( data ) {
					return '<p>' + data + '</p>';
				}

				return match;
			});
		}
		
		var selectSlider = function(){
			/*
			editor.windowManager.open({
				title 		: editor.getLang('fa_slider.select_win_title'),
				autoScroll 	: true,
				height		: 900,
				width 		: 900,
				html 		: '',
				buttons		: [
				     { text : editor.getLang('fa_slider.close_win'), onclick: 'close' }
				]
			});
			*/
			editSlider( false, editor.getLang('fa_slider.add_new_window_title') );
		}	
		
		var editSlider = function( node, title ){
			
			var data = {};
			
			if( node ){
				var code = window.decodeURIComponent( editor.dom.getAttrib( node, 'data-fa_slider' ) );
				code.replace( /([a-z\_?]+)\="([^\"]+)/ig, function( a, b, c, d, e ){
					data[b] = c;
				});
			}	
						
			// Advanced dialog shows general+advanced tabs
			win = editor.windowManager.open({
				title: title || editor.getLang('fa_slider.window_title'),
				data: data,
				body: [
					{
						title: 'Advanced',
						type: 'form',
						pack: 'start',
						items: [
							{
								label: editor.getLang('fa_slider.label_slider'),
								name : 'id',
								type : 'listbox',
								values : fa_sliders
							},
							{
								label: editor.getLang('fa_slider.label_title'),
								name : 'title',
								type : 'textbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_title'),
								name : 'show_title',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_in_archive'),
								name: 'singular',
								type: 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_width'),
								name : 'width',
								type : 'textbox',
								maxWidth : 50,
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_height'),
								name : 'height',
								type : 'textbox',
								maxWidth : 50,
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_font_size'),
								name : 'font_size',
								type : 'textbox',
								maxWidth : 50,
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_full_width'),
								name : 'full_width',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_top'),
								name : 'top',
								type : 'textbox',
								maxWidth : 50,
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_bottom'),
								name : 'bottom',
								type : 'textbox',
								maxWidth : 50,
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_slide_title'),
								name : 'show_slide_title',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_content'),
								name : 'show_content',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_date'),
								name : 'show_date',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_read_more'),
								name : 'show_read_more',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_show_play_video'),
								name : 'show_play_video',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_img_click'),
								name : 'img_click',
								type : 'checkbox',
								disabled : 'disabled'
							},
							{
								label: editor.getLang('fa_slider.label_auto_slide'),
								name : 'auto_slide',
								type : 'checkbox',
								disabled : 'disabled'
							},
						]
					}
				],
				onSubmit: function(e){
					var s = '[fa_slider id="' + e.data.id + '"]';					
					if( node ){ 
						editor.dom.setAttrib( node, 'data-fa_slider', window.encodeURIComponent( s ) );
					}
					editor.insertContent( s );
				}
			});
			
		}
		
		editor.on( 'mouseup', function( event ) {
			var dom 	= editor.dom,
				node 	= event.target;
			
			function unselect() {
				dom.removeClass( dom.select( 'img.wp-slider-selected' ), 'wp-slider-selected' );
			}

			if ( node.nodeName === 'IMG' && dom.getAttrib( node, 'data-fa_slider' ) ) {
				// Don't trigger on right-click
				if ( event.button !== 2 ) {
					if ( dom.hasClass( node, 'wp-slider-selected' ) ) {
						editSlider( node );
					} else {
						unselect();
						dom.addClass( node, 'wp-slider-selected' );
					}
				}
			} else {
				unselect();
			}
		});
		
		editor.on( 'BeforeSetContent', function( event ) {
			event.content = replaceFAShortcodes( event.content );			
		});
		
		editor.on( 'PostProcess', function( event ) {
			if ( event.get ) {
				event.content = restoreFAShortcodes( event.content );
			}
		});
		
		// Register button
		editor.addButton( 'fa_slider', {
			title 	: editor.getLang('fa_slider.button_title'),
			onclick : selectSlider,
			image 	: url + '/ico.png'
		});
		
		
	});
	
})();