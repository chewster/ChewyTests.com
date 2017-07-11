<div id="wpbody">
	<div id="wpbody-content">	
		<div class="wrap">
			<form method="post" action="<?php fa_iframe_admin_page_url('fa-post-slide-edit', array( 'post_id' => $post->ID, 'slider_id' => $slider_id ) );?>" id="fa-slide-edit-form">
				<?php wp_nonce_field( 'fa-slide-modal-options-save', 'fa-slide-modal-settings-nonce' );?>
				<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID;?>" />
				<input type="hidden" id="slider_id" name="slider_ID" value="<?php echo $slider_id;?>" />
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div id="titlediv">
								<div id="titlewrap">
									<label class="screen-reader-text" id="title-prompt-text" for="title"><?php _e( 'Enter slide title', 'featured-articles-lite' ); ?></label>
									<input id="title" type="text" autocomplete="off" value="<?php echo $options['title'];?>" size="30" name="fa_slide[title]" />
								</div><!-- #titlewrap -->
							</div><!-- #titlediv -->
							<div id="postdivrich" class="postarea edit-form-section">
								<?php 
									wp_editor( 
										$options['content'] , 
										'content', 
										array(
											'textarea_name' => 'fa_slide[content]',
											'teeny' => true
										)
									);
								?>
							</div><!-- postdivrich -->	
						</div><!-- #post-body-content -->
						
						<div id="postbox-container-1" class="postbox-container">
							<div id="side-sortables" class="meta-box-sortables ui-sortable">
								<?php do_meta_boxes( $screen_id, 'core', $post);?>
								<?php do_meta_boxes( $screen_id, 'side', $post);?>
							</div><!-- side-sortables -->
						</div><!-- #postbox-container-1 -->
						
						<div id="postbox-container-2" class="postbox-container">
							<div id="normal-sortables" class="meta-box-sortables ui-sortable">
								<?php do_meta_boxes( $screen_id, 'advanced', $post);?>
							</div><!-- normal-sortables -->
						</div><!-- #postbox-container-2 -->
					</div><!-- #post-body -->
					
				</div><!-- #poststuff -->				
			</form>
		</div>
	</div><!-- #wpbody-content -->	
</div><!-- #wpbody -->	