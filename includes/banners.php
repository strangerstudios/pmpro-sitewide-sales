<?php
/**
 * Generates banners for Sitewide Sale
 *
 * @package pmpro-sitewide-sale/includes
 */

$options = pmprosws_get_options();
if ( false !== $options['discount_code_id'] &&
			false !== $options['landing_page_post_id'] &&
			'no' !== $options['use_banner'] ) {

	// Display the appropriate banner
	// $options['use_banner'] will be something like top, bottom, etc.
	if ( file_exists( PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php' ) ) {
		require_once PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php';
		// Maybe call a function here...
	}
}
