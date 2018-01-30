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

if ( ! function_exists( 'wpyes_init_example_1' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_init_example_1() {

		$settings = new Wpyes( 'wpyes_example_1' ); // Initialize the Wpyes class.

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
				'id' => 'wpyes_example_1_field_1_section_1_tab_1',
			)
		);

		$settings->add_field(
			array(
				'id' => 'wpyes_example_1_field_2_section_1_tab_1',
			)
		);

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_init_example_1();
}// End if().

if ( ! function_exists( 'wpyes_init_example_2' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_init_example_2() {
		$settings = new Wpyes(
			'wpyes_example_2', array(
				'action_button_url'  => admin_url( 'test' ),
				'action_button_text' => 'Add New',
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
				'id'       => 'wpyes_example_2_field_1_section_1_tab_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_2_field_2_section_1_tab_1',
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
				'id'       => 'wpyes_example_2_field_1_section_1_tab_2',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wpyes_example_2_field_2_section_1_tab_2',
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

		$settings->add_action_button( 'Add New', 'admin.php' );

		$settings->init(); // Run the Wpyes class.
	}

	wpyes_init_example_2();
}// End if().
