<?php
/**
 * Video content model.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Content {
	public function __construct() {
		add_action( 'init', array( $this, 'register_content' ) );
		add_filter( 'template_include', array( $this, 'templates' ) );
		add_action( 'pre_get_posts', array( $this, 'archive_query' ), 20 );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_filter( 'post_type_link', array( $this, 'episode_permalink' ), 10, 2 );
	}

	/**
	 * Expose the filter query vars so WordPress keeps them on the archive URL.
	 *
	 * @param string[] $vars Public query vars.
	 * @return string[]
	 */
	public function register_query_vars( $vars ) {
		foreach ( array( 'video_topic', 'video_level' ) as $var ) {
			if ( ! in_array( $var, $vars, true ) ) {
				$vars[] = $var;
			}
		}
		return $vars;
	}

	private function labels( $singular, $plural ) {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new'            => __( 'Add New', 'gravitas-video' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'gravitas-video' ), $singular ),
			'edit_item'          => sprintf( __( 'Edit %s', 'gravitas-video' ), $singular ),
			'new_item'           => sprintf( __( 'New %s', 'gravitas-video' ), $singular ),
			'view_item'          => sprintf( __( 'View %s', 'gravitas-video' ), $singular ),
			'search_items'       => sprintf( __( 'Search %s', 'gravitas-video' ), $plural ),
			'not_found'          => sprintf( __( 'No %s found.', 'gravitas-video' ), strtolower( $plural ) ),
			'all_items'          => sprintf( __( 'All %s', 'gravitas-video' ), $plural ),
			'menu_name'          => $plural,
			'featured_image'     => __( 'Cover image', 'gravitas-video' ),
			'set_featured_image' => __( 'Set cover image', 'gravitas-video' ),
		);
	}

	public function register_content() {
		register_post_type( 'gravitas_video', array(
			'labels' => $this->labels( __( 'Video', 'gravitas-video' ), __( 'Videos', 'gravitas-video' ) ), 'public' => true, 'show_in_rest' => true, 'menu_icon' => 'dashicons-format-video', 'has_archive' => 'videos', 'rewrite' => array( 'slug' => 'videos' ), 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments', 'revisions', 'custom-fields' ), 'capability_type' => array( 'gravitas_video', 'gravitas_videos' ), 'map_meta_cap' => true,
		) );
		register_post_type( 'gravitas_playlist', array(
			'labels' => $this->labels( __( 'Playlist', 'gravitas-video' ), __( 'Playlists', 'gravitas-video' ) ), 'public' => true, 'show_in_rest' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'has_archive' => 'playlists', 'rewrite' => array( 'slug' => 'playlists' ), 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'custom-fields' ),
		) );
		register_post_type( 'gravitas_channel', array(
			'labels' => $this->labels( __( 'Channel', 'gravitas-video' ), __( 'Channels', 'gravitas-video' ) ), 'public' => true, 'show_in_rest' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'has_archive' => 'channels', 'rewrite' => array( 'slug' => 'channels' ), 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'custom-fields' ),
		) );
		register_post_type( 'gravitas_series', array(
			'labels' => $this->labels( __( 'Series', 'gravitas-video' ), __( 'Series', 'gravitas-video' ) ), 'public' => true, 'show_in_rest' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'has_archive' => 'series', 'rewrite' => array( 'slug' => 'series' ), 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'custom-fields' ),
		) );
		register_post_type( 'gravitas_person', array(
			'labels' => $this->labels( __( 'Video Person', 'gravitas-video' ), __( 'People', 'gravitas-video' ) ), 'public' => true, 'show_in_rest' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'has_archive' => 'video-people', 'rewrite' => array( 'slug' => 'video-people' ), 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
		) );
		register_post_type( 'gravitas_report', array(
			'labels' => $this->labels( __( 'Video Report', 'gravitas-video' ), __( 'Reports', 'gravitas-video' ) ), 'public' => false, 'show_ui' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'supports' => array( 'title', 'editor', 'author', 'custom-fields' ),
		) );
		register_post_type( 'gravitas_feed', array(
			'labels' => $this->labels( __( 'Import Feed', 'gravitas-video' ), __( 'Import Feeds', 'gravitas-video' ) ), 'public' => false, 'show_ui' => true, 'show_in_menu' => 'edit.php?post_type=gravitas_video', 'supports' => array( 'title', 'editor', 'custom-fields' ),
		) );

		$this->taxonomy( 'video_category', __( 'Video Category', 'gravitas-video' ), __( 'Video Categories', 'gravitas-video' ), array( 'gravitas_video' ), 'video-category', true );
		$this->taxonomy( 'video_tag', __( 'Video Tag', 'gravitas-video' ), __( 'Video Tags', 'gravitas-video' ), array( 'gravitas_video' ), 'video-tag', false );
		$this->taxonomy( 'video_topic', __( 'Topic', 'gravitas-video' ), __( 'Topics', 'gravitas-video' ), array( 'gravitas_video', 'gravitas_playlist', 'gravitas_series' ), 'video-topic', true );
		$this->taxonomy( 'video_level', __( 'Level', 'gravitas-video' ), __( 'Levels', 'gravitas-video' ), array( 'gravitas_video' ), 'video-level', true );
		$this->taxonomy( 'playlist_category', __( 'Playlist Category', 'gravitas-video' ), __( 'Playlist Categories', 'gravitas-video' ), array( 'gravitas_playlist' ), 'playlist-category', true );
		$this->taxonomy( 'channel_category', __( 'Channel Category', 'gravitas-video' ), __( 'Channel Categories', 'gravitas-video' ), array( 'gravitas_channel' ), 'channel-category', true );
		$this->taxonomy( 'series_category', __( 'Series Category', 'gravitas-video' ), __( 'Series Categories', 'gravitas-video' ), array( 'gravitas_series' ), 'series-category', true );
		$this->taxonomy( 'person_role', __( 'Role', 'gravitas-video' ), __( 'Roles', 'gravitas-video' ), array( 'gravitas_person' ), 'video-role', true );
	}

	private function taxonomy( $name, $singular, $plural, $types, $slug, $hierarchical ) {
		register_taxonomy( $name, $types, array(
			'labels' => array( 'name' => $plural, 'singular_name' => $singular, 'search_items' => sprintf( __( 'Search %s', 'gravitas-video' ), $plural ), 'all_items' => sprintf( __( 'All %s', 'gravitas-video' ), $plural ), 'edit_item' => sprintf( __( 'Edit %s', 'gravitas-video' ), $singular ), 'add_new_item' => sprintf( __( 'Add New %s', 'gravitas-video' ), $singular ), 'menu_name' => $plural ),
			'public' => true, 'show_in_rest' => true, 'show_admin_column' => true, 'hierarchical' => $hierarchical, 'rewrite' => array( 'slug' => $slug ),
		) );
	}

	public function templates( $template ) {
		$is_video_search = is_search() && 'gravitas_video' === get_query_var( 'post_type' );
		$map = array(
			'single-gravitas_video.php'    => is_singular( 'gravitas_video' ),
			'archive-gravitas_video.php'   => is_post_type_archive( 'gravitas_video' ) || is_tax( array( 'video_category', 'video_tag', 'video_topic', 'video_level' ) ) || $is_video_search,
			'single-video-collection.php'  => is_singular( array( 'gravitas_playlist', 'gravitas_channel', 'gravitas_series', 'gravitas_person' ) ),
			'archive-video-collection.php' => is_post_type_archive( array( 'gravitas_playlist', 'gravitas_channel', 'gravitas_series', 'gravitas_person' ) ),
		);
		foreach ( $map as $file => $matches ) {
			if ( $matches ) {
				$theme_template = locate_template( 'gravitas-video/' . $file );
				return $theme_template ?: GRAVITAS_VIDEO_PATH . 'templates/' . $file;
			}
		}
		return $template;
	}

	public function archive_query( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$post_type = $query->get( 'post_type' );
		$requested = isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$is_video_query = $query->is_post_type_archive( 'gravitas_video' )
			|| 'gravitas_video' === $requested
			|| 'gravitas_video' === $post_type
			|| ( is_array( $post_type ) && in_array( 'gravitas_video', $post_type, true ) );
		if ( ! $is_video_query ) {
			return;
		}

		$query->set( 'post_type', 'gravitas_video' );
		$query->set( 'posts_per_page', max( 1, absint( gravitas_video_get_option( 'videos_per_page', 12 ) ) ) );

		$tax_query = array( 'relation' => 'AND' );
		foreach ( array( 'video_topic', 'video_level' ) as $taxonomy ) {
			$value = '';
			if ( isset( $_GET[ $taxonomy ] ) && is_string( $_GET[ $taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$value = sanitize_title( wp_unslash( $_GET[ $taxonomy ] ) );
			} elseif ( $query->get( $taxonomy ) ) {
				$value = sanitize_title( $query->get( $taxonomy ) );
			}
			if ( $value && term_exists( $value, $taxonomy ) ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $value,
				);
			}
		}
		if ( count( $tax_query ) > 1 ) {
			$query->set( 'tax_query', $tax_query );
		}
	}

	public function episode_permalink( $link, $post ) {
		return $link;
	}
}
