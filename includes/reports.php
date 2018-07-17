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
	echo pmpro_sws_get_report_for_code();
}

/**
 * Report Page
 */
function pmpro_report_pmpro_sws_reports_page() {
	global $wpdb;
	$options          = pmprosws_get_options();
	$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
	$current_discount = $options['discount_code_id'];
	echo '<table><tr><td><h3>Choose Code to View Reports For: </h3></td><td><select id="pmpro_sws_discount_code_select">';
	foreach ( $codes as $code ) {
		$selected_modifier = '';
		if ( $code->id === $current_discount ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $code->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $code->code, 'pmpro-sitewide-sale' ) . '</option>';
	}
	echo '</select></td></tr></table>';
	echo '<div id="pmpro_sws_reports_container">';
	echo pmpro_sws_get_report_for_code();
	echo '</div>';
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_discount_code_select").selectWoo();
			jQuery("#pmpro_sws_discount_code_select").change(function() {
				var data = {
					'action': 'pmpro_sws_ajax_reporting',
					'code_id': jQuery("#pmpro_sws_discount_code_select").val()
				};
				jQuery.post('<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>', data, function(response) {
					jQuery("#pmpro_sws_reports_container").html(response.slice(0, -1));
				});
			});
		});
	</script>
	<?php
}

function pmpro_sws_ajax_reporting() {
	echo pmpro_sws_get_report_for_code( $_POST['code_id'] );
}
add_action( 'wp_ajax_pmpro_sws_ajax_reporting', 'pmpro_sws_ajax_reporting' );

