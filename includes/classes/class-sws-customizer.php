<?php

// namespace PMPro_SWS_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Customizer {


	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'engage_the_customizer' ) );
		add_action( 'admin_menu', array( __CLASS__, 'pmpro_sws_dashboard_menu' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue_inline' ) );
		// add_action( 'admin_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
		// add_action( 'customize_controls_init', array( __CLASS__, 'set_customizer_preview_url' ) );
	}

	public static function get_sws_settings() {
		$sws_settings = pmprosws_get_options();
		return $sws_settings;
	}
	/**
	 * Customizer manager demo
	 *
	 * @param  WP_Customizer_Manager $pmpro_manager
	 * @return void
	 */
	public static function engage_the_customizer( $pmpro_manager ) {
		// self::pmpro_panel( $pmpro_manager );
		self::pmpro_section( $pmpro_manager );
	}

	/**
	 * [customizer_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function customizer_enqueue() {
		wp_enqueue_style( 'customizer-section', plugins_url( 'css/customizer-section.php', dirname( dirname( __FILE__ ) ) ), array( 'frontend' ) );
	}
	/**
	 * [pmpro_sws_dashboard_menu description]
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_sws_dashboard_menu() {
		add_dashboard_page( __( 'SWS Dashboard', 'pmpro-sitewide-sale' ), __( 'SWS Dashboard', 'pmpro-sitewide-sale' ), 'manage_options', 'pmpro-sws-dashboard.php', array( __CLASS__, 'pmpro_sws_dashboard_page' ) );
	}

	/**
	 * [pmpro_sws_dashboard_page description]
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_sws_dashboard_page() {
		echo '<div class="wrap">';
		echo '<h2>' . __FUNCTION__ . '</h2>';
		$user_id = 1;
		$sws_options = self::get_sws_settings();
		$theme_mods = get_theme_mods();
		echo '<a href="' . admin_url( 'admin.php?page=pmpro-sws' ) . '"><input type="button" class="button button-primary button-hero" value="Return to SWS Settings" /></a>';
		echo '<pre>pmprosws_get_options ';
		print_r( $sws_options );
		echo '</pre>';
		echo '<pre>get_theme_mods ';
		print_r( $theme_mods );
		echo '</pre>';
		echo '</div>';
	}

	public static function customizer_enqueue_inline() {
		wp_enqueue_style( 'customizer-inline', plugins_url( 'css/customizer-section.css', dirname( dirname( __FILE__ ) ) ), array( 'frontend' ) );
		$bg_color = get_option( 'sws_background_color' );
		$txt_color = get_option( 'sws_text_color' );
		$bg_img = get_option( 'sws_bgimg_upload' );
		$custom_css = "
                #pmpro_sws_banner_bottom_right {
                        background: {$bg_color} url({$bg_img}) no-repeat center;;
                }
                #pmpro_sws_banner_bottom_right h3,
                #pmpro_sws_banner_bottom_right {
                        color: {$txt_color};
                }
                ";
		wp_add_inline_style( 'customizer-inline', $custom_css );
	}

	/**
	 * [engage_customizer description]
	 *
	 * @param [type] $pmpro_manager [description]
	 * @return [type]             [description]
	 */
	private static function pmpro_panel( $pmpro_manager ) {
		$pmpro_manager->add_panel(
			'pmpro_sws_customizer_panel',
			array(
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'description' => 'Want to switch pages via javascript',
				'title'       => __( 'PMPro SWS Panel', 'pmpro-sitewide-sale' ),
			)
		);
	}

	/**
	 * The pmpro_section function adds a new section
	 * to the Customizer to display the settings and
	 * controls that we build.
	 *
	 * @param  [type] $pmpro_manager [description]
	 * @return [type]             [description]
	 */
	private static function pmpro_section( $pmpro_manager ) {
		$sws_options = self::get_sws_settings();

		$pmpro_manager->add_section(
			'pmpro_section',
			array(
				'title'        => 'PMPro Sitewide Sale',
				'priority'     => 9,
				// 'panel'          => 'pmpro_sws_customizer_panel',
				'description'  => 'This is a description of this text setting in the PMPro Customizer Controls section',
			)
		);
		// Add setting
		$pmpro_manager->add_setting(
			'sws_banner_title', array(
				'default'           => __( $sws_options['banner_title'], 'pmpro-sitewide-sale' ),
				'sanitize_callback' => 'sanitize_text',
			)
		);
		// $pmpro_manager->add_setting(
		// 'footer_text_block', array(
		// 'default'           => __( 'default text', 'pmpro-sitewide-sale' ),
		// 'sanitize_callback' => 'sanitize_text',
		// )
		// );
		// Add control
		$pmpro_manager->add_control(
			new WP_Customize_Control(
				$pmpro_manager,
				'sws_banner_title',
				array(
					'label'    => __( 'SWS Banner Title', 'pmpro-sitewide-sale' ),
					'section'  => 'pmpro_section',
					'settings' => 'sws_banner_title',
					'type'     => 'text',
				)
			)
		);

		   // 'settings' => $sws_options['landing_page_post_id'],
		/**
		 * Radio control
		 */
		$pmpro_manager->add_setting(
			'sws_bgimg_upload',
			array(
				'type'        => 'option',
			)
		);

		$pmpro_manager->add_control(
			new WP_Customize_Image_Control(
				$pmpro_manager,
				'sws_bgimg_upload',
				array(
					'label' => 'Upload Background Image',
					'section' => 'pmpro_section',
					'settings' => 'sws_bgimg_upload',
				)
			)
		);

		$pmpro_manager->add_setting(
			'sws_background_color',
			array(
				'type'       => 'option',
				'transport'  => 'refresh',
				'default'    => '#4ab1ad',
			)
		);
		$pmpro_manager->add_control(
			new WP_Customize_Color_Control(
				$pmpro_manager,
				'sws_background_color',
				array(
					'label'      => __( 'SWS Background Color', 'pmpro-sitewide-sale' ),
					'section'  => 'pmpro_section',
					'settings'   => 'sws_background_color',
				)
			)
		);
		$pmpro_manager->add_setting(
			'sws_text_color',
			array(
				'type'       => 'option',
				'transport'  => 'refresh',
				'default'    => '#ffffff',
			)
		);
		$pmpro_manager->add_control(
			new WP_Customize_Color_Control(
				$pmpro_manager,
				'sws_text_color',
				array(
					'label'      => __( 'SWS Text Color', 'pmpro-sitewide-sale' ),
					'section'  => 'pmpro_section',
					'settings'   => 'sws_text_color',
				)
			)
		);
	}

	/**
	 * The sanitize_text function adds a new section
	 * to the Customizer to display the settings and
	 * controls that we build.
	 *
	 * @param  [type] $text [description]
	 * @return [type]             [description]
	 */
	private static function sanitize_text( $text ) {
			return sanitize_text_field( $text );
	}

	public static function set_customizer_preview_url() {
		global $wp_customize;
		if ( ! isset( $_GET['url'] ) ) {
			$wp_customize->set_preview_url( get_permalink( get_page_by_title( 'Customizer Dev Page' ) ) );
		}
	}

}
