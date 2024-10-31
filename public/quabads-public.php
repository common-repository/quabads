<?php

/**
 * The public-facing functionality of the plugin.
 */
class QuabAdsPublic {

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
	 * @var QuabAds_Settings_Helper $setting_helper Settings helper instance
	 */
	private $anti_adblock_helper;

	/**
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name    = $plugin_name;
		$this->version        = $version;
		$this->setting_helper = new QuabAdsSettings( $this->plugin_name );
		$this->anti_adblock_helper = new QuabAdsAntiAdblock( null, $this->setting_helper->get_field_value( 'general', 'publisher-token' ));
	}
	
	public function quabads_shortcode( $atts ) {
		$a = shortcode_atts( array(
			'size' => 'inline-rectangle',
		), $atts );
		$adslot_name = str_replace('_','-',$atts["size"]);
		return $this->insert_script($adslot_name);
	}
	
    public function customRead($url){
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$contents = curl_exec($ch);
		if (curl_errno($ch)) {
		  //echo curl_error($ch);
		  //echo "\n<br />";
		  $contents = '';
		} else {
		  curl_close($ch);
		}
		if (!is_string($contents) || !strlen($contents)) {
		//echo "Failed to get contents.";
		$contents = '';
		}
		return $contents;
	}
    /**
	 * Primary functionality - registers both widgets.
	 */
	public function quabads_custom_sidebar_widgets() {
		register_widget( 'quabadsSideBarWidget' );
		add_action( 'widgets_init', 'custom_widgets' );
	}
    	
    public function sanitize_redirect_url( $url ) {
		$clean_url = '';
		$scheme = parse_url( $url, PHP_URL_SCHEME );
		$host = untrailingslashit( parse_url( $url, PHP_URL_HOST ) );
		if ( $scheme && $host ) {
			$current_host = untrailingslashit( parse_url( home_url(), PHP_URL_HOST ) );
			if ( $host !== $current_host ) {
				$path = (string) parse_url( $url, PHP_URL_PATH );
				$clean_url = "{$scheme}://{$host}{$path}";
			}
		}

		return $clean_url;
	}
	/**
	 * Insert ad
	 */
	public function insert_script($adslot_name) {
		if ($this->setting_helper->get_field_value( 'general', 'anti-adblock-ads' )) {
			//Get code for anti-adblock ads
			return $this->get_standard_script( $adslot_name );
		} else {
			//Get standard code for ads
			return $this->get_standard_script( $adslot_name );
		}
	}
	/**
	 * Get standard tag
	 *
	 * @param   string $format
	 * @return  string
	 */
	private function get_standard_script( $adslot_name ) {
		$is_enabled = $this->setting_helper->get_field_value( 'general', 'anti-adblock-ads' );
		$publisher_id = $this->setting_helper->get_field_value( 'general', 'publisher-token' );
		$adslot_id   = $this->setting_helper->get_field_value( 'adslots', $adslot_name );

		if ( ! $is_enabled && ( ! empty( $publisher_id ) && ! empty($adslot_id)) ) {
			return $this->get_standard_script_template( $publisher_id , $adslot_id );
		}else if($is_enabled && ( ! empty( $publisher_id ) && ! empty($adslot_id)) ){
			return $this->get_anti_ad_blocker_script_template( $publisher_id , $adslot_id );
		}

		return '';
	}

	/**
	 * Get template for standard tag
	 *
	 * @param string $format
	 * @return string
	 */
	private function get_standard_script_template( $publisher_id, $adslot_id ) {
		return '<ins  class="quabads-slot" data-slot-id="'.$adslot_id.'" data-pub-id="'.$publisher_id.'"></ins>';
	}
	/**
	 * Get template for anti adblocker tag
	 *
	 * @param string $format
	 * @return string
	 */
	private function get_anti_ad_blocker_script_template( $publisher_id, $adslot_id ) {
        /*$anti_adblock = new QuabAdsAntiAdblock($adslot_id,$publisher_id);
        return $anti_adblock->displayAd($adslot_id);*/
        return '<ins  class="quabads-slot" data-slot-id="'.$adslot_id.'" data-pub-id="'.$publisher_id.'"></ins>';
	}
    
    public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/quabads-head.css', array(), $this->version, 'all' );
	}
    
    public function enqueue_scripts() {
		/*wp_enqueue_script( $this->plugin_name,'https://adshop.quabads.com/js/quabadsadgen.js',array(),null,false);*/
        $is_enabled = $this->setting_helper->get_field_value( 'general', 'anti-adblock-ads' );
		$publisher_id = $this->setting_helper->get_field_value( 'general', 'publisher-token' );
		if ( ! $is_enabled && ( ! empty( $publisher_id )) ) {
			wp_enqueue_script( $this->plugin_name,'https://adshop.quabads.com/js/quabadsadgen.js',array(),null,false);
		}else if($is_enabled && ( ! empty( $publisher_id )) ){
            $UploadDir = wp_upload_dir();
            $UploadURL = $UploadDir['baseurl'];
            $dir = $UploadURL . "/" . md5(strtr($publisher_id,'us_','123'));
            $this->anti_adblock_helper->findTmpDir();
            $this->anti_adblock_helper->requestServiceWorkers();
			wp_enqueue_script( $this->plugin_name,$dir.'/'.$this->anti_adblock_helper->adjsFile,array(),null,false);
		}
	}
    
    public function quabads_custom_header()
    {
        register_sidebar( array(
        'name' => 'QuabAds Header',
        'id' => 'quabads_header_position',
        'description' => __( 'A banner ad space for your site header', 'QuabAds' ),
        'before_widget' => '<div id="QuabAdsWidget" class="quabads_widget">',
        'after_widget' => "</div>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
        ) );
    }
    
    public function add_quabads_header(){
        if ( is_active_sidebar( 'quabads_header_position' ) ) :?>
        <div id="header-widget-area" class="custom-widget-area widget-area" role="complementary">
        <?php dynamic_sidebar( 'quabads_header_position' ); ?>
        </div>
        <?php endif;
    }

}
