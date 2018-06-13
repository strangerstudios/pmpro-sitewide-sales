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
function pmpro_report_pmpro_sws_reports_widget() {
	global $wpdb;
	$options = pmprosws_get_options();
	$code_id = $options['discount_code_id'];
	// check if discount_code_id is set.
	$reports = get_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking' );
	if ( false === $reports ) {
		$reports = array(
			'banner_impressions'     => 0,
			'landing_page_visits'    => 0,
			'langing_page_checkouts' => 0,
		);
		update_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking', $reports, 'no' );
	}

	$banner_impressions       = $reports['banner_impressions'];
	$landing_page_visits      = $reports['landing_page_visits'];
	$landing_page_conversions = $reports['langing_page_checkouts'];
	if ( is_nan( $landing_page_convert_percent ) ) {
		$landing_page_convert_percent = 0;
	}
	$landing_page_convert_percent = ( $landing_page_conversions / $landing_page_visits ) * 100;
	$orders_with_code             = $wpdb->get_results( $wpdb->prepare( "SELECT orders.total FROM $wpdb->pmpro_membership_orders orders LEFT JOIN wp_pmpro_discount_codes_uses codes ON orders.id = codes.order_id WHERE codes.code_id = %s", $code_id ) );
	$discount_code_uses           = count($orders_with_code);
	$revenue_from_sale            = 0;
	foreach ( $orders_with_code as $order ) {
		$revenue_from_sale += $order->total;
	}
?>
<span id="pmpro_sws_reports">
	<table class="wp-list-table widefat fixed striped">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Banner Impressions', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo number_format_i18n( $banner_impressions ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Landing Page Visits', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo number_format_i18n( $landing_page_visits ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Landing Page Conversions', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo number_format_i18n( $landing_page_conversions ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Landing Page Conversion %', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo number_format_i18n( $landing_page_convert_percent ) . '%'; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Discount Code Uses', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo number_format_i18n( $discount_code_uses ); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Revenue From Sale', 'pmpro_sitewide_sale' ); ?></th>
			<td><?php echo '$' . number_format_i18n( $revenue_from_sale ); ?></td>
		</tr>
	</tbody>
	</table>
</span>
<?php
}

/**
 * Report Page
 */
function pmpro_report_pmpro_sws_reports_page() {
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
	wp_register_script( 'pmpro_sws', plugins_url( 'js/pmpro-sitewide-sale.js', PMPROSWS_BASENAME ), array( 'jquery', 'utils' ) );

	$pmpro_sws_data = array(
		'landing_page'      => is_page( $options['landing_page_post_id'] ),
		'confirmation_page' => is_page( $pmpro_pages['confirmation'] ),
		'checkout_page'     => is_page( $pmpro_pages['checkout'] ),
		'discount_code_id'  => $options['discount_code_id'],
		'ajax_url'          => admin_url( 'admin-ajax.php' ),
	);

	wp_localize_script( 'pmpro_sws', 'pmpro_sws', $pmpro_sws_data );

	wp_enqueue_script( 'pmpro_sws' );

}
add_action( 'wp_enqueue_scripts', 'pmpro_sws_tracking_js' );


function pmpro_sws_ajax_tracking() {
	global $wpdb;
	$code_id = $_POST['code_id'];
	$element = $_POST['element'];
	$options = get_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking' );
	if ( false === $options ) {
		$options = array(
			'banner_impressions'     => 0,
			'landing_page_visits'    => 0,
			'langing_page_checkouts' => 0,
		);
	}
	if ( array_key_exists( $element, $options ) ) {
		$options[ $element ] += 1;
		update_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking', $options, 'no' );
	} else {
		return -1;
	}
}
add_action( 'wp_ajax_pmpro_sws_ajax_tracking', 'pmpro_sws_ajax_tracking' );
add_action( 'wp_ajax_nopriv_pmpro_sws_ajax_tracking', 'pmpro_sws_ajax_tracking' );
