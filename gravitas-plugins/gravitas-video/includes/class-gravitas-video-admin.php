<?php
/**
 * Video administration.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Admin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'manage_gravitas_video_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_gravitas_video_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}

	public static function add_capabilities() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			foreach ( array( 'edit_gravitas_video', 'read_gravitas_video', 'delete_gravitas_video', 'edit_gravitas_videos', 'edit_others_gravitas_videos', 'publish_gravitas_videos', 'read_private_gravitas_videos', 'delete_gravitas_videos', 'delete_private_gravitas_videos', 'delete_published_gravitas_videos', 'delete_others_gravitas_videos', 'edit_private_gravitas_videos', 'edit_published_gravitas_videos' ) as $cap ) {
				$role->add_cap( $cap );
			}
		}
	}

	public function menu() {
		add_submenu_page( 'edit.php?post_type=gravitas_video', __( 'Video Dashboard', 'gravitas-video' ), __( 'Dashboard', 'gravitas-video' ), 'edit_posts', 'gravitas-video-dashboard', array( $this, 'dashboard' ) );
		add_submenu_page( 'edit.php?post_type=gravitas_video', __( 'Import Videos', 'gravitas-video' ), __( 'Import Videos', 'gravitas-video' ), 'edit_posts', 'gravitas-video-import', array( $this, 'import_page' ) );
		add_submenu_page( 'edit.php?post_type=gravitas_video', __( 'Video Analytics', 'gravitas-video' ), __( 'Analytics', 'gravitas-video' ), 'edit_posts', 'gravitas-video-analytics', array( $this, 'analytics' ) );
		add_submenu_page( 'edit.php?post_type=gravitas_video', __( 'Video Settings', 'gravitas-video' ), __( 'Settings', 'gravitas-video' ), 'manage_options', 'gravitas-video-settings', array( $this, 'settings' ) );
		add_submenu_page( 'edit.php?post_type=gravitas_video', __( 'Video Demo', 'gravitas-video' ), __( 'Demo Content', 'gravitas-video' ), 'manage_options', 'gravitas-video-demo', array( $this, 'demo' ) );
	}

	public function register_settings() {
		register_setting( 'gravitas_video_settings', 'gravitas_video_options', array( 'type' => 'array', 'sanitize_callback' => array( $this, 'sanitize' ), 'default' => $this->defaults() ) );
	}

	private function defaults() {
		return array( 'videos_per_page' => 12, 'youtube_api_key' => '', 'player_color' => '#e8674f', 'enable_reactions' => 1, 'enable_reports' => 1, 'enable_submissions' => 1, 'enable_downloads' => 1, 'default_access' => 'public', 'submission_status' => 'pending' );
	}

	public function sanitize( $input ) {
		return array(
			'videos_per_page' => min( 48, max( 1, absint( $input['videos_per_page'] ?? 12 ) ) ),
			'youtube_api_key' => sanitize_text_field( $input['youtube_api_key'] ?? '' ),
			'player_color' => sanitize_hex_color( $input['player_color'] ?? '' ) ?: '#e8674f',
			'enable_reactions' => empty( $input['enable_reactions'] ) ? 0 : 1,
			'enable_reports' => empty( $input['enable_reports'] ) ? 0 : 1,
			'enable_submissions' => empty( $input['enable_submissions'] ) ? 0 : 1,
			'enable_downloads' => empty( $input['enable_downloads'] ) ? 0 : 1,
			'default_access' => in_array( $input['default_access'] ?? '', array( 'public', 'logged_in', 'members' ), true ) ? $input['default_access'] : 'public',
			'submission_status' => 'draft' === ( $input['submission_status'] ?? '' ) ? 'draft' : 'pending',
		);
	}

	public function assets( $hook ) {
		if ( false !== strpos( $hook, 'gravitas-video' ) || 'post.php' === $hook || 'post-new.php' === $hook ) {
			wp_enqueue_style( 'gravitas-video-admin', GRAVITAS_VIDEO_URL . 'assets/admin.css', array(), GRAVITAS_VIDEO_VERSION );
		}
	}

	private function header( $title, $description = '' ) {
		echo '<div class="wrap gravitas-video-admin"><header><div><span>' . esc_html__( 'Gravitas Video', 'gravitas-video' ) . '</span><h1>' . esc_html( $title ) . '</h1><p>' . esc_html( $description ) . '</p></div><b>v' . esc_html( GRAVITAS_VIDEO_VERSION ) . '</b></header>';
	}

	public function dashboard() {
		$this->header( __( 'Video Dashboard', 'gravitas-video' ), __( 'Publish, organize, monetize, and measure your coaching video library.', 'gravitas-video' ) );
		$counts = array( 'gravitas_video' => __( 'Videos', 'gravitas-video' ), 'gravitas_playlist' => __( 'Playlists', 'gravitas-video' ), 'gravitas_channel' => __( 'Channels', 'gravitas-video' ), 'gravitas_series' => __( 'Series', 'gravitas-video' ) );
		echo '<div class="gravitas-video-stats">';
		foreach ( $counts as $type => $label ) { $count = wp_count_posts( $type ); printf( '<a href="%1$s"><strong>%2$d</strong><span>%3$s</span></a>', esc_url( admin_url( 'edit.php?post_type=' . $type ) ), absint( $count->publish ?? 0 ), esc_html( $label ) ); }
		echo '</div><div class="gravitas-video-admin-grid"><section><h2>' . esc_html__( 'Quick actions', 'gravitas-video' ) . '</h2><p><a class="button button-primary" href="' . esc_url( admin_url( 'post-new.php?post_type=gravitas_video' ) ) . '">' . esc_html__( 'Add video', 'gravitas-video' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'edit.php?post_type=gravitas_video&page=gravitas-video-import' ) ) . '">' . esc_html__( 'Import videos', 'gravitas-video' ) . '</a></p></section><section><h2>' . esc_html__( 'Membership readiness', 'gravitas-video' ) . '</h2><p>' . esc_html__( 'Choose public, account-only, or active-member access per video. Paid Memberships Pro and WooCommerce Memberships are detected automatically.', 'gravitas-video' ) . '</p></section></div></div>';
	}

	public function import_page() {
		$this->header( __( 'Import Videos', 'gravitas-video' ), __( 'Create draft videos from individual URLs or a YouTube search/channel.', 'gravitas-video' ) );
		if ( isset( $_GET['imported'] ) ) { $good = absint( $_GET['imported'] ) > 0; echo '<div class="notice ' . ( $good ? 'notice-success' : 'notice-error' ) . '"><p>' . esc_html( $good ? sprintf( __( '%d item(s) imported.', 'gravitas-video' ), absint( $_GET['imported'] ) ) : sanitize_text_field( wp_unslash( $_GET['message'] ?? __( 'Import failed.', 'gravitas-video' ) ) ) ) . '</p></div>'; }
		?>
		<div class="gravitas-video-admin-grid"><section><h2><?php esc_html_e( 'Import a URL', 'gravitas-video' ); ?></h2><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="gravitas_video_import_url"><?php wp_nonce_field( 'gravitas_video_import_url' ); ?><p><label><?php esc_html_e( 'YouTube, Vimeo, or supported oEmbed URL', 'gravitas-video' ); ?><input class="widefat" type="url" name="video_url" required></label></p><p><select name="post_status"><option value="draft"><?php esc_html_e( 'Save as draft', 'gravitas-video' ); ?></option><option value="publish"><?php esc_html_e( 'Publish immediately', 'gravitas-video' ); ?></option></select></p><?php submit_button( __( 'Import URL', 'gravitas-video' ), 'primary', 'submit', false ); ?></form></section>
		<section><h2><?php esc_html_e( 'YouTube bulk import', 'gravitas-video' ); ?></h2><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="gravitas_video_import_youtube"><?php wp_nonce_field( 'gravitas_video_import_youtube' ); ?><p><label><?php esc_html_e( 'Search phrase or channel ID', 'gravitas-video' ); ?><input class="widefat" type="text" name="youtube_query" required></label></p><p><label><?php esc_html_e( 'Number of videos', 'gravitas-video' ); ?><input type="number" name="youtube_count" value="10" min="1" max="50"></label></p><p><label><?php esc_html_e( 'Assign Gravitas channel ID', 'gravitas-video' ); ?><input type="number" name="channel_id" min="0"></label></p><?php submit_button( __( 'Import YouTube drafts', 'gravitas-video' ), 'primary', 'submit', false ); ?></form></section></div></div>
		<?php
	}

	public function analytics() {
		$this->header( __( 'Video Analytics', 'gravitas-video' ), __( 'An editorial snapshot based on first-party view and reaction counters.', 'gravitas-video' ) );
		$videos = get_posts( array( 'post_type' => 'gravitas_video', 'posts_per_page' => 25, 'meta_key' => '_gravitas_views', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) );
		echo '<section class="gravitas-video-table"><table class="widefat striped"><thead><tr><th>' . esc_html__( 'Video', 'gravitas-video' ) . '</th><th>' . esc_html__( 'Views', 'gravitas-video' ) . '</th><th>' . esc_html__( 'Likes', 'gravitas-video' ) . '</th><th>' . esc_html__( 'Access', 'gravitas-video' ) . '</th></tr></thead><tbody>';
		foreach ( $videos as $video ) { printf( '<tr><td><a href="%1$s">%2$s</a></td><td>%3$s</td><td>%4$s</td><td>%5$s</td></tr>', esc_url( get_edit_post_link( $video->ID ) ), esc_html( $video->post_title ), esc_html( gravitas_video_format_count( gravitas_video_get_meta( $video->ID, 'views', 0 ) ) ), esc_html( gravitas_video_format_count( gravitas_video_get_meta( $video->ID, 'likes', 0 ) ) ), esc_html( ucfirst( str_replace( '_', ' ', gravitas_video_get_meta( $video->ID, 'access_level', 'public' ) ) ) ) ); }
		echo '</tbody></table></section></div>';
	}

	public function settings() {
		$this->header( __( 'Video Settings', 'gravitas-video' ), __( 'Configure imports, archives, engagement, and publishing defaults.', 'gravitas-video' ) );
		$options = wp_parse_args( get_option( 'gravitas_video_options', array() ), $this->defaults() );
		?>
		<section class="gravitas-video-form"><form method="post" action="options.php"><?php settings_fields( 'gravitas_video_settings' ); ?><table class="form-table"><tr><th><?php esc_html_e( 'Videos per archive page', 'gravitas-video' ); ?></th><td><input type="number" min="1" max="48" name="gravitas_video_options[videos_per_page]" value="<?php echo absint( $options['videos_per_page'] ); ?>"></td></tr><tr><th><?php esc_html_e( 'YouTube API key', 'gravitas-video' ); ?></th><td><input class="regular-text" type="password" name="gravitas_video_options[youtube_api_key]" value="<?php echo esc_attr( $options['youtube_api_key'] ); ?>"><p class="description"><?php esc_html_e( 'Used only for channel/search imports and scheduled feeds.', 'gravitas-video' ); ?></p></td></tr><tr><th><?php esc_html_e( 'Player accent color', 'gravitas-video' ); ?></th><td><input type="color" name="gravitas_video_options[player_color]" value="<?php echo esc_attr( $options['player_color'] ); ?>"></td></tr><tr><th><?php esc_html_e( 'Community features', 'gravitas-video' ); ?></th><td><?php foreach ( array( 'enable_reactions' => __( 'Likes and dislikes', 'gravitas-video' ), 'enable_reports' => __( 'Viewer reports', 'gravitas-video' ), 'enable_submissions' => __( 'Front-end submissions', 'gravitas-video' ), 'enable_downloads' => __( 'Resource downloads', 'gravitas-video' ) ) as $key => $label ) { printf( '<label><input type="checkbox" name="gravitas_video_options[%1$s]" value="1" %2$s> %3$s</label><br>', esc_attr( $key ), checked( ! empty( $options[ $key ] ), true, false ), esc_html( $label ) ); } ?></td></tr><tr><th><?php esc_html_e( 'Default access', 'gravitas-video' ); ?></th><td><select name="gravitas_video_options[default_access]"><?php foreach ( array( 'public' => __( 'Public', 'gravitas-video' ), 'logged_in' => __( 'Logged-in users', 'gravitas-video' ), 'members' => __( 'Active members', 'gravitas-video' ) ) as $value => $label ) { printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $options['default_access'], $value, false ), esc_html( $label ) ); } ?></select></td></tr></table><?php submit_button(); ?></form></section></div>
		<?php
	}

	public function demo() {
		$this->header( __( 'Video Demo Content', 'gravitas-video' ), __( 'Add an organized soft-skills video library without changing your existing pages.', 'gravitas-video' ) );
		echo '<section class="gravitas-video-form"><h2>' . esc_html__( 'Starter video library', 'gravitas-video' ) . '</h2><p>' . esc_html__( 'Creates channels, playlists, a series, six sample video entries, and library pages. Running it again preserves existing records and adds only missing demo items.', 'gravitas-video' ) . '</p><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="gravitas_video_import_demo">'; wp_nonce_field( 'gravitas_video_import_demo' ); submit_button( __( 'Import Video Demo', 'gravitas-video' ), 'primary', 'submit', false ); echo '</form></section></div>';
	}

	public function columns( $columns ) {
		$columns['gravitas_stats'] = __( 'Engagement', 'gravitas-video' );
		$columns['gravitas_access'] = __( 'Access', 'gravitas-video' );
		return $columns;
	}

	public function column_content( $column, $post_id ) {
		if ( 'gravitas_stats' === $column ) { echo esc_html( gravitas_video_format_count( gravitas_video_get_meta( $post_id, 'views', 0 ) ) . ' views · ' . gravitas_video_format_count( gravitas_video_get_meta( $post_id, 'likes', 0 ) ) . ' likes' ); }
		if ( 'gravitas_access' === $column ) { echo esc_html( ucfirst( str_replace( '_', ' ', gravitas_video_get_meta( $post_id, 'access_level', 'public' ) ) ) ); }
	}

	public function notice() {
		if ( get_transient( 'gravitas_video_activated' ) && current_user_can( 'manage_options' ) ) { delete_transient( 'gravitas_video_activated' ); echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Gravitas Video is ready.', 'gravitas-video' ) . '</strong> <a href="' . esc_url( admin_url( 'edit.php?post_type=gravitas_video&page=gravitas-video-dashboard' ) ) . '">' . esc_html__( 'Open the video dashboard', 'gravitas-video' ) . '</a></p></div>'; }
	}
}
