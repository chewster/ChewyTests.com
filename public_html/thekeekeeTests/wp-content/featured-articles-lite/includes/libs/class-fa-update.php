<?php
class FA_Update{
	
	private $auto_display = array();
	private $slides = array();
	
	public function __construct(){		
		$this->update_settings();
		$this->process_sliders();		
		$this->delete_options();		
	}
	
	/**
	 * Transfer plugin settings from old format to new one
	 */
	private function update_settings(){
		$settings 	= get_option( 'feat_art_options', array() );
		
		// update new settings format
		if( $settings ){
			$update = array(
				'complete_uninstall' => $settings['complete_uninstall']
			);
			fa_update_options( 'settings' , $update );
		}	
	}
	
	/**
	 * Update sliders
	 */
	private function process_sliders(){
		// ge tthe sliders
		$sliders = get_posts(array(
			'post_type' 		=> fa_post_type_slider(),
			'post_status' 		=> 'any',
			'posts_per_page' 	=> -1
		));
		if( !$sliders ){
			return;
		}
		
		// load themes manager to allow options from themes to be merged with the plugin options
		fa_get_themes();
		
		// run sliders
		foreach( $sliders as $slider ){
			$options = $this->process_old_slider_options( $slider->ID );
			
			// get old content option
			$o = get_post_meta( $slider->ID, '_fa_lite_content', true );
			
			// set the slider content
			if( isset( $o['displayed_content'] ) ){
				$options['slides']['type'] = ( 1 == $o['displayed_content'] ? 'post' : 'mixed' );
			}
			
			// set the categories
			if( isset( $o['display_from_category'] ) ){
				$tags = array();
				if( $o['display_from_category'] ){
					if( 1 == count( $o['display_from_category'] ) && empty( $o['display_from_category'][0] ) ){
						$tags['category'] = array();	
					}else{					
						$tags['category'] = (array) $o['display_from_category'];
					}	
				}
				
				$options['slides']['tags'] = $tags;				
			}
			
			// set orderby
			if( isset( $o['display_order'] ) ){
				switch( $o['display_order'] ){
					case 1;
					default:
						$options['slides']['orderby'] = 'date';
					break;	
					case 2:
						$options['slides']['orderby'] = 'comments';
					break;
					case 3:
						$options['slides']['orderby'] = 'random';
					break;	
				}
			}
			
			// set posts
			if( isset( $o['display_pages'] ) ){
				$options['slides']['posts'] = (array)$o['display_pages'];
				if( $options['slides']['posts'] ){
					foreach( $options['slides']['posts'] as $pid ){
						delete_post_meta( $pid, '_fa_lite_' . $slider->ID . '_page_ord' );
					}					
					$this->slides = array_merge( $this->slides, $options['slides']['posts'] );
				}
			}
			
			// get old aspect option
			$o = get_post_meta( $slider->ID, '_fa_lite_aspect', true );
			
			// set the content to be displayed
			if( isset( $o['use_custom_text'] ) && $o['use_custom_text'] ){
				$options['content_text']['use'] = 'custom';
			}else if( isset( $o['use_excerpt'] ) && $o['use_excerpt'] ){
				$options['content_text']['use'] = 'excerpt';
			}else{
				$options['content_text']['use'] = 'content';
			}
			
			// set slider fullwidth
			if( isset( $o['slider_width'] ) ){
				if( '100%' == $o['slider_width'] ){
					$options['layout']['full_width'] = true;
				}else{
					$options['layout']['width'] = $o['slider_width'];
				}
			}
			
			// set homepage display - part of automatic display feature
			$o = get_post_meta( $slider->ID, '_fa_lite_home_display', true );
			if( $o ){
				$options['display']['home'] = true;
				$this->auto_display['loop_start'][] = $slider->ID;
			}
			
			// set categories display
			$o = get_post_meta( $slider->ID, '_fa_lite_categ_display', true );
			if( $o ){
				if( in_array( 'all', (array) $o ) ){
					$options['display']['all_categories'] = true;
				}elseif( in_array( 'everywhere',  $o ) ){
					$options['display']['everywhere'] = true;
				}else{				
					$args = array(
						'include' => $o,
						'hide_empty' => false,
					);
					$taxonomies = get_taxonomies( array( 'public' => true ) );
					$terms = get_terms( $taxonomies, $args );
					
					$opt = array();
					if( $terms ){
						foreach( $terms as $term ){
							$opt[ $term->taxonomy ][] = $term->term_id;
						}
						$this->auto_display['loop_start'][] = $slider->ID;
					}				
					$options['display']['tax'] = $opt;
				}	
			}
			
			// set pages display
			$o = get_post_meta( $slider->ID, '_fa_lite_page_display', true );
			if( $o ){
				$opt = array();
				foreach( $o as $post_id ){
					$post_type = get_post_type( $post_id );
					if( $post_type ){
						$opt[ $post_type ][] = $post_id;
					}
				}			
				$options['display']['posts'] = $opt;
				$this->auto_display['loop_start'][] = $slider->ID;
			}		
			
			// get current theme
			$o = get_post_meta( $slider->ID, '_fa_lite_theme', true );
			if( isset( $o['active_theme'] ) ){
				
				$theme = $this->get_new_theme( $o['active_theme'] );
				if( $theme ){
					$options['theme']['active'] = $theme['theme'];
					if( $theme['params'] ){
						foreach( $theme['params'] as $key1 => $values1 ){
							if( is_array( $values1 ) ){
								foreach( $values1 as $key2 => $values2 ){
									if( is_array( $values2 ) ){
										foreach( $values2 as $key3 => $values3 ){
											$options[ $key1 ][ $key2 ][ $key3 ] = $values3;
										}
									}else{
										$options[ $key1 ][ $key2 ] = $values2;
									}
								}
							}else{
								$options[ $key1 ] = $values1;
							}
						}
					}
				}
			}
			
			// set the new option
			$result = fa_update_slider_options( $slider->ID , $options );			
			
			// delete the old options
			delete_post_meta( $slider->ID , '_fa_lite_content');
			delete_post_meta( $slider->ID , '_fa_lite_aspect');
			delete_post_meta( $slider->ID , '_fa_lite_display');
			delete_post_meta( $slider->ID , '_fa_lite_js');
			delete_post_meta( $slider->ID , '_fa_lite_theme');
			delete_post_meta( $slider->ID , '_fa_lite_theme_details');
			delete_post_meta( $slider->ID , '_fa_lite_home_display');
			delete_post_meta( $slider->ID , '_fa_lite_categ_display');
			delete_post_meta( $slider->ID , '_fa_lite_page_display');		
		}

		$this->store_auto_displays();
		$this->process_slides();
	}
	
