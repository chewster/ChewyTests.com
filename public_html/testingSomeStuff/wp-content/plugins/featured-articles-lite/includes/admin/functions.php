<?php 
/**
 * Displays admin messages
 */
function fa_display_admin_message(){
	
	$messages = array(
		701 => __('Preview settings saved.', 'featured-articles-lite'),
		801 => __('Plugin settings saved.', 'featured-articles-lite')
		
	);
	
	if( isset( $_GET['message'] ) ){
		$message_id = absint( $_GET['message'] );		
		if( array_key_exists( $message_id , $messages ) ){
?>			
<div id="message" class="updated"><p><?php echo $messages[ $message_id ];?></p></div>
<?php			
		}		
	}	
}

function fa_option_not_available( $text = false, $echo = true ){
	
	$result = '<span class="fapro-option-not-available"><span>PRO</span></span>';
	if( !$text ){
		$result .= ' <span class="description">not available</span>';
	}else{
		$result .= ' <span class="description">' . $text . '</span>';
	}
	
	if( $echo ){
		echo $result;
	}	
	return $result;
}

/**
 * Generates the absolute path to an administration page template.
 * Templates are named: template-$template.php
 * Only NAME should be passed to this function.
 * 
 * @uses fa_view_path
 * 
 * @param string $template - template name without template- prefix and .php extension
 * @return string - absolute path to template location 
 */
function fa_template_path( $template ){	
	$file = 'template-' .  $template  . '.php'; 
	return fa_view_path($file);	
}

/**
 * Generates the absolute path to an administration metabox template.
 * Templates are named: metabox-$template.php
 * Only NAME should be passed to this function.
 * 
 * @uses fa_view_path()
 * 
 * @param string $template - template name without metabox- prefix and .php extension
 * @return string - absolute path to template location 
 */
function fa_metabox_path( $template ){
	$file = 'metabox-' .  $template . '.php';
	return fa_view_path($file);	
}

/**
 * Generates the absolute path to an administration modal template.
 * Templates are named: modal-{$template}.php
 * Only NAME should be passed to this function.
 * 
 * @uses fa_view_path()
 * 
 * @param string $template - template name without metabox- prefix and .php extension
 * @return string - absolute path to template location 
 */
function fa_modal_path( $template ){
	$file = 'modal-' . $template . '.php';
	return fa_view_path( $file );
}

/**
 * Returns absolute path for a file in plugin views folder
 * @param string $file
 */
function fa_view_path( $file ){
	$rel_path = 'views/' . sanitize_file_name( $file ); 
	$path = wp_normalize_path( path_join( FA_PATH, $rel_path ) );
	if( !is_file( $path ) ){
		trigger_error( sprintf( __('Template %s does not exist.', 'featured-articles-lite'), $path), E_USER_WARNING );
	}else{
		return $path;
	}	
}

/**
 * Enqueues a given admin stylesheet. Parameter should
 * be without .css extension 
 * 
 * @param string $stylesheet - stylesheet filename from within folder assets/admin/css without .css extension
 */
function fa_load_admin_style( $stylesheet ){
	
	$url = fa_get_uri( 'assets/admin/css/' . $stylesheet . '.css' );
	wp_enqueue_style(
		'fa-style-' . $stylesheet,
		$url,
		false,
		FA_VERSION
	);
	return 'fa-style-' . $stylesheet;
}

/**
 * Enqueues the stylesheet for a given template. Stylesheet should be inside plugin folder:
 * assets/admin/css and should be named template-$template.css
 * 
 * @param string $template
 */
function fa_load_template_style( $template ){	
	return fa_load_admin_style( 'template-' . $template );	
} 

/**
 * Enqueues a given admin script. File name should not have .js extension.
 * An array of dependencies can be passed to it.
 * 
 * @param string $script - filename from within plugin folder assets/admin/js without the .js extension
 * @param array $dependency - array of dependencies. Defaults to jquery
 * 
 * @return string - script handle
 */
function fa_load_admin_script( $script, $dependency = array( 'jquery' ) ){	
	
	if( defined('FA_SCRIPT_DEBUG_ADMIN') && FA_SCRIPT_DEBUG_ADMIN ){
		$script .= '.dev';
	}else{
		$script .= '.min';
	}
	
	$url = fa_get_uri( 'assets/admin/js/' . $script . '.js' );
	wp_enqueue_script(
		'fa-script-' . $script,
		$url,
		$dependency		
	);	
	return 'fa-script-' . $script;
}

