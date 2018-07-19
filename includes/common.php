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
			'active_sitewide_sale_id' => false,
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
