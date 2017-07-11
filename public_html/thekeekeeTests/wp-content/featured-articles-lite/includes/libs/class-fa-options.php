<?php

/**
 * Manages slider options
 *
 */
class FA_Slider_Options extends FA_Options{
	
	/**
	 *  Store variables added by deprecated themes. 
	 *  Part of the compatibility layer with slider themes 
	 *  developed for version previous to 3.0
	 */
	private $deprecated = false;
	
	// store defaults
	public function __construct( $post_id = false ){
		/**
		 * The default values
		 * @var array
		 */
		$this->defaults = array(
			'slides' => array(
				/**
				 * Can have values:
				 * - post: sliders made from latest posts
				 * - mixed: manually selected slides from posts, pages and custom slides
				 * - image: manually selected slides made from media images
				 */
				'type' => 'post',
				/**
				 * For slides made of posts, set the maximum number of slides
				 */
				'limit' => 5,
				/**
				 * For slides made of posts, set the tags to get posts from.
				 * Includes regular post categories
				 */
				'tags' => array(),
				/**
				 * For slides made of posts, set the authors to get slides from
				 */
				'author' => 0,
				/**
				 * Can have values:
				 * - date: order by publish date descending
				 * - comments: order by number or comments descending
				 * - random: random order each time
				 */
				'orderby' => 'date',
				/**
				 * Array of manually selected post ids to be used as slides.
				 * Used when slides['type'] = 'mixed'
				 * 
				 * Post ids are stored as: post_order => post_id
				 * ie: 0=>post_id, 1=>other_post_id ...
				 */
				'posts' 		=> array()
			),
			
			/**
			 * Slides content options
			 */			
			// all image options
			'content_image' => array(
				'show'			=> true, 		// display or not the image
				'preload'		=> false, 		// preload images
				'show_width' 	=> true, 		// put width attribute on image
				'show_height' 	=> true, 		// put height attribute on image
				'clickable' 	=> false, 		// image can be clicked and it will take the user to the designated URL
				'sizing' 		=> 'wp', 		// image size can be taken from default WP sizes or entered as custom size ( values: wp/custom )
				'width'			=> 0, 			// custom image size resize
				'height'		=> 0, 			// custom image size resize
				'wp_size'		=> 'full', // if sizing is wp, store the registered image size				
			),
			
			// all slide title options
			'content_title' => array(
				'show' 			=> true, // display the post title
				'use_custom' 	=> true, // use custom title stored by the plugin if any
				'clickable' 	=> true, // title can be clicked and it will take the user to the designated URL
			),
			// all slide content options
			'content_text' => array(
				'show' 				=> true, 		// display the content or not
				'use'				=> 'custom', 	// where should the text be pulled from ( values: content, excerpt, custom )
				'allow_tags' 		=> '<p>,<a>', 	// HTML tags that will be allowed in post content
				'allow_all_tags' 	=> false, 		// allow all HTML tags from post content
				'strip_shortcodes' 	=> true, 		// remove all shortcodes from post content
				'max_length' 		=> 250, 		// maximum text length to be pulled from content and used as slide text
				'max_length_noimg' 	=> 500, 		// maximum content length for slides without image
				'end_truncate' 		=> '...', 		// a text to close the truncated post text
			),
			// the read more link
			'content_read_more' => array(
				'show' => true, 					// display read more or not
				'text' => __('Read more', 'featured-articles-lite'), // text for read more
			),
			// post date option
			'content_date' => array(
				'show' => false, // display the post date
			),
			// post author 
			'content_author' => array(
				'show' 		=> false, 	// display the post author
				'link' 		=> true, 	// link to post author page
			),
			// end content options
 			
			/**
			 * JavaScript slider options
			 */
			'js' => array(
				'auto_slide' 		=> false, 	// change slides automatically
				'slide_duration' 	=> 5, 		// how long should a slide stay on when auto is true (in seconds)
				'effect_duration'	=> .6, 		// duration of effect when changing slides (in seconds)
				'click_stop'		=> false, 	// when navigation elements are clicked, autoslide is stopped
				'cycle'				=> true,	// go to first slide or last when reaching edges
				'distance_in'		=> 0, 		// the distance a slide enters from when sliding in (in pixels)
				'distance_out'		=> 0, 		// the distance to slide to when sliding out (in pixels)
				'position_in'		=> 'left', 	// the side from where the slide should come in (left, right, top, bottom)
				'position_out'		=> 'right', // the side from where the slide should go out (left, right, top, bottom)
				'event'				=> 'click', // the navigation event (click or over)
				'effect'			=> '', 		// the slide effect for sliders having full sized images
			),
			
			/**
			 * Slider layout options
			 */
			'layout' => array(
				'class'				=> '',		// for themes implementing the extra class functionality, stores the value set by user
				'show_title'		=> false, 	// display title of slider
				'width'				=> 900, 	// slider width
				'height'			=> 500, 	// slider height
				'full_width'		=> true, 	// slider should go fullwidth
				'height_resize'		=> false,	// the height set on the slider will also be the maximum height the slider can have
				'font_size'			=> '100%',	// size of fonts in slider
				'center'			=> true,	// align slider horizontally centered
				'show_main_nav'		=> true, 	// display main navigation
				'show_side_nav'		=> true, 	// display sideways navigation
				'margin_top'		=> 0,
				'margin_bottom'		=> 0,				
			),
			
			/**
			 * Slider theme
			 */
			'theme' => array(
				/**
				 * The theme selected for the slider
				 */
				'active' 	=> 'simple',
				/**
				 * The theme color for the slider
				 */
				'color'		=> 'default',
				/**
				 * The theme details
				 */
				'details'	=> array() // by default, the theme details are empty
			),
			
			/**
			 * Allowed posts for display in dynamic area
			 */
			'display' => array(
				'posts' 		=> array(), // array of post ID's where a slider published in a dynamic area is allowed to be displayed
				'tax'			=> array(), // array of tax ID's archive pages where a slider published in a dynamic area is allowed to be displayed
				'home'			=> false, // slider should be displayed on homepage or not
				'everywhere' 	=> false, // slider will be displayed on all blog pages
				'all_pages'		=> false, // slider will be displayed on all pages
				'all_categories'=> false
			)
		);
		
		/**
		 * Filter to extend the slider options with extra options from
		 * slider themes.
		 * 
		 * @param array - an empty array
		 */
		$extra = apply_filters('fa_extra_slider_options', array());
		
		// use a function to add the old filter that allows themes to add their own variables
		$deprecated = _deprecated_fa_extend_options( array() );
		
		if( $deprecated && is_array( $deprecated ) ){
			// store deprecated themes variables
			$this->deprecated = $deprecated;
			$extra = array_merge( $deprecated, $extra );
		}
		
		// incorporate the themes variables into the slider default options
		$this->defaults['themes_params'] = (array) $extra;		
		// parent class arguments
		$args = array(
			'post_id' 			=> $post_id,
			'option_name' 		=> '_fa_slider_settings',
			'option_type'		=> 'post_meta',
			'option_default'	=> 	$this->defaults
		);

		parent::__construct( $args );		
	}
	
