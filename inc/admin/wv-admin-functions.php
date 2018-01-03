<?php
/**
 * %NAME% Admin Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author %AUTHOR%
 * @category Admin
 * @package %PACKAGENAME%/Functions
 * @since 1.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add video URL meta when the post is saved
 */
function wvc_set_video_url_meta( $post_id ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( wv_get_first_video_url( $post_id ) ) {
		update_post_meta( $post_id, 'wv_video_url', wv_get_first_video_url( $post_id ) );
	} else {
		delete_post_meta( $post_id, 'wv_video_url' );
	}
}
add_action( 'save_post', 'wvc_set_video_url_meta' );

/**
 * Return the first video URL in the post if a video URL is found
 *
 * @return string
 */
function wv_get_first_video_url( $post_id = null ) {

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content = get_post_field( 'post_content', $post_id );

	$has_video_url =
	// youtube
	preg_match( '#http?://(?:\www.)?\youtube.com/watch\?v=([A-Za-z0-9\-_]+)#', $content, $match )
	|| preg_match( '#https?://(?:\www.)?\youtube.com/watch\?v=([A-Za-z0-9\-_]+)#', $content, $match )
	|| preg_match( '#http?://(?:\www.)?\youtu.be/([A-Za-z0-9\-_]+)#', $content, $match )
	|| preg_match( '#https?://(?:\www.)?\youtu.be/([A-Za-z0-9\-_]+)#', $content, $match )

	// vimeo
	|| preg_match( '#vimeo\.com/([0-9]+)#', $content, $match )

	// other
	|| preg_match( '#http://blip.tv/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?dailymotion\.com/.*#', $content, $match )
	|| preg_match( '#http://dai.ly/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?hulu\.com/watch/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?viddler\.com/.*#', $content, $match )
	|| preg_match( '#http://qik.com/.*#', $content, $match )
	|| preg_match( '#http://revision3.com/.*#', $content, $match )
	|| preg_match( '#http://wordpress.tv/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?funnyordie\.com/videos/.*#', $content, $match )
	|| preg_match( '#https?://(www\.)?flickr\.com/.*#', $content, $match )
	|| preg_match( '#http://flic.kr/.*#', $content, $match )

	// Video Format
	|| preg_match( '/(http:|https:)?\/\/[a-zA-Z0-9\/.?&=_-]+.mp4/', $content, $match );

	$video_url = ( $has_video_url ) ? esc_url( $match[0] ) : null;

	return $video_url;
}