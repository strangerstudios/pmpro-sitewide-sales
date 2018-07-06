<?php
/**
 * Sets up banner on the bottom right of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */
function pmpro_sws_show_bottom_right_banner() {
	$options = pmprosws_get_options();
	?>
	<div id="pmpro_sws_banner_bottom_right" class="pmpro_sws_banner">
		<a href="javascript:void(0);" onclick="document.getElementById('pmpro_sws_banner_bottom_right').style.display = 'none';" class="dismiss">x</a>
		<h3><?php _e( $options['banner_title'] ); ?></h3>
		<?php echo apply_filters( 'pmpro_sws_banner_bottom', $options['banner_description'] ); ?>
		<?php echo wpautop( $options['banner_description'] ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_right_banner' );
