<?php
/**
 * Sets up banner on top of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */
function pmpro_sws_show_top_banner() {
	$options = pmprosws_get_options();
	/* Maybe use JavaScript here to detect the height of the bar and adjust margin-top of html elemenet. */
	?>
	<div id="pmpro_sws_banner_top" class="pmpro_sws_banner">
		<span><?php echo esc_attr_e( $options['banner_description'] ); ?></span>
		<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_head', 'pmpro_sws_show_top_banner' );
