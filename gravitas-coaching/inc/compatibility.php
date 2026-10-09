<?php
/**
 * Optional plugin compatibility.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'elevora_elementor_locations' );

/**
 * Limit Elementor editing to standard pages and blog posts.
 *
 * @param string[] $post_types Existing Elementor-supported post types.
 * @return string[]
 */
function elevora_elementor_supported_post_types( $post_types ) {
	return array( 'page', 'post' );
}
add_filter( 'option_elementor_cpt_support', 'elevora_elementor_supported_post_types', 20 );
add_filter( 'pre_update_option_elementor_cpt_support', 'elevora_elementor_supported_post_types', 20 );

/**
 * Persist supported post types so Elementor's settings UI and editor agree.
 */
function elevora_elementor_post_types() {
	$post_types = (array) get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
	$updated    = elevora_elementor_supported_post_types( $post_types );
	if ( $post_types !== $updated ) {
		update_option( 'elementor_cpt_support', $updated, false );
	}
}
add_action( 'after_switch_theme', 'elevora_elementor_post_types' );
add_action( 'elementor/init', 'elevora_elementor_post_types' );
add_action( 'init', 'elevora_elementor_post_types', 99 );

/**
 * Get the Elementor editor URL for a post when the current user may edit it.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function elevora_get_elementor_edit_url( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) || ! did_action( 'elementor/loaded' ) || ! class_exists( '\\Elementor\\Plugin' ) ) {
		return '';
	}

	$post = get_post( $post_id );
	if ( ! $post || ! in_array( $post->post_type, elevora_elementor_supported_post_types( array() ), true ) ) {
		return '';
	}

	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document ? $document->get_edit_url() : '';
}

/**
 * Guarantee an Elementor action on every supported post and page list row.
 *
 * @param string[] $actions Row actions.
 * @param WP_Post  $post    Current post.
 * @return string[]
 */
function elevora_elementor_row_action( $actions, $post ) {
	if ( isset( $actions['edit_with_elementor'] ) ) {
		return $actions;
	}

	$url = elevora_get_elementor_edit_url( $post->ID );
	if ( $url ) {
		$actions['edit_with_elementor'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Edit with Elementor', 'gravitas-coaching' ) . '</a>';
	}

	return $actions;
}
add_filter( 'page_row_actions', 'elevora_elementor_row_action', 20, 2 );
add_filter( 'post_row_actions', 'elevora_elementor_row_action', 20, 2 );

/**
 * Add a prominent Elementor button to the classic post editing screen.
 */
function elevora_elementor_edit_screen_button() {
	global $post;
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	$url = elevora_get_elementor_edit_url( $post->ID );
	if ( $url ) {
		echo '<div class="misc-pub-section"><a class="button button-primary button-large" href="' . esc_url( $url ) . '">' . esc_html__( 'Edit with Elementor', 'gravitas-coaching' ) . '</a></div>';
	}
}
add_action( 'post_submitbox_misc_actions', 'elevora_elementor_edit_screen_button' );

/**
 * Add an Elementor shortcut while viewing any supported singular item.
 *
 * @param WP_Admin_Bar $admin_bar Admin toolbar instance.
 */
function elevora_elementor_admin_bar_link( $admin_bar ) {
	$post_id = 0;
	if ( is_admin() ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'post' === $screen->base && isset( $_GET['post'] ) ) {
			$post_id = absint( wp_unslash( $_GET['post'] ) );
		}
	} elseif ( is_singular() ) {
		$post_id = get_queried_object_id();
	}

	$url = elevora_get_elementor_edit_url( $post_id );
	if ( $url ) {
		$admin_bar->add_node(
			array(
				'id'    => 'elevora-edit-with-elementor',
				'title' => __( 'Edit with Elementor', 'gravitas-coaching' ),
				'href'  => $url,
			)
		);
	}
}
add_action( 'admin_bar_menu', 'elevora_elementor_admin_bar_link', 90 );

/**
 * Keep theme spacing out of Elementor canvas and full-width content.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function elevora_elementor_page_classes( $classes ) {
	if ( is_singular( array( 'page', 'post' ) ) && did_action( 'elementor/loaded' ) && class_exists( '\\Elementor\\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get( get_queried_object_id() );
		if ( $document && $document->is_built_with_elementor() ) {
			$classes[] = 'elevora-elementor-page';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'elevora_elementor_page_classes' );

/**
 * Determine whether the current document was built with Elementor.
 *
 * @return bool
 */
function elevora_is_elementor_document() {
	if ( ! is_singular( array( 'page', 'post' ) ) || ! did_action( 'elementor/loaded' ) || ! class_exists( '\\Elementor\\Plugin' ) ) {
		return false;
	}

	if ( isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		return true;
	}

	if ( isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		return true;
	}

	$document = \Elementor\Plugin::$instance->documents->get( get_queried_object_id() );

	return $document && $document->is_built_with_elementor();
}

