<?php
/**
 * Built-in one-click demo importer.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_demo_menu() {
	add_theme_page(
		__( 'Gravitas Demo Import', 'gravitas-coaching' ),
		__( 'Import Gravitas Demo', 'gravitas-coaching' ),
		'manage_options',
		'elevora-demo-import',
		'elevora_demo_page'
	);
}
add_action( 'admin_menu', 'elevora_demo_menu' );

/**
 * Load importer styles only on the Gravitas importer screen.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function elevora_demo_admin_assets( $hook_suffix ) {
	$importer_hooks = array( 'appearance_page_elevora-demo-import', 'gravitas_page_gravitas-demo-link' );
	if ( ! in_array( $hook_suffix, $importer_hooks, true ) ) {
		return;
	}

	wp_enqueue_style( 'elevora-demo-import', ELEVORA_URI . '/assets/css/demo-import.css', array(), ELEVORA_VERSION );
	wp_enqueue_script( 'elevora-demo-import', ELEVORA_URI . '/assets/js/demo-import.js', array(), ELEVORA_VERSION, true );
	wp_localize_script(
		'elevora-demo-import',
		'elevoraDemoImport',
		array(
			'importStages' => array(
				esc_html__( 'Preparing the demo package…', 'gravitas-coaching' ),
				esc_html__( 'Importing images and content…', 'gravitas-coaching' ),
				esc_html__( 'Building pages and navigation…', 'gravitas-coaching' ),
				esc_html__( 'Applying theme settings…', 'gravitas-coaching' ),
				esc_html__( 'Finishing the demo import…', 'gravitas-coaching' ),
			),
			'removeStages' => array(
				esc_html__( 'Preparing demo cleanup…', 'gravitas-coaching' ),
				esc_html__( 'Removing demo posts and pages…', 'gravitas-coaching' ),
				esc_html__( 'Removing imported media and menus…', 'gravitas-coaching' ),
				esc_html__( 'Resetting demo settings…', 'gravitas-coaching' ),
				esc_html__( 'Finishing cleanup…', 'gravitas-coaching' ),
			),
			'importTitle' => esc_html__( 'Importing your Gravitas demo', 'gravitas-coaching' ),
			'removeTitle' => esc_html__( 'Removing Gravitas demo content', 'gravitas-coaching' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'elevora_demo_admin_assets' );

function elevora_demo_page() {
	$imported     = get_option( 'elevora_demo_imported_at' );
	$blog_layouts = elevora_get_blog_layouts();
	?>
	<div class="wrap elevora-import-wrap">
		<h1><?php esc_html_e( 'Gravitas Demo Import', 'gravitas-coaching' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Import the complete coaching site or choose one of six focused blog designs. Imports use local assets, add missing demo items, and preserve content that already exists.', 'gravitas-coaching' ); ?></p>
		<?php if ( filter_input( INPUT_GET, 'imported', FILTER_VALIDATE_BOOLEAN ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Demo imported successfully. You can switch blog designs any time in Appearance > Customize > Gravitas Blog.', 'gravitas-coaching' ); ?></p></div><?php endif; ?>
		<?php if ( filter_input( INPUT_GET, 'removed', FILTER_VALIDATE_BOOLEAN ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Gravitas demo content was removed. Demo posts were moved to Trash.', 'gravitas-coaching' ); ?></p></div><?php endif; ?>
		<div class="elevora-import-progress" data-demo-progress hidden aria-live="polite">
			<div class="elevora-import-progress__header"><strong data-demo-progress-title><?php esc_html_e( 'Preparing demo import', 'gravitas-coaching' ); ?></strong><span data-demo-progress-value>0%</span></div>
			<div class="elevora-import-progress__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-demo-progress-bar><span data-demo-progress-fill></span></div>
			<p data-demo-progress-stage><?php esc_html_e( 'Please keep this page open until WordPress finishes.', 'gravitas-coaching' ); ?></p>
		</div>
		<div class="elevora-import-card elevora-import-card--featured">
			<div class="elevora-import-card__icon"><span class="dashicons dashicons-welcome-widgets-menus"></span></div>
			<div><h2><?php esc_html_e( 'Complete Coaching Site', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Homepage, consultants, programs, testimonials, journal, navigation, settings, and optional video content.', 'gravitas-coaching' ); ?></p><?php if ( $imported ) : ?><p><strong><?php echo esc_html( sprintf( __( 'Last imported: %s', 'gravitas-coaching' ), $imported ) ); ?></strong></p><?php endif; ?><?php elevora_demo_import_form( 'complete', __( 'Import Complete Demo', 'gravitas-coaching' ), true ); ?></div>
		</div>
		<h2 class="elevora-import-heading"><?php esc_html_e( 'Blog starter demos', 'gravitas-coaching' ); ?></h2>
		<div class="elevora-import-grid">
			<?php foreach ( $blog_layouts as $layout => $label ) : ?>
				<div class="elevora-import-card elevora-import-card--blog">
					<div class="elevora-demo-preview elevora-demo-preview--<?php echo esc_attr( $layout ); ?>"><span></span><span></span><span></span><span></span></div>
					<div><h3><?php echo esc_html( $label ); ?></h3><p><?php echo esc_html( elevora_demo_layout_description( $layout ) ); ?></p><?php elevora_demo_import_form( $layout, sprintf( __( 'Import %s', 'gravitas-coaching' ), $label ) ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="elevora-import-card elevora-import-card--danger">
			<div><h2><?php esc_html_e( 'Uninstall demo content', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Move imported demo posts, pages, trainers, services, testimonials, and video records to Trash. Imported media and the Gravitas demo menu will be removed permanently. Your unrelated content is not selected.', 'gravitas-coaching' ); ?></p></div>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-demo-form="remove" data-confirm-message="<?php esc_attr_e( 'Remove all Gravitas demo content? Demo posts will be moved to Trash and imported media will be deleted.', 'gravitas-coaching' ); ?>">
				<?php wp_nonce_field( 'elevora_uninstall_demo', 'elevora_uninstall_nonce' ); ?>
				<input type="hidden" name="action" value="elevora_uninstall_demo">
				<label class="elevora-reset-option"><input type="checkbox" name="reset_theme_options" value="1" checked> <?php esc_html_e( 'Reset Gravitas Theme Options for a clean start', 'gravitas-coaching' ); ?></label>
				<button class="button button-secondary" type="submit"><?php esc_html_e( 'Uninstall Demo', 'gravitas-coaching' ); ?></button>
			</form>
		</div>
	</div>
	<?php
}

/**
 * Render a secure demo import form.
 *
 * @param string $demo       Demo slug.
 * @param string $label      Button label.
 * @param bool   $is_primary Whether the button is primary.
 */
function elevora_demo_import_form( $demo, $label, $is_primary = false ) {
	?>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-demo-form="import">
		<?php wp_nonce_field( 'elevora_import_demo', 'elevora_import_nonce' ); ?>
		<input type="hidden" name="action" value="elevora_import_demo">
		<input type="hidden" name="demo" value="<?php echo esc_attr( $demo ); ?>">
		<button class="button <?php echo $is_primary ? 'button-primary' : 'button-secondary'; ?>" type="submit"><?php echo esc_html( $label ); ?></button>
	</form>
	<?php
}

/**
 * Describe a blog layout for the demo chooser.
 *
 * @param string $layout Layout slug.
 * @return string
 */
function elevora_demo_layout_description( $layout ) {
	$descriptions = array(
		'editorial' => __( 'A large lead story followed by a balanced two-column journal.', 'gravitas-coaching' ),
		'magazine'  => __( 'A dense, energetic news layout with prominent anchor stories.', 'gravitas-coaching' ),
		'masonry'   => __( 'An image-led, staggered layout for visual storytelling.', 'gravitas-coaching' ),
		'grid'      => __( 'A dependable two-column card grid for any coaching blog.', 'gravitas-coaching' ),
		'list'      => __( 'Wide horizontal stories with generous summaries and imagery.', 'gravitas-coaching' ),
		'minimal'   => __( 'A quiet, typography-forward reading list with compact images.', 'gravitas-coaching' ),
	);

	return isset( $descriptions[ $layout ] ) ? $descriptions[ $layout ] : '';
}

/**
 * Store demo values in the option source used by the front end.
 *
 * @param array<string, mixed> $updates Option values to merge.
 */
function elevora_update_demo_core_options( $updates ) {
	$options = (array) get_option( 'gravitas_core_options', array() );
	update_option( 'gravitas_core_options', array_merge( $options, $updates ), false );
}

