<?php
/**
 * Sets up banner on the bottom right of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */

function pmpro_sws_show_bottom_right_banner() {
	$options              = pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	?>
	<div id="pmpro_sws_banner_bottom_right" class="pmpro_sws_banner">
		<a href="javascript:void(0);" onclick="document.getElementById('pmpro_sws_banner_bottom_right').style.display = 'none';" class="dismiss">x</a>
<<<<<<< HEAD
		<h3><?php _e( $options['banner_title'] ); ?></h3>
		<?php echo apply_filters( 'pmpro_sws_banner_bottom_right', $options['banner_description'] ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( $options['landing_page_post_id'] ); ?>"><?php _e( $options['link_text'] ); ?></a>
=======
		<h3><?php _e( get_post_meta( $active_sitewide_sale, 'banner_title', true ) ); ?></h3>
		<?php echo wpautop( get_post_field('post_content', $active_sitewide_sale) ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
>>>>>>> cpt-version
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_right_banner' );
