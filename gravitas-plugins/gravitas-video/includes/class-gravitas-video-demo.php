<?php
/**
 * Video demo content.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Demo {
	public function __construct() {
		add_action( 'admin_post_gravitas_video_import_demo', array( $this, 'handle' ) );
	}

	public function handle() {
		check_admin_referer( 'gravitas_video_import_demo' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You cannot import demo content.', 'gravitas-video' ) );
		}
		gravitas_video_import_demo_content();
		wp_safe_redirect( add_query_arg( array( 'post_type' => 'gravitas_video', 'page' => 'gravitas-video-demo', 'imported' => 1 ), admin_url( 'edit.php' ) ) );
		exit;
	}
}

function gravitas_video_demo_post( $type, $slug, $data, $meta = array(), $thumbnail_id = 0 ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	if ( $existing && get_post_meta( $existing->ID, '_gravitas_demo_content', true ) ) {
		return (int) $existing->ID;
	}
	$postarr  = wp_parse_args( $data, array( 'post_type' => $type, 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => ucwords( str_replace( '-', ' ', $slug ) ), 'post_content' => '' ) );
	$post_id = wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $post_id ) ) {
		return 0;
	}
	update_post_meta( $post_id, '_gravitas_demo_content', 1 );
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, '_gravitas_' . $key, $value );
	}
	if ( $thumbnail_id ) {
		set_post_thumbnail( $post_id, $thumbnail_id );
	}
	return $post_id;
}

function gravitas_video_import_demo_content( $images = array(), $trainer_ids = array() ) {
	if ( ! post_type_exists( 'gravitas_video' ) ) {
		return array();
	}
	if ( ! $images && function_exists( 'elevora_import_demo_images' ) ) {
		$images = elevora_import_demo_images();
	}
	foreach ( array( 'Communication Skills', 'Executive Presence', 'Professional Image', 'Business Etiquette', 'Career Confidence' ) as $topic ) {
		if ( ! term_exists( $topic, 'video_topic' ) ) {
			wp_insert_term( $topic, 'video_topic' );
		}
	}
	foreach ( array( 'Beginner', 'Intermediate', 'Advanced' ) as $level ) {
		if ( ! term_exists( $level, 'video_level' ) ) {
			wp_insert_term( $level, 'video_level' );
		}
	}

	$hero_image = absint( $images['hero'] ?? 0 );
	$channel_id = gravitas_video_demo_post( 'gravitas_channel', 'gravitas-learning-studio', array( 'post_title' => 'Gravitas Learning Studio', 'post_excerpt' => 'Practical communication, confidence, etiquette, and image lessons from Gravitas consultants.', 'post_content' => '<p>Short, focused lessons designed to turn professional presence into a repeatable practice.</p>' ), array(), $hero_image );
	$playlist_id = gravitas_video_demo_post( 'gravitas_playlist', 'presence-essentials', array( 'post_title' => 'Professional Presence Essentials', 'post_excerpt' => 'A practical starter playlist for communicating, dressing, and showing up with intention.', 'post_content' => '<p>Begin here to align your message, behavior, and visual identity.</p>' ), array(), $hero_image );
	$series_id = gravitas_video_demo_post( 'gravitas_series', 'confidence-in-action', array( 'post_title' => 'Confidence in Action', 'post_excerpt' => 'A six-part coaching series for credible, visible confidence.', 'post_content' => '<p>Move from self-awareness to confident application in meetings, presentations, interviews, and networking.</p>' ), array(), $hero_image );

	$video_data = array(
		'command-the-room' => array( 'Command the Room Without Raising Your Voice', 'Executive Presence', 'Intermediate', '12:40', 'Structure, pace, pauses, and posture create more authority than volume.', 'boardroom' ),
		'confident-introduction' => array( 'Craft a Confident Professional Introduction', 'Communication Skills', 'Beginner', '08:15', 'Create a concise introduction that communicates your value naturally.', 'podcast_inclusive' ),
		'visual-credibility' => array( 'The Five Elements of Visual Credibility', 'Professional Image', 'Beginner', '14:05', 'Use fit, color, proportion, grooming, and consistency with intention.', 'image_consultant' ),
		'difficult-conversations' => array( 'A Calm Framework for Difficult Conversations', 'Communication Skills', 'Advanced', '18:30', 'Prepare, listen, name the issue, and agree a useful next step.', 'executive_woman' ),
		'networking-etiquette' => array( 'Modern Networking and Business Etiquette', 'Business Etiquette', 'Beginner', '10:20', 'Navigate introductions, conversations, and follow-up with ease.', 'podcast_night' ),
		'interview-presence' => array( 'Interview Presence: Answer With Evidence', 'Career Confidence', 'Intermediate', '16:10', 'Turn experience into concise stories that make your value visible.', 'executive_vertical' ),
	);
	$video_ids = array();
	$sample_url = 'https://vimeo.com/76979871';
	$episode = 1;
	foreach ( $video_data as $slug => $video ) {
		$thumbnail = absint( $images[ $video[5] ] ?? $hero_image );
		$post_id = gravitas_video_demo_post(
			'gravitas_video',
			$slug,
			array( 'post_title' => $video[0], 'post_excerpt' => $video[4], 'post_content' => '<p>' . esc_html( $video[4] ) . '</p><h2>What you will learn</h2><p>Use the lesson, reflection prompts, and downloadable practice guide to apply this skill in your next real professional situation.</p>' ),
			array( 'source_type' => 'embed', 'video_url' => $sample_url, 'trailer_url' => $sample_url, 'duration' => $video[3], 'access_level' => 6 === $episode ? 'members' : 'public', 'channel_id' => $channel_id, 'playlist_ids' => (string) $playlist_id, 'series_id' => $series_id, 'episode_number' => $episode, 'views' => 340 + ( $episode * 173 ), 'likes' => 18 + ( $episode * 7 ), 'chapters' => "00:00|Introduction\n02:15|Core idea\n06:30|Practice example\n10:00|Reflection", 'downloads' => "Practice guide|https://example.com/practice-guide.pdf\nReflection worksheet|https://example.com/reflection.pdf", 'allow_downloads' => 1, 'featured_video' => $episode <= 3 ? 1 : 0 ),
			$thumbnail
		);
		if ( ! $post_id ) {
			continue;
		}
		wp_set_object_terms( $post_id, $video[1], 'video_topic' );
		wp_set_object_terms( $post_id, $video[2], 'video_level' );
		$video_ids[] = $post_id;
		$episode++;
	}

	$library_id = gravitas_video_demo_post( 'page', 'video-library', array( 'post_title' => 'Video Library', 'post_content' => '<!-- wp:shortcode -->[gravitas_video_grid count="12"]<!-- /wp:shortcode -->' ) );
	$watch_id = gravitas_video_demo_post( 'page', 'watch-later', array( 'post_title' => 'Watch Later', 'post_content' => '<!-- wp:shortcode -->[gravitas_watch_later]<!-- /wp:shortcode -->' ) );
	$submit_id = gravitas_video_demo_post( 'page', 'submit-a-video', array( 'post_title' => 'Submit a Video', 'post_content' => '<!-- wp:shortcode -->[gravitas_video_submit]<!-- /wp:shortcode -->' ) );
	$my_videos_id = gravitas_video_demo_post( 'page', 'my-videos', array( 'post_title' => 'My Videos', 'post_content' => '<!-- wp:shortcode -->[gravitas_my_videos]<!-- /wp:shortcode -->' ) );

	if ( 6 === count( array_filter( $video_ids ) ) ) {
		update_option( 'gravitas_video_demo_imported_at', current_time( 'mysql' ), false );
	}
	flush_rewrite_rules();
	return array( 'channel_id' => $channel_id, 'playlist_id' => $playlist_id, 'series_id' => $series_id, 'video_ids' => $video_ids, 'library_id' => $library_id, 'watch_id' => $watch_id, 'submit_id' => $submit_id, 'my_videos_id' => $my_videos_id );
}
