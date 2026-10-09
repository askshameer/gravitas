<?php
/**
 * Content grids and front-end trainer submission.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_trainers_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'count' => 6, 'specialization' => '', 'columns' => 3 ), $atts, 'elevora_trainers' );
	$args = array( 'post_type' => 'trainer', 'posts_per_page' => min( 24, max( 1, absint( $atts['count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	if ( $atts['specialization'] ) {
		$args['tax_query'] = array( array( 'taxonomy' => 'trainer_specialization', 'field' => 'slug', 'terms' => sanitize_title( $atts['specialization'] ) ) );
	}
	$query   = new WP_Query( $args );
	$columns = in_array( absint( $atts['columns'] ), array( 2, 3, 4 ), true ) ? absint( $atts['columns'] ) : 3;
	ob_start();
	if ( $query->have_posts() ) {
		echo '<div class="card-grid card-grid--' . esc_attr( array( 2 => 'two', 3 => 'three', 4 => 'four' )[ $columns ] ) . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/card', 'trainer' );
		}
		echo '</div>';
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'elevora_trainers', 'elevora_trainers_shortcode' );

function elevora_services_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'count' => 6, 'category' => '', 'columns' => 3 ), $atts, 'elevora_services' );
	$args  = array( 'post_type' => 'coaching_service', 'posts_per_page' => min( 24, max( 1, absint( $atts['count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	if ( $atts['category'] ) {
		$args['tax_query'] = array( array( 'taxonomy' => 'service_category', 'field' => 'slug', 'terms' => sanitize_title( $atts['category'] ) ) );
	}
	$query   = new WP_Query( $args );
	$columns = in_array( absint( $atts['columns'] ), array( 2, 3 ), true ) ? absint( $atts['columns'] ) : 3;
	ob_start();
	if ( $query->have_posts() ) {
		echo '<div class="card-grid card-grid--' . esc_attr( 2 === $columns ? 'two' : 'three' ) . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/card', 'service' );
		}
		echo '</div>';
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'elevora_services', 'elevora_services_shortcode' );

/**
 * Display blog posts in any page builder or block content area.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function elevora_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'    => 6,
			'category' => '',
			'layout'   => 'grid',
		),
		$atts,
		'elevora_posts'
	);
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => min( 24, max( 1, absint( $atts['count'] ) ) ),
		'ignore_sticky_posts' => true,
	);
	if ( $atts['category'] ) {
		$args['category_name'] = sanitize_title( $atts['category'] );
	}

	$query  = new WP_Query( $args );
	$layout = elevora_sanitize_blog_layout( $atts['layout'] );
	ob_start();
	if ( $query->have_posts() ) {
		$post_index = 0;
		echo '<div class="blog-loop blog-loop--' . esc_attr( $layout ) . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			++$post_index;
			$card_class = '';
			if ( 'editorial' === $layout && 1 === $post_index ) {
				$card_class = 'post-card--featured';
			} elseif ( 'magazine' === $layout && in_array( $post_index, array( 1, 6 ), true ) ) {
				$card_class = 'post-card--featured';
			}
			get_template_part( 'template-parts/card', 'post', array( 'class' => $card_class ) );
		}
		echo '</div>';
	} else {
		get_template_part( 'template-parts/content', 'none' );
	}
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'elevora_posts', 'elevora_posts_shortcode' );

/**
 * Render the homepage consultant finder inside Elementor or post content.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function elevora_home_finder_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'eyebrow'    => __( 'Start here', 'gravitas-coaching' ),
			'title'      => __( 'What would you like to strengthen?', 'gravitas-coaching' ),
			'placeholder'=> __( 'Skill, goal, or consultant', 'gravitas-coaching' ),
			'button'     => __( 'Find a match', 'gravitas-coaching' ),
		),
		$atts,
		'elevora_home_finder'
	);
	$specializations = get_terms( array( 'taxonomy' => 'trainer_specialization', 'hide_empty' => false ) );
	ob_start();
	?>
	<form class="finder-form" action="<?php echo esc_url( get_post_type_archive_link( 'trainer' ) ); ?>" method="get">
		<div class="finder-form__lead"><span><?php echo esc_html( $atts['eyebrow'] ); ?></span><strong><?php echo esc_html( $atts['title'] ); ?></strong></div>
		<label class="screen-reader-text" for="elementor-trainer-search"><?php esc_html_e( 'Search trainers', 'gravitas-coaching' ); ?></label>
		<input id="elementor-trainer-search" type="search" name="trainer_search" placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>">
		<label class="screen-reader-text" for="elementor-specialization"><?php esc_html_e( 'Specialization', 'gravitas-coaching' ); ?></label>
		<select id="elementor-specialization" name="specialization"><option value=""><?php esc_html_e( 'All specializations', 'gravitas-coaching' ); ?></option><?php foreach ( is_wp_error( $specializations ) ? array() : $specializations as $term ) : ?><option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select>
		<button class="button button--dark" type="submit"><?php echo esc_html( $atts['button'] ); ?><?php echo elevora_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'elevora_home_finder', 'elevora_home_finder_shortcode' );

/**
 * Display testimonial cards in builder content.
 *
 * @param array<string, mixed> $atts Shortcode attributes.
 * @return string
 */
