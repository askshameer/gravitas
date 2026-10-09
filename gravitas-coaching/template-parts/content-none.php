<?php
/**
 * Empty state.
 *
 * @package Elevora
 */
$message = isset( $args['message'] ) ? $args['message'] : __( 'Nothing was found here. Try a different search.', 'gravitas-coaching' );
?>
<div class="empty-state">
	<div class="empty-state__icon"><?php echo elevora_icon( 'compass' ); // phpcs:ignore ?></div>
	<h2><?php esc_html_e( 'Let’s try another direction.', 'gravitas-coaching' ); ?></h2>
	<p><?php echo esc_html( $message ); ?></p>
	<a class="button button--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'gravitas-coaching' ); ?></a>
</div>