	/**
	 * Get the option
	 * @param int $post_id - post ID to get option for
	 * @param string $key - key from options to return
	 */
	public function get_option( $post_id = false, $key = false ){
		if( $post_id ){
			parent::set_post_id( $post_id );
		}
		
		$options = parent::get_the_option();
		foreach( $options as $k => $v ){
			if( !isset( $this->defaults[ $k ] ) ){
				unset( $options[ $k ] );
				continue;
			}
			
			if( is_array( $v ) ){
				$options[ $k ] = wp_parse_args( $v, $this->defaults[ $k ] );
			}
		}
		
		// for themes params, merge the defaults with the options retrieved from DB
		// this is needed in case the theme implements new other fields to any existing ones
		foreach( $this->defaults['themes_params'] as $theme => $params ){
			if( isset( $options['themes_params'][ $theme ] ) ){
				$options['themes_params'][ $theme ] = wp_parse_args( $options['themes_params'][ $theme ], $params );
			}	
		}
		
		if( $key && array_key_exists( $key, $options ) ){
			return $options[ $key ];
		}
		
		return $options;	
	}
	
	/**
	 * Updates the slider options
	 * @param int $post_id
	 * @param array $values
	 */
	public function update_option( $post_id, $values  ){
		if( !current_user_can( 'manage_options' ) && !current_user_can( 'edit_fa_items', $post_id ) ){
			wp_die( __('You are not allowed to do this.', 'featured-articles-lite'), __('Not allowed', 'featured-articles-lite') );
		}
		// get the defaults; also sets the post ID in parent class
		$defaults = $this->get_option( $post_id );
		// stores theme details
		$theme_details = array();
		
		// iterate the defaults and check if variables are set
		foreach( $defaults as $key => $vals ){
			// iterate the group defaults			
			foreach( $vals as $field => $default_value ){
				// if value isn't set in class defaults, skip it
				if( !isset( $this->defaults[ $key ][ $field ] ) ){
					continue;
				}
				
				// get the details of the theme set as active
				if( 'theme' == $key && 'active' == $field ){					
					if( isset( $values[ $key ][ $field ] ) ){
						$theme = $values[ $key ][ $field ];
						$theme_details = fa_get_theme($theme);										
					}
				}
				
				// process themes params
				if( 'themes_params' == $key ){
					// Deprecaterd themes parameters processing
					if( $this->deprecated && is_array( $this->deprecated ) ){
						if( array_key_exists( $field , $this->deprecated ) ){
							foreach( $this->deprecated[ $field ] as $f => $v ){
								if( is_bool( $v ) ){
									$defaults[ $key ][ $field ][ $f ] = isset( $values[ $f ] );
									continue;
								}								
								if( isset( $values[ $f ] ) ){
									$defaults[ $key ][ $field ][ $f ] = $values[ $f ];
								}
							}
							continue;
						}
					}
					// end deprecated themes params processing
					foreach( $default_value as $f => $v ){
						if( is_numeric( $v ) ){
							if( isset( $values[ $key ][ $field ][ $f ] ) ){
								$defaults[ $key ][ $field ][ $f ] = (int) $values[ $key ][ $field ][ $f ];
							}
							continue;
						}
						if( is_bool( $v ) ){
							$defaults[ $key ][ $field ][ $f ] = isset( $values[ $key ][ $field ][ $f ] ) && $values[ $key ][ $field ][ $f ];
							continue;	
						}
						if( isset( $values[ $key ][ $field ][ $f ] ) ){
							$defaults[ $key ][ $field ][ $f ] = $values[ $key ][ $field ][ $f ];
						}						
					}					
					continue;
				}
				
				if( is_int( $this->defaults[ $key ][ $field ] ) ){
					if( isset( $values[ $key ][ $field ] ) ){
						$defaults[ $key ][ $field ] = (int) $values[ $key ][ $field ];
					}
					continue;
				}
				if( is_bool( $this->defaults[ $key ][ $field ] ) ){
					$defaults[ $key ][ $field ] = isset( $values[ $key ][ $field ] ) && $values[ $key ][ $field ] ;
					continue;	
				}
				if( isset( $values[ $key ][ $field ] ) ){
					$defaults[ $key ][ $field ] = $values[ $key ][ $field ];
				}				
			}
		}
		
		// store the theme details in slider settings
		$defaults['theme']['details'] = $theme_details;
		
		/**
		 * Action on slide settings save
		 * 
		 * @param int $post_id - id of post being saved
		 * @param array $defaults - the new options
		 * @param arrat $value - the values passed to be saved
		 */
		do_action('fa-save-slider-options', $post_id, $defaults, $values);
		
		return parent::update_the_option( $defaults );		
	}
	
