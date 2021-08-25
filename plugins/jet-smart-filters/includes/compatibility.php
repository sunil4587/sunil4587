<?php
/**
 * Compatibility filters and actions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Compatibility' ) ) {

	/**
	 * Define Jet_Engine_Compatibility class
	 */
	class Jet_Smart_Filters_Compatibility {

		/**
		 * Constructor for the class
		 */
		function __construct() {
			// WPML compatibility
			if ( defined( 'WPML_ST_VERSION' ) ) {
				add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'add_action_to_multi_currency_ajax' ), 10, 1 );
				add_filter( 'jet-smart-filters/render_filter_template/filter_id', array( $this, 'modify_filter_id' ) );
				add_filter( 'jet-smart-filters/filters/posts-source/args', array( $this, 'modify_posts_source_args' ) );

				// For Indexer
				add_filter( 'jet-smart-filters/indexer/tax-query-args', array( $this, 'remove_wpml_terms_filters' ) );
			}

			add_filter( 'jet-smart-filters/filters/localized-data',  array( $this, 'datepicker_texts' ) );
		}

		public function add_action_to_multi_currency_ajax( $ajax_actions = array() ) {
			$ajax_actions[] = 'jet_smart_filters';
			return $ajax_actions;
		}

		public function modify_filter_id( $filter_id ) {
			return apply_filters( 'wpml_object_id', $filter_id, jet_smart_filters()->post_type->post_type, true );
		}

		public function modify_posts_source_args( $args ) {

			if ( isset( $args['post_type'] ) ) {
				$is_translated_post_type = apply_filters( 'wpml_is_translated_post_type', null, $args['post_type'] );

				if ( $is_translated_post_type ) {
					$args['suppress_filters'] = false;
				}
			}

			return $args;
		}

		public function remove_wpml_terms_filters( $args ) {

			global $sitepress;

			remove_filter( 'get_term',       array( $sitepress, 'get_term_adjust_id' ), 1 );
			remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10 );
			remove_filter( 'terms_clauses',  array( $sitepress, 'terms_clauses' ), 10 );

			$args['suppress_filters'] = true;

			return $args;
		}

		public function datepicker_texts( $args ) {

			$args['datePickerData'] = array(
				'closeText'       => esc_html__( 'Done', 'jet-smart-filters' ),
				'prevText'        => esc_html__( 'Prev', 'jet-smart-filters' ),
				'nextText'        => esc_html__( 'Next', 'jet-smart-filters' ),
				'currentText'     => esc_html__( 'Today', 'jet-smart-filters' ),
				'monthNames'      => array(
					esc_html__( 'January', 'jet-smart-filters' ),
					esc_html__( 'February', 'jet-smart-filters' ),
					esc_html__( 'March', 'jet-smart-filters' ),
					esc_html__( 'April', 'jet-smart-filters' ),
					esc_html__( 'May', 'jet-smart-filters' ),
					esc_html__( 'June', 'jet-smart-filters' ),
					esc_html__( 'July', 'jet-smart-filters' ),
					esc_html__( 'August', 'jet-smart-filters' ),
					esc_html__( 'September', 'jet-smart-filters' ),
					esc_html__( 'October', 'jet-smart-filters' ),
					esc_html__( 'November', 'jet-smart-filters' ),
					esc_html__( 'December', 'jet-smart-filters' ),
				),
				'monthNamesShort' => array(
					esc_html__( 'Jan', 'jet-smart-filters' ),
					esc_html__( 'Feb', 'jet-smart-filters' ),
					esc_html__( 'Mar', 'jet-smart-filters' ),
					esc_html__( 'Apr', 'jet-smart-filters' ),
					esc_html__( 'May', 'jet-smart-filters' ),
					esc_html__( 'Jun', 'jet-smart-filters' ),
					esc_html__( 'Jul', 'jet-smart-filters' ),
					esc_html__( 'Aug', 'jet-smart-filters' ),
					esc_html__( 'Sep', 'jet-smart-filters' ),
					esc_html__( 'Oct', 'jet-smart-filters' ),
					esc_html__( 'Nov', 'jet-smart-filters' ),
					esc_html__( 'Dec', 'jet-smart-filters' ),
				),
				'dayNames'        => array(
					esc_html__( 'Sunday', 'jet-smart-filters' ),
					esc_html__( 'Monday', 'jet-smart-filters' ),
					esc_html__( 'Tuesday', 'jet-smart-filters' ),
					esc_html__( 'Wednesday', 'jet-smart-filters' ),
					esc_html__( 'Thursday', 'jet-smart-filters' ),
					esc_html__( 'Friday', 'jet-smart-filters' ),
					esc_html__( 'Saturday', 'jet-smart-filters' )
				),
				'dayNamesShort'   => array(
					esc_html__( 'Sun', 'jet-smart-filters' ),
					esc_html__( 'Mon', 'jet-smart-filters' ),
					esc_html__( 'Tue', 'jet-smart-filters' ),
					esc_html__( 'Wed', 'jet-smart-filters' ),
					esc_html__( 'Thu', 'jet-smart-filters' ),
					esc_html__( 'Fri', 'jet-smart-filters' ),
					esc_html__( 'Sat', 'jet-smart-filters' )
				),
				'dayNamesMin'     => array(
					esc_html__( 'Su', 'jet-smart-filters' ),
					esc_html__( 'Mo', 'jet-smart-filters' ),
					esc_html__( 'Tu', 'jet-smart-filters' ),
					esc_html__( 'We', 'jet-smart-filters' ),
					esc_html__( 'Th', 'jet-smart-filters' ),
					esc_html__( 'Fr', 'jet-smart-filters' ),
					esc_html__( 'Sa', 'jet-smart-filters' ),
				),
				'weekHeader'      => esc_html__( 'Wk', 'jet-smart-filters' ),
			);

			return $args;
		}
	}
}
