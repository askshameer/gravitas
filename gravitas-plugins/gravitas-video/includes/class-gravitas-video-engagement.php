<?php
/**
 * Views, reactions, reports, subscriptions, and user library.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Engagement {
	public function __construct() {
		foreach ( array( 'view', 'react', 'watch_later', 'report', 'subscribe', 'history' ) as $action ) {
			add_action( 'wp_ajax_gravitas_video_' . $action, array( $this, $action ) );
			add_action( 'wp_ajax_nopriv_gravitas_video_' . $action, array( $this, $action ) );
		}
	}

	private function verify( $login = false ) {
		check_ajax_referer( 'gravitas_video_action', 'nonce' );
		if ( $login && ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Please sign in first.', 'gravitas-video' ) ), 401 );
		}
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( $post_id && 'gravitas_video' !== get_post_type( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid video.', 'gravitas-video' ) ), 400 );
		}
		return $post_id;
	}

	public function view() {
		$post_id = $this->verify();
		$count   = absint( get_post_meta( $post_id, '_gravitas_views', true ) ) + 1;
		update_post_meta( $post_id, '_gravitas_views', $count );
		if ( is_user_logged_in() ) {
			$this->update_list( get_current_user_id(), '_gravitas_video_history', $post_id, true );
		}
		wp_send_json_success( array( 'count' => $count ) );
	}

	public function react() {
		$post_id = $this->verify( true );
		$type    = isset( $_POST['reaction'] ) && 'dislike' === $_POST['reaction'] ? 'dislike' : 'like';
		$user_id = get_current_user_id();
		$state   = (array) get_user_meta( $user_id, '_gravitas_video_reactions', true );
		$old     = isset( $state[ $post_id ] ) ? $state[ $post_id ] : '';
		if ( $old === $type ) {
			$count = max( 0, absint( get_post_meta( $post_id, '_gravitas_' . $type . 's', true ) ) - 1 );
			update_post_meta( $post_id, '_gravitas_' . $type . 's', $count );
			unset( $state[ $post_id ] );
		} else {
			if ( $old ) {
				update_post_meta( $post_id, '_gravitas_' . $old . 's', max( 0, absint( get_post_meta( $post_id, '_gravitas_' . $old . 's', true ) ) - 1 ) );
			}
			$count = absint( get_post_meta( $post_id, '_gravitas_' . $type . 's', true ) ) + 1;
			update_post_meta( $post_id, '_gravitas_' . $type . 's', $count );
			$state[ $post_id ] = $type;
		}
		update_user_meta( $user_id, '_gravitas_video_reactions', $state );
		wp_send_json_success( array( 'active' => isset( $state[ $post_id ] ) ? $state[ $post_id ] : '', 'likes' => absint( get_post_meta( $post_id, '_gravitas_likes', true ) ), 'dislikes' => absint( get_post_meta( $post_id, '_gravitas_dislikes', true ) ) ) );
	}

	public function watch_later() {
		$post_id = $this->verify( true );
		$added   = $this->toggle_list( get_current_user_id(), '_gravitas_watch_later', $post_id );
		wp_send_json_success( array( 'added' => $added ) );
	}

	public function history() {
		$post_id = $this->verify( true );
		$this->update_list( get_current_user_id(), '_gravitas_video_history', $post_id, true );
		wp_send_json_success();
	}

	public function subscribe() {
		$this->verify( true );
		$channel_id = isset( $_POST['channel_id'] ) ? absint( $_POST['channel_id'] ) : 0;
		if ( 'gravitas_channel' !== get_post_type( $channel_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid channel.', 'gravitas-video' ) ), 400 );
		}
		$added = $this->toggle_list( get_current_user_id(), '_gravitas_channel_subscriptions', $channel_id );
		wp_send_json_success( array( 'subscribed' => $added ) );
	}

	public function report() {
		$post_id = $this->verify();
		$reason  = isset( $_POST['reason'] ) ? sanitize_textarea_field( wp_unslash( $_POST['reason'] ) ) : __( 'Viewer report', 'gravitas-video' );
		$report_id = wp_insert_post( array( 'post_type' => 'gravitas_report', 'post_status' => 'publish', 'post_title' => sprintf( __( 'Report: %s', 'gravitas-video' ), get_the_title( $post_id ) ), 'post_content' => $reason, 'post_author' => get_current_user_id() ) );
		if ( $report_id && ! is_wp_error( $report_id ) ) {
			update_post_meta( $report_id, '_gravitas_report_video_id', $post_id );
			update_post_meta( $report_id, '_gravitas_report_ip', sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
		}
		wp_send_json_success();
	}

	private function toggle_list( $user_id, $key, $post_id ) {
		$list = array_map( 'absint', (array) get_user_meta( $user_id, $key, true ) );
		if ( in_array( $post_id, $list, true ) ) {
			$list = array_values( array_diff( $list, array( $post_id ) ) );
			$added = false;
		} else {
			array_unshift( $list, $post_id );
			$list = array_slice( array_unique( $list ), 0, 100 );
			$added = true;
		}
		update_user_meta( $user_id, $key, $list );
		return $added;
	}

	private function update_list( $user_id, $key, $post_id, $prepend = false ) {
		$list = array_values( array_diff( array_map( 'absint', (array) get_user_meta( $user_id, $key, true ) ), array( $post_id ) ) );
		$prepend ? array_unshift( $list, $post_id ) : $list[] = $post_id;
		update_user_meta( $user_id, $key, array_slice( $list, 0, 100 ) );
	}
}
