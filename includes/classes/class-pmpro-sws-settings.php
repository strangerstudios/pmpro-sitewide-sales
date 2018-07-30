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
		add_shortcode( 'pmpro_sws', array( __CLASS__, 'shortcode' ) );
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

	/**
	 * Displays pre-sale content, sale content, or post-sale content
	 * depending on page and date
	 *
	 * @param array $atts attributes passed with shortcode.
	 */
	public static function shortcode( $atts ) {
		$post_id = get_the_ID();
		$sitewide_sale = get_posts(
			array(
				'post_type'      => 'sws_sitewide_sale',
				'meta_key'       => 'landing_page_post_id',
				'meta_value'     => '' . $post_id,
				'posts_per_page' => 1,
			)
		);

		if ( 1 > count( $sitewide_sale ) ) {
			return '';
		}

		$sitewide_sale = $sitewide_sale[0];

		$sale_content           = 'sale';
		$possible_sale_contents = [ 'pre-sale', 'sale', 'post-sale' ];

		if ( is_array( $atts ) && array_key_exists( 'sale_content', $atts ) && in_array( $atts['sale_content'], $possible_sale_contents, true ) ) {
			$sale_content = $atts['sale_content'];
		} elseif ( date( 'Y-m-d' ) < get_post_meta( $sitewide_sale->ID, 'start_date', true ) ) {
			$sale_content = 'pre-sale';
		} elseif ( date( 'Y-m-d' ) > get_post_meta( $sitewide_sale->ID, 'end_date', true ) ) {
			$sale_content = 'post-sale';
		}

		switch ( $sale_content ) {
			case 'pre-sale':
				return get_post_meta( $sitewide_sale->ID, 'pre_sale_content', true );
			case 'sale':
				return get_post_meta( $sitewide_sale->ID, 'sale_content', true );
			case 'post-sale':
				return get_post_meta( $sitewide_sale->ID, 'post_sale_content', true );
		}
	}
}
