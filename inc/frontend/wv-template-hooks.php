<?php
/**
 * WolfVideos Hooks
 *
 * Action/filter hooks used for WolfVideos functions/templates
 *
 * @author WolfThemes
 * @category Core
 * @package WolfVideos/Templates
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Body class
 *
 * @see  wfolio_body_class()
 */
add_filter( 'body_class', 'wv_body_class' );

/**
 * WP Header
 *
 * @see  wolf_videos_generator_tag()
 */
add_action( 'get_the_generator_html', 'wolf_videos_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'wolf_videos_generator_tag', 10, 2 );

/** Template Hooks ********************************************************/

if ( ! is_admin() || defined('DOING_AJAX') ) {

	/**
	 * Content Wrappers
	 *
	 * @see wolf_videos_output_content_wrapper()
	 * @see wolf_videos_output_content_wrapper_end()
	 */
	add_action( 'wolf_videos_before_main_content', 'wolf_videos_output_content_wrapper', 10 );
	add_action( 'wolf_videos_after_main_content', 'wolf_videos_output_content_wrapper_end', 10 );
}

/** Event Hooks *****************************************************/

add_action( 'template_redirect', 'wolf_videos_template_redirect', 40 );