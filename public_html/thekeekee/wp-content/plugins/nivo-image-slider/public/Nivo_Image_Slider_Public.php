<?php

class Nivo_Image_Slider_Public {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name.'-main', plugin_dir_url( __FILE__ ) . 'css/nivo-slider.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-default', plugin_dir_url( __FILE__ ) . 'themes/default/default.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-light', plugin_dir_url( __FILE__ ) . 'themes/light/light.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-dark', plugin_dir_url( __FILE__ ) . 'themes/dark/dark.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name.'-bar', plugin_dir_url( __FILE__ ) . 'themes/bar/bar.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script('jquery');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jquery.nivo.slider.js', array( 'jquery' ), $this->version, false );

	}

}
