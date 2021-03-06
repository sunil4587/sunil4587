<?php
/**
 * XT WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      XplodedThemes
 * @copyright   Copyright (c) 2019, XplodedThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discount class
 *
 * Handles generating the coupon code and data that allows the user to redeem their points for a discount
 *
 * @since 1.0.00
 */
class XT_Woo_Points_Rewards_Discount {

	/**
	 * Core class reference.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      XT_Woo_Points_Rewards    $core    Core Class
	 */
	private $core;

	/**
	 * Add coupon-related filters to help generate the custom coupon
	 *
	 * @param XT_Woo_Points_Rewards $core
	 * @since 1.0.0
	 */
	public function __construct( &$core ) {

		$this->core = $core;

		if(!$core->enabled()) {
			return $this;
		}

		$this->hooks( 'add' );
	}

	/**
	 * Add or remove callbacks to/from the hooks.
	 *
	 * @since 1.0.05
	 * @version 1.0.0
	 *
	 * @param string $verb What operation to perform (either 'add' or 'remove').
	 */
	protected function hooks( $verb ) {
		$filters = array(
			array( 'woocommerce_get_shop_coupon_data', array( $this, 'get_discount_data' ), 10, 2 ),
			array( 'woocommerce_coupon_message', array( $this, 'get_discount_applied_message' ), 10, 3 ),
			array( 'woocommerce_coupon_get_discount_amount', array( $this, 'get_discount_amount' ), 10, 5 ),
            array( 'woocommerce_cart_totals_coupon_label', array($this, 'coupon_label'), 10, 2)
		);

		$func = 'add' === $verb ? 'add_filter' : 'remove_filter';
		foreach ( $filters as $filter ) {
			call_user_func_array( $func, $filter );
		}
	}

	/**
	 * Generate the coupon data required for the discount
	 *
	 * @deprecated 1.6.0
	 * @since 1.0
	 * @param array $data the coupon data
	 * @param string $code the coupon code
	 * @return array the custom coupon data
	 */
	public function get_discount_data( $data, $code ) {
		if ( strtolower( $code ) != $this->get_discount_code() ) {
			return $data;
		}

		// note: we make our points discount "greedy" so as many points as possible are
		//   applied to the order.  However we also want to play nice with other discounts
		//   so if another coupon is applied we want to use less points than otherwise.
		//   The solution is to make this discount apply post-tax so that both pre-tax
		//   and post-tax discounts can be considered.  At the same time we use the cart
		//   subtotal excluding tax to calculate the maximum points discount, so it
		//   functions like a pre-tax discount in that sense.
		$data = array(
			'id'                         => true,
			'type'                       => 'fixed_cart',
			'amount'                     => 0,
			'coupon_amount'              => 0, // 2.2
			'individual_use'             => false,
			'usage_limit'                => '',
			'usage_count'                => '',
			'expiry_date'                => '',
			'apply_before_tax'           => true,
			'free_shipping'              => false,
			'product_categories'         => array(),
			'exclude_product_categories' => array(),
			'exclude_sale_items'         => false,
			'minimum_amount'             => '',
			'maximum_amount'             => '',
			'customer_email'             => '',
		);

		return $data;
	}

	/**
	 * Get total amount discounted by existing fixed_product coupons.
	 *
	 * @since 1.0.05
	 * @version 1.0.0
	 *
	 * @return float
	 */
	public function get_discount_total_from_existing_coupons() {

		$total_discount = 0;
		foreach ( WC()->cart->get_cart() as $item ) {
			$total_discount += $this->get_cart_item_discount_total( $item );
		}

		return $total_discount;
	}

	/**
	 * Get discount total (using fixed_product coupons) from a given cart item.
	 *
	 * @since 1.0.05
	 * @version 1.0.0
	 *
	 * @param mixed $item Cart item.
	 *
	 * @return float Discount total.
	 */
	public function get_cart_item_discount_total( $item ) {

        $cache_key = $this->core->plugin_short_prefix('get_cart_item_discount_total'.$item['key']);
        $discount = wp_cache_get( $cache_key );

        if ( false === $discount ) {

            // Since we call get_discount_amount this could potentially result in
            // a loop.
            $this->hooks( 'remove' );

            $coupons_cache_key = $this->core->plugin_short_prefix('get_cart_coupons');
            $coupons = wp_cache_get( $coupons_cache_key );

            if ( false === $coupons ) {

                $coupons = WC()->cart->get_coupons();

                wp_cache_set( $coupons_cache_key, $coupons );
            }

            foreach ($coupons as $coupon) {
                if (strtolower($coupon->get_code()) === $this->get_discount_code() || !$coupon->is_type('fixed_product')) {
                    continue;
                }

                if (!$coupon->is_valid()) {
                    continue;
                }

                if ($coupon->is_valid_for_product($item['data'], $item) || $coupon->is_valid_for_cart()) {
                    $discount += (float)$coupon->get_discount_amount($item['data']->get_price(), $item, true) * $item['quantity'];
                }
            }

            // Add the hooks back.
            $this->hooks('add');

            $discount = is_numeric($discount) ? $discount : 0;

            wp_cache_set( $cache_key, $discount );
        }

		return $discount;
	}

