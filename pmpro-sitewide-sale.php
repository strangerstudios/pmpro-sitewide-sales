<?php
/**
 * Plugin Name: Paid Memberships Pro - Sitewide Sale Add On
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/sitewide-sale/
 * Description: Run a sitewide sale (Black Friday, Cyber Monday, etc.) with Paid Memberships Pro
 * Author: strangerstudios, dlparker1005, pbrocks
 * Author URI: https://www.paidmembershipspro.com
 * Version: .1.3.2
 * Plugin URI:
 * License: GNU GPLv2+
 * Text Domain: pmpro-sitewide-sale
 *
 * @package pmpro-sitewide-sale
 */

define( 'PMPROSWS_DIR', dirname( __FILE__ ) );
define( 'PMPROSWS_BASENAME', plugin_basename( __FILE__ ) );

require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-banners.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-checkout.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-metaboxes.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-post-types.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-reports.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-settings.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-setup.php';
require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-templates.php';

PMPro_SWS_Banners::init();
PMPro_SWS_Checkout::init();
PMPro_SWS_Post_Types::init();
PMPro_SWS_Reports::init();
PMPro_SWS_Settings::init();
PMPro_SWS_Setup::init();