	/**
	 * Set sliders that should auto display on hooks
	 */
	private function store_auto_displays(){
		if( $this->auto_display ){
			$option = fa_get_options( 'hooks' );
			foreach( $this->auto_display as $hook => $sliders ){
				if( array_key_exists( $hook ,  $option ) ){
					$option[ $hook ]['sliders'] = $sliders;
				}
			}
			fa_update_options( 'hooks' , $option );
		}	
	}
	
	/**
	 * Processes slides
	 */
	private function process_slides(){
		if( !$this->slides ){
			return;
		}
		
		$posts = get_posts(array(
			'include' 			=> $this->slides,
			'posts_per_page' 	=> -1,
			'post_status' 		=> 'any',
			'post_type'			=> 'any'
		));
		
		if( $posts ){
			foreach( $posts as $post ){
				$slide_options = array(
					'link_text' 	=> get_post_meta( $post->ID, '_fa_cust_link', true ),
					'class'			=> get_post_meta( $post->ID, '_fa_cust_class', true ),
					'title'			=> get_post_meta( $post->ID, '_fa_cust_title', true ),
					'content'		=> get_post_meta( $post->ID, '_fa_cust_txt', true ),
					'image'			=> get_post_meta( $post->ID, '_fa_image', true ),
					'temp_image_id' => get_post_meta( $post->ID, '_fa_image_autodetect', true )
				);
				
				fa_update_slide_options( $post->ID ,  $slide_options );
				
				// delete old meta
				delete_post_meta( $post->ID , '_fa_cust_link' );
				delete_post_meta( $post->ID , '_fa_cust_class' );
				delete_post_meta( $post->ID , '_fa_bg_color' );
				delete_post_meta( $post->ID , '_fa_cust_title' );
				delete_post_meta( $post->ID , '_fa_cust_txt' );
				delete_post_meta( $post->ID , '_fa_image' );
				delete_post_meta( $post->ID, '_fa_image_autodetect' );
			}
		}		
	}
	
