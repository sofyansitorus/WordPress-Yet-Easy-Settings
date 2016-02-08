<?php

/**
 * Wpyes class.
 *
 * WordPress Yet Easy Settings class is PHP class for easy to build wordpress admin settings page
 *
 * @since      0.0.1
 * @package    Wpyes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */

class Wpyes {

	/**
	 * settings prefix
	 *
	 * @var string
	 */
	private $auto_prefix = true;

	/**
	 * settings prefix
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * settings tabs array
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * settings tabs array
	 *
	 * @var array
	 */
	private $settings_tabs = array();

	/**
	 * settings sections array
	 *
	 * @var array
	 */
	private $settings_sections = array();

	/**
	 * Settings fields array
	 *
	 * @var array
	 */
	private $settings_fields = array();

	/**
	 * Settings menu page
	 *
	 * @var array
	 */
	private $admin_pages = array();

	/**
	 * Settings menu page index
	 *
	 * @var array
	 */
	private $admin_page_index;

	/**
	 * Constructor method
	 */
	public function __construct( $prefix ){
		$this->prefix = $prefix;
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		do_action( $this->prefix . '_after_construct', $this );
	}

	/**
	 * Disable auto prepend the prefix for option name.
	 * Make sure you run this method before init method
	 */
	public function disable_auto_prefix() {
		$this->auto_prefix = false;
	}

	/**
	 * Get settings tabs, sections and fields as associative array array
	 *
	 * @var array
	 */
	private function get_settings() {
		return apply_filters( $this->prefix . '_settings', $this->settings );
	}

	/**
	 * Normalize settings tab
	 */
	private function normalize_tab( $args ) {

		$defaults = array(
			'id' => '',
			'label' => '',
			'sections' => array(),
			'priority' => 10
		);

		$args = wp_parse_args( $args, $defaults );
		if( empty( $args['label'] ) ){
			$args['label'] = $this->humanize_slug( $args['id'] );
		}
		return $args;
	}

	/**
	 * Add settings tabs
	 *
	 * @param array   $tabs setting tabs array
	 */
	public function add_tabs( $tabs ) {

		if( is_array( $tabs ) ){
			foreach ($tabs as $tab) {
				$this->add_tab($tab);
			}
		}

		return $this->get_tabs();
	}

	/**
	 * Add settings tab
	 *
	 * @param array   $tabs setting tabs array
	 */
	private function add_tab( $tab ) {

		$tab = $this->normalize_tab( $tab );

		if( !empty( $tab['id'] ) ){
			$this->settings_tabs[$tab['id']] = $tab;
		}

		return $this->get_tabs();
	}

	/**
	 * Get settings tabs
	 *
	 * @return array   $tabs setting tabs array
	 */
	private function get_tabs() {
		$settings_tabs = $this->settings_tabs;
		uasort( $settings_tabs, array( $this, 'sort_by_priority' ) );
		return apply_filters( $this->prefix.'_settings_tabs', $settings_tabs );
	}

	/**
	 * Get settings tab
	 *
	 * @return array   $tab setting tab array
	 */
	private function get_tab( $id ) {

		$tabs = $this->get_tabs();
		if( isset( $tabs[$id] ) ){
			return $tabs[$id];
		}

		$tab_found = array();
		foreach ( $tabs as $tab)  {
			if( $tab['id'] == $id ){
				$tab_found = $tab;
				break;
			}
		}
		return $tab_found;
	}

	/**
	 * Render the settings tab
	 *
	 * @return array   $tab setting tab array
	 */
	public function render_tab( $tab, $tab_key ){
		foreach ($tab['sections'] as $section_key => $section) {
			do_settings_sections( $tab_key . '_' . $section_key );
		}
	}

