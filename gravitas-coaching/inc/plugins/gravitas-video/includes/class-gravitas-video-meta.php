<?php
/**
 * Native and REST-compatible video metadata.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Meta {
	public function __construct() {
		add_action( 'init', array( $this, 'register_meta' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'boxes' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public static function fields() {
		return array(
			'source_type'      => array( 'type' => 'string', 'default' => 'embed' ),
			'video_url'        => array( 'type' => 'string', 'default' => '' ),
			'video_file_id'    => array( 'type' => 'integer', 'default' => 0 ),
			'embed_code'       => array( 'type' => 'string', 'default' => '' ),
			'poster_id'        => array( 'type' => 'integer', 'default' => 0 ),
			'trailer_url'      => array( 'type' => 'string', 'default' => '' ),
			'duration'         => array( 'type' => 'string', 'default' => '' ),
			'views'            => array( 'type' => 'integer', 'default' => 0 ),
			'likes'            => array( 'type' => 'integer', 'default' => 0 ),
			'dislikes'         => array( 'type' => 'integer', 'default' => 0 ),
			'chapters'         => array( 'type' => 'string', 'default' => '' ),
			'downloads'        => array( 'type' => 'string', 'default' => '' ),
			'subtitles'        => array( 'type' => 'string', 'default' => '' ),
			'alternate_sources'=> array( 'type' => 'string', 'default' => '' ),
			'imdb_id'          => array( 'type' => 'string', 'default' => '' ),
			'tmdb_id'          => array( 'type' => 'string', 'default' => '' ),
			'external_rating'  => array( 'type' => 'string', 'default' => '' ),
			'access_level'     => array( 'type' => 'string', 'default' => 'public' ),
			'pmpro_levels'     => array( 'type' => 'string', 'default' => '' ),
			'ihc_levels'       => array( 'type' => 'string', 'default' => '' ),
			'woo_product_id'   => array( 'type' => 'integer', 'default' => 0 ),
			'preview_seconds'  => array( 'type' => 'integer', 'default' => 0 ),
			'preroll_url'      => array( 'type' => 'string', 'default' => '' ),
			'preroll_click'    => array( 'type' => 'string', 'default' => '' ),
			'preroll_skip'     => array( 'type' => 'integer', 'default' => 5 ),
			'overlay_text'     => array( 'type' => 'string', 'default' => '' ),
			'overlay_link'     => array( 'type' => 'string', 'default' => '' ),
			'channel_id'       => array( 'type' => 'integer', 'default' => 0 ),
			'playlist_ids'     => array( 'type' => 'string', 'default' => '' ),
			'series_id'        => array( 'type' => 'integer', 'default' => 0 ),
			'episode_number'   => array( 'type' => 'integer', 'default' => 0 ),
			'people_ids'       => array( 'type' => 'string', 'default' => '' ),
			'live_start'       => array( 'type' => 'string', 'default' => '' ),
			'live_status'      => array( 'type' => 'string', 'default' => 'recorded' ),
			'autoplay'         => array( 'type' => 'boolean', 'default' => false ),
			'muted'            => array( 'type' => 'boolean', 'default' => false ),
			'allow_downloads'  => array( 'type' => 'boolean', 'default' => false ),
			'featured_video'   => array( 'type' => 'boolean', 'default' => false ),
		);
	}

	public function register_meta() {
		foreach ( self::fields() as $key => $args ) {
			register_post_meta( 'gravitas_video', '_gravitas_' . $key, array(
				'type' => $args['type'], 'single' => true, 'default' => $args['default'], 'show_in_rest' => true,
				'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
				'sanitize_callback' => array( $this, 'sanitize_registered_meta' ),
			) );
		}
		foreach ( array( 'channel_id', 'series_id', 'playlist_ids', 'episode_number', 'people_ids' ) as $key ) {
			register_post_meta( 'gravitas_video', '_gravitas_' . $key, array( 'single' => true, 'show_in_rest' => true, 'auth_callback' => function() { return current_user_can( 'edit_posts' ); } ) );
		}
	}

	public function sanitize_registered_meta( $value, $meta_key ) {
		if ( preg_match( '/(_id|_ids|_number|_seconds|_skip|_views|_likes|_dislikes)$/', $meta_key ) ) {
			return is_array( $value ) ? array_map( 'absint', $value ) : absint( $value );
		}
		if ( '_gravitas_embed_code' === $meta_key ) {
			return $this->sanitize_embed_code( $value );
		}
		return is_bool( $value ) ? $value : sanitize_text_field( $value );
	}

	private function sanitize_embed_code( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( '[' === substr( $value, 0, 1 ) ) {
			return sanitize_text_field( $value );
		}
		if ( ! preg_match( '/<iframe[^>]+src=["\']([^"\']+)["\']/i', $value, $match ) ) {
			return '';
		}
		$host = strtolower( (string) wp_parse_url( $match[1], PHP_URL_HOST ) );
		if ( ! in_array( $host, array( 'www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'youtube-nocookie.com', 'player.vimeo.com' ), true ) ) {
			return '';
		}
		return wp_kses( $value, array( 'iframe' => array( 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true, 'allow' => true, 'allowfullscreen' => true, 'title' => true, 'loading' => true, 'referrerpolicy' => true, 'class' => true ) ) );
	}

	public function boxes() {
		add_meta_box( 'gravitas-video-source', __( 'Video Source & Playback', 'gravitas-video' ), array( $this, 'source_box' ), 'gravitas_video', 'normal', 'high' );
		add_meta_box( 'gravitas-video-organize', __( 'Channel, Playlist & Series', 'gravitas-video' ), array( $this, 'organize_box' ), 'gravitas_video', 'side' );
		add_meta_box( 'gravitas-video-access', __( 'Access & Monetization', 'gravitas-video' ), array( $this, 'access_box' ), 'gravitas_video', 'normal' );
		add_meta_box( 'gravitas-video-extras', __( 'Chapters, Downloads & Advertising', 'gravitas-video' ), array( $this, 'extras_box' ), 'gravitas_video', 'normal' );
		add_meta_box( 'gravitas-feed-settings', __( 'Automated Feed Settings', 'gravitas-video' ), array( $this, 'feed_box' ), 'gravitas_feed', 'normal', 'high' );
	}

	private function value( $key ) {
		return gravitas_video_get_meta( get_the_ID(), $key, self::fields()[ $key ]['default'] );
	}

	private function input( $key, $label, $type = 'text', $description = '' ) {
		printf( '<p class="gravitas-meta-field"><label for="gv-%1$s"><strong>%2$s</strong></label><input class="widefat" id="gv-%1$s" type="%3$s" name="gravitas_video[%1$s]" value="%4$s">%5$s</p>', esc_attr( $key ), esc_html( $label ), esc_attr( $type ), esc_attr( $this->value( $key ) ), $description ? '<small>' . esc_html( $description ) . '</small>' : '' );
	}

	private function textarea( $key, $label, $description = '' ) {
		printf( '<p class="gravitas-meta-field"><label for="gv-%1$s"><strong>%2$s</strong></label><textarea class="widefat" rows="5" id="gv-%1$s" name="gravitas_video[%1$s]">%3$s</textarea>%4$s</p>', esc_attr( $key ), esc_html( $label ), esc_textarea( $this->value( $key ) ), $description ? '<small>' . esc_html( $description ) . '</small>' : '' );
	}

	private function select( $key, $label, $choices ) {
		echo '<p class="gravitas-meta-field"><label for="gv-' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><select class="widefat" id="gv-' . esc_attr( $key ) . '" name="gravitas_video[' . esc_attr( $key ) . ']">';
		foreach ( $choices as $value => $label_text ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $this->value( $key ), $value, false ), esc_html( $label_text ) );
		}
		echo '</select></p>';
	}

	private function check( $key, $label ) {
		printf( '<p><label><input type="checkbox" name="gravitas_video[%1$s]" value="1" %2$s> %3$s</label></p>', esc_attr( $key ), checked( (bool) $this->value( $key ), true, false ), esc_html( $label ) );
	}

	public function source_box() {
		wp_nonce_field( 'gravitas_video_meta', 'gravitas_video_nonce' );
		$this->select( 'source_type', __( 'Source type', 'gravitas-video' ), array( 'embed' => __( 'Remote URL / oEmbed', 'gravitas-video' ), 'youtube' => __( 'YouTube URL', 'gravitas-video' ), 'vimeo' => __( 'Vimeo URL', 'gravitas-video' ), 'self_hosted' => __( 'Self-hosted media', 'gravitas-video' ), 'shortcode' => __( 'YouTube/Vimeo embed code or shortcode', 'gravitas-video' ), 'live' => __( 'Live stream URL', 'gravitas-video' ) ) );
		$this->input( 'video_url', __( 'Video or stream URL', 'gravitas-video' ), 'url', __( 'Paste a YouTube watch/share/embed URL, a Vimeo video/player URL, or a direct media URL.', 'gravitas-video' ) );
		$this->input( 'video_file_id', __( 'Media attachment ID', 'gravitas-video' ), 'number', __( 'Upload a video in Media and paste its attachment ID.', 'gravitas-video' ) );
		$this->textarea( 'embed_code', __( 'YouTube/Vimeo embed code or shortcode', 'gravitas-video' ), __( 'Choose the embed-code source above, then paste a YouTube or Vimeo iframe, or a registered WordPress shortcode.', 'gravitas-video' ) );
		$this->input( 'poster_id', __( 'Poster attachment ID', 'gravitas-video' ), 'number' );
		$this->input( 'trailer_url', __( 'Public trailer URL', 'gravitas-video' ), 'url' );
		$this->input( 'duration', __( 'Duration', 'gravitas-video' ), 'text', __( 'Example: 18:45', 'gravitas-video' ) );
		$this->select( 'live_status', __( 'Broadcast status', 'gravitas-video' ), array( 'recorded' => __( 'Recorded', 'gravitas-video' ), 'scheduled' => __( 'Scheduled', 'gravitas-video' ), 'live' => __( 'Live now', 'gravitas-video' ), 'ended' => __( 'Ended', 'gravitas-video' ) ) );
		$this->input( 'live_start', __( 'Scheduled start', 'gravitas-video' ), 'datetime-local' );
		$this->check( 'autoplay', __( 'Autoplay when supported', 'gravitas-video' ) );
		$this->check( 'muted', __( 'Start muted', 'gravitas-video' ) );
		$this->check( 'featured_video', __( 'Feature this video', 'gravitas-video' ) );
	}

	public function organize_box() {
		$this->input( 'channel_id', __( 'Channel post ID', 'gravitas-video' ), 'number' );
		$this->input( 'playlist_ids', __( 'Playlist post IDs', 'gravitas-video' ), 'text', __( 'Comma-separated IDs.', 'gravitas-video' ) );
		$this->input( 'series_id', __( 'Series post ID', 'gravitas-video' ), 'number' );
		$this->input( 'episode_number', __( 'Episode number', 'gravitas-video' ), 'number' );
		$this->input( 'people_ids', __( 'People post IDs', 'gravitas-video' ), 'text', __( 'Comma-separated presenter, actor, or director IDs.', 'gravitas-video' ) );
	}

	public function access_box() {
		$this->select( 'access_level', __( 'Who can watch?', 'gravitas-video' ), array( 'public' => __( 'Everyone', 'gravitas-video' ), 'logged_in' => __( 'Logged-in users', 'gravitas-video' ), 'members' => __( 'Active members', 'gravitas-video' ) ) );
		$this->input( 'pmpro_levels', __( 'Paid Memberships Pro level IDs', 'gravitas-video' ), 'text', __( 'Comma-separated. Leave empty for any active level.', 'gravitas-video' ) );
		$this->input( 'ihc_levels', __( 'Ultimate Membership Pro level IDs', 'gravitas-video' ), 'text', __( 'Comma-separated. Leave empty for any active level.', 'gravitas-video' ) );
		$this->input( 'woo_product_id', __( 'WooCommerce product ID', 'gravitas-video' ), 'number' );
		$this->input( 'preview_seconds', __( 'Free preview seconds', 'gravitas-video' ), 'number' );
	}

	public function extras_box() {
		$this->textarea( 'chapters', __( 'Chapters', 'gravitas-video' ), __( 'One per line: 00:00|Welcome', 'gravitas-video' ) );
		$this->textarea( 'subtitles', __( 'Subtitle tracks', 'gravitas-video' ), __( 'One per line: English|https://example.com/captions.vtt', 'gravitas-video' ) );
		$this->textarea( 'alternate_sources', __( 'Alternate video links', 'gravitas-video' ), __( 'One per line: YouTube|https://youtube.com/...', 'gravitas-video' ) );
		$this->textarea( 'downloads', __( 'Download resources', 'gravitas-video' ), __( 'One per line: Workbook|https://example.com/workbook.pdf', 'gravitas-video' ) );
		$this->check( 'allow_downloads', __( 'Display download resources', 'gravitas-video' ) );
		$this->input( 'preroll_url', __( 'Pre-roll video URL', 'gravitas-video' ), 'url' );
		$this->input( 'preroll_click', __( 'Pre-roll click-through URL', 'gravitas-video' ), 'url' );
		$this->input( 'preroll_skip', __( 'Skip ad after seconds', 'gravitas-video' ), 'number' );
		$this->input( 'overlay_text', __( 'Player overlay text', 'gravitas-video' ) );
		$this->input( 'overlay_link', __( 'Player overlay URL', 'gravitas-video' ), 'url' );
		$this->input( 'imdb_id', __( 'IMDb ID', 'gravitas-video' ), 'text', __( 'Optional external catalog reference.', 'gravitas-video' ) );
		$this->input( 'tmdb_id', __( 'TMDB ID', 'gravitas-video' ), 'text' );
		$this->input( 'external_rating', __( 'External rating', 'gravitas-video' ), 'text' );
	}

	public function feed_box( $post ) {
		wp_nonce_field( 'gravitas_feed_meta', 'gravitas_feed_nonce' );
		$fields = array(
			'query' => array( __( 'YouTube search phrase or channel ID', 'gravitas-video' ), 'text', '' ),
			'count' => array( __( 'Videos per run', 'gravitas-video' ), 'number', 10 ),
			'channel_id' => array( __( 'Assign Gravitas channel ID', 'gravitas-video' ), 'number', 0 ),
			'interval' => array( __( 'Run interval in seconds', 'gravitas-video' ), 'number', DAY_IN_SECONDS ),
		);
		foreach ( $fields as $key => $field ) {
			$value = get_post_meta( $post->ID, '_gravitas_feed_' . $key, true );
			$value = '' === $value ? $field[2] : $value;
			printf( '<p><label><strong>%1$s</strong><input class="widefat" type="%2$s" name="gravitas_feed[%3$s]" value="%4$s"></label></p>', esc_html( $field[0] ), esc_attr( $field[1] ), esc_attr( $key ), esc_attr( $value ) );
		}
		echo '<p>' . esc_html__( 'Published feeds run through WP-Cron. The YouTube API key is configured under Videos → Settings.', 'gravitas-video' ) . '</p>';
	}

	public function save( $post_id ) {
		if ( 'gravitas_feed' === get_post_type( $post_id ) ) {
			$this->save_feed( $post_id );
			return;
		}
		if ( 'gravitas_video' !== get_post_type( $post_id ) || ! isset( $_POST['gravitas_video_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gravitas_video_nonce'] ) ), 'gravitas_video_meta' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		$input = isset( $_POST['gravitas_video'] ) ? (array) wp_unslash( $_POST['gravitas_video'] ) : array();
		foreach ( self::fields() as $key => $args ) {
			$value = isset( $input[ $key ] ) ? $input[ $key ] : ( 'boolean' === $args['type'] ? 0 : '' );
			if ( 'integer' === $args['type'] ) {
				$value = absint( $value );
			} elseif ( 'boolean' === $args['type'] ) {
				$value = empty( $value ) ? 0 : 1;
			} elseif ( in_array( $key, array( 'video_url', 'trailer_url', 'preroll_url', 'preroll_click', 'overlay_link' ), true ) ) {
				$value = esc_url_raw( $value );
			} elseif ( in_array( $key, array( 'embed_code' ), true ) ) {
				$value = $this->sanitize_embed_code( $value );
			} elseif ( in_array( $key, array( 'chapters', 'downloads', 'subtitles', 'alternate_sources' ), true ) ) {
				$value = sanitize_textarea_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, '_gravitas_' . $key, $value );
		}
	}

	private function save_feed( $post_id ) {
		if ( ! isset( $_POST['gravitas_feed_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gravitas_feed_nonce'] ) ), 'gravitas_feed_meta' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		$input = isset( $_POST['gravitas_feed'] ) ? (array) wp_unslash( $_POST['gravitas_feed'] ) : array();
		update_post_meta( $post_id, '_gravitas_feed_query', sanitize_text_field( $input['query'] ?? '' ) );
		update_post_meta( $post_id, '_gravitas_feed_count', min( 50, max( 1, absint( $input['count'] ?? 10 ) ) ) );
		update_post_meta( $post_id, '_gravitas_feed_channel_id', absint( $input['channel_id'] ?? 0 ) );
		update_post_meta( $post_id, '_gravitas_feed_interval', max( HOUR_IN_SECONDS, absint( $input['interval'] ?? DAY_IN_SECONDS ) ) );
	}
}
