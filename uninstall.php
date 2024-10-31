<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
global $wpdb, $wp_version;
// Delete folders and files created by the plugin
$publisher_id = get_option( 'quabads_general_publisher-token');
$UploadDir = wp_upload_dir();
$UploadURL = $UploadDir['basedir'];
$dir = realpath($UploadURL . DIRECTORY_SEPARATOR  . md5(strtr($publisher_id,'us_','123')));
@array_map('unlink', glob("$dir/*"));
@rmdir($dir);

// TODO: remove shortcodes here

$plugin_shortcodes = array('[quabads size="inline_rectangle"]',
                           '[quabads size="large_rectangle"]',
                           '[quabads size="small_square"]',
                           '[quabads size="square"]',
                           '[quabads size="banner"]',
                           '[quabads size="billboard"]',
                           '[quabads size="leaderboard"]',
                           '[quabads size="mobile_large_banner"]',
                           '[quabads size="mobile_leaderboard"]',
                           '[quabads size="skyscraper"]',
                           '[quabads size="wide_skyscraper"]',
                           '[quabads size="half_page"]',
                           '[quabads size="large_leaderboard"]',
                          );

// Delete options incase they were not caught.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'quabads\_%';" );
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'widget\_quabads%';" );

//Remove all shortcodes
foreach($plugin_shortcodes as $key){
    
    //delete block inserted by gutenberg editor
    $block = '
    <!-- wp:quabads/quabads-banner-block -->\r\n
    <!--  -->\r\n
    <!-- /wp:quabads/quabads-banner-block -->';
    $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE( REPLACE( REPLACE( post_content, '<!-- wp:quabads/quabads-banner-block -->', '' ),'<!-- {$key} -->',''),'<!-- /wp:quabads/quabads-banner-block -->','');");
    
    //delete shortcode inserted by classic editor and tinymice
    $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, '{$key}', '' );");
}

// Clear any cached data that has been removed.
wp_cache_flush();
