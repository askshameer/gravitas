<?php
/**
 * Bundled and recommended plugin installer.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_plugin_catalog() {
	return array(
		'bundled' => array(
			'gravitas-core' => array( 'name' => __( 'Gravitas Core', 'gravitas-coaching' ), 'file' => 'gravitas-core/gravitas-core.php', 'zip' => 'gravitas-core.zip', 'description' => __( 'Required dashboard, branding options, layout controls, diagnostics, and Elementor widgets.', 'gravitas-coaching' ) ),
			'gravitas-video' => array( 'name' => __( 'Gravitas Video', 'gravitas-coaching' ), 'file' => 'gravitas-video/gravitas-video.php', 'zip' => 'gravitas-video.zip', 'description' => __( 'Video library, player, channels, playlists, series, imports, submissions, engagement, ads, and membership gates.', 'gravitas-coaching' ) ),
		),
		'public' => array(
			'advanced-custom-fields' => array( 'name' => 'Advanced Custom Fields', 'file' => 'advanced-custom-fields/acf.php', 'description' => __( 'Build custom editorial fields using the registered Gravitas post types and taxonomies.', 'gravitas-coaching' ) ),
			'elementor' => array( 'name' => 'Elementor', 'file' => 'elementor/elementor.php', 'description' => __( 'Visual page building with Gravitas trainer, service, and video widgets.', 'gravitas-coaching' ) ),
			'woocommerce' => array( 'name' => 'WooCommerce', 'file' => 'woocommerce/woocommerce.php', 'description' => __( 'Sell coaching packages, products, downloads, and membership-linked access.', 'gravitas-coaching' ) ),
			'paid-memberships-pro' => array( 'name' => 'Paid Memberships Pro', 'file' => 'paid-memberships-pro/paid-memberships-pro.php', 'description' => __( 'Create membership levels used by protected Gravitas videos.', 'gravitas-coaching' ) ),
			'contact-form-7' => array( 'name' => 'Contact Form 7', 'file' => 'contact-form-7/wp-contact-form-7.php', 'description' => __( 'Add contact, booking, and consultation enquiry forms.', 'gravitas-coaching' ) ),
			'bbpress' => array( 'name' => 'bbPress', 'file' => 'bbpress/bbpress.php', 'description' => __( 'Add member discussion forums around lessons and programs.', 'gravitas-coaching' ) ),
			'buddypress' => array( 'name' => 'BuddyPress', 'file' => 'buddypress/bp-loader.php', 'description' => __( 'Add community profiles, groups, activity, and member networking.', 'gravitas-coaching' ) ),
			'ajax-search-lite' => array( 'name' => 'Ajax Search Lite', 'file' => 'ajax-search-lite/ajax-search-lite.php', 'description' => __( 'Optional instant site-wide search experience.', 'gravitas-coaching' ) ),
			'mailpoet' => array( 'name' => 'MailPoet', 'file' => 'mailpoet/mailpoet.php', 'description' => __( 'Email newsletters and new-video notifications.', 'gravitas-coaching' ) ),
			'wordpress-seo' => array( 'name' => 'Yoast SEO', 'file' => 'wordpress-seo/wp-seo.php', 'description' => __( 'Search metadata and sitemap controls.', 'gravitas-coaching' ) ),
		),
	);
}

function elevora_plugin_installer_menu() {
	add_theme_page( __( 'Gravitas Plugins', 'gravitas-coaching' ), __( 'Gravitas Plugins', 'gravitas-coaching' ), 'install_plugins', 'gravitas-plugins', 'elevora_render_plugin_installer' );
}
add_action( 'admin_menu', 'elevora_plugin_installer_menu' );

function elevora_plugin_status( $file ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( $file ) ) {
		return 'active';
	}
	return file_exists( WP_PLUGIN_DIR . '/' . $file ) ? 'installed' : 'missing';
}

function elevora_render_plugin_installer() {
	$catalog = elevora_plugin_catalog();
	?>
	<div class="wrap elevora-plugin-center">
		<h1><?php esc_html_e( 'Gravitas Plugin Center', 'gravitas-coaching' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Install the included Gravitas companion plugins and connect trusted public plugins for commerce, memberships, forms, community, and marketing. The companion plugins ship inside the theme, so no separate upload is needed.', 'gravitas-coaching' ); ?></p>
		<?php if ( isset( $_GET['plugin-result'] ) ) : ?><div class="notice notice-<?php echo 'success' === $_GET['plugin-result'] ? 'success' : 'error'; ?> is-dismissible"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['message'] ?? '' ) ) ); ?></p></div><?php endif; ?>
		<h2><?php esc_html_e( 'Gravitas companion plugins', 'gravitas-coaching' ); ?></h2>
		<div class="elevora-plugin-grid">
			<?php foreach ( $catalog['bundled'] as $slug => $plugin ) : $status = elevora_plugin_status( $plugin['file'] ); ?>
			<section class="elevora-plugin-card"><div class="elevora-plugin-card__icon"><span class="dashicons <?php echo 'gravitas-video' === $slug ? 'dashicons-format-video' : 'dashicons-admin-customizer'; ?>"></span></div><div><h3><?php echo esc_html( $plugin['name'] ); ?> <span class="elevora-plugin-status is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( ucfirst( $status ) ); ?></span></h3><p><?php echo esc_html( $plugin['description'] ); ?></p><?php echo elevora_plugin_action_button( $slug, $plugin, $status, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></section>
			<?php endforeach; ?>
		</div>
		<h2><?php esc_html_e( 'Recommended public plugins', 'gravitas-coaching' ); ?></h2>
		<div class="elevora-plugin-grid is-public">
			<?php foreach ( $catalog['public'] as $slug => $plugin ) : $status = elevora_plugin_status( $plugin['file'] ); ?>
			<section class="elevora-plugin-card"><div><h3><?php echo esc_html( $plugin['name'] ); ?> <span class="elevora-plugin-status is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( ucfirst( $status ) ); ?></span></h3><p><?php echo esc_html( $plugin['description'] ); ?></p><?php echo elevora_plugin_action_button( $slug, $plugin, $status, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></section>
			<?php endforeach; ?>
		</div>
	</div>
	<style>.elevora-plugin-center{max-width:1150px}.elevora-plugin-center>.description{max-width:820px;font-size:15px}.elevora-plugin-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin:15px 0 30px}.elevora-plugin-grid.is-public{grid-template-columns:repeat(3,1fr)}.elevora-plugin-card{display:flex;gap:18px;padding:22px;border:1px solid #dce3e0;border-radius:13px;background:#fff;box-shadow:0 5px 18px rgba(16,35,31,.04)}.elevora-plugin-card__icon{display:grid;place-items:center;flex:0 0 52px;height:52px;border-radius:12px;background:#edf5f2;color:#e8674f}.elevora-plugin-card__icon .dashicons{font-size:26px;width:26px;height:26px}.elevora-plugin-card h3{margin:0 0 8px;font-size:17px}.elevora-plugin-card p{min-height:42px;color:#64716d}.elevora-plugin-status{padding:3px 7px;border-radius:12px;background:#f0f0f1;color:#646970;font-size:10px;text-transform:uppercase}.elevora-plugin-status.is-active{background:#e7f7eb;color:#167333}.elevora-plugin-status.is-installed{background:#fff4dd;color:#8a5b00}@media(max-width:1050px){.elevora-plugin-grid.is-public{grid-template-columns:1fr 1fr}}@media(max-width:700px){.elevora-plugin-grid,.elevora-plugin-grid.is-public{grid-template-columns:1fr}}</style>
	<?php
}

function elevora_plugin_action_button( $slug, $plugin, $status, $bundled ) {
	if ( 'active' === $status ) {
		return '<span class="button disabled">' . esc_html__( 'Active', 'gravitas-coaching' ) . '</span>';
	}
	if ( 'installed' === $status ) {
		$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin['file'] ) ), 'activate-plugin_' . $plugin['file'] );
		return '<a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Activate', 'gravitas-coaching' ) . '</a>';
	}
	if ( $bundled ) {
		$url = wp_nonce_url( add_query_arg( array( 'action' => 'elevora_install_bundled_plugin', 'plugin' => $slug ), admin_url( 'admin-post.php' ) ), 'elevora_install_' . $slug );
		return '<a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Install & Activate', 'gravitas-coaching' ) . '</a>';
	}
	$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $slug ) ), 'install-plugin_' . $slug );
	return '<a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Install', 'gravitas-coaching' ) . '</a>';
}

function elevora_install_bundled_plugin() {
	$slug    = sanitize_key( wp_unslash( $_GET['plugin'] ?? '' ) );
	$catalog = elevora_plugin_catalog();
	if ( ! current_user_can( 'install_plugins' ) || empty( $catalog['bundled'][ $slug ] ) ) {
		wp_die( esc_html__( 'You cannot install this plugin.', 'gravitas-coaching' ) );
	}
	check_admin_referer( 'elevora_install_' . $slug );
	$result = elevora_install_bundled_plugin_now( $slug );
	if ( is_wp_error( $result ) ) {
		elevora_plugin_result_redirect( false, $result->get_error_message() );
	}
	elevora_plugin_result_redirect( true, sprintf( __( '%s was installed and activated.', 'gravitas-coaching' ), $catalog['bundled'][ $slug ]['name'] ) );
}
add_action( 'admin_post_elevora_install_bundled_plugin', 'elevora_install_bundled_plugin' );

/**
 * Install and activate a bundled companion plugin without redirecting.
 *
 * @param string $slug Bundled plugin slug.
 * @return true|WP_Error
 */
