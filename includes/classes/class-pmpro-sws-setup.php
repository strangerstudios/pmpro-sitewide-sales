<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Setup {
	/**
	 *
	 * Initial plugin setup
	 *
	 * @package pmpro-sitewide-sale/includes
	 */
	public static function init() {
		add_filter( 'renaming_cpt_menu_filter', array( __CLASS__, 'pmpro_sws_cpt_name' ) );
		register_activation_hook( PMPROSWS_BASENAME, array( __CLASS__, 'pmpro_sws_admin_notice_activation_hook' ) );
		add_action( 'init', array( __CLASS__, 'pmpro_sws_check_cookie' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'pmpro_sws_plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . PMPROSWS_BASENAME, array( __CLASS__, 'pmpro_sws_plugin_action_links' ) );
		add_action( 'admin_notices', array( __CLASS__, 'pmpro_sws_admin_notice' ) );
	}
	public static function pmpro_sws_cpt_name() {
		$label = 'All PMPro CPTs';
		return $label;
	}
	/**
	 * Runs only when the plugin is activated.
	 *
	 * @since 0.0.0
	 */
	public static function pmpro_sws_admin_notice_activation_hook() {
		// Create transient data.
		set_transient( 'pmpro-sws-admin-notice', true, 5 );
	}


	/**
	 * Automatically applies discount code if user has the cookie set from sale page
	 */
	public static function pmpro_sws_check_cookie() {
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
	 * Returns if the user is on the login page (currently works for TML)
	 * Can probably switch to is_login_page from PMPro core
	 */
	public static function is_login_page() {
		global $post;
		$slug = get_site_option( 'tml_login_slug' );
		if ( false === $slug ) {
			$slug = 'login';
		}
		return ( ( ! empty( $post->post_name ) && $slug === $post->post_name ) || is_page( 'login' ) || in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) );
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
	 * Function to add links to the plugin action links
	 *
	 * @param array $links Array of links to be shown in plugin action links.
	 */
	public static function pmpro_sws_plugin_action_links( $links ) {
		if ( current_user_can( 'manage_options' ) ) {
			$new_links = array(
				'<a href="' . get_admin_url( null, 'admin.php?page=pmpro-sws' ) . '">' . __( 'Settings', 'pmpro-sitewide-sale' ) . '</a>',
			);
		}
		return array_merge( $new_links, $links );
	}

	/**
	 * Function to add links to the plugin row meta
	 *
	 * @param array  $links Array of links to be shown in plugin meta.
	 * @param string $file Filename of the plugin meta is being shown for.
	 */
	public static function pmpro_sws_plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'pmpro-sitewide-sale.php' ) !== false ) {
			$new_links = array(
				'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/sitewide-sale/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro-sitewide-sale' ) . '</a>',
				'<a href="' . esc_url( 'https://www.paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro-sitewide-sale' ) . '</a>',
			);
			$links     = array_merge( $links, $new_links );
		}
		return $links;
	}

}
