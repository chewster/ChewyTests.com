<?php 
function wpfcas_featuredcslider_shortcode( $atts) {
	
	extract(shortcode_atts(array(
		"limit" => '',		
		"cat_id" => '',
		"post_type" => '',
		"design" => '',
		"fa_icon_color" => '',
		"image_style" => '',
		"display_read_more" => '',
		'slides_column'     => '',
		'slides_scroll'     => '',		
		'dots'     			=> '',
		'arrows'     		=> '',				
		'autoplay'     		=> '',		
		'autoplay_interval'  => '',				
		'speed'             => '',
		"content_words_limit" => '',
		"show_content" => '',		
	), $atts));
	
	// Required enqueue_script
	wp_enqueue_script( 'wpos-slick-jquery' );
	
	// Define limit
	if( $limit ) { 
		$posts_per_page = $limit;
             
	} else {
		$posts_per_page = '-1';
	}
	
	if( $cat_id ) { 
		 $cat = $cat_id; 
	} else {
		$cat = '';
	}	
	
	if( $post_type ) { 
		 $postType = $post_type; 
	} else {
		$postType = 'featured_post';
	}
	
	if( $image_style ) { 
		 $imagestyle = $image_style; 
	} else {
		$imagestyle = 'square';
	}
	
	
	if( $design ) { 
		$designfc = $design; 
	} else {
		$designfc = 'design-1';
	}
	
	if( $display_read_more ) { 
		$displayreadMore = $display_read_more; 
	} else {
		$displayreadMore = 'true';
	}
	
	if( $fa_icon_color ) { 
		 $faIconcolor = $fa_icon_color; 
	} else {
		$faIconcolor = '#3ab0e2';
	}
	
	if( $slides_column ) { 
		 $slidesColumn = $slides_column; 
	} else {
		$slidesColumn = '3';
	}
	if( $slides_scroll ) { 
		 $slidesScroll = $slides_scroll; 
	} else {
		$slidesScroll = '1';
	}
	
	if( $dots ) { 
		 $slidedots = $dots; 
	} else {
		$slidedots = 'true';
	}
	if( $arrows ) { 
		 $slidearrows = $arrows; 
	} else {
		$slidearrows = 'true';
	}
	
	if( $autoplay ) { 
		 $slideautoplay = $autoplay; 
	} else {
		$slideautoplay = 'true';
	}
	
	if( $autoplay_interval ) { 
		 $slideautoplayInterval = $autoplay_interval; 
	} else {
		$slideautoplayInterval = '3000';
	} 
	
	if( $speed ) { 
		 $slidespeed = $speed; 
	} else {
		$slidespeed = '300';
	} 
	
	 if( $content_words_limit ) { 
        $words_limit = $content_words_limit; 
    } else {
        $words_limit = '50';
    }
	if( $show_content ) { 
        $showContent = $show_content; 
    } else {
        $showContent = 'true';
    }

	ob_start();

	$unique 		= wpfcas_get_unique();
	$posts_type 	=  $postType;
	$orderby 		= 'post_date';
	$order 			= 'DESC';
	       			
	 $args = array ( 
            'post_type'      => $posts_type, 
            'orderby'        => $orderby, 
            'order'          => $order,
            'posts_per_page' => $posts_per_page,  
           
            );
	if($cat != ""){
            	$args['tax_query'] = array( array( 'taxonomy' => 'wpfcas-category', 'field' => 'id', 'terms' => $cat) );
            }        
      $query = new WP_Query($args);
	
	$post_count = $query->post_count; ?>
	<div class="wpfcas-content-slider-<?php echo $unique; ?> featured-content-slider <?php echo $designfc; ?>">
		 <?php while ($query->have_posts()) : $query->the_post();
				$class = "";
				$css_class = "";		

		switch ($designfc) {
				 case "design-1":
					include('designs/design-1.php');
					break;
				 case "design-2":
					include('designs/design-2.php');
					break;
				case "design-3":
					include('designs/design-3.php');
					break;			
				case "design-4":
					include('designs/design-4.php');
					break;
				 default:		 

						include('designs/design-1.php');

					}

		endwhile; ?>
	</div>
	<?php wp_reset_query(); ?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
		jQuery('.wpfcas-content-slider-<?php echo $unique; ?>').slick({
			dots: <?php echo $slidedots; ?>,
			infinite: true,
			arrows: <?php echo $slidearrows; ?>,
			speed: <?php echo $slidespeed; ?>,
			autoplay: <?php echo $slideautoplay; ?>,						
			autoplaySpeed: <?php echo $slideautoplayInterval; ?>,
			slidesToShow: <?php echo $slidesColumn; ?>,
			slidesToScroll: <?php echo $slidesScroll; ?>,
			responsive: [
    {
      breakpoint: 769,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 650,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 481,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
		});
	});
	</script>    
<?php
return ob_get_clean();
}
add_shortcode('featured-content-slider','wpfcas_featuredcslider_shortcode');