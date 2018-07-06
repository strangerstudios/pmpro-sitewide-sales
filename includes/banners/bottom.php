<?php
/**
 * Sets up banner on the bottom of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */
function pmpro_sws_show_bottom_banner() {
	$options = pmprosws_get_options();
	?>
	<div id="pmpro_sws_banner_bottom" class="pmpro_sws_banner">
		<div class="pmpro_sws_banner-inner">
			<div class="pmpro_sws_banner-inner-left">
				<h3><?php _e( $options['banner_title'] ); ?></h3>
				<?php echo apply_filters( 'pmpro_sws_banner_bottom', $options['banner_description'] ); ?>
			</div>
			<div class="pmpro_sws_banner-inner-right">
				<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
			</div>
		</div>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_banner' );
add_filter( 'pmpro_sws_banner_bottom', 'pmpro_sws_bottom_additions' );
function pmpro_sws_bottom_additions() {
	echo '<h2>pmpro_sws_bottom_additions</h2>';
}
