<?php

/**
 * Plugin Name:       WP Enhancements
 * Plugin URI:        https://wordpress.org/plugins/wp-enhancements/
 * Description:       Easily enable various admin- and public- facing enhancements to a WordPress installation.
 * Version:           1.0.0
 * Author:            Bowo
 * Author URI:        https://bowo.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-enhancements
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPENHA_VERSION', '1.8.6' );
define( 'WPENHA_SLUG', 'wp-enhancements' );
define( 'WPENHA_URL', plugins_url( '/', __FILE__ ) ); // e.g. https://www.example.com/wp-content/plugins/this-plugin/
define( 'WPENHA_PATH', plugin_dir_path( __FILE__ ) ); // e.g. /home/user/apps/wp-root/wp-content/plugins/this-plugin/
// define( 'WPE_BASE', plugin_basename( __FILE__ ) ); // e.g. plugin-slug/this-file.php
// define( 'WPE_FILE', __FILE__ ); // /home/user/apps/wp-root/wp-content/plugins/this-plugin/this-file.php

// Register autoloading classes
spl_autoload_register( 'wpenha_autoloader' );

/**
 * Autoload classes defined by this plugin
 *
 * @param string $class_name e.g. \WPENHA\Classes\The_Name
 * @since 1.0.0
 */
function wpenha_autoloader( $class_name ) {

	$namespace = 'WPENHA';

	// Only process classes within this plugin's namespace

	if ( false !== strpos( $class_name, $namespace ) ) {

		// Assemble file path where class is defined

		// \WPENHA\Classes\The_Name => \Classes\The_Name
		$path = str_replace( $namespace, "", $class_name );

		// \Classes\The_Name => /classes/the_name
		$path = str_replace( "\\", DIRECTORY_SEPARATOR, strtolower( $path ) );

		// /classes/the_name =>  /classes/the-name.php
		$path = str_replace( "_", "-", $path ) . '.php';

		// /classes/the-name.php => /classes/class-the-name.php
		$path = str_replace( "classes" . DIRECTORY_SEPARATOR, "classes" . DIRECTORY_SEPARATOR . "class-", $path );

		// Remove first '/'
		$path = substr( $path, 1 );

		// Get /plugin-path/classes/class-the-name.php
		$path = WPENHA_PATH . $path;

		if ( file_exists( $path ) ) {
			require_once( $path );
		}																		

	}

}

/**
 * Code that runs on plugin activation
 * 
 * @since 1.0.0
 */
function wpenha_on_activation() {
	$activation = new WPENHA\Classes\Activation;
    $activation->activate();
}

/**
 * Code that runs on plugin deactivation
 * 
 * @since 1.0.0
 */
function wpenha_on_deactivation() {
    $deactivation = new WPENHA\Classes\Deactivation;
    $deactivation->deactivate();
}

// Register code that runs on plugin activation
register_activation_hook( __FILE__, 'wpenha_on_activation');

// Register code that runs on plugin deactivation
register_deactivation_hook( __FILE__, 'wpenha_on_deactivation' );

// Bootstrap the core functionalities of this plugin
require WPENHA_PATH . 'bootstrap.php';