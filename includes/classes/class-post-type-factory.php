<?php
// namespace PMPro_Sitewide_Sale\includes\classes;
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class Post_Type_Factory {

	public static function init() {
		// add_action( 'admin_enqueue_scripts', array( __CLASS__, 'wp_timer_scripts' ) );
	}

	public static function get_label_defaults() {
		return array(
			'name'                  => _x( 'Pages', 'Post Type General Name', 'pmpro-sitewide-sale' ),
			'singular_name'         => _x( 'Page', 'Post Type Singular Name', 'pmpro-sitewide-sale' ),
			'menu_name'             => __( 'Pages', 'pmpro-sitewide-sale' ),
			'name_admin_bar'        => __( 'Page', 'pmpro-sitewide-sale' ),
			'archives'              => __( 'Page Archives', 'pmpro-sitewide-sale' ),
			'parent_item_colon'     => __( 'Parent Page:', 'pmpro-sitewide-sale' ),
			'all_items'             => __( 'All Pages', 'pmpro-sitewide-sale' ),
			'add_new_item'          => __( 'Add New Page', 'pmpro-sitewide-sale' ),
			'add_new'               => __( 'Add New', 'pmpro-sitewide-sale' ),
			'new_item'              => __( 'New Page', 'pmpro-sitewide-sale' ),
			'edit_item'             => __( 'Edit Page', 'pmpro-sitewide-sale' ),
			'update_item'           => __( 'Update Page', 'pmpro-sitewide-sale' ),
			'view_item'             => __( 'View Page', 'pmpro-sitewide-sale' ),
			'search_items'          => __( 'Search Page', 'pmpro-sitewide-sale' ),
			'not_found'             => __( 'Not found', 'pmpro-sitewide-sale' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'pmpro-sitewide-sale' ),
			'featured_image'        => __( 'Featured Image', 'pmpro-sitewide-sale' ),
			'set_featured_image'    => __( 'Set featured image', 'pmpro-sitewide-sale' ),
			'remove_featured_image' => __( 'Remove featured image', 'pmpro-sitewide-sale' ),
			'use_featured_image'    => __( 'Use as featured image', 'pmpro-sitewide-sale' ),
			'insert_into_item'      => __( 'Insert into page', 'pmpro-sitewide-sale' ),
			'uploaded_to_this_item' => __( 'Uploaded to this page', 'pmpro-sitewide-sale' ),
			'items_list'            => __( 'Pages list', 'pmpro-sitewide-sale' ),
			'items_list_navigation' => __( 'Pages list navigation', 'pmpro-sitewide-sale' ),
			'filter_items_list'     => __( 'Filter pages list', 'pmpro-sitewide-sale' ),
		);
	}

	public static function get_args_defaults() {
		return array(
			'label'                 => __( 'Page', 'pmpro-sitewide-sale' ),
			'description'           => __( 'Page Description', 'pmpro-sitewide-sale' ),
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'            => array( 'category' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-admin-page',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			// 'has_archive'           => true,
			'has_archive'           => false,
			'rewrite'               => array(
				'with_front' => false,
				'slug' => 'page',
			),
			'exclude_from_search'   => false,
			'query_var'             => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
			'rest_base'             => 'pages',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
	}

	public static function get_tax_label_defaults() {
		return array(
			'name'                       => _x( 'Taxonomies', 'Taxonomy General Name', 'pmpro-sitewide-sale' ),
			'singular_name'              => _x( 'Taxonomy', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' ),
			'menu_name'                  => __( 'Taxonomy', 'pmpro-sitewide-sale' ),
			'all_items'                  => __( 'All Items', 'pmpro-sitewide-sale' ),
			'parent_item'                => __( 'Parent Item', 'pmpro-sitewide-sale' ),
			'parent_item_colon'          => __( 'Parent Item:', 'pmpro-sitewide-sale' ),
			'new_item_name'              => __( 'New Item Name', 'pmpro-sitewide-sale' ),
			'add_new_item'               => __( 'Add New Item', 'pmpro-sitewide-sale' ),
			'edit_item'                  => __( 'Edit Item', 'pmpro-sitewide-sale' ),
			'update_item'                => __( 'Update Item', 'pmpro-sitewide-sale' ),
			'view_item'                  => __( 'View Item', 'pmpro-sitewide-sale' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'pmpro-sitewide-sale' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'pmpro-sitewide-sale' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'pmpro-sitewide-sale' ),
			'popular_items'              => __( 'Popular Items', 'pmpro-sitewide-sale' ),
			'search_items'               => __( 'Search Items', 'pmpro-sitewide-sale' ),
			'not_found'                  => __( 'Not Found', 'pmpro-sitewide-sale' ),
			'no_terms'                   => __( 'No items', 'pmpro-sitewide-sale' ),
			'items_list'                 => __( 'Items list', 'pmpro-sitewide-sale' ),
			'items_list_navigation'      => __( 'Items list navigation', 'pmpro-sitewide-sale' ),
		);
	}

	public static function get_tax_args_defaults() {
		return array(
			'labels'                     => __( 'Taxonomies', 'pmpro-sitewide-sale' ),
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
	}

	public static function unset_element_from_array( $element, $array ) {
		$comments_key = array_search( $element, $array );
		if ( false !== $comments_key ) {
			unset( $array[ $comments_key ] );
		}
		return $array;
	}

	public static function categories_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
		add_post_type_support( 'page', 'excerpt' );
	}
}
