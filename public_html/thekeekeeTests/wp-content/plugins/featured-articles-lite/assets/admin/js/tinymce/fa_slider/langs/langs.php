<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

if ( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function shortcode_fa_slider_translations() {
    $strings = array(
    	// editor menu button title
    	'button_title'		=> __('Insert new slider', 'featured-articles-lite'),
        // edit shortcode window
    	'add_new_window_title' => __('Add new slider', 'featured-articles-lite'),
    	'window_title' 		=> __('Edit slider properties', 'featured-articles-lite'),
    	'label_slider'		=> __('Select slider', 'featured-articles-lite'),
    	'label_title' 		=> __('Slider title (PRO)', 'featured-articles-lite'),
    	'label_show_title' 	=> __('Show title (PRO)', 'featured-articles-lite'),
    	'label_in_archive' 	=> __("Don't show in archive pages (PRO)", 'featured-articles-lite'),
    	'label_width' 		=> __('Width (PRO)', 'featured-articles-lite'),	
    	'label_height' 		=> __('Height  (PRO)', 'featured-articles-lite'),
    	'label_font_size' 	=> __('Font size  (PRO)', 'featured-articles-lite'),
    	'label_full_width' 	=> __('Allow full width (PRO)', 'featured-articles-lite'),
    	'label_top' 		=> __('Distance top  (PRO)', 'featured-articles-lite'),
    	'label_bottom' 		=> __('Distance bottom  (PRO)', 'featured-articles-lite'),
    	'label_show_slide_title'=> __('Show slides titles (PRO)', 'featured-articles-lite'),
    	'label_show_content' 	=> __('Show slides content (PRO)', 'featured-articles-lite'),
    	'label_show_date' 		=> __('Show slides date (PRO)', 'featured-articles-lite'),
    	'label_show_read_more' 	=> __('Show slides read more (PRO)', 'featured-articles-lite'),
    	'label_show_play_video' => __('Show slides play video (PRO)', 'featured-articles-lite'),
    	'label_img_click' 		=> __('Image clickable (PRO)', 'featured-articles-lite'),
    	'label_auto_slide' 		=> __('Autoslide (PRO)', 'featured-articles-lite'),    
    	// add shortcode window
    	'select_win_title' 	=> __('Select slider', 'featured-articles-lite'),
    	'close_win'			=> __('Close', 'featured-articles-lite') 
    );
    $locale = _WP_Editors::$mce_locale;
    $translated = 'tinyMCE.addI18n("' . $locale . '.fa_slider", ' . json_encode( $strings ) . ");\n";
    
    // output the sliders in variable
    $sliders = fa_get_sliders('publish');
    $output = array();
    foreach( $sliders as $slider ){
    	$output[] = array(
    		'value' => $slider->ID,
    		'text' 	=> empty( $slider->post_title ) ? '(' . __('no title', 'featured-articles-lite') . ')' : esc_attr( $slider->post_title )
    	);
    }
    $translated.= 'var fa_sliders=' . json_encode( $output ) . ";\n";
    
    return $translated;
}
$strings = shortcode_fa_slider_translations();