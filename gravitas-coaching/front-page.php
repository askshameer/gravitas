<?php
/**
 * Homepage template.
 *
 * @package Elevora
 */

if ( is_singular( 'post' ) ) {
	include ELEVORA_DIR . '/single.php';
	return;
}

/*
 * WordPress always gives front-page.php priority, including on a brand-new
 * activation. Only use the bundled showcase renderer for a homepage created
 * by the explicit demo importer. Otherwise respect the site's real page or
 * posts index so installing/updating the theme cannot replace its content.
 */
$elevora_demo_home = is_page()
	&& get_option( 'elevora_demo_imported_at' )
	&& get_post_meta( get_queried_object_id(), '_elevora_demo_content', true );

if ( ! $elevora_demo_home ) {
	if ( is_page() ) {
		include ELEVORA_DIR . '/page.php';
	} elseif ( file_exists( ELEVORA_DIR . '/home.php' ) ) {
		include ELEVORA_DIR . '/home.php';
	} else {
		include ELEVORA_DIR . '/index.php';
	}
	return;
}

get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}

if ( elevora_render_elementor_document() ) {
	get_footer();
	return;
}

elevora_render_homepage();
get_footer();
