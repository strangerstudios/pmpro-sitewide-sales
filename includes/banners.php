<?php
/**
 * Generates banners for Sitewide Sale
 *
 * @package pmpro-sitewide-sale/includes
 */

add_action( 'wp', 'pmpro_sws_init_banners' );
/**
 * Logic for when to show banners/which banner to show
 */
function pmpro_sws_init_banners() {
	global $pmpro_pages;
	$options          = pmprosws_get_options();
	$membership_level = pmpro_getMembershipLevelForUser();

	if ( false !== $options['discount_code_id'] &&
				false !== $options['landing_page_post_id'] &&
				'no' !== $options['use_banner'] &&
				! pmpro_sws_is_login_page() &&
				! is_page( intval( $options['landing_page_post_id'] ) ) &&
				! ( $options['hide_on_checkout'] && is_page( $pmpro_pages['checkout'] ) ) &&
				! ( false !== $membership_level && in_array( $membership_level->ID, $options['hide_for_levels'], true ) )
			) {

		// Display the appropriate banner
		// $options['use_banner'] will be something like top, bottom, etc.
		if ( file_exists( PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php' ) ) {
			require_once PMPROSWS_DIR . '/includes/banners/' . $options['use_banner'] . '.php';
			// Maybe call a function here...
		}
	}
}

/**
 * Returns if the user is on the login page (currently works for TML)
 * Can probably switch to is_login_page from PMPro core
 */
function pmpro_sws_is_login_page() {
	global $post;
	$slug = get_site_option( 'tml_login_slug' );
	if ( false === $slug ) {
		$slug = 'login';
	}
	return ( $slug === $post->post_name || is_page( 'login' ) || in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) );
}
