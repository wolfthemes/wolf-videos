<?php
/**
 * The Template for displaying the main videos page
 *
 * Override this template by copying it to yourtheme/wolf-videos/videos-template.php
 *
 * @author WpWolf
 * @package WolfVideos/Templates
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'videos' ); 

if ( get_query_var( 'paged' ) ) {

	$paged = get_query_var( 'paged' );

} elseif ( get_query_var( 'page' ) ) {

	$paged = get_query_var( 'page' );

} else {

	$paged = 1;

}

$args = array(
	'post_type' => 'video',
	'meta_key'    => '_thumbnail_id',
	'posts_per_page' => -1,
	//'paged' => $paged
);

/* video Post Loop */
$loop = new WP_Query( $args );
?>
	<div class="videos-container">
		<?php if ( $loop->have_posts() ) : ?>
			
			<?php
				/**
				 * Video Category Filter
				 */
				wolf_videos_get_template( 'filter.php' );
			?>
			
			<?php wolf_videos_loop_start(); ?>
				
				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
				
					<?php wolf_videos_get_template_part( 'content', 'video' ); ?>
				
				<?php endwhile; ?>
			
			<?php wolf_videos_loop_end(); ?>
			
			<?php else : ?>

				<?php wolf_videos_get_template( 'loop/no-video-found.php' ); ?>
			
			<?php endif; // end have_posts() check ?>
	</div><!-- .video-container -->
<?php get_footer( 'videos' ); ?>