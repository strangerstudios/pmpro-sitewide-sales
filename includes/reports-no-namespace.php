<?php

/**
 * Report Widget, needs to be outside namespace because
 * function name is 'calculated'in core
 */
function pmpro_report_pmpro_sws_reports_widget() {
	echo PMPro_Sitewide_Sale\includes\classes\PMPro_SWS_Reports::get_report_for_code();
}

/**
 * Report Page, needs to be outside namespace because
 * function name is 'calculated'in core
 */
function pmpro_report_pmpro_sws_reports_page() {
	global $wpdb;
	$options = PMPro_Sitewide_Sale\includes\classes\PMPro_SWS_Settings::pmprosws_get_options();
	$sitewide_sales = get_posts(
		[
			'post_type' => 'pmpro_sitewide_sale',
			'post_status' => 'publish',
			'numberposts' => -1,
		]
	);
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	echo '<table><tr><td><h3>' . esc_html__( 'Choose Sitewide Sale to View Reports For', 'pmpro-sitewide-sale' ) . ': </h3></td><td><select id="pmpro_sws_sitewide_sale_select">';

	foreach ( $sitewide_sales as $sitewide_sale ) {
		$selected_modifier = '';
		if ( $sitewide_sale->ID . '' === $active_sitewide_sale . '' ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $sitewide_sale->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( get_the_title( $sitewide_sale->ID ) ) . '</option>';
	}
	echo '</select></td></tr></table>';
	echo '<div id="pmpro_sws_reports_container">';
	echo PMPro_Sitewide_Sale\includes\classes\PMPro_SWS_Reports::get_report_for_code();
	echo '</div>';
}
