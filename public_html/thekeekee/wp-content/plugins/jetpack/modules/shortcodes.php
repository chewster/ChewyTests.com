<?php

/**
 * Module Name: Shortcode Embeds
 * Module Description: Embed media from popular sites without any coding.
 * Sort Order: 3
 * First Introduced: 1.1
 * Major Changes In: 1.2
 * Requires Connection: No
 * Auto Activate: Yes
 * Module Tags: Photos and Videos, Social, Writing, Appearance
 * Feature: Writing
 * Additional Search Queries: shortcodes, shortcode, embeds, media, bandcamp, blip.tv, dailymotion, facebook, flickr, google calendars, google maps, google+, polldaddy, recipe, recipes, scribd, slideshare, slideshow, slideshows, soundcloud, ted, twitter, vimeo, vine, youtube
 */

/**
 * Transforms the $atts array into a string that the old functions expected
 *
 * The old way was:
 * [shortcode a=1&b=2&c=3] or [shortcode=1]
 * This is parsed as array( a => '1&b=2&c=3' ) and array( 0 => '=1' ), which is useless
 *
 * @param array $params             Array of old shortcode parameters.
 * @param bool  $old_format_support true if [shortcode=foo] format is possible.
 *
 * @return string $params
 */
function shortcode_new_to_old_params( $params, $old_format_support = false ) {
	$str = '';

	if ( $old_format_support && isset( $params[0] ) ) {
		$str = ltrim( $params[0], '=' );
	} elseif ( is_array( $params ) ) {
		foreach ( array_keys( $params ) as $key ) {
			if ( ! is_numeric( $key ) ) {
				$str = $key . '=' . $params[ $key ];
			}
		}
	}

	return str_replace( array( '&amp;', '&#038;' ), '&', $str );
}

/**
 * Load all available Jetpack shortcode files.
 */
function jetpack_load_shortcodes() {
	$shortcode_includes = array();

	foreach ( Jetpack::glob_php( dirname( __FILE__ ) . '/shortcodes' ) as $file ) {
		$filename = substr( basename( $file ), 0, -4 );

		$shortcode_includes[ $filename ] = $file;
	}

	/**
	 * This filter allows other plugins to override which shortcodes Jetpack loads.
	 *
	 * @module shortcodes
	 *
	 * @since 2.2.1
	 * @since 4.2.0 Added filename without extension as array key.
	 *
	 * @param array $shortcode_includes An array of which shortcodes to include.
	 */
	$shortcode_includes = apply_filters( 'jetpack_shortcodes_to_include', $shortcode_includes );

	foreach ( $shortcode_includes as $include ) {
		include $include;
	}
}

/**
 * Runs preg_replace so that replacements don't happen within open tags.
 * Parameters are the same as preg_replace, with an added optional search param for improved performance
 *
 * @param string $pattern     Pattern to search for.
 * @param string $replacement String to replace.
 * @param string $content     Post content.
 * @param string $search      String to search for.
 *
 * @return string $content    Replaced post content.
 */
function jetpack_preg_replace_outside_tags( $pattern, $replacement, $content, $search = null ) {
	if ( ! function_exists( 'wp_html_split' ) ) {
		return $content;
	}

	if ( $search && false === strpos( $content, $search ) ) {
		return $content;
	}

	$textarr = wp_html_split( $content );
	unset( $content );
	foreach ( $textarr as &$element ) {
		if ( '' === $element || '<' === $element{0} ) {
			continue;
		}
		$element = preg_replace( $pattern, $replacement, $element );
	}

	return join( $textarr );
}

/**
 * Runs preg_replace_callback so that replacements don't happen within open tags.
 * Parameters are the same as preg_replace, with an added optional search param for improved performance.
 *
 * @param string $pattern  Pattern to search for.
 * @param string $callback Callback returning the replacement string.
 * @param string $content  Post content.
 * @param string $search   String to search for.
 *
 * @return string $content Replaced post content.
 */
function jetpack_preg_replace_callback_outside_tags( $pattern, $callback, $content, $search = null ) {
	if ( ! function_exists( 'wp_html_split' ) ) {
		return $content;
	}

	if ( $search && false === strpos( $content, $search ) ) {
		return $content;
	}

	$textarr = wp_html_split( $content );
	unset( $content );
	foreach ( $textarr as &$element ) {
		if ( '' === $element || '<' === $element{0} ) {
			continue;
		}
		$element = preg_replace_callback( $pattern, $callback, $element );
	}

	return join( $textarr );
}

if ( ! function_exists( 'jetpack_shortcode_get_wpvideo_id' ) ) {
	/**
	 * Get VideoPress ID from wpvideo shortcode attributes.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return int  $id   VideoPress ID.
	 */
	function jetpack_shortcode_get_wpvideo_id( $atts ) {
		if ( isset( $atts[0] ) ) {
			return $atts[0];
		} else {
			return 0;
		}
	}
}

if ( ! function_exists( 'jetpack_shortcode_get_videopress_id' ) ) {
	/**
	 * Get VideoPress ID from videopress shortcode attributes.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return int  $id   VideoPress ID.
	 */
	function jetpack_shortcode_get_videopress_id( $atts ) {
		if ( isset( $atts[0] ) ) {
			return $atts[0];
		} else {
			return 0;
		}
	}
}

jetpack_load_shortcodes();
