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
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'load-post.php', array( __CLASS__, 'init_metabox' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'init_metabox' ) );
		add_action( 'pmpro_save_discount_code', array( __CLASS__, 'discount_code_on_save' ) );
		add_action( 'save_post', array( __CLASS__, 'landing_page_on_save' ), 10, 3 );
		add_action( 'admin_notices', array( __CLASS__, 'return_from_editing_discount_code_box' ) );
		add_action( 'enter_title_here', array( __CLASS__, 'update_title_placeholder_text' ), 10, 2 );
		add_filter( 'redirect_post_location', array( __CLASS__, 'redirect_after_page_save' ), 10, 2 );
		add_action( 'wp_ajax_pmpro_sws_create_landing_page', array( __CLASS__, 'create_landing_page_ajax' ) );
		add_action( 'wp_ajax_pmpro_sws_create_discount_code', array( __CLASS__, 'create_discount_code_ajax' ) );
	}

	/**
	 * Enqueues js/pmpro-sws-cpt-meta.js
	 */
	public static function enqueue_scripts() {
		global $typenow;
		if ( 'pmpro_sitewide_sale' === $typenow ) {
			wp_register_script( 'pmpro_sws_cpt_meta', plugins_url( 'includes/js/pmpro-sws-cpt-meta.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
			wp_enqueue_script( 'pmpro_sws_cpt_meta' );
			wp_localize_script( 'pmpro_sws_cpt_meta', 'pmpro_sws', array(
				'create_discount_code_nonce' => wp_create_nonce( 'pmpro_sws_create_discount_code' ),
				'create_landing_page_nonce' => wp_create_nonce( 'pmpro_sws_create_landing_page' ),
				'home_url' => home_url(),
				'admin_url' => admin_url(),
				)
			);

			wp_register_style( 'admin-dash', plugins_url( 'includes/css/sws-admin.css', dirname( dirname( __FILE__ ) ) ), '1.0.4' );
			wp_enqueue_style( 'admin-dash' );
		}
	}

	/**
	 * Meta box initialization.
	 */
	public static function init_metabox() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_sws_metaboxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_sws_metaboxes' ), 10, 2 );
	}

	/**
	 * Add/remove the metaboxes.
	 */
	public static function add_sws_metaboxes() {
		add_meta_box(
			'pmpro_sws_cpt_set_as_sitewide_sale',
			__( 'Sitewide Sale', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_set_as_sitewide_sale' ),
			array( 'pmpro_sitewide_sale' ),
			'side',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_1',
			__( 'Step 1: Start and End Dates', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_1' ),
			array( 'pmpro_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_2',
			__( 'Step 2: Discount Code', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_2' ),
			array( 'pmpro_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_3',
			__( 'Step 3: Landing Page', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_3' ),
			array( 'pmpro_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_4',
			__( 'Step 4: Banners', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_4' ),
			array( 'pmpro_sitewide_sale' ),
			'normal',
			'high'
		);
		add_meta_box(
			'pmpro_sws_cpt_step_5',
			__( 'Step 5: Reports', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_step_5' ),
			array( 'pmpro_sitewide_sale' ),
			'normal',
			'high'
		);

		// remove some default metaboxes
		remove_meta_box( 'slugdiv', 'pmpro_sitewide_sale', 'normal' );
		remove_meta_box( 'submitdiv', 'pmpro_sitewide_sale', 'side' );
	}

	public static function display_set_as_sitewide_sale( $post ) {
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		$init_checked = false;
		if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
			$init_checked = true;
		} else {
			$options = PMPro_SWS_Settings::pmprosws_get_options();
			if ( empty( $options['active_sitewide_sale_id'] ) && $post->post_status == 'auto-draft'
				|| $post->ID . '' === $options['active_sitewide_sale_id'] ) {
				$init_checked = true;
			}
		}
		?>
		<table class="form-table">
			<tr>
				<th scope="row" valign="top">
					<label for="pmpro_sws_set_as_sitewide_sale"><?php esc_html_e( 'Set as Current Sitewide Sale:', 'pmpro-sitewide-sale' );?></label>
				</th>
				<td>
					<input name="pmpro_sws_set_as_sitewide_sale" id="pmpro_sws_set_as_sitewide_sale" type="checkbox" <?php checked( $init_checked, true );?> />
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save All Settings', 'pmpro-sitewide-sale' ); ?>">
				</td>
			</tr>
			<tr>
				<td><a target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ) );?>"><?php esc_html_e( 'View Sitewide Sale Reports', 'pmpro-sitewide-sale' ); ?></a></td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Filter the "Enter title here" placeholder in the title field
	 */
	public static function update_title_placeholder_text( $text, $post ) {
		if ( $post->post_type == 'pmpro_sitewide_sale' ) {
			$text = __( 'Enter title here. (For reference only.)', 'pmpro-sitewide-sale' );
		}

		return $text;
	}

	public static function display_step_1( $post ) {

		global $wpdb;

		$start_day   = 0;
		$start_month = 0;
		$start_year  = 0;
		$end_day     = 0;
		$end_month   = 0;
		$end_year    = 0;

		$start_date = esc_html( get_post_meta( $post->ID, 'pmpro_sws_start_date', true ) );
		$end_date = esc_html( get_post_meta( $post->ID, 'pmpro_sws_end_date', true ) );

		if ( ! empty( $start_date ) && ! empty( $end_date ) &&
					is_string( $start_date ) && is_string( $end_date ) &&
					count( explode( '-', $start_date ) ) === 3 && count( explode( '-', $end_date ) ) === 3 ) {
			$start_exploded = explode( '-', $start_date );
			$end_exploded   = explode( '-', $end_date );
			$start_day      = $start_exploded[2];
			$start_month    = $start_exploded[1];
			$start_year     = $start_exploded[0];
			$end_day        = $end_exploded[2];
			$end_month      = $end_exploded[1];
			$end_year       = $end_exploded[0];
		} else {
			$start_day   = date( 'd', current_time( 'timestamp') );
			$start_month = date( 'm', current_time( 'timestamp') );
			$start_year  = date( 'Y', current_time( 'timestamp') );
			$end_day     = date( 'd', strtotime( '+1 week', current_time( 'timestamp') ) );
			$end_month   = date( 'm', strtotime( '+1 week', current_time( 'timestamp') ) );
			$end_year    = date( 'Y', strtotime( '+1 week', current_time( 'timestamp') ) );
		}
		?>
		<p><?php esc_html_e( 'These fields control when the banner (if applicable) and built-in sale reporting will be active for your site. They also control what content is displayed on your sale Landing Page according to the "Landing Page" settings in Step 3 below.', 'pmpro-sitewide-sale' ); ?></p>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label for="pmpro_sws_start_date"><?php _e( 'Sale Start Date', 'pmpro-sitewide-sale' );?>:</label></th>
					<td>
						<select name="pmpro_sws_start_month">
							<?php
							for ( $i = 1; $i < 13; $i++ ) {
								?>
								<option value="<?php echo esc_html( $i ); ?>" <?php if ($i == $start_month) { ?>selected="selected"<?php } ?>><?php echo date_i18n("M", strtotime($i . "/1/" . $start_year, current_time("timestamp")))?></option>
								<?php
							}
							?>
						</select>
						<input name="pmpro_sws_start_day" type="text" size="2" value="<?php echo esc_html( $start_day ); ?>" />
						<input name="pmpro_sws_start_year" type="text" size="4" value="<?php echo esc_html( $start_year ); ?>" />
						<p><small class="pmpro_lite"><?php esc_html_e( 'Set this date to the first day of your sale.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label for="pmpro_sws_end_date"><?php _e('Sale End Date', 'pmpro-sitewide-sale' );?>:</label></th>
					<td>
						<select name="pmpro_sws_end_month">
							<?php
							for ( $i = 1; $i < 13; $i++ ) {
								?>
								<option value="<?php echo esc_html( $i ); ?>" <?php if ( $i == $end_month ) { ?>selected="selected"<?php } ?>><?php echo date_i18n("M", strtotime($i . "/1/" . $end_year, current_time("timestamp")))?></option>
								<?php
							}
							?>
						</select>
						<input name="pmpro_sws_end_day" type="text" size="2" value="<?php echo esc_html( $end_day ); ?>" />
						<input name="pmpro_sws_end_year" type="text" size="4" value="<?php echo esc_html( $end_year ); ?>" />
						<p><small class="pmpro_lite"><?php esc_html_e( 'Set this date to the last full day of your sale.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save All Settings', 'pmpro-sitewide-sale' ); ?>">
		<?php
	}

	public static function display_step_2( $post ) {
		global $wpdb;
		$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
		$current_discount = esc_html( get_post_meta( $post->ID, 'pmpro_sws_discount_code_id', true ) );
		if ( empty( $current_discount ) ) {
			$current_discount = false;
		}
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="pmpro_sws_discount_code_id">Discount Code</label></th>
					<td>
						<select class="discount_code_select pmpro_sws_option" id="pmpro_sws_discount_code_select" name="pmpro_sws_discount_code_id">
							<option value="0"><?php esc_html_e( '- Choose One -', 'pmpro-sitewide-sale'); ?></option>
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
						?>
						</select>
						<p>
							<span id="pmpro_sws_after_discount_code_select">
							<?php
								if ( $code_found ) {
									$edit_code_url = admin_url( 'admin.php?page=pmpro-discountcodes&edit=' . $current_discount );
									?>
									<a target="_blank" class="button button-secondary" id="pmpro_sws_edit_discount_code" href="<?php echo esc_url( $edit_code_url );?>"><?php esc_html_e( 'edit code', 'pmpro-sitewide-sale' );?></a>
									<?php
									esc_html_e( ' or ', 'pmpro_sitewide_sale' );
								}
							?>
							</span>
							<button type="button" id="pmpro_sws_create_discount_code" class="button button-secondary"><?php esc_html_e( 'create a new discount code', 'pmpro-sitewide-sale' );?></button>
							<p><small class="pmpro_lite"><?php esc_html_e( 'Select the code that will be automatically applied for users that complete an applicable membership checkout after visiting your Landing Page.', 'pmpro-sitewide-sale' ); ?></small></p>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save All Settings', 'pmpro-sitewide-sale' ); ?>">
		<?php
	}

	public static function display_step_3( $post ) {
		global $wpdb;
		$pages        = get_pages();
		$current_page = esc_html( get_post_meta( $post->ID, 'pmpro_sws_landing_page_post_id', true ) );
		if ( empty( $current_page ) ) {
			$current_page = false;
		}

		$default_level = get_post_meta( $post->ID, 'pmpro_sws_landing_page_default_level_id', true );

		$template = esc_html( get_post_meta( $post->ID, 'pmpro_sws_landing_page_template', true ) );
		if ( empty( $template ) ) {
			$template = false;
		}

		$pre_sale_content = esc_html( get_post_meta( $post->ID, 'pmpro_sws_pre_sale_content', true ) );
		if ( empty( $pre_sale_content ) ) {
			$pre_sale_content = '';
		}

		$sale_content = esc_html( get_post_meta( $post->ID, 'pmpro_sws_sale_content', true ) );
		if ( empty( $sale_content ) ) {
			$sale_content = '';
		}

		$post_sale_content = esc_html( get_post_meta( $post->ID, 'pmpro_sws_post_sale_content', true ) );
		if ( empty( $post_sale_content ) ) {
			$post_sale_content = '';
		}
		?>
		<input type="hidden" id="pmpro_sws_old_landing_page_post_id" name="pmpro_sws_old_landing_page_post_id" value="<?php echo esc_attr( $current_page );?>" />
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="pmpro_sws_landing_page_post_id"><?php esc_html_e( 'Landing Page', 'pmpro-sitewide-sale'); ?></label></th>
					<td>
						<select class="landing_page_select pmpro_sws_option" id="pmpro_sws_landing_page_select" name="pmpro_sws_landing_page_post_id">
							<option value="0"><?php esc_html_e( '- Choose One -', 'pmpro-sitewide-sale'); ?></option>
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
						?>
						</select><br />
						<small class="pmpro_lite"><?php esc_html_e( 'Include the [pmpro_sws] shortcode.', 'pmpro-sitewide-sale' );?></small>

						<p>
							<span id="pmpro_sws_after_landing_page_select" <?php if ( ! $page_found ) {?>style="display: none;"<?php } ?>>
							<?php
								$edit_page_url = admin_url( 'post.php?post=' . $current_page . '&action=edit&pmpro_sws_callback=' . $post->ID );
								$view_page_url = get_permalink( $current_page );
							?>
							<a target="_blank" class="button button-secondary" id="pmpro_sws_edit_landing_page" href="<?php echo esc_url( $edit_page_url );?>"><?php esc_html_e( 'edit page', 'pmpro-sitewide-sale' );?></a>
							&nbsp;
							<a target="_blank" class="button button-secondary" id="pmpro_sws_view_landing_page" href="<?php echo esc_url( $view_page_url );?>"><?php esc_html_e( 'view page', 'pmpro-sitewide-sale' );?></a>
							<?php
								esc_html_e( ' or ', 'pmpro_sitewide_sale' );
							?>
							</span>
							<button type="button" id="pmpro_sws_create_landing_page" class="button button-secondary"><?php esc_html_e( 'create a new landing page', 'pmpro-sitewide-sale' );?></button>
						</p>
					</td>
				</tr>
				<tr>
					<th><label for="pmpro_sws_landing_page_default_level"><?php esc_html_e( 'Default Level', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<select id="pmpro_sws_landing_page_default_level" name="pmpro_sws_landing_page_default_level">
						<option value="0"><?php esc_html_e( '- Choose One -', 'pmpro-sitewide-sale'); ?></option>
						<?php
							$all_levels = pmpro_getAllLevels( true, true );
							foreach( $all_levels as $level ) {
							?>
							<option value="<?php echo esc_attr( $level->id ); ?>" <?php selected( $default_level, $level->id );?>><?php echo esc_textarea( $level->name ); ?></option>
							<?php
							}
						?>
						</select>
					</td>
				</tr>
				<?php
					// Allow template selection if using Memberlite.
					if ( DEFINED( 'MEMBERLITE_VERSION' ) ) { ?>
					<tr>
						<th><label for="pmpro_sws_landing_page_template"><?php esc_html_e( 'Landing Page Template', 'pmpro-sitewide-sale'); ?></label></th>
						<td>
							<select class="landing_page_select_template pmpro_sws_option" id="pmpro_sws_landing_page_template" name="pmpro_sws_landing_page_template">
								<option value="0"><?php esc_html_e( 'None', 'pmpro-sitewide-sale'); ?></option>
								<?php
									$templates = array(
										'gradient' => 'Gradient',
										'neon' => 'Neon',
										'ocean' => 'Ocean',
										'photo' => 'Photo',
										'scroll' => 'Scroll',
									);
									$templates = apply_filters( 'pmpro_sws_landing_page_templates', $templates );
									foreach ( $templates as $key => $value ) {
										//d( $template );
										echo '<option value="' . esc_html( $key ) . '" ' . selected( $template, esc_html( $key ) ) . '>' . esc_html( $value ) . '</option>';
									}
								?>
							</select>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<hr />
		<p><?php _e( 'If the [pmpro_sws] shortcode is in the landing page post content, then the content from the settings below will be automatically shown on the page before, during, and after the sale. Alternatively, you can remove the shortcode and manually update the landing page content.', 'pmpro-sitewide-sale' );?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Pre-Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<textarea class="pmpro_sws_option" rows="4" name="pmpro_sws_pre_sale_content"><?php echo( esc_html( $pre_sale_content ) ); ?></textarea><br />
						<p><small class="pmpro_lite"><?php esc_html_e( 'Mention when the sale is starting and how awesome it will be.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<textarea class="pmpro_sws_option" rows="4" name="pmpro_sws_sale_content"><?php echo( esc_html( $sale_content ) ); ?></textarea><br />
						<p><small class="pmpro_lite"><?php esc_html_e( 'A membership checkout form will automatically be included when the sale is active.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Post-Sale Content', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<textarea class="pmpro_sws_option" rows="4" name="pmpro_sws_post_sale_content"><?php echo( esc_html( $post_sale_content ) ); ?></textarea><br />
						<p><small class="pmpro_lite"><?php esc_html_e( 'Mention that the sale has ended and thank your customers.', 'pmpro-sitewide-sale' );?></small></p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save All Settings', 'pmpro-sitewide-sale' ); ?>">
		<?php
	}

	public static function display_step_4( $post ) {
		// This should be optimized to use a single get_post_meta call.
		$use_banner = esc_html( get_post_meta( $post->ID, 'pmpro_sws_use_banner', true ) );
		if ( empty( $use_banner ) ) {
			$use_banner = 'no';
		}

		// Set defaults if this is a brand new post.
		if ( $post->post_status == 'auto-draft' ) {
			$banner_title = esc_html__( 'Limited Time Offer', 'pmpro-sitewide-sale' );
			$banner_text = sprintf( esc_html__( 'Save on %s membership.', 'pmpro-sitewide-sale' ), get_bloginfo( 'sitename' ) );
			$link_text = esc_html__( 'Buy Now', 'pmpro-sitewide-sale' );
			$css_option = '';
			$hide_for_levels = PMPro_SWS_Setup::get_paid_level_ids();
			$hide_on_checkout = true;
		} else {
			$banner_text = $post->post_content;

			$banner_title = esc_html( get_post_meta( $post->ID, 'pmpro_sws_banner_title', true ) );
			if ( empty( $banner_title ) ) {
				$banner_title = '';
			}
			$link_text = esc_html( get_post_meta( $post->ID, 'pmpro_sws_link_text', true ) );
			if ( empty( $link_text ) ) {
				$link_text = 'Buy Now';
			}
			$css_option = esc_html( get_post_meta( $post->ID, 'pmpro_sws_css_option', true ) );
			if ( empty( $css_option ) ) {
				$css_option = '';
			}
			$hide_for_levels = get_post_meta( $post->ID, 'pmpro_sws_hide_for_levels', true );
			if ( empty( $hide_for_levels ) ) {
				$hide_for_levels = array();
			}

			$hide_on_checkout = esc_html( get_post_meta( $post->ID, 'pmpro_sws_hide_on_checkout', true ) );
			if ( empty( $hide_on_checkout ) ) {
				$hide_on_checkout = false;
			}
		}
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Use the built-in banner?', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<select class="use_banner_select pmpro_sws_option" id="pmpro_sws_use_banner_select" name="pmpro_sws_use_banner">
							<option value="no" <?php selected( $use_banner, 'no' ); ?>><?php esc_html_e( 'No', 'pmpro-sitewide-sale' ); ?></option>
							<?php
								$registered_banners = PMPro_SWS_Banners::get_registered_banners();
								foreach ( $registered_banners as $banner => $data ) {
									if ( is_string( $banner ) && is_array( $data ) && ! empty( $data['option_title'] ) && is_string( $data['option_title'] ) ) {
										echo '<option value="' . $banner . '"' . selected( $use_banner, $banner ) . '>' . esc_html( $data['option_title'] ) . '</option>';
									}
								}
							?>
						</select>
						<input type="submit" class="button button-secondary" id="pmpro_sws_preview" name="pmpro_sws_preview" value="<?php echo esc_html__( 'Save and Preview', 'pmpro-sitewide-sale' ); ?>">
						<p><small class="pmpro_lite"><?php esc_html_e( 'Optionally display a banner, which you can customize using additional settings below, to advertise your sale.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table" id="pmpro_sws_banner_options" <?php if ( $use_banner == 'no' ) { ?>style="disaply: none;"<?php } ?>>
			<tbody>
				<tr>
					<th><label for="pmpro_sws_banner_title"><?php esc_html_e( 'Banner Title', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<input type="textbox" name="pmpro_sws_banner_title" value="<?php esc_html_e( $banner_title, 'pmpro-sitewide-sale' ); ?>">
						<p><small class="pmpro_lite"><?php esc_html_e( 'A brief title for your sale, such as the holiday or purpose of the sale. (i.e. "Limited Time Offer")', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th><label for="pmpro_sws_banner_text"><?php esc_html_e( 'Banner Text', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<textarea class="pmpro_sws_option" id="pmpro_sws_banner_text" name="pmpro_sws_banner_text"><?php echo esc_textarea( $banner_text, 'pmpro-sitewide-sale' ); ?></textarea>
						<p><small class="pmpro_lite"><?php esc_html_e( 'A brief message about your sale. (i.e. "Save 50% on membership through December.")', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Button Text', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<input class="pmpro_sws_option" type="text" name="pmpro_sws_link_text" value="<?php esc_html_e( $link_text, 'pmpro-sitewide-sale' ); ?>">
						<p><small class="pmpro_lite"><?php esc_html_e( 'The text displayed on the button of your banner that links to the Landing Page.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Custom Banner CSS', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<textarea class="pmpro_sws_option" name="pmpro_sws_css_option"><?php esc_html_e( $css_option, 'pmpro-sitewide-sale' ); ?></textarea>
						<p><small class="pmpro_lite"><?php esc_html_e( 'Optional. Use this area to add custom styles to modify the banner appearance.', 'pmpro-sitewide-sale'); ?></small></p>

						<p id="pmpro_sws_css_selectors_description" class="description" <?php if ( empty($use_banner) || $use_banner == 'no' ) {?>style="display:none;"<?php } ?>><?php esc_html_e( 'Use these selectors to alter the appearance of your banners.', 'pmpro-sitewide-sale' ); ?></p>
						<?php foreach( $registered_banners as $key => $registered_banner ) { ?>
							<div data-pmprosws-banner="<?php echo esc_attr($key);?>" class="pmpro_sws_banner_css_selectors" <?php if( $key != $use_banner ) {?>style="display: none;"<?php } ?>>
							<?php
								$css_selectors = $registered_banner['css_selectors'];
								if ( is_string( $css_selectors ) ) {
									echo $css_selectors;
								} elseif ( is_array( $css_selectors ) ) {
									foreach ( $css_selectors as $css_selector ) {
										if ( is_string( $css_selector ) ) {
											echo $css_selector . ' { }<br/>';
										}
									}
								}
							?>
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Hide Banner by Membership Level', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<input type="hidden" name="pmpro_sws_hide_for_levels_exists" value="1" />
						<select multiple class="pmpro_sws_option" id="pmpro_sws_hide_levels_select" name="pmpro_sws_hide_for_levels[]" style="width:12em">
						<?php
							$all_levels    = pmpro_getAllLevels( true, true );
							foreach ( $all_levels as $level ) {
								$selected_modifier = in_array( $level->id, $hide_for_levels ) ? ' selected="selected"' : '';
								echo '<option value=' . esc_html( $level->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $level->name ) . '</option>';
							}
						?>
						</select>
						<p><small class="pmpro_lite"><?php esc_html_e( 'This setting will hide the banner for members of the selected levels.', 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
				<tr>
					<?php
						$checked_modifier = $hide_on_checkout ? ' checked' : '';
					?>
					<th scope="row" valign="top"><label><?php esc_html_e( 'Hide Banner at Checkout', 'pmpro-sitewide-sale' ); ?></label></th>
					<td>
						<input type="hidden" name="pmpro_sws_hide_on_checkout_exists" value="1" />
						<input class="pmpro_sws_option" type="checkbox" id="pmpro_sws_hide_on_checkout" name="pmpro_sws_hide_on_checkout" <?php checked( $hide_on_checkout, 1 ); ?>> <label for="pmpro_sws_hide_on_checkout"><?php esc_html_e( 'Check this box to hide the banner on checkout pages.', 'pmpro-sitewide-sale' ); ?></label>
						<p><small class="pmpro_lite"><?php esc_html_e( "Recommended: Leave checked so only users using your landing page will pay the sale price.", 'pmpro-sitewide-sale' ); ?></small></p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save All Settings', 'pmpro-sitewide-sale' ); ?>">
		<?php
	}

	public static function display_step_5( $post ) { ?>
		<input type="submit" class="button button-primary" name="pmpro_sws_view_reports" value="<?php echo esc_html__( 'Click here to view Sitewide Sale reports', 'pmpro-sitewide-sale' ); ?>">
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
		global $wpdb;

		if ( 'pmpro_sitewide_sale' !== $post->post_type ) {
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

		if ( 'auto-draft' === $post->post_status || 'trash' === $post->post_status ) {
			return;
		}

		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ? sanitize_text_field( $_POST['custom_nonce'] ): '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			die( '<br/>Nonce failed' );
		}

		// Make sure the post title is not blank
		if( isset( $_POST['post_title'] ) && empty( $_POST['post_title'] ) ) {
			$post->post_title = sanitize_post_field(
				'post_title',
				esc_html__( 'Sitewide Sale', 'pmpro-sitewide-sale' ),
				$post->ID,
				'edit'
			);
		}

		if ( isset( $_POST['pmpro_sws_discount_code_id'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_discount_code_id', intval( $_POST['pmpro_sws_discount_code_id'] ) );
		}

		if ( ! empty( $_POST['pmpro_sws_landing_page_post_id'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_landing_page_post_id', intval( $_POST['pmpro_sws_landing_page_post_id'] ) );
			update_post_meta( intval( $_POST['pmpro_sws_landing_page_post_id'] ), 'pmpro_sws_sitewide_sale_id', $post_id );
		} elseif( isset( $_POST['pmpro_sws_landing_page_post_id'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_landing_page_post_id', false );
			delete_post_meta( intval( $_REQUEST['pmpro_sws_old_landing_page_post_id'] ), 'pmpro_sws_sitewide_sale_id' );
		}

		if ( isset( $_POST['pmpro_sws_landing_page_default_level'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_landing_page_default_level_id', intval( $_POST['pmpro_sws_landing_page_default_level'] ) );
		}

		if ( isset( $_POST['pmpro_sws_landing_page_template'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_landing_page_template', wp_kses_post( $_POST['pmpro_sws_landing_page_template'] ) );
		}

		if ( isset( $_POST['pmpro_sws_start_day'] ) && is_numeric( $_POST['pmpro_sws_start_day'] ) &&
				isset( $_POST['pmpro_sws_start_month'] ) && is_numeric( $_POST['pmpro_sws_start_month'] ) &&
				isset( $_POST['pmpro_sws_start_year'] ) && is_numeric( $_POST['pmpro_sws_start_year'] ) &&
				isset( $_POST['pmpro_sws_end_day'] ) && is_numeric( $_POST['pmpro_sws_end_day'] ) &&
				isset( $_POST['pmpro_sws_end_month'] ) && is_numeric( $_POST['pmpro_sws_end_month'] ) &&
				isset( $_POST['pmpro_sws_end_year'] ) && is_numeric( $_POST['pmpro_sws_end_year'] )
		) {
			$start_day = intval($_POST['pmpro_sws_start_day']);
			$start_month = intval($_POST['pmpro_sws_start_month']);
			$start_year = intval($_POST['pmpro_sws_start_year']);
			$end_day = intval($_POST['pmpro_sws_end_day']);
			$end_month = intval($_POST['pmpro_sws_end_month']);
			$end_year = intval($_POST['pmpro_sws_end_year']);

			//fix up dates
			$start_date = date_i18n("Y-m-d", strtotime($start_month . "/" . $start_day . "/" . $start_year, current_time("timestamp")));
			$end_date = date_i18n("Y-m-d", strtotime($end_month . "/" . $end_day . "/" . $end_year, current_time("timestamp")));

			update_post_meta( $post_id, 'pmpro_sws_start_date', $start_date );
			update_post_meta( $post_id, 'pmpro_sws_end_date', $end_date );
		}

		if ( isset( $_POST['pmpro_sws_pre_sale_content'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_pre_sale_content', wp_kses_post( $_POST['pmpro_sws_pre_sale_content'] ) );
		}

		if ( isset( $_POST['pmpro_sws_sale_content'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_sale_content', wp_kses_post( $_POST['pmpro_sws_sale_content'] ) );
		}

		if ( isset( $_POST['pmpro_sws_post_sale_content'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_post_sale_content', wp_kses_post( $_POST['pmpro_sws_post_sale_content'] ) );
		}

		$possible_options = array_merge( array( 'no' => 'no' ), PMPro_SWS_Banners::get_registered_banners() );
		if ( isset( $_POST['pmpro_sws_use_banner'] ) && array_key_exists( trim( $_POST['pmpro_sws_use_banner'] ), $possible_options ) ) {
			update_post_meta( $post_id, 'pmpro_sws_use_banner', trim( $_POST['pmpro_sws_use_banner'] ) );
		}

		if ( ! empty( $_POST['pmpro_sws_banner_title'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_banner_title', wp_kses_post( $_POST['pmpro_sws_banner_title'] ) );
		} elseif( isset( $_POST['pmpro_sws_banner_title'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_banner_title', $post->post_title );
		}

		if ( isset( $_POST['pmpro_sws_banner_text'] ) ) {
			$post->post_content = trim( $_POST['pmpro_sws_banner_text'] );
			remove_action( 'save_post', array( __CLASS__, 'save_sws_metaboxes' ) );
			wp_update_post( $post, true );
			add_action( 'save_post', array( __CLASS__, 'save_sws_metaboxes' ), 10, 2 );
		}

		if ( ! empty ( $_POST['pmpro_sws_link_text'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_link_text', sanitize_text_field( $_POST['pmpro_sws_link_text'] ) );
		} elseif( isset( $_POST['pmpro_sws_link_text'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_link_text', 'Buy Now' );
		}

		if ( isset( $_POST['pmpro_sws_css_option'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_css_option', wp_kses_post( $_POST['pmpro_sws_css_option'] ) );
		}

		if ( ! empty( $_POST['pmpro_sws_hide_for_levels'] ) && is_array( $_POST['pmpro_sws_hide_for_levels'] ) ) {
			$pmpro_sws_hide_for_levels = array_map( 'intval', $_POST['pmpro_sws_hide_for_levels'] );
			update_post_meta( $post_id, 'pmpro_sws_hide_for_levels', $pmpro_sws_hide_for_levels );
		} elseif ( isset( $_POST['pmpro_sws_hide_for_levels_exists'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_hide_for_levels', false );
		}

		if ( ! empty( $_POST['pmpro_sws_hide_on_checkout'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_hide_on_checkout', true );
		} elseif ( isset( $_POST['pmpro_sws_hide_on_checkout_exists'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_hide_on_checkout', false );
		}

		if ( isset( $_POST['pmpro_sws_upsell_enabled'] ) ) {
			update_post_meta( $post_id, 'pmpro_sws_upsell_enabled', true );
			if ( isset( $_POST['pmpro_sws_upsell_levels'] ) && is_array( $_POST['pmpro_sws_upsell_levels'] ) ) {
				$pmpro_sws_upsell_levels = array_map( 'intval', $_POST['pmpro_sws_upsell_levels'] );
				update_post_meta( $post_id, 'pmpro_sws_upsell_levels', $pmpro_sws_upsell_levels );
			} else {
				update_post_meta( $post_id, 'pmpro_sws_upsell_levels', array() );
			}
			if ( isset( $_POST['pmpro_sws_upsell_text'] ) ) {
				update_post_meta( $post_id, 'pmpro_sws_upsell_text', wp_kses_post( $_POST['pmpro_sws_upsell_text'] ) );
			} else {
				update_post_meta( $post_id, 'pmpro_sws_upsell_text', '' );
			}
		} else {
			update_post_meta( $post_id, 'pmpro_sws_upsell_enabled', false );
			update_post_meta( $post_id, 'pmpro_sws_upsell_levels', array() );
			update_post_meta( $post_id, 'pmpro_sws_upsell_text', '' );
		}

		$options = PMPro_SWS_Settings::pmprosws_get_options();
		if ( isset( $_POST['pmpro_sws_set_as_sitewide_sale'] ) ) {
			$options['active_sitewide_sale_id'] = $post_id;
		} elseif ( $options['active_sitewide_sale_id'] === $post_id . '' ) {
			$options['active_sitewide_sale_id'] = false;
		}
		PMPro_SWS_Settings::pmprosws_save_options( $options );

		if ( isset( $_POST['pmpro_sws_preview'] ) ) {
			$url_to_open = get_home_url() . '?pmpro_sws_preview_sale_banner=' . $post_id;
			wp_redirect( $url_to_open );
			exit();
		}
		if ( isset( $_POST['pmpro_sws_view_reports'] ) ) {
			wp_redirect( admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ) );
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
			update_post_meta( intval( $_REQUEST['pmpro_sws_callback'] ), 'pmpro_sws_discount_code_id', $saveid );
			?>
			<script type="text/javascript">
				window.location = "<?php echo esc_url( admin_url( 'post.php?post=' . intval( $_REQUEST['pmpro_sws_callback'] ) . '&action=edit' ) ); ?>";
			</script>
			<?php
		}
	}

	/**
	 * Displays a link back to Sitewide Sale when discount code is edited/saved
	 */
	public static function return_from_editing_discount_code_box() {
		if ( isset( $_REQUEST['pmpro_sws_callback'] ) && 'memberships_page_pmpro-discountcodes' === get_current_screen()->base ) {
			?>
			<div class="notice notice-success">
				<p><?php esc_html_e( 'Click ', 'pmpro_sitewide_sale' ); ?>
					<a href="<?php echo esc_url( admin_url( 'post.php?post=' . intval( $_REQUEST['pmpro_sws_callback'] ) . '&action=edit' ) ); ?>">
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
			update_post_meta( intval( $_REQUEST['pmpro_sws_callback'] ), 'pmpro_sws_landing_page_post_id', $saveid );
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
			$location = esc_url( admin_url( 'post.php?post=' . $sitewide_sale_id . '&action=edit' ) );
		}
		return $location;
	}

	/**
	 * AJAX callback to create a new discount code for your sale
	 */
	public static function create_discount_code_ajax() {
		global $wpdb;

		check_ajax_referer( 'pmpro_sws_create_discount_code', 'nonce' );

		if ( ! function_exists('pmpro_getDiscountCode') ) {
			exit;
		}

		$sitewide_sale_id = intval( $_REQUEST['pmpro_sws_id'] );
		if ( empty( $sitewide_sale_id ) ) {
			echo json_encode( array( 'status' => 'error', 'error' => esc_html__( 'No sitewide sale ID given. Try doing it manually.', 'pmpro-sitewide-sale') ) );
			exit;
		}

		$wpdb->insert(
			$wpdb->pmpro_discount_codes,
			array(
				'id'=>0,
				'code' => pmpro_getDiscountCode(),
				'starts' => sanitize_text_field( $_REQUEST['pmpro_sws_start'] ),
				'expires' => sanitize_text_field( $_REQUEST['pmpro_sws_end'] ),
				'uses' => 0
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%d'
			)
		);

		if ( ! empty( $wpdb->last_error ) ) {
			$r = array( 'status' => 'error', 'error' => esc_html__( 'Error inserting discount code. Try doing it manually.', 'pmpro-sitewide-sale' ) );
		} else {
			$discount_code = $wpdb->get_row( "SELECT * FROM $wpdb->pmpro_discount_codes WHERE id = '" . intval( $wpdb->insert_id ) . "' LIMIT 1");
			$r = array( 'status' => 'success', 'code' => $discount_code );
		}

		echo json_encode( $r );
		exit;
	}

	/**
	 * AJAX callback to create a new landing page for your sale
	 */
	public static function create_landing_page_ajax() {
		check_ajax_referer( 'pmpro_sws_create_landing_page', 'nonce' );

		$sitewide_sale_id = intval( $_REQUEST['pmpro_sws_id'] );
		if ( empty( $sitewide_sale_id ) ) {
			echo json_encode( array( 'status' => 'error', 'error' => esc_html__( 'No sitewide sale ID given. Try doing it manually.', 'pmpro-sitewide-sale') ) );
			exit;
		}

		$landing_page_title = sanitize_text_field( $_REQUEST['pmpro_sws_landing_page_title'] );
		if ( empty( $landing_page_title ) ) {
			$landing_page_title = esc_html__( 'Sitewide Sale Landing Page', 'pmpro-sitewide-sale' );
		}

		$landing_page_post_id = wp_insert_post( array(
			'post_title' => $landing_page_title,
			'post_content' => '[pmpro_sws]',
			'post_type' => 'page',
			'post_status' => 'publish',
			)
		);

		if ( empty( $landing_page_post_id ) ) {
			$r = array( 'status' => 'error', 'error' => esc_html__( 'Error inserting post. Try doing it manually.', 'pmpro-sitewide-sale' ) );
		} else {
			$r = array( 'status' => 'success', 'post' => get_post( $landing_page_post_id ) );
		}

		echo json_encode( $r );
		exit;
	}
}
