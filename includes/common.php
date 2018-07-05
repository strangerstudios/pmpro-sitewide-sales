<?php
/**
 * Accesses stored information about sale.
 *
 * @package pmpro-sitewide-sale/includes
 */

/**
 * Get the Sitewide Sale Options
 **/
function pmprosws_get_options() {

	$options = get_option( 'pmpro_sitewide_sale' );

	// Set the defaults.
	if ( empty( $options ) ) {
		$options = array(
			'discount_code_id'     => false,
			'landing_page_post_id' => false,
			'use_banner'           => 'no',
			'banner_title'         => '',
			'banner_description'   => '',
			'link_text'            => '',
			'css_option'           => '',
			'hide_for_levels'      => [],
			'hide_on_checkout'     => false,
			'hide_on_login'        => false,
		);
	}
	return $options;
}

/**
 * [pmprosws_save_options description]
 *
 * @param array $options contains information about sale to be saved.
 */
function pmprosws_save_options( $options ) {
	return update_option( 'pmpro_sitewide_sale', $options, 'no' );
}
