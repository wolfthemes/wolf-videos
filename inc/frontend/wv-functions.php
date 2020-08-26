<?php
/**
 * WolfVideos Functions
 *
 * Hooked-in functions for WolfVideos related events on the front-end.
 *
 * @author WolfThemes
 * @category Core
 * @package WolfVideos/Functions
 * @since 1.0.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue scripts
 */
function wv_enqueue_scripts() {

	// Styles
	wp_enqueue_style( 'wolf-videos', WV_CSS . '/videos.min.css', array(), WV_VERSION, 'all' );

	// Scripts
	wp_register_script( 'imagesloaded', WV_JS . '/lib/imagesloaded.pkgd.min.js', 'jquery', '3.1.8', true );
	wp_register_script( 'isotope', WV_JS . '/lib/isotope.pkgd.min.js', 'jquery', '2.0.1', true );
	wp_register_script( 'wolf-videos', WV_JS . '/videos.min.js', 'jquery', WV_VERSION, true );

	if ( wolf_videos_get_option( 'isotope' ) && is_page( wolf_videos_get_page_id() ) ) {

		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'isotope' );
		wp_enqueue_script( 'wolf-videos' );
	}
}
add_action( 'wp_enqueue_scripts', 'wv_enqueue_scripts' );

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page videos.
 *
 * @return void
 */
function wolf_videos_template_redirect() {

	if ( is_page( wolf_videos_get_page_id() ) && ! post_password_required() ) {
		wolf_videos_get_template( 'videos-template.php' );
		exit();
	}
}