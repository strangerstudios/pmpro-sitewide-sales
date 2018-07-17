<?php
// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Custom_Post_Types extends Post_Type_Factory {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_sitewide_sale_cpt' ) );
	}

	public static function register_sitewide_sale_cpt() {
		$labels = Post_Type_Factory::get_label_defaults();
		$labels['name']                  = _x( 'Sitewide Sales', 'Post Type General Name', 'pmpro-sitewide-sale' );
		$labels['singular_name']         = _x( 'Sitewide Sale', 'Post Type Singular Name', 'pmpro-sitewide-sale' );
		$labels['all_items']             = __( 'All Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['menu_name']             = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['name_admin_bar']        = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['add_new_item']        = __( 'Add New Sitewide Sale', 'pmpro-sitewide-sale' );

		$args = Post_Type_Factory::get_args_defaults();
		$args['label']               = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$args['description']         = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
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
			'slug' => 'sws-sitewide-sale',
		);
		$args['rest_base']           = __( 'sws_sitewide_sale', 'pmpro-sitewide-sale' );

		register_post_type( 'sws_sitewide_sale', $args );
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
}
