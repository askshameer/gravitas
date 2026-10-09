<?php
/**
 * Site footer.
 *
 * @package Elevora
 */
?>
	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) : ?>
	<footer class="site-footer" id="contact">
		<div class="container footer-cta">
			<div>
				<span class="eyebrow eyebrow--light"><?php esc_html_e( 'Strengthen your presence', 'gravitas-coaching' ); ?></span>
				<h2><?php esc_html_e( 'Let your skills, style, and confidence tell one clear story.', 'gravitas-coaching' ); ?></h2>
			</div>
			<a class="button button--light" href="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>"><?php esc_html_e( 'Meet a consultant', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></a>
		</div>
		<div class="container footer-grid">
			<div class="footer-brand">
				<?php elevora_brand( 'footer' ); ?>
				<p><?php echo esc_html( elevora_get_setting( 'footer_description', 'elevora_footer_text', __( 'Soft skills coaching and image consulting for professionals who want their presence to match their potential.', 'gravitas-coaching' ) ) ); ?></p>
				<div class="footer-contact">
					<a href="mailto:<?php echo esc_attr( antispambot( elevora_get_setting( 'email', 'elevora_email', 'hello@example.com' ) ) ); ?>"><?php echo elevora_icon( 'mail' ); // phpcs:ignore ?><?php echo esc_html( antispambot( elevora_get_setting( 'email', 'elevora_email', 'hello@example.com' ) ) ); ?></a>
					<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', elevora_get_setting( 'phone', 'elevora_phone', '+1 (800) 555-0144' ) ) ); ?>"><?php echo elevora_icon( 'phone' ); // phpcs:ignore ?><?php echo esc_html( elevora_get_setting( 'phone', 'elevora_phone', '+1 (800) 555-0144' ) ); ?></a>
				</div>
				<?php if ( function_exists( 'gravitas_core_get_option' ) ) : ?>
					<div class="gravitas-footer-social">
						<?php foreach ( array( 'instagram_url' => 'Instagram', 'linkedin_url' => 'LinkedIn', 'youtube_url' => 'YouTube', 'facebook_url' => 'Facebook' ) as $social_key => $social_label ) : $social_url = gravitas_core_get_option( $social_key ); if ( $social_url ) : ?><a href="<?php echo esc_url( $social_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $social_label ); ?></a><?php endif; endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div><?php dynamic_sidebar( 'footer-one' ); ?></div>
			<div><?php dynamic_sidebar( 'footer-two' ); ?></div>
			<div><?php dynamic_sidebar( 'footer-three' ); ?></div>
		</div>
		<div class="container footer-bottom">
			<p><?php $copyright = function_exists( 'gravitas_core_get_option' ) ? gravitas_core_get_option( 'copyright_text' ) : ''; echo $copyright ? esc_html( $copyright ) : '&copy; ' . esc_html( gmdate( 'Y' ) ) . ' ' . esc_html( get_bloginfo( 'name' ) ) . '. ' . esc_html__( 'All rights reserved.', 'gravitas-coaching' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'footer-menu', 'depth' => 1 ) ); ?>
			<?php endif; ?>
		</div>
	</footer>
	<?php endif; ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
