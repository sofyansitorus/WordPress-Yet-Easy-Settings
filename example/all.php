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

if ( ! function_exists( 'wp_yes_all' ) ) {
	/**
	 * Example for settings with all available fields type.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	function wp_yes_all() {
		$settings = new WP_Yes(
			'wp_yes_all',
			array(
				'menu_title' => __( 'WP_Yes All Fields', 'wp_yes_txt' ),
				'page_title' => __( 'All Settings Fields', 'wp_yes_txt' ),
			)
		); // Initialize the WP_Yes class.

		$settings->add_tab(
			array(
				'id' => 'tab_1',
			)
		);

		$settings->add_section(
			array(
				'id'    => 'section_basic',
				'title' => __( 'Basic Field Type', 'wp_yes_txt' ),
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_text',
				'label' => __( 'Text', 'wp_yes_txt' ),
				'type'  => 'text',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_url',
				'label' => __( 'URL', 'wp_yes_txt' ),
				'type'  => 'url',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_number',
				'label' => __( 'Number', 'wp_yes_txt' ),
				'type'  => 'number',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_decimal',
				'label' => __( 'Decimal', 'wp_yes_txt' ),
				'type'  => 'decimal',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_password',
				'label' => __( 'Password', 'wp_yes_txt' ),
				'type'  => 'password',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_email',
				'label' => __( 'Email', 'wp_yes_txt' ),
				'type'  => 'email',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_textarea',
				'label' => __( 'Textarea', 'wp_yes_txt' ),
				'type'  => 'textarea',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_checkbox',
				'label' => __( 'Checkbox', 'wp_yes_txt' ),
				'type'  => 'checkbox',
			)
		);

		$settings->add_field(
			array(
				'id'      => 'wp_yes_all_field_multicheckbox',
				'label'   => __( 'Multi Checkbox', 'wp_yes_txt' ),
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
				'id'      => 'wp_yes_all_field_select',
				'label'   => __( 'Select', 'wp_yes_txt' ),
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
				'id'      => 'wp_yes_all_field_multiselect',
				'label'   => __( 'Multi Select', 'wp_yes_txt' ),
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
				'id'      => 'wp_yes_all_field_radio',
				'label'   => __( 'Radio', 'wp_yes_txt' ),
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
				'id'    => 'section_advanced',
				'title' => __( 'Advanced Field Type', 'wp_yes_txt' ),
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_color',
				'label' => __( 'Color Picker', 'wp_yes_txt' ),
				'type'  => 'color',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_file',
				'label' => __( 'File Upload', 'wp_yes_txt' ),
				'type'  => 'file',
			)
		);

		$settings->add_field(
			array(
				'id'    => 'wp_yes_all_field_wysiwyg',
				'label' => __( 'WYSIWYG', 'wp_yes_txt' ),
				'type'  => 'wysiwyg',
			)
		);

		$settings->init(); // Run the WP_Yes class.
	}
}
add_action( 'init', 'wp_yes_all' );
