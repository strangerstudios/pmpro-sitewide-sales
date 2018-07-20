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
		// add_action( 'admin_menu', array( __CLASS__, 'pmpro_sws_dashboard_menu' ) );
		// add_action( 'admin_notices', array( __CLASS__, 'pmpro_sws_admin_notice' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue_inline' ) );
	}

	/**
	 * Admin Notice on Activation.
	 *
	 * @since 0.1.0
	 */
	public static function pmpro_sws_admin_notice() {
		// Check transient, if available display notice.
		if ( get_transient( 'pmpro-sws-admin-notice' ) ) { ?>
			<div class="updated notice is-dismissible">
				<p><?php printf( __( 'Thank you for activating. <a href="%s">Visit the settings page</a> to get started with the Sitewide Sale Add On.', 'pmpro-sitewide-sale' ), get_admin_url( null, 'admin.php?page=pmpro-sws' ) ); ?></p>
			</div>
			<?php
			// Delete transient, only display this notice once.
			delete_transient( 'pmpro-sws-admin-notice' );
		}
	}

	/**
	 * Get the Sitewide Sale Options
	 **/
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
		return update_option( 'pmpro_sitewide_sale', $options, 'no' );
	}

}
