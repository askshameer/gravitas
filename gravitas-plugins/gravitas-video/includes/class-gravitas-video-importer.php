<?php
/**
 * Remote import and scheduled feeds.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Importer {
	public function __construct() {
		add_action( 'admin_post_gravitas_video_import_url', array( $this, 'handle_url_import' ) );
		add_action( 'admin_post_gravitas_video_import_youtube', array( $this, 'handle_youtube_import' ) );
		add_action( 'gravitas_video_process_feeds', array( $this, 'process_feeds' ) );
	}

	public function handle_url_import() {
		check_admin_referer( 'gravitas_video_import_url' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You cannot import videos.', 'gravitas-video' ) );
		}
		$url = esc_url_raw( wp_unslash( $_POST['video_url'] ?? '' ) );
		$result = $this->import_url( $url, array( 'status' => sanitize_key( $_POST['post_status'] ?? 'draft' ) ) );
		$this->redirect_result( is_wp_error( $result ) ? $result : 1 );
	}

	public function import_url( $url, $args = array() ) {
		if ( ! wp_http_validate_url( $url ) ) {
			return new WP_Error( 'invalid_url', __( 'Enter a valid video URL.', 'gravitas-video' ) );
		}
		$existing = get_posts( array( 'post_type' => 'gravitas_video', 'post_status' => 'any', 'meta_key' => '_gravitas_video_url', 'meta_value' => $url, 'fields' => 'ids', 'posts_per_page' => 1 ) );
		if ( $existing ) {
			return new WP_Error( 'duplicate', __( 'This video URL has already been imported.', 'gravitas-video' ) );
		}
		$data = _wp_oembed_get_object()->get_data( $url );
		$title = $data && ! empty( $data->title ) ? $data->title : wp_parse_url( $url, PHP_URL_HOST );
		$post_id = wp_insert_post( array( 'post_type' => 'gravitas_video', 'post_status' => $args['status'] ?? 'draft', 'post_title' => sanitize_text_field( $title ), 'post_excerpt' => $data && ! empty( $data->author_name ) ? sprintf( __( 'Video by %s', 'gravitas-video' ), sanitize_text_field( $data->author_name ) ) : '', 'post_author' => get_current_user_id() ) );
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}
		update_post_meta( $post_id, '_gravitas_source_type', 'embed' );
		update_post_meta( $post_id, '_gravitas_video_url', $url );
		update_post_meta( $post_id, '_gravitas_access_level', 'public' );
		if ( ! empty( $args['channel_id'] ) ) {
			update_post_meta( $post_id, '_gravitas_channel_id', absint( $args['channel_id'] ) );
		}
		if ( $data && ! empty( $data->thumbnail_url ) ) {
			$this->sideload_thumbnail( $post_id, $data->thumbnail_url );
		}
		return $post_id;
	}

	public function handle_youtube_import() {
		check_admin_referer( 'gravitas_video_import_youtube' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You cannot import videos.', 'gravitas-video' ) );
		}
		$query = sanitize_text_field( wp_unslash( $_POST['youtube_query'] ?? '' ) );
		$max   = min( 50, max( 1, absint( $_POST['youtube_count'] ?? 10 ) ) );
		$result = $this->import_youtube( $query, $max, absint( $_POST['channel_id'] ?? 0 ) );
		$this->redirect_result( $result );
	}

	public function import_youtube( $query, $max = 10, $channel_id = 0 ) {
		$key = gravitas_video_get_option( 'youtube_api_key' );
		if ( ! $key ) {
			return new WP_Error( 'missing_key', __( 'Add a YouTube Data API key in Video Settings first.', 'gravitas-video' ) );
		}
		$params = array( 'part' => 'snippet', 'type' => 'video', 'maxResults' => $max, 'key' => $key );
		if ( preg_match( '/^UC[\w-]+$/', $query ) ) {
			$params['channelId'] = $query;
		} else {
			$params['q'] = $query;
		}
		$response = wp_remote_get( add_query_arg( $params, 'https://www.googleapis.com/youtube/v3/search' ), array( 'timeout' => 25 ) );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['items'] ) ) {
			return new WP_Error( 'no_results', $body['error']['message'] ?? __( 'No YouTube videos were returned.', 'gravitas-video' ) );
		}
		$imported = 0;
		foreach ( $body['items'] as $item ) {
			if ( empty( $item['id']['videoId'] ) ) {
				continue;
			}
			$result = $this->import_url( 'https://www.youtube.com/watch?v=' . rawurlencode( $item['id']['videoId'] ), array( 'status' => 'draft', 'channel_id' => $channel_id ) );
			if ( ! is_wp_error( $result ) ) {
				wp_update_post( array( 'ID' => $result, 'post_title' => sanitize_text_field( $item['snippet']['title'] ?? '' ), 'post_excerpt' => sanitize_textarea_field( $item['snippet']['description'] ?? '' ) ) );
				$imported++;
			}
		}
		return $imported;
	}

	public function process_feeds() {
		$feeds = get_posts( array( 'post_type' => 'gravitas_feed', 'post_status' => 'publish', 'posts_per_page' => 20 ) );
		foreach ( $feeds as $feed ) {
			$interval = absint( get_post_meta( $feed->ID, '_gravitas_feed_interval', true ) ) ?: DAY_IN_SECONDS;
			$last_run = absint( get_post_meta( $feed->ID, '_gravitas_feed_last_run', true ) );
			if ( time() - $last_run < $interval ) {
				continue;
			}
			$this->import_youtube( get_post_meta( $feed->ID, '_gravitas_feed_query', true ), absint( get_post_meta( $feed->ID, '_gravitas_feed_count', true ) ) ?: 10, absint( get_post_meta( $feed->ID, '_gravitas_feed_channel_id', true ) ) );
			update_post_meta( $feed->ID, '_gravitas_feed_last_run', time() );
		}
	}

	private function sideload_thumbnail( $post_id, $url ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attachment_id = media_sideload_image( esc_url_raw( $url ), $post_id, get_the_title( $post_id ), 'id' );
		if ( ! is_wp_error( $attachment_id ) ) {
			set_post_thumbnail( $post_id, $attachment_id );
		}
	}

	private function redirect_result( $result ) {
		$url = admin_url( 'edit.php?post_type=gravitas_video&page=gravitas-video-import' );
		if ( is_wp_error( $result ) ) {
			$url = add_query_arg( array( 'imported' => 0, 'message' => rawurlencode( $result->get_error_message() ) ), $url );
		} else {
			$url = add_query_arg( array( 'imported' => is_numeric( $result ) ? $result : 1 ), $url );
		}
		wp_safe_redirect( $url );
		exit;
	}
}
