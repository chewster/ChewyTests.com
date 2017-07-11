<?php 
function wpfcas_setup_post_types() {

	$wpfcas_labels =  apply_filters( 'wpfcas_labels', array(
		'name'                => 'Featured Content',
		'singular_name'       => 'Featured Content',
		'add_new'             => __('Add Content', 'featured_post'),
		'add_new_item'        => __('Add Content', 'featured_post'),
		'edit_item'           => __('Edit Content', 'featured_post'),
		'new_item'            => __('New Content', 'featured_post'),
		'all_items'           => __('All Content', 'featured_post'),
		'view_item'           => __('View Content', 'featured_post'),
		'search_items'        => __('Search Content', 'featured_post'),
		'not_found'           => __('No Content found', 'featured_post'),
		'not_found_in_trash'  => __('No Content found in Trash', 'featured_post'),
		'parent_item_colon'   => '',
		'menu_name'           => __('Featured Content', 'featured_post'),
		'exclude_from_search' => true
	) );


	$wpfcas_args = array(
		'labels' 			=> $wpfcas_labels,
		'public' 			=> true,
		'publicly_queryable'		=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'query_var' 		=> true,
		'capability_type' 	=> 'post',
		'has_archive' 		=> true,
		'hierarchical' 		=> false,
		'menu_icon'   => 'dashicons-star-filled',
		'supports' => array('title','editor','thumbnail','excerpt')
		
	);
	register_post_type( 'featured_post', apply_filters( 'wpfcas_post_type_args', $wpfcas_args ) );

}
add_action('init', 'wpfcas_setup_post_types');


/* Register Taxonomy */
add_action( 'init', 'wpfcas_taxonomies');
function wpfcas_taxonomies() {
    $labels = array(
        'name'              => _x( 'Category', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Category' ),
        'all_items'         => __( 'All Category' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Category' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'wpfcas-category' ),
    );

    register_taxonomy( 'wpfcas-category', array( 'featured_post' ), $args );
}

function wpfcas_rewrite_flush() {  
		wpfcas_setup_post_types();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'wpfcas_rewrite_flush' );


// Manage Category Shortcode Columns

add_filter("manage_wpfcas-category_custom_column", 'wpfcas_category_columns', 10, 3);
add_filter("manage_edit-wpfcas-category_columns", 'wpfcas_category_manage_columns'); 
function wpfcas_category_manage_columns($theme_columns) {
    $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => __('Name'),
            'featured_shortcode' => __( 'Category Shortcode', 'featured_post' ),
            'slug' => __('Slug'),
            'posts' => __('Posts')
			);

    return $new_columns;
}

function wpfcas_category_columns($out, $column_name, $theme_id) {
    $theme = get_term($theme_id, 'wpfcas-category');
    switch ($column_name) {      
        case 'title':
            echo get_the_title();
        break;
        case 'featured_shortcode':
			echo '[featured-content cat_id="' . $theme_id. '"]<br />';			  	  
			echo '[featured-content-slider cat_id="' . $theme_id. '"]';
        break;
        default:
            break;
    }
    return $out;   

}



/* Custom meta box for slider link */
function wpfcas_add_meta_box() {
		add_meta_box('custom-metabox',__( 'Add Link For Read More', 'wp-featured-content-and-slider' ),'wpfcas_box_callback','featured_post');
}
add_action( 'add_meta_boxes', 'wpfcas_add_meta_box' );
function wpfcas_box_callback( $post ) {
	wp_nonce_field( 'wpfcas_save_meta_box_data', 'wpfcas_meta_box_nonce' );
	$value = get_post_meta( $post->ID, 'wpfcas_slide_link', true );
	echo '<input type="url" id="wpfcas_slide_link" name="wpfcas_slide_link" value="' . esc_attr( $value ) . '" size="25" /><br />';
	echo 'ie http://www.google.com';
}
function wpfcas_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['wpfcas_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['wpfcas_meta_box_nonce'], 'wpfcas_save_meta_box_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'featured_post' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	if ( ! isset( $_POST['wpfcas_slide_link'] ) ) {
		return;
	}
	$link_data = sanitize_text_field( $_POST['wpfcas_slide_link'] );
	update_post_meta( $post_id, 'wpfcas_slide_link', $link_data );
}
add_action( 'save_post', 'wpfcas_save_meta_box_data' );

function wpfcas_icon_add_meta_box() {
		add_meta_box('custom-metabox-icon',__( 'Add Font Awesome (If not adding Featured Image)', 'wp-featured-content-and-slider' ),'wpfcas_icon_box_callback','featured_post','side');
}
add_action( 'add_meta_boxes', 'wpfcas_icon_add_meta_box' );

function wpfcas_icon_box_callback( $post ) {
	wp_nonce_field( 'wpfcas_icon_save_meta_box_data', 'icon_meta_box_nonce' );
	$value = get_post_meta( $post->ID, 'wpfcas_slide_icon', true );
	echo '<input type="text" id="wpfcas_slide_icon" name="wpfcas_slide_icon" value="' . esc_attr( $value ) . '" size="25" />';
	echo '<p><strong>For example : fa fa-bluetooth-b</strong></p>';
	echo 'Get icon class details : <a href="https://fortawesome.github.io/Font-Awesome/icons/" target="_blank"> Font Awesome</a>';
}
function wpfcas_icon_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['icon_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['icon_meta_box_nonce'], 'wpfcas_icon_save_meta_box_data' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'featured_post' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['wpfcas_slide_icon'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['wpfcas_slide_icon'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, 'wpfcas_slide_icon', $my_data );
}
add_action( 'save_post', 'wpfcas_icon_save_meta_box_data' );



function wpfcas_limit_words($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}
