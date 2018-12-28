=== Paid Memberships Pro - Sitewide Sales Add On ===
Contributors: strangerstudios, dlparker1005, pbrocks
Tags: paid memberships pro, pmpro, memberships, ecommerce
Requires at least:
Tested up to: 4.9.8
Stable tag: 1.1.1

Create, manage, and view advanced reports for a sitewide or flash sale on membership (Black Friday or Cyber Monday) using Paid Memberships Pro.

== Description ==

This plugin requires Paid Memberships Pro to function.

The Sitewide Sales Add On allows you to create flash or sitewide sales. A Sitewide Sale CPT allows you to create multiple sales, each with an associated discount code and landing page. The plugin will automatically apply a discount code for users who visit the sale landing page.

The plugin also adds the option to display sitewide page banners to advertise your sale and gives you statistics about the outcome of your sale.

== Installation ==

1. Upload the `pmpro-sitewide-sales` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Create a new `Sitewide Sale` under `Memberships` > `Sitewide Sales` > `Add New`.

Documentation for additional settings can be found here: https://www.paidmembershipspro.com/add-ons/sitewide-sales/

== Changelog ==

= 1.1.1 =
* BUG FIX: Buy link in bottom banner is fixed now.

= 1.1 = 
* BUG FIX: Render (but keep hidden) the edit code button even if there is no code set for the sale yet.
* BUG FIX: Now correctly passing start and end date to the create discount code ajax.
* BUG FIX: Fixed end date check for banner.
* BUG FIX/ENHANCEMENT: Applying 'the_content' filter to the pre and post sale content
* ENHANCEMENT: Adding new "vintage" template style -- since 2018-11-10;

= 1.0 =
* Initial Release
