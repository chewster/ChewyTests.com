<?php

/**
 * Plugin Name:       Nivo Image Slider
 * Plugin URI:        http://wordpress.org/plugins/nivo-image-slider/
 * Description:       A WordPress plugin to include image slider into your theme.
 * Version:           1.3.2
 * Author:            Sayful Islam
 * Author URI:        http://sayful.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nivo-image-slider
 * Domain Path:       /languages/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nivo-image-slider-activator.php
 */
function activate_nivo_image_slider() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Nivo_Image_Slider_Activator.php';
	Nivo_Image_Slider_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nivo-image-slider-deactivator.php
 */
function deactivate_nivo_image_slider() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/Nivo_Image_Slider_Deactivator.php';
	Nivo_Image_Slider_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nivo_image_slider' );
register_deactivation_hook( __FILE__, 'deactivate_nivo_image_slider' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/Nivo_Image_Slider.php';


function run_nivo_image_slider() {

	$plugin = new Nivo_Image_Slider();
	$plugin->run();

}
run_nivo_image_slider();
