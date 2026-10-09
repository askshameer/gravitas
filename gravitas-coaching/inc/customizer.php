<?php
/**
 * Theme Customizer settings.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'elevora_home', array( 'title' => __( 'Gravitas Homepage', 'gravitas-coaching' ), 'priority' => 30 ) );
	$settings = array(
		'elevora_hero_eyebrow'  => array( __( 'Hero eyebrow', 'gravitas-coaching' ), __( 'Soft skills. Stronger presence.', 'gravitas-coaching' ), 'sanitize_text_field' ),
		'elevora_hero_title'    => array( __( 'Hero title', 'gravitas-coaching' ), __( 'Unlock Your Next Level', 'gravitas-coaching' ), 'sanitize_text_field' ),
		'elevora_hero_highlight' => array( __( 'Hero gold headline', 'gravitas-coaching' ), __( 'of Growth and Leadership', 'gravitas-coaching' ), 'sanitize_text_field' ),
		'elevora_hero_text'     => array( __( 'Hero description', 'gravitas-coaching' ), __( 'Build your image, presence, and essential soft skills for a more confident professional future.', 'gravitas-coaching' ), 'sanitize_textarea_field' ),
		'elevora_hero_button'   => array( __( 'Primary button text', 'gravitas-coaching' ), __( 'Find your consultant', 'gravitas-coaching' ), 'sanitize_text_field' ),
		'elevora_hero_url'      => array( __( 'Primary button URL', 'gravitas-coaching' ), '', 'esc_url_raw' ),
		'elevora_hero_alt_text' => array( __( 'Secondary button text', 'gravitas-coaching' ), __( 'Explore programs', 'gravitas-coaching' ), 'sanitize_text_field' ),
		'elevora_hero_alt_url'  => array( __( 'Secondary button URL', 'gravitas-coaching' ), '', 'esc_url_raw' ),
	);
	foreach ( $settings as $id => $setting ) {
		$wp_customize->add_setting( $id, array( 'default' => $setting[1], 'sanitize_callback' => $setting[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $setting[0], 'section' => 'elevora_home', 'type' => false !== strpos( $id, '_text' ) ? 'textarea' : 'text' ) );
	}
	$wp_customize->add_setting( 'elevora_hero_image', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'elevora_hero_image', array( 'label' => __( 'Hero image', 'gravitas-coaching' ), 'section' => 'elevora_home', 'mime_type' => 'image' ) ) );

	$wp_customize->add_section( 'elevora_layout_options', array( 'title' => __( 'Gravitas Layout & Framing', 'gravitas-coaching' ), 'priority' => 29 ) );
	$wp_customize->add_setting( 'elevora_site_layout', array( 'default' => 'full', 'sanitize_callback' => 'elevora_sanitize_site_layout' ) );
	$wp_customize->add_control( 'elevora_site_layout', array( 'label' => __( 'Site Layout Mode', 'gravitas-coaching' ), 'description' => __( 'Choose between edge-to-edge Full-width or a framed Boxed layout.', 'gravitas-coaching' ), 'section' => 'elevora_layout_options', 'type' => 'select', 'choices' => array( 'full' => __( 'Full-width (Default)', 'gravitas-coaching' ), 'boxed' => __( 'Boxed', 'gravitas-coaching' ) ) ) );

	$wp_customize->add_section( 'elevora_blog_options', array( 'title' => __( 'Gravitas Blog', 'gravitas-coaching' ), 'priority' => 31 ) );
	$wp_customize->add_setting( 'elevora_blog_layout', array( 'default' => 'editorial', 'sanitize_callback' => 'elevora_sanitize_blog_layout' ) );
	$wp_customize->add_control( 'elevora_blog_layout', array( 'label' => __( 'Blog layout', 'gravitas-coaching' ), 'section' => 'elevora_blog_options', 'type' => 'select', 'choices' => elevora_get_blog_layouts() ) );
	$wp_customize->add_setting( 'elevora_blog_sidebar', array( 'default' => true, 'sanitize_callback' => 'elevora_sanitize_checkbox' ) );
	$wp_customize->add_control( 'elevora_blog_sidebar', array( 'label' => __( 'Show blog archive sidebar', 'gravitas-coaching' ), 'section' => 'elevora_blog_options', 'type' => 'checkbox' ) );
	$wp_customize->add_setting( 'elevora_single_sidebar', array( 'default' => 'right', 'sanitize_callback' => 'elevora_sanitize_single_sidebar' ) );
	$wp_customize->add_control( 'elevora_single_sidebar', array( 'label' => __( 'Single Post Sidebar', 'gravitas-coaching' ), 'description' => __( 'Position of the sidebar on single post articles.', 'gravitas-coaching' ), 'section' => 'elevora_blog_options', 'type' => 'select', 'choices' => array( 'right' => __( 'Right Sidebar (Default)', 'gravitas-coaching' ), 'left' => __( 'Left Sidebar', 'gravitas-coaching' ), 'none' => __( 'No Sidebar (Centered)', 'gravitas-coaching' ) ) ) );
	$wp_customize->add_setting( 'elevora_blog_eyebrow', array( 'default' => __( 'Gravitas journal', 'gravitas-coaching' ), 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'elevora_blog_eyebrow', array( 'label' => __( 'Blog eyebrow', 'gravitas-coaching' ), 'section' => 'elevora_blog_options', 'type' => 'text' ) );
	$wp_customize->add_setting( 'elevora_blog_description', array( 'default' => __( 'Practical guidance for stronger soft skills, authentic personal style, and confident professional presence.', 'gravitas-coaching' ), 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'elevora_blog_description', array( 'label' => __( 'Blog description', 'gravitas-coaching' ), 'section' => 'elevora_blog_options', 'type' => 'textarea' ) );

	$wp_customize->add_section( 'elevora_brand_options', array( 'title' => __( 'Gravitas Brand & Contact', 'gravitas-coaching' ), 'priority' => 31 ) );
	$brand_settings = array(
		'elevora_accent'      => array( __( 'Accent color', 'gravitas-coaching' ), '#c6a454', 'sanitize_hex_color', 'color' ),
		'elevora_phone'       => array( __( 'Contact phone', 'gravitas-coaching' ), '+1 (800) 555-0144', 'sanitize_text_field', 'text' ),
		'elevora_email'       => array( __( 'Contact email', 'gravitas-coaching' ), 'hello@example.com', 'sanitize_email', 'email' ),
		'elevora_header_cta'  => array( __( 'Header CTA text', 'gravitas-coaching' ), __( 'Book a Consultation', 'gravitas-coaching' ), 'sanitize_text_field', 'text' ),
		'elevora_header_url'  => array( __( 'Header CTA URL or Shortcode', 'gravitas-coaching' ), '#contact', 'elevora_sanitize_url_or_shortcode', 'text' ),
		'elevora_footer_text' => array( __( 'Footer description', 'gravitas-coaching' ), __( 'Soft skills coaching and image consulting for professionals who want their presence to match their potential.', 'gravitas-coaching' ), 'sanitize_textarea_field', 'textarea' ),
	);
	foreach ( $brand_settings as $id => $setting ) {
		$wp_customize->add_setting( $id, array( 'default' => $setting[1], 'sanitize_callback' => $setting[2] ) );
		$wp_customize->add_control( $id, array( 'label' => $setting[0], 'section' => 'elevora_brand_options', 'type' => $setting[3] ) );
	}
	$wp_customize->add_setting( 'elevora_light_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'elevora_light_logo', array( 'label' => __( 'Light-mode header logo', 'gravitas-coaching' ), 'section' => 'elevora_brand_options', 'mime_type' => 'image' ) ) );
	$wp_customize->add_setting( 'elevora_dark_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'elevora_dark_logo', array( 'label' => __( 'Dark-mode header logo', 'gravitas-coaching' ), 'section' => 'elevora_brand_options', 'mime_type' => 'image' ) ) );
}
add_action( 'customize_register', 'elevora_customize_register' );

/**
 * Sanitize a checkbox value.
 *
 * @param mixed $checked Checkbox value.
 * @return bool
 */
