<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Settings {
	/**
	 * Initial plugin setup
	 *
	 * @package pmpro-sitewide-sale/includes
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Add settings menu
	 **/
	public static function menu() {
		add_submenu_page(
			'pmpro-membershiplevels',
			__( 'Sitewide Sale', 'pmpro-sitewide-sale' ),
			__( 'Sitewide Sale', 'pmpro-sitewide-sale' ),
			'manage_options',
			'pmpro-sws',
			'PMPro_SWS_Settings::options_page'
		);
	}

	/**
	 * Save submitted fields
	 * Combine elements of settings page
	 **/
	public static function options_page() {
	?>
	<div class="wrap">
		<?php require_once PMPRO_DIR . '/adminpages/admin_header.php'; ?>
		<h1><?php esc_attr_e( 'Paid Memberships Pro - Sitewide Sale Add On', 'pmpro-sitewide-sale' ); ?></h1>
		<form id="pmpro_sws_options" action="options.php" method="POST">
			<?php settings_fields( 'pmpro-sws-group' ); ?>
			<?php do_settings_sections( 'pmpro-sws' ); ?>
			<?php submit_button(); ?>
			<?php require_once PMPROSWS_DIR . '/includes/classes/class-pmpro-sws-reports.php'; ?>
		</form>
		<?php require_once PMPRO_DIR . '/adminpages/admin_footer.php'; ?>
	</div>
<?php
	}

	/**
	 * Init settings page
	 **/
	public static function admin_init() {
		register_setting( 'pmpro-sws-group', 'pmpro_sitewide_sale', array( __CLASS__, 'validate' ) );
		add_settings_section( 'pmpro-sws-section-select_sitewide_sale', __( 'Choose Active Sitewide Sale', 'pmpro_sitewide_sale' ), array( __CLASS__, 'section_select_sitewide_sale_callback' ), 'pmpro-sws' );
		add_settings_field( 'pmpro-sws-sitewide-sale', __( 'Sitewide Sale', 'pmpro_sitewide_sale' ), array( __CLASS__, 'select_sitewide_sale_callback' ), 'pmpro-sws', 'pmpro-sws-section-select_sitewide_sale' );
	}

	public static function section_select_sitewide_sale_callback() {
	?>
	<?php
	}

	/**
	 * Creates field to select an active sale
	 */
	public static function select_sitewide_sale_callback() {
		global $wpdb;
		$options = self::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		$sitewide_sales       = get_posts(
			[
				'post_type' => 'sws_sitewide_sale',
			]
		);
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
	}


	/**
	 * Get the Sitewide Sale Options
	 *
	 * @return array [description]
	 */
	public static function pmprosws_get_options() {
		$options = get_option( 'pmpro_sitewide_sale' );

		// Set the defaults.
		if ( empty( $options ) ) {
			$options = array(
				'active_sitewide_sale_id' => false,
			);
		}
		return $options;
	}

	/**
	 * [pmprosws_save_options description]
	 *
	 * @param array $options contains information about sale to be saved.
	 */
	public static function pmprosws_save_options( $options ) {
		update_option( 'pmpro_sitewide_sale', $options, 'no' );
	}

	/**
	 * Validates sitewide sale options
	 *
	 * @param  array $input info to be validated.
	 */
	public static function validate( $input ) {
		$options = self::pmprosws_get_options();
		if ( ! empty( $input['active_sitewide_sale_id'] ) && '-1' !== $input['active_sitewide_sale_id'] ) {
			$options['active_sitewide_sale_id'] = trim( $input['active_sitewide_sale_id'] );
		} else {
			$options['active_sitewide_sale_id'] = false;
		}

		return $options;
	}

}
