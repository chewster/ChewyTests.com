<?php
/**
 * Posts List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class FA_Posts_List_Table extends WP_List_Table {
	
	private $post_type;
	
	/**
	 * Whether the items should be displayed hierarchically or linearly
	 *
	 * @since 3.1.0
	 * @var bool
	 * @access protected
	 */
	var $hierarchical_display;

	/**
	 * Holds the number of pending comments for each post
	 *
	 * @since 3.1.0
	 * @var int
	 * @access protected
	 */
	var $comment_pending_count;

	/**
	 * Holds the number of posts for this user
	 *
	 * @since 3.1.0
	 * @var int
	 * @access private
	 */
	var $user_posts_count;

	/**
	 * Holds the number of posts which are sticky.
	 *
	 * @since 3.1.0
	 * @var int
	 * @access private
	 */
	var $sticky_posts_count = 0;

	function __construct( $args = array() ) {
		global $post_type_object, $wpdb;
		
		fa_load_admin_style( 'list-tables' );
		
		parent::__construct( array(
			'plural' => 'posts',
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
		) );
		
		if( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) ){
			$this->post_type = $_GET['post_type'];						
		}else{
			$this->post_type = 'post';
		}
		
		$post_type = $this->post_type;
		
		$post_type_object = get_post_type_object( $post_type );

		if ( !current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			$exclude_states = get_post_stati( array( 'show_in_admin_all_list' => false ) );
			$this->user_posts_count = $wpdb->get_var( $wpdb->prepare( "
				SELECT COUNT( 1 ) FROM $wpdb->posts
				WHERE post_type = %s AND post_status NOT IN ( '" . implode( "','", $exclude_states ) . "' )
				AND post_author = %d
			", $post_type, get_current_user_id() ) );

			if ( $this->user_posts_count && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) && empty( $_REQUEST['show_sticky'] ) )
				$_GET['author'] = get_current_user_id();
		}

		if ( 'post' == $post_type && $sticky_posts = get_option( 'sticky_posts' ) ) {
			$sticky_posts = implode( ', ', array_map( 'absint', (array) $sticky_posts ) );
			$this->sticky_posts_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = %s AND post_status NOT IN ('trash', 'auto-draft') AND ID IN ($sticky_posts)", $post_type ) );
		}
	}

	function ajax_user_can() {
		return current_user_can( get_post_type_object( $this->post_type )->cap->edit_posts );
	}

	function prepare_items() {
		global $avail_post_stati, $wp_query, $per_page, $mode;

		$avail_post_stati = wp_edit_posts_query();
		
		$this->hierarchical_display = ( is_post_type_hierarchical( $this->post_type ) && 'menu_order title' == $wp_query->query['orderby'] );

		$total_items = $this->hierarchical_display ? $wp_query->post_count : $wp_query->found_posts;

		$post_type = $this->post_type;
		$per_page = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' );

		if ( $this->hierarchical_display )
			$total_pages = ceil( $total_items / $per_page );
		else
			$total_pages = $wp_query->max_num_pages;

		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

		$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash';

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page
		) );
	}

	function has_items() {
		return have_posts();
	}

	function no_items() {
		if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] )
			echo get_post_type_object( $this->post_type )->labels->not_found_in_trash;
		else
			echo get_post_type_object( $this->post_type )->labels->not_found;
	}

	function get_views() {
		$post_types = array('post', 'page');
		
		$views = array();
		
		if( !isset( $_GET['show_all'] ) ){
			$views['fa_custom_slide'] = fa_option_not_available(' ', false) . sprintf( 
				'<a href="%s" class="%s">%s</a>', 
				'#',
				'',	
				'Custom slides'  
			);
		}
		
		foreach( $post_types as $post_type ){
			$obj = get_post_type_object($post_type);
			$class = $this->post_type == $post_type ? 'current' : '';
			
			$args = array( 
				'post_type' => $post_type
			);
			if( isset( $_GET['show_all'] ) ){
				$args['show_all'] = 'true';
			}			
			$url = fa_iframe_admin_page_url( 'fa-mixed-content-modal', $args, false );			
			$views[ $post_type ] = sprintf( 
				'<a href="%s" class="%s">%s</a>', 
				$url, 
				$class,	
				$obj->labels->name 
			);			
		}		
		
		/**
		 * Action that can be used by third party plugins to add content.
		 * For example, this is used by WPML compatibility to insert the language links
		 */
		do_action( '_fa_posts_table_views', $this->post_type );
		
		return $views;
	}

	function extra_tablenav( $which ) {
		global $cat;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() ) {

			$this->months_dropdown( $this->post_type );

			if ( is_object_in_taxonomy( $this->post_type, 'category' ) ) {
				$dropdown_options = array(
					'show_option_all' => __( 'View all categories' ),
					'hide_empty' => 0,
					'hierarchical' => 1,
					'show_count' => 0,
					'orderby' => 'name',
					'selected' => $cat
				);
				wp_dropdown_categories( $dropdown_options );
			}

			submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) );
		}		
