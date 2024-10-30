<?php

require_once IBX_WP_PLUGIN_DIR . '/includes/functions.php';
require_once IBX_WP_PLUGIN_DIR . '/includes/file-functions.php';

if ( is_admin() ) {
	require_once IBX_WP_PLUGIN_DIR . '/admin/admin.php';
}

add_action( 'plugins_loaded', 'ibx_wp', 10, 0 );

function ibx_wp() {
	// Iboxindia_WP_Admin::load_modules();
}

add_action( 'init', 'ibx_wp_init', 10, 0 );

function ibx_wp_init() {
	do_action( 'ibx_wp_init' );
}

add_action( 'admin_init', 'ibx_wp_upgrade', 10, 0 );

function ibx_wp_upgrade() {
	$old_ver = Iboxindia_WP_Settings::get( 'version', '0' );
	$new_ver = IBX_WP_PLUGIN_VER;

	if ( $old_ver == $new_ver ) {
		return;
	}

	do_action( 'ibx_wp_upgrade', $new_ver, $old_ver );

	Iboxindia_WP_Settings::set( 'version', $new_ver );
}

/* Install and default settings */

add_action( 'activate_' . IBX_WP_PLUGIN_BASENAME, 'ibx_wp_install', 10, 0 );

function ibx_wp_install() {
	if ( $opt = get_option( 'ibx_wp' ) ) {
		return;
	}

	ibx_wp_upgrade();
}
