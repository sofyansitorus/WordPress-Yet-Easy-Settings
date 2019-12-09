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

if ( ! function_exists( 'wp_yes_sub_menu' ) ) {
	/**
	 * Example for sub-menu
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wp_yes_sub_menu() {
		// Initialize blank parent menu.
		$settings = new WP_Yes(
			'wp_yes_sub_menu_1',
			array(
				'menu_title' => 'WP_Yes',
				'callback'   => null,
			)
		); // Initialize the WP_Yes class.

		$settings->init(); // Run the WP_Yes class.

		$settings = new WP_Yes(
			'wp_yes_sub_menu_1',
			array(
				'menu_title'  => 'Sub-menu 1',
				'page_title'  => 'Sub-menu 1 Page Title',
				'method'      => 'add_submenu_page',
				'parent_slug' => 'wp_yes_sub_menu_1',
			)
		); // Initialize the WP_Yes class.

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
				'id'       => 'wp_yes_sub_menu_1_field_1',
				'required' => true,
				'type'     => 'text',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_sub_menu_1_field_2',
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
				'id'       => 'wp_yes_sub_menu_1_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_sub_menu_1_field_4',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->init(); // Run the WP_Yes class.

		$settings = new WP_Yes(
			'wp_yes_sub_menu_2',
			array(
				'menu_title'  => 'Sub-menu 2',
				'page_title'  => 'Sub-menu 2 Page Title',
				'method'      => 'add_submenu_page',
				'parent_slug' => 'wp_yes_sub_menu_1',
			)
		); // Initialize the WP_Yes class.

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
				'id'       => 'wp_yes_sub_menu_2_field_1',
				'required' => true,
				'type'     => 'text',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_sub_menu_2_field_2',
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
				'id'       => 'wp_yes_sub_menu_2_field_3',
				'required' => true,
				'type'     => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'       => 'wp_yes_sub_menu_2_field_4',
				'required' => true,
				'type'     => 'multiselect',
				'options'  => array(
					'foo'     => 'foo',
					'bar'     => 'bar',
					'foo bar' => 'foo bar',
				),
			)
		);

		$settings->init(); // Run the WP_Yes class.
	}
}
add_action( 'init', 'wp_yes_sub_menu' );
