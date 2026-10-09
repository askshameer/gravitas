<?php
/**
 * Template Name: Gravitas Full Width
 * Template Post Type: page
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

while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="site-main gravitas-full-width">
		<article <?php post_class(); ?>>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
