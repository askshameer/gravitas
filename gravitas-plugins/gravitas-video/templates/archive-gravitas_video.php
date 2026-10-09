<?php
/**
 * Video archive template.
 *
 * @package Gravitas_Video
 */
get_header();
if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'archive' ) ) {
	get_footer();
	return;
}
?>
<main id="primary" class="site-main gravitas-video-archive">
	<section class="gravitas-video-archive__hero"><div class="gravitas-video-wrap"><span class="eyebrow"><?php esc_html_e( 'Gravitas Learning Studio', 'gravitas-video' ); ?></span><h1><?php post_type_archive_title(); ?></h1><p><?php esc_html_e( 'Practical lessons for communication, confidence, executive presence, professional image, and business etiquette.', 'gravitas-video' ); ?></p></div></section>
	<section class="gravitas-video-wrap">
		<form class="gravitas-video-filter" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'gravitas_video' ) ); ?>">
			<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search videos', 'gravitas-video' ); ?>">
			<input type="hidden" name="post_type" value="gravitas_video">
			<?php wp_dropdown_categories( array( 'taxonomy' => 'video_topic', 'name' => 'video_topic', 'hide_empty' => true, 'show_option_all' => __( 'All topics', 'gravitas-video' ), 'value_field' => 'slug', 'selected' => isset( $_GET['video_topic'] ) ? sanitize_title( wp_unslash( $_GET['video_topic'] ) ) : '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<?php wp_dropdown_categories( array( 'taxonomy' => 'video_level', 'name' => 'video_level', 'hide_empty' => true, 'show_option_all' => __( 'All levels', 'gravitas-video' ), 'value_field' => 'slug', 'selected' => isset( $_GET['video_level'] ) ? sanitize_title( wp_unslash( $_GET['video_level'] ) ) : '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<button class="gravitas-button" type="submit"><?php esc_html_e( 'Filter', 'gravitas-video' ); ?></button>
			<?php if ( get_search_query() || get_query_var( 'video_topic' ) || get_query_var( 'video_level' ) ) : ?><a class="gravitas-video-filter__reset" href="<?php echo esc_url( get_post_type_archive_link( 'gravitas_video' ) ); ?>"><?php esc_html_e( 'Clear filters', 'gravitas-video' ); ?></a><?php endif; ?>
		</form>
		<div class="gravitas-video-grid"><?php if ( have_posts() ) : while ( have_posts() ) : the_post(); echo gravitas_video()['player']->card( get_the_ID() ); endwhile; else : ?><p><?php esc_html_e( 'No videos found.', 'gravitas-video' ); ?></p><?php endif; ?></div>
		<?php the_posts_pagination(); ?>
	</section>
</main>
<?php get_footer(); ?>