	/**
	 * Return default settings for sliders
	 */
	public function get_defaults(){
		return $this->defaults;
	}
}

/**
 * Manages options setting/getting for slides 
 */
class FA_Slide_Options extends FA_Options{
	// store default values
	private $defaults;
	
	/**
	 * Constructor
	 * @param int $post_id
	 */
	public function __construct( $post_id = false ){
		
		$this->defaults = array(
			'title'			=> '',
			'content'		=> '',
			'link_to_post'	=> true, // for other post types than fa custom slide, the slide can link to original post URL or a different URL
			'link_text' 	=> '', // read more link text
			'class'			=> '', // a custom CSS class that can be used to style a particular slide differently
			/**
			 * Image ID for image set by user to be used as slide image
			 */
			'image'			=> '', // store image ID to be used as slider image
			/**
			 * Image ID for image detected in post content and also found in media gallery.
			 */
			'temp_image_id'	=> '', // store image ID detected in post content if any
			/**
			 * Image URL for image detected in post content but that couldn't be identified in media gallery
			 */
			'temp_image_url'=> '', // store image URL detected in post content if image couldn't be found in media gallery		
		);
		
		// parent class arguments
		$args = array(
			'post_id' 			=> $post_id,
			'option_name' 		=> '_fa_slide_settings',
			'option_type'		=> 'post_meta',
			'option_default'	=> 	$this->defaults
		);		
		parent::__construct( $args );
		
	}
	
