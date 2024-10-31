<?php
class quabadsHeader extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'quabads',
			__( 'QuabAds Head Banner', 'text_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}
	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = array(
			'title'    => '',
			'select'   => '',
		);
		
		// Parse current settings with defaults
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php // Dropdown ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Select', 'text_domain' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'select' ); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefat">
			<?php
			// Your options array
			$options = array(
				''        => __( 'Select Ad Size', 'text_domain' ),
				'[quabads size="banner"]' => __( 'Banner (468x60)', 'text_domain' ),
				'[quabads size="leaderboard"]' => __( 'Leaderboard (768x90)', 'text_domain' ),
				'[quabads size="half_page"]' => __( 'Half-Page (300x600)', 'text_domain' ),
				'[quabads size="large_leaderboard"]' => __( 'Large Leaderboard (970x90)', 'text_domain' ),
                '[quabads size="billboard"]' => __( 'Billboard (970x250)', 'text_domain' ),
                '[quabads size="large_mobile_banner"]' => __( 'Large Mobile Banner (320x100)', 'text_domain' ),
			);
			// Loop through options and add each one to the select dropdown
			foreach ( $options as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" '. selected( $select, $key, false ) . '>'. $name . '</option>';
			} ?>
			</select>
		</p>

	<?php }
	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']    = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['select']   = isset( $new_instance['select'] ) ? wp_strip_all_tags( $new_instance['select'] ) : '';
		return $instance;
	}
	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

		$select   = isset( $instance['select'] ) ? $instance['select'] : '';
		// WordPress core before_widget hook (always include )
		echo $before_widget;
		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box quabads-header-widget" style="max-width:100%; text-align:center;box-sizing: border-box;overflow: hidden;margin: 0;padding: 0;">';
			// Display widget title if defined
			/*if ( $title ) {
				echo $before_title . $title . $after_title;
			}*/
			// Display select field
			if ( $select ) {
				echo do_shortcode($select);
			}
			// Display something if checkbox is true
			//if ( $checkbox ) {
//				echo '<p>Something awesome</p>';
//			}
		echo '</div>';
		// WordPress core after_widget hook (always include )
		echo $after_widget;
	}
    
}
?>
