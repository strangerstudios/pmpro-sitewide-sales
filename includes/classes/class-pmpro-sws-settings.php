<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Settings {
	/**
	 * Initial plugin setup
	 *
	 * @package pmpro-sitewide-sale/includes
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'pmpro_preheader') );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
		add_shortcode( 'pmpro_sws', array( __CLASS__, 'shortcode' ) );
		add_filter( 'display_post_states', array( __CLASS__, 'add_display_post_states' ), 10, 2 );
		add_filter( 'page_row_actions', array( __CLASS__, 'add_page_row_actions' ), 10, 2 );
	}

	/**
	 * Init settings page
	 */
	public static function admin_init() {
		register_setting( 'pmpro-sws-group', 'pmpro_sitewide_sale', array( __CLASS__, 'validate' ) );
	}

	/**
	 * Init preheaders for Checkout template
	 */
	public static function pmpro_preheader() {
		// Load the checkout preheader on the landing page.
		if ( ! defined( 'PMPRO_DIR' ) ) {
			return;
		}

		if ( ! is_admin() ) {
			// JASON: this needs to be updated to default to first level in filtered levels array that matches discount code for sale.
			if ( empty( $_REQUEST['level'] ) ) {
				$_REQUEST['level'] = 1;
			}
			require_once(PMPRO_DIR . '/preheaders/checkout.php');
		}
	}

	/**
	 * Get the Sitewide Sale Options
	 *
	 * @return array [description]
	 */
	public static function pmprosws_get_options() {
		$options = get_option( 'pmpro_sitewide_sale' );

		// Set the defaults.
		if ( empty( $options ) || ! array_key_exists( 'active_sitewide_sale_id', $options ) ) {
			$options = self::pmprosws_reset_options();
		}
		return $options;
	}

	/**
	 * Sets SWS settings to default
	 */
	public static function pmprosws_reset_options() {
		$options = get_option( 'pmpro_sitewide_sale' );

		// Set the defaults.
		if ( empty( $options ) ) {
			$options = array(
				'active_sitewide_sale_id' => false,
			);
		}
		return $options;
	}

	/**
	 * [pmprosws_save_options description]
	 *
	 * @param array $options contains information about sale to be saved.
	 */
	public static function pmprosws_save_options( $options ) {
		update_option( 'pmpro_sitewide_sale', $options, 'no' );
	}

	/**
	 * Validates sitewide sale options
	 *
	 * @param  array $input info to be validated.
	 */
	public static function validate( $input ) {
		$options = self::pmprosws_get_options();
		if ( ! empty( $input['active_sitewide_sale_id'] ) && '-1' !== $input['active_sitewide_sale_id'] ) {
			$options['active_sitewide_sale_id'] = trim( $input['active_sitewide_sale_id'] );
		} else {
			$options['active_sitewide_sale_id'] = false;
		}
		return $options;
	}

	/**
	 * Displays pre-sale content, sale content, or post-sale content
	 * depending on page and date
	 *
	 * Attribute sitewide_sale_id sets Sitewide Sale to get meta from.
	 * Attribute sale_content sets time period to display.
	 *
	 * @param array $atts attributes passed with shortcode.
	 */
	public static function shortcode( $atts ) {
		$sitewide_sale = null;

		if ( is_array( $atts ) && array_key_exists( 'sitewide_sale_id', $atts ) ) {
			$sitewide_sale = get_post( $atts['sitewide_sale_id'] );
			if ( empty( $sitewide_sale ) && 'sws_sitewide_sale' !== $sitewide_sale->post_type ) {
				return '';
			}
		} else {
			$post_id = get_the_ID();
			$sitewide_sale = get_posts(
				array(
					'post_type'      => 'sws_sitewide_sale',
					'meta_key'       => 'landing_page_post_id',
					'meta_value'     => '' . $post_id,
					'posts_per_page' => 1,
				)
			);

			if ( 1 > count( $sitewide_sale ) ) {
				return '';
			}
			$sitewide_sale = $sitewide_sale[0];
		}

		$sale_content = 'sale';
		$possible_sale_contents = [ 'pre-sale', 'sale', 'post-sale' ];

		$sale_start_date = get_post_meta( $sitewide_sale->ID, 'start_date', true );
		$sale_end_date = get_post_meta( $sitewide_sale->ID, 'end_date', true );

		if ( current_user_can( 'administrator' ) && isset( $_REQUEST['pmpro_sws_preview_content'] ) && in_array( $_REQUEST['pmpro_sws_preview_content'], $possible_sale_contents, true ) ) {
			$sale_content = $_REQUEST['pmpro_sws_preview_content'];
		} elseif ( is_array( $atts ) && array_key_exists( 'sale_content', $atts ) ) {
			if ( in_array( $atts['sale_content'], $possible_sale_contents, true ) ) {
				$sale_content = $atts['sale_content'];
			} else {
				return '';
			}
		} elseif ( date( 'Y-m-d', current_time( 'timestamp') ) < $sale_start_date ) {
			$sale_content = 'pre-sale';
		} elseif ( date( 'Y-m-d', current_time( 'timestamp') ) > $sale_end_date ) {
			$sale_content = 'post-sale';
		}

  		if ( $sale_content === 'pre-sale') {
			$landing_content = get_post_meta( $sitewide_sale->ID, 'pre_sale_content', true );
			$r = '<div class="pmpro_sws_landing_content pmpro_sws_landing_content_pre-sale">';
			$r .= $landing_content;
			$formatted_start_date = date( get_option( 'date_format' ), strtotime( $sale_start_date, current_time( 'timestamp' ) ) );
			$r .= '<p class="pmpro_sws_date pmpro_sws_date_start">' . $formatted_start_date . '</p>';
			$r .= '</div> <!-- .pmpro_sws_landing_content -->';
		} elseif ( $sale_content === 'post-sale' ) {
			$landing_content = get_post_meta( $sitewide_sale->ID, 'post_sale_content', true );
			$r = '<div class="pmpro_sws_landing_content pmpro_sws_landing_content_post-sale">';
			$r .= $landing_content;
			$formatted_end_date = date( get_option( 'date_format' ), strtotime( $sale_end_date, current_time( 'timestamp' ) ) );
			$r .= '<p class="pmpro_sws_date pmpro_sws_date_end">' . $formatted_end_date . '</p>';
			$r .= '</div> <!-- .pmpro_sws_landing_content -->';
		} else {
			$landing_content = apply_filters( 'the_content', get_post_meta( $sitewide_sale->ID, 'sale_content', true ) );
			$template = pmpro_loadTemplate('checkout', 'local', 'pages');
			$r = '<div class="pmpro_sws_landing_content pmpro_sws_landing_content_sale">';
			$r .= $landing_content;
			$r .= '</div> <!-- .pmpro_sws_landing_content -->';
			$r .= $template;
		}

		return $r;
	}

	/**
	 * Add a post display state for special Landing Pages in the page list table.
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post The current post object.
	 */

	public static function add_display_post_states( $post_states, $post ) {
		$sitewide_sale = get_posts(
			array(
				'post_type'      => 'sws_sitewide_sale',
				'meta_key'       => 'landing_page_post_id',
				'meta_value'     => '' . $post->ID,
				'posts_per_page' => 1,
			)
		);

		if( ! empty ( $sitewide_sale[0] ) ) {
			$post_states['pmpro_sws_landing_page'] = __( 'Sitewide Sale Landing Page', 'pmpro-sitewide-sale' );
		}

		return $post_states;
	}

	/**
	 * Add page row action to edit the associated Sitewide Sale for special Landing Pages in the page list table.
	 *
	 * @param array   $actions An array of page row actions.
	 * @param WP_Post $post The current post object.
	 */

	public static function add_page_row_actions( $actions, $post ) {
		$sitewide_sale = get_posts(
			array(
				'post_type'      => 'sws_sitewide_sale',
				'meta_key'       => 'landing_page_post_id',
				'meta_value'     => '' . $post->ID,
				'posts_per_page' => 1,
			)
		);

		if( ! empty ( $sitewide_sale[0] ) ) {
			$actions['pmpro_sws_edit_sale'] = sprintf(
				'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
				get_edit_post_link( $sitewide_sale[0]->ID ),
				esc_attr( 'Edit Sitewide Sale', 'pmpro-sitewide-sale' ),
				esc_attr( 'Edit Sitewide Sale', 'pmpro-sitewide-sale' )
			);
		}

		return $actions;
	}

}
