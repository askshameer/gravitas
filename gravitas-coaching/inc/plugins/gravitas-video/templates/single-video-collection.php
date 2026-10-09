<?php
/**
 * Video collection template.
 *
 * @package Gravitas_Video
 */
get_header();
while ( have_posts() ) : the_post();
	$type = get_post_type();
	$shortcode = 'gravitas_playlist' === $type ? 'gravitas_playlist' : ( 'gravitas_channel' === $type ? 'gravitas_channel' : ( 'gravitas_series' === $type ? 'gravitas_series' : 'gravitas_video_grid' ) );
	?>
	<main id="primary" class="site-main gravitas-collection"><section class="gravitas-video-archive__hero"><div class="gravitas-video-wrap"><span class="eyebrow"><?php echo esc_html( get_post_type_object( $type )->labels->singular_name ); ?></span><h1><?php the_title(); ?></h1><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php if ( 'gravitas_channel' === $type ) : ?><button class="gravitas-button" data-video-action="subscribe" data-channel-id="<?php the_ID(); ?>" data-login-url="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Subscribe to channel', 'gravitas-video' ); ?></button><?php endif; ?></div></section><section class="gravitas-video-wrap"><div class="entry-content"><?php the_content(); ?></div><h2><?php esc_html_e( 'Videos', 'gravitas-video' ); ?></h2><?php echo do_shortcode( '[' . $shortcode . ' id="' . get_the_ID() . '" count="24"]' ); ?></section></main>
	<?php
endwhile;
get_footer();
