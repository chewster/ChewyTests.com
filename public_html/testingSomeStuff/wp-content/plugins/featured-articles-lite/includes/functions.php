<?php

// add WP 3.9.0 wp_normalize_path if unavailable
if( !function_exists('wp_normalize_path') ){
	/**
	 * Normalize a filesystem path.
	 *
	 * Replaces backslashes with forward slashes for Windows systems,
	 * and ensures no duplicate slashes exist.
	 *
	 * @since 3.9.0
	 *
	 * @param string $path Path to normalize.
	 * @return string Normalized path.
	 */
	function wp_normalize_path( $path ) {
		$path = str_replace( '\\', '/', $path );
		$path = preg_replace( '|/+|','/', $path );
		return $path;
	}
}

/**
 * Used for file includes, it generates an absolute path within the plugin 
 * folder from a given relative path.
 * 
 * @uses path_join
 * @uses wp_normalize_path
 * 
 * @param string $rel_path - relative path to file
 * @return string - absolute path to file
 */
function fa_get_path( $rel_path ){
	$path = path_join( FA_PATH, $rel_path );
	return wp_normalize_path( $path );
}

/**
 * Generates a complete URL to files located inside the plugin folder.
 * 
 * @param string $rel_path - relative path to file
 * @return string - complete URL to file
 */
function fa_get_uri( $rel_path ){
	$uri = is_ssl() ? str_replace('http://', 'https://', FA_URL) : FA_URL;	
	$path = path_join( $uri, $rel_path );
	return $path;
}

/**
 * Returns slider post type
 */
function fa_post_type_slider(){
	global $fa_lite;
	return $fa_lite->post_type_slider();
}

/**
 * Checks if preview slider variable is set
 */
function fa_is_preview(){
	if( isset( $_GET['fa_slider_preview'] ) ){
		if( current_user_can('edit_fa_items') ){
			return true;
		}
	}	
	return false;	
}

/**
 * Starts an instance of plugin options class
 */
function require_fa_options( $type = false ){
	global $fa_plugin_options, $fa_slide_options, $fa_slider_options;
	if( !class_exists( 'FA_Plugin_Options' ) ){
		require_once fa_get_path( 'includes/libs/class-fa-options.php' );
	}
	
	switch( $type ){
		case 'plugin':
			if( !isset( $fa_plugin_options ) ){
				$fa_plugin_options = new FA_Plugin_Options();
			}
			return $fa_plugin_options;	
		break;
		case 'slide':
			if( !isset( $fa_slide_options ) ){
				$fa_slide_options = new FA_Slide_Options();
			}
			return $fa_slide_options;
		break;
		case 'slider':
			if( !isset( $fa_slider_options ) ){
				$fa_slider_options = new FA_Slider_Options();
			}
			return $fa_slider_options;
		break;	
	}	
}

/**
 * Get a plugin options set
 * 
 * @param string $option. Values:

 * - display: get the categories/pages/home slideshows
 * - plugin_details : get the plugin details set on plugin activation
 * - settings: get the plugin settings set in plugin Settings page
 * - hooks: get the plugin hooks (part of hooks management plugin feature
 * - license: get the plugin license key and license activation date
 * - theme_editor_preview: the theme editor preview settings 
 * 
 * @return array
 */
function fa_get_options( $key = false ){
	$plugin_options = require_fa_options( 'plugin' );
	if( !$key ){
		return $plugin_options->get_option();
	}
	return $plugin_options->get_option( $key );
}

/**
 * Updates an option
 * @param string $key - key to update
 * @param mixed $value - value to update with
 */
function fa_update_options( $key, $value ){
	if( !current_user_can('manage_options') ){
		return;
	}
	$plugin_options = require_fa_options( 'plugin' );
	return $plugin_options->update_option( $key, $value );
}

/**********************************
 * Slide options
 **********************************/

/**
 * Get slide options.
 * @param bool/int $post_id - id of post to get options from
 */
