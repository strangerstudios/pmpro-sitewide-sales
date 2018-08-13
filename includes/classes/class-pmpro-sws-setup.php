<?php

namespace PMPro_Sitewide_Sale\includes\classes;

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
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'pmpro_sws_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'pmpro_sws_frontend_scripts' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'pmpro_sws_plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . PMPROSWS_BASENAME, array( __CLASS__, 'pmpro_sws_plugin_action_links' ) );
		add_action( 'admin_notices', array( __CLASS__, 'pmpro_sws_admin_notice' ) );
	}
	public static function pmpro_sws_cpt_name() {
		$label = 'All PMPro CPTs';
		return $label;
	}

	/**
	 * Enqueues selectWoo
	 */
	public static function pmpro_sws_admin_scripts() {
		$screen = get_current_screen();

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'selectWoo', plugins_url( 'includes/js/selectWoo.full' . $suffix . '.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'selectWoo' );
		wp_register_style( 'selectWooCSS', plugins_url( 'includes/css/selectWoo' . $suffix . '.css', PMPROSWS_BASENAME ) );
		wp_enqueue_style( 'selectWooCSS' );
	}

	/**
	 * Enqueues selectWoo
	 */
	public static function pmpro_sws_frontend_scripts() {
		wp_register_style( 'frontend', plugins_url( 'includes/css/frontend.css', PMPROSWS_BASENAME ), '1.1' );
		wp_enqueue_style( 'frontend' );
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
				<p><?php printf( __( 'Thank you for activating. You can create your first Sitewide Sale <a href="%s">here</a>.', 'pmpro-sitewide-sale' ), get_admin_url( null, 'edit.php?post_type=sws_sitewide_sale' ) ); ?></p>
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
