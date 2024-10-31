<?php
   /*
   Plugin Name: QuabAds
   Plugin URI: http://www.quabads.com
   description: This plugin is designed for those who want to monetize their websites through advertisements.
   Version: 1.2.1
   Author: QuabAds
   Author URI: http://quabads.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       quabads
   */
?>
<?php
/*
* If this file is called directly, abort.
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_quabads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/quabads-activator.php';
	QuabAdsActivator::activate();
}

function deactivate_quabads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/quabads-deactivator.php';
	QuabAdsDeactivator::deactivate();    // TODO: deregister options
}

register_activation_hook( __FILE__, 'activate_quabads' );
register_deactivation_hook( __FILE__, 'deactivate_quabads' );

require plugin_dir_path( __FILE__ ) . 'includes/quabads.php';

function run_quabads() {
	$plugin = new QuabAds();
	$plugin->run();
}
run_quabads();
?>
