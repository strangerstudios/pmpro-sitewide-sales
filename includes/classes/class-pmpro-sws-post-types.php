<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Post_Types {

	/**
	 * [init description]
	 *
	 * @return [type] [description]
	 */
	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_cpt_to_admin_bar' ), 1001 );
		add_action( 'admin_menu', array( __CLASS__, 'add_cpt_to_menu' ) );
		add_action( 'init', array( __CLASS__, 'register_sitewide_sale_cpt' ) );
		add_filter( 'manage_sws_sitewide_sale_posts_columns', array( __CLASS__, 'set_sitewide_sale_columns' ) );
		add_action( 'manage_sws_sitewide_sale_posts_custom_column', array( __CLASS__, 'fill_sitewide_sale_columns' ), 10, 2 );
		add_filter( 'months_dropdown_results', '__return_empty_array' );
		add_filter( 'post_row_actions', array( __CLASS__, 'remove_sitewide_sale_row_actions' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_pmpro_sws_set_active_sitewide_sale', array( __CLASS__, 'set_active_sitewide_sale' ) );
	}

	/**
	 * [renaming_cpt_menu_function description]
	 *
	 * @return [type] [description]
	 */
	public static function renaming_cpt_menu_function() {
		$renaming_menu = apply_filters( 'renaming_cpt_menu_filter', 'PMPro CPTs' );
		return $renaming_menu;
	}

	/**
	 * [register_sitewide_sale_cpt description]
	 *
	 * @return [type] [description]
	 */
	public static function register_sitewide_sale_cpt() {
		$menu_name						= self::renaming_cpt_menu_function();

		// Set the custom post type labels.
		$labels['name']					= _x( 'Sitewide Sales', 'Post Type General Name', 'pmpro-sitewide-sale' );
		$labels['singular_name']		= _x( 'Sitewide Sale', 'Post Type Singular Name', 'pmpro-sitewide-sale' );
		$labels['all_items']			= __( 'All Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['menu_name']			= __( $menu_name, 'pmpro-sitewide-sale' );
		$labels['name_admin_bar']		= __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['all_items']			= __( 'All Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['add_new_item']			= __( 'Add New Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['add_new']				= __( 'Add New', 'pmpro-sitewide-sale' );
		$labels['new_item']				= __( 'New Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['edit_item']			= __( 'Edit Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['update_item']			= __( 'Update Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['view_item']			= __( 'View Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['search_items']			= __( 'Search Sitewide Sales', 'pmpro-sitewide-sale' );
		$labels['not_found']			= __( 'Not found', 'pmpro-sitewide-sale' );
		$labels['not_found_in_trash']	= __( 'Not found in Trash', 'pmpro-sitewide-sale' );
		$labels['insert_into_item']		= __( 'Insert into Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['uploaded_to_this_item']= __( 'Uploaded to this Sitewide Sale', 'pmpro-sitewide-sale' );
		$labels['items_list']			= __( 'Sitewide Sales list', 'pmpro-sitewide-sale' );
		$labels['items_list_navigation']= __( 'Sitewide Sales list navigation', 'pmpro-sitewide-sale' );
		$labels['filter_items_list']	= __( 'Filter sitewide sales list', 'pmpro-sitewide-sale' );

		// Build the post type args.
		$args['labels']				= $labels;
		$args['description']        = __( 'Sitewide Sales', 'pmpro-sitewide-sale' );
		$args['public']        		= false;
		$args['publicly_queryable']	= false;
		$args['show_ui']			= true;
		$args['show_in_menu']		= false;
		$args['show_in_nav_menus']	= false;
		$args['can_export']			= true;
		$args['has_archive']		= false;
		$args['rewrite']			= false;
		$args['exclude_from_search']= true;
		$args['query_var']			= false;
		$args['capability_type']	= 'page';
		$args['show_in_rest']		= false;
		$args['rest_base']			= 'sws_sitewide_sale';
		$args['supports']           = array(
			'title',
		);
		/*
		$args['rewrite']             = array(
			'with_front' => true,
			'slug' => 'sws-sitewide-sale',
		);
		*/
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
	 * [enqueue_scripts description]
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
		$columns['discount_code'] = __( 'Discount Code', 'pmpro_sitewide_sale' );
		$columns['landing_page'] = __( 'Landing Page', 'pmpro_sitewide_sale' );
		$columns['reports']  = __( 'Reports', 'pmpro_sitewide_sale' );
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
			case 'discount_code':
				$discount_code = get_post_meta( $post_id, 'pmpro_sws_discount_code_id', true );
				if ( false !== $discount_code ) {
					global $wpdb;
					$code_name = $wpdb->get_results( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id=%s", $discount_code ) );
					if ( 0 < count( $code_name ) && ! empty( $code_name[0]->code ) ) {
						echo esc_html( $code_name[0]->code );
					}
				}
				break;
			case 'landing_page':
				$landing_page = get_post_meta( $post_id, 'pmpro_sws_landing_page_post_id', true );
				if ( false !== $landing_page ) {
					$title = get_the_title( $landing_page );
					if ( ! empty( $title ) ) {
						echo '<a href="' . esc_html( get_permalink( $landing_page ) ) . '">' . esc_html( $title ) . '</a>';
					}
				}
				break;
			case 'reports':
					echo '<a class="button button-primary" href="' . admin_url( 'admin.php?page=pmpro-reports&report=pmpro_sws_reports' ) . '">' . __( 'View Reports', 'pmpro-sitewide-sale' ) . '</a>';
					break;
			case 'set_active':
				$options = PMPro_SWS_Settings::pmprosws_get_options();
				if ( array_key_exists( 'active_sitewide_sale_id', $options ) && $post_id . '' === $options['active_sitewide_sale_id'] ) {
					echo '<button class="button button-primary pmpro_sws_column_set_active" id="pmpro_sws_column_set_active_' . $post_id . '">' . __( 'Remove Active', 'pmpro-sitewide-sale' ) . '</button>';
				} else {
					echo '<button class="button button-secondary pmpro_sws_column_set_active" id="pmpro_sws_column_set_active_' . $post_id . '">' . __( 'Set Active', 'pmpro-sitewide-sale' ) . '</button>';
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
	 * [remove_sitewide_sale_row_actions description]
	 *
	 * @param  [type] $actions  [description]
	 * @param  [type] $post [description]
	 * @return [type]          [description]
	 */
	public static function remove_sitewide_sale_row_actions( $actions, $post ) {
		// Removes the "Quick Edit" action.
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}

}
