<?php
/**
 * Creates settings pages and other options for plugin
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
		__( 'PMPro Sitewide Sale', 'pmpro-sitewide-sale' ),
		__( 'PMPro Sitewide Sale', 'pmpro-sitewide-sale' ),
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
		<form action="options.php" method="POST">
			<?php settings_fields( 'pmpro-sws-group' ); ?>
			<?php do_settings_sections( 'pmpro-sws' ); ?>
			<?php submit_button(); ?>
			<?php require_once PMPROSWS_DIR . '/includes/reports.php'; ?>
		</form>
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
	add_settings_section( 'pmpro-sws-section', __( 'Sitewide Sale Settings', 'pmpro_sitewide_sale' ), 'pmpro_sws_section_callback', 'pmpro-sws' );
	add_settings_field( 'pmpro-sws-discount-code', __( 'Discount Code', 'pmpro_sitewide_sale' ), 'pmpro_sws_discount_code_callback', 'pmpro-sws', 'pmpro-sws-section' );
	add_settings_field( 'pmpro-sws-sale-page', __( 'Sale Page', 'pmpro_sitewide_sale' ), 'pmpro_sws_sale_page_callback', 'pmpro-sws', 'pmpro-sws-section' );
	add_settings_field( 'pmpro-sws-banners', __( 'Banners', 'pmpro_sitewide_sale' ), 'pmpro_sws_banners_callback', 'pmpro-sws', 'pmpro-sws-section' );
}

/**
 * General plugin instructions
 **/
function pmpro_sws_section_callback() {
	?>
	<ol>
		<li><?php esc_html_e( 'Choose Discount Code to Associate With Sale', 'pmpro-sitewide-sale' ); ?></li>
		<li><?php esc_html_e( 'Create Landing Page', 'pmpro-sitewide-sale' ); ?></li>
		<li><?php esc_html_e( 'Setup Banners', 'pmpro-sitewide-sale' ); ?></li>
		<li><?php esc_html_e( 'Monitor Your Sale', 'pmpro-sitewide-sale' ); ?></li>
	</ol>
	<hr>
	<?php
}

/**
 * Creates field to select a discount code for sale
 */
function pmpro_sws_discount_code_callback() {
	global $wpdb;
	$options          = pmprosws_get_options();
	$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
	$current_discount = $options['discount_code_id'];

	?>
	<select class="discount_code_select" id="pmpro_sws_discount_code_select" name="pmpro_sitewide_sale[discount_code_id]">
	<option value=-1></option>
	<?php
	foreach ( $codes as $code ) {
		$selected_modifier = '';
		if ( $code->id === $current_discount ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $code->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $code->code, 'pmpro-sitewide-sale' ) . '</option>';
	}
	echo '</select> ' . esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) .
	'admin.php?page=pmpro-discountcodes&edit=-1&set_sitewide_sale=true">' . esc_html( 'create a new discount code', 'pmpro_sitewide_sale' ) . '</a.>';
		?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_discount_code_select").selectWoo();
		});
	</script>
	<?php
}

/**
 * Creates field to select a discount code for sale
 */
function pmpro_sws_sale_page_callback() {
	global $wpdb;
	$options      = pmprosws_get_options();
	$pages        = get_pages();
	$current_page = $options['landing_page_post_id'];

	?>
	<select class="landing_page_select" id="pmpro_sws_landing_page_select" name="pmpro_sitewide_sale[landing_page_post_id]">
	<option value=-1></option>
	<?php
	foreach ( $pages as $page ) {
		$selected_modifier = '';
		if ( $page->ID . '' === $current_page ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value=' . esc_html( $page->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( $page->post_title ) . '</option>';
	}
	echo '</select><span id="pmpro_sws_after_choose_page">';
	if ( $current_page <= 0 ) {
		echo esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) . 'post-new.php?post_type=page&set_sitewide_sale=true">
				 ' . esc_html( 'create a new page', 'pmpro_sitewide_sale' ) . '</a>.';
	} else {
		?>
				<a target="_blank" href="post.php?post=<?php echo $current_page ?>&action=edit"
					 class="button button-secondary pmpro_page_edit"><?php _e('edit page', 'paid-memberships-pro' ); ?></a>
				&nbsp;
				<a target="_blank" href="<?php echo get_permalink($current_page); ?>"
					 class="button button-secondary pmpro_page_view"><?php _e('view page', 'paid-memberships-pro' ); ?></a>
		<?php
	}
	echo '</span>'
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_landing_page_select").selectWoo();
			jQuery( "#pmpro_sws_landing_page_select" ).change(function() {
				jQuery( "#pmpro_sws_after_choose_page" ).html('');
			});
		});
	</script>
	<?php
}

