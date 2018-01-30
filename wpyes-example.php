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
 * @since             1.0.0
 * @package           Wpyes
 *
 * Plugin Name:       Wpyes Example
 * Plugin URI:        https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings
 * Description:       This is sample for how to use WordPress-Yet-Easy-Settings in a plugin.
 * Version:           1.0.0
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
 * @since 1.0.0
 */
function wpyes_example_load_textdomain() {
	load_plugin_textdomain( 'wpyes_example', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wpyes_example_load_textdomain' );

// Include the dependencies.
if ( ! class_exists( 'Wpyes' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpyes.php';
}

if ( ! function_exists( 'wpyes_example_simple' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_simple() {

		$settings = new Wpyes( 'wpyes_example_simple' ); // Initialize the Wpyes class.

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
				'id' => 'wpyes_example_field_1',
			)
		);

		$settings->add_field(
			array(
				'id' => 'wpyes_example_field_2',
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_example_simple();
}// End if().

if ( ! function_exists( 'wpyes_example_tabs' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_tabs() {
		$settings = new Wpyes( 'wpyes_example_tabs' ); // Initialize the Wpyes class.

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
				'id'       => 'wpyes_example_tabs_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_tabs_field_2',
				'required' => true,
				'type'     => 'multiselect',
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
				'id'       => 'wpyes_example_tabs_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_tabs_field_4',
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

	wpyes_example_tabs();
}// End if().

if ( ! function_exists( 'wpyes_example_help_tabs' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_help_tabs() {
		$settings = new Wpyes( 'wpyes_example_help_tabs' ); // Initialize the Wpyes class.

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
				'id'       => 'wpyes_example_help_tabs_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_help_tabs_field_2',
				'required' => true,
				'type'     => 'multiselect',
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
				'id'       => 'wpyes_example_help_tabs_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_help_tabs_field_4',
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

	wpyes_example_help_tabs();
}// End if().

if ( ! function_exists( 'wpyes_example_action_button' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_action_button() {
		$settings = new Wpyes( 'wpyes_example_action_button' ); // Initialize the Wpyes class.

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
				'id'       => 'wpyes_example_action_button_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_action_button_field_2',
				'required' => true,
				'type'     => 'multiselect',
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
				'id'       => 'wpyes_example_action_button_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_action_button_field_4',
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

		$settings->add_action_button( 'New Custom Button', 'index.php' );

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_example_action_button();
}// End if().

if ( ! function_exists( 'wpyes_example_submenu' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_submenu() {
		$settings = new Wpyes(
			'wpyes_example_submenu', array(
				'method'      => 'add_submenu_page',
				'parent_slug' => 'wpyes_example_simple',
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
				'id'       => 'wpyes_example_submenu_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_submenu_field_2',
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

		$settings->add_action_button( 'New Custom Button', 'index.php' );

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_example_submenu();
}// End if().


if ( ! function_exists( 'wpyes_example_all_fields' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_all_fields() {
		$settings = new Wpyes( 'wpyes_example_all_fields' ); // Initialize the Wpyes class.

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
				'id'       => 'wpyes_example_all_fields_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_all_fields_field_2',
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

		$settings->add_action_button( 'New Custom Button', 'index.php' );

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_example_all_fields();
}// End if().
