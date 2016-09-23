<?php 
/**
 * Install
 *
 * Runs on plugin install by setting up the current version,
 * After successful install, the user is redirected to the CPB Welcome
 * screen.
 *
 * @since 1.0
 * @global $wpdb
 * @global $epl_options
 * @global $wp_version
 * @return void
 */
function cpb_install() {
	global $wpdb, $wp_version;

	// Add Upgraded From Option
	$current_version = get_option( 'cpb_version' );
	if ( $current_version != '' ) {
		update_option( 'cpb_version_upgraded_from', $current_version );

	} else {
		$data = get_plugin_data(CPB_PLUGIN_PATH.'/custom-posts-builder.php');
		update_option( 'cpb_version', $data['Version'] );
	}

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect
	set_transient( '_cpb_activation_redirect', true, 30 );
}
register_activation_hook( CPB_PLUGIN_FILE, 'cpb_install' );