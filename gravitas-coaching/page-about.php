<?php
/**
 * Template Name: About Page (Visual)
 * Template Post Type: page
 *
 * Visual, high-impact About template for Gravitas Coaching.
 *
 * @package Elevora
 */

get_header();

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'single' ) ) {
	get_footer();
	return;
}

if ( elevora_render_elementor_document() ) {
	get_footer();
	return;
}

$img_dir      = ELEVORA_URI . '/assets/images/';
$contact_page = get_page_by_path( 'contact' );
$contact_url  = $contact_page ? get_permalink( $contact_page ) : '#contact';
$services_url = get_post_type_archive_link( 'coaching_service' ) ?: '#services';
$trainers_url = get_post_type_archive_link( 'trainer' ) ?: '#trainers';

while ( have_posts() ) :
	the_post();
	?>
<main id="primary" class="site-main about-main">
	<header class="page-hero">
		<div class="container">
			<?php if ( elevora_get_setting( 'show_page_breadcrumbs', '', 1 ) ) { elevora_breadcrumbs(); } ?>
			<span class="eyebrow"><?php esc_html_e( 'About Gravitas', 'gravitas-coaching' ); ?></span>
			<h1><?php the_title(); ?></h1>
			<p><?php echo has_excerpt() ? esc_html( get_the_excerpt() ) : esc_html__( 'Professional presence becomes powerful when skills, confidence, and image communicate the same message.', 'gravitas-coaching' ); ?></p>
		</div>
	</header>

	<!-- Section 1: Who We Are & What We Do (Dual Visual Showcase) -->
	<section class="about-section about-intro">
		<div class="container">
			<div class="about-gallery-duo">
				<div class="about-gallery-duo__item about-gallery-duo__item--primary">
					<img src="<?php echo esc_url( $img_dir . 'stock-executive-presence.jpg' ); ?>" alt="<?php esc_attr_e( 'Executive boardroom leadership coaching', 'gravitas-coaching' ); ?>" loading="eager">
					<div class="about-badge-floating">
						<span class="about-badge-floating__mark"><?php echo elevora_icon( 'spark' ); // phpcs:ignore ?></span>
						<div>
							<strong><?php esc_html_e( 'The Gravitas Standard', 'gravitas-coaching' ); ?></strong>
							<span><?php esc_html_e( 'Substance & Signature Presence', 'gravitas-coaching' ); ?></span>
						</div>
					</div>
				</div>
				<div class="about-gallery-duo__item about-gallery-duo__item--secondary">
					<img src="<?php echo esc_url( $img_dir . 'stock-professional-image.jpg' ); ?>" alt="<?php esc_attr_e( 'Personal styling and image consultation', 'gravitas-coaching' ); ?>" loading="lazy">
				</div>
			</div>

			<div class="about-lead-block">
				<span class="eyebrow"><?php esc_html_e( 'Who We Are & What We Do', 'gravitas-coaching' ); ?></span>
				<h2 class="about-serif-title"><?php esc_html_e( 'Guiding Leaders to Embody Authentic Authority & Style', 'gravitas-coaching' ); ?></h2>
				<div class="about-ornament" aria-hidden="true">
					<span></span><i>◆</i><span></span>
				</div>
				<p class="about-lead-text">
					<?php esc_html_e( 'Gravitas Coaching was established on a singular principle: true executive influence requires both deep substance and conscious visual intention. We partner with founders, executives, and rising leaders to cultivate nonverbal authority, master persuasive communication, and curate an authentic wardrobe that commands trust in every room.', 'gravitas-coaching' ); ?>
				</p>
			</div>

			<div class="about-pillars-grid">
				<div class="about-pillar-card">
					<div class="about-pillar-card__icon"><?php echo elevora_icon( 'spark' ); // phpcs:ignore ?></div>
					<h3><?php esc_html_e( 'Executive Presence', 'gravitas-coaching' ); ?></h3>
					<p><?php esc_html_e( 'Cultivate composure under stress, commanding body language, boardroom gravitas, and active listening in high-stakes environments.', 'gravitas-coaching' ); ?></p>
				</div>
				<div class="about-pillar-card">
					<div class="about-pillar-card__icon"><?php echo elevora_icon( 'compass' ); // phpcs:ignore ?></div>
					<h3><?php esc_html_e( 'Wardrobe & Image Styling', 'gravitas-coaching' ); ?></h3>
					<p><?php esc_html_e( 'Align silhouette, proportion, fabrics, and colors into a cohesive personal brand that simplifies daily dressing and elevates credibility.', 'gravitas-coaching' ); ?></p>
				</div>
				<div class="about-pillar-card">
					<div class="about-pillar-card__icon"><?php echo elevora_icon( 'microphone' ); // phpcs:ignore ?></div>
					<h3><?php esc_html_e( 'Strategic Communication', 'gravitas-coaching' ); ?></h3>
					<p><?php esc_html_e( 'Structure persuasive arguments, project vocal resonance, eliminate filler, and present with clarity whether in person or on video.', 'gravitas-coaching' ); ?></p>
				</div>
				<div class="about-pillar-card">
					<div class="about-pillar-card__icon"><?php echo elevora_icon( 'people' ); // phpcs:ignore ?></div>
					<h3><?php esc_html_e( 'Business Etiquette', 'gravitas-coaching' ); ?></h3>
					<p><?php esc_html_e( 'Command executive dinners, international delegations, and complex negotiations with diplomatic tact, polish, and cultural fluency.', 'gravitas-coaching' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 2: Split Story Section (Methodology & Philosophy) -->
	<section class="about-section about-philosophy">
		<div class="container">
			<div class="about-split-grid">
				<div class="about-split-visual">
					<div class="about-split-image-wrap">
						<img src="<?php echo esc_url( $img_dir . 'stock-presentation-opening.jpg' ); ?>" alt="<?php esc_attr_e( 'Gravitas coaching and executive presence studio', 'gravitas-coaching' ); ?>" loading="lazy">
						<div class="about-experience-card">
							<span class="about-experience-card__num">15+</span>
							<div class="about-experience-card__info">
								<strong><?php esc_html_e( 'Years of Excellence', 'gravitas-coaching' ); ?></strong>
								<div class="stars">★★★★★</div>
								<span><?php esc_html_e( '500+ Executives Guided', 'gravitas-coaching' ); ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="about-split-content">
					<span class="eyebrow"><?php esc_html_e( 'Our Philosophy', 'gravitas-coaching' ); ?></span>
					<h2 class="about-serif-title"><?php esc_html_e( 'Presence Built from the Inside Out', 'gravitas-coaching' ); ?></h2>
					<p class="about-split-intro">
						<?php esc_html_e( 'We do not believe in superficial makeovers or scripted performances. Real professional impact is born when internal clarity, behavioral habits, and external presentation communicate the exact same truth.', 'gravitas-coaching' ); ?>
					</p>
					<ul class="about-checklist">
						<li>
							<span class="about-checklist__check"><?php echo elevora_icon( 'check' ); // phpcs:ignore ?></span>
							<div>
								<strong><?php esc_html_e( 'Authenticity Before Imitation', 'gravitas-coaching' ); ?></strong>
								<p><?php esc_html_e( 'We amplify your innate leadership archetype rather than fitting you into a rigid corporate mold.', 'gravitas-coaching' ); ?></p>
							</div>
						</li>
						<li>
							<span class="about-checklist__check"><?php echo elevora_icon( 'check' ); // phpcs:ignore ?></span>
							<div>
								<strong><?php esc_html_e( 'Real-Play Practice & Feedback', 'gravitas-coaching' ); ?></strong>
								<p><?php esc_html_e( 'Rehearse high-pressure presentations and difficult conversations with timestamped video analysis.', 'gravitas-coaching' ); ?></p>
							</div>
						</li>
						<li>
							<span class="about-checklist__check"><?php echo elevora_icon( 'check' ); // phpcs:ignore ?></span>
							<div>
								<strong><?php esc_html_e( 'Sustainable Wardrobe Systems', 'gravitas-coaching' ); ?></strong>
								<p><?php esc_html_e( 'Build a high-functioning capsule wardrobe that eliminates decision friction and ensures consistent polish.', 'gravitas-coaching' ); ?></p>
							</div>
						</li>
						<li>
							<span class="about-checklist__check"><?php echo elevora_icon( 'check' ); // phpcs:ignore ?></span>
							<div>
								<strong><?php esc_html_e( 'Measurable Behavioral Growth', 'gravitas-coaching' ); ?></strong>
								<p><?php esc_html_e( 'Track visible progress through stakeholder perception shifts and board-level confidence.', 'gravitas-coaching' ); ?></p>
							</div>
						</li>
					</ul>
					<div class="about-split-actions">
						<a class="button button--accent" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Book a Consultation', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
						<a class="text-link" href="<?php echo esc_url( $services_url ); ?>"><?php esc_html_e( 'Explore Coaching Programs', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 3: 3-Column Highlights / Stats / Proof Points -->
	<section class="about-section about-highlights">
		<div class="container">
			<div class="section-heading-centered">
				<span class="eyebrow"><?php esc_html_e( 'Proven Impact', 'gravitas-coaching' ); ?></span>
				<h2 class="about-serif-title"><?php esc_html_e( 'The Hallmarks of Gravitas Engagements', 'gravitas-coaching' ); ?></h2>
				<div class="about-ornament" aria-hidden="true">
					<span></span><i>◆</i><span></span>
				</div>
			</div>

			<div class="about-highlights-grid">
				<article class="about-highlight-card">
					<div class="about-highlight-card__media">
						<img src="<?php echo esc_url( $img_dir . 'stock-confident-speaking.jpg' ); ?>" alt="<?php esc_attr_e( '500+ Executives Coached', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-highlight-card__body">
						<span class="about-highlight-card__tag"><?php esc_html_e( 'Track Record', 'gravitas-coaching' ); ?></span>
						<h3 class="about-serif-subtitle"><?php esc_html_e( '500+ Executives Coached', 'gravitas-coaching' ); ?></h3>
						<p><?php esc_html_e( 'Trusted by founders, VP-level directors, and partners across top technology, finance, legal, and consumer organizations worldwide.', 'gravitas-coaching' ); ?></p>
					</div>
				</article>

				<article class="about-highlight-card">
					<div class="about-highlight-card__media">
						<img src="<?php echo esc_url( $img_dir . 'stock-leadership-wardrobe.jpg' ); ?>" alt="<?php esc_attr_e( '100% Bespoke Consultation', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-highlight-card__body">
						<span class="about-highlight-card__tag"><?php esc_html_e( 'Individual Tailoring', 'gravitas-coaching' ); ?></span>
						<h3 class="about-serif-subtitle"><?php esc_html_e( '100% Bespoke Curriculum', 'gravitas-coaching' ); ?></h3>
						<p><?php esc_html_e( 'Every engagement is customized to your role expectations, industry dress codes, vocal habits, and unique professional ambitions.', 'gravitas-coaching' ); ?></p>
					</div>
				</article>

				<article class="about-highlight-card">
					<div class="about-highlight-card__media">
						<img src="<?php echo esc_url( $img_dir . 'stock-meeting-preparation.jpg' ); ?>" alt="<?php esc_attr_e( 'Measurable Career Impact', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-highlight-card__body">
						<span class="about-highlight-card__tag"><?php esc_html_e( 'Outcomes', 'gravitas-coaching' ); ?></span>
						<h3 class="about-serif-subtitle"><?php esc_html_e( 'Measurable Career Momentum', 'gravitas-coaching' ); ?></h3>
						<p><?php esc_html_e( 'Over 88% of our executive clients report expedited promotion, higher board visibility, and calmer confidence within three months.', 'gravitas-coaching' ); ?></p>
					</div>
				</article>
			</div>
		</div>
	</section>

	<!-- Section 4: Leadership Advisory Team -->
	<section class="about-section about-team">
		<div class="container">
			<div class="about-team-header">
				<div>
					<span class="eyebrow"><?php esc_html_e( 'Expert Advisory', 'gravitas-coaching' ); ?></span>
					<h2 class="about-serif-title"><?php esc_html_e( 'Meet Our Leadership Consultants', 'gravitas-coaching' ); ?></h2>
				</div>
				<a class="button button--secondary" href="<?php echo esc_url( $trainers_url ); ?>"><?php esc_html_e( 'View All Consultants', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
			</div>

			<div class="about-team-grid">
				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-marcus.jpg' ); ?>" alt="<?php esc_attr_e( 'Marcus Reed', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>Marcus Reed</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Executive Presence & Leadership Coach', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Specializes in board-level poise, high-stakes communication, and calm authority under intense pressure.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>

				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-priya.jpg' ); ?>" alt="<?php esc_attr_e( 'Priya Nair', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>Priya Nair</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Image Consultant & Personal Brand Strategist', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Connects color psychology, body architecture, and intentional wardrobe strategy into a repeatable signature look.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>

				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-kenji.jpg' ); ?>" alt="<?php esc_attr_e( 'Kenji Sato', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>Kenji Sato</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Communication & Cross-Cultural Coach', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Brings clarity to multicultural environments, persuasive pitch delivery, and delicate stakeholder alignment.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>

				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-claire.jpg' ); ?>" alt="<?php esc_attr_e( 'Claire Bennett', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>Claire Bennett</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Confidence & Personal Impact Coach', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Guides leaders stepping into higher visibility roles, keynotes, and critical public media appearances.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>

				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-elena.jpg' ); ?>" alt="<?php esc_attr_e( 'Elena Rostova', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>Elena Rostova</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Executive Voice & Keynote Poise Coach', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Specializes in commanding vocal projection, breath architecture, and presence for keynote auditoriums.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>

				<div class="about-team-card">
					<div class="about-team-card__media">
						<img src="<?php echo esc_url( $img_dir . 'trainer-david.jpg' ); ?>" alt="<?php esc_attr_e( 'David Sterling', 'gravitas-coaching' ); ?>" loading="lazy">
					</div>
					<div class="about-team-card__body">
						<h3>David Sterling</h3>
						<span class="about-team-card__role"><?php esc_html_e( 'Strategic Negotiation & Poise Consultant', 'gravitas-coaching' ); ?></span>
						<p><?php esc_html_e( 'Prepares senior executives for adversarial stakeholder negotiations, board inquiries, and high-consequence decisions.', 'gravitas-coaching' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 5: Executive Endorsement / Quote Banner -->
	<section class="about-section about-quote-section">
		<div class="container">
			<div class="about-quote-card">
				<div class="about-quote-card__mark"><?php echo elevora_icon( 'quote' ); // phpcs:ignore ?></div>
				<blockquote class="about-quote-card__text">
					“Working with Gravitas completely transformed how I show up in board meetings. My communication is sharper, my presence is calm and authoritative, and my wardrobe finally reflects the level I operate at.”
				</blockquote>
				<div class="about-quote-card__author">
					<strong>Maya Thompson</strong>
					<span><?php esc_html_e( 'VP, Customer Experience — Northstar Labs', 'gravitas-coaching' ); ?></span>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 6: Consultation CTA Strip -->
	<section class="about-section about-cta-section">
		<div class="container">
			<div class="about-cta-card">
				<span class="eyebrow"><?php esc_html_e( 'Start Your Journey', 'gravitas-coaching' ); ?></span>
				<h2 class="about-serif-title"><?php esc_html_e( 'Ready to Elevate Your Executive Presence?', 'gravitas-coaching' ); ?></h2>
				<p><?php esc_html_e( 'Schedule a confidential 30-minute discovery conversation with our senior advisory team to discuss your goals and shape a tailored coaching program.', 'gravitas-coaching' ); ?></p>
				<div class="about-cta-card__actions">
					<a class="button button--accent" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Schedule Discovery Call', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
					<a class="button button--secondary" href="<?php echo esc_url( $services_url ); ?>"><?php esc_html_e( 'View Programs & Pricing', 'gravitas-coaching' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php
	$about_custom_content = trim( get_the_content() );
	$legacy_demo_content  = '<h2>Presence from the inside out</h2><p>Gravitas Coaching brings soft skills development and image consulting into one practical process. We help professionals communicate clearly, behave confidently, and present themselves with intention.</p><h2>What we value</h2><ul><li>Authenticity before imitation</li><li>Practical learning for real situations</li><li>Respect for culture, context, and individuality</li><li>Visible progress that clients can sustain</li></ul>';
	$is_legacy_demo_copy  = get_post_meta( get_the_ID(), '_elevora_demo_content', true ) && $about_custom_content === $legacy_demo_content;
	?>
	<?php if ( '' !== $about_custom_content && ! $is_legacy_demo_copy ) : ?>
	<section class="about-section about-custom-content">
		<div class="container container--article">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>
</main>
	<?php
endwhile;
get_footer();
