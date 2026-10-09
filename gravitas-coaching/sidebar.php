<?php
/**
 * Default sidebar.
 *
 * @package Elevora
 */
?>
<aside class="site-sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'gravitas-coaching' ); ?>">
<?php if ( is_singular( 'post' ) ) : ?>
	<?php
	$social_profiles = array(
		'facebook' => array( elevora_get_setting( 'facebook_url', '', '' ), 'Facebook', 'f' ),
		'linkedin' => array( elevora_get_setting( 'linkedin_url', '', '' ), 'LinkedIn', 'in' ),
		'youtube'  => array( elevora_get_setting( 'youtube_url', '', '' ), 'YouTube', '▶' ),
	);
	?>
	<section class="widget soledad-sidebar-widget single-social-widget">
		<h3 class="widget-title soledad-box-heading"><span><?php esc_html_e( 'FOLLOW GRAVITAS', 'gravitas-coaching' ); ?></span></h3>
		<div class="single-social-grid">
			<?php foreach ( $social_profiles as $network => $profile ) : ?>
				<?php if ( $profile[0] ) : ?>
					<a class="single-social-card is-<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $profile[0] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( __( 'Follow Gravitas on %s', 'gravitas-coaching' ), $profile[1] ) ); ?>">
				<?php else : ?>
					<span class="single-social-card is-<?php echo esc_attr( $network ); ?> is-disabled" aria-label="<?php echo esc_attr( sprintf( __( '%s profile is not configured', 'gravitas-coaching' ), $profile[1] ) ); ?>">
				<?php endif; ?>
					<strong aria-hidden="true"><?php echo esc_html( $profile[2] ); ?></strong>
					<small><?php echo esc_html( $profile[1] ); ?></small>
				<?php echo $profile[0] ? '</a>' : '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endforeach; ?>
		</div>
	</section>

	<?php
	$recent_posts = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 5,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
		)
	);
	if ( $recent_posts->have_posts() ) :
		?>
		<section class="widget soledad-sidebar-widget single-recent-widget">
			<h3 class="widget-title soledad-box-heading"><span><?php esc_html_e( 'RECENT POSTS', 'gravitas-coaching' ); ?></span></h3>
			<ul class="soledad-recent-list">
				<?php while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>">
					<span class="single-recent-widget__image"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'thumbnail' ); } else { echo '<span class="soledad-thumb-placeholder"></span>'; } ?></span>
					<span class="single-recent-widget__content"><strong class="soledad-recent-title"><?php the_title(); ?></strong><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
				</a></li>
				<?php endwhile; ?>
			</ul>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
		<?php $GLOBALS['elevora_rendering_main_sidebar'] = true; ?>
		<?php add_filter( 'sidebars_widgets', 'elevora_sidebar_widgets_without_recent_posts', 20 ); ?>
		<?php dynamic_sidebar( 'sidebar-main' ); ?>
		<?php remove_filter( 'sidebars_widgets', 'elevora_sidebar_widgets_without_recent_posts', 20 ); ?>
		<?php $GLOBALS['elevora_rendering_main_sidebar'] = false; ?>
	<?php else : ?>
		<section class="widget soledad-sidebar-widget single-categories-widget">
			<h3 class="widget-title soledad-box-heading"><span><?php esc_html_e( 'CATEGORIES', 'gravitas-coaching' ); ?></span></h3>
			<ul class="soledad-cat-list">
			<?php foreach ( get_categories( array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => true ) ) as $cat ) : ?>
				<li><a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"><span class="cat-left"><span class="cat-arrow">&#8250;</span> <?php echo esc_html( $cat->name ); ?></span><span class="cat-count">(<?php echo esc_html( $cat->count ); ?>)</span></a></li>
			<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>

	<?php $sidebar_ad_id = absint( elevora_get_setting( 'sidebar_ad_image_id', '', 0 ) ); ?>
	<?php if ( $sidebar_ad_id ) : ?>
		<?php $sidebar_ad_url = elevora_get_setting( 'sidebar_ad_url', '', '' ); ?>
		<section class="widget soledad-sidebar-widget single-sidebar-ad" aria-label="<?php esc_attr_e( 'Advertisement', 'gravitas-coaching' ); ?>">
			<?php if ( $sidebar_ad_url ) : ?><a href="<?php echo esc_url( $sidebar_ad_url ); ?>"<?php echo elevora_get_setting( 'sidebar_ad_new_tab', '', 1 ) ? ' target="_blank" rel="noopener noreferrer"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php endif; ?>
			<?php echo wp_get_attachment_image( $sidebar_ad_id, 'large', false, array( 'class' => 'single-sidebar-ad__image', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $sidebar_ad_url ) : ?></a><?php endif; ?>
		</section>
	<?php endif; ?>

	<?php if ( ! is_active_sidebar( 'sidebar-main' ) ) : ?>
	<section class="widget soledad-sidebar-widget single-newsletter-widget">
		<h3 class="widget-title soledad-box-heading"><span><?php esc_html_e( 'NEWSLETTER', 'gravitas-coaching' ); ?></span></h3>
		<p class="soledad-newsletter-desc"><?php esc_html_e( 'Subscribe for new articles, practical coaching ideas, and professional presence tips.', 'gravitas-coaching' ); ?></p>
		<form class="soledad-newsletter-form" onsubmit="event.preventDefault(); var s = this.querySelector('.newsletter-success'); if (s) s.style.display='block'; this.reset();">
			<input type="text" placeholder="<?php esc_attr_e( 'Name...', 'gravitas-coaching' ); ?>" required><input type="email" placeholder="<?php esc_attr_e( 'Email...', 'gravitas-coaching' ); ?>" required>
			<button type="submit" class="soledad-newsletter-submit"><?php esc_html_e( 'SUBSCRIBE', 'gravitas-coaching' ); ?></button><div class="newsletter-success" style="display:none"><?php esc_html_e( 'Thank you for subscribing!', 'gravitas-coaching' ); ?></div>
		</form>
	</section>
	<?php endif; ?>
<?php elseif ( is_active_sidebar( 'sidebar-main' ) ) : ?>
	<?php dynamic_sidebar( 'sidebar-main' ); ?>
<?php else : ?>
	<section class="widget"><h3 class="widget-title"><?php esc_html_e( 'Search', 'gravitas-coaching' ); ?></h3><?php get_search_form(); ?></section>
	<section class="widget widget--accent"><span class="eyebrow"><?php esc_html_e( 'Ready for support?', 'gravitas-coaching' ); ?></span><h3><?php esc_html_e( 'Meet a coach who gets it.', 'gravitas-coaching' ); ?></h3><a class="button button--dark button--block" href="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>"><?php esc_html_e( 'Find a coach', 'gravitas-coaching' ); ?></a></section>
<?php endif; ?>
</aside>
