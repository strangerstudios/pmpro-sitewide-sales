<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * Register a meta box using a class.
 */
class PMPro_SWS_MetaBoxes {

	/**
	 * Constructor.
	 */
	// public function __construct() {
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'load-post.php', array( __CLASS__, 'init_metabox' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'init_metabox' ) );
		// add_filter( 'mce_buttons2', array( __CLASS__, 'remove_editor_buttons' ) );
		add_action( 'pmpro_save_discount_code', array( __CLASS__, 'discount_code_on_save' ) );
		add_action( 'save_post', array( __CLASS__, 'landing_page_on_save' ), 10, 3 );
		add_action( 'admin_notices', array( __CLASS__, 'return_from_editing_discount_code' ) );
		add_filter( 'redirect_post_location', array( __CLASS__, 'redirect_after_page_save' ), 10, 2 );
	}

	/**
	 * Enqueues js/pmpro-sws-cpt-meta.js
	 */
	public static function enqueue_scripts() {
		global $typenow;
		// if ( 'sws_sitewide_sale' === $typenow ) {
			wp_register_script( 'pmpro_sws_cpt_meta', plugins_url( 'includes/js/pmpro-sws-cpt-meta.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
			wp_enqueue_script( 'pmpro_sws_cpt_meta' );

			wp_register_style( 'admin-dash', plugins_url( 'includes/css/sws-admin.css', dirname( dirname( __FILE__ ) ) ), '1.0.4' );
			wp_enqueue_style( 'admin-dash' );
		// }
	}

	/**
	 * Meta box initialization.
	 */
	public static function init_metabox() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_sws_metaboxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_sws_metaboxes' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'metaboxes_above_editor' ) );
		add_action( 'edit_form_after_title', array( __CLASS__, 'move_metaboxes_above_editor' ) );
		// add_action( 'save_post', 'pmpro_sws_save_cpt', 10, 2 );
	}
	public static function metaboxes_above_editor( $post_type ) {
		add_meta_box(
			'pmpro_sws_cpt_step_1',
			__( 'Step 1: Settings to Associate With Sale', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_1' ),
			array( 'sws_sitewide_sale' ),
			( defined( 'GUTENBERG_VERSION' ) ? 'normal' : 'above_editor' ),
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_2',
			__( 'Step 2: Action after Click', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_2' ),
			array( 'sws_sitewide_sale' ),
			( defined( 'GUTENBERG_VERSION' ) ? 'normal' : 'above_editor' ),
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_3',
			__( 'Step 3: Customize your Message', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_3' ),
			array( 'sws_sitewide_sale' ),
			( defined( 'GUTENBERG_VERSION' ) ? 'normal' : 'above_editor' ),
			'high'
		);
	}
	public static function move_metaboxes_above_editor() {
		// Get the globals:
		global $post, $wp_meta_boxes;

		// Output the "advanced" meta boxes:
		do_meta_boxes( get_current_screen(), 'above_editor', $post );

		// Remove the initial "advanced" meta boxes:
		unset( $wp_meta_boxes['sws_sitewide_sale']['above_editor'] );
	}

	/**
	 * Add the metaboxes.
	 */
	public static function add_sws_metaboxes() {
		add_meta_box(
			'pmpro_sws_cpt_set_as_sitewide_sale',
			__( 'Sitewide Sale', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_set_as_sitewide_sale' ),
			array( 'sws_sitewide_sale' ),
			'side',
			'high'
		);

		// Removing Step 1
		add_meta_box(
			'pmpro_sws_cpt_step_4',
			__( 'Step 4: Setup Banners', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_4' ),
			array( 'sws_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_5',
			__( 'Step 5: After Checkout', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_5' ),
			array( 'sws_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_6',
			__( 'Step 6: Track Sale Progress with Reports', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_6' ),
			array( 'sws_sitewide_sale' ),
			'normal',
			'high'
		);
	}


	function remove_editor_buttons( $buttons ) {
		$remove_buttons = array(
			'bold',
			'italic',
			'strikethrough',
			'bullist',
			'numlist',
			'blockquote',
			'hr',
			'link',
			'unlink',
			'wp_more',
			'spellchecker',
			'dfw',
		);
		foreach ( $buttons as $button_key => $button_value ) {
			if ( in_array( $button_value, $remove_buttons ) ) {
				unset( $buttons[ $button_key ] );
			}
		}
		return $buttons;
	}

	/**
	 * Renders the meta box.
	 */
	public static function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
	}

	public static function display_set_as_sitewide_sale( $post ) {
		$init_checked = false;
		if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
			$init_checked = true;
		} else {
			$options = PMPro_SWS_Settings::pmprosws_get_options();
			if ( $post->ID . '' === $options['active_sitewide_sale_id'] ) {
				$init_checked = true;
			}
		}
		echo '<table class="form-table"><tr>
	<th scope="row" valign="top"><label>' . esc_html__( 'Set as Current Sitewide Sale', 'pmpro-sitewide-sale' ) . ':</label></th>
	<td><input name="pmpro_sws_set_as_sitewide_sale" type="checkbox" ' . ( $init_checked ? 'checked' : '' ) . ' /></td>
	</tr>
	</table>';
	}

	public static function display_step_1( $post ) {
		global $wpdb;
		$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
		$current_discount = esc_html( get_post_meta( $post->ID, 'discount_code_id', true ) );
		if ( empty( $current_discount ) ) {
			$current_discount = false;
		}

		$custom_dates = get_post_meta( $post->ID, 'custom_dates', true );
		if ( empty( $custom_dates ) ) {
			$custom_dates = false;
		}
		$hidden_modifier_date  = '';
		$checked_modifier_date = ' checked ';
		if ( ! $custom_dates ) {
			$hidden_modifier_date  = ' hidden';
			$checked_modifier_date = '';
		}

		$start_date = esc_html( get_post_meta( $post->ID, 'start_date', true ) );
		if ( empty( $start_date ) ) {
			$start_date = date( 'Y-m-d' );
		}
		$end_date = esc_html( get_post_meta( $post->ID, 'end_date', true ) );
		if ( empty( $end_date ) ) {
			$end_date = date( 'Y-m-d' );
		}

	?>
	<label for="pmpro_sws_discount_code_id"><b>Choose Discount Code</b> </label><select class="discount_code_select pmpro_sws_option" id="pmpro_sws_discount_code_select" name="pmpro_sws_discount_code_id">
	<option value=-1></option>
	<?php
	$code_found = false;
	foreach ( $codes as $code ) {
		$selected_modifier = '';
		if ( $code->id === $current_discount ) {
			$selected_modifier = ' selected="selected"';
			$code_found        = true;
		}
		echo '<option value = ' . esc_html( $code->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $code->code ) . '</option>';
	}
	echo '</select><span id="pmpro_sws_after_discount_code_select">';
	if ( $code_found ) {
		echo esc_html__( ' or ', 'pmpro_sitewide_sale' ) . ' <input type="submit" class="button button-primary" name="pmpro_sws_edit_discount" value="' . esc_html__( 'edit current discount code', 'pmpro-sitewide-sale' ) . '">';
	}
	echo '</span>' . esc_html__( ' or ', 'pmpro_sitewide_sale' ) . ' <input type="submit" class="button button-primary" name="pmpro_sws_create_discount" value="' . esc_html__( 'create a new discount code', 'pmpro-sitewide-sale' ) . '"><br/><br/>';
	echo '<label for="pmpro_sws_custom_sale_dates"><b>Custom Sale Start/End Dates</b></label>
	<input type="checkbox" id="pmpro_sws_custom_sale_dates" name="pmpro_sws_custom_sale_dates" ' . $checked_modifier_date . '\><br/>';
	echo '<div id="pmpro_sws_custom_date_select"' . $hidden_modifier_date . '>
	<label for="pmpro_sws_start_date">Sale Start Date</label>
	<input type="date" name="pmpro_sws_start_date" value="' . $start_date . '" /> </br>
	<label for="pmpro_sws_end_date">Sale End Date</label>
	<input type="date" name="pmpro_sws_end_date" value="' . $end_date . '" /></div><br/>';
	}

	public static function display_step_2( $post ) {
		global $wpdb;
		$pages        = get_pages();
		$current_page = esc_html( get_post_meta( $post->ID, 'landing_page_post_id', true ) );
		if ( empty( $current_page ) ) {
			$current_page = false;
		}
		$pre_sale_content = esc_html( get_post_meta( $post->ID, 'pre_sale_content', true ) );
		if ( empty( $pre_sale_content ) ) {
			$pre_sale_content = '';
		}

		$sale_content = esc_html( get_post_meta( $post->ID, 'sale_content', true ) );
		if ( empty( $sale_content ) ) {
			$sale_content = '';
		}

		$post_sale_content = esc_html( get_post_meta( $post->ID, 'post_sale_content', true ) );
		if ( empty( $post_sale_content ) ) {
			$post_sale_content = '';
		}

		?>
		<label for="pmpro_sws_landing_page_post_id"><b>Create Landing Page</b></label> <select class="landing_page_select pmpro_sws_option" id="pmpro_sws_landing_page_select" name="pmpro_sws_landing_page_post_id">
		<option value=-1></option>
		<?php
		$page_found = false;
		foreach ( $pages as $page ) {
			$selected_modifier = '';
			if ( $page->ID . '' === $current_page ) {
				$selected_modifier = ' selected="selected"';
				$page_found        = true;
			}
			echo '<option value=' . esc_html( $page->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( $page->post_title ) . '</option>';
		}
		echo '</select><span id="pmpro_sws_after_landing_page_select">';
		if ( $page_found ) {
			echo esc_html__( ' or ', 'pmpro_sitewide_sale' ) . ' <input type="submit" class="button button-primary" name="pmpro_sws_edit_landing_page" value="' . esc_html__( 'edit current landing page', 'pmpro-sitewide-sale' ) . '">';
		}
		echo '</span>' . esc_html__( ' or ', 'pmpro_sitewide_sale' ) . ' <input type="submit" class="button button-primary" name="pmpro_sws_create_landing_page" value="' . esc_html__( 'create a new landing page', 'pmpro-sitewide-sale' ) . '"><br/><br/>';
		?>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top"><label><?php esc_html_e( 'Pre-Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
				<td><textarea class="pmpro_sws_option" name="pmpro_sws_pre_sale_content"><?php echo( esc_html( $pre_sale_content ) ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label><?php esc_html_e( 'Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
				<td><textarea class="pmpro_sws_option" name="pmpro_sws_sale_content"><?php echo( esc_html( $sale_content ) ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label><?php esc_html_e( 'Post-Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
				<td><textarea class="pmpro_sws_option" name="pmpro_sws_post_sale_content"><?php echo( esc_html( $post_sale_content ) ); ?></textarea></td>
			</tr>
		</table>


		<?php
	}

	public static function display_step_3_heading() {
		$return = '<h3>' . __FUNCTION__ . '</h3>';
		return $return;
	}

	public static function display_step_3( $post ) {
		$label = self::display_step_3_heading();
		$label = ucwords( preg_replace( '/_+/', ' ', $label ) );
		$value = $label;
		$value .= apply_filters( 'sws_step_3_description', 'Use this filter: sws_step_3_description to provide some instructions about how to set up a Sitewide Sale.' );
		echo $value;
	}

	public static function display_step_4( $post ) {
		// This should be optimized to use a single get_post_meta call.
		$use_banner = esc_html( get_post_meta( $post->ID, 'use_banner', true ) );
		if ( empty( $use_banner ) ) {
			$use_banner = 'no';
		}
		$custom_banner_title = get_post_meta( $post->ID, 'custom_banner_title', true );
		if ( empty( $custom_banner_title ) ) {
			$custom_banner_title = false;
		}
		$hidden_modifier_title  = '';
		$checked_modifier_title = ' checked ';
		if ( ! $custom_banner_title ) {
			$hidden_modifier_title  = ' hidden';
			$checked_modifier_title = '';
		}
		$banner_title = esc_html( get_post_meta( $post->ID, 'banner_title', true ) );
		if ( empty( $banner_title ) ) {
			$banner_title = '';
		}
		$link_text = esc_html( get_post_meta( $post->ID, 'link_text', true ) );
		if ( empty( $link_text ) ) {
			$link_text = '';
		}
		$css_option = esc_html( get_post_meta( $post->ID, 'css_option', true ) );
		if ( empty( $css_option ) ) {
			$css_option = '';
		}
		$hide_for_levels = get_post_meta( $post->ID, 'hide_for_levels', true );
		if ( empty( $hide_for_levels ) ) {
			$hide_for_levels = [];
		}
		$hide_on_checkout = esc_html( get_post_meta( $post->ID, 'hide_on_checkout', true ) );
		if ( empty( $hide_on_checkout ) ) {
			$hide_on_checkout = false;
		}
		?>
		</br>
		<table class="form-table"><tr>
			<th scope="row" valign="top"><label><?php esc_html_e( 'Use the built-in banner?', 'pmpro-sitewide-sale' ); ?></label></th>
			<td><select class="use_banner_select pmpro_sws_option" id="pmpro_sws_use_banner_select" name="pmpro_sws_use_banner">
				<option value="no" <?php selected( $use_banner, 'no' ); ?>><?php esc_html_e( 'No', 'pmpro-sitewide-sale' ); ?></option>
				<?php
				$registered_banners = PMPro_SWS_Banners::get_registered_banners();
				foreach ( $registered_banners as $banner => $data ) {
					if ( is_string( $banner ) && is_array( $data ) && ! empty( $data['option_title'] ) && is_string( $data['option_title'] ) ) {
						echo '<option value="' . $banner . '"' . selected( $use_banner, $banner ) . '>' . esc_html( $data['option_title'] ) . '</option>';
					}
				}
				?>
			</select></td>
		</tr></table>
		<table class="form-table" id="pmpro_sws_banner_options">
	<?php
	echo '
	<tr>
		<th><label for="pmpro_sws_custom_sale_title"><b>Custom Banner Title</b></label></th>
		<td><input type="checkbox" id="pmpro_sws_custom_banner_title" name="pmpro_sws_custom_banner_title" ' . $checked_modifier_title . '\></td>
	</tr>';
	echo '
	<tr id="pmpro_sws_custom_title_select"' . $hidden_modifier_title . '>
		<th><label for="pmpro_sws_banner_title">Banner Title</label></th>
		<td><input type="textbox" name="pmpro_sws_banner_title" value="' . $banner_title . '" /></td>
	</tr>';
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Button Text', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><input class="pmpro_sws_option" type="text" name="pmpro_sws_link_text" value="' . esc_html( $link_text ) . '"/></td>
	</tr>';

	echo '
	<tr>
		<th scope="row" valign="top"><label>' . esc_html__( 'Custom Banner CSS', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea class="pmpro_sws_option" name="pmpro_sws_css_option">' . esc_html( $css_option ) . '</textarea><p class="description">Use these selectors to alter the appearance of your banners.</p>
			<div id="pmpro_sws_banner_css_selectors">';

	if ( isset( $registered_banners[ $use_banner ] ) && ! empty( $registered_banners[ $use_banner ]['css_selectors'] ) ) {
		$css_selectors = $registered_banners[ $use_banner ]['css_selectors'];
		if ( is_string( $css_selectors ) ) {
			echo $css_selectors;
		} elseif ( is_array( $css_selectors ) ) {
			foreach ( $css_selectors as $css_selector ) {
				if ( is_string( $css_selector ) ) {
					echo $css_selector . '<br/>';
				}
			}
		}
	}

	echo '
			</div>
		</td>
	</tr>';
	echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html__( 'Hide Banner by Membership Level', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><select class="pmpro_sws_option" id="pmpro_sws_hide_levels_select" name="pmpro_sws_hide_for_levels[]" style="width:12em" multiple/>';
	$all_levels    = pmpro_getAllLevels( true, true );
	$hidden_levels = $hide_for_levels;
	foreach ( $all_levels as $level ) {
		$selected_modifier = in_array( $level->id, $hidden_levels, true ) ? ' selected' : '';
		echo '<option value=' . esc_html( $level->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $level->name ) . '</option>';
	}
	$checked_modifier = $hide_on_checkout ? ' checked' : '';
	echo '</td></tr>
		<tr>
			<th scope="row" valign="top"><label>' . esc_html__( 'Hide Banner at Checkout', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><input class="pmpro_sws_option" type="checkbox" name="pmpro_sws_hide_on_checkout" ' . esc_html( $checked_modifier ) . '/></td>
		</tr></table>';
	}

	public static function display_step_5( $post ) {
		$upsell_enabled = get_post_meta( $post->ID, 'upsell_enabled', true );
		if ( empty( $upsell_enabled ) ) {
			$upsell_enabled = false;
		}
		$hidden_modifier_upsell  = '';
		$checked_modifier_upsell = ' checked ';
		if ( ! $upsell_enabled ) {
			$hidden_modifier_upsell  = ' hidden';
			$checked_modifier_upsell = '';
		}

		$upsell_levels = get_post_meta( $post->ID, 'upsell_levels', true );
		if ( empty( $upsell_levels ) ) {
			$upsell_levels = [];
		}

		$upsell_text = esc_html( get_post_meta( $post->ID, 'upsell_text', true ) );
		if ( empty( $upsell_text ) ) {
			$upsell_text = '';
		}

		echo '
		<table>
		<tr>
			<th><label for="pmpro_sws_upsell_enabled">Upsell on Checkout</label></th>
			<td><input type="checkbox" id="pmpro_sws_upsell_enabled" name="pmpro_sws_upsell_enabled" ' . $checked_modifier_upsell . '\></td>
		</tr>';
		echo '
		<tr class="pmpro_sws_upsell_settings"' . $hidden_modifier_upsell . '>
			<th><label for="pmpro_sws_upsell_levels">Levels to upsell To</label></th>
			<td><select class="pmpro_sws_option" id="pmpro_sws_upsell_levels" name="pmpro_sws_upsell_levels[]" style="width:12em" multiple/>';
		$all_levels    = pmpro_getAllLevels( true, true );
		$hidden_levels = $upsell_levels;
		foreach ( $all_levels as $level ) {
			$selected_modifier = in_array( $level->id, $hidden_levels, true ) ? ' selected' : '';
			echo '<option value=' . esc_html( $level->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $level->name ) . '</option>';
		}
		echo '</select></td>
		</tr>
		<tr class="pmpro_sws_upsell_settings"' . $hidden_modifier_upsell . '>
			<th><label for="pmpro_sws_upsell_text">Upsell Text</label></th>
			<td><textarea class="pmpro_sws_option" name="pmpro_sws_upsell_text">' . esc_html( $upsell_text ) . '</textarea><p class="description">Use !!sws_landing_page_url!! to get the url of your Sitewside Sale landing page.</p></td>
		</tr></table>';
	}

	public static function display_step_6( $post ) {
		?>
		<a href="<?php echo admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ); ?>" target="_blank"><button class="button button-secondary"> <?php _e( 'Click here to view Sitewide Sale reports', 'pmpro-sitewide-sale' ); ?></button></a>
	<?php
	}

	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public static function save_sws_metaboxes( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		/*
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}
		*/

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'sws_sitewide_sale' !== $post->post_type ) {
			return;
		}

		global $wpdb;

		if ( isset( $_POST['pmpro_sws_discount_code_id'] ) ) {
			update_post_meta( $post_id, 'discount_code_id', trim( $_POST['pmpro_sws_discount_code_id'] ) );
		} else {
			update_post_meta( $post_id, 'discount_code_id', false );
		}

		if ( isset( $_POST['pmpro_sws_custom_sale_dates'] ) ) {
			if ( isset( $_POST['pmpro_sws_start_date'] ) && isset( $_POST['pmpro_sws_end_date'] ) ) {
				update_post_meta( $post_id, 'start_date', trim( $_POST['pmpro_sws_start_date'] ) );
				update_post_meta( $post_id, 'end_date', trim( $_POST['pmpro_sws_end_date'] ) );
			}
			update_post_meta( $post_id, 'custom_dates', true );
		} elseif ( isset( $_POST['pmpro_sws_discount_code_id'] ) ) {
			$discount_code_dates = $wpdb->get_results( $wpdb->prepare( "SELECT starts, expires FROM $wpdb->pmpro_discount_codes where id = %d", intval( $_POST['pmpro_sws_discount_code_id'] ) ) );
			if ( ! empty( $discount_code_dates ) && ! empty( $discount_code_dates[0] ) && ! empty( $discount_code_dates[0]->starts ) && ! empty( $discount_code_dates[0]->expires ) ) {
				update_post_meta( $post_id, 'start_date', $discount_code_dates[0]->starts );
				update_post_meta( $post_id, 'end_date', $discount_code_dates[0]->expires );
			}
			update_post_meta( $post_id, 'custom_dates', false );
		}

		if ( isset( $_POST['pmpro_sws_custom_banner_title'] ) ) {
			if ( isset( $_POST['pmpro_sws_banner_title'] ) ) {
				update_post_meta( $post_id, 'banner_title', trim( $_POST['pmpro_sws_banner_title'] ) );
			}
			update_post_meta( $post_id, 'custom_banner_title', true );
		} else {
			if ( isset( $_POST['post_title'] ) ) {
				update_post_meta( $post_id, 'banner_title', trim( $_POST['post_title'] ) );
			}
			update_post_meta( $post_id, 'custom_banner_title', false );
		}

		if ( isset( $_POST['pmpro_sws_landing_page_post_id'] ) ) {
			update_post_meta( $post_id, 'landing_page_post_id', trim( $_POST['pmpro_sws_landing_page_post_id'] ) );
		} else {
			update_post_meta( $post_id, 'landing_page_post_id', false );
		}

		if ( isset( $_POST['pmpro_sws_pre_sale_content'] ) ) {
			update_post_meta( $post_id, 'pre_sale_content', trim( $_POST['pmpro_sws_pre_sale_content'] ) );
		} else {
			update_post_meta( $post_id, 'pre_sale_content', '' );
		}

		if ( isset( $_POST['pmpro_sws_sale_content'] ) ) {
			update_post_meta( $post_id, 'sale_content', trim( $_POST['pmpro_sws_sale_content'] ) );
		} else {
			update_post_meta( $post_id, 'sale_content', '' );
		}

		if ( isset( $_POST['pmpro_sws_post_sale_content'] ) ) {
			update_post_meta( $post_id, 'post_sale_content', trim( $_POST['pmpro_sws_post_sale_content'] ) );
		} else {
			update_post_meta( $post_id, 'post_css_option', '' );
		}

		$possible_options = PMPro_SWS_Banners::get_registered_banners();
		if ( isset( $_POST['pmpro_sws_use_banner'] ) && array_key_exists( trim( $_POST['pmpro_sws_use_banner'] ), $possible_options ) ) {
			update_post_meta( $post_id, 'use_banner', trim( $_POST['pmpro_sws_use_banner'] ) );
		} else {
			update_post_meta( $post_id, 'use_banner', 'no' );
		}

		if ( isset( $_POST['pmpro_sws_link_text'] ) ) {
			update_post_meta( $post_id, 'link_text', trim( $_POST['pmpro_sws_link_text'] ) );
		} else {
			update_post_meta( $post_id, 'link_text', '' );
		}

		if ( isset( $_POST['pmpro_sws_css_option'] ) ) {
			update_post_meta( $post_id, 'css_option', trim( $_POST['pmpro_sws_css_option'] ) );
		} else {
			update_post_meta( $post_id, 'css_option', '' );
		}

		if ( isset( $_POST['pmpro_sws_hide_for_levels'] ) && is_array( $_POST['pmpro_sws_hide_for_levels'] ) ) {
			update_post_meta( $post_id, 'hide_for_levels', $_POST['pmpro_sws_hide_for_levels'] );
		} else {
			update_post_meta( $post_id, 'hide_for_levels', [] );
		}

		if ( isset( $_POST['pmpro_sws_hide_on_checkout'] ) ) {
			update_post_meta( $post_id, 'hide_on_checkout', true );
		} else {
			update_post_meta( $post_id, 'hide_on_checkout', false );
		}

		if ( isset( $_POST['pmpro_sws_upsell_enabled'] ) ) {
			update_post_meta( $post_id, 'upsell_enabled', true );
			if ( isset( $_POST['pmpro_sws_upsell_levels'] ) && is_array( $_POST['pmpro_sws_upsell_levels'] ) ) {
				update_post_meta( $post_id, 'upsell_levels', $_POST['pmpro_sws_upsell_levels'] );
			} else {
				update_post_meta( $post_id, 'upsell_levels', [] );
			}
			if ( isset( $_POST['pmpro_sws_upsell_text'] ) ) {
				update_post_meta( $post_id, 'upsell_text', trim( $_POST['pmpro_sws_upsell_text'] ) );
			} else {
				update_post_meta( $post_id, 'upsell_text', '' );
			}
		} else {
			update_post_meta( $post_id, 'upsell_enabled', false );
			update_post_meta( $post_id, 'upsell_levels', [] );
			update_post_meta( $post_id, 'upsell_text', '' );
		}

		$options = PMPro_SWS_Settings::pmprosws_get_options();
		if ( isset( $_POST['pmpro_sws_set_as_sitewide_sale'] ) ) {
			$options['active_sitewide_sale_id'] = $post_id;
		} elseif ( $options['active_sitewide_sale_id'] === $post_id . '' ) {
			$options['active_sitewide_sale_id'] = false;
		}
		PMPro_SWS_Settings::pmprosws_save_options( $options );

		if ( isset( $_POST['pmpro_sws_create_discount'] ) ) {
			wp_redirect( esc_html( get_admin_url() ) . 'admin.php?page=pmpro-discountcodes&edit=-1&pmpro_sws_callback=' . $post_id );
			exit();
		}

		if ( isset( $_POST['pmpro_sws_edit_discount'] ) ) {
			wp_redirect( esc_html( get_admin_url() ) . 'admin.php?page=pmpro-discountcodes&edit=' . get_post_meta( $post_id, 'discount_code_id', true ) . '&pmpro_sws_callback=' . $post_id );
			exit();
		}

		if ( isset( $_POST['pmpro_sws_create_landing_page'] ) ) {
			wp_redirect( esc_html( get_admin_url() ) . 'post-new.php?post_type=page&pmpro_sws_callback=' . $post_id );
			exit();
		}
		if ( isset( $_POST['pmpro_sws_edit_landing_page'] ) ) {
			wp_redirect( esc_html( get_admin_url() ) . 'post.php?post=' . get_post_meta( $post_id, 'landing_page_post_id', true ) . '&action=edit&pmpro_sws_callback=' . $post_id );
			exit();
		}
	}

	/**
	 * Updates Sitewide Sale's discount code id on save
	 *
	 * @param int $saveid discount code being saved.
	 */
	public static function discount_code_on_save( $saveid ) {
		if ( isset( $_REQUEST['pmpro_sws_callback'] ) ) {
			update_post_meta( $_REQUEST['pmpro_sws_callback'], 'discount_code_id', $saveid );
		}
	}

	/**
	 * Displays a link back to Sitewide Sale when discount code is edited/saved
	 */
	public static function return_from_editing_discount_code() {
		if ( isset( $_REQUEST['pmpro_sws_callback'] ) && 'memberships_page_pmpro-discountcodes' === get_current_screen()->base ) {
			?>
			<div class="notice notice-success">
				<p><?php esc_html_e( 'Click ', 'pmpro_sitewide_sale' ); ?>
					<a href="<?php echo esc_html( get_admin_url() ) . 'post.php?post=' . $_REQUEST['pmpro_sws_callback'] . '&action=edit'; ?>">
						<?php esc_html_e( 'here', 'pmpro_sitewide_sale' ); ?>
					</a>
					<?php esc_html_e( ' to go back to editing Sitewide Sale', 'pmpro_sitewide_sale' ); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Updates Sitewide Sale's landing page id on save
	 *
	 * @param int $saveid landing page being saved.
	 */
	public static function landing_page_on_save( $saveid ) {
		if ( isset( $_REQUEST['pmpro_sws_callback'] ) ) {
			update_post_meta( $_REQUEST['pmpro_sws_callback'], 'landing_page_post_id', $saveid );
		}
	}

	/**
	 * Redirects to Sitewide Sale after landing page is saved
	 *
	 * @param  string $location Previous redirect location.
	 * @param  int    $post_id  id of page that was edited.
	 * @return string           New redirect location
	 */
	public static function redirect_after_page_save( $location, $post_id ) {
		$post_type = get_post_type( $post_id );
		// Grab referrer url to see if it was sent there from editing a sitewide sale.
		$url = $_REQUEST['_wp_http_referer'];
		if ( 'page' === $post_type && ! empty( strpos( $url, 'pmpro_sws_callback=' ) ) ) {
			// Get id of sitewide sale to redirect to.
			$sitewide_sale_id = explode( 'pmpro_sws_callback=', $url )[1];
			$sitewide_sale_id = explode( '$', $sitewide_sale_id )[0];
			$location = esc_html( get_admin_url() ) . 'post.php?post=' . $sitewide_sale_id . '&action=edit';
		}
		return $location;
	}
}

new PMPro_SWS_MetaBoxes();
