<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 * @package    XT_Woo_Points_Rewards
 * @author     XplodedThemes
*/
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class XT_Woo_Points_Rewards extends XT_Framework
{
    /**
     * The single instance of XT_Woo_Points_Rewards.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static  $_instance = null ;
    /** @var \WC_Logger instance */
    private  $logger ;
    /** @var string the user points log database tablename */
    public  $user_points_log_db_tablename ;
    /** @var string the user points database tablename */
    public  $user_points_db_tablename ;
    /**
     * Bootstrap plugin
     *
     * This hack is needed. Overriding parent for Freemius to work properly.
     * Freemius needs to be called from each plugin and not from the XT Framework instance.
     * This way, when Freemius calls the function "get_caller_main_file_and_type", it will return the correct plugin path
     * Otherwise, the main path will be seen for all plugins and will cause issues
     *
     * Waiting for a fix from Freemius
     *
     * @since    1.0.0
     * @access   public
     */
    public function bootstrap()
    {
        global  $wpdb ;
        // initialize the custom table names
        $this->user_points_log_db_tablename = $wpdb->prefix . 'xt_woopr_user_points_log';
        $this->user_points_db_tablename = $wpdb->prefix . 'xt_woopr_user_points';
        add_action( 'after_switch_theme', array( $this, 're_activate' ) );
        add_action( $this->plugin_prefix( 'migration_complete' ), array( $this, 're_activate' ) );
        parent::bootstrap();
    }
    
    /**
     * Load Freemius License Manager
     *
     * This hack is needed. Implementing this abstract XT Framework method for Freemius to work properly.
     * Freemius fs_dynamic_init needs to be called from each plugin and not from the XT Framework instance,
     * This way the "is_premium" param will correctly be generated for both free and premium versions
     *
     * Waiting for a fix from Freemius
     *
     * @return mixed
     * @since    1.0.0
     */
    protected function freemius_access_manager()
    {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_' . $this->market_product()->id . '_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_' . $this->market_product()->id . '_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once $this->plugin_framework_path( 'includes/freemius', 'start.php' );
        $menu = array(
            'slug'    => $this->plugin_slug(),
            'contact' => false,
            'support' => false,
        );
        if ( !$this->plugin()->top_menu() ) {
            $menu['parent'] = array(
                'slug' => $this->framework_slug(),
            );
        }
        return fs_dynamic_init( array(
            'id'              => $this->market_product()->id,
            'slug'            => $this->market_product()->freemium_slug,
            'premium_slug'    => $this->market_product()->premium_slug,
            'type'            => 'plugin',
            'public_key'      => $this->market_product()->key,
            'is_premium'      => false,
            'premium_suffix'  => 'Pro',
            'has_addons'      => false,
            'has_paid_plans'  => true,
            'has_affiliation' => 'all',
            'trial'           => array(
            'days'               => 14,
            'is_require_payment' => true,
        ),
            'menu'            => $menu,
            'navigation'      => 'menu',
            'is_live'         => true,
        ) );
    }
    
    /**
     * Re-run plugin activation after a theme switch
     */
    public function re_activate()
    {
        $this->plugin_base_hooks()->activate();
    }
    
    public function enabled()
    {
        return $this->settings()->get_option_bool( 'enabled' );
    }
    
    /**
     * Log errors / messages to WooCommerce error log (/wp-content/woocommerce/logs/)
     *
     * @param string $message
     * @since 1.0
     */
    public function log( $message )
    {
        if ( !is_object( $this->logger ) ) {
            $this->logger = new WC_Logger();
        }
        $this->logger->add( 'points-rewards', $message );
    }
    
    /**
     * Returns the points label, singular or plural form, based on $count
     *
     * @param int $count the count
     * @return string the points label
     * @since 0.1
     */
    public function get_points_label( $count, $lowercase = false )
    {
        list( $singular, $plural ) = explode( ':', get_option( 'xt_woopr_points_label', 'Point:Points' ) );
        $label = ( 1 == $count ? $singular : $plural );
        if ( $lowercase ) {
            $label = strtolower( $label );
        }
        return $label;
    }
    
    /**
     * Returns the singular points label
     *
     * @return string the points label
     * @since 0.1
     */
    public function get_singlular_points_label( $lowercase = false )
    {
        return $this->get_points_label( 1, $lowercase );
    }
    
    /**
     * Returns the plural points label
     *
     * @return string the points label
     * @since 0.1
     */
    public function get_plural_points_label( $lowercase = false )
    {
        return $this->get_points_label( 2, $lowercase );
    }
    
    /**
     * The reference to the class that manages the frontend side of the plugin.
     *
     * @return   XT_Woo_Points_Rewards_Public $plugin_frontend
     * @since    1.0.0
     */
    public function frontend()
    {
        return parent::frontend();
    }
    
    /**
     * The reference to the class that manages the backend side of the plugin.
     *
     * @return   XT_Woo_Points_Rewards_Admin $plugin_backend
     * @since    1.0.0
     */
    public function backend()
    {
        return parent::backend();
    }
    
    /**
     * Main XT_Woo_Points_Rewards Instance
     *
     * Ensures only one instance of XT_Woo_Points_Rewards is loaded or can be loaded.
     *
     * @return XT_Woo_Points_Rewards instance
     * @see XT_Woo_Points_Rewards()
     * @since 1.0.00
     * @static
     */
    public static function instance( $plugin )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $plugin );
        }
        return self::$_instance;
    }

}