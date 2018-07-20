<?php

// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Dev_Info {

	public static function init() {
		// add_action( 'wp_head', array( __CLASS__, 'pmpro_show_info' ) );
		if ( is_admin() ) {
			add_action( 'load-post.php', array( __CLASS__, 'init_metabox' ) );
		}
		// add_action( 'save_post', 'pmpro_sws_save_cpt', 10, 2 );
	}

	/**
	 * Meta box initialization.
	 */
	public static function init_metabox() {
		$instance = new PMPro_SWS_MetaBoxes();
		// $metabox = $instance->init_metabox();
		// $metabox = self::create_metabox_instance();
		add_action( 'add_meta_boxes', array( $instance, 'add_sws_metaboxes' ) );
		add_action( 'save_post', array( $instance, 'save_sws_metaboxes' ), 10, 2 );
		add_action( 'add_meta_boxes', array( __CLASS__, 'metaboxes_above_editor' ), 7 );
		add_action( 'edit_form_after_title', array( $instance, 'move_metaboxes_above_editor' ) );
		// add_action( 'save_post', 'pmpro_sws_save_cpt', 10, 2 );
	}
	public static function create_metabox_instance() {
		$instance = new PMPro_SWS_MetaBoxes();
		$metabox = $instance->init_metabox();
		return $metabox;
	}
	public static function metaboxes_above_editor( $post_type ) {
		add_meta_box(
			'pmpro_sws_dev_info_0',
			__( 'Step 0.1: Dev Info', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_dev_info_0' ),
			array( 'sws_sitewide_sale' ),
			'above_editor',
			'high'
		);
		add_meta_box(
			'pmpro_sws_dev_info_1',
			__( 'Step 0.2: Dev Info Settings', 'pmpro_sitewide_sale' ),
			array( __CLASS__, 'display_dev_info_1' ),
			array( 'sws_sitewide_sale' ),
			'above_editor',
			'high'
		);
	}

	public static function display_dev_info_0() {
		$sws_settings = pmprosws_get_options();
		echo '<h4>' . __CLASS__ . '</h4>';
		echo PMPROSWS_DIR . '<br>';
		$info = file_get_contents( PMPROSWS_DIR . '/dev-info.md' );
		echo wpautop( $info );
		echo '</pre>';
	}
	public static function display_dev_info_1() {
		$sws_settings = pmprosws_get_options();
		echo '<pre>';
		echo '<h4>' . __CLASS__ . '</h4>';
		print_r( $sws_settings );
		echo '</pre>';
	}
}
