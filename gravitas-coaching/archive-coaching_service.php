<?php
/**
 * Services archive.
 *
 * @package Elevora
 */
get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main services-archive-main">
	<section class="archive-hero archive-hero--services"><div class="container"><?php elevora_breadcrumbs(); ?><div class="archive-hero__content"><span class="eyebrow eyebrow--light"><?php esc_html_e( 'Soft skills & image consulting', 'gravitas-coaching' ); ?></span><h1><?php esc_html_e( 'Build a presence that earns attention and trust.', 'gravitas-coaching' ); ?></h1><p><?php esc_html_e( 'Choose a focused program for communication, executive presence, personal image, etiquette, or career readiness.', 'gravitas-coaching' ); ?></p></div></div></section>
	<section class="section">
		<div class="container service-category-nav"><?php foreach ( get_terms( array( 'taxonomy' => 'service_category', 'hide_empty' => true ) ) as $term ) : ?><a class="tag tag--large" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?><span><?php echo esc_html( $term->count ); ?></span></a><?php endforeach; ?></div>
		<div class="container card-grid card-grid--three"><?php if ( have_posts() ) : while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card', 'service' ); endwhile; else : get_template_part( 'template-parts/content', 'none' ); endif; ?></div>
		<div class="container"><?php elevora_pagination(); ?></div>
	</section>
</main>
<?php get_footer(); ?>
