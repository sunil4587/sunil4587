<?php
/**
 * Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Hierarchy' ) ) {

	class Jet_Smart_Filters_Hierarchy {

		private $filter_id  = 0;
		private $depth      = 0;
		private $values     = array();
		private $args       = array();
		private $filter     = null;
		private $single_tax = null;
		private $hierarchy  = null;
		private $indexer    = false;

		/**
		 * Class constructor
		 *
		 * @param integer|object $filter_id [description]
		 * @param integer $depth     [description]
		 * @param array   $values    [description]
		 * @param array   $args      [description]
		 */
		public function __construct( $filter = 0, $depth = 0, $values = array(), $args = array(), $indexer = false ) {

			if ( isset( $args['layout_options'] ) ) {
				$layout_options = $args['layout_options'];
				unset( $args['layout_options'] );
				$args = array_merge( $args, $layout_options );
			}

			$this->args = $args;

			if ( is_integer( $filter ) ) {

				$this->filter_id = $filter;
				$this->filter    = jet_smart_filters()->filter_types->get_filter_instance(
					$this->filter_id,
					null,
					$this->args
				);

			} else {
				$this->filter_id = $filter->get_filter_id();
				$this->filter    = $filter;
			}

			$this->depth     = $depth;
			$this->values    = $values;
			$this->indexer   = $indexer;
			$this->hierarchy = $this->get_hierarchy();

		}

		/**
		 * Returns current filter hieararchy map or false
		 *
		 * @return boolean|array
		 */
		public function get_hierarchy() {

			$hierarchy = get_post_meta( $this->filter_id, '_ih_source_map', true );

			if ( empty( $hierarchy ) ) {
				return false;
			}

			$result = array();

			foreach ( array_values( $hierarchy ) as $depth => $data ) {

				$result[] = array(
					'depth'       => $depth,
					'tax'         => $data['tax'],
					'label'       => $data['label'],
					'placeholder' => $data['placeholder'],
					'options'     => false,
				);

			}

			return $result;

		}

		/**
		 * Prepare indexer
		 *
		 * @return [type] [description]
		 */
		public function setup_indexer() {

			if ( empty( $this->indexer ) || ! is_array( $this->indexer ) ) {
				return false;
			}

			if ( ! jet_smart_filters()->indexer->data ) {
				return false;
			}

			jet_smart_filters()->query->set_is_ajax_filter();
			jet_smart_filters()->query->set_provider( $this->indexer['provider'] );

			$provider = jet_smart_filters()->query->get_current_provider( 'provider' );
			$query_id = jet_smart_filters()->query->get_current_provider( 'query_id' );

			jet_smart_filters()->query->store_provider_default_query(
				$provider,
				$this->indexer['defaults'],
				$query_id
			);

			jet_smart_filters()->indexer->data->setup_queries_from_request();

			return true;

		}

		/**
		 * Returns hiearachy evels data starting from $this->depth
		 *
		 * @return  array
		 */
		public function get_levels() {

			timer_start();

			if ( empty( $this->hierarchy ) ) {
				return;
			}

			$has_indexer = $this->setup_indexer();

			$result = array();
			$filter = $this->filter;

			$from_depth = ( false !== $this->depth ) ? $this->depth : 0;

			$single_tax = $this->hierarchy[0]['tax'];
			foreach ( $this->hierarchy as $hierarchy_level ) {
				$single_tax = $single_tax === $hierarchy_level['tax'] ? $hierarchy_level['tax'] : false;
			}

			for ( $i = $from_depth; $i <= count( $this->hierarchy ); $i++ ) {

				$level = ! empty( $this->hierarchy[ $i ] ) ? $this->hierarchy[ $i ] : false;

				if ( ! $level ) {
					continue;
				}

				$args = $filter->get_args();

				$args['depth']           = $level['depth'];
				$args['query_var']       = $level['tax'];
				$args['placeholder']     = ! empty( $level['placeholder'] ) ? $level['placeholder'] : __( 'Select...', 'jet-smart-filters' );
				$args['max_depth']       = count( $this->hierarchy ) - 1;
				$args['options']         = array();
				$args['filter_label']    = ! empty( $level['label'] ) ? $level['label'] : '';
				$args['show_label']      = ! empty( $this->args['show_label'] ) ? $this->args['show_label'] : '';
				$args['display_options'] = ! empty( $this->args['display_options'] ) ? $this->args['display_options'] : array();

				if ( $single_tax ) {
					$args['single_tax'] = $single_tax;
				}

				if ( ! empty( $_REQUEST['hc'] ) ) {
					$hierarchical_chain = explode( ',', $_REQUEST['hc'] );

					if ( ! empty( $hierarchical_chain[$level['depth']] ) ) {
						$args['current_value'] = $hierarchical_chain[$level['depth']];
					}
				}

				if ( false === $this->depth ) {
					if ( $i <= count( $this->values ) ) {

						$args['options'] = $this->get_level_options( $i, $level );
						$value = isset( $this->values[ $i ] ) ? $this->values[ $i ]['value'] : false;

						if ( false !== $value ) {
							$args['current_value'] = $value;
						}

					}
				} elseif ( $i === $from_depth ) {
					$args['options'] = $this->get_level_options( $i, $level );
				}

				$result[ 'level_' . $i ] = $this->filter->get_rendered_template( $args );

			}

			if ( $has_indexer ) {
				$result['jetFiltersIndexedData'] = jet_smart_filters()->indexer->data->prepare_provider_counts();
			}

			return $result;

		}

		/**
		 * Returns terms for options
		 *
		 * @return [type] [description]
		 */
		public function get_level_options( $i = 0, $level = array() ) {

			global $wpdb;

			$single_tax = $this->is_single_tax_hierarchy();
			$result     = array();

			if ( $single_tax ) {

				if ( false === $this->depth && 0 === $i ) {
					$value = 0;
				} else {
					$index = $i - 1;
					$value = isset( $this->values[ $index ] ) ? $this->values[ $index ]['value'] : false;
				}

				if ( false !== $value ) {

					if ( empty( $value ) && 0 !== $value ) {
						return array();
					}

					$result = jet_smart_filters()->data->get_terms_for_options(
						$level['tax'],
						false,
						array(
							'parent' => $value,
						)
					);

				}

			} else {

				$from  = '';
				$on    = '';
				$where = '';
				$glue  = '';
				$index = 0;

				$prepared_values = array();

				/**
				 * Ensure we left only latest child of each taxonomy
				 */
				for ( $level_index = 0; $level_index < $i; $level_index++ ) {
					$level_val = $this->values[ $level_index ];
					$prepared_values[ $level_val['tax'] ] = $level_val['value'];
				}

				foreach ( $prepared_values as $tax => $value ) {
					if ( $value ) {

						$table            = $wpdb->term_relationships;
						$value            = absint( $value );
						$term_taxonomy    = get_term( $value );
						$term_taxonomy_id = ! is_wp_error($term_taxonomy) ? $term_taxonomy->term_taxonomy_id : false;

						if ( 0 === $index ) {
							$from  .= "SELECT t0.object_id FROM $table AS t0";
							$where .= " WHERE t0.term_taxonomy_id = {$term_taxonomy_id}";
						} else {
							$from  .= " INNER JOIN $table AS t{$index}";
							$where .= " AND t{$index}.term_taxonomy_id = {$term_taxonomy_id}";
							$prev   = $index - 1;
							$on    .= "{$glue}t{$prev}.object_id = t{$index}.object_id";
							$glue   = ' AND ';
						}

						$index++;
					}
				}

				if ( ! empty( $on ) ) {
					$on = ' ON ( ' . $on . ' )';
				}

				if ( $from ) {

					$ids = $wpdb->get_results( $from . $on . $where, OBJECT_K );

					if ( ! empty( $ids ) ) {

						$result = jet_smart_filters()->data->get_terms_for_options(
							$level['tax'],
							false,
							array(
								'object_ids' => array_keys( $ids ),
							)
						);

					}
				
				} else {

					$result = jet_smart_filters()->data->get_terms_for_options(
						$level['tax'],
						false,
						array()
					);

				}
			}

			return $result;

		}

		/**
		 * Check if all previous hierarchy levels has same taxonomy.
		 * In this case we need get only direct children of latest value
		 *
		 * @return bool false or string - taxonomy slug
		 */
		public function is_single_tax_hierarchy() {

			if ( null !== $this->single_tax ) {
				return $this->single_tax;
			}

			$single_tax = true;
			$tax        = null;
			$to_depth   = ( false !== $this->depth ) ? $this->depth : count( $this->values );

			for ( $i = 0; $i <= $to_depth; $i++ ) {

				$level = ! empty( $this->hierarchy[ $i ] ) ? $this->hierarchy[ $i ] : false;

				if ( ! $level ) {
					continue;
				}

				if ( ! $tax ) {
					$tax = $level['tax'];
				} elseif ( $tax !== $level['tax'] ) {
					$single_tax = false;
				}
			}

			if ( $single_tax ) {
				$this->single_tax = $tax;
			} else {
				$this->single_tax = false;
			}

			return $this->single_tax;

		}

	}

}