	/**
	 * Normalize settings section
	 */
	private function normalize_section( $args ) {
		$defaults = array(
			'id' => '',
			'title' => '',
			'callback' => null,
			'fields' => array(),
			'tab' => '',
			'priority' => 10
		);

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Add settings sections
	 *
	 * @param array   $sections setting sections array
	 */
	public function add_sections( $sections ) {

		if( is_array( $sections ) ){
			foreach ( $sections as $section ) {
				$this->add_section( $section );
			}
		}

		return $this->get_sections();
	}

	/**
	 * Add a single section
	 *
	 * @param array   $section
	 */
	private function add_section( $section ) {

		$section = $this->normalize_section( $section );

		if(!empty( $section['id'] ) ){
			$this->settings_sections[$section['id']] = $section;
		}

		return $this->get_sections();
	}

	/**
	 * Get settings sections
	 *
	 * @return array   $sections setting sections array
	 */
	private function get_sections() {
		$settings_sections = $this->settings_sections;
		uasort( $settings_sections, array( $this, 'sort_by_priority' ) );
		return apply_filters( $this->prefix.'_settings_sections', $settings_sections );
	}

	/**
	 * Get settings section
	 *
	 * @return array   $section setting section array
	 */
	private function get_section( $id ) {

		$sections = $this->get_sections();
		if( isset( $sections[$id] ) ){
			return $sections[$id];
		}

		$section_found = array();
		foreach ($sections as $section) {
			if( $section['id'] == $id ){
				$section_found = $section;
				break;
			}
		}
		return $section_found;
	}

	/**
	 * Normalize field to have default field keys
	 *
	 * @return array   $args field args array
	 */
	private function normalize_field( $args ){

		$defaults = array(
			'type' => 'text',
			'id' => '',
			'label' => '',
			'desc' => '',
			'callback' => '',
			'callback_before' => '',
			'callback_after' => '',
			'sanitize' => '',
			'section' => '',
			'default' => '',
			'options' => array(),
			'attrs' => array(),
			'priority' => 10
		);

		return wp_parse_args( $args, $defaults );

	}

	/**
	 * Add settings fields
	 *
	 * @param array   $fields settings fields array
	 */
	public function add_fields( $fields ) {

		if( is_array( $fields ) ){
			foreach ( $fields as $field ) {
				$this->add_field( $field );
			}
		}

		return $this->get_fields();
	}

	/**
	 * Add settings field
	 *
	 * @param array   $field settings field array
	 */
	private function add_field( $field ) {

		$field = $this->normalize_field( $field );

		if(!empty( $field['id'] ) ){
			$this->settings_fields[$field['id']] = $field;
		}

		return $this->get_fields();
	}

	/**
	 * Get settings fields
	 *
	 * @return array   $fields settings fields array
	 */
	private function get_fields() {
		$settings_fields = $this->settings_fields;
		uasort( $settings_fields, array( $this, 'sort_by_priority' ) );
		return apply_filters( $this->prefix.'_settings_fields', $settings_fields );
	}

	/**
	 * Get settings field
	 *
	 * @return array   $field settings field array
	 */
	private function get_field( $id ) {
		$fields = $this->get_fields();
		if( isset( $fields[$id] ) ){
			return $fields[$id];
		}

		$field_found = array();
		foreach ($fields as $field) {
			if( $field['id'] == $id ){
				$field_found = $field;
				break;
			}
		}
		return $field_found;
	}

	/**
	 * Get settings field id
	 */
	private function get_field_id( $args ) {
		$type = $args['type'];
		switch ( $type ) {
			case 'text':
					$id = $args['section'] . '_' . $args['id'];
				break;
			
			default:
					$id = $args['section'] . '_' . $args['id'];
				break;
		}
		return ($this->auto_prefix === true ) ? $this->prefix . '_' . $id : $id;
	}

	/**
	 * Get settings field name
	 */
	private function get_field_name( $args ) {
		$type = $args['type'];
		switch ( $type ) {
			case 'text':
					$name = $args['id'];
				break;
			
			default:
					$name = $args['id'];
				break;
		}
		return ($this->auto_prefix === true) ? $this->prefix . '_' . $name : $name;
	}

	/**
	 * Get settings field value
	 */
	private function get_field_value( $args ) {
		return get_option( $this->get_field_name($args), $args['default']);
	}

	/**
	 * Get settings field attributes
	 */
	private function get_field_attrs( $args ) {
		$attrs_temp = array();
		$attrs = array();
		$type = $args['type'];
		switch ( $type ) {
			case 'text':
			case 'url':
			case 'number':
			case 'password':
			case 'email':
			case 'file':
					$attrs_temp['class'] = 'regular-text';
				break;
			case 'color':
					$attrs_temp['class'] = 'regular-text wp-color-picker-field';
				break;
			case 'textarea':
					$attrs_temp['class'] = 'large-text';
					$attrs_temp['rows'] = '10';
					$attrs_temp['cols'] = '50';
				break;
			
			default:
				break;
		}

		$attrs_temp = apply_filters( $this->prefix . '_field_attrs', $attrs_temp, $args );

		foreach ($attrs_temp as $key => $value) {
			$attrs[] = $key . '="' . esc_attr( $value ) . '"';
		}

		return implode(" ", $attrs);
	}

	/**
	 * Get field description for display
	 *
	 * @param array   $args settings field args
	 */
	private function get_field_description( $args ) {
		if ( ! empty( $args['desc'] ) ) {
			$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
		} else {
			$desc = '';
		}

		return $desc;
	}

	/**
	 * Render the setting field
	 */
	public function render_field( $args ) {
		$type = $args['type'];

		if( !empty( $args['callback_before'] ) ){
			call_user_func( $args['callback_before'], $args );
		}

		if ( method_exists( $this, 'render_field_' . $type ) ){
			call_user_func( array( $this, 'render_field_' . $type ), $args );
		}else{
			if( !empty( $args['callback'] ) ){
				call_user_func( $args['callback'], $args );
			}else{
				do_action( $this->prefix . '_render_field_' . $type, $args );
			}
		}

		if( !empty( $args['callback_after'] ) ){
			call_user_func( $args['callback_after'], $args );
		}
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	private function render_field_text( $args ) {
		$type = $args['type'];
		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = '';
		$html .= sprintf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" %5$s/>', $type, $this->get_field_id( $args ), $this->get_field_name( $args ), $value , $attrs);
		$html .= $this->get_field_description( $args );

		echo $html;
	}

	/**
	 * Displays a url field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	private function render_field_url( $args ) {
		$this->render_field_text( $args );
	}

	/**
	 * Displays a number field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	private function render_field_number( $args ) {
		$this->render_field_text( $args );
	}

	/**
	 * Displays a password field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_password( $args ) {
		$this->render_field_text( $args );
	}

	/**
	 * Displays a password field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_email( $args ) {
		$this->render_field_text( $args );
	}

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_checkbox( $args ) {

		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = '<fieldset>';
		$html .= sprintf( '<label for="%1$s">', $this->get_field_id( $args ) );
		$html .= sprintf( '<input type="hidden" name="%1$s" value="off" />', $this->get_field_name( $args ) );
		$html .= sprintf( '<input type="checkbox" id="%1$s" name="%2$s" value="on" %3$s %4$s />', $this->get_field_id( $args ), $this->get_field_name( $args ), checked( $value, 'on', false ), $attrs );
		$html .= sprintf( '%1$s</label>', $args['desc'] );
		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_multicheck( $args ) {

		$value = $this->get_field_value( $args );
		$attrs = $this->get_field_attrs( $args );

		$html = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$checked = isset( $value[$key] ) ? $value[$key] : '0';
			$html .= sprintf( '<label for="%1$s[%2$s]">', $this->get_field_id( $args ), $key );
			$html .= sprintf( '<input type="checkbox" id="%1$s[%3$s]" name="%2$s[%3$s]" value="%3$s" %4$s %5$s />', $this->get_field_id( $args ), $this->get_field_name( $args ), $key, checked( $checked, $key, false ), $attrs );
			$html .= sprintf( '%1$s</label><br>',  $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * Displays a multicheckbox a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_radio( $args ) {

		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = '<fieldset>';
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<label for="%1$s[%2$s]">', $this->get_field_id( $args ), $key );
			$html .= sprintf( '<input type="radio" id="%1$s[%3$s]" name="%2$s" value="%3$s" %4$s %5$s />', $this->get_field_id( $args ), $this->get_field_name( $args ), $key, checked( $value, $key, false ), $attrs );
			$html .= sprintf( '%1$s</label><br>', $label );
		}
		$html .= $this->get_field_description( $args );
		$html .= '</fieldset>';

		echo $html;
	}

	/**
	 * Displays a selectbox for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_select( $args ) {

		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = sprintf( '<select name="%2$s" id="%1$s" %3$s>', $this->get_field_id( $args ), $this->get_field_name( $args ), $attrs );
		foreach ( $args['options'] as $key => $label ) {
			$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
		}
		$html .= sprintf( '</select>' );
		$html .= $this->get_field_description( $args );

		echo $html;
	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_textarea( $args ) {

		$value = esc_textarea( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = sprintf( '<textarea id="%1$s" name="%2$s" %3$s>%4$s</textarea>', $this->get_field_id( $args ), $this->get_field_name( $args ), $attrs, $value );
		$html .= $this->get_field_description( $args );

		echo $html;
	}

	/**
	 * Displays a rich text textarea for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_wysiwyg( $args ) {

		$value = $this->get_field_value( $args );
		$size = isset( $args['options']['size'] ) ? $args['options']['size'] : '500px';

		echo '<div style="max-width: ' . $size . ';">';

		$editor_settings = array(
			'teeny' => true,
			'textarea_name' => $this->get_field_name( $args ),
			'textarea_rows' => 10
		);

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}

		wp_editor( $value, $this->get_field_name( $args ), $editor_settings );

		echo '</div>';

		echo $this->get_field_description( $args );
	}

	/**
	 * Displays a file upload field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_file( $args ) {

		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );
		$button_label = isset( $args['options']['button_label'] ) ?
						$args['options']['button_label'] :
						__( 'Choose File' );

		$html  = sprintf( '<input type="text" id="%1$s" name="%2$s" value="%4$s" %3$s />', $this->get_field_id( $args ), $this->get_field_name( $args ), $attrs, $value );
		$html .= '<input type="button" class="button button-browse-file" value="' . $button_label . '" />';
		$html .= $this->get_field_description( $args );

		echo $html;
	}

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @param array   $args settings field args
	 */
	public function render_field_color( $args ) {

		$value = esc_attr( $this->get_field_value( $args ) );
		$attrs = $this->get_field_attrs( $args );

		$html = sprintf( '<input type="text" id="%1$s" name="%2$s" value="%4$s" data-default-color="%5$s" %3$s />', $this->get_field_id( $args ), $this->get_field_name( $args ), $attrs, $value, $args['default'] );
		$html .= $this->get_field_description( $args );

		echo $html;
	}

	/**
	 * Initialize and build the settings sections and fileds
	 */
	public function init() {
		$tabs = $this->get_tabs();
		$sections = $this->get_sections();
		$fields = $this->get_fields();

		foreach ($fields as $field_key => $field) {
			if(isset($field['section']) && isset($sections[$field['section']])){
				$sections[$field['section']]['fields'][$field_key] = $field;
			}
		}

		foreach ($sections as $section_key => $section) {
			if(isset($section['tab']) && isset($tabs[$section['tab']])){
				$tabs[$section['tab']]['sections'][$section_key] = $section;
			}
		}

		$this->settings = $tabs;

		//$this->register_settings();

		add_action( 'admin_init', array($this, 'register_settings' ), 10);

		add_action( 'admin_menu', array($this, 'register_admin_page' ), 10);
	}

	/**
	 * Registers the settings sections and fileds to WordPress
	 */
	public function register_settings() {
		$settings = $this->get_settings();

		foreach ($settings as $tab_key => $tab) {
			foreach ($tab['sections'] as $section_key => $section) {
				add_settings_section( $section_key, $section['title'], $section['callback'], $tab_key . '_' . $section_key );
				foreach ($section['fields'] as $field_key => $field) {
					add_settings_field( $this->get_field_name( $field ), $field['label'], array( $this, 'render_field' ), $tab_key . '_' . $section_key, $section_key, $field );
					if( !empty( $field['sanitize'] ) ){
						register_setting( $this->prefix, $field_key, $field['sanitize'] );
					}else{
						register_setting( $this->prefix, $this->get_field_name( $field ) );  
					}
					
				}
			}
			add_action( $this->prefix . '_setting_tab_' . $tab_key, array( $this, 'render_tab' ), 10, 2 );
		}
		
	}

	public function set_admin_page($args = array() ){

		$defaults = array(
			'method' => 'add_menu_page',
			'page_title' => $this->prefix,
			'menu_title' => $this->prefix,
			'capability' => 'manage_options',
			'menu_slug' => $this->prefix,
			'callback' => array( $this, 'render_form' ),
			'icon_url' => '',
			'position' => null,
			'parent_slug' => ''
		);

		$this->admin_pages[] = wp_parse_args( $args, $defaults );
	}

	public function register_admin_page(){
		if( $this->admin_pages ){
			foreach ($this->admin_pages as $key => $admin_page) {
				$this->admin_page_index = $key;
				switch ($admin_page['method']) {
					case 'add_submenu_page':
						if( !empty($admin_page['parent_slug']) ){
							call_user_func(
								$admin_page['method'], 
								$admin_page['parent_slug'],
						        $admin_page['page_title'],
						        $admin_page['menu_title'],
						        $admin_page['capability'],
						        $admin_page['menu_slug'],
						        $admin_page['callback']
							);
						}
						break;
					
					default:
						call_user_func(
							$admin_page['method'], 
					        $admin_page['page_title'],
					        $admin_page['menu_title'],
					        $admin_page['capability'],
					        $admin_page['menu_slug'],
					        $admin_page['callback'],
					        $admin_page['icon_url'],
					        $admin_page['position']
						);
						break;
				}
			}
		}
	}

	/**
	 * Show the section settings forms
	 *
	 * This function displays every sections in a different form
	 */
	public function render_form() {

		if( $this->admin_pages ){
		?>
		<div class="wrap">
		<h1><?php echo $this->admin_pages[$this->admin_page_index]['page_title']; ?></h1>
		<?php
		}

		$settings = $this->get_settings();

		$html = '<h2 class="nav-tab-wrapper">';
		$settings = $this->get_settings();
		foreach ( $settings as $tab_key => $tab ) {
			$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab_key, $tab['label'] );
		}
		$html .= '</h2>';
		echo $html;
		?>
		<form method="post" action="options.php">
		<div class="metabox-holder">
			<?php foreach ( $settings as $tab_key => $tab ) { ?>
				<div id="<?php echo $tab['id']; ?>" class="group" style="display: none;">
					<?php
					do_action( $this->prefix . '_setting_tab_before_' . $tab_key, $tab, $tab_key );
					do_action( $this->prefix . '_setting_tab_' . $tab_key, $tab, $tab_key );
					do_action( $this->prefix . '_setting_tab_after_' . $tab_key, $tab, $tab_key );
					?>
				</div>
			<?php } ?>
		</div>
		<div style="padding-left: 10px">
			<?php settings_fields( $this->prefix ); ?>
			<?php submit_button(); ?>
		</div>
		</form>
		<?php
		if( $this->admin_pages ){
		?>
		</div>
		<?php
		}
		$this->script();
	}

	/**
	 * Tabbable JavaScript codes & Initiate Color Picker
	 *
	 * This code uses localstorage for displaying active tabs
	 */
	private function script() {
		?>
		<script>
			jQuery(document).ready(function($) {
				//Initiate Color Picker
				$('.wp-color-picker-field').wpColorPicker();

				// Switches option sections
				$('.group').hide();
				var activetab = '';
				if (typeof(localStorage) != 'undefined' ) {
					activetab = localStorage.getItem("activetab");
				}
				if (activetab != '' && $(activetab).length ) {
					$(activetab).fadeIn();
				} else {
					$('.group:first').fadeIn();
				}
				$('.group .collapsed').each(function(){
					$(this).find('input:checked').parent().parent().parent().nextAll().each(
					function(){
						if ($(this).hasClass('last')) {
							$(this).removeClass('hidden');
							return false;
						}
						$(this).filter('.hidden').removeClass('hidden');
					});
				});

				if (activetab != '' && $(activetab + '-tab').length ) {
					$(activetab + '-tab').addClass('nav-tab-active');
				}
				else {
					$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
				}
				$('.nav-tab-wrapper a').click(function(evt) {
					$('.nav-tab-wrapper a').removeClass('nav-tab-active');
					$(this).addClass('nav-tab-active').blur();
					var clicked_group = $(this).attr('href');
					if (typeof(localStorage) != 'undefined' ) {
						localStorage.setItem("activetab", $(this).attr('href'));
					}
					$('.group').hide();
					$(clicked_group).fadeIn();
					evt.preventDefault();
				});

				$('.button-browse-file').on('click', function (event) {
					event.preventDefault();

					var self = $(this);

					var input_field = self.closest('td').find('input[type="text"]');

					// Create the media frame.
					var file_frame = wp.media.frames.file_frame = wp.media({
						title: self.data('uploader_title'),
						button: {
							text: self.data('uploader_button_text'),
						},
						multiple: false
					});

					file_frame.on('select', function () {
						attachment = file_frame.state().get('selection').first().toJSON();

						input_field.val(attachment.url);
					});

					// Finally, open the modal
					file_frame.open();
				});
		});
		</script>
		<?php
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker', array( 'jquery' ) );
		wp_enqueue_media();
	}

	/**
	 * Sort array by priority
	 *
	 * @since    1.0.0
	 */
	private function sort_by_priority($a, $b){
		$a = isset($a['priority']) ? (int) $a['priority'] : 10;
		$b = isset($b['priority']) ? (int) $b['priority'] : 10;

		if ($a == $b){
			return 1;
		}

		return ($a < $b) ? -1 : 2;

	}

	private function humanize_slug( $slug ){
		$text = str_replace( array( '_', '-' ), " ", $slug);
		return trim( ucwords( strtolower( $text ) ) );
	}
}