function fa_get_slide_options( $post_id = false ){
	if( !$post_id ){
		global $post;
		if( !$post ){
			return false;
		}
		$post_id = $post->ID;
	}
	
	$slide_options = require_fa_options( 'slide' );
	$opt = $slide_options->get_option( $post_id );
	
	/**
	 * Filter on slide settings retrieval
	 * 
	 * @param int $options - array of slide options
	 * @param object $post - post that has the options retrieved for
	 */
	$options = apply_filters( 'fa_get_slide_options', $opt, $post_id );
	
	return $options;
}

/**
 * Update slide options with given values
 * @param int $post_id
 * @param array $value
 */
function fa_update_slide_options( $post_id, $value ){
	if( !current_user_can('manage_options') && !current_user_can( 'edit_fa_items', $post_id ) ){
		return new WP_Error('fa-not-allowed', __("You don't have the permission to update slide options."));
	}
	$slide_options = require_fa_options( 'slide' );
	return $slide_options->update_option( $post_id, $value );
}

/**
 * Truncates a text containing HTML markup. 
 * Closes all tags that remain opened after truncating to a given text length
 * @param string $string
 * @param int $length
 * @param string $ending
 */
function fa_truncate_html( $string, $length = 80, $ending = '...' ){
	if( $length == 0 ){
		return '';
	}
	
	$str_length = function_exists('mb_strlen') ? mb_strlen( preg_replace('/<.*?>/', '', $string )) : strlen( preg_replace('/<.*?>/', '', $string ) );	
	// if text without HTML is smaller than length, return the whole text
	if ( $str_length <= $length ) {
		return $string;
	}
	
	$truncated 		= '';
	$total_length 	= 0;
	$opened 		= array();
	$auto_closed 	= array('img','br','input','hr','area','base','basefont','col','frame','isindex','link','meta','param');
	
	preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $string, $tags, PREG_SET_ORDER);
	
	foreach( $tags as $tag ){
		$tag_name = strtolower( $tag[2] );
		if( !in_array( $tag_name, $auto_closed ) ){
			if ( preg_match('/<[\w]+[^>]*>/s', $tag[0] ) ){
				array_unshift( $opened, $tag[2] );
			} else if ( preg_match( '/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag ) ){
				$pos = array_search( $closeTag[1], $opened );
				if ( $pos !== false ) {
					array_splice( $opened, $pos, 1 );
				}
			}		
		}
		// if empty, it's plain text
		if( !empty( $tag[2] ) ){
			$truncated .= $tag[1];
		}	
		// calculate string length
		$string_length = function_exists( 'mb_strlen' ) ? mb_strlen( $tag[3] ) : strlen( $tag[3] );
		if( $total_length + $string_length <= $length ){
			$truncated .= $tag[3];
			$total_length += $string_length;
		}else{
			if( $total_length == 0 ){
				$truncated .= function_exists( 'mb_substr' ) ? mb_substr( $tag[3], 0, $length ) . $ending : substr( $tag[3], 0, $length ) . $ending;
				break;	
			}
			$diff = $length - $total_length;
			$truncated .= function_exists( 'mb_substr' ) ? mb_substr( $tag[3], 0, $diff ) . $ending : substr( $tag[3], 0, $diff ) . $ending;
			break;
		}		
	}
	// close all opened tags
	foreach ( $opened as $tag ) {
		$truncated .= '</'.$tag.'>';
	}
	return $truncated;
}

/**
 * Given some content, the function will returned the image ID or image URL 
 * for the first image it finds into the content. Will return false if no image
 * tag is found.
 *
 * @param string $content
 * @return boolean/string - false if no image is found, image ID or image URL if image is found
 */
