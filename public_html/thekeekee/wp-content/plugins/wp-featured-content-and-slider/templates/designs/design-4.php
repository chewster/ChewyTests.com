<?php 
global $post;
$wpfcasfeat_image = wp_get_attachment_url( get_post_thumbnail_id() );
$sliderurl = get_post_meta( get_the_ID(),'wpfcas_slide_link', true );
$wpfcasIcon = get_post_meta( get_the_ID(),'wpfcas_slide_icon', true ); 
if(isset($slidesColumn) && $slidesColumn != ''){ $slideClass = $slidesColumn; }else{ $slideClass = '';}
?>
<div class="<?php echo $css_class.' '.$class.' slider-col-'.$slideClass; ?>">  
				<div class="featured-content-position">
					<div class="featured-content-image-bg"> 
						<img src="<?php echo $wpfcasfeat_image; ?>"alt="<?php the_title(); ?>" />				
						
						
					</div>
					<div class="featured-content-overlay">
					<div class="featured-content-image <?php echo $imagestyle; ?>"> 
						<?php if($sliderurl != '') { ?>
						<a href="<?php echo $sliderurl; ?>" > 	<?php if($wpfcasIcon != '') { echo '<i style="color:'.$faIconcolor.'" class="'.$wpfcasIcon.'"></i>';  } ?>		</a>
						<?php } 
							else
								{								
							if($wpfcasIcon != '') { echo '<i style="color:'.$faIconcolor.'" class="'.$wpfcasIcon.'"></i>'; }  ?>	
						<?php } ?>
					</div>
					<h3 class="entry-title">
					<?php if($sliderurl != '') { ?>
					<a href="<?php echo $sliderurl; ?>" ><?php the_title(); ?></a>
						<?php } else { ?>
						<?php the_title(); ?>
						<?php } ?>
					</h3>
					<?php if($showContent == "true") { ?>
					<div class="featured_short_content">
					<?php
					$customExcerpt = get_the_excerpt();				
					if (has_excerpt($post->ID))  { ?>
						<div class="sub-content"><?php echo $customExcerpt ; ?></div>
					<?php } else {		
					$excerpt = strip_tags(get_the_content()); ?>
                    <div class="sub-content"><?php echo wpfcas_limit_words($excerpt,$words_limit); ?></div>	
					<?php } ?>	
					</div>					
					<?php } if($displayreadMore == 'true') {
							if($sliderurl != '') { ?>
					<div class="featured-read-more">					
					<a href="<?php echo $sliderurl; ?>" ><?php esc_html_e( 'Read More', 'wp-featured-content-and-slider' ); ?></a>					
					
					</div>
					<?php } } ?>
					</div>
					</div>
					
					
					
				</div>				
				