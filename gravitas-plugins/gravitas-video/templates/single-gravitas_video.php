<?php
/** Single video template. @package Gravitas_Video */
get_header();
while ( have_posts() ) :
	the_post();
	$objects           = gravitas_video();
	$chapters          = $objects['player']->parse_rows( gravitas_video_get_meta( get_the_ID(), 'chapters' ) );
	$downloads         = $objects['player']->parse_rows( gravitas_video_get_meta( get_the_ID(), 'downloads' ) );
	$alternate_sources = $objects['player']->parse_rows( gravitas_video_get_meta( get_the_ID(), 'alternate_sources' ) );
	$channel_id        = absint( gravitas_video_get_meta( get_the_ID(), 'channel_id' ) );
	?>
	<main id="primary" class="site-main gravitas-video-single">
		<section class="gravitas-video-hero"><div class="gravitas-video-wrap"><?php echo gravitas_video_render_player(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></section>
		<section class="gravitas-video-wrap gravitas-video-details">
			<article>
				<div class="gravitas-video-heading"><?php the_terms( get_the_ID(), 'video_topic', '<div class="gravitas-video-topics">', ' &middot; ', '</div>' ); ?><h1><?php the_title(); ?></h1><div class="gravitas-video-byline"><span><?php echo esc_html( gravitas_video_format_count( gravitas_video_get_meta( get_the_ID(), 'views', 0 ) ) ); ?> <?php esc_html_e( 'views', 'gravitas-video' ); ?></span><span><?php echo esc_html( get_the_date() ); ?></span><?php if ( gravitas_video_get_meta( get_the_ID(), 'duration' ) ) : ?><span><?php echo esc_html( gravitas_video_get_meta( get_the_ID(), 'duration' ) ); ?></span><?php endif; ?></div><p><?php echo esc_html( get_the_excerpt() ); ?></p></div>
				<div class="gravitas-video-toolbar">
					<?php if ( gravitas_video_get_option( 'enable_reactions', 1 ) ) : ?><button data-video-action="react" data-reaction="like" data-post-id="<?php the_ID(); ?>" data-login-url="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">&#9650; <span data-like-count><?php echo absint( gravitas_video_get_meta( get_the_ID(), 'likes', 0 ) ); ?></span></button><button data-video-action="react" data-reaction="dislike" data-post-id="<?php the_ID(); ?>" data-login-url="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">&#9660; <span data-dislike-count><?php echo absint( gravitas_video_get_meta( get_the_ID(), 'dislikes', 0 ) ); ?></span></button><?php endif; ?>
					<button data-video-action="watch_later" data-post-id="<?php the_ID(); ?>" data-login-url="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">+ <?php esc_html_e( 'Watch later', 'gravitas-video' ); ?></button>
					<?php foreach ( $alternate_sources as $source_index => $alternate_source ) : ?><a class="gravitas-video-source" href="<?php echo esc_url( add_query_arg( 'video_source', $source_index + 1, get_permalink() ) ); ?>"><?php echo esc_html( $alternate_source['label'] ); ?></a><?php endforeach; ?>
					<?php if ( gravitas_video_get_option( 'enable_reports', 1 ) ) : ?><button data-video-action="report" data-post-id="<?php the_ID(); ?>" data-reason="<?php esc_attr_e( 'Reported from the video page', 'gravitas-video' ); ?>"><?php esc_html_e( 'Report', 'gravitas-video' ); ?></button><?php endif; ?>
				</div>
				<?php if ( $channel_id ) : ?><div class="gravitas-video-channel"><a href="<?php echo esc_url( get_permalink( $channel_id ) ); ?>"><strong><?php echo esc_html( get_the_title( $channel_id ) ); ?></strong></a><button data-video-action="subscribe" data-channel-id="<?php echo absint( $channel_id ); ?>" data-login-url="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Subscribe', 'gravitas-video' ); ?></button></div><?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div><?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
			</article>
			<aside><?php if ( $chapters ) : ?><div class="gravitas-video-chapters"><h3><?php esc_html_e( 'Chapters', 'gravitas-video' ); ?></h3><?php foreach ( $chapters as $chapter ) : ?><a href="#" data-chapter="<?php echo esc_attr( $chapter['label'] ); ?>"><strong><?php echo esc_html( $chapter['label'] ); ?></strong> <?php echo esc_html( $chapter['url'] ); ?></a><?php endforeach; ?></div><?php endif; ?><?php if ( $downloads && gravitas_video_get_meta( get_the_ID(), 'allow_downloads' ) ) : ?><div class="gravitas-video-resources"><h3><?php esc_html_e( 'Resources', 'gravitas-video' ); ?></h3><?php foreach ( $downloads as $download ) : ?><a href="<?php echo esc_url( $download['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $download['label'] ); ?></a><?php endforeach; ?></div><?php endif; ?></aside>
		</section>
		<section class="gravitas-video-wrap gravitas-video-related"><h2><?php esc_html_e( 'Continue learning', 'gravitas-video' ); ?></h2><?php echo do_shortcode( '[gravitas_video_grid count="3"]' ); ?></section>
	</main>
	<?php
endwhile;
get_footer();