function elevora_install_bundled_plugin_now( $slug ) {
	$catalog = elevora_plugin_catalog();
	if ( empty( $catalog['bundled'][ $slug ] ) ) {
		return new WP_Error( 'invalid_plugin', __( 'The requested bundled plugin is not available.', 'gravitas-coaching' ) );
	}
	$plugin = $catalog['bundled'][ $slug ];
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$installed_file = elevora_find_installed_plugin_file( $plugin['file'], $slug );
	if ( $installed_file ) {
		$activated = is_plugin_active( $installed_file ) ? true : activate_plugin( $installed_file );
		return is_wp_error( $activated ) ? $activated : true;
	}

	$zip    = ELEVORA_DIR . '/inc/plugins/' . $plugin['zip'];
	$source = ELEVORA_DIR . '/inc/plugins/' . $slug;
	if ( ! file_exists( $zip ) && ! is_dir( $source ) ) {
		return new WP_Error( 'missing_package', __( 'The bundled plugin package is missing. Reinstall the complete theme package.', 'gravitas-coaching' ) );
	}
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$result = false;
	$error  = '';

	if ( file_exists( $zip ) ) {
		$skin     = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $zip, array( 'overwrite_package' => true ) );
		if ( is_wp_error( $result ) ) {
			$error = $result->get_error_message();
		} elseif ( ! $result ) {
			$skin_errors = $skin->get_errors();
			if ( is_wp_error( $skin_errors ) && $skin_errors->has_errors() ) {
				$error = $skin_errors->get_error_message();
			}
		}
	}

	if ( ( is_wp_error( $result ) || ! $result ) && is_dir( $source ) ) {
		$fallback = elevora_copy_bundled_plugin( $source, $slug );
		if ( true === $fallback ) {
			$result = true;
		} elseif ( is_wp_error( $fallback ) ) {
			$error = $fallback->get_error_message();
		}
	}

	if ( is_wp_error( $result ) || ! $result ) {
		return new WP_Error( 'install_failed', $error ? $error : __( 'WordPress could not install the bundled plugin package. Check filesystem permissions.', 'gravitas-coaching' ) );
	}
	$plugin_file = elevora_find_installed_plugin_file( $plugin['file'], $slug );
	if ( ! $plugin_file ) {
		return new WP_Error( 'plugin_not_found', __( 'The plugin was unpacked, but WordPress could not locate its main plugin file.', 'gravitas-coaching' ) );
	}
	$activated = activate_plugin( $plugin_file );
	if ( is_wp_error( $activated ) ) {
		return $activated;
	}
	return true;
}