function fa_detect_image( $content ){
	// check for images in text
	preg_match_all("#\<img(.*)src\=(\"|\')(.*)(\"|\')(/?[^\>]+)?\>#Ui", $content, $matches);
	// no image is available
	if( !isset($matches[0][0]) ){ 
		return false;
	}
	
	$result = array(
		'img' 	=> false, 
		'id' 	=> false
	);
	
	// get image attributes in order to determine the attachment guid
	preg_match_all("#([a-z]+)=\"(.*)\"#Ui", $matches[0][0], $attrs);
	$inversed = array_flip( $attrs[1] );
	
	// if image doesn't have width/height attributes set on it, there's no point in going further
	if( !array_key_exists( 'width', $inversed ) || !array_key_exists( 'height', $inversed ) ){
		$result['img'] = $matches[3][0];
		return $result;
	}
	
	// image attributes hold the image URL. Replace those to get the real image guid
	$img_size_url = '-'.$attrs[2][$inversed['width']].'x'.$attrs[2][$inversed['height']];
	$real_image_guid = str_replace( $img_size_url, '', $matches[3][0] );
	
	global $wpdb;
	$the_image = $wpdb->get_row( 
		$wpdb->prepare( 
			"SELECT * FROM $wpdb->posts WHERE guid = '%s' AND post_type='attachment'", 
			$real_image_guid 
		) 
	);
	// create the result
	$result['img'] = $matches[3][0];
	// if image was found, add the image ID to the result
	if( $the_image ){
		$result['id'] = $the_image->ID;				
	}
	return $result;	
}

/*************************************
 * Slider options
 *************************************/

/**
 * Displays a slider based on the slider ID
 * @param int $slider_id
 * @param bool/string $dynamic_area - if slider is displayed into a dynamic area, the parameter contains the area ID
 */
function fa_display_slider( $slider_id, $dynamic_area = false ){
	/**
	 * Filter that can prevent a slider from being displayed. 
	 * Mainly used by dynamic areas to only show sliders in post/pages/categories allowed by user
	 * in slider settings.
	 * 
	 * @param bool - show slider
	 * @param int $slider_id - ID of slider being displayed
	 * @param bool/string $dynamic_area - the area ID that the slider is set to be published in (false if not in dynamic area)
	 */
	$show = apply_filters( 'fa_display_slider' , true, $slider_id, $dynamic_area );
	if( !$show ){
		return;
	}
	
	/**
	 * Action on slider display.
	 * 
	 * @param int $slider_id
	 * @param string $dynamic_area
	 */
	do_action( 'fa_slider_display', $slider_id, $dynamic_area );
	
	/**
	 * Filters to apply on content that is displayed into slides
	 */
	add_filter( 'the_fa_content', 'wptexturize'        );
	add_filter( 'the_fa_content', 'convert_smilies'    );
	add_filter( 'the_fa_content', 'convert_chars'      );
	add_filter( 'the_fa_content', 'wpautop'            );
	add_filter( 'the_fa_content', 'shortcode_unautop'  );	
	
	
	require_once fa_get_path('includes/libs/class-fa-slider.php');
	$slider = new FA_Slider( $slider_id );
	$slider->display();
	
	if( defined( 'FA_SCRIPT_DEBUG' ) && FA_SCRIPT_DEBUG ){
		echo '<!-- Slider ID: ' . $slider_id . ' ; Dynamic area ID: ' . $dynamic_area . ' -->';	
	}	
}

/**
 * Get slider options
 * @param int $post_id - post id of slider to get options for
 * @param string $key - get a single key from options array
 */
function fa_get_slider_options( $post_id = false, $key = false ){
	if( !$post_id ){
		global $post;
		if( !$post ){
			return false;
		}
		$post_id = $post->ID;
	}
	
	// load slider themes to have the themes options injected into the plugin options
	if( function_exists('fa_get_themes') && is_admin() ){
		fa_get_themes();
	}
	
	$slider_options = require_fa_options('slider');
	$opt = $slider_options->get_option( $post_id, $key );
	/**
	 * Filter on slider options. Allow modifications of slider options if needed.
	 * 
 	 * @param $opt - the slider options
 	 * @param $key - the array key asked for from slider options
 	 * @param $post_id - the ID of the slider
	 */
	$option = apply_filters( 'fa_get_slider_options', $opt, $key, $post_id );
	
	return $option;
}

/**
 * Get default slider options
 */
function fa_get_slider_default_options(){
	$slider_options = require_fa_options('slider');
	return $slider_options->get_defaults();
}

/**
 * Update slider options with given values
 * @param int $post_id
 * @param array $value
 */
