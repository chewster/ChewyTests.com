<?php
/**
 * Plugin Name: WP Featured Content and Slider
 * Plugin URI: http://www.wponlinesupport.com/
 * Text Domain: wp-featured-content-and-slider
 * Domain Path: /languages/
 * Description: Easy to add and display what features your company, product or service offers, using our shortcode OR template code.
 * Author: WP Online Support
 * Version: 1.2.4
 * Author URI: http://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author WP Online Support
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;

if( !defined( 'WPFCAS_VERSION' ) ) {
	define( 'WPFCAS_VERSION', '1.2.4' ); // Version of plugin
}

if( !defined( 'WPFCAS_DIR' ) ) {
    define( 'WPFCAS_DIR', dirname( __FILE__ ) ); // Plugin dir
}

if( !defined( 'WPFCAS_URL' ) ) {
    define( 'WPFCAS_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}

if( !defined( 'WPFCAS_POST_TYPE' ) ) {
    define( 'WPFCAS_POST_TYPE', 'featured_post' ); // Plugin post type
}

add_action('plugins_loaded', 'wp_wpfcas_load_textdomain');
function wp_wpfcas_load_textdomain() {
	load_plugin_textdomain( 'wp-featured-content-and-slider', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

/**
 * Function to get unique value number
 * 
 * @package WP Featured Content and Slider
 * @since 1.2.1
 */
function wpfcas_get_unique() {
	static $unique = 0;
	$unique++;

	return $unique;
}

add_action( 'wp_enqueue_scripts','wpfcas_style_css' );
function wpfcas_style_css() {
	
	wp_register_style( 'wpfcas-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), WPFCAS_VERSION );
	wp_enqueue_style( 'wpfcas-font-awesome' );
	
	wp_enqueue_style( 'wpfcas_style',  WPFCAS_URL. 'assets/css/featured-content-style.css', array(), WPFCAS_VERSION );	
	wp_enqueue_style( 'wpfcas_slick_style',  WPFCAS_URL. 'assets/css/slick.css', array(), WPFCAS_VERSION);
	
		// Registring slick slider script
	if( !wp_script_is( 'wpos-slick-jquery', 'registered' ) ) {
		wp_register_script( 'wpos-slick-jquery', WPFCAS_URL.'assets/js/slick.min.js', array('jquery'), WPFCAS_VERSION, true );		
	}
	
}

require_once( 'includes/featured-content-functions.php' );
require_once( 'templates/featured-content-template.php' );
require_once( 'templates/featured-content-slider-template.php' );

// How it work file, Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( WPFCAS_DIR . '/includes/admin/wpfcas-how-it-work.php' );
	require_once( WPFCAS_DIR . '/includes/featured-content_menu_function.php' );
}