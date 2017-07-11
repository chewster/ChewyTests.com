<?php

/***** Register Widgets *****/

function smart_magazine_register_homepage_widgets() {
	register_widget('smart_magazine_homepage_header_widget');
}
add_action('widgets_init', 'smart_magazine_register_homepage_widgets');


class smart_magazine_homepage_header_widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'header_post_grid', 'description' => __('Header Grid Widget to display posts in grid format based on categories or tags. Please use it in Homepage Header', 'smart-magazine'));
        parent::__construct('smart-magazine-homepage-header', __('Header Post Grid', 'smart-magazine'), $widget_ops);
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $category = isset($instance['category']) ? $instance['category'] : '';
        $tags = empty($instance['tags']) ? '' : $instance['tags'];
        $postcount = empty($instance['postcount']) ? '5' : $instance['postcount'];
        $offset = empty($instance['offset']) ? '' : $instance['offset'];
        $sticky = isset($instance['sticky']) ? $instance['sticky'] : 0;

        if ($category) {
        	$cat_url = get_category_link($category);
	        $before_title = $before_title . '<a href="' . esc_url($cat_url) . '" class="widget-title-link">';
	        $after_title = '</a>' . $after_title;
        }

        echo $before_widget;
        if (!empty( $title)) { echo $before_title . esc_attr($title) . $after_title; } ?>
        <?php
		$args = array('posts_per_page' => $postcount, 'offset' => $offset, 'cat' => $category, 'tag' => $tags, 'ignore_sticky_posts' => $sticky);
		$counter = 1;
		$the_query = new WP_Query($args);
		if($the_query->have_posts()): 
		
		$count = 0;
		$remaining_posts = '';
		while ( $the_query->have_posts() ) : $the_query->the_post(); 
			
			$post_id = get_the_ID();
			
			$img_src= '';
			if ( has_post_thumbnail($post_id ) ) {
				$post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
				$img_src = $post_image[0];
			}
			$post_permalink = get_the_permalink();
			$post_title = get_the_title();
			$post_date = get_the_date('M d, Y');
			$post_categories = wp_get_post_categories( $post_id );
			$cat_html = '';
			if(!empty($post_categories)){
				foreach($post_categories as $post_category){
					$cat = get_category( $post_category );
					
					
					$cat_html .= '<li class="cat"><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></li>';
				}
			}
			
			if($count == 0){
			
			$first_grid_post = <<<HTML
   <div class='col-sm-6 col-xs-6  gum_grid_large'>
	<div class="gum_post_grid gum_large_grid" style="background-image: url('$img_src');'">
				<div class="gum_post_data">
								<a href="$post_permalink" class="p_title">$post_title</a>
								
								<ul>
								$cat_html
								<li class="date">$post_date</li></ul>									
								
							</div><!-- .gum_post_data -->
							
							
					
			</div> <!-- gum_large_grid -->
					</div> <!-- gum_grid_large -->
HTML;



			
			} // count==0 
			
			else{
				
				$remaining_posts .= <<<HTML
									<div class="col-sm-6  col-xs-6">
						<div class="gum_post_grid gum_small_grid m_b_10" style=" background-image: url('$img_src');">
							<div class="gum_post_data">
								<a href="$post_permalink" class="p_title">$post_title</a>
								
								<ul>$cat_html
								<li class="date">$post_date</li></ul>									
							</div><!-- gum_post_data -->
						</div><!-- gum_small_grid -->
				</div>
HTML;
				
			}
			
		$count++;
	endwhile; 
	wp_reset_postdata();
	?>
		<div class="gum_posts_grid_wrapper">
				<div class="gum_posts_grid_slide">
				<?php echo $first_grid_post;?>
				
				<div class="col-sm-6  gum_grid_small">
					<div class="row">
						<?php echo $remaining_posts; ?>
					</div><!-- row -->
			
				</div><!-- gum_grid_small -->
				<div class="clearfix"></div>
		</div><!-- .gum_posts_grid_slide -->
	</div><!-- .gum_posts_grid_wrapper -->        
     <?php endif; ?>   
        <?php
        echo $after_widget;
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['category'] = absint($new_instance['category']);
        $instance['postcount'] = absint($new_instance['postcount']);
        $instance['offset'] = absint($new_instance['offset']);
        $instance['tags'] = sanitize_text_field($new_instance['tags']);
        $instance['sticky'] = isset($new_instance['sticky']) ? strip_tags($new_instance['sticky']) : '';
        return $instance;
    }
    function form($instance) {
        $defaults = array('title' => '', 'category' => '', 'tags' => '','sticky' => 0, 'offset' => 0);
        $instance = wp_parse_args((array) $instance, $defaults); ?>

        <p>
        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['title']); ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Select a Category:', 'smart-magazine'); ?></label>
			<select id="<?php echo $this->get_field_id('category'); ?>" class="widefat" name="<?php echo $this->get_field_name('category'); ?>">
				<option value="0" <?php if (!$instance['category']) echo 'selected="selected"'; ?>><?php _e('All', 'smart-magazine'); ?></option>
				<?php
				$categories = get_categories(array('type' => 'post'));
				foreach($categories as $cat) {
					echo '<option value="' . $cat->cat_ID . '"';
					if ($cat->cat_ID == $instance['category']) { echo ' selected="selected"'; }
					echo '>' . $cat->cat_name . ' (' . $cat->category_count . ')';
					echo '</option>';
				}
				?>
			</select>
		</p>
		<p>
        	<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Filter Posts by Tags (e.g. lifestyle):', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['tags']); ?>" name="<?php echo $this->get_field_name('tags'); ?>" id="<?php echo $this->get_field_id('tags'); ?>" />
	    </p>
    
	    <p>
        	<label for="<?php echo $this->get_field_id('offset'); ?>"><?php _e('Skip:', 'smart-magazine'); ?></label>
			<input type="text" size="2" value="<?php echo esc_attr($instance['offset']); ?>" name="<?php echo $this->get_field_name('offset'); ?>" id="<?php echo $this->get_field_id('offset'); ?>" /> <?php _e('Posts', 'smart-magazine'); ?>
	    </p>
        <p>
      		<input id="<?php echo $this->get_field_id('sticky'); ?>" name="<?php echo $this->get_field_name('sticky'); ?>" type="checkbox" value="1" <?php checked('1', $instance['sticky']); ?>/>
	  		<label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e('Ignore Sticky Posts', 'smart-magazine'); ?></label>
    	</p>
    	<?php
    }
}