<?php
if( !class_exists( 'FA_Ajax_Actions' ) ){
	require_once fa_get_path( 'includes/admin/libs/class-fa-ajax-actions.php' );
}

/**
 * Implements the administration functionality
 *
 */
class FA_Admin extends FA_Custom_Post_Type{
	/**
	 * Stores WP errors when processing data
	 * @var WP_Error
	 */
	private $errors;
	
	/**
	 * Prefix on meta boxes ids added by the plugin; used
	 * to identify them.
	 * @var string
	 */
	private $meta_box_prefix = 'fapro';
	
	/**
	 * Reference for WP Ajax actions class
	 * @var object
	 */
	private $wp_ajax;
	
	/**
	 * Constructor
	 */
	public function __construct(){
		
		parent::__construct();
		
		// start ajax actions
		$this->wp_ajax = new FA_Ajax_Actions();
		// check for iframe plugin variables and set the according variables needed by WordPress
		add_action('admin_init', array( $this, 'is_iframe' ), -9999);
		// check for previews
		add_action('admin_init', array( $this, 'is_preview' ), -9999);
		
		// admin menu
		add_action('admin_menu', array($this, 'admin_menu'), 1);
		// add scripts
		add_action('load-post.php', array( $this, 'post_edit_assets' ));
		add_action('load-post-new.php', array( $this, 'post_edit_assets' ));
		
		// remove autosave script from slider editing screen
		add_action( 'admin_enqueue_scripts', array( $this, 'dequeue_slider_autosave' ) );
		
		// save slide data
		add_action('save_post', array( $this, 'save_slide' ), 1, 3);
				
		// save slider data
		add_action('save_post_' . parent::get_type_slider(), array( $this, 'save_slider' ), 10, 3);	
		// save slider revisions (for preview purposes)
		add_action('save_post_revision', array( $this, 'save_slider_revisions' ), 10, 3);
		
		// detect images in post contents when saving post
		add_action('save_post', array( $this, 'detect_image' ), 10, 3);
		
		// add extra columns on sliders table
		add_filter( 'manage_' . parent::get_type_slider() . '_posts_columns', array( $this, 'extra_slider_columns' ), 9999 );
		add_action( 'manage_'. parent::get_type_slider() .'_posts_custom_column', array($this, 'output_extra_slider_columns'), 10, 2 );	
		
		// add meta boxes for slider post type
		add_action( 'fa_meta_box_cb_' . parent::get_type_slider(), array( $this, 'register_slider_meta_boxes' ) );
				
		// remove all metaboxes except the ones implemented by the plugin and the default allowed ones
		add_action('screen_options_show_screen', array( $this, 'remove_meta_boxes' ));
		
		// add slide edit meta box on post and page editing
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ), 10, 2 );
		
		// tinymce
		add_action('admin_head', array( $this, 'tinymce' ) );
		add_filter('mce_external_languages', array( $this, 'tinymce_languages' ) );
		
		// for WP version prior to WP 4, use alternative for slider shortcode tinyMCE button
		if( version_compare( get_bloginfo( 'version' ), '4', '<' ) ){
			// register shortcode meta box if version smaller than 4
			add_action( 'add_meta_boxes', array( $this, 'register_slider_shortcode_meta_box' ), 10, 2 );
		}
		
		add_filter( 'enter_title_here', array( $this, 'post_title_label' ), 999, 2);		
		add_filter( 'preview_post_link', array( $this, 'slider_preview_link' ), 999, 1 );

		// add a filter to detect if FA PRO is installed and remove activation link and add a message
		add_filter('plugin_row_meta', array( $this, 'plugin_meta' ), 10, 2);
		add_filter('plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2);

		add_filter( 'admin_footer_text', array( $this, 'admin_footer_message' ) );
	}
	
	/**
	 * Modify post preview link with our own implementation.
	 * When user hits preview theme from slider settings, this is the URL he will be taken to.
	 * 
	 * @param string $url
	 */
	public function slider_preview_link( $url ){
		global $post;
		if( !$post || parent::get_type_slider() != $post->post_type ){
			return $url;
		}
		
		$preview_args = array(
			'post_id' 	=> $post->ID,
			'theme' 	=> ( isset( $_POST['theme']['active'] ) ? $_POST['theme']['active'] : '' ),
			'vars'		=> array(
				'color' => ( isset( $_POST['theme']['color'] ) ? $_POST['theme']['color'] : '' )
			),
			'echo'		=> false
		);
		$url = fa_slider_preview_homepage( $preview_args );
		return htmlspecialchars_decode( $url );
	}
	
	/**
	 * Check if iframe request was issued
	 */
	public function is_iframe(){
		if( isset($_GET['fapro_inline']) ){
			$_GET['noheader'] = true;
			if( !defined( 'IFRAME_REQUEST' ) ){
				define( 'IFRAME_REQUEST', true );
			}
			if( !defined( 'FAPRO_IFRAME' ) ){
				define( 'FAPRO_IFRAME', true );
			}
			
			fa_load_admin_style('iframe');	
		}		
	}
	
	/**
	 * Init callback that checks if a preview should be displayed.
	 * Verifies only front-end pages.
	 * Adds filter loop_start to display the slider preview.
	 */
	public function is_preview(){
		
		// previews not available on admin pages
		if( is_admin() ){
			return;
		}
		// check for preview variable
		if( !fa_is_preview() ){
			// user must be capabile of editing fa items
			if( !current_user_can('edit_fa_items') ){
				wp_die( __('Not allowed.', 'featured-articles-lite') );
			}			
			return;
		}
		
		check_admin_referer( 'fa-slider-theme-preview', 'fa-preview-nonce' );	
	}
	
	/**
	 * Remove all other metaboxes from slider and slide edit screen except the ones
	 * added by the plugin and the default WP meta boxes
	 * @param object $post
	 */
	public function remove_meta_boxes( $show_screen, $check_screen = true ){		
		$screen = get_current_screen();
		$page 	= $screen->id;
		
		if( $check_screen ){
			// apply this only for sliders and slides
			if( parent::get_type_slider() != $page ){
				return $show_screen;			
			}
		}
		
		global $wp_meta_boxes;
		$default_meta_boxes = array( 
			'submitdiv', 
			'postimagediv', 
			'authordiv'
		);
		
		// loop all contexts
		foreach( array('side', 'normal', 'advanced') as $context ){
			// if context is missing, skip it
			if( !isset( $wp_meta_boxes[ $page ][ $context ] ) ){
				continue;
			}
			// loop priorities
			foreach ( array('high', 'core', 'default', 'low') as $priority ){
				// if priority is missing, skip it
				if( !isset( $wp_meta_boxes[ $page ][ $context ][ $priority ] ) ){
					continue;
				}
				// loop registered meta boxes
				foreach( $wp_meta_boxes[ $page ][ $context ][ $priority ] as $id => $meta_box ){
					// if plugin meta box, keep it
					if( $this->meta_box_prefix === substr( $id, 0, strlen( $this->meta_box_prefix ) ) ){
						continue;						
					}
					// if default wp meta box, keep it
					if( in_array( $id, $default_meta_boxes) ){
						continue;
					}	
					// remove all other meta boxes		
					$wp_meta_boxes[ $page ][ $context ][ $priority ][ $id ] = false;	
				}				
			}					
		}
		return $show_screen;		
	}
	
	/**
	 * Admin menu setup
	 */
	public function admin_menu(){
		// get slide object post type to retrieve labels
		$parent_slug 	= 'edit.php?post_type=' . parent::get_type_slider();
		
		$this->pro_menu_pages( $parent_slug );
		
		// Plugin settings menu page
		$settings = add_submenu_page(
			$parent_slug, 
			__('Settings', 'featured-articles-lite'),
			__('Settings', 'featured-articles-lite'), 
			'manage_options', 
			'fapro_settings',
			array($this, 'page_settings')
		);
		// load action for plugin settings page
		add_action( 'load-' . $settings, array( $this, 'on_page_settings_load' ) );
		
		$tax_modal = add_submenu_page(
			null, 
			'', 
			'', 
			'edit_fa_items', 
			'fa-tax-modal',
			array( $this, 'modal_taxonomy' ));
		
		$mixed_modal = add_submenu_page(
			null, 
			'', 
			'', 
			'edit_fa_items', 
			'fa-mixed-content-modal',
			array( $this, 'modal_mixed_content' ));	
		add_action('load-' . $mixed_modal, array( $this, 'on_slides_modal_load' ) );
			
		$mixed_slide_edit = add_submenu_page(
			null,
			'',
			'',
			'edit_fa_items',
			'fa-post-slide-edit',
			array( $this, 'modal_mixed_slide_edit' )
		);	
		add_action('load-' . $mixed_slide_edit, array( $this, 'on_slide_modal_load' ) );
	}
	
	private function pro_menu_pages( $parent_slug ){
		$options = fa_get_options( 'settings' );
		if( $options['lite_admin_menu'] ){
			return;
		}
		
		// slides list menu page
		$slides = add_submenu_page(
			$parent_slug,
			__('Custom slides (PRO)', 'featured-articles-lite'),  
			__('Custom slides <span class="awaiting-mod"><span class="pending-count">PRO</span></span>', 'featured-articles-lite'), 
			'edit_fa_items', 
			'custom_slides_list',
			array( $this, 'custom_slides' )
		);
		
		// new slide menu page
		$new_slide = add_submenu_page(
			$parent_slug, 
			__('New slide (PRO)', 'featured-articles-lite'), 
			__('New slide <span class="awaiting-mod"><span class="pending-count">PRO</span></span>', 'featured-articles-lite'), 
			'edit_fa_items', 
			'custom_slide_add',
			array( $this, 'custom_slide_add' )
		);
		
		// slides taxonomy
		$slide_groups = add_submenu_page(
			$parent_slug, 
			__('Slide groups (PRO)', 'featured-articles-lite'), 
			__('Slide groups <span class="awaiting-mod"><span class="pending-count">PRO</span></span>', 'featured-articles-lite'), 
			'manage_fa_terms', 
			'custom_slide_groups',
			array( $this, 'custom_slide_groups' )
		);
				
		$dynamic_areas = add_submenu_page(
			$parent_slug, 
			__('Dynamic areas (PRO)', 'featured-articles-lite'), 
			__('Dynamic areas <span class="awaiting-mod"><span class="pending-count">PRO</span></span>', 'featured-articles-lite'), 
			'manage_options', 
			'fapro_hooks',
			array( $this, 'dynamic_slider_areas' )
		);
		
		$themes_edit = add_submenu_page(
			$parent_slug, 
			__('Themes (PRO)', 'featured-articles-lite'), 
			__('Themes <span class="awaiting-mod"><span class="pending-count">PRO</span></span>', 'featured-articles-lite'), 
			'manage_options', 
			'fapro_themes',
			array($this, 'themes_manager')
		);		
	}
	
	public function custom_slides(){
		$template = fa_template_path('custom-slides');
		include_once $template;
	}
	
	public function custom_slide_add(){
		$template = fa_template_path('custom-slide-add');
		include_once $template;	
	}
	
	public function custom_slide_groups(){
		$template = fa_template_path('custom-slide-groups');
		include_once $template;
	}
	
	/**
	 * Dynamic slider areas admin page
	 */
	public function dynamic_slider_areas(){
		$template = fa_template_path('dynamic-slider-areas');
		include_once $template;
	}
	
	/**
	 * Themes color editor admin page
	 */
	public function themes_manager(){
		$template = fa_template_path('themes');
		include_once $template;
	}
	
	/**
	 * Modal taxonomy iframe
	 */
	public function modal_taxonomy(){
		if( defined('FAPRO_IFRAME') ){
			iframe_header();
		}		
		
		require_once fa_get_path('includes/admin/libs/class-fa-taxonomies-list-table.php');
		$tbl = new FA_Taxonomies_List_Table();
		$tbl->prepare_items();
		?>
		<?php $tbl->views();?>
		<form method="get" action="">
			<input type="hidden" name="page" value="fa-tax-modal" />
			<input type="hidden" name="fapro_inline" value="true" />
			<input type="hidden" name="pt" value="<?php echo $tbl->get_post_type();?>" />
			<input type="hidden" name="tax" value="<?php echo $tbl->get_taxonomy();?>" />		
			<?php $tbl->search_box( __('search', 'featured-articles-lite'), 'id');?>
		</form>
		<?php $tbl->display();?>
		<?php
		if( defined('FAPRO_IFRAME') ){
			iframe_footer();
			exit();
		}
	}
	
	public function on_slides_modal_load(){
		fa_load_admin_style( 'slides-list-table-modal' );
	}
	
	/**
	 * Mixed slider content selection modal iframe
	 */
	public function modal_mixed_content(){
		if( defined('FAPRO_IFRAME') ){
			iframe_header();
		}
		
		require_once fa_get_path('includes/admin/libs/class-fa-posts-list-table.php');
		$tbl = new FA_Posts_List_Table();
		$tbl->prepare_items();
		?>
		<form method="get" action="">
			<input type="hidden" name="page" value="fa-mixed-content-modal" />
			<input type="hidden" name="fapro_inline" value="true" />
			<input type="hidden" name="post_type" value="<?php echo $tbl->get_post_type();?>" />			
			<?php $tbl->views();?>
			<?php $tbl->search_box(__('search', 'featured-articles-lite'), 'id');?>
			<?php $tbl->display();?>
		</form>
		<?php 
		if( defined('FAPRO_IFRAME') ){
			iframe_footer();
			exit();
		}		
	}
	
	/**
	 * Register meta boxes on post and page editing
	 */
	public function register_meta_boxes( $post_type, $post ){
		if( !in_array( $post_type ,  array('post', 'page') ) || !fa_allowed_slide_edit() ){
			return;
		}	
		
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slide-settings', 
			__('Featured Articles Lite - slide settings', 'featured-articles-lite'), 
			array( $this, 'meta_box_slide_settings' ));
		
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slide-video-query', 
			__('Featured Articles Lite - video', 'featured-articles-lite'), 
			array( $this, 'meta_box_slide_video' ),
			null,
			'side');		
	}
		
	/**
	 * Slide modal edit load page callback
	 */
	public function on_slide_modal_load(){
		
		if( !isset( $_GET['post_id'] ) ){
			wp_die(-1);
		}
		
		$post_id = absint( $_GET['post_id'] );		
		if( !current_user_can('edit_fa_items', $post_id) ){
			wp_die(-1);
		}
		
		$screen = get_current_screen();
		global $post;
		$post = get_post( $post_id );
		require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );
		
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slide-settings', 
			__('Featured Articles PRO - slide settings', 'featured-articles-lite'), 
			array( $this, 'meta_box_slide_settings' ),
			$screen->id,
			'advanced',
			'default' );
		
		
		// submitdiv	
		add_meta_box( 
			'submitdiv', 
			__( 'Publish' ), 
			'post_submit_meta_box', 
			$screen->id, 
			'side', 
			'core'
		);		
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slide-video-query', 
			__('Featured Articles PRO - video', 'featured-articles-lite'), 
			array( $this, 'meta_box_slide_video' ),
			$screen->id,
			'side',
			'default' );	
		
		add_meta_box(
			'postimagediv', 
			__('Featured Image'), 
			array( $this, 'post_thumbnail_meta_box' ), 
			$screen->id, 
			'side', 
			'low'
		);			
			
			
		$this->load_slide_assets();	
		wp_enqueue_script( 'post' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'jquery-cookie' );
		
		// Save slide settings
		// check for nonce presence
		if( !isset( $_POST['fa-slide-modal-settings-nonce'] ) ){
			return;
		}
		
		$post = get_post( $post_id );
		check_admin_referer( 'fa-slide-modal-options-save', 'fa-slide-modal-settings-nonce' );
		
		// update options not needed. Update performed on save_post hook in edit_post() above
		edit_post();
		
		/**
		 * Action on slide save
		 * @param int $post_id - ID of post being saved
		 * @param object $post - object of post being saved
		 * @param bool $update - is update or new post
		 */
		do_action('fa-save-slide', $post_id, $post, true);		
	}
	
	/**
	 * Slide settings meta box callback. Allows setting link more text, url and others
	 * @param object $post - current post being edited
	 */
	public function meta_box_slide_settings( $post ){
		// get the slide options
		$options = fa_get_slide_options( $post->ID );
		
		$template = fa_metabox_path('slide-settings');
		include_once $template;
	}
	
	/**
	 * Slide video attachment meta box. Allows attaching video content to slides.
	 * @param object $post - current post being edited
	 */
	public function meta_box_slide_video( $post ){
		// get the slide options
		$options = fa_get_slide_options( $post->ID );		
		$template = fa_metabox_path('slide-video-query');
		include_once $template;
	}
	
	/**
	 * Display featured image meta box on slide edit modal
	 */
	public function post_thumbnail_meta_box( $post ){
		remove_all_filters('admin_post_thumbnail_html');
		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
		echo _wp_post_thumbnail_html( $thumbnail_id, $post->ID );
	}
	
	/**
	 * Load JavaScript needed on slide editing screen.
	 * Used by function $this->post_edit_assets.
	 */
	private function load_slide_assets(){
		fa_load_admin_style('slide-edit');
		$handle = fa_load_admin_script( 'slide-edit', array( 'jquery' ) );
		
		wp_localize_script( $handle, 'faEditSlide', array(
			'id_prefix' 	=> $this->meta_box_prefix, // meta boxes prefix
			
			'remove_image_nonce' 	=> $this->wp_ajax->get_nonce('remove_slide_image'),
			'remove_image_action' 	=> $this->wp_ajax->get_action('remove_slide_image'),
		
			'messages' => array()	
		));

		wp_localize_script( $handle, 'faEditSlider', array(
			'assign_image_nonce' 		=> $this->wp_ajax->get_nonce('assign_slide_image'),	
			'assign_image_ajax_action' 	=> $this->wp_ajax->get_action('assign_slide_image'),			
		));
		
	}
	
	/**
	 * Mixed content slide edit details modal
	 */
	public function modal_mixed_slide_edit(){
		if( defined('FAPRO_IFRAME') ){
			iframe_header();
		}
		
		$post_id = absint( $_GET['post_id'] );
		$post = get_post( $post_id );
		if( !$post ){
			wp_die( __('Post not found.', 'featured-articles-lite') );			
		}
		
		$slider_id = absint( $_GET['slider_id'] );
		$slider = get_post( $slider_id );
		if( !$slider ){
			wp_die( __('Slider not found.', 'featured-articles-lite') );
		}
		
		$options = fa_get_slide_options( $post_id );
		
		if( 'auto-draft' == $post->post_status ){
			$post->post_title = '';
			$options['title'] = '';
		}
		
		$screen = get_current_screen();
		$screen_id = $screen->id;
		
		$this->remove_meta_boxes( null, false );
		
		$modal = fa_modal_path('slide-settings');
		include_once $modal;
		 
		if( defined('FAPRO_IFRAME') ){
			iframe_footer();
			exit();
		}
	}
	
	/**
	 * Loads assets on slide/slider post type edit
	 */
	public function post_edit_assets(){
		$screen = get_current_screen();
		$page = $screen->id;
		
		// load different things for sliders
		if( parent::get_type_slider() == $page ){
			$this->load_slider_assets();
			return;			
		}

		// allow slide edit scripts on all allowed post types
		if( in_array( $page, array('post', 'page') ) ){
			// if not allowed to edit slides and not type slide, return
			if( !fa_allowed_slide_edit() ){
				return;
			}			
			$this->load_slide_assets();
			return;
		}	
	}
	
	/**
	 * Dequeue autosave.js from slider edit screen
	 */
	public function dequeue_slider_autosave(){
		if( parent::get_type_slider() == get_post_type() ){
			wp_dequeue_script('autosave');
		}
	}
	
	/**
	 * Load JavaScript needed on slider editing screen.
	 * Used by function $this->post_edit_assets.
	 */
	private function load_slider_assets(){
		fa_load_admin_style('slider-edit');
		wp_enqueue_style('media-views');
		$modal = fa_load_admin_script('modal');		
		$handle = fa_load_admin_script('slider-edit', array( $modal, 'jquery-ui-tabs' ,'jquery-ui-sortable', 'jquery' ));
		
		wp_localize_script( $handle, 'faEditSlider', array(
			'assign_slide_wp_nonce' 	=> $this->wp_ajax->get_nonce('assign_slide'),
			'assign_slide_ajax_action' 	=> $this->wp_ajax->get_action('assign_slide'),
			
			'messages' => array(
				'close_modal' 		=> __('Close', 'featured-articles-lite'),
				'title_slides' 		=> __('Choose slides', 'featured-articles-lite'),
				'title_edit_post' 	=> __('Edit slide options', 'featured-articles-lite'),
				'title_categories' 	=> __('Choose categories', 'featured-articles-lite')
			)
		));
		
		// Add the action to the footer to output the modal window.
        add_action( 'admin_footer', array( $this, 'tax_selection_modal' ) );
	}
	
	public function tax_selection_modal(){
?>
<div class="fapro-default-ui-wrapper" id="fapro-modal" style="display: none;">
	<div class="fapro-default-ui">
		<div class="media-modal wp-core-ui">
			<a class="media-modal-close" href="#"><span class="media-modal-icon"></span></a>
			<div class="media-modal-content">
				<div class="media-frame wp-core-ui hide-menu hide-router fapro-meta-wrap">
					<div class="media-frame-title">
						<h1 data-title="<?php echo esc_attr( __('Choose', 'featured-articles-lite') );?>"><?php _e( 'Choose', 'featured-articles-lite' ); ?></h1>
					</div>
					<div class="media-frame-content">
		            	<!-- Injected by functions -->   
					</div><!-- .media-frame-content -->
					<div class="media-frame-toolbar">
						<div class="media-toolbar">
							<div class="media-toolbar-secondary">
								<a href="#" class="fapro-cancel-action button media-button button-large button-secondary media-button-insert" title="<?php esc_attr_e( 'Cancel', 'featured-articles-lite' ); ?>"><?php _e( 'Cancel', 'featured-articles-lite' ); ?></a>
							</div>
							<div class="media-toolbar-primary">
								<a href="#" class="fapro-make-action button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save', 'featured-articles-lite' ); ?>"><?php _e( 'Save', 'featured-articles-lite' ); ?></a>
							</div><!-- .media-toolbar-primary -->
						</div><!-- .media-toolbar -->
					</div><!-- .media-frame-toolbar -->					
				</div><!-- .media-frame -->		     	                                           
			</div><!-- .media-modal-content -->
		</div><!-- .media-modal -->
		<div class="media-modal-backdrop"></div>
	</div><!-- #fapro-default-ui -->
</div><!-- #fapro-default-ui-wrapper -->
<?php 				
	}
	
	/**
	 * Meta box output for slider post type.
	 * Prefix all plugin meta boxes with $this->meta_box_prefix-NAME to avoid having them removed by
	 * function $this->remove_meta_boxes
	 */
	public function register_slider_meta_boxes( $post ){
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slider-settings', 
			__('Slider content', 'featured-articles-lite'), 
			array( $this, 'meta_box_slider_content' ),
			null,
			'normal'
		);
		// slide theme meta box
		add_meta_box(
			$this->meta_box_prefix . '-slider-theme', 
			__('Slider output', 'featured-articles-lite'), 
			array( $this, 'meta_box_slider_theme' ),
			null,
			'normal'
		);	
		
		// slide details meta box
		add_meta_box(
			$this->meta_box_prefix . '-slider-options', 
			__('Slider options', 'featured-articles-lite'), 
			array( $this, 'meta_box_slider_options' ),
			null,
			'side'
		);
		
		// help links meta box
		add_meta_box(
			$this->meta_box_prefix . '-codeflavors-docs',
			__('Docs & Help', 'featured-articles-lite'  ),
			array( $this, 'meta_box_docs' ),
			null,
			'side'
		);
		
		// slider PHP code
		add_meta_box(
			$this->meta_box_prefix . '-slider-code', 
			__('Slider PHP code', 'featured-articles-lite'), 
			array( $this, 'meta_box_slider_code' ),
			null,
			'side'
		);	
		
		// add the expiration date and other to post submitbox for slider
		add_action('post_submitbox_misc_actions', array( $this, 'slider_submitbox' ));	
	}
	
	public function slider_submitbox(){
		global $post;
		if( !$post || parent::get_type_slider() != $post->post_type ){
			return;
		}

		$stamp = __('No expiration date.', 'featured-articles-lite');				
	?>
	<div class="misc-pub-section curtime misc-pub-exptime">
		<span id="exp_timestamp">
		<?php echo $stamp; ?></span>
		<a href="#timestamp_exp_div" class="edit-exp_timestamp hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit slider expiration date', 'featured-articles-lite' ); ?></span></a>
		<div id="timestamp_exp_div" class="hide-if-js">
			<?php fa_option_not_available( __('Set slider expiration date. After reaching the date, slider is unpublished from front-end.', 'featured-articles-lite') );?>
			<p>
				<a href="#edit_exp_timestamp" class="cancel-exp-timestamp hide-if-no-js button-cancel"><?php _e('Close'); ?></a>
			</p>
		</div>
	</div>
	<?php 
	}
	
	/**
	 * Slider options meta box callback function
	 * @param object $post - current slider post being edited
	 */
	public function meta_box_slider_options( $post ){
		// load the template
		$template 	= fa_metabox_path('slider-options');
		include_once $template;
	}	
	
	/**
	 * Display a list of useful links
	 */
	public function meta_box_docs( $post ){
		$links = array(
			array(
				'text' => __( 'How to create sliders', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documentation/featured-articles-pro-3/creating-sliders/'
			),
			array(
				'text' => __( 'Publish sliders above the page loop', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documentation/basic-tutorials/publish-sliders-page-loop/'
			),
			array(
				'text' => __( 'Use the slider shortcode', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documentation/basic-tutorials/how-to-use-the-slider-shortcode/'
			),
			array(
				'text' => __( 'How to use the slider widget', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/featured-articles-for-wp/how-to-create-a-wordpress-slider-widget-with-featured-articles/',
			),
			array(
				'text' => __( 'Allow custom post types in sliders', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/featured-articles-for-wp/create-wordpress-slider-from-custom-post-type/'
			),
			array(
				'text' => __( 'Allow plugin access based on user roles', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documentation/basic-tutorials/allow-plugin-access-based-user-roles/'
			),
			array(
				'text' => __( 'Store slider themes outside plugin folder', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documentation/intermediate-tutorials/moving-slider-themes-folder/'
			)			
		);
		
		$extra = array(
			array(
				'text' => __( 'Plugin Homepage', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/featured-articles-pro/'
			),
			array(
				'text' => __( 'Slider examples', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/featured-articles-pro/examples/'
			),
			array(
				'text' => __( 'Docs page', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/documents/featured-articles-pro-3/'
			),
			array(
				'text' => __( 'Plugin Forum', 'featured-articles-lite' ),
				'url' => 'http://www.codeflavors.com/codeflavors-forums/forum/featured-articles-3-0/'
			)
		);
		
		$campaign = array(
			'utm_source' => 'plugin',
			'utm_medium' => 'doc_link',
			'utm_campaign' => 'fa_lite'
		);
		$q = http_build_query( $campaign );
?>
<?php _e( 'Below is a list of useful tutorials to help you get started.', 'featured-articles-lite' );?>

<ul>
<?php foreach( $links as $link ):?>
	<li><a href="<?php  echo $link['url'] . '?' . $q;?>" target="_blank" title="<?php esc_attr( $link['text'] );?>"><?php echo $link['text'];?></a></li>
<?php endforeach;?>
</ul>

<?php _e('Other useful links', 'featured-articles-lite');?>

<ul>
<?php foreach( $extra as $link ):?>
	<li><a href="<?php  echo $link['url'] . '?' . $q;?>" target="_blank" title="<?php esc_attr( $link['text'] );?>"><?php echo $link['text'];?></a></li>
<?php endforeach;?>
</ul>
<?php	
	}
	
	/**
	 * Slider code metabox callback
	 * @param object $post - current slider post being edited
	 */
	public function meta_box_slider_code( $post ){
		$template = fa_metabox_path('slider-code');
		include_once $template;
	}
	
	/**
	 * Slider content meta box callback function.
	 * @param object $post - current slider post being edited
	 */
	public function meta_box_slider_content( $post ){
		// get the themes
		$themes = fa_get_themes();
		// get the options
		$options = fa_get_slider_options( $post->ID );
		// metabox template
		$template = fa_metabox_path('slider-content');
		include_once $template;
	}
	
	/**
	 * Slider theme meta box callback function
	 * @param object $post - current slider post being edited
	 */
	public function meta_box_slider_theme( $post ){		
		// get the options		
		$options 	= fa_get_slider_options( $post->ID );		
		// get the themes
		$themes 	= fa_get_themes();
		// get the display areas
		$areas = fa_get_options( 'hooks' );
		// load the template
		$template 	= fa_metabox_path('slider-theme');
		include_once $template;
	}
	
	/**
	 * Save slider details. Callback function for action save_post_{post_type}
	 * @param int $post_id
	 * @param object $post
	 * @param bool $update
	 */
	public function save_slider( $post_id, $post, $update ){
		if( !current_user_can('edit_fa_items', $post_id) ){
			wp_die(-1);
		}
		
		// check for the nonce presence
		if( !isset( $_POST['fa-slider-settings-nonce'] ) ){
			return;
		}
		
		check_admin_referer('fa-slider-options-save', 'fa-slider-settings-nonce');
		
		// set default expiration date
		$_POST['slider']['expires'] = '0000-00-00 00:00:00';
		
		// do not allow no post type specified for posts
		if( !isset( $_POST['slides']['post_type'] ) ){
			$_POST['slides']['post_type'][] = 'post';
		}

		// allow no categories specified (allow all categories if none specified)
		if( !isset( $_POST['slides']['tags'] ) ){
			$_POST['slides']['tags'] = array();
		}
		
		// allow empty content on mixed posts
		if( !isset( $_POST['slides']['posts'] ) ){
			$_POST['slides']['posts'] = array();
		}
		// allow empty content on images
		if( !isset( $_POST['slides']['images'] ) ){
			$_POST['slides']['images'] = array();
		}
		// set the slider color
		if( isset( $_POST['theme']['active'] ) ){
			$theme = $_POST['theme']['active'];
			
			// process the layout variation if available
			if( isset( $_POST['layout']['class'][ $theme ] ) ){
				$_POST['layout']['class'] = $_POST['layout']['class'][ $theme ];
			}else{
				$_POST['layout']['class'] = '';
			}			
			// set the color
			if( isset( $_POST['theme_color'][ $theme ] ) ){
				$_POST['theme']['color'] = $_POST['theme_color'][ $theme ];
			}else{
				$_POST['theme']['color'] = '';
			}
		}
		// allow empty on display categories
		if( !isset( $_POST['display']['tax'] ) ){
			$_POST['display']['tax'] = array();
		}
		// allow empty on display posts
		if( !isset( $_POST['display']['posts'] ) ){
			$_POST['display']['posts'] = array();
		}
		
		// process the publish areas
		$areas = fa_get_options('hooks');
		$set = isset( $_POST['slider_area'] ) ? $_POST['slider_area'] : array();
		foreach( $areas as $area_id => $area ){
			if( in_array( $area_id, $set ) ){
				if( !in_array( $post_id, $area['sliders'] ) ){
					$areas[ $area_id ]['sliders'][] = $post_id;
				}				
			}else{
				if( in_array( $post_id , $area['sliders']) ){
					$key = array_search( $post_id , $area['sliders'] );
					if( false !== $key ){
						unset( $areas[ $area_id ]['sliders'][ $key ] );
					}	
				}
			}			
		}
		fa_update_options( 'hooks' , $areas );
		
		// update the slider options
		fa_update_slider_options( $post_id, $_POST );
		
		/**
		 * Action on slider save
		 * @param int $post_id - ID of post being saved
		 * @param object $post - object of post being saved
		 * @param bool $update - is update or new post
		 * @param array - values send from form
		 */
		do_action('fa-save-slider', $post_id, $post, $update, $_POST);
	}
	
	/**
	 * Store the options on slider revision. The revisions are created whenever a
	 * preview is triggered by user on published sliders.
	 * 
	 * @param int $post_id
	 * @param object $post
	 * @param bool $update
	 */
	public function save_slider_revisions( $revision_id, $revision, $update ){
		global $post;
		if( !$post || parent::get_type_slider() != $post->post_type ){
			return;
		}
		// confirm that revision belongs to current post
		if( $revision->post_parent != $post->ID ){
			return;			
		}
		
		// check for the nonce presence
		if( !isset( $_POST['fa-slider-settings-nonce'] ) ){
			return;
		}
		check_admin_referer('fa-slider-options-save', 'fa-slider-settings-nonce');
				
		// do not allow no post type specified for posts
		if( !isset( $_POST['slides']['post_type'] ) ){
			$_POST['slides']['post_type'][] = 'post';
		}

		// allow no categories specified (allow all categories if none specified)
		if( !isset( $_POST['slides']['tags'] ) ){
			$_POST['slides']['tags'] = array();
		}
		
		// allow empty content on mixed posts
		if( !isset( $_POST['slides']['posts'] ) ){
			$_POST['slides']['posts'] = array();
		}
		// allow empty content on images
		if( !isset( $_POST['slides']['images'] ) ){
			$_POST['slides']['images'] = array();
		}
		// set the slider color
		if( isset( $_POST['theme']['active'] ) ){
			$theme = $_POST['theme']['active'];
			
			// process the layout variation if available
			if( isset( $_POST['layout']['class'][ $theme ] ) ){
				$_POST['layout']['class'] = $_POST['layout']['class'][ $theme ];
			}else{
				$_POST['layout']['class'] = '';
			}			
			// set the color
			if( isset( $_POST['theme_color'][ $theme ] ) ){
				$_POST['theme']['color'] = $_POST['theme_color'][ $theme ];
			}else{
				$_POST['theme']['color'] = '';
			}
		}
		// allow empty on display categories
		if( !isset( $_POST['display']['tax'] ) ){
			$_POST['display']['tax'] = array();
		}
		// allow empty on display posts
		if( !isset( $_POST['display']['posts'] ) ){
			$_POST['display']['posts'] = array();
		}
		
		fa_update_slider_options( $revision_id , $_POST );		
	}
	
	/**
	 * Callback on hook save_post. Detects images in post content
	 * to be used as slide image. Applies only for post types allowed from plugin settings.
	 */
	public function detect_image( $post_id, $post, $update ){
		// no autodetect for post types not allowed from plugin settings
		$allowed = fa_allowed_post_types();
		if( !in_array( $post->post_type , $allowed) || 'trash' == $post->post_status ){
			return;
		}
		
		// scan content for image
		$image = $this->find_image_in_post_content( $post->post_content );
		if( isset( $image['id'] ) && $image['id'] ){
			fa_update_slide_options( $post_id, 
				array( 
					'temp_image_id' 	=> $image['id'], // set the image ID if found 
					'temp_image_url' 	=> ''  // unset any image url set on post
				) 
			);			
		}elseif ( isset( $image['img'] ) && $image['img'] ){
			fa_update_slide_options( $post_id, 
				array( 
					'temp_image_id' 	=> '', // unset the image ID 
					'temp_image_url' 	=> $image['img']  // set image url
				) 
			);			
		}		
	}
	
	/**
	 * Scans a given post content for images.
	 * 
	 * @param string $content - the post content to be scanned
	 * @return array
	 */
	private function find_image_in_post_content( $content ){
		return fa_detect_image( $content );	
	}
	
	/**
	 * Create extra columns on slider display table for administrators.
	 * 
	 * @param array $columns
	 */
	public function extra_slider_columns( $columns ){
		$columns = array(
			'cb' 		=> $columns['cb'], 
			'title' 	=> $columns['title'], 
			'content'	=> __('Content Type', 'featured-articles-lite'),
			'theme'		=> __('Slider Theme', 'featured-articles-lite'),
			'auto_display' => __('Display on', 'featured-articles-lite'),
			'author' 	=> __('Author', 'featured-articles-lite'),
			'date' 		=> $columns['date']
		);
		
		fa_load_admin_style( 'list-tables' );
		
		return $columns;
	}
	
	/**
	 * Output the extra columns data
	 * 
	 * @param string $column_name
	 * @param int $post_id
	 */
	public function output_extra_slider_columns(  $column_name, $post_id  ){
		switch( $column_name ){
			// output slider content type (latest posts, mixed content, images)
			case 'content':
				$options = fa_get_slider_options( $post_id, 'slides' );
				switch( $options['type'] ){
					case 'post':
						$order = 'latest';
						if( 'comments' == $options['orderby'] ){
							$order = __('most commented', 'featured-articles-lite');
						}else if( 'random' == $options['orderby'] ){
							$order = __('random', 'featured-articles-lite');
						}
						// 5 (recent|most commented|random) posts from 
						printf( __('%d %s posts', 'featured-articles-lite'), $options['limit'], $order );
					break;
					case 'mixed':
						$count = count( $options['posts'] );
						printf( __('%d manually selected posts', 'featured-articles-lite'), $count );
					break;
					case 'image':
						$count = count( $options['images'] );
						printf( __('%d manually selected images', 'featured-articles-lite'), $count );
					break;	
				}				
			break;	
			case 'theme':
				$options = fa_get_slider_options( $post_id, 'theme' );
				$name = isset( $options['details']['theme_config']['name'] ) ? $options['details']['theme_config']['name'] : ucfirst( $options['active'] );
				printf( __('%s', 'featured-articles-lite'), $name );
			break;	
			case 'auto_display':
				$options = fa_get_slider_options( $post_id, 'display' );
				$output = array();
				
				if( $options['everywhere'] ){
					$output[] = __('Everywhere', 'featured-articles-lite');
				}else{
					if( $options['home'] ){
						$output[] = __( 'Homepage', 'featured-articles-lite' );
					}
					
					if( $options['all_pages'] ){
						$output[] = __( 'All single post/pages', 'featured-articles-lite' );
					}else{
						if( $options['posts'] ){
							$count = 0;
							foreach( $options['posts'] as $posts ){
								$count += count( $posts );
							}					
							$output[] = sprintf( __( '%d posts/pages', 'featured-articles-lite' ), $count );
						}
					}

					if( $options['all_categories'] ){
						$output[] = __( 'All archive pages', 'featured-articles-lite' );
					}else{
						if( $options['tax'] ){
							$count = 0;
							foreach( $options['tax'] as $categories ){
								$count += count( $categories );
							}					
							$output[] = sprintf( __( '%d category pages', 'featured-articles-lite' ), $count );
						}
					}					
				}
				
				if( $output ){
					echo implode(', ', $output);
				}else{
					echo '-';
				}				
				
			break;	
		}		
	}
	
	
	/**
	 * Save post action callback. Stores the slide options on post meta. 
	 *
	 * @param int $post_id
	 * @param object $post
	 * @param bool $update
	 */
	public function save_slide( $post_id, $post, $update ){
		if( !current_user_can('edit_fa_items', $post_id) ){
			return;
		}
		
		// check for the nonce presence
		if( !isset( $_POST['fa-slide-settings-nonce'] ) ){
			return;
		}
		
		check_admin_referer('fa-slide-options-save', 'fa-slide-settings-nonce');
		
		fa_update_slide_options( $post_id, $_POST['fa_slide'] );
		
		/**
		 * Action on slide save
		 * @param int $post_id - ID of post being saved
		 * @param object $post - object of post being saved
		 * @param bool $update - is update or new post
		 */
		do_action('fa-save-slide', $post_id, $post, $update);
	}
	
	/**
	 * Callback function on settings page load
	 */
	public function on_page_settings_load(){
		
		if( isset( $_POST['fa_nonce'] ) && check_admin_referer('fapro_save_settings', 'fa_nonce') ){
			if( !current_user_can('manage_options') ){
				wp_die( __('Sorry, you are not allowed to do this.', 'featured-articles-lite'), __('Access denied', 'featured-articles-lite') );
			}
			
			// update general settings
			$result = fa_update_options('settings', $_POST);
			if( is_wp_error( $result ) ){
				$this->errors = $result;
			}			
			
			if( is_wp_error( $result ) ){
				$this->errors = $result;
			}				
			
			$this->save_caps();
			if( !$this->errors && !is_wp_error( $this->errors ) ){
				$url = add_query_arg( array('message' => 801) , html_entity_decode( menu_page_url( 'fapro_settings', false ) ) );
				wp_redirect( $url );
				die();				
			}						
		}
		
		fa_load_template_style( 'settings' );
		fa_load_admin_script( 'tabs', array('jquery', 'jquery-ui-tabs') );		
	}
	
	/**
	 * Save capabilities
	 */
	private function save_caps(){
		if( isset( $_POST['fa_nonce'] ) ){
			if( check_admin_referer('fapro_save_settings', 'fa_nonce') ){
				if( !current_user_can('manage_options') ){
					wp_die( __('Sorry, you are not allowed to do this.', 'featured-articles-lite'), __('Access denied', 'featured-articles-lite') );
				}

				// get roles
				global $wp_roles;				
				$roles = $wp_roles->get_names();
				// get plugin capabilities
				$capabilities = parent::get_caps();
				// remove administrator and subscriber roles
				unset( $roles['administrator'] );		
				// remove capabilities
				foreach( $roles as $role => $name ){
					$r = get_role( $role );
					foreach( $capabilities as $cap ){
						$r->remove_cap($cap);
					}
				}
				unset( $roles['subscriber'] );
				
				if( isset( $_POST['caps'] ) ){
					// allow capabilities for given roles
					foreach( $roles as $role => $name ){
						$r = get_role( $role );
						if( isset( $_POST['caps'][ $role ] ) ){
							foreach( $capabilities as $cap ){
								if( isset( $_POST['caps'][ $role ][ $cap ] ) ){
									$r->add_cap($cap);
								}else{
									$r->remove_cap($cap);
								}	
							}							
						}else{
							foreach( $capabilities as $cap ){
								$r->remove_cap($cap);
							}
						}
					}					
				}// end if				
			}// end if check admin referer			
		}// end if		
	}
	
	/**
	 * Plugin settings admin page
	 */
	public function page_settings(){
		// get user roles
		global $wp_roles;
		$roles = $wp_roles->get_names();
		// remove administrator and subscriber
		unset( $roles['administrator'], $roles['subscriber'] );
		// get the plugin settings
		$settings = fa_get_options('settings');	
		// load the template
		$template = fa_template_path( 'settings' );
		include_once $template;
	}
	
	/**
	 * Display any errors returned by the plugin
	 */
	public function show_errors(){
		if( !is_wp_error( $this->errors ) ){
			return;
		}
		$codes = $this->errors->get_error_codes();				
?>
<div class="error">
	<p>
	<?php foreach ($codes as $code):?>
		<?php echo $this->errors->get_error_message( $code );?><br />
	<?php endforeach;?>
	</p>
</div>
<?php	
	}// show_errors()
	
	/**
	 * Adds tinyce plugins to editor
	 */
	public function tinymce(){
		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
	 	
		// Don't load unless is post editing (includes post, page and any custom posts set)
		$screen = get_current_screen();
		if( 'post' != $screen->base ){
			return;
		}  

		// Add only in Rich Editor mode
		if ( version_compare( get_bloginfo( 'version' ) , '4', '>=' ) && get_user_option('rich_editing') == 'true') {
	   		add_filter('mce_external_plugins', array( $this, 'tinymce_plugins' ) );
		    add_filter('mce_buttons', array( $this, 'tinyce_buttons' ) );
		    add_filter('mce_css', array( $this, 'tinymce_css' ) );
	   }
	}
	
	/**
	 * Filter mce_buttons callback.
	 */
	public function tinyce_buttons( $mce_buttons ){
		array_push( $mce_buttons, 'separator', 'fa_slider' );
		return $mce_buttons;
	}
	
	/**
	 * Filter mce_external_plugins callback function.
	 */
	public function tinymce_plugins( $plugin_array ) {
		$plugin_array['fa_slider'] = fa_tinymce_plugin_url( 'fa_slider' );
		return $plugin_array;
	}
	
	/**
	 * Filter mce_css callback function.
	 */
	public function tinymce_css( $css ){
		$css .= ',' . fa_tinymce_plugin_style( 'fa_slider' );
		return $css;
	}
	
	/**
	 * Add tinyMce plugin translations
	 */
	public function tinymce_languages( $locales ){
		$locales['fa_slider'] = fa_get_path( 'assets/admin/js/tinymce/fa_slider/langs/langs.php' );
		return $locales;
	}
	
	/**
	 * Register alternative meta box for WP version prior to WP 4
	 * @param string $post_type
	 * @param object $post
	 */
	public function register_slider_shortcode_meta_box( $post_type, $post ){
		if( parent::get_type_slider() == $post_type ){
			return;
		}
		
		// Slide video attachment meta box
		add_meta_box(
			$this->meta_box_prefix . '-slider-shortcode', 
			__('Featured Articles Lite - slider shortcode', 'featured-articles-lite'), 
			array( $this, 'meta_box_slider_shortcode' ),
			null,
			'side'
		);
		
		fa_load_admin_script( 'insert-shortcode' );
	}
	
	/**
	 * Meta box callback for slider shortcode
	 * @param object $post
	 */
	public function meta_box_slider_shortcode( $post ){
		// output the sliders in variable
	    $sliders = fa_get_sliders('publish');
	    $options = array();
	    foreach( $sliders as $slider ){
	    	$text = empty( $slider->post_title ) ? '(' . __('no title', 'featured-articles-lite') . ')' : esc_attr( $slider->post_title );
	    	$options[] = '<option value="' . $slider->ID . '">' . $text . ' (#' . $slider->ID . ')</option>';
	    }
?>
<label for="fa-slider-shortcode"><?php _e( 'Select slider', 'featured-articles-lite' );?> :
<?php if( $options ):?>
<select id="fa-slider-shortcode" name="fa-slider-shortcode">
	<option value=""><?php _e( 'Select', 'featured-articles-lite' );?></option>
	<?php echo implode( "\n", $options );?>
</select>
</label>
<p style="text-align:right;"><input type="button" id="fa-insert-shortcode" value="<?php _e('Insert shortcode', 'featured-articles-lite');?>" class="button" /></p>
<?php else:?>
	<em><?php _e( 'No published sliders found', 'featured-articles-lite' );?></em>
</label>
<?php endif;?>
<?php		
	}
	
	/**
	 * Callback for filter enter_title_here that controls the label on post edit screen
	 * @param string $label
	 * @param object $post
	 */
	public function post_title_label( $label, $post ){
		switch( $post->post_type ){
			// slider edit title label
			case parent::get_type_slider():
				return __('Slider title', 'featured-articles-lite');
			break;
			// return the default label
			default:
				return $label;
			break;
		}
	}
	
	/**
	 * Add meta description to plugin row in plugins page
	 * @param array $meta
	 * @param string $file
	 */
	public function plugin_meta( $meta, $file ){
		// add Settings link to plugin actions
		$plugin_file = plugin_basename( FA_PATH . '/index.php' );
		// check if FA PRO is installed and disable activate link
		$pro_file = str_replace( 'lite', 'pro-3', $plugin_file);
		
		if( $file == $pro_file ){
			$meta[] = '<span class="file-error">' . __('To activate PRO you must first deactivate Featured Articles Lite 3.', 'featured-articles-lite') . '</span>';			
		}
		
		// check if 2.X version of the plugin is installed and recommet removal
		$files = array(
			'pro' 	=> str_replace( array( 'lite', 'index.php' ), array( 'pro', 'main.php' ), $plugin_file )
		);
		if( in_array( $file, $files ) ){
			$meta[] = '<span class="file-error">' . __("You should remove this version of the plugin (<strong>do not delete the data</strong> when removing the plugin).", 'featured-articles-lite') . '</span>';
		}
		
		return $meta;
	}
	
	/**
	 * Add extra actions links to plugin row in plugins page
	 * @param array $links
	 * @param string $file
	 */
	public function plugin_action_links( $links, $file ){
		// add Settings link to plugin actions
		$plugin_file = plugin_basename( FA_PATH . '/index.php' );
		if( $file == $plugin_file ){
			$links[] = sprintf( '<a href="%s">%s</a>', menu_page_url( 'fapro_settings' , false), __('Settings', 'featured-articles-lite') );
		}
		
		// check if FA PRO is installed and disable activate link
		$pro_file = str_replace( 'lite', 'pro-3', $plugin_file);
		if( $file == $pro_file ){
			unset( $links['activate'] );
		}

		// check if 2.X version of the plugin is installed
		$files = array(
			'pro' 	=> str_replace( array( 'lite', 'index.php' ), array( 'pro', 'main.php' ), $plugin_file )
		);
		if( in_array( $file, $files ) ){
			unset( $links['activate'] );
		}
		
		return $links;
	}
	
	public function admin_footer_message( $footer_text ){
		
	 	global $current_screen;
		
	 	// Only display custom text on Easing Slider pages
        if ( isset( $current_screen->id ) && false !== strpos( $current_screen->id, 'fa_slider' ) ) {
            return sprintf( 
            	__( 'Please rate <strong>Featured Articles Lite</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%1$s" target="_blank">WordPress.org</a>.  Thank you from the CodeFlavors team!', 'featured-articles-lite' ), 
            	'https://wordpress.org/support/view/plugin-reviews/featured-articles-lite#postform' 
            );
        } else {
            return $footer_text;
        }
		
	}
}