<?php

$options = pmprosws_get_options();
if ( false !== $options['discount_code_id'] && false !== $options['landing_page_post_id'] ) {
	echo '<table class="widefat membership-levels">';
	$reports = array(
		'times_code_used'  => __( 'Code Uses:', 'pmpro-sitewide-sale' ),
		'revenue'          => __( 'Revenue:', 'pmpro-sitewide-sale' ),
		'num_landing'      => __( 'Sale Page Visits:', 'pmpro-sitewide-sale' ),
		'num_checkout'     => __( 'Checkout Page Visits After Sale Page:', 'pmpro-sitewide-sale' ),
		'num_confirmation' => __( 'Checkouts After Sale Page:', 'pmpro-sitewide-sale' ),
	);
	foreach ( $reports as $db_name => $output_name ) {
		echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( $output_name ) . '</label></th>
			<td><p>' . esc_html( $options[ $db_name ] ) . '</p></td>
		</tr>';
	}

	echo '</table>';
}