	private function delete_options(){
		// remove old options
		delete_option( 'feat_art_options' );
		delete_option( 'fa_plugin_details' );
		delete_option( 'fa_lite_categories' );
		delete_option( 'fa_lite_home' );
		delete_option( 'fa_lite_pages' );
	}
	
	private function process_old_slider_options( $slider_id ){
		// map some of the old options into the new options structure
		$map = array(
			'slides' => array(
				'option' => '_fa_lite_content',
				'mapping' => array(
					/* new key => old key */
					'limit' 		=> 'num_articles',
					'author' 		=> 'author'
				)
			),
			'content_image' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show' 			=> 'thumbnail_display',
					'preload' 		=> 'thumbnail_preloader',
					'show_width' 	=> 'thumbnail_width',
					'show_height' 	=> 'thumbnail_height',
					'clickable'		=> 'thumbnail_click',
					'sizing' 		=> 'fa_image_source',
					'width' 		=> 'custom_image_width',
					'height' 		=> 'custom_image_height',
					'wp_size' 		=> 'th_size'
				)
			),
			'content_title' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show' 			=> 'show_title',
					'use_custom' 	=> 'title_custom',
					'clickable' 	=> 'title_click'
				)
			),
			'content_text' => array(
				'option' 	=> '_fa_lite_aspect',
				'mapping' 	=> array(
					'show' 				=> 'show_text',
					'allow_tags' 		=> 'allowed_tags',
					'allow_all_tags' 	=> 'allow_all_tags',
					'strip_shortcodes' 	=> 'strip_shortcodes',
					'max_length' 		=> 'desc_truncate',
					'max_length_noimg' 	=> 'desc_truncate_noimg',
					'end_truncate' 		=> 'end_truncate'
				)
			),
			'content_read_more' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show' 	=> 'show_read_more',
					'text'	=> 'read_more'
				)
			),
			'content_date' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show' => 'show_date'
				)
			),
			'content_author' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show' => 'show_post_author',
					'link' => 'link_post_author'
				)
			),
			'layout' => array(
				'option' => '_fa_lite_aspect',
				'mapping' => array(
					'show_title' 	=> 'section_display',
					'height' 		=> 'slider_height',
					'show_main_nav' => 'bottom_nav',
					'show_side_nav' => 'sideways_nav'
				)
			),
			'js' => array(
				'option' => '_fa_lite_js',
				'mapping' => array(
					'auto_slide' 		=> 'autoSlide',
					'slide_duration' 	=> 'slideDuration',
					'effect_duration' 	=> 'effectDuration',
					'click_stop' 		=> 'stopSlideOnClick',
					'distance_in' 		=> 'fadeDist',
					'position_in' 		=> 'fadePosition',
					'event' 			=> 'navEvent'
				)
			)
		);
		
		// new slider options defaults
		$defaults = fa_get_slider_default_options();
		
		foreach ( $map as $option_key => $details ){
			$old_option = get_post_meta( $slider_id, $details['option'], true );
			foreach ( $details['mapping'] as $key => $old_key ){
				if( isset( $old_option[ $old_key ] ) ){
					$defaults[ $option_key ][ $key ] = $old_option[ $old_key ];
				}
			}
		}
		
		return $defaults;
	}
	
	/**
	 * Mapping of old slider themes to set up correctly the new slider themes
	 * @param string $old_theme
	 */
	private function get_new_theme( $old_theme ){
		
		$themes = array(
			'classic' => array(
				'theme' => 'simple',
				'params' => array()
			),
			/*
			'smoke' => array(
				'theme' => 'cristal',
				'params' => array(
					'themes_params' => array(
						'cristal' => array(
							'navigation' => 'dots'
						)
					),
					'layout' => array(
						'class' => 'content-left background',
						'show_side_nav' => false,
						'show_main_nav' => true
					)
				)
			),
			'title_navigation' => array(
				'theme' =>  'list',
				'params' => array()
			)
			*/
		);
		
		if( array_key_exists( $old_theme ,  $themes ) ){
			return $themes[ $old_theme ];
		}
		return false;
	}
	
}