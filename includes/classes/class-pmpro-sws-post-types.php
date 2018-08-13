<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Post_Types {

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_cpt_to_admin_bar' ), 1001 );
		add_action( 'admin_menu', array( __CLASS__, 'add_cpt_to_menu' ) );
		add_action( 'init', array( __CLASS__, 'register_sitewide_sale_cpt' ) );
		add_filter( 'manage_sws_sitewide_sale_posts_columns', array( __CLASS__, 'set_sitewide_sale_columns' ) );
		add_action( 'manage_sws_sitewide_sale_posts_custom_column', array( __CLASS__, 'fill_sitewide_sale_columns' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_pmpro_sws_set_active_sitewide_sale', array( __CLASS__, 'set_active_sitewide_sale' ) );
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function renaming_cpt_menu_function() {
		$renaming_menu = apply_filters( 'renaming_cpt_menu_filter', 'PMPro CPTs' );
		return $renaming_menu;
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function register_sitewide_sale_cpt() {
		$labels = self::get_label_defaults();
		$menu_name = self::renaming_cpt_menu_function();
		$labels['name']     = _x( 'Sitewide Sales', 'Post Type General Name', 'pmpro-sitewide-sale' );
		$labels['singular_name']         = _x( 'Sitewide Sale', 'Post Type Singular Name', 'pmpro-sitewide-sale' );
		$labels['all_items']             = __( 'All Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['menu_name']             = __( $menu_name, 'pmpro-sitewide-sale' );
		$labels['name_admin_bar']        = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['add_new_item']          = __( 'Add New Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['search_items']          = __( 'Search Sitewide Sales', 'pmpro-sitewide-sale' );

		$args = self::get_args_defaults();
		$args['label']  = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$args['description']         = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$args['labels'] = $labels;
		$args['public']  = false;
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
		$args['rest_base']           = 'sws_sitewide_sale';
		$args['show_in_menu']        = false;

		register_post_type( 'sws_sitewide_sale', $args );
	}

	/**
	 * Adds Sitewide Sale to admin bar
	 */
	public static function add_cpt_to_admin_bar() {
		global $wp_admin_bar;

		//view menu at all?
		if ( ! current_user_can( 'pmpro_memberships_menu' ) || ! is_admin_bar_showing() ) {
			return;
		}

		//array of all caps in the menu
		$pmpro_caps = pmpro_getPMProCaps();

		//the top level menu links to the first page they have access to
		foreach ( $pmpro_caps as $cap ) {
			if ( current_user_can( $cap ) ) {
				$top_menu_page = str_replace( '_', '-', $cap );
				break;
			}
		}
		if ( current_user_can( 'manage_options' ) ) {
			$wp_admin_bar->add_menu( array(
				'id'     => 'pmpro-sitewide-sale',
				'parent' => 'paid-memberships-pro',
				'title'  => __( 'Sitewide Sales', 'paid-memberships-pro' ),
				'href'   => get_admin_url( null, '/edit.php?post_type=sws_sitewide_sale' ),
			) );
		}
	}

	/**
	 * Adds Sitewide Sale to admin menu
	 */
	public static function add_cpt_to_menu() {
		add_submenu_page( 'pmpro-membershiplevels', __('Sitewide Sales', 'paid-memberships-pro' ), __('Sitewide Sales', 'paid-memberships-pro' ), 'manage_options', 'edit.php?post_type=sws_sitewide_sale' );
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function enqueue_scripts() {
		wp_register_script( 'pmpro_sws_set_active_sitewide_sale', plugins_url( 'includes/js/pmpro-sws-set-active-sitewide-sale.js', PMPROSWS_BASENAME ), array( 'jquery' ), '1.0.4' );
		wp_enqueue_script( 'pmpro_sws_set_active_sitewide_sale' );
	}

	/**
	 * set_sitewide_sale_columns Assigning labels to WP_List_Table columns will add a checkbox to the full list page's Screen Options.
	 *
	 * @param [type] $columns [description]
	 */
	public static function set_sitewide_sale_columns( $columns ) {
		$columns['is_active']  = __( 'Selected', 'pmpro_sitewide_sale' );
		$columns['discount_code'] = __( 'Discount Code', 'pmpro_sitewide_sale' );
		$columns['landing_page'] = __( 'Landing Page', 'pmpro_sitewide_sale' );
		$columns['set_active'] = __( 'Select Active Sale', 'pmpro_sitewide_sale' );

		return $columns;
	}

	/**
	 * [fill_sitewide_sale_columns description]
	 *
	 * @param  [type] $column  [description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public static function fill_sitewide_sale_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'is_active':
				$options = PMPro_SWS_Settings::pmprosws_get_options();
				if ( array_key_exists( 'active_sitewide_sale_id', $options ) && $post_id . '' === $options['active_sitewide_sale_id'] ) {
					echo '<span class="pmpro_sws_column_active" id="pmpro_sws_column_active_' . $post_id . '">Active Sitewide Sale</span>';
				} else {
					echo '<span class="pmpro_sws_column_active" id="pmpro_sws_column_active_' . $post_id . '"></span>';
				}
				break;
			case 'discount_code':
				$discount_code = get_post_meta( $post_id, 'discount_code_id', true );
				if ( false !== $discount_code ) {
					global $wpdb;
					$code_name = $wpdb->get_results( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s", $discount_code ) );
					if ( 0 < count( $code_name ) && ! empty( $code_name[0]->code ) ) {
						echo esc_html( $code_name[0]->code );
					}
				}
				break;
			case 'landing_page':
				$landing_page = get_post_meta( $post_id, 'landing_page_post_id', true );
				if ( false !== $landing_page ) {
					$title = get_the_title( $landing_page );
					if ( ! empty( $title ) ) {
						echo '<a href="' . esc_html( get_permalink( $post_id ) ) . '">' . esc_html( $title ) . '</a>';
					}
				}
				break;
			case 'set_active':
				$options = PMPro_SWS_Settings::pmprosws_get_options();
				if ( array_key_exists( 'active_sitewide_sale_id', $options ) && $post_id . '' === $options['active_sitewide_sale_id'] ) {
					echo '<button class="button button-primary pmpro_sws_column_set_active" id="pmpro_sws_column_set_active_' . $post_id . '">Remove Active</button>';
				} else {
					echo '<button class="button button-secondary pmpro_sws_column_set_active" id="pmpro_sws_column_set_active_' . $post_id . '">Set Active</button>';
				}
				break;
		}
	}

	/**
	 * [set_active_sitewide_sale description]
	 */
	public static function set_active_sitewide_sale() {
		$sitewide_sale_id = $_POST['sitewide_sale_id'];
		$options          = PMPro_SWS_Settings::pmprosws_get_options();

		if ( array_key_exists( 'active_sitewide_sale_id', $options ) && $sitewide_sale_id === $options['active_sitewide_sale_id'] ) {
			$options['active_sitewide_sale_id'] = false;
		} else {
			$options['active_sitewide_sale_id'] = $sitewide_sale_id;
		}

		PMPro_SWS_Settings::pmprosws_save_options( $options );
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function register_sidecat_taxonomy() {
		$tax_labels = self::get_tax_label_defaults();
		$tax_labels['name']     = _x( 'SideCats', 'Taxonomy General Name', 'pmpro-sitewide-sale' );
		$tax_labels['singular_name']         = _x( 'SideCat', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' );
		$tax_labels['menu_name']         = _x( 'SideCat', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' );

		$tax_args = self::get_tax_args_defaults();
		$tax_args['label']  = __( 'SideCat', 'pmpro-sitewide-sale' );
		$tax_args['labels'] = $tax_labels;
		$tax_args['hierarchical']         = __( true, 'pmpro-sitewide-sale' );

		register_taxonomy( 'sidecat', array( 'sitewide_sale_banner' ), $tax_args );
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function get_label_defaults() {
		return array(
			'name'     => _x( 'Pages', 'Post Type General Name', 'pmpro-sitewide-sale' ),
			'singular_name'         => _x( 'Page', 'Post Type Singular Name', 'pmpro-sitewide-sale' ),
			'menu_name'             => __( 'Pages', 'pmpro-sitewide-sale' ),
			'name_admin_bar'        => __( 'Page', 'pmpro-sitewide-sale' ),
			'archives' => __( 'Page Archives', 'pmpro-sitewide-sale' ),
			'parent_item_colon'     => __( 'Parent Page:', 'pmpro-sitewide-sale' ),
			'all_items'             => __( 'All Pages', 'pmpro-sitewide-sale' ),
			'add_new_item'          => __( 'Add New Page', 'pmpro-sitewide-sale' ),
			'add_new'  => __( 'Add New', 'pmpro-sitewide-sale' ),
			'new_item' => __( 'New Page', 'pmpro-sitewide-sale' ),
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

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function get_args_defaults() {
		return array(
			'label'    => __( 'Page', 'pmpro-sitewide-sale' ),
			'description'           => __( 'Page Description', 'pmpro-sitewide-sale' ),
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
			'taxonomies'            => array( 'category' ),
			'hierarchical'          => true,
			'public'   => true,
			'show_ui'  => true,
			'show_in_menu'          => true,
			'menu_position'         => 205,
			'menu_icon'             => 'dashicons-admin-page',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			// 'has_archive'           => true,
			'has_archive'           => false,
			'rewrite'  => array(
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

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function get_tax_label_defaults() {
		return array(
			'name'          => _x( 'Taxonomies', 'Taxonomy General Name', 'pmpro-sitewide-sale' ),
			'singular_name' => _x( 'Taxonomy', 'Taxonomy Singular Name', 'pmpro-sitewide-sale' ),
			'menu_name'     => __( 'Taxonomy', 'pmpro-sitewide-sale' ),
			'all_items'     => __( 'All Items', 'pmpro-sitewide-sale' ),
			'parent_item'   => __( 'Parent Item', 'pmpro-sitewide-sale' ),
			'parent_item_colon'          => __( 'Parent Item:', 'pmpro-sitewide-sale' ),
			'new_item_name' => __( 'New Item Name', 'pmpro-sitewide-sale' ),
			'add_new_item'  => __( 'Add New Item', 'pmpro-sitewide-sale' ),
			'edit_item'     => __( 'Edit Item', 'pmpro-sitewide-sale' ),
			'update_item'   => __( 'Update Item', 'pmpro-sitewide-sale' ),
			'view_item'     => __( 'View Item', 'pmpro-sitewide-sale' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'pmpro-sitewide-sale' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'pmpro-sitewide-sale' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'pmpro-sitewide-sale' ),
			'popular_items' => __( 'Popular Items', 'pmpro-sitewide-sale' ),
			'search_items'  => __( 'Search Items', 'pmpro-sitewide-sale' ),
			'not_found'     => __( 'Not Found', 'pmpro-sitewide-sale' ),
			'no_terms'      => __( 'No items', 'pmpro-sitewide-sale' ),
			'items_list'    => __( 'Items list', 'pmpro-sitewide-sale' ),
			'items_list_navigation'      => __( 'Items list navigation', 'pmpro-sitewide-sale' ),
		);
	}


	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function get_tax_args_defaults() {
		return array(
			'labels'        => __( 'Taxonomies', 'pmpro-sitewide-sale' ),
			'hierarchical'  => false,
			'public'        => true,
			'show_ui'       => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud' => true,
		);
	}

	/**
	 * [unset_element_from_array description]
	 *
	 * @param  [type] $element [description]
	 * @param  [type] $array   [description]
	 * @return [type]          [description]
	 */
	public static function unset_element_from_array( $element, $array ) {
		$comments_key = array_search( $element, $array );
		if ( false !== $comments_key ) {
				unset( $array[ $comments_key ] );
		}
		return $array;
	}

	/**
	 * [categories_to_pages description]
	 *
	 * @return [type] [description]
	 */
	public static function categories_to_pages() {
		register_taxonomy_for_object_type( 'category', 'page' );
		add_post_type_support( 'page', 'excerpt' );
	}
}
