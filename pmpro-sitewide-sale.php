<?php
/**
 * Plugin Name: Paid Memberships Pro - Sitewide Sale
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/pmpro-slack-integration/
 * Description: Run a sitewide sale (Black Friday, Cyber Monday, etc) with Paid Memberships Pro
 * Author: strangerstudios, dlparker1005
 * Author URI: https://www.paidmembershipspro.com
 * Version: .1
 * Plugin URI:
 * License: GNU GPLv2+
 * Text Domain: pmpro-sitewide-sale
 */

define( 'PMPROSWS_DIR', dirname( __FILE__ ) );
define( 'PMPROSWS_BASENAME', plugin_basename( __FILE__ ) );

require_once( PMPROSWS_DIR . '/includes/common.php' );
require_once( PMPROSWS_DIR . '/includes/admin.php' );
require_once( PMPROSWS_DIR . '/includes/settings.php' );