function fa_update_slider_options( $post_id, $value ){
	if( !current_user_can( 'manage_options' ) && !current_user_can( 'edit_fa_items', $post_id ) ){
		return new WP_Error('fa-no-permission', __("You don't have the permission to update slider options."));
	}
	
	// load slider themes to have the themes options injected into the plugin options
	if( function_exists('fa_get_themes') && is_admin() ){
		fa_get_themes();
	}
	
	$slider_options = require_fa_options( 'slider' );
	return $slider_options->update_option( $post_id, $value );
}


/**
 * Returns all taxonomies allowed by the user to create slides from.
 */
function fa_get_allowed_taxonomies(){	
	$tax_obj = get_taxonomy('category');
	$result = array(
		'post' => array(
			array(
				'taxonomy' 	=> 'category',
				'name' 		=> $tax_obj->labels->name
			)
		)
	);	
	return $result;
}

/**
 * Returns a list of al registered and public taxonomies
 */
function fa_get_registered_taxonomies(){	
	$args = array(
		'public' => true
	);
	$taxonomies = get_taxonomies( $args );
	$result = array();
	foreach( $taxonomies as $tax => $name ){
		$tax_obj = get_taxonomy( $tax );
		// allow only categories for builtin tax
		if( $tax_obj->_builtin && 'category' != $tax ){
			continue;
		}		
		$result[ $tax_obj->object_type[0] ][] = array(
			'taxonomy' 	=> $tax,
			'name'		=> $tax_obj->labels->name
		);
	}
	
	return $result;	
}

/**
 * Enqueues a given front-end stylesheet. Parameter should
 * be without .css extension 
 * 
 * @param string $stylesheet - stylesheet filename from within folder assets/front/css without .css extension
 */
function fa_load_style( $stylesheet ){

	if( defined( 'FA_CSS_DEBUG' ) && FA_CSS_DEBUG ){
		$stylesheet .= '.dev';
	}else{
		$stylesheet .= '.min';
	}	
	
	$url = fa_get_uri( 'assets/front/css/' . $stylesheet . '.css' );
	wp_enqueue_style(
		'fa-style-' . $stylesheet,
		$url,
		array(),
		false
	);
	return 'fa-style-' . $stylesheet;
}

/**
 * Enqueues a given front-end script. File name should not have .js extension.
 * An array of dependencies can be passed to it.
 * 
 * @param string $script - filename from within plugin folder assets/front/js without the .js extension
 * @param array $dependency - array of dependencies. Defaults to jquery
 * 
 * @return string - script handle
 */
function fa_load_script( $script, $dependency = array( 'jquery' ) ){	
	
	if( defined('FA_SCRIPT_DEBUG') && FA_SCRIPT_DEBUG ){
		$script .= '.dev';
	}
	
	$url = fa_get_uri( 'assets/front/js/' . $script . '.js' );
	wp_enqueue_script(
		'fa-script-' . $script,
		$url,
		$dependency,
		FA_VERSION		
	);	
	return 'fa-script-' . $script;
}

/**************************************************
 * Slide image functionality
 **************************************************/
/**
 * Returns the attached image ID. Will first check for the special
 * image field. If not found, will check for the featured image ID. If
 * still not found, will check for autodetected images from post contents.
 * 
 * @param int $post_id - ID of post to retrieve image for
 * @return int - ID of image
 */
