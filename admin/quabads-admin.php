<?php
/**
 * The admin-specific functionality of the plugin.
 */
class QuabAdsAdmin {

	/**
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * @var QuabAds_Settings_Helper $setting_helper Settings helper instance
	 */
	private $setting_helper;
	/**
	 * Just tinymice buttons for the classic editor.
	 */
	public function tiny_mce_add_buttons( $plugins ) {
	  $plugins['QuabAds'] = plugin_dir_url( __FILE__ ) . 'js/quabads-admin.js';
	  return $plugins;
	}

	public function tiny_mce_register_buttons( $buttons ) {
	  //register buttons with their id.
		array_push($buttons, "green");
		return $buttons;
	}
	
	/**
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name    = $plugin_name;
		$this->version        = $version;
		$this->setting_helper = new QuabAdsSettings( $this->plugin_name );
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/quabads-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		//enqueue TinyMCE plugin script with its ID.
		$plugin_array["quabads_btn_plugin"] =  plugin_dir_url(__FILE__) . "js/quabads-admin.js";
		return $plugin_array;
	}

	/**
	 * Add an settings page to the main menu
	 */
	public function add_settings_page() {
		// TODO: check https://developer.wordpress.org/reference/functions/add_menu_page/#notes about capabilities
        add_menu_page(
            __( 'QuabAds', 'quabads' ), 
            __( 'QuabAds', 'quabads' ), 
            'administrator', 
            $this->plugin_name, array($this,'display_options_page'),
            'none',
			76);
        /*add_submenu_page( 'quabads', 'Settings page title', 'Setup', 'administrator', 'quabads-settings', 'wps_theme_func_settings');*/
       // add_submenu_page( 'quabads', 'QuabAds Clean Up', 'Cleanup', 'administrator', 'quabads-cleanup', array($this,'display_cleanup_page'));
        //add_submenu_page( 'quabads', 'QuabAds FAQs', 'FAQs', 'administrator', 'quabads-faq', array($this,'display_options_page'));

	}
    
    /*function wps_theme_func(){
        echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
        <h2>Theme</h2></div>';
    }
    function wps_theme_func_settings(){
            echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
            <h2>Settings</h2></div>';
    }
    function wps_theme_func_faq(){
            echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
            <h2>FAQ</h2></div>';
    }*/
    /**
	 * Add an settings page to the main menu
	 */
	public function add_cleanup_page() {
		// TODO: check https://developer.wordpress.org/reference/functions/add_menu_page/#notes about capabilities
		
	}

	/**
	 * Render the options page
	 */
	public function display_options_page() {
		include_once 'partials/quabads-admin-display.php';
	}
    
    /**
	 * Render the options page
	 */
	public function display_cleanup_page() {
		include_once 'partials/quabads-admin-cleanup.php';
	}

	/**
	 * Register all plugin settings
	 */
	public function register_setup_settings() {
		$this->setting_helper->add_section( array(
			'id'    => 'general',
			'title' => 'General',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'general',
			'id'          => 'publisher-token',
			'title'       => 'Publisher ID',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/what-is-a-publisher-id" target="_blank">What is a Publisher ID?</a>',
		) );

		$this->setting_helper->add_field( array(
			'section'        => 'general',
			'id'             => 'anti-adblock-ads',
			'title'          => 'Anti-AdBlocker',
			'type'           => QuabAdsSettings::FIELD_TYPE_CHECKBOX,
			'checkbox_label' => 'Enable Anti-Adblocker Mode',
			'description'    => __( 'You can enable anti-adblocker mode to bypass adblokers(Disabling it will make ads display using standard mode).', 'quabads' )
		) );

		// Onclick
		$this->setting_helper->add_section( array(
			'id'    => 'adslots',
			'title' => 'Registered Ad Slots',
		) );        
        
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'banner',
			'title'       => 'Banner (468x60)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#banner" target="_blank">Banner (468x60)</a>',
		) );        
        
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'billboard',
			'title'       => 'Billboard (970x250)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#billboard" target="_blank">Billboard (970x250)</a>',
		) );

		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'inline-rectangle',
			'title'       => 'Inline Rectangle (300x250)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#inline-rectangle" target="_blank">Inline Rectangle (300x250)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'inline-rectangle',
			'title'       => 'Inline Rectangle (300x250)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#inline-rectangle" target="_blank">Inline Rectangle (300x250)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'large-rectangle',
			'title'       => 'Large Rectangle (336x280)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#large-rectangle" target="_blank">Large Rectangle (336x280)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'small-square',
			'title'       => 'Small Square (200x200)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#small-square" target="_blank">Small Square (200x200)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'square',
			'title'       => 'Square (250x250)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#square" target="_blank">Square (250x250)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'leaderboard',
			'title'       => 'Leaderboard (728x90)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#leaderboard" target="_blank">Leaderboard (728x90)</a>',
		) );
        
        $this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'mobile-leaderboard',
			'title'       => 'Mobile Leaderboard (320x50)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#mobile-leaderboard" target="_blank">Mobile Leaderboard (320x50)</a>',
		) );
        
        $this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'mobile-large-banner',
			'title'       => 'Mobile Large Banner (320x100)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#mobile-large-leaderboard" target="_blank">Mobile Large Leaderboard (320x100)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'skyscraper',
			'title'       => 'Skyscraper (120x600)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#skyscraper" target="_blank">Skyscraper (120x600)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'wide-skyscraper',
			'title'       => 'Wide Skyscraper (160x600)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#wide-skyscraper" target="_blank">Wide Skyscraper (160x600)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'half-page',
			'title'       => 'Half-Page Ad (300x600)',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#half-page-ad" target="_blank">Half-Page Ad (300x600)</a>',
		) );
		
		$this->setting_helper->add_field( array(
			'section'     => 'adslots',
			'id'          => 'large-leaderboard',
			'title'       => 'Large Leaderboard (970x90) ',
			'type'        => QuabAdsSettings::FIELD_TYPE_INPUT_TEXT,
			'size'        => 35,
			'description' => '<a href="https://help.quabads.com/topic/adslots#large-leaderboard" target="_blank">Large Leaderboard (970x90) </a>',
		) );

	}
    
    function my_custom_favicon() {
      echo '
        <style>
        .dashicons-cake {
            background-image: url("'.plugin_dir_path( __FILE__ ).'admin/img/icon.png");
            background-repeat: no-repeat;
            background-position: center; 
        }
        </style>
    '; }
    
    function block_quabads_block_init() {
        if ( ! function_exists( 'register_block_type' ) ) {
            // Gutenberg is not active.
            return;
        }
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        wp_register_script(
            'quabads-banner-block',
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_style(
            'quabads-banner-block',
            plugins_url( 'css/editor.css', __FILE__ ),
            array( ),
            filemtime( plugin_dir_path( __FILE__ ) . 'css/editor.css' )
        );

        wp_register_style(
            'quabads-banner-block',
            plugins_url( 'css/style.css', __FILE__ ),
            array( ),
            filemtime( plugin_dir_path( __FILE__ ) . 'css/style.css' )
        );

        register_block_type( 'quabads/quabads-banner-block', array(
            'editor_script' => 'quabads-banner-block',
            'editor_style'  => 'quabads-banner-block',
            'style'         => 'quabads-banner-block',
        ) );
    }



}
