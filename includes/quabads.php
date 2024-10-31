<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class QuabAds {

	/**
	 * @var      QuabAds_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {
		$this->plugin_name = 'quabads';
		$this->version     = '1.2.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - QuabAds_Loader. Orchestrates the hooks of the plugin.
	 * - QuabAds_Ads_i18n. Defines internationalization functionality.
	 * - QuabAds_Ads_Admin. Defines all hooks for the admin area.
	 * - QuabAds_Ads_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/quabads-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/quabads-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/quabads-admin-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/quabads-anti-adblock.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/quabads-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/quabads-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/quabads-sidebar-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/quabads-header-widget.php';

		$this->loader = new QuabAdsLoader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		$plugin_i18n = new QuabAdsi18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new QuabAdsAdmin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'init', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'init', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'my_custom_favicon' );
		$this->loader->add_action( 'init', $plugin_admin, 'block_quabads_block_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_cleanup_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setup_settings' );
		$this->loader->add_filter( 'mce_buttons', $plugin_admin, 'tiny_mce_register_buttons' );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {
		$plugin_public = new QuabAdsPublic( $this->get_plugin_name(), $this->get_version() );
		add_filter( 'widget_text', 'shortcode_unautop');
		add_filter( 'widget_text', 'do_shortcode', 11);
		$this->loader->add_filter( 'wp_footer', $plugin_public, 'insert_script' );
		//$this->loader->add_action( 'init',$plugin_public,  'anti_adblocker_redirect' );
        $this->loader->add_action( 'init', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'init', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'widgets_init',$plugin_public,  'quabads_custom_sidebar_widgets' );
		$this->loader->add_action( 'widgets_init',$plugin_public,  'quabads_custom_header' );
        $this->loader->add_filter( 'wp_head', $plugin_public, 'add_quabads_header' );
		$this->loader->add_shortcode( 'quabads', $plugin_public,'quabads_shortcode' );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    QuabAds_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
