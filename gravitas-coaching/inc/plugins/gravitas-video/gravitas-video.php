<?php
/**
 * Plugin Name: Gravitas Video
 * Plugin URI: https://example.com/gravitas-coaching
 * Description: Video publishing, playlists, channels, series, engagement, submissions, ads, imports, and membership access for Gravitas Coaching.
 * Version: 1.3.0
 * Author: Gravitas Coaching
 * Text Domain: gravitas-video
 * Requires at least: 6.2
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GRAVITAS_VIDEO_VERSION', '1.3.0' );
define( 'GRAVITAS_VIDEO_FILE', __FILE__ );
define( 'GRAVITAS_VIDEO_PATH', plugin_dir_path( __FILE__ ) );
define( 'GRAVITAS_VIDEO_URL', plugin_dir_url( __FILE__ ) );

require_once GRAVITAS_VIDEO_PATH . 'includes/functions.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-content.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-meta.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-player.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-engagement.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-submissions.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-importer.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-admin.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-demo.php';
require_once GRAVITAS_VIDEO_PATH . 'includes/class-gravitas-video-elementor.php';

function gravitas_video() {
	static $instances = array();
	if ( empty( $instances ) ) {
		$instances['content']     = new Gravitas_Video_Content();
		$instances['meta']        = new Gravitas_Video_Meta();
		$instances['player']      = new Gravitas_Video_Player();
		$instances['engagement']  = new Gravitas_Video_Engagement();
		$instances['submissions'] = new Gravitas_Video_Submissions();
		$instances['importer']    = new Gravitas_Video_Importer();
		$instances['admin']       = new Gravitas_Video_Admin();
		$instances['demo']        = new Gravitas_Video_Demo();
		$instances['elementor']   = new Gravitas_Video_Elementor();
	}
	return $instances;
}
add_action( 'plugins_loaded', 'gravitas_video' );

function gravitas_video_activate() {
	$content = new Gravitas_Video_Content();
	$content->register_content();
	Gravitas_Video_Admin::add_capabilities();
	if ( ! wp_next_scheduled( 'gravitas_video_process_feeds' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'gravitas_video_process_feeds' );
	}
	flush_rewrite_rules();
	set_transient( 'gravitas_video_activated', 1, MINUTE_IN_SECONDS );
}
register_activation_hook( __FILE__, 'gravitas_video_activate' );

function gravitas_video_deactivate() {
	$timestamp = wp_next_scheduled( 'gravitas_video_process_feeds' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'gravitas_video_process_feeds' );
	}
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gravitas_video_deactivate' );
