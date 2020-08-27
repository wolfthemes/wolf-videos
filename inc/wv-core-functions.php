<?php
/**
 * Videos Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author WolfThemes
 * @category Core
 * @package WolfVideos/Functions
 * @since 1.0.3
 */

defined( 'ABSPATH' ) || exit;

// Hack for old php versions to use boolval()
if ( ! function_exists( 'boolval' ) ) {
	function boolval( $val ) {
		return (bool) $val;
	}
}

/**
 * Add image sizes
 *
 * These size will be ued for galleries and sliders
 *
 * @since 1.1.4
 */
function wv_add_image_sizes() {

	add_image_size( 'video-cover', 640, 360, true );
}
add_action( 'init', 'wv_add_image_sizes' );

/**
 * wolf_videos page ID
 *
 * retrieve page id - used for the main videos page
 *
 * @return int
 */
function wolf_videos_get_page_id() {

	$page_id = -1;

	if ( -1 != get_option( '_wolf_videos_page_id' ) && get_option( '_wolf_videos_page_id' ) ) {

		$page_id = get_option( '_wolf_videos_page_id' );

	}

	if ( -1 != $page_id ) {
		$page_id = apply_filters( 'wpml_object_id', absint( $page_id ), 'page', true ); // filter for WPML
	}

	return $page_id;
}

/**
 * Returns the URL of the videos page
 */
function wolf_get_videos_url() {

	$page_id = wolf_videos_get_page_id();

	if ( -1 != $page_id ) {
		return get_permalink( $page_id );
	}
}

/**
 * Widget function
 *
 * Displays the show list in the widget
 *
 * @param int $count, string $url, bool $link
 * @return string
 */
function wolf_videos_get_option( $value, $default = null ) {

	global $options;

	$wolf_videos_settings = get_option( 'wolf_videos_settings' );

	if ( isset( $wolf_videos_settings[ $value ] ) && '' != $wolf_videos_settings[ $value ] ) {

		return $wolf_videos_settings[ $value ];

	} elseif( $default ) {

		return $default;
	}
}

/**
 * Get template part (for templates like the videos-loop).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function wolf_videos_get_template_part( $slug, $name = '' ) {
	$wolf_videos = WV();
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/wolf-videos/slug-name.php
	if ( $name )
		$template = locate_template( array( "{$slug}-{$name}.php", "{$wolf_videos->template_url}{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( $wolf_videos->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
		$template = $wolf_videos->plugin_path() . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wolf-videos/slug.php
	if ( ! $template )
		$template = locate_template( array( "{$slug}.php", "{$wolf_videos->template_url}{$slug}.php" ) );

	if ( $template )
		load_template( $template, false );
}

/**
 * Get other templates (e.g. ticket attributes) passing attributes and including the file.
 *
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function wolf_videos_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array($args) )
		extract( $args );

	$located = wolf_videos_locate_template( $template_name, $template_path, $default_path );

	do_action( 'wolf_videos_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'wolf_videos_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function wolf_videos_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	if ( ! $template_path ) $template_path = WV()->template_url;
	if ( ! $default_path ) $default_path = WV()->plugin_path() . '/templates/';

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters( 'wolf_videos_locate_template', $template, $template_name, $template_path );
}