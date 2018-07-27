<?php
/**
 * Sets up banner on top of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */

function pmpro_sws_show_top_banner() {
	$options = PMPro_SWS_Settings::pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	/* Maybe use JavaScript here to detect the height of the bar and adjust margin-top of html elemenet. */
	?>
	<div id="pmpro_sws_banner_top" class="pmpro_sws_banner">
		<?php echo esc_attr_e( get_post_field( 'post_content', $active_sitewide_sale ) ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_head', 'pmpro_sws_show_top_banner' );
