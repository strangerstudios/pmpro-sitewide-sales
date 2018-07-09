<?php
/**
 * Automatically applies discound code on checkout if
 * landing page has been visited.
 *
 * @package pmpro-sitewide-sale/includes
 */

add_action( 'posts_selection', 'pmpro_sws_check_cookie' );
/**
 * Automatically applies discount code if user has the cookie set from sale page
 */
function pmpro_sws_check_cookie() {
	global $wpdb, $post, $pmpro_pages;

	if ( ! is_page( $pmpro_pages['checkout'] ) || empty( $_REQUEST['level'] ) || ! empty( $_REQUEST['discount_code'] ) ) {
		return;
	}
	$options = pmprosws_get_options();
	if ( empty( $options['discount_code_id'] ) ) {
		return;
	}
	$cookie_name = 'pmpro_sitewide_sale_' . $options['discount_code_id'] . '_tracking';
	if ( ! isset( $_COOKIE[ $cookie_name ] ) || false == strpos( $_COOKIE[ $cookie_name ], ';1;' ) ) {
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
