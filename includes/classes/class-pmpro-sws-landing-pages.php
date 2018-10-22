<?php

namespace PMPro_Sitewide_Sale\includes\classes;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

class PMPro_SWS_Landing_Pages {
    /**
	 * Initial plugin setup
	 *
	 * @package pmpro-sitewide-sale/includes
	 */
	public static function init() {
		add_action( 'wp', array( __CLASS__, 'pmpro_preheader'), 1 );
		add_shortcode( 'pmpro_sws', array( __CLASS__, 'shortcode' ) );
		add_filter( 'display_post_states', array( __CLASS__, 'add_display_post_states' ), 10, 2 );
		add_filter( 'page_row_actions', array( __CLASS__, 'add_page_row_actions' ), 10, 2 );
	}

    /**
     * Get the default level to use on a landing page
     * @param $post_id Post ID of the landing page
     */
     public static function get_default_level( $post_id ) {
         global $post, $wpdb;

         // guess
         $all_levels = pmpro_getAllLevels(true, true);
         if ( ! empty( $all_levels ) ) {
             $keys = array_keys( $all_levels );
             $level_id = $keys[0];
         } else {
             return false;
         }

         // default post_id
         if ( empty ( $post_id ) ) {
             $post_id = $post->ID;
         }

         // must have a post_id
         if ( empty ( $post_id ) ) {
             return $level_id;
         }

         // get sale for this $post_id
         $sitewide_sale_id = get_post_meta( $post_id, 'pmpro_sws_sitewide_sale_id', true );

         if ( ! empty( $sitewide_sale_id ) ) {
             // get the discount code for this sale
             $discount_code_id = get_post_meta( $sitewide_sale_id, 'pmpro_sws_discount_code_id', true );

             // get first level that uses this code
             if ( ! empty ( $discount_code_id ) ) {
                 $level_id = $wpdb->get_var( "SELECT level_id FROM $wpdb->pmpro_discount_codes_levels WHERE code_id = '" . esc_sql( $discount_code_id )  . "' ORDER BY level_id LIMIT 1" );
             }
         }

         return $level_id;
     }

    /**
	 * Load the checkout preheader on the landing page.
	 */
	public static function pmpro_preheader() {
        // Make sure PMPro is loaded.
		if ( ! defined( 'PMPRO_DIR' ) ) {
			return;
		}

        // Don't do this in the dashboard.
        if ( is_admin() ) {
            return;
        }

        // Check if this is the landing page
        $queried_object = get_queried_object();
        if ( empty ( $queried_object ) ) {
            return;
        }
        if ( ! has_shortcode( $queried_object->post_content, 'pmpro_sws' ) ) {
            return;
        }

		// Choose a default level if none specified.
		if ( empty( $_REQUEST['level'] ) ) {
            $_REQUEST['level'] = PMPro_SWS_Landing_Pages::get_default_level( $queried_object->ID );
		}

        require_once(PMPRO_DIR . '/preheaders/checkout.php');
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
					'meta_key'       => 'pmpro_sws_landing_page_post_id',
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

		$sale_start_date = get_post_meta( $sitewide_sale->ID, 'pmpro_sws_start_date', true );
		$sale_end_date = get_post_meta( $sitewide_sale->ID, 'pmpro_sws_end_date', true );

		if ( current_user_can( 'administrator' ) && isset( $_REQUEST['pmpro_sws_preview_content'] ) && in_array( $_REQUEST['pmpro_sws_preview_content'], $possible_sale_contents ) ) {
			$sale_content = $_REQUEST['pmpro_sws_preview_content'];
		} elseif ( is_array( $atts ) && array_key_exists( 'sale_content', $atts ) ) {
			if ( in_array( $atts['sale_content'], $possible_sale_contents ) ) {
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
			$landing_content = get_post_meta( $sitewide_sale->ID, 'pmpro_sws_pre_sale_content', true );
			$r = '<div class="pmpro_sws_landing_content pmpro_sws_landing_content_pre-sale">';
			$r .= $landing_content;
			$formatted_start_date = date( get_option( 'date_format' ), strtotime( $sale_start_date, current_time( 'timestamp' ) ) );
			$r .= '<p class="pmpro_sws_date pmpro_sws_date_start">' . $formatted_start_date . '</p>';
			$r .= '</div> <!-- .pmpro_sws_landing_content -->';
		} elseif ( $sale_content === 'post-sale' ) {
			$landing_content = get_post_meta( $sitewide_sale->ID, 'pmpro_sws_post_sale_content', true );
			$r = '<div class="pmpro_sws_landing_content pmpro_sws_landing_content_post-sale">';
			$r .= $landing_content;
			$formatted_end_date = date( get_option( 'date_format' ), strtotime( $sale_end_date, current_time( 'timestamp' ) ) );
			$r .= '<p class="pmpro_sws_date pmpro_sws_date_end">' . $formatted_end_date . '</p>';
			$r .= '</div> <!-- .pmpro_sws_landing_content -->';
		} else {
			$landing_content = apply_filters( 'the_content', get_post_meta( $sitewide_sale->ID, 'pmpro_sws_sale_content', true ) );
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
				'meta_key'       => 'pmpro_sws_landing_page_post_id',
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
				'meta_key'       => 'pmpro_sws_landing_page_post_id',
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
