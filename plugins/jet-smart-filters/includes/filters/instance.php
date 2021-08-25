<?php
/**
 * New filter instance class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Filter_Instance' ) ) {

	class Jet_Smart_Filters_Filter_Instance {

		public  $type      = null;
		private $args      = null;
		private $filter_id = null;
		private $hierarchy = null;
		private $depth     = null;

		/**
		 * Constructor for the class
		 *
		 * @param [type] $filter_id   [description]
		 * @param [type] $filter_type [description]
		 */
		public function __construct( $filter_id = 0, $filter_type = null, $args = array() ) {

			$this->filter_id = $filter_id;
			$this->type      = jet_smart_filters()->filter_types->get_filter_types( $filter_type );

			$args['filter_id'] = $this->filter_id;
			$this->args        = $this->type->prepare_args( $args );

			$this->args['query_id']        = isset( $args['query_id'] ) ? $args['query_id'] : 'default';
			$this->args['show_label']      = isset( $args['show_label'] ) ? $args['show_label'] : false;
			$this->args['display_options'] = isset( $args['display_options'] ) ? $args['display_options'] : array();

		}

		/**
		 * Returns current instance arguments
		 *
		 * @return [type] [description]
		 */
		public function get_args() {
			return $this->args;
		}

		/**
		 * Returns current instance filter ID
		 *
		 * @return [type] [description]
		 */
		public function get_filter_id() {
			return $this->filter_id;
		}

		/**
		 * Return current filter value from request by filter arguments
		 *
		 * @param  array $args [description]
		 *
		 * @return [type]       [description]
		 */
		public function get_current_filter_value( $args = array() ) {

			$query_var = sprintf( '_%s_%s', $args['query_type'], $args['query_var'] );

			if ( false !== $args['query_var_suffix'] ) {
				$query_var .= '|' . $args['query_var_suffix'];
			}

			if ( isset( $_REQUEST[$query_var] ) ) {
				return $_REQUEST[$query_var];
			}

			if ( isset( $args['current_value'] ) ) {
				return $args['current_value'];
			}

			return false;

		}

		/**
		 * Print required data-attributes for filter container
		 *
		 * @param  array $args All argumnets.
		 * @param  object $filter Filter instance.
		 *
		 * @return void
		 */
		public function filter_data_atts( $args ) {

			$provider             = ! empty( $args['content_provider'] ) ? $args['content_provider'] : '';
			$additional_providers = ! empty( $args['additional_providers'] ) ? $args['additional_providers'] : '';
			$query_id             = ! empty( $args['query_id'] ) ? $args['query_id'] : 'default';
			$filter_id            = ! empty( $args['filter_id'] ) ? $args['filter_id'] : 0;
			$active_label         = get_post_meta( $filter_id, '_active_label', true );

			$atts = array(
				'data-query-type'           => $args['query_type'],
				'data-query-var'            => $args['query_var'],
				'data-smart-filter'         => $this->type->get_id(),
				'data-filter-id'            => $filter_id,
				'data-apply-type'           => $args['apply_type'],
				'data-content-provider'     => $provider,
				'data-additional-providers' => $additional_providers,
				'data-query-id'             => $query_id,
				'data-active-label'         => htmlspecialchars($active_label),
				'data-layout-options'       => array(
					'show_label'      => ! empty( $args['show_label'] ) ? $args['show_label'] : '',
					'display_options' => ! empty( $args['display_options'] ) ? $args['display_options'] : array(),
				),
			);

			if ( isset( $args['query_var_suffix'] ) ) {
				$atts['data-query-var-suffix'] = $args['query_var_suffix'];
			}

			if ( ! empty( $args['is_hierarchical'] ) ) {
				$atts['data-hierarchical'] = true;

				if ( ! empty( $args['single_tax'] ) ) {
					$atts['data-single-tax'] = $args['single_tax'];
				}
			}

			if ( ! empty( $args['relational_operator'] ) && 'OR' !== $args['relational_operator'] ) {
				$atts['data-relational-operator'] = $args['relational_operator'];
			}

			if ( method_exists( $this->type, 'additional_filter_data_atts' ) ) {
				$atts = array_merge( $atts, $this->type->additional_filter_data_atts( $args ) );
			}

			echo $this->get_atts_string( $atts );

		}

		/**
		 * Return HTML attributes string from key=>value array
		 *
		 * @param  array $atts Attributes array.
		 *
		 * @return string
		 */
		public function get_atts_string( $atts ) {

			$result = array();

			foreach ( $atts as $key => $value ) {

				if ( is_array( $value ) ) {
					$value = htmlspecialchars( json_encode( $value ) );
				}

				$result[] = sprintf( '%1$s="%2$s"', $key, $value );
			}

			return implode( ' ', $result );

		}

		/**
		 * Render filter of current instance
		 *
		 * @return [type] [description]
		 */
		public function render() {

			if ( empty( $this->type->get_template() ) || ! file_exists( $this->type->get_template() ) ) {
				return;
			}

			$args = $this->args;

			if ( ! empty( $args['is_hierarchical'] ) ) {

				if ( ! class_exists( 'Jet_Smart_Filters_Hierarchy' ) ) {
					require jet_smart_filters()->plugin_path( 'includes/hierarchy.php' );
				}

				$queried_hierarchy = jet_smart_filters()->query->get_queried_hierarchy();
				$values            = ! empty( $queried_hierarchy ) ? $queried_hierarchy['trail'] : array();
				$hierarchy         = new Jet_Smart_Filters_Hierarchy( $this, false, $values, $args );
				$levels            = $hierarchy->get_levels();

				if ( ! empty( $levels ) ) {

					echo '<div class="jet-filters-group">';

					foreach ( $levels as $level ) {
						echo $level;
					}

					echo '</div>';

				}

			} else {
				include $this->type->get_template();
			}

		}

		/**
		 * Returns rendered tempalte for current type
		 *
		 * @return [type] [description]
		 */
		public function get_rendered_template( $args = array() ) {
			ob_start();
			include $this->type->get_template();
			return ob_get_clean();
		}

	}

}
