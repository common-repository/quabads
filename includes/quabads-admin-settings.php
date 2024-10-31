<?php

/**
 * Helper functions for registering / rendering settings
 */
class QuabAdsSettings {

	// Field types
	const FIELD_TYPE_CHECKBOX       = 'checkbox';
	const FIELD_TYPE_INPUT_INTEGER  = 'input_integer';
	const FIELD_TYPE_INPUT_TEXT     = 'input_text';

	/**
	 * @var string  $settings_page  The slug-name of the settings page
	 */
	private $settings_page;

	/**
	 * @var string  $settings_prefix    Unique options prefix for plugin
	 */
	private $settings_prefix;

	public function __construct( $settings_page ) {
		$this->settings_page = $settings_page;
		$this->settings_prefix = str_replace('-', '_', $this->settings_page);
	}

	/**
	 * Add settings section to plugin settings page
	 *
	 * @param array $config     Key-value config (id, title)
	 */
	public function add_section( $config ) {
		add_settings_section(
			$this->get_section_id( $config['id'] ),
			__( $config['title'], $this->settings_page ),   // TODO: is it ok for i18n tools?
			array( $this, 'render_section' ),   // TODO: Do we need to configure callback?
			$this->settings_page
		);
	}

	public function render_section() {
		echo '';
	}

	/**
	 * Register setting and setup field rendering / sanitization
	 *
	 * @param array $config     Key-value config (type, id, title, section)
	 */
	public function add_field( $config ) {
		$field_id = $this->get_field_id( $config['section'], $config['id'] );
		$renderer_name = 'render_' . $config['type'];
		$args = array_merge( $config, array(
			'id'        => $field_id,
			'label_for' => $field_id,
			'value'     => $this->get_field_value( $config['section'], $config['id'] ),
		));

		add_settings_field(
			$field_id,
			__( $config['title'], $this->settings_page ),
			array( $this, $renderer_name ),
			$this->settings_page,
			$this->get_section_id( $config['section'] ),
			$args
		);

		register_setting(
			$this->settings_page,
			$field_id,
			$this->get_sanitize_callback( $config['type'] )
		);
	}

	public function render_checkbox( $args ) {
		?>
		<label>
			<input type="checkbox"
			       name="<?php echo $args['id']; ?>"
			       id="<?php echo $args['id']; ?>"
			       <?php checked( $args['value'], 1 ); ?>
				   value="1"
			/>
			<?php _e( $args['checkbox_label'], 'quabads' ) ?>
		</label>
		<?php $this->print_field_description($args); ?>
		<?php
	}

	public function render_input_integer( $args ) {
		$size = array_key_exists('size', $args) ? $args['size'] : 10;
		?>
		<input type="number"
		       name="<?php echo $args['id']; ?>"
		       id="<?php echo $args['id']; ?>"
		       value="<?php echo $args['value']; ?>"
		       size="<?php echo $size; ?>"
		       step="1"
		       min="1"
		/>
		<?php $this->print_field_description($args); ?>
		<?php
	}

	public function render_input_text( $args ) {
		$size = array_key_exists('size', $args) ? $args['size'] : 10;
		?>
		<input type="text"
		       name="<?php echo $args['id']; ?>"
		       id="<?php echo $args['id']; ?>"
		       value="<?php echo $args['value']; ?>"
		       size="<?php echo $size; ?>"
		/>
		<?php $this->print_field_description($args); ?>
		<?php
	}
    
    public function render_button( $args ) {
		$size = array_key_exists('size', $args) ? $args['size'] : 10;
		?>
		<button
		       name="<?php echo $args['id']; ?>"
		       id="<?php echo $args['id']; ?>"
		       value="<?php echo $args['value']; ?>"
		       size="<?php echo $size; ?>"
		>Perform Cleanup</button>
		<?php
	}

	/**
	 * Get field (option) value
	 *
	 * @param int $section_id
	 * @param int $field_id
	 *
	 * @return mixed    Option value
	 */
	public function get_field_value( $section_id, $field_id ) {
		return get_option( $this->get_field_id( $section_id, $field_id ) );
	}

	private function get_section_id( $id ) {
		return sprintf( '%s_%s', $this->settings_prefix, $id );
	}

	private function get_field_id( $section_id, $field_id ) {
		return sprintf( '%s_%s_%s', $this->settings_prefix, $section_id, $field_id );
	}

	private function get_sanitize_callback( $type ) {
		if ( $type === self::FIELD_TYPE_CHECKBOX ) {
			return 'intval';
		}
		return '';
	}

	private function print_field_description( $args ) {
		if ( array_key_exists( 'description', $args ) ) {
			?>
			<p class="description"><?php echo $args['description']; ?></p>
			<?php
		}
	}
    
}
