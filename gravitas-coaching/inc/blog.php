<?php
/**
 * Blog layout helpers.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the available blog layouts.
 *
 * @return array<string, string>
 */
function elevora_get_blog_layouts() {
	return array(
		'editorial' => __( 'Editorial Feature', 'gravitas-coaching' ),
		'magazine'  => __( 'Modern Magazine', 'gravitas-coaching' ),
		'masonry'   => __( 'Creative Masonry', 'gravitas-coaching' ),
		'grid'      => __( 'Classic Grid', 'gravitas-coaching' ),
		'list'      => __( 'Elegant List', 'gravitas-coaching' ),
		'minimal'   => __( 'Minimal Journal', 'gravitas-coaching' ),
	);
}

/**
 * Sanitize a blog layout value.
 *
 * @param string $layout Layout slug.
 * @return string
 */
function elevora_sanitize_blog_layout( $layout ) {
	$layouts = elevora_get_blog_layouts();

	return isset( $layouts[ $layout ] ) ? $layout : 'editorial';
}

/**
 * Get the selected blog layout.
 *
 * @return string
 */
function elevora_get_blog_layout() {
	return elevora_sanitize_blog_layout( elevora_get_setting( 'blog_layout', 'elevora_blog_layout', 'editorial' ) );
}

/** Detect articles that should carry video treatment in listing views. */
function elevora_post_is_video( $post_id = 0 ) {
	$post_id = $post_id ?: get_the_ID();
	$content = (string) get_post_field( 'post_content', $post_id );
	return 'video' === get_post_format( $post_id ) || has_block( 'core/video', $content ) || has_block( 'core/embed', $content ) || false !== stripos( $content, '<video' );
}

/** Render a compact category rail inspired by video-magazine archives. */
function elevora_blog_topic_bar() {
	$categories = get_categories( array( 'number' => 8, 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true ) );
	if ( ! $categories ) {
		return;
	}
	?>
	<nav class="blog-topic-bar" aria-label="<?php esc_attr_e( 'Journal topics', 'gravitas-coaching' ); ?>">
		<span><?php esc_html_e( 'Explore', 'gravitas-coaching' ); ?></span>
		<div><a class="<?php echo is_home() ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Latest', 'gravitas-coaching' ); ?></a><?php foreach ( $categories as $category ) : ?><a class="<?php echo is_category( $category->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endforeach; ?></div>
	</nav>
	<?php
}

/**
 * Determine whether the journal sidebar should be displayed.
 *
 * @return bool
 */
function elevora_has_blog_sidebar() {
	if ( function_exists( 'gravitas_core_get_option' ) ) {
		return 'none' !== gravitas_core_get_option( 'sidebar_position', 'none' );
	}

	return (bool) get_theme_mod( 'elevora_blog_sidebar', false );
}

/**
 * Get the blog heading.
 *
 * @return string
 */
function elevora_get_blog_title() {
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		return get_the_title( (int) get_option( 'page_for_posts' ) );
	}

	if ( is_archive() ) {
		return get_the_archive_title();
	}

	return __( 'Ideas for a more powerful presence.', 'gravitas-coaching' );
}

/**
 * Render the selected blog loop.
 */
function elevora_render_blog_loop() {
	get_template_part(
		'template-parts/blog-loop',
		null,
		array(
			'layout' => elevora_get_blog_layout(),
		)
	);
}

/**
 * Add useful body classes for blog styling and integrations.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function elevora_blog_body_classes( $classes ) {
	if ( is_home() || is_archive() || is_search() ) {
		$classes[] = 'elevora-blog-layout-' . elevora_get_blog_layout();
	}

	if ( did_action( 'elementor/loaded' ) ) {
		$classes[] = 'elevora-elementor-active';
	}

	return $classes;
}
add_filter( 'body_class', 'elevora_blog_body_classes' );
