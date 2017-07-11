<?php
/**
 * Displays slider title according to slider settings
 * 
 * @deprecated 3.0
 * @deprecated Use the_slider_title()
 * @see the_slider_title()
 * 
 * @param string $before - html before the title
 * @param string $after - html after the title
 * @param bool $echo - output the title
 * @return string
 */
function the_slideshow_title( $before = '<h2 class="fa_title_section">', $after = '</h2>', $echo = true ){
	//_deprecated_function( __FUNCTION__, '3.0', 'the_slider_title()' );
		
	return the_slider_title( $before, $after, $echo );
}

/**
 * Displays slide image
 * 
 * @deprecated 3.0
 * @deprecated Use fa_get_custom_image()
 * @see fa_get_custom_image()
 * 
 * @param object $post
 * @param int $slider_id
 * @param bool/array $other_size
 * @param bool $return_path
 */
function FA_article_image( $post, $slider_id, $other_size = false, $return_path = true ){
	//_deprecated_function( __FUNCTION__, '3.0', 'fa_get_custom_image()' );
	
	return fa_get_custom_image( $post, $other_size );	
}

/**
 * Implements a deprecated filter not used since version 3.0.
 * Instead of filter fa-extend-options use fa_extra_slider_options with the same arguments.
 * 
 * @deprecated 3.0
 * 
 * @param array $options
 */
function _deprecated_fa_extend_options( $options ){
	/**
	 * Deprecated filter. Use fa_extra_slider_options filter instead.
	 * 
	 * @see fa_extra_slider_options
	 */
	return apply_filters('fa-extend-options', $options);
}

/**
 * Implements a deprecated action not used since version 3.0.
 * Instead of action fa_extra_animation_fields use fa_theme_js_settings-THEME_NAME.
 * 
 * @deprecated 3.0
 * 
 */
function _deprecated_show_themes_animation_fields(){
	global $post;	
	echo '<div class="fa_deprecated animation_fields">';
	$options = fa_get_slider_options( $post->ID, 'themes_params' );
	
	ob_start();	
	/**
	 * Deprecated action. Use fa_theme_js_settings-THEME_NAME action instead.
	 */
	do_action( 'fa_extra_animation_fields' , $options );
	/**
	 * Deprecated action. Use fa_theme_js_settings-THEME_NAME action instead.
	 */
	do_action('fa_extra_autoplay_fields', $options);
	/**
	 * Deprecated action. Use fa_theme_js_settings-THEME_NAME action instead.
	 */
	do_action('fa_extra_user_interaction_fields', $options);
	
	$output = ob_get_clean();
	if( $output ){
		echo '<hr />';
		?>
		<p class="description">
			<?php _e('Below are parameters from themes designed for FA PRO before version 3.0.', 'featured-articles-lite');?><br />
			<?php _e('We strongly recommend that you update all deprecated slider themes to current FA PRO version.', 'featured-articles-lite');?>
		</p>
		<?php
		echo str_replace( 'disabled="disabled"', '', $output );
	}
	
	echo '</div><br class="clear" />';
}

/**
 * Implements a deprecated action not used since version 3.0.
 * Instead of action fa_extra_animation_fields use fa_theme_layout_settings--THEME_NAME.
 * 
 * @deprecated 3.0
 * 
 */
function _deprecated_show_themes_layout_fields(){
	global $post;	
	echo '<div class="fa_deprecated theme_layout_fields">';
	$options = fa_get_slider_options( $post->ID, 'themes_params' );
	
	ob_start();	
	/**
	 * Deprecated action. Use fa_theme_layout_settings-THEME_NAME action instead.
	 * 
	 * @see fa_extra_slider_options
	 */
	do_action( 'fa_extra_theme_fields' , $options );
	
	$output = ob_get_clean();
	if( $output ){
		echo '<hr />';
		?>
		<p class="description">
			<?php _e('Below are parameters from themes designed for FA PRO before version 3.0.', 'featured-articles-lite');?><br />
			<?php _e('We strongly recommend that you update all deprecated slider themes to current FA PRO version.', 'featured-articles-lite');?>
		</p>
		<?php
		echo str_replace( 'disabled="disabled"', '', $output );
	}
	
	echo '</div><br class="clear" />';
}