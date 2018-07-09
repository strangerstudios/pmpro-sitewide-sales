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
	public static function customizer_enqueue_inline() {
		wp_enqueue_style( 'customizer-inline', plugins_url( 'css/customizer-section.css', dirname( dirname( __FILE__ ) ) ), array( 'frontend' ) );
		$bg_color = get_option( 'sws_background_color' );
		$txt_color = get_option( 'sws_text_color' );
		$custom_css = "
                #pmpro_sws_banner_bottom_right {
                        background: {$bg_color};
                }
                #pmpro_sws_banner_bottom_right h3,
                #pmpro_sws_banner_bottom_right {
                        color: {$txt_color};
                }
                ";
		wp_add_inline_style( 'customizer-inline', $custom_css );
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
		$array = PMPro_Helpers\inc\classes\PMPro_Helper_Functions::get_pmpro_member_array( $user_id );
		echo '<pre> get_pmpro_member_array ';
		echo '<h2>' . $array['level_id'] . '</h2>';
		print_r( $array );
		echo '</pre>';
		echo '</div>';
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
				'description' => 'Wnat to switch pages via javascript',
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
		$pmpro_manager->add_section(
			'pmpro_section',
			array(
				'title'        => 'PMPro Sitewide Sale',
				'priority'     => 9,
				// 'panel'          => 'pmpro_sws_customizer_panel',
				'description'  => 'This is a description of this text setting in the PMPro Customizer Controls section',
			)
		);

		/**
		 * Radio control
		 */
		$pmpro_manager->add_setting(
			'sws_response_radio',
			array(
				'default'     => '1',
				'type'        => 'option',
			)
		);

		$pmpro_manager->add_control(
			'sws_response_radio',
			array(
				'section'     => 'pmpro_section',
				'type'        => 'radio',
				'settings'    => 'sws_response_radio',
				'label'       => 'Limit Post View Response',
				'description' => 'After the limit has been reached, what behavior do you want to see ',
				'choices'     => array(
					'footer'   => 'Footer enlargement',
					'popup'    => 'Pop Up',
					'redirect' => 'Redirect',
				),
				'priority'    => 11,
			)
		);

		if ( 'footer' === get_option( 'sws_response_radio' ) ) {
			$pmpro_manager->add_setting(
				'footer_text_block', array(
					'default'           => __( 'footer text', 'pmpro-sitewide-sale' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'footer_text_block',
					array(
						'label'    => __( 'Footer Text', 'pmpro-sitewide-sale' ),
						'section'  => 'pmpro_section',
						'settings' => 'footer_text_block',
						'type'     => 'text',
						'priority'    => 12,
					)
				)
			);
		}
		if ( 'popup' === get_option( 'sws_response_radio' ) ) {
			// Add setting
			$pmpro_manager->add_setting(
				'modal_header_text', array(
					'type'        => 'option',
					'default'           => __( 'Modal header text', 'pmpro-sitewide-sale' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_header_text',
					array(
						'label'    => __( 'Modal Header Text', 'pmpro-sitewide-sale' ),
						'section'  => 'pmpro_section',
						'settings' => 'modal_header_text',
						'type'     => 'text',
						'priority'    => 15,
					)
				)
			);

			// Add setting
			$pmpro_manager->add_setting(
				'modal_body_text', array(
					'type'        => 'option',
					'default'           => __( 'Modal body text', 'pmpro-sitewide-sale' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_body_text',
					array(
						'label'    => __( 'Modal Body Text', 'pmpro-sitewide-sale' ),
						'section'  => 'pmpro_section',
						'settings' => 'modal_body_text',
						'type'     => 'text',
						'priority'    => 15,
					)
				)
			);

			// Add setting
			$pmpro_manager->add_setting(
				'modal_footer_text', array(
					'type'        => 'option',
					'default'           => __( 'Modal default text', 'pmpro-sitewide-sale' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_footer_text',
					array(
						'label'    => __( 'Modal Footer Text', 'pmpro-sitewide-sale' ),
						'section'  => 'pmpro_section',
						'settings' => 'modal_footer_text',
						'type'     => 'text',
						'priority' => 15,
					)
				)
			);
		}

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
}