	/**
	 * Get the slide options. If passing a post ID, the parent class will be refreshed and 
	 * the option will be retrieved for the given ID.
	 * 
	 * @param bool/int $post_id
	 */
	public function get_option( $post_id = false ){
		if( $post_id ){
			parent::set_post_id( $post_id );
		}
		
		$post_id = parent::get_post_id();
		$post = get_post( $post_id, ARRAY_A );
		
		$options = parent::get_the_option();
		foreach( $options as $k => $v ){
			if( 'title' == $k || 'content' == $k ){
				// on auto draft, don't put any defaults
				if( 'auto-draft' == $post['post_status'] ){
					continue;
				}				
				// if title isn't set on meta show the post title and content
				if( empty( $v ) ){
					$options[ $k ] = $post[ 'post_' . $k ];
					continue;
				}
			}
						
			if( is_array( $v ) ){
				$options[ $k ] = wp_parse_args( $v, $this->defaults[ $k ] );
			}
		}
		
		return $options;
	}
	
	/**
	 * Updates slide options for given post id
	 * @param int $post_id
	 * @param array $value
	 */
	public function update_option( $post_id, $value ){
		if( !current_user_can( 'manage_options' ) && !current_user_can( 'edit_fa_items', $post_id ) ){
			wp_die( __('You are not allowed to do this.', 'featured-articles-lite'), __('Not allowed', 'featured-articles-lite') );
		}
		
		$defaults = $this->get_option( $post_id );
		
		foreach( $this->defaults as $k => $v ){
			
			if( is_array( $v ) ){
				foreach( $v as $kk => $vv ){
					if( is_numeric( $vv ) ){
						if( isset( $value[ $k ][ $kk ] ) ){
							$defaults[ $k ][ $kk ] = (int)$value[ $k ][ $kk ];
						}
					}
					if( is_bool( $vv ) ){
						if( 'iv_load_policy' == $kk ){
							$defaults[ $k ][ $kk ] = isset( $value[ $k ][ $kk ] ) ? 3 : 1;
							continue;
						}						
						$defaults[ $k ][ $kk ] = isset( $value[ $k ][ $kk ] );
					}
					if( isset( $value[ $k ][ $kk ] ) ){
						$defaults[ $k ][ $kk ] = $value[ $k ][ $kk ];
					}
				}				
				continue;
			}
						
			if( is_numeric( $v ) ){
				if( isset( $value[ $k ] ) ){
					$defaults[ $k ] = (int)$value[ $k ];
				}
			}
			if( is_bool( $v ) ){
				$defaults[ $k ] = isset( $value[ $k ] ) && $value[ $k ];
			}
			if( isset( $value[ $k ] ) ){
				$defaults[ $k ] = $value[ $k ];
			}			
		}
		
		/**
		 * Action on slide settings save
		 * 
		 * @param int $post_id - id of post being saved
		 * @param array $defaults - the new options
		 * @param arrat $value - the values passed to be saved
		 */
		do_action('fa_save_slide_options', $post_id, $defaults, $value);
		
		return parent::update_the_option( $defaults );
	}	
}

