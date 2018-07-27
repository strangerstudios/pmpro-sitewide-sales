<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Banners {

	public static function init() {
		if ( ! is_admin() ) {
			add_action( 'wp', array( __CLASS__, 'choose_banner' ) );
			add_action( 'wp_head', array( __CLASS__, 'apply_custom_css' ), 5 );
		}
	}

	/**
	 * Gets info about available banners including name and available
	 * css selectors.
	 *
	 * @return array banner_name => array( option_title=>string, css_selctors=>array(strings) )
	 */
	public static function get_registered_banners() {

		$registered_banners = array(
			'top' => array(
				'option_title'  => __( 'Yes, Top of Site', 'pmpro_sitewide_sale' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_top',
					'.pmpro_btn',
				),
			),
			'bottom' => array(
				'option_title'  => __( 'Yes, Bottom of Site', 'pmpro_sitewide_sale' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_bottom',
					'.pmpro_sws_banner-inner',
					'.pmpro_sws_banner-inner-left',
					'.pmpro_sws_banner-inner-right',
					'.pmpro_btn',
				),
			),
			'bottom_right' => array(
				'option_title'  => __( 'Yes, Bottom Right of Site', 'pmpro_sitewide_sale' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_bottom_right',
					'.pmpro_btn',
				),
			),
		);

		/**
		 * Modify Registerted Banners
		 *
		 * @since 0.0.1
		 *
		 * @param array $registered_banners contains all currently registered banners.
		 */
		$registered_banners = apply_filters( 'pmpro_sws_registered_banners', $registered_banners );

		return $registered_banners;
	}

	/**
	 * Logic for when to show banners/which banner to show
	 */
	public static function choose_banner() {
		// Can be optimized to use a single get_post_meta call.
		global $pmpro_pages;
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( false === $active_sitewide_sale || 'sws_sitewide_sale' !== get_post_type( $active_sitewide_sale ) ) {
			// $active_sitewide_sale not set or is a different post type.
			return;
		}

		$membership_level = pmpro_getMembershipLevelForUser();

		if ( false !== get_post_meta( $active_sitewide_sale, 'discount_code_id', true ) &&
					false !== get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) &&
					'no' !== get_post_meta( $active_sitewide_sale, 'use_banner', true ) &&
					! PMPro_SWS_Setup::is_login_page() &&
					! is_page( intval( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ) ) &&
					! ( get_post_meta( $active_sitewide_sale, 'hide_on_checkout', true ) && is_page( $pmpro_pages['checkout'] ) ) &&
					( false === $membership_level || ! in_array( $membership_level->ID, get_post_meta( $active_sitewide_sale, 'hide_for_levels', true ), true ) ) &&
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
	 * Applies user's custom css to banner
	 */
	public static function apply_custom_css() {
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( false === $active_sitewide_sale || 'sws_sitewide_sale' !== get_post_type( $active_sitewide_sale ) ) {
			// $active_sitewide_sale not set or is a different post type.
			return;
		}

		$css = get_post_meta( $active_sitewide_sale, 'css_option', true )
		?>
		<style type="text/css">
			<?php
			if ( ! empty( $css ) ) {
				echo $css;
			}
			?>
		</style>
		<?php
	}
}
