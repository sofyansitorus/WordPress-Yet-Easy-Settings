<?php
/**
 * Core file for WP_Yes Class.
 *
 * @link       https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings
 * @since      1.0.0
 * @package    WP_Yes
 */

/**
 * WP_Yes class.
 *
 * WordPress Yet Easy Settings class is PHP class for easy to build WordPress admin settings page.
 *
 * @since      1.0.0
 * @package    WP_Yes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class WP_Yes {

	/**
	 * Current version
	 *
	 * @var string
	 */
	public static $version = '1.0.4';

	/**
	 * Admin menu slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Admin menu arguments.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $menu_args;

	/**
	 * Setting prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $setting_prefix = '';

	/**
	 * Populated settings data.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $settings = array();

	/**
	 * Recent tab id registered.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $recent_tab;

	/**
	 * Recent section id registered.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $recent_section;

	/**
	 * Recent field id registered.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $recent_field;

	/**
	 * All tabs data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $all_tabs = array();

	/**
	 * All sections data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $all_sections = array();

	/**
	 * All fields data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $all_fields = array();

	/**
	 * Admin screen help_tabs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $help_tabs = array();

	/**
	 * Admin screen action buttons.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $buttons = array();

	/**
	 * If set to true errors will not be shown if the settings page has
	 * already been submitted.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $hide_on_update = false;

	/**
	 * Custom scripts data.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $custom_scripts = array();

	/**
	 * Custom CSS stylesheets data.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $custom_styles = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param string $menu_slug        The slug name to refer to this menu (should be unique).
	 * @param array  $menu_args        { Optional. Array of properties for the new admin menu object. Default empty array.
	 *  @type string     $method               Built-in WP function used to register menu. Available options: add_menu_page, add_management_page, add_options_page,
	 *                                         add_theme_page, add_plugins_page, add_users_page, add_dashboard_page, add_posts_page, add_media_page, add_links_page,
	 *                                         add_pages_page, add_comments_page, add_submenu_page. Default 'add_menu_page'.
	 *  @type string     $capability           Capability required for this menu to be displayed to the user. Default 'manage_options'.
	 *  @type string     $page_title           Text to be displayed in the title tags of the page when the menu is selected. Default $menu_slug property.
	 *  @type string     $menu_title           Text to be used for the menu. Default $menu_slug property.
	 *  @type callable   $callback             Function to be called to output the content for this page. Default WP_Yes::render_form.
	 *  @type string     $icon_url             URL to the icon to be used for this menu. Used when $method is 'add_menu_page'. Default empty.
	 *  @type integer    $position             Position in the menu order this one should appear. Used when $method is 'add_menu_page'. Default null.
	 *  @type string     $parent_slug          Slug name for the parent menu. Required if $method is 'add_submenu_page'. Default empty.
	 * }
	 * @param string $setting_prefix   Setting field prefix. This will affect you how you to get the option value. If not empty, the $setting_prefix would be
	 *                                 pre-pended when getting option value. Example: If $setting_prefix = 'wp_yes_txt', to get option value for setting id 'example_1'
	 *                                 is get_option('wp_yes_example_1'). Default empty.
	 *
	 * @throws Exception Throw an exception when the requirements is not met.
	 */
	public function __construct( $menu_slug, $menu_args = array(), $setting_prefix = '' ) {
		$check_requirements = self::check_requirements();

		if ( is_wp_error( $check_requirements ) ) {
			throw new Exception( $check_requirements->get_error_message() );
		}

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

		if ( $setting_prefix ) {
			$this->set_prefix( $setting_prefix );
		}
	}

	/**
	 * Set setting name prefix
	 *
	 * @since 1.0.3
	 *
	 * @param string $setting_prefix Setting name prefix.
	 *
	 * @return void
	 */
	public function set_prefix( $setting_prefix ) {
		$this->setting_prefix = trim( $setting_prefix, '_' );
	}

	/**
	 * Normalize settings tab property.
	 *
	 * @since 1.0.0
	 * @param array $args { Optional. Array of properties for the new tab object.
	 *  @type string          $id              ID for the setting tab. Default empty.
	 *  @type string          $title           Title for the setting tab. Default empty.
	 *  @type array           $sections        Setting sections that will be linked to the tab. Default array().
	 *  @type integer         $position        Setting tab position. Higher will displayed last. Default 10.
	 *  @type callable        $callback        Callable function to be called to render output the tab content. Default WP_Yes::render_tab.
	 * }
	 * @return array Normalized setting tab property.
	 */
	protected function normalize_tab( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'       => '',
				'title'    => '',
				'sections' => array(),
				'position' => 10,
				'callback' => '',
			)
		);

		// Create title if empty and not false.
		if ( empty( $args['title'] ) && ! is_bool( $args['title'] ) ) {
			$args['title'] = $this->humanize_slug( $args['id'] );
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param array $args { Optional. Array of properties for the new tab object.
	 *  @type string          $id              ID for the setting tab. Default empty.
	 *  @type string          $label           Label for the setting tab. Default empty.
	 *  @type array           $sections        Setting sections that will be linked to the tab. Default array().
	 *  @type integer         $position        Setting tab position. Higher will displayed last. Default 10.
	 * }
	 */
	public function add_tab( $args ) {
		$args = $this->normalize_tab( $args );

		if ( empty( $args['id'] ) ) {
			return;
		}

		$unique_id = $this->get_tab_id( $args );

		if ( isset( $this->all_tabs[ $unique_id ] ) ) {
			return;
		}

		$this->all_tabs[ $unique_id ] = $args;
		$this->recent_tab             = $args['id'];
		$this->recent_section         = null;
	}

	/**
	 * Render settings tab.
	 *
	 * @since 1.0.0
	 * @param array $tab Setting tab property.
	 */
	public function render_tab( $tab ) {
		foreach ( $tab['sections'] as $section ) {
			do_settings_sections( $this->get_section_id( $section ) );
		}
	}

	/**
	 * Normalize settings section property.
	 *
	 * @since 1.0.0
	 * @param array $args { Optional. Array of properties for the new section object.
	 *  @type string          $id              ID for the setting section. Default empty.
	 *  @type string          $label           Label for the setting section. Default empty.
	 *  @type callable        $callback        A callback function that render the setting section.
	 *  @type array           $fields          Setting fields that linked directly to the section. Default array().
	 *  @type integer         $position        Setting section position. Higher will displayed last. Default 10.
	 *  @type string          $tab             Tab ID where will be the section displayed. Default empty.
	 * }
	 * @return array Normalized setting section property.
	 */
	protected function normalize_section( $args ) {
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

		if ( empty( $args['tab'] ) ) {
			if ( empty( $this->recent_tab ) ) {
				$this->add_tab(
					array(
						'id' => $this->menu_slug,
					)
				);
			}

			$args['tab'] = $this->recent_tab;
		}

		return $args;
	}

	/**
	 * Register settings sections in bulk.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param array $args { Optional. Array of properties for the new section object.
	 *  @type string          $id              ID for the setting section. Default empty.
	 *  @type string          $label           Label for the setting section. Default empty.
	 *  @type callable        $callback        A callback function that render the setting section.
	 *  @type array           $fields          Setting fields that linked directly to the section. Default array().
	 *  @type integer         $position        Setting section position. Higher will displayed last. Default 10.
	 *  @type string          $tab             Tab ID where will be the section displayed. Default empty.
	 * }
	 */
	public function add_section( $args ) {
		$args = $this->normalize_section( $args );

		if ( empty( $args['id'] ) ) {
			return;
		}

		if ( empty( $args['tab'] ) ) {
			return;
		}

		$unique_id = $this->get_section_id( $args );

		if ( isset( $this->all_sections[ $unique_id ] ) ) {
			return;
		}

		$this->all_sections[ $unique_id ] = $args;
		$this->recent_section             = $args['id'];
	}

	/**
	 * Normalize setting field properties
	 *
	 * @since 1.0.0
	 * @param array $args {
	 *  Optional. Array of properties for the new field object.
	 *
	 *  @type string|callable $type               Type for the setting field or callable function to render the setting field. Valid values are 'url', 'number', 'decimal',
	 *                                            'password', 'email', 'toggle', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'wysiwyg', 'file'
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
	protected function normalize_field( $args ) {
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
				'options_stacked'   => false,
				'attrs'             => array(),
				'position'          => 10,
				'tab'               => '',
				'section'           => '',
				'required'          => false,
				'show_in_rest'      => false,
				'conditional'       => array(),
			)
		);

		if ( empty( $args['tab'] ) ) {
			if ( empty( $this->recent_tab ) ) {
				$this->add_tab(
					array(
						'id' => $this->menu_slug,
					)
				);
			}

			$args['tab'] = $this->recent_tab;
		}

		if ( empty( $args['section'] ) ) {
			if ( empty( $this->recent_section ) ) {
				$this->add_section(
					array(
						'id' => $this->menu_slug,
					)
				);
			}

			$args['section'] = $this->recent_section;
		}

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

		$field_class = isset( $args['attrs']['class'] ) ? $args['attrs']['class'] : array();

		if ( ! is_array( $field_class ) ) {
			$field_class = explode( ' ', $field_class );
		}

		if ( ! in_array( 'wp-yes--field', $field_class, true ) ) {
			$field_class[] = 'wp-yes--field';
		}

		if ( ! in_array( 'wp-yes--field--' . $args['type'], $field_class, true ) ) {
			$field_class[] = 'wp-yes--field--' . $args['type'];
		}

		$args['attrs']['class'] = implode( ' ', $field_class );

		if ( ! empty( $args['conditional'] ) ) {
			$conditional = array();

			foreach ( $args['conditional'] as $key => $value ) {
				$conditional[ $this->get_field_name( $key ) ] = $value;
			}

			$args['conditional'] = $conditional;
		}

		$args['name'] = $this->get_field_name( $args );

		return $args;
	}

	/**
	 * Register settings fields in bulk.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param array $args {
	 *  Optional. Array of properties for the new field object.
	 *
	 *  @type string|callable $type               Type for the setting field or callable function to render the setting field. Valid values are 'url', 'number', 'decimal',
	 *                                            'password', 'email', 'toggle', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'wysiwyg', 'file'
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

		if ( empty( $args['id'] ) ) {
			return;
		}

		if ( empty( $args['tab'] ) || empty( $args['section'] ) ) {
			return;
		}

		$unique_id = $this->get_field_id( $args );

		if ( isset( $this->all_fields[ $unique_id ] ) ) {
			return;
		}

		$this->all_fields[ $unique_id ] = $args;
		$this->recent_field             = $args['id'];
	}

	/**
	 * Get settings tab unique ID.
	 *
	 * @since 1.0.0
	 * @param array $tab Setting tab property.
	 * @return string Unique ID of tab object.
	 */
	protected function get_tab_id( $tab ) {
		if ( is_array( $tab ) && isset( $tab['id'] ) ) {
			return $tab['id'];
		}

		return $tab;
	}

	/**
	 * Get settings section unique ID.
	 *
	 * @since 1.0.0
	 * @param array  $section Setting section property.
	 * @param string $tab Setting section tab slug.
	 *
	 * @return string Unique ID of section object.
	 */
	protected function get_section_id( $section, $tab = '' ) {
		$tab_id     = is_array( $section ) && isset( $section['tab'] ) ? $section['tab'] : $tab;
		$section_id = is_array( $section ) && isset( $section['id'] ) ? $section['id'] : $section;

		return $tab_id . '_' . $section_id;
	}

	/**
	 * Get settings field by option name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option Setting key.
	 *
	 * @return array
	 */
	protected function get_field( $option ) {
		$field = array();

		foreach ( $this->all_fields as $item ) {
			if ( $this->get_field_name( $item ) === $option ) {
				$field = $item;
				break;
			}
		}

		return $field;
	}

	/**
	 * Get settings field unique ID.
	 *
	 * @since 1.0.0
	 * @param array  $field Setting field property.
	 * @param string $section Setting field section slug.
	 * @param string $tab Setting field tab slug.
	 *
	 * @return string Unique ID of field object.
	 */
	protected function get_field_id( $field, $section = '', $tab = '' ) {
		$tab_id     = is_array( $field ) && isset( $field['tab'] ) ? $field['tab'] : $tab;
		$section_id = is_array( $field ) && isset( $field['section'] ) ? $field['section'] : $section;
		$field_id   = is_array( $field ) && isset( $field['id'] ) ? $field['id'] : $field;

		return $tab_id . '_' . $section_id . '_' . $field_id;
	}

	/**
	 * Get settings field attribute name.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function get_field_name( $field ) {
		$field_id = is_array( $field ) && isset( $field['id'] ) ? $field['id'] : $field;

		if ( ! $this->setting_prefix ) {
			return $field_id;
		}

		return $this->setting_prefix . '_' . $field_id;
	}

	/**
	 * Get settings field value from DB.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function get_field_value( $field ) {
		return get_option( $this->get_field_name( $field ), $field['default'] );
	}

	/**
	 * Get settings field attributes.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function field_attrs( $field ) {
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
					$field['attrs']['class'] = 'regular-text wp-yes--color-picker';
				}
				if ( false === strpos( $field['attrs']['class'], 'regular-text' ) ) {
					$field['attrs']['class'] .= ' regular-text';
				}
				if ( false === strpos( $field['attrs']['class'], 'wp-yes--color-picker' ) ) {
					$field['attrs']['class'] .= ' wp-yes--color-picker';
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

		// Remove core field attributes to avoid conflict.
		unset( $field['attrs']['id'] );
		unset( $field['attrs']['name'] );
		unset( $field['attrs']['value'] );
		unset( $field['attrs']['type'] );
		unset( $field['attrs']['checked'] );
		unset( $field['attrs']['selected'] );
		unset( $field['attrs']['multiple'] );

		foreach ( $field['attrs'] as $key => $value ) {
			if ( in_array( $key, array( 'src', 'href' ), true ) ) {
				echo esc_html( $key ) . '="' . esc_url( $value ) . '" ';
			} else {
				echo esc_html( $key ) . '="' . esc_attr( $value ) . '" ';
			}
		}
	}

	/**
	 * Render the setting field.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field( $field ) {
		?>
		<div data-id="<?php echo esc_attr( $field['name'] ); ?>" class="wp-yes--field--wrap">
		<?php
		if ( ! empty( $field['callback_before'] ) && is_callable( $field['callback_before'] ) ) {
			call_user_func( $field['callback_before'], $field );
		}

		if ( is_string( $field['type'] ) && is_callable( array( $this, 'render_field_type__' . $field['type'] ) ) ) {
			call_user_func( array( $this, 'render_field_type__' . $field['type'] ), $field );
		}

		if ( ! is_string( $field['type'] ) && is_callable( $field['type'] ) ) {
			call_user_func( $field['type'], $field );
		}

		if ( ! empty( $field['callback_after'] ) && is_callable( $field['callback_after'] ) ) {
			call_user_func( $field['callback_after'], $field );
		}
		?>
		<div>
		<?php
	}

	/**
	 * Render field description
	 *
	 * @since 1.0.3
	 *
	 * @param array $field Field data.
	 *
	 * @return void
	 */
	protected function render_field_description( $field ) {
		if ( empty( $field['description'] ) ) {
			return;
		}
		?>
		<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
		<?php
	}

	/**
	 * Render the setting field for text.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function render_field_type__text( $field ) {
		?>
		<input 
			type="<?php echo esc_attr( $field['type'] ); ?>" 
			id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
			value="<?php echo esc_attr( $this->get_field_value( $field ) ); ?>" 
			<?php $this->field_attrs( $field ); ?>
		/>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for url.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function render_field_type__url( $field ) {
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for number.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function render_field_type__number( $field ) {
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for decimal.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	protected function render_field_type__decimal( $field ) {
		$field['type'] = 'number';
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for password.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__password( $field ) {
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for email.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__email( $field ) {
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for color.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__color( $field ) {
		$this->render_field_type__text( $field );
	}

	/**
	 * Render the setting field for toggle.
	 *
	 * @since 1.0.4
	 * @param array $field Setting field property.
	 */
	public function render_field_type__toggle( $field ) {
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
					<?php $this->field_attrs( $field ); ?>
				/>
				<?php $this->render_field_description( $field ); ?>
			</label>
		</fieldset>
		<?php
	}

	/**
	 * Render the setting field for checkbox.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__checkbox( $field ) {
		$value = $this->get_field_value( $field );
		if ( empty( $value ) || ! is_array( $value ) ) {
			$value = array();
		}

		$label_class = $field['options_stacked'] ? 'wp-yes--field--options--vertical' : 'wp-yes--field--options--horizontal';
		?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" class="wp-yes--field--options--label <?php echo esc_attr( $label_class ); ?>">
					<input 
						type="checkbox" 
						id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" 
						name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>[]" 
						value="<?php echo esc_attr( $option_value ); ?>" 
						<?php checked( in_array( $option_value, $value, true ), true ); ?>
						<?php $this->field_attrs( $field ); ?>
					/>
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for radio.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__radio( $field ) {
		$value = $this->get_field_value( $field );
		if ( ! is_string( $value ) ) {
			$value = $field['default'];
		}

		$label_class = $field['options_stacked'] ? 'wp-yes--field--options--vertical' : 'wp-yes--field--options--horizontal';
		?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo esc_html( $field['label'] ); ?></span></legend>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<label for="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" class="wp-yes--field--options--label <?php echo esc_attr( $label_class ); ?>">
					<input 
						type="radio" 
						id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>[<?php echo esc_attr( $option_value ); ?>]" 
						name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
						value="<?php echo esc_attr( $option_value ); ?>" 
						<?php checked( $value, $option_value ); ?>
						<?php $this->field_attrs( $field ); ?>
					/>
					<?php echo esc_html( $option_label ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for select.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__select( $field ) {
		$value = $this->get_field_value( $field );
		?>
		<select id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" <?php $this->field_attrs( $field ); ?>>
			<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_attr( $option_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for multiselect.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__multiselect( $field ) {
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
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for textarea.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__textarea( $field ) {
		?>
		<textarea 
			id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
			<?php $this->field_attrs( $field ); ?>
		><?php echo esc_textarea( $this->get_field_value( $field ) ); ?></textarea>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for wysiwyg.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__wysiwyg( $field ) {
		$editor_settings = array(
			'wpautop'          => true,
			'media_buttons'    => false,
			'textarea_name'    => $this->get_field_name( $field ),
			'textarea_rows'    => 7,
			'tabindex'         => '',
			'editor_css'       => '',
			'editor_class'     => $field['attrs']['class'],
			'editor_height'    => '',
			'teeny'            => true,
			'dfw'              => false,
			'tinymce'          => true,
			'quicktags'        => true,
			'drag_drop_upload' => false,
		);

		foreach ( array_keys( $editor_settings ) as $key ) {
			if ( ! isset( $field['options'][ $key ] ) ) {
				continue;
			}

			$editor_settings[ $key ] = $field['options'][ $key ];
		}
		?>
		<?php wp_editor( $this->get_field_value( $field ), $this->get_field_id( $field ), $editor_settings ); ?>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Render the setting field for file.
	 *
	 * @since 1.0.0
	 * @param array $field Setting field property.
	 */
	public function render_field_type__file( $field ) {
		?>
		<input 
			type="text" 
			id="<?php echo esc_attr( $this->get_field_id( $field ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( $field ) ); ?>" 
			value="<?php echo esc_attr( $this->get_field_value( $field ) ); ?>" 
			<?php $this->field_attrs( $field ); ?>
		/>
		<button type="button" class="button wp-yes--browse-media"><span class="dashicons dashicons-upload"></span></button>
		<button type="button" class="button wp-yes--remove-media"><span class="dashicons dashicons-trash"></span></button>
		<?php
		$this->render_field_description( $field );
	}

	/**
	 * Validate setting field.
	 *
	 * This function is hooked into sanitize_option_{$option} filter.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value          The sanitized option value.
	 * @param string $option         The option name.
	 *
	 * @throws Exception Throw an exception if the field validation not passed.
	 */
	public function validate_field( $value, $option ) {
		$field = $this->get_field( $option );

		if ( empty( $field ) ) {
			return $value;
		}

		try {
			if ( ! isset( $_POST['wp_yes_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_yes_nonce'] ) ), $this->menu_slug ) ) {
				throw new Exception( __( 'Sorry, your nonce did not verify.', 'wp_yes_txt' ) );
			}

			if ( $field['conditional'] ) {
				$need_validation = true;

				foreach ( $field['conditional'] as $conditional_key => $conditional_value ) {
					$conditional_value_submit = isset( $_POST[ $conditional_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $conditional_key ] ) ) : '';

					$need_validation = is_array( $conditional_value ) ? in_array( $conditional_value_submit, $conditional_value, true ) : $conditional_value === $conditional_value_submit;

					if ( ! $need_validation ) {
						break;
					}
				}

				if ( ! $need_validation ) {
					return $value;
				}
			}

			$validate_value = is_array( $value ) ? array_map( 'trim', $value ) : trim( $value );

			// Validate if field is required.
			if ( $field['required'] ) {
				if ( ( is_array( $validate_value ) && empty( $validate_value ) ) || ( ! is_array( $validate_value ) && ! strlen( $validate_value ) ) ) {
					throw new Exception( __( 'Value can not be empty.', 'wp_yes_txt' ) );
				}
			}

			if ( $validate_value ) {
				// Validate by field type.
				switch ( $field['type'] ) {
					case 'email':
						if ( ! is_email( $validate_value ) ) {
							throw new Exception( __( 'Value must be a valid email address.', 'wp_yes_txt' ) );
						}
						break;

					case 'url':
						if ( false === filter_var( $validate_value, FILTER_VALIDATE_URL ) ) {
							throw new Exception( __( 'Value must be a valid URL.', 'wp_yes_txt' ) );
						}
						break;

					case 'number':
						if ( $validate_value > intval( $validate_value ) || $validate_value < intval( $validate_value ) ) {
							throw new Exception( __( 'Value must be an integer.', 'wp_yes_txt' ) );
						}
						break;

					case 'decimal':
						if ( ! is_numeric( $validate_value ) ) {
							throw new Exception( __( 'Value must be a number.', 'wp_yes_txt' ) );
						}
						break;
				}
			}

			return $validate_value;
		} catch ( Exception $e ) {
			$field_name = $this->get_field_name( $field );
			$label      = ! empty( $field['label'] ) ? $field['label'] : $this->humanize_slug( $option );
			$error_msg  = sprintf( '%1$s: %2$s', $label, $e->getMessage() );

			add_settings_error( $option, $field_name, $error_msg );

			return get_option( $option, $field['default'] );
		}
	}

	/**
	 * Populate settings data.
	 *
	 * @since 1.0.3
	 *
	 * @return void
	 */
	protected function populate_settings() {
		$settings = array();

		foreach ( $this->all_tabs as $unique_id => $tab ) {
			$settings[ $unique_id ] = $tab;
		}

		foreach ( $this->all_sections as $unique_id => $section ) {
			$tab_unique_id = $this->get_tab_id( $section['tab'] );

			if ( ! isset( $settings[ $tab_unique_id ] ) ) {
				continue;
			}

			$settings[ $tab_unique_id ]['sections'][ $unique_id ] = $section;
		}

		foreach ( $this->all_fields as $unique_id => $field ) {
			$tab_unique_id = $this->get_tab_id( $field['tab'] );

			if ( ! isset( $settings[ $tab_unique_id ] ) ) {
				continue;
			}

			$section_unique_id = $this->get_section_id( $field['section'], $field['tab'] );

			if ( ! isset( $settings[ $tab_unique_id ]['sections'][ $section_unique_id ] ) ) {
				continue;
			}

			$settings[ $tab_unique_id ]['sections'][ $section_unique_id ]['fields'][ $unique_id ] = $field;
		}

		$this->settings = apply_filters( 'wp_yes_settings', $settings, $this );
	}

	/**
	 * Initialize and build the settings tabs, sections and fields.
	 *
	 * @since 1.0.0
	 *
	 * @return void
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
	 * @since 1.0.0
	 *
	 * This function is hooked into admin_init.
	 *
	 * @return void
	 */
	public function register_settings() {
		if ( ! $this->settings ) {
			return;
		}

		foreach ( $this->settings as $tab_key => $tab ) {

			// Add action hook to render for the tab content.
			add_action( 'wp_yes_' . $this->menu_slug . '_setting_tab_' . $tab_key, $tab['callback'] );

			foreach ( $tab['sections'] as $section ) {
				$section_unique_id = $this->get_section_id( $section );

				// Add a new section to a settings page.
				add_settings_section( $section_unique_id, $section['title'], $section['callback'], $section_unique_id );

				foreach ( $section['fields'] as $field ) {
					$option = $this->get_field_name( $field );

					// Register a settings field to a settings page and section.
					add_settings_field( $option, $field['label'], array( $this, 'render_field' ), $section_unique_id, $section_unique_id, $field );

					// Register a setting and its data.
					register_setting(
						$this->menu_slug,
						$option,
						array(
							'type'              => $field['data_type'],
							'group'             => $this->menu_slug,
							'description'       => $field['description'],
							'sanitize_callback' => $field['sanitize_callback'],
							'show_in_rest'      => $field['show_in_rest'],
						)
					);

					// Add filter hook to validate for the setting field.
					add_filter( "sanitize_option_{$option}", array( $this, 'validate_field' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Render settings page title
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function page_title() {
		?>
		<h1 class="<?php echo empty( $this->buttons ) ? '' : esc_attr( 'wp-heading-inline' ); ?>">
			<?php echo esc_html( $this->menu_args['page_title'] ); ?>
			<?php if ( ! empty( $this->buttons ) ) : ?>
				<?php foreach ( $this->buttons as $index => $button ) : ?>
					<a href="<?php echo esc_url( $button['url'] ); ?>" id="<?php echo empty( $button['id'] ) ? 'button-' . esc_attr( $this->menu_slug ) . '-' . esc_attr( $index ) : esc_attr( $button['id'] ); ?>" class="page-title-action"><?php echo esc_html( $button['text'] ); ?></a>
				<?php endforeach; ?>
				<hr class="wp-header-end">
			<?php endif; ?>
		</h1>
		<?php
	}

	/**
	 * Render the settings form.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_form() {
		?>
		<div class="wrap wp-yes--wrap">
			<?php $this->page_title(); ?>
			<?php settings_errors( '', false, $this->hide_on_update ); ?>
			<?php if ( 1 < count( $this->settings ) ) : ?>
				<div class="metabox-holder">
					<h2 class="wp-yes--nav-tab-wrapper nav-tab-wrapper">
						<?php foreach ( $this->settings as $tab_key => $tab ) : ?>
						<a href="#<?php echo esc_attr( $tab_key ); ?>" class="wp-yes--nav-tab nav-tab" id="<?php echo esc_attr( $tab_key ); ?>-tab"><?php echo esc_html( $tab['title'] ); ?></a>
						<?php endforeach; ?>
					</h2>
				</div>
				<div class="clear"></div>
			<?php endif; ?>
			<form method="post" action="options.php" id="wp-yes--form--<?php echo esc_attr( $this->menu_slug ); ?>" class="wp-yes--form">
				<?php wp_nonce_field( $this->menu_slug, 'wp_yes_nonce' ); ?>
				<div class="wp-yes--tab-wrapper metabox-holder">
					<?php foreach ( $this->settings as $tab_key => $tab ) : ?>
						<div id="<?php echo esc_attr( $tab_key ); ?>" class="wp-yes--tab-group">
							<?php do_action( 'wp_yes_' . $this->menu_slug . '_setting_tab_' . $tab_key, $tab ); ?>
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( 0 < count( $this->settings ) ) : ?>
				<div class="wp-yes--button-wrapper">
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
	 * @since 1.0.0
	 *
	 * This function is hooked into admin_menu to affect admin only.
	 *
	 * @return void
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
	 * @since 1.0.0
	 * @param array $help_tabs Indexed array of properties for the new tab object.
	 */
	public function add_help_tabs( $help_tabs ) {

		// Validate help tabs property.
		if ( ! $help_tabs || ! is_array( $help_tabs ) ) {
			return;
		}

		foreach ( $help_tabs as $help_tab ) {
			$this->add_help_tab( $help_tab );
		}
	}

	/**
	 * Add help tab item.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * @since    1.0.0
	 * @param string $hook Current admin page slug loaded.
	 */
	public function admin_enqueue_scripts( $hook ) {
		if ( ! $this->menu_args['callback'] || ! $this->is_current_screen( $hook ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_media();

		wp_enqueue_script( 'wp_yes', $this->get_url( 'assets/wp-yes.js' ), array( 'jquery', 'underscore' ), self::$version, true );
		wp_enqueue_style( 'wp_yes', $this->get_url( 'assets/wp-yes.css' ), array(), self::$version );
		wp_localize_script(
			'wp_yes',
			'wpYesVar',
			array(
				'menuSlug' => $this->menu_slug,
				'tabs'     => array_values( (array) $this->all_tabs ),
				'section'  => array_values( (array) $this->all_sections ),
				'fields'   => array_values( (array) $this->all_fields ),
			)
		);

		if ( $this->custom_styles ) {
			foreach ( $this->custom_styles as $custom_style ) {
				call_user_func_array( 'wp_enqueue_style', (array) $custom_style );
			}
		}

		if ( $this->custom_scripts ) {
			foreach ( $this->custom_scripts as $custom_script ) {
				call_user_func_array( 'wp_enqueue_script', (array) $custom_script );
			}
		}

		// Do custom action hook to enqueue custom script and styles.
		do_action( 'wp_yes_' . $this->menu_slug . '_admin_enqueue_scripts', $hook, $this );
	}

	/**
	 * Print scripts needed to initiate Color Picker & Tab element.
	 *
	 * This function is hooked into admin_print_footer_scripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_footer_js() {
		$screen = get_current_screen();

		if ( ! $this->menu_args['callback'] || ! $screen || ! $this->is_current_screen( $screen->id ) ) {
			return;
		}

		// Do custom action hook to print scripts needed.
		do_action( 'wp_yes_' . $this->menu_slug . '_admin_footer_js', $screen, $this );
	}

	/**
	 * Sort array by position
	 *
	 * @since    1.0.0
	 * @param array $a First index of the array.
	 * @param array $b Compared array.
	 * @return integer
	 */
	protected function sort_by_position( $a, $b ) {
		$a_position = isset( $a['position'] ) ? (int) $a['position'] : 10;
		$b_position = isset( $b['position'] ) ? (int) $b['position'] : 10;
		$a_index    = isset( $a['index'] ) ? (int) $a['index'] : 10;
		$b_index    = isset( $b['index'] ) ? (int) $b['index'] : 10;

		if ( $a_position === $b_position ) {
			return ( $a_index < $b_index ) ? -1 : 1;
		}

		return ( $a_position < $b_position ) ? -1 : 1;
	}

	/**
	 * Humanize slug to make them human readable.
	 *
	 * @since 1.0.0
	 * @param string $slug Slug string that will be humanized.
	 * @return string Humanized text.
	 */
	protected function humanize_slug( $slug ) {
		// Split slug by dash and underscore as array.
		$words = preg_split( '/(_|-)/', $slug );

		// Check if array words is empty.
		if ( empty( $words ) ) {
			return $slug;
		}

		// Define ignored words.
		$ignores = apply_filters( 'wp_yes_humanize_slug_ignores', array( 'a', 'and', 'or', 'to', 'in', 'at', 'in', 'of' ) );

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

	/**
	 * Check if the plugin meets requirements
	 *
	 * @since 1.0.0
	 *
	 * @return bool|WP_Error true on success, WP_Error on failure.
	 */
	public static function check_requirements() {
		if ( version_compare( PHP_VERSION, '5.6.3', '<' ) ) {
			return new WP_Error( 'php_version', __( 'This plugin requires PHP 5.6.3 or higher!', 'wp_yes_txt' ) );
		}

		if ( version_compare( get_bloginfo( 'version' ), '4.9', '<' ) ) {
			return new WP_Error( 'php_version', __( 'WordPress must be at least version 4.7.3 or greater!', 'wp_yes_txt' ) );
		}

		return true;
	}

	/**
	 * Check if current admin screen is match
	 *
	 * @since 1.0.3
	 *
	 * @param string $hook Current admin screen hook.
	 *
	 * @return boolean
	 */
	private function is_current_screen( $hook ) {
		$parts = explode( '_' . $this->menu_slug, $hook );

		if ( 2 !== count( $parts ) ) {
			return false;
		}

		return 1 === count( array_filter( $parts ) );
	}

	/**
	 * Check if integrated as plugin.
	 *
	 * @since 1.0.3
	 *
	 * @return boolean
	 */
	public static function is_as_plugin() {
		return self::is_as_theme() || self::is_as_child_theme() ? false : true;
	}

	/**
	 * Check if integrated as theme.
	 *
	 * @since 1.0.6
	 *
	 * @return boolean
	 */
	public static function is_as_theme() {
		if ( 0 === strpos( wp_normalize_path( __FILE__ ), wp_normalize_path( get_template_directory() ) ) ) {
			return true;
		}

		return ! is_child_theme() && false !== strpos( wp_normalize_path( __FILE__ ), '/' . get_template() . '/' );
	}

	/**
	 * Check if integrated as child theme.
	 *
	 * @since 1.0.6
	 *
	 * @return boolean
	 */
	public static function is_as_child_theme() {
		if ( 0 === strpos( wp_normalize_path( __FILE__ ), wp_normalize_path( get_stylesheet_directory() ) ) ) {
			return true;
		}

		return is_child_theme() && false !== strpos( wp_normalize_path( __FILE__ ), '/' . get_template() . '/' );
	}

	/**
	 * Get base URL
	 *
	 * @since 1.0.3
	 *
	 * @return string
	 */
	public function get_base_url() {
		$slice_offset = self::is_as_plugin() ? -5 : -4;
		$slice_length = self::is_as_plugin() ? null : 3;
		$dir_split    = explode( '/', trim( wp_normalize_path( __DIR__ ), '/' ) );
		$dir_base     = '/' . implode( '/', array_slice( $dir_split, $slice_offset, $slice_length ) ) ;

		if ( self::is_as_child_theme() ) {
			return untrailingslashit( get_stylesheet_directory() . $dir_base );
		}

		if ( self::is_as_theme() ) {
			return untrailingslashit( get_template_directory_uri() . $dir_base );
		}

		return untrailingslashit( plugin_dir_url( $dir_base ) );
	}

	/**
	 * Get base directory
	 *
	 * @since 1.0.3
	 *
	 * @return string
	 */
	public function get_base_dir() {
		return untrailingslashit( wp_normalize_path( dirname( __DIR__ ) ) );
	}

	/**
	 * Get URL
	 *
	 * @since 1.0.3
	 *
	 * @param string $path Path to be appended.
	 *
	 * @return string
	 */
	public function get_url( $path = '' ) {
		if ( ! $path ) {
			return $this->get_base_url();
		}

		return $this->get_base_url() . '/' . ltrim( wp_normalize_path( $path ), '/' );
	}

	/**
	 * Get directory
	 *
	 * @since 1.0.3
	 *
	 * @param string $path Path to be appended.
	 *
	 * @return string
	 */
	public function get_dir( $path = '' ) {
		if ( ! $path ) {
			return $this->get_base_dir();
		}

		return $this->get_base_dir() . '/' . ltrim( wp_normalize_path( $path ), '/' );
	}

	/**
	 * Register custom script to be enqueued in admin screen.
	 *
	 * @since 1.0.3
	 *
	 * @param string  $handle Name of the script. Should be unique.
	 * @param string  $src Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param array   $deps An array of registered script handles this script depends on.
	 * @param boolean $ver String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes.
	 *                     If version is set to false, a version number is automatically added equal to current installed WordPress version.
	 *                     If set to null, no version is added.
	 * @param boolean $in_footer Whether to enqueue the script before </body> instead of in the <head>.
	 *
	 * @return void
	 */
	public function enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		$this->custom_scripts[ $handle ] = array( $handle, $src, $deps, $ver, $in_footer );
	}

	/**
	 * Register custom CSS stylesheet to be enqueued in admin screen.
	 *
	 * @since 1.0.3
	 *
	 * @param string  $handle Name of the script. Should be unique.
	 * @param string  $src Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param array   $deps An array of registered script handles this script depends on.
	 * @param boolean $ver String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes.
	 *                     If version is set to false, a version number is automatically added equal to current installed WordPress version.
	 *                     If set to null, no version is added.
	 * @param boolean $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @return void
	 */
	public function enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = false ) {
		$this->custom_styles[ $handle ] = array( $handle, $src, $deps, $ver, $media );
	}
}
