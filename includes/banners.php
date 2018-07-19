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
	// Can be optimized to use a single get_post_meta call.
	global $pmpro_pages;
	$options              = pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	if ( false === $active_sitewide_sale || 'sws_sitewide_sale' !== get_post_type( $active_sitewide_sale ) ) {
		// $active_sitewide_sale not set or is a different post type.
		return;
	}

	if ( false !== get_post_meta( $active_sitewide_sale, 'discount_code_id', true ) &&
				false !== get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) &&
				'no' !== get_post_meta( $active_sitewide_sale, 'use_banner', true ) &&
				! is_page( 'login' ) &&
				! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) &&
				! is_page( intval( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ) ) &&
				! ( get_post_meta( $active_sitewide_sale, 'hide_on_checkout', true ) && is_page( $pmpro_pages['checkout'] ) ) &&
				! in_array( pmpro_getMembershipLevelForUser()->ID, get_post_meta( $active_sitewide_sale, 'hide_for_levels', true ), true ) &&
				date( 'Y-m-d' ) > get_post_meta( $active_sitewide_sale, 'start_date', true ) &&
				date( 'Y-m-d' ) < get_post_meta( $active_sitewide_sale, 'end_date', true )
			) {

		// Display the appropriate banner
		// get_post_meta( $active_sitewide_sale, 'use_banner', true ) will be something like top, bottom, etc.
		if ( file_exists( PMPROSWS_DIR . '/includes/banners/' . get_post_meta( $active_sitewide_sale, 'use_banner', true ) . '.php' ) ) {
			require_once PMPROSWS_DIR . '/includes/banners/' . get_post_meta( $active_sitewide_sale, 'use_banner', true ) . '.php';
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

add_action( 'wp_head', 'pmpro_sws_custom_css', 5 );
/**
 * Logic for when to show banners/which banner to show
 */
function pmpro_sws_custom_css() {
	$options = pmprosws_get_options();
	?>
	<style type="text/css">
		<?php
		if ( isset( $options['css_option'] ) && ! empty( $options['css_option'] ) ) {
			echo $options['css_option'];
		}
		?>
	</style>
	<?php
}
