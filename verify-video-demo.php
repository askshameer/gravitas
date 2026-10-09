<?php
define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );
$test_posts = array();
$test_meta = array();
$test_options = array();

function __( $text ) { return $text; }
function esc_html__( $text ) { return $text; }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function wp_parse_args( $args, $defaults ) { return array_merge( $defaults, $args ); }
function get_page_by_path() { return null; }
function wp_slash( $value ) { return $value; }
function wp_insert_post( $postarr ) { global $test_posts; $id = count( $test_posts ) + 1; $postarr['ID'] = $id; $test_posts[ $id ] = $postarr; return $id; }
function is_wp_error() { return false; }
function update_post_meta( $id, $key, $value ) { global $test_meta; $test_meta[ $id ][ $key ] = $value; }
function set_post_thumbnail( $id, $thumbnail ) { global $test_meta; $test_meta[ $id ]['_thumbnail_id'] = $thumbnail; }
function post_type_exists( $type ) { return 'gravitas_video' === $type; }
function term_exists() { return true; }
function wp_insert_term() { return array( 'term_id' => 1 ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_set_object_terms() { return true; }
function current_time() { return '2026-10-05 12:00:00'; }
function update_option( $key, $value ) { global $test_options; $test_options[ $key ] = $value; }
function flush_rewrite_rules() {}
function add_action() {}
function check_admin_referer() {}
function current_user_can() { return true; }
function wp_die( $message ) { throw new RuntimeException( $message ); }
function add_query_arg() { return ''; }
function admin_url() { return ''; }
function wp_safe_redirect() {}

require __DIR__ . '/gravitas-plugins/gravitas-video/includes/class-gravitas-video-demo.php';

$images = array(
	'hero' => 1,
	'boardroom' => 2,
	'podcast_inclusive' => 3,
	'image_consultant' => 4,
	'executive_woman' => 5,
	'podcast_night' => 6,
	'executive_vertical' => 7,
);
$result = gravitas_video_import_demo_content( $images );
$counts = array_count_values( array_column( $test_posts, 'post_type' ) );
$expected = array( 'gravitas_video' => 6, 'gravitas_channel' => 1, 'gravitas_playlist' => 1, 'gravitas_series' => 1, 'page' => 4 );
foreach ( $expected as $type => $count ) {
	if ( ( $counts[ $type ] ?? 0 ) !== $count ) {
		throw new RuntimeException( "Expected {$count} {$type} records." );
	}
}
if ( count( $result['video_ids'] ) !== 6 || empty( $test_options['gravitas_video_demo_imported_at'] ) ) {
	throw new RuntimeException( 'The video demo did not finish successfully.' );
}
foreach ( $result['video_ids'] as $video_id ) {
	if ( empty( $test_meta[ $video_id ]['_gravitas_video_url'] ) || empty( $test_meta[ $video_id ]['_thumbnail_id'] ) ) {
		throw new RuntimeException( 'A video is missing its source or thumbnail.' );
	}
}
echo "Video demo import passed: 6 lessons, 3 collections, and 4 utility pages.\n";
