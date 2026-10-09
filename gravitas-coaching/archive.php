<?php
/**
 * Generic archives.
 *
 * @package Elevora
 */
get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main listing-main">
	<section class="page-hero"><div class="container"><?php elevora_breadcrumbs(); ?><span class="eyebrow"><?php esc_html_e( 'Explore the archive', 'gravitas-coaching' ); ?></span><h1><?php the_archive_title(); ?></h1><?php the_archive_description( '<div class="archive-description">', '</div>' ); ?></div></section>
	<section class="section blog-section"><div class="container"><?php if ( is_category() || is_tag() || is_author() || is_date() ) { elevora_blog_topic_bar(); } ?><div class="blog-shell<?php echo esc_attr( elevora_has_blog_sidebar() ? '' : ' blog-shell--no-sidebar' ); ?>"><?php elevora_render_blog_loop(); ?><?php if ( elevora_has_blog_sidebar() ) : get_sidebar(); endif; ?></div></div></section>
</main>
<?php get_footer(); ?>
