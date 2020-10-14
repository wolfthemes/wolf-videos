<?php
/**
 * Videos Admin Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author WolfThemes
 * @category Admin
 * @package WolfVideos/Functions
 * @since 1.2.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate and save video URL and iframe code as meta when the post is saved
 */
function wvc_set_video_meta( $post_id ) {

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	/* Set video URL meta */
	if ( wv_get_first_video_url( $post_id ) ) {

		update_post_meta( $post_id, '_wv_video_url', wv_get_first_video_url( $post_id ) );

	} else {
		delete_post_meta( $post_id, '_wv_video_url' );
	}

	/* Set videoiframe meta */
	if ( wv_get_first_video_url( $post_id ) ) {

		$iframe = null;

		$video_url = wv_get_first_video_url( $post_id );

		$has_yt_url =
			preg_match( '#youtube(?:\-nocookie)?\.com/watch\?v=([A-Za-z0-9\-_]+)#', $video_url, $match )
			|| preg_match( '#youtube(?:\-nocookie)?\.com/v/([A-Za-z0-9\-_]+)#', $video_url, $match )
			|| preg_match( '#youtube(?:\-nocookie)?\.com/embed/([A-Za-z0-9\-_]+)#', $video_url, $match )
			|| preg_match( '#youtu.be/([A-Za-z0-9\-_]+)#', $video_url, $match );

		/* If YT */
		if ( $has_yt_url ) {

			$yt_id = $match[1];

			$iframe = '<iframe class="wv-yt" width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr( $yt_id ) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

			//update_post_meta( $post_id, 'wv_video_id', $yt_id );
			//update_post_meta( $post_id, '_wv_video_iframe', $iframe );

			$iframe_bg = '<iframe class="wv-yt-bg" src="https://www.youtube.com/embed/' . esc_attr( $yt_id ) . '?controls=0&showinfo=0&rel=0&autoplay=1&loop=1&playlist=W0LHTWG-UmQ" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';

			//update_post_meta( $post_id, '_wv_video_iframe_bg', $iframe_bg );

		/* If vimeo */
		} elseif ( preg_match( '#vimeo\.com/([0-9]+)#', $video_url, $match ) ) {

			$vimeo_id = $match[1];

			$iframe = '<iframe class="wv-vimeo" src="https://player.vimeo.com/video/' . esc_attr( $vimeo_id ) . '?color=ffffff&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

			//update_post_meta( $post_id, '_wv_video_iframe', $iframe );

			$iframe_bg = '<iframe class="wv-vimeo-bg" src="https://player.vimeo.com/video/' . esc_attr( $vimeo_id ) . '?autoplay=1&loop=1&byline=0&title=0&background=1"></iframe>';

			//update_post_meta( $post_id, '_wv_video_iframe_bg', $iframe_bg );


		// preg_match( '/(http:|https:)?\/\/[a-zA-Z0-9\/.?&=_-]+.mp4/', $content, $match

		} elseif ( preg_match( '/(http:|https:)?\/\/[a-zA-Z0-9\/.?&=_-]+.mp4/', $video_url, $match ) ) {

			$iframe = '<video src="' . esc_url( $video_url ) . '" autoplay></video>';

			//update_post_meta( $post_id, '_wv_video_iframe', $iframe );

		} else {
			delete_post_meta( $post_id, 'wv_video_video_id' );
			delete_post_meta( $post_id, '_wv_video_iframe' );
		}

	} else {
		delete_post_meta( $post_id, '_wv_video_iframe' );
	}

	/* Remove old unused meta */
	if ( get_post_meta( $post_id, 'wv_video_video_id' ) ) {
		delete_post_meta( $post_id, 'wv_video_video_id' );
	}
	if ( get_post_meta( $post_id, '_wv_video_iframe' ) ) {
		delete_post_meta( $post_id, '_wv_video_iframe' );
	}
	if ( get_post_meta( $post_id, '_wv_video_iframe_bg' ) ) {
		delete_post_meta( $post_id, '_wv_video_iframe_bg' );
	}

}
add_action( 'save_post', 'wvc_set_video_meta' );

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

/**
 * Display archive page state
 *
 * @param array $states
 * @param object $post
 * @return array $states
 */
function wv_custom_post_states( $states, $post ) {

	if ( 'page' == get_post_type( $post->ID ) && absint( $post->ID ) === wolf_videos_get_page_id() ) {

		$states[] = esc_html__( 'Videos Page' );
	}

	return $states;
}
add_filter( 'display_post_states', 'wv_custom_post_states', 10, 2 );
