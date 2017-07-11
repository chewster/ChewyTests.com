<?php
/**
 * @package Featured articles PRO - Wordpress plugin
 * @author CodeFlavors ( codeflavors[at]codeflavors.com )
 * @version 3.0+
 */

/**
 * Theme details for theme Smoke
 */
function fa_nivo_slider_theme_details( $defaults ){	
	$description = "Pinterest style theme that allows displaying of featured content. Video enabled and responsive.";		
	$defaults = array(
		'author' 		=> 'CodeFlavors',
		'author_uri' 	=> 'http://www.codeflavors.com',
		'copyright' 	=> 'author',
		'compatibility' => '3.0',
		'version'		=> '1.0',
		'name'			=> 'Nivo Slider',
		'fields'		=> array(
			'content-image-preload' => false,
			'content-image-width-attr' => false,
			'content-image-height-attr' => false,
			'content-image-link' => false,
			'layout-show-title'	=> false,
			'layout-show-main-nav' => false,
			'layout-show-side-nav' => false,
			'layout-slider-height' => false,
			'layout-height-resize' => false,
			'layout-font-size' => false,
			'js-click-stop' => false,
			'js-position-in' 	=> false,
			'js-position-out'	=> false,
			'js-distance-in' 	=> false,
			'js-distance-out'	=> false,
			'js-effect' => false,
			'js-cycle' => false,
			'js-click-event' => false
		),
		'classes' => array(),
		'colors' => array(),
		'stylesheets' => array(
			'font-awesome' 		=> false,
			'jquery-ui-dialog' 	=> true
		),
		'scripts' => array(
			'jquery-ui-dialog' 	=> true,
			// don't enqueue these scripts because the theme doesn't need them
			'slider' 			=> false, 
			'accordion' 		=> false, 
			'carousel' 			=> false, 
			'jquery-mobile' 	=> false, 
			'jquery-transit' 	=> false, 
			'round-timer' 		=> false,
			'video-player2'		=> true
		),
		'extra_scripts' => array(
			// theme enqueues relative path within theme folder
			'enqueue' => array( 
				'nivoSlider' => '/js/jquery.nivo.slider.pack.js' 
			)
		),
		'message' => 'Responsive.',
		'description' => $description
	);

	return $defaults;	
}
add_filter( 'fa-theme-details-' . fa_get_theme_key( __FILE__ ), 'fa_nivo_slider_theme_details', 1);
?>