<?php
/**
 * Checkboxes filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Check_Range_Filter' ) ) {

	/**
	 * Define Jet_Smart_Filters_Check_Range_Filter class
	 */
	class Jet_Smart_Filters_Check_Range_Filter extends Jet_Smart_Filters_Filter_Base {

		/**
		 * Get provider name
		 *
		 * @return string
		 */
		public function get_name() {
			return __( 'Check Range', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 *
		 * @return string
		 */
		public function get_id() {
			return 'check-range';
		}

		/**
		 * Get provider wrapper selector
		 *
		 * @return string
		 */
		public function get_scripts() {
			return false;
		}

		/**
		 * Prepare filter template argumnets
		 *
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		public function prepare_args( $args ) {

			$filter_id            = $args['filter_id'];
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;

			if ( ! $filter_id ) {
				return false;
			}

			$source       = get_post_meta( $filter_id, '_data_source', true );
			$raw_options  = get_post_meta( $filter_id, '_source_manual_input_range', true );
			$prefix       = get_post_meta( $filter_id, '_values_prefix', true );
			$suffix       = get_post_meta( $filter_id, '_values_suffix', true );
			$query_type   = 'meta_query';
			$query_var    = get_post_meta( $filter_id, '_query_var', true );
			$filter_label = get_post_meta( $filter_id, '_filter_label', true );
			$options      = array();
			$format       = array();

			$format['thousands_sep'] = get_post_meta( $filter_id, '_values_thousand_sep', true );
			$format['decimal_sep']   = get_post_meta( $filter_id, '_values_decimal_sep', true );
			$format['decimal_num']   = get_post_meta( $filter_id, '_values_decimal_num', true );
			$format['decimal_num']   = absint( $format['decimal_num'] );

			if ( ! empty( $raw_options ) ) {
				foreach ( $raw_options as $option ) {

					$min = ! empty( $option['min'] ) ? $option['min'] : 0;
					$max = ! empty( $option['max'] ) ? $option['max'] : 100;
					$key = $min . '-' . $max;
					$min = trim( $min );
					$max = trim( $max );

					$min = number_format(
						$min,
						$format['decimal_num'],
						$format['decimal_sep'],
						$format['thousands_sep']
					);

					$max = number_format(
						$max,
						$format['decimal_num'],
						$format['decimal_sep'],
						$format['thousands_sep']
					);

					$value = $prefix . $min . $suffix . ' — ' . $prefix . $max . $suffix;

					$options[ $key ] = $value;
				}
			}

			$options = apply_filters( 'jet-smart-filters/filters/filter-options', $options, $filter_id, $this );

			return array(
				'options'              => $options,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'prefix'               => $prefix,
				'suffix'               => $suffix,
				'format'               => $format,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'filter_label'         => $filter_label,
			);

		}

	}

}