function get_the_fa_image_id( $post_id = false ){
	// ge the post ID
	if( !$post_id ){
		global $post;
		if( !$post ){
			return false;
		}
		$post_id = $post->ID;
	}	
	// slide options
	$options = fa_get_slide_options( $post_id );
	$image_id = false;
	// try to get the slide attached image
	if( isset( $options['image'] ) && $options['image'] ){
		$image_id = $options['image'];
		if( !wp_get_attachment_image_src( $image_id, 'thumbnail' ) ){
			$image_id = false;
		};
	}
	// try to get the slide featured image if slide image isn't available
	if( !$image_id ){
		$image_id = get_post_thumbnail_id( $post_id );
	}
	// try to get the image retrieved from post content if none of the above worked
	if( !$image_id && isset( $options['temp_image_id'] ) && $options['temp_image_id'] ){
		$image_id = $options['temp_image_id'];
		if( !wp_get_attachment_image_src( $image_id, 'thumbnail' ) ){
			$image_id = false;
		}	
	}
	
	if( !$image_id ){			
		// image still wasn't found, try to autodetect, if option is enabled
		$plugin_options = fa_get_options( 'settings' );
		if( isset( $plugin_options['allow_image_autodetect'] ) && $plugin_options['allow_image_autodetect'] ){
			$current_post = get_post( $post_id );
			if( $current_post ){
							
				$image = fa_detect_image( $current_post->post_content );
				if( isset( $image['id'] ) ){
					$image_id = $image['id'];
					// set the temporary image ID to avoid autodetecting in the future
					fa_update_slide_options( $post_id , array( 'temp_image_id' => $image_id ) );
				}
			}	
		}
	}
	return $image_id;
}

/**
 * Returns the image URL for a given slide ID ( $post_id ) that belongs to
 * a given slider id( $slider_id ). Will do all size checking and will return
 * an array with the image details or false if image isn't found.
 * 
 * @param int $post_id - ID of slide
 * @param int $slider_id - ID of slider
 * @return array( 'url' => URL of image, 'width' => image width, 'height' => image height, 'id' => image ID if found in media gallery )
 */
function get_the_fa_slide_image_url( $post_id = false, $slider_id = false ){
	// get the slider ID
	if( !$slider_id ){
		global $fa_slider;
		if( $fa_slider ){
			$slider_id = $fa_slider->ID;
		}else{
			_doing_it_wrong( 'get_the_fa_image_url()' , __( 'Use this function inside a slide loop or pass the slider ID to the function.', 'featured-articles-lite' ), '3.0');
		}
	}
	
	// set the post ID
	if( !$post_id ){
		if( isset( $fa_slider ) ){
			$post = get_current_slide();
		}else{		
			global $post;
		}	
		if( $post ){
			$post_id = $post->ID;
		}else{
			_doing_it_wrong( 'get_the_fa_image_url()' , __( 'Use this function inside a slide loop or pass the slide (post) ID to the function.', 'featured-articles-lite' ), '3.0');
		}
	}else{
		$post = get_post( $post_id );
	}	
	
	if( !$post || !$post_id || !$slider_id ){
		return false;
	}
	
	// get slider options and check if image is visible
	$slider_options = fa_get_slider_options( $slider_id, 'content_image' );
	if( !$slider_options['show'] ){
		return;
	}
	
	// slide options
	$options = fa_get_slide_options( $post_id );
	// get the attached image ID
	$image_id = get_the_fa_image_id( $post_id );
		
	// try to make the image url
	$image_url = false;
	if( 'wp' == $slider_options['sizing'] && $image_id ){
		$wp_attachment = wp_get_attachment_image_src( $image_id, $slider_options['wp_size'] );
		if( $wp_attachment ){
			$image_url = $wp_attachment[0];
		}
	}else if( 'custom' == $slider_options['sizing'] && $image_id ){
		$image_url = fa_get_custom_image_size( $image_id, $slider_options['width'], $slider_options['height'] );
	}
	// last options, check on slide settings if an image URL is set
	if( !$image_url ){
		if( isset( $options['temp_image_url'] ) && !empty( $options['temp_image_url'] ) ){
			$image_url = $options['temp_image_url'];
		}else{
			// try to autodetect the image URL in post content if option is enabled
			$plugin_options = fa_get_options( 'settings' );
			if( isset( $plugin_options['allow_image_autodetect'] ) && $plugin_options['allow_image_autodetect'] ){
				$current_post = get_post( $post_id );
				if( $current_post ){
					$image = fa_detect_image( $current_post->post_content );
					if( !$image['id'] && $image['img'] ){
						$image_url = $image['img'];
						fa_update_slide_options( $post_id, array( 'temp_image_url' => $image_url ) );
					}
				}
			}
		}
	}
	// if no image URL was detected, stop
	if( !$image_url ){
		return false;
	}
	
	$width = $height = false;
	// get the image size
	if( 'custom' == $slider_options['sizing'] ){
		$width 	= absint( $slider_options['width'] );
		$height = absint( $slider_options['height'] );
	}elseif ( 'wp' == $slider_options['sizing'] ){
		if( isset( $wp_attachment ) && $wp_attachment ){
			$width 	= $wp_attachment[1];
			$height = $wp_attachment[2];
		}
	}
	// create the response
	return array( 
		'url' 		=> $image_url, 
		'width' 	=> $width, 
		'height' 	=> $height, 
		'id'		=> $image_id 
	);	
}

