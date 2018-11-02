<?php

namespace PMPro_Sitewide_Sales\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Reports {

	/**
	 * Adds actions for class
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'assign_pmpro_sws_reports' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_reports_js' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_tracking_js' ) );
		add_action( 'wp_ajax_pmpro_sws_ajax_reporting', array( __CLASS__, 'ajax_reporting' ) );
		add_action( 'wp_ajax_pmpro_sws_ajax_tracking', array( __CLASS__, 'ajax_tracking' ) );
		add_action( 'wp_ajax_nopriv_pmpro_sws_ajax_tracking', array( __CLASS__, 'ajax_tracking' ) );
	}

	/**
	 * Adds SWS Report to PMPro Reports
	 */
	public static function assign_pmpro_sws_reports() {
		global $pmpro_reports;
		// Functions called by adding this report are below the class.
		$pmpro_reports['pmpro_sws_reports'] = __( 'Sitewide Sale', 'pmpro-sitewide-sales' );
		return $pmpro_reports;
	}

	/**
	 * Allows reports for a given sale to be generated in JS
	 */
	public static function ajax_reporting() {
		$ajax_dropdown = self::get_report_for_code( $_POST['sitewide_sale_id'] );
		echo $ajax_dropdown;
		exit;
	}

	/**
	 * Gets the statistics table for a Sitewide Sale
	 *
	 * @param  int $sitewide_sale_id ID of sale to get stats for, null is current active sale
	 * @return String                HTML table
	 */
	public static function get_report_for_code( $sitewide_sale_id = null ) {
		global $wpdb;
		$options = PMPro_SWS_Settings::get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( null === $sitewide_sale_id ) {
			$sitewide_sale_id = $active_sitewide_sale;
		}
		if ( false === $sitewide_sale_id ) {
			$sitewide_sales = get_posts(
				[
					'post_type' => 'pmpro_sitewide_sale',
					'post_status' => 'publish',
					'numberposts' => 1,
					'orderby' => 'ID',
					'order' => 'DESC',
				]
			);
			$sitewide_sale_id = $sitewide_sales[0]->ID;
		}
		$code_id = get_post_meta( $sitewide_sale_id, 'pmpro_sws_discount_code_id', true ) . '';

		if ( ! empty( $code_id ) ) {
			$code_name = $wpdb->get_var( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s LIMIT 1", $code_id ) );
		}

		if ( empty( $code_name ) ) {
			return __( 'No discount code set.', 'pmpro-sitewide-sale' );
		}

		// check if discount_code_id is set.
		$reports = get_option( 'pmpro_sws_' . $sitewide_sale_id . '_tracking' );
		if ( false === $reports ) {
			$reports = array(
				'banner_impressions'   => 0,
				'landing_page_visits'  => 0,
				'landing_page_after_banner'         => 0,
				'checkout_conversions_with_code'    => 0,
				'checkout_conversions_without_code' => 0,
			);
			update_option( 'pmpro_sws_' . $sitewide_sale_id . '_tracking', $reports, 'no' );
		}

		// Reports regarding total sales.
		$orders_during_sale   = $wpdb->get_results( $wpdb->prepare( "SELECT orders.total, orders.subscription_transaction_id, orders.timestamp, orders.user_id, orders.id, codes.code_id FROM $wpdb->pmpro_membership_orders orders LEFT JOIN wp_pmpro_discount_codes_uses codes ON orders.id = codes.order_id WHERE orders.timestamp >= %s AND orders.timestamp <= %s AND orders.total > 0", get_post_meta( $sitewide_sale_id, 'pmpro_sws_start_date', true ), date( 'Y-m-d', strtotime( '+1 day', strtotime(get_post_meta( $sitewide_sale_id, 'pmpro_sws_end_date', true ) ) ) ) ) );
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
		$banner_impressions                    = $reports['banner_impressions'];
		$landing_page_visits                   = $reports['landing_page_visits'];
		$landing_page_after_banner             = $reports['landing_page_after_banner'];
		$landing_page_after_banner_percent     = self::divide_into_percent( $landing_page_after_banner, $banner_impressions );
		$landing_page_not_after_banner         = $landing_page_visits - $landing_page_after_banner;
		$landing_page_not_after_banner_percent = self::divide_into_percent( $landing_page_not_after_banner, $landing_page_visits );
		$checkout_conversions_with_code        = $reports['checkout_conversions_with_code'];
		$checkout_conversions_without_code     = $reports['checkout_conversions_without_code'];
		$checkout_conversions                  = $checkout_conversions_with_code + $checkout_conversions_without_code;
		$checkout_conversions_percent          = self::divide_into_percent( $checkout_conversions, $landing_page_visits );

		$reports_to_output = array(
			'Total Sales During Sale Period' => array(
				'value'  => '$' . number_format_i18n( $total_revenue ) . ' (' . number_format_i18n( $total_sales ) . ')',
				'child' => false,
			),
			'Using the Discount Code ' . $code_name => array(
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
			'Landing Page Visits After Banner Impression' => array(
				'value'  => number_format_i18n( $landing_page_after_banner ) . ' (' . number_format_i18n( $landing_page_after_banner_percent ) . '% of Banner Impressions)',
				'child' => true,
			),
			'Landing Page Visits Without Banner Impression' => array(
				'value'  => number_format_i18n( $landing_page_not_after_banner ) . ' (' . number_format_i18n( $landing_page_not_after_banner_percent ) . '% of Sitewide Sale Page Visits)',
				'child' => true,
			),
			'Sales After Landing Page Visit' => array(
				'value'  => number_format_i18n( $checkout_conversions ),
				'child' => false,
			),
			'Landing Page Conversion Rate' => array(
				'value'  =>  number_format_i18n( $checkout_conversions_percent ) . '%',
				'child' => true,
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

		$to_return = '<span id="pmpro_sws_reports">';
		$to_return .= '<hr /><h2>' . esc_html( get_the_title( $sitewide_sale_id ) ) . '</h2>';
		$to_return .= date_i18n( get_option( 'date_format' ), ( new \DateTime( get_post_meta( $sitewide_sale_id, 'pmpro_sws_start_date', true ) ) )->format( 'U' ) );
		$to_return .= ' - ';
		$to_return .= date_i18n( get_option( 'date_format' ), ( new \DateTime( get_post_meta( $sitewide_sale_id, 'pmpro_sws_end_date', true ) ) )->format( 'U' ) );
		$to_return .= '<table class="wp-list-table widefat striped"><tbody>';

		foreach ( $reports_to_output as $name => $value ) {
			if ( ! is_array( $value ) || ! is_string( $name ) || ! isset( $value['value'] ) ) {
				continue;
			}
			if ( ! empty( $value['child'] && true === $value['child'] ) ) {
				$to_return .= '
				<tr>
					<td scope="row"> - ' . esc_textarea( $name ) . '</td>
					<td>' . esc_textarea( $value['value'] ) . '</td>
				</tr>
				';
			} else {
				$to_return .= '
				<tr>
					<td scope="row"><h2><strong>' . esc_textarea( $name ) . '</strong></h2></td>
					<td><h2><strong>' . esc_textarea( $value['value'] ) . '</strong></h2></td>
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

	/**
	 * Used to calculate percentge-based stats about sale
	 *
	 * @param  int $num   numerator of division.
	 * @param  int $denom denominator of division.
	 * @return int        percentage
	 */
	public static function divide_into_percent( $num, $denom ) {
		if ( $denom <= 0 ) {
			if ( $num <= 0 ) {
				return 0;
			}
			return 100; // 100%
		}
		return ( $num / $denom ) * 100;
	}

	/**
	 * Enqueues js for switching sale being displayed in reports
	 */
	public static function enqueue_reports_js() {
		if ( isset( $_REQUEST['page'] ) && 'pmpro-reports' === $_REQUEST['page'] ) {
			wp_register_script( 'pmpro_sws_reports', plugins_url( 'includes/js/pmpro-sws-reports.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
			wp_enqueue_script( 'pmpro_sws_reports' );
		}
	}

	/**
	 * Setup JS vars and enqueue our JS for tracking user behavior
	 */
	public static function enqueue_tracking_js() {
		global $pmpro_pages;

		$options = PMPro_SWS_Settings::get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		wp_register_script( 'pmpro_sws_tracking', plugins_url( 'includes/js/pmpro-sws-tracking.js', PMPROSWS_BASENAME ), array( 'jquery', 'utils' ) );

		$used_discount_code = 0;
		if ( is_page( $pmpro_pages['confirmation'] ) ) {
			$order = new \MemberOrder();
			$order->getLastMemberOrder();
			if ( isset( $order->id ) ) {
				$code = $order->getDiscountCode();
				if ( isset( $code->id ) && $code->id . '' === get_post_meta( $active_sitewide_sale, 'pmpro_sws_discount_code_id', true ) . '' ) {
					$used_discount_code = 1;
				}
			}
		}

		$pmpro_sws_data = array(
			'landing_page'      => is_page( get_post_meta( $active_sitewide_sale, 'pmpro_sws_landing_page_post_id', true ) ),
			'confirmation_page' => is_page( $pmpro_pages['confirmation'] ),
			'checkout_page'     => is_page( $pmpro_pages['checkout'] ),
			'used_sale_code'    => $used_discount_code,
			'sitewide_sale_id'  => $active_sitewide_sale,
			'ajax_url'          => admin_url( 'admin-ajax.php' ),
		);

		wp_localize_script( 'pmpro_sws_tracking', 'pmpro_sws', $pmpro_sws_data );

		wp_enqueue_script( 'pmpro_sws_tracking' );

	}

	/**
	 * Ajax call to update SWS statistics
	 */
	public static function ajax_tracking() {
		global $wpdb;
		$sitewide_sale_id = $_POST['sitewide_sale_id'];
		$element = $_POST['element'];
		$reports = get_option( 'pmpro_sws_' . $sitewide_sale_id . '_tracking' );
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
			update_option( 'pmpro_sws_' . $sitewide_sale_id . '_tracking', $reports, 'no' );
		} else {
			return -1;
		}
	}
}
