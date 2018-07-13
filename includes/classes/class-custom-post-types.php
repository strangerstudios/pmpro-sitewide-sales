<?php
// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Custom_Post_Types extends Post_Type_Factory {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_sitewide_sale_banners' ) );
		add_action( 'init', array( __CLASS__, 'register_sitewide_sale_landing_pages' ) );
	}

	public static function register_sitewide_sale_banners() {
		$labels = Post_Type_Factory::get_label_defaults();
		$labels['name']                  = _x( 'SWS Banners', 'Post Type General Name', 'pmpro-sitewide-sale' );
		$labels['singular_name']         = _x( 'SWS Banner', 'Post Type Singular Name', 'pmpro-sitewide-sale' );
		$labels['all_items']             = __( 'All SWS Banners', 'pmpro-sitewide-sale' );
		$labels['menu_name']             = __( 'SWS Banners', 'pmpro-sitewide-sale' );
		$labels['name_admin_bar']        = __( 'SWS Banners', 'pmpro-sitewide-sale' );
		$labels['add_new_item']        = __( 'Add New SWS Banner', 'pmpro-sitewide-sale' );

		$args = Post_Type_Factory::get_args_defaults();
		$args['label']               = __( 'SWS Banners', 'pmpro-sitewide-sale' );
		$args['description']         = __( 'SWS Banners', 'pmpro-sitewide-sale' );
		$args['labels']              = $labels;
		$args['menu_icon']           = 'dashicons-id';
		$args['has_archive']         = true;
		$args['taxonomies']          = array( 'sidecat' );
		$args['supports']            = array(
			'title',
			'editor',
		);

		$args['rewrite']             = array(
			'with_front' => true,
			'slug' => 'sws-banner',
		);
		$args['rest_base']           = __( 'sws_banner', 'pmpro-sitewide-sale' );

		register_post_type( 'sws_banner', $args );
	}


	public static function register_sidecat_taxonomy() {
		$tax_labels = Post_Type_Factory::get_tax_label_defaults();
		$tax_labels['name']                  = _x( 'SideCats', 'Taxonomy General Name', 'pmpro-sitewide-sale' );
		$tax_labels['singular_name']         = _x( 'SideCat', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' );
		$tax_labels['menu_name']         = _x( 'SideCat', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' );

		$tax_args = Post_Type_Factory::get_tax_args_defaults();
		$tax_args['label']               = __( 'SideCat', 'pmpro-sitewide-sale' );
		$tax_args['labels']              = $tax_labels;
		$tax_args['hierarchical']         = __( true, 'pmpro-sitewide-sale' );

		register_taxonomy( 'sidecat', array( 'sitewide_sale_banner' ), $tax_args );
	}

	public static function register_sitewide_sale_landing_pages() {
		$labels = Post_Type_Factory::get_label_defaults();
		$labels['name']                  = _x( 'SWS Landing Pages', 'Post Type General Name', 'pmpro-sitewide-sale' );
		$labels['singular_name']         = _x( 'SWS Landing Page', 'Post Type Singular Name', 'pmpro-sitewide-sale' );
		$labels['all_items']             = __( 'All SWS Landing Pages', 'pmpro-sitewide-sale' );
		$labels['menu_name']             = __( 'SWS Landing Pages', 'pmpro-sitewide-sale' );
		$labels['name_admin_bar']        = __( 'SWS Landing Pages', 'pmpro-sitewide-sale' );
		$labels['add_new_item']        = __( 'Add New SWS Landing Page', 'pmpro-sitewide-sale' );

		$args = Post_Type_Factory::get_args_defaults();
		$args['label']               = __( 'SWS Landing Pages', 'pmpro-sitewide-sale' );
		$args['description']         = __( 'SWS Landing Pages', 'pmpro-sitewide-sale' );
		$args['labels']              = $labels;
		$args['menu_icon']           = 'dashicons-id';
		$args['has_archive']         = true;
		$args['taxonomies']          = array( 'sidecat' );
		$args['supports']            = array(
			'title',
			'editor',
		);
		$args['rewrite']             = array(
			'with_front' => true,
			'slug' => 'sws-landing-page',
		);
		$args['rest_base']           = __( 'sws_landing_page', 'pmpro-sitewide-sale' );

		register_post_type( 'sws_landing_page', $args );
	}
}
