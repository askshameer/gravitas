<?php
/**
 * Plugin Name: Gravitas Core
 * Plugin URI: https://example.com/gravitas-coaching
 * Description: Theme dashboard, visual controls, integrations, and builder widgets for Gravitas Coaching.
 * Version: 1.7.4
 * Author: Gravitas Coaching
 * Text Domain: gravitas-core
 * Requires at least: 6.2
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRAVITAS_CORE_VERSION', '1.7.4' );
define( 'GRAVITAS_CORE_FILE', __FILE__ );
define( 'GRAVITAS_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRAVITAS_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once GRAVITAS_CORE_PATH . 'includes/class-gravitas-core-settings.php';
require_once GRAVITAS_CORE_PATH . 'includes/class-gravitas-core-admin.php';
require_once GRAVITAS_CORE_PATH . 'includes/class-gravitas-core-elementor.php';

function gravitas_core() {
	static $instance = null;

	if ( null === $instance ) {
		$instance = new Gravitas_Core_Settings();
		new Gravitas_Core_Admin( $instance );
		new Gravitas_Core_Elementor();
	}

	return $instance;
}
add_action( 'plugins_loaded', 'gravitas_core' );

function gravitas_core_get_option( $key, $default = '' ) {
	$options = wp_parse_args( get_option( 'gravitas_core_options', array() ), Gravitas_Core_Settings::defaults() );
	return array_key_exists( $key, $options ) ? $options[ $key ] : $default;
}

function gravitas_core_activate() {
	$current = get_option( 'gravitas_core_options', array() );
	update_option( 'gravitas_core_options', wp_parse_args( $current, Gravitas_Core_Settings::defaults() ) );
	set_transient( 'gravitas_core_activated', 1, MINUTE_IN_SECONDS );
}
register_activation_hook( __FILE__, 'gravitas_core_activate' );
