<?php
/**
 * Not found page.
 *
 * @package Elevora
 */
get_header();
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main not-found-main"><div class="container not-found"><span class="not-found__number">404</span><span class="eyebrow"><?php esc_html_e( 'A small detour', 'gravitas-coaching' ); ?></span><h1><?php esc_html_e( 'This path does not lead anywhere yet.', 'gravitas-coaching' ); ?></h1><p><?php esc_html_e( 'Try a search, browse our coaches, or return to the beginning.', 'gravitas-coaching' ); ?></p><?php get_search_form(); ?><div class="not-found__actions"><a class="button button--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'gravitas-coaching' ); ?></a><a class="text-link" href="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>"><?php esc_html_e( 'Browse trainers', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a></div></div></main>
<?php get_footer(); ?>