/**
 * Function to load a tinymce js plugin file. 
 * Tinymce plugins are located inside folder assets/js/tinymce/PLUGIN_NAME
 * Only pass the plugin folder name to the function. Actual js file should always be named plugin.js
 * 
 * @param string $plugin
 * @param array $dependency
 */
function fa_tinymce_plugin_url( $plugin ){
	$rel_path = 'assets/admin/js/tinymce/' . $plugin . '/plugin.js';
	return fa_get_uri( $rel_path );
}

/**
 * Function to load a tinymce plugin styling.
 * Tinymce plugins are located inside folder assets/js/tinymce/PLUGIN_NAME
 * Only pass the plugin folder name to the function. Actual css file should always be named style.css
 * @param string $plugin
 */
function fa_tinymce_plugin_style( $plugin ){
	$rel_path = 'assets/admin/js/tinymce/' . $plugin . '/style.css';
	return fa_get_uri( $rel_path );
}

/**
 * Processes the allowed post types that slides can be made from.
 * Uses the settings from plugin Settings page.
 * @return array - array of allowed post types
 */
function fa_allowed_post_types(){
	return array('post', 'page');	
}

/**
 * Returns setting for slide edit on post edit page
 */
function fa_allowed_slide_edit(){
	// get the allowed post types from plugin settings
	$options = fa_get_options('settings');
	if( isset( $options['post_slide_edit'] ) ){
		return $options['post_slide_edit'];
	}
	return false;	
}

/**
 * Returns a list of checkboxes for a given set of options
 * @param array $attr - attributes for displaying the checkboxes
 */
function fa_checkboxes( $attr ){
	$defaults = array(
		'name' 		=> false,
		'id'		=> '',
		'selected' 	=> array(),
		'options' 	=> array(),
		'separator' => '<br />',
		'echo'		=> true
	);
	extract( wp_parse_args($attr, $defaults), EXTR_SKIP );
	
	if( !$options ){
		return false;
	}
	if( !is_array( $selected ) ){
		$seelected = array();
	}
	if( empty( $id ) ){
		$id = $name;
	}
	
	$output = '';
	foreach( $options as $value => $label ){
		$checked = in_array($value, $selected) ? 'checked="checked"' : '';
		$el_id = esc_attr( $id . $value );
		$output .= sprintf(
			'<input type="checkbox" name="%1$s" value="%2$s" id="%3$s" %4$s /><label for="%3$s">%5$s</label>%6$s',
			$name . '[]',
			$value,
			$el_id,
			$checked,
			$label,
			$separator
		);
	}
	
	if( $echo ){
		echo $output;
	}
	return $output;
}

/**
 * Displays checked argument in checkbox
 * @param bool $val
 * @param bool $echo
 */
function fa_checked( $val, $echo = true ){
	$checked = '';
	if( is_bool($val) && $val ){
		$checked = ' checked="checked"';
	}
	if( $echo ){
		echo $checked;
	}else{
		return $checked;
	}	
}

/**
 * Displays a style="hidden" on an element if $val is bool true
 *  
 * @param bool $val - value to evaluate
 * @param bool $include_style - include style="" or just display the css
 * @param bool $echo
 */
function fa_hide( $val, $include_style = true, $echo = true ){
	if( !is_bool( $val ) ){
		if( defined( 'FA_SCRIPT_DEBUG' ) && FA_SCRIPT_DEBUG ){
			$trace = debug_backtrace();
			trigger_error( sprintf('Value passed to function should be type BOOL ( function called from %s line %d ).', $trace[0]['file'], $trace[0]['line']), E_USER_ERROR );
			return;
		}
	}
	
	$output = '';
	if( !$val ){
		return $output; 
	}else{
		$output = 'display:none;';
		if( $include_style ){
			$output = 'style="' . $output . '"';
		}
	}
	if( $echo ){
		echo $output;
	}
	return $output;	
}

/**
 * Display select box
 * @param array $args - see $defaults in function
 * @param bool $echo
 */
