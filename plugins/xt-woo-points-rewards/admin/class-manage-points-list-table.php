<?php

/**
 * XT WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/List-Table
 * @author      XplodedThemes
 * @copyright   Copyright (c) 2019, XplodedThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Points and Rewards Manage Points List Table class
 *
 * Extends WP_List_Table to display customer reward points
 *
 * @since 1.0
 * @extends \WP_List_Table
 */
class XT_Woo_Points_Rewards_Manage_Points_List_Table extends WP_List_Table
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      XT_Woo_Points_Rewards    core    Core Class
     */
    protected  $core ;
    /**
     * Setup list table
     *
     * @see WP_List_Table::__construct()
     * @since 1.0
     * @return \XT_Woo_Points_Rewards_Manage_Points_List_Table
     */
    public function __construct()
    {
        parent::__construct( array(
            'singular' => esc_html__( 'Point', 'xt-woo-points-rewards' ),
            'plural'   => esc_html__( 'Points', 'xt-woo-points-rewards' ),
            'ajax'     => false,
            'screen'   => 'woocommerce_page_xt_woopr_manage_points',
        ) );
        $this->core = xt_woo_points_rewards();
    }
    
    /**
     * Gets the bulk action available for user points: update
     *
     * @see WP_List_Table::get_bulk_actions()
     * @since 1.0
     * @return array associative array of action_slug => action_title
     */
    public function get_bulk_actions()
    {
        $actions = array();
        return $actions;
    }
    
    /**
     * Returns the column slugs and titles
     *
     * @see WP_List_Table::get_columns()
     * @since 1.0
     * @return array of column slug => title
     */
    public function get_columns()
    {
        $columns = array();
        $columns['customer'] = esc_html__( 'Customer', 'xt-woo-points-rewards' );
        $columns['points'] = esc_html__( 'Points', 'xt-woo-points-rewards' );
        $columns['value'] = esc_html__( 'Value', 'xt-woo-points-rewards' );
        $columns['update'] = esc_html__( 'Update', 'xt-woo-points-rewards' );
        return $columns;
    }
    
    /**
     * Returns the sortable columns and initial direction
     *
     * @see WP_List_Table::get_sortable_columns()
     * @since 1.0
     * @return array of sortable column slug => array( 'orderby', boolean )
     *         where true indicates the initial sort is descending
     */
    public function get_sortable_columns()
    {
        // really the only thing that makes sense to sort is the points column
        return array(
            'points' => array( 'points', false ),
        );
    }
    
    /**
     * Get content for the special checkbox column
     *
     * @see WP_List_Table::single_row_columns()
     * @since 1.0
     * @param object $row one row (item) in the table
     * @return string the checkbox column content
     */
    public function column_cb( $row )
    {
        return '';
    }
    
    /**
     * Get column content, this is called once per column, per row item ($user_points)
     * returns the content to be rendered within that cell.
     *
     * @see WP_List_Table::single_row_columns()
     * @since 1.0
     * @param object $user_points one row (item) in the table
     * @param string $column_name the column slug
     * @return string the column content
     */
    public function column_default( $user_points, $column_name )
    {
        $points_balance = 0;
        // todo: we need to rethink the logic of getting/saving points
        // for now this is a temporary work around to allow zero points
        // users to have their points updated (also {@see column_cb()})
        
        if ( null === $user_points->points_balance ) {
            // get user id from email
            $user = get_user_by( 'email', $user_points->user_email );
            $user_points->ID = $user->ID;
        }
        
        if ( isset( $user_points->points_balance ) ) {
            $points_balance = intval( $user_points->points_balance );
        }
        switch ( $column_name ) {
            case 'customer':
                $customer_email = $user_points->user_email;
                $column_content = sprintf( '<a href="%s">%s</a>', get_edit_user_link( $user_points->ID ), $customer_email );
                break;
            case 'points':
                $column_content = $points_balance;
                break;
            case 'value':
                $column_content = wc_price( XT_Woo_Points_Rewards_Manager::get_users_points_value( $user_points->ID ) );
                break;
            case 'update':
                $column_content = '<input type="text" class="points_balance" name="points_balance[' . esc_attr( $user_points->ID ) . ']" value="' . $points_balance . '" />' . ' <a class="button update_points" href="' . wp_nonce_url( remove_query_arg( 'points_balance', add_query_arg( array(
                    'action'  => 'update',
                    'user_id' => $user_points->ID,
                ) ) ), 'xt_woopr_update' ) . '">' . esc_html__( 'Update', 'xt-woo-points-rewards' ) . '</a>';
                break;
            default:
                $column_content = '';
                break;
        }
        return $column_content;
    }
    
    /**
     * Get the current action selected from the bulk actions dropdown, verifying
     * that it's a valid action to perform
     *
     * @see WP_List_Table::current_action()
     * @since 1.0
     * @return string|bool The action name or False if no action was selected
     */
    public function current_action()
    {
        $current_action = parent::current_action();
        return $current_action;
    }
    
    /**
     * Handle actions for both individual items and bulk update
     *
     * @since 1.0
     */
    public function process_actions()
    {
        // get the current action (if any)
        $action = $this->current_action();
        // get the set of users to operate on
        $user_ids = ( isset( $_REQUEST['user_id'] ) ? array_map( 'absint', (array) $_REQUEST['user_id'] ) : array() );
        // no action, or invalid action
        if ( false === $action || empty($user_ids) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'xt_woopr_update' ) && !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-points' ) ) {
            return;
        }
        $success_count = $error_count = 0;
        // process the users
        foreach ( $user_ids as $user_id ) {
            // perform the action
            switch ( $action ) {
                case 'update':
                    
                    if ( XT_Woo_Points_Rewards_Manager::set_points_balance( $user_id, $_REQUEST['points_balance'][$user_id], 'admin-adjustment' ) ) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                    
                    break;
            }
        }
        // build the result message(s)
        switch ( $action ) {
            case 'update':
                if ( $success_count > 0 ) {
                    $this->core->plugin_notices()->add_success_message( sprintf( _n(
                        '%d customer updated.',
                        '%s customers updated.',
                        $success_count,
                        'xt-woo-points-rewards'
                    ), $success_count ) );
                }
                if ( $error_count > 0 ) {
                    $this->core->plugin_notices()->add_error_message( sprintf( _n(
                        '%d customer could not be updated.',
                        '%s customers could not be updated.',
                        $error_count,
                        'xt-woo-points-rewards'
                    ), $error_count ) );
                }
                break;
        }
    }
    
    /**
     * Output any messages from the bulk action handling
     *
     * @since 1.0
     */
    public function render_messages()
    {
        $this->core->plugin_notices()->render_backend_messages();
    }
    
    /**
     * Gets the current orderby, defaulting to 'user_id' if none is selected
     *
     * @since 1.0
     */
    private function get_current_orderby()
    {
        $orderby = ( isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'points' );
        // order by points or default of user ID
        switch ( $orderby ) {
            case 'points':
                return 'points';
            default:
                return 'ID';
        }
    }
    
    /**
     * Gets the current orderby, defaulting to 'DESC' if none is selected
     *
     * @since 1.0
     */
    private function get_current_order()
    {
        return ( isset( $_GET['order'] ) ? $_GET['order'] : 'DESC' );
    }
    
    /**
     * Generates queries to get our list table items.
     */
    private function get_items()
    {
        global  $wpdb ;
        $per_page = $this->get_items_per_page( 'xt_woopr_manage_points_per_page' );
        $offset = ($this->get_pagenum() - 1) * $per_page;
        switch ( $this->get_current_order() ) {
            case 'asc':
                $order = 'ASC';
                break;
            default:
                $order = 'DESC';
                break;
        }
        // Do we need to filter by customer?
        $where = '';
        if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
            $where = $wpdb->prepare( "AND users.ID = %d", $_GET['_customer_user'] );
        }
        // Build a query we can use for count and results
        $query = "FROM {$wpdb->users} as users LEFT JOIN {$wpdb->prefix}xt_woopr_user_points as points_table ON users.ID = points_table.user_id WHERE users.user_email != '' {$where} GROUP BY users.ID ORDER BY SUM(points_table.points_balance+0) {$order}";
        return array(
            'count'   => $wpdb->get_var( "SELECT COUNT( DISTINCT users.ID ) as found_user_points FROM {$wpdb->users} as users LEFT JOIN {$wpdb->prefix}xt_woopr_user_points as points_table ON users.ID = points_table.user_id WHERE 1=1 {$where}" ),
            'results' => $wpdb->get_results( $wpdb->prepare( "SELECT points_table.user_id as ID, SUM( points_table.points_balance ) as points_balance, users.user_email {$query} LIMIT %d, %d", $offset, $per_page ) ),
        );
    }
    
    /**
     * Prepare the list of user points items for display
     *
     * @see WP_List_Table::prepare_items()
     * @since 1.0
     */
    public function prepare_items()
    {
        $this->process_actions();
        $per_page = $this->get_items_per_page( 'xt_woopr_manage_points_per_page' );
        $items = $this->get_items();
        $this->items = $items['results'];
        $count = $items['count'];
        $this->set_pagination_args( array(
            'total_items' => $count,
            'per_page'    => $per_page,
            'total_pages' => ceil( $count / $per_page ),
        ) );
    }
    
    /**
     * Adds in any query arguments based on the current filters
     *
     * @since 1.0
     * @param array $args associative array of WP_Query arguments used to query and populate the list table
     * @return array associative array of WP_Query arguments used to query and populate the list table
     */
    private function add_filter_args( $args )
    {
        // filter by customer
        if ( isset( $_GET['_customer_user'] ) && $_GET['_customer_user'] > 0 ) {
            $args['include'] = array( $_GET['_customer_user'] );
        }
        return $args;
    }
    
    /**
     * The text to display when there are no user pointss
     *
     * @see WP_List_Table::no_items()
     * @since 1.0
     */
    public function no_items()
    {
        
        if ( isset( $_REQUEST['s'] ) ) {
            ?>
			<p><?php 
            _e( 'No user points found', 'xt-woo-points-rewards' );
            ?></p>
		<?php 
        } else {
            ?>
			<p><?php 
            _e( 'User points will appear here for you to view and manage once you have customers.', 'xt-woo-points-rewards' );
            ?></p>
		<?php 
        }
    
    }
    
    /**
     * Extra controls to be displayed between bulk actions and pagination, which
     * includes our Filters: Customers, Products, Availability Dates
     *
     * @see WP_List_Table::extra_tablenav();
     * @since 1.0
     * @param string $which the placement, one of 'top' or 'bottom'
     */
    public function extra_tablenav( $which )
    {
        
        if ( 'top' == $which ) {
            echo  '<div class="alignleft actions">' ;
            // Customers, products
            $user_string = '';
            $customer_id = '';
            
            if ( !empty($_GET['_customer_user']) ) {
                $customer_id = absint( $_GET['_customer_user'] );
                $user = get_user_by( 'id', $customer_id );
                $user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
            }
            
            ?>

            <select id="customer_user" class="wc-customer-search" name="_customer_user"
                    data-placeholder="<?php 
            esc_attr_e( 'Show All Customers', 'xt-woo-points-rewards' );
            ?>"
                    data-allow_clear="true">

                <?php 
            if ( !empty($customer_id) ) {
                echo  '<option value="' . esc_attr( $customer_id ) . '">' . wp_kses_post( $user_string ) . '</option>' ;
            }
            ?>
            </select>

			<?php 
            submit_button(
                esc_html__( 'Filter', 'xt-woo-points-rewards' ),
                'button',
                false,
                false,
                array(
                'id' => 'post-query-submit',
            )
            );
            echo  '</div>' ;
            // javascript
            xtfw_enqueue_js( "\n            // submit the single-row Update action\n            \$( 'a.update_points' ).click( function() {\n                var \$el = \$( this );\n                \$el.attr( 'href', \$el.attr( 'href' ) + '&' + \$el.prev().attr('name') + '=' + \$el.prev().val() );\n            } );\n\n            // when the focus is on one of the 'points balance' inputs, and the form is submitted, assume we're updating only that one record\n            \$( 'form#mainform' ).submit( function() {\n                var \$focused = \$( ':focus' );\n\n                if ( \$focused && \$focused.hasClass( 'points_balance' ) ) {\n                    location.href = \$focused.next().attr( 'href' ) + '&' + \$focused.attr('name') + '=' + \$focused.val();\n                    return false;\n                }\n\n                return true;\n            } );\n\n            // Handle 'Filter' button separately from form so the filter parameters will not be via 'post' method.\n            \$( '#post-query-submit' ).on( 'click', function() {\n                var customer_id = \$( '#customer_user' ).val();\n                if ( null === customer_id ) {\n                    // Clear _customer_user parameter from url (in case user has intentionally cleared filter).\n                    location.href = location.href.replace( /&?_customer_user=([^&]\$|[^&]*)/i, \"\" );\n                } else {\n                    location.href = location.href + '&_customer_user=' + customer_id;\n                }\n                \n                return false;\n            } );\n        " );
        }
    
    }

}