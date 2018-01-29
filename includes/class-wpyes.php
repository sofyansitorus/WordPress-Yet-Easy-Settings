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
	 * Settings menu_slug.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Settings menu_slug.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $menu_args;

	/**
	 * Setting field prefix.
	 *
	 * @since 0.0.1
	 * @var boolean
	 */
	private $setting_prefix;

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
	 * Recent tab id registered.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $recent_tab;

	/**
	 * Recent section id registered.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $recent_section;

	/**
	 * Recent field id registered.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $recent_field;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 * @param string $menu_slug Admin page menu slug.
	 * @param array  $menu_args { Optional. Array of properties for the new field object. Default empty array.
	 *  @type string          $method             Built-in WP function used to register menu. Available options: add_menu_page, add_management_page, add_options_page, add_theme_page, add_plugins_page
	 *                                            add_users_page, add_dashboard_page, add_posts_page, add_media_page, add_links_page, add_pages_page, add_comments_page, add_submenu_page
	 *                                            Default 'add_menu_page'.
	 *  @type string          $capability         The capability required for this menu to be displayed to the user. Default 'manage_options'.
	 *  @type string          $page_title         The text to be displayed in the title tags of the page when the menu is selected. Default $menu_slug property.
	 *  @type string          $menu_title         The text to be used for the menu. Default $menu_slug property.
	 *  @type callable        $callback           The function to be called to output the content for this page. Default Wpyes::render_form.
	 *  @type string          $icon_url           The URL to the icon to be used for this menu. Default empty.
	 *  @type integer         $position           The position in the menu order this one should appear. Default null.
	 *  @type string          $parent_slug       The slug name for the parent menu. Required if $method add_submenu_page is used. Default empty.
	 * }
	 * @param string $setting_prefix Setting field prefix. Default empty.
	 */
	public function __construct( $menu_slug, $menu_args = array(), $setting_prefix = '' ) {

		// Set the menu slug property.
		$this->menu_slug = sanitize_key( $menu_slug );

		// Set the menu arguments property.
		$this->menu_args = wp_parse_args(
			$menu_args,
			array(
				'method'      => 'add_menu_page',
				'capability'  => 'manage_options',
				'page_title'  => $this->humanize_slug( $this->menu_slug ),
				'menu_title'  => $this->humanize_slug( $this->menu_slug ),
				'callback'    => array( $this, 'render_form' ),
				'icon_url'    => '',
				'position'    => null,
				'parent_slug' => '',
			)
		);

		// Set the menu arguments property.
		$this->setting_prefix = $setting_prefix;
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
	 * }
	 * @return array Normalized setting tab property.
	 */
	private function normalize_tab( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'       => '',
				'label'    => '',
				'sections' => array(),
				'priority' => 10,
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
			$this->recent_tab      = $tab['id'];
			$this->settings_tabs[] = $tab;
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
		return apply_filters( $this->menu_slug . '_settings_tabs', $settings_tabs );
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
			do_settings_sections( $this->menu_slug . '_' . $tab_key . '_' . $section_key );
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
	 *  @type integer         $priority        Setting section position priority. Default 10.
	 *  @type string          $tab             Tab ID where whill be the section displayed. Default empty.
	 * }
	 * @return array Normalized setting section property.
	 */
	private function normalize_section( $args ) {
		$defaults = array(
			'id'       => '',
			'title'    => '',
			'callback' => null,
			'fields'   => array(),
			'priority' => 10,
			'tab'      => ! is_null( $this->recent_tab ) ? $this->recent_tab : '',
		);

		if ( empty( $args['title'] ) && ! is_bool( $args['title'] ) ) {
			$args['title'] = $this->humanize_slug( $args['id'] );
		}

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
			$this->recent_section      = $section['id'];
			$this->settings_sections[] = $section;
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
		return apply_filters( $this->menu_slug . '_settings_sections', $settings_sections );
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
			'default'           => '',
			'options'           => array(),
			'attrs'             => array(),
			'priority'          => 10,
			'section'           => ! is_null( $this->recent_section ) ? $this->recent_section : '',
			'tab'               => ! is_null( $this->recent_tab ) ? $this->recent_tab : '',
		);

		if ( empty( $args['label'] ) && ! is_bool( $args['label'] ) ) {
			$args['label'] = $this->humanize_slug( $args['id'] );
		}

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
		$this->settings_fields[] = $this->normalize_field( $field );
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
		return apply_filters( $this->menu_slug . '_settings_fields', $settings_fields );
	}

	/**
	 * Get settings field attribute id.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_id( $field ) {
		return $this->setting_prefix ? $this->setting_prefix . '_' . $field['section'] . '_' . $field['id'] : $field['section'] . '_' . $field['id'];
	}

	/**
	 * Get settings field attribute name.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_name( $field ) {
		return $this->setting_prefix ? $this->setting_prefix . '_' . $field['id'] : $field['id'];
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
	 * Get settings tabs, sections and fields as associative array array
	 *
	 * @since 0.0.1
	 * @return array All settings data array.
	 */
	private function get_settings() {
		return apply_filters( $this->menu_slug . '_settings', $this->settings );
	}

	/**
	 * Initialize and build the settings sections and fileds
	 */
	public function init() {

		$settings = array();
		$tabs     = $this->get_tabs();
		$sections = $this->get_sections();
		$fields   = $this->get_fields();

		if ( empty( $tabs ) ) {
			$this->add_tab(
				array(
					'id' => $this->menu_slug,
				)
			);
			$tabs = $this->get_tabs();
		}

		foreach ( $tabs as $tab ) {
			$settings[ $tab['id'] ] = $tab;
		}

		foreach ( $sections as $section ) {
			if ( ! isset( $section['tab'] ) ) {
				continue;
			}
			$settings[ $section['tab'] ]['sections'][ $section['id'] ] = $section;
		}

		foreach ( $fields as $field_key => $field ) {
			if ( ! isset( $field['tab'] ) || ! isset( $field['section'] ) ) {
				continue;
			}
			$settings[ $field['tab'] ]['sections'][ $field['section'] ]['fields'][ $field['id'] ] = $field;
		}

		$this->settings = $settings;

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
				$section_unique_id = $this->menu_slug . '_' . $tab_key . '_' . $section_key;
				add_settings_section( $section_unique_id, $section['title'], $section['callback'], $section_unique_id );
				foreach ( $section['fields'] as $field_key => $field ) {
					add_settings_field( $this->get_field_name( $field ), $field['label'], array( $this, 'render_field' ), $section_unique_id, $section_unique_id, $field );
					register_setting( $this->menu_slug, $this->get_field_name( $field ), $field['sanitize_callback'] );
				}
			}
			add_action( $this->menu_slug . '_setting_tab_' . $tab_key, array( $this, 'render_tab' ), 10, 2 );
		}
	}

	/**
	 * Register admin menus to the WP Admin.
	 *
	 * This function is hooked into admin_menu to affect admin only.
	 */
	public function admin_menu() {

		switch ( $this->menu_args['method'] ) {
			case 'add_submenu_page':
				if ( ! empty( $this->menu_args['parent_slug'] ) ) {
					call_user_func(
						$this->menu_args['method'],
						$this->menu_args['parent_slug'],
						$this->menu_args['page_title'],
						$this->menu_args['menu_title'],
						$this->menu_args['capability'],
						$this->menu_slug,
						$this->menu_args['callback']
					);
				}
				break;

			default:
				call_user_func(
					$this->menu_args['method'],
					$this->menu_args['page_title'],
					$this->menu_args['menu_title'],
					$this->menu_args['capability'],
					$this->menu_slug,
					$this->menu_args['callback'],
					$this->menu_args['icon_url'],
					$this->menu_args['position']
				);
				break;
		}
	}

	/**
	 * Render the settings form.
	 */
	public function render_form() {

		$settings = $this->get_settings();
		?>
		<div class="wrap">
			<h1><?php echo esc_html( $this->menu_args['page_title'] ); ?></h1>
			<h2 class="nav-tab-wrapper">
			<?php foreach ( $settings as $tab_key => $tab ) : ?>
			<a href="#<?php echo esc_attr( $tab_key ); ?>" class="nav-tab" id="<?php echo esc_attr( $tab_key ); ?>-tab"><?php echo esc_html( $tab['label'] ); ?></a>
			<?php endforeach; ?>
			</h2>
			<form method="post" action="options.php">
				<div class="metabox-holder">
					<?php foreach ( $settings as $tab_key => $tab ) : ?>
						<div id="<?php echo esc_attr( $tab['id'] ); ?>" class="group">
							<?php do_action( $this->menu_slug . '_setting_tab_before_' . $tab_key, $tab, $tab_key ); ?>
							<?php do_action( $this->menu_slug . '_setting_tab_' . $tab_key, $tab, $tab_key ); ?>
							<?php do_action( $this->menu_slug . '_setting_tab_after_' . $tab_key, $tab, $tab_key ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<div style="padding-left: 10px">
					<?php settings_fields( $this->menu_slug ); ?>
					<?php submit_button(); ?>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Print scripts needed to initiate Color Picker & Tab element.
	 *
	 * @since 0.0.1
	 */
	public function admin_footer_js() {
		$screen = get_current_screen();
		if ( ! strpos( $screen->base, '_' . $this->menu_slug ) ) {
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

		// Do custom action hook to print scripts needed.
		do_action( $this->menu_slug . '_admin_footer_js', $screen, $this );
	}

	/**
	 * Enqueue scripts and styles for the setting page.
	 *
	 * @since    0.0.1
	 * @param string $hook Current admin page slug loaded.
	 */
	public function admin_enqueue_scripts( $hook ) {

		if ( ! strpos( $hook, '_' . $this->menu_slug ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', array( 'jquery' ) );
		wp_enqueue_media();

		// Do custom action hook to enqueue custom script and styles.
		do_action( $this->menu_slug . '_admin_enqueue_scripts', $hook, $this );
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
			return 0;
		}

		return ( $a < $b ) ? -1 : 1;
	}

	/**
	 * Humanize slug to make them readable.
	 *
	 * @since 0.0.1
	 * @param string $slug Slug string that will be humanized.
	 * @return string
	 */
	private function humanize_slug( $slug ) {

		// Split slug by dash and underscore as array.
		$words = preg_split( '/(_|-)/', $slug );

		// Check if array words is empty.
		if ( empty( $words ) ) {
			return $slug;
		}

		// Define ignored words.
		$ignores = apply_filters( $this->menu_slug . '_humanize_slug_ignores', array( 'a', 'and', 'or', 'to', 'in', 'at', 'in', 'of' ) );

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
