<?php


namespace Stop_User_Enumeration;
// @TODO options for xforwarded

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'STOP_USER_ENUMERATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STOP_USER_ENUMERATION_PLUGIN_VERSION', '1.3.15' );

// Include the autoloader so we can dynamically include the classes.
require_once( STOP_USER_ENUMERATION_PLUGIN_DIR . 'includes/autoloader.php' );


function run_stop_user_enumeration() {
	/**
	 *  Load freemius SDK
	 */
	$freemius    = new \Stop_User_Enumeration\Includes\Freemius_Config();
	$freemiusSDK = $freemius->init();
	// Signal that SDK was initiated.
	do_action( 'sue_fs_loaded' );


	$freemiusSDK->add_action( 'after_uninstall', array( '\Stop_User_Enumeration\Includes\Uninstall', 'uninstall' ) );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */

	$plugin = new \Stop_User_Enumeration\Includes\Core( $freemiusSDK );
	$plugin->run();

}

run_stop_user_enumeration();


?>