function elevora_testimonials_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'count' => 3 ), $atts, 'elevora_testimonials' );
	$query = new WP_Query( array( 'post_type' => 'testimonial', 'posts_per_page' => min( 12, max( 1, absint( $atts['count'] ) ) ), 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
	ob_start();
	if ( $query->have_posts() ) {
		echo '<div class="testimonial-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<figure class="testimonial-card"><?php echo elevora_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><blockquote><?php the_content(); ?></blockquote><figcaption><strong><?php the_title(); ?></strong><span><?php echo esc_html( trim( elevora_get_field_value( 'elevora_client_role' ) . ', ' . elevora_get_field_value( 'elevora_client_company' ), ', ' ) ); ?></span></figcaption></figure>
			<?php
		}
		echo '</div>';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'elevora_testimonials', 'elevora_testimonials_shortcode' );

function elevora_trainer_submission_shortcode() {
	if ( ! is_user_logged_in() ) {
		return '<div class="submission-login"><h2>' . esc_html__( 'Apply to join Gravitas Coaching', 'gravitas-coaching' ) . '</h2><p>' . esc_html__( 'Create or sign in to your account before submitting a consultant profile.', 'gravitas-coaching' ) . '</p><a class="button button--dark" href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' . esc_html__( 'Sign in to continue', 'gravitas-coaching' ) . '</a></div>';
	}
	$specializations = get_terms( array( 'taxonomy' => 'trainer_specialization', 'hide_empty' => false ) );
	ob_start();
	if ( isset( $_GET['trainer_submitted'] ) ) : ?>
		<div class="submission-success"><h2><?php esc_html_e( 'Thank you for applying.', 'gravitas-coaching' ); ?></h2><p><?php esc_html_e( 'Your profile is pending review. The site team will be in touch with next steps.', 'gravitas-coaching' ); ?></p></div>
	<?php else : ?>
		<form class="trainer-submission-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<?php wp_nonce_field( 'elevora_submit_trainer', 'elevora_submission_nonce' ); ?>
			<input type="hidden" name="action" value="elevora_submit_trainer">
			<div class="form-grid"><label><?php esc_html_e( 'Profile name', 'gravitas-coaching' ); ?><input type="text" name="trainer_name" required></label><label><?php esc_html_e( 'Professional title', 'gravitas-coaching' ); ?><input type="text" name="trainer_role" required></label><label><?php esc_html_e( 'Public email', 'gravitas-coaching' ); ?><input type="email" name="trainer_email" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" required></label><label><?php esc_html_e( 'Years of experience', 'gravitas-coaching' ); ?><input type="number" name="trainer_experience" min="0" max="80"></label></div>
			<label><?php esc_html_e( 'Short tagline', 'gravitas-coaching' ); ?><input type="text" name="trainer_tagline" maxlength="160" required></label>
			<label><?php esc_html_e( 'Professional biography', 'gravitas-coaching' ); ?><textarea name="trainer_bio" rows="9" required></textarea></label>
			<?php if ( ! is_wp_error( $specializations ) && $specializations ) : ?><fieldset><legend><?php esc_html_e( 'Specializations', 'gravitas-coaching' ); ?></legend><div class="checkbox-grid"><?php foreach ( $specializations as $term ) : ?><label><input type="checkbox" name="trainer_specializations[]" value="<?php echo esc_attr( $term->term_id ); ?>"> <?php echo esc_html( $term->name ); ?></label><?php endforeach; ?></div></fieldset><?php endif; ?>
			<label class="consent-field"><input type="checkbox" name="trainer_consent" value="1" required> <?php esc_html_e( 'I confirm that this information is accurate and may be reviewed by the site administrator.', 'gravitas-coaching' ); ?></label>
			<button class="button button--accent" type="submit"><?php esc_html_e( 'Submit profile for review', 'gravitas-coaching' ); ?><?php echo elevora_icon( 'arrow' ); // phpcs:ignore ?></button>
		</form>
	<?php endif;
	return ob_get_clean();
}
add_shortcode( 'elevora_trainer_submission', 'elevora_trainer_submission_shortcode' );

function elevora_handle_trainer_submission() {
	if ( ! is_user_logged_in() ) {
		auth_redirect();
	}
	if ( ! isset( $_POST['elevora_submission_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elevora_submission_nonce'] ) ), 'elevora_submit_trainer' ) ) {
		wp_die( esc_html__( 'The submission could not be verified.', 'gravitas-coaching' ) );
	}
	if ( empty( $_POST['trainer_consent'] ) ) {
		wp_die( esc_html__( 'Please confirm the submission consent.', 'gravitas-coaching' ) );
	}
	$name = isset( $_POST['trainer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['trainer_name'] ) ) : '';
	$bio  = isset( $_POST['trainer_bio'] ) ? wp_kses_post( wp_unslash( $_POST['trainer_bio'] ) ) : '';
	if ( ! $name || ! $bio ) {
		wp_die( esc_html__( 'A name and biography are required.', 'gravitas-coaching' ) );
	}
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'trainer',
			'post_status'  => 'pending',
			'post_author'  => get_current_user_id(),
			'post_title'   => $name,
			'post_content' => $bio,
			'post_excerpt' => isset( $_POST['trainer_tagline'] ) ? sanitize_text_field( wp_unslash( $_POST['trainer_tagline'] ) ) : '',
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		wp_die( esc_html( $post_id->get_error_message() ) );
	}
	update_post_meta( $post_id, 'elevora_role', isset( $_POST['trainer_role'] ) ? sanitize_text_field( wp_unslash( $_POST['trainer_role'] ) ) : '' );
	update_post_meta( $post_id, 'elevora_tagline', isset( $_POST['trainer_tagline'] ) ? sanitize_text_field( wp_unslash( $_POST['trainer_tagline'] ) ) : '' );
	update_post_meta( $post_id, 'elevora_email', isset( $_POST['trainer_email'] ) ? sanitize_email( wp_unslash( $_POST['trainer_email'] ) ) : '' );
	update_post_meta( $post_id, 'elevora_experience', isset( $_POST['trainer_experience'] ) ? absint( $_POST['trainer_experience'] ) : 0 );
	if ( isset( $_POST['trainer_specializations'] ) && is_array( $_POST['trainer_specializations'] ) ) {
		$term_ids = array_values( array_filter( array_map( 'absint', wp_unslash( $_POST['trainer_specializations'] ) ) ) );
		wp_set_object_terms( $post_id, $term_ids, 'trainer_specialization', false );
	}
	$redirect = wp_get_referer() ?: home_url( '/' );
	wp_safe_redirect( add_query_arg( 'trainer_submitted', '1', $redirect ) );
	exit;
}
add_action( 'admin_post_elevora_submit_trainer', 'elevora_handle_trainer_submission' );
