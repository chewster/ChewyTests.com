<?php
/*
Plugin Name: A Featured Page Widget
Plugin URI: http://github.com/eduardozulian/a-featured-page-widget
Description: Feature a page and display its excerpt and post thumbnail.
Version: 1.3
Author: Eduardo Zulian
Author URI: http://flutuante.com.br
License: GPL2

Copyright 2013 Eduardo Zulian

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Load translated strings
 */
function afpw_load_textdomain() {
	load_plugin_textdomain( 'a-featured-page-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'afpw_load_textdomain' );

/**
 * Enqueue the stylesheet
 */
function afpw_enqueue_stylesheet() {
	wp_register_style( 'a-featured-page-widget', plugins_url( 'css/a-featured-page-widget.css', __FILE__ ) );
	wp_enqueue_style( 'a-featured-page-widget' );
}

add_action( 'wp_enqueue_scripts', 'afpw_enqueue_stylesheet' );

/**
 * Register the widget
 */
function afpw_register_widget() {
	register_widget( 'A_Featured_Page_Widget' );
}

add_action( 'widgets_init', 'afpw_register_widget' );

/**
 * A Featured Page Widget
 * Feature a page, showing its excerpt and thumbnail.
 *
 */
class A_Featured_Page_Widget extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct(
	 		'a_featured_page_widget',
			__( 'A Featured Page Widget', 'a-featured-page-widget' ),
			array( 'description' => __( 'Feature a page and display its excerpt and post thumbnail.', 'a-featured-page-widget' ) )
		);
	}

	/**
	 * Outputs the content for a new widget instance.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args 		Widget arguments.
	 * @param array $instance 	Saved values from database.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		if ( isset( $instance['page'] ) && $instance['page'] != -1 ) {

			$page_id = (int) $instance['page'];
			$link_text = isset( $instance['link-text'] ) ? strip_tags( $instance['link-text'] ) : apply_filters( 'afpw_link_text', __( 'Continue reading', 'a-featured-page-widget' ) );
			$image_size = isset( $instance['image-size'] ) ? strip_tags( $instance['image-size'] ) : 'thumbnail';

			$p = new WP_Query( array( 'page_id' => $page_id ) );

			if ( $p->have_posts() ) {
				$p->the_post();

				$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? get_the_title() : $instance['title'], $instance, $this->id_base );

				echo $before_widget;
				echo $before_title;
				echo $title;
				echo $after_title;
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( $image_size != 'no-thumbnail' && has_post_thumbnail() ) : ?>
						<div class="post-thumbnail entry-image">
							<a class="thumbnail-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">						
								<?php the_post_thumbnail( $image_size ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="entry-content">
						<?php the_excerpt(); ?>
						<?php if ( ! empty( $link_text ) ) : ?>
						<a href="<?php the_permalink(); ?>" class="more-link"><?php echo $link_text; ?></a>
						<?php endif; ?>
					</div>
				</div>

				<?php
				echo $after_widget;

				wp_reset_postdata();
			}
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['page'] = (int)( $new_instance['page'] );
		$instance['image-size'] = strip_tags( $new_instance['image-size'] );
		$instance['link-text'] = strip_tags( $new_instance['link-text'] );

		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? strip_tags( $instance['title'] ) : '';
		$page = isset( $instance['page'] ) ? (int) $instance['page'] : 0;
		$image_size = isset( $instance['image-size'] ) ? strip_tags( $instance['image-size'] ) : 'thumbnail';
		$link_text = isset( $instance['link-text'] ) ? strip_tags( $instance['link-text'] ) : apply_filters( 'afpw_link_text', __( 'Continue reading', 'a-featured-page-widget' ) );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'a-featured-page-widget' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Page:', 'a-featured-page-widget' ); ?></label>

			<?php
			/**
			 *  Mimics wp_dropdown_pages() funcionality to add a 'widefat' class to the <select> tag
			 */
			$args = array(
	            'depth' 				=> 0,
	            'child_of' 				=> 0,
	            'selected' 				=> $page,
	            'name' 					=> $this->get_field_name( 'page' ),
	            'id' 					=> $this->get_field_id( 'page' ),
	            'show_option_none' 		=> '',
	            'show_option_no_change' => '',
	            'option_none_value' 	=> ''
	        );

	        extract( $args, EXTR_SKIP );
	        $pages = get_pages( $args );

	        if ( ! empty( $pages ) ) : ?>
	            <select class="widefat" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>">
	            	<option value="-1"><?php _e( 'Select a page', 'a-featured-page-widget' ); ?></option>
	            	<?php echo walk_page_dropdown_tree( $pages, $depth, $args ) ?>;
	            </select>
	        <?php
	        endif;
	        ?>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image-size' ); ?>"><?php _e( 'Post thumbnail size:', 'a-featured-page-widget' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'image-size' ); ?>" name="<?php echo $this->get_field_name( 'image-size' ); ?>">
				<option value="no-thumbnail" <?php selected( $image_size, 'no-thumbnail' ); ?>><?php _e( 'No post thumbnail, thanks', 'a-featured-page-widget' ); ?></option>
				<?php
				$all_image_sizes = $this->_get_all_image_sizes();
				foreach ( $all_image_sizes as $key => $value ) :
					$image_dimensions = $value['width'] . 'x' . $value['height']; ?>
					<option value="<?php echo $key; ?>" <?php selected( $image_size, $key ); ?>><?php echo $key; ?> (<?php echo $image_dimensions; ?>)</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link-text' ) ); ?>"><?php _e( 'Link text:', 'a-featured-page-widget' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link-text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link-text' ) ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" />
			<small><?php _e( 'If empty, there will be no link to featured page.', 'a-featured-page-widget' ); ?></small>
		</p>
	<?php
	}

	/**
	 * Returns all the registered image sizes along with their dimensions
	 *
	 * @global array $_wp_additional_image_sizes
	 *
	 * @link http://core.trac.wordpress.org/ticket/18947 Reference ticket
	 * @return array $image_sizes The image sizes
	 */
	function _get_all_image_sizes() {
		global $_wp_additional_image_sizes;

		$default_image_sizes = array( 'thumbnail', 'medium', 'large' );

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[$size]['width']	= intval( get_option( "{$size}_size_w") );
			$image_sizes[$size]['height'] = intval( get_option( "{$size}_size_h") );
			$image_sizes[$size]['crop']	= get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
		}

		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
		}

		return $image_sizes;
	}
}
?>