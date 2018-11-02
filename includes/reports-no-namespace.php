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
	$options = PMPro_Sitewide_Sales\includes\classes\PMPro_SWS_Settings::get_options();
	$sitewide_sales = get_posts(
		[
			'post_type' => 'pmpro_sitewide_sale',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby' => 'ID',
			'order' => 'DESC',
		]
	);
	if( ! empty( $_REQUEST['pmpro_sws_sitewide_sale_id'] ) ) {
		$sitewide_sale_id = intval( $_REQUEST['pmpro_sws_sitewide_sale_id'] );
	} else {
		$sitewide_sale_id = $options['active_sitewide_sale_id'];
	}

	$stats = PMPro_Sitewide_Sales\includes\classes\PMPro_SWS_Reports::get_stats_for_sale( $sitewide_sale_id );

	?>
	<form id="posts-filter" method="get" action="">
		<h1>
			<?php esc_html_e( 'Sitewide Sales Report', 'pmpro-sitewide-sales' );?>
		</h1>
		<ul class="subsubsub">
			<li>
				<?php esc_html_e( 'Show reports for ', 'pmpro-sitewide-sales' );?>
				<select name="pmpro_sws_sitewide_sale_id">
				<?php
					foreach ( $sitewide_sales as $sitewide_sale ) {
						echo '<option value="' . esc_attr( $sitewide_sale->ID ) . '" ' . selected( $sitewide_sale_id, $sitewide_sale->ID ) . '>' . esc_html( get_the_title( $sitewide_sale->ID ) ) . '</option>';
					}
				?>
				</select>

				<input type="hidden" name="page" value="pmpro-reports" />
				<input type="hidden" name="report" value="pmpro_sws_reports" />
				<input type="submit" class="button" value="<?php echo esc_attr_e( 'Generate Report', 'pmpro-sitewide-sales' );?>" />
			</li>
		</ul>
		<br /><br />
		<hr class="clear" />
		<p>
		<?php
			printf( wp_kses_post( 'From %s to %s using discount code %s on landing page <a target="_blank" href="%s">%s</a>.', 'pmpro-sitewide-sales' ),
					$stats['start_date'],
					$stats['end_date'],
					$stats['discount_code'],
					$stats['landing_page_url'],
					$stats['landing_page_title']
				);
		?>
		</p>
		<hr />
		<style>
		.pmpro_sws_reports-box {
			background: #FFF;
			border: 1px solid #CCC;
			margin-bottom: 2rem;
			padding: 20px;
			text-align: center;
		}
		.pmpro_sws_reports-box h1.pmpro_sws_reports-box-title {
			border-bottom: 1px solid #EFEFEF;
			color: #999;
			font-family: Georgia, Times, "Times New Roman", serif;
			margin: 0 0 1rem 0;
			padding: 0 0 1rem 0;
		}
		.pmpro_sws_reports-data {
			display: grid;
			grid-gap: 1rem;
			grid-template-columns: 1fr 1fr 1fr;
		}
		.pmpro_sws_reports-data-section {
		}
		.pmpro_sws_reports-data-section h1 {
			font-size: 40px;
			line-height: 50px;
			margin: 0;
		}
		.pmpro_sws_reports-data-section-sub {
			display: grid;
			grid-gap: 1rem;
			grid-template-columns: 3fr 1fr;
			margin: 1rem 0;
			text-align: left;
		}
		.pmpro_sws_reports-data-section-sub .pmpro_sws_reports-data-label {
			text-align: right;
		}
		</style>
		<div class="pmpro_sws_reports-box">
			<h1 class="pmpro_sws_reports-box-title"><?php esc_html_e( 'Overall Sale Performance', 'pmpro-sitewide-sales' ); ?></h1>
			<div class="pmpro_sws_reports-data">
				<div class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['new_rev_with_code'] ); ?></h1>
					<p><?php esc_html_e( 'Total Sales Revenue Using Discount', 'pmpro-sitewide-sales' ); ?></p>
				</div>
				<div id="pmpro_sws_reports-data-section_banner" class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['banner_impressions'] ); ?></h1>
					<p><?php esc_html_e( 'Banner Impressions', 'pmpro-sitewide-sales' ); ?></p>
				</div>
				<div id="pmpro_sws_reports-data-section_sales" class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['landing_page_visits'] ); ?></h1>
					<p><?php esc_html_e( 'Landing Page Visits', 'pmpro-sitewide-sales' ); ?></p>
				</div>
			</div>
		</div>
		<div class="pmpro_sws_reports-box">
			<h1 class="pmpro_sws_reports-box-title"><?php esc_html_e( 'Sales Comparision Data', 'pmpro-sitewide-sales' ); ?></h1>
			<div class="pmpro_sws_reports-data">
				<div class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['checkout_conversions_with_code'] ); ?></h1>
					<p><?php esc_html_e( 'Checkouts Using Discount', 'pmpro-sitewide-sales' ); ?></p>
				</div>
				<div id="pmpro_sws_reports-data-section_banner" class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['checkout_conversions_without_code'] ); ?></h1>
					<p><?php esc_html_e( 'Checkouts Without Discount', 'pmpro-sitewide-sales' ); ?></p>
				</div>
				<div id="pmpro_sws_reports-data-section_sales" class="pmpro_sws_reports-data-section">
					<h1><?php echo esc_attr( $stats['checkout_conversions_with_code'] ) + esc_attr( $stats['checkout_conversions_with_code'] ); ?></h1>
					<p><?php esc_html_e( 'Total Checkouts in Period', 'pmpro-sitewide-sales' ); ?></p>
				</div>
			</div>
			<hr />
			<div class="pmpro_sws_reports-data">
				<div class="pmpro_sws_reports-data-section">
					<span class="pmpro_sws_reports-data-label"><?php esc_html_e( 'Sales Without Discount in Period', 'pmpro-sitewide-sales' ); ?></span>
					<span class="pmpro_sws_reports-data-value"><?php echo esc_attr( $stats['new_rev_without_code'] ); ?></span>
				</div>
				<div class="pmpro_sws_reports-data-section">
					<span class="pmpro_sws_reports-data-label"><?php esc_html_e( 'Other Sales (Renewals) in Period', 'pmpro-sitewide-sales' ); ?></span>
					<span class="pmpro_sws_reports-data-value"><?php echo esc_attr( $stats['old_rev'] ); ?></span>
				</div>
				<div class="pmpro_sws_reports-data-section">	
					<span class="pmpro_sws_reports-data-label"><?php esc_html_e( 'Total Sales in Period', 'pmpro-sitewide-sales' ); ?></span>
					<span class="pmpro_sws_reports-data-value"><?php echo esc_attr( $stats['new_rev_with_code'] ) + esc_attr( $stats['new_rev_without_code'] ) + esc_attr( $stats['old_rev'] ); ?></span>
				</div>
			</div>			
		</div>
		<pre>
			<?php var_dump( $stats ); ?>
		</pre>
	</form>
	<?php
}
