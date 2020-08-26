<?php
/**
 * The Template for displaying the single video page
 *
 * Override this template by copying it to yourtheme/wolf-videos/single-video.php
 *
 * @author WolfThemes
 * @package WolfVideos/Templates
 */
get_header( 'videos' ); 
?>
	<div id="primary" class="content-area">
		<main id="content" class="site-content clearfix" role="main">
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> data-post-id="<?php the_ID(); ?>">
					<?php
						the_content();
					?>
				</article>
			<?php endwhile; ?>
		</main><!-- main#content .site-content-->
	</div><!-- #primary .content-area -->
<?php
get_footer( 'videos' ); 
?>