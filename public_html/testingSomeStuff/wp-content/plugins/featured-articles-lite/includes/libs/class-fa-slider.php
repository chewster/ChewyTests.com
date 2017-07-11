<?php
class FA_Slider{
	/**
	 * Stores the post slider
	 */
	private $slider = false;
	/**
	 * Stores the options of the slider
	 */
	private $options = false;
	/**
	 * Stores retrieved slides 
	 */
	private $slides = false;
	
	/**
	 * Constructor. Takes as argument the slider ID
	 * @param int $slider_id
	 */
	public function __construct( $slider_id ){
		$this->timer_start = microtime( true );
		// get the slider post
		$post = get_post( $slider_id );
		
		if( !$post || $post->post_type != fa_post_type_slider() ){
			return;
		}
		// check post status, allow any if preview
		if( !fa_is_preview() && 'publish' != $post->post_status ){
			return;
		}
		
		// if preview, check other sliders status that might be displayed into the page and allow only published ones 
		if( fa_is_preview() ){
			$preview_id = absint( $_GET['slider_id'] );
			if( $slider_id != $preview_id && 'publish' != $post->post_status ){
				return false;
			}						
		}
		
		// store the slider
		$this->slider = $post;	
		
		// If preview, get the options from the revision, if any is available
		if( fa_is_preview() ){
			$statuses = array( 'future', 'publish', 'draft' );
			if( in_array( $post->post_status, $statuses ) ){
				$children = get_children( array(
					'post_parent' 		=> $slider_id,
					'post_type' 		=> 'revision',
					'orderby'          	=> 'post_date',
					'order'            	=> 'DESC',
					'posts_per_page'	=> 1
				));
				if( $children ){
					$revision	= array_pop( $children );
					$options	= fa_get_slider_options( $revision->ID );
					$this->options = $options;
				}
			}
		}		
		
		// filter the options to push the theme from preview
		if( fa_is_preview() ){
			if( isset( $_GET['slider_id'] ) && $slider_id == $_GET['slider_id'] ){			
				// filter the slider options to set the theme from preview
				add_filter('fa_get_slider_options', array( $this, 'modify_preview_options' ), 1, 3);
			}
		}
		
		// if options aren't already set by a preview, set them now
		if( !$this->options ){
			// get the slider options
			$this->options = fa_get_slider_options( $post->ID );
		}
		
		// get the slides
		$this->slides = $this->get_slides();		
	}
	
	/**
	 * Returns the slides according to slider content settings
	 */
	private function get_slides(){
		$result 	= array();
		// content selection is stored on key slides
		$options 	= $this->options['slides'];
		switch( $options['type'] ){
			// posts query
			case 'post':
				$args = array(
					'post_status' 			=> 'publish',
					'post_type'				=> 'post',
					'numberposts'			=> absint( $options['limit'] ),
					'order'					=> 'DESC',
					'ignore_sticky_posts' 	=> true					
				);
				
				/**
				 * Allow extra query arguments to be set on post query
				 */ 
				$extra_args = apply_filters( '_fa_slider_posts_query_args' , array() );
				// recreate args array
				$args = array_merge( $extra_args, $args );
				
		        // tax query
				$taxonomies = get_object_taxonomies( 'post' );
				foreach( $options['tags'] as $tax => $terms  ){
					if( !in_array( $tax , $taxonomies) || !$terms ){
						continue;
					}						
					if( !isset( $args['tax_query']['relation'] ) ){
						$args['tax_query']['relation'] = 'OR';
					}
					// add the taxonomies
					$args['tax_query'][] = array(
						'taxonomy' 	=> $tax,
						'field'		=> 'id',
						'terms'		=> $terms
					);						
				}					
				
				// set orderby parameter
				switch( $options['orderby'] ){
					case 'date':
					default:	
						$args['orderby'] = 'post_date';						
					break;
					case 'comments':
						$args['orderby'] = 'comment_count post_date';
					break;
					case 'random':
						$args['orderby'] = 'rand';
					break;						
				}
				// set author
				if( isset( $options['author'] ) && 0 != $options['author'] ){
					$args['author'] = absint( $options['author'] );
				}		
				$result = get_posts( $args );
				
				/**
				 * Action when retrieving posts. Useful for third party compatibility.
				 * 
				 * @param $result - array of posts that the slider is made of
				 * @param $this->slider->ID - slider ID being processed
				 */
				do_action( 'fa_slider_post_slides', $result, $this->slider->ID );
				
			break;
			// mixed content query
			case 'mixed':
				// if no posts selected, break
				if( !$options['posts'] ){
					break;					
				}
				$args = array(
					'post_status'			=> 'publish',
					'post_type'				=> array( 'post', 'page' ),
					'posts_per_page' 		=> -1,
					'nopaging'				=> true,
					'ignore_sticky_posts' 	=> true,
					'offset'				=> 0,
					'include'				=> (array) $options['posts']											
				);
				
				/**
				 * Allow extra query arguments to be set on post query
				 */
				$extra_args = apply_filters( '_fa_slider_mixed_content_query_args' , array() );
				// recreate args array
				$args = array_merge( $extra_args, $args );
				
				$posts = get_posts( $args );
				
				$result = array();
				if( $posts ){
					foreach( $posts as $post ){
						$key = array_search( $post->ID, $options['posts'] );
						$result[ $key ] = $post;
					}					
				}
				// arrange the values according to settings
				ksort($result);	
				// regenerate the keys to start from 0 ascending
				$result = array_values( $result );			
			break;
		}		
		return $result;
	}
	
