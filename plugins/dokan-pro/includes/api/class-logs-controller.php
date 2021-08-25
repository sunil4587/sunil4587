<?php

/**
* All Logs API controller
*
* @since 2.9.4
*
* @package dokan
*/
class Dokan_REST_Logs_Controller extends Dokan_REST_Admin_Controller {

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'logs';

    /**
     * Register all routes related with logs
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_logs' ),
                'permission_callback' => array( $this, 'check_permission' )
            ),
        ) );
    }

    /**
     * Get all logs
     *
     * @since 2.9.4
     *
     * @return object
     */
    public function get_logs( $request ) {
        global $wpdb;

        $params = wp_unslash( $request->get_params() );
        $limit  = isset( $params['per_page'] ) ? (int) $params['per_page'] : 20;
        $offset = isset( $params['page'] ) ? (int) ( $params['page'] - 1 ) * $params['per_page'] : 0;

        // filter the log query
        $order_id      = ! empty( $params['order_id'] ) ? $params['order_id'] : 0;
        $vendor_id     = ! empty( $params['vendor_id'] ) ? (int) $params['vendor_id'] : 0;
        $order_status  = ! empty( $params['order_status'] ) ? $params['order_status'] : '';

        $order_clause  = $order_id ? "order_id = {$order_id}" : "order_id != 0";
        $seller_clause = $vendor_id ? "seller_id = {$vendor_id}" : "seller_id != 0";
        $status_clause = $order_status ? "p.post_status = '{$order_status}'" : "p.post_status != 'trash'";
        $where_query   = "{$seller_clause} AND {$status_clause} AND {$order_clause}";

        $items = $wpdb->get_row(
            "SELECT COUNT( do.id ) as total FROM {$wpdb->prefix}dokan_orders do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            WHERE $where_query
            ORDER BY do.order_id"
        );

        if ( is_wp_error( $items ) ) {
            return $items->get_error_message();
        }

        if ( ! $items->total ) {
            wp_send_json_error( __( 'No logs found', 'dokan' ) );
        }

        $sql = $wpdb->prepare(
            "SELECT do.*, p.post_date FROM {$wpdb->prefix}dokan_orders do
            LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
            WHERE $where_query
            ORDER BY do.order_id DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );

        $results = $wpdb->get_results( $sql );
        $logs     = [];
        $statuses = wc_get_order_statuses();

        foreach ( $results as $result ) {
            $order       = wc_get_order( $result->order_id );
            $order_total = $order->get_total();
            $has_refund  = $order->get_total_refunded() ? true : false;

            $logs[] = [
                'order_id'             => $result->order_id,
                'vendor_id'            => $result->seller_id,
                'vendor_name'          => dokan()->vendor->get( $result->seller_id )->get_shop_name(),
                'previous_order_total' => $order_total,
                'order_total'          => $result->order_total,
                'vendor_earning'       => $result->net_amount,
                'commission'           => $result->order_total - $result->net_amount,
                'status'               => $statuses[ $result->order_status ],
                'date'                 => $result->post_date,
                'has_refund'           => $has_refund,
            ];
        }

        $response = rest_ensure_response( $logs );
        $response = $this->format_collection_response( $response, $request, $items->total );

        return $response;
    }
}