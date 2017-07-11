<?php

/**
 * To be used only in slider theme display.php file.
 * Returns options implemented by the slider theme for the slider being displayed.
 */
function get_slider_theme_options(){
	global $fa_slider;
	$theme 			= fa_get_slider_options( $fa_slider->ID, 'theme' );
	$themes_options = fa_get_slider_options( $fa_slider->ID, 'themes_params' );
	
	$active_theme = $theme['active'];
	if( array_key_exists( $active_theme, $themes_options ) ){
		return $themes_options[ $active_theme ];
	}
	return false;	
}

/**
 * Displays slider title according to settings
 */
function the_slider_title( $before = '<h2 class="fa_title_section">', $after = '</h2>', $echo = true ){
	global $fa_slider;
	$options = fa_get_slider_options( $fa_slider->ID, 'layout' );
	if( !$options['show_title'] || empty( $fa_slider->post_title ) ){
		return;
	}
	/**
	 * Filter on slider title.
	 * 
	 * @var title - the post type slider title
	 * @var id	- the slider ID
	 */
	$title = apply_filters( 'the_fa_slider_title' , $fa_slider->post_title, $fa_slider->ID );	
	$output = $before . esc_html( $title ) . $after;
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the color for styling. 
 * To be added as CSS class on slider container.
 */
function the_slider_color( $echo = true ){
	global $fa_slider;
	$options = fa_get_slider_options( $fa_slider->ID, 'theme' );
	
	if( isset( $options['color'] ) && !empty( $options['color'] ) ){
		$color = str_replace( '.min' , '', $options['color']);
	}else{
		$color = '';
	}
		
	if( $echo ){
		echo $color;
	}	
	return $color;
}

/**
 * Applies the CSS classes of a slider.
 * Pass the main slider class to the function.
 * Will also implement the color class.
 * 
 * @param string $class - main slider class
 */
function the_slider_class( $class, $echo = true ){
	global $fa_slider;	
	// add fa-slider class to all sliders
	$classes = array( 'fa-slideshow' );
	if( $class ){
		$classes[] = esc_attr( $class );
	}
	
	$settings = fa_get_options( 'settings' );
	if( $settings['preload_sliders'] ){
		$classes[] = 'slider-loading';
	};
	
	$color = the_slider_color( false );
	if( $color && -1 != $color ){
		$classes[] = esc_attr( $color );
	}
	
	// add the layout variation class if available
	$options = fa_get_slider_options( $fa_slider->ID, 'layout' );
	if( isset( $options['class'] ) && !empty( $options['class'] ) ){
		$classes[] = esc_attr( $options['class'] );
	}
	
	// add a no-title class if slider is set not to display the title
	$title_options = fa_get_slider_options( $fa_slider->ID, 'content_title' );
	if( !$title_options['show'] ){
		$classes[] = 'no-title';
	}
	
	// add a no-content class if slider is set not to display the text
	$content_options = fa_get_slider_options( $fa_slider->ID, 'content_text' );
	if( !$content_options['show'] ){
		$classes[] = 'no-content';
	}	
	
	// add a no-secondary-nav if slider is set not to display secondary navigation
	if( !has_sideways_nav() ){
		$classes[] = 'no-secondary-navs';
	}
	
	// add a no-primary-nav if slider is set not to display primary navigation
	if( !has_bottom_nav() ){
		$classes[] = 'no-primary-nav';
	}
	
	$output = implode( ' ' , $classes);
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs styling for slider width
 * @param bool $echo
 */
function the_slider_width( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	$width 		= $options['width'];
	if( $options['full_width'] ){
		$width = '100%';
	}
	
	$output 	= 'width:' . $width . ( is_numeric( $width ) ? 'px' : '' ) . '; ';
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the slider margin from slider settings.
 * @param bool $echo
 */
function the_slider_margin( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	
	$styles = array();
	$margins = array( 'top', 'bottom' );
	foreach( $margins as $margin ){		
		if( is_numeric( $options[ 'margin_' . $margin ] ) ){
			$styles[] = 'margin-' . $margin . ':' . $options[ 'margin_' . $margin ] . 'px';
		}
	}
	
	$output = '';
	if( $styles ){
		$output = implode('; ', $styles) . ';';
	}
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Styling to horizontally center the slider according to user settings
 * @param bool $echo
 */
function the_slider_horizontal_align( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	if( !$options['center'] ){
		return;
	}
	
	$output = 'margin-left:auto; margin-right: auto;';
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the slider font size on slider as style
 * @param bool $echo
 */
function the_slider_font_size( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	if( !$options['font_size'] ){
		return;
	}
	
	$output = 'font-size:'. absint( $options['font_size'] ) .'%;';
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs styling of current slider
 * @param bool $echo
 */
function the_slider_styles( $echo = true ){
	$output = the_slider_margin( false );
	$output.= the_slider_horizontal_align( false );
	$output.= the_slider_font_size( false );
	
	$settings = fa_get_options( 'settings' );
	if( $settings['preload_sliders'] ){
		$output .= the_slider_width();
		$output .= the_slider_height();
	};
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the slider HTML id
 */
function the_slider_id( $echo = true ){
	global $fa_slider;
	$output = 'fa-slider-' . $fa_slider->ID;
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Output the slider js settings as data-PARAM on slider container.
 * @param bool $echo
 */
function the_slider_data( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID );
	$theme 		= $options['theme']['active'];
	// the plugin js options
	$js_options 	= $options['js'];
	
	$data = array();
	foreach( $js_options as $option => $value ){
		$data[] = 'data-' . $option . '="' . esc_attr( $value ) . '"'; 
	}
	
	$data[] = 'data-width="' . absint( $options['layout']['width'] ) . '"';
	$data[] = 'data-height="' . absint( $options['layout']['height'] ) . '"';
	$data[] = 'data-height_resize="' . (bool)$options['layout']['height_resize'] . '"';
	$data[] = 'data-fullwidth="' . ( (bool) $options['layout']['full_width'] ) . '"';
	$data[] = 'data-font_size="' . absint( $options['layout']['font_size'] ) . '"';
	$data[] = 'data-is_mobile="' . wp_is_mobile() . '"';
	
	// set theme data on slider data attributes
	$theme_options = get_slider_theme_options();
	if( $theme_options ){
		foreach( $theme_options as $option => $value ){
			$data[] = 'data-theme_opt_' . $option . '="' . esc_attr( (string)$value ) . '"';
		}	
	}
	
	$output = implode( ' ', $data );
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs styling for slider height
 * @param bool $echo
 */
function the_slider_height( $echo = true ){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	$height 	= $options['height'];
	$output 	= 'height:' . $height . ( is_numeric( $height ) ? 'px;' : '' );
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Verifies if main navigation should be displayed
 */
function has_bottom_nav(){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	return (bool) $options['show_main_nav'];
}

/**
 * Verifies if left/right navigation should be displayed
 */
function has_sideways_nav(){
	global $fa_slider;
	$options 	= fa_get_slider_options( $fa_slider->ID, 'layout' );
	return (bool) $options['show_side_nav'];
}

/***************************************************
 * Slides templating functions
 ***************************************************/

/**
 * Loops the current slider slides.
 * 
 * Usage:
 * 
 * while( have_slides() ){
 * 		... code here
 * }
 * 
 */
function have_slides(){
	global $fa_slider;
	if( !$fa_slider->slides ){
		return false;
	}
	
	if ( $fa_slider->current_slide + 1 < $fa_slider->slide_count ) {
		$fa_slider->current_slide++;
		return true;
	} elseif ( $fa_slider->current_slide + 1 == $fa_slider->slide_count && $fa_slider->slide_count > 0 ) {
		/**
		 * Fires once the loop has ended.
		 *
		 * @since 3.0
		 *
		 * @param $fa_slider - the post object for the current slider
		 */
		do_action( 'slides_loop_end', $fa_slider );
		// Do some cleaning up after the loop
		$fa_slider->current_slide = -1;			
	}
	return false;	
}

/**
 * Returns the number of slides in current slider
 */
function get_slides_count(){
	global $fa_slider;
	if( !$fa_slider->slides ){
		return 0;
	}
	return $fa_slider->slide_count;
}

/**
 * Returns the current slide in loop
 */
function get_current_slide(){
	global $fa_slider;
	// @todo - an error message here if there's nothing in $fa_slider
	
	$index = $fa_slider->current_slide;
	$slide = $fa_slider->slides[ $index ];
	return $slide;
}

/**
 * Outputs custom slide CSS class set by user
 * @param bool $echo
 */
function the_fa_class( $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	$options = fa_get_slide_options( $post->ID );
	$classes = array();
	
	if( !empty( $options['class'] ) ){
		$classes[] = esc_attr( $options['class'] );
	}
	
	$image = get_the_fa_slide_image_url( $post->ID, $fa_slider->ID );
	if( !$image ){
		$classes[] = 'no-image'; 
	}
	
	if( $classes ){
		$output = implode( ' ',  $classes );		
		if( $echo ){
			echo $output;
		}
		return $output;
	}
}

/**
 * Outputs the slide image according to size set in slider settings
 * 
 * @param string $before
 * @param string $after
 * @param bool $set_width
 * @param bool $overlay_link
 * @param bool $echo
 */
function the_fa_image( $before = '<div class="image_container">', $after = '</div>', $set_width = false, $overlay_link = true, $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_image' );
	if( !$slider_options['show'] ){
		return;
	}
	
	// slide options
	$options = fa_get_slide_options( $post->ID );
	// get the attached image ID
	$image = get_the_fa_slide_image_url( $post->ID, $fa_slider->ID );
	// if no image URL was detected, stop
	if( !$image ){
		return false;
	}
	// the image URL
	$image_url = $image['url'];
	
	$attrs = array();
	if( $slider_options['show_width'] ){
		$attrs[] = 'width="' . absint( $image['width'] ) . '"';
	}
	if( $slider_options['show_height'] ){
		$attrs[] = 'height="' . absint( $image['height'] ) . '"';
	}	
	$attrs[] = 'data-width="' . absint( $image['width'] ) . '"';
	$attrs[] = 'data-height="' . absint( $image['height'] ) . '"';
	
	$style = array();
	if( $set_width ){
		$style[] = 'width:' . absint( $image['width'] ) . 'px';
	}
		
	// create the link
	$link = array('', '');
	if( $slider_options['clickable'] ){
		$url = get_permalink( $post->ID );
		if( $url && !empty( $url ) ){
			$link[0] = sprintf( '<a href="%1$s" title="%2$s" target="%3$s">',
				$url,
				esc_attr( $post->post_title ),
				'_self'
			);
			$link[1] = '</a>';
		}
	}
	
	/**
	 * Set up an alternative text for images from the outside.
	 * @var unknown
	 */
	$alt_text = apply_filters( 'the_fa_image_alt_text' , false, $fa_slider->ID, $post->ID, $image['id'] );
	
	if( false === $alt_text ){
		$alt_text = get_post_meta( $image['id'], '_wp_attachment_image_alt', true );
	}
	
	// the image output
	$img_html = '<img class="fa_slide_image main-image" src="' . $image_url . '" ' . implode( ' ', $attrs ) . ' style="' . implode( '; ', $style ) . '" alt="' . esc_attr( $alt_text ) . '" />';
	
	// preload image functionality
	$container_class = array( 'fa-image-container' );
	$container_data = array();
	if( $slider_options['preload'] ){
		$container_class[] = 'preload_image';
		$container_data = array(
			'data-image' 		=> 'data-image="' . $image_url . '"',
			'data-image_class' 	=> 'data-image_class="fa_slide_image main-image"',
			'data-width' 		=> 'data-width="' . $image['width'] . '"',
			'data-height'		=> 'data-height="' . $image['height'] . '"'
		);

		$img_url 	= fa_get_uri( 'assets/front/images/loading.gif' );
		$img_html 	= '<img class="fa_slide_image main-image preloader" src="' . $img_url . '" alt="' . esc_attr( $alt_text ) . '" />';		
	}
	
	// start the output
	$img_output = $before . '<div class="' . implode(' ', $container_class) . '" ' . implode(' ', $container_data) . '>' . $link[0] . $img_html . $link[1] . '</div>' . $after;
	/**
	 * Filter the image output
	 * 
	 * @var string $img_output - the image HTML output
	 * @var object $post - the slide post being processed
	 * @var object $fa_slider - the slider post being processed
	 */
	$output = apply_filters( 'the_fa_slide_image' , $img_output, $post, $fa_slider );
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Displays the slide title. Checks slider settings for title and
 * custom title setting.
 * 
 * @param string $before
 * @param string $after
 * @param string $echo
 */
function the_fa_title( $before = '<h2>', $after = '</h2>', $echo = true, $raw = false ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_title' );
	if( !$slider_options['show'] ){
		return;
	}
	
	// slide options
	$options = fa_get_slide_options( $post->ID );
	// get the title
	$title = get_the_fa_title();
	
	if( $raw ){
		if( $echo ){
			echo $title;
		}
		return $title;
	}
	
	$title_color = false;
	if( !empty( $options['title_color'] ) ){
		$title_color = $options['title_color'];
	}else if( !empty( $slider_options['color'] ) ){
		$title_color = $slider_options['color'];
	}
	
	if( $title_color ){
		$title = '<span style="color: ' . $title_color . '">' . $title . '</span>';
	}
	
	// create the link
	$link = array('', '');
	if( $slider_options['clickable'] ){
		$url = get_permalink( $post->ID );
		if( $url && !empty( $url ) ){
			$link[0] = sprintf( '<a href="%1$s" title="%2$s" target="%3$s">',
				$url,
				esc_attr( $post->post_title ),
				'_self'
			);
			$link[1] = '</a>';
		}
	}
	
	$title_output = $before. $link[0] . $title . $link[1] . $after;
	
	if( !$raw ){
		/**
		 * Filter the slide title
		 * 
		 * @var $title_output - the slide title
		 * @var $post - the current slide being processed
		 * @var $fa_slider - the current slider being processed
		 */
		$output = apply_filters('the_fa_slide_title', $title_output, $post, $fa_slider);
	}
		
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Get the title of a slide depending on slide settings
 * 
 * @return string - the title
 */
function get_the_fa_title(){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slide options
	$options = fa_get_slide_options( $post->ID );
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_title' );
	if( $slider_options['use_custom'] && !empty( $options['title'] ) ){
		$title = esc_attr( $options['title'] );
	}else{
		$title = $post->post_title;
	}
	return $title;
}

/**
 * Get the URL for current slide in the slides loop
 */
function get_the_slide_url(){
	global $fa_slider;
	$post = get_current_slide();
	
	// slide options
	$options = fa_get_slide_options( $post->ID );
	$url = '';		
	
	if( !empty( $options['url'] ) && !$options['link_to_post'] ){
		$url = $options['url'];
	}else{
		$url = get_permalink( $post->ID );
	}
		
	return $url;
}

/**
 * Displays the slide date
 *
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function the_fa_date( $before = '<span class="fa-date">', $after = '</span>', $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_date' );
	if( !$slider_options['show'] ){
		return;
	}
	
	$the_date = get_the_date( get_option( 'date_format' ), $post );
	$date_output = $before . $the_date . $after;
	/**
	 * Filter the slide date
	 * 
	 * @var $date_output - the slide date
	 * @var $post - the current slide being processed
	 * @var $fa_slider - the current slider being processed
	 */
	$output = apply_filters('the_fa_slide_date', $date_output, $post, $fa_slider);
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Output the slide content according to slider settings.
 * 
 * @param $before - HTML before the content
 * @param $after - HTML after the content
 * @param $echo - output the content(true)
 */
function the_fa_content( $before = '', $after = '', $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_text' );
	if( !$slider_options['show'] ){
		return;
	}
	// get slide options
	$options = fa_get_slide_options( $post->ID );
	
	if( 'custom' == $slider_options['use'] && strcmp( preg_replace("|([^a-z])|i", '', $options['content'] ) , preg_replace("|([^a-z])|i", '', $post->post_content) ) !== 0 ){
		// leave the contents as they were set by the user
		$post_content = $options['content'];
		if( $slider_options['strip_shortcodes'] ){
			$post_content = strip_shortcodes( $post_content );
		}else{
			$post_content = do_shortcode( $post_content );
		}		
	}else{ // slide content is made from the post content or excerpt
		$post_content = 'excerpt' == $slider_options['use'] ? $post->post_excerpt : $post->post_content;
		// set the text length
		$text_length = $slider_options['max_length'];
		$image_options = fa_get_slider_options( $fa_slider->ID, 'content_image' );
		if( !$image_options['show'] || ( !get_the_fa_image_id( $post->ID ) && empty( $options['temp_image_url'] ) ) ){
			$text_length = $slider_options['max_length_noimg'];
		}
		// strip the tags
		if( !$slider_options['allow_all_tags'] ){
			$post_content = strip_tags( $post_content, $slider_options['allow_tags'] );
		}
		// remove shortcodes
		if( $slider_options['strip_shortcodes'] ){
			$post_content = strip_shortcodes( $post_content );
		}else{
			$post_content = do_shortcode( $post_content );
		}
		// truncate the text
		$post_content = fa_truncate_html( $post_content, $text_length, $slider_options['end_truncate'] );		
	}
		
	/**
	 * Filter the slide contents
	 * @var $post_content - the slide contents
	 * @var $post - current slide being processed
	 * @var $fa_slider - current slider being processed
	 */
	$post_content = apply_filters('the_fa_content', $post_content, $post, $fa_slider);
	
	if( empty( $post_content ) ){
		return;
	}
	
	$text_color = false;
	if( !empty( $options['content_color'] ) ){
		$text_color = $options['content_color'];
	}else if( !empty( $slider_options['color'] ) ){
		$text_color = $slider_options['color'];
	}	
	if( $text_color ){		
		$post_content = '<div style="color:' . $text_color . '">' . $post_content . '</div>';
	}
	
	$output = $before . $post_content . $after;
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the read more link for a slide
 * @param string $class
 * @param bool $echo
 */
function the_fa_read_more( $class = 'fa-read-more', $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_read_more' );
	if( !$slider_options['show'] ){
		return;
	}
	
	$text 	= false;
	$url 	= false;
	
	// first get the text setting from slider settings
	$text = trim( $slider_options['text'] );
	
	$options = fa_get_slide_options( $post->ID );
	if( !empty( $options['link_text'] ) ){
		$text = $options['link_text'];
	}
	
	$url = get_permalink( $post->ID );
		
	if( !$url || empty( $url ) || empty( $text ) ){
		return;
	}
	
	$rm_output = sprintf( '<a href="%s" title="%s" target="%s" class="%s">%s</a>',
		$url,
		esc_attr( $post->post_title ),
		'_self',
		esc_attr( $class ),
		esc_attr( $text )
	);
	
	/**
	 * Filter the slide read more link
	 * 
	 * @var $rm_output - the slide read more link
	 * @var $post - the current slide being processed
	 * @var $fa_slider - the current slider being processed
	 */
	$output = apply_filters('the_fa_slide_read_more', $rm_output, $post, $fa_slider);
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Displays a play video link
 * @param string $class
 * @param bool $echo
 */
function the_fa_play_video( $class = '', $open = 'self', $show_text = true, $echo = true ){
	return;
}

/**
 * Output the slide background image.
 * 
 * @param $show_image - display image or hide it
 * @param $position - position of background
 * @param $repeat - background repeat attribute
 * @param $echo - output the styling
 */
function the_fa_background( $show_image = true, $position = 'top left', $repeat = 'no-repeat', $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_image' );
	// get slide options
	$options = fa_get_slide_options( $post->ID );
	$style = array();
	// set the slide background color
	if( isset( $options['background'] ) && !empty( $options['background'] ) ){
		$style[] = 'background-color:' . $options['background'];
	}
	if( $show_image ){
		$image = get_the_fa_slide_image_url( $post->ID, $fa_slider->ID );
		if( $image ){
			$style[] = 'background-image:url(' . $image['url'] . ')';
			$style[] = 'background-position:' . $position;
			$style[] = 'background-repeat:' . $repeat;
		}
	}
	// if no styling, bail out
	if( !$style ){
		return;
	}
	
	$output = implode('; ', $style);
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the background color for the current slide in loop
 * @param string $before
 * @param string $after
 */
function the_fa_background_color( $echo = true, $before = ' ', $after = ';' ){
	$post = get_current_slide();
	$options = fa_get_slide_options( $post->ID );
	$output = '';
	if( !empty( $options['background'] ) ){
		$output = $before . 'background-color:' . $options['background'] . $after;
	}
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs styling for the current slide in loop
 * @param bool $echo
 */
function the_slide_styles( $echo = true ){
	
	$styles = array();
	$background_color = the_fa_background_color( false );
	if( $background_color ){
		$styles[] = $background_color;
	}
	
	$output = implode( $styles );
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Outputs the author into slide
 * @param string $before
 * @param string $after
 * @param bool $echo
 */
function the_fa_author( $before = '', $after = '', $echo = true ){
	global $fa_slider;
	
	$post = get_current_slide();
	$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_author' );
	if( !$slider_options['show'] ){
		return;
	}
	// get author data
	$author_id = $post->post_author;
	$user_data = get_userdata( $author_id );
	if( !$user_data ){
		return;
	}
	// store author name in variable
	$author_name = $user_data->display_name;	
	if( $slider_options['link'] ){
		$url = get_author_posts_url( $author_id );
		$author_name = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', $url, $author_name );
	}

	$author_output = $before . $author_name . $after;
	/**
	 * Filter the slide author link
	 * 
	 * @var $author_output - the slide author
	 * @var $post - the current slide being processed
	 * @var $fa_slider - the current slider being processed
	 */
	$output = apply_filters('the_fa_slide_author', $author_output, $post, $fa_slider);
	
	
	// result
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Returns the avatar of the post author with or without a link placed on it
 * @param string $before
 * @param string $after
 * @param bool $author_link
 * @param bool $echo
 */
function the_fa_avatar( $before = '', $after = '', $author_link = false, $echo = true ){
	$post = get_current_slide();
	if( !$post ){
		return;
	}
	
	$author_id = $post->post_author;
	$avatar = get_avatar( $author_id );
	if( !$avatar ){
		return;
	}
	
	if( !$author_link ){
		if( $echo ){
			echo $avatar;
		}		
		return $avatar;
	}
	
	$user_data = get_userdata($author_id);
	if(!$user_data){
		return;
	}
	$author_name 	= $user_data->display_name;
	$author_url 	= get_author_posts_url($author_id);
	$result 		= sprintf('<a href="%s" title="%s">%s</a>',
		$author_url,
		$author_name,
		$avatar	
	);
	
	$output = $before . $result . $after;
	
	if( $echo ){
		echo $output;
	}
	
	return $output;
}

/**
 * Displays a link to current post author profile page
 * @param string $text
 * @param string $class
 * @param bool $echo
 */
function the_fa_author_link( $text = '', $class='', $echo = true ){
	$post = get_current_slide();
	if( !$post ){
		return;
	}
	
	$author_id 		= $post->post_author;
	$author_url 	= get_author_posts_url($author_id);
	
	if( !$author_url ){
		return;
	}
	
	$user_data = get_userdata($author_id);
	if(!$user_data){
		return;
	}
	
	if( empty( $text ) ){
		$text = $user_data->display_name;
	}
	
	$css_class = !empty($class) ? ' class="'.$class.'"' : '';
	
	$result = sprintf('<a href="%s" title="%s"%s>%s</a>',
		$author_url,
		$user_data->display_name,
		$css_class,
		$text
	);
	
	if( $echo ){
		echo $result;
	}
	
	return $result;
}

/**
 * Display a content wrapper around the slide content.
 * @todo - check for fields disabled by the theme
 */
function fa_content_wrapper( $tag, $ignore = array() ){
	global $fa_slider;
	// get slider options and check if titles are visible
	$slider_options = fa_get_slider_options( $fa_slider->ID );
	$show = false;
	foreach( $slider_options as $opt => $params ){
		if( 'content_' != substr( $opt, 0, 8 ) ){
			continue;
		}
		
		$k = substr( $opt , 8 );
		if( !in_array( $k , $ignore) &&  $params['show'] ){
			$show = true;
			break;
		}
	}
	if( $show ){
		echo $tag;
	}	
}

/**
 * Returns a custom image size for the image attached to a given slide post.
 * @param object $post - a given slide post
 * @param array $size - image width ( as: array('width' => W, 'height' => H) ) or string ( ie: thumbnail )
 * @return string - the image URL
 */
function fa_get_custom_image( $post, $size = array( 'width' => 0, 'height' => 0 ) ){
	if( !$post ){
		return;
	}
	// slide can be attachment image, in this case the image ID is the actual post ID
	if( 'attachment' == $post->post_type ){
		$image_id = $post->ID;
	}else{
		$image_id = get_the_fa_image_id( $post->ID );
		if( !$image_id ){
			global $fa_slider;
			if( $fa_slider ){
				$slider_options = fa_get_slider_options( $fa_slider->ID, 'content_image' );				
			}		
			if( !$image_id ){		
				return false;
			}
		}
	}	
	
	if( is_string( $size ) ){
		$imgsz = get_intermediate_image_sizes();
		if( in_array( $size , $imgsz) ){
			$width = get_option( $size . '_size_w' );
			$height = get_option( $size . '_size_h' );
		}
	}elseif ( is_array( $size ) ){
		extract( $size, EXTR_SKIP );
	}else{
		return;
	}
		
	$image_url = fa_get_custom_image_size( $image_id , $width, $height );
	return $image_url;
}