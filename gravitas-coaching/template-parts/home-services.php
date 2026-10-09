<?php
/** Compact, linked service cards with a useful pre-import fallback. @package Elevora */
$focus_services = new WP_Query( array( 'post_type' => 'coaching_service', 'posts_per_page' => 4, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
$focus_cards = array();
while ( $focus_services->have_posts() ) {
	$focus_services->the_post();
	$focus_cards[] = array(
		'title' => get_the_title(),
		'text'  => wp_trim_words( elevora_get_field_value( 'elevora_service_summary' ) ?: get_the_excerpt(), 22 ),
		'icon'  => elevora_get_field_value( 'elevora_service_icon', get_the_ID(), 'spark' ),
		'url'   => get_permalink(),
	);
}
wp_reset_postdata();
if ( ! $focus_cards ) {
	$focus_cards = array(
		array( 'title' => __( 'Personal Branding', 'gravitas-coaching' ), 'text' => __( 'Express your individuality through a credible, consistent professional image.', 'gravitas-coaching' ), 'icon' => 'spark' ),
		array( 'title' => __( 'Executive Presence', 'gravitas-coaching' ), 'text' => __( 'Lead with confidence, communicate with authority, and inspire trust.', 'gravitas-coaching' ), 'icon' => 'microphone' ),
		array( 'title' => __( 'Soft Skills Training', 'gravitas-coaching' ), 'text' => __( 'Build stronger connections through communication, empathy, and collaboration.', 'gravitas-coaching' ), 'icon' => 'people' ),
		array( 'title' => __( 'Leadership Development', 'gravitas-coaching' ), 'text' => __( 'Develop the perspective and people skills to bring out the best in others.', 'gravitas-coaching' ), 'icon' => 'chart' ),
	);
}
?>
<section class="focus-services container" id="services" aria-label="<?php esc_attr_e( 'Our coaching services', 'gravitas-coaching' ); ?>">
	<?php foreach ( $focus_cards as $focus_card ) : ?>
		<a class="focus-card" href="<?php echo esc_url( $focus_card['url'] ?? get_post_type_archive_link( 'coaching_service' ) ); ?>">
			<?php echo elevora_icon( $focus_card['icon'] ); // phpcs:ignore ?>
			<h2><?php echo esc_html( $focus_card['title'] ); ?></h2>
			<p><?php echo esc_html( $focus_card['text'] ); ?></p>
			<span class="focus-card__rule" aria-hidden="true"></span>
		</a>
	<?php endforeach; ?>
</section>
