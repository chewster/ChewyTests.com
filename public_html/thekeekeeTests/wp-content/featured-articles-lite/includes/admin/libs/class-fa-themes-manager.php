<?php
/**
 * Slideshow themes manager.
 * Solves paths and includes any neccessary files.
 */
class FA_Themes_Manager{
	
	/**
	 * Holds the path for the default themes that come with the plugin
	 * @var string
	 */
	private $default_themes_path;
	/**
	 * Holds the URL to the default themes folder
	 * @var string
	 */
	private $default_themes_url;	
	/**
	 * Holds the path for the extra themes created by the user 
	 * @var string
	 */
	private $extra_themes_path;
	/**
	 * Holds the URL to extra themes folder
	 * @var string
	 */
	private $extra_themes_url;
	/**
	 * Holds the existing slideshow themes
	 * @var array
	 */
	private $themes = null;
	
	/**
	 * Constructor
	 */
	public function __construct(){
		// default themes path and URL
		$this->default_themes_path 	= fa_get_path( 'themes' );
		$this->default_themes_url 	= fa_get_uri( 'themes' );
		
		// extra themes path and URL
		$option 	= fa_get_options( 'settings' );		
		
		$rel_path 	= isset( $option['themes_dir'] ) ? $option['themes_dir'] : false;
		if( $rel_path ){
			$path = wp_normalize_path( path_join( WP_CONTENT_DIR , $rel_path) );
			if( $path != $this->default_themes_path && is_dir( $path ) ){
				$this->extra_themes_path 	= $path;
				$this->extra_themes_url 	= ( is_ssl() ? str_replace('http://', 'https://', WP_CONTENT_URL) : WP_CONTENT_URL ).'/'.$rel_path;
			}			
		}		
	}	
	
	/**
	 * Get all themes, both default themes that can stay inside the plugin folder
	 * and also the other extra themes that the user can save into an external folder
	 * to prevent losing them when performing plugin updates.
	 * 
	 * @return array - an array of themes and their details
	 */
	public function get_themes(){
		
		if( null !== $this->themes ){
			return $this->themes;
		}
		
		$this->themes = $this->read_themes( 'default' );
		$extra_themes = $this->read_themes( 'extra' );		
		$this->themes = array_merge( $this->themes, $extra_themes );
		
		/**
		 * Include themes functions file to allow hooks and filters to run.
		 */
		foreach( $this->themes as $name => $theme ){
			if( isset( $theme['funcs'] ) && is_file( $theme['funcs'] ) ){
				include_once $theme['funcs'];
			}
				
			/**
	         * Filter on theme details. Allows slideshow themes to add their own information
	         * that will be displayed when user is editing a slideshow.
	         * @var array
	         */
			$theme_details = apply_filters('fa-theme-details-'.$name, $this->default_headers());
			$this->themes[$name]['theme_config'] = wp_parse_args( $theme_details, $this->default_headers() );
			
			if( !isset( $this->themes[ $name ]['theme_config']['name'] ) || empty( $this->themes[ $name ]['theme_config']['name'] ) ){				
				// create the theme name
	        	$this->themes[$name]['theme_config']['name'] = ucfirst( str_replace('_', ' ', $name) );
			}		
			
			// add the theme message. This will be displayed when user selects a theme
			$this->themes[$name]['theme_config']['message'] = preg_replace("|(\s)|", " ", $this->themes[$name]['theme_config']['message']);
			
		}

		return $this->themes;
	}
	
	/**
	 * Returns details about a theme
	 * 
	 * @param string $theme
	 * @return array/WP_Error - array of details if theme is found or WP error
	 */
	public function get_theme( $theme ){
		$themes = $this->get_themes();
		if( array_key_exists( $theme, $themes ) ){
			return $themes[ $theme ];
		}
		
		$error = new WP_Error();
		$error->add('fa-theme-not-found', sprintf(__('Theme %s not found.', 'featured-articles-lite'), $theme));
		return $error;
	}
	
