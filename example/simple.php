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

if ( ! function_exists( 'wp_yes_example_simple' ) ) {
	/**
	 * Example for simple settings
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wp_yes_example_simple() {
		$settings = new WP_Yes( 'wp_yes_example_simple' ); // Initialize the WP_Yes class.

		$settings->add_field(
			array(
				'id' => 'wp_yes_example_simple_field_1',
			)
		);

		$settings->add_field(
			array(
				'id' => 'wp_yes_example_simple_field_2',
			)
		);

		$settings->init(); // Run the WP_Yes class.
	}
}
add_action( 'init', 'wp_yes_example_simple' );
