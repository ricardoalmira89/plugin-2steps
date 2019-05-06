<?php
/**
 * Plugin Name: 2Step Reviews App
 * Description: List your Google and Facebook reviews in a page with a shortcode. This plugin also aggregates the total reviews and rating from your Google My Business and adds them as a schema for Google results.
 * Author: Ricardo Almira
 * Author URI: http://github.com/ricardoalmira89
 * Version: 1.0.1
 * License: GPL-v2.0 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * exit if file is accessed directly
 */
defined('ABSPATH') || exit;

/**
 * define plugin root directory PATH
 */
if ( ! defined( 'TSRAPP_DIR_PATH' ) ) {
	define(
		'TSRAPP_DIR_PATH',
		plugin_dir_path( __FILE__ )
	);
}

/**
 * define plugin root directory URI
 */
if ( ! defined( 'TSRAPP_DIR_URI' ) ) {
	define(
		'TSRAPP_DIR_URI',
		plugin_dir_url( __FILE__ )
	);
}

/**
 * define plugin assets directory URI
 */
define(
	'TSRAPP_ASSETS_URI',
	trailingslashit( TSRAPP_DIR_URI . 'assets' )
);

/**
 * define plugin name, slug, version
 * and minimum WordPress version required
 */
define(
	'TSRAPP_SLUG',
	'two-step-reviews-app'
);
define(
	'TSRAPP_NAME',
	__( 'Two Step Reviews App', TSRAPP_SLUG )
);
define(
	'TSRAPP_VERSION',
	'1.0.1'
);
define(
	'TSRAPP_MIN_WP_VERSION',
	'4.0'
);

/**
 * define plugin support URLs
 */
define(
	'TSRAPP_WPORG_SUPPORT_URL',
	trailingslashit( 'https://wordpress.org/support/plugin' )
);

/**
 * Register plugin activation & deactivation hooks
 */
register_activation_hook( __FILE__, array( 'Two_Step_Reviews_App', 'eb_plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Two_Step_Reviews_App', 'eb_plugin_deactivation' ) );

/**
 * include the plugin main class & inc dir main file
 */
require_once( TSRAPP_DIR_PATH . 'class-two-stepreviewsapp.php' );
//require_once( TSRAPP_DIR_PATH . 'inc/init.php' );

/**
 * initialize the plugin
 */
add_action( 'init', array( 'Two_Step_Reviews_App', 'init' ) );

/**
 * Establecer el timezone porque me joden los tokens
 */
//date_default_timezone_set(get_option('timezone_string'));

