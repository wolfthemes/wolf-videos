<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author WolfThemes
 * @package WolfVideos/Templates
 * @since 1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$term_list = '';
$post_id   = get_the_ID();
if ( get_the_terms( $post_id, 'video_type' ) ) {
	foreach ( get_the_terms( $post_id, 'video_type' ) as $term ) {
		$term_list .= $term->slug .' ';
	}
}
$term_list = ( $term_list ) ? substr( $term_list, 0, -1 ) : '';
?>
<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'video-item-container', $term_list ) ); ?>>
	<span class="video-item">
		<a class="entry-link" href="<?php the_permalink(); ?>" title="<?php printf( __( 'Watch %s', 'wolf-videos' ), get_the_title() ); ?>">
			<span class="video-thumb">
				<span class="play-overlay"></span>
				<?php the_post_thumbnail( 'video-cover' ); ?>
				<span class="video-title">
					<h5 class="video-heading"><?php the_title(); ?></h5>
				</span>
			</span>
		</a>
	</span>
</article><!-- article.video-item -->
<?php endif; ?>