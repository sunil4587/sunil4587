<?php

/**
 * XT WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      XplodedThemes
 * @copyright   Copyright (c) 2019, XplodedThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Cart / Checkout class
 *
 * Adds earn/redeem messages to the cart / checkout page and calculates the discounts available
 *
 * @since 1.0
 */
class XT_Woo_Points_Rewards_Cart_Checkout
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      XT_Woo_Points_Rewards $core Core Class
     */
    private  $core ;
    /**
     * Add cart/checkout related hooks / filters
     *
     * @param XT_Woo_Points_Rewards $core
     * @since 1.0
     */
    public function __construct( &$core )
    {
        $this->core = $core;
        if ( !$core->enabled() ) {
            return $this;
        }
        // Coupon display
        add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ) );
        // Coupon loading
        add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'points_last' ) );
        add_action( 'woocommerce_applied_coupon', array( $this, 'points_last' ) );
        // add earn points/redeem points message above shop / cart / checkout
        add_action( 'xt_woofc_before_cart_list', array( $this, 'render_messages' ), 15 );
        add_action( 'woocommerce_before_cart', array( $this, 'render_messages' ), 15 );
        add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'render_messages' ), 15 );
        add_action( 'woocommerce_before_shop_loop', array( $this, 'render_messages' ), 15 );
        // Remove messages above floating cart checkout form since they are already shown above the cart list
        add_action( 'xt_woofc_before_cart_list', array( $this, 'remove_messages_above_woofc_checkout_form' ), 15 );
        // add earned points message on the thank you / order received & order detail page
        add_action( 'woocommerce_view_order', array( $this, 'render_order_message' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'render_order_message' ) );
        // handle the apply discount AJAX submit
        add_action( 'wp_ajax_xt_woopr_apply_discount', array( $this, 'ajax_maybe_apply_discount' ) );
        // set messages fragments
        add_filter(
            'woocommerce_add_to_cart_fragments',
            array( $this, 'set_fragments' ),
            1,
            1
        );
        add_filter(
            'woocommerce_update_order_review_fragments',
            array( $this, 'set_fragments' ),
            1,
            1
        );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        if ( !is_admin() && !wp_is_json_request() && $this->core->access_manager()->can_use_premium_code__premium_only() ) {
            // Register shortcodes
            add_action( 'init', array( $this, 'add_shortcodes__premium_only' ) );
        }
    }
    
    public function body_class( $classes )
    {
        if ( !$this->is_message_visible( 'earn' ) ) {
            $classes[] = 'xt_woopr-hide-earn-message';
        }
        if ( !$this->is_message_visible( 'redeem' ) ) {
            $classes[] = 'xt_woopr-hide-redeem-message';
        }
        return $classes;
    }
    
    public function is_message_visible( $type )
    {
        $cache_key = $this->core->plugin_short_prefix( 'is_message_visible_' . $type );
        $visible = wp_cache_get( $cache_key );
        
        if ( false === $visible ) {
            $on_pages = $this->core->settings()->get_option( $type . '_points_message_pages', array( 'shop', 'cart', 'checkout' ) );
            $visible = ( is_shop() && !in_array( 'shop', $on_pages ) || is_cart() && !in_array( 'cart', $on_pages ) || is_checkout() && !in_array( 'checkout', $on_pages ) ? 'no' : 'yes' );
            wp_cache_set( $cache_key, $visible );
        }
        
        return $visible === 'yes';
    }
    
    public function set_fragments( $fragments )
    {
        
        if ( wp_doing_ajax() ) {
            WC()->cart->calculate_totals();
            $fragments[".xt-framework-notices:not(.xt_woopr-shortcode)"] = $this->get_rendered_messages();
            $fragments[".xt-framework-notices.xt_woopr-shortcode"] = $this->get_rendered_messages( true );
        }
        
        return $fragments;
    }
    
    /**
     * Make the label for the coupon look nicer
     * @param string $label
     * @return string
     */
    public function coupon_label( $label )
    {
        if ( strstr( strtoupper( $label ), 'WC_POINTS_REDEMPTION' ) ) {
            $label = esc_html( esc_html__( 'Points redemption', 'xt-woo-points-rewards' ) );
        }
        return $label;
    }
    
    /**
     * Ensure points are applied before tax, last
     */
    public function points_last()
    {
        $ordered_coupons = array();
        $points = array();
        foreach ( WC()->cart->get_applied_coupons() as $code ) {
            
            if ( strstr( $code, $this->core->frontend()->discount->discount_code_prefix() ) ) {
                $points[] = $code;
            } else {
                $ordered_coupons[] = $code;
            }
        
        }
        WC()->cart->applied_coupons = array_merge( $ordered_coupons, $points );
    }
    
    /**
     * Redeem the available points by generating and applying a discount code via AJAX on the checkout page
     *
     * @since 1.0
     */
    public function ajax_maybe_apply_discount()
    {
        check_ajax_referer( 'apply-coupon', 'security' );
        // bail if the discount has already been applied
        $existing_discount = XT_Woo_Points_Rewards_Discount::get_discount_code();
        // bail if the discount has already been applied
        if ( !empty($existing_discount) && WC()->cart->has_discount( $existing_discount ) ) {
            die;
        }
        // Get discount amount if set and store in session
        WC()->session->set( 'xt_woopr_discount_amount', ( !empty($_POST['discount_amount']) ? absint( $_POST['discount_amount'] ) : '' ) );
        // generate and set unique discount code
        $discount_code = XT_Woo_Points_Rewards_Discount::generate_discount_code();
        // apply the discount
        WC()->cart->add_discount( $discount_code );
        wc_clear_notices();
        die;
    }
    
    public function render_messages( $fromShortcode = false )
    {
        $messages = array();
        $earn_points_message = $this->get_earn_points_message();
        if ( !empty($earn_points_message['message']) ) {
            $messages[] = $earn_points_message;
        }
        $redeem_points_message = $this->get_redeem_points_message();
        if ( !empty($redeem_points_message['message']) ) {
            $messages[] = $redeem_points_message;
        }
        foreach ( $messages as $item ) {
            $this->core->plugin_frontend_notices()->add_info_message( $item['message'], $item['classes'] );
        }
        $classes = ( $fromShortcode ? array( 'xt_woopr-shortcode' ) : array() );
        $this->core->plugin_frontend_notices()->render_frontend_messages( $classes );
    }
    
    public function remove_messages_above_woofc_checkout_form()
    {
        remove_action( 'woocommerce_checkout_before_customer_details', array( $this, 'render_messages' ), 15 );
    }
    
    public function get_rendered_messages( $fromShortcode = false )
    {
        ob_start();
        $this->render_messages( $fromShortcode );
        return ob_get_clean();
    }
    
    /**
     * get the message above the cart displaying how many points the customer will receive for completing their purchase
     *
     * @since 1.0
     */
    public function get_earn_points_message()
    {
        // get the total points earned for this purchase
        $points_earned = $this->get_points_earned_for_purchase();
        $message = $this->core->settings()->get_option( 'earn_points_message' );
        // bail if no message set or no points will be earned for purchase
        if ( !$message || !$points_earned ) {
            return null;
        }
        // points earned
        $message = str_replace( '{points}', number_format_i18n( $points_earned ), $message );
        // points label
        $message = str_replace( '{points_label}', $this->core->get_points_label( $points_earned ), $message );
        $message = apply_filters( 'xt_woopr_earn_points_message', $message, $points_earned );
        // 'View Cart' button
        $buttons = '<a class="button wc-forward xt_woopr-hide-on-cart xt_woopr-hide-on-checkout" href="' . esc_url( wc_get_cart_url() ) . '">' . esc_html__( 'View Cart', 'xt-woo-points-rewards' ) . '</a>';
        $buttons .= '<a class="button wc-forward xt_woopr-hide-on-shop xt_woopr-hide-on-checkout xt_woopr-hide-if-not-wc-page" href="' . esc_url( wc_get_checkout_url() ) . '">' . esc_html__( 'Checkout', 'xt-woo-points-rewards' ) . '</a>';
        $message = array( $message, $buttons );
        return array(
            'classes' => array( 'xt_woofc-earn-message' ),
            'message' => $message,
        );
    }
    
    /**
     * Renders a message on the thank you / order received & order detail page that tells the customer how many points they earned and
     * how many they have total
     */
    public function render_order_message( $order_id )
    {
        $points = $this->core->frontend()->order->get_points_earned_for_order( $order_id );
        $total_points = XT_Woo_Points_Rewards_Manager::get_users_points();
        $message = get_option( 'xt_woopr_order_message' );
        if ( !$message || !$points ) {
            return;
        }
        $message = str_replace( '{points}', number_format_i18n( $points ), $message );
        $message = str_replace( '{points_label}', $this->core->get_points_label( $points ), $message );
        $message = str_replace( '{total_points}', number_format_i18n( $total_points ), $message );
        $message = str_replace( '{total_points_label}', $this->core->get_points_label( $total_points ), $message );
        $message = '<p>' . $message . '</p>';
        echo  apply_filters(
            'xt_woopr_order_message',
            $message,
            $points,
            $total_points
        ) ;
    }
    
    /**
     * Determines if the users cart is fully discounted.
     *
     * @return boolean
     * @since 1.0.010
     *
     */
    public function is_fully_discounted()
    {
        // If cart includes tax AND cart total is 0
        if ( WC()->cart->prices_include_tax && 0 >= WC()->cart->cart_contents_total + WC()->cart->tax_total ) {
            return true;
        }
        // If cart excludes tax AND cart total is 0
        if ( !WC()->cart->prices_include_tax && 0 >= WC()->cart->cart_contents_total ) {
            return true;
        }
        return false;
    }
    
    /**
     * Get the message and button above the cart displaying the points available to redeem for a discount
     *
     * @since 1.0
     */
    public function get_redeem_points_message()
    {
        $message = '';
        $existing_discount = XT_Woo_Points_Rewards_Discount::get_discount_code();
        /*
         * Don't display a points message to the user if:
         * The cart total is fully discounted OR
         * Coupons are disabled OR
         * Points have already been applied for a discount.
         */
        
        if ( !($this->is_fully_discounted() || !wc_coupons_enabled() || !empty($existing_discount) && WC()->cart->has_discount( $existing_discount )) ) {
            // get the total discount available for redeeming points
            $discount_available = $this->get_discount_for_redeeming_points( false, null, true );
            $message = $this->core->settings()->get_option( 'redeem_points_message' );
            // bail if no message set or no points will be earned for purchase
            
            if ( $message && $discount_available ) {
                // points required to redeem for the discount available
                $points = XT_Woo_Points_Rewards_Manager::calculate_points_for_discount( $discount_available );
                $message = str_replace( '{points}', number_format_i18n( $points ), $message );
                // the maximum discount available given how many points the customer has
                $message = str_replace( '{points_value}', wc_price( $discount_available ), $message );
                // points label
                $message = str_replace( '{points_label}', $this->core->get_points_label( $points ), $message );
                $message = apply_filters( 'xt_woopr_redeem_points_message', $message, $discount_available );
                // add 'Apply Discount' button
                $button = '<div data-points="' . esc_attr( $points ) . '" class="xt_woopr_apply_discount_container" data-apply_coupon_nonce="' . wp_create_nonce( "apply-coupon" ) . '">';
                if ( xt_woo_points_rewards()->access_manager()->can_use_premium_code__premium_only() && 'yes' === get_option( 'xt_woopr_partial_redemption_enabled' ) ) {
                    $button .= '   <input type="number" name="xt_woopr_apply_discount_amount" value="' . esc_attr( $points ) . '" class="xt_woopr_apply_discount_amount" />';
                }
                $button .= '   <input type="submit" class="button xt_woopr_apply_discount" name="xt_woopr_apply_discount" value="' . esc_html__( 'Redeem', 'xt-woo-points-rewards' ) . '" />';
                $button .= '</div>';
                $message = array( $message, $button );
            } else {
                $message = '';
            }
        
        }
        
        return array(
            'classes' => array( 'xt_woofc-redeem-message' ),
            'message' => $message,
        );
    }
    
    /**
     * Renders a message and button above the cart displaying the points available to redeem for a discount
     *
     * @since 1.0
     */
    public function render_redeem_points_message()
    {
        $message = $this->get_redeem_points_message();
        if ( $message ) {
            echo  $message ;
        }
    }
    
    /**
     * Returns the amount of points earned for the purchase, calculated by getting the points earned for each individual
     * product purchase multiplied by the quantity being ordered
     *
     * @since 1.0
     */
    private function get_points_earned_for_purchase()
    {
        $points_earned = 0;
        foreach ( WC()->cart->get_cart() as $item_key => $item ) {
            $points_earned += apply_filters(
                'woocommerce_points_earned_for_cart_item',
                XT_Woo_Points_Rewards_Product::get_points_earned_for_product_purchase( $item['data'] ),
                $item_key,
                $item
            ) * $item['quantity'];
        }
        /*
         * Reduce by any discounts.  One minor drawback: if the discount includes a discount on tax and/or shipping
         * it will cost the customer points, but this is a better solution than granting full points for discounted orders.
         */
        $discount = ( wc_prices_include_tax() ? WC()->cart->discount_cart + WC()->cart->discount_cart_tax : WC()->cart->discount_cart );
        $discount_amount = min( XT_Woo_Points_Rewards_Manager::calculate_points( $discount ), $points_earned );
        // Apply a filter that will allow users to manipulate the way discounts affect points earned.
        $points_earned = apply_filters(
            'xt_woopr_discount_points_modifier',
            $points_earned - $discount_amount,
            $points_earned,
            $discount_amount,
            $discount
        );
        // Check if applied coupons have a points modifier and use it to adjust the points earned.
        $coupons = WC()->cart->get_applied_coupons();
        $points_earned = XT_Woo_Points_Rewards_Manager::calculate_points_modification_from_coupons( $points_earned, $coupons );
        $points_earned = apply_filters( 'xt_woopr_points_earned_for_purchase', $points_earned, WC()->cart );
        $points_earned = XT_Woo_Points_Rewards_Manager::round_the_points( $points_earned );
        return $points_earned;
    }
    
    /**
     * Returns the maximum possible discount available given the total amount of points the customer has
     *
     * @param bool $applying To indicate if this method is called during application of the points.
     * @param float $existing_discount_amounts Total amount for existing discount for items in cart.
     * @param bool $for_display Whether to generate discount amount for message display purposes or for the actual discount.
     * @version 1.0.0
     * @since 1.0.00
     */
    public static function get_discount_for_redeeming_points( $applying = false, $existing_discount_amounts = null, $for_display = false )
    {
        $core = xt_woo_points_rewards();
        // get the value of the user's point balance
        $available_user_discount = XT_Woo_Points_Rewards_Manager::get_users_points_value();
        // no discount
        if ( $available_user_discount <= 0 ) {
            return 0;
        }
        
        if ( xt_woo_points_rewards()->access_manager()->can_use_premium_code__premium_only() && $applying && 'yes' === get_option( 'xt_woopr_partial_redemption_enabled' ) && WC()->session->get( 'xt_woopr_discount_amount' ) ) {
            $requested_user_discount = XT_Woo_Points_Rewards_Manager::calculate_points_value( WC()->session->get( 'xt_woopr_discount_amount' ) );
            if ( $requested_user_discount > 0 && $requested_user_discount < $available_user_discount ) {
                $available_user_discount = $requested_user_discount;
            }
        }
        
        // Limit the discount available by the global minimum discount if set.
        $minimum_discount = get_option( 'xt_woopr_cart_min_discount', '' );
        if ( $available_user_discount < $minimum_discount ) {
            return 0;
        }
        $discount_applied = 0;
        if ( !did_action( 'woocommerce_before_calculate_totals' ) ) {
            WC()->cart->calculate_totals();
        }
        /*
         * Calculate the discount to be applied by iterating through each item in the cart and calculating the individual
         * maximum discount available.
         */
        foreach ( WC()->cart->get_cart() as $item_key => $item ) {
            $max_discount = null;
            
            if ( $core->access_manager()->can_use_premium_code__premium_only() && is_numeric( $max_discount ) ) {
                // adjust the max discount by the quantity being ordered
                $max_discount *= $item['quantity'];
                // if the discount available is greater than the max discount, apply the max discount
                $discount = ( $available_user_discount <= $max_discount ? $available_user_discount : $max_discount );
                // Max should be product price. As this will be applied before tax, it will respect other coupons.
            } else {
                /*
                 * Only exclude taxes when configured to in settings and when generating a discount amount for displaying in
                 * the checkout message. This makes the actual discount money amount always tax inclusive.
                 */
                
                if ( 'exclusive' === get_option( 'xt_woopr_points_tax_application', ( wc_prices_include_tax() ? 'inclusive' : 'exclusive' ) ) && $for_display ) {
                    
                    if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                        $max_discount = wc_get_price_excluding_tax( $item['data'], array(
                            'qty' => $item['quantity'],
                        ) );
                    } elseif ( method_exists( $item['data'], 'get_price_excluding_tax' ) ) {
                        $max_discount = $item['data']->get_price_excluding_tax( $item['quantity'] );
                    } else {
                        $max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
                    }
                
                } else {
                    
                    if ( function_exists( 'wc_get_price_including_tax' ) ) {
                        $max_discount = wc_get_price_including_tax( $item['data'], array(
                            'qty' => $item['quantity'],
                        ) );
                    } elseif ( method_exists( $item['data'], 'get_price_including_tax' ) ) {
                        $max_discount = $item['data']->get_price_including_tax( $item['quantity'] );
                    } else {
                        $max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
                    }
                
                }
                
                // if the discount available is greater than the max discount, apply the max discount
                $discount = ( $available_user_discount <= $max_discount ? $available_user_discount : $max_discount );
            }
            
            // add the discount to the amount to be applied
            $discount_applied += $discount;
            // reduce the remaining discount available to be applied
            $available_user_discount -= $discount;
        }
        if ( is_null( $existing_discount_amounts ) ) {
            $existing_discount_amounts = WC()->cart->get_cart_discount_total();
        }
        // if the available discount is greater than the order total, make the discount equal to the order total less any other discounts
        
        if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
            $discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal_ex_tax - $existing_discount_amounts ) );
        } else {
            $discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal - $existing_discount_amounts ) );
        }
        
        // limit the discount available by the global maximum discount if set
        $max_discount = get_option( 'xt_woopr_cart_max_discount' );
        if ( false !== strpos( $max_discount, '%' ) ) {
            $max_discount = self::calculate_discount_modifier( $max_discount );
        }
        if ( $max_discount && $max_discount < $discount_applied ) {
            $discount_applied = $max_discount;
        }
        return filter_var( $discount_applied, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
    }
    
    /**
     * Calculate the maximum points discount when it's set to a percentage by multiplying the percentage times the cart's
     * price
     *
     * @param string $percentage the percentage to multiply the price by
     * @return float the maximum discount after adjusting for the percentage
     * @since 1.0
     */
    private static function calculate_discount_modifier( $percentage )
    {
        $percentage = str_replace( '%', '', $percentage ) / 100;
        
        if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
            $discount = WC()->cart->subtotal_ex_tax;
        } else {
            $discount = WC()->cart->subtotal;
        }
        
        return $percentage * $discount;
    }

}
// end \XT_Woo_Points_Rewards_Cart_Checkout class