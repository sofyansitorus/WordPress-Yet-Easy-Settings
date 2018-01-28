<?php
/**
 * Plugin Name:       Wpyes Example
 * Plugin URI:        http://www.sofyansitorus.com/wpyes/
 * Description:       This is sample for how to use WordPress-Yet-Easy-Settings in a plugin.
 * Version:           1.0.0
 * Author:            Sofyan Sitorus
 * Author URI:        http://www.sofyansitorus.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpyes
 * Domain Path:       /languages
 */

class Wpyes_Example {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_slug;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_slug       The name of this plugin.
	 */
	public function __construct( $plugin_slug ) {

		$this->plugin_slug = $plugin_slug;
		$this->load_dependencies();
		$this->build_settings();

	}

	/**
	 * Include the dependencies files
	 */
	private function load_dependencies() {
		  /**
		 * Include the wpyes setting class file
		 */
		 require_once plugin_dir_path( __FILE__ ) . 'wpyes.php';
	}

	/**
	 * Get all the settings tabs
	 *
	 * @return array settings sections
	 */
	private function get_settings_tabs() {
		$tabs = array(
			array(
				'id' => 'email_settings',
			),
		);
		return $tabs;
	}

	/**
	 * Get all the settings sections
	 *
	 * @return array settings sections
	 */
	private function get_settings_sections() {
		$sections = array(
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_sender',
				'title' => __( 'Email Sender Options', $this->plugin_slug ),
			),
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_smtp',
				'title' => __( 'SMTP Server Options', $this->plugin_slug ),
			),
			array(
				'tab'   => 'email_settings',
				'id'    => 'email_settings_imap',
				'title' => __( 'IMAP Server Options', $this->plugin_slug ),
			),
		);
		return $sections;
	}

	/**
	 * Get all the settings fields
	 *
	 * @return array settings fields
	 */
	private function get_settings_fields() {
		$fields = array(
			array(
				'section'  => 'email_settings_sender',
				'id'       => 'email_from_name',
				'label'    => __( '"From" Name', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_sender',
				'id'       => 'email_from_address',
				'label'    => __( '"From" Email Address', $this->plugin_slug ),
				'type'     => 'email',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_server',
				'label'    => __( 'SMTP Server', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_username',
				'label'    => __( 'SMTP Username', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_password',
				'label'    => __( 'SMTP Password', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 30,
			),
			array(
				'section'  => 'email_settings_smtp',
				'id'       => 'smtp_port',
				'label'    => __( 'SMTP Port', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 40,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_server',
				'label'    => __( 'IMAP Server', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 10,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_username',
				'label'    => __( 'IMAP Username', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 20,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_password',
				'label'    => __( 'IMAP Password', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 30,
			),
			array(
				'section'  => 'email_settings_imap',
				'id'       => 'imap_port',
				'label'    => __( 'IMAP Port', $this->plugin_slug ),
				'type'     => 'text',
				'priority' => 40,
			),
		);
		return $fields;
	}

	private function build_settings() {

		$settings = new Wpyes( $this->plugin_name ); // Initialize the Wpyes class

		$settings->add_tabs( $this->get_settings_tabs() ); // Set the settings tabs

		$settings->add_sections( $this->get_settings_sections() ); // Set the settings sections

		$settings->add_fields( $this->get_settings_fields() ); // Set the settings fields

		// Set the admin page for the settings
		$settings->set_admin_page(
			array(
				'page_title' => 'Wpyes Settings Example',
				'menu_title' => 'Wpyes Settings',
			)
		);

		$settings->init(); // Run the Wpyes class
	}

}

new Wpyes_Example( 'wpyes_example' );
