<?php
/**
 * Trainer card.
 *
 * @package Elevora
 */
$role       = elevora_get_field_value( 'elevora_role' );
$rating     = (float) elevora_get_field_value( 'elevora_rating', get_the_ID(), 5 );
$rate       = elevora_get_field_value( 'elevora_hourly_rate' );
$experience = elevora_get_field_value( 'elevora_experience' );
$location   = get_the_terms( get_the_ID(), 'trainer_location' );
?>
<article <?php post_class( 'trainer-card' ); ?>>
	<a class="trainer-card__media" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'elevora-trainer', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<div class="media-placeholder"><span><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></span></div>
		<?php endif; ?>
		<span class="trainer-card__rating"><?php echo elevora_icon( 'star' ); // phpcs:ignore ?><?php echo esc_html( number_format_i18n( $rating, 1 ) ); ?></span>
	</a>
	<div class="trainer-card__body">
		<div class="trainer-card__heading"><div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php if ( $role ) : ?><p><?php echo esc_html( $role ); ?></p><?php endif; ?></div><a class="circle-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'gravitas-coaching' ), get_the_title() ) ); ?>"><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a></div>
		<div class="tag-list"><?php elevora_term_links( get_the_ID(), 'trainer_specialization', 2 ); ?></div>
		<div class="trainer-card__meta">
			<?php if ( $location && ! is_wp_error( $location ) ) : ?><span><?php echo elevora_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $location[0]->name ); ?></span><?php endif; ?>
			<?php if ( $experience ) : ?><span><?php echo esc_html( sprintf( _n( '%s year', '%s years', $experience, 'gravitas-coaching' ), number_format_i18n( $experience ) ) ); ?></span><?php endif; ?>
		</div>
		<?php if ( $rate ) : ?><div class="trainer-card__rate"><span><?php esc_html_e( 'From', 'gravitas-coaching' ); ?></span><strong><?php echo esc_html( ( is_numeric( $rate ) ? '$' . number_format_i18n( (float) $rate ) : $rate ) . ' / ' . __( 'session', 'gravitas-coaching' ) ); ?></strong></div><?php endif; ?>
	</div>
</article>
