<?php
/**
 * Video player and display shortcodes.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Player {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_head', array( $this, 'schema' ) );
		add_shortcode( 'gravitas_video_player', array( $this, 'player_shortcode' ) );
		add_shortcode( 'gravitas_video_grid', array( $this, 'grid_shortcode' ) );
		add_shortcode( 'gravitas_playlist', array( $this, 'collection_shortcode' ) );
		add_shortcode( 'gravitas_channel', array( $this, 'collection_shortcode' ) );
		add_shortcode( 'gravitas_series', array( $this, 'collection_shortcode' ) );
		add_shortcode( 'gravitas_watch_later', array( $this, 'saved_shortcode' ) );
		add_shortcode( 'gravitas_history', array( $this, 'history_shortcode' ) );
	}

	public function assets() {
		wp_enqueue_style( 'gravitas-video', GRAVITAS_VIDEO_URL . 'assets/front.css', array(), GRAVITAS_VIDEO_VERSION );
		wp_enqueue_style( 'gravitas-video-layout', GRAVITAS_VIDEO_URL . 'assets/front-layout.css', array( 'gravitas-video' ), GRAVITAS_VIDEO_VERSION );
		wp_enqueue_script( 'gravitas-video', GRAVITAS_VIDEO_URL . 'assets/front.js', array(), GRAVITAS_VIDEO_VERSION, true );
		wp_localize_script( 'gravitas-video', 'gravitasVideo', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'gravitas_video_action' ),
			'userId'  => get_current_user_id(),
			'labels'  => array( 'saved' => __( 'Saved', 'gravitas-video' ), 'watchLater' => __( 'Watch later', 'gravitas-video' ), 'reported' => __( 'Report received', 'gravitas-video' ), 'error' => __( 'Please try again.', 'gravitas-video' ) ),
		) );
	}

	public function player_shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => get_the_ID() ), $atts, 'gravitas_video_player' );
		return $this->render( absint( $atts['id'] ) );
	}

	public function render( $post_id ) {
		if ( 'gravitas_video' !== get_post_type( $post_id ) ) {
			return '';
		}
		$allowed = gravitas_video_user_can_watch( $post_id );
		$source  = gravitas_video_get_meta( $post_id, 'source_type', 'embed' );
		$url     = $allowed ? gravitas_video_get_meta( $post_id, 'video_url' ) : gravitas_video_get_meta( $post_id, 'trailer_url' );
		$alternate_sources = $this->parse_rows( gravitas_video_get_meta( $post_id, 'alternate_sources' ) );
		$source_index = isset( $_GET['video_source'] ) ? absint( $_GET['video_source'] ) : 0;
		if ( $allowed && $source_index > 0 && isset( $alternate_sources[ $source_index - 1 ] ) ) {
			$url = esc_url_raw( $alternate_sources[ $source_index - 1 ]['url'] );
			$source = 'embed';
		}
		$poster  = absint( gravitas_video_get_meta( $post_id, 'poster_id' ) );
		$poster  = $poster ? wp_get_attachment_image_url( $poster, 'full' ) : get_the_post_thumbnail_url( $post_id, 'full' );
		$preroll = $allowed ? gravitas_video_get_meta( $post_id, 'preroll_url' ) : '';
		$has_trailer = ! $allowed && ! empty( $url );
		$media   = '';

		if ( $url ) {
			$media = $this->source_markup( $post_id, $source, $url, $poster );
		} elseif ( $allowed && 'self_hosted' === $source ) {
			$file_id = absint( gravitas_video_get_meta( $post_id, 'video_file_id' ) );
			$file_url = $file_id ? wp_get_attachment_url( $file_id ) : '';
			$media = $file_url ? $this->native_video( $post_id, $file_url, $poster ) : '';
		} elseif ( $allowed && 'shortcode' === $source ) {
			$embed = gravitas_video_get_meta( $post_id, 'embed_code' );
			$media = do_shortcode( $embed );
			$media = wp_kses( $media, $this->embed_allowed_html() );
		}

		ob_start();
		?>
		<div class="gravitas-player<?php echo $allowed ? '' : ' is-locked'; ?>" data-video-player data-video-id="<?php echo absint( $post_id ); ?>" data-preroll="<?php echo esc_url( $preroll ); ?>" data-preroll-click="<?php echo esc_url( gravitas_video_get_meta( $post_id, 'preroll_click' ) ); ?>" data-preroll-skip="<?php echo absint( gravitas_video_get_meta( $post_id, 'preroll_skip', 5 ) ); ?>">
			<div class="gravitas-player__stage">
				<?php if ( $media ) : ?><?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php elseif ( ! $allowed && $poster ) : ?><img class="gravitas-player__poster" src="<?php echo esc_url( $poster ); ?>" alt=""><?php else : ?><div class="gravitas-player__empty"><span class="dashicons dashicons-controls-play"></span><p><?php esc_html_e( 'Add a video source to begin playback.', 'gravitas-video' ); ?></p></div><?php endif; ?>
				<?php if ( ! $allowed ) : ?><div class="gravitas-player__gate<?php echo $has_trailer ? ' is-trailer-gate' : ''; ?>"><span class="gravitas-lock" aria-hidden="true">&#128274;</span><div><h3><?php echo $has_trailer ? esc_html__( 'Preview', 'gravitas-video' ) : esc_html__( 'Members-only video', 'gravitas-video' ); ?></h3><p><?php esc_html_e( 'Sign in with an eligible membership to watch the full session.', 'gravitas-video' ); ?></p></div><div class="gravitas-player__gate-actions"><a class="gravitas-button" href="<?php echo esc_url( wp_login_url( get_permalink( $post_id ) ) ); ?>"><?php esc_html_e( 'Sign in', 'gravitas-video' ); ?></a><a class="gravitas-button is-secondary" href="<?php echo esc_url( gravitas_video_membership_url( $post_id ) ); ?>"><?php esc_html_e( 'View memberships', 'gravitas-video' ); ?></a></div></div><?php endif; ?>
				<?php if ( $allowed && gravitas_video_get_meta( $post_id, 'overlay_text' ) ) : ?><a class="gravitas-player__overlay" href="<?php echo esc_url( gravitas_video_get_meta( $post_id, 'overlay_link', '#' ) ); ?>" target="_blank" rel="noopener sponsored"><?php echo esc_html( gravitas_video_get_meta( $post_id, 'overlay_text' ) ); ?></a><?php endif; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private function source_markup( $post_id, $source, $url, $poster ) {
		if ( in_array( $source, array( 'self_hosted', 'live' ), true ) && preg_match( '/\.(mp4|webm|ogg|m3u8)(\?.*)?$/i', $url ) ) {
			return $this->native_video( $post_id, $url, $poster );
		}
		$trusted_embed_url = $this->trusted_embed_url( $url );
		if ( $trusted_embed_url ) {
			return '<div class="gravitas-player__embed"><iframe src="' . esc_url( $trusted_embed_url ) . '" width="1280" height="720" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen loading="lazy" title="' . esc_attr( get_the_title( $post_id ) ) . '"></iframe></div>';
		}
		$embed = wp_oembed_get( $url, array( 'width' => 1280 ) );
		if ( $embed ) {
			return '<div class="gravitas-player__embed">' . wp_kses( $embed, $this->embed_allowed_html() ) . '</div>';
		}
		return $this->native_video( $post_id, $url, $poster );
	}

	private function trusted_embed_url( $url ) {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
		if ( in_array( $host, array( 'youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com' ), true ) ) {
			parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
			$parts = explode( '/', $path );
			$video_id = isset( $query['v'] ) ? $query['v'] : ( in_array( $parts[0] ?? '', array( 'embed', 'shorts', 'live' ), true ) ? ( $parts[1] ?? '' ) : '' );
			$video_id = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $video_id );
			return $video_id ? 'https://www.youtube-nocookie.com/embed/' . $video_id : '';
		}
		if ( 'youtu.be' === $host || 'www.youtu.be' === $host ) {
			$video_id = preg_replace( '/[^A-Za-z0-9_-]/', '', explode( '/', $path )[0] ?? '' );
			return $video_id ? 'https://www.youtube-nocookie.com/embed/' . $video_id : '';
		}
		if ( in_array( $host, array( 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ), true ) ) {
			preg_match_all( '/\d+/', $path, $matches );
			$video_id = ! empty( $matches[0] ) ? end( $matches[0] ) : '';
			return $video_id ? 'https://player.vimeo.com/video/' . $video_id : '';
		}
		return '';
	}

	private function native_video( $post_id, $url, $poster ) {
		$tracks = '';
		foreach ( $this->parse_rows( gravitas_video_get_meta( $post_id, 'subtitles' ) ) as $track ) {
			$tracks .= sprintf( '<track kind="subtitles" label="%1$s" srclang="%2$s" src="%3$s">', esc_attr( $track['label'] ), esc_attr( substr( strtolower( $track['label'] ), 0, 2 ) ), esc_url( $track['url'] ) );
		}
		return sprintf( '<video controls playsinline %1$s %2$s %3$s preload="metadata"><source src="%4$s">%5$s</video>', gravitas_video_get_meta( $post_id, 'autoplay' ) ? 'autoplay' : '', gravitas_video_get_meta( $post_id, 'muted' ) ? 'muted' : '', $poster ? 'poster="' . esc_url( $poster ) . '"' : '', esc_url( $url ), $tracks );
	}

	private function embed_allowed_html() {
		$allowed = wp_kses_allowed_html( 'post' );
		$allowed['iframe'] = array( 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true, 'allow' => true, 'allowfullscreen' => true, 'title' => true, 'loading' => true, 'referrerpolicy' => true, 'class' => true );
		return $allowed;
	}

	public function grid_shortcode( $atts ) {
		$atts = shortcode_atts( array( 'count' => 9, 'category' => '', 'topic' => '', 'level' => '', 'channel' => 0, 'series' => 0, 'featured' => '', 'orderby' => 'date' ), $atts, 'gravitas_video_grid' );
		$args = array( 'post_type' => 'gravitas_video', 'post_status' => 'publish', 'posts_per_page' => min( 48, absint( $atts['count'] ) ), 'orderby' => sanitize_key( $atts['orderby'] ) );
		$tax_query = array();
		foreach ( array( 'category' => 'video_category', 'topic' => 'video_topic', 'level' => 'video_level' ) as $attribute => $taxonomy ) {
			if ( $atts[ $attribute ] ) {
				$tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => sanitize_title( $atts[ $attribute ] ) );
			}
		}
		if ( $tax_query ) {
			$args['tax_query'] = $tax_query;
		}
		$meta_query = array();
		if ( $atts['channel'] ) {
			$meta_query[] = array( 'key' => '_gravitas_channel_id', 'value' => absint( $atts['channel'] ) );
		}
		if ( $atts['series'] ) {
			$meta_query[] = array( 'key' => '_gravitas_series_id', 'value' => absint( $atts['series'] ) );
		}
		if ( '' !== $atts['featured'] ) {
			$meta_query[] = array( 'key' => '_gravitas_featured_video', 'value' => 1 );
		}
		if ( $meta_query ) {
			$args['meta_query'] = $meta_query;
		}
		$query = new WP_Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			echo '<div class="gravitas-video-grid">';
			while ( $query->have_posts() ) {
				$query->the_post();
				echo $this->card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</div>';
		} else {
			echo '<p class="gravitas-video-empty">' . esc_html__( 'No videos match this selection yet.', 'gravitas-video' ) . '</p>';
		}
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function card( $post_id ) {
		$terms = get_the_terms( $post_id, 'video_topic' );
		$topic = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : __( 'Coaching', 'gravitas-video' );
		ob_start();
		?>
		<article class="gravitas-video-card">
			<a class="gravitas-video-card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
				<?php if ( has_post_thumbnail( $post_id ) ) : ?><?php echo get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?><?php else : ?><span class="gravitas-video-card__placeholder"></span><?php endif; ?>
				<span class="gravitas-video-card__play" aria-hidden="true">&#9658;</span>
				<?php if ( gravitas_video_get_meta( $post_id, 'duration' ) ) : ?><span class="gravitas-video-card__duration"><?php echo esc_html( gravitas_video_get_meta( $post_id, 'duration' ) ); ?></span><?php endif; ?>
				<?php if ( 'live' === gravitas_video_get_meta( $post_id, 'live_status' ) ) : ?><span class="gravitas-video-card__live"><?php esc_html_e( 'Live', 'gravitas-video' ); ?></span><?php endif; ?>
			</a>
			<div class="gravitas-video-card__body"><span class="gravitas-video-card__topic"><?php echo esc_html( $topic ); ?></span><h3><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3><div class="gravitas-video-card__meta"><span><?php echo esc_html( gravitas_video_format_count( gravitas_video_get_meta( $post_id, 'views', 0 ) ) ); ?> <?php esc_html_e( 'views', 'gravitas-video' ); ?></span><span><?php echo esc_html( get_the_date( '', $post_id ) ); ?></span></div></div>
		</article>
		<?php
		return ob_get_clean();
	}

	public function collection_shortcode( $atts, $content, $tag ) {
		$atts = shortcode_atts( array( 'id' => get_the_ID(), 'count' => 12 ), $atts, $tag );
		$id   = absint( $atts['id'] );
		$key  = 'gravitas_channel' === $tag ? 'channel' : ( 'gravitas_series' === $tag ? 'series' : 'playlist' );
		if ( 'playlist' === $key ) {
			$args = array( 'post_type' => 'gravitas_video', 'posts_per_page' => absint( $atts['count'] ), 'meta_query' => array( array( 'key' => '_gravitas_playlist_ids', 'value' => '(^|,)' . $id . '(,|$)', 'compare' => 'REGEXP' ) ) );
		} else {
			$args = array( 'post_type' => 'gravitas_video', 'posts_per_page' => absint( $atts['count'] ), 'meta_key' => '_gravitas_' . $key . '_id', 'meta_value' => $id );
		}
		$query = new WP_Query( $args );
		ob_start();
		echo '<div class="gravitas-video-grid">';
		while ( $query->have_posts() ) { $query->the_post(); echo $this->card( get_the_ID() ); }
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function saved_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . sprintf( wp_kses_post( __( '<a href="%s">Sign in</a> to use your watch-later list.', 'gravitas-video' ) ), esc_url( wp_login_url( get_permalink() ) ) ) . '</p>';
		}
		$ids = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_gravitas_watch_later', true ) );
		return $this->ids_grid( $ids );
	}

	public function history_shortcode() {
		if ( ! is_user_logged_in() ) {
			return '<div class="gravitas-local-history" data-local-history><p>' . esc_html__( 'Your recent videos will appear here on this device.', 'gravitas-video' ) . '</p></div>';
		}
		$ids = array_map( 'absint', (array) get_user_meta( get_current_user_id(), '_gravitas_video_history', true ) );
		return $this->ids_grid( $ids );
	}

	private function ids_grid( $ids ) {
		if ( empty( $ids ) ) {
			return '<p class="gravitas-video-empty">' . esc_html__( 'No videos saved yet.', 'gravitas-video' ) . '</p>';
		}
		$query = new WP_Query( array( 'post_type' => 'gravitas_video', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => 50 ) );
		ob_start(); echo '<div class="gravitas-video-grid">'; while ( $query->have_posts() ) { $query->the_post(); echo $this->card( get_the_ID() ); } echo '</div>'; wp_reset_postdata(); return ob_get_clean();
	}

	public function parse_rows( $value ) {
		$rows = array();
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $value ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( 2 === count( $parts ) && $parts[0] && $parts[1] ) {
				$rows[] = array( 'label' => $parts[0], 'url' => $parts[1] );
			}
		}
		return $rows;
	}

	public function schema() {
		if ( ! is_singular( 'gravitas_video' ) || ( function_exists( 'gravitas_core_get_option' ) && ! gravitas_core_get_option( 'enable_schema', 1 ) ) ) {
			return;
		}
		$post_id = get_queried_object_id();
		$data = array( '@context' => 'https://schema.org', '@type' => 'VideoObject', 'name' => get_the_title( $post_id ), 'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ?: get_post_field( 'post_content', $post_id ) ), 'thumbnailUrl' => array_values( array_filter( array( get_the_post_thumbnail_url( $post_id, 'full' ) ) ) ), 'uploadDate' => get_the_date( DATE_W3C, $post_id ), 'contentUrl' => gravitas_video_get_meta( $post_id, 'video_url' ) );
		if ( gravitas_video_get_meta( $post_id, 'duration' ) && preg_match( '/^(?:(\d+):)?(\d+):(\d+)$/', gravitas_video_get_meta( $post_id, 'duration' ), $parts ) ) {
			$data['duration'] = 'PT' . ( ! empty( $parts[1] ) ? absint( $parts[1] ) . 'H' : '' ) . absint( $parts[2] ) . 'M' . absint( $parts[3] ) . 'S';
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array_filter( $data ) ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
