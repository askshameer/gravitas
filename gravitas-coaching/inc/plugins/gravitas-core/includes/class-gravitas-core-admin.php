<?php
/**
 * Gravitas admin dashboard.
 *
 * @package Gravitas_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Gravitas_Core_Admin {
	private $settings;

	public function __construct( Gravitas_Core_Settings $settings ) {
		$this->settings = $settings;
		add_action( 'admin_menu', array( $this, 'menu' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
		add_action( 'admin_post_gravitas_save_options', array( $this, 'save_options' ) );
	}

	public function menu() {
		add_menu_page( __( 'Gravitas', 'gravitas-core' ), __( 'Gravitas', 'gravitas-core' ), 'manage_options', 'gravitas', array( $this, 'dashboard' ), 'dashicons-welcome-learn-more', 3 );
		add_submenu_page( 'gravitas', __( 'Gravitas Overview', 'gravitas-core' ), __( 'Overview', 'gravitas-core' ), 'manage_options', 'gravitas', array( $this, 'dashboard' ) );
		add_submenu_page( 'gravitas', __( 'Theme Options', 'gravitas-core' ), __( 'Theme Options', 'gravitas-core' ), 'manage_options', 'gravitas-options', array( $this, 'options' ) );
		add_submenu_page( 'gravitas', __( 'Plugin Center', 'gravitas-core' ), __( 'Plugins', 'gravitas-core' ), 'install_plugins', 'gravitas-plugins', array( $this, 'plugins' ) );
		add_submenu_page( 'gravitas', __( 'Import Demo', 'gravitas-core' ), __( 'Demo Import', 'gravitas-core' ), 'manage_options', 'gravitas-demo-link', array( $this, 'demo' ) );
		add_submenu_page( 'gravitas', __( 'System Status', 'gravitas-core' ), __( 'System Status', 'gravitas-core' ), 'manage_options', 'gravitas-status', array( $this, 'status' ) );
	}

	public function assets( $hook ) {
		if ( false === strpos( $hook, 'gravitas' ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'gravitas-core-admin', GRAVITAS_CORE_URL . 'assets/admin.css', array( 'wp-color-picker' ), GRAVITAS_CORE_VERSION );
		wp_enqueue_script( 'gravitas-core-admin', GRAVITAS_CORE_URL . 'assets/admin.js', array( 'jquery', 'wp-color-picker' ), GRAVITAS_CORE_VERSION, true );
	}

	private function header( $title, $description = '' ) {
		?>
		<div class="wrap gravitas-admin">
			<header class="gravitas-admin__hero">
				<div><span class="gravitas-admin__eyebrow"><?php esc_html_e( 'Gravitas Coaching', 'gravitas-core' ); ?></span><h1><?php echo esc_html( $title ); ?></h1><?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?></div>
				<span class="gravitas-admin__version"><?php echo esc_html( 'Core ' . GRAVITAS_CORE_VERSION ); ?></span>
			</header>
		<?php
	}

	private function footer() {
		echo '</div>';
	}

	public function dashboard() {
		$this->header( __( 'Welcome to Gravitas', 'gravitas-core' ), __( 'Manage your coaching platform, visual identity, video library, and launch content from one place.', 'gravitas-core' ) );
		$cards = array(
			array( 'dashicons-admin-customizer', __( 'Shape your brand', 'gravitas-core' ), __( 'Control logos, colors, type, headers, layouts, and contact details.', 'gravitas-core' ), admin_url( 'admin.php?page=gravitas-options' ), __( 'Open theme options', 'gravitas-core' ) ),
			array( 'dashicons-format-video', __( 'Build your video hub', 'gravitas-core' ), __( 'Publish courses, interviews, webinars, playlists, channels, and series.', 'gravitas-core' ), post_type_exists( 'gravitas_video' ) ? admin_url( 'edit.php?post_type=gravitas_video' ) : admin_url( 'admin.php?page=gravitas-plugins' ), __( 'Manage videos', 'gravitas-core' ) ),
			array( 'dashicons-groups', __( 'Grow your directory', 'gravitas-core' ), __( 'Create detailed trainer profiles, specialist services, and searchable expertise.', 'gravitas-core' ), admin_url( 'edit.php?post_type=trainer' ), __( 'Manage trainers', 'gravitas-core' ) ),
			array( 'dashicons-download', __( 'Launch the demo', 'gravitas-core' ), __( 'Import the starter site, professional imagery, menus, widgets, and video content.', 'gravitas-core' ), admin_url( 'admin.php?page=gravitas-demo-link' ), __( 'Import demo', 'gravitas-core' ) ),
		);
		?>
		<div class="gravitas-card-grid">
			<?php foreach ( $cards as $card ) : ?>
				<section class="gravitas-card"><span class="dashicons <?php echo esc_attr( $card[0] ); ?>"></span><h2><?php echo esc_html( $card[1] ); ?></h2><p><?php echo esc_html( $card[2] ); ?></p><a class="button button-primary" href="<?php echo esc_url( $card[3] ); ?>"><?php echo esc_html( $card[4] ); ?></a></section>
			<?php endforeach; ?>
		</div>
		<section class="gravitas-panel"><h2><?php esc_html_e( 'Platform readiness', 'gravitas-core' ); ?></h2><div class="gravitas-status-grid">
			<?php $this->status_item( __( 'Gravitas Coaching theme', 'gravitas-core' ), 'gravitas-coaching' === get_template(), wp_get_theme()->get( 'Name' ) ); ?>
			<?php $this->status_item( __( 'Gravitas Video', 'gravitas-core' ), defined( 'GRAVITAS_VIDEO_VERSION' ), defined( 'GRAVITAS_VIDEO_VERSION' ) ? GRAVITAS_VIDEO_VERSION : __( 'Not active', 'gravitas-core' ) ); ?>
			<?php $this->status_item( __( 'Elementor integration', 'gravitas-core' ), did_action( 'elementor/loaded' ), did_action( 'elementor/loaded' ) ? __( 'Ready', 'gravitas-core' ) : __( 'Optional', 'gravitas-core' ) ); ?>
			<?php $this->status_item( __( 'Membership integration', 'gravitas-core' ), defined( 'PMPRO_VERSION' ) || class_exists( 'WC_Memberships' ), defined( 'PMPRO_VERSION' ) || class_exists( 'WC_Memberships' ) ? __( 'Connected', 'gravitas-core' ) : __( 'Ready to connect', 'gravitas-core' ) ); ?>
		</div></section>
		<?php
		$this->footer();
	}

	private function status_item( $label, $good, $value ) {
		printf( '<div class="gravitas-status"><span class="gravitas-dot %1$s"></span><div><strong>%2$s</strong><small>%3$s</small></div></div>', $good ? 'is-good' : 'is-neutral', esc_html( $label ), esc_html( $value ) );
	}

	public function options() {
		$this->header( __( 'Theme Options', 'gravitas-core' ), __( 'A unified control panel for the Gravitas Coaching front end.', 'gravitas-core' ) );
		if ( filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Theme Options saved and front-end caches refreshed.', 'gravitas-core' ) . '</p></div>';
		}
		$saved_options = (array) get_option( 'gravitas_core_options', array() );
		$options       = wp_parse_args( $saved_options, Gravitas_Core_Settings::defaults() );
		$hero_theme_mods = array(
			'hero_image_id'       => 'elevora_hero_image',
			'hero_eyebrow'        => 'elevora_hero_eyebrow',
			'hero_title'          => 'elevora_hero_title',
			'hero_highlight'      => 'elevora_hero_highlight',
			'hero_text'           => 'elevora_hero_text',
			'hero_primary_text'   => 'elevora_hero_button',
			'hero_primary_url'    => 'elevora_hero_url',
			'hero_secondary_text' => 'elevora_hero_alt_text',
			'hero_secondary_url'  => 'elevora_hero_alt_url',
		);
		foreach ( $hero_theme_mods as $option_key => $theme_mod ) {
			if ( ! array_key_exists( $option_key, $saved_options ) ) {
				$options[ $option_key ] = get_theme_mod( $theme_mod, $options[ $option_key ] );
			}
		}
		$tabs = array(
			'brand'      => __( 'Brand', 'gravitas-core' ),
			'typography' => __( 'Typography', 'gravitas-core' ),
			'header'     => __( 'Header', 'gravitas-core' ),
			'hero'       => __( 'Hero', 'gravitas-core' ),
			'page_titles'=> __( 'Page Titles', 'gravitas-core' ),
			'single_post'=> __( 'Single Post', 'gravitas-core' ),
			'post_style' => __( 'Post Style', 'gravitas-core' ),
			'related'    => __( 'Related Posts', 'gravitas-core' ),
			'comments'   => __( 'Comments', 'gravitas-core' ),
			'pages'      => __( 'Pages', 'gravitas-core' ),
			'layout'     => __( 'Layout', 'gravitas-core' ),
			'navigation' => __( 'Pagination & Loading', 'gravitas-core' ),
			'footer'     => __( 'Footer & Social', 'gravitas-core' ),
			'advanced'   => __( 'Advanced', 'gravitas-core' ),
		);
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="gravitas-options-form">
			<?php wp_nonce_field( 'gravitas_save_options', 'gravitas_options_nonce' ); ?>
			<input type="hidden" name="action" value="gravitas_save_options">
			<div class="gravitas-options-toolbar"><div><strong><?php esc_html_e( 'Theme Options', 'gravitas-core' ); ?></strong><span data-save-state aria-live="polite"><?php esc_html_e( 'All settings', 'gravitas-core' ); ?></span></div><label class="gravitas-options-search"><span class="screen-reader-text"><?php esc_html_e( 'Search settings', 'gravitas-core' ); ?></span><input type="search" data-options-search placeholder="<?php esc_attr_e( 'Search settings…', 'gravitas-core' ); ?>"></label><button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'gravitas-core' ); ?></button></div>
			<nav class="gravitas-tabs" aria-label="<?php esc_attr_e( 'Option sections', 'gravitas-core' ); ?>"><?php foreach ( $tabs as $key => $label ) : ?><button type="button" class="gravitas-tab<?php echo 'brand' === $key ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></button><?php endforeach; ?></nav>
			<div class="gravitas-options-content">
				<section class="gravitas-option-section is-active" data-panel="brand"><h2><?php esc_html_e( 'Visual identity', 'gravitas-core' ); ?></h2><?php $this->media_field( 'light_logo_id', __( 'Light-mode header logo', 'gravitas-core' ), $options ); ?><?php $this->media_field( 'dark_logo_id', __( 'Dark-mode header logo', 'gravitas-core' ), $options ); ?><?php $this->color_field( 'accent_color', __( 'Accent color', 'gravitas-core' ), $options ); ?><?php $this->color_field( 'secondary_color', __( 'Secondary color', 'gravitas-core' ), $options ); ?><?php $this->color_field( 'ink_color', __( 'Text color', 'gravitas-core' ), $options ); ?><?php $this->color_field( 'paper_color', __( 'Page background', 'gravitas-core' ), $options ); ?><?php $this->color_field( 'dark_color', __( 'Dark background', 'gravitas-core' ), $options ); ?></section>
				<section class="gravitas-option-section" data-panel="typography"><h2><?php esc_html_e( 'Typography', 'gravitas-core' ); ?></h2><?php $this->select_field( 'body_font', __( 'Body font family', 'gravitas-core' ), $options, $this->font_choices() ); ?><?php $this->select_field( 'heading_font', __( 'Heading font family', 'gravitas-core' ), $options, $this->font_choices() ); ?><?php $this->select_field( 'nav_font', __( 'Navigation font family', 'gravitas-core' ), $options, $this->font_choices() ); ?><?php $this->number_field( 'body_size', __( 'Base font size', 'gravitas-core' ), $options, 12, 24, 'px' ); ?><?php $this->text_field( 'custom_font_url', __( 'Custom font stylesheet URL', 'gravitas-core' ), $options, 'url', __( 'Optional. Add an approved hosted font stylesheet.', 'gravitas-core' ) ); ?></section>
				<section class="gravitas-option-section" data-panel="header"><h2><?php esc_html_e( 'Header', 'gravitas-core' ); ?></h2><?php $this->select_field( 'header_layout', __( 'Header layout', 'gravitas-core' ), $options, array( 'standard' => __( 'Standard', 'gravitas-core' ), 'centered' => __( 'Centered', 'gravitas-core' ), 'minimal' => __( 'Minimal', 'gravitas-core' ) ) ); ?><?php $this->checkbox_field( 'sticky_header', __( 'Sticky header', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_announcement', __( 'Show announcement bar', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_search', __( 'Show search', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_dark_toggle', __( 'Show dark-mode toggle', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'header_cta_text', __( 'CTA label', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'header_cta_url', __( 'CTA URL or Shortcode', 'gravitas-core' ), $options, 'text', __( 'Enter a link URL or booking shortcode (e.g. [amelia_booking]).', 'gravitas-core' ) ); ?><?php $this->select_field( 'default_scheme', __( 'Default color scheme', 'gravitas-core' ), $options, array( 'light' => __( 'Light', 'gravitas-core' ), 'dark' => __( 'Dark', 'gravitas-core' ) ) ); ?></section>
				<section class="gravitas-option-section" data-panel="hero"><h2><?php esc_html_e( 'Homepage hero', 'gravitas-core' ); ?></h2><p><?php esc_html_e( 'Control the main homepage message, image, calls to action, height, and alignment.', 'gravitas-core' ); ?></p><?php $this->media_field( 'hero_image_id', __( 'Background image', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_eyebrow', __( 'Eyebrow', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_title', __( 'Main heading', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_highlight', __( 'Highlighted heading line', 'gravitas-core' ), $options ); ?><?php $this->textarea_field( 'hero_text', __( 'Description', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_primary_text', __( 'Primary button label', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_primary_url', __( 'Primary button URL', 'gravitas-core' ), $options, 'url' ); ?><?php $this->text_field( 'hero_secondary_text', __( 'Secondary button label', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'hero_secondary_url', __( 'Secondary button URL', 'gravitas-core' ), $options, 'url' ); ?><?php $this->number_field( 'hero_min_height', __( 'Minimum height', 'gravitas-core' ), $options, 360, 900, 'px' ); ?><?php $this->number_field( 'hero_overlay', __( 'Image overlay strength', 'gravitas-core' ), $options, 0, 100, '%' ); ?><?php $this->select_field( 'hero_alignment', __( 'Content alignment', 'gravitas-core' ), $options, array( 'left' => __( 'Left', 'gravitas-core' ), 'center' => __( 'Centered', 'gravitas-core' ) ) ); ?></section>
				<section class="gravitas-option-section" data-panel="page_titles"><h2><?php esc_html_e( 'Page and post title bars', 'gravitas-core' ); ?></h2><p><?php esc_html_e( 'Set the native title bar height and title size independently. Elementor-built documents keep their own responsive sizing.', 'gravitas-core' ); ?></p><h3><?php esc_html_e( 'Pages', 'gravitas-core' ); ?></h3><?php $this->number_field( 'page_header_height', __( 'Page title bar height', 'gravitas-core' ), $options, 120, 720, 'px' ); ?><?php $this->number_field( 'page_title_size', __( 'Page title size', 'gravitas-core' ), $options, 24, 120, 'px' ); ?><h3><?php esc_html_e( 'Blog posts', 'gravitas-core' ); ?></h3><?php $this->number_field( 'post_header_height', __( 'Post header height', 'gravitas-core' ), $options, 120, 720, 'px' ); ?><?php $this->number_field( 'post_title_size', __( 'Post title size', 'gravitas-core' ), $options, 24, 120, 'px' ); ?></section>
				<section class="gravitas-option-section" data-panel="single_post">
					<h2><?php esc_html_e( 'Single post', 'gravitas-core' ); ?></h2>
					<p><?php esc_html_e( 'Choose exactly which elements appear on standard blog posts. Elementor-built posts retain their own layout.', 'gravitas-core' ); ?></p>
					<?php $this->select_field( 'post_title_alignment', __( 'Title alignment', 'gravitas-core' ), $options, array( 'left' => __( 'Left', 'gravitas-core' ), 'center' => __( 'Centered', 'gravitas-core' ) ) ); ?>
					<?php $this->number_field( 'single_content_width', __( 'Content width without sidebar', 'gravitas-core' ), $options, 560, 1200, 'px' ); ?>
					<?php $this->select_field( 'single_sidebar', __( 'Single-post sidebar', 'gravitas-core' ), $options, array( 'right' => __( 'Right', 'gravitas-core' ), 'left' => __( 'Left', 'gravitas-core' ), 'none' => __( 'No sidebar', 'gravitas-core' ) ) ); ?>
					<h3><?php esc_html_e( 'Sidebar advertisement', 'gravitas-core' ); ?></h3>
					<?php $this->media_field( 'sidebar_ad_image_id', __( 'Advertisement image', 'gravitas-core' ), $options ); ?>
					<?php $this->text_field( 'sidebar_ad_url', __( 'Advertisement link', 'gravitas-core' ), $options, 'url' ); ?>
					<?php $this->checkbox_field( 'sidebar_ad_new_tab', __( 'Open advertisement in a new tab', 'gravitas-core' ), $options ); ?>
					<h3><?php esc_html_e( 'Header and media', 'gravitas-core' ); ?></h3>
					<?php $this->checkbox_field( 'show_post_breadcrumbs', __( 'Show breadcrumbs', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_categories', __( 'Show categories', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_date', __( 'Show publish date', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_author', __( 'Show author', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_reading_time', __( 'Show reading time', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_featured', __( 'Show featured image', 'gravitas-core' ), $options ); ?>
					<h3><?php esc_html_e( 'After the article', 'gravitas-core' ); ?></h3>
					<?php $this->checkbox_field( 'show_post_share', __( 'Show social sharing', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_navigation', __( 'Show previous/next navigation', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_comments', __( 'Show comments', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_related_posts', __( 'Show related posts', 'gravitas-core' ), $options ); ?><?php $this->number_field( 'related_posts_count', __( 'Related post count', 'gravitas-core' ), $options, 1, 6, __( 'posts', 'gravitas-core' ) ); ?>
				</section>
				<section class="gravitas-option-section" data-panel="pages"><h2><?php esc_html_e( 'Standard pages', 'gravitas-core' ); ?></h2><p><?php esc_html_e( 'These settings apply to native page templates. Elementor pages remain completely controlled by Elementor.', 'gravitas-core' ); ?></p><?php $this->checkbox_field( 'show_page_breadcrumbs', __( 'Show page breadcrumbs', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_page_featured', __( 'Show page featured image', 'gravitas-core' ), $options ); ?></section>
				<section class="gravitas-option-section" data-panel="layout"><h2><?php esc_html_e( 'Layout', 'gravitas-core' ); ?></h2><?php $this->number_field( 'container_width', __( 'Container width', 'gravitas-core' ), $options, 960, 1600, 'px' ); ?><?php $this->number_field( 'border_radius', __( 'Card radius', 'gravitas-core' ), $options, 0, 48, 'px' ); ?><?php $this->select_field( 'blog_layout', __( 'Blog archive layout', 'gravitas-core' ), $options, array( 'editorial' => __( 'Editorial Feature', 'gravitas-core' ), 'magazine' => __( 'Modern Magazine', 'gravitas-core' ), 'masonry' => __( 'Creative Masonry', 'gravitas-core' ), 'grid' => __( 'Classic Grid', 'gravitas-core' ), 'list' => __( 'Elegant List', 'gravitas-core' ), 'minimal' => __( 'Minimal Journal', 'gravitas-core' ) ) ); ?></section>
				<section class="gravitas-option-section" data-panel="footer"><h2><?php esc_html_e( 'Footer and contact', 'gravitas-core' ); ?></h2><?php $this->textarea_field( 'footer_description', __( 'Footer description', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'copyright_text', __( 'Copyright text', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'phone', __( 'Phone', 'gravitas-core' ), $options ); ?><?php $this->text_field( 'email', __( 'Email', 'gravitas-core' ), $options, 'email' ); ?><?php foreach ( array( 'instagram_url' => 'Instagram', 'linkedin_url' => 'LinkedIn', 'youtube_url' => 'YouTube', 'facebook_url' => 'Facebook' ) as $key => $label ) { $this->text_field( $key, $label . ' URL', $options, 'url' ); } ?></section>
				<section class="gravitas-option-section" data-panel="advanced"><h2><?php esc_html_e( 'Performance and custom code', 'gravitas-core' ); ?></h2><?php $this->checkbox_field( 'enable_schema', __( 'Enable structured data', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'enable_back_to_top', __( 'Enable back-to-top button', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'enable_lazy_loading', __( 'Enable native image lazy loading', 'gravitas-core' ), $options ); ?><?php $this->textarea_field( 'custom_css', __( 'Custom CSS', 'gravitas-core' ), $options, true ); ?></section>
				<section class="gravitas-option-section" data-panel="post_style"><h2><?php esc_html_e( 'Single-post style and reading tools', 'gravitas-core' ); ?></h2><?php $this->select_field( 'post_title_position', __( 'Title position', 'gravitas-core' ), $options, array( 'before' => __( 'Above featured image', 'gravitas-core' ), 'after' => __( 'Below featured image', 'gravitas-core' ) ) ); ?><?php $this->number_field( 'post_content_size', __( 'Content font size', 'gravitas-core' ), $options, 10, 28, 'px' ); ?><?php $this->number_field( 'post_meta_size', __( 'Metadata font size', 'gravitas-core' ), $options, 10, 28, 'px' ); ?><?php $this->number_field( 'post_h2_size', __( 'Content H2 size', 'gravitas-core' ), $options, 20, 72, 'px' ); ?><?php $this->number_field( 'featured_radius', __( 'Featured-image radius', 'gravitas-core' ), $options, 0, 80, 'px' ); ?><?php $this->select_field( 'blockquote_style', __( 'Blockquote style', 'gravitas-core' ), $options, array( 'accent' => __( 'Accent border', 'gravitas-core' ), 'boxed' => __( 'Boxed', 'gravitas-core' ), 'plain' => __( 'Plain', 'gravitas-core' ) ) ); ?><?php $this->checkbox_field( 'show_featured_caption', __( 'Show featured-image caption', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_modified_date', __( 'Show modified date', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_comment_count', __( 'Show comment count in metadata', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_post_tags', __( 'Show tags after content', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'show_author_box', __( 'Show author biography', 'gravitas-core' ), $options ); ?><h3><?php esc_html_e( 'Reading progress', 'gravitas-core' ); ?></h3><?php $this->checkbox_field( 'enable_reading_progress', __( 'Enable reading progress bar', 'gravitas-core' ), $options ); ?><?php $this->select_field( 'reading_progress_position', __( 'Progress position', 'gravitas-core' ), $options, array( 'top' => __( 'Top', 'gravitas-core' ), 'bottom' => __( 'Bottom', 'gravitas-core' ) ) ); ?><?php $this->number_field( 'reading_progress_height', __( 'Progress height', 'gravitas-core' ), $options, 2, 12, 'px' ); ?><?php $this->color_field( 'reading_progress_color', __( 'Progress color', 'gravitas-core' ), $options ); ?></section>
				<section class="gravitas-option-section" data-panel="related"><h2><?php esc_html_e( 'Related posts query and display', 'gravitas-core' ); ?></h2><?php $this->text_field( 'related_posts_title', __( 'Section title', 'gravitas-core' ), $options ); ?><?php $this->select_field( 'related_posts_by', __( 'Match posts by', 'gravitas-core' ), $options, array( 'category' => __( 'Categories', 'gravitas-core' ), 'tag' => __( 'Tags', 'gravitas-core' ), 'author' => __( 'Author', 'gravitas-core' ), 'latest' => __( 'Latest posts', 'gravitas-core' ) ) ); ?><?php $this->select_field( 'related_orderby', __( 'Order by', 'gravitas-core' ), $options, array( 'date' => __( 'Date', 'gravitas-core' ), 'rand' => __( 'Random', 'gravitas-core' ), 'comment_count' => __( 'Comment count', 'gravitas-core' ), 'title' => __( 'Title', 'gravitas-core' ) ) ); ?><?php $this->select_field( 'related_order', __( 'Sort direction', 'gravitas-core' ), $options, array( 'DESC' => __( 'Descending', 'gravitas-core' ), 'ASC' => __( 'Ascending', 'gravitas-core' ) ) ); ?><?php $this->checkbox_field( 'related_show_date', __( 'Show dates on related posts', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'hide_related_mobile', __( 'Hide related posts on mobile', 'gravitas-core' ), $options ); ?></section>
				<section class="gravitas-option-section" data-panel="comments"><h2><?php esc_html_e( 'Single-post comments', 'gravitas-core' ); ?></h2><?php $this->checkbox_field( 'comments_remove_website', __( 'Remove website field', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'comments_remove_cookies', __( 'Remove save-details checkbox', 'gravitas-core' ), $options ); ?><?php $this->checkbox_field( 'comments_gdpr', __( 'Show a GDPR consent message', 'gravitas-core' ), $options ); ?><?php $this->textarea_field( 'comments_gdpr_text', __( 'GDPR message', 'gravitas-core' ), $options ); ?></section>
				<section class="gravitas-option-section" data-panel="navigation"><h2><?php esc_html_e( 'Pagination & loading', 'gravitas-core' ); ?></h2><p><?php esc_html_e( 'Navigation stays below the post list. Load more adds posts without leaving the current page, with page links available if loading fails or JavaScript is disabled.', 'gravitas-core' ); ?></p><?php $this->select_field( 'blog_pagination', __( 'Blog navigation', 'gravitas-core' ), $options, array( 'numbers' => __( 'Numbered navigation at end of page', 'gravitas-core' ), 'load_more' => __( 'Load more posts dynamically', 'gravitas-core' ) ) ); ?></section>
				<p class="gravitas-search-empty" hidden><?php esc_html_e( 'No settings match your search.', 'gravitas-core' ); ?></p>
				<div class="gravitas-options-footer"><?php submit_button( __( 'Save Changes', 'gravitas-core' ) ); ?><span><?php esc_html_e( 'Changes apply after saving.', 'gravitas-core' ); ?></span></div>
			</div>
		</form>
		<?php
		$this->footer();
	}

	/**
	 * Save Theme Options through a dedicated, capability-checked endpoint.
	 */
	public function save_options() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to update Theme Options.', 'gravitas-core' ) );
		}
		check_admin_referer( 'gravitas_save_options', 'gravitas_options_nonce' );

		$input = isset( $_POST['gravitas_core_options'] ) ? wp_unslash( $_POST['gravitas_core_options'] ) : array();
		if ( ! is_array( $input ) ) {
			$input = array();
		}
		$sanitized = $this->settings->sanitize( $input );
		update_option( 'gravitas_core_options', $sanitized, false );

		wp_safe_redirect( add_query_arg( array( 'page' => 'gravitas-options', 'settings-updated' => '1' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	private function option_name( $key ) {
		return 'gravitas_core_options[' . $key . ']';
	}

	private function text_field( $key, $label, $options, $type = 'text', $description = '' ) {
		printf( '<div class="gravitas-field"><label for="gc-%1$s"><strong>%2$s</strong>%3$s</label><input id="gc-%1$s" type="%4$s" name="%5$s" value="%6$s"></div>', esc_attr( $key ), esc_html( $label ), $description ? '<small>' . esc_html( $description ) . '</small>' : '', esc_attr( $type ), esc_attr( $this->option_name( $key ) ), esc_attr( $options[ $key ] ) );
	}

	private function textarea_field( $key, $label, $options, $code = false ) {
		printf( '<div class="gravitas-field"><label for="gc-%1$s"><strong>%2$s</strong></label><textarea id="gc-%1$s" class="%3$s" rows="%4$d" name="%5$s">%6$s</textarea></div>', esc_attr( $key ), esc_html( $label ), $code ? 'code' : '', $code ? 12 : 5, esc_attr( $this->option_name( $key ) ), esc_textarea( $options[ $key ] ) );
	}

	private function color_field( $key, $label, $options ) {
		printf( '<div class="gravitas-field"><label for="gc-%1$s"><strong>%2$s</strong></label><input id="gc-%1$s" class="gravitas-color" type="text" name="%3$s" value="%4$s"></div>', esc_attr( $key ), esc_html( $label ), esc_attr( $this->option_name( $key ) ), esc_attr( $options[ $key ] ) );
	}

	private function checkbox_field( $key, $label, $options ) {
		printf( '<div class="gravitas-field gravitas-field--check"><label><input type="checkbox" name="%1$s" value="1" %2$s><span><strong>%3$s</strong></span></label></div>', esc_attr( $this->option_name( $key ) ), checked( ! empty( $options[ $key ] ), true, false ), esc_html( $label ) );
	}

	private function number_field( $key, $label, $options, $min, $max, $suffix ) {
		printf( '<div class="gravitas-field"><label for="gc-%1$s"><strong>%2$s</strong></label><div class="gravitas-number"><input id="gc-%1$s" type="number" min="%3$d" max="%4$d" name="%5$s" value="%6$d"><span>%7$s</span></div></div>', esc_attr( $key ), esc_html( $label ), absint( $min ), absint( $max ), esc_attr( $this->option_name( $key ) ), absint( $options[ $key ] ), esc_html( $suffix ) );
	}

	private function select_field( $key, $label, $options, $choices ) {
		echo '<div class="gravitas-field"><label for="gc-' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . '</strong></label><select id="gc-' . esc_attr( $key ) . '" name="' . esc_attr( $this->option_name( $key ) ) . '">';
		foreach ( $choices as $value => $choice ) {
			printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $options[ $key ], $value, false ), esc_html( $choice ) );
		}
		echo '</select></div>';
	}

	private function media_field( $key, $label, $options ) {
		$image = $options[ $key ] ? wp_get_attachment_image_url( $options[ $key ], 'medium' ) : '';
		printf( '<div class="gravitas-field"><label><strong>%1$s</strong></label><div class="gravitas-media"><input type="hidden" name="%2$s" value="%3$d" data-media-id><div class="gravitas-media__preview" data-media-preview>%4$s</div><div><button type="button" class="button" data-media-select>%5$s</button> <button type="button" class="button-link-delete" data-media-remove>%6$s</button></div></div></div>', esc_html( $label ), esc_attr( $this->option_name( $key ) ), absint( $options[ $key ] ), $image ? '<img src="' . esc_url( $image ) . '" alt="">' : '', esc_html__( 'Choose image', 'gravitas-core' ), esc_html__( 'Remove', 'gravitas-core' ) );
	}

	private function font_choices() {
		return array( 'professional' => __( 'Professional neutral sans', 'gravitas-core' ), 'editorial_sans' => __( 'Editorial humanist sans', 'gravitas-core' ), 'literary' => __( 'Literary old-style serif', 'gravitas-core' ), 'system' => __( 'Modern system sans', 'gravitas-core' ), 'serif' => __( 'Classic serif', 'gravitas-core' ), 'humanist' => __( 'Humanist sans', 'gravitas-core' ), 'geometric' => __( 'Geometric sans', 'gravitas-core' ), 'elegant' => __( 'Elegant editorial serif', 'gravitas-core' ) );
	}

	public function plugins() {
		if ( function_exists( 'elevora_render_plugin_installer' ) ) {
			elevora_render_plugin_installer();
			return;
		}
		$this->header( __( 'Plugin Center', 'gravitas-core' ) );
		echo '<section class="gravitas-panel"><p>' . esc_html__( 'Activate the Gravitas Coaching theme to install bundled and recommended plugins from this screen.', 'gravitas-core' ) . '</p></section>';
		$this->footer();
	}

	public function demo() {
		if ( function_exists( 'elevora_demo_page' ) ) {
			elevora_demo_page();
			return;
		}
		$this->header( __( 'Demo Import', 'gravitas-core' ) );
		echo '<section class="gravitas-panel"><p>' . esc_html__( 'Activate the Gravitas Coaching theme to access its one-click starter-site importer.', 'gravitas-core' ) . '</p></section>';
		$this->footer();
	}

	public function status() {
		$this->header( __( 'System Status', 'gravitas-core' ), __( 'Review the environment before importing media or launching memberships.', 'gravitas-core' ) );
		$upload = wp_upload_dir();
		$rows = array(
			__( 'WordPress version', 'gravitas-core' ) => get_bloginfo( 'version' ),
			__( 'PHP version', 'gravitas-core' )       => PHP_VERSION,
			__( 'Memory limit', 'gravitas-core' )      => ini_get( 'memory_limit' ),
			__( 'Upload limit', 'gravitas-core' )      => size_format( wp_max_upload_size() ),
			__( 'Permalink structure', 'gravitas-core' ) => get_option( 'permalink_structure' ) ?: __( 'Plain (pretty permalinks recommended)', 'gravitas-core' ),
			__( 'Uploads writable', 'gravitas-core' )  => wp_is_writable( $upload['basedir'] ) ? __( 'Yes', 'gravitas-core' ) : __( 'No', 'gravitas-core' ),
			__( 'HTTPS', 'gravitas-core' )             => is_ssl() ? __( 'Enabled', 'gravitas-core' ) : __( 'Not enabled', 'gravitas-core' ),
			__( 'Active theme', 'gravitas-core' )      => wp_get_theme()->get( 'Name' ) . ' ' . wp_get_theme()->get( 'Version' ),
			__( 'Gravitas Video', 'gravitas-core' )    => defined( 'GRAVITAS_VIDEO_VERSION' ) ? GRAVITAS_VIDEO_VERSION : __( 'Inactive', 'gravitas-core' ),
		);
		echo '<section class="gravitas-panel"><table class="widefat striped gravitas-system-table"><tbody>';
		foreach ( $rows as $label => $value ) {
			printf( '<tr><th>%1$s</th><td>%2$s</td></tr>', esc_html( $label ), esc_html( $value ) );
		}
		echo '</tbody></table></section>';
		$this->footer();
	}

	public function welcome_notice() {
		if ( ! get_transient( 'gravitas_core_activated' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		delete_transient( 'gravitas_core_activated' );
		printf( '<div class="notice notice-success is-dismissible"><p><strong>%1$s</strong> %2$s <a href="%3$s">%4$s</a></p></div>', esc_html__( 'Gravitas Core is ready.', 'gravitas-core' ), esc_html__( 'Start with the dashboard or import the launch demo.', 'gravitas-core' ), esc_url( admin_url( 'admin.php?page=gravitas' ) ), esc_html__( 'Open Gravitas', 'gravitas-core' ) );
	}
}