function fa_dropdown( $args = array() ){
	
	$defaults = array(
		'options' 	=> array(),
		'name'		=> false,
		'id'		=> false,
		'class'		=> '',
		'selected'	=> false,
		'use_keys'	=> true,
		'hide_if_empty' => true,
		'show_option_none' => __('No options', 'featured-articles-lite'),
		'select_opt'	=> __('Choose', 'featured-articles-lite'),
		'select_opt_style' => false,
		'attrs'	=> '',
		'echo' => true
	);
	
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
	
	if( $hide_if_empty  && !$options && !$show_option_none){
		return;
	}
	
	if( !$id ){
		$id = $name;		
	}
	
	$output = sprintf( '<select autocomplete="off" name="%1$s" id="%2$s" class="%3$s" %4$s>', esc_attr( $name ), esc_attr( $id ), esc_attr( $class ), $attrs );
	if( !$options && $show_option_none ){
		$output .= '<option value="">' . $show_option_none . '</option>';	
	}elseif( $select_opt ){		
		$output .= '<option value=""'. ( $select_opt_style ? ' style="' . $select_opt_style . '"' : '' ) .'>' . $select_opt . '</option>';	
	}	
	
	foreach( $options as $val => $text ){
		$opt = '<option value="%1$s"%2$s>%3$s</option>';
		$value = $use_keys ? $val : $text;
		$c = $use_keys ? $val == $selected : $text == $selected;
		$checked = $c ? ' selected="selected"' : '';		
		$output .= sprintf($opt, $value, $checked, $text);		
	}
	
	$output .= '</select>';
	
	if( $echo ){
		echo $output;
	}
	
	return $output;
}

/**
 * For a given theme, outputs a dropdown containing the 
 * CSS layout variations implemented in theme functions.php file.
 * 
 * @param string $theme - the theme identifier
 * @param array $args
 */
function fa_theme_layouts_dropdown( $theme, $args ){
	if( !$theme ){
		return false;
	}	
	
	$theme = fa_get_theme( $theme );
	if( !$theme['theme_config']['classes'] ){
		return false;
	}
	
	$defaults = array(
		'name'		=> false,
		'id'		=> false,
		'selected'	=> false,
		'use_keys'	=> true,
		'select_opt'	=> __('Choose', 'featured-articles-lite')
	);
	$args = wp_parse_args( $args, $defaults );
	$args['options'] = $theme['theme_config']['classes'];
	return fa_dropdown( $args );
}

/**
 * Display a dropdown of slide effects
 * @param unknown_type $args
 */