function elevora_import_attachment( $key, $filename, $title ) {
	$known = (array) get_option( 'elevora_demo_attachments', array() );
	if ( ! empty( $known[ $key ] ) && get_post( $known[ $key ] ) ) {
		return absint( $known[ $key ] );
	}
	$file = ELEVORA_DIR . '/assets/images/' . $filename;
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$upload = wp_upload_bits( 'elevora-' . $filename, null, file_get_contents( $file ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $upload['type'],
			'post_title'     => sanitize_text_field( $title ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $title ) );
	$known[ $key ] = $attachment_id;
	update_option( 'elevora_demo_attachments', $known, false );
	return $attachment_id;
}

/**
 * Import the complete demo image collection.
 *
 * @return array<string, int>
 */
function elevora_import_demo_images() {
	return array(
		'hero'               => elevora_import_attachment( 'hero', 'hero-coaching.jpg', __( 'Coaching workshop', 'gravitas-coaching' ) ),
		'marcus'             => elevora_import_attachment( 'marcus', 'trainer-marcus.jpg', __( 'Leadership coach', 'gravitas-coaching' ) ),
		'priya'              => elevora_import_attachment( 'priya', 'trainer-priya.jpg', __( 'Image consultant', 'gravitas-coaching' ) ),
		'kenji'              => elevora_import_attachment( 'kenji', 'trainer-kenji.jpg', __( 'Communication coach', 'gravitas-coaching' ) ),
		'claire'             => elevora_import_attachment( 'claire', 'trainer-claire.jpg', __( 'Confidence coach', 'gravitas-coaching' ) ),
		'elena'              => elevora_import_attachment( 'elena', 'trainer-elena.jpg', __( 'Executive voice and keynote coach', 'gravitas-coaching' ) ),
		'david'              => elevora_import_attachment( 'david', 'trainer-david.jpg', __( 'Negotiation and boardroom coach', 'gravitas-coaching' ) ),
		'executive_woman' => elevora_import_attachment( 'stock-difficult-conversation', 'stock-difficult-conversation.jpg', __( 'Colleagues in a supportive coaching conversation', 'gravitas-coaching' ) ),
		'executive_vertical' => elevora_import_attachment( 'stock-meeting-preparation', 'stock-meeting-preparation.jpg', __( 'Colleagues preparing meeting objectives', 'gravitas-coaching' ) ),
		'style_coach' => elevora_import_attachment( 'stock-leadership-wardrobe', 'stock-leadership-wardrobe.jpg', __( 'Choosing a professional wardrobe', 'gravitas-coaching' ) ),
		'image_consultant' => elevora_import_attachment( 'stock-professional-image', 'stock-professional-image.jpg', __( 'Fashion professional reviewing a clothing color palette', 'gravitas-coaching' ) ),
		'boardroom' => elevora_import_attachment( 'stock-executive-presence', 'stock-executive-presence.jpg', __( 'Team leader briefing colleagues in a conference room', 'gravitas-coaching' ) ),
		'podcast_team' => elevora_import_attachment( 'stock-presentation-opening', 'stock-presentation-opening.jpg', __( 'Business presenter addressing an audience', 'gravitas-coaching' ) ),
		'podcast_inclusive' => elevora_import_attachment( 'stock-confident-speaking', 'stock-confident-speaking.jpg', __( 'Business coach presenting to a team', 'gravitas-coaching' ) ),
		'podcast_night' => elevora_import_attachment( 'stock-business-networking', 'stock-business-networking.jpg', __( 'Professionals taking part in a business networking event', 'gravitas-coaching' ) ),
		'brand_audit' => elevora_import_attachment( 'stock-personal-brand-audit', 'stock-personal-brand-audit.jpg', __( 'Professional reviewing her work and plans', 'gravitas-coaching' ) ),
	);
}

function elevora_demo_post( $post_type, $slug, $data, $meta = array(), $taxonomies = array(), $thumbnail_id = 0 ) {
	$existing = get_page_by_path( $slug, OBJECT, $post_type );

	/*
	 * Never claim or overwrite content that merely shares a demo slug. Demo
	 * posts are also immutable on subsequent imports so customer edits survive
	 * theme upgrades and an accidental re-run of the installer.
	 */
	if ( $existing && get_post_meta( $existing->ID, '_elevora_demo_content', true ) ) {
		$post_id = (int) $existing->ID;
		$demo_posts = array_map( 'absint', (array) get_option( 'elevora_demo_post_ids', array() ) );
		if ( ! in_array( $post_id, $demo_posts, true ) ) {
			$demo_posts[] = $post_id;
			update_option( 'elevora_demo_post_ids', $demo_posts, false );
		}
		return $post_id;
	}

	$postarr  = wp_parse_args(
		$data,
		array(
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => ucwords( str_replace( '-', ' ', $slug ) ),
			'post_content' => '',
		)
	);
	$post_id = wp_insert_post( wp_slash( $postarr ), true );
	if ( is_wp_error( $post_id ) ) {
		return 0;
	}
	update_post_meta( $post_id, '_elevora_demo_content', 1 );
	$demo_posts = array_map( 'absint', (array) get_option( 'elevora_demo_post_ids', array() ) );
	if ( ! in_array( $post_id, $demo_posts, true ) ) {
		$demo_posts[] = $post_id;
		update_option( 'elevora_demo_post_ids', $demo_posts, false );
	}
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}
	foreach ( $taxonomies as $taxonomy => $terms ) {
		wp_set_object_terms( $post_id, $terms, $taxonomy, false );
	}
	if ( $thumbnail_id ) {
		set_post_thumbnail( $post_id, $thumbnail_id );
	}
	return $post_id;
}

/**
 * Create the editable Elementor version of the Gravitas homepage.
 *
 * Existing user-created Elementor documents are never replaced. An existing
 * Gravitas demo document is retained and repaired by restoring the metadata
 * Elementor uses to recognize it.
 *
 * @param int $post_id Homepage post ID.
 */