	/**
	 * Reads the themes folders and loads the slideshow themes.
	 * Can read the default themes folder from inside the plugin or the extra themes created by 
	 * the user.
	 * 
	 * @param string $which - default: read default themes folder; extra: read extra themes folder if any
	 */
	private function read_themes( $which = 'default' ){
		$base_path 	= $which === 'default' ? $this->default_themes_path : $this->extra_themes_path;
		$base_url 	= $which === 'default' ? $this->default_themes_url : $this->extra_themes_url;
		$themes 	= array();
		// if base path is false (in case of extra themes for example), means the user didn't used this option
		if( !$base_path ){
			return $themes;
		}
		
		$theme_folders = $this->read_dir( $base_path );
		foreach ($theme_folders as $theme){
			
			// theme path and URL
			$theme_path = path_join( $base_path, $theme );
			$theme_url	= $base_url . '/' . $theme;
			
			if( !is_dir( $theme_path ) ){
				continue;
			}
			
			// theme should have a display.php file. If it doesn't, skip it.
			$display_file = path_join( $theme_path, 'display.php' );
			if( !is_file( $display_file ) ){
				continue; // stop processing this theme. It doesn't have a display.php file
			}else{
				$themes[ $theme ]['display'] = $display_file;
			}
			
			// put path and url to theme folder in theme details
			$themes[ $theme ]['path'] = $theme_path;
			$themes[ $theme ]['url'] = $theme_url;
			$themes[ $theme ]['dir'] = $theme;
			
			// check for functions file
	        $functions_file = path_join( $theme_path, 'functions.php' );
	        if( is_file( $functions_file ) ){
	        	// include functions file
	        	//include_once $functions_file;	
	        	$themes[ $theme ]['funcs'] = $functions_file;
	        }else{
	        	$themes[ $theme ]['funcs'] = false;
	        }
	        
	        // set default theme info
			$themes[ $theme ]['theme_config'] = $this->default_headers();
			
			// theme preview image
	        $preview_image = path_join( $theme_path, 'preview.jpg' );
	        if( is_file( $preview_image ) ){
	        	$themes[ $theme ]['preview'] = $theme_url.'/preview.jpg';        	
	        }else{
	        	$themes[ $theme ]['preview'] = false;        	
	        }
			
	        // color stylesheets
	        $colors_path = path_join( $theme_path, 'colors' );
		    $colors = $this->read_dir( $colors_path );
			$formatted_colors = array();
		    
		    // accept only files having extension css
		    foreach( $colors as $ck => $color ){
		    	$ext = strtolower( substr($color, -3, 3) );
		    	if( 'css' != $ext || '.min.css' == strtolower( substr( $color, -8, 8 ) ) ){
		    		unset( $colors[ $ck ] );
		    	}else{
		    		$key = str_replace('.css', '', $color);		    		
		    		$formatted_colors[ $key ] = array(
		    			'name' 	=> ucfirst( str_replace(array('-', '_'), ' ', $key) ),
		    			'url'	=> path_join( $theme_url , 'colors/' . $color) 
		    		);
		    	}
		    }		    
		    $themes[$theme]['colors'] = $formatted_colors;		        
		}	
			
		return $themes;		
	}
	
	/**
	 * Default theme details
	 */
	private function default_headers(){
		 // default theme information		
		$default_headers = array(
        	'author'		=>'',
        	'author_uri'	=>'', 
        	'copyright'		=>'', 
        	'compatibility'	=> '', 
			'version'		=> '',
        	'name'			=> '',
        	'fields'		=> array(),
			'classes'		=> array(),
			'colors'		=> array(),
			'stylesheets'	=> array(),
			'message'		=> ''
        );		
		return $default_headers;
	}
	
	/**
	 * Reads a given folder path
	 * @param path to folder $path
	 * @param array $exclude - filenames to exclude
	 */
	private function read_dir(  $path, $exclude = array()  ){	
		$path 	= wp_normalize_path( $path );
		$result = array();	
		// if not a directory, bail out	
		if( !is_dir($path) ){
			return $result;
		};
		
		$not 	 = array( '.', '..' );
		$exclude = array_merge( $exclude, $not );	
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ){
				if( in_array( $file, $exclude ) || substr( $file, 0, 1 ) == '.' || substr( $file, 0, 1 ) == '_' ) {
					continue;
				}
				$result[] = $file;
			}		
		}
		return $result;
	}	
}