function fa_slide_effect_dropdown( $args = array() ){
	// the effects
	$options = array(
		'squares' 		 => __( 'Moving rectangles', 'featured-articles-lite' ),
		'zipper'		 => __( 'Zipper', 'featured-articles-lite' ),
		'ripple'		 => __( 'Ripple', 'featured-articles-lite' ),
		'fade'			 => __( 'Progressive fade', 'featured-articles-lite' ),
		'simple_squares' => __( 'Fading rectangles', 'featured-articles-lite' )
	);
	
	$defaults = array(
		'name'		=> false,
		'id'		=> false,
		'class'		=> '',
		'selected'	=> false,
		'select_opt'	=> __('Select effect', 'featured-articles-lite'),
		'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );
	$args['options'] = $options;
	$output = fa_dropdown( $args );
	return $output;
}

/**
 * Display a select box with folders from within wp-content folder
 * @param array $args
 */
function fa_select_extra_dir( $args = array() ){
	$default = array(
		'name' 		=> 'themes_dir',
		'id'		=> false,
		'selected'	=> false,
		'echo'		=> true,
		'select_opt'=> __('Choose folder', 'featured-articles-lite'),
		'hide_if_empty' => true,
		'show_option_none' => __('Nothing found', 'featured-articles-lite'),
		'use_keys' => false
	);
	$args = wp_parse_args($args, $default);
	$args['options'] = read_wp_content_dir();
	
	$output = fa_dropdown( $args );	
	return $output;
}

/**
 * Check if user has set the external directory for slider themes.
 */
function fa_is_extra_dir_set(){
	$options = fa_get_options('settings');
	if( isset( $options['themes_dir'] ) && !empty( $options['themes_dir'] ) ){
		return true;
	}
	return false;
}

/**
 * Output a dropdown with all registered WP image sizes
 * @param array $args
 */
function fa_wp_image_size_dropdown( $args = array() ){
	global $_wp_additional_image_sizes;
	
	$sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';
	$options = array();
	foreach( $sizes as $size ){
		$w = $h = 0;
		switch( $size ){
			case 'thumbnail':
				$w = intval( get_option('thumbnail_size_w') );
				$h = intval( get_option('thumbnail_size_h') );				
			break;
			case 'medium':
				$w = intval(get_option('medium_size_w'));
				$h = intval(get_option('medium_size_h'));
			break;
			case 'large':
				$w = intval(get_option('large_size_w'));
				$h = intval(get_option('large_size_h'));	
			break;
			case 'full':
				// nothing
			break;
			default:
				$w = isset( $_wp_additional_image_sizes[ $size ] ) ? $_wp_additional_image_sizes[ $size ]['width'] : 0;
				$h = isset( $_wp_additional_image_sizes[ $size ] ) ? $_wp_additional_image_sizes[ $size ]['height'] : 0;			
			break;
		}
		$options[ $size ] = ucfirst( str_replace( array('-', '_'), ' ', $size ) ) . ( $w && $h ? ' - max. '. $w . 'x' . $h . 'px' : '' );		
	}
	
	$default = array(
		'name' 			=> 'image_size',
		'id' 			=> false,
		'selected' 		=> false,
		'echo' 			=> true,
		'select' 		=> false,
		'select_opt'	=> false,
		'use_keys'		=> true
	);
	$args 				= wp_parse_args( $args, $default );
	$args['options'] 	= $options;
	
	$output = fa_dropdown( $args );
	return $output;
}

/**
 * Displays a dropdown of sliders
 * @param string $name
 * @param string $id
 * @param int $selected
 */
function fa_sliders_dropdown( $name, $id = false, $selected = false, $class = '', $status = 'publish', $echo = true ){
	
	$sliders = fa_get_sliders( $status );
	if( !$sliders ){
		return false;	
	}
		
	$options = array();
	foreach( $sliders as $slider ){
		$options[ $slider->ID ] = !empty( $slider->post_title ) ? esc_attr( $slider->post_title ) : '(' . esc_attr( __('no title', 'featured-articles-lite')) . ')';
	}
	
	$args = array(
		'options' 	=> $options,
		'name'		=> $name,
		'id'		=> $id,
		'class'		=> $class,
		'selected'	=> $selected,
		'select_opt'	=> __('Choose slider', 'featured-articles-lite'),
		'echo' 		=> false
	);
	$result = fa_dropdown( $args );
	if( $echo ){
		echo $result;
	}
	return $result;
}

/**
 * Reads the folders inside wp_content folder to help the user choose the folder where to store 
 * extra fa themes
 */
function read_wp_content_dir(){	
	$content_dir = @ opendir( WP_CONTENT_DIR );
	$folders = array();
	if ( $content_dir ) {
		while (($file = readdir( $content_dir ) ) !== false ) {
			if ( substr($file, 0, 1) == '.' )
				continue;
			if ( is_dir( WP_CONTENT_DIR.'/'.$file ) ) {
				$folders[] = $file;
			}
		}
		closedir( $content_dir );
	}
	return $folders;	
}

/**
 * Color picker
 * @param array $args
 */
function fa_color_picker( $args = array() ){
	
	$defaults = array(
		'name' 			=> 'fa_color_picker',
		'id'			=> false,
		'value'			=> '',
		'attr'			=> false,
		'autoload'		=> true,
		'class'			=> false
	);
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
	
	if( !$id ){
		$id = $name;		
	}
	// load the script that starts the color pickers
	if( $autoload ){
		// load assets
		fa_load_admin_script(
			'color-picker',
			array(
				'wp-color-picker'
			)
		);
	}else{
		wp_enqueue_script('wp-color-picker');
	}	
	wp_enqueue_style('wp-color-picker');	
?>
<input class="fapro-color-picker-hex <?php echo $class;?>" type="text" maxlength="7" <?php echo $attr;?> name="<?php echo esc_attr( $name );?>" id="<?php echo esc_attr( $id );?>" value="<?php echo $value;?>" placeholder="<?php _e('Hex value', 'featured-articles-lite')?>" />
<?php	
}

/**
 * Display upload button
 * @param string $name
 * @param string $id
 */
function fa_media_gallery( $args = array() ){
	
	$defaults = array(
		'name' 				=> 'fa_upload',
		'id'				=> 'fa_upload',
		'page_title' 		=> __('Select images (multiple image select enabled)', 'featured-articles-lite'),
		'button_text' 		=> __('Set images', 'featured-articles-lite'),
		'select_multiple' 	=> true, // allows multiple images selection
		'class'				=> 'button',
		'update_elem'		=> '#fa-selected-images', // the element ID that should be updated with the response from Ajax after selecting images
		'append_response'	=> true, // append response to $update_elem element ID
		'attributes'		=> false // extra attributes
	);
	extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
	
	
	if( !$id ){
		$id = $name;		
	}
	wp_enqueue_media();
	fa_load_admin_script('media-gallery');
?>
<div class="uploader stag-metabox-table">
	<a href="#" class="fa_upload_image_button <?php echo $class?>" <?php echo $attributes;?> data-title="<?php echo $page_title;?>" data-multiple="<?php echo (bool) $select_multiple;?>" data-update="<?php echo $update_elem;?>" data-append="<?php echo $append_response;?>" id="<?php echo $id;?>_button">
		 <?php echo $button_text;?>
	</a>
</div>
<?php 	
}

/**
 * Display a dropdown to select positions (top, left, right, bottom)
 * @param array $args
 */
function fa_sliding_positions_dropdown( $args ){
	
	$options = array(
		'left' 		=> __( 'Left', 'featured-articles-lite' ),
		'right' 	=> __( 'Right', 'featured-articles-lite' ),
		'top' 		=> __( 'Top', 'featured-articles-lite' ),
		'bottom' 	=> __( 'Bottom', 'featured-articles-lite' ),
	);
	
	$defaults = array(
		'name'			=> false,
		'id'			=> false,
		'class'			=> '',
		'selected'		=> false,
		'use_keys'		=> true,
		'select_opt'	=> false,
		'echo' 			=> true
	);
	
	if( isset( $args['options'] ) )
		unset( $args['options'] );
	
	$defaults['options'] = $options;
	
	$args = wp_parse_args( $args, $defaults );
	$dropdown = fa_dropdown( $args );
	
	return $dropdown;	
}

/**
 * Output theme colors dropdown
 * @param array $theme_details - theme details as returned by theme manager
 * @param array $args
 */
function fa_theme_colors_dropdown( $theme_details, $args = array() ){
	
	$colors = $theme_details['colors'];
	if( !$colors ){
		return;
	}
	
	$options = array();
	foreach( $colors as $c => $d ){
		$options[ $c ] = $d['name'];
	}
	if( isset($args['options']) ){
		unset( $args['options'] );
	}
	
	$defaults = array(
		'name' 		=> false,
		'id'		=> false,
		'multiple'	=> true,
		'label'		=> false,
		'selected' 	=> false,
		'options' 	=> $options,
		'use_keys' 	=> true,
		'select_opt'=> false
	);
	$args = wp_parse_args( $args, $defaults );
	if( $args['name'] ){
		if( $args['multiple'] ){
			$args['name'] .= '[' . $theme_details['dir'] . ']';
		}	
	}
	if( $args['id'] ){
		$args['id'] .= '-' . $theme_details['dir'];
	}
	$args['echo'] = false;
	$dropdown = fa_dropdown( $args );
	$label = '';
	
	if( $args['label'] ){
		$label = sprintf('<label for="%s">%s</label>: ',
			$args['id'],
			$args['label']
		);
	}	
	echo $label . $dropdown;
}

/**
 * Display a dropdown of mouse events
 * @param array $args
 */
function fa_sliding_event_dropdown( $args ){
	$options = array(
		'click' 		=> __( 'Click', 'featured-articles-lite' ),
		'mouseenter' 	=> __( 'Mouse hover', 'featured-articles-lite' )
	);
	
	$defaults = array(
		'name'			=> false,
		'id'			=> false,
		'class'			=> '',
		'selected'		=> false,
		'use_keys'		=> true,
		'select_opt' 	=> false,
		'echo' 			=> true
	);
	
	if( isset( $args['options'] ) )
		unset( $args['options'] );
	
	$defaults['options'] = $options;
	
	$args = wp_parse_args( $args, $defaults );
	$dropdown = fa_dropdown( $args );
	
	return $dropdown;	
}

/**
 * Generates an iframe link request from a given admin menu slug
 * @param string $slug - admin menu slug
 * @param bool $echo
 */
function fa_iframe_admin_page_url( $slug, $args = array(), $echo = true ){
	
	$args = array_merge( array(
		'fapro_inline' => 'true'
	), $args );
	
	$page_url = menu_page_url( $slug, false );
	if( defined('DOING_AJAX') && DOING_AJAX ){
		$page_url = 'admin.php?page=' . $slug;		
	}
	
	$url = add_query_arg( $args, $page_url);
	
	if( $echo ){
		echo $url;
	}	
	return $url;
	
}

/**
 * Outputs a formatted post status from given DB post status
 * @param string $status - status of post as returned from database
 */
function fa_output_post_status( $status ){
	switch ( $status ) {
		case 'private':
			_e('Privately Published', 'featured-articles-lite');
			break;
		case 'publish':
			_e('Published', 'featured-articles-lite');
			break;
		case 'future':
			_e('Scheduled', 'featured-articles-lite');
			break;
		case 'pending':
			_e('Pending Review', 'featured-articles-lite');
			break;
		case 'draft':
		case 'auto-draft':
			_e('Draft', 'featured-articles-lite');
			break;
	}
}

/**
 * Outputs a formatted post date from the database date
 * @param string $date - mysql date
 */
function fa_output_post_date( $date, $echo = true ){
	$datef = __( 'M j, Y @ G:i' );
	$d = date_i18n( $datef, strtotime( $date ) );
	if( $echo ){
		echo $d;
	}
	return $d;
}

/**
 * Outputs the registered post name of a given post type
 * @param string $post_type
 */
function fa_output_post_type( $post_type ){
	if( !post_type_exists( $post_type ) ){
		return;
	}
	$obj = get_post_type_object( $post_type );
	echo $obj->labels->singular_name;
}

/**
 * Display a warning for post statuses pending, future and draft
 * 
 * @param obj $post
 * @param string $before
 * @param string $after
 */
function fa_post_status_message( $post, $before, $after ){
	$message = false;
	switch( $post->post_status ){
		case 'pending':
			$message = __('<strong>Warning! </strong> Slide not visible until post is published.', 'featured-articles-lite');
		break;
		case 'future':
			$message = sprintf( __('<strong>Warning!</strong> Slide not visible until %s.', 'featured-articles-lite'), fa_output_post_date( $post->post_date, false ) );
		break;	
		case 'draft':
			$message = __('<strong>Warning!</strong> Slide not visible until post published.', 'featured-articles-lite');
		break;	
	}
	if( $message ){
		echo $before . $message . $after;	
	}	
}

/**
 * Displays the HTML used in admin area to display the manually
 * selected mixed posts.
 * 
 * @param int/obj $post
 */
function fa_slide_panel( $post, $slider_id ){
	if( is_numeric( $post ) ){
		$post = get_post( $post );
	}
	
	if( !$post ){
		return ;
	}
	
	$options = fa_get_slide_options( $post->ID );
	$slide_title = $post->post_title;
	$slide_content = $post->post_content;
	
	if( !empty( $options['title'] ) && $post->post_title != $options['title'] ){
		$slide_title = $options['title'];
		$post_title = $post->post_title;			
	}
	if( !empty( $options['content'] ) ){
		$slide_content = $options['content'];
	}
?>
<div class="fa-slide <?php echo esc_attr( $post->post_status );?>" id="post-<?php echo $post->ID;?>" data-post_id="<?php echo $post->ID;?>">
	<a href="<?php fa_iframe_admin_page_url( 'fa-post-slide-edit', array('post_id' => $post->ID, 'slider_id' => $slider_id ) );?>" id="fa-slide-edit-<?php echo $post->ID;?>" class="fapro-modal-trigger fa-slide-edit" data-target="fapro-modal" data-slide_id="<?php echo $post->ID;?>" data-slider_id="<?php echo $slider_id;?>" data-type="mixed"><i class="dashicons dashicons-admin-generic"></i></a>
	<a href="#" id="fa-slide-remove-<?php echo $post->ID;?>" class="fa-slide-remove"><i class="dashicons dashicons-dismiss"></i></a>
	<div class="slide-inside">
		<h3>
			<a href="<?php echo get_edit_post_link( $post->ID, '' );?>" target="_blank"><?php echo wp_trim_words( $slide_title, 6, '...' );?></a>
		</h3>
		<div class="slide-details">
			<ul>
				<?php if( isset( $post_title ) ):?>
				<li><strong><?php _e('Post title', 'featured-articles-lite');?>:</strong> <?php echo wp_trim_words( $post_title, 3, '...' );?></li>
				<?php endif;?>
				<li><strong><?php _e('Post status', 'featured-articles-lite');?>:</strong> <?php fa_output_post_status( $post->post_status );?></li>
				<li><strong><?php _e('Post date', 'featured-articles-lite');?>:</strong> <?php fa_output_post_date( $post->post_date );?></li>
				<li><strong><?php _e('Post type', 'featured-articles-lite');?>:</strong> <?php fa_output_post_type( $post->post_type );?></li>
				<?php 
    			    /**
    			     * Allow display of other slide details
    			     * @param $post - current post object
    			     */
    			    do_action( '_fa_slide_panel', $post );
    			?>
				<li>
					<strong><?php _e('Image', 'featured-articles-lite');?>:</strong>
					<?php 
						require_once fa_get_path( 'includes/templating.php' );
						$image_id = get_the_fa_image_id( $post->ID );
						if( !$image_id ){
							if( !empty( $options['temp_image_url'] ) ){
								$image_url = $options['temp_image_url'];
							}			
						}else{
							$image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
							if( $image ){
								$image_url = $image[0];					
							}		
						}
						if( isset( $image_url ) && $image_url ):
					?>
					<img src="<?php echo $image_url?>" />
					<?php else:?>
					<?php _e('none', 'featured-articles-lite');?>	
					<?php endif;?>
				</li>
				<?php fa_post_status_message( $post, '<li class="warning">', '</li>' );?>			
			</ul>
			<input type="hidden" name="slides[posts][]" value="<?php echo $post->ID;?>" />
		</div>	
	</div>		
</div>
<?php
}

/**
 * Output the image attached to a slide
 * @param int $post_id
 */
function the_fa_slide_image( $post_id ){
	$post_id = absint( $post_id );
	$options = fa_get_slide_options( $post_id );
	
	$image = false;
	if( $options['image'] ){
		$image = wp_get_attachment_image( $options['image'], 'thumbnail' );		
	}
	// always enqueue the media gallery scripts
	wp_enqueue_media();
	fa_load_admin_script('media-gallery');	
?>	
<div id="fa-selected-images">
<?php if( $image ):?>
	<div class="fa-slide-image" data-post_id="<?php echo $options['image']?>">		
		<?php
			$args = array(
				'page_title' => __('Select image to use in slide', 'featured-articles-lite'),
				'button_text' => $image,
				'select_multiple' => false,
				'class'				=> 'fa-img'
			);
			fa_media_gallery( $args );
		?>			
	</div>
	<a href="#" id="fa-remove-slide-image" data-post_id=<?php echo $post_id;?>><?php _e('Remove image', 'featured-articles-lite');?></a>	
<?php else:// show the image select button?>
	<?php 
		$args = array(
			'page_title' => __('Select image to use in slide', 'featured-articles-lite'),
			'button_text' => __('Select image', 'featured-articles-lite'),
			'select_multiple' => false
		);
		fa_media_gallery( $args );
	?>
<?php endif;// if($image)?>
</div>
<?php	
}// the_fa_slide_image

/***********************************************************************
 * Slideshow themes management
 ***********************************************************************/
/**
 * Get registered slideshow themes
 */
function fa_get_themes(){
	global $fa_theme_manager;
	if( !class_exists('FA_Themes_Manager') ){
		require_once fa_get_path('includes/admin/libs/class-fa-themes-manager.php');
		$fa_theme_manager = new FA_Themes_Manager();
	}
	if( !$fa_theme_manager ){
		$fa_theme_manager = new FA_Themes_Manager();
	}
	
	$themes = $fa_theme_manager->get_themes();
	return $themes;
}

/**
 * Get a theme details
 * @param string $theme
 */
function fa_get_theme( $theme ){
	$themes = fa_get_themes();
	if( array_key_exists( $theme , $themes) ){
		return $themes[ $theme ];
	}
	return false;
}

/**
 * Get theme settings for plugin optional fields
 * @param string $theme
 */
function fa_get_themes_fields(){
	$themes = fa_get_themes();
	$result = array();
	
	foreach( $themes as $theme => $details ){
		$result[ $theme ] = isset( $details['theme_config']['fields'] ) ? (array) $details['theme_config']['fields'] : array();
	}
	return $result;	
}

/**
 * Displays data attributes for a given field stating which themes should enable/disable the field by default
 * @param string $field
 */
function fa_optional_field_data( $field ){
	$themes = fa_get_themes_fields();
	$disable = array();
	$enable = array();
	foreach( $themes as $theme => $fields ){
		if( isset( $fields[ $field ] ) ){
			if( $fields[ $field ] ){
				$enable[] = $theme;
			}else{
				$disable[] = $theme;
			}
		}else{
			$enable[] = $theme;
		}
	}
	
	$output = ' data-theme_enable="' . implode(',', $enable) . '"';
	$output.= ' data-theme_disable="' . implode(',', $disable) . '"';
	echo $output;
}

/**
 * Check if a field is enabled/disabled by the slider theme
 * @param string $theme
 * @param string $field
 */
function fa_theme_field_enabled( $theme, $field ){
	$theme = fa_get_theme( $theme );
	if( !$theme ){
		return true;
	}
	
	if( isset( $theme['theme_config']['fields'][ $field ] ) ){
		return (bool) $theme['theme_config']['fields'][ $field ] ;
	}	
	return true;
}

/**
 * Displays a dropdown of available slideshow themes
 * @param array $args
 */
function fa_themes_dropdown( $args = array() ){
	$defaults = array(
		'name'		=> false,
		'id'		=> false,
		'class'		=> '',
		'selected'	=> false,
		'use_keys'	=> true,
		'hide_if_empty' => true,
		'show_option_none' => __('No themes', 'featured-articles-lite'),
		'select_opt'	=> __('Choose theme', 'featured-articles-lite'),
		'echo' => true
	);
	
	if( isset( $args['options'] ) )
		unset( $args['options'] );
	
	$themes = fa_get_themes();
	$options = array();
	foreach( $themes as $theme => $theme_data ){
		$options[ $theme ] = ucfirst( str_replace('_', ' ', $theme) );
	}
	$defaults['options'] = $options;
	
	$args = wp_parse_args( $args, $defaults );
	$dropdown = fa_dropdown( $args );
	
	return $dropdown;	
}

/************************************************************
 * Slider preview functionality
 ************************************************************/
/**
 * Outputs a preview link to homepage URL
 * @param array $args
 */
function fa_slider_preview_homepage( $args ){
	$defaults = array(
		'post_id' 	=> false,
		'theme' 	=> false,
		'vars'		=> array(),
		'echo'		=> true
	);
	
	extract( wp_parse_args($args, $defaults), EXTR_SKIP );
	if( !$post_id ){
		echo '#';
	}
	
	$params = array(
		'slider_id' 		=> $post_id,
		'theme'				=> $theme,
		'fa_slider_preview' => true
	);
	if( is_array( $vars ) ){
		$params = array_merge( $params, $vars );
	}
	
	$homepage_url = add_query_arg( $params, home_url('/') );
	
	$homepage_url = wp_nonce_url( $homepage_url, 'fa-slider-theme-preview', 'fa-preview-nonce' );	

	if( $echo ){
		echo $homepage_url;
	}
	return $homepage_url;	
}

/************************************************************
 * Slider theme functions.php specific functions
 * These functions are designed to be used only in
 * slider themes functions.php file.
 ************************************************************/
/**
 * This function should only be called from slider theme folder.
 * Returns a specific key for a given slider theme functions file path.
 * The key is the actual name of the folder that contains all slider theme files.
 * 
 * @param string $file - absolute path to slider theme functions.php file
 */
function fa_get_theme_key( $file ){
	$key = basename( dirname( $file ) );
	return $key;
}

/**
 * Gets the options implemented by a theme based on the absolute path
 * of the functions.php slider theme file
 * 
 * @param string $file - absolute path to slider theme functions.php file
 */
function fa_get_theme_options( $file, $post ){
	$key = fa_get_theme_key( $file );
	if( is_object( $post ) ){
		$post_id = $post->ID;
	}else{
		$post_id = absint( $post );
	}
	
	$options = fa_get_slider_options( $post_id, 'themes_params' );
	if( $options ){
		if( isset( $options[ $key ] ) ){
			return $options[ $key ];
		}
	}	
	return false;	
}

/**
 * Returns the variable name that is compatible with the plugin way of saving variables
 * @param string $var_name
 * @param string $file
 */
function fa_theme_var_name( $var_name, $file, $echo = true ){
	$name = esc_attr( $var_name );
	$key = fa_get_theme_key( $file );
	$output = 'themes_params[' . $key . '][' . $name . ']';
	if( $echo ){
		echo $output;
	}
	
	return $output;	
}

/**
 * Check the existance of the theme CSS customization function.
 * @param string $theme - theme name
 */
function fa_theme_is_customizable( $theme ){
	return function_exists( 'fa_theme_css_' . $theme );
}

/***********************************
 * SLIDERS
 ***********************************/

/**
 * Returns all sliders
 */
function fa_get_sliders( $status = 'publish' ){	
	$args = array(
		'post_type' => fa_post_type_slider(),
		'post_status' => $status
	);
	$sliders = get_posts( $args );	
	return $sliders;	
}

/**
 * Retrieves the posts attached to a slider that are used to
 * create the slides. Used when slides are made of manually
 * selected mixed posts, pages and custom types
 *
 * @param $post_id - slider ID
 * @param $post_status - the status of the posts
 */
function fa_get_slider_posts( $post_id, $post_status = 'publish' ){

    $settings = fa_get_options('settings');
    $slider_options = fa_get_slider_options( $post_id, 'slides' );

    $post_ids = (array) $slider_options['posts'];
    if( !$post_ids ){
        return;
    }

    $args = array(
        'posts_per_page' 		=> -1,
        'nopaging'				=> true,
        'ignore_sticky_posts' 	=> true,
        'offset'				=> 0,
        'post__in'				=> $post_ids,
        'post_type'				=> array( 'post', 'page' ),
        'post_status'			=> $post_status,
        'suppress_filters'      => true
    );

    $query = new WP_Query( $args );
    $posts = $query->get_posts();

    $result = array();
    foreach( $post_ids as $post_id ){
        foreach( $posts as $post ){
            if( $post_id == $post->ID ){
                $result[] = $post;
                break;
            }
        }
    }
    return $result;
}