<?php 
	// get the options implemented by the slider theme
	$options = get_slider_theme_options();
?>
<div class="<?php the_slider_class( 'fa_slider_simple' );?>" style="<?php the_slider_styles();?>" id="<?php the_slider_id();?>" <?php the_slider_data();?>>
	<?php while( have_slides() ): ?>
	<div class="fa_slide <?php the_fa_class();?>" style="<?php the_slide_styles();?>">
		
		<div class="fa_slide_content">	
			<?php the_fa_title('<h2>', '</h2>');?>
			<?php the_fa_content('<div class="description">', '</div>');?>
			<?php the_fa_read_more('fa_read_more');?>
			<?php the_fa_play_video('fa_play_video', 'modal');?>
		</div>
		<?php the_fa_image( '<div class="fa_image">', '</div>', false );?>			
			
	</div>	
	<?php endwhile;// have_slides()?>
	<?php if( has_sideways_nav() ):?>
		<div class="go-forward"></div>
		<div class="go-back"></div>
	<?php endif;?>
	<?php if( has_bottom_nav() ):?>
		<div class="main-nav">
			<?php while( have_slides() ):?>
			<a href="#" title="" class="fa-nav"></a>
			<?php endwhile;?>		
		</div>
	<?php endif;?>
	<?php 
		// show progress bar according to user settings
		if( isset( $options['show_timer'] ) && $options['show_timer'] ):
	?>	
	<div class="progress-bar"><!-- slider progress bar --></div>
	<?php endif;// show timer?>	
</div>