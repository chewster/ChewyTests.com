<?php 
	// the current active theme
	$active = $options['theme']['active'];
?>
<?php wp_nonce_field('fa-slider-options-save', 'fa-slider-settings-nonce');?>
<div id="fa-select-content-type">
	<!-- Slider from posts -->
	<input autocomplete="off" data-panel="fapro-content-post" type="radio" name="slides[type]" value="post" id="content-post"<?php fa_checked( 'post' == $options['slides']['type'] );?> />
	<label for="content-post"><?php _e('Create slider from posts', 'featured-articles-lite');?></label><br />
	<!-- Slider from mixed content -->
	<input autocomplete="off" data-panel="fapro-content-mixed" type="radio" name="slides[type]" value="mixed" id="content-mixed"<?php fa_checked( 'mixed' == $options['slides']['type'] );?> />
	<label for="content-mixed"><?php _e('Mixed content', 'featured-articles-lite');?></label>
	<span class="description">(<?php _e('create slider that can incorporate posts and pages', 'featured-articles-lite');?>)</span><br />
	<!-- Slider from images -->
	<input disabled="disabled" type="radio" name="" value="" id="content-image" />
	<label for="content-image"><?php _e('Images', 'featured-articles-lite');?> <?php fa_option_not_available(' ');?> ( <?php _e( 'create sliders from WP Gallery images', 'featured-articles-lite');?> )</label>
</div>

