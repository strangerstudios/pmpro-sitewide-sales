<?php

// namespace PMPro_LPV_UI\inc\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_LPV_Customizer {


	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'engage_the_customizer' ) );
		add_action( 'admin_menu', array( __CLASS__, 'pmpro_lpv_dashboard_menu' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'customizer_enqueue' ) );
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
		self::pmpro_panel( $pmpro_manager );
		self::pmpro_section( $pmpro_manager );
	}

	/**
	 * [customizer_enqueue description]
	 *
	 * @return [type] [description]
	 */
	public static function customizer_enqueue() {
		wp_enqueue_style( 'customizer-section', plugins_url( '../css/customizer-section.css', __FILE__ ) );
	}
	/**
	 * [pmpro_lpv_dashboard_menu description]
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_dashboard_menu() {
		add_dashboard_page( __( 'LPV Dashboard', 'pmpro-lpv-ui' ), __( 'LPV Dashboard', 'pmpro-lpv-ui' ), 'manage_options', 'pmpro-lpv-dashboard.php', array( __CLASS__, 'pmpro_lpv_dashboard_page' ) );
	}
	/**
	 * [pmpro_lpv_dashboard_page description]
	 *
	 * @return [type] [description]
	 */
	public static function pmpro_lpv_dashboard_page() {
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
			'pmpro_lpv_customizer_panel',
			array(
				'priority'    => 10,
				'capability'  => 'edit_theme_options',
				'description' => 'Wnat to switch pages via javascript',
				'title'       => __( 'PMPro LPV Panel', 'pmpro-lpv-customizer' ),
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
				'title'        => 'PMPro Limit Post Views',
				'priority'     => 9,
				'panel'          => 'pmpro_lpv_customizer_panel',
				'description'  => 'This is a description of this text setting in the PMPro Customizer Controls section',
			)
		);

		$pmpro_manager->add_setting(
			'lpv_diagnostic_header',
			array(
				'default'    => 1,
				'type'       => 'option',
				'transport'  => 'refresh',
			)
		);

		// include plugin_dir_path( __FILE__ ) . 'class-soderland-toggle-control.php';
		$pmpro_manager->add_control(
			// new Soderland_Toggle_Control(
				// $pmpro_manager,
			'lpv_diagnostic_header',
			array(
				'label'     => __( 'Show PMPro Header Diagnostic', 'pmpro-lpv-customizer' ),
				'settings'  => 'lpv_diagnostic_header',
				'section'   => 'pmpro_section',
				'priority'  => 10,
				// 'type'      => 'ios',
				'type'      => 'checkbox',
			)
			// )
		);

		/**
		 * Radio control
		 */
		$pmpro_manager->add_setting(
			'lpv_response_radio',
			array(
				'default'     => '1',
				'type'        => 'option',
			)
		);

		$pmpro_manager->add_control(
			'lpv_response_radio',
			array(
				'section'     => 'pmpro_section',
				'type'        => 'radio',
				'settings'    => 'lpv_response_radio',
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

		if ( 'footer' === get_option( 'lpv_response_radio' ) ) {
			$pmpro_manager->add_setting(
				'footer_text_block', array(
					'default'           => __( 'footer text', 'pmpro-lpv-customizer' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'footer_text_block',
					array(
						'label'    => __( 'Footer Text', 'pmpro-lpv-customizer' ),
						'section'  => 'pmpro_section',
						'settings' => 'footer_text_block',
						'type'     => 'text',
						'priority'    => 12,
					)
				)
			);
		}
		if ( 'popup' === get_option( 'lpv_response_radio' ) ) {
			// Add setting
			$pmpro_manager->add_setting(
				'modal_header_text', array(
					'default'           => __( 'Modal header text', 'pmpro-lpv-customizer' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_header_text',
					array(
						'label'    => __( 'Modal Header Text', 'pmpro-lpv-customizer' ),
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
					'default'           => __( 'Modal body text', 'pmpro-lpv-customizer' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_body_text',
					array(
						'label'    => __( 'Modal Body Text', 'pmpro-lpv-customizer' ),
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
					'default'           => __( 'Modal default text', 'pmpro-lpv-customizer' ),
					'sanitize_callback' => 'sanitize_text',
				)
			);
			// Add control
			$pmpro_manager->add_control(
				new WP_Customize_Control(
					$pmpro_manager,
					'modal_footer_text',
					array(
						'label'    => __( 'Modal Footer Text', 'pmpro-lpv-customizer' ),
						'section'  => 'pmpro_section',
						'settings' => 'modal_footer_text',
						'type'     => 'text',
						'priority'    => 15,
					)
				)
			);
		}

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
