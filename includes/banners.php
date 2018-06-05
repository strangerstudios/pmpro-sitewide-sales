<?php
/**
 * Generates banners for Sitewide Sale
 *
 * @package pmpro-sitewide-sale/includes
 */

$options = pmprosws_get_options();
if ( false !== $options['discount_code_id'] && false !== $options['landing_page_post_id'] && 'no' !== $options['use_banner'] ) {
	// Probably not right place to hook into...
	add_action( 'wp_footer', 'pmpro_sws_banner_init' );
}

/**
 * Decides which banner to display
 */
function pmpro_sws_banner_init() {
	$options = pmprosws_get_options();
	// $options['use_banner'] will be something like top, bottom, etc.
	if ( file_exists( PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php' ) ) {
		require_once PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php';
		// Maybe call a function here...
	}
}
