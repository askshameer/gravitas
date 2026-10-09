<?php
/**
 * Main blog index.
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
	<section class="page-hero blog-hero"><div class="container"><?php if ( elevora_get_setting( 'show_page_breadcrumbs', '', 1 ) ) { elevora_breadcrumbs(); } ?><span class="eyebrow"><?php echo esc_html( get_theme_mod( 'elevora_blog_eyebrow', __( 'Gravitas journal', 'gravitas-coaching' ) ) ); ?></span><h1><?php echo esc_html( elevora_get_blog_title() ); ?></h1><p><?php echo esc_html( get_theme_mod( 'elevora_blog_description', __( 'Practical guidance for stronger soft skills, authentic personal style, and confident professional presence.', 'gravitas-coaching' ) ) ); ?></p></div></section>
	<section class="section blog-section"><div class="container"><?php elevora_blog_topic_bar(); ?><div class="blog-shell<?php echo esc_attr( elevora_has_blog_sidebar() ? '' : ' blog-shell--no-sidebar' ); ?>"><?php elevora_render_blog_loop(); ?><?php if ( elevora_has_blog_sidebar() ) : get_sidebar(); endif; ?></div></div></section>
</main>
<?php get_footer(); ?>