/**
 * Class to manage plugin options
 *
 */
class FA_Plugin_Options extends FA_Options {
	/**
	 * Stores the default values
	 * @var array
	 */
	private $defaults;
	
	/**
	 * Constructor, instantiates the parent class
	 */
	public function __construct(){
		
		$this->defaults = array(
			/**
			 * Stores plugin details on activation. Helpful on plugin updates to allow the plugin
			 * to update settings depending on version (if needed)
			 */
			'plugin_details' => array(
				'version' 		=> '', // plugin version installed
				'wp_version' 	=> '', // wp version on plugin activation
				'activated_on'	=> '', // date of activation				
			),
			/**
			 * Plugin general settings
			 */
			'settings' => array(
				'complete_uninstall' => false, // perform a complete unistall
				'post_slide_edit'	 => true, // load slide edit meta boxes on post/page edit screen
				'load_in_wptouch'	 => false, // load slideshows on mobile version of wp touch
				'themes_dir' 		 => '', // extra slideshow themes relative folder path (must be inside wp-content folder)
				'edit_links'		 => false, // when true, it will display an edit link under the slider for logged in users that can edit
				'preload_sliders'	 => false, // when true, it will load a small script and some styles into the head section of the website to preload any existing sliders
				'load_font_awesome'	 => true, // when true, if slider themes require font awesome it will be enqueued
				'lite_admin_menu'	 => false, // when true will remove PRO pages from admin menu
				'allow_image_autodetect' => true, // when true, if no image is found on a slide the plugin will check the post contents and see if an image can be found
			),
			/**
			 * Part of hooks management feature. Stores different hooks that can be used to display the slider
			 * in various sections of the WP theme.
			 */
			'hooks' => array(
				'loop_start' => array(
					'name' 			=> __('Above posts loop', 'featured-articles-lite'),
					'description'	=> __('Displays above the posts loop in current page', 'featured-articles-lite'),
					'sliders' 		=> array()
				)
			),
			/**
			 * Stores if plugin updated from a previous version 
			 */
			'updated' => array(
				'from' 	=> false,
				'to' 	=> false
			)
		);
		
		/**
		 * Filter settings key to set up extra themes folder path.
		 */
		add_filter('fa-set-options_settings', array( $this, 'update_themes_path' ), 1, 4);
		
		$args = array(
			'option_name' 		=> 'fa_plugin_options',
			'option_default' 	=> $this->defaults,
			'option_type'		=> 'wp_option'
		);		
		parent::__construct( $args );
	}
	
	/**
	 * Processes the extra themes folder path setting.
	 * Ran by filter fa-set-options_plugin_details set in class constructor.
	 * 
	 * @param array $processed - values processed by default by the update function
	 * @param array $values - the values that must be saved
	 * @param string $key - the key in plugin options array that needs to be saved
	 * @param array $option - the complete option from database
	 */
	public function update_themes_path( $processed, $values, $key, $option ){
		// process extra themes path
		if( isset( $values['themes_dir'] ) ){
			if( empty( $values['themes_dir'] ) ){
				$processed['themes_dir'] = '';
				return $processed;
			}
			// full path to directory
			$full_path = wp_normalize_path( path_join(WP_CONTENT_DIR, $values['themes_dir']) );
			if( is_dir( $full_path ) ){
				$processed['themes_dir'] = wp_normalize_path( $values['themes_dir'] );
				return $processed; 
			}else{
				// if not dir, revert to previous setting
				$processed['themes_dir'] = $option['themes_dir'];
			}			
		}else{
			/**
			 * Add themes path to processed values if not in values.
			 * Used on plugin activation when the plugin details are stored.
			 */
			$processed['themes_dir'] = $option['themes_dir'];
		}
		
		return $processed;
	}
	
