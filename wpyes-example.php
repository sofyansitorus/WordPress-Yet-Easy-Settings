<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/sofyansitorus
 * @since             0.0.1
 * @package           Wpyes
 *
 * Plugin Name:       Wpyes Example
 * Plugin URI:        https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings
 * Description:       This is sample for how to use WordPress-Yet-Easy-Settings in a plugin.
 * Version:           0.0.1
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpyes
 * Domain Path:       /languages
 */

/**
 * Load plugin textdomain.
 *
 * @since 0.0.1
 */
function wpyes_example_load_textdomain() {
	load_plugin_textdomain( 'wpyes', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wpyes_example_load_textdomain' );

// Include the dependencies.
if ( ! class_exists( 'Wpyes' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpyes.php';
}

if ( ! function_exists( 'wpyes_simple' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_simple() {

		$settings = new Wpyes( 'wpyes_simple' ); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id' => 'wpyes_simple_field_1',
			)
		);

		$settings->add_field(
			array(
				'id' => 'wpyes_simple_field_2',
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_simple();
}// End if().

if ( ! function_exists( 'wpyes_tabs' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_tabs() {
		$settings = new Wpyes( 'wpyes_tabs' ); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_tabs_field_1',
				'required' => true,
				'type'     => 'text',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_tabs_field_2',
				'required' => true,
				'type'     => 'multicheckbox',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_tab(
			array(
				'id' => 'tab_2',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_tabs_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_tabs_field_4',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_tabs();
}// End if().

if ( ! function_exists( 'wpyes_button' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_button() {
		$settings = new Wpyes( 'wpyes_button' ); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_button_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_button_field_2',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_button( 'Custom Action Button', 'index.php' );

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_button();
}// End if().


if ( ! function_exists( 'wpyes_help_tabs' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_help_tabs() {
		$settings = new Wpyes( 'wpyes_help_tabs' ); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_help_tabs_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_help_tabs_field_2',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_help_tab(
			array(
				'id'      => 'my_help_tab',
				'title'   => __( 'My Help Tab' ),
				'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
			)
		);

		$settings->add_help_tab(
			array(
				'id'      => 'my_help_tab2',
				'title'   => __( 'My Help Tab2' ),
				'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here 2.' ) . '</p>',
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_help_tabs();
}// End if().

if ( ! function_exists( 'wpyes_submenu' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_submenu() {
		$settings = new Wpyes(
			'wpyes_submenu', array(
				'method'      => 'add_submenu_page',
				'parent_slug' => 'wpyes_simple',
			)
		); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_1',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_submenu_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_submenu_field_2',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_submenu();
}// End if().


if ( ! function_exists( 'wpyes_all' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wpyes_all() {

		$settings = new Wpyes(
			'wpyes_all', array(
				'page_title' => __( 'All Settings Fields', 'wpyes' ),
			)
		); // Initialize the Wpyes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_basic',
				'id' => __( 'Basic Field Type', 'wpyes' ),
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_text',
				'label' => __( 'Text', 'wpyes' ),
				'type'  => 'text',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_url',
				'label' => __( 'URL', 'wpyes' ),
				'type'  => 'url',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_number',
				'label' => __( 'Number', 'wpyes' ),
				'type'  => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_decimal',
				'label' => __( 'Decimal', 'wpyes' ),
				'type'  => 'decimal',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_password',
				'label' => __( 'Password', 'wpyes' ),
				'type'  => 'password',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_email',
				'label' => __( 'Email', 'wpyes' ),
				'type'  => 'email',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_textarea',
				'label' => __( 'Textarea', 'wpyes' ),
				'type'  => 'textarea',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_checkbox',
				'label' => __( 'Checkbox', 'wpyes' ),
				'type'  => 'checkbox',
			)
		);

		$settings->add_field(
			array(
				'id'      => 'wpyes_all_field_multicheckbox',
				'label'   => __( 'Multi Checkbox', 'wpyes' ),
				'type'    => 'multicheckbox',
				'options' => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_field(
			array(
				'id'      => 'wpyes_all_field_select',
				'label'   => __( 'Select', 'wpyes' ),
				'type'    => 'select',
				'options' => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_field(
			array(
				'id'      => 'wpyes_all_field_multiselect',
				'label'   => __( 'Multi Select', 'wpyes' ),
				'type'    => 'multiselect',
				'options' => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_field(
			array(
				'id'      => 'wpyes_all_field_radio',
				'label'   => __( 'Radio', 'wpyes' ),
				'type'    => 'radio',
				'options' => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->add_section(
			array(
				'id' => 'section_advanced',
				'id' => __( 'Advanced Field Type', 'wpyes' ),
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_color',
				'label' => __( 'Color Picker', 'wpyes' ),
				'type'  => 'color',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_file',
				'label' => __( 'File Upload', 'wpyes' ),
				'type'  => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wpyes_all_field_wysiwyg',
				'label' => __( 'WYSIWYG', 'wpyes' ),
				'type'  => 'wysiwyg',
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_all();
}// End if().
