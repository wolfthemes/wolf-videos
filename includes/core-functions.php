<?php
/**
 * WolfVideos Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author WpWolf
 * @category Core
 * @package WolfVideos/Functions
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'wolf_videos_get_page_id' ) ) {

	/**
	 * wolf_videos page ID
	 *
	 * retrieve page id - used for the main videos page
	 *
	 *
	 * @access public
	 * @return int
	 */
	function wolf_videos_get_page_id() {
		
		$page_id = -1;
		$theme_dir = get_template_directory();

		if ( -1 != get_option( '_wolf_videos_page_id' ) && get_option( '_wolf_videos_page_id' ) ) {
			
			$page_id = get_option( '_wolf_videos_page_id' );
		
		// back compatibility with the old template system
		} elseif ( is_file( $theme_dir . '/videos-template.php' ) ) {
			
			$templates = array( 
				'videos-template.php',
				'page-templates/videos.php',
			);

			foreach ( $templates as $template ) {

				$pages = get_pages( array(
					'meta_key' => '_wp_page_template',
					'meta_value' => $template
				) );


				if ( $pages && isset( $pages[0] ) ) {
					$page_id = $pages[0]->ID;
					break;
				}	
			}

		}

		return $page_id;
	}
}

if ( ! function_exists( 'wolf_get_videos_url' ) ) {
	/**
	 * Returns the URL of the videos page
	 */
	function wolf_get_videos_url() {
		
		$page_id = wolf_videos_get_page_id();

		if ( -1 != $page_id )
			return get_permalink( $page_id );

	}
}

if ( ! function_exists( 'wolf_videos_get_option' ) ) {
	/**
	 * Get videos option
	 *
	 * @access public
	 * @param string
	 * @return void
	 */
	function wolf_videos_get_option( $value, $default = null ) {

		global $wolf_videos;
		return $wolf_videos->get_option( $value, $default );

	}
}

if ( ! function_exists( 'wolf_video_nav' ) ) {
	/**
	 * Displays navigation to next/previous post when applicable.
	 *
	 *
	 * @access public
	 * @return string/bool
	 */
	function wolf_video_nav() {
		
		global $wolf_videos;
		return $wolf_videos->navigation();

	}
}

/**
 * Get template part (for templates like the release-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function wolf_videos_get_template_part( $slug, $name = '' ) {
	global $wolf_videos;
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
 * @access public
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function wolf_videos_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	global $wolf_videos;

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
 * @access public
 * @param mixed $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function wolf_videos_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	global $wolf_videos;

	if ( ! $template_path ) $template_path = $wolf_videos->template_url;
	if ( ! $default_path ) $default_path = $wolf_videos->plugin_path() . '/templates/';

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