	/**
	 * Displays the slider. If $echo, it will output the contents 
	 * 
	 * @param bool $echo
	 */
	public function display( $echo = true ){
		if( ( !$this->slider || !$this->slides ) ){
			if( !fa_is_preview() ){
				return;
			}
		}
		
		$theme = $this->options['theme'];
		$theme_file = $theme['details']['display'];
		if( !file_exists( $theme_file ) ){
			// trigger error just for admins
			if( current_user_can( 'manage_options' ) ){
				trigger_error( sprintf( __('Slider theme <strong>%s</strong> display file could not be found.', 'featured-articles-lite'), $theme['details']['theme_config']['name']) );
			}	
			return;
		}
		// make ssl friendly theme URL
		if( is_ssl() ){
			$theme['details']['url'] = str_replace( 'http://', 'https://', $theme['details']['url'] );
		}
		
		// load minified stylesheet
		$suffix = defined('FA_CSS_DEBUG') && FA_CSS_DEBUG ? '' : '.min';
		wp_enqueue_style(
			'fa-theme-' . $theme['active'],
			path_join( $theme['details']['url'] , 'stylesheet' . $suffix . '.css'),
			false,
			FA_VERSION
		);
		
		if( isset( $theme['details']['theme_config']['stylesheets'] ) ){
			$extra_styles = (array) $theme['details']['theme_config']['stylesheets'];
			
			/**
			 * Filter that can be used in themes to prevent FontAwesome from being loaded
			 * if the theme already uses it.
			 * @var bool
			 */
			$allow_fa = apply_filters( 'fa-load-font-awesome-css' , true );
			
			// prevent Font Awesome to be loaded if set by the user in admin area
			$options = fa_get_options( 'settings' );
			if( isset( $options['load_font_awesome'] ) && !$options['load_font_awesome'] ){
				$allow_fa = false;
			}
			
			// enqueue font awesome if specified by the theme settings
			if( $allow_fa && isset( $extra_styles['font-awesome'] ) && $extra_styles['font-awesome'] ){
				// enqueue font awesome only if specified by the theme settings
				wp_enqueue_style(
					'font-awesome',
					( is_ssl() ? 'https://' : 'http://' ) . 'maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css',
					array(),
					FA_VERSION
				);
			}
		}
		
		if( !empty( $this->options['theme']['color'] ) ){
			wp_enqueue_style(
				'fa-theme-' . $theme['active'] . '-' . $theme['color'],
				path_join( $theme['details']['url'] , 'colors/' . str_replace( '.min' , '', $theme['color'] ) . '.css'),
				array( 'fa-theme-' . $theme['active'] ),
				FA_VERSION
			);
		}
		
		/**
		 * Theme starter dependencies
		 */
		$dependencies = array( 'jquery' );
		$theme_scripts = isset( $theme['details']['theme_config']['scripts'] ) ? (array) $theme['details']['theme_config']['scripts'] : array();
		
		// extra theme handles implemented by the theme
		$extra_handles = isset( $theme['details']['theme_config']['extra_scripts']['handles'] ) ? (array)$theme['details']['theme_config']['extra_scripts']['handles'] : array();
		if( $extra_handles ){
			foreach( $extra_handles as $handle ){
				$dependencies[] = $handle;
			}
		}
		
		// theme scripts that should be enqueued
		$enqueue_scripts = isset( $theme['details']['theme_config']['extra_scripts']['enqueue'] ) ? (array)$theme['details']['theme_config']['extra_scripts']['enqueue'] : array();
		if( $enqueue_scripts ){
			foreach( $enqueue_scripts as $handle => $rel_path ){
				wp_register_script(
					$handle,
					$theme['details']['url'] .'/'. ltrim( $rel_path, '/\\' )
				);
				$dependencies[] = $handle;
			}
		}
		
		$script_handles = array('slider', 'jquery-mobile', 'jquery-transit' );
		
		// when debug is on, load each individual .dev file
		if( defined('FA_SCRIPT_DEBUG') && FA_SCRIPT_DEBUG ){
			/**
			 * Following handles are enqueued by themes that use regular slider script.
			 * These script can be skipped by themes by specifying in theme functions.php
			 * file inside function that passes the details not to embed them.
			 * Dissalowing embed for certain themes can be done with an array like
			 * 'scripts' => array( 'slider' => false )
			 */
			foreach ( $script_handles as $handle ){
				if( !isset( $theme_scripts[ $handle ] ) || $theme_scripts[ $handle ] ){
					$dependencies[] = fa_load_script( $handle );
				}
			}	
		}else{
			/**
			 * Iterate all handles and if one isn't set or is set true, load minimized
			 * scripts file.
			 */
			$load_scripts = false;
			foreach ( $script_handles as $handle ){
				if( !isset( $theme_scripts[ $handle ] ) || $theme_scripts[ $handle ] ){
					$load_scripts = true;
					break;
				}
			}
			if( $load_scripts ){
				// load only the minified file containing all scripts
				$dependencies[] = fa_load_script( '_scripts.min' );
			}
		}	
		
		// load theme starter
		$suffix = defined('FA_SCRIPT_DEBUG') && FA_SCRIPT_DEBUG ? '.dev' : '.min';
		wp_enqueue_script(
			'fa-theme-' . $theme['active'] . '-starter',
			path_join( $theme['details']['url'] , 'starter' . $suffix . '.js'),
			$dependencies,
			FA_VERSION,
			true
		);
		
		
		global $slider_id, $fa_slider;
		// global $post
		$slider_id = $this->slider->ID;
		/**
		 * Set the global $fa_slider variable that will be used
		 * in templating functions and other functions.
		 * 
		 * @var object - the post object of the current slider
		 */
		$fa_slider = $this->slider;
		// store the posts on the slider global variable
		$fa_slider->slides = $this->slides;
		// set the current to 0
		$fa_slider->current_slide = -1;
		// set the number of slides
		$fa_slider->slide_count = count( $this->slides );
		// include the templating functions
		include_once fa_get_path( 'includes/templating.php' );
		// capture the output
		ob_start();
		include( $theme_file );
		$output = ob_get_clean();
		
		// include some cache stats on previews
		if( fa_is_preview() || current_user_can( 'manage_options' ) ){
			$output .= '<!-- Slider generated in '.number_format( microtime(true) - $this->timer_start, 5 ).' seconds -->';
		}
		
		// show the edit link on slider output
		if( current_user_can( 'edit_fa_items', $this->slider->ID ) ){
			$settings = fa_get_options( 'settings' );
			$show = (bool) $settings['edit_links'];
			/**
			 * Show slider edit link in front-end output.
			 * 
			 * @var bool - if callback returns false, edit link will be hidden
			 * @var slider_id - id of slider
			 */
			$show = apply_filters( 'fa_show_slider_edit_link', $show, $this->slider->ID );
			if( $show ){
				$edit_link = get_edit_post_link( $this->slider->ID );
				$output .= sprintf( '<a href="%s" title="%s">%s</a>',
					$edit_link,
					esc_attr( __( 'Edit slider', 'featured-articles-lite' ) ),
					__('Edit slider', 'featured-articles-lite')
				);
			}	
		}
		
		if( $echo ){
			echo $output;
		}
		
		return $output;
	}
	
	/**
	 * Callback for filter fa_get_slider_options.
	 * When a preview is displayed, the function will overwrite
	 * the slider options with the options passed over $_GET
	 */
	public function modify_preview_options( $options, $key, $slider_id ){
		// make this work only for previews
		if( !fa_is_preview() ){
			return $options;
		}
		// check that is the same slider ID
		if( $slider_id != $this->slider->ID ){
			return $options;
		}
		
		// On preview, force the review options instead of slider options
		if( !$key ){
			return $this->options;
		}else{
			return $this->options[ $key ];
		}
		return $options;
	}
	
	/**
	 * Class destructor.
	 */
	public function __destruct(){
		global $fa_slider;
		$fa_slider = false;
	}	
}