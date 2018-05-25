<?php
/**
 * Get the Sitewide Sale Options
 */
function pmprosws_get_options() {

	$options = get_option( 'pmpro_sitewide_sale' );

	// Set the defaults.
	if( empty( $options ) ) {
		$options = array(
			'discount_code_id' => false,
			'landing_page_post_id' => false,
			//add other settings here
		);
	}

	return $options;
}

/**
 * Save Sitewide Sale Options
 */
function pmprosws_save_options( $options ) {
	return update_option( 'pmpro_sitewide_sale', $options, 'no');
}