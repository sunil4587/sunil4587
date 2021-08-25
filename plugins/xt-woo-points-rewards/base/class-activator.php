<?php

/**
 * Fired during plugin activation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Points_Rewards
 * @subpackage XT_Woo_Points_Rewards/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    XT_Woo_Points_Rewards
 * @subpackage XT_Woo_Points_Rewards/includes
 * @author     XplodedThemes 
 */
class XT_Woo_Points_Rewards_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		xt_woo_points_rewards()->frontend()->add_endpoints();
		
		flush_rewrite_rules();
		
		self::expire_points_schedule();

        do_action('xt_woopr_activate');
	}

	public static function expire_points_schedule() {

        if (!wp_next_scheduled('xt_woopr_expire_points_daily')) {

            wp_schedule_event( time(), 'daily', 'xt_woopr_expire_points_daily' );
        }

	}

}

XT_Woo_Points_Rewards_Activator::activate();