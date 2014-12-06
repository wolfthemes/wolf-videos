<?php
/**
 * WolfVideos Functions
 *
 * Hooked-in functions for WolfVideos related events on the front-end.
 *
 * @author WpWolf
 * @category Core
 * @package WolfVideos/Functions
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle redirects before content is output - hooked into template_redirect so is_page videos.
 *
 * @access public
 * @return void
 */
function wolf_videos_template_redirect() {

	if ( is_page( wolf_videos_get_page_id() ) ) {

		wolf_videos_get_template( 'videos-template.php' );
		exit();

	}
}