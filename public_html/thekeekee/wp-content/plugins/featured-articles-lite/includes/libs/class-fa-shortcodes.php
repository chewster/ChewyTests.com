<?php
/**
 * Class that implements all plugin shortcodes
 */
class FA_Shortcodes{
	
	private $slider_id 	= false;
	private $atts		= array();
	
	/**
	 * Constructor; starts all plugin shortcodes
	 */
	public function __construct(){
		$shortcodes = $this->shortcodes();
		foreach( $shortcodes as $tag => $data ){
			add_shortcode( $tag , $data['callback'] );
		}		
	}
	
	/**
	 * Contains all shortcodes implemented by the plugin
	 */
	private function shortcodes( $shortcode = false ){
		$shortcodes = array(
			'fa_slider' => array(
				'callback' => array( $this, 'shortcode_fa_slider' ),
				'atts' => array(
					'id' 		=> 0
				)
			)
		);	
		$shortcodes['FA_Lite'] = $shortcodes['fa_slider'];
		
		if( $shortcode ){
			if( array_key_exists( $shortcode , $shortcodes ) ){
				return $shortcodes[ $shortcode ];
			}else{
				return false;
			}
		}		
		return $shortcodes;
	}
	
	/**
	 * Shortcode fa_slider callback function.
	 * Displays a slider from shortcode.
	 */
	public function shortcode_fa_slider( $atts ){
		// if displaying a slider, don't allow the shortcodes to avoid an infinite loop
		global $fa_slider;
		if( $fa_slider ){
			return;
		}
		
		$data = $this->shortcodes('fa_slider');
		$this->atts = shortcode_atts( 
			$data['atts'], 
			$atts 
		);
		
		foreach( $this->atts as $key => $value ){
			if( 'false' == $value && is_bool( $data['atts'][ $key ] ) ){
				$this->atts[ $key ] = false;
			}
		}
		
		extract( $this->atts , EXTR_SKIP );
		$show = true;	
		
		// store the current slider id
		$this->slider_id = $id;
				
		// display the slider. Assign as area shortcode_area
		ob_start();	
		fa_display_slider( $id, 'shortcode_area' );
		$output = ob_get_clean();
		
		
		// clear the slider id class variable
		$this->slider_id 	= false;
		$this->atts			= array();
				
		// return the slider output
		return $output;					
	}	
}