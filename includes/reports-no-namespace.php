<?php

/**
 * Report Widget, needs to be outside namespace because
 * function name is 'calculated' in core
 */
function pmpro_report_pmpro_sws_reports_widget() {
	esc_html_e( 'View reports for your most recent sales.', 'pmpro-sitewide-sale' );
	$sitewide_sales = get_posts(
		[
			'post_type' => 'pmpro_sitewide_sale',
			'post_status' => 'publish',
			'numberposts' => 5,
			'orderby' => 'ID',
			'order' => 'DESC',
		]
	);
	if ( ! empty ( $sitewide_sales ) ) {
		foreach ( $sitewide_sales as $sitewide_sale ) {
			echo '<p>';
			echo '<strong><a href="' . admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ) . '">' . esc_html( get_the_title( $sitewide_sale->ID ) ) . '</a></strong>';
			echo ' (';
			echo date_i18n( get_option( 'date_format' ), ( new \DateTime( get_post_meta( $sitewide_sale->ID, 'pmpro_sws_start_date', true ) ) )->format( 'U' ) );
			echo ' - ';
			echo date_i18n( get_option( 'date_format' ), ( new \DateTime( get_post_meta( $sitewide_sale->ID, 'pmpro_sws_end_date', true ) ) )->format( 'U' ) );
			echo ')';
			echo '</p>';
		}
	}
}

/**
 * Report Page, needs to be outside namespace because
 * function name is 'calculated' in core
 */
function pmpro_report_pmpro_sws_reports_page() {
	global $wpdb;
	$options = PMPro_Sitewide_Sale\includes\classes\PMPro_SWS_Settings::get_options();
	$sitewide_sales = get_posts(
		[
			'post_type' => 'pmpro_sitewide_sale',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby' => 'ID',
			'order' => 'DESC',
		]
	);
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	echo '<h1>' . esc_html( 'Sitewide Sale Reports', 'pmpro-sitewide-sale' ) . '</h1>';
	echo '<p><label for="pmpro_sws_sitewide_sale_select">' . esc_html__( 'Select a Sitewide Sale', 'pmpro-sitewide-sale' ) . '</label> ';
	echo '<select id="pmpro_sws_sitewide_sale_select">';

	foreach ( $sitewide_sales as $sitewide_sale ) {
		$selected_modifier = '';
		if ( $sitewide_sale->ID . '' === $active_sitewide_sale . '' ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $sitewide_sale->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( get_the_title( $sitewide_sale->ID ) ) . '</option>';
	}
	echo '</select></p>';
	echo '<div id="pmpro_sws_reports_container">';
	echo PMPro_Sitewide_Sale\includes\classes\PMPro_SWS_Reports::get_report_for_code();
	echo '</div>';
}
