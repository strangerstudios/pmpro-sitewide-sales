<?php
/**
 * Sets up banner on the bottom of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */

function pmpro_sws_show_bottom_banner() {
	$options = pmprosws_get_options();
	?>
	<style>
		#pmpro_sws_banner_bottom {
			background: rgba(255, 255, 255, .95);
			bottom: 0;
			box-shadow: 0 2px 20px 0 rgba(0,0,0,.2);
			left: 0;
			padding: 20px 0;
			position: fixed;
			right: 0;
			width: 100%;
			z-index: 400;
		}
		#pmpro_sws_banner_bottom .pmpro_sws_banner-inner {
			margin: 0 auto;
			max-width: 1170px;
		}
		#pmpro_sws_banner_bottom .pmpro_sws_banner-inner-left {
			display: inline-block;
			padding-right: 5%;
			width: 70%;
		}
		#pmpro_sws_banner_bottom .pmpro_sws_banner-inner-right {
			display: inline-block;
			width: 25%;
		}
		#pmpro_sws_banner_bottom .pmpro_sws_banner-inner-right .pmpro_btn {
			display: block;
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
	<div id="pmpro_sws_banner_bottom" class="pmpro_sws_banner">
		<div class="pmpro_sws_banner-inner">
			<div class="pmpro_sws_banner-inner-left">
				<h3><?php _e( $options['banner_title'] ); ?></h3>
				<?php echo apply_filters( 'the_content', $options['banner_description'] ); ?>
			</div>
			<div class="pmpro_sws_banner-inner-right">
				<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
			</div>
		</div>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_banner' );
