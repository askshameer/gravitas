<?php
/**
 * Video collections archive.
 *
 * @package Gravitas_Video
 */
get_header();
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main gravitas-collection-archive"><section class="gravitas-video-archive__hero"><div class="gravitas-video-wrap"><span class="eyebrow"><?php esc_html_e( 'Browse', 'gravitas-video' ); ?></span><h1><?php post_type_archive_title(); ?></h1></div></section><section class="gravitas-video-wrap"><div class="gravitas-video-grid"><?php while ( have_posts() ) : the_post(); ?><article class="gravitas-video-card"><a class="gravitas-video-card__media" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large' ); } else { echo '<span class="gravitas-video-card__placeholder"></span>'; } ?></a><div class="gravitas-video-card__body"><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html( get_the_excerpt() ); ?></p></div></article><?php endwhile; ?></div><?php the_posts_pagination(); ?></section></main>
<?php get_footer(); ?>
