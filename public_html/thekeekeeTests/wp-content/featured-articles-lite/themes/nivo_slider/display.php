<?php 
	/**
	 * Featured Articles PRO - Nivo Slider theme 
	 */
	// store slides captions
	$captions = array();	
?>
<div class="<?php the_slider_class( 'slider-wrapper fa-nivo-slider-wrapper' );?>" style="<?php the_slider_styles(); the_slider_width();?>" id="<?php the_slider_id();?>" <?php the_slider_data();?>>
	<div class="nivoSlider fa-nivo-slider">
    	<?php while( have_slides() ):?>
    		<?php 
    			$url = get_the_slide_url();
    			$image = get_the_fa_slide_image_url();
    			$caption_id = 'fa-caption-' . rand(1, 1000);   			
    			
    			if( !$image ){
    				echo "<!-- Slide skipped because it doesn't have an image set on it or images are blocked by slider settings. -->\n";
    				continue;
    			}
    			
    			$template = ( $url ? '<a href="%1$s"><img src="%2$s" alt="%3$s" title="%4$s" /></a>' : '%1$s<img src="%2$s" alt="%3$s" title="%4$s" />' ) . "\n";
				
    			$content = the_fa_title( '<h2>', '</h2>', false ) . the_fa_date( '<span class="fa-date"> ', '</span>', false ). the_fa_author( '<span class="fa-author">' . __(' by '), '</span> ', false ) . the_fa_content( '', '', false ) . the_fa_read_more( 'fa-read-more', false );
    			if( !empty( $content ) ){
    				$captions[] = sprintf(
						'<div id="%s" class="nivo-html-caption">%s</div>',
						$caption_id,
						$content
					);    				
    			}else{
    				$caption_id = false;    				
    			}
				
    			printf( 
					$template, 
					$url, 
					$image['url'], 
					esc_attr( the_fa_title( '', '', false, true ) ),
					( $caption_id ? '#' . $caption_id : '' )
				);
    		?>
    	<?php endwhile;?>
    </div>
    <?php 
    	// show the captions
    	echo implode( "\n", $captions );
    ?>
</div>