<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Settings {
	/**
	 * Initial plugin setup
	 *
	 * @package pmpro-sitewide-sale/includes
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Init settings page
	 **/
	public static function admin_init() {
		register_setting( 'pmpro-sws-group', 'pmpro_sitewide_sale', array( __CLASS__, 'validate' ) );
	}

	/**
	 * Get the Sitewide Sale Options
	 *
	 * @return array [description]
	 */
	public static function pmprosws_get_options() {
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
	public static function pmprosws_save_options( $options ) {
		update_option( 'pmpro_sitewide_sale', $options, 'no' );
	}

	/**
	 * Validates sitewide sale options
	 *
	 * @param  array $input info to be validated.
	 */
	public static function validate( $input ) {
		$options = self::pmprosws_get_options();
		if ( ! empty( $input['active_sitewide_sale_id'] ) && '-1' !== $input['active_sitewide_sale_id'] ) {
			$options['active_sitewide_sale_id'] = trim( $input['active_sitewide_sale_id'] );
		} else {
			$options['active_sitewide_sale_id'] = false;
		}

		return $options;
	}

}
