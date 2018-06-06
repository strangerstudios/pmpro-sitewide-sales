<?php
/**
 * Sets and checks cookie for automatically applying discount code,
 * also keeping track of statistics with cookie
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
	if ( $options['landing_page_post_id'] === $post->ID . '' && ! isset( $_COOKIE['pmpro_sws_tracking'] ) ) {
		// setcookie( 'pmpro_sws_tracking', 'landing_page', 7 * WEEK_IN_SECONDS, 'COOKIEPATH, COOKIE_DOMAIN, false' );
		?>
			<script>document.cookie = "pmpro_sws_tracking=landing_page";</script>
		<?php
		$options['num_landing'] += 1;
		pmprosws_save_options( $options );
	}
}

add_action( 'init', 'pmpro_sws_check_cookie' );
/**
 * Automatically applies discount code if user has the cookie set from sale page
 */
function pmpro_sws_check_cookie() {
	global $wpdb, $post;

	if ( ! pmpro_getOption( 'checkout_page_id' ) === $post->ID || empty( $_REQUEST['level'] ) || ! isset( $_COOKIE['pmpro_sws_tracking'] ) ) {
			return;
	}

	$options = pmprosws_get_options();
	if ( 'landing_page' === $_COOKIE['pmpro_sws_tracking'] ) {
		$options['num_checkout'] += 1;
		pmprosws_save_options( $options );
		//setcookie( 'pmpro_sws_tracking', 'checkout', 7 * WEEK_IN_SECONDS, '/', COOKIE_DOMAIN );
		?>
			<script>document.cookie = "pmpro_sws_tracking=checkout";</script>
		<?php
	}

	if ( ! empty( $_REQUEST['discount_code'] ) ) {
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

add_action( 'pmpro_after_checkout', 'pmpro_sws_after_checkout' );
/**
 * Updates cookie and statistics after user checks out for a level
 */
function pmpro_sws_after_checkout() {
	global $wpdb;
	$options = pmprosws_get_options();
	if ( 'checkout' === $_COOKIE['pmpro_sws_tracking'] ) {
		$options['num_confirmation'] += 1;
		//setcookie( 'pmpro_sws_tracking', 'confirmation', 7 * WEEK_IN_SECONDS, '/', COOKIE_DOMAIN );
		?>
			<script>document.cookie = "pmpro_sws_tracking=confirmation";</script>
		<?php
	}
	$discount           = $options['discount_code_id'];
	$sale_discount_code = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes WHERE id = $discount", OBJECT )[0]->code;
	if ( $_REQUEST['discount_code'] === $sale_discount_code ) {
		$options['times_code_used'] += 1;
		$code_levels                 = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes_levels WHERE code_id = $discount", OBJECT );
		$checkout_level              = $_REQUEST['level'];
		foreach ( $code_levels as $code ) {
			if ( $code->level_id . '' === $checkout_level ) {
				$options['revenue'] += $code->initial_payment;
			}
		}
	}
	pmprosws_save_options( $options );
}
