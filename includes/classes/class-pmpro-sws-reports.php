<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Reports {

	public static function init() {
		global $pmpro_reports;

		// Functions called by adding this report are below the class.
		$pmpro_reports['pmpro_sws_reports'] = __( 'PMPro Sitewide Sale', 'pmpro_sitewide_sale' );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_reports_js' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_tracking_js' ) );
		add_action( 'wp_ajax_pmpro_sws_ajax_reporting', array( __CLASS__, 'ajax_reporting' ) );
		add_action( 'wp_ajax_pmpro_sws_ajax_tracking', array( __CLASS__, 'ajax_tracking' ) );
		add_action( 'wp_ajax_nopriv_pmpro_sws_ajax_tracking', array( __CLASS__, 'ajax_tracking' ) );
	}

	public static function ajax_reporting() {
		echo self::get_report_for_code( $_POST['sitewide_sale_id'] );
	}

	public static function get_report_for_code( $sitewide_sale_id = null ) {
		global $wpdb;
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( null === $sitewide_sale_id ) {
			$sitewide_sale_id = $active_sitewide_sale;
		}
		if ( false === $sitewide_sale_id ) {
			return __( 'No Sitewide Sale Set.', 'pmpro-sitewide-sale' );
		}
		$code_id   = get_post_meta( $active_sitewide_sale, 'discount_code_id', true ) . '';
		$code_name = $wpdb->get_results( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s", $code_id ) )[0]->code;
		// check if discount_code_id is set.
		$reports = get_option( 'pmpro_sitewide_sale_' . $sitewide_sale_id . '_tracking' );
		if ( false === $reports ) {
			$reports = array(
				'banner_impressions'   => 0,
				'landing_page_visits'  => 0,
				'landing_page_after_banner'         => 0,
				'checkout_conversions_with_code'    => 0,
				'checkout_conversions_without_code' => 0,
			);
			update_option( 'pmpro_sitewide_sale_' . $sitewide_sale_id . '_tracking', $reports, 'no' );
		}

		// Reports regarding total sales.
		$orders_during_sale   = $wpdb->get_results( $wpdb->prepare( "SELECT orders.total, orders.subscription_transaction_id, orders.timestamp, orders.user_id, orders.id, codes.code_id FROM $wpdb->pmpro_membership_orders orders LEFT JOIN wp_pmpro_discount_codes_uses codes ON orders.id = codes.order_id WHERE orders.timestamp >= %s AND orders.timestamp <= %s AND orders.total > 0", get_post_meta( $active_sitewide_sale, 'start_date', true ), date( 'Y-m-d', strtotime( '+1 day', strtotime( get_post_meta( $active_sitewide_sale, 'end_date', true ) ) ) ) ) );
		$orders_with_code     = 0;
		$new_orders_with_code = 0;
		$revenue_with_code    = 0;
		$orders_without_code  = 0;
		$revenue_without_code = 0;
		$recurring_orders     = 0;
		$recurring_revenue    = 0;
		foreach ( $orders_during_sale as $order ) {
			if ( $code_id === $order->code_id ) {
				$orders_with_code++;
				$revenue_with_code += intval( $order->total );
				$previous_orders = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $wpdb->pmpro_membership_orders WHERE timestamp < %s AND user_id = %d LIMIT 1", $order->timestamp, $order->user_id ) );
				if ( empty( $previous_orders ) ) {
					$new_orders_with_code++;
				}
			} elseif ( empty( $order->subscription_transaction_id ) || empty( $order->timestamp ) ) {
				$orders_without_code++;
				$revenue_without_code += intval( $order->total );
			} else {
				$orders_with_same_id = $wpdb->get_results( $wpdb->prepare( "SELECT id FROM $wpdb->pmpro_membership_orders WHERE timestamp < %s AND subscription_transaction_id = %s LIMIT 1", $order->timestamp, $order->subscription_transaction_id ) );
				if ( empty( $orders_with_same_id ) ) {
					$orders_without_code++;
					$revenue_without_code += intval( $order->total );
				} else {
					$recurring_orders++;
					$recurring_revenue += intval( $order->total );
				}
			}
		}
		$total_revenue = $revenue_with_code + $revenue_without_code + $recurring_revenue;
		$total_sales   = $orders_with_code + $orders_without_code + $recurring_orders;

		// Reports regarding advertising/conversions.
		$banner_impressions   = $reports['banner_impressions'];
		$landing_page_visits  = $reports['landing_page_visits'];
		$landing_page_after_banner         = $reports['landing_page_after_banner'];
		$landing_page_after_banner_percent = self::safe_divide( $landing_page_after_banner, $banner_impressions ) * 100;
		if ( is_nan( $landing_page_after_banner_percent ) ) {
			$landing_page_after_banner_percent = 0;
		}
		$landing_page_not_after_banner         = $landing_page_visits - $landing_page_after_banner;
		$landing_page_not_after_banner_percent = self::safe_divide( $landing_page_not_after_banner, $landing_page_visits ) * 100;
		if ( is_nan( $landing_page_not_after_banner_percent ) ) {
			$landing_page_not_after_banner_percent = 0;
		}
		$checkout_conversions_with_code    = $reports['checkout_conversions_with_code'];
		$checkout_conversions_without_code = $reports['checkout_conversions_without_code'];
		$checkout_conversions = $checkout_conversions_with_code + $checkout_conversions_without_code;
		$checkout_conversions_percent      = self::safe_divide( $checkout_conversions, $landing_page_visits ) * 100;
		if ( is_nan( $checkout_conversions_percent ) ) {
			$checkout_conversions_percent = 0;
		}

		$reports_to_output = array(
			'Total Sales' => array(
				'value'  => '$' . number_format_i18n( $total_revenue ) . ' (' . number_format_i18n( $total_sales ) . ')',
				'child' => false,
			),
			'With the Discount Code ' . $code_name => array(
				'value'  => '$' . number_format_i18n( $revenue_with_code ) . ' (' . number_format_i18n( $orders_with_code ) . ' Total, ' . number_format_i18n( $new_orders_with_code ) . ' New)',
				'child' => true,
			),
			'Recurring Revenue' => array(
				'value'  => '$' . number_format_i18n( $recurring_revenue ) . ' (' . number_format_i18n( $recurring_orders ) . ')',
				'child' => true,
			),
			'Other Revenue' => array(
				'value'  => '$' . number_format_i18n( $revenue_without_code ) . ' (' . number_format_i18n( $orders_without_code ) . ')',
				'child' => true,
			),
			'Banner Impressions' => array(
				'value'  => number_format_i18n( $banner_impressions ),
				'child' => false,
			),
			'Sitewide Sale Landing Page Visits' => array(
				'value'  => number_format_i18n( $landing_page_visits ),
				'child' => false,
			),
			'People Going to Sitewide Sale Page after Seeing Banner' => array(
				'value'  => number_format_i18n( $landing_page_after_banner ) . ' (' . number_format_i18n( $landing_page_after_banner_percent ) . '% of Banner Impressions)',
				'child' => true,
			),
			'People Going Directly to Sitewide Sale Page without Seeing Banner' => array(
				'value'  => number_format_i18n( $landing_page_not_after_banner ) . ' (' . number_format_i18n( $landing_page_not_after_banner_percent ) . '% of Sitewide Sale Page Visits)',
				'child' => true,
			),
			'Sales After Visiting Sitewide Sale Page' => array(
				'value'  => number_format_i18n( $checkout_conversions ) . ' (' . number_format_i18n( $checkout_conversions_percent ) . '% of Sitewide Sale Page Visits)',
				'child' => false,
			),
			'Using the Discount Code ' . $code_name => array(
				'value'  => number_format_i18n( $checkout_conversions_with_code ),
				'child' => true,
			),
			'Other Sales' => array(
				'value'  => number_format_i18n( $checkout_conversions_without_code ),
				'child' => true,
			),
		);

		/**
		 * Modify rows for Sitewide Sale reports.
		 *
		 * @since 0.0.1
		 *
		 * @param array $reports_to_output contains reports that will be converted to rows.
		 * @param string  $sitewide_sale_id id of sitewide sale to get reports for.
		 */
		$reports_to_output = apply_filters( 'pmpro_sws_reports', $reports_to_output, $sitewide_sale_id );

		$to_return = '
		<span id="pmpro_sws_reports">
			<table class="widefat fixed striped">
				<thead>
					<tr>
						<td>' . esc_html( 'Sitewide Sale', 'pmpro_sitewide_sale' ) . '</td>
						<td>' . esc_html( get_the_title( $sitewide_sale_id ) ) .
						' (' . date_i18n( get_option( 'date_format' ), ( new DateTime( get_post_meta( $active_sitewide_sale, 'start_date', true ) ) )->format( 'U' ) ) .
						' - ' . date_i18n( get_option( 'date_format' ), ( new DateTime( get_post_meta( $active_sitewide_sale, 'end_date', true ) ) )->format( 'U' ) ) . ')</td>
					</tr>
				</thead>
				<tbody>';

		foreach ( $reports_to_output as $name => $value ) {
			if ( ! is_array( $value ) || ! is_string( $name ) || ! isset( $value['value'] ) ) {
				continue;
			}
			if ( ! empty( $value['child'] && true === $value['child'] ) ) {
				$to_return .= '
				<tr>
					<td scope="row"> - ' . esc_html( $name, 'pmpro_sitewide_sale' ) . '</td>
					<td>' . esc_html( $value['value'], 'pmpro_sitewide_sale' ) . '</td>
				</tr>
				';
			} else {
				$to_return .= '
				<tr>
					<td scope="row"><strong>' . esc_html( $name, 'pmpro_sitewide_sale' ) . '</strong></td>
					<td><strong>' . esc_html( $value['value'], 'pmpro_sitewide_sale' ) . '</strong></td>
				</tr>
				';
			}
		}

		$to_return .= '
				</tbody>
			</table>
		</span>';
		return $to_return;
	}

	public static function safe_divide( $num, $denom ) {
		if ( $denom <= 0 ) {
			if ( $num <= 0 ) {
				return 0;
			}
			return 1; // 100%
		}
		return $num / $denom;
	}

	/**
	 * Enqueues js for switching sale that is being reported
	 */
	public static function enqueue_reports_js() {
		if ( isset( $_REQUEST['page'] ) && 'pmpro-reports' === $_REQUEST['page'] ) {
			wp_register_script( 'pmpro_sws_reports', plugins_url( 'js/pmpro-sws-reports.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
			wp_enqueue_script( 'pmpro_sws_reports' );
		}
	}

	/**
	 * Setup JS vars and enqueue our JS
	 */
	public static function enqueue_tracking_js() {
		global $pmpro_pages;

		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		wp_register_script( 'pmpro_sws_tracking', plugins_url( 'js/pmpro-sws-tracking.js', PMPROSWS_BASENAME ), array( 'jquery', 'utils' ) );

		$used_discount_code = 0;
		if ( is_page( $pmpro_pages['confirmation'] ) ) {
			$order = new MemberOrder();
			$order->getLastMemberOrder();
			$code = $order->getDiscountCode()->id;
			if ( $code . '' === get_post_meta( $active_sitewide_sale, 'discount_code_id', true ) . '' ) {
				$used_discount_code = 1;
			}
		}

		$pmpro_sws_data = array(
			'landing_page'      => is_page( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ),
			'confirmation_page' => is_page( $pmpro_pages['confirmation'] ),
			'checkout_page'     => is_page( $pmpro_pages['checkout'] ),
			'used_sale_code'    => $used_discount_code,
			'sitewide_sale_id'  => $active_sitewide_sale,
			'ajax_url'          => admin_url( 'admin-ajax.php' ),
		);

		wp_localize_script( 'pmpro_sws_tracking', 'pmpro_sws', $pmpro_sws_data );

		wp_enqueue_script( 'pmpro_sws_tracking' );

	}


	public static function ajax_tracking() {
		global $wpdb;
		$sitewide_sale_id = $_POST['sitewide_sale_id'];
		$element = $_POST['element'];
		$reports = get_option( 'pmpro_sitewide_sale_' . $sitewide_sale_id . '_tracking' );
		if ( false === $reports ) {
			$reports = array(
				'banner_impressions'   => 0,
				'landing_page_visits'  => 0,
				'landing_page_after_banner'         => 0,
				'checkout_conversions_with_code'    => 0,
				'checkout_conversions_without_code' => 0,
			);
		}
		if ( array_key_exists( $element, $reports ) ) {
			$reports[ $element ] += 1;
			update_option( 'pmpro_sitewide_sale_' . $sitewide_sale_id . '_tracking', $reports, 'no' );
		} else {
			return -1;
		}
	}
}

/**
 * Report Widget, needs to be outside class because
 * function name is 'calculated'in core
 */
function pmpro_report_pmpro_sws_reports_widget() {
	echo PMPro_SWS_Reports::get_report_for_code();
}

/**
 * Report Page, needs to be outside class because
 * function name is 'calculated'in core
 */
function pmpro_report_pmpro_sws_reports_page() {
	global $wpdb;
	$options = PMPro_SWS_Settings::pmprosws_get_options();
	$sitewide_sales = get_posts(
		[
			'post_type' => 'sws_sitewide_sale',
			'post_status' => 'publish',
			'numberposts' => -1,
		]
	);
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	echo '<table><tr><td><h3>' . esc_html( 'Choose Sitewide Sale to View Reports For', 'pmpro-sitewide-sale' ) . ': </h3></td><td><select id="pmpro_sws_sitewide_sale_select">';

	foreach ( $sitewide_sales as $sitewide_sale ) {
		$selected_modifier = '';
		if ( $sitewide_sale->ID . '' === $active_sitewide_sale . '' ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $sitewide_sale->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( get_the_title( $sitewide_sale->ID ), 'pmpro-sitewide-sale' ) . '</option>';
	}
	echo '</select></td></tr></table>';
	echo '<div id="pmpro_sws_reports_container">';
	echo PMPro_SWS_Reports::get_report_for_code();
	echo '</div>';
}