<div class="fa-horizontal-tabs" id="fa-slider-content-tabs">
	<ul class="fa-tabs-nav">
		<li><a href="#fa-slider-content"><?php _e('Content', 'featured-articles-lite');?></a></li>
		<li><a href="#fa-slider-content-settings"><?php _e('Text options', 'featured-articles-lite');?></a></li>
		<li><a href="#fa-slider-content-image-settings"><?php _e('Media options', 'featured-articles-lite');?></a></li>
	</ul>
	<!-- Slide content -->
	<div id="fa-slider-content">
		<!-- Settings for sliders made from posts -->
		<div id="fapro-content-post"<?php if( 'post' != $options['slides']['type'] ):?> style="display:none;"<?php endif;?>>
			<table class="form-table">
				<tbody>
					<tr>
						<th valign="top" scope="row"><label for="content-post-type"><?php _e('Post type', 'featured-articles-lite')?>:</label></th>
						<td>
							<?php fa_option_not_available();?>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="content-limit"><?php _e('Number of posts', 'featured-articles-lite')?>:</label></th>
						<td>
							<input type="text" name="slides[limit]" value="<?php echo $options['slides']['limit'];?>" id="content-limit" size="1" />
							<span class="description"><?php _e('maximum number of posts that will display in slider (numeric)', 'featured-articles-lite')?></span>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">
							<label for="content-categories"><?php _e('From categories', 'featured-articles-lite')?>:</label><br />
							(<a id="fa-content-posts-categories" class="fapro-modal-trigger" data-target="fapro-modal" href="<?php fa_iframe_admin_page_url('fa-tax-modal');?>"><?php _e('click to select', 'featured-articles-lite');?></a>)
						</th>
						<td>
							<div id="fa-selected-categories">
								<?php 
									$tax = fa_get_allowed_taxonomies();
									$selected = false;
									$first = true;
									foreach( $tax as $post_type => $taxonomies ):
										foreach( $taxonomies as $taxonomy ):
											$tax = $taxonomy['taxonomy'];
											$css_hide = isset( $options['slides']['tags'][ $tax ] ) ? 'display:block;' : 'display:none;';
								?>
								<div id="fa-tax-<?php echo $tax;?>" class="fa-selected-terms<?php if( $first ):?> first<?php endif;?>" style="<?php echo $css_hide;?>">
									<strong><?php echo $taxonomy['name'];?></strong>:
									<?php 
										if( isset( $options['slides']['tags'][ $tax ] ) ):
											foreach( $options['slides']['tags'][ $tax ] as $term_id ):
												$term_object = get_term($term_id, $tax);
												if( !$term_object || is_wp_error( $term_object )){
													continue;
												}
												$selected = true;	
									?>							
									<span class="fa-term" data-term_id="<?php echo $term_id;?>" data-taxonomy="<?php echo $tax;?>" id="fa_term_<?php echo $term_id?>">
										<a href="#" class="fa_remove_tag"><i class="dashicons dashicons-dismiss"></i></a> <?php echo $term_object->name;?>
										<input type="hidden" name="slides[tags][<?php echo $tax;?>][]" value="<?php echo $term_id?>" />
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
								<span style="<?php echo $hide_desc;?>" class="description" id="fa-all-categories"><?php _e('Slides will display posts from all categories.', 'featured-articles-lite');?></span>
							</div>
						</td>
					</tr>
					
					<?php 
						$args = array(
							'show_option_all' => __('Any author', 'featured-articles-lite'),
							'hide_if_only_one_author' => true,
							'multi'	=> true,
							'echo' => false,
							'selected' => $options['slides']['author'],
							'name' => 'slides[author]',
							'id' => 'fa-users-dropdown'
						);
						$users = wp_dropdown_users( $args );
						if( $users ):
					?>						
					<tr>
						<th><label for="content-author"><?php _e('By author', 'featured-articles-lite')?>:</label></th>
						<td>
							<?php echo $users;?>
						</td>
					</tr>
					<?php 
						endif;// if( $users ):
					?>
					<tr>
						<th scope="row" valign="top"><label for="content-orderby"><?php _e('Ordered by', 'featured-articles-lite')?>:</label></th>
						<td>
							<input id="content-orderby-date" type="radio" value="date" name="slides[orderby]"<?php fa_checked( 'date' == $options['slides']['orderby'] );?> /><label for="content-orderby-date"><?php _e('post publish date', 'featured-articles-lite')?></label><br />
							<input id="content-orderby-comments" type="radio" value="comments" name="slides[orderby]"<?php fa_checked( 'comments' == $options['slides']['orderby'] );?> /><label for="content-orderby-comments"><?php _e('number of comments', 'featured-articles-lite')?></label><br>
							<input id="content-orderby-rand" type="radio" value="random" name="slides[orderby]"<?php fa_checked( 'random' == $options['slides']['orderby'] );?> /><label for="content-orderby-rand"><?php _e('random order', 'featured-articles-lite');?></label>
						</td>
					</tr>		
				</tbody>
			</table>
		</div><!-- #fapro-content-posts -->
		
		<!-- Settings for sliders made from mixed content -->
		<div id="fapro-content-mixed"<?php if( 'mixed' != $options['slides']['type'] ):?> style="display:none;"<?php endif;?>>
			<a id="fa-select-mixed-content" class="button button-primary fapro-modal-trigger" data-target="fapro-modal" href="<?php fa_iframe_admin_page_url('fa-mixed-content-modal');?>"><?php _e('Select slides', 'featured-articles-lite');?></a>
			<a id="" class="button button-secondary" href="#"><?php _e('Create custom slide', 'featured-articles-lite');?> <?php fa_option_not_available(' ');?></a>
			
			<div id="fa-selected-posts">		
				<?php 
					$posts = fa_get_slider_posts( $post->ID, 'any' );
					if( $posts ){
						foreach( $posts as $slide_post ){
							fa_slide_panel( $slide_post, $post->ID );	
						}				
					}else{
					?>
				<p class="description">
					<?php _e('No posts assigned. To select posts, pages or custom created slides, please click button above.', 'featured-articles-lite');?>
				</p>
					<?php	
					}			
				?>				
			</div><!-- #fa-selected-posts -->
			<br class="clear" />		
		</div><!-- #fapro-content-mixed -->
	</div><!-- #fa-slider-content -->
	<!-- Slider content options -->
	<div id="fa-slider-content-settings">
		<?php 
			// title should be displayed?
			$show_title = (bool) $options['content_title']['show'];			
		?>
		<h2><?php _e('Slide title options', 'featured-articles-lite');?></h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="content-title-show"><?php _e( 'Show title', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input class="toggler" data-toggle="title-options" autocomplete="off" type="checkbox" name="content_title[show]" id="content-title-show" value="1"<?php fa_checked( $options['content_title']['show'] );?> />
						<span class="description"><?php _e( 'show titles in slides', 'featured-articles-lite' );?></span>
					</td>
				</tr>
				<!-- Title color -->
				<tr class="title-options optional content-title-color"<?php fa_optional_field_data('content-title-color')?> <?php fa_hide( !$show_title );?>>
					<th><label for="content-title-color"><?php _e('Title color', 'featured-articles-lite');?>:</label></th>
					<td>
						<?php fa_option_not_available();?>	
					</td>
				</tr>
				<!-- Optional title click field -->
				<tr class="title-options optional content-title-click"<?php fa_optional_field_data('content-title-clickable')?> <?php fa_hide( !$show_title );?>>
					<th><label for="content-title-clickable"><?php _e( 'Title is clickable', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-title-clickable' ) );?>>
							<input type="checkbox" name="content_title[clickable]" id="content-title-clickable" value="1"<?php fa_checked( $options['content_title']['clickable'] );?> />
							<span class="description"><?php _e( 'when checked, title will have an anchor set on it poiting to the designated slide URL', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-title-clickable' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>				
				<!-- /Optional title click field -->
				<tr class="title-options" <?php fa_hide( !$show_title );?>>
					<th><label for="content-title-use_custom"><?php _e( 'Display custom title', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input type="checkbox" name="content_title[use_custom]" id="content-title-use_custom" value="1"<?php fa_checked( $options['content_title']['use_custom'] );?> />
						<span class="description"><?php _e( 'use the custom title instead of the regular post title', 'featured-articles-lite' );?></span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<h2><?php _e('Slide content options', 'featured-articles-lite');?></h2>
		<?php 
			// title should be displayed?
			$show_content = !fa_theme_field_enabled( $active, 'content-text-show' ) ? true : (bool) $options['content_text']['show'];			
		?>
		<table class="form-table">		
			<tbody>
				<tr class="optional content-show-text"<?php fa_optional_field_data('content-text-show')?>">
					<th><label for="content-text-show"><?php _e( 'Show slide content', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-text-show' ) );?>>
							<input class="toggler" data-toggle="content-options" autocomplete="off" type="checkbox" name="content_text[show]" id="content-text-show" value="1"<?php fa_checked( $options['content_text']['show'] );?> />
							<span class="description"><?php _e( 'show slide content', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-text-show' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<!-- Title color -->
				<tr class="title-options optional content-text-color"<?php fa_optional_field_data('content-text-color')?> <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-color"><?php _e('Text color', 'featured-articles-lite');?>:</label></th>
					<td>
						<?php fa_option_not_available();?>		
					</td>
				</tr>
				
				
				<tr class="content-options" <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-use"><?php _e( 'Get content from', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<?php 
							$args = array(
								'options' => array(
									'content' => __('Post content', 'featured-articles-lite'),
									'excerpt' => __('Post excerpt', 'featured-articles-lite'),
									'custom'  => __('Custom text set for slide', 'featured-articles-lite')
								),
								'select_opt' 	=> false,
								'selected' 		=> $options['content_text']['use'],
								'name' 			=> 'content_text[use]',
								'id'			=> 'content-text-use'
							);
							fa_dropdown( $args );
						?>
						<p class="description">
							<?php _e( 'Where should the content for the slide be primarely retrieved from', 'featured-articles-lite' );?><br />
							<?php _e( 'If set to excerpt or custom text, if they are empty, content will be retrieved from post content.', 'featured-articles-lite' );?>
						</p>
					</td>
				</tr>
				<tr class="content-options" <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-allow_tags"><?php _e( 'Allowed HTML tags', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input type="text" name="content_text[allow_tags]" id="content-text-allow_tags" value="<?php echo $options['content_text']['allow_tags'];?>" />
						<input type="checkbox" name="content_text[allow_all_tags]" id="content-text-allow_all_tags" value="1"<?php fa_checked( $options['content_text']['allow_all_tags'] );?> />
						<label for="content-text-allow_all_tags"><?php _e('allow all tags', 'featured-articles-lite')?></label>
						<p class="description"><?php echo esc_html( __('allowed HTML tags (ie: <a>,<p>,<h1>)', 'featured-articles-lite') );?></p>
					</td>
				</tr>
				<tr class="content-options" <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-strip_shortcodes"><?php _e( 'Remove shortcodes', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input type="checkbox" name="content_text[strip_shortcodes]" id="content-text-strip_shortcodes" value="1"<?php fa_checked( $options['content_text']['strip_shortcodes'] );?> />
						<span class="description"><?php _e( 'when checked, any shortcodes in post content will not be processed', 'featured-articles-lite' );?></span>
					</td>
				</tr>
				<tr class="content-options" <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-max_length"><?php _e( 'Text length with image', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input size="1" type="text" name="content_text[max_length]" id="content-text-max_length" value="<?php echo $options['content_text']['max_length'];?>" />
						<span class="description"><?php _e( 'maximum text length to get from post content for slide with image', 'featured-articles-lite' );?></span>
					</td>
				</tr>
				<tr class="content-options optional content-length-noimg"<?php fa_optional_field_data( 'content-text-max-length-noimg' );?> <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-max_length_noimg"><?php _e( 'Text length without image', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-text-max-length-noimg' ) );?>>	
							<input size="1" type="text" name="content_text[max_length_noimg]" id="content-text-max_length_noimg" value="<?php echo $options['content_text']['max_length_noimg'];?>" />
							<span class="description"><?php _e( 'maximum text length to get from posts for slides without image', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-text-max-length-noimg' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<tr class="content-options" <?php fa_hide( !$show_content );?>>
					<th><label for="content-text-end_truncate"><?php _e( 'End text with', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input size="5" type="text" name="content_text[end_truncate]" id="content-text-end_truncate" value="<?php echo $options['content_text']['end_truncate'];?>" />
						<span class="description"><?php _e( 'truncated texts will end with the string entered', 'featured-articles-lite' );?></span>
					</td>
				</tr>
				
				
				<tr class="content-read_more optional content-show-read-more"<?php fa_optional_field_data( 'content-read-more-show' );?>>
					<th><label for="content-read_more-show"><?php _e( 'Show read more', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-read-more-show' ) );?>>	
							<input class="toggler" data-toggle="readme-options" autocomplete="off" type="checkbox" name="content_read_more[show]" id="content-read_more-show" value="1"<?php fa_checked( $options['content_read_more']['show'] );?> />
							<span class="description"><?php _e( 'show read more link in slides', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-read-more-show' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>		
					</td>
				</tr>
				<?php 
					$read_me = $options['content_read_more']['show'];
					if( !fa_theme_field_enabled( $active, 'content-show-read-more' ) ){
						$read_me = false;
					}
				?>
				<tr class="content-read_more readme-options" <?php fa_hide( !$read_me );?>>
					<th><label for="content-read_more-text"><?php _e( 'Read more text', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<input type="text" size="10" name="content_read_more[text]" id="content-read_more-text" value="<?php echo $options['content_read_more']['text'];?>" />
						<span class="description"><?php _e( 'text to display as read more', 'featured-articles-lite' );?></span>
					</td>
				</tr>
				
				
			</tbody>
		</table>
		
		<h2><?php _e('Other options', 'featured-articles-lite');?></h2>
		<table class="form-table">
			<tbody>
				<tr class="optional content-show-date"<?php fa_optional_field_data( 'content-date-show' );?>>
					<th><label for="content-date-show"><?php _e( 'Show post date', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-date-show' ) );?>>
							<input type="checkbox" name="content_date[show]" id="content-date-show" value="1"<?php fa_checked( $options['content_date']['show'] );?> />
							<span class="description"><?php _e( 'show post publish date in slide', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-date-show' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>
					</td>
				</tr>
				<tr class="optional content-show-author"<?php fa_optional_field_data( 'content-author-show' );?>>
					<th><label for="content-author-show"><?php _e( 'Show post author name', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-author-show' ) );?>>
							<input class="toggler" data-toggle="author-options" autocomplete="off" type="checkbox" name="content_author[show]" id="content-author-show" value="1"<?php fa_checked( $options['content_author']['show'] );?> />
							<span class="description"><?php _e( 'show post author in slide', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-author-show' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<?php $show_author = fa_theme_field_enabled( $active, 'content-author-show' ) ? $options['content_author']['show'] : false; ?>
				<tr class="optional author-options" <?php fa_hide( !$show_author );?> <?php fa_optional_field_data( 'content-author-link' );?>>
					<th><label for="content-author-link"><?php _e( 'Link to author page', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-author-link' ) );?>>
							<input type="checkbox" name="content_author[link]" id="content-author-link" value="1"<?php fa_checked( $options['content_author']['link'] );?> />
							<span class="description"><?php _e( 'link author to author page', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-author-link' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
			</tbody>
		</table>
		
		<?php 
			// theme specific options - can be added from theme functions.php file
			foreach( $themes as $theme => $theme_details ):
		?>
		<div id="slider-content-settings-<?php echo esc_attr( $theme );?>" class="slider-content-settings theme-settings <?php echo esc_attr( $theme );?>" <?php fa_hide( $theme != $active );?>>
			<?php 
				/**
				 * Action to allow slider themes to implement new fields.
				 * 
				 * @param $post - the slider post being edited
				 */
				do_action( 'fa_slider_content_settings-' . $theme, $post );
			?>
		</div>
		<?php endforeach;?>
		
	</div><!-- #fa-slider-content-settings -->
	
	<div id="fa-slider-content-image-settings">
		<?php $show_image = !fa_theme_field_enabled( $active, 'content-image-show' ) ? true : $options['content_image']['show'];?>
		<table class="form-table">
			<tbody>
				<tr class="content-show-video">
					<th><label for="content-video-show"><?php _e( 'Show videos', 'featured-articles-lite' );?>:</label></th>
					<td>
						<?php fa_option_not_available();?>
					</td>
				</tr>
				<tr class="content-play-video">
					<th><label for="content-play-video-show"><?php _e( 'Show play video link', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<?php fa_option_not_available();?>								
					</td>
				</tr>
				<tr class="content-play-video play-video-options">
					<th><label for="content-play-video-text"><?php _e( 'Play video text', 'featured-articles-lite' );?>:</label></th>	
					<td>
						<?php fa_option_not_available();?>
					</td>
				</tr>				
				<!-- Optional show image field -->
				<tr class="optional content-show-mage"<?php fa_optional_field_data('content-image-show')?>>
					<th><label for="content-image-show"><?php _e( 'Show image', 'featured-articles-lite' );?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-image-show' ) );?>>
							<input class="toggler" data-toggle="image-options" autocomplete="off" type="checkbox" name="content_image[show]" id="content-image-show" value="1"<?php fa_checked( $options['content_image']['show'] );?> />
							<span class="description"><?php _e( 'display images in slides', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-image-show' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>						
					</td>
				</tr>
				<!-- /Optional show image field -->
				<tr class="image-options" <?php fa_hide( !$show_image );?>>
					<th><label for="content-image-default-image"><?php _e( 'Default image', 'featured-articles-lite' );?>:</label></th>
					<td>
						<?php fa_option_not_available();?>
					</td>
				</tr>
				
				<!-- /Optional show image field -->
				<tr class="image-options optional content-image-preload"<?php fa_optional_field_data('content-image-preload')?> <?php fa_hide( !$show_image );?>>
					<th><label for="content-image-preload"><?php _e( 'Preload images', 'featured-articles-lite' );?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-image-preload' ) );?>>	
							<input type="checkbox" name="content_image[preload]" id="content-image-preload" value="1"<?php fa_checked( $options['content_image']['preload'] );?> />
							<span class="description"><?php _e( 'preloads images when checked', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-image-preload' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<tr class="image-options optional content-image-width-attr"<?php fa_optional_field_data('content-image-width-attr')?> <?php fa_hide( !$show_image );?>>
					<th><label for="content-image-show_width"><?php _e( 'Use width attribute', 'featured-articles-lite' );?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-image-width-attr' ) );?>>	
							<input type="checkbox" name="content_image[show_width]" id="content-image-show_width" value="1"<?php fa_checked( $options['content_image']['show_width'] );?> />
							<span class="description"><?php _e( 'when checked, will add width attribute on image', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-image-width-attr' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<tr class="image-options optional content-image-height-attr"<?php fa_optional_field_data('content-image-height-attr')?> <?php fa_hide( !$show_image );?>>
					<th><label for="content-image-show_height"><?php _e( 'Use height attribute', 'featured-articles-lite' );?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-image-height-attr' ) );?>>	
							<input type="checkbox" name="content_image[show_height]" id="content-image-show_height" value="1"<?php fa_checked( $options['content_image']['show_height'] );?> />
							<span class="description"><?php _e( 'when checked, will add height attribute on image', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-image-height-attr' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<tr class="image-options optional content-image-link"<?php fa_optional_field_data('content-image-link')?> <?php fa_hide( !$show_image );?>>
					<th><label for="content-image-clickable"><?php _e( 'Link image', 'featured-articles-lite' );?>:</label></th>
					<td>
						<span class="fa-optional-field-enabled" <?php fa_hide( !fa_theme_field_enabled( $active, 'content-image-link' ) );?>>		
							<input type="checkbox" name="content_image[clickable]" id="content-image-clickable" value="1"<?php fa_checked( $options['content_image']['clickable'] );?> />
							<span class="description"><?php _e( 'when checked, image will have anchor set on it poiting to the designated URL', 'featured-articles-lite' );?></span>
						</span>
						<span class="fa-optional-field-disabled" <?php fa_hide( fa_theme_field_enabled( $active, 'content-image-link' ) );?>>
							<?php _e('NOT AVAILABLE (set by slider theme settings)', 'featured-articles-lite');?>
						</span>	
					</td>
				</tr>
				<tr class="image-options" <?php fa_hide( !$show_image );?>>
					<th>
						<label for="content-image-wp"><?php _e( 'Image size', 'featured-articles-lite' );?>:</label>						
					</th>
					<td>
						<input class="toggler" data-show="wp-image" data-hide="custom-image" autocomplete="off" type="radio" name="content_image[sizing]" id="content-image-wp" value="wp"<?php fa_checked( $options['content_image']['sizing'] == 'wp' );?> />
						<label for="content-image-wp"><?php _e('use WordPress image sizes', 'featured-articles-lite');?></label><br />
						<input class="toggler"  data-show="custom-image" data-hide="wp-image" autocomplete="off"  type="radio" name="content_image[sizing]" id="content-image-custom" value="custom"<?php fa_checked( $options['content_image']['sizing'] == 'custom' );?> />
						<label for="content-image-custom"><?php _e('use custom image size', 'featured-articles-lite');?></label>						
					</td>
				</tr>
				<?php 
					$show_wp = !$show_image ? $show_image : ( $options['content_image']['sizing'] == 'wp' );
				?>
				<tr class="image-options wp-image" <?php fa_hide( !$show_wp );?>>
					<th>
						<label for="content-image-wp_size"><?php _e( 'WordPress image size', 'featured-articles-lite' );?>:</label>						
					</th>
					<td>
						<?php 
							$args = array(
								'name' 	=> 'content_image[wp_size]',
								'id'	=> 'content-image-wp_size',
								'selected' => $options['content_image']['wp_size']
							);
							fa_wp_image_size_dropdown( $args );
						?>
					</td>
				</tr>
				<?php 
					$show_custom = !$show_image ? $show_image : ( $options['content_image']['sizing'] == 'custom' );
				?>
				<tr class="image-options custom-image" <?php fa_hide( !$show_custom );?>>
					<th>
						<label for="content-image-width"><?php _e('Custom size', 'featured-articles-lite')?>:</label>
					</th>
					<td>
						<label for="content-image-width"><?php _e('width', 'featured-articles-lite');?></label>
						<input size="2" type="text" name="content_image[width]" id="content-image-width" value="<?php echo $options['content_image']['width'];?>" /> px ;  
						<label for="content-image-height"><?php _e('height', 'featured-articles-lite');?></label>
						<input size="2" type="text" name="content_image[height]" id="content-image-height" value="<?php echo $options['content_image']['height'];?>" /> px
						<p class="description"><?php _e('if real image size is smaller than the values above, full size image will be used', 'featured-articles-lite');?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php 
			// theme specific options - can be added from theme functions.php file
			foreach( $themes as $theme => $theme_details ):
		?>
		<div id="slider-images-settings-<?php echo esc_attr( $theme );?>" class="slider-images-settings theme-settings <?php echo esc_attr( $theme );?>" <?php fa_hide( $theme != $active );?>>
			<?php 
				/**
				 * Action to allow slider themes to add new settings for images.
				 * Settings will be displayed under *Media options* tab when editing a slider.
				 * 
				 * @param $post - the slider post being edited
				 */
				do_action( 'fa_slider_images_settings-' . $theme, $post );
			?>
		</div>
		<?php endforeach;?>
	</div><!-- #fa-slider-content-image-settings -->
</div>