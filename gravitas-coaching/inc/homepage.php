<?php
/**
 * Shared native homepage renderer.
 *
 * The front-page template and Elementor homepage widget both use this exact
 * markup so editing with Elementor never changes the original design.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return editable homepage defaults.
 *
 * @return array<string, mixed>
 */
function elevora_homepage_defaults() {
	$hero_image_id = absint( elevora_get_setting( 'hero_image_id', 'elevora_hero_image', 0 ) );
	return array(
		'hero_image'              => array( 'id' => $hero_image_id, 'url' => $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : ELEVORA_URI . '/assets/images/hero-coaching.jpg' ),
		'hero_eyebrow'            => elevora_get_setting( 'hero_eyebrow', 'elevora_hero_eyebrow', __( 'Soft skills. Stronger presence.', 'gravitas-coaching' ) ),
		'hero_title'              => elevora_get_setting( 'hero_title', 'elevora_hero_title', __( 'Unlock Your Next Level', 'gravitas-coaching' ) ),
		'hero_highlight'          => elevora_get_setting( 'hero_highlight', 'elevora_hero_highlight', __( 'of Growth and Leadership', 'gravitas-coaching' ) ),
		'hero_text'               => elevora_get_setting( 'hero_text', 'elevora_hero_text', __( 'Build your image, presence, and essential soft skills for a more confident professional future.', 'gravitas-coaching' ) ),
		'hero_primary_text'       => elevora_get_setting( 'hero_primary_text', 'elevora_hero_button', __( 'Find your consultant', 'gravitas-coaching' ) ),
		'hero_primary_url'        => elevora_get_setting( 'hero_primary_url', 'elevora_hero_url', '' ) ?: get_post_type_archive_link( 'trainer' ),
		'hero_secondary_text'     => elevora_get_setting( 'hero_secondary_text', 'elevora_hero_alt_text', __( 'Explore programs', 'gravitas-coaching' ) ),
		'hero_secondary_url'      => elevora_get_setting( 'hero_secondary_url', 'elevora_hero_alt_url', '' ) ?: get_post_type_archive_link( 'coaching_service' ),
		'focus_count'             => 4,
		'intro_eyebrow'           => __( 'Presence with purpose', 'gravitas-coaching' ),
		'intro_title'             => __( 'Elevate Your Impact. Inspire Others.', 'gravitas-coaching' ),
		'intro_text'              => __( 'Your expertise deserves to be seen. Connect your personal image, communication, and confidence to make a lasting impression wherever your career takes you.', 'gravitas-coaching' ),
		'intro_link_text'         => __( 'Meet your consultant', 'gravitas-coaching' ),
		'intro_detail'            => __( 'Professional growth starts with how you show up. Our coaching brings together the human skills and personal presence that help you build trust, connect with others, and lead with intention.', 'gravitas-coaching' ),
		'intro_bullet_1'          => __( 'Communicate with clarity and confidence', 'gravitas-coaching' ),
		'intro_bullet_2'          => __( 'Build an authentic professional image', 'gravitas-coaching' ),
		'intro_bullet_3'          => __( 'Turn new skills into lasting habits', 'gravitas-coaching' ),
		'process_eyebrow'         => __( 'A path made for you', 'gravitas-coaching' ),
		'process_title'           => __( 'Our Simple Four-Step Process', 'gravitas-coaching' ),
		'process_1_title'         => __( 'Consultation', 'gravitas-coaching' ),
		'process_1_text'          => __( 'Share your goals in an initial conversation with your consultant.', 'gravitas-coaching' ),
		'process_2_title'         => __( 'Assessment', 'gravitas-coaching' ),
		'process_2_text'          => __( 'Discover your strengths and the opportunities to build on them.', 'gravitas-coaching' ),
		'process_3_title'         => __( 'Customized Plan', 'gravitas-coaching' ),
		'process_3_text'          => __( 'Shape a practical coaching plan around your ambitions and needs.', 'gravitas-coaching' ),
		'process_4_title'         => __( 'Coaching & Growth', 'gravitas-coaching' ),
		'process_4_text'          => __( 'Put learning into practice with personal support and feedback.', 'gravitas-coaching' ),
		'finder_eyebrow'          => __( 'Start here', 'gravitas-coaching' ),
		'finder_title'            => __( 'What would you like to strengthen?', 'gravitas-coaching' ),
		'finder_placeholder'      => __( 'Skill, goal, or consultant', 'gravitas-coaching' ),
		'finder_button'           => __( 'Find a match', 'gravitas-coaching' ),
		'services_eyebrow'        => __( 'Choose your focus', 'gravitas-coaching' ),
		'services_title'          => __( 'Practical programs for skills, style, and professional impact.', 'gravitas-coaching' ),
		'services_link_text'      => __( 'All programs', 'gravitas-coaching' ),
		'services_count'          => 6,
		'trainers_eyebrow'        => __( 'Meet the experts', 'gravitas-coaching' ),
		'trainers_title'          => __( 'Consultants who connect inner confidence with visible presence.', 'gravitas-coaching' ),
		'trainers_link_text'      => __( 'View the directory', 'gravitas-coaching' ),
		'trainers_count'          => 8,
		'testimonials_eyebrow'    => __( 'Client stories', 'gravitas-coaching' ),
		'testimonials_title'      => __( 'Progress feels different when it is yours.', 'gravitas-coaching' ),
		'testimonials_count'      => 3,
		'journal_eyebrow'         => __( 'The Gravitas journal', 'gravitas-coaching' ),
		'journal_title'           => __( 'Practical ideas for communication, style, and influence.', 'gravitas-coaching' ),
		'journal_count'           => 3,
	);
}

