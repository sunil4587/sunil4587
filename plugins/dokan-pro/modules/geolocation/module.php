<?php

namespace WeDevs\DokanPro\Modules\GeoLocation;

/**
 * Dokan_Live_Search class
 *
 * @class Dokan_Live_Search The class that holds the entire Dokan_Live_Search plugin
 */
class Module {

    /**
     * Constructor for the Dokan_Live_Search class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        add_action( 'plugins_loaded', function () {
            include dirname( __FILE__ ) . '/geolocation.php';
        } );
    }

}
