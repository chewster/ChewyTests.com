<?php

function install_plugins() {
	global $pagenow;
	if ( ! ( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
		return;
	}
	$active_plugins = (array) get_option( 'active_plugins', array() );
	$active_plugins[] = 'jetpack/jetpack.php';
	$active_plugins[] = 'mojo-marketplace-wp-plugin/mojo-marketplace.php';
	update_option( 'active_plugins', array_unique( $active_plugins ) );
	update_option( 'jetpack_activated',   3 );
	update_option( 'jetpack_do_activate', 1 );
	update_option( 'close_comments_for_old_posts', 1 );
	update_option( 'close_comments_days_old', 28 );
	update_option( 'comments_per_page', 20 );
	unlink( __FILE__ );
}
add_action( 'shutdown', 'install_plugins' );
