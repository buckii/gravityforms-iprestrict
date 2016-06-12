<?php
/**
 * Plugin Name: Gravity Forms IP Restrict
 * Plugin URI: https://buckeyeinteractive.com
 * Description: Add a setting for restrcting gravity forms to only specific IP addresses
 * Version: 1.0
 * Author: Buckeye Interactive
 * Author URI: https://buckeyeinteractive.com
 * License: GPL2
 *
 * @package Gravity Forms IP Restrict
 * @author Buckeye Interactive
 */

define( 'GF_IP_RESTRICT_VERSION', '1.0' );

class GF_IP_Restrict_Bootstrap {

	/**
	 * Class constructor
	 */
	public function __construct() {
		// This would be a good place to call methods you put in this class
	}

	// Add methods to register custom post types, taxonomies, etc. here

	/**
	 * Bootstrap the plugin
	 * @return void
	 */
	function init() {
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        require_once( 'class-gfiprestrict.php' );

        GFAddOn::register( 'GFIPRestrict' );

		return;
	}

}

add_action( 'gform_loaded', array( 'GF_IP_Restrict_Bootstrap', 'init' ), 5 );

function gf_ip_restrict() {
    return GFIPRestrict::get_instance();
}