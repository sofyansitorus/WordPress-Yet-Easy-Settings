<?php
/**
 * The file core settings class
 *
 * @link       https://github.com/sofyansitorus
 * @since      0.0.1
 *
 * @package    Wpyes
 */

/**
 * Wpyes class.
 *
 * WordPress Yet Easy Settings class is PHP class for easy to build WordPress admin settings page.
 *
 * @version    0.0.1
 * @since      0.0.1
 * @package    Wpyes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Wpyes {

	/**
	 * Settings prefix.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $prefix;

	/**
	 * Auto prefix setting state.
	 *
	 * @since 0.0.1
	 * @var boolean
	 */
	private $auto_prefix;

	/**
	 * Settings data array.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $settings = array();

	/**
	 * Settings tabs array.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $settings_tabs = array();

	/**
	 * Settings sections array.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $settings_sections = array();

	/**
	 * Settings fields array.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $settings_fields = array();

	/**
	 * Settings menu page
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $admin_menus = array();

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 * @param string  $prefix Setting prefix parameter.
	 * @param boolean $auto_prefix If true, the setting field key will be prepended with prefix parameter. Default true.
	 */
	public function __construct( $prefix, $auto_prefix = true ) {
		$this->prefix      = $prefix;
		$this->auto_prefix = $auto_prefix;
	}

	/**
	 * Get settings tabs, sections and fields as associative array array
	 *
	 * @since 0.0.1
	 * @param string $menu_slug Current menu page loaded.
	 * @return array All settings data array.
	 */
	private function get_settings( $menu_slug = null ) {

		if ( $menu_slug && is_string( $menu_slug ) ) {
			$settings = array();
			foreach ( $this->settings as $tab_key => $tab ) {
				if ( $tab['menu_slug'] === $menu_slug || ( empty( $tab['menu_slug'] ) && $menu_slug === $this->prefix ) ) {
					$settings[ $tab_key ] = $tab;
				}
			}
			return apply_filters( 'wpyes_settings_' . $menu_slug, $settings );
		}

		return apply_filters( 'wpyes_settings', $this->settings );

	}

	/**
	 * Normalize settings tab property.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new tab object. Default empty array.
	 *  @type string          $id              ID for the setting tab. Default empty.
	 *  @type string          $label           Label for the setting tab. Default empty.
	 *  @type array           $sections        Setting sections that will be linked to the tab. Default array().
	 *  @type integer         $priority        Setting tab position priority. Default 10.
	 *  @type string          $menu_slug       Menu slug that will be used to show the tab. Default empty.
	 * }
	 * @return array Normalized setting tab property.
	 */
	private function normalize_tab( $args ) {

		$args = wp_parse_args(
			$args, array(
				'id'        => '',
				'label'     => '',
				'sections'  => array(),
				'priority'  => 10,
				'menu_slug' => '',
			)
		);

		if ( empty( $args['label'] ) ) {
			$args['label'] = $this->humanize_slug( $args['id'] );
		}

		return $args;
	}

	/**
	 * Add settings tabs in bulk.
	 *
	 * @since 0.0.1
	 * @param array $tabs Setting tabs data in bulk.
	 */
	public function add_tabs( $tabs ) {
		if ( is_array( $tabs ) ) {
			foreach ( $tabs as $tab ) {
				$this->add_tab( $tab );
			}
		}
	}

	/**
	 * Add settings tab individually.
	 *
	 * @since 0.0.1
	 * @param array $tab Setting tab property.
	 */
	public function add_tab( $tab ) {

		$tab = $this->normalize_tab( $tab );

		if ( ! empty( $tab['id'] ) ) {
			$this->settings_tabs[ $tab['id'] ] = $tab;
		}
	}

	/**
	 * Get settings tabs registered.
	 *
	 * @since 0.0.1
	 * @return array All setting tabs registered.
	 */
	private function get_tabs() {
		$settings_tabs = $this->settings_tabs;
		uasort( $settings_tabs, array( $this, 'sort_by_priority' ) );
		return apply_filters( 'wpyes_settings_tabs', $settings_tabs );
	}

	/**
	 * Render the settings tab
	 *
	 * @since 0.0.1
	 * @param array  $tab Setting tab data.
	 * @param string $tab_key Setting tab id.
	 */
	public function render_tab( $tab, $tab_key ) {
		foreach ( $tab['sections'] as $section_key => $section ) {
			do_settings_sections( $tab_key . '_' . $section_key );
		}
	}

	/**
	 * Normalize settings section property.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new section object. Default empty array.
	 *  @type string          $id              ID for the setting section. Default empty.
	 *  @type string          $label           Label for the setting section. Default empty.
	 *  @type callable        $callback        A callback function that render the setting section.
	 *  @type array           $fields          Setting fields that linked directly to the section. Default array().
	 *  @type string          $tab             Tab ID where whill be the section displayed. Default empty.
	 *  @type integer         $priority        Setting section position priority. Default 10.
	 * }
	 * @return array Normalized setting section property.
	 */
	private function normalize_section( $args ) {
		$defaults = array(
			'id'       => '',
			'title'    => '',
			'callback' => null,
			'fields'   => array(),
			'tab'      => '',
			'priority' => 10,
		);

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Add settings sections in bulk.
	 *
	 * @since 0.0.1
	 * @param array $sections Setting sections data in bulk.
	 */
	public function add_sections( $sections ) {
		if ( is_array( $sections ) ) {
			foreach ( $sections as $section ) {
				$this->add_section( $section );
			}
		}
	}

	/**
	 * Add settings section individually.
	 *
	 * @since 0.0.1
	 * @param array $section Setting section property.
	 */
	public function add_section( $section ) {

		$section = $this->normalize_section( $section );

		if ( ! empty( $section['id'] ) ) {
			$this->settings_sections[ $section['id'] ] = $section;
		}

	}

	/**
	 * Get all settings sections registered.
	 *
	 * @since 0.0.1
	 * @return array All settings sections registered.
	 */
	private function get_sections() {
		$settings_sections = $this->settings_sections;
		uasort( $settings_sections, array( $this, 'sort_by_priority' ) );
		return apply_filters( 'wpyes_settings_sections', $settings_sections );
	}

	/**
	 * Normalize setting field properties
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new field object. Default empty array.
	 *  @type string|callable $type               Type for the setting field or callable to render the setting field. Default 'text'.
	 *  @type string          $id                 ID for the setting field. Default empty.
	 *  @type string          $label              Label for the setting field. Default empty.
	 *  @type string          $desc               Description for the setting field. Default empty.
	 *  @type callable        $callback_before    Callback function that will be called before the setting field rendered. Default empty.
	 *  @type callable        $callback_after     Callback function that will be called after the setting field rendered. Default empty.
	 *  @type callable        $sanitize_callback  Callback function to sanitize setting field value. Default null.
	 *  @type string          $section            Section ID for the setting field. Default empty.
	 *  @type string          $default            Default value for the setting field. Default empty.
	 *  @type array           $options            Setting field input options, a key value pair used for setting field type select, radio, checkbox. Default array().
	 *  @type array           $attrs              Setting field input attributes. Default array().
	 *  @type integer         $priority           Setting field position priority. Default 10.
	 * }
	 */
	private function normalize_field( $args ) {

		$defaults = array(
			'type'              => 'text',
			'id'                => '',
			'label'             => '',
			'desc'              => '',
			'callback_before'   => '',
			'callback_after'    => '',
			'sanitize_callback' => null,
			'section'           => '',
			'default'           => '',
			'options'           => array(),
			'attrs'             => array(),
			'priority'          => 10,
		);

		return wp_parse_args( $args, $defaults );

	}

	/**
	 * Add settings fields.
	 *
	 * @since 0.0.1
	 * @param array $fields Add setting fields in bulk.
	 */
	public function add_fields( $fields ) {

		if ( is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				$this->add_field( $field );
			}
		}

		return $this->get_fields();
	}

	/**
	 * Add settings field individually.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function add_field( $field ) {

		$field = $this->normalize_field( $field );

		if ( ! empty( $field['id'] ) ) {
			$this->settings_fields[ $field['id'] ] = $field;
		}

		return $this->get_fields();
	}

	/**
	 * Get all settings fields registered.
	 *
	 * @since 0.0.1
	 * @return array  All settings fields registered.
	 */
	private function get_fields() {
		$settings_fields = $this->settings_fields;
		uasort( $settings_fields, array( $this, 'sort_by_priority' ) );
		return apply_filters( 'wpyes_settings_fields', $settings_fields );
	}

	/**
	 * Get settings field attribute id.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_id( $field ) {

		$field_id = $field['section'] . '_' . $field['id'];

		return $this->auto_prefix ? $this->prefix . '_' . $field_id : $field_id;
	}

	/**
	 * Get settings field attribute name.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_name( $field ) {
		return $this->auto_prefix ? $this->prefix . '_' . $field['id'] : $field['id'];
	}

	/**
	 * Get settings field value from DB.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_value( $field ) {
		return get_option( $this->get_field_name( $field ), $field['default'] );
	}

	/**
	 * Get settings field attributes.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function render_field_attrs( $field ) {

		switch ( $field['type'] ) {
			case 'text':
			case 'url':
			case 'number':
			case 'password':
			case 'email':
				if ( isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] .= ' regular-text';
				} else {
					$field['attrs']['class'] = 'regular-text';
				}
				break;
			case 'file':
				if ( isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] .= ' regular-text';
				} else {
					$field['attrs']['class'] = 'regular-text';
				}
				$field['attrs']['readonly'] = 'readonly';
				break;
			case 'color':
				if ( isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] .= ' regular-text wp-color-picker-field';
				} else {
					$field['attrs']['class'] = 'regular-text wp-color-picker-field';
				}
				break;
			case 'textarea':
				if ( ! isset( $field['attrs']['rows'] ) ) {
					$field['attrs']['rows'] = '10';
				}

				if ( ! isset( $field['attrs']['cols'] ) ) {
					$field['attrs']['cols'] = '50';
				}

				break;
		}

		unset( $field['attrs']['id'] );
		unset( $field['attrs']['name'] );
		unset( $field['attrs']['value'] );
		unset( $field['attrs']['type'] );
		unset( $field['attrs']['checked'] );
		unset( $field['attrs']['selected'] );

		foreach ( $field['attrs'] as $key => $value ) {
			echo esc_html( $key ) . '="' . esc_attr( $value ) . '" ';
		}
	}

	/**
	 * Render the setting field.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field( $field ) {

		if ( ! empty( $field['callback_before'] ) && is_callable( $field['callback_before'] ) ) {
			call_user_func( $field['callback_before'], $field, $this );
		}

		if ( is_string( $field['type'] ) && method_exists( $this, 'render_field_' . $field['type'] ) ) {
			call_user_func( array( $this, 'render_field_' . $field['type'] ), $field );
		}

		if ( ! is_string( $field['type'] ) && is_callable( $field['type'] ) ) {
			call_user_func( $type, $field, $this );
		}

		if ( ! empty( $field['callback_after'] ) && is_callable( $field['callback_after'] ) ) {
			call_user_func( $field['callback_after'], $field, $this );
		}

	}

	/**
	 * Render the setting field for text.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function render_field_text( $field ) {
		?>
		<input 
		type="<?php echo esc_attr( $field['type'] ); ?>" 
		id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
		name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
		value="<?php echo esc_attr( $this->get_field_value( $field ) ); ?>" 
		<?php $this->render_field_attrs( $field ); ?> />
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for url.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function render_field_url( $field ) {
		$this->render_field_text( $field );
	}

	/**
	 * Render the setting field for number.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function render_field_number( $field ) {
		$this->render_field_text( $field );
	}

	/**
	 * Render the setting field for password.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_password( $field ) {
		$this->render_field_text( $field );
	}

	/**
	 * Render the setting field for email.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_email( $field ) {
		$this->render_field_text( $field );
	}

	/**
	 * Render the setting field for checkbox.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_checkbox( $field ) {
		?>
		<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="off" />
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>">
				<input 
				type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
				value="on" 
				<?php checked( $this->get_field_value( $field ), 'on' ); ?>
				<?php $this->render_field_attrs( $field ); ?> />
				<?php echo esc_html( $field['desc'] ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Render the setting field for multicheckbox.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_multicheckbox( $field ) {
		$value = $this->get_field_value( $field );
		?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]">
					<input 
					type="checkbox" 
					id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" 
					name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>[]" 
					value="<?php echo esc_attr( $option_value ); ?>" 
					<?php checked( in_array( $option_value, $value, true ), true ); ?>
					<?php $this->render_field_attrs( $field ); ?> />
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for radio.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_radio( $field ) {
		$value = $this->get_field_value( $field );
		?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]">
					<input 
					type="radio" 
					id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" 
					name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
					value="<?php echo esc_attr( $option_value ); ?>" 
					<?php checked( $value, $option_value ); ?>
					<?php $this->render_field_attrs( $field ); ?> />
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for select.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_select( $field ) {
		$value = $this->get_field_value( $field );
		?>
		<select id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" <?php $this->render_field_attrs( $field ); ?>>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_attr( $option_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for textarea.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_textarea( $field ) {
		?>
		<textarea 
		id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
		name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
		<?php $this->render_field_attrs( $field ); ?>
		><?php echo esc_textarea( $this->get_field_value( $field ) ); ?></textarea>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for wysiwyg.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_wysiwyg( $field ) {

		$editor_settings = array(
			'teeny'         => true,
			'media_buttons' => false,
			'textarea_name' => $this->get_field_name( $field ),
			'textarea_rows' => 7,
		);

		if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $field['options'] );
		}

		$width = isset( $field['width'] ) ? $field['width'] : '500px';
		?>
		<div style="max-width:<?php echo esc_attr( $width ); ?>;" <?php $this->render_field_attrs( $field ); ?>>
		<?php wp_editor( $this->get_field_value( $field ), $this->get_field_name( $field ), $editor_settings ); ?>
		</div>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for file.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_file( $field ) {
		?>
		<input 
		type="text" 
		id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
		name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
		value="<?php echo esc_attr( $this->get_field_value( $field ) ); ?>" 
		<?php $this->render_field_attrs( $field ); ?> />
		<button type="button" class="button button-browse-file"><span class="dashicons dashicons-upload"></span></button>
		<button type="button" class="button button-remove-file"><span class="dashicons dashicons-trash"></span></button>
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for color.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_color( $field ) {
		?>
		<input 
		type="text" 
		id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
		name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
		value="<?php echo esc_attr( $this->get_field_value( $field ) ); ?>" 
		<?php $this->render_field_attrs( $field ); ?> />
		<?php if ( ! empty( $field['desc'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Initialize and build the settings sections and fileds
	 */
	public function init() {

		$tabs     = $this->get_tabs();
		$sections = $this->get_sections();
		$fields   = $this->get_fields();

		foreach ( $fields as $field_key => $field ) {
			if ( isset( $field['section'] ) && isset( $sections[ $field['section'] ] ) ) {
				$sections[ $field['section'] ]['fields'][ $field_key ] = $field;
			}
		}

		foreach ( $sections as $section_key => $section ) {
			if ( isset( $section['tab'] ) && isset( $tabs[ $section['tab'] ] ) ) {
				$tabs[ $section['tab'] ]['sections'][ $section_key ] = $section;
			}
		}

		$this->settings = $tabs;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_print_footer_scripts', array( $this, 'admin_footer_js' ) );

		add_action( 'admin_init', array( $this, 'register_settings' ), 10 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
	}

	/**
	 * Registers the settings sections and fileds to WordPress
	 */
	public function register_settings() {

		$settings = $this->get_settings();

		foreach ( $settings as $tab_key => $tab ) {
			foreach ( $tab['sections'] as $section_key => $section ) {
				add_settings_section( $section_key, $section['title'], $section['callback'], $tab_key . '_' . $section_key );
				foreach ( $section['fields'] as $field_key => $field ) {
					add_settings_field( $this->get_field_name( $field ), $field['label'], array( $this, 'render_field' ), $tab_key . '_' . $section_key, $section_key, $field );
					register_setting( $this->prefix, $this->get_field_name( $field ), $field['sanitize_callback'] );
				}
			}
			add_action( 'wpyes_setting_tab_' . $tab_key, array( $this, 'render_tab' ), 10, 2 );
		}

	}

	/**
	 * Register new custom admin menu page.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new admin menu object. Default empty array.
	 *  @type string    $method       Method that used to register the menu to WP Admin. Default 'add_menu_page'.
	 *  @type string    $page_title   The text to be displayed in the title tags of the page. Default is prefix defined in constructor.
	 *  @type string    $menu_title   The text to be used for the menu. Default is prefix defined in constructor.
	 *  @type string    $capability   The capability required for this menu to be displayed to the user. Default "manage_options".
	 *  @type string    $menu_slug    Admin menu slug. Default is prefix defined in constructor.
	 *  @type callable  $callback     Callback to render the setting form. Default Wpyes::render_form.
	 *  @type string    $icon_url     URL for admin menu icon. Default empty.
	 *  @type integer   $position     Admin menu priority posisition. Default null.
	 *  @type string    $parent_slug  Parent menu slug if the $method defined is "add_submenu_page". Default empty.
	 * }
	 */
	public function register_admin_menu( $args = array() ) {

		$menu = wp_parse_args(
			$args, array(
				'method'      => 'add_menu_page',
				'capability'  => 'manage_options',
				'menu_slug'   => $this->prefix,
				'menu_title'  => $this->humanize_slug( $this->prefix ),
				'page_title'  => $this->humanize_slug( $this->prefix ),
				'callback'    => array( $this, 'render_form' ),
				'icon_url'    => '',
				'position'    => null,
				'parent_slug' => '',
			)
		);

		$this->admin_menus[ $menu['menu_slug'] ] = $menu;
	}

	/**
	 * Register admin menus to the WP Admin.
	 *
	 * This function is hooked into admin_menu to affect admin only.
	 */
	public function admin_menu() {

		// Register default admin menu if there is no custom menu registered yet.
		if ( empty( $this->admin_menus ) ) {
			$this->register_admin_menu(
				array(
					'menu_slug' => $this->prefix,
				)
			);
		}

		foreach ( $this->admin_menus as $key => $admin_menu ) {
			switch ( $admin_menu['method'] ) {
				case 'add_submenu_page':
					if ( ! empty( $admin_menu['parent_slug'] ) ) {
						call_user_func(
							$admin_menu['method'],
							$admin_menu['parent_slug'],
							$admin_menu['page_title'],
							$admin_menu['menu_title'],
							$admin_menu['capability'],
							$admin_menu['menu_slug'],
							$admin_menu['callback']
						);
					}
					break;

				default:
					call_user_func(
						$admin_menu['method'],
						$admin_menu['page_title'],
						$admin_menu['menu_title'],
						$admin_menu['capability'],
						$admin_menu['menu_slug'],
						$admin_menu['callback'],
						$admin_menu['icon_url'],
						$admin_menu['position']
					);
					break;
			}
		}

	}

	/**
	 * Get current menu page displayed.
	 */
	private function current_admin_menu() {
		$page = isset( $_GET['page'] ) ? $_GET['page'] : $this->prefix;
		return isset( $this->admin_menus[ $page ] ) ? $this->admin_menus[ $page ] : false;
	}

	/**
	 * Render the settings form.
	 */
	public function render_form() {

		$admin_menu = $this->current_admin_menu();

		if ( ! $admin_menu ) {
			return;
		}

		$settings = $this->get_settings( $admin_menu['menu_slug'] );

		?>
		<div class="wrap">
			<h1><?php echo esc_html( $admin_menu['page_title'] ); ?></h1>
			<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $settings as $tab_key => $tab ) :
			?>
			<a href="#<?php echo esc_attr( $tab_key ); ?>" class="nav-tab" id="<?php echo esc_attr( $tab_key ); ?>-tab"><?php echo esc_html( $tab['label'] ); ?></a>
			<?php
			endforeach;
			?>
			</h2>
			<form method="post" action="options.php">
				<div class="metabox-holder">
					<?php foreach ( $settings as $tab_key => $tab ) { ?>
						<div id="<?php echo esc_attr( $tab['id'] ); ?>" class="group" style="display: none;">
							<?php
							do_action( 'wpyes_setting_tab_before_' . $tab_key, $tab, $tab_key );
							do_action( 'wpyes_setting_tab_' . $tab_key, $tab, $tab_key );
							do_action( 'wpyes_setting_tab_after_' . $tab_key, $tab, $tab_key );
							?>
						</div>
					<?php } ?>
				</div>
				<div style="padding-left: 10px">
					<?php settings_fields( $this->prefix ); ?>
					<?php submit_button(); ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Prints scripts needed to u]initiate Color Picker & Tab element.
	 *
	 * @since 0.0.1
	 */
	public function admin_footer_js() {
		$screen = get_current_screen();

		$slug_match = false;

		$menu_slugs = array_keys( $this->admin_menus );

		foreach ( $menu_slugs as $menu_slug ) {
			if ( strpos( $screen->base, '_' . $menu_slug ) ) {
				$slug_match = $menu_slug;
				break;
			}
		}

		if ( ! $slug_match ) {
			return;
		}
		?>
		<script>
			(function($) {
				$(document).ready(function($) {
					//Initiate Color Picker
					$('.wp-color-picker-field').wpColorPicker();

					// Switches option sections
					$('.group').hide();
					var activetab = '';
					if (typeof(localStorage) != 'undefined') {
						activetab = localStorage.getItem("activetab");
					}
					if (activetab != '' && $(activetab).length) {
						$(activetab).fadeIn();
					} else {
						$('.group:first').fadeIn();
					}
					$('.group .collapsed').each(function() {
						$(this).find('input:checked').parent().parent().parent().nextAll().each(
							function() {
								if ($(this).hasClass('last')) {
									$(this).removeClass('hidden');
									return false;
								}
								$(this).filter('.hidden').removeClass('hidden');
							});
					});

					if (activetab != '' && $(activetab + '-tab').length) {
						$(activetab + '-tab').addClass('nav-tab-active');
					} else {
						$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
					}
					$('.nav-tab-wrapper a').click(function(e) {
						e.preventDefault();
						$('.nav-tab-wrapper a').removeClass('nav-tab-active');
						$(this).addClass('nav-tab-active').blur();
						var clicked_group = $(this).attr('href');
						if (typeof(localStorage) != 'undefined') {
							localStorage.setItem("activetab", $(this).attr('href'));
						}
						$('.group').hide();
						$(clicked_group).fadeIn();
					});

					// Media file browser.
					$('.button-browse-file').on('click', function(e) {
						e.preventDefault();

						var self = $(this);

						// Create the media frame.
						var file_frame = wp.media.frames.file_frame = wp.media({
							multiple: false
						});

						file_frame.on('select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();
							self.closest('td').find('input[type="text"]').val(attachment.url);
						});

						// Finally, open the modal
						file_frame.open();
					});

					// Remove file from input.
					$('.button-remove-file').on('click', function(e) {
						e.preventDefault();
						$(this).closest('td').find('input[type="text"]').val('');
					});
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Enqueue scripts and styles for the setting page.
	 *
	 * @since    0.0.1
	 * @param string $hook Current admin page slug loaded.
	 */
	public function admin_enqueue_scripts( $hook ) {

		$slug_match = false;

		$menu_slugs = array_keys( $this->admin_menus );

		foreach ( $menu_slugs as $menu_slug ) {
			if ( strpos( $hook, '_' . $menu_slug ) ) {
				$slug_match = $menu_slug;
				break;
			}
		}

		if ( ! $slug_match ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', array( 'jquery' ) );
		wp_enqueue_media();

		// Do custom action hook to enqueue custom script and styles.
		do_action( 'wpyes_admin_enqueue_scripts', $slug_match, $hook );
	}

	/**
	 * Sort array by priority
	 *
	 * @since    0.0.1
	 * @param array $a First index of the array.
	 * @param array $b Compared array.
	 * @return integer
	 */
	private function sort_by_priority( $a, $b ) {

		$a = isset( $a['priority'] ) ? (int) $a['priority'] : 10;
		$b = isset( $b['priority'] ) ? (int) $b['priority'] : 10;

		if ( $a === $b ) {
			return 1;
		}

		return ( $a < $b ) ? -1 : 2;

	}

	/**
	 * Humanize slug to make them readable.
	 *
	 * @since 0.0.1
	 * @param string $slug Slug string that will be humanized.
	 * @param string $prefix Prefix that will be removed.
	 * @return string
	 */
	private function humanize_slug( $slug, $prefix = null ) {

		// Remove autoprefixed string.
		if ( ! empty( $prefix ) ) {
			$slug = preg_replace( '/^' . $prefix . '_/', '', $slug );
		}

		// Split slug by dash and underscore as array.
		$words = preg_split( '/(_|-)/', $slug );

		// Check if array words is empty.
		if ( empty( $words ) ) {
			return $slug;
		}

		// Define ignored words.
		$ignores = apply_filters( 'wpyes_humanize_slug_ignores', array( 'a', 'and', 'or', 'to', 'in', 'at', 'in', 'of' ) );

		foreach ( $words as $index => $word ) {

			// Check if the word is ignored.
			if ( in_array( strtolower( $word ), $ignores, true ) ) {
				$words[ $index ] = strtolower( $word );
				continue;
			}

			// Check if the word first character is numeric.
			if ( preg_match( '/^\d/', $word ) ) {
				if ( 2 === strlen( $word ) ) {
					$words[ $index ] = strtoupper( strtolower( $word ) ); // Convert to uppercase for 2 characters word. Ex: 2D, 3D, 4K.
				} else {
					$words[ $index ] = $word;
				}
				continue;
			}

			$words[ $index ] = ucwords( strtolower( $word ) );
		}

		// Return joined words with space.
		return implode( ' ', $words );
	}
}
