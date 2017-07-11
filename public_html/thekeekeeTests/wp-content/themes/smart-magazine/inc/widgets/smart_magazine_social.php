<?php
/***** Register Widgets *****/

function smart_magazine_register_social_widgets() {
	register_widget('smart_magazine_social_widget');
}
add_action('widgets_init', 'smart_magazine_register_social_widgets');


class smart_magazine_social_widget extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'gum_social', 'description' => __('Social Media Links', 'smart-magazine'));
        parent::__construct('smart-magazine-gum-social', __('Social Media links', 'smart-magazine'), $widget_ops);
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $facebook_link = isset($instance['facebook_link']) ? esc_url($instance['facebook_link']) : '';
        $twitter_link = isset($instance['twitter_link']) ? esc_url($instance['twitter_link']) : '';
        $googleplus_link = isset($instance['googleplus_link']) ? esc_url($instance['googleplus_link']) : '';
        $youtube_link = isset($instance['youtube_link']) ? esc_url($instance['youtube_link']) : '';

       
        echo $before_widget;
        if (!empty( $title)) {  echo $before_title . esc_attr($title) . $after_title; 
		} ?>
        	<ul class="social-icons icon-rounded  list-unstyled list-inline"> 
				<?php
					if(strlen($facebook_link) > 3 ) echo ' <li> <a href="'.$facebook_link.'"><i class="fa fa-facebook-square"></i></a></li>';
					if(strlen($twitter_link) > 3 ) echo ' <li> <a href="'.$twitter_link.'"><i class="fa fa-twitter"></i></a></li>';

					if(strlen($googleplus_link) > 3 ) echo ' <li> <a href="'.$googleplus_link.'"><i class="fa fa-google-plus"></i></a></li>';

					if(strlen($youtube_link) > 3 ) echo ' <li> <a href="'.$youtube_link.'"><i class="fa fa-youtube"></i></a></li>';

				
				?>
        	
        	</ul>        	
         <?php
        echo $after_widget;
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['facebook_link'] = sanitize_text_field($new_instance['facebook_link']);
        $instance['twitter_link'] = sanitize_text_field($new_instance['twitter_link']);
        $instance['googleplus_link'] = sanitize_text_field($new_instance['googleplus_link']);
        $instance['youtube_link'] = sanitize_text_field($new_instance['youtube_link']);
     
        return $instance;
    }
    function form($instance) {
        $defaults = array('title' => '', 'facebook_link' => '', 'twitter_link' => '','googleplus_link' => '', 'youtube_link' => '');
        $instance = wp_parse_args((array) $instance, $defaults); ?>

        <p>
        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['title']); ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        
            <p>
        	<label for="<?php echo $this->get_field_id('facebook_link'); ?>"><?php _e('Facebook:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['facebook_link']); ?>" name="<?php echo $this->get_field_name('facebook_link'); ?>" id="<?php echo $this->get_field_id('facebook_link'); ?>" />
			
        </p>
        
            <p>
        	<label for="<?php echo $this->get_field_id('twitter_link'); ?>"><?php _e('Twitter:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['twitter_link']); ?>" name="<?php echo $this->get_field_name('twitter_link'); ?>" id="<?php echo $this->get_field_id('twitter_link'); ?>" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('googleplus_link'); ?>"><?php _e('Googleplus:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['googleplus_link']); ?>" name="<?php echo $this->get_field_name('googleplus_link'); ?>" id="<?php echo $this->get_field_id('googleplus_link'); ?>" />
        </p>
        
        <p>
        	<label for="<?php echo $this->get_field_id('youtube_link'); ?>"><?php _e('Youtube:', 'smart-magazine'); ?></label>
			<input class="widefat" type="text" value="<?php echo esc_attr($instance['youtube_link']); ?>" name="<?php echo $this->get_field_name('youtube_link'); ?>" id="<?php echo $this->get_field_id('youtube_link'); ?>" />
        </p>
     
    	<?php
    }
}