function pmpro_sws_get_report_for_code( $code_id = null ) {
	global $wpdb;
	$options = pmprosws_get_options();
	if ( null === $code_id ) {
		$code_id = $options['discount_code_id'];
	}
	if ( false === $code_id ) {
		return 'No Discount Code Set.';
	}
	$code_id = $code_id . '';
	$code_name = $wpdb->get_results( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s", $code_id ) )[0]->code;
	// check if discount_code_id is set.
	$reports = get_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking' );
	if ( false === $reports ) {
		$reports = array(
			'banner_impressions'                => 0,
			'landing_page_visits'               => 0,
			'landing_page_after_banner'         => 0,
			'checkout_conversions_with_code'    => 0,
			'checkout_conversions_without_code' => 0,
		);
		update_option( 'pmpro_sitewide_sale_' . $code_id . '_tracking', $reports, 'no' );
	}

	// Reports regarding total sales.
	$discount_code_dates  = $wpdb->get_results( $wpdb->prepare( "SELECT starts, expires FROM $wpdb->pmpro_discount_codes where id = %d", intval($code_id) ) )[0];
	$orders_during_sale   = $wpdb->get_results( $wpdb->prepare( "SELECT orders.total, codes.code_id FROM $wpdb->pmpro_membership_orders orders LEFT JOIN wp_pmpro_discount_codes_uses codes ON orders.id = codes.order_id WHERE orders.timestamp >= %s AND orders.timestamp <= %s", $discount_code_dates->starts, date("Y-m-d", strtotime( '+1 day', strtotime( $discount_code_dates->expires ) ) ) ) );
	$orders_with_code     = 0;
	$revenue_with_code    = 0;
	$orders_without_code  = 0;
	$revenue_without_code = 0;
	foreach ( $orders_during_sale as $order ) {
		if ( $code_id === $order->code_id ) {
			$orders_with_code++;
			$revenue_with_code += intval( $order->total );
		} else {
			$orders_without_code++;
			$revenue_without_code += intval( $order->total );
		}
	}
	$total_revenue = $revenue_with_code + $revenue_without_code;
	$total_sales   = $orders_with_code + $orders_without_code;

	// Reports regarding advertising/conversions.
	$banner_impressions                    = $reports['banner_impressions'];
	$landing_page_visits                   = $reports['landing_page_visits'];
	$landing_page_after_banner             = $reports['landing_page_after_banner'];
	$landing_page_after_banner_percent     = pmpro_sws_divide_into_percent( $landing_page_after_banner, $banner_impressions );
	$landing_page_not_after_banner         = $landing_page_visits - $landing_page_after_banner;
	$landing_page_not_after_banner_percent = pmpro_sws_divide_into_percent( $landing_page_not_after_banner, $landing_page_visits );
	$checkout_conversions_with_code        = $reports['checkout_conversions_with_code'];
	$checkout_conversions_without_code     = $reports['checkout_conversions_without_code'];
	$checkout_conversions                  = $checkout_conversions_with_code + $checkout_conversions_without_code;
	$checkout_conversions_percent          = pmpro_sws_divide_into_percent( $checkout_conversions, $landing_page_visits );

	return '
	<span id="pmpro_sws_reports">
		<table class="widefat fixed striped">
			<thead>
				<tr>
					<td>' . esc_html( 'Code', 'pmpro_sitewide_sale' ) . '</td>
					<td>' . esc_html( $code_name ) . '</td>
				</tr>
			</thead>
			<tbody>
					<th scope="row"><strong>' . esc_html( 'Total Sales', 'pmpro_sitewide_sale' ) . '</strong></th>
					<th><strong>' . '$' . number_format_i18n( $total_revenue ) . ' (' . number_format_i18n( $total_sales ) . ')</strong></th>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- With the Discount Code "', 'pmpro_sitewide_sale' ) . $code_name . '"</td>
					<td>' . '$' . number_format_i18n( $revenue_with_code ) . ' (' . number_format_i18n( $orders_with_code ) . ')</td>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- Other Revenue', 'pmpro_sitewide_sale' ) . '</td>
					<td>' . '$' . number_format_i18n( $revenue_without_code ) . ' (' . number_format_i18n( $orders_without_code ) . ')</td>
				</tr>
				<tr>
					<th scope="row"><strong>' . esc_html( 'Banner Impressions', 'pmpro_sitewide_sale' ) . '</strong></th>
					<th><strong>' . number_format_i18n( $banner_impressions ) . '</strong></th>
				</tr>
				<tr>
					<th scope="row"><strong>' . esc_html( 'Sitewide Sale Page Visits', 'pmpro_sitewide_sale' ) . '</strong></th>
					<th><strong>' . number_format_i18n( $landing_page_visits ) . '</strong></th>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- People Going to Sitewide Sale Page after Seeing Banner', 'pmpro_sitewide_sale' ) . '</td>
					<td>' . number_format_i18n( $landing_page_after_banner ) . ' (' . number_format_i18n( $landing_page_after_banner_percent ) . '% of Banner Impressions)</td>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- People Going Directly to Sitewide Sale Page without Seeing Banner', 'pmpro_sitewide_sale' ) . '</td>
					<td>' . number_format_i18n( $landing_page_not_after_banner ) . ' (' . number_format_i18n( $landing_page_not_after_banner_percent ) . '% of Sitewide Sale Page Visits)</td>
				</tr>
				<tr>
					<th scope="row"><strong>' . esc_html( 'Sales After Visiting Sitewide Sale Page', 'pmpro_sitewide_sale' ) . '</strong></th>
					<th><strong>' . number_format_i18n( $checkout_conversions ) . ' (' . number_format_i18n( $checkout_conversions_percent ) . '% of Sitewide Sale Page Visits)</strong></th>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- Using the Discount Code "', 'pmpro_sitewide_sale' ) . $code_name . '"</td>
					<td>' . number_format_i18n( $checkout_conversions_with_code ) . '</th>
				</tr>
				<tr>
					<td scope="row">' . esc_html( '- Other Sales', 'pmpro_sitewide_sale' ) . '</td>
					<td>' . number_format_i18n( $checkout_conversions_without_code ) . '</td>
				</tr>
			</tbody>
		</table>
	</span>';
}

/**
 * Used to calculate percentge-based stats about sale
 * @param  int $num   numerator of division
 * @param  int $denom denominator of division
 * @return int        percentage
 */
function pmpro_sws_divide_into_percent( $num, $denom ) {
	if ( $denom <= 0 ) {
		if ( $num <= 0 ) {
			return 0;
		}
		return 100; // 100%
	}
	return ( $num / $denom ) * 100;
}

/**
 * Setup JS vars and enqueue our JS
 */
function pmpro_sws_tracking_js() {
	global $pmpro_pages;

	$options = pmprosws_get_options();
	wp_register_script( 'pmpro_sws', plugins_url( 'js/pmpro-sitewide-sale.js', PMPROSWS_BASENAME ), array( 'jquery', 'utils' ) );

	$used_discount_code = 0;
	if ( is_page( $pmpro_pages['confirmation'] ) ) {
		$order = new MemberOrder();
		$order->getLastMemberOrder();
		$code = $order->getDiscountCode()->id;
		if ( $code . '' === $options['discount_code_id'] . '' ) {
			$used_discount_code = 1;
		}
	}

	$pmpro_sws_data = array(
		'landing_page'      => is_page( $options['landing_page_post_id'] ),
		'confirmation_page' => is_page( $pmpro_pages['confirmation'] ),
		'checkout_page'     => is_page( $pmpro_pages['checkout'] ),
		'used_sale_code'    => $used_discount_code,
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
			'banner_impressions'                => 0,
			'landing_page_visits'               => 0,
			'landing_page_after_banner'         => 0,
			'checkout_conversions_with_code'    => 0,
			'checkout_conversions_without_code' => 0,
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
