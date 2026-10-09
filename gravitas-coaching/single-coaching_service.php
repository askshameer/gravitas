<?php
/**
 * Single coaching service.
 *
 * @package Elevora
 */
get_header();

while ( have_posts() ) : the_post();
	$summary     = elevora_get_field_value( 'elevora_service_summary' ) ?: get_the_excerpt();
	$duration    = elevora_get_field_value( 'elevora_service_duration' );
	$price       = elevora_get_field_value( 'elevora_service_price' );
	$duration_label   = elevora_get_field_value( 'elevora_duration_label', get_the_ID(), __( 'Typical duration', 'gravitas-coaching' ) );
	$investment_label = elevora_get_field_value( 'elevora_investment_label', get_the_ID(), __( 'Investment', 'gravitas-coaching' ) );
	$format_label     = elevora_get_field_value( 'elevora_format_label', get_the_ID(), __( 'Format', 'gravitas-coaching' ) );
	$format_text      = elevora_get_field_value( 'elevora_service_format' );
	$includes_title   = elevora_get_field_value( 'elevora_includes_title', get_the_ID(), __( 'This service includes', 'gravitas-coaching' ) );
	$includes_items   = elevora_get_field_value( 'elevora_includes_items', get_the_ID(), "A clear starting assessment\nPersonalized session plan\nPractical tools and reflection\nProgress check-ins" );
	$get_started_text = elevora_get_field_value( 'elevora_get_started_text', get_the_ID(), __( 'Get started', 'gravitas-coaching' ) );
	$icon        = elevora_get_field_value( 'elevora_service_icon', get_the_ID(), 'spark' );
	$booking_url = elevora_get_field_value( 'elevora_booking_url', get_the_ID(), '#contact' );
	$trainers    = (array) elevora_get_field_value( 'elevora_service_trainers', get_the_ID(), array() );
	?>
	<main id="primary" class="site-main service-single">
		<section class="service-hero">
			<div class="container"><?php elevora_breadcrumbs(); ?></div>
			<div class="container service-hero__grid"><div><div class="service-hero__icon"><?php echo elevora_icon( $icon ); // phpcs:ignore ?></div><span class="eyebrow eyebrow--light"><?php $category = get_the_terms( get_the_ID(), 'service_category' ); echo esc_html( $category && ! is_wp_error( $category ) ? $category[0]->name : __( 'Coaching service', 'gravitas-coaching' ) ); ?></span><h1><?php the_title(); ?></h1><?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?><?php echo elevora_render_action_button( $booking_url, __( 'Book a consultation', 'gravitas-coaching' ), 'button button--accent', 'arrow', 'service-booking-' . get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><div class="service-hero__facts"><?php if ( $duration ) : ?><div><span><?php echo esc_html( $duration_label ); ?></span><strong><?php echo esc_html( $duration ); ?></strong></div><?php endif; ?><?php if ( $price ) : ?><div><span><?php echo esc_html( $investment_label ); ?></span><strong><?php echo esc_html( $price ); ?></strong></div><?php endif; ?><div><span><?php echo esc_html( $format_label ); ?></span><strong><?php $delivery = get_the_terms( get_the_ID(), 'service_delivery' ); echo esc_html( $format_text ?: ( $delivery && ! is_wp_error( $delivery ) ? implode( ', ', wp_list_pluck( $delivery, 'name' ) ) : __( 'Flexible', 'gravitas-coaching' ) ) ); ?></strong></div></div></div>
		</section>
		<div class="container service-layout">
			<article class="service-content"><span class="eyebrow"><?php esc_html_e( 'What to expect', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'A focused process, tailored to you.', 'gravitas-coaching' ); ?></h2><div class="entry-content"><?php the_content(); ?></div><?php elevora_social_share(); ?></article>
			<aside class="service-aside"><h3><?php echo esc_html( $includes_title ); ?></h3><ul><?php foreach ( preg_split( '/\r\n|\r|\n/', wp_strip_all_tags( $includes_items ) ) as $included_item ) : if ( trim( $included_item ) ) : ?><li><?php echo elevora_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( trim( $included_item ) ); ?></li><?php endif; endforeach; ?></ul><?php echo elevora_render_action_button( $booking_url, $get_started_text, 'button button--dark button--block', '', 'service-get-started-' . get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></aside>
		</div>
		<?php if ( $trainers ) : ?><section class="section section--sage"><div class="container section-heading"><div><span class="eyebrow"><?php esc_html_e( 'Your coaching team', 'gravitas-coaching' ); ?></span><h2><?php esc_html_e( 'Specialists for this service.', 'gravitas-coaching' ); ?></h2></div></div><div class="container card-grid card-grid--three"><?php foreach ( $trainers as $trainer ) : $trainer_id = is_object( $trainer ) ? $trainer->ID : absint( $trainer ); if ( 'trainer' !== get_post_type( $trainer_id ) ) { continue; } $GLOBALS['post'] = get_post( $trainer_id ); setup_postdata( $GLOBALS['post'] ); get_template_part( 'template-parts/card', 'trainer' ); endforeach; wp_reset_postdata(); ?></div></section><?php endif; ?>
	</main>
	<?php elevora_related_posts( 'coaching_service', 3 ); ?>
<?php endwhile; get_footer(); ?>
