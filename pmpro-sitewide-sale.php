<?php
/**
 * Plugin Name: Paid Memberships Pro - Sitewide Sale Add On
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/sitewide-sale/
 * Description: Run a sitewide sale (Black Friday, Cyber Monday, etc.) with Paid Memberships Pro
 * Author: strangerstudios, dlparker1005
 * Author URI: https://www.paidmembershipspro.com
 * Version: .1
 * Plugin URI:
 * License: GNU GPLv2+
 * Text Domain: pmpro-sitewide-sale
 *
 * @package pmpro-sitewide-sale
 */

define( 'PMPROSWS_DIR', dirname( __FILE__ ) );
define( 'PMPROSWS_BASENAME', plugin_basename( __FILE__ ) );

require_once PMPROSWS_DIR . '/includes/common.php';
require_once PMPROSWS_DIR . '/includes/admin.php';
require_once PMPROSWS_DIR . '/includes/settings.php';
require_once PMPROSWS_DIR . '/includes/automatic-discount.php';
require_once PMPROSWS_DIR . '/includes/banners.php';

add_action( 'admin_enqueue_scripts', 'pmpro_sws_admin_scripts' );
/**
 * Enqueues selectWoo
 */
function pmpro_sws_admin_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_script( 'selectWoo', '/wp-content/plugins/pmpro-sitewide-sale/js/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
	wp_enqueue_script( 'selectWoo' );
	wp_register_style( 'selectWooCSS', '/wp-content/plugins/pmpro-sitewide-sale/css/selectWoo' . $suffix . '.css' );
	wp_enqueue_style( 'selectWooCSS' );
}

/**
 * Runs only when the plugin is activated.
 *
 * @since 0.1.0
 */
function pmpro_sws_admin_notice_activation_hook() {
	// Create transient data.
	set_transient( 'pmpro-sws-admin-notice', true, 5 );
}
register_activation_hook( PMPROSWS_BASENAME, 'pmpro_sws_admin_notice_activation_hook' );

/**
 * Admin Notice on Activation.
 *
 * @since 0.1.0
 */
function pmpro_sws_admin_notice() {
	// Check transient, if available display notice.
	if ( get_transient( 'pmpro-sws-admin-notice' ) ) { ?>
		<div class="updated notice is-dismissible">
			<p><?php printf( __( 'Thank you for activating. <a href="%s">Visit the settings page</a> to get started with the Sitewide Sale Add On.', 'pmpro-sitewide-sale' ), get_admin_url( null, 'admin.php?page=pmpro-sitewide-sale' ) ); ?></p>
		</div>
		<?php
		// Delete transient, only display this notice once.
		delete_transient( 'pmpro-sws-admin-notice' );
	}
}
add_action( 'admin_notices', 'pmpro_sws_admin_notice' );

/**
 * Function to add links to the plugin action links
 *
 * @param array $links Array of links to be shown in plugin action links.
 */
function pmpro_sws_plugin_action_links( $links ) {
	if ( current_user_can( 'manage_options' ) ) {
		$new_links = array(
			'<a href="' . get_admin_url( null, 'admin.php?page=pmpro-sidewide-sale' ) . '">' . __( 'Settings', 'pmpro-sidewide-sale' ) . '</a>',
		);
	}
	return array_merge( $new_links, $links );
}
add_filter( 'plugin_action_links_' . PMPROBP_BASENAME, 'pmpro_sws_plugin_action_links' );

/**
 * Function to add links to the plugin row meta
 *
 * @param array  $links Array of links to be shown in plugin meta.
 * @param string $file Filename of the plugin meta is being shown for.
 */
function pmpro_sws_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-sitewide-sale.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/sitewide-sale/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro-sitewide-sale' ) . '</a>',
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro-sitewide-sale' ) . '</a>',
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'pmpro_sws_plugin_row_meta', 10, 2 );
