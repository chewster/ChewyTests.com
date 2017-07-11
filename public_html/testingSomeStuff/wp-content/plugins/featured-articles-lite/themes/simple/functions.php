<?php
/**
 * Extend slider option with additional variables
 */
function theme_simple_extra_options( $option ){
	// get this theme key
	$key = fa_get_theme_key( __FILE__ );
	$option[ $key ] = array(
		'show_timer' => true,
		'auto_resize' 	=> false
	);
	
	return $option;
}
add_filter( 'fa_extra_slider_options', 'theme_simple_extra_options' );

/**
 * Output for the fields above
 * @param array $options - current options set
 */
function simple_layout_fields_output( $post ){
	// get the stored values of the options implemented by this theme
	$theme_options = fa_get_theme_options( __FILE__, $post );
	$theme_details = fa_theme_simple_details( false );
?>
<h2><?php printf( __('%s : theme specific settings', 'featured-articles-lite'), $theme_details['name']);?></h2>
<table class="form-table">
	<tbody>
		<tr>
			<th><label for="simple_timer"><?php _e('Show timer', 'featured-articles-lite');?>:</label></th>
			<td>
				<input id="simple_timer" type="checkbox" name="<?php fa_theme_var_name('show_timer', __FILE__);?>" value="1" <?php fa_checked( $theme_options['show_timer'] )?> />
				<span class="description"><?php _e('When checked, will display the bar timer when slider is set to auto slide.', 'featured-articles-lite');?></span>
			</td>
		</tr>
		<tr>
			<th><label for="auto_resize"><?php _e('Enable slider auto resize', 'featured-articles-lite');?>:</label></th>
			<td>
				<input id="auto_resize" type="checkbox" name="<?php fa_theme_var_name('auto_resize', __FILE__);?>" value="1" <?php fa_checked( $theme_options['auto_resize'] )?> />
				<span class="description"><?php _e('When checked the slider will resize its height automatically to fit the current slide.', 'featured-articles-lite');?></span>
			</td>
		</tr>
	</tbody>
</table>
<?php 	
}
add_action('fa_theme_layout_settings-' . fa_get_theme_key( __FILE__ ), 'simple_layout_fields_output', 10, 1);


/**
 * Some details about the theme. 
 * Also notice key Fields. It stores the above field and flags it as enabled for this theme. All other themes will display this field disabled.
 */
function fa_theme_simple_details( $defaults ){
	$defaults = array(
		'author'		=> 'CodeFlavors',
		'authorURI'		=> 'http://www.codeflavors.com',
		'copyright'		=> 'author',
		'compatibility'	=> '3.0',
		'version'		=> '1.0',
		'name'			=> 'Simple',
		'fields'		=> array(
			'js-effect' 			=> false,
			'content-date-show' 	=> false,
			'content-author-show' 	=> false,
			'content-author-link' 	=> false,
			'layout-show-title'		=> false
		),
		'colors'		=> array( // tell the plugin about the default color schemes so they won't be edited in theme editor
			'dark'
		),
		'classes' => array(
			'content-right' => __( 'Image left, content right', 'featured-articles-lite' )
		),
		'stylesheets' => array(
			'font-awesome' => true
		),
		'scripts' => array(),
		'message'		=> '<strong>Responsive slider theme.</strong>'
	);	
	return $defaults;	
}
add_filter('fa-theme-details-' . fa_get_theme_key( __FILE__ ), 'fa_theme_simple_details', 1);

/**
 * CSS customizations
 */
