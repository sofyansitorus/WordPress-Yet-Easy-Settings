<?php
/**
 * Example file for WP_Yes Class.
 *
 * @link       https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings
 * @since      1.0.0
 * @package    WP_Yes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'wp_yes_button' ) ) {
	/**
	 * Example for settings with custom action button
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wp_yes_button() {
		$settings = new WP_Yes( 'wp_yes_button' ); // Initialize the WP_Yes class.

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
				'id'       => 'wp_yes_button_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_button_field_2',
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
		$settings->add_button( 'Custom Action Button 2', 'index.php' );

		$settings->init(); // Run the WP_Yes class.
	}
}
add_action( 'init', 'wp_yes_button' );
