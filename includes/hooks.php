<?php
/**
 * WolfVideos Hooks
 *
 * Action/filter hooks used for WolfVideos functions/templates
 *
 * @author WpWolf
 * @category Core
 * @package WolfVideos/Templates
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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

add_action( 'template_redirect', 'wolf_videos_template_redirect' );