<?php
/**
 * Register a meta box using a class.
 */
class SWS_Meta_Boxes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	/**
	 * Meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_sws_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_sws_metaboxes' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'metaboxes_above_editor' ) );
		add_action( 'edit_form_after_title', array( $this, 'move_metaboxes_above_editor' ) );
		// add_action( 'save_post', 'pmpro_sws_save_cpt', 10, 2 );
	}
	public function metaboxes_above_editor( $post_type ) {
		add_meta_box(
			'pmpro_sws_cpt_step_1',
			__( 'Step 1: Choose Discount Code to Associate With Sale', 'pmpro_sitewide_sale' ),
			array( $this, 'pmpro_sws_cpt_display_step_1' ),
			array( 'sws_banner', 'sws_landing_page' ),
			'above_editor',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_2',
			__( 'Step 2: Create Landing Page', 'pmpro_sitewide_sale' ),
			array( $this, 'pmpro_sws_cpt_display_step_2' ),
			array( 'sws_banner', 'sws_landing_page' ),
			'above_editor',
			'high'
		);
	}
	public function move_metaboxes_above_editor() {
		// Get the globals:
		global $post, $wp_meta_boxes;

		// Output the "advanced" meta boxes:
		do_meta_boxes( get_current_screen(), 'above_editor', $post );

		// Remove the initial "advanced" meta boxes:
		unset( $wp_meta_boxes['sws_banner']['above_editor'] );
		unset( $wp_meta_boxes['sws_landing_page']['above_editor'] );
	}

	/**
	 * Add the metaboxes.
	 */
	public function add_sws_metaboxes() {
		add_meta_box(
			'pmpro_sws_cpt_set_as_sitewide_sale',
			__( 'Sitewide Sale', 'pmpro_sitewide_sale' ),
			array( $this, 'pmpro_sws_cpt_display_set_as_sitewide_sale' ),
			array( 'sws_banner', 'sws_landing_page' ),
			'normal',
			'side'
		);

		// Removing Step 1
		add_meta_box(
			'pmpro_sws_cpt_step_3',
			__( 'Step 3: Steup Banners', 'pmpro_sitewide_sale' ),
			array( $this, 'pmpro_sws_cpt_display_step_3' ),
			array( 'sws_banner', 'sws_landing_page' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_4',
			__( 'Step 4: Steup Banners', 'pmpro_sitewide_sale' ),
			array( $this, 'pmpro_sws_cpt_display_step_4' ),
			array( 'sws_banner', 'sws_landing_page' ),
			'normal',
			'high'
		);
	}

	/**
	 * Renders the meta box.
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
	}

	public function pmpro_sws_cpt_display_set_as_sitewide_sale( $post ) {
		$init_checked = false;
		if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
			$init_checked = true;
		} else {
			$options = pmprosws_get_options();
			if ( $post->ID . '' === $options['active_sitewide_sale_id'] ) {
				$init_checked = true;
			}
		}
		echo '<table class="form-table"><tr>
	<th scope="row" valign="top"><label>' . esc_html( 'Set as Current Sitewide Sale', 'pmpro-sitewide-sale' ) . ':</label></th>
	<td><input name="pmpro_sws_set_as_sitewide_sale" type="checkbox" ' . ( $init_checked ? 'checked' : '' ) . ' /></td>
	</tr></table>';
	}

	public function pmpro_sws_cpt_display_step_1( $post ) {
		global $wpdb;
		$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
		$current_discount = esc_html( get_post_meta( $post->ID, 'discount_code_id', true ) );
		if ( empty( $current_discount ) ) {
			$current_discount = false;
		}
	?>
	<select class="discount_code_select pmpro_sws_option" id="pmpro_sws_discount_code_select" name="pmpro_sws_discount_code_id">
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
	'admin.php?page=pmpro-discountcodes&edit=-1&set_sitewide_sale=true">' . esc_html( 'create a new discount code, doesn\'t update', 'pmpro_sitewide_sale' ) . '</a>';
		?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_discount_code_select").selectWoo();
		});
	</script>
	<?php
	}

	public function pmpro_sws_cpt_display_step_2( $post ) {
		global $wpdb;
		$pages        = get_pages();
		$current_page = esc_html( get_post_meta( $post->ID, 'landing_page_post_id', true ) );
		if ( empty( $current_page ) ) {
			$current_page = false;
		}

		?>
		<select class="landing_page_select pmpro_sws_option" id="pmpro_sws_landing_page_select" name="pmpro_sws_landing_page_post_id">
		<option value=-1></option>
		<?php
		foreach ( $pages as $page ) {
			$selected_modifier = '';
			if ( $page->ID . '' === $current_page ) {
				$selected_modifier = ' selected="selected"';
			}
			echo '<option value=' . esc_html( $page->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( $page->post_title ) . '</option>';
		}
		echo '</select> ' . esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) . 'post-new.php?post_type=page&set_sitewide_sale=true&sws_default=true">
			 ' . esc_html( 'create a new page, doesn\'t work yet', 'pmpro_sitewide_sale' ) . '</a>.';
		?>
		<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_landing_page_select").selectWoo();
		});
		</script>
		<?php
	}

	public function pmpro_sws_cpt_display_step_3( $post ) {
		// This should be optimized to use a single get_post_meta call.
		$use_banner = esc_html( get_post_meta( $post->ID, 'use_banner', true ) );
		if ( empty( $use_banner ) ) {
			$use_banner = 'no';
		}
		$banner_title = esc_html( get_post_meta( $post->ID, 'banner_title', true ) );
		if ( empty( $banner_title ) ) {
			$banner_title = '';
		}
		$banner_description = esc_html( get_post_meta( $post->ID, 'banner_description', true ) );
		if ( empty( $banner_description ) ) {
			$banner_description = '';
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
				<option value="top" <?php selected( $use_banner, 'top' ); ?>><?php esc_html_e( 'Yes. Top of Site.', 'pmpro-sitewide-sale' ); ?></option>
				<option value="bottom" <?php selected( $use_banner, 'bottom' ); ?>><?php esc_html_e( 'Yes. Bottom of Site.', 'pmpro-sitewide-sale' ); ?></option>
				<option value="bottom-right" <?php selected( $use_banner, 'bottom-right' ); ?>><?php esc_html_e( 'Yes. Bottom Right of Site.', 'pmpro-sitewide-sale' ); ?></option>
			</select></td>
		</tr></table>
		<table class="form-table" id="pmpro_sws_banner_options">
	<?php
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Banner Title', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><input class="pmpro_sws_option" type="text" name="pmpro_sws_banner_title" value="' . esc_html( $banner_title ) . '"/></td>
	</tr>';
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Banner Description', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea rows="5" cols="20" class="pmpro_sws_option" name="pmpro_sws_banner_description">' . esc_textarea( $banner_description ) . '</textarea></td>
	</tr>';
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Button Text', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><input class="pmpro_sws_option" type="text" name="pmpro_sws_link_text" value="' . esc_html( $link_text ) . '"/></td>
	</tr>';

	echo '
	<tr>
		<th scope="row" valign="top"><label>' . esc_html( 'Custom Banner CSS', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea class="pmpro_sws_option" name="pmpro_sws_css_option">' . esc_html( $css_option ) . '</textarea></td>
	</tr>';
	echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( 'Hide Banner by Membership Level', 'pmpro-sitewide-sale' ) . '</label></th>
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
			<th scope="row" valign="top"><label>' . esc_html( 'Hide Banner at Checkout', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><input class="pmpro_sws_option" type="checkbox" name="pmpro_sws_hide_on_checkout" ' . esc_html( $checked_modifier ) . '/></td>
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

	public function pmpro_sws_cpt_display_step_4( $post ) {
		?>
		<a href="<?php echo admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ); ?>" target="_blank"><?php _e( 'Click here to view Sitewide Sale reports, need direct link.', 'pmpro-sitewide-sale' ); ?></a>
	<?php
	}

	public function pmpro_sws_save_cpt( $post_id, $post ) {
		if ( 'pmpro_sitewide_sale' !== $post->post_type ) {
			return;
		}

		if ( isset( $_POST['pmpro_sws_discount_code_id'] ) ) {
			update_post_meta( $post_id, 'discount_code_id', trim( $_POST['pmpro_sws_discount_code_id'] ) );
		} else {
			update_post_meta( $post_id, 'discount_code_id', false );
		}

		if ( isset( $_POST['pmpro_sws_landing_page_post_id'] ) ) {
			update_post_meta( $post_id, 'landing_page_post_id', trim( $_POST['pmpro_sws_landing_page_post_id'] ) );
		} else {
			update_post_meta( $post_id, 'landing_page_post_id', false );
		}

		$possible_options = [ 'no', 'top', 'bottom', 'bottom-right' ];
		if ( isset( $_POST['pmpro_sws_use_banner'] ) && in_array( trim( $_POST['pmpro_sws_use_banner'] ), $possible_options, true ) ) {
			update_post_meta( $post_id, 'use_banner', trim( $_POST['pmpro_sws_use_banner'] ) );
		} else {
			update_post_meta( $post_id, 'use_banner', 'no' );
		}

		if ( isset( $_POST['pmpro_sws_banner_title'] ) ) {
			update_post_meta( $post_id, 'banner_title', trim( $_POST['pmpro_sws_banner_title'] ) );
		} else {
			update_post_meta( $post_id, 'banner_title', '' );
		}

		if ( isset( $_POST['pmpro_sws_banner_description'] ) ) {
			update_post_meta( $post_id, 'banner_description', trim( $_POST['pmpro_sws_banner_description'] ) );
		} else {
			update_post_meta( $post_id, 'banner_description', '' );
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

		$options = pmprosws_get_options();
		if ( isset( $_POST['pmpro_sws_set_as_sitewide_sale'] ) ) {
			$options['active_sitewide_sale_id'] = $post_id;
		} elseif ( $options['active_sitewide_sale_id'] === $post_id . '' ) {
			$options['active_sitewide_sale_id'] = false;
		}
		pmprosws_save_options( $options );
	}

	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_sws_metaboxes( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

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
	}
}

new SWS_Meta_Boxes();