function elevora_restore_native_demo_home( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

	$elementor_data = (string) get_post_meta( $post_id, '_elementor_data', true );
	$is_gravitas_demo = false !== strpos( $elementor_data, 'gvhero01' );
	if ( $elementor_data && ! $is_gravitas_demo ) {
		return;
	}

	$is_original_home_widget = false !== strpos( $elementor_data, 'gravitas-homepage' );
	if ( ! $is_original_home_widget ) {
		$home_settings = function_exists( 'elevora_homepage_defaults' ) ? elevora_homepage_defaults() : array();
		$elements      = array(
			array(
				'id'       => 'gvhome01',
				'elType'   => 'section',
				'settings' => array( 'content_width' => 'full_width', 'stretch_section' => 'section-stretched', 'gap' => 'no', 'css_classes' => 'gravitas-original-home-section' ),
				'elements' => array(
					array(
						'id'       => 'gvhomecl',
						'elType'   => 'column',
						'settings' => array( '_column_size' => 100, '_inline_size' => 100 ),
						'elements' => array(
							array( 'id' => 'gvhero01', 'elType' => 'widget', 'widgetType' => 'gravitas-homepage', 'settings' => $home_settings, 'elements' => array() ),
						),
					),
				),
			),
		);
		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
		$elementor_data = wp_json_encode( $elements );
	}

	if ( ! $elementor_data ) {
		$options    = wp_parse_args( (array) get_option( 'gravitas_core_options', array() ), array() );
		$image_id   = absint( isset( $options['hero_image_id'] ) ? $options['hero_image_id'] : get_theme_mod( 'elevora_hero_image', 0 ) );
		$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : ELEVORA_URI . '/assets/images/hero-coaching.jpg';
		$trainers   = get_post_type_archive_link( 'trainer' );
		$services   = get_post_type_archive_link( 'coaching_service' );
		$widget     = static function ( $id, $type, $settings ) {
			return array( 'id' => $id, 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => array() );
		};
		$column     = static function ( $id, $elements, $width = 100 ) {
			return array( 'id' => $id, 'elType' => 'column', 'settings' => array( '_column_size' => $width, '_inline_size' => $width ), 'elements' => $elements );
		};
		$section    = static function ( $id, $class, $columns, $settings = array() ) {
			return array( 'id' => $id, 'elType' => 'section', 'settings' => array_merge( array( 'css_classes' => $class, 'content_width' => 'boxed' ), $settings ), 'elements' => $columns );
		};

		$elements = array(
			$section( 'gvhero01', 'gravitas-elementor-hero', array( $column( 'gvherocl', array(
				$widget( 'gvheroey', 'heading', array( 'title' => elevora_get_setting( 'hero_eyebrow', 'elevora_hero_eyebrow', __( 'Soft skills. Stronger presence.', 'gravitas-coaching' ) ), 'header_size' => 'h6', 'title_color' => '#c6a454' ) ),
				$widget( 'gvherohd', 'heading', array( 'title' => elevora_get_setting( 'hero_title', 'elevora_hero_title', __( 'Unlock Your Next Level', 'gravitas-coaching' ) ) . ' ' . elevora_get_setting( 'hero_highlight', 'elevora_hero_highlight', __( 'of Growth and Leadership', 'gravitas-coaching' ) ), 'header_size' => 'h1', 'title_color' => '#ffffff' ) ),
				$widget( 'gvherotx', 'text-editor', array( 'editor' => '<p>' . esc_html( elevora_get_setting( 'hero_text', 'elevora_hero_text', __( 'Build your image, presence, and essential soft skills for a more confident professional future.', 'gravitas-coaching' ) ) ) . '</p>', 'text_color' => '#ffffff' ) ),
				$widget( 'gvherobt', 'button', array( 'text' => elevora_get_setting( 'hero_primary_text', 'elevora_hero_button', __( 'Find your consultant', 'gravitas-coaching' ) ), 'link' => array( 'url' => $trainers ), 'button_type' => 'warning' ) ),
				$widget( 'gvhero2b', 'button', array( 'text' => elevora_get_setting( 'hero_secondary_text', 'elevora_hero_alt_text', __( 'Explore programs', 'gravitas-coaching' ) ), 'link' => array( 'url' => $services ), 'button_type' => 'info' ) ),
			), 58 ) ), array( 'min_height' => array( 'unit' => 'px', 'size' => 560 ), 'background_background' => 'classic', 'background_image' => array( 'id' => $image_id, 'url' => $image_url ), 'background_position' => 'center center', 'background_size' => 'cover', 'background_overlay_background' => 'classic', 'background_overlay_color' => 'rgba(12,29,48,0.78)' ) ),
			$section( 'gvintro1', 'gravitas-elementor-intro', array( $column( 'gvintrocl', array(
				$widget( 'gvintroey', 'heading', array( 'title' => __( 'Coaching that meets the whole professional', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'gvintrohd', 'heading', array( 'title' => __( 'Grow the skills people notice and the confidence you feel.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'gvintrot', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Practical coaching, communication development, and image consulting come together in one focused experience.', 'gravitas-coaching' ) . '</p>' ) ),
			) ) ) ),
			$section( 'gvfind01', 'gravitas-elementor-finder', array( $column( 'gvfindcl', array( $widget( 'gvfindwd', 'gravitas-finder', array() ) ) ) ) ),
			$section( 'gvserv01', 'gravitas-elementor-services', array( $column( 'gvservcl', array(
				$widget( 'gvservey', 'heading', array( 'title' => __( 'Choose your focus', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'gvservhd', 'heading', array( 'title' => __( 'Practical programs for skills, style, and professional impact.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'gvservwd', 'gravitas-services', array( 'count' => 6 ) ),
			) ) ) ),
			$section( 'gvtrain1', 'gravitas-elementor-trainers', array( $column( 'gvtraincl', array(
				$widget( 'gvtraine', 'heading', array( 'title' => __( 'Meet the experts', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'gvtrainh', 'heading', array( 'title' => __( 'Consultants who connect inner confidence with visible presence.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'gvtrainw', 'gravitas-directory', array( 'count' => 4, 'columns' => 4 ) ),
			) ) ) ),
			$section( 'gvtest01', 'gravitas-elementor-testimonials', array( $column( 'gvtestcl', array(
				$widget( 'gvtestey', 'heading', array( 'title' => __( 'Client stories', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'gvtesthd', 'heading', array( 'title' => __( 'Progress feels different when it is yours.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'gvtestwd', 'gravitas-testimonials', array( 'count' => 3 ) ),
			) ) ) ),
			$section( 'gvblog01', 'gravitas-elementor-journal', array( $column( 'gvblogcl', array(
				$widget( 'gvblogey', 'heading', array( 'title' => __( 'The Gravitas journal', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'gvbloghd', 'heading', array( 'title' => __( 'Practical ideas for communication, style, and influence.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'gvblogwd', 'gravitas-posts', array( 'count' => 3, 'layout' => 'grid' ) ),
			) ) ) ),
		);

		update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	}

	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
	update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
	update_post_meta( $post_id, '_wp_page_template', 'default' );

	if ( did_action( 'elementor/loaded' ) && class_exists( '\\Elementor\\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

/**
 * Create or restore the editable Elementor document for the About page.
 *
 * @param int $post_id Post ID for the About page.
 * @param array<string, int> $images Imported attachment IDs.
 */
function elevora_restore_elementor_about( $post_id, $images = array() ) {
	if ( ! $post_id ) {
		return;
	}
	if ( get_post_meta( $post_id, '_elementor_data', true ) ) {
		return;
	}

	$widget = static function ( $id, $type, $settings ) {
		return array( 'id' => $id, 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => array() );
	};
	$column = static function ( $id, $elements, $width = 100 ) {
		return array( 'id' => $id, 'elType' => 'column', 'settings' => array( '_column_size' => $width, '_inline_size' => $width ), 'elements' => $elements );
	};
	$section = static function ( $id, $class, $columns, $settings = array() ) {
		return array( 'id' => $id, 'elType' => 'section', 'settings' => array_merge( array( 'css_classes' => $class, 'content_width' => 'boxed' ), $settings ), 'elements' => $columns );
	};

	$contact_page  = get_page_by_path( 'contact' );
	$contact_url   = $contact_page ? get_permalink( $contact_page ) : '#contact';
	$boardroom_id  = isset( $images['boardroom'] ) ? absint( $images['boardroom'] ) : 0;
	$consultant_id = isset( $images['image_consultant'] ) ? absint( $images['image_consultant'] ) : 0;
	$podcast_id    = isset( $images['podcast_team'] ) ? absint( $images['podcast_team'] ) : 0;

	$elements = array(
		$section( 'abhero01', 'page-hero about-elementor-hero', array(
			$column( 'abherocl', array(
				$widget( 'abheroey', 'heading', array( 'title' => __( 'About Gravitas', 'gravitas-coaching' ), 'header_size' => 'h6', 'title_color' => '#c59b3f' ) ),
				$widget( 'abherohd', 'heading', array( 'title' => __( 'Guiding Leaders to Embody Authentic Authority & Style', 'gravitas-coaching' ), 'header_size' => 'h1', 'title_color' => '#fcfbf8' ) ),
				$widget( 'abherotx', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Professional presence becomes powerful when skills, confidence, and image communicate the same message.', 'gravitas-coaching' ) . '</p>', 'text_color' => '#8c9eae' ) ),
			) )
		) ),
		$section( 'abintro1', 'about-section about-intro', array(
			$column( 'abintcl1', array(
				$widget( 'abimg01', 'image', array( 'image' => array( 'id' => $boardroom_id, 'url' => $boardroom_id ? wp_get_attachment_image_url( $boardroom_id, 'full' ) : ELEVORA_URI . '/assets/images/stock-executive-presence.jpg' ) ) ),
			), 55 ),
			$column( 'abintcl2', array(
				$widget( 'abimg02', 'image', array( 'image' => array( 'id' => $consultant_id, 'url' => $consultant_id ? wp_get_attachment_image_url( $consultant_id, 'full' ) : ELEVORA_URI . '/assets/images/stock-professional-image.jpg' ) ) ),
				$widget( 'ableadtx', 'text-editor', array( 'editor' => '<h3>' . esc_html__( 'The Gravitas Standard', 'gravitas-coaching' ) . '</h3><p>' . esc_html__( 'Gravitas Coaching was established on a singular principle: true executive influence requires both deep substance and conscious visual intention. We partner with founders, executives, and rising leaders to cultivate nonverbal authority, master persuasive communication, and curate an authentic wardrobe that commands trust in every room.', 'gravitas-coaching' ) . '</p>' ) ),
			), 45 )
		) ),
		$section( 'abpillars', 'about-section about-pillars-section', array(
			$column( 'abpilcl1', array(
				$widget( 'abpil1', 'icon-box', array( 'title_text' => __( 'Executive Presence', 'gravitas-coaching' ), 'description_text' => __( 'Cultivate composure under stress, commanding body language, and boardroom gravitas.', 'gravitas-coaching' ) ) ),
			), 25 ),
			$column( 'abpilcl2', array(
				$widget( 'abpil2', 'icon-box', array( 'title_text' => __( 'Wardrobe & Image Styling', 'gravitas-coaching' ), 'description_text' => __( 'Align silhouette, proportion, fabrics, and colors into a cohesive personal brand.', 'gravitas-coaching' ) ) ),
			), 25 ),
			$column( 'abpilcl3', array(
				$widget( 'abpil3', 'icon-box', array( 'title_text' => __( 'Strategic Communication', 'gravitas-coaching' ), 'description_text' => __( 'Structure persuasive arguments, project vocal resonance, and eliminate verbal filler.', 'gravitas-coaching' ) ) ),
			), 25 ),
			$column( 'abpilcl4', array(
				$widget( 'abpil4', 'icon-box', array( 'title_text' => __( 'Business Etiquette', 'gravitas-coaching' ), 'description_text' => __( 'Command executive dinners and delicate negotiations with diplomatic polish and fluency.', 'gravitas-coaching' ) ) ),
			), 25 ),
		) ),
		$section( 'abmethod', 'about-section about-philosophy', array(
			$column( 'abmetcl1', array(
				$widget( 'abimg03', 'image', array( 'image' => array( 'id' => $podcast_id, 'url' => $podcast_id ? wp_get_attachment_image_url( $podcast_id, 'full' ) : ELEVORA_URI . '/assets/images/stock-presentation-opening.jpg' ) ) ),
			), 50 ),
			$column( 'abmetcl2', array(
				$widget( 'abmethd', 'heading', array( 'title' => __( 'Presence Built from the Inside Out', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'abmettx', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'We do not believe in superficial makeovers or scripted performances. Real professional impact is born when internal clarity, behavioral habits, and external presentation communicate the exact same truth.', 'gravitas-coaching' ) . '</p><ul><li><strong>' . esc_html__( 'Authenticity Before Imitation', 'gravitas-coaching' ) . '</strong></li><li><strong>' . esc_html__( 'Real-Play Practice & Feedback', 'gravitas-coaching' ) . '</strong></li><li><strong>' . esc_html__( 'Sustainable Wardrobe Systems', 'gravitas-coaching' ) . '</strong></li></ul>' ) ),
				$widget( 'abmetbt', 'button', array( 'text' => __( 'Book a Consultation', 'gravitas-coaching' ), 'link' => array( 'url' => $contact_url ), 'button_type' => 'warning' ) ),
			), 50 )
		) ),
		$section( 'abteam01', 'about-section about-team', array(
			$column( 'abteamcl', array(
				$widget( 'abteamhd', 'heading', array( 'title' => __( 'Meet Our Leadership Consultants', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'abteamwd', 'gravitas-directory', array( 'count' => 6, 'columns' => 3 ) ),
			) )
		) ),
		$section( 'abcta001', 'about-section about-cta-section', array(
			$column( 'abctacl1', array(
				$widget( 'abctahd', 'heading', array( 'title' => __( 'Ready to Elevate Your Executive Presence?', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'abctatx', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Schedule a confidential 30-minute discovery conversation with our senior advisory team to discuss your goals.', 'gravitas-coaching' ) . '</p>' ) ),
				$widget( 'abctabt', 'button', array( 'text' => __( 'Schedule Discovery Call', 'gravitas-coaching' ), 'link' => array( 'url' => $contact_url ), 'button_type' => 'warning' ) ),
			) )
		) ),
	);

	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
	update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
}

/**
 * Create or restore the editable Elementor document for the Contact page.
 *
 * @param int                $post_id Post ID for the Contact page.
 * @param array<string, int> $images  Imported demo attachment IDs.
 */
function elevora_restore_elementor_contact( $post_id, $images = array() ) {
	if ( ! $post_id ) {
		return;
	}
	if ( get_post_meta( $post_id, '_elementor_data', true ) ) {
		return;
	}

	$widget = static function ( $id, $type, $settings ) {
		return array( 'id' => $id, 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => array() );
	};
	$column = static function ( $id, $elements, $width = 100 ) {
		return array( 'id' => $id, 'elType' => 'column', 'settings' => array( '_column_size' => $width, '_inline_size' => $width ), 'elements' => $elements );
	};
	$section = static function ( $id, $class, $columns, $settings = array() ) {
		return array( 'id' => $id, 'elType' => 'section', 'settings' => array_merge( array( 'css_classes' => $class, 'content_width' => 'boxed' ), $settings ), 'elements' => $columns );
	};
	$image = static function ( $key, $fallback ) use ( $images ) {
		$id = isset( $images[ $key ] ) ? absint( $images[ $key ] ) : 0;
		return array(
			'id'  => $id,
			'url' => $id ? wp_get_attachment_image_url( $id, 'full' ) : ELEVORA_URI . '/assets/images/' . $fallback,
		);
	};

	$hero_image      = $image( 'executive_woman', 'stock-difficult-conversation.jpg' );
	$presence_image  = $image( 'boardroom', 'stock-executive-presence.jpg' );
	$style_image     = $image( 'image_consultant', 'stock-professional-image.jpg' );
	$speaking_image  = $image( 'podcast_inclusive', 'stock-confident-speaking.jpg' );

	$elements = array(
		$section( 'cthero01', 'page-hero contact-elementor-hero', array(
			$column( 'ctherocl', array(
				$widget( 'ctheroey', 'heading', array( 'title' => __( 'Get In Touch', 'gravitas-coaching' ), 'header_size' => 'h6', 'title_color' => '#c59b3f' ) ),
				$widget( 'ctherohd', 'heading', array( 'title' => __( 'Contact Gravitas Coaching', 'gravitas-coaching' ), 'header_size' => 'h1', 'title_color' => '#fcfbf8' ) ),
				$widget( 'ctherotx', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Tell us where you want to feel more confident, credible, or polished.', 'gravitas-coaching' ) . '</p>', 'text_color' => '#8c9eae' ) ),
			) )
		), array( 'background_background' => 'classic', 'background_image' => $hero_image, 'background_position' => 'center center', 'background_size' => 'cover', 'background_overlay_background' => 'classic', 'background_overlay_color' => 'rgba(8, 27, 45, 0.78)' ) ),
		$section( 'ctgallery1', 'section contact-visual-section', array(
			$column( 'ctgalleryhd', array(
				$widget( 'ctgalleryey', 'heading', array( 'title' => __( 'How we can help', 'gravitas-coaching' ), 'header_size' => 'h6' ) ),
				$widget( 'ctgallerytl', 'heading', array( 'title' => __( 'Start the conversation with a clearer picture of what is possible.', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
			) ),
		) ),
		$section( 'ctgallery2', 'section contact-image-gallery', array(
			$column( 'ctgalleryc1', array(
				$widget( 'ctgalleryi1', 'image', array( 'image' => $presence_image, 'image_size' => 'large', 'caption_source' => 'custom', 'caption' => __( 'Executive presence', 'gravitas-coaching' ) ) ),
			), 34 ),
			$column( 'ctgalleryc2', array(
				$widget( 'ctgalleryi2', 'image', array( 'image' => $style_image, 'image_size' => 'large', 'caption_source' => 'custom', 'caption' => __( 'Professional image', 'gravitas-coaching' ) ) ),
			), 33 ),
			$column( 'ctgalleryc3', array(
				$widget( 'ctgalleryi3', 'image', array( 'image' => $speaking_image, 'image_size' => 'large', 'caption_source' => 'custom', 'caption' => __( 'Confident communication', 'gravitas-coaching' ) ) ),
			), 33 ),
		) ),
		$section( 'ctmain01', 'section contact-section', array(
			$column( 'ctinfocl', array(
				$widget( 'ctinfohd', 'heading', array( 'title' => __( 'Start with a conversation', 'gravitas-coaching' ), 'header_size' => 'h2' ) ),
				$widget( 'ctinfotx', 'text-editor', array( 'editor' => '<p>' . esc_html__( 'Whether you are seeking personal coaching, an executive presence audit, or team workshops for your leadership cohort, we look forward to hearing from you.', 'gravitas-coaching' ) . '</p><div class="contact-card-info" style="margin-top:24px;line-height:2.2;"><strong>' . esc_html__( 'Email:', 'gravitas-coaching' ) . '</strong> <a href="mailto:hello@example.com">hello@example.com</a><br><strong>' . esc_html__( 'Phone:', 'gravitas-coaching' ) . '</strong> +1 (800) 555-0144<br><strong>' . esc_html__( 'Headquarters:', 'gravitas-coaching' ) . '</strong> New York, London, Singapore<br><strong>' . esc_html__( 'Hours:', 'gravitas-coaching' ) . '</strong> Mon–Fri, 9:00 AM – 6:00 PM EST</div>' ) ),
			), 45 ),
			$column( 'ctformcl', array(
				$widget( 'ctformhd', 'heading', array( 'title' => __( 'Send an Inquiry', 'gravitas-coaching' ), 'header_size' => 'h3' ) ),
				$widget( 'ctformtx', 'text-editor', array( 'editor' => '<form class="contact-form-styled" onsubmit="event.preventDefault(); alert(\'' . esc_js( __( 'Thank you! Your inquiry has been sent.', 'gravitas-coaching' ) ) . '\'); this.reset();" style="display:flex;flex-direction:column;gap:16px;"><label><span>' . esc_html__( 'Your Name', 'gravitas-coaching' ) . ' *</span><input type="text" required style="width:100%;padding:12px;border:1px solid #d5dbdb;border-radius:8px;"></label><label><span>' . esc_html__( 'Work Email', 'gravitas-coaching' ) . ' *</span><input type="email" required style="width:100%;padding:12px;border:1px solid #d5dbdb;border-radius:8px;"></label><label><span>' . esc_html__( 'Area of Focus', 'gravitas-coaching' ) . '</span><select style="width:100%;padding:12px;border:1px solid #d5dbdb;border-radius:8px;"><option>' . esc_html__( 'Executive Presence', 'gravitas-coaching' ) . '</option><option>' . esc_html__( 'Image Consulting & Personal Brand', 'gravitas-coaching' ) . '</option><option>' . esc_html__( 'Strategic Communication & Keynotes', 'gravitas-coaching' ) . '</option><option>' . esc_html__( 'Team / Enterprise Workshop', 'gravitas-coaching' ) . '</option></select></label><label><span>' . esc_html__( 'Your Message', 'gravitas-coaching' ) . '</span><textarea rows="4" style="width:100%;padding:12px;border:1px solid #d5dbdb;border-radius:8px;"></textarea></label><button type="submit" class="button button--accent" style="align-self:flex-start;padding:12px 28px;cursor:pointer;">' . esc_html__( 'Send Message', 'gravitas-coaching' ) . '</button></form>' ) ),
			), 55 )
		) ),
	);

	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
	update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
}

/**
 * Repair demo homepages created by versions that removed Elementor metadata.
 */
function elevora_maybe_initialize_elementor_home() {
	$home_id = absint( get_option( 'page_on_front' ) );
	if ( ! $home_id || ! get_post_meta( $home_id, '_elevora_demo_content', true ) ) {
		return;
	}
	$elementor_data = (string) get_post_meta( $home_id, '_elementor_data', true );
	if ( false !== strpos( $elementor_data, 'gravitas-homepage' ) ) {
		return;
	}

	elevora_restore_native_demo_home( $home_id );
}

/**
 * Create the shared journal content used by every blog demo.
 *
 * @param array<string, int> $images Imported attachment IDs.
 */
function elevora_import_blog_posts( $images ) {
	$posts = array(
		'executive-presence-signals'     => array( 'Seven signals that strengthen executive presence', 'Leadership', 'Executive presence is built through small, observable choices in language, voice, posture, preparation, and response.', 'boardroom' ),
		'professional-image-foundation'  => array( 'Build a professional image without losing your identity', 'Personal Style', 'A strong professional image should clarify who you are, not disguise it. Start with role, context, color, fit, and consistency.', 'image_consultant' ),
		'speak-with-more-confidence'      => array( 'How to sound more confident without speaking louder', 'Communication', 'Confidence is often heard in structure, pace, pauses, and clean endings rather than volume.', 'podcast_inclusive' ),
		'lead-a-difficult-conversation'   => array( 'A calmer way to lead difficult conversations', 'Leadership', 'Prepare for the outcome, name the issue without drama, and make space for a response you may not expect.', 'executive_woman' ),
		'wardrobe-for-visible-leadership' => array( 'A practical wardrobe formula for visible leadership', 'Personal Style', 'A useful professional wardrobe reduces friction and creates a consistent visual message across high-stakes moments.', 'style_coach' ),
		'better-presentation-openings'    => array( 'Five presentation openings that earn attention', 'Communication', 'The first minute tells your audience whether to lean in. Begin with relevance, tension, or a precise promise.', 'podcast_team' ),
		'confidence-before-big-meeting'   => array( 'What to do in the ten minutes before a big meeting', 'Career Growth', 'A short preparation ritual can settle your pace, sharpen your message, and move attention away from self-judgment.', 'executive_vertical' ),
		'personal-brand-audit'            => array( 'The thirty-minute personal brand audit', 'Career Growth', 'Compare what you want to be known for with the evidence people can see in your communication, work, and presence.', 'brand_audit' ),
		'networking-without-performance'  => array( 'Networking without feeling like you are performing', 'Career Growth', 'Replace the pressure to impress with curiosity, useful questions, and a clear way to describe what matters to you.', 'podcast_night' ),
	);

	foreach ( $posts as $slug => $post ) {
		$image_id = isset( $images[ $post[3] ] ) ? absint( $images[ $post[3] ] ) : 0;
		elevora_demo_post(
			'post',
			$slug,
			array(
				'post_title'   => $post[0],
				'post_excerpt' => $post[2],
				'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $post[2] ) . '</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Start with what is true now</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Useful change begins with an honest picture of your current situation. Notice where your energy goes, which conversations you avoid, and what you keep telling yourself must happen first.</p><!-- /wp:paragraph --><!-- wp:heading --><h2>Choose one meaningful experiment</h2><!-- /wp:heading --><!-- wp:paragraph --><p>You do not need a complete reinvention. Select one action small enough to try this week and significant enough to teach you something.</p><!-- /wp:paragraph --><!-- wp:quote --><blockquote class="wp-block-quote"><p>Progress becomes easier to trust when you can see it in your own behavior.</p></blockquote><!-- /wp:quote -->',
			),
			array(),
			array( 'category' => $post[1] ),
			$image_id
		);
	}
}

/**
 * Import a focused blog starter demo.
 *
 * @param string $layout Blog layout slug.
 */
function elevora_import_blog_demo( $layout ) {
	$layout = elevora_sanitize_blog_layout( $layout );
	$images = elevora_import_demo_images();

	elevora_import_blog_posts( $images );

	$blog_id = elevora_demo_post( 'page', 'journal', array( 'post_title' => __( 'Journal', 'gravitas-coaching' ), 'post_content' => '' ) );
	$home_id = (int) get_option( 'page_on_front' );
	if ( ! $home_id ) {
		$home_id = elevora_demo_post( 'page', 'home', array( 'post_title' => __( 'Home', 'gravitas-coaching' ), 'post_content' => '<!-- wp:paragraph --><p>' . esc_html__( 'Welcome to Gravitas Coaching.', 'gravitas-coaching' ) . '</p><!-- /wp:paragraph -->' ) );
	}
	if ( get_post_meta( $home_id, '_elevora_demo_content', true ) ) {
		elevora_restore_native_demo_home( $home_id );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $blog_id );
	update_option( 'posts_per_page', 9 );
	update_option( 'permalink_structure', '/%postname%/' );
	set_theme_mod( 'elevora_blog_layout', $layout );
	set_theme_mod( 'elevora_blog_sidebar', 'minimal' !== $layout );
	elevora_update_demo_core_options(
		array(
			'blog_layout'      => $layout,
			'sidebar_position' => 'minimal' === $layout ? 'none' : 'right',
		)
	);
	update_option( 'elevora_demo_imported_at', current_time( 'mysql' ), false );
	global $wp_rewrite;
	if ( is_object( $wp_rewrite ) ) {
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules( true );
	} else {
		flush_rewrite_rules();
	}
}

function elevora_import_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to import demo content.', 'gravitas-coaching' ) );
	}
	check_admin_referer( 'elevora_import_demo', 'elevora_import_nonce' );
	$demo = isset( $_POST['demo'] ) ? sanitize_key( wp_unslash( $_POST['demo'] ) ) : 'complete';

	if ( 'complete' !== $demo ) {
		elevora_import_blog_demo( $demo );
		wp_safe_redirect( add_query_arg( array( 'page' => 'elevora-demo-import', 'imported' => '1' ), admin_url( 'themes.php' ) ) );
		exit;
	}

	$images = elevora_import_demo_images();

	$terms = array(
		'trainer_specialization' => array( 'Executive Presence', 'Professional Image', 'Communication', 'Public Speaking', 'Personal Branding', 'Business Etiquette', 'Leadership Soft Skills', 'Career Readiness' ),
		'trainer_location'       => array( 'New York', 'London', 'Singapore', 'Online' ),
		'coaching_format'        => array( 'Online', 'In Person', 'Hybrid' ),
		'trainer_language'       => array( 'English', 'Hindi', 'Japanese' ),
		'service_category'       => array( 'Soft Skills', 'Image Consulting', 'Leadership', 'Career Readiness' ),
		'service_delivery'       => array( 'One to One', 'Group Program', 'Workshop', 'Online' ),
		'service_audience'       => array( 'Individuals', 'Leaders', 'Teams', 'Organizations' ),
	);
	foreach ( $terms as $taxonomy => $names ) {
		foreach ( $names as $name ) {
			if ( ! term_exists( $name, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy );
			}
		}
	}

	$service_data = array(
		'executive-presence' => array(
			'title' => 'Executive Presence Intensive', 'summary' => 'Build the composure, clarity, and credibility to lead visibly when the stakes are high.', 'duration' => '8 weeks', 'price' => 'From $1,200', 'icon' => 'compass', 'category' => 'Leadership', 'delivery' => array( 'One to One', 'Online' ), 'audience' => array( 'Leaders', 'Organizations' ),
			'content' => '<h2>Lead before you begin speaking</h2><p>Executive presence is the alignment of confidence, communication, and visible credibility. This intensive helps you understand the signals you send and strengthen the ones that build trust.</p><h3>What we will work through</h3><ul><li>Composure and confident body language</li><li>Concise leadership communication</li><li>Meeting and stakeholder presence</li><li>A personal presence plan for high-visibility moments</li></ul><blockquote>Gravitas is not performance. It is clarity made visible.</blockquote>',
		),
		'professional-image' => array(
			'title' => 'Professional Image Consulting', 'summary' => 'Create a polished, authentic visual identity aligned with your role, goals, and lifestyle.', 'duration' => '3–5 sessions', 'price' => 'From $750', 'icon' => 'spark', 'category' => 'Image Consulting', 'delivery' => array( 'One to One', 'Online' ), 'audience' => array( 'Individuals', 'Leaders' ),
			'content' => '<h2>Make your visual message intentional</h2><p>Your wardrobe, grooming, color choices, and fit communicate before your introduction does. Build an image system that feels credible, current, and genuinely yours.</p><h3>Your consultation includes</h3><ul><li>Personal style and professional image assessment</li><li>Color, proportion, fit, and wardrobe guidance</li><li>Grooming and finishing details</li><li>A practical shopping and outfit strategy</li></ul>',
		),
		'communication-public-speaking' => array(
			'title' => 'Communication & Public Speaking', 'summary' => 'Speak with structure, warmth, and conviction in meetings, presentations, and conversations.', 'duration' => '6 weeks', 'price' => 'From $900', 'icon' => 'chart', 'category' => 'Soft Skills', 'delivery' => array( 'One to One', 'Workshop', 'Online' ), 'audience' => array( 'Individuals', 'Leaders', 'Teams' ),
			'content' => '<h2>Turn ideas into messages people remember</h2><p>Strengthen your verbal delivery, storytelling, listening, and nonverbal communication through practical rehearsal and focused feedback.</p><ul><li>Message structure and executive summaries</li><li>Voice, pace, posture, and gesture</li><li>Presentation confidence and audience connection</li><li>Difficult conversations and active listening</li></ul>',
		),
		'business-etiquette' => array(
			'title' => 'Business Etiquette & Personal Brand', 'summary' => 'Navigate professional situations with polish while building a reputation that feels authentic.', 'duration' => '4 weeks', 'price' => 'From $650', 'icon' => 'people', 'category' => 'Image Consulting', 'delivery' => array( 'One to One', 'Workshop' ), 'audience' => array( 'Individuals', 'Teams', 'Organizations' ),
			'content' => '<h2>Confidence for the moments without a script</h2><p>Learn the contemporary etiquette, relationship skills, and personal-brand habits that make professional interactions smoother and more memorable.</p><ul><li>Introductions, networking, and conversation skills</li><li>Meeting, dining, digital, and cross-cultural etiquette</li><li>Personal brand positioning</li><li>Professional reputation and online presence</li></ul>',
		),
		'leadership-soft-skills' => array(
			'title' => 'Leadership Soft Skills Lab', 'summary' => 'Develop the communication, feedback, empathy, and influence skills that help people do their best work.', 'duration' => '6 workshops', 'price' => 'Custom proposal', 'icon' => 'target', 'category' => 'Leadership', 'delivery' => array( 'Group Program', 'Workshop', 'Online' ), 'audience' => array( 'Leaders', 'Teams', 'Organizations' ),
			'content' => '<h2>Make human skills a leadership advantage</h2><p>A practical program for managers and emerging leaders who want stronger conversations, healthier accountability, and greater influence.</p><ul><li>Emotional intelligence and self-awareness</li><li>Feedback, coaching, and difficult conversations</li><li>Influence without authority</li><li>Collaboration and conflict navigation</li></ul>',
		),
		'career-readiness' => array(
			'title' => 'Interview & Career Presence', 'summary' => 'Present your experience with confidence through a clear story, polished image, and credible interview presence.', 'duration' => '4 weeks', 'price' => 'From $600', 'icon' => 'heart', 'category' => 'Career Readiness', 'delivery' => array( 'One to One', 'Online' ), 'audience' => array( 'Individuals' ),
			'content' => '<h2>Show the value behind your experience</h2><p>Bring your communication and professional image together for interviews, promotions, networking, and career transitions.</p><ul><li>Career narrative and personal introduction</li><li>Interview answers and mock practice</li><li>Role-appropriate wardrobe and grooming</li><li>Networking confidence and follow-up</li></ul>',
		),
	);

	$service_ids = array();
	$service_images = array(
		'executive-presence'             => 'boardroom',
		'professional-image'             => 'image_consultant',
		'communication-public-speaking'  => 'podcast_inclusive',
		'business-etiquette'             => 'style_coach',
		'leadership-soft-skills'         => 'podcast_team',
		'career-readiness'                => 'executive_woman',
	);
	$order       = 0;
	foreach ( $service_data as $slug => $service ) {
		$service_ids[ $slug ] = elevora_demo_post(
			'coaching_service',
			$slug,
			array( 'post_title' => $service['title'], 'post_content' => $service['content'], 'post_excerpt' => $service['summary'], 'menu_order' => $order++ ),
			array( 'elevora_service_summary' => $service['summary'], 'elevora_service_duration' => $service['duration'], 'elevora_service_price' => $service['price'], 'elevora_service_icon' => $service['icon'], 'elevora_service_featured' => 1 ),
			array( 'service_category' => $service['category'], 'service_delivery' => $service['delivery'], 'service_audience' => $service['audience'] ),
			$images[ $service_images[ $slug ] ]
		);
	}

	$trainer_data = array(
		'marcus-reed' => array( 'name' => 'Marcus Reed', 'image' => 'marcus', 'role' => 'Executive Presence & Leadership Coach', 'tagline' => 'Lead with calm authority when visibility and stakes are high.', 'experience' => 14, 'rate' => 240, 'rating' => 4.9, 'sessions' => 1860, 'availability' => 'Available for new clients this month', 'location' => 'New York', 'formats' => array( 'Online', 'In Person' ), 'languages' => array( 'English' ), 'specializations' => array( 'Executive Presence', 'Communication', 'Leadership Soft Skills' ), 'services' => array( 'executive-presence', 'communication-public-speaking', 'leadership-soft-skills' ), 'credentials' => "ICF Professional Certified Coach (PCC)\nExecutive Presence Practitioner\nFormer Fortune 100 People & Culture Executive\nMSc Organizational Psychology", 'content' => '<p>Marcus works with senior and emerging leaders who want their presence to match the quality of their thinking. His coaching combines direct reflection, practical rehearsal, and focused feedback on communication and nonverbal impact.</p><p>Before coaching, Marcus led leadership and organizational development teams across technology and professional services. He understands boardrooms, stakeholder pressure, and the challenge of remaining authentic while becoming more visible.</p><h3>Consulting approach</h3><p>Expect thoughtful challenge, real-play practice, and clear observations about the signals your words, voice, posture, and decisions send.</p>' ),
		'priya-nair' => array( 'name' => 'Priya Nair', 'image' => 'priya', 'role' => 'Image Consultant & Personal Brand Strategist', 'tagline' => 'Create a visual identity that feels polished, credible, and unmistakably yours.', 'experience' => 11, 'rate' => 180, 'rating' => 5.0, 'sessions' => 1420, 'availability' => 'Two consultations available next week', 'location' => 'London', 'formats' => array( 'Online', 'Hybrid' ), 'languages' => array( 'English', 'Hindi' ), 'specializations' => array( 'Professional Image', 'Personal Branding', 'Business Etiquette', 'Career Readiness' ), 'services' => array( 'professional-image', 'business-etiquette', 'career-readiness' ), 'credentials' => "Certified Image Consultant\nPersonal Branding Strategist\nColor and Style Analysis Practitioner\nFormer Global Talent Partner", 'content' => '<p>Priya helps professionals align how they look with how they want to be known. Her consultations move beyond fashion to connect color, fit, grooming, wardrobe, behavior, and personal brand.</p><p>Clients value her practical eye and her ability to make style decisions feel clear rather than overwhelming. Every recommendation considers role, culture, lifestyle, comfort, and ambition.</p><h3>Consulting approach</h3><p>Priya combines objective image analysis with an encouraging, highly personal process. The goal is a repeatable system, not a temporary makeover.</p>' ),
		'kenji-sato' => array( 'name' => 'Kenji Sato', 'image' => 'kenji', 'role' => 'Communication & Cross-Cultural Skills Coach', 'tagline' => 'Communicate with clarity across roles, rooms, and cultures.', 'experience' => 9, 'rate' => 165, 'rating' => 4.8, 'sessions' => 980, 'availability' => 'Welcoming online clients worldwide', 'location' => 'Singapore', 'formats' => array( 'Online' ), 'languages' => array( 'English', 'Japanese' ), 'specializations' => array( 'Communication', 'Public Speaking', 'Business Etiquette', 'Leadership Soft Skills' ), 'services' => array( 'communication-public-speaking', 'business-etiquette', 'leadership-soft-skills' ), 'credentials' => "ICF Associate Certified Coach (ACC)\nCross-Cultural Communication Facilitator\nCertified Public Speaking Coach\nFormer Product & Operations Leader", 'content' => '<p>Kenji helps professionals communicate clearly in multicultural, high-pressure environments. His work is especially useful for managers, presenters, and client-facing teams navigating different expectations and communication styles.</p><p>Drawing on coaching, facilitation, and international leadership experience, Kenji turns communication concepts into behaviors clients can practice immediately.</p><h3>Coaching approach</h3><p>Calm, specific, and grounded in real situations. Sessions include rehearsal, feedback, and small adjustments that create a noticeable shift in clarity and confidence.</p>' ),
		'claire-bennett' => array( 'name' => 'Claire Bennett', 'image' => 'claire', 'role' => 'Confidence & Personal Impact Coach', 'tagline' => 'Build the self-belief and visible presence to step forward with conviction.', 'experience' => 16, 'rate' => 210, 'rating' => 4.9, 'sessions' => 2140, 'availability' => 'Next opening in two weeks', 'location' => 'Online', 'formats' => array( 'Online', 'In Person' ), 'languages' => array( 'English' ), 'specializations' => array( 'Executive Presence', 'Professional Image', 'Personal Branding', 'Career Readiness' ), 'services' => array( 'executive-presence', 'professional-image', 'career-readiness' ), 'credentials' => "ICF Professional Certified Coach (PCC)\nCertified Image Consultant\nPersonal Impact and Presence Facilitator\nMA Applied Positive Psychology", 'content' => '<p>Claire supports professionals at visible transition points: a promotion, leadership role, important interview, public platform, or career return. She helps clients strengthen inner confidence and express it through communication, body language, and personal presentation.</p><p>Her style is warm, perceptive, and refreshingly direct. Clients often describe feeling both deeply understood and productively stretched.</p><h3>Coaching approach</h3><p>Claire integrates confidence coaching with practical personal-impact work, helping each client create a presence that feels natural, intentional, and sustainable.</p>' ),
		'elena-rostova'  => array( 'name' => 'Elena Rostova', 'image' => 'elena', 'role' => 'Executive Voice & Keynote Poise Coach', 'tagline' => 'Own the room with vocal resonance, poise, and commanding storytelling.', 'experience' => 13, 'rate' => 225, 'rating' => 4.9, 'sessions' => 1640, 'availability' => 'Accepting 3 executive clients this month', 'location' => 'London', 'formats' => array( 'Online', 'In Person' ), 'languages' => array( 'English' ), 'specializations' => array( 'Executive Presence', 'Communication', 'Public Speaking' ), 'services' => array( 'executive-presence', 'communication-public-speaking' ), 'credentials' => "MA Voice & Performance Coaching\nCertified High-Stakes Keynote Strategist\nFormer West End & TEDx Senior Vocal Coach\nICF Certified Coach", 'content' => '<p>Elena specializes in helping leaders project vocal depth, eliminate nervous vocal fatigue, and command international auditoriums and virtual broadcasts with unshakeable poise.</p><p>Her signature methodology connects breath control, cadence, resonant pitch, and nonverbal posture into an authentic leadership presence that keeps audiences captivated from the opening sentence.</p><h3>Coaching approach</h3><p>Practical, acoustically grounded, and deeply empowering. Elena uses high-definition audio/video playback and real-time cadence training to unlock your natural vocal authority.</p>' ),
		'david-sterling' => array( 'name' => 'David Sterling', 'image' => 'david', 'role' => 'Strategic Negotiation & Poise Consultant', 'tagline' => 'Navigate intense stakeholder negotiations and high-pressure stakes with composure.', 'experience' => 18, 'rate' => 260, 'rating' => 5.0, 'sessions' => 2300, 'availability' => 'Consultations open for Q4 leadership tracks', 'location' => 'New York', 'formats' => array( 'In Person', 'Hybrid' ), 'languages' => array( 'English' ), 'specializations' => array( 'Executive Presence', 'Business Etiquette', 'Leadership Soft Skills' ), 'services' => array( 'executive-presence', 'business-etiquette', 'leadership-soft-skills' ), 'credentials' => "Harvard PON Negotiation Master Class Alum\nFormer Chief Legal & Commercial Strategy Partner\nSenior Executive Advisor to Global Tech Boards\nICF Master Certified Coach (MCC)", 'content' => '<p>David coaches senior executives, enterprise founders, and partners facing high-consequence negotiations, merger integrations, and board scrutiny.</p><p>With nearly two decades of executive advisory experience, David teaches leaders how to maintain tactical patience, master non-defensive questioning, and project gravitational authority even when negotiations become fraught.</p><h3>Coaching approach</h3><p>Rigorous, discreet, and intensely tactical. David simulates adversarial negotiation scenarios with targeted behavioral feedback, leaving you prepared for every curveball.</p>' ),
	);

	$trainer_ids = array();
	$order       = 0;
	foreach ( $trainer_data as $slug => $trainer ) {
		$related_services = array_values( array_filter( array_map( function( $key ) use ( $service_ids ) { return isset( $service_ids[ $key ] ) ? $service_ids[ $key ] : 0; }, $trainer['services'] ) ) );
		$trainer_ids[ $slug ] = elevora_demo_post(
			'trainer',
			$slug,
			array( 'post_title' => $trainer['name'], 'post_content' => $trainer['content'], 'post_excerpt' => $trainer['tagline'], 'menu_order' => $order++ ),
			array( 'elevora_role' => $trainer['role'], 'elevora_tagline' => $trainer['tagline'], 'elevora_experience' => $trainer['experience'], 'elevora_hourly_rate' => $trainer['rate'], 'elevora_rating' => $trainer['rating'], 'elevora_sessions' => $trainer['sessions'], 'elevora_availability' => $trainer['availability'], 'elevora_booking_url' => '#contact', 'elevora_credentials' => $trainer['credentials'], 'elevora_trainer_services' => $related_services, 'elevora_portfolio' => array_values( array_filter( array( $images['hero'], $images[ $trainer['image'] ], $images['boardroom'], $images['podcast_inclusive'] ) ) ) ),
			array( 'trainer_location' => $trainer['location'], 'coaching_format' => $trainer['formats'], 'trainer_language' => $trainer['languages'], 'trainer_specialization' => $trainer['specializations'] ),
			$images[ $trainer['image'] ]
		);
	}

	foreach ( $service_data as $slug => $service ) {
		$related = array();
		foreach ( $trainer_data as $trainer_slug => $trainer ) {
			if ( in_array( $slug, $trainer['services'], true ) && ! empty( $trainer_ids[ $trainer_slug ] ) ) {
				$related[] = $trainer_ids[ $trainer_slug ];
			}
		}
		update_post_meta( $service_ids[ $slug ], 'elevora_service_trainers', $related );
	}

	$testimonials = array(
		array( 'Maya Thompson', 'VP, Customer Experience', 'Northstar Labs', 'I now enter senior meetings with a clear message, a stronger voice, and a presence that feels like me.' ),
		array( 'Daniel Ortiz', 'Product Director', 'Independent', 'The feedback was precise and practical. Within weeks, my presentations felt calmer and far more persuasive.' ),
		array( 'Amelia Wright', 'Founder', 'Field Notes Studio', 'My wardrobe finally supports my role. Getting ready is easier, and I feel consistently polished without feeling styled.' ),
	);
	foreach ( $testimonials as $index => $testimonial ) {
		elevora_demo_post( 'testimonial', sanitize_title( $testimonial[0] ), array( 'post_title' => $testimonial[0], 'post_content' => '<p>' . esc_html( $testimonial[3] ) . '</p>', 'menu_order' => $index ), array( 'elevora_client_role' => $testimonial[1], 'elevora_client_company' => $testimonial[2], 'elevora_testimonial_rating' => 5 ) );
	}

	$home_id = elevora_demo_post( 'page', 'home', array( 'post_title' => 'Home', 'post_content' => '<!-- wp:paragraph --><p>Gravitas Coaching soft skills and image consulting homepage.</p><!-- /wp:paragraph -->' ) );
	elevora_restore_native_demo_home( $home_id );
	$blog_id = elevora_demo_post( 'page', 'journal', array( 'post_title' => 'Journal', 'post_content' => '' ) );
	$about_id = elevora_demo_post( 'page', 'about', array( 'post_title' => 'About', 'post_excerpt' => 'Professional presence becomes powerful when skills, confidence, and image communicate the same message.', 'post_content' => '' ), array( '_wp_page_template' => 'page-about.php' ), array(), $images['boardroom'] );
	update_post_meta( $about_id, '_wp_page_template', 'page-about.php' );
	elevora_restore_elementor_about( $about_id, $images );
	$contact_id = elevora_demo_post( 'page', 'contact', array( 'post_title' => 'Contact', 'post_excerpt' => 'Tell us where you want to feel more confident, credible, or polished.', 'post_content' => '<h2>Start with a conversation</h2><p>Email <a href="mailto:hello@example.com">hello@example.com</a> or call +1 (800) 555-0144. We typically reply within one business day.</p><h3>For organizations</h3><p>Ask about executive presence, communication, business etiquette, personal branding, and image consulting programs for teams.</p>' ) );
	elevora_restore_elementor_contact( $contact_id, $images );
	update_option( 'elevora_video_demo_pending', 1, false );
	elevora_repair_video_demo( $images );

	$posts = array(
		'executive-presence-signals' => array( 'Seven signals that strengthen executive presence', 'Executive presence is built through small, observable choices in language, voice, posture, preparation, and response.', $images['marcus'] ),
		'professional-image-foundation' => array( 'Build a professional image without losing your identity', 'A strong professional image should clarify who you are, not disguise it. Start with role, context, color, fit, and consistency.', $images['priya'] ),
		'speak-with-more-confidence' => array( 'How to sound more confident without speaking louder', 'Confidence is often heard in structure, pace, pauses, and clean endings—not volume.', $images['kenji'] ),
	);
	foreach ( $posts as $slug => $post ) {
		elevora_demo_post( 'post', $slug, array( 'post_title' => $post[0], 'post_excerpt' => $post[1], 'post_content' => '<p>' . esc_html( $post[1] ) . '</p><h2>Start with what is true now</h2><p>Useful change begins with an honest picture of your current situation. Notice where your energy goes, which conversations you avoid, and what you keep telling yourself must happen first.</p><h2>Choose one meaningful experiment</h2><p>You do not need a complete reinvention. Select one action small enough to try this week and significant enough to teach you something. Reflection turns that action into evidence.</p><blockquote>Progress becomes easier to trust when you can see it in your own behavior.</blockquote><h2>Make the learning visible</h2><p>At the end of the week, ask what changed, what surprised you, and what you want to repeat. This is how momentum becomes a practice rather than a temporary feeling.</p>' ), array(), array( 'category' => 'Perspective' ), $post[2] );
	}
	elevora_import_blog_posts( $images );
	set_theme_mod( 'elevora_blog_layout', 'editorial' );
	set_theme_mod( 'elevora_blog_sidebar', true );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $blog_id );
	update_option( 'posts_per_page', 9 );
	update_option( 'permalink_structure', '/%postname%/' );

	$menu_name = 'Gravitas Primary';
	$menu      = wp_get_nav_menu_object( $menu_name );
	$menu_id   = $menu ? $menu->term_id : wp_create_nav_menu( $menu_name );
	$menu_items = array(
		array( 'Home', 'post_type', 'page', $home_id, '' ),
		array( 'Consultants', 'custom', 'custom', 0, get_post_type_archive_link( 'trainer' ) ),
		array( 'Programs', 'custom', 'custom', 0, get_post_type_archive_link( 'coaching_service' ) ),
		array( 'Videos', 'custom', 'custom', 0, post_type_exists( 'gravitas_video' ) ? get_post_type_archive_link( 'gravitas_video' ) : home_url( '/videos/' ) ),
		array( 'About', 'post_type', 'page', $about_id, '' ),
		array( 'Journal', 'post_type', 'page', $blog_id, '' ),
		array( 'Contact', 'post_type', 'page', $contact_id, '' ),
	);
	$existing_items = wp_get_nav_menu_items( $menu_id );
	$existing_titles = $existing_items ? wp_list_pluck( $existing_items, 'title' ) : array();
	foreach ( $menu_items as $position => $item ) {
		if ( in_array( $item[0], $existing_titles, true ) ) {
			continue;
		}
		$args = array( 'menu-item-title' => $item[0], 'menu-item-status' => 'publish', 'menu-item-position' => $position + 1, 'menu-item-type' => $item[1] );
		if ( 'post_type' === $item[1] ) {
			$args['menu-item-object']    = $item[2];
			$args['menu-item-object-id'] = $item[3];
		} else {
			$args['menu-item-url']    = $item[4];
			$args['menu-item-object'] = 'custom';
		}
		wp_update_nav_menu_item( $menu_id, 0, $args );
	}
	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	$locations['footer']  = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
	set_theme_mod( 'elevora_hero_image', $images['hero'] );
	set_theme_mod( 'elevora_hero_url', get_post_type_archive_link( 'trainer' ) );
	set_theme_mod( 'elevora_hero_alt_url', get_post_type_archive_link( 'coaching_service' ) );
	set_theme_mod( 'elevora_header_url', get_permalink( $contact_id ) );
	elevora_update_demo_core_options(
		array(
			'hero_image_id'     => absint( $images['hero'] ),
			'hero_primary_url'  => get_post_type_archive_link( 'trainer' ),
			'hero_secondary_url' => get_post_type_archive_link( 'coaching_service' ),
			'header_cta_url'    => get_permalink( $contact_id ),
			'blog_layout'       => 'editorial',
			'sidebar_position'  => isset( $_POST['elevora_setup_running'] ) ? 'none' : 'right',
		)
	);

	update_option( 'elevora_demo_imported_at', current_time( 'mysql' ), false );
	global $wp_rewrite;
	if ( is_object( $wp_rewrite ) ) {
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$wp_rewrite->flush_rules( true );
	} else {
		flush_rewrite_rules();
	}
	if ( isset( $_POST['elevora_setup_running'] ) ) {
		$args = array( 'page' => 'elevora-setup', 'complete' => '1' );
		if ( ! empty( $_POST['elevora_setup_warning'] ) ) {
			$args['warning'] = sanitize_text_field( wp_unslash( $_POST['elevora_setup_warning'] ) );
		}
		wp_safe_redirect( add_query_arg( $args, admin_url( 'themes.php' ) ) );
	} else {
		wp_safe_redirect( add_query_arg( array( 'page' => 'elevora-demo-import', 'imported' => '1' ), admin_url( 'themes.php' ) ) );
	}
	exit;
}
add_action( 'admin_post_elevora_import_demo', 'elevora_import_demo' );

/**
 * Return known demo records for installations created before ownership
 * tracking was introduced.
 *
 * @return array<string, string[]>
 */
function elevora_demo_content_manifest() {
	return array(
		'page'              => array( 'home', 'journal', 'about', 'contact', 'video-library', 'watch-later', 'submit-a-video', 'my-videos' ),
		'post'              => array( 'executive-presence-signals', 'professional-image-foundation', 'speak-with-more-confidence', 'lead-a-difficult-conversation', 'wardrobe-for-visible-leadership', 'better-presentation-openings', 'confidence-before-big-meeting', 'personal-brand-audit', 'networking-without-performance' ),
		'trainer'           => array( 'marcus-reed', 'priya-nair', 'kenji-sato', 'claire-bennett' ),
		'coaching_service'  => array( 'executive-presence', 'professional-image', 'communication-public-speaking', 'business-etiquette', 'leadership-soft-skills', 'career-readiness' ),
		'testimonial'       => array( 'maya-thompson', 'daniel-ortiz', 'amelia-wright' ),
		'gravitas_video'    => array( 'command-the-room', 'confident-introduction', 'visual-credibility', 'difficult-conversations', 'networking-etiquette', 'interview-presence' ),
		'gravitas_channel'  => array( 'gravitas-learning-studio' ),
		'gravitas_playlist' => array( 'presence-essentials' ),
		'gravitas_series'   => array( 'confidence-in-action' ),
	);
}

/**
 * Remove content created by the Gravitas demo importer.
 */
function elevora_uninstall_demo() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to remove demo content.', 'gravitas-coaching' ) );
	}
	check_admin_referer( 'elevora_uninstall_demo', 'elevora_uninstall_nonce' );

	$post_ids = array_map( 'absint', (array) get_option( 'elevora_demo_post_ids', array() ) );
	$tracked  = get_posts(
		array(
			'post_type'      => 'any',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'OR',
				array( 'key' => '_elevora_demo_content', 'value' => '1' ),
				array( 'key' => '_gravitas_demo_content', 'value' => '1' ),
			),
		)
	);
	$post_ids = array_merge( $post_ids, $tracked );

	$post_ids       = array_unique( array_filter( array_map( 'absint', $post_ids ) ) );
	$page_on_front  = (int) get_option( 'page_on_front' );
	$page_for_posts = (int) get_option( 'page_for_posts' );
	foreach ( $post_ids as $post_id ) {
		if ( get_post( $post_id ) ) {
			wp_trash_post( $post_id );
		}
	}
	if ( in_array( $page_on_front, $post_ids, true ) ) {
		update_option( 'page_on_front', 0 );
		update_option( 'show_on_front', 'posts' );
	}
	if ( in_array( $page_for_posts, $post_ids, true ) ) {
		update_option( 'page_for_posts', 0 );
	}

	$attachments = array_map( 'absint', (array) get_option( 'elevora_demo_attachments', array() ) );
	foreach ( array_filter( $attachments ) as $attachment_id ) {
		wp_delete_attachment( $attachment_id, true );
	}

	$menu = wp_get_nav_menu_object( 'Gravitas Primary' );
	if ( $menu ) {
		$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $locations as $location => $menu_id ) {
			if ( (int) $menu_id === (int) $menu->term_id ) {
				unset( $locations[ $location ] );
			}
		}
		set_theme_mod( 'nav_menu_locations', $locations );
		wp_delete_nav_menu( $menu->term_id );
	}

	foreach ( array( 'elevora_demo_imported_at', 'elevora_demo_post_ids', 'elevora_demo_attachments', 'gravitas_video_demo_imported_at', 'elevora_video_demo_pending', 'elevora_video_repair_v2' ) as $option ) {
		delete_option( $option );
	}
	foreach ( array( 'elevora_hero_image', 'elevora_hero_url', 'elevora_hero_alt_url', 'elevora_blog_layout', 'elevora_blog_sidebar' ) as $theme_mod ) {
		remove_theme_mod( $theme_mod );
	}
	if ( ! empty( $_POST['reset_theme_options'] ) ) {
		delete_option( 'gravitas_core_options' );
		foreach ( array( 'elevora_accent', 'elevora_header_cta', 'elevora_header_url', 'elevora_footer_text', 'elevora_phone', 'elevora_email', 'elevora_hero_eyebrow', 'elevora_hero_title', 'elevora_hero_highlight', 'elevora_hero_text', 'elevora_hero_button', 'elevora_hero_alt_text' ) as $theme_mod ) {
			remove_theme_mod( $theme_mod );
		}
	}

	flush_rewrite_rules();
	wp_safe_redirect( add_query_arg( array( 'page' => 'elevora-demo-import', 'removed' => '1' ), admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_elevora_uninstall_demo', 'elevora_uninstall_demo' );

function elevora_activation_notice() {
	if ( get_option( 'elevora_demo_imported_at' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || false === strpos( $screen->id, 'theme' ) ) {
		return;
	}
	?>
	<div class="notice notice-info"><p><strong><?php esc_html_e( 'Welcome to Gravitas Coaching.', 'gravitas-coaching' ); ?></strong> <?php esc_html_e( 'Install the optional companion plugins and demo content when you are ready.', 'gravitas-coaching' ); ?> <a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=elevora-setup' ) ); ?>"><?php esc_html_e( 'Setup Wizard', 'gravitas-coaching' ); ?></a></p></div>
	<?php
}
add_action( 'admin_notices', 'elevora_activation_notice' );

/** Complete a previously requested demo when Gravitas Video becomes available. */
function elevora_repair_video_demo( $images = array() ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'elevora_demo_imported_at' ) && ! get_option( 'gravitas_video_demo_imported_at' ) && ! function_exists( 'gravitas_video_import_demo_content' ) ) {
		update_option( 'elevora_video_demo_pending', 1, false );
	}
	if ( ! function_exists( 'gravitas_video_import_demo_content' ) || ! post_type_exists( 'gravitas_video' ) ) {
		return;
	}
	$pending = get_option( 'elevora_video_demo_pending' );
	$legacy = get_option( 'elevora_demo_imported_at' ) && ! get_option( 'gravitas_video_demo_imported_at' );
	if ( get_option( 'elevora_demo_imported_at' ) && ! get_option( 'elevora_video_repair_v2' ) ) {
		foreach ( array( 'command-the-room', 'confident-introduction', 'visual-credibility', 'difficult-conversations', 'networking-etiquette', 'interview-presence' ) as $slug ) {
			if ( ! get_page_by_path( $slug, OBJECT, 'gravitas_video' ) ) {
				$legacy = true;
				break;
			}
		}
	}
	if ( ! $pending && ! $legacy ) {
		return;
	}
	if ( ! $images ) {
		$images = elevora_import_demo_images();
	}
	$result = gravitas_video_import_demo_content( $images );
	if ( ! empty( $result['video_ids'] ) && 6 === count( array_filter( $result['video_ids'] ) ) ) {
		delete_option( 'elevora_video_demo_pending' );
		update_option( 'elevora_video_repair_v2', 1, false );
	}
}
add_action( 'admin_init', 'elevora_repair_video_demo', 40 );

/** Make a deferred video import visible instead of silently skipping it. */
function elevora_video_demo_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'elevora_video_demo_pending' ) || function_exists( 'gravitas_video_import_demo_content' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p>' . esc_html__( 'Your video demo is queued. Install and activate Gravitas Video from the Plugin Center to finish importing the six lessons, channel, playlist, series, and video pages.', 'gravitas-coaching' ) . ' <a href="' . esc_url( admin_url( 'themes.php?page=gravitas-plugins' ) ) . '">' . esc_html__( 'Open Plugin Center', 'gravitas-coaching' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'elevora_video_demo_notice' );
