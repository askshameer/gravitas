<?php
/**
 * Site header.
 *
 * @package Elevora
 */
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'gravitas-coaching' ); ?></a>
<div class="site-shell">
	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
	<?php if ( has_nav_menu( 'utility' ) ) : ?><div class="announcement-bar">
		<div class="container announcement-inner">
			<p><?php esc_html_e( 'Your next chapter deserves focused support.', 'gravitas-coaching' ); ?></p>
			<?php if ( has_nav_menu( 'utility' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'utility', 'container' => false, 'menu_class' => 'utility-menu', 'depth' => 1 ) ); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<header class="site-header" data-site-header>
		<div class="container header-inner">
			<div class="brand-wrap"><?php elevora_brand(); ?></div>
			<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'gravitas-coaching' ); ?>" data-primary-nav>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'elevora_fallback_menu', 'depth' => 3 ) ); ?>
			</nav>
			<div class="header-tools">
				<button class="icon-button" type="button" data-search-open aria-label="<?php esc_attr_e( 'Open search', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e( 'Toggle dark mode', 'gravitas-coaching' ); ?>"><span class="theme-icon-moon"><?php echo elevora_icon( 'moon' ); // phpcs:ignore ?></span><span class="theme-icon-sun"><?php echo elevora_icon( 'sun' ); // phpcs:ignore ?></span></button>
				<?php
				$header_cta_text = elevora_get_setting( 'header_cta_text', 'elevora_header_cta', __( 'Book a Consultation', 'gravitas-coaching' ) );
				$header_cta_url  = elevora_get_setting( 'header_cta_url', 'elevora_header_url', '#contact' );
				echo elevora_render_action_button( $header_cta_url, $header_cta_text, 'button button--flat button--small header-cta', '', 'header-cta-modal' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<button class="menu-toggle icon-button" type="button" data-menu-toggle aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle menu', 'gravitas-coaching' ); ?>"><span class="menu-open-icon"><?php echo elevora_icon( 'menu' ); // phpcs:ignore ?></span><span class="menu-close-icon"><?php echo elevora_icon( 'close' ); // phpcs:ignore ?></span></button>
			</div>
		</div>
	</header>

	<div class="search-panel" data-search-panel hidden>
		<div class="search-panel__backdrop" data-search-close></div>
		<div class="search-panel__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site search', 'gravitas-coaching' ); ?>">
			<button class="icon-button search-panel__close" type="button" data-search-close aria-label="<?php esc_attr_e( 'Close search', 'gravitas-coaching' ); ?>"><?php echo elevora_icon( 'close' ); // phpcs:ignore ?></button>
			<span class="eyebrow"><?php esc_html_e( 'Search Gravitas Coaching', 'gravitas-coaching' ); ?></span>
			<h2><?php esc_html_e( 'What would you like to explore?', 'gravitas-coaching' ); ?></h2>
			<?php get_search_form(); ?>
			<div class="search-panel__links">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>"><?php esc_html_e( 'Browse trainers', 'gravitas-coaching' ); ?></a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'coaching_service' ) ); ?>"><?php esc_html_e( 'View services', 'gravitas-coaching' ); ?></a>
			</div>
		</div>
	</div>
	<?php endif; ?>
