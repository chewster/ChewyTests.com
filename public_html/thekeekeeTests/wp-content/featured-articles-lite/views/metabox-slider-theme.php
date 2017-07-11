<?php 
	// the current active theme
	$active = $options['theme']['active'];
?>
<div class="fa-horizontal-tabs" id="fa-themes-tabs">
	<ul class="fa-tabs-nav">
		<li><a href="#fa-registered-themes"><?php _e('Theme', 'featured-articles-lite');?></a></li>
		<li><a href="#fa-theme-layout-settings"><?php _e('Layout', 'featured-articles-lite');?></a></li>
		<li><a href="#fa-theme-slider-settings"><?php _e('Animation', 'featured-articles-lite');?></a></li>
		<li><a href="#fa-theme-slider-publish"><?php _e('Publish', 'featured-articles-lite');?></a></li>
	</ul>
	
	<!-- Themes tab -->
	<div class="fa-themes" id="fa-registered-themes">
		<?php 
			if( !fa_is_extra_dir_set() ):
		?>
		<div class="alert">
			<span class="dashicons dashicons-flag"></span> 
			<?php _e( 'Your slider themes are inside plugin folder.', 'featured-articles-lite');?><br />
			<?php printf( __( 'If you modify existing themes, create additional themes or create additional color schemes to existing themes, please make sure that you first %smove slider themes outside plugin folder%s.', 'featured-articles-lite' ), '<a href="http://www.codeflavors.com/documentation/intermediate-tutorials/moving-slider-themes-folder/?utm_source=plugin&utm_medium=doc_link&utm_campaign=fa_lite" target="_blank">' ,'</a>');?>		
		</div>
		<?php endif;?>	
		<?php foreach( $themes as $theme => $theme_details ):?>	
		<div class="fa-theme<?php if( $active == $theme ):?> active<?php endif;?>" id="fa-theme-<?php echo esc_attr( $theme );?>">
			<?php 
				$img = isset( $theme_details['preview'] ) && !empty( $theme_details['preview'] ) ? $theme_details['preview'] : false;
			?>
			<div class="fa-screenshot<?php if( !$img ):?> blank<?php endif;?>">
				<?php if( $img ):?><img src="<?php echo $theme_details['preview'];?>" /><?php endif;?>
			</div><!-- .fa-screenshot -->
			<h3 class="theme-name">
				<?php echo $theme_details['theme_config']['name'];?>
			</h3><!-- .theme-name -->
			<div class="theme-actions">
				<?php 
					$args = array(
						'name' 	=> 'theme_color',
						'id'	=> 'theme-color',
						'label' => __('Color', 'featured-articles-lite'),
						'selected' => $options['theme']['color']
					);
					fa_theme_colors_dropdown( $theme_details, $args );
				?>							
				<?php 
					$preview_args = array(
						'post_id' 	=> $post->ID,
						'theme' 	=> $theme 
					);
				?>
				<a class="button-primary fa-customize" target="fa-slider-preview" href="<?php fa_slider_preview_homepage( $preview_args );?>" <?php fa_hide( $active != $theme );?>><?php _e('Preview', 'featured-articles-lite');?></a>
				<a class="button-secondary fa-select" data-theme="<?php echo esc_attr( $theme );?>" <?php fa_hide( $active == $theme );?>><?php _e('Select', 'featured-articles-lite');?></a>
			</div><!-- .theme-actions -->
		</div><!-- .fa-theme -->		
		<?php endforeach;// end themes loop?>	
		<input type="hidden" name="theme[active]" id="fa_active_theme" value="<?php echo esc_attr( $active );?>" />
		<br class="clear" />
	</div><!-- #fa-registered-themes -->
	
	<!-- Theme layout -->
	<div id="fa-theme-layout-settings">
		<h2><?php _e('Slider layout settings', 'featured-articles-lite');?></h2>
		<table class="form-table">
			<tbody>
				
				<tr class="">
					<th><label for=""><?php _e('Layout variations', 'featured-articles-lite');?>:</label></th>
					<td>
					<?php foreach( $themes as $theme => $theme_details ):?>
						<?php 
							$class = $active == $theme ? '' : 'hide-if-js';
						
							// display the layout variations for the theme
							$args = array(
								'name' 		=> 'layout[class][' . $theme . ']',
								'id' 		=> 'layout-classes-' . $theme,
								'selected' 	=> $options['layout']['class'],
								'select_opt' => __('Default', 'featured-articles-lite'),
								'class'		=> $class . ' layout-color-variations'
							);
							$select_box = fa_theme_layouts_dropdown( $theme, $args );
							if( !$select_box ){
							?>
							<span class="description layout-color-variations <?php echo $class;?>" id="layout-classes-<?php echo $theme;?>"><?php _e('Not available', 'featured-articles-lite');?></span>
							<?php 	
							}
						?>
					<?php endforeach;?>
					</td>					
				</tr>				
				<!-- Slide title -->
				<tr class="optional layout-show-slider-title"<?php fa_optional_field_data( 'layout-show-title' );?>>
					<th><label for="show_title"><?php _e('Show slider title', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-show-title' ) );?>>
							<input type="checkbox" value="1" id="show_title" name="layout[show_title]"<?php fa_checked( $options['layout']['show_title'] );?> />
							<span class="description"><?php _e('when checked, the slideshow title will be displayed above it', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-show-title' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<!-- Main navigation -->
				<tr class="optional layout-slider-main-nav"<?php fa_optional_field_data( 'layout-show-main-nav' );?>>
					<th><label for="show_main_nav"><?php _e('Show main navigation', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-show-main-nav' ) );?>>
							<input type="checkbox" name="layout[show_main_nav]" id="show_main_nav"<?php fa_checked( $options['layout']['show_main_nav'] );?> />	
							<span class="description"><?php _e('display the one by one slide navigation (usually located at the bottom of the slider)', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-show-main-nav' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<!-- Secondary navigation -->
				<tr class="optional layout-secondary-nav"<?php fa_optional_field_data( 'layout-show-side-nav' );?>>
					<th><label for="show_side_nav"><?php _e('Show secondary navigation', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-show-side-nav' ) );?>>
							<input type="checkbox" name="layout[show_side_nav]" id="show_side_nav"<?php fa_checked( $options['layout']['show_side_nav'] );?> />	
							<span class="description"><?php _e('displays the previous/next slide navigation', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-show-side-nav' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<!-- Slider width -->
				<tr>
					<th><label for="slider_width"><?php _e('Slider width', 'featured-articles-lite');?>:</label></th>
					<td>
						<input type="text" name="layout[width]" id="slider_width" value="<?php echo esc_attr( $options['layout']['width'] );?>" size="2" />	
						<span class="description"><?php _e('the slider width in pixels', 'featured-articles-lite');?></span>
					</td>
				</tr>
				<!-- Slider full width -->
				<tr>
					<th><label for="slider_fullwidth"><?php _e('Allow fullwidth', 'featured-articles-lite');?>:</label></th>
					<td>
						<input type="checkbox" name="layout[full_width]" id="slider_fullwidth" value="1" <?php fa_checked( (bool) $options['layout']['full_width'] );?> />	
						<span class="description"><?php _e('allow slider to display in full width (will keep width/height proportions)', 'featured-articles-lite');?></span>
					</td>
				</tr>
				<!-- Slider height -->
				<tr class="optional layout-slider-height"<?php fa_optional_field_data( 'layout-slider-height' );?>>
					<th><label for="slider_height"><?php _e('Slider height', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-slider-height' ) );?>>	
							<input type="text" name="layout[height]" id="slider_height" value="<?php echo esc_attr( $options['layout']['height'] );?>" size="2" />	
							<span class="description"><?php _e('the slider height in pixels', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-slider-height' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				
				<tr class="optional layout-height-resize"<?php fa_optional_field_data( 'layout-height-resize' );?>>
					<th><label for="layout_height_resize"><?php _e('Allow height resize', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-height-resize' ) );?>>
							<input type="checkbox" name="layout[height_resize]" id="layout_height_resize" value="1" <?php fa_checked( (bool) $options['layout']['height_resize'] );?> />
							<span class="description"><?php _e('when checked, full width slider will resize height proportionally', 'featured-articles-lite');?></span>						
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-height-resize' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				
				<!-- Font size -->
				<tr class="optional layout-font-size"<?php fa_optional_field_data( 'layout-font-size' );?>>
					<th><label for="font_size"><?php _e('Font size', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'layout-font-size' ) );?>>	
							<input type="text" name="layout[font_size]" id="font_size" value="<?php echo esc_attr( $options['layout']['font_size'] );?>" size="2" />	
							<span class="description"><?php _e('percentual slider font size', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'layout-font-size' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				
				<!-- Margin top -->
				<tr>
					<th><label for="margin_top"><?php _e('Distance top', 'featured-articles-lite');?>:</label></th>
					<td>
						<input type="text" name="layout[margin_top]" id="margin_top" value="<?php echo esc_attr( $options['layout']['margin_top'] );?>" size="2" />	
						<span class="description"><?php _e('slider top margin in pixels', 'featured-articles-lite');?></span>
					</td>
				</tr>
				<!-- Margin bottom -->
				<tr>
					<th><label for="margin_bottom"><?php _e('Distance bottom', 'featured-articles-lite');?>:</label></th>
					<td>
						<input type="text" name="layout[margin_bottom]" id="margin_bottom" value="<?php echo esc_attr( $options['layout']['margin_bottom'] );?>" size="2" />	
						<span class="description"><?php _e('slider bottom margin in pixels', 'featured-articles-lite');?></span>
					</td>
				</tr>
				<!-- Align horizontal center -->
				<tr>
					<th><label for="layout_center"><?php _e('Center horizontally', 'featured-articles-lite');?>:</label></th>
					<td>
						<input type="checkbox" name="layout[center]" id="layout_center" value="1" <?php fa_checked( (bool) $options['layout']['center'] );?> />	
						<span class="description"><?php _e('center slider horizontally', 'featured-articles-lite');?></span>
					</td>
				</tr>								
			</tbody>
		</table><!-- .form-table -->
		<?php 
			// theme specific options - can be added from theme functions.php file
			foreach( $themes as $theme => $theme_details ):
		?>
		<div id="theme-layout-settings-<?php echo esc_attr( $theme );?>" class="theme-layout-settings theme-settings <?php echo esc_attr( $theme );?>" <?php fa_hide( $theme != $active );?>>
			<?php do_action( 'fa_theme_layout_settings-' . $theme, $post );?>
		</div>
		<?php endforeach;?>
		<?php 
			/**
			 * Backwards compatibility function that allows older themes
			 * to display the optional fields they implement.
			 */
			_deprecated_show_themes_layout_fields();
		?>
	</div><!-- #fa-theme-layout-settings -->
	
	<!-- Theme settings tab -->
	<div class="fa-theme-settings" id="fa-theme-slider-settings">		
		<h2><?php _e('Animation settings', 'featured-articles-lite');?></h2>
		<table class="form-table">
			<tbody>
				<!-- Autoslide -->
				<tr class="optional js-autoslide"<?php fa_optional_field_data( 'js-auto-slide' );?>>					
					<th><label for="auto_slide"><?php _e('Slide automatically', 'featured-articles-lite');?>:</label></th>		
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-auto-slide' ) );?>>	
							<input class="toggler" data-toggle="autoslide-settings" autocomplete="off" type="checkbox" name="js[auto_slide]" value="1"<?php fa_checked( $options['js']['auto_slide'] );?> />
							<span class="description"><?php _e('when checked, slider will autoslide once page has loaded', 'featured-articles-lite');?></span>
						</span>	
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-auto-slide' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>	
				<!-- Stop autoslide on click -->
				<tr class="autoslide-settings optional js-click-stop"<?php fa_optional_field_data('js-click-stop');?> <?php fa_hide( !$options['js']['auto_slide'] );?>>					
					<th><label for="click_stop"><?php _e('Stop sliding on click', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-click-stop' ) );?>>		
							<input type="checkbox" name="js[click_stop]" value="1"<?php fa_checked( $options['js']['click_stop'] );?> />
							<span class="description"><?php _e('when checked, autoslide will stop when slider navigation is clicked', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-click-stop' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<!-- Slide duration -->
				<tr class="autoslide-settings optional js-duration"<?php fa_optional_field_data('js-slide-duration');?> <?php fa_hide( !$options['js']['auto_slide'] );?>>					
					<th><label for="slide_duration"><?php _e('Slide duration', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-slide-duration' ) );?>>
							<input type="text" name="js[slide_duration]" value="<?php echo $options['js']['slide_duration'];?>" size="1" />
							<span class="description"><?php _e('how many seconds should a slide be displayed when automatic sliding is on', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-slide-duration' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>	
				
				
				<!-- Effect -->
				<tr class="optional js-effect"<?php fa_optional_field_data('js-effect');?>>					
					<th><label for="effect"><?php _e('Effect', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-effect' ) );?>>
							<?php 
								$args = array(
									'name' => 'js[effect]',
									'id'	=> 'effect',
									'selected' => $options['js']['effect']
								);
								fa_slide_effect_dropdown( $args );
							?>
							<span class="description"><?php _e('full image sliders can apply effects on images', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-effect' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>				
				<!-- Effect duration -->
				<tr class="optional js-effect-duration"<?php fa_optional_field_data('js-effect-duration');?>>					
					<th><label for="effect_duration"><?php _e('Effect duration', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-effect-duration' ) );?>>
							<input type="text" name="js[effect_duration]" value="<?php echo $options['js']['effect_duration']?>" size="1" />
							<span class="description"><?php _e('how many seconds the transition effect between slides should take', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-effect-duration' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<!-- Cycle slider -->
				<tr class="optional js-cycle"<?php fa_optional_field_data('js-cycle');?>>					
					<th><label for="cycle"><?php _e('Continuous slider', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-cycle' ) );?>>	
							<input type="checkbox" name="js[cycle]" value="1"<?php fa_checked( $options['js']['cycle'] );?> />
							<span class="description"><?php _e('when checked the slider will not stop when reaching the first or last slide', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-cycle' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<!-- Position in -->
				<tr class="optional js-position-in"<?php fa_optional_field_data('js-position-in');?>>					
					<th><label for="position_in"><?php _e('Position slides enter from', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-position-in' ) );?>>
							<?php 
								$args = array(
									'name' 	=> 'js[position_in]',
									'id'	=> 'position_in',
									'selected' => $options['js']['position_in']
								);
								fa_sliding_positions_dropdown( $args );
							?>
							<span class="description"><?php _e('the position from where the slides will enter', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-position-in' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<!-- Distance in -->
				<tr class="optional js-distance-in"<?php fa_optional_field_data('js-distance-in');?>>					
					<th><label for="distance_in"><?php _e('Distance slides enter from', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-distance-in' ) );?>>
							<input type="text" name="js[distance_in]" value="<?php echo $options['js']['distance_in'];?>" size="2" />
							<span class="description"><?php _e('distance in pixels that slides should slide in', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-distance-in' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>	
				<!-- Position out -->
				<tr class="optional js-position-out"<?php fa_optional_field_data('js-position-out');?>>					
					<th><label for="position_out"><?php _e('Position slides exit to', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-position-out' ) );?>>
							<?php 
								$args = array(
									'name' 	=> 'js[position_out]',
									'id'	=> 'position_out',
									'selected' => $options['js']['position_out']
								);
								fa_sliding_positions_dropdown( $args );
							?>
							<span class="description"><?php _e('the position to where slides should exit', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-position-out' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<!-- Distance out -->
				<tr class="optional js-distance-out"<?php fa_optional_field_data('js-distance-out');?>>					
					<th><label for="distance_out"><?php _e('Distance slides exit to', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-distance-out' ) );?>>
							<input type="text" name="js[distance_out]" value="<?php echo $options['js']['distance_out'];?>" size="2" />
							<span class="description"><?php _e('distance in pixels that slides should slide out', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-distance-out' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>	
				<!-- Click event -->
				<tr class="optional js-click-event"<?php fa_optional_field_data('js-click-event');?>>					
					<th><label for="event"><?php _e('Slide change event', 'featured-articles-lite');?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'js-click-event' ) );?>>	
							<?php 
								$args = array(
									'name' 	=> 'js[event]',
									'id'	=> 'event',
									'selected' => $options['js']['event']
								);
								fa_sliding_event_dropdown( $args );
							?>
							<span class="description"><?php _e('the event that should trigger slides to change when using the slider navigation', 'featured-articles-lite');?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'js-click-event' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>	
			</tbody>
		</table><!-- .form-table -->
		<?php 
			// theme specific options - can be added from theme functions.php file
			foreach( $themes as $theme => $theme_details ):
		?>
		<div id="theme-js-settings-<?php echo esc_attr( $theme );?>" class="theme-js-settings theme-settings <?php echo esc_attr( $theme );?>" <?php fa_hide( $theme != $active );?>>
			<?php do_action( 'fa_theme_js_settings-' . $theme, $post );?>					
		</div>
		<?php endforeach;?>	
		<?php 
			/**
			 * Backwards compatibility function that allows older themes
			 * to display the optional fields they implement.
			 */
			_deprecated_show_themes_animation_fields();
		?>					
	</div><!-- #fa-theme-slider-settings -->
	<div id="fa-theme-slider-publish">
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for=""><?php _e('Display in dynamic areas', 'featured-articles-lite');?>:</label></th>
					<td>
						<?php foreach( $areas as $area_id => $area ):?>
						<input type="checkbox" name="slider_area[]" value="<?php echo $area_id;?>" id="area_<?php echo $area_id;?>" <?php fa_checked( in_array( $post->ID, $area['sliders'] ) );?> />
						<label for="area_<?php echo $area_id;?>"><?php echo $area['name'];?></label>
						<?php if( !empty( $area['description'] ) ):?> <span class="description">( <?php echo $area['description'];?> )</span> <?php endif;?><br />
						<?php endforeach;?>
					</td>
				</tr>
				<?php $publish_everywhere = $options['display']['everywhere'];?>
				<tr>
					<th><label for="display-everywhere"><?php _e('Display everywhere', 'featured-articles-lite');?>:</label></th>
					<td>
						<input autocomplete="off" type="checkbox" class="toggler" data-toggle="publish-settings" data-action="hide" name="display[everywhere]" value="1" id="display-everywhere" <?php fa_checked( $options['display']['everywhere'] );?> />
						<span class="description">
							<?php _e('Slider will be displayed on all pages of your WordPress website.', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>				
				<tr class="publish-settings" <?php fa_hide( $publish_everywhere );?>>
					<th><label for="display-home"><?php _e('Display on homepage', 'featured-articles-lite');?>:</label></th>
					<td>
						<input autocomplete="off" type="checkbox" name="display[home]" value="1" id="display-home" <?php fa_checked( $options['display']['home'] );?> />
						<span class="description">
							<?php _e('Slider will be displayed on homepage if the area code is inside the WP theme files that is part of the homepage template.', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				<tr class="publish-settings" <?php fa_hide( $publish_everywhere );?>>
					<th><label for="display-all-pages"><?php _e('Display on all pages', 'featured-articles-lite');?>:</label></th>
					<td>
						<input autocomplete="off" type="checkbox" name="display[all_pages]" class="toggler" data-toggle="publish-pages" data-action="hide" value="1" id="display-all-pages" <?php fa_checked( $options['display']['all_pages'] );?> />
						<span class="description">
							<?php _e('Slider will be displayed on all WordPress pages.', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				<tr class="publish-settings" <?php fa_hide( $publish_everywhere );?>>
					<th><label for="display-all-categories"><?php _e('Display on all categories/taxonomies', 'featured-articles-lite');?>:</label></th>
					<td>
						<input autocomplete="off" type="checkbox" name="display[all_categories]" class="toggler" data-toggle="publish-categories" data-action="hide" value="1" id="display-all-categories" <?php fa_checked( $options['display']['all_categories'] );?> />
						<span class="description">
							<?php _e('Slider will be displayed on all WordPress categories and taxonomies.', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				<?php $publish_pages = $publish_everywhere ? $publish_everywhere : $options['display']['all_pages'] ;?>
				<tr class="publish-settings publish-pages" <?php fa_hide( $publish_pages );?>>
					<th>
						<label for="display-posts"><?php _e('Display on single posts/pages', 'featured-articles-lite');?>:</label><br />
						(<a id="fa-display-posts" class="fapro-modal-trigger" data-target="fapro-modal" href="<?php fa_iframe_admin_page_url('fa-mixed-content-modal', array( 'show_all' => 'true' ) );?>"><?php _e('click to select', 'featured-articles-lite');?></a>)
					</th>
					<td>
						<div id="fa-selected-display-posts">
							<?php 
								$post_types = get_post_types( array( 'public' => true ), 'names' );
								$selected = false;
								$first = true;
								foreach( $post_types as $post_type => $name ):									
									$css_hide = isset( $options['display']['posts'][ $post_type ] ) ? 'display:block;' : 'display:none;';
									$pt_object = get_post_type_object($post_type);
									
							?>
							<div id="fa-display-posts-<?php echo $post_type;?>" class="fa-selected-posts<?php if( $first ):?> first<?php endif;?>" style="<?php echo $css_hide;?>">
								<strong><?php echo $pt_object->labels->name;?></strong>:
								<?php 
									if( isset( $options['display']['posts'][ $post_type ] ) ):
										foreach( $options['display']['posts'][ $post_type ] as $post_id ):
											$p = get_post( $post_id );
											if( !$p || is_wp_error( $p )){
												continue;
											}
											$selected = true;	
								?>							
								<span class="fa-post" data-post_id="<?php echo $post_id;?>" data-post_type="<?php echo $post_type;?>" id="fa_display_post_<?php echo $p->ID;?>">
									<a href="#" class="fa_remove_display_post"><i class="dashicons dashicons-dismiss"></i></a> <?php echo $p->post_title;?>
									<input type="hidden" name="display[posts][<?php echo $post_type;?>][]" value="<?php echo $post_id?>" />
								</span>
								<?php 
											$first = false;
										endforeach;
									endif;
								?>
							</div>
							<?php									
								endforeach;
							?>	
							<?php $hide_desc = $selected ? 'display:none;' : 'display:block;';?>
							<span style="<?php echo $hide_desc;?>" class="description" id="fa-all-display-posts"><?php _e('Slides will not be displayed on any posts or pages.', 'featured-articles-lite');?></span>
						</div>
					</td>
				</tr>
				<?php $publish_categories = $publish_everywhere ? $publish_everywhere : $options['display']['all_categories'] ;?>
				<tr class="publish-settings publish-categories"<?php fa_hide( $publish_categories );?>>
					<th>
						<label for="display-tax"><?php _e('Display on categories', 'featured-articles-lite');?>:</label><br />
						(<a id="fa-display-posts-categories" class="fapro-modal-trigger" data-target="fapro-modal" href="<?php fa_iframe_admin_page_url( 'fa-tax-modal', array('show_all' => 'true') );?>"><?php _e('click to select', 'featured-articles-lite');?></a>)
					</th>
					<td>
						<div id="fa-selected-display-categories">
							<?php 
								$tax = fa_get_registered_taxonomies();
								$selected = false;
								$first = true;
								foreach( $tax as $post_type => $taxonomies ):
									foreach( $taxonomies as $taxonomy ):
										$tax = $taxonomy['taxonomy'];
										$css_hide = isset( $options['display']['tax'][ $tax ] ) ? 'display:block;' : 'display:none;';
							?>
							<div id="fa-display-tax-<?php echo $tax;?>" class="fa-selected-terms<?php if( $first ):?> first<?php endif;?>" style="<?php echo $css_hide;?>">
								<strong><?php echo $taxonomy['name'];?></strong>:
								<?php 
									if( isset( $options['display']['tax'][ $tax ] ) ):
										foreach( $options['display']['tax'][ $tax ] as $term_id ):
											$term_object = get_term( $term_id, $tax );
											if( !$term_object || is_wp_error( $term_object )){
												continue;
											}
											$selected = true;	
								?>							
								<span class="fa-term" data-term_id="<?php echo $term_id;?>" data-taxonomy="<?php echo $tax;?>" id="fa_display_term_<?php echo $term_id?>">
									<a href="#" class="fa_remove_display_tag"><i class="dashicons dashicons-dismiss"></i></a> <?php echo $term_object->name;?>
									<input type="hidden" name="display[tax][<?php echo $tax;?>][]" value="<?php echo $term_id?>" />
								</span>
								<?php 
											$first = false;
										endforeach;
									endif;
								?>
							</div>
							<?php	
									endforeach;;
								endforeach;
							?>	
							<?php $hide_desc = $selected ? 'display:none;' : 'display:block;';?>
							<span style="<?php echo $hide_desc;?>" class="description" id="fa-all-display-categories"><?php _e('Slides will not be displayed on any category archive page.', 'featured-articles-lite');?></span>
						</div>
					</td>
				</tr>				
			</tbody>
		</table>
	</div><!-- #fa-theme-slider-publish -->	
</div>	
