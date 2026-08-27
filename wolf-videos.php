<?php
/**
 * Plugin Name: Videos
 * Plugin URI: https://wlfthm.es/wolf-videos
 * Description: A video gallery post type for your site.
 * Version: 1.4.0
 * Author: WolfThemes
 * Author URI: https://wolfthemes.com
 * Requires at least: 6.0
 * Tested up to: 7.1
 *
 * Text Domain: wolf-videos
 * Domain Path: /languages/
 *
 * @package WolfVideos
 * @category Core
 * @author WolfThemes
 *
 * Verified customers who have purchased a premium theme at https://wlfthm.es/tf/
 * will have access to support for this plugin in the forums
 * https://wlfthm.es/help/
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WV_PLUGIN_FILE' ) ) {
	define( 'WV_PLUGIN_FILE', __FILE__ );
}

/**
 * Autoload WolfVideos\* classes from Functions/ and lazily alias
 * legacy (pre-namespace) class names for backward compatibility.
 */
spl_autoload_register( function ( $class ) {

	$legacy_aliases = array(
		'Wolf_Videos'         => 'WolfVideos\Plugin',
		'WV_Admin'            => 'WolfVideos\Admin\Admin',
		'WV_Options'          => 'WolfVideos\Admin\Options',
		'WV_Admin_Metabox'    => 'WolfVideos\Admin\Metabox',
		'WV_Update'           => 'WolfVideos\Admin\Update',
		'WV_Shortcode'        => 'WolfVideos\Frontend\Shortcodes',
	);

	if ( isset( $legacy_aliases[ $class ] ) ) {
		class_alias( $legacy_aliases[ $class ], $class );
		return;
	}

	if ( 0 !== strpos( $class, 'WolfVideos\\' ) ) {
		return;
	}

	$path = __DIR__ . '/Functions/' . str_replace( '\\', '/', substr( $class, strlen( 'WolfVideos\\' ) ) ) . '.php';

	if ( file_exists( $path ) ) {
		require $path;
	}
} );

if ( ! function_exists( 'WV' ) ) {
	/**
	 * Returns the main instance of the plugin to prevent the need to use globals.
	 *
	 * @return \WolfVideos\Plugin
	 */
	function WV() {
		return \WolfVideos\Plugin::instance();
	}
}

WV(); // Go
