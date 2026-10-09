<?php
/**
 * Standard page.
 *
 * @package Elevora
 */
get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}

if ( elevora_render_elementor_document() ) {
	get_footer();
	return;
}

while ( have_posts() ) : the_post();
	$page_eyebrow = '';
	if ( is_page( 'about' ) ) {
		$page_eyebrow = __( 'About Gravitas', 'gravitas-coaching' );
	} elseif ( is_page( 'contact' ) ) {
		$page_eyebrow = __( 'Get In Touch', 'gravitas-coaching' );
	} else {
		$page_eyebrow = __( 'Gravitas Coaching', 'gravitas-coaching' );
	}

	$page_subtitle = get_the_excerpt();
	if ( ! $page_subtitle ) {
		if ( is_page( 'about' ) ) {
			$page_subtitle = __( 'Professional presence becomes powerful when skills, confidence, and image communicate the same message.', 'gravitas-coaching' );
		} elseif ( is_page( 'contact' ) ) {
			$page_subtitle = __( 'Tell us where you want to feel more confident, credible, or polished.', 'gravitas-coaching' );
		}
	}
?>
<main id="primary" class="site-main page-main">
	<header class="page-hero">
		<div class="container">
			<?php if ( elevora_get_setting( 'show_page_breadcrumbs', '', 1 ) ) { elevora_breadcrumbs(); } ?>
			<span class="eyebrow"><?php echo esc_html( $page_eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( ! empty( $page_subtitle ) ) : ?>
				<p><?php echo esc_html( $page_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php if ( elevora_get_setting( 'show_page_featured', '', 0 ) && has_post_thumbnail() ) : ?><div class="container article-featured"><?php the_post_thumbnail( 'elevora-wide' ); ?></div><?php endif; ?>
	<div class="container container--article page-content"><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div><?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?></div>
</main>
<?php endwhile; get_footer(); ?>