	/**
	 * Get a key from plugin options. Possible values:
	 * 
	 * - display: get the categories/pages/home slideshows
	 * - plugin_details : get the plugin details set on plugin activation
	 * - settings: get the plugin settings set in plugin Settings page
	 * - hooks: get the plugin hooks (part of hooks management plugin feature
	 */
	public function get_option( $key = false ){		
		$option = parent::get_the_option();
		
		if( !$key ){
			return $option;
		}
		
		if( array_key_exists($key, $option) ){
			return wp_parse_args( $option[ $key ], $this->defaults[ $key ]);
		}else{
			trigger_error( sprintf(__('Key %s not found in plugin options.', 'featured-articles-lite'), $key), E_USER_NOTICE);
		}		
	}
	
	/**
	 * Updates a given key in plugin options
	 * @param string $key
	 * @param mixed $value
	 */
	public function update_option( $key, $value ){
		
		if( !current_user_can( 'manage_options' ) ){
			wp_die( __('You are not allowed to do this.', 'featured-articles-lite'), __('Not allowed', 'featured-articles-lite') );
		}
		
		if( !$key ){
			trigger_error( __('No option key specified.'), E_USER_WARNING );
			return false;
		}
		if( !$value ){
			trigger_error( sprintf(__('No value specified for option key %s', 'featured-articles-lite'), $key), E_USER_WARNING );
			return false;
		}
		if( !array_key_exists($key, $this->defaults) ){
			trigger_error( sprintf(__('Key %s not found in options','featured-articles-lite'), $key), E_USER_WARNING );
			return false;
		}
		// get the defaults
		$defaults = $this->defaults[ $key ];
		// get all stored options	
		$option = $this->get_option();
		
		// processing the entered data
		foreach ( $defaults as $k => $v ){
			if( is_numeric( $v ) ){
				if( isset( $value[ $k ] ) ){
					$defaults[ $k ] = (int)$value[ $k ];
				}
			}
			if( is_bool( $v ) ){
				$defaults[ $k ] = isset( $value[ $k ] ) && $value[ $k ];
			}
			if( isset( $value[ $k ] ) ){
				$defaults[ $k ] = $value[ $k ];
			}			
		}
		
		/**
		 * Filter the values to be saved
		 * 
		 * @param $defaults: values processed by default
		 * @param $value : raw value
		 * @param $key : options key in plugin options to be updated
		 * @param $defaults: the processed values
		 * 
		 * @var array
		 */
		$defaults = apply_filters('fa-set-options_'.$key, $defaults, $value, $key, $option[ $key ]);
		
		// in case of errors, applied filters should return a WP error
		if( is_wp_error( $defaults ) ){
			return $defaults;
		}
		
		$option[ $key ] = $defaults;
		$updated = parent::update_the_option( $option );
		
		/**
		 * Action after saving options
		 * 
		 * @param $defaults: values processed by default
		 * @param $value : raw value
		 * @param $key : options key in plugin options to be updated
		 * @param $defaults: the processed values
		 * 
		 * @var array
		 */
		do_action('fa-updated-options_'.$key, $defaults, $value, $key, $option[ $key ]);
		
		return $updated;
	}
}

/**
 * General management class
 */
class FA_Options{
	/**
	 * Store option name
	 * @var string
	 */
	private $option_name;
	/**
	 * Store option default values
	 * @var mixed
	 */
	private $option_default;
	/**
	 * Type of option to retrieve. Possible values: post_meta or wp_option
	 * @var string
	 */
	private $option_type;
	/**
	 * The post ID if retrieveing post meta
	 * @var int
	 */
	private $post_id;
	/**
	 * Stores the retrieved option
	 * @var mixed
	 */
	private $option = NULL;
	