/**
 * Copy a prepackaged plugin directory when the ZIP upgrader is unavailable.
 *
 * @param string $source Absolute source directory.
 * @param string $slug   Plugin directory name.
 * @return true|WP_Error
 */
function elevora_copy_bundled_plugin( $source, $slug ) {
	global $wp_filesystem;

	require_once ABSPATH . 'wp-admin/includes/file.php';
	if ( ! WP_Filesystem() || ! $wp_filesystem ) {
		return new WP_Error( 'filesystem_unavailable', __( 'WordPress could not access the plugins directory.', 'gravitas-coaching' ) );
	}

	$destination = trailingslashit( WP_PLUGIN_DIR ) . sanitize_file_name( $slug );
	if ( ! $wp_filesystem->is_dir( $destination ) && ! $wp_filesystem->mkdir( $destination, FS_CHMOD_DIR ) ) {
		return new WP_Error( 'plugin_directory_failed', __( 'WordPress could not create the plugin directory. Check wp-content/plugins permissions.', 'gravitas-coaching' ) );
	}

	$copied = copy_dir( trailingslashit( $source ), trailingslashit( $destination ) );
	return is_wp_error( $copied ) ? $copied : true;
}

/**
 * Locate an installed bundled plugin even if WordPress renamed its folder.
 *
 * @param string $expected_file Expected plugin basename.
 * @param string $slug          Bundled plugin slug.
 * @return string
 */
