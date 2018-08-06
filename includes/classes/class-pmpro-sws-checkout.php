<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Checkout {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'automatic_discount_application' ) );
		add_filter( 'pmpro_email_body', array( __CLASS__, 'insert_upsell_into_confirmation_emails' ), 10, 2 );
		add_filter( 'pmpro_confirmation_message', array( __CLASS__, 'insert_upsell_into_confirmation_page' ) );
	}

	/**
	 * Automatically applies discount code if user has the cookie set from sale page
	 */
	public static function automatic_discount_application() {
		global $wpdb, $post, $pmpro_pages;

		if ( empty( $_REQUEST['level'] ) || ! empty( $_REQUEST['discount_code'] ) ) {
			return;
		}
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		$current_discount     = get_post_meta( $active_sitewide_sale, 'discount_code_id', true );
		if ( empty( $current_discount ) ||
		date( 'Y-m-d' ) < get_post_meta( $active_sitewide_sale, 'start_date', true ) ||
		date( 'Y-m-d' ) > get_post_meta( $active_sitewide_sale, 'end_date', true )
		) {
			return;
		}
		$cookie_name = 'pmpro_sitewide_sale_' . $active_sitewide_sale . '_tracking';
		if ( ! isset( $_COOKIE[ $cookie_name ] ) || false == strpos( $_COOKIE[ $cookie_name ], ';1;' ) ) {
			return;
		}
		$checkout_level = $_REQUEST['level'];
		$discount       = $current_discount;
		$code_levels    = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes_levels WHERE code_id = $discount", OBJECT );
		foreach ( $code_levels as $code ) {
			if ( $code->level_id . '' === $checkout_level ) {
				$codes = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes WHERE id = $discount", OBJECT );
				wp_redirect( $_SERVER['REQUEST_URI'] . '&discount_code=' . $codes[0]->code );
				exit();
			}
		}
	}

	/**
	 * Inserts upsale info into confirmation emails on checkout.
	 *
	 * @param  string     $body  current body of email.
	 * @param  PMProEmail $email email object.
	 * @return string            new email body.
	 */
	public static function insert_upsell_into_confirmation_emails( $body, $email ) {
		// Check if sending a confirmation email.
		if ( 0 !== strpos( $email->template, 'checkout' ) ) {
			return $body;
		}

		global $current_user;
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( false === $active_sitewide_sale || empty( $current_user->membership_level ) ) {
			return $confirmation_message;
		}
		$upsell_enabled = get_post_meta( $active_sitewide_sale, 'upsell_enabled', true );
		$upsell_levels = get_post_meta( $active_sitewide_sale, 'upsell_levels', true );
		$upsell_text = get_post_meta( $active_sitewide_sale, 'upsell_text', true );
		$landing_page_id = get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true );
		if ( empty( $upsell_enabled ) || false === $upsell_enabled ||
				empty( $upsell_levels ) || ! in_array( $current_user->membership_level->id, $upsell_levels, true ) ||
				empty( $upsell_text ) || '' === $upsell_text ||
				false === $landing_page_id || false === get_permalink( intval( $landing_page_id ) ) ) {
			return $body;
		}
		$upsell_text = str_replace( '!!sws_landing_page_url!!', get_permalink( intval( $landing_page_id ) ), $upsell_text );
		return $body . $upsell_text . '<br/><br/>';
	}

	/**
	 * Inserts upsale info into confirmation emails on checkout.
	 *
	 * @param  string $confirmation_message  current body of email.
	 * @return string                            new confirmation message.
	 */
	public static function insert_upsell_into_confirmation_page( $confirmation_message ) {
		global $current_user;
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( false === $active_sitewide_sale || empty( $current_user->membership_level ) ) {
			return $confirmation_message;
		}
		$upsell_enabled = get_post_meta( $active_sitewide_sale, 'upsell_enabled', true );
		$upsell_levels = get_post_meta( $active_sitewide_sale, 'upsell_levels', true );
		$upsell_text = get_post_meta( $active_sitewide_sale, 'upsell_text', true );
		$landing_page_id = get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true );
		if ( empty( $upsell_enabled ) || false === $upsell_enabled ||
				empty( $upsell_levels ) || ! in_array( $current_user->membership_level->id, $upsell_levels, true ) ||
				empty( $upsell_text ) || '' === $upsell_text ||
				false === $landing_page_id || false === get_permalink( intval( $landing_page_id ) ) ) {
			return $confirmation_message;
		}
		$upsell_text = str_replace( '!!sws_landing_page_url!!', get_permalink( intval( $landing_page_id ) ), $upsell_text );
		return $confirmation_message . $upsell_text . '<br/><br/>';
	}
}
