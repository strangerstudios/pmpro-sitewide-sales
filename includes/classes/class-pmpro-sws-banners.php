<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Banners {

	public static function init() {
		add_action( 'wp', array( __CLASS__, 'choose_banner' ) );
		add_action( 'wp_head', array( __CLASS__, 'apply_custom_css' ), 5 );
	}

	/**
	 * Gets info about available banners including name and available
	 * css selectors.
	 *
	 * @return array banner_name => array( option_title=>string, callback=>string, css_selctors=>array(strings) )
	 */
	public static function get_registered_banners() {

		$registered_banners = array(
			'top' => array(
				'option_title'  => __( 'Yes, Top of Site', 'pmpro_sitewide_sale' ),
				'callback'      => array( __CLASS__, 'hook_top_banner' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_top',
					'.pmpro_btn',
				),
			),
			'bottom' => array(
				'option_title'  => __( 'Yes, Bottom of Site', 'pmpro_sitewide_sale' ),
				'callback'      => array( __CLASS__, 'hook_bottom_banner' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_bottom',
					'.pmpro_sws_banner-inner',
					'.pmpro_sws_banner-inner-left',
					'.pmpro_sws_banner-inner-right',
					'.pmpro_btn',
				),
			),
			'bottom_right' => array(
				'option_title'  => __( 'Yes, Bottom Right of Site', 'pmpro_sitewide_sale' ),
				'callback'      => array( __CLASS__, 'hook_bottom_right_banner' ),
				'css_selectors' => array(
					'#pmpro_sws_banner_bottom_right',
					'.pmpro_btn',
				),
			),
		);

		/**
		 * Modify Registerted Banners
		 *
		 * @since 0.0.1
		 *
		 * @param array $registered_banners contains all currently registered banners.
		 */
		$registered_banners = apply_filters( 'pmpro_sws_registered_banners', $registered_banners );

		return $registered_banners;
	}

	/**
	 * Logic for when to show banners/which banner to show
	 */
	public static function choose_banner() {
		// Can be optimized to use a single get_post_meta call.
		global $pmpro_pages;
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		if ( false === $active_sitewide_sale || 'sws_sitewide_sale' !== get_post_type( $active_sitewide_sale ) ) {
			// $active_sitewide_sale not set or is a different post type.
			// return;
		}

		$membership_level = pmpro_getMembershipLevelForUser();

		if ( false !== get_post_meta( $active_sitewide_sale, 'discount_code_id', true ) &&
					false !== get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) &&
					'no' !== get_post_meta( $active_sitewide_sale, 'use_banner', true ) &&
					! PMPro_SWS_Setup::is_login_page() &&
					! is_page( intval( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ) ) &&
					! ( get_post_meta( $active_sitewide_sale, 'hide_on_checkout', true ) && is_page( $pmpro_pages['checkout'] ) ) &&
					( false === $membership_level || ! in_array( $membership_level->ID, get_post_meta( $active_sitewide_sale, 'hide_for_levels', true ), true ) ) &&
					date( 'Y-m-d' ) >= get_post_meta( $active_sitewide_sale, 'start_date', true ) &&
					date( 'Y-m-d' ) <= get_post_meta( $active_sitewide_sale, 'end_date', true )
				) {

			// Display the appropriate banner
			// get_post_meta( $active_sitewide_sale, 'use_banner', true ) will be something like top, bottom, etc.
			$registered_banners = self::get_registered_banners();
			$banner_to_use = get_post_meta( $active_sitewide_sale, 'use_banner', true );
			if ( array_key_exists( $banner_to_use, $registered_banners ) && array_key_exists( 'callback', $registered_banners[ $banner_to_use ] ) ) {
				$callback_func = $registered_banners[ $banner_to_use ]['callback'];
				if ( is_array( $callback_func ) ) {
					if ( 2 >= count( $callback_func ) && method_exists( $callback_func[0], $callback_func[1] ) && is_callable( $callback_func[0], $callback_func[1] ) ) {
						call_user_func( $callback_func[0] . '::' . $callback_func[1] );
					}
				} elseif ( is_string( $callback_func ) ) {
					if ( is_callable( $callback_func ) ) {
						call_user_func( $callback_func );
					}
				}
			}
		}
	}

	/**
	 * Applies user's custom css to banner
	 */
	public static function apply_custom_css() {
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$registered_banners = self::get_registered_banners();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		$print_reg_nanners = print_r( $active_sitewide_sale );
		if ( false === $active_sitewide_sale || 'sws_sitewide_sale' !== get_post_type( $active_sitewide_sale ) ) {
			// $active_sitewide_sale not set or is a different post type.
			return;
		}

		$css = get_post_meta( $active_sitewide_sale, 'css_option', true )
		?>
		<style type="text/css">
			#page::before {
				content: "Not seeing anything: should be  . $active_sitewide_sale ";
			}
			<?php
			if ( ! empty( $css ) ) {
				echo $css;
			}
			?>
		</style>
		<?php
	}

	/**
	 * Sets top banner to be added
	 */
	public static function hook_top_banner() {
		add_action( 'wp_head', array( __CLASS__, 'show_top_banner' ) );
	}

	/**
	 * Adds top banner
	 */
	public static function show_top_banner() {
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		/* Maybe use JavaScript here to detect the height of the bar and adjust margin-top of html elemenet. */
		?>
		<div id="pmpro_sws_banner_top" class="pmpro_sws_banner">
			<?php echo esc_attr_e( get_post_field( 'post_content', $active_sitewide_sale ) ); ?>
			<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
		</div> <!-- end pmpro_sws_banner -->
		<?php
	}

	/**
	 * Sets bottom banner to be added
	 */
	public static function hook_bottom_banner() {
		add_action( 'wp_footer', array( __CLASS__, 'show_bottom_banner' ) );
	}

	/**
	 * Adds bottom banner
	 */
	public static function show_bottom_banner() {
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		?>
		<div id="pmpro_sws_banner_bottom" class="pmpro_sws_banner">
			<div class="pmpro_sws_banner-inner">
			<a href="javascript:void(0);" onclick="document.getElementById('pmpro_sws_banner_bottom').style.display = 'none';" class="dismiss">x</a>
				<div class="pmpro_sws_banner-inner-left">
					<h3><?php _e( get_post_meta( $active_sitewide_sale, 'banner_title', true ) ); ?></h3>
					<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $active_sitewide_sale ) ); ?>
				</div>
				<div class="pmpro_sws_banner-inner-right">
					<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
				</div>
			</div>
		</div> <!-- end pmpro_sws_banner -->
		<?php
	}

	/**
	 * Sets bottom right banner to be added
	 */
	public static function hook_bottom_right_banner() {
		add_action( 'wp_footer', array( __CLASS__, 'show_bottom_right_banner' ) );
	}

	/**
	 * Adds bottom right banner
	 */
	public static function show_bottom_right_banner() {
		$options = PMPro_SWS_Settings::pmprosws_get_options();
		$active_sitewide_sale = $options['active_sitewide_sale_id'];
		?>
		<div id="pmpro_sws_banner_bottom_right" class="pmpro_sws_banner">
			<a href="javascript:void(0);" onclick="document.getElementById('pmpro_sws_banner_bottom_right').style.display = 'none';" class="dismiss">x</a>
			<h3><?php _e( get_post_meta( $active_sitewide_sale, 'banner_title', true ) ); ?></h3>
			<?php echo wpautop( get_post_field( 'post_content', $active_sitewide_sale ) ); ?>
			<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
		</div> <!-- end pmpro_sws_banner -->
		<?php
	}
}
