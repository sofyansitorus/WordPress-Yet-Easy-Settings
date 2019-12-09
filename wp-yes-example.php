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
 * @package           WP_Yes
 *
 * Plugin Name:       WP Yes Example
 * Plugin URI:        https://github.com/sofyansitorus/WordPress-Yet-Easy-Settings
 * Description:       This is sample for how to use WordPress-Yet-Easy-Settings in a plugin.
 * Version:           1.0.0
 * Author:            Sofyan Sitorus
 * Author URI:        https://github.com/sofyansitorus
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp_yes_txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Autoload Dependencies.
require 'vendor/autoload.php';

require_once 'example/all.php';
require_once 'example/button.php';
require_once 'example/tabs.php';
require_once 'example/help-tabs.php';
require_once 'example/custom-tab-content.php';
require_once 'example/custom-page-content.php';
require_once 'example/sub-menu.php';
require_once 'example/sub-menu-under-tools.php';
