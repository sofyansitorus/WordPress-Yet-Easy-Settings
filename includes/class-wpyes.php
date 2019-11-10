<?php
/**
 * Core file for Wpyes Class.
 *
 * @link       https://github.com/sofyansitorus
 * @since      0.0.1
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
	 * Admin menu slug.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Admin menu arguments.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $menu_args;

	/**
	 * Setting prefix.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $setting_prefix;

	/**
	 * Settings data.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $settings = array();

	/**
	 * Populated settings data.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $settings_populated = array();

	/**
	 * Setting errors data.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $errors = array();

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
	 * Admin screen help_tabs.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	private $help_tabs = array();

	/**
	 * Admin screen action buttons.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $buttons = array();

	/**
	 * If set to true errors will not be shown if the settings page has
	 * already been submitted.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $hide_on_update = false;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 * @param string $menu_slug        The slug name to refer to this menu (should be unique).
	 * @param array  $menu_args        { Optional. Array of properties for the new admin menu object. Default empty array.
	 *  @type string     $method               Built-in WP function used to register menu. Available options: add_menu_page, add_management_page, add_options_page,
	 *                                         add_theme_page, add_plugins_page, add_users_page, add_dashboard_page, add_posts_page, add_media_page, add_links_page,
	 *                                         add_pages_page, add_comments_page, add_submenu_page. Default 'add_menu_page'.
	 *  @type string     $capability           Capability required for this menu to be displayed to the user. Default 'manage_options'.
	 *  @type string     $page_title           Text to be displayed in the title tags of the page when the menu is selected. Default $menu_slug property.
	 *  @type string     $menu_title           Text to be used for the menu. Default $menu_slug property.
	 *  @type callable   $callback             Function to be called to output the content for this page. Default Wpyes::render_form.
	 *  @type string     $icon_url             URL to the icon to be used for this menu. Used when $method is 'add_menu_page'. Default empty.
	 *  @type integer    $position             Position in the menu order this one should appear. Used when $method is 'add_menu_page'. Default null.
	 *  @type string     $parent_slug          Slug name for the parent menu. Required if $method is 'add_submenu_page'. Default empty.
	 * }
	 * @param string $setting_prefix   Setting field prefix. This will affect you how you to get the option value. If not empty, the $setting_prefix would be
	 *                                 prepended when getting option value. Example: If $setting_prefix = 'wpyes', to get option value for setting id 'example_1'
	 *                                 is get_option('wpyes_example_1'). Default empty.
	 */
	public function __construct( $menu_slug, $menu_args = array(), $setting_prefix = '' ) {

		// Set the menu slug property.
		$this->menu_slug = sanitize_key( $menu_slug );

		// Set the menu arguments property.
		$menu_args = wp_parse_args(
			$menu_args,
			array(
				'method'      => 'add_menu_page',
				'capability'  => 'manage_options',
				'page_title'  => '',
				'menu_title'  => '',
				'callback'    => array( $this, 'render_form' ),
				'icon_url'    => '',
				'position'    => null,
				'parent_slug' => '',
			)
		);

		// Set page_title if empty and not false.
		if ( empty( $menu_args['page_title'] ) && ! is_bool( $menu_args['page_title'] ) ) {
			$menu_args['page_title'] = $this->humanize_slug( $this->menu_slug );
		}

		// Set menu_title if empty and not false.
		if ( empty( $menu_args['menu_title'] ) && ! is_bool( $menu_args['menu_title'] ) ) {
			$menu_args['menu_title'] = $this->humanize_slug( $this->menu_slug );
		}

		$this->menu_args = $menu_args;

		// Set the menu arguments property.
		$this->setting_prefix = trim( $setting_prefix, '_' );
	}

	/**
	 * Normalize settings tab property.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new tab object.
	 *  @type string          $id              ID for the setting tab. Default empty.
	 *  @type string          $label           Label for the setting tab. Default empty.
	 *  @type array           $sections        Setting sections that will be linked to the tab. Default array().
	 *  @type integer         $position        Setting tab position. Higher will displayed last. Default 10.
	 *  @type callable        $callback        Callable function to be called to render output the tab content. Default Wpyes::render_tab.
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
				'position' => 10,
				'callback' => '',
			)
		);

		// Create label if empty and not false.
		if ( empty( $args['label'] ) && ! is_bool( $args['label'] ) ) {
			$args['label'] = $this->humanize_slug( $args['id'] );
		}

		// Add default callback to render tab content.
		if ( empty( $args['callback'] ) || ! is_callable( $args['callback'] ) ) {
			$args['callback'] = array( $this, 'render_tab' );
		}

		return $args;
	}

	/**
	 * Register settings tabs in bulk.
	 *
	 * @since 0.0.1
	 * @param array $tabs Indexed array of settings tab property.
	 */
	public function add_tabs( $tabs ) {
		if ( $tabs && is_array( $tabs ) ) {
			foreach ( $tabs as $tab ) {
				$this->add_tab( $tab );
			}
		}
	}

	/**
	 * Register settings tab.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new tab object.
	 *  @type string          $id              ID for the setting tab. Default empty.
	 *  @type string          $label           Label for the setting tab. Default empty.
	 *  @type array           $sections        Setting sections that will be linked to the tab. Default array().
	 *  @type integer         $position        Setting tab position. Higher will displayed last. Default 10.
	 * }
	 */
	public function add_tab( $args ) {
		$args = $this->normalize_tab( $args );
		if ( ! empty( $args['id'] ) ) {
			$this->settings[] = array(
				'type' => 'tab',
				'data' => $args,
			);
		}
	}

	/**
	 * Render settings tab.
	 *
	 * @since 0.0.1
	 * @param array $tab Setting tab property.
	 */
	public function render_tab( $tab ) {
		foreach ( $tab['sections'] as $section_id => $section ) {
			do_settings_sections( $this->get_section_unique_id( $section ) );
		}
	}

	/**
	 * Normalize settings section property.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new section object.
	 *  @type string          $id              ID for the setting section. Default empty.
	 *  @type string          $label           Label for the setting section. Default empty.
	 *  @type callable        $callback        A callback function that render the setting section.
	 *  @type array           $fields          Setting fields that linked directly to the section. Default array().
	 *  @type integer         $position        Setting section position. Higher will displayed last. Default 10.
	 *  @type string          $tab             Tab ID where whill be the section displayed. Default empty.
	 * }
	 * @return array Normalized setting section property.
	 */
	private function normalize_section( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'       => '',
				'title'    => '',
				'callback' => null,
				'fields'   => array(),
				'position' => 10,
				'tab'      => '',
			)
		);

		return $args;
	}

	/**
	 * Register settings sections in bulk.
	 *
	 * @since 0.0.1
	 * @param array $sections Indexed array of settings section property.
	 */
	public function add_sections( $sections ) {
		if ( $sections && is_array( $sections ) ) {
			foreach ( $sections as $section ) {
				$this->add_section( $section );
			}
		}
	}

	/**
	 * Register settings section.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new section object.
	 *  @type string          $id              ID for the setting section. Default empty.
	 *  @type string          $label           Label for the setting section. Default empty.
	 *  @type callable        $callback        A callback function that render the setting section.
	 *  @type array           $fields          Setting fields that linked directly to the section. Default array().
	 *  @type integer         $position        Setting section position. Higher will displayed last. Default 10.
	 *  @type string          $tab             Tab ID where whill be the section displayed. Default empty.
	 * }
	 */
	public function add_section( $args ) {
		$args = $this->normalize_section( $args );
		if ( ! empty( $args['id'] ) ) {
			$this->settings[] = array(
				'type' => 'section',
				'data' => $args,
			);
		}
	}

	/**
	 * Get settings section unique ID.
	 *
	 * @since 0.0.1
	 * @param array $section Setting section property.
	 * @return string Unique ID of section object.
	 */
	private function get_section_unique_id( $section ) {
		return $this->menu_slug . '_' . $section['tab'] . '_' . $section['id'];
	}

	/**
	 * Normalize setting field properties
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new field object.
	 *  @type string|callable $type               Type for the setting field or callable function to render the setting field. Valid values are 'url', 'number', 'decimal',
	 *                                            'password', 'email', 'checkbox', 'multicheckbox', 'radio', 'select', 'multiselect', 'textarea', 'wysiwyg', 'file'
	 *                                            Default 'text'.
	 *  @type string          $data_type          The type of data associated with this setting. Valid values are 'string', 'boolean', 'integer', and 'number'.
	 *                                            Default 'string'.
	 *  @type string          $id                 ID for the setting field. Default empty.
	 *  @type string          $label              Label for the setting field. Default empty.
	 *  @type string          $description        Description for the setting field. Default empty.
	 *  @type callable        $callback_before    Callback function that will be called before the setting field rendered. Default empty.
	 *  @type callable        $callback_after     Callback function that will be called after the setting field rendered. Default empty.
	 *  @type callable        $sanitize_callback  Callback function to sanitize setting field value. Default null.
	 *  @type string          $default            Default value for the setting field. Default empty.
	 *  @type array           $options            Setting field input options, a key value pair used for setting field type select, radio, checkbox. Default array().
	 *  @type array           $attrs              Setting field input attributes. Default array().
	 *  @type integer         $position           Setting field position. Higher will displayed last. Default 10.
	 *  @type string          $tab                Tab ID for the setting field. Default empty.
	 *  @type string          $section            Section ID for the setting field. Default empty.
	 *  @type bool            $required           Set the setting field is required. Default false.
	 *  @type bool            $show_in_rest       Whether data associated with this setting should be included in the REST API. Default false.
	 * }
	 */
	private function normalize_field( $args ) {

		$args = wp_parse_args(
			$args,
			array(
				'type'              => 'text',
				'data_type'         => 'string',
				'id'                => '',
				'label'             => '',
				'description'       => '',
				'callback_before'   => '',
				'callback_after'    => '',
				'sanitize_callback' => null,
				'default'           => '',
				'options'           => array(),
				'attrs'             => array(),
				'position'          => 10,
				'tab'               => '',
				'section'           => '',
				'required'          => false,
				'show_in_rest'      => false,
			)
		);

		// Set label if empty and not false.
		if ( empty( $args['label'] ) && ! is_bool( $args['label'] ) ) {
			$args['label'] = $this->humanize_slug( $args['id'] );
		}

		// Set data_type to 'integer' if empty and type is 'number'.
		if ( empty( $args['data_type'] ) && 'number' === ( $args['type'] ) ) {
			$args['data_type'] = 'integer';
		}

		// Set data_type to 'number' if empty and type is 'decimal'.
		if ( empty( $args['data_type'] ) && 'decimal' === ( $args['type'] ) ) {
			$args['data_type'] = 'number';
		}

		return $args;
	}

	/**
	 * Register settings fields in bulk.
	 *
	 * @since 0.0.1
	 * @param array $fields Indexed array of settings field property.
	 */
	public function add_fields( $fields ) {
		if ( $fields && is_array( $fields ) ) {
			foreach ( $fields as $field ) {
				$this->add_field( $field );
			}
		}
	}

	/**
	 * Register settings field.
	 *
	 * @since 0.0.1
	 * @param array $args { Optional. Array of properties for the new field object.
	 *  @type string|callable $type               Type for the setting field or callable function to render the setting field. Valid values are 'url', 'number', 'decimal',
	 *                                            'password', 'email', 'checkbox', 'multicheckbox', 'radio', 'select', 'multiselect', 'textarea', 'wysiwyg', 'file'
	 *                                            Default 'text'.
	 *  @type string          $data_type          The type of data associated with this setting. Valid values are 'string', 'boolean', 'integer', and 'number'.
	 *                                            Default 'string'.
	 *  @type string          $id                 ID for the setting field. Default empty.
	 *  @type string          $label              Label for the setting field. Default empty.
	 *  @type string          $description        Description for the setting field. Default empty.
	 *  @type callable        $callback_before    Callback function that will be called before the setting field rendered. Default empty.
	 *  @type callable        $callback_after     Callback function that will be called after the setting field rendered. Default empty.
	 *  @type callable        $sanitize_callback  Callback function to sanitize setting field value. Default null.
	 *  @type string          $section            Section ID for the setting field. Default empty.
	 *  @type string          $default            Default value for the setting field. Default empty.
	 *  @type array           $options            Setting field input options, a key value pair used for setting field type select, radio, checkbox. Default array().
	 *  @type array           $attrs              Setting field input attributes. Default array().
	 *  @type integer         $position           Setting field position. Higher will displayed last. Default 10.
	 *  @type bool            $required           Set the setting field is required. Default false.
	 *  @type bool            $show_in_rest       Whether data associated with this setting should be included in the REST API. Default false.
	 * }
	 */
	public function add_field( $args ) {
		$args = $this->normalize_field( $args );
		if ( ! empty( $args['id'] ) ) {
			$this->settings[] = array(
				'type' => 'field',
				'data' => $args,
			);
		}
	}

	/**
	 * Get settings field attribute id.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function get_field_id( $field ) {
		return implode( '_', array( $field['tab'], $field['section'], $field['id'] ) );
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
	private function field_attrs( $field ) {
		switch ( $field['type'] ) {
			case 'text':
			case 'url':
			case 'number':
			case 'password':
			case 'email':
				if ( ! isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] = 'regular-text';
				}
				if ( false === strpos( $field['attrs']['class'], 'regular-text' ) ) {
					$field['attrs']['class'] .= ' regular-text';
				}
				break;
			case 'file':
				if ( ! isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] = 'regular-text';
				}
				if ( false === strpos( $field['attrs']['class'], 'regular-text' ) ) {
					$field['attrs']['class'] .= ' regular-text';
				}
				$field['attrs']['readonly'] = 'readonly';
				break;
			case 'color':
				if ( ! isset( $field['attrs']['class'] ) ) {
					$field['attrs']['class'] = 'regular-text wpyes-color-picker';
				}
				if ( false === strpos( $field['attrs']['class'], 'regular-text' ) ) {
					$field['attrs']['class'] .= ' regular-text';
				}
				if ( false === strpos( $field['attrs']['class'], 'wpyes-color-picker' ) ) {
					$field['attrs']['class'] .= ' wpyes-color-picker';
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

		if ( ! isset( $field['attrs']['class'] ) ) {
			$field['attrs']['class'] = 'wpyes-field';
		}

		if ( false === strpos( $field['attrs']['class'], 'wpyes-field' ) ) {
			$field['attrs']['class'] .= ' wpyes-field';
		}

		if ( is_string( $field['type'] ) ) {
			if ( false === strpos( $field['attrs']['class'], 'wpyes-field-' . $field['type'] ) ) {
				$field['attrs']['class'] .= ' wpyes-field-' . $field['type'];
			}
		}

		// Remove core field attributes to avoid conflict.
		unset( $field['attrs']['id'] );
		unset( $field['attrs']['name'] );
		unset( $field['attrs']['value'] );
		unset( $field['attrs']['type'] );
		unset( $field['attrs']['checked'] );
		unset( $field['attrs']['selected'] );
		unset( $field['attrs']['multiple'] );

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
			call_user_func( $field['callback_before'], $field );
		}

		if ( is_string( $field['type'] ) && is_callable( array( $this, 'render_field_' . $field['type'] ) ) ) {
			call_user_func( array( $this, 'render_field_' . $field['type'] ), $field );
		}

		if ( ! is_string( $field['type'] ) && is_callable( $field['type'] ) ) {
			call_user_func( $field['type'], $field );
		}

		if ( ! empty( $field['callback_after'] ) && is_callable( $field['callback_after'] ) ) {
			call_user_func( $field['callback_after'], $field );
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
		<?php $this->field_attrs( $field ); ?> />
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
	 * Render the setting field for decimal.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	private function render_field_decimal( $field ) {
		$field['type']          = 'number';
		$field['attrs']['step'] = 'any';
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
	 * Render the setting field for color.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_color( $field ) {
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
		<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" value="0" />
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>">
				<input 
				type="checkbox" 
				id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
				value="1" 
				<?php checked( $this->get_field_value( $field ), '1' ); ?>
				<?php $this->field_attrs( $field ); ?> />
				<?php echo esc_html( $field['description'] ); ?>
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
		if ( empty( $value ) || ! is_array( $value ) ) {
			$value = array();
		}
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
					<?php $this->field_attrs( $field ); ?> />
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
					<?php $this->field_attrs( $field ); ?> />
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
		<select id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" <?php $this->field_attrs( $field ); ?>>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_attr( $option_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the setting field for multiselect.
	 *
	 * @since 0.0.1
	 * @param array $field Setting field property.
	 */
	public function render_field_multiselect( $field ) {
		$value = $this->get_field_value( $field );
		if ( empty( $value ) || ! is_array( $value ) ) {
			$value = array();
		}
		?>
		<select id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>[]" multiple <?php $this->field_attrs( $field ); ?>>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( in_array( $option_value, $value, true ), true ); ?>><?php echo esc_attr( $option_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
		<?php $this->field_attrs( $field ); ?>
		><?php echo esc_textarea( $this->get_field_value( $field ) ); ?></textarea>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
		<div style="max-width:<?php echo esc_attr( $width ); ?>;" <?php $this->field_attrs( $field ); ?>>
		<?php wp_editor( $this->get_field_value( $field ), $this->get_field_name( $field ), $editor_settings ); ?>
		</div>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
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
		<?php $this->field_attrs( $field ); ?> />
		<button type="button" class="button wpyes-browse-media"><span class="dashicons dashicons-upload"></span></button>
		<button type="button" class="button wpyes-remove-media"><span class="dashicons dashicons-trash"></span></button>
		<?php if ( ! empty( $field['description'] ) ) : ?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Validate setting field.
	 *
	 * This function is hooked into sanitize_option_{$option} filter.
	 *
	 * @since 0.0.1
	 *
	 * @param string $value          The sanitized option value.
	 * @param string $option         The option name.
	 * @param string $original_value The original value passed to the function.
	 *
	 * @throws Exception Throw an exception if the field validation not passed.
	 */
	public function validate_field( $value, $option, $original_value ) {
		try {

			$field = isset( $this->settings[ $option ] ) ? $this->settings[ $option ] : false;
			if ( empty( $field ) ) {
				return $value;
			}

			// Validate if field is required.
			if ( $field['required'] && ! is_numeric( $value ) && empty( $value ) ) {
				throw new Exception( __( 'Value can not be empty.', 'wpyes' ) ); // @todo Change text-domain based on your plugin or theme.
			}

			// If value is empty and not numeric, no need to validate further.
			if ( empty( $value ) && ! is_numeric( $value ) ) {
				return $value;
			}

			// Validate by field type.
			switch ( $field['type'] ) {
				case 'email':
					if ( ! is_email( $value ) ) {
						throw new Exception( __( 'Value must be a valid email address.', 'wpyes' ) ); // @todo Change text-domain based on your plugin or theme.
					}
					break;
				case 'url':
					if ( false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
						throw new Exception( __( 'Value must be a valid URL.', 'wpyes' ) ); // @todo Change text-domain based on your plugin or theme.
					}
					break;
				case 'number':
					if ( $value > intval( $value ) || $value < intval( $value ) ) {
						throw new Exception( __( 'Value must be an integer.', 'wpyes' ) ); // @todo Change text-domain based on your plugin or theme.
					}
					$value = intval( $value );
					break;
				case 'decimal':
					if ( ! is_numeric( $value ) ) {
						throw new Exception( __( 'Value must be a number.', 'wpyes' ) ); // @todo Change text-domain based on your plugin or theme.
					}
					break;
			}
		} catch ( Exception $e ) {

			// Check if same error already exists.
			if ( isset( $this->errors[ $option ] ) ) {
				return $value;
			}

			// Add settings error.
			$label     = ! empty( $field['label'] ) ? $field['label'] : $this->humanize_slug( $option );
			$error_msg = sprintf( '%1$s: %2$s', $label, $e->getMessage() );
			add_settings_error(
				$option,
				$this->get_field_id( $field ),
				$error_msg,
				'error'
			);
			$this->errors[ $option ] = $error_msg;
		}

		return $value;
	}

	/**
	 * Get settings tabs, sections and fields as associative array array
	 *
	 * @since 0.0.1
	 * @return array All settings data array.
	 */
	private function get_settings() {
		if ( ! $this->settings_populated ) {
			return array();
		}
		return apply_filters( 'wpyes_' . $this->menu_slug . '_settings', $this->settings );
	}

	/**
	 * Populate settings data.
	 */
	private function populate_settings() {

		$tabs     = array();
		$sections = array();
		$fields   = array();

		foreach ( $this->settings as $setting ) {

			if ( empty( $setting['type'] ) || empty( $setting['data'] ) ) {
				continue;
			}

			$data = $setting['data'];

			switch ( $setting['type'] ) {
				case 'tab':
					// Assign recent tab ID.
					$this->recent_tab = $data['id'];

					// Push data to tabs variable.
					$tabs[ $data['id'] ] = $data;

					break;
				case 'section':
					// Set tab key for $data if empty and $recent_tab is not empty.
					if ( empty( $data['tab'] ) && ! empty( $this->recent_tab ) ) {
						$data['tab'] = $this->recent_tab;
					}

					if ( empty( $data['tab'] ) && empty( $this->recent_tab ) ) {
						$auto_tab                 = $this->normalize_tab(
							array(
								'id' => $this->menu_slug,
							)
						);
						$tabs[ $this->menu_slug ] = $auto_tab;

						$this->recent_tab = $this->menu_slug;
						$data['tab']      = $this->recent_tab;
					}

					// Check if tab key for section $data is not empty.
					if ( ! empty( $data['tab'] ) ) {
						// Assign recent section ID.
						$this->recent_section = $data['id'];

						$section_unique_id = $data['tab'] . '_' . $data['id'];

						$data['id'] = $section_unique_id;

						// Push data to sections variable.
						$sections[ $section_unique_id ] = $data;
					}

					break;
				case 'field':
					// Set tab key for $data if empty and $recent_tab is not empty.
					if ( empty( $data['tab'] ) && ! empty( $this->recent_tab ) ) {
						$data['tab'] = $this->recent_tab;
					}

					if ( empty( $data['tab'] ) && empty( $this->recent_tab ) ) {
						$this->recent_tab         = $this->menu_slug;
						$auto_tab                 = $this->normalize_tab(
							array(
								'id' => $this->menu_slug,
							)
						);
						$tabs[ $this->menu_slug ] = $auto_tab;
						$data['tab']              = $this->recent_tab;
					}

					// Set section key for $data if empty and $recent_section is not empty.
					if ( empty( $data['section'] ) && ! empty( $this->recent_section ) ) {
						$data['section'] = $this->recent_section;
					}

					// Set section key for $data if empty and $recent_section is not empty.
					if ( empty( $data['section'] ) && empty( $this->recent_section ) ) {
						$this->recent_section           = $this->menu_slug;
						$auto_section                   = $this->normalize_section(
							array(
								'id'  => $this->menu_slug,
								'tab' => $data['tab'],
							)
						);
						$section_unique_id              = $data['tab'] . '_' . $auto_section['id'];
						$sections[ $section_unique_id ] = $auto_section;
						$data['section']                = $auto_section['id'];
					}

					// Check if tab and section key for field $data is not empty.
					if ( ! empty( $data['tab'] ) && ! empty( $data['section'] ) ) {
						$data['section']       = $data['tab'] . '_' . $data['section'];
						$fields[ $data['id'] ] = $data;
					}

					break;
			}
		}

		if ( empty( $tabs ) || empty( $sections ) || empty( $fields ) ) {
			return;
		}

		// Sort tabs to settings data by positin property.
		uasort( $tabs, array( $this, 'sort_by_position' ) );

		// Sort tabs to settings data by positin property.
		uasort( $sections, array( $this, 'sort_by_position' ) );

		// Sort tabs to settings data by positin property.
		uasort( $fields, array( $this, 'sort_by_position' ) );

		$this->settings = array();

		foreach ( $fields as $key => $field ) {

			// Validate field data.
			if ( ! isset( $tabs[ $field['tab'] ] ) || ! isset( $sections[ $field['section'] ] ) ) {
				continue;
			}

			$sections[ $field['section'] ]['fields'][ $key ]   = $field;
			$this->settings[ $this->get_field_name( $field ) ] = $field;
		}

		foreach ( $sections as $key => $section ) {

			// Validate section data.
			if ( ! isset( $tabs[ $section['tab'] ] ) ) {
				continue;
			}

			$tabs[ $section['tab'] ]['sections'][ $key ] = $section;
		}

		// Set populated settings data.
		foreach ( $tabs as $key => $tab ) {
			$this->settings_populated[ $key ] = $tab;
		}
	}

	/**
	 * Initialize and build the settings tabs, sections and fileds.
	 */
	public function init() {

		$this->populate_settings();

		add_action( 'admin_init', array( $this, 'register_settings' ), 10 );

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_print_footer_scripts', array( $this, 'admin_footer_js' ) );

	}

	/**
	 * Registers the settings to WordPress.
	 *
	 * This function is hooked into admin_init.
	 */
	public function register_settings() {

		if ( ! $this->settings_populated ) {
			return;
		}

		foreach ( $this->settings_populated as $tab_key => $tab ) {

			// Add action hook to render for the tab content.
			add_action( 'wpyes_' . $this->menu_slug . '_setting_tab_' . $tab_key, $tab['callback'] );

			foreach ( $tab['sections'] as $section_key => $section ) {
				$section_unique_id = $this->get_section_unique_id( $section );

				// Add a new section to a settings page.
				add_settings_section( $section_unique_id, $section['title'], $section['callback'], $section_unique_id );

				foreach ( $section['fields'] as $field_key => $field ) {
					$option = $this->get_field_name( $field );

					// Register a settings field to a settings page and section.
					add_settings_field( $option, $field['label'], array( $this, 'render_field' ), $section_unique_id, $section_unique_id, $field );

					// Register a setting and its data.
					register_setting(
						$this->menu_slug, $option, array(
							'type'              => $field['data_type'],
							'group'             => $this->menu_slug,
							'description'       => $field['description'],
							'sanitize_callback' => $field['sanitize_callback'],
							'show_in_rest'      => $field['show_in_rest'],
						)
					);

					// Add filter hook to validate for the setting field.
					add_filter( "sanitize_option_{$option}", array( $this, 'validate_field' ), 10, 3 );
				}
			}
		}
	}

	public function page_title() {
		?>
		<h1 class="<?php echo empty( $this->buttons ) ? '' : esc_attr( 'wp-heading-inline' ); ?>">
			<?php echo esc_html( $this->menu_args['page_title'] ); ?>
			<?php if ( ! empty( $this->buttons ) ) : ?>
				<?php foreach( $this->buttons as $index => $button ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" id="<?php echo empty( $button['id'] ) ? 'button-' . $this->menu_slug . '-' . esc_attr( $index ) : esc_attr( $button['id'] ); ?>" class="page-title-action"><?php echo esc_html( $button['text'] ); ?></a>
				<?php endforeach; ?>
				<hr class="wp-header-end">
			<?php endif; ?>
		</h1>
		<?php
	}

	/**
	 * Render the settings form.
	 */
	public function render_form() {
		?>
		<div class="wrap wpyes-wrap">
			<?php $this->page_title(); ?>
			<?php settings_errors( '', false, $this->hide_on_update ); ?>
			<?php if ( 1 < count( $this->settings_populated ) ) : ?>
				<div class="metabox-holder">
					<h2 class="wpyes-nav-tab-wrapper nav-tab-wrapper">
						<?php foreach ( $this->settings_populated as $tab_key => $tab ) : ?>
						<a href="#<?php echo esc_attr( $tab_key ); ?>" class="wpyes-nav-tab nav-tab" id="<?php echo esc_attr( $tab_key ); ?>-tab"><?php echo esc_html( $tab['label'] ); ?></a>
						<?php endforeach; ?>
					</h2>
				</div>
				<div class="clear"></div>
			<?php endif; ?>
			<form method="post" action="options.php">
				<div class="wpyes-tab-wrapper metabox-holder">
					<?php foreach ( $this->settings_populated as $tab_key => $tab ) : ?>
						<div id="<?php echo esc_attr( $tab['id'] ); ?>" class="wpyes-tab-group">
							<?php do_action( 'wpyes_' . $this->menu_slug . '_setting_tab_' . $tab_key, $tab ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( 0 < count( $this->settings ) ) : ?>
				<div class="wpyes-button-wrapper">
					<?php settings_fields( $this->menu_slug ); ?>
					<?php submit_button(); ?>
				</div>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register admin menus to the WP Admin.
	 *
	 * This function is hooked into admin_menu to affect admin only.
	 */
	public function admin_menu() {

		$allowed = array(
			'add_menu_page',
			'add_management_page',
			'add_options_page',
			'add_theme_page',
			'add_plugins_page',
			'add_users_page',
			'add_dashboard_page',
			'add_posts_page',
			'add_media_page',
			'add_links_page',
			'add_pages_page',
			'add_comments_page',
			'add_submenu_page',
		);

		if ( ! in_array( $this->menu_args['method'], $allowed, true ) ) {
			$this->menu_args['method'] = 'add_menu_page';
		}

		switch ( $this->menu_args['method'] ) {
			case 'add_submenu_page':
				if ( empty( $this->menu_args['parent_slug'] ) ) {
					$this->menu_args['parent_slug'] = 'options-general.php';
				}

				if ( false !== strpos( $this->menu_args['parent_slug'], 'options-general.php' ) ) {
					$this->hide_on_update = true;
				}

				$admin_page = call_user_func(
					$this->menu_args['method'],
					$this->menu_args['parent_slug'],
					$this->menu_args['page_title'],
					$this->menu_args['menu_title'],
					$this->menu_args['capability'],
					$this->menu_slug,
					$this->menu_args['callback']
				);
				break;

			case 'add_menu_page':
				$admin_page = call_user_func(
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

			default:
				if ( 'add_options_page' === $this->menu_args['method'] ) {
					$this->hide_on_update = true;
				}
				$admin_page = call_user_func(
					$this->menu_args['method'],
					$this->menu_args['page_title'],
					$this->menu_args['menu_title'],
					$this->menu_args['capability'],
					$this->menu_slug,
					$this->menu_args['callback']
				);
				break;
		}

		// Register help tabs to current admin screen.
		add_action( 'load-' . $admin_page, array( $this, 'register_help_tabs' ) );
	}

	/**
	 * Add help tab items.
	 *
	 * @since 0.0.1
	 * @param array $help_tabs Indexed array of properties for the new tab object.
	 */
	public function add_help_tabs( $help_tabs ) {

		// Validate help tab property.
		if ( ! $help_tab || ! is_array( $help_tab ) ) {
			return;
		}

		foreach ( $help_tabs as $help_tab ) {
			$this->add_help_tab( $help_tab );
		}
	}

	/**
	 * Add help tab item.
	 *
	 * @since 0.0.1
	 * @param array $help_tab { Array of properties for the help tab object.
	 *  @type string            $id              ID for the setting tab. Default empty.
	 *  @type string            $title           Label for the setting tab. Default empty.
	 *  @type string|callable   $content         Setting sections that will be linked to the tab. Default array().
	 * }
	 */
	public function add_help_tab( $help_tab ) {

		// Validate help tab property.
		if ( ! $help_tab || ! is_array( $help_tab ) || ! isset( $help_tab['id'], $help_tab['title'], $help_tab['content'] ) ) {
			return;
		}

		$this->help_tabs[ $help_tab['id'] ] = $help_tab;
	}

	/**
	 * Register help tabs to current admin screen.
	 *
	 * This function is hooked into "load-{$this->menu_slug}" action.
	 *
	 * @since 0.0.1
	 */
	public function register_help_tabs() {
		if ( ! $this->help_tabs ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}
		foreach ( $this->help_tabs as $help_tab ) {
			$screen->add_help_tab( $help_tab );
		}
	}

	/**
	 * Add custom button beside of the setting page title.
	 *
	 * @since 0.0.1
	 * @param string $text   The action button text.
	 * @param string $url    The action button URL.
	 * @param string $id     The action button ID.
	 * @return void
	 */
	public function add_button( $text, $url, $id = '' ) {

		// Validate action button text and url.
		if ( empty( $text ) || empty( $url ) || ! is_string( $text ) || ! is_string( $url ) ) {
			return;
		}

		$this->buttons[] = array(
			'text' => $text,
			'url'  => $url,
			'id'   => $id,
		);
	}

	/**
	 * Enqueue scripts and styles for the setting page.
	 *
	 * This function is hooked into admin_enqueue_scripts.
	 *
	 * @since    0.0.1
	 * @param string $hook Current admin page slug loaded.
	 */
	public function admin_enqueue_scripts( $hook ) {

		if ( ! $this->menu_args['callback'] || ! strpos( $hook, '_' . $this->menu_slug ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', array( 'jquery' ) );
		wp_enqueue_media();

		// Do custom action hook to enqueue custom script and styles.
		do_action( 'wpyes_' . $this->menu_slug . '_admin_enqueue_scripts', $hook, $this );
	}

	/**
	 * Print scripts needed to initiate Color Picker & Tab element.
	 *
	 * This function is hooked into admin_print_footer_scripts.
	 *
	 * @since 0.0.1
	 */
	public function admin_footer_js() {

		$screen = get_current_screen();

		if ( ! $this->menu_args['callback'] || ! isset( $screen->base ) || ! strpos( $screen->base, '_' . $this->menu_slug ) ) {
			return;
		}
		?>
		<script>
			(function($) {

				"use strict";

				$(document).ready(function($) {

					var menuSlug = "<?php echo esc_html( $this->menu_slug ); ?>";

					// Initiate color picker.
					$(".wpyes-color-picker").wpColorPicker();

					// Initiate tabs.
					$(".wpyes-tab-group").hide();

					var activeTab = "";

					if (typeof localStorage != "undefined") {
						activeTab = localStorage.getItem("wpyes-active-tab-" + menuSlug);
					}

					if (activeTab != "" && $(activeTab).length) {
						$(activeTab).fadeIn();
					} else {
						$(".wpyes-tab-group:first").fadeIn();
					}

					if (activeTab != "" && $(activeTab + "-tab").length) {
						$(activeTab + "-tab").addClass("nav-tab-active");
					} else {
						$(".wpyes-nav-tab-wrapper a:first").addClass("nav-tab-active");
					}

					$(".wpyes-nav-tab-wrapper a").click(function(e) {
						e.preventDefault();

						$(".wpyes-nav-tab-wrapper a").removeClass("nav-tab-active");

						$(this).addClass("nav-tab-active").blur();

						if (typeof localStorage != "undefined") {
							localStorage.setItem("wpyes-active-tab-" + menuSlug, $(this).attr("href"));
						}

						$(".wpyes-tab-group").hide();

						$($(this).attr("href")).fadeIn();
					});

					// Media file browser.
					$(".wpyes-browse-media").on("click", function(e) {
						e.preventDefault();

						var self = $(this);

						// Create the media frame.
						var mediaModal = (wp.media.frames.file_frame = wp.media({
							multiple: false
						}));

						mediaModal.on("select", function() {

							var attachment = mediaModal.state().get("selection").first().toJSON();

							self.closest("td").find('input[type="text"]').val(attachment.url);

						});

						// Finally, open the modal
						mediaModal.open();
					});

					// Remove file from input.
					$(".wpyes-remove-media").on("click", function(e) {
						e.preventDefault();
						$(this).closest("td").find('input[type="text"]').val("");
					});

				});

			})(jQuery);
		</script>
		<?php

		// Do custom action hook to print scripts needed.
		do_action( 'wpyes_' . $this->menu_slug . '_admin_footer_js', $screen, $this );
	}

	/**
	 * Sort array by position
	 *
	 * @since    0.0.1
	 * @param array $a First index of the array.
	 * @param array $b Compared array.
	 * @return integer
	 */
	private function sort_by_position( $a, $b ) {
		$a = isset( $a['position'] ) ? (int) $a['position'] : 10;
		$b = isset( $b['position'] ) ? (int) $b['position'] : 10;

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
	 * @return string Humanized text.
	 */
	private function humanize_slug( $slug ) {

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