?>
		</div>
<?php
	}

	function pagination( $which ) {
		global $mode;

		parent::pagination( $which );

		if ( 'top' == $which && ! is_post_type_hierarchical( $this->post_type ) )
			$this->view_switcher( $mode );
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', is_post_type_hierarchical( $this->post_type ) ? 'pages' : 'posts' );
	}

	function get_columns() {
		$post_type = $this->post_type;

		$posts_columns = array();

		$posts_columns['cb'] = '<input type="checkbox" />';

		/* translators: manage posts column name */
		$posts_columns['title'] = _x( 'Title', 'column name' );
		$posts_columns['slide_title'] = _x( 'Slide title', 'slide title column name', 'featured-articles-lite' );
		
		if ( post_type_supports( $post_type, 'author' ) )
			$posts_columns['author'] = __( 'Author', 'featured-articles-lite' );

		$taxonomies = array();

		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		$taxonomies = wp_filter_object_list( $taxonomies, array( 'show_admin_column' => true ), 'and', 'name' );

		/**
		 * Filter the taxonomy columns in the Posts list table.
		 *
		 * The dynamic portion of the hook name, $post_type, refers to the post
		 * type slug.
		 *
		 * @since 3.5.0
		 *
		 * @param array  $taxonomies Array of taxonomies to show columns for.
		 * @param string $post_type  The post type.
		 */
		$taxonomies = apply_filters( "manage_taxonomies_for_{$post_type}_columns", $taxonomies, $post_type );
		$taxonomies = array_filter( $taxonomies, 'taxonomy_exists' );

		foreach ( $taxonomies as $taxonomy ) {
			if ( 'category' == $taxonomy )
				$column_key = 'categories';
			elseif ( 'post_tag' == $taxonomy )
				$column_key = 'tags';
			else
				$column_key = 'taxonomy-' . $taxonomy;

			$posts_columns[ $column_key ] = get_taxonomy( $taxonomy )->labels->name;
		}

		$post_status = !empty( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : 'all';
		if ( post_type_supports( $post_type, 'comments' ) && !in_array( $post_status, array( 'pending', 'draft', 'future' ) ) )
			$posts_columns['comments'] = '<span class="vers"><span title="' . esc_attr__( 'Comments' ) . '" class="comment-grey-bubble"></span></span>';

		$posts_columns['date'] = __( 'Date' );

		if ( 'page' == $post_type ) {

			/**
			 * Filter the columns displayed in the Pages list table.
			 *
			 * @since 2.5.0
			 *
			 * @param array $post_columns An array of column names.
			 */
			$posts_columns = apply_filters( 'fa_manage_pages_columns', $posts_columns );
		} else {

			/**
			 * Filter the columns displayed in the Posts list table.
			 *
			 * @since 1.5.0
			 *
			 * @param array  $posts_columns An array of column names.
			 * @param string $post_type     The post type slug.
			 */
			$posts_columns = apply_filters( 'fa_manage_posts_columns', $posts_columns, $post_type );
		}

		/**
		 * Filter the columns displayed in the Posts list table for a specific post type.
		 *
		 * The dynamic portion of the hook name, $post_type, refers to the post type slug.
		 *
		 * @since 3.0.0
		 *
		 * @param array $post_columns An array of column names.
		 */
		$posts_columns = apply_filters( "fa_manage_{$post_type}_posts_columns", $posts_columns );

		return $posts_columns;
	}

	function get_sortable_columns() {
		return array(
			'title'    => 'title',
			'parent'   => 'parent',
			'comments' => 'comment_count',
			'date'     => array( 'date', true )
		);
	}

	function display_rows( $posts = array(), $level = 0 ) {
		global $wp_query, $per_page;

		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		add_filter( 'the_title', 'esc_html' );

		if ( $this->hierarchical_display ) {
			$this->_display_rows_hierarchical( $posts, $this->get_pagenum(), $per_page );
		} else {
			$this->_display_rows( $posts, $level );
		}
	}

	function _display_rows( $posts, $level = 0 ) {
		global $mode;

		// Create array of post IDs.
		$post_ids = array();

		foreach ( $posts as $a_post )
			$post_ids[] = $a_post->ID;

		$this->comment_pending_count = get_pending_comments_num( $post_ids );

		foreach ( $posts as $post )
			$this->single_row( $post, $level );
	}

	function _display_rows_hierarchical( $pages, $pagenum = 1, $per_page = 20 ) {
		global $wpdb;

		$level = 0;

		if ( ! $pages ) {
			$pages = get_pages( array( 'sort_column' => 'menu_order' ) );

			if ( ! $pages )
				return false;
		}

		/*
		 * Arrange pages into two parts: top level pages and children_pages
		 * children_pages is two dimensional array, eg.
		 * children_pages[10][] contains all sub-pages whose parent is 10.
		 * It only takes O( N ) to arrange this and it takes O( 1 ) for subsequent lookup operations
		 * If searching, ignore hierarchy and treat everything as top level
		 */
		if ( empty( $_REQUEST['s'] ) ) {

			$top_level_pages = array();
			$children_pages = array();

			foreach ( $pages as $page ) {

				// catch and repair bad pages
				if ( $page->post_parent == $page->ID ) {
					$page->post_parent = 0;
					$wpdb->update( $wpdb->posts, array( 'post_parent' => 0 ), array( 'ID' => $page->ID ) );
					clean_post_cache( $page );
				}

				if ( 0 == $page->post_parent )
					$top_level_pages[] = $page;
				else
					$children_pages[ $page->post_parent ][] = $page;
			}

			$pages = &$top_level_pages;
		}

		$count = 0;
		$start = ( $pagenum - 1 ) * $per_page;
		$end = $start + $per_page;

		foreach ( $pages as $page ) {
			if ( $count >= $end )
				break;

			if ( $count >= $start ) {
				echo "\t";
				$this->single_row( $page, $level );
			}

			$count++;

			if ( isset( $children_pages ) )
				$this->_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
		}

		// if it is the last pagenum and there are orphaned pages, display them with paging as well
		if ( isset( $children_pages ) && $count < $end ){
			foreach ( $children_pages as $orphans ){
				foreach ( $orphans as $op ) {
					if ( $count >= $end )
						break;

					if ( $count >= $start ) {
						echo "\t";
						$this->single_row( $op, 0 );
					}

					$count++;
				}
			}
		}
	}

	/**
	 * Given a top level page ID, display the nested hierarchy of sub-pages
	 * together with paging support
	 *
	 * @since 3.1.0 (Standalone function exists since 2.6.0)
	 *
	 * @param array $children_pages
	 * @param int $count
	 * @param int $parent
	 * @param int $level
	 * @param int $pagenum
	 * @param int $per_page
	 */
	function _page_rows( &$children_pages, &$count, $parent, $level, $pagenum, $per_page ) {

		if ( ! isset( $children_pages[$parent] ) )
			return;

		$start = ( $pagenum - 1 ) * $per_page;
		$end = $start + $per_page;

		foreach ( $children_pages[$parent] as $page ) {

			if ( $count >= $end )
				break;

			// If the page starts in a subtree, print the parents.
			if ( $count == $start && $page->post_parent > 0 ) {
				$my_parents = array();
				$my_parent = $page->post_parent;
				while ( $my_parent ) {
					$my_parent = get_post( $my_parent );
					$my_parents[] = $my_parent;
					if ( !$my_parent->post_parent )
						break;
					$my_parent = $my_parent->post_parent;
				}
				$num_parents = count( $my_parents );
				while ( $my_parent = array_pop( $my_parents ) ) {
					echo "\t";
					$this->single_row( $my_parent, $level - $num_parents );
					$num_parents--;
				}
			}

			if ( $count >= $start ) {
				echo "\t";
				$this->single_row( $page, $level );
			}

			$count++;

			$this->_page_rows( $children_pages, $count, $page->ID, $level + 1, $pagenum, $per_page );
		}

		unset( $children_pages[$parent] ); //required in order to keep track of orphans
	}

	function single_row( $post, $level = 0 ) {
		global $mode;
		static $alternate;
		
		/**
		 * WP 4.7 passes as parameter in $post a stdClass object for pages.
		 * Get the post to avoid any issues.
		 */
		$post = get_post( $post->ID );
		
		$global_post = get_post();
		$GLOBALS['post'] = $post;
		setup_postdata( $post );

		$edit_link = get_edit_post_link( $post->ID );
		$title = _draft_or_post_title();
		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post = current_user_can( 'edit_post', $post->ID );

		$alternate = 'alternate' == $alternate ? '' : 'alternate';
		$classes = $alternate . ' iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );

		$lock_holder = wp_check_post_lock( $post->ID );
		if ( $lock_holder ) {
			$classes .= ' wp-locked';
			$lock_holder = get_userdata( $lock_holder );
		}

		if ( $post->post_parent ) {
		    $count = count( get_post_ancestors( $post->ID ) );
		    $classes .= ' level-'. $count;
		} else {
		    $classes .= ' level-0';
		}
	?>
		<tr id="post-<?php echo $post->ID; ?>" class="<?php echo implode( ' ', get_post_class( $classes, $post->ID ) ); ?>">
	<?php

		list( $columns, $hidden ) = $this->get_column_info();

		foreach ( $columns as $column_name => $column_display_name ) {
			$class = "class=\"$column_name column-$column_name\"";

			$style = '';
			if ( in_array( $column_name, $hidden ) )
				$style = ' style="display:none;"';

			$attributes = "$class$style";

			switch ( $column_name ) {

			case 'cb':
			?>
			<th scope="row" class="check-column">
				<label class="screen-reader-text" for="cb-select-<?php the_ID(); ?>"><?php printf( __( 'Select %s' ), $title ); ?></label>
				<input id="cb-select-<?php the_ID(); ?>" type="checkbox" name="select_posts[]" value="<?php the_ID(); ?>" data-post_id="<?php the_ID(); ?>" data-post_type="<?php echo $post->post_type;?>" />
				<?php
				if ( $can_edit_post ) {
				?>
				<div class="locked-indicator"></div>
				<?php
				}
				?>
			</th>
			<?php
			break;

			case 'title':
				$attributes = 'class="post-title page-title column-title"' . $style;
				if ( $this->hierarchical_display ) {
					if ( 0 == $level && (int) $post->post_parent > 0 ) {
						//sent level 0 by accident, by default, or because we don't know the actual level
						$find_main_page = (int) $post->post_parent;
						while ( $find_main_page > 0 ) {
							$parent = get_post( $find_main_page );

							if ( is_null( $parent ) )
								break;

							$level++;
							$find_main_page = (int) $parent->post_parent;

							if ( !isset( $parent_name ) ) {
								/** This filter is documented in wp-includes/post-template.php */
								$parent_name = apply_filters( 'the_title', $parent->post_title, $parent->ID );
							}
						}
					}
				}

				$pad = str_repeat( '&#8212; ', $level );
				echo "<td $attributes><strong>";
				
				// post format filtering
				if ( $format = get_post_format( $post->ID ) ) {
					$label = get_post_format_string( $format );

					echo '<a href="' . fa_iframe_admin_page_url('fa-mixed-content-modal', array('post_format' => $format, 'post_type' => $post->post_type), false)  . '" class="post-state-format post-format-icon post-format-' . $format . '" title="' . $label . '">' . $label . ":</a> ";
				}
				
				// Post title with edit link
				if ( $can_edit_post && $post->post_status != 'trash' ) {
					echo '<a target="_blank" class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . $pad . '<span id="fa-name-' . $post->ID . '">' .$title . '</span></a>';
				} else {
					echo $pad . '<span id="fa-name-' . $post->ID . '">' . $title . '</span>';
				}
				_post_states( $post );

				if ( isset( $parent_name ) )
					echo ' | ' . $post_type_object->labels->parent_item_colon . ' ' . esc_html( $parent_name );

				echo "</strong>\n";

				if ( $can_edit_post && $post->post_status != 'trash' ) {
					if ( $lock_holder ) {
						$locked_avatar = get_avatar( $lock_holder->ID, 18 );
						$locked_text = esc_html( sprintf( __( '%s is currently editing' ), $lock_holder->display_name ) );
					} else {
						$locked_avatar = $locked_text = '';
					}

					echo '<div class="locked-info"><span class="locked-avatar">' . $locked_avatar . '</span> <span class="locked-text">' . $locked_text . "</span></div>\n";
				}

				if ( ! $this->hierarchical_display && 'excerpt' == $mode && current_user_can( 'read_post', $post->ID ) )
						the_excerpt();
				
				// actions		
				$actions = array();
				// edit link
				if ( $can_edit_post && 'trash' != $post->post_status ) {
					$actions['edit'] = '<a target="_blank" href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
				}
				
				// View/Preview links
				if ( $post_type_object->public ) {
					if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
						if ( $can_edit_post ) {

							/** This filter is documented in wp-admin/includes/meta-boxes.php */
							$actions['view'] = '<a target="_blank" href="' . esc_url( apply_filters( 'preview_post_link', set_url_scheme( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
						}
					} elseif ( 'trash' != $post->post_status ) {
						$actions['view'] = '<a target="_blank" href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
					}
				}

				if ( is_post_type_hierarchical( $post->post_type ) ) {

					/**
					 * Filter the array of row action links on the Pages list table.
					 *
					 * The filter is evaluated only for hierarchical post types.
					 *
					 * @since 2.8.0
					 *
					 * @param array   $actions An array of row action links. Defaults are
					 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
					 *                         'Delete Permanently', 'Preview', and 'View'.
					 * @param WP_Post $post    The post object.
					 */
					$actions = apply_filters( 'fa_page_row_actions', $actions, $post );
				} else {

					/**
					 * Filter the array of row action links on the Posts list table.
					 *
					 * The filter is evaluated only for non-hierarchical post types.
					 *
					 * @since 2.8.0
					 *
					 * @param array   $actions An array of row action links. Defaults are
					 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
					 *                         'Delete Permanently', 'Preview', and 'View'.
					 * @param WP_Post $post    The post object.
					 */
					$actions = apply_filters( 'fa_post_row_actions', $actions, $post );
				}

				echo $this->row_actions( $actions );

				get_inline_data( $post );
				echo '</td>';
			break;
			
			case 'slide_title':
				$fa_slide = fa_get_slide_options( $post->ID );
				if( isset( $fa_slide['title'] ) ){
					echo '<td ' . $attributes . '>';
					if( $post->post_title != $fa_slide['title'] ){
						echo '<strong>' . $fa_slide['title'] . '</strong>';
					}else{
						echo '<i>' . $fa_slide['title'] . '</i>';
					}					
					echo '</td>';
				}
			break;	
			
			case 'date':
				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$t_time = $h_time = __( 'Unpublished' );
					$time_diff = 0;
				} else {
					$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
					$m_time = $post->post_date;
					$time = get_post_time( 'G', true, $post );

					$time_diff = time() - $time;

					if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS )
						$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
					else
						$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
				}

				echo '<td ' . $attributes . '>';
				if ( 'excerpt' == $mode ) {

					/**
					 * Filter the published time of the post.
					 *
					 * If $mode equals 'excerpt', the published time and date are both displayed.
					 * If $mode equals 'list' (default), the publish date is displayed, with the
					 * time and date together available as an abbreviation definition.
					 *
					 * @since 2.5.1
					 *
					 * @param array   $t_time      The published time.
					 * @param WP_Post $post        Post object.
					 * @param string  $column_name The column name.
					 * @param string  $mode        The list display mode ('excerpt' or 'list').
					 */
					echo apply_filters( 'fa_post_date_column_time', $t_time, $post, $column_name, $mode );
				} else {

					/** This filter is documented in wp-admin/includes/class-wp-posts-list-table.php */
					echo '<abbr title="' . $t_time . '">' . apply_filters( 'fa_post_date_column_time', $h_time, $post, $column_name, $mode ) . '</abbr>';
				}
				echo '<br />';
				if ( 'publish' == $post->post_status ) {
					_e( 'Published' );
				} elseif ( 'future' == $post->post_status ) {
					if ( $time_diff > 0 )
						echo '<strong class="attention">' . __( 'Missed schedule' ) . '</strong>';
					else
						_e( 'Scheduled' );
				} else {
					_e( 'Last Modified' );
				}
				echo '</td>';
			break;

			case 'comments':
			?>
			<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
			<?php
				$pending_comments = isset( $this->comment_pending_count[$post->ID] ) ? $this->comment_pending_count[$post->ID] : 0;
			?>
				<strong class="post-com-count"><span><?php echo $pending_comments;?></span></strong>
			</div></td>
			<?php
			break;

			case 'author':
			?>
			<td <?php echo $attributes ?>><?php
				printf( '<a href="%s">%s</a>',
					fa_iframe_admin_page_url( 'fa-mixed-content-modal', array( 'post_type'=>$post->post_type, 'author' => get_the_author_meta( 'ID' ) ), false ),
					get_the_author()
				);
			?></td>
			<?php
			break;

			default:
				if ( 'categories' == $column_name )
					$taxonomy = 'category';
				elseif ( 'tags' == $column_name )
					$taxonomy = 'post_tag';
				elseif ( 0 === strpos( $column_name, 'taxonomy-' ) )
					$taxonomy = substr( $column_name, 9 );
				else
					$taxonomy = false;

				if ( $taxonomy ) {
					$taxonomy_object = get_taxonomy( $taxonomy );
					echo '<td ' . $attributes . '>';
					if ( $terms = get_the_terms( $post->ID, $taxonomy ) ) {
						$out = array();
						foreach ( $terms as $t ) {
							$posts_in_term_qv = array();
							$posts_in_term_qv['post_type'] = $post->post_type;
							if ( $taxonomy_object->query_var ) {
								$posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
							} else {
								$posts_in_term_qv['taxonomy'] = $taxonomy;
								$posts_in_term_qv['term'] = $t->slug;
							}

							$out[] = sprintf( '<a href="%s">%s</a>',
								fa_iframe_admin_page_url('fa-mixed-content-modal', $posts_in_term_qv, false),
								//esc_url( add_query_arg( $posts_in_term_qv, 'edit.php' ) ),
								esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) )
							);
						}
						/* translators: used between list items, there is a space after the comma */
						echo join( __( ', ' ), $out );
					} else {
						echo '&#8212;';
					}
					echo '</td>';
					break;
				}
			?>
			<td <?php echo $attributes ?>><?php
				if ( is_post_type_hierarchical( $post->post_type ) ) {

					/**
					 * Fires in each custom column on the Posts list table.
					 *
					 * This hook only fires if the current post type is hierarchical,
					 * such as pages.
					 *
					 * @since 2.5.0
					 *
					 * @param string $column_name The name of the column to display.
					 * @param int    $post_id     The current post ID.
					 */
					do_action( 'fa_manage_pages_custom_column', $column_name, $post->ID );
				} else {

					/**
					 * Fires in each custom column in the Posts list table.
					 *
					 * This hook only fires if the current post type is non-hierarchical,
					 * such as posts.
					 *
					 * @since 1.5.0
					 *
					 * @param string $column_name The name of the column to display.
					 * @param int    $post_id     The current post ID.
					 */
					do_action( 'fa_manage_posts_custom_column', $column_name, $post->ID );
				}

				/**
				 * Fires for each custom column of a specific post type in the Posts list table.
				 *
				 * The dynamic portion of the hook name, $post->post_type, refers to the post type.
				 *
				 * @since 3.1.0
				 *
				 * @param string $column_name The name of the column to display.
				 * @param int    $post_id     The current post ID.
				 */
				do_action( "fa_manage_{$post->post_type}_posts_custom_column", $column_name, $post->ID );
			?></td>
			<?php
			break;
			}
		}
	?>
		</tr>
	<?php
		$GLOBALS['post'] = $global_post;
	}

	public function get_post_type(){
		return $this->post_type;
	}
}
