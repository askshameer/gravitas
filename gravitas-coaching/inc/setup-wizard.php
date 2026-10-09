<?php
/**
 * Guided first-run setup.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_setup_wizard_menu() {
	add_theme_page( __( 'Gravitas Setup', 'gravitas-coaching' ), __( 'Gravitas Setup', 'gravitas-coaching' ), 'manage_options', 'elevora-setup', 'elevora_render_setup_wizard' );
}
add_action( 'admin_menu', 'elevora_setup_wizard_menu' );

function elevora_setup_wizard_assets( $hook ) {
	if ( 'appearance_page_elevora-setup' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'elevora-setup-wizard', ELEVORA_URI . '/assets/css/setup-wizard.css', array(), ELEVORA_VERSION );
	wp_enqueue_script( 'elevora-setup-wizard', ELEVORA_URI . '/assets/js/setup-wizard.js', array(), ELEVORA_VERSION, true );
	wp_localize_script(
		'elevora-setup-wizard',
		'elevoraSetupWizard',
		array(
			'stages' => array(
				__( 'Preparing your site…', 'gravitas-coaching' ),
				__( 'Installing companion plugins…', 'gravitas-coaching' ),
				__( 'Applying theme settings…', 'gravitas-coaching' ),
				__( 'Importing demo content and media…', 'gravitas-coaching' ),
				__( 'Finishing your site…', 'gravitas-coaching' ),
			),
			'working' => __( 'Setup in progress…', 'gravitas-coaching' ),
			'complete' => __( 'Setup complete. Opening your site tools…', 'gravitas-coaching' ),
			'error' => __( 'Setup could not be completed. Your current site has not been removed. Please try again or review the server error log.', 'gravitas-coaching' ),
			'retry' => __( 'Try guided setup again', 'gravitas-coaching' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'elevora_setup_wizard_assets' );

function elevora_render_setup_wizard() {
	$complete = isset( $_GET['complete'] );
	$warning  = isset( $_GET['warning'] ) ? sanitize_text_field( wp_unslash( $_GET['warning'] ) ) : '';
	$home_edit = $complete && function_exists( 'elevora_get_elementor_edit_url' ) ? elevora_get_elementor_edit_url( (int) get_option( 'page_on_front' ) ) : '';
	?>
	<div class="wrap elevora-setup">
		<div class="elevora-setup__masthead"><span><?php esc_html_e( 'Gravitas Coaching', 'gravitas-coaching' ); ?></span><strong><?php esc_html_e( 'Guided setup', 'gravitas-coaching' ); ?></strong></div>
		<?php if ( $complete ) : ?>
			<section class="elevora-setup__panel elevora-setup__complete">
				<div class="elevora-setup__check">&#10003;</div>
				<h1><?php esc_html_e( 'Your site is ready', 'gravitas-coaching' ); ?></h1>
				<p><?php esc_html_e( 'The recommended design, companion plugins, demo content, navigation, and Elementor-ready homepage have been configured.', 'gravitas-coaching' ); ?></p>
				<?php if ( $warning ) : ?><div class="notice notice-warning inline"><p><?php echo esc_html( $warning ); ?></p></div><?php endif; ?>
				<div class="elevora-setup__actions"><?php if ( $home_edit ) : ?><a class="button button-primary button-hero" href="<?php echo esc_url( $home_edit ); ?>"><?php esc_html_e( 'Edit homepage with Elementor', 'gravitas-coaching' ); ?></a><?php endif; ?><a class="button <?php echo $home_edit ? '' : 'button-primary'; ?> button-hero" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'View site', 'gravitas-coaching' ); ?></a><a class="button button-hero" href="<?php echo esc_url( admin_url( 'admin.php?page=gravitas-options' ) ); ?>"><?php esc_html_e( 'Theme Options', 'gravitas-coaching' ); ?></a></div>
			</section>
		<?php else : ?>
			<section class="elevora-setup__panel">
				<p class="elevora-setup__eyebrow"><?php esc_html_e( 'A polished starting point in one step', 'gravitas-coaching' ); ?></p>
				<h1><?php esc_html_e( 'Set up Gravitas for me', 'gravitas-coaching' ); ?></h1>
				<p class="elevora-setup__lead"><?php esc_html_e( 'This guided setup applies the recommended professional typography and layouts, installs the included Core and Video plugins, and imports the complete editable demo.', 'gravitas-coaching' ); ?></p>
				<div class="elevora-setup__grid">
					<div><span class="dashicons dashicons-admin-appearance"></span><h2><?php esc_html_e( 'Professional design', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Neutral sans typography, compact titles, clean navigation, and balanced spacing.', 'gravitas-coaching' ); ?></p></div>
					<div><span class="dashicons dashicons-admin-plugins"></span><h2><?php esc_html_e( 'Included features', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Activates Gravitas Core and Gravitas Video. Elementor is optional and can be installed separately from the Plugin Center.', 'gravitas-coaching' ); ?></p></div>
					<div><span class="dashicons dashicons-edit-page"></span><h2><?php esc_html_e( 'Editable starter site', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Imports the original demo design with Elementor-ready pages and focused native editors for Gravitas content.', 'gravitas-coaching' ); ?></p></div>
				</div>
				<form class="elevora-setup__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'elevora_run_guided_setup', 'elevora_setup_nonce' ); ?>
					<input type="hidden" name="action" value="elevora_run_guided_setup">
					<button class="button button-primary button-hero elevora-setup__submit" type="submit"><span class="elevora-setup__button-label"><?php esc_html_e( 'Start guided setup', 'gravitas-coaching' ); ?></span><span class="elevora-setup__spinner" aria-hidden="true"></span></button>
					<a class="elevora-setup__skip" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>"><?php esc_html_e( 'Skip for now', 'gravitas-coaching' ); ?></a>
					<div class="elevora-setup__progress" hidden aria-live="polite">
						<div class="elevora-setup__progress-heading"><strong class="elevora-setup__status"><?php esc_html_e( 'Preparing your site…', 'gravitas-coaching' ); ?></strong><span class="elevora-setup__percentage">0%</span></div>
						<div class="elevora-setup__track" role="progressbar" aria-label="<?php esc_attr_e( 'Guided setup progress', 'gravitas-coaching' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><span></span></div>
						<p><?php esc_html_e( 'Keep this page open. This can take a few minutes depending on your server and internet connection.', 'gravitas-coaching' ); ?></p>
					</div>
					<div class="elevora-setup__error" role="alert" hidden></div>
				</form>
				<p class="elevora-setup__note"><?php esc_html_e( 'Safe to run again: existing content is preserved and missing demo items are added.', 'gravitas-coaching' ); ?></p>
			</section>
		<?php endif; ?>
	</div>
	<?php
}

function elevora_run_guided_setup() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to run setup.', 'gravitas-coaching' ) );
	}
	check_admin_referer( 'elevora_run_guided_setup', 'elevora_setup_nonce' );

	$warnings = array();
	foreach ( array( 'gravitas-core', 'gravitas-video' ) as $plugin ) {
		$result = elevora_install_bundled_plugin_now( $plugin );
		if ( is_wp_error( $result ) ) {
			$warnings[] = $result->get_error_message();
		}
	}

	$options = (array) get_option( 'gravitas_core_options', array() );
	$options = array_merge(
		$options,
		array(
			'body_font'         => 'professional',
			'heading_font'      => 'professional',
			'nav_font'          => 'professional',
			'body_size'         => 16,
			'page_header_height'=> 160,
			'page_title_size'   => 44,
			'post_header_height'=> 160,
			'post_title_size'   => 48,
			'header_layout'     => 'standard',
			'sticky_header'     => 1,
			'blog_layout'       => 'editorial',
			'blog_pagination'   => 'load_more',
			'sidebar_position'  => 'none',
			'single_sidebar'    => 'right',
		)
	);
	update_option( 'gravitas_core_options', $options, false );
	update_option( 'gravitas_typography_refresh_v2', 1, false );
	update_option( 'elevora_setup_complete', current_time( 'mysql' ), false );
	elevora_elementor_post_types();

	// Reuse the idempotent complete-demo importer after setup has prepared plugins and options.
	$_POST['demo']                  = 'complete';
	$_POST['elevora_import_nonce']  = wp_create_nonce( 'elevora_import_demo' );
	$_REQUEST['elevora_import_nonce'] = $_POST['elevora_import_nonce'];
	$_POST['elevora_setup_running'] = '1';
	if ( $warnings ) {
		$_POST['elevora_setup_warning'] = implode( ' ', array_unique( $warnings ) );
	}
	elevora_import_demo();
}
add_action( 'admin_post_elevora_run_guided_setup', 'elevora_run_guided_setup' );
