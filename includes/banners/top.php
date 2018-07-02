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
	<style>
		html {
			margin-top: 100px !important;
		}
		.pmpro_sws_banner {
			background: rgba(255, 255, 255, 1.0);
			box-shadow: 0 2px 20px 0 rgba(0,0,0,.2);
			left: 0;
			padding: 20px 0;
			position: fixed;
			right: 0;
			text-align: center;
			top: 0;
			width: 100%;
			z-index: 400;
		}
		.pmpro_sws_banner h3 {
			margin: 0 0 10px 0;
			padding: 0;
		}
		.pmpro_sws_banner p {
			margin: 0 0 10px 0;
			padding: 0;
		}
	</style>
	<div id="pmpro_sws_banner_top" class="pmpro_sws_banner">
		<?php echo esc_attr_e( $options['banner_description'] ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_top_banner' );