/**
 * Render the current Elementor document without native theme constraints.
 *
 * This is the Elementor Free fallback. Elementor Pro Theme Builder locations
 * are checked by each template before this function is called.
 *
 * @return bool True when an Elementor document was rendered.
 */
function elevora_render_elementor_document() {
	if ( ! elevora_is_elementor_document() ) {
		return false;
	}

	while ( have_posts() ) {
		the_post();
		$elementor_data = (string) get_post_meta( get_the_ID(), '_elementor_data', true );
		if ( is_front_page() && false !== strpos( $elementor_data, 'gravitas-homepage' ) ) {
			// The homepage widget already prints the exact native <main> markup.
			the_content();
			continue;
		}
		?>
		<main id="primary" class="site-main elevora-elementor-document">
			<article <?php post_class( 'elevora-elementor-content' ); ?>>
				<?php the_content(); ?>
			</article>
		</main>
		<?php
	}

	return true;
}

function elevora_woocommerce_setup_wrappers() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'elevora_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'elevora_woocommerce_wrapper_end', 10 );
}
add_action( 'after_setup_theme', 'elevora_woocommerce_setup_wrappers' );

function elevora_woocommerce_wrapper_start() {
	echo '<main id="primary" class="site-main shop-main"><div class="container">';
}

function elevora_woocommerce_wrapper_end() {
	echo '</div></main>';
}

function elevora_woocommerce_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'elevora_woocommerce_products_per_page' );

function elevora_add_plugin_body_classes( $classes ) {
	if ( function_exists( 'buddypress' ) ) {
		$classes[] = 'elevora-buddypress';
	}
	if ( class_exists( 'bbPress' ) ) {
		$classes[] = 'elevora-bbpress';
	}
	if ( function_exists( 'pmpro_has_membership_access' ) || function_exists( 'pmpro_hasMembershipLevel' ) || function_exists( 'wc_memberships' ) ) {
		$classes[] = 'elevora-memberships';
	}
	if ( function_exists( 'tutor' ) ) {
		$classes[] = 'elevora-tutor-lms';
	}
	if ( defined( 'IHC_PATH' ) || defined( 'IHC_PLUGIN_NAME' ) ) {
		$classes[] = 'elevora-ultimate-membership';
	}
	if ( class_exists( 'wp_ep_fitness' ) || defined( 'wp_ep_fitness_DIR' ) ) {
		$classes[] = 'elevora-fitness-trainer';
	}

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$content = (string) get_post_field( 'post_content', $post_id );
		$builder = (string) get_post_meta( $post_id, '_elementor_data', true );
		$plugin_shortcodes = array(
			'tutor_dashboard', 'tutor_course', 'tutor_student_registration_form', 'tutor_instructor_registration_form', 'tutor_instructor_list', 'tutor_cart', 'tutor_checkout',
			'ihc-user-page', 'ihc-register', 'ihc-register-lite', 'ihc-login-form', 'ihc-pass-reset', 'ihc-select-level', 'ihc-checkout-page', 'ihc-thank-you-page', 'ihc-list-users', 'ihc-membership-card', 'ihc-edit-profile-form',
			'ep_fitness_profile_template', 'ep_fitness_profile_public', 'ep_fitness_form_wizard', 'ep_fitness_price_table', 'ep_fitness_user_directory', 'ep_fitness_login',
		);
		foreach ( $plugin_shortcodes as $shortcode ) {
			if ( has_shortcode( $content, $shortcode ) || false !== strpos( $builder, '[' . $shortcode ) ) {
				$classes[] = 'elevora-plugin-page';
				break;
			}
		}
	}
	return $classes;
}
add_filter( 'body_class', 'elevora_add_plugin_body_classes' );

/** Let Tutor LMS identify the sticky Gravitas header in learning mode. */
function elevora_tutor_header_selector() {
	return '.site-header';
}
add_filter( 'tutor_theme_header_selector', 'elevora_tutor_header_selector' );

/**
 * Keep plugin-managed learning, account, and checkout post types out of the
 * native blog single layout when Elementor asks for supported post types.
 * Their plugins own those templates and navigation flows.
 *
 * @param string[] $post_types Elementor-enabled post types.
 * @return string[]
 */
function elevora_filter_plugin_elementor_post_types( $post_types ) {
	$plugin_owned = array( 'lesson', 'tutor_quiz', 'tutor_assignments', 'tutor_enrolled', 'tutor_order', 'physical-record', 'iv_payment' );
	return array_values( array_diff( (array) $post_types, $plugin_owned ) );
}
add_filter( 'option_elementor_cpt_support', 'elevora_filter_plugin_elementor_post_types', 100 );
add_filter( 'pre_update_option_elementor_cpt_support', 'elevora_filter_plugin_elementor_post_types', 100 );
