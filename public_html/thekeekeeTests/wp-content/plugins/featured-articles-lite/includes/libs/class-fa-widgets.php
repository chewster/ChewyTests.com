<?php
/**
 * Enqueue some functionality scripts on widgets page
 */
function fa_widgets_styles(){
	fa_load_admin_style('widgets');	
}
add_action('admin_print_styles-widgets.php', 'fa_widgets_styles');

/**
 * Featured Articles slider widget class.
 * Allows multiple instances of the widget.
 */
class FA_Widgets extends WP_Widget{
	
	private $slider_id 	= false;
	private $atts		= array();
	
	/**
	 * Create the slider widget
	 */
	public function __construct(){
		/* Widget settings. */
		$widget_opts = array( 
			'classname' => 'fa_slideshow', 
			'description' => __('Add a Featured Articles slider widget', 'featured-articles-lite') );

		/* Widget control settings. */
		$control_opts = array( 'id_base' => 'fa-slideshow-widget' );

		/* Create the widget. */
		parent::__construct( 
			'fa-slideshow-widget', 
			__('Featured Articles slider', 'featured-articles-lite'), 
			$widget_opts, 
			$control_opts
		);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::widget()
	 */
	function widget( $args, $instance ){
		if( !$instance || ( !isset( $instance['slider_id'] ) || !$instance['slider_id'] ) ){
			return;			
		}
		
		$slider = get_post( $instance['slider_id'] );
		if( !$slider || ( 'publish' != $slider->post_status && !fa_is_preview() ) ){
			return;
		}
		
		extract( $args, EXTR_SKIP );
		// output HTML before widget as set by sidebar
		echo $before_widget;
		// output the widget title
		$title = apply_filters('widget_title', $instance['title'] );
		if( $instance['title'] ){
			// output the widget title
			echo $before_title . $title . $after_title;			
		}
		
		$this->slider_id = $instance['slider_id'];
		$this->atts = $instance;
						
		// display the slider; assign it to widget area to be able to check into the display filter (index.php in plugin files).
		fa_display_slider( $instance['slider_id'], 'widget_area' );		
		// output HTML after widget as set by sidebar
		echo $after_widget;
		
		// clear the slider id class variable
		$this->slider_id 	= false;
		$this->atts			= array();
		// remove the filter to avoid messing the display of the slider on other areas
		remove_filter( 'fa_get_slider_options', array( $this, 'options' ), 999 );
		// remove show filter
		remove_filter( 'fa_display_slider' , array( $this, 'overwrite_options' ), 999 );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::form()
	 */
	function form( $instance ){
		extract( wp_parse_args( $instance, $this->_defaults() ), EXTR_SKIP );		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' )?>"><?php _e( 'Title', 'featured-articles-lite' );?>: </label>
			<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' );?>" id="<?php echo $this->get_field_id( 'title' );?>" value="<?php echo esc_attr( $title );?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'slider_id' );?>"><?php _e('Slider', 'featured-articles-lite');?>: </label>
			<?php fa_sliders_dropdown( $this->get_field_name( 'slider_id' ), $this->get_field_id( 'slider_id' ), $slider_id, 'widefat' );?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' )?>"><?php _e('Width', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'height' )?>"><?php _e('Height', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'full_width' )?>"><?php _e('Display full width', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'margin_top' )?>"><?php _e('Top distance', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'margin_bottom' )?>"><?php _e('Bottom distance', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'font_size' )?>"><?php _e('Font size', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_title' )?>"><?php _e('Show titles in slides', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_date' )?>"><?php _e('Show slides date', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_content' )?>"><?php _e('Show slide text', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_read_more' )?>"><?php _e('Show read more link', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_play_video' )?>"><?php _e('Show play video link', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'img_click' )?>"><?php _e('Image is clickable', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'auto_slide' )?>"><?php _e('Autoslide', 'featured-articles-lite');?>: </label>
			<?php fa_option_not_available();?>
		</p>	
		<?php
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_Widget::update()
	 */
	function update( $new_instance, $old_instance ){
		$instance = array();
		$defaults = $this->_defaults();
		
		foreach( $defaults as $field => $value ){
			$type = gettype( $value );
			switch( $type ){
				case 'integer':
					if( isset( $new_instance[ $field ] ) ){
						$defaults[ $field ] = absint( $new_instance[ $field ] );
					}
				break;
				case 'string':
					$defaults[ $field ] = $new_instance[ $field ];					
				break;				
			}			
		}
		
		return $defaults;
	}
	
	/**
	 * Widget default values
	 */
	private function _defaults(){
		$defaults = array(
			'title' 		=> __('Featured', 'featured-articles-lite'),
			'slider_id' 	=> 0
		);
		return $defaults;
	}
}