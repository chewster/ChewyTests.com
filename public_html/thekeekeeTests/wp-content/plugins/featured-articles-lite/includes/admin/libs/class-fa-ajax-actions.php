<?php
/**
 * Registers and manages all AJAX calls
 */
class FA_Ajax_Actions{
	
	/**
	 * Constructor. Sets all registered ajax actions.
	 */
	public function __construct(){
		// get the actions
		$actions = $this->actions();
		// add wp actions
		foreach( $actions as $action ){
			add_action('wp_ajax_' . $action['action'], $action['callback']);
		}		
	}
	
	/**
	 * Returns the output for a slide assigned to a slider
	 */
	public function assign_slide(){
		$action = $this->get_action_data( 'assign_slide' );
		check_ajax_referer( $action['nonce']['action'], $action['nonce']['name'] );
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : -1;
		
		if( !current_user_can( 'edit_fa_items', $post_id ) ){
			wp_die( -1 );
		}
		
		$slider_id = absint( $_POST['slider_id'] );
		
		$post = get_post( $post_id );
		$output = '';
		if( $post ){
			if( 'auto-draft' != $post->post_status ){
				ob_start();
				fa_slide_panel( $post_id, $slider_id );
				$output = ob_get_clean();
			}			
		}		
		
		/**
		 * Action on post assignment to slider. Will run every time a post is
		 * set as a slide to a given slider.
		 * 
		 * @param int $post_id - ID of post being assigned to slider 
		 */
		do_action('fa_assign_post_to_slider', $post_id);
		
		wp_send_json_success( $output );
		
		die();
	}
	
	/**
	 * Assigns an image from the media gallery to be used as slide image
	 */
	public function assign_slide_image(){
		$action = $this->get_action_data( 'assign_slide_image' );
		check_ajax_referer( $action['nonce']['action'], $action['nonce']['name'] );
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : -1;
		
		if( !current_user_can( 'edit_fa_items', $post_id ) ){
			wp_die( -1 );
		}
		
		$image_id = isset( $_POST['images'][0] ) ? absint( $_POST['images'][0] ) : false;
		if( !$image_id ){
			wp_send_json_error(__('No image selected.', 'featured-articles-lite'));
		}
		// update the image option
		fa_update_slide_options( $post_id , array( 'image' => $image_id ) );
		ob_start();
		// get the image output
		the_fa_slide_image( $post_id );
		// capture the output
		$output = ob_get_clean();
		wp_send_json_success( $output );		
		die();
	}
	
	/**
	 * Removes a previously set slide custom image
	 */
	public function remove_slide_image(){
		$action = $this->get_action_data( 'remove_slide_image' );
		check_ajax_referer( $action['nonce']['action'], $action['nonce']['name'] );
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : -1;
		
		if( !current_user_can( 'edit_fa_items', $post_id ) ){
			wp_die( -1 );
		}
		
		// update the image option
		fa_update_slide_options( $post_id , array( 'image' => '' ) );
		ob_start();
		// get the image output
		the_fa_slide_image( $post_id );
		// capture the output
		$output = ob_get_clean();
		wp_send_json_success( $output );		
		die();		
	}
	
	/**
	 * Assigns an image from the media gallery to be used as slide image
	 */
	public function assign_theme_image(){
		$action = $this->get_action_data( 'assign_theme_image' );
		check_ajax_referer( $action['nonce']['action'], $action['nonce']['name'] );
		if( !current_user_can( 'edit_fa_items' ) ){
			wp_die( -1 );
		}
		
		$image_id = isset( $_POST['images'][0] ) ? absint( $_POST['images'][0] ) : false;
		if( !$image_id ){
			wp_send_json_error(__('No image selected.', 'featured-articles-lite'));
		}
		
		$image = wp_get_attachment_image_src( $image_id, 'full' );
		if( isset( $image[0] ) ){
			$thumb = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			$output = '<div class="fa_slide_image" data-post_id="' . $image_id . '">';
			$output.= sprintf( '<img src="%s">', $thumb[0] );
			$output.= '</div>';			
			wp_send_json_success( array( 'html' => $output, 'image_url' => $image[0] ) );
		}else{
			wp_send_json_error(__('Image not found', 'featured-articles-lite'));
		}		
				
		die();
	}
	
