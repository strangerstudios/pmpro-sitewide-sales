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
namespace PMPro_Sitewide_Sale;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

define( 'PMPROSWS_DIR', dirname( __FILE__ ) );
define( 'PMPROSWS_BASENAME', plugin_basename( __FILE__ ) );

require 'autoload.php';

includes\classes\PMPro_SWS_Banners::init();
includes\classes\PMPro_SWS_Checkout::init();
includes\classes\PMPro_SWS_Post_Types::init();
includes\classes\PMPro_SWS_Reports::init();
includes\classes\PMPro_SWS_Settings::init();
includes\classes\PMPro_SWS_Setup::init();