	/**
	 * Class constructor. Takes an array of arguments:
	 * 
	 * - option_name: name of the option to be retrieved
	 * - option_default: default option value (optional)
	 * - option_type: the type of option to retrieve (can be post_meta for posts or wp_option for general options)
	 * - post_id: in case post meta is retrieved, it will need the post ID to retrieve meta from
	 * 
	 * @param unknown_type $args
	 */
	public function __construct( $args = array() ){
		// defaults
		$defaults = array(
			'option_name' 		=> false,
			'option_default' 	=> false,
			'option_type' 		=> false, // possible values: post_meta or wp_option
			'post_id'			=> false, // optional for post meta
		);
		
		if( !$args ){
			trigger_error( __('No class arguments specified.', 'featured-articles-lite'), E_USER_NOTICE );
			return;
		}
		
		// mix the two, arguments with defaults
		$args = wp_parse_args( $args, $defaults );
		// check option name
		if( !$args['option_name'] ){
			trigger_error(__('No option name specified.', 'featured-articles-lite'), E_USER_NOTICE);
			return;
		}
		// check option type
		if( !$args['option_type'] ){
			trigger_error(__('No option type specified', 'featured-articles-lite'), E_USER_NOTICE);
			return;
		}
				
		$this->option_name 		= $args['option_name'];
		$this->option_default 	= $args['option_default'];
		$this->option_type 		= $args['option_type'];
		$this->post_id			= absint( $args['post_id'] );
	}
	
	/**
	 * Get option
	 */
	public function get_the_option(){
		if( !is_null( $this->option ) ){
			return $this->option;
		}		
		$option = NULL;		
		switch( $this->option_type ){
			// get WP option
			case 'wp_option':
				$option = get_option( $this->option_name, $this->option_default );
			break;
			// get post option
			case 'post_meta':
				$option = get_post_meta( $this->post_id, $this->option_name, true );				
			break;
			// only 2 types allowed, return error for anything else
			default:
				trigger_error(__('No option type specified', 'featured-articles-lite'), E_USER_NOTICE);
			break;	
		}
		
		if( !is_null($option) && is_array( $this->option_default ) ){
			if( !is_array( $option ) ){
				return $this->option_default;
			}
			$option = wp_parse_args( (array)$option, $this->option_default );
		}
		$this->option = $option;	
		return $option;		
	}
	
	/**
	 * Updates the option with a given value
	 * @param mixed $value
	 */
	public function update_the_option( $value ){
		if( !$value ){
			trigger_error(__('No value passed to be saved.', 'featured-articles-lite'), E_USER_WARNING);
			return;
		}
		switch( $this->option_type ){
			// get WP option
			case 'wp_option':
				$result = update_option( $this->option_name, $value );
			break;
			// get post option
			case 'post_meta':
				//$result = update_post_meta( $this->post_id, $this->option_name, $value );		
				$result = update_metadata('post', $this->post_id, $this->option_name, $value );			
			break;
			// only 2 types allowed, return error for anything else
			default:
				trigger_error(__('No option type specified', 'featured-articles-lite'), E_USER_NOTICE);
			break;	
		}

		if( isset( $result ) && $result ){
			$this->reset_option();
			return $result;
		}
		return false;
	}
	
	/**
	 * On option update, it resets $this->option with the new values
	 */
	private function reset_option(){
		$this->option = null;
		$this->get_the_option();
	}
	
	/**
	 * Setter - sets a new post ID
	 * @param int $post_id
	 */
	public function set_post_id( $post_id ){
		if( is_object( $post_id ) ){
			$post_id = $post_id->ID;
		}
		
		if( $this->post_id != $post_id ){
			$this->post_id = absint( $post_id );
			$this->reset_option();
		}
	}
	
	/**
	 * Getter - returns the current post id
	 */
	public function get_post_id(){
		return $this->post_id;
	}
	
}