<?php
/**
 * Single trainer profile.
 *
 * @package Elevora
 */
get_header();

while ( have_posts() ) : the_post();
	$role         = elevora_get_field_value( 'elevora_role' );
	$tagline      = elevora_get_field_value( 'elevora_tagline' );
	$rating       = (float) elevora_get_field_value( 'elevora_rating', get_the_ID(), 5 );
	$experience   = elevora_get_field_value( 'elevora_experience' );
	$sessions     = elevora_get_field_value( 'elevora_sessions' );
	$rate         = elevora_get_field_value( 'elevora_hourly_rate' );
	$availability = elevora_get_field_value( 'elevora_availability' );
	$booking_eyebrow = elevora_get_field_value( 'elevora_booking_eyebrow', get_the_ID(), __( 'Start a conversation', 'gravitas-coaching' ) );
	$rate_label      = elevora_get_field_value( 'elevora_rate_label', get_the_ID(), __( 'Sessions from', 'gravitas-coaching' ) );
	$booking_benefits = elevora_get_field_value( 'elevora_booking_benefits', get_the_ID(), "Introductory fit call\nClear goals and next steps\nFlexible online sessions" );
	$availability_button_text = elevora_get_field_value( 'elevora_availability_button_text', get_the_ID(), __( 'Check availability', 'gravitas-coaching' ) );
	$booking_url      = elevora_get_field_value( 'elevora_booking_url', get_the_ID(), '#contact' );
	$availability_url = elevora_get_field_value( 'elevora_availability_url', get_the_ID(), $booking_url );
	$video_url    = elevora_get_field_value( 'elevora_video_url' );
	$credentials  = elevora_get_field_value( 'elevora_credentials' );
	$services     = (array) elevora_get_field_value( 'elevora_trainer_services', get_the_ID(), array() );
	$portfolio    = (array) elevora_get_field_value( 'elevora_portfolio', get_the_ID(), array() );
	$location     = get_the_terms( get_the_ID(), 'trainer_location' );
	?>
	<main id="primary" class="site-main trainer-profile">
		<section class="profile-hero">
			<div class="container"><?php elevora_breadcrumbs(); ?></div>
			<div class="container profile-hero__grid">
				<div class="profile-hero__media"><?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'elevora-trainer' ); else : ?><div class="media-placeholder"><span><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></span></div><?php endif; ?><div class="profile-hero__badge"><?php echo elevora_icon( 'check' ); // phpcs:ignore ?><span><?php esc_html_e( 'Verified coach', 'gravitas-coaching' ); ?></span></div></div>
				<div class="profile-hero__content">
					<div class="tag-list"><?php elevora_term_links( get_the_ID(), 'trainer_specialization', 3 ); ?></div>
					<h1><?php the_title(); ?></h1>
					<?php if ( $role ) : ?><p class="profile-role"><?php echo esc_html( $role ); ?></p><?php endif; ?>
					<?php if ( $tagline ) : ?><p class="profile-tagline"><?php echo esc_html( $tagline ); ?></p><?php endif; ?>
					<div class="profile-facts">
						<span><?php echo elevora_icon( 'star' ); // phpcs:ignore ?><strong><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></strong> <?php esc_html_e( 'rating', 'gravitas-coaching' ); ?></span>
						<?php if ( $experience ) : ?><span><strong><?php echo esc_html( number_format_i18n( $experience ) ); ?>+</strong> <?php esc_html_e( 'years', 'gravitas-coaching' ); ?></span><?php endif; ?>
						<?php if ( $sessions ) : ?><span><strong><?php echo esc_html( number_format_i18n( $sessions ) ); ?>+</strong> <?php esc_html_e( 'sessions', 'gravitas-coaching' ); ?></span><?php endif; ?>
						<?php if ( $location && ! is_wp_error( $location ) ) : ?><span><?php echo elevora_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $location[0]->name ); ?></span><?php endif; ?>
					</div>
					<div class="profile-actions"><?php echo elevora_render_action_button( $booking_url, __( 'Book a discovery call', 'gravitas-coaching' ), 'button button--accent', 'arrow', 'discovery-trainer-' . get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php if ( $video_url ) : ?><a class="button button--ghost" href="#introduction-video"><?php echo elevora_icon( 'play' ); // phpcs:ignore ?><?php esc_html_e( 'Watch introduction', 'gravitas-coaching' ); ?></a><?php endif; ?></div>
				</div>
			</div>
		</section>

		<div class="container profile-layout">
			<article class="profile-content">
				<section class="profile-section"><span class="eyebrow"><?php esc_html_e( 'About', 'gravitas-coaching' ); ?></span><h2><?php echo esc_html( sprintf( __( 'Work with %s', 'gravitas-coaching' ), get_the_title() ) ); ?></h2><div class="entry-content"><?php the_content(); ?></div></section>
				<?php if ( $video_url ) : ?><section class="profile-section" id="introduction-video"><span class="eyebrow"><?php esc_html_e( 'Introduction', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Hear their approach', 'gravitas-coaching' ); ?></h2><div class="video-embed"><?php echo wp_kses_post( wp_oembed_get( esc_url( $video_url ) ) ?: '<a class="button button--dark" href="' . esc_url( $video_url ) . '" target="_blank" rel="noopener">' . esc_html__( 'Watch video', 'gravitas-coaching' ) . '</a>' ); ?></div></section><?php endif; ?>
				<?php if ( $services ) : ?><section class="profile-section"><span class="eyebrow"><?php esc_html_e( 'Ways to work together', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Services offered', 'gravitas-coaching' ); ?></h2><div class="card-grid card-grid--two"><?php foreach ( $services as $service ) : $service_id = is_object( $service ) ? $service->ID : absint( $service ); if ( 'coaching_service' !== get_post_type( $service_id ) ) { continue; } $GLOBALS['post'] = get_post( $service_id ); setup_postdata( $GLOBALS['post'] ); get_template_part( 'template-parts/card', 'service' ); endforeach; wp_reset_postdata(); ?></div></section><?php endif; ?>
				<?php if ( $credentials ) : ?><section class="profile-section credentials-section"><span class="eyebrow"><?php esc_html_e( 'Experience', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Credentials and training', 'gravitas-coaching' ); ?></h2><ul class="check-list"><?php foreach ( preg_split( '/\r\n|\r|\n/', wp_strip_all_tags( $credentials ) ) as $credential ) : if ( trim( $credential ) ) : ?><li><?php echo elevora_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( trim( $credential ) ); ?></li><?php endif; endforeach; ?></ul></section><?php endif; ?>
				<?php if ( $portfolio ) : ?><section class="profile-section"><span class="eyebrow"><?php esc_html_e( 'Selected work', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Portfolio and outcomes', 'gravitas-coaching' ); ?></h2><div class="portfolio-grid"><?php foreach ( $portfolio as $image ) : $image_id = is_array( $image ) && isset( $image['ID'] ) ? absint( $image['ID'] ) : absint( $image ); if ( $image_id ) : ?><a href="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ); ?>" class="lightbox-trigger" data-lightbox="portfolio" data-title="<?php echo esc_attr( get_the_title( $image_id ) ); ?>" aria-label="<?php esc_attr_e( 'View full image in lightbox', 'gravitas-coaching' ); ?>"><?php echo wp_get_attachment_image( $image_id, 'elevora-card', false, array( 'loading' => 'lazy' ) ); ?></a><?php endif; endforeach; ?></div></section><?php endif; ?>
				<section class="profile-section"><span class="eyebrow"><?php esc_html_e( 'Focus areas', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Specializations and working style', 'gravitas-coaching' ); ?></h2><div class="taxonomy-groups"><div><h3><?php esc_html_e( 'Specializations', 'gravitas-coaching' ); ?></h3><div class="tag-list"><?php elevora_term_links( get_the_ID(), 'trainer_specialization', 20 ); ?></div></div><div><h3><?php esc_html_e( 'Coaching formats', 'gravitas-coaching' ); ?></h3><div class="tag-list"><?php elevora_term_links( get_the_ID(), 'coaching_format', 20 ); ?></div></div><div><h3><?php esc_html_e( 'Languages', 'gravitas-coaching' ); ?></h3><div class="tag-list"><?php elevora_term_links( get_the_ID(), 'trainer_language', 20 ); ?></div></div></div></section>
				<?php if ( comments_open() || get_comments_number() ) : ?><section class="profile-section reviews-section"><?php comments_template(); ?></section><?php endif; ?>
			</article>
			<aside class="booking-card">
				<span class="eyebrow"><?php echo esc_html( $booking_eyebrow ); ?></span>
				<?php if ( $rate ) : ?><p class="booking-card__price"><span><?php echo esc_html( $rate_label ); ?></span><strong><?php echo esc_html( is_numeric( $rate ) ? '$' . number_format_i18n( (float) $rate ) : $rate ); ?></strong></p><?php endif; ?>
				<?php if ( $availability ) : ?><p class="availability"><i></i><?php echo esc_html( $availability ); ?></p><?php endif; ?>
				<ul><?php foreach ( preg_split( '/\r\n|\r|\n/', wp_strip_all_tags( $booking_benefits ) ) as $benefit ) : if ( trim( $benefit ) ) : ?><li><?php echo elevora_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( trim( $benefit ) ); ?></li><?php endif; endforeach; ?></ul>
				<?php echo elevora_render_action_button( $availability_url, $availability_button_text, 'button button--accent button--block', 'arrow', 'availability-trainer-' . get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php $email = elevora_get_field_value( 'elevora_email' ); if ( $email ) : ?><a class="booking-card__contact" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo elevora_icon( 'mail' ); // phpcs:ignore ?><?php esc_html_e( 'Send a message', 'gravitas-coaching' ); ?></a><?php endif; ?>
			</aside>
		</div>
	</main>
	<?php elevora_related_posts( 'trainer', 3 ); ?>
<?php endwhile; get_footer(); ?>
