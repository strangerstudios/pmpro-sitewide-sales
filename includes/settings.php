<?php
/**
 * Creates settings pages and other options for plugin
 *
 * Ultimately, this page should have a list of all the
 * sales, noting which sale is active, with a button next
 * to each sale to set it as the active sale.
 *
 * @package pmpro-sitewide-sale/includes
 */

add_action( 'admin_menu', 'pmpro_sws_menu' );
/**
 * Add settings menu
 **/
function pmpro_sws_menu() {
	add_submenu_page(
		'pmpro-membershiplevels',
		__( 'Sitewide Sale', 'pmpro-sitewide-sale' ),
		__( 'Sitewide Sale', 'pmpro-sitewide-sale' ),
		'manage_options',
		'pmpro-sws',
		'pmprosla_sws_options_page'
	);
}

/**
 * Save submitted fields
 * Combine elements of settings page
 **/
function pmprosla_sws_options_page() {
	?>
	<div class="wrap">
		<?php require_once PMPRO_DIR . '/adminpages/admin_header.php'; ?>
		<h1><?php esc_attr_e( 'Paid Memberships Pro - Sitewide Sale Add On', 'pmpro-sitewide-sale' ); ?></h1>
		<form id="pmpro_sws_options" action="options.php" method="POST">
			<?php settings_fields( 'pmpro-sws-group' ); ?>
			<?php do_settings_sections( 'pmpro-sws' ); ?>
			<?php submit_button(); ?>
			<?php require_once PMPROSWS_DIR . '/includes/reports.php'; ?>
		</form>
		<script>

			jQuery( document ).ready(function() {
				jQuery(".pmpro_sws_option").change(function() {
					window.onbeforeunload = function() {
			    	return true;
					};
				});
				jQuery("#pmpro_sws_options").submit(function() {
					window.onbeforeunload = null;
				});
			});
		</script>
		<?php require_once PMPRO_DIR . '/adminpages/admin_footer.php'; ?>
	</div>
<?php
}

add_action( 'admin_init', 'pmpro_sws_admin_init' );
/**
 * Init settings page
 **/
function pmpro_sws_admin_init() {
	register_setting( 'pmpro-sws-group', 'pmpro_sitewide_sale', 'pmpro_sws_validate' );
	add_settings_section( 'pmpro-sws-section-select_sitewide_sale', __( 'Choose Active Sitewide Sale', 'pmpro_sitewide_sale' ), 'pmpro_sws_section_select_sitewide_sale_callback', 'pmpro-sws' );
	add_settings_field( 'pmpro-sws-sitewide-sale', __( 'Sitewide Sale', 'pmpro_sitewide_sale' ), 'pmpro_sws_select_sitewide_sale_callback', 'pmpro-sws', 'pmpro-sws-section-select_sitewide_sale' );
}

function pmpro_sws_section_select_sitewide_sale_callback() {
	?>
	<?php
}

/**
 * Creates field to select an active sale
 */
function pmpro_sws_select_sitewide_sale_callback() {
	global $wpdb;
	$options              = pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	$sitewide_sales       = get_posts([
		'post_type' => 'sws_sitewide_sale',
	]);
	?>
	<select class="pmpro_sws_sitewide_sale_select pmpro_sws_option" id="pmpro_sws_sitewide_sale_select" name="pmpro_sitewide_sale[active_sitewide_sale_id]">
	<option value=-1></option>
	<?php
	foreach ( $sitewide_sales as $sitewide_sale ) {
		$selected_modifier = '';
		if ( $sitewide_sale->ID . '' === $active_sitewide_sale ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value=' . esc_html( $sitewide_sale->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( $sitewide_sale->post_title ) . '</option>';
	}
	echo '</select> ' . esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) . 'post-new.php?post_type=sws_sitewide_sale&set_sitewide_sale=true">
			 ' . esc_html( 'create a new Sitewide Sale', 'pmpro_sitewide_sale' ) . '</a>.';
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_sitewide_sale_select").selectWoo();
		});
	</script>
	<?php
}


/**
 * Validates sitewide sale options
 *
 * @param  array $input info to be validated.
 */
function pmpro_sws_validate( $input ) {
	$options = pmprosws_get_options();

	if ( ! empty( $input['active_sitewide_sale_id'] ) && '-1' !== $input['active_sitewide_sale_id'] ) {
			$options['active_sitewide_sale_id'] = trim( $input['active_sitewide_sale_id'] );
	} else {
			$options['active_sitewide_sale_id'] = false;
	}

	return $options;
}