/**
 * Render the original homepage design with optional Elementor values.
 *
 * @param array<string, mixed> $settings Editable homepage values.
 */
function elevora_render_homepage( $settings = array() ) {
	$settings    = wp_parse_args( $settings, elevora_homepage_defaults() );
	$hero_image  = is_array( $settings['hero_image'] ) && ! empty( $settings['hero_image']['url'] ) ? $settings['hero_image']['url'] : ELEVORA_URI . '/assets/images/hero-coaching.jpg';
	$trainer_url = $settings['hero_primary_url'] ?: get_post_type_archive_link( 'trainer' );
	$service_url = $settings['hero_secondary_url'] ?: get_post_type_archive_link( 'coaching_service' );
	?>
	<main id="primary" class="site-main home-main">
		<section class="hero" style="--hero-image:url('<?php echo esc_url( $hero_image ); ?>')"><div class="hero__wash"></div><div class="container hero__inner"><div class="hero__content">
			<span class="eyebrow eyebrow--light"><?php echo esc_html( $settings['hero_eyebrow'] ); ?></span>
			<h1><?php echo esc_html( $settings['hero_title'] ); ?><span class="hero__accent"><?php echo esc_html( $settings['hero_highlight'] ); ?></span></h1>
			<p><?php echo esc_html( $settings['hero_text'] ); ?></p>
			<div class="hero__actions"><a class="button button--accent" href="<?php echo esc_url( $trainer_url ); ?>"><?php echo esc_html( $settings['hero_primary_text'] ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><a class="text-link text-link--light" href="<?php echo esc_url( $service_url ); ?>"><?php echo esc_html( $settings['hero_secondary_text'] ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
		</div><div class="hero__note"><div class="hero__note-number">4.9</div><div><div class="stars">★★★★★</div><p><?php esc_html_e( 'Rated by 500+ Executives', 'gravitas-coaching' ); ?></p></div></div></div></section>

		<?php
		$focus_services = new WP_Query( array( 'post_type' => 'coaching_service', 'posts_per_page' => min( 8, max( 1, absint( $settings['focus_count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
		$focus_cards = array();
		while ( $focus_services->have_posts() ) {
			$focus_services->the_post();
			$focus_cards[] = array( 'title' => get_the_title(), 'text' => wp_trim_words( elevora_get_field_value( 'elevora_service_summary' ) ?: get_the_excerpt(), 22 ), 'icon' => elevora_get_field_value( 'elevora_service_icon', get_the_ID(), 'spark' ), 'url' => get_permalink() );
		}
		wp_reset_postdata();
		if ( ! $focus_cards ) {
			$focus_cards = array(
				array( 'title' => __( 'Personal Branding', 'gravitas-coaching' ), 'text' => __( 'Express your individuality through a credible, consistent professional image.', 'gravitas-coaching' ), 'icon' => 'spark', 'url' => $service_url ),
				array( 'title' => __( 'Executive Presence', 'gravitas-coaching' ), 'text' => __( 'Lead with confidence, communicate with authority, and inspire trust.', 'gravitas-coaching' ), 'icon' => 'microphone', 'url' => $service_url ),
				array( 'title' => __( 'Soft Skills Training', 'gravitas-coaching' ), 'text' => __( 'Build stronger connections through communication, empathy, and collaboration.', 'gravitas-coaching' ), 'icon' => 'people', 'url' => $service_url ),
				array( 'title' => __( 'Leadership Development', 'gravitas-coaching' ), 'text' => __( 'Develop the perspective and people skills to bring out the best in others.', 'gravitas-coaching' ), 'icon' => 'chart', 'url' => $service_url ),
			);
			$focus_cards = array_slice( $focus_cards, 0, min( 4, absint( $settings['focus_count'] ) ) );
		}
		?>
		<?php if ( $focus_cards ) : ?><section class="focus-services container" id="services" aria-label="<?php esc_attr_e( 'Our coaching services', 'gravitas-coaching' ); ?>"><?php foreach ( $focus_cards as $focus_card ) : ?><a class="focus-card" href="<?php echo esc_url( $focus_card['url'] ); ?>"><?php echo elevora_icon( $focus_card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><h2><?php echo esc_html( $focus_card['title'] ); ?></h2><p><?php echo esc_html( $focus_card['text'] ); ?></p><span class="focus-card__rule" aria-hidden="true"></span></a><?php endforeach; ?></section><?php endif; ?>

		<section class="consulting-intro section" id="about"><div class="container consulting-intro__grid"><div><span class="eyebrow"><?php echo esc_html( $settings['intro_eyebrow'] ); ?></span><h2><?php echo esc_html( $settings['intro_title'] ); ?></h2><p><?php echo esc_html( $settings['intro_text'] ); ?></p><a class="text-link" href="<?php echo esc_url( $trainer_url ); ?>"><?php echo esc_html( $settings['intro_link_text'] ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div><div class="consulting-intro__detail"><p><?php echo esc_html( $settings['intro_detail'] ); ?></p><ul><li><?php echo esc_html( $settings['intro_bullet_1'] ); ?></li><li><?php echo esc_html( $settings['intro_bullet_2'] ); ?></li><li><?php echo esc_html( $settings['intro_bullet_3'] ); ?></li></ul></div><div class="consulting-process" id="process"><span class="eyebrow"><?php echo esc_html( $settings['process_eyebrow'] ); ?></span><h2><?php echo esc_html( $settings['process_title'] ); ?></h2><ol><?php for ( $step = 1; $step <= 4; $step++ ) : ?><li><h3><?php echo esc_html( $settings[ 'process_' . $step . '_title' ] ); ?></h3><p><?php echo esc_html( $settings[ 'process_' . $step . '_text' ] ); ?></p></li><?php endfor; ?></ol></div></div></section>

		<section class="finder-strip" aria-label="<?php esc_attr_e( 'Coach finder', 'gravitas-coaching' ); ?>"><div class="container"><?php echo elevora_home_finder_shortcode( array( 'eyebrow' => $settings['finder_eyebrow'], 'title' => $settings['finder_title'], 'placeholder' => $settings['finder_placeholder'], 'button' => $settings['finder_button'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></section>

		<?php $services = new WP_Query( array( 'post_type' => 'coaching_service', 'posts_per_page' => min( 24, max( 1, absint( $settings['services_count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ); if ( $services->have_posts() ) : ?><section class="section section--sage services-section"><div class="container section-heading"><div><span class="eyebrow"><?php echo esc_html( $settings['services_eyebrow'] ); ?></span><h2><?php echo esc_html( $settings['services_title'] ); ?></h2></div><a class="text-link" href="<?php echo esc_url( $service_url ); ?>"><?php echo esc_html( $settings['services_link_text'] ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div><div class="container card-grid card-grid--three"><?php while ( $services->have_posts() ) : $services->the_post(); get_template_part( 'template-parts/card', 'service' ); endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>

		<?php
		$trainers = new WP_Query(
			array(
				'post_type'      => 'trainer',
				'posts_per_page' => min( 24, max( 1, absint( $settings['trainers_count'] ) ) ),
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			)
		);
		if ( $trainers->have_posts() ) :
		?>
		<section class="section trainers-section" id="trainers">
			<div class="container section-heading section-heading--with-nav">
				<div>
					<span class="eyebrow"><?php echo esc_html( $settings['trainers_eyebrow'] ); ?></span>
					<h2><?php echo esc_html( $settings['trainers_title'] ); ?></h2>
				</div>
				<div class="trainers-heading-actions">
					<a class="text-link" href="<?php echo esc_url( $trainer_url ); ?>">
						<?php echo esc_html( $settings['trainers_link_text'] ); ?>
						<?php echo elevora_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div class="trainer-carousel-nav" aria-label="<?php esc_attr_e( 'Consultant carousel controls', 'gravitas-coaching' ); ?>">
						<button type="button" class="trainer-carousel-arrow trainer-carousel-arrow--prev" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous consultants', 'gravitas-coaching' ); ?>">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
						</button>
						<button type="button" class="trainer-carousel-arrow trainer-carousel-arrow--next" data-carousel-next aria-label="<?php esc_attr_e( 'Next consultants', 'gravitas-coaching' ); ?>">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
						</button>
					</div>
				</div>
			</div>
			<div class="container trainer-carousel-wrap" data-trainer-carousel>
				<div class="trainer-carousel-track" data-carousel-track role="region" aria-label="<?php esc_attr_e( 'Consultants slider', 'gravitas-coaching' ); ?>" tabindex="0">
					<?php
					while ( $trainers->have_posts() ) :
						$trainers->the_post();
						?>
						<div class="trainer-carousel-slide">
							<?php get_template_part( 'template-parts/card', 'trainer' ); ?>
						</div>
					<?php endwhile; ?>
				</div>
				<div class="trainer-carousel-dots" data-carousel-dots aria-hidden="true"></div>
			</div>
		</section>
		<?php endif; wp_reset_postdata(); ?>

		<?php $testimonials = new WP_Query( array( 'post_type' => 'testimonial', 'posts_per_page' => min( 12, max( 1, absint( $settings['testimonials_count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) ); if ( $testimonials->have_posts() ) : ?><section class="section testimonials-section"><div class="container section-heading"><div><span class="eyebrow"><?php echo esc_html( $settings['testimonials_eyebrow'] ); ?></span><h2><?php echo esc_html( $settings['testimonials_title'] ); ?></h2></div></div><div class="container testimonial-grid"><?php while ( $testimonials->have_posts() ) : $testimonials->the_post(); ?><figure class="testimonial-card"><?php echo elevora_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><blockquote><?php the_content(); ?></blockquote><figcaption><strong><?php the_title(); ?></strong><span><?php echo esc_html( trim( elevora_get_field_value( 'elevora_client_role' ) . ', ' . elevora_get_field_value( 'elevora_client_company' ), ', ' ) ); ?></span></figcaption></figure><?php endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>

		<?php $insights = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => min( 24, max( 1, absint( $settings['journal_count'] ) ) ), 'ignore_sticky_posts' => true ) ); if ( $insights->have_posts() ) : ?><section class="section journal-section"><div class="container section-heading"><div><span class="eyebrow"><?php echo esc_html( $settings['journal_eyebrow'] ); ?></span><h2><?php echo esc_html( $settings['journal_title'] ); ?></h2></div></div><div class="container card-grid card-grid--three"><?php while ( $insights->have_posts() ) : $insights->the_post(); get_template_part( 'template-parts/card', 'post' ); endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>
	</main>
	<?php
}
