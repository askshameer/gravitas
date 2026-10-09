<?php
/**
 * Service card.
 *
 * @package Elevora
 */
$summary  = elevora_get_field_value( 'elevora_service_summary' ) ?: get_the_excerpt();
$duration = elevora_get_field_value( 'elevora_service_duration' );
$price    = elevora_get_field_value( 'elevora_service_price' );
$icon     = elevora_get_field_value( 'elevora_service_icon', get_the_ID(), 'spark' );
?>
<article <?php post_class( 'service-card' ); ?>>
	<div class="service-card__icon"><?php echo elevora_icon( $icon ); // phpcs:ignore ?></div>
	<div class="service-card__number"><?php echo esc_html( str_pad( (string) ( get_post_field( 'menu_order' ) + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
	<div class="service-card__meta">
		<?php if ( $duration ) : ?><span><?php echo elevora_icon( 'clock' ); // phpcs:ignore ?><?php echo esc_html( $duration ); ?></span><?php endif; ?>
		<?php if ( $price ) : ?><span><?php echo esc_html( $price ); ?></span><?php endif; ?>
	</div>
	<a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Explore service', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
</article>

