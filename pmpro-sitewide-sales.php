<?php
/**
 * Plugin Name: Paid Memberships Pro - Sitewide Sales Add On
 * Plugin URI: https://www.paidmembershipspro.com/add-ons/sitewide-sales/
 * Description: Run sitewide sales (Black Friday, Cyber Monday, etc.) with Paid Memberships Pro
 * Author: Paid Memberships Pro
 * Author URI: https://www.paidmembershipspro.com
 * Version: 1.0.1
 * Plugin URI:
 * License: GNU GPLv2+
 * Text Domain: pmpro-sitewide-sales
 *
 * @package pmpro-sitewide-sales
 */
namespace PMPro_Sitewide_Sales;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

define( 'PMPROSWS_DIR', dirname( __FILE__ ) );
define( 'PMPROSWS_BASENAME', plugin_basename( __FILE__ ) );

require 'autoload.php';
require 'includes/reports-no-namespace.php';

// Handles registering banners and displaying banners on frontend.
includes\classes\PMPro_SWS_Banners::init();

// Handles applying discount automatically and inserting upsell offers.
includes\classes\PMPro_SWS_Checkout::init();

// Sets up shortcode [pmpro_sws] and landing page-related code.
includes\classes\PMPro_SWS_Landing_Pages::init();

// Handles displaying/saving metaboxes for Sitewide Sale CPT and
// returning from editing a discount code/landing page associated
// with Sitewide Sale.
includes\classes\PMPro_SWS_MetaBoxes::init();

// Sets up Sitewide Sale CPT and associated menu.
includes\classes\PMPro_SWS_Post_Types::init();

// Generates report pages and enqueues JS to track interaction
// with Sitewide Sale.
includes\classes\PMPro_SWS_Reports::init();

// Sets up pmpro_sitewide_sale option.
includes\classes\PMPro_SWS_Settings::init();

// Enqueues scripts and does other administrative things.
includes\classes\PMPro_SWS_Setup::init();