/**
 * Displays banner settings
 */
function pmpro_sws_banners_callback() {
	$options                          = pmprosws_get_options();
	$use_banner_top_modifier          = ( 'top' === $options['use_banner'] ) ? ' selected="selected"' : '';
	$use_banner_bottom_right_modifier = ( 'bottom-right' === $options['use_banner'] ) ? ' selected="selected"' : '';
	?>
	</br>
		<table class="form-table"><tr>
			<th scope="row" valign="top"><label><?php esc_html_e( 'Use the built-in banner?', 'pmpro-sitewide-sale' ); ?></label></th>
			<td><select class="use_banner_select" id="pmpro_sws_use_banner_select" name="pmpro_sitewide_sale[use_banner]">
				<option value="no"><?php esc_html_e( 'No', 'pmpro-sitewide-sale' ); ?></option>
				<option value="top"' . $use_banner_top_modifier . '><?php esc_html_e( 'Yes. Top of Site.', 'pmpro-sitewide-sale' ); ?></option>
				<option value="bottom-right"' . $use_banner_bottom_right_modifier . '><?php esc_html_e( 'Yes. Bottom Right of Site.', 'pmpro-sitewide-sale' ); ?></option>
			</select></td>
		</tr></table>
		<table class="form-table" id="pmpro_sws_banner_options">
	<?php
	$banner_option_fields = array(
		'banner_title'       => __( 'Banner Title:', 'pmpro-sitewide-sale' ),
		'banner_description' => __( 'Banner Description:', 'pmpro-sitewide-sale' ),
		'link_text'          => __( 'Link Text:', 'pmpro-sitewide-sale' ),
	);
	foreach ( $banner_option_fields as $db_name => $output_name ) {
		echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( $output_name ) . '</label></th>
			<td><input type="text" name="pmpro_sitewide_sale[' . esc_html( $db_name ) . ']" value="' . esc_html( $options[ $db_name ] ) . '"/></td>
		</tr>';
	}
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . esc_html( 'CSS Option:', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea name="pmpro_sitewide_sale[css_option]">' . esc_html( $options['css_option'] ) . '</textarea></td>
	</tr>';
	echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( 'Hide for Levels:', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><select id="pmpro_sws_hide_levels_select" name="pmpro_sitewide_sale[hide_for_levels][]" multiple/>';
	$all_levels    = pmpro_getAllLevels( true, true );
	$hidden_levels = $options[ hide_for_levels ];
	foreach ( $all_levels as $level ) {
		$selected_modifier = in_array( $level->id, $hidden_levels, true ) ? ' selected' : '';
		echo '<option value=' . esc_html( $level->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $level->name ) . '</option>';
	}
	$checked_modifier = $options[ hide_on_checkout ] ? ' checked' : '';
	echo '</td></tr>
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( 'Hide on checkout:', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><input type="checkbox" name="pmpro_sitewide_sale[hide_on_checkout]" ' . esc_html( $checked_modifier ) . '/></td>
		</tr></table>';
		?>
		<script>
			jQuery( document ).ready(function() {
				jQuery("#pmpro_sws_use_banner_select").selectWoo();
				jQuery("#pmpro_sws_hide_levels_select").selectWoo();
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

	$string_inputs = [ 'discount_code_id', 'landing_page_post_id' ];
	foreach ( $string_inputs as $str ) {
		if ( ! empty( $input[ $str ] ) && '-1' !== $input[ $str ] ) {
			$options[ $str ] = trim( $input[ $str ] );
		} else {
			$options[ $str ] = false;
		}
	}

	$possible_options = [ 'no', 'top', 'bottom-right' ];
	if ( ! empty( $input['use_banner'] ) && in_array( trim( $input['use_banner'] ), $possible_options, true ) ) {
		$options['use_banner'] = trim( $input['use_banner'] );
	} else {
		$options['use_banner'] = 'no';
	}

	$string_inputs = [ 'banner_title', 'banner_description', 'link_text', 'css_option' ];
	foreach ( $string_inputs as $str ) {
		if ( ! empty( $input[ $str ] ) ) {
			$options[ $str ] = trim( $input[ $str ] );
		} else {
			$options[ $str ] = '';
		}
	}

	if ( ! empty( $input['hide_for_levels'] ) && is_array( $input['hide_for_levels'] ) ) {
		$options['hide_for_levels'] = $input['hide_for_levels'];
	} else {
		$options['hide_for_levels'] = [];
	}

	if ( empty( $input['hide_on_checkout'] ) ) {
		$options['hide_on_checkout'] = false;
	} else {
		$options['hide_on_checkout'] = true;
	}

	$string_inputs = [ 'times_code_used', 'revenue', 'num_landing', 'num_checkout', 'num_confirmation' ];
	foreach ( $string_inputs as $str ) {
		if ( ! empty( $input[ $str ] ) ) {
			if ( is_string( $input[ $str ] ) ) {
				$input[ $str ] = intval( trim( $input[ $str ] ) );
			}
			if ( is_int( $input[ $str ] ) ) {
				$options[ $str ] = $input[ $str ];
			} else {
				$options[ $str ] = 0;
			}
		} else {
			$options[ $str ] = 0;
		}
	}

	return $options;
}




add_action( 'pmpro_discount_code_after_settings', 'pmpro_sws_discount_codes_setting' );
/**
 * Puts Sitewide Sale checkbox on edit discount code page
 *
 * @param  int $edit discount_code_id being edited.
 */
function pmpro_sws_discount_codes_setting( $edit ) {
	$init_checked = false;
	if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
		$init_checked = true;
	} else {
		$options = pmprosws_get_options();
		if ( $edit . '' === $options['discount_code_id'] ) {
			$init_checked = true;
		}
	}

	echo '<table class="form-table"><tr>
	<th scope="row" valign="top"><label>' . esc_html( 'Sitewide Sale', 'pmpro-sitewide-sale' ) . ':</label></th>
	<td><input name="sitewide_sale" type="checkbox" ' . ( $init_checked ? 'checked' : '' ) . ' /></td>
	</tr></table>';
}

add_action( 'pmpro_save_discount_code', 'pmpro_sws_discount_codes_save' );
/**
 * Saves the contents of the sitewide sale checkbox
 *
 * @param  int $saveid discount_code_id being saved.
 */
function pmpro_sws_discount_codes_save( $saveid ) {
	$options = pmprosws_get_options();
	if ( ! empty( $_REQUEST['sitewide_sale'] ) ) {
		$sale_page = $options['landing_page_post_id'];
		if ( false !== $sale_page && '-1' !== $sale_page ) {
			echo '<div id="message" class="updated fade"><p>View sale page <a href=' .
			esc_url( get_permalink( $options['landing_page_post_id'] ) ) . '>here</a>.</p></div>';
		}
		$options['discount_code_id'] = $saveid;
	} elseif ( $options['discount_code_id'] === $saveid . '' ) {
		$options['discount_code_id'] = false;
	}
	pmprosws_save_options( $options );
}



/**
 * Puts Sitewide Sale checkbox on edit page sidebar
 **/
function pmpro_sws_page_meta() {
	global $post;
	$post_id      = $post->ID;
	$options      = pmprosws_get_options();
	$init_checked = false;

	if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
		$init_checked = true;
	} elseif ( $post_id . '' === $options['landing_page_post_id'] ) {
		$init_checked = true;
	}

	echo '<input name="' . esc_html( 'Sitewide Sale', 'pmpro-sitewide-sale' ) . '" type="checkbox" ' . ( $init_checked ? 'checked' : '' ) . ' />';
}

/**
 * Saves the contents of the sitewide sale checkbox
 *
 * @param  int $post_id being saved.
 */
function pmpro_sws_page_save( $post_id ) {
	$options = pmprosws_get_options();
	if ( ! empty( $_REQUEST['sitewide_sale'] ) ) {
		$options['landing_page_post_id'] = $post_id;
	} elseif ( $options['landing_page_post_id'] === $post_id . '' ) {
		$options['landing_page_post_id'] = false;
	}
	pmprosws_save_options( $options );
}

/**
 * Adds meta boxes to edit page
 **/
function pmpro_sws_page_meta_wrapper() {
	add_meta_box( 'pmpro_sws_page_meta', __( 'Set Sitewide Sale', 'pmpro-sitewide-sale' ), 'pmpro_sws_page_meta', 'page', 'side' );
}
if ( is_admin() ) {
	add_action( 'admin_menu', 'pmpro_sws_page_meta_wrapper' );
	add_action( 'save_post', 'pmpro_sws_page_save' );
}