/**
 * Finds/creates a custom image size for a given image ID and size
 * @param int $image_id
 * @param int $width
 * @param int $height
 */
function fa_get_custom_image_size( $image_id, $width, $height ){
	$image_id 	= absint( $image_id );
	$width 		= absint( $width );
	$height 	= absint( $height );
	// if width or height is 0, don't do anything
	if( $width == 0 || $height == 0 ){
		return false;
	}
	// get the metadata from image	
	$attachment_meta = get_post_meta( $image_id, '_wp_attachment_metadata', true );
	if( !$attachment_meta ){
		return false;
	}
	// if width and height exceed the full image size, return the full image
	if( $width >= $attachment_meta['width'] && $height >= $attachment_meta['height'] ){
		$attachment = wp_get_attachment_image_src( $image_id, 'full' );
		return $attachment[0];
	}
	
	// check if any of the registered sizes match the size we're looking for
	foreach( $attachment_meta['sizes'] as $size_name => $size_details ){
		// size matched, return it
		if( $width == $size_details['width'] && $height == $size_details['height'] ){
			$attachment = wp_get_attachment_image_src( $image_id, $size_name );	
			return $attachment[0];		
		}
	}
	
	// get the upload dir details
	$wp_upload_dir = wp_upload_dir();
	// an extra meta field on image to store fa image sizes of resized images
	$fa_sizes = get_post_meta( $image_id, '_fa_attachment_metadata', true );

	// check sizes stored by FA
	if( $fa_sizes ){
		foreach( $fa_sizes as $details ){
			if( $width == $details['width'] && $height == $details['height'] ){
				return $wp_upload_dir['baseurl'] . wp_normalize_path( $details['rel_path'] );
			}
		}
	}
	
	// create the new size if not found yet
	$image_path = path_join( $wp_upload_dir['basedir'] , $attachment_meta['file'] );
	// create the new image size
	$img_editor = wp_get_image_editor( $image_path );
	$img_editor->set_quality( 90 );			
	$resized 	= $img_editor->resize( $width, $height, true );
	$new_file 	= $img_editor->generate_filename( null, null );
	$saved 		= $img_editor->save( $new_file );
	// relative file path
	$rel_path = str_replace( $wp_upload_dir['basedir'], '', $new_file );
	$new_file_url = $wp_upload_dir['baseurl'] . wp_normalize_path( $rel_path );
	
	// store the new size on image meta
	$fa_sizes = is_array( $fa_sizes ) ? $fa_sizes : array();
	$file_details = array(
		'basename' 	=> wp_basename( $new_file ),
		'rel_path' 	=> $rel_path,
		'width' 	=> $width,
		'height' 	=> $height
	);
	$fa_sizes[] = $file_details;
	update_post_meta( $image_id, '_fa_attachment_metadata', $fa_sizes);
	return $new_file_url;
}

/**********************************
 * WPtouch plugin related functions
 **********************************/

/**
 * Checks if WPTouch plugin is installed
 */
function fa_is_wptouch_installed(){
	$installed = class_exists('WPtouchPlugin') || class_exists('WPtouchPro');
	return $installed;
}

/**
 * Check if wptouch is set on exclusive. This means that, if plugin is installed,
 * it checks if option for not loading styles and scripts into header or footer is on.
 * @return bool
 */
function fa_is_wptouch_exclusive(){	
	$result = false;	
	// wptouch has an option to disable scripts and stylesheets in header/footer
	if( function_exists('bnc_wptouch_is_exclusive') ){
		$result = bnc_wptouch_is_exclusive();
	}	
	return $result;
}