function fa_theme_css_simple(){
	$rules = array(
		'container' => array(
			'css_selector' => '.fa_slider_simple', // all child elements from container will descend from this
			'description' => __( 'Slideshow container', 'featured-articles-lite' ),
			'properties' => array( // not all properties are supported
				'border-width' 		=> '0px',
				'border-style' 		=> 'none',
				'border-color' 		=> 'transparent', 
				'background-color' 	=> '#FFFFFF'
			)	
		),
		'slide_title' => array(
			'css_selector' 	=> '.fa_slide_content h2',
			'description' 	=> __( 'Slide title', 'featured-articles-lite' ),
			'properties' 	=> array(
				'font-size' 	=> '2.5em',
				'color'			=> '#575757',
				'font-weight' 	=> '300',
				'text-shadow'	=> '1px 1px 1px #DBDBDB'
			)
		),
		'slide_title_anchor' => array(
			'css_selector' 	=> '.fa_slide_content h2 a',
			'description' 	=> __( 'Slide title anchor', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color'				=> '#575757',
				'text-decoration' 	=> 'none'
			)
		),
		'slide_text' => array(
			'css_selector' 	=> '.fa_slide_content div.description',
			'description' 	=> __( 'Slide text', 'featured-articles-lite' ),
			'properties' 	=> array(
				'font-size' 	=> '1em',
				'color' 		=> '#575757',
				'font-weight' 	=> 400
			)
		),
		'read_more' => array(
			'css_selector' 	=> '.fa_slide_content .fa_read_more',
			'description' 	=> __( 'Slide read more link', 'featured-articles-lite' ),
			'properties' 	=> array(
				'font-size' 		=> '1em',
				'color'				=> '#FFFFFFF',
				'font-weight' 		=> 300,
				'text-decoration' 	=> 'none',
				'background-color'	=> '#575757'
			)
		),
		'content_play_video_link' => array(
			'css_selector' 	=> '.fa_slide_content .fa_play_video',
			'description' 	=> __( 'Play video link', 'featured-articles-lite' ),
			'properties' 	=> array(
				'font-size' 		=> '1em',
				'color'				=> '#000000',
				'text-decoration' 	=> 'underline'
			)
		),
		'video_container' => array(
			'css_selector' 	=> '.fa_image .fa-video-wrap .fa-video',
			'description' 	=> __( 'Video container', 'featured-articles-lite' ),
			'properties' 	=> array(
				'background-color' => '#0000000',
				'border-width' => '1px',
				'border-style' => 'solid',
				'border-color' => '#9999999',
				'background-color' => '#000000'
			)
		),
		'video_container_play_video' => array(
			'css_selector' 	=> '.fa_image .play-video',
			'description' 	=> __( 'Play video image link', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' 		=> '#FFFFFF',
				'background' 	=> '#000000',
				'border-radius' => '5px'
			)
		),
		'nav_right' => array(
			'css_selector' 	=> '.go-forward',
			'description' 	=> __( 'Navigation forward', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' 			=> '#C9C9C9',
				'background-color' 	=> '#EDEDED'
			)
		),
		'nav_left' => array(
			'css_selector' 	=> '.go-back',
			'description' 	=> __( 'Navigation back', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' 			=> '#C9C9C9',
				'background-color' 	=> '#EDEDED'
			)
		),
		'nav_bottom_idle' => array(
			'css_selector' 	=> '.main-nav .fa-nav',
			'description' 	=> __( 'Navigation bottom', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' 		=> '#777777',
				'text-shadow' 	=> '0px 0px 1px #999999'
			)
		),
		'nav_bottom_hover' => array(
			'css_selector' 	=> '.main-nav .fa-nav:hover',
			'description' 	=> __( 'Navigation bottom mouse over', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' => '#000000'
			)
		),
		'nav_bottom_active' => array(
			'css_selector' 	=> '.main-nav .fa-nav.active',
			'description' 	=> __( 'Navigation bottom current item', 'featured-articles-lite' ),
			'properties' 	=> array(
				'color' => '#575757'
			)
		),
		'progress_bar' => array(
			'css_selector' 	=> '.progress-bar',
			'description' 	=> __( 'Slide timer (on autoslide)', 'featured-articles-lite' ),
			'properties' 	=> array(
				'background-color' => '#999999'
			)
		)
	);
	
	return $rules;
}
