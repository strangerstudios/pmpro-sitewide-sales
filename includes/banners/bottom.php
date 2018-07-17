<?php
/**
 * Sets up banner on the bottom of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */

function pmpro_sws_show_bottom_banner() {
	$options              = pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	?>
	<div id="pmpro_sws_banner_bottom" class="pmpro_sws_banner">
		<div class="pmpro_sws_banner-inner">
			<div class="pmpro_sws_banner-inner-left">
				<h3><?php _e( get_the_title( $active_sitewide_sale ) ); ?></h3>
				<?php echo apply_filters( 'the_content', get_post_field('post_content', $active_sitewide_sale) ); ?>
			</div>
			<div class="pmpro_sws_banner-inner-right">
				<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
			</div>
		</div>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_banner' );
