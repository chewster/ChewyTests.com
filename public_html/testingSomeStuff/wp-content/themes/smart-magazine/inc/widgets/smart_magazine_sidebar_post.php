<?php

/***** Register Widgets *****/

function smart_magazine_register_sidebar_posts_widgets() {
	register_widget('smart_magazine_sidebar_posts_widget');
}
add_action('widgets_init', 'smart_magazine_register_sidebar_posts_widgets');


class smart_magazine_sidebar_posts_widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'sidebar_post', 'description' => __('Posts with featured image', 'smart-magazine'));
        parent::__construct('smart-magazine-gum-sidebar-posts', __('Sidebar Posts with images', 'smart-magazine'), $widget_ops);
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $category = isset($instance['category']) ? $instance['category'] : '';
        $tags = empty($instance['tags']) ? '' : $instance['tags'];
        $postcount = empty($instance['postcount']) ? '5' : $instance['postcount'];
        $offset = empty($instance['offset']) ? '' : $instance['offset'];
        $sticky = isset($instance['sticky']) ? $instance['sticky'] : 0;
        $random = isset($instance['random']) ? $instance['random'] : 0;

        if ($category) {
        	$cat_url = get_category_link($category);
	        $before_title = $before_title . '<a href="' . esc_url($cat_url) . '" class="widget-title-link">';
	        $after_title = '</a>' . $after_title;
        }

        echo $before_widget;
        if (!empty( $title)) { echo $before_title . esc_attr($title) . $after_title; } ?>
        <?php
		$args = array('posts_per_page' => $postcount, 'offset' => $offset, 'cat' => $category, 'tag' => $tags, 'ignore_sticky_posts' => $sticky);
		if($random == 1)$args['orderby'] = 'rand';
		$counter = 1;
		$the_query = new WP_Query($args);
		if($the_query->have_posts()): 
		
		$count = 0;
		$widget_posts = '';
		while ( $the_query->have_posts() ) : $the_query->the_post(); 
			
			$post_id = get_the_ID();
			
			$img_src= '';
			if ( has_post_thumbnail($post_id ) ) {
				$post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
				$img_src = $post_image[0];
			}
			
			$post_permalink = get_the_permalink();
			$post_title = get_the_title();
			$post_date = get_the_date('M d, Y');
			
			$post_image='';
			if(strlen($img_src) > 3){
				$post_image = '<a href="'.$post_permalink.'"><img src="'.$img_src.'" alt="'.$post_title.'" /></a>';
			}
			$widget_posts .= <<<HTML
			
								<div class="gum_sidebar_widget clearfix">
									<div class="col-sm-4 col-xs-2 gum_sidebar_post_image">
										$post_image
									</div>
					<div class="gum_sidebar_post_title col-sm-8 col-xs-10">
						
							<a href="$post_permalink" class="p_title">$post_title</a>
							
							<ul>
							<li class="date">$post_date</li></ul>									
						
					</div><!-- gum_sidebar_post_title -->
			</div><!-- gum_sidebar_widget -->
HTML;
				
			
	endwhile; 
	wp_reset_postdata();
	?>
		<div class="gum_sidebar_posts">
			<?php echo $widget_posts; ?>
		</div><!-- .gum_sidebar_posts -->        
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
        $instance['random'] = isset($new_instance['random']) ? strip_tags($new_instance['random']) : '';
        return $instance;
    }
    function form($instance) {
        $defaults = array('title' => '', 'category' => '', 'tags' => '','sticky' => 0, 'offset' => 0, 'random'=>0,);
        $instance = wp_parse_args((array) $instance, $defaults); ?>

        <p>
        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['title']); ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
      		<input id="<?php echo $this->get_field_id('random'); ?>" name="<?php echo $this->get_field_name('random'); ?>" type="checkbox" value="1" <?php checked('1', $instance['random']); ?>/>
	  		<label for="<?php echo $this->get_field_id('random'); ?>"><?php _e('Random Posts', 'smart-magazine'); ?></label>
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