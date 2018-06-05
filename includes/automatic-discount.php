<?php
/**
 * Sets and checks cookie for automatically applying discount code
 *
 * @package pmpro-sitewide-sale/includes
 */

add_action( 'wp', 'pmpro_sws_set_cookie' );
/**
 * Sets a cookie when user visits the page designated as the sale page
 */
function pmpro_sws_set_cookie() {
	global $post;
	$options = pmprosws_get_options();
	if ( $options['landing_page_post_id'] === $post->ID . '' ) {
		setcookie( 'pmpro_sws_sale_page_visited', '1', NULL, COOKIEPATH, COOKIE_DOMAIN, false );
	}
}

add_action( 'init', 'pmpro_sws_check_cookie' );
/**
 * Automatically applies discount code if user has the cookie set from sale page
 */
function pmpro_sws_check_cookie() {
	global $wpdb;
	$options = pmprosws_get_options();
	if ( empty( $_REQUEST['level'] ) || ! empty( $_REQUEST['discount_code'] || ! isset( $_COOKIE['pmpro_sws_sale_page_visited'] ) ) ) {
			return;
	}
	$checkout_level = $_REQUEST['level'];
	$discount       = $options['discount_code_id'];
	$code_levels    = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes_levels WHERE code_id = $discount", OBJECT );
	foreach ( $code_levels as $code ) {
		if ( $code->level_id . '' === $checkout_level ) {
			$codes                     = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes WHERE id = $discount", OBJECT );
			$_REQUEST['discount_code'] = $codes[0]->code;
			return;
		}
	}
}
