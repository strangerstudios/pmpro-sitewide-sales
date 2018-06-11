<?php
/*
	PMPro Sitewide Sale Reports
	Title: pmpro_sws_reports
	Slug: pmpro_sws_reports
	
	For each report, add a line like:
	global $pmpro_reports;
	$pmpro_reports['slug'] = 'Title';
	
	For each report, also write two functions:
	* pmpro_report_{slug}_widget()   to show up on the report homepage.
	* pmpro_report_{slug}_page()     to show up when users click on the report page widget.
*/
global $pmpro_reports;
$pmpro_reports['pmpro_sws_reports'] = __('PMPro Sitewide Sale', 'pmpro_sitewide_sale');

/**
 * Report Widget
 */
function pmpro_sws_reports_widget() {	
?>
<p>This is what shows up on the main reports page.</p>
<?php
}

/** 
 * Report Page
 */
function pmpro_sws_reports_page() {
?>
<h2>
	<?php _e('PMPro Sitewide Sales', 'ppmpro_sitewide_sale');?>
</h2>
<p>This shows up when you click on the report.</p>
<hr />
<?php
}

/**
 * Setup JS vars and enqueue our JS
 */
function pmpro_sws_tracking_js() {
	global $pmpro_pages;

	$options = pmprosws_get_options();

	wp_register_script( 'pmpro_sws', plugins_url( 'js/pmpro-sitewide-sale.js', PMPROSWS_BASENAME ), array( 'jquery', 'wpCookies') );

	$pmpro_sws_data = array(
		'landing_page' => is_page( $options['landing_page_post_id']),
		'confirmation_page' => is_page( $pmpro_pages['confirmation'] ),
		'checkout_page' => is_page( $pmpro_pages['checkout'] ),
	);

	wp_localize_script( 'pmpro_sws', 'pmpro_sws', $pmpro_sws_data );

	wp_enqueue_script( 'pmpro_sws' );

}
add_action( 'wp_enqueue_scripts', 'pmpro_sws_tracking_js' );


