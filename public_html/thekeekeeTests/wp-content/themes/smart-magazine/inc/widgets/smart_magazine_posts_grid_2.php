<?php

/***** Register Widgets *****/

function smart_magazine_register_posts_grid_2_widgets() {
	register_widget('smart_magazine_posts_grid_2_widget');
}
add_action('widgets_init', 'smart_magazine_register_posts_grid_2_widgets');

class smart_magazine_posts_grid_2_widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'gum_grid_block_2', 'description' => __('Posts Grid block 2', 'smart-magazine'));
        parent::__construct('smart-magazine-posts-grid-2', __('Posts Grid 2', 'smart-magazine'), $widget_ops);
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $category = isset($instance['category']) ? $instance['category'] : '';
        $tags = empty($instance['tags']) ? '' : $instance['tags'];
        $postcount = empty($instance['postcount']) ? '8' : $instance['postcount'];
        $offset = empty($instance['offset']) ? '' : $instance['offset'];
        $sticky = isset($instance['sticky']) ? $instance['sticky'] : 0;
        $show_cat = isset($instance['show_cat']) ? $instance['show_cat'] : 0;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : 1;

        if ($category) {
        	$cat_url = get_category_link($category);
	        $before_title = $before_title . '<a href="' . esc_url($cat_url) . '" class="widget-title-link">';
	        $after_title = '</a>' . $after_title;
        }

        echo $before_widget;
        if (!empty( $title)) {  echo '<div class="gum_post_grid_header">
			<div class="grid_heading">'.$before_title . esc_attr($title) . $after_title.'   </div>
			<div class="clearfix"></div>
			</div>';
		} ?>
        <?php
		$args = array('posts_per_page' => $postcount, 'offset' => $offset, 'cat' => $category, 'tag' => $tags, 'ignore_sticky_posts' => $sticky);
		$counter = 1;
		$the_query = new WP_Query($args);
		if($the_query->have_posts()):

		$count = 0;
		$remaining_posts = '';
		$first_grid_post='';
		while ( $the_query->have_posts() ) : $the_query->the_post();

			$post_id = get_the_ID();

			$img_src= '';
			if ( has_post_thumbnail($post_id ) ) {
				$post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
				$img_src = $post_image[0];
			}
			$post_permalink = get_the_permalink();
			$post_title = get_the_title();
			$excerpt = get_the_excerpt();
			$post_date = get_the_date('M d, Y');
			$post_categories = wp_get_post_categories( $post_id );
			$cat_html = '';
			if(!empty($post_categories)){
				foreach($post_categories as $post_category){
					$cat = get_category( $post_category );


					$cat_html .= '<li class="cat"><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></li>';
				}
			}
			if($show_cat != 1 ) $cat_html ='';
			$date_li='';
			if($show_date == 1) $date_li = '<li class="date">'.$post_date.'</li>';

			if($count <= 1){

			$first_grid_post .= <<<HTML
   <div class="gum_block_1 col-sm-12 col-md-6 large_post">
            <div class="single_post_block">

             <a href="$post_permalink"><img src="$img_src" alt="$post_title"/></a>
            <a href="$post_permalink" class="p_title">$post_title</a>

			 <div class="gum_post_block_meta">
                  <ul>
                     $cat_html	$date_li
                  </ul>
               </div>

			</div> <!-- gum_large_grid -->
					</div> <!-- gum_grid_large -->
HTML;

				if($count == 1) $first_grid_post .=  '<div class="clearfix"></div>';


			} // count==0

			else{

				$remaining_posts .= <<<HTML
				<div class="single_post_block gum_block_3 col-sm-6 ">
               <div class="col-xs-4 col-sm-5 col-md-5 small_post_block_img"><a href="$post_permalink"><img src="$img_src" alt="$post_title"></a></div>
               <div class="col-xs-8 col-sm-7 col-md-7 small_post_block_copy">
                  <a href="$post_permalink" class="post_title">$post_title</a>
                  <div class="gum_post_block_meta">
                     <ul>
                        $cat_html	$date_li
                     </ul>
                  </div>
               </div>
               <!-- col-sm-7 -->
               <div class="clearfix"></div>
            </div>
            <!-- single_post_block -->
HTML;

				if($counter % 2 == 0) $remaining_posts .= '<div class="clearfix"></div>';

			}

		$count++;
		$counter++;
	endwhile;
	wp_reset_postdata();
	?>
		<div class="gum_posts_block_wrapper">
      <div class="gum_posts_block gum_posts_block_2">
         <?php echo $first_grid_post; ?>

            <?php echo $remaining_posts; ?>
             <div class="clearfix"></div>
         <div class="clearfix"></div>
      </div> <!-- gum_posts_block -->
   </div>
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
        $instance['show_cat'] = isset($new_instance['show_cat']) ? strip_tags($new_instance['show_cat']) : '';

        $instance['show_date'] = isset($new_instance['show_date']) ? strip_tags($new_instance['show_date']) : '';

        return $instance;
    }
    function form($instance) {
        $defaults = array('title' => '', 'category' => '', 'tags' => '','sticky' => 0, 'postcount'=>8, 'offset' => 0, 'show_cat' => 1, 'show_date'=>1);
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

			<input type="text" size="2" value="<?php echo esc_attr($instance['postcount']); ?>" name="<?php echo $this->get_field_name('postcount'); ?>" id="<?php echo $this->get_field_id('postcount'); ?>" /> <?php _e('Number of Posts', 'smart-magazine'); ?>
	    </p>

        <p>
      		<input id="<?php echo $this->get_field_id('sticky'); ?>" name="<?php echo $this->get_field_name('sticky'); ?>" type="checkbox" value="1" <?php checked('1', $instance['sticky']); ?>/>
	  		<label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e('Ignore Sticky Posts', 'smart-magazine'); ?></label>
    	</p>
    	 <p>
      		<input id="<?php echo $this->get_field_id('show_cat'); ?>" name="<?php echo $this->get_field_name('show_cat'); ?>" type="checkbox" value="1" <?php checked('1', $instance['show_cat']); ?>/>
	  		<label for="<?php echo $this->get_field_id('show_cat'); ?>"><?php _e('Show category name', 'smart-magazine'); ?></label>
    	</p>

    	 <p>
      		<input id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" value="1" <?php checked('1', $instance['show_date']); ?>/>
	  		<label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show date', 'smart-magazine'); ?></label>
    	</p>
    <?php
    }
}