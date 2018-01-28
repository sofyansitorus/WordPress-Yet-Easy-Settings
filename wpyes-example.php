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

if ( ! function_exists( 'wpyes_example_init' ) ) {
	/**
	 * Initialize the plugin.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function wpyes_example_init() {

		$setting_tabs = array(
			array(
				'id'       => 'miscellaneous_settings',
				'priority' => 20,
				'label'    => __( 'Miscellaneous', 'wpyes_example' ),
			),
			array(
				'id' => 'email_settings',
			),
		);

		$sections_of_email_tab = array(
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_sender',
				'title' => __( 'Email Sender Options', 'wpyes_example' ),
			),
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_smtp',
				'title' => __( 'SMTP Server Options', 'wpyes_example' ),
			),
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_imap',
				'title' => __( 'IMAP Server Options', 'wpyes_example' ),
			),
		);

		$sections_of_miscellaneous_tab = array(
			array(
				'tab'   => 'miscellaneous_settings',
				'id'    => 'email_settings_senderx',
				'title' => __( 'Email Sender Options', 'wpyes_example' ),
			),
			array(
				'tab'   => 'miscellaneous_settings',
				'id'    => 'email_settings_smtpx',
				'title' => __( 'SMTP Server Options', 'wpyes_example' ),
			),
			array(
				'tab'   => 'miscellaneous_settings',
				'id'    => 'email_settings_imapx',
				'title' => __( 'IMAP Server Options', 'wpyes_example' ),
			),
		);

		$fields_of_email_tab = array(
			array(
				'section'  => 'email_settings_sender',
				'id'       => 'email_from_name',
				'label'    => __( '"From" Name', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_sender',
				'id'       => 'email_from_address',
				'label'    => __( '"From" Email Address', 'wpyes_example' ),
				'type'     => 'email',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_server',
				'label'    => __( 'SMTP Server', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_username',
				'label'    => __( 'SMTP Username', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_password',
				'label'    => __( 'SMTP Password', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 30,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_port',
				'label'    => __( 'SMTP Port', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 40,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_server',
				'label'    => __( 'IMAP Server', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_username',
				'label'    => __( 'IMAP Username', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_password',
				'label'    => __( 'IMAP Password', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 30,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_port',
				'label'    => __( 'IMAP Port', 'wpyes_example' ),
				'type'     => 'text',
				'priority' => 40,
			),
		);

		$settings = new Wpyes( 'wpyes_example' ); // Initialize the Wpyes class.

		$settings->add_tabs( $setting_tabs ); // Set the settings tabs.

		$settings->add_sections( $sections_of_email_tab ); // Set the settings sections.

		$settings->add_sections( $sections_of_miscellaneous_tab ); // Set the settings sections.

		$settings->add_fields( $fields_of_email_tab ); // Set the settings fields.

		// Set the admin page for the settings.
		$settings->set_admin_page(
			array(
				'page_title' => __( 'Wpyes Settings Example', 'wpyes_example' ),
				'menu_title' => __( 'Wpyes Settings', 'wpyes_example' ),
			)
		);

		$settings->init(); // Run the Wpyes class.

	}

	wpyes_example_init();

}// End if().
