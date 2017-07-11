<?php
/**
 * Load WP_List_Table class
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Terms List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class FA_Taxonomies_List_Table extends WP_List_Table {

	var $taxonomy = 'category';
	var $post_type = 'post';

	function __construct( $args = array() ) {
		global $post_type, $taxonomy, $action, $tax;
        
		fa_load_admin_style( 'list-tables' );
		
		parent::__construct( array(
			'plural' 	=> 'tags',
			'singular' 	=> 'tag',
			'screen' 	=> isset( $args['screen'] ) ? $args['screen'] : null,
		) );
		
		if( isset( $_GET['pt'] ) && post_type_exists( $_GET['pt'] ) ){
			if( isset( $_GET['tax'] ) && taxonomy_exists( $_GET['tax'] ) ){
				$this->post_type = $_GET['pt'];
				$this->taxonomy = $_GET['tax'];	
			}			
		}
			
	}

	function prepare_items() {
		$tags_per_page = $this->get_items_per_page( 'edit_' . $this->taxonomy . '_per_page' );
		$search = !empty( $_REQUEST['s'] ) ? trim( wp_unslash( $_REQUEST['s'] ) ) : '';

		$args = array(
			'search'	=> $search,
			'page' 		=> $this->get_pagenum(),
			'number' 	=> $tags_per_page,
		);

		if ( !empty( $_REQUEST['orderby'] ) )
			$args['orderby'] = trim( wp_unslash( $_REQUEST['orderby'] ) );

		if ( !empty( $_REQUEST['order'] ) )
			$args['order'] = trim( wp_unslash( $_REQUEST['order'] ) );

		$this->callback_args = $args;

		$this->set_pagination_args( array(
			'total_items' => wp_count_terms( $this->taxonomy, compact( 'search' ) ),
			'per_page' => $tags_per_page,
		) );
	}

	function has_items() {
		// todo: populate $this->items in prepare_items()
		return true;
	}

	function get_bulk_actions() {
		return array();
	}

	function current_action() {
		if ( isset( $_REQUEST['action'] ) && isset( $_REQUEST['delete_tags'] ) && ( 'delete' == $_REQUEST['action'] || 'delete' == $_REQUEST['action2'] ) )
			return 'bulk-delete';

		return parent::current_action();
	}
	
	function get_views(){
	    
	    /**
	     * Action that can be used by third party plugins to insert extra views.
	     * For example, this is used by WPML compatibility layer to insert the language switcher.
	     */
	    do_action( '_fa_taxonomies_table_views', $this->post_type, $this->taxonomy );
	    
		return;
	}
	
	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'name'        => _x( 'Name', 'term name' ),
			'description' => __( 'Description' ),
			'slug'        => __( 'Slug' ),
			'posts'		  => __('Posts')
		);

		return $columns;
	}

	function get_sortable_columns() {
		return array(
			'name'        => 'name',
			'description' => 'description',
			'slug'        => 'slug',
			'posts'       => 'count'
		);
	}

	function display_rows_or_placeholder() {
		$taxonomy = $this->taxonomy;

		$args = wp_parse_args( $this->callback_args, array(
			'page' => $this->get_pagenum(),
			'number' => 20,
			'search' => '',
			'hide_empty' => 0
		) );

		extract( $args, EXTR_SKIP );

		$args['offset'] = $offset = ( $page - 1 ) * $number;

		// convert it to table rows
		$count = 0;

		$terms = array();

		if ( is_taxonomy_hierarchical( $taxonomy ) && !isset( $orderby ) ) {
			// We'll need the full set of terms then.
			$args['number'] = $args['offset'] = 0;
		}
		
		$terms = get_terms( $taxonomy, $args );

		if ( empty( $terms ) ) {
			list( $columns, $hidden ) = $this->get_column_info();
			echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
			$this->no_items();
			echo '</td></tr>';
			return;
		}

		if ( is_taxonomy_hierarchical( $taxonomy ) && !isset( $orderby ) ) {
			if ( !empty( $search ) ) // Ignore children on searches.
				$children = array();
			else
				$children = _get_term_hierarchy( $taxonomy );

			// Some funky recursion to get the job done( Paging & parents mainly ) is contained within, Skip it for non-hierarchical taxonomies for performance sake
			$this->_rows( $taxonomy, $terms, $children, $offset, $number, $count );
		} else {
			$terms = get_terms( $taxonomy, $args );
			foreach ( $terms as $term )
				$this->single_row( $term );
			$count = $number; // Only displaying a single page.
		}
	}

	function _rows( $taxonomy, $terms, &$children, $start, $per_page, &$count, $parent = 0, $level = 0 ) {

		$end = $start + $per_page;

		foreach ( $terms as $key => $term ) {

			if ( $count >= $end )
				break;

			if ( $term->parent != $parent && empty( $_REQUEST['s'] ) )
				continue;

			// If the page starts in a subtree, print the parents.
			if ( $count == $start && $term->parent > 0 && empty( $_REQUEST['s'] ) ) {
				$my_parents = $parent_ids = array();
				$p = $term->parent;
				while ( $p ) {
					$my_parent = get_term( $p, $taxonomy );
					$my_parents[] = $my_parent;
					$p = $my_parent->parent;
					if ( in_array( $p, $parent_ids ) ) // Prevent parent loops.
						break;
					$parent_ids[] = $p;
				}
				unset( $parent_ids );

				$num_parents = count( $my_parents );
				while ( $my_parent = array_pop( $my_parents ) ) {
					echo "\t";
					$this->single_row( $my_parent, $level - $num_parents );
					$num_parents--;
				}
			}

			if ( $count >= $start ) {
				echo "\t";
				$this->single_row( $term, $level );
			}

			++$count;

			unset( $terms[$key] );

			if ( isset( $children[$term->term_id] ) && empty( $_REQUEST['s'] ) )
				$this->_rows( $taxonomy, $terms, $children, $start, $per_page, $count, $term->term_id, $level + 1 );
		}
	}

	function single_row( $tag, $level = 0 ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		$this->level = $level;

		echo '<tr id="tag-' . $tag->term_id . '"' . $row_class . '>';
		$this->single_row_columns( $tag );
		echo '</tr>';
	}

	function column_cb( $tag ) {
		$default_term = get_option( 'default_' . $this->taxonomy );
		return '<label class="screen-reader-text" for="cb-select-' . $tag->term_id . '">' . sprintf( __( 'Select %s' ), $tag->name ) . '</label>'
				. '<input type="checkbox" data-term_id="' . $tag->term_id . '" data-post_type="' . $this->post_type . '" data-taxonomy="' . $this->taxonomy . '" name="select_tags[]" value="' . $tag->term_id . '" id="cb-select-' . $tag->term_id . '" />';		
	}

	function column_name( $tag ) {
		$taxonomy = $this->taxonomy;
		$tax = get_taxonomy( $taxonomy );

		$default_term = get_option( 'default_' . $taxonomy );

		$pad = str_repeat( '&#8212; ', max( 0, $this->level ) );
		
		$name = $pad . ' ' . '<label for="cb-select-' . $tag->term_id . '"><span id="fa-name-' . $tag->term_id . '">' . $tag->name . '</span></label>';
		return $name;
	}

	function column_description( $tag ) {
		return $tag->description;
	}

	function column_slug( $tag ) {
		/** This filter is documented in wp-admin/edit-tag-form.php */
		return apply_filters( 'editable_slug', $tag->slug );
	}

	function column_posts( $tag ) {
		$count = number_format_i18n( $tag->count );

		$tax = get_taxonomy( $this->taxonomy );

		$ptype_object = get_post_type_object( $this->post_type );
		if ( ! $ptype_object->show_ui )
			return $count;

		if ( $tax->query_var ) {
			$args = array( $tax->query_var => $tag->slug );
		} else {
			$args = array( 'taxonomy' => $tax->name, 'term' => $tag->slug );
		}

		if ( 'post' != $this->post_type )
			$args['post_type'] = $this->post_type;

		return "<a target='_blank' href='" . esc_url ( add_query_arg( $args, 'edit.php' ) ) . "'>$count</a>";
	}

	function column_default( $tag, $column_name ) {
		/**
		 * Filter the displayed columns in the terms list table.
		 *
		 * The dynamic portion of the hook name, $this->screen->taxonomy,
		 * refers to the slug of the current taxonomy.
		 *
		 * @since 2.8.0
		 *
		 * @param string $string      Blank string.
		 * @param string $column_name Name of the column.
		 * @param int    $term_id     Term ID.
		 */
		return apply_filters( "fa-manage_{$this->taxonomy}_custom_column", '', $column_name, $tag->term_id );
	}
	
	function get_post_type(){
		return $this->post_type;
	}
	
	function get_taxonomy(){
		return $this->taxonomy;
	}
}
