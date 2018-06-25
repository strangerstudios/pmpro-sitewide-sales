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
require_once PMPROSWS_DIR . '/includes/checkout.php';
require_once PMPROSWS_DIR . '/includes/banners.php';
require_once PMPROSWS_DIR . '/includes/reports.php';
require_once PMPROSWS_DIR . '/includes/templates.php';

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