	/**
	 * Get coupon discount amount
	 *
	 * @since 1.0.00
	 * @version 1.0.0
	 * @param  float $discount Amount this coupon has been discounted
	 * @param  float $discounting_amount Amount the coupon is being applied to
	 * @param  array $cart_item Cart item being discounted if applicable
	 * @param  boolean $single True if discounting a single qty item, false if its the line
	 * @param  WC_Coupon $coupon
	 * @return float Amount this coupon will be discounted
	 */
	public function get_discount_amount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
		if ( strtolower( $coupon->get_code() ) != $this->get_discount_code() ) {
			return $discount;
		}

		$existing_discount_amounts = $this->get_discount_total_from_existing_coupons();

		/**
		 * This is the most complex discount - we need to divide the discount between rows based on their price in
		 * proportion to the subtotal. This is so rows with different tax rates get a fair discount, and so rows
		 * with no price (free) don't get discounted.
		 *
		 * Get item discount by dividing item cost by subtotal to get a %
		 */

		$cart_item_qty    = $cart_item['quantity'];
		$cart_item_data   = $cart_item['data'];

		if ( wc_prices_include_tax() ) {
			$sub_total_inc_tax = WC()->cart->subtotal - $existing_discount_amounts;

			$discount_percent = (
				wc_get_price_including_tax( $cart_item_data ) * $cart_item_qty - $this->get_cart_item_discount_total( $cart_item )
			) / $sub_total_inc_tax;
		} else {
			$sub_total_ex_tax = WC()->cart->subtotal_ex_tax - $existing_discount_amounts;

			$discount_percent = (
				wc_get_price_excluding_tax( $cart_item_data ) * $cart_item_qty - $this->get_cart_item_discount_total( $cart_item )
			) / $sub_total_ex_tax;
		}

		$total_discount                 = XT_Woo_Points_Rewards_Cart_Checkout::get_discount_for_redeeming_points( true, $existing_discount_amounts );
		$total_with_discount_percent    = (float) $total_discount * $discount_percent;

		if ( version_compare( WC_VERSION, '3.2.0', '<' ) ) {
			$total_with_discount_percent = $total_with_discount_percent / $cart_item['quantity'];
		}

	
		return round( min( $total_with_discount_percent, $discounting_amount ), wc_get_rounding_precision() );
		
	}

	/**
	 * Change the "Coupon applied successfully" message to "Discount Applied Successfully"
	 *
	 * @since 1.0.00
	 * @param string $message the message text
	 * @param string $message_code the message code
	 * @param object $coupon the WC_Coupon instance
	 * @return string the modified messages
	 */
	public function get_discount_applied_message( $message, $message_code, $coupon ) {
		if ( WC_Coupon::WC_COUPON_SUCCESS === $message_code && $coupon->get_code() === $this->get_discount_code() ) {
			return __( 'Discount Applied Successfully', 'xt-woo-points-rewards' );
		} else {
			return $message;
		}
	}

	public function coupon_label($label, $coupon) {

	    if(strpos($coupon, $this->discount_code_prefix()) !== false) {
            return $this->core->settings()->get_option('points_coupon_label', esc_html__('Points Redemption', 'xt-woo-points-rewards'));
        }

        return $label;
    }
	
	/**
	 * Get discount code prefix
	 *
	 * @since 1.0.00
	 */
	public static function discount_code_prefix() {
		
		return apply_filters('discount_code_prefix', 'points_redemption_');
	}

	/**
	 * Generates a unique discount code tied to the current user ID and timestamp
	 *
	 * @since 1.0.00
	 */
	public static function generate_discount_code() {
		// set the discount code to the current user ID + the current time in YYYY_MM_DD_H_M format
	
		$discount_code = sprintf( '%s%s_%s', self::discount_code_prefix(), get_current_user_id(), date( 'Y_m_d_h_i', current_time( 'timestamp' ) ) );

		WC()->session->set( 'xt_woopr_discount_code', $discount_code );

		return $discount_code;
	}

	/**
	 * Returns the unique discount code generated for the applied discount if set
	 *
	 * @since 1.0.00
	 */
	public static function get_discount_code() {
		if ( WC()->session !== null ) {
			return WC()->session->get( 'xt_woopr_discount_code' );
		}
	}
}