	/**
	 * Assign sliders to dynamic areas
	 */
	public function slider_to_area(){
		$action = $this->get_action_data( 'assign_to_area' );
		check_ajax_referer( $action['nonce']['action'], $action['nonce']['name'] );
		if( !current_user_can( 'edit_fa_items' ) ){
			wp_die( -1 );
		}
		if( !isset( $_POST['areas'] ) ){
			wp_die( -1 );
		}
		
		$settings = fa_get_options('hooks');
		foreach( $_POST['areas'] as $area => $sliders ){
			// if area isn't found in stored areas, skip it
			if( !array_key_exists( $area , $settings ) ){
				continue;
			}
			$result = array();
			// empty the area if nothing is set
			if( empty( $sliders ) ){
				$settigs[ $area ]['sliders'] = $result;
			}
			
			$sliders = explode(',', $sliders);
			foreach( $sliders as $slider ){
				$slider_id = absint( str_replace( 'fa_slider-', '', $slider ) );
				$result[] = $slider_id;
			}
			$settings[ $area ]['sliders'] = $result;			
		}
		
		fa_update_options( 'hooks' , $settings );
		die();
	}
	
	/**
	 * Stores all ajax actions references.
	 * This is where all ajax actions are added.
	 */	
	private function actions(){
		$actions = array(
			/**
			 * Adds a new slide to slider.
			 */
			'assign_slide' => array(
				'action' 	=> 'fa-add-slide',
				'callback' 	=> array( $this, 'assign_slide' ),
				'nonce' 	=> array(
					'name'		=> 'fa_ajax_nonce',
					'action'	=> 'fa-assign-slide'
				) 
			),
						
			'assign_slide_image' => array(
				'action' 	=> 'fa-assign-slide-image',
				'callback' 	=> array( $this, 'assign_slide_image' ),
				'nonce' 	=> array(
					'name' 		=> 'fa_ajax_nonce',
					'action' 	=> 'fa_assign_slide_image' 
				)
			),
			'remove_slide_image' => array(
				'action' 	=> 'fa-remove-slide-image',
				'callback' 	=> array( $this, 'remove_slide_image' ),
				'nonce' 	=> array(
					'name' 		=> 'fa_ajax_nonce',
					'action' 	=> 'fa_remove_slide_image' 
				)
			),
			'assign_theme_image' => array(
				'action' 	=> 'fa-assign-theme-image',
				'callback' 	=> array( $this, 'assign_theme_image' ),
				'nonce' 	=> array(
					'name' 		=> 'fa_ajax_nonce',
					'action' 	=> 'fa_assign_theme_image' 
				)
			),
			// assign sliders to dynamic areas
			'assign_to_area' => array(
				'action' 	=> 'fa-assign-to-area',
				'callback' 	=> array( $this, 'slider_to_area' ),
				'nonce' 	=> array(
					'name' 		=> 'fa_ajax_nonce',
					'action' 	=> 'fa_assign_slider_to_area'
				)
			)
		);
		
		return $actions;
	}
	
	/**
	 * Get the wp action name for a given action
	 * @param string $key
	 */
	public function get_action( $key ){
		$action = $this->get_action_data( $key );
		return $action['action'];
	}
	
	/**
	 * Get the wp action nonce for a given action
	 * @param string $key
	 */
	public function get_nonce( $key ){
		$action = $this->get_action_data( $key );
		
		$nonce = wp_create_nonce( $action['nonce']['action'] );
		$result = array(
			'name' 	=> $action['nonce']['name'],
			'nonce' => $nonce
		);		
		return $result;
	}
	
	/**
	 * Gets all details of a given action from registered actions
	 * @param string $key
	 */
	private function get_action_data( $key ){
		$actions = $this->actions();
		if( array_key_exists( $key, $actions ) ){
			return $actions[ $key ];
		}else{
			trigger_error( sprintf( __( 'Action %s not found.'), $key ), E_USER_WARNING);
		}
	}	
}