function elevora_find_installed_plugin_file( $expected_file, $slug ) {
	$expected_path = WP_PLUGIN_DIR . '/' . $expected_file;
	if ( file_exists( $expected_path ) ) {
		return $expected_file;
	}

	$plugins = get_plugins();
	foreach ( $plugins as $plugin_file => $headers ) {
		$text_domain = isset( $headers['TextDomain'] ) ? sanitize_key( $headers['TextDomain'] ) : '';
		$directory   = dirname( $plugin_file );
		if ( $slug === $text_domain || 0 === strpos( $directory, $slug ) ) {
			return $plugin_file;
		}
	}

	return '';
}

/**
 * Install and activate a recommended WordPress.org plugin during guided setup.
 *
 * @param string $slug Plugin repository slug.
 * @return true|WP_Error
 */
function elevora_install_public_plugin_now( $slug ) {
	$catalog = elevora_plugin_catalog();
	if ( empty( $catalog['public'][ $slug ] ) ) {
		return new WP_Error( 'invalid_public_plugin', __( 'The requested recommended plugin is not available.', 'gravitas-coaching' ) );
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$plugin = $catalog['public'][ $slug ];
	if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] ) ) {
		$result = is_plugin_active( $plugin['file'] ) ? true : activate_plugin( $plugin['file'] );
		return is_wp_error( $result ) ? $result : true;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
	if ( is_wp_error( $api ) ) {
		return $api;
	}
	$skin     = new Automatic_Upgrader_Skin();
	$upgrader = new Plugin_Upgrader( $skin );
	$result   = $upgrader->install( $api->download_link );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	if ( ! $result || ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['file'] ) ) {
		return new WP_Error( 'public_install_failed', sprintf( __( '%s could not be installed automatically.', 'gravitas-coaching' ), $plugin['name'] ) );
	}
	$activated = activate_plugin( $plugin['file'] );
	return is_wp_error( $activated ) ? $activated : true;
}

function elevora_plugin_result_redirect( $success, $message ) {
	wp_safe_redirect( add_query_arg( array( 'page' => 'gravitas-plugins', 'plugin-result' => $success ? 'success' : 'error', 'message' => rawurlencode( $message ) ), admin_url( 'themes.php' ) ) );
	exit;
}

