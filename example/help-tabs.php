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

if ( ! function_exists( 'wp_yes_help_tabs' ) ) {
	/**
	 * Example for settings with help tabs
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wp_yes_help_tabs() {
		$settings = new WP_Yes( 'wp_yes_help_tabs' ); // Initialize the WP_Yes class.

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
				'id'       => 'wp_yes_help_tabs_field_1',
				'required' => true,
				'type'     => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_help_tabs_field_2',
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
				'title'   => __( 'My Help Tab', 'wp_yes_txt' ),
				'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.', 'wp_yes_txt' ) . '</p>',
			)
		);

		$settings->add_help_tab(
			array(
				'id'      => 'my_help_tab2',
				'title'   => __( 'My Help Tab2', 'wp_yes_txt' ),
				'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here 2.', 'wp_yes_txt' ) . '</p>',
			)
		);

		$settings->init(); // Run the WP_Yes class.
	}
}
add_action( 'init', 'wp_yes_help_tabs' );
