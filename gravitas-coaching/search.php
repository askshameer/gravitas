<?php
/**
 * Search results.
 *
 * @package Elevora
 */
get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main search-main">
	<section class="page-hero"><div class="container"><span class="eyebrow"><?php esc_html_e( 'Search results', 'gravitas-coaching' ); ?></span><h1><?php echo esc_html( sprintf( /* translators: %s: search query. */ __( 'Results for “%s”', 'gravitas-coaching' ), get_search_query( false ) ) ); ?></h1><?php get_search_form(); ?></div></section>
	<section class="section"><div class="container search-results-list"><?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?><article <?php post_class( 'search-result' ); ?>><div class="search-result__type"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></div><div><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html( get_the_excerpt() ); ?></p></div><a class="circle-link" href="<?php the_permalink(); ?>"><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a></article><?php endwhile; elevora_pagination(); else : get_template_part( 'template-parts/content', 'none' ); endif; ?></div></section>
</main>
<?php get_footer(); ?>
