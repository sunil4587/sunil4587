<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Points_Rewards
 * @subpackage XT_Woo_Points_Rewards/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    XT_Woo_Points_Rewards
 * @subpackage XT_Woo_Points_Rewards/includes
 * @author     XplodedThemes 
 */
class XT_Woo_Points_Rewards_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		self::expire_points_remove_schedule();

        do_action('xt_woopr_deactivate');
	}

	public static function expire_points_remove_schedule() {
		
		wp_clear_scheduled_hook( 'xt_woopr_expire_points' );
	}

}

XT_Woo_Points_Rewards_Deactivator::deactivate();