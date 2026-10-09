<?php
/**
 * Single blog post.
 *
 * @package Elevora
 */
get_header();

if ( 'post' === get_post_type() && function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}

if ( elevora_render_elementor_document() ) {
	get_footer();
	return;
}

while ( have_posts() ) : the_post();
?>
<?php if ( elevora_get_setting( 'enable_reading_progress', '', 0 ) ) : ?><div class="gravitas-reading-progress is-<?php echo esc_attr( elevora_get_setting( 'reading_progress_position', '', 'top' ) ); ?>" data-reading-progress><span></span></div><?php endif; ?>

<?php
$sidebar_pos = elevora_get_setting( 'single_sidebar', 'elevora_single_sidebar', 'right' );
if ( ! in_array( $sidebar_pos, array( 'right', 'left', 'none' ), true ) ) {
	$sidebar_pos = 'right';
}
?>
<main id="primary" class="site-main single-post-main soledad-single-style gravitas-single-sidebar-<?php echo esc_attr( $sidebar_pos ); ?>">
	<article <?php post_class(); ?>>
		<div class="container article-layout<?php echo 'none' === $sidebar_pos ? ' article-layout--no-sidebar' : ''; ?><?php echo 'left' === $sidebar_pos ? ' article-layout--sidebar-left' : ''; ?>">
			<div class="single-post-content">

				<!-- 1. Soledad Overlay Hero Header -->
				<header class="soledad-entry-header">
					<div class="soledad-header-media">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'full', array( 'class' => 'soledad-hero-img' ) ); ?>
						<?php else : ?>
							<div class="soledad-hero-placeholder"></div>
						<?php endif; ?>
						<div class="soledad-header-overlay">
							<div class="soledad-header-inner">
								<?php 
								$categories = get_the_category();
								if ( $categories ) : ?>
									<div class="soledad-cat">
										<a href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>">
											<?php echo esc_html( $categories[0]->name ); ?>
										</a>
									</div>
								<?php endif; ?>

								<h1 class="soledad-title"><?php the_title(); ?></h1>

								<div class="soledad-meta">
									<span class="soledad-meta-author">
										<svg class="meta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
										<?php esc_html_e( 'written by', 'gravitas-coaching' ); ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a>
									</span>
									<span class="soledad-meta-date">
										<svg class="meta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
										<?php echo esc_html( get_the_date() ); ?>
									</span>
									<span class="soledad-meta-comments">
										<svg class="meta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
										<a href="#comments"><?php echo absint( get_comments_number() ); ?> <?php esc_html_e( 'comments', 'gravitas-coaching' ); ?></a>
									</span>
									<span class="soledad-meta-views">
										<svg class="meta-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
										<?php printf( esc_html__( '%d min read', 'gravitas-coaching' ), elevora_reading_time() ); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
				</header>

				<!-- 2. Entry Content with Drop Cap & Editorial Typography -->
				<div class="entry-content">
					<?php the_content(); wp_link_pages(); ?>
				</div>

				<!-- 3. Tags in Outlined Pill Style -->
				<?php if ( has_tag() ) : ?>
					<div class="soledad-post-tags">
						<?php the_tags( '', '', '' ); ?>
					</div>
				<?php endif; ?>

				<!-- 4. Soledad Dual-Tone Social Share Bar -->
				<div class="soledad-share-bar">
					<div class="soledad-share-tag">
						<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
						<span><?php esc_html_e( 'SHARE', 'gravitas-coaching' ); ?></span>
					</div>
					<button class="soledad-btn-like" type="button" aria-label="<?php esc_attr_e( 'Like this post', 'gravitas-coaching' ); ?>">
						<span class="like-number">3</span>
						<svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
					</button>
					<a class="soledad-share-btn soledad-share-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="btn-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></span>
						<span class="btn-text">Facebook</span>
					</a>
					<a class="soledad-share-btn soledad-share-tw" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="btn-icon"><svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></span>
						<span class="btn-text">Twitter</span>
					</a>
					<a class="soledad-share-btn soledad-share-pin" href="https://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_permalink() ); ?>&media=<?php echo rawurlencode( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>&description=<?php echo rawurlencode( get_the_title() ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="btn-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0a12 12 0 0 0-4.37 23.18c-.06-.99-.12-2.52.02-3.6.14-.99.98-4.14.98-4.14s-.25-.5-.25-1.25c0-1.17.68-2.05 1.53-2.05.72 0 1.07.54 1.07 1.19 0 .73-.46 1.82-.7 2.83-.2.85.42 1.54 1.26 1.54 1.51 0 2.68-1.59 2.68-3.89 0-2.03-1.46-3.45-3.55-3.45-2.42 0-3.84 1.81-3.84 3.68 0 .73.28 1.51.63 1.94.07.08.08.16.06.24-.07.28-.22.89-.25 1.01-.04.16-.13.2-.3.12-1.11-.52-1.81-2.14-1.81-3.44 0-2.8 2.04-5.38 5.88-5.38 3.09 0 5.49 2.2 5.49 5.14 0 3.07-1.93 5.54-4.62 5.54-.9 0-1.75-.47-2.04-1.03l-.56 2.12c-.2.78-.75 1.76-1.12 2.36A12 12 0 1 0 12 0z"/></svg></span>
						<span class="btn-text">Pinterest</span>
					</a>
					<a class="soledad-share-btn soledad-share-mail" href="mailto:?subject=<?php echo rawurlencode( get_the_title() ); ?>&body=<?php echo rawurlencode( get_permalink() ); ?>">
						<span class="btn-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
						<span class="btn-text">Email</span>
					</a>
				</div>

				<!-- 5. Post Navigation (Previous / Next Post) -->
				<?php
				$prev_post = get_previous_post();
				$next_post = get_next_post();
				if ( $prev_post || $next_post ) :
				?>
				<nav class="soledad-post-nav">
					<?php if ( $prev_post ) : ?>
						<div class="soledad-nav-card soledad-nav-prev">
							<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
								<span class="nav-sub"><?php esc_html_e( 'previous post', 'gravitas-coaching' ); ?></span>
								<h4 class="nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></h4>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( $next_post ) : ?>
						<div class="soledad-nav-card soledad-nav-next">
							<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
								<span class="nav-sub"><?php esc_html_e( 'next post', 'gravitas-coaching' ); ?></span>
								<h4 class="nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></h4>
							</a>
						</div>
					<?php endif; ?>
				</nav>
				<?php endif; ?>

				<!-- 6. Author Box -->
				<div class="soledad-author-box">
					<div class="soledad-author-avatar">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 85 ); ?>
					</div>
					<div class="soledad-author-info">
						<h4 class="soledad-author-name"><?php the_author(); ?></h4>
						<p class="soledad-author-bio">
							<?php echo esc_html( get_the_author_meta( 'description' ) ?: __( 'I’m a passionate creator, sharing perspectives on personal style, creative expression, and authentic presence.', 'gravitas-coaching' ) ); ?>
						</p>
						<div class="soledad-author-social">
							<?php if ( get_the_author_meta( 'user_url' ) ) : ?>
								<a href="<?php echo esc_url( get_the_author_meta( 'user_url' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Website">
									<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
								</a>
							<?php endif; ?>
							<a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
								<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
							</a>
							<a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
							</a>
							<a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
								<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
							</a>
						</div>
					</div>
				</div>

				<!-- 7. Related Posts in 2-Column Soledad Grid -->
				<div class="soledad-related-section">
					<div class="soledad-box-heading"><?php esc_html_e( 'RELATED POSTS', 'gravitas-coaching' ); ?></div>
					<div class="soledad-related-grid">
						<?php
						$post_categories = wp_get_post_categories( get_the_ID() );
						$related_query   = new WP_Query(
							array(
								'post_type'           => 'post',
								'posts_per_page'      => 4,
								'post__not_in'        => array( get_the_ID() ),
								'category__in'        => $post_categories,
								'ignore_sticky_posts' => true,
							)
						);
						if ( ! $related_query->have_posts() ) {
							$related_query = new WP_Query(
								array(
									'post_type'           => 'post',
									'posts_per_page'      => 4,
									'post__not_in'        => array( get_the_ID() ),
									'ignore_sticky_posts' => true,
								)
							);
						}
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							?>
							<article class="soledad-related-card">
								<a class="soledad-related-thumb" href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'elevora-card' ); ?>
									<?php else : ?>
										<div class="soledad-thumb-placeholder"></div>
									<?php endif; ?>
								</a>
								<div class="soledad-related-content">
									<h4 class="soledad-related-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h4>
									<div class="soledad-related-meta">
										<span class="related-date"><?php echo esc_html( get_the_date() ); ?></span>
										<span class="related-views"><?php printf( esc_html__( '%d min read', 'gravitas-coaching' ), elevora_reading_time() ); ?></span>
									</div>
								</div>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</div>

				<!-- 8. Comments with Styled Centered Heading -->
				<?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>

			</div>

			<?php if ( 'none' !== $sidebar_pos ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</article>
</main>
<?php endwhile; get_footer(); ?>
