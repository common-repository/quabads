<?php

class QuabAdsActivator {

	public static function activate() {
        global $wpdb, $wp_version;
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
        //comment all shortcodes
        //It won't run immediately after installation. Only helpful when 
        //I know, it is taxing to the db but which is better? mmmh!
        foreach($plugin_shortcodes as $key){
            //unhide shortcodes inserted by both editors and tinymice
            $wpdb->query("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content,'<!-- {$key} -->',  '{$key}' );");
        }
	}

}
