<?php

class Nivo_Image_Slider {

	protected $loader;

	protected $plugin_name;

	protected $version;

	public function __construct() {

		$this->plugin_name = 'nivo-image-slider';
		$this->version = '1.2.5';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->includes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Nivo_Image_Slider_Loader.php';

		/**
		 * The class responsible for defining internationalization functionality of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Nivo_Image_Slider_i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/Nivo_Image_Slider_Admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/Nivo_Image_Slider_Public.php';

		$this->loader = new Nivo_Image_Slider_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Nivo_Image_Slider_i18n class in order to set the domain and to register the hook with WordPress.
	 */
	private function set_locale() {

		$plugin_i18n = new Nivo_Image_Slider_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Nivo_Image_Slider_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_admin, 'custom_post_type' );
		$this->loader->add_action( 'init', $plugin_admin, 'custom_taxonomy' );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'post_thumbnail_image_box' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Nivo_Image_Slider_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Include admin and frontend files.
	 */
	public function includes() {

		if( !is_admin() ){
			$this->frontend_includes();
		}

	}

	/**
	 * Include frontend files.
	 */
	public function frontend_includes(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes/shortcodes.php';
	}
	
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