function elevora_sanitize_checkbox( $checked ) {
	return (bool) $checked;
}

/**
 * Sanitize site layout option (full or boxed).
 *
 * @param string $layout Input layout.
 * @return string
 */
function elevora_sanitize_site_layout( $layout ) {
	return in_array( $layout, array( 'full', 'boxed' ), true ) ? $layout : 'full';
}

/**
 * Sanitize single post sidebar position (right, left, or none).
 *
 * @param string $position Input position.
 * @return string
 */
function elevora_sanitize_single_sidebar( $position ) {
	return in_array( $position, array( 'right', 'left', 'none' ), true ) ? $position : 'right';
}

/**
 * Synchronize Customizer values back into Gravitas Core options table so both systems stay unified.
 */
function elevora_sync_customizer_to_core() {
	if ( ! function_exists( 'gravitas_core_get_option' ) ) {
		return;
	}
	$options = get_option( 'gravitas_core_options', array() );
	$sync_keys = array(
		'site_layout'    => 'elevora_site_layout',
		'single_sidebar' => 'elevora_single_sidebar',
		'blog_layout'    => 'elevora_blog_layout',
	);
	$changed = false;
	foreach ( $sync_keys as $opt_key => $mod_key ) {
		$mod_val = get_theme_mod( $mod_key, null );
		if ( null !== $mod_val && ( ! isset( $options[ $opt_key ] ) || $options[ $opt_key ] !== $mod_val ) ) {
			$options[ $opt_key ] = $mod_val;
			$changed = true;
		}
	}
	if ( $changed ) {
		update_option( 'gravitas_core_options', $options );
	}
}
add_action( 'customize_save_after', 'elevora_sync_customizer_to_core' );

function elevora_customizer_css() {
	$accent = sanitize_hex_color( elevora_get_setting( 'accent_color', 'elevora_accent', '#c6a454' ) );
	if ( ! $accent ) {
		$accent = '#c6a454';
	}
	echo '<style id="elevora-customizer-css">:root{--color-accent:' . esc_attr( $accent ) . ';}</style>';
}
add_action( 'wp_head', 'elevora_customizer_css', 20 );
