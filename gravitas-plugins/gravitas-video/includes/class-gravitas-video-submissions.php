<?php
/**
 * Front-end video submissions.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Video_Submissions {
	public function __construct() {
		add_shortcode( 'gravitas_video_submit', array( $this, 'form' ) );
		add_shortcode( 'gravitas_my_videos', array( $this, 'my_videos' ) );
		add_action( 'admin_post_gravitas_submit_video', array( $this, 'handle' ) );
		add_action( 'admin_post_gravitas_delete_video', array( $this, 'delete' ) );
	}

	public function form() {
		if ( ! is_user_logged_in() ) {
			return '<div class="gravitas-video-notice"><p>' . esc_html__( 'Sign in to submit a video for review.', 'gravitas-video' ) . '</p><a class="gravitas-button" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . esc_html__( 'Sign in', 'gravitas-video' ) . '</a></div>';
		}
		ob_start();
		if ( isset( $_GET['video-submitted'] ) ) {
			echo '<p class="gravitas-video-notice is-success">' . esc_html__( 'Thanks. Your video was submitted for editorial review.', 'gravitas-video' ) . '</p>';
		}
		?>
		<form class="gravitas-submit-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="gravitas_submit_video">
			<?php wp_nonce_field( 'gravitas_submit_video', 'gravitas_submit_nonce' ); ?>
			<p><label><strong><?php esc_html_e( 'Video title', 'gravitas-video' ); ?></strong><input type="text" name="video_title" required></label></p>
			<p><label><strong><?php esc_html_e( 'Summary', 'gravitas-video' ); ?></strong><textarea name="video_excerpt" rows="3" required></textarea></label></p>
			<p><label><strong><?php esc_html_e( 'Description', 'gravitas-video' ); ?></strong><textarea name="video_content" rows="8" required></textarea></label></p>
			<p><label><strong><?php esc_html_e( 'YouTube, Vimeo, stream, or video URL', 'gravitas-video' ); ?></strong><input type="url" name="video_url" required></label></p>
			<p><label><strong><?php esc_html_e( 'Cover image', 'gravitas-video' ); ?></strong><input type="file" name="video_cover" accept="image/*"></label></p>
			<p><label><strong><?php esc_html_e( 'Topic', 'gravitas-video' ); ?></strong><?php wp_dropdown_categories( array( 'taxonomy' => 'video_topic', 'name' => 'video_topic', 'hide_empty' => false, 'show_option_none' => __( 'Choose a topic', 'gravitas-video' ) ) ); ?></label></p>
			<p><label><input type="checkbox" name="video_rights" value="1" required> <?php esc_html_e( 'I own this content or have permission to publish it.', 'gravitas-video' ); ?></label></p>
			<button class="gravitas-button" type="submit"><?php esc_html_e( 'Submit for review', 'gravitas-video' ); ?></button>
		</form>
		<?php
		return ob_get_clean();
	}

	public function handle() {
		if ( ! is_user_logged_in() || ! isset( $_POST['gravitas_submit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['gravitas_submit_nonce'] ) ), 'gravitas_submit_video' ) ) {
			wp_die( esc_html__( 'Submission verification failed.', 'gravitas-video' ) );
		}
		if ( empty( $_POST['video_rights'] ) ) {
			wp_die( esc_html__( 'Please confirm that you can publish this content.', 'gravitas-video' ) );
		}
		$post_id = wp_insert_post( array(
			'post_type' => 'gravitas_video', 'post_status' => 'pending', 'post_author' => get_current_user_id(),
			'post_title' => sanitize_text_field( wp_unslash( $_POST['video_title'] ?? '' ) ),
			'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['video_excerpt'] ?? '' ) ),
			'post_content' => wp_kses_post( wp_unslash( $_POST['video_content'] ?? '' ) ),
		) );
		if ( is_wp_error( $post_id ) ) {
			wp_die( esc_html( $post_id->get_error_message() ) );
		}
		update_post_meta( $post_id, '_gravitas_source_type', 'embed' );
		update_post_meta( $post_id, '_gravitas_video_url', esc_url_raw( wp_unslash( $_POST['video_url'] ?? '' ) ) );
		update_post_meta( $post_id, '_gravitas_access_level', 'public' );
		if ( ! empty( $_POST['video_topic'] ) ) {
			wp_set_object_terms( $post_id, absint( $_POST['video_topic'] ), 'video_topic' );
		}
		if ( ! empty( $_FILES['video_cover']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$attachment_id = media_handle_upload( 'video_cover', $post_id );
			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
			}
		}
		wp_safe_redirect( add_query_arg( 'video-submitted', '1', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	public function my_videos() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'Sign in to manage your submissions.', 'gravitas-video' ) . '</p>';
		}
		$query = new WP_Query( array( 'post_type' => 'gravitas_video', 'post_status' => array( 'publish', 'pending', 'draft' ), 'author' => get_current_user_id(), 'posts_per_page' => 50 ) );
		ob_start();
		echo '<div class="gravitas-my-videos">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$delete_url = wp_nonce_url( add_query_arg( array( 'action' => 'gravitas_delete_video', 'post_id' => get_the_ID() ), admin_url( 'admin-post.php' ) ), 'gravitas_delete_video_' . get_the_ID() );
			printf( '<article><div><strong>%1$s</strong><small>%2$s</small></div><div><a href="%3$s">%4$s</a> <a href="%5$s" onclick="return confirm(\'%6$s\')">%7$s</a></div></article>', esc_html( get_the_title() ), esc_html( get_post_status_object( get_post_status() )->label ), esc_url( get_edit_post_link() ), esc_html__( 'Edit', 'gravitas-video' ), esc_url( $delete_url ), esc_attr__( 'Delete this submission?', 'gravitas-video' ), esc_html__( 'Delete', 'gravitas-video' ) );
		}
		echo '</div>';
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function delete() {
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		if ( ! is_user_logged_in() || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'gravitas_delete_video_' . $post_id ) || (int) get_post_field( 'post_author', $post_id ) !== get_current_user_id() ) {
			wp_die( esc_html__( 'You cannot delete this submission.', 'gravitas-video' ) );
		}
		wp_trash_post( $post_id );
		wp_safe_redirect( wp_get_referer() ?: home_url( '/' ) );
		exit;
	}
}
