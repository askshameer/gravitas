<?php
/**
 * Theme setup and assets.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_setup() {
	load_theme_textdomain( 'gravitas-coaching', ELEVORA_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'tutor' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'gravitas-coaching' ),
			'utility' => __( 'Utility navigation', 'gravitas-coaching' ),
			'footer'  => __( 'Footer navigation', 'gravitas-coaching' ),
		)
	);

	add_image_size( 'elevora-card', 720, 520, true );
	add_image_size( 'elevora-trainer', 800, 1000, true );
	add_image_size( 'elevora-wide', 1440, 810, true );
}
add_action( 'after_setup_theme', 'elevora_setup' );

function elevora_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'elevora_content_width', 760 );
}
add_action( 'after_setup_theme', 'elevora_content_width', 0 );

function elevora_widgets_init() {
	$sidebars = array(
		'sidebar-main'    => __( 'Main Sidebar', 'gravitas-coaching' ),
		'sidebar-trainer' => __( 'Trainer Sidebar', 'gravitas-coaching' ),
		'sidebar-service' => __( 'Service Sidebar', 'gravitas-coaching' ),
		'footer-one'      => __( 'Footer One', 'gravitas-coaching' ),
		'footer-two'      => __( 'Footer Two', 'gravitas-coaching' ),
		'footer-three'    => __( 'Footer Three', 'gravitas-coaching' ),
	);

	foreach ( $sidebars as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}
add_action( 'widgets_init', 'elevora_widgets_init' );

function elevora_enqueue_assets() {
	$style_version       = file_exists( ELEVORA_DIR . '/style.css' ) ? (string) filemtime( ELEVORA_DIR . '/style.css' ) : ELEVORA_VERSION;
	$main_version        = file_exists( ELEVORA_DIR . '/assets/css/main.css' ) ? (string) filemtime( ELEVORA_DIR . '/assets/css/main.css' ) : ELEVORA_VERSION;
	$content_hub_version = file_exists( ELEVORA_DIR . '/assets/css/content-hub.css' ) ? (string) filemtime( ELEVORA_DIR . '/assets/css/content-hub.css' ) : ELEVORA_VERSION;
	$script_version      = file_exists( ELEVORA_DIR . '/assets/js/theme.js' ) ? (string) filemtime( ELEVORA_DIR . '/assets/js/theme.js' ) : ELEVORA_VERSION;
	wp_enqueue_style( 'elevora-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500..800;1,500..800&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap', array(), null );
	wp_enqueue_style( 'elevora-style', get_stylesheet_uri(), array( 'elevora-fonts' ), $style_version );
	wp_enqueue_style( 'elevora-main', ELEVORA_URI . '/assets/css/main.css', array( 'elevora-style' ), $main_version );
	wp_enqueue_style( 'elevora-content-hub', ELEVORA_URI . '/assets/css/content-hub.css', array( 'elevora-main' ), $content_hub_version );
	if ( function_exists( 'buddypress' ) || function_exists( 'pmpro_hasMembershipLevel' ) || defined( 'IHC_PATH' ) || function_exists( 'wc_memberships' ) ) {
		wp_enqueue_style( 'elevora-community', ELEVORA_URI . '/assets/css/community.css', array( 'elevora-main' ), ELEVORA_VERSION );
	}

	wp_enqueue_script( 'elevora-theme', ELEVORA_URI . '/assets/js/theme.js', array(), $script_version, true );
	wp_localize_script(
		'elevora-theme',
		'elevoraTheme',
		array(
			'loadMore' => __( 'Load more', 'gravitas-coaching' ),
			'loading'  => __( 'Loading posts…', 'gravitas-coaching' ),
			'loaded'   => __( 'More posts loaded.', 'gravitas-coaching' ),
			'allDone'  => __( 'All posts loaded.', 'gravitas-coaching' ),
			'failed'   => __( 'Unable to load posts. Use the page links below.', 'gravitas-coaching' ),
		)
	);

	if ( is_post_type_archive( 'trainer' ) || is_tax( array( 'trainer_specialization', 'trainer_location', 'coaching_format', 'trainer_language' ) ) ) {
		wp_enqueue_script( 'elevora-directory', ELEVORA_URI . '/assets/js/directory.js', array(), ELEVORA_VERSION, true );
		wp_localize_script(
			'elevora-directory',
			'elevoraDirectory',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'elevora_directory' ),
				'loading' => __( 'Finding the right coaches…', 'gravitas-coaching' ),
				'error'   => __( 'We could not refresh the directory. Please try again.', 'gravitas-coaching' ),
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'elevora_enqueue_assets' );

function elevora_body_classes( $classes ) {
	if ( is_singular() && ! is_front_page() ) {
		$classes[] = 'elevora-singular';
	}
	if ( is_active_sidebar( 'sidebar-main' ) && ! is_front_page() ) {
		$classes[] = 'has-sidebar';
	}
	if ( class_exists( 'WooCommerce' ) ) {
		$classes[] = 'has-woocommerce';
	}

	$site_layout = elevora_get_setting( 'site_layout', 'elevora_site_layout', 'full' );
	$classes[]   = 'layout-' . sanitize_html_class( $site_layout );

	if ( is_singular( 'post' ) ) {
		$single_sidebar = elevora_get_setting( 'single_sidebar', 'elevora_single_sidebar', 'right' );
		$classes[]      = 'gravitas-single-sidebar-' . sanitize_html_class( $single_sidebar );
		if ( 'none' === $single_sidebar ) {
			$classes[] = 'elevora-no-sidebar';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'elevora_body_classes' );

/**
 * Queue a rewrite refresh when the theme is activated.
 *
 * Core refreshes rewrite rules on the next load, but doing it again on init
 * (after our post types and taxonomies register) guarantees that blog, custom
 * post type, and taxonomy links resolve immediately after installation.
 */
function elevora_flag_rewrite_flush() {
	update_option( 'elevora_flush_rewrite_rules', 1, false );
}
add_action( 'after_switch_theme', 'elevora_flag_rewrite_flush' );

function elevora_maybe_flush_rewrite_rules() {
	global $wp_rewrite;

	if ( get_option( 'elevora_flush_rewrite_rules' ) ) {
		delete_option( 'elevora_flush_rewrite_rules' );
		if ( is_object( $wp_rewrite ) ) {
			$structure = get_option( 'permalink_structure' );
			if ( ! empty( $structure ) ) {
				$wp_rewrite->set_permalink_structure( $structure );
			}
			$wp_rewrite->flush_rules( true );
		} else {
			flush_rewrite_rules();
		}
		return;
	}

	// Self-healing check: if permalinks are enabled but post rules are missing from the rewrite array.
	$structure = get_option( 'permalink_structure' );
	if ( ! empty( $structure ) && is_object( $wp_rewrite ) ) {
		$rules = get_option( 'rewrite_rules' );
		if ( empty( $rules ) || ! is_array( $rules ) || ! preg_grep( '/name=/', array_values( $rules ) ) ) {
			$wp_rewrite->set_permalink_structure( $structure );
			$wp_rewrite->flush_rules( true );
		}
	}
}
add_action( 'init', 'elevora_maybe_flush_rewrite_rules', 99 );
