<?php
/**
 * Native meta boxes and ACF field groups.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_get_field_value( $key, $post_id = 0, $default = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, $post_id );
		if ( false !== $value && null !== $value && '' !== $value ) {
			return $value;
		}
	}
	$value = get_post_meta( $post_id, $key, true );
	return ( '' !== $value && null !== $value ) ? $value : $default;
}

function elevora_meta_box_definitions() {
	return array(
		'trainer' => array(
			'elevora_role'             => array( __( 'Professional title', 'gravitas-coaching' ), 'text' ),
			'elevora_tagline'          => array( __( 'Short tagline', 'gravitas-coaching' ), 'text' ),
			'elevora_email'            => array( __( 'Public email', 'gravitas-coaching' ), 'email' ),
			'elevora_phone'            => array( __( 'Public phone', 'gravitas-coaching' ), 'text' ),
			'elevora_website'          => array( __( 'Website', 'gravitas-coaching' ), 'url' ),
			'elevora_booking_url'      => array( __( 'Discovery Call URL or Shortcode', 'gravitas-coaching' ), 'url_or_shortcode' ),
			'elevora_video_url'        => array( __( 'Introduction video URL', 'gravitas-coaching' ), 'url' ),
			'elevora_credentials'      => array( __( 'Credentials (one per line)', 'gravitas-coaching' ), 'textarea' ),
			'elevora_trainer_services' => array( __( 'Related service IDs (comma-separated)', 'gravitas-coaching' ), 'ids' ),
			'elevora_portfolio'        => array( __( 'Portfolio media IDs (comma-separated)', 'gravitas-coaching' ), 'ids' ),
		),
		'coaching_service' => array(
			'elevora_service_summary'  => array( __( 'Card summary', 'gravitas-coaching' ), 'textarea' ),
			'elevora_service_icon'     => array( __( 'Icon name (compass, spark, target, people, heart, chart)', 'gravitas-coaching' ), 'text' ),
			'elevora_service_featured' => array( __( 'Featured service', 'gravitas-coaching' ), 'checkbox' ),
			'elevora_service_trainers' => array( __( 'Related trainer IDs (comma-separated)', 'gravitas-coaching' ), 'ids' ),
		),
		'testimonial' => array(
			'elevora_client_role'        => array( __( 'Client role', 'gravitas-coaching' ), 'text' ),
			'elevora_client_company'     => array( __( 'Client company', 'gravitas-coaching' ), 'text' ),
			'elevora_testimonial_rating' => array( __( 'Rating (1–5)', 'gravitas-coaching' ), 'number' ),
		),
	);
}

function elevora_add_meta_boxes() {
	add_meta_box( 'elevora-trainer-statistics', __( 'Profile Statistics', 'gravitas-coaching' ), 'elevora_render_trainer_statistics', 'trainer', 'side', 'high' );
	add_meta_box( 'elevora-trainer-booking-text', __( 'Trainer Booking Card Text', 'gravitas-coaching' ), 'elevora_render_trainer_booking_text', 'trainer', 'normal', 'high' );
	add_meta_box( 'elevora-service-page-text', __( 'Service Page Text & Pricing', 'gravitas-coaching' ), 'elevora_render_service_page_text', 'coaching_service', 'normal', 'high' );
	if ( function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	foreach ( elevora_meta_box_definitions() as $post_type => $fields ) {
		add_meta_box(
			'elevora-' . $post_type . '-details',
			__( 'Gravitas Details', 'gravitas-coaching' ),
			'elevora_render_meta_box',
			$post_type,
			'normal',
			'high',
			array( 'fields' => $fields )
		);
	}
}
add_action( 'add_meta_boxes', 'elevora_add_meta_boxes' );

/** Display the figures shown beneath the trainer introduction. */
function elevora_render_trainer_statistics( $post ) {
	wp_nonce_field( 'elevora_save_trainer_statistics', 'elevora_trainer_statistics_nonce' );
	$rating     = get_post_meta( $post->ID, 'elevora_rating', true );
	$experience = get_post_meta( $post->ID, 'elevora_experience', true );
	$sessions   = get_post_meta( $post->ID, 'elevora_sessions', true );
	$price      = get_post_meta( $post->ID, 'elevora_hourly_rate', true );
	?>
	<p><?php esc_html_e( 'These values appear directly below the trainer introduction.', 'gravitas-coaching' ); ?></p>
	<p><label for="elevora-stat-price"><strong><?php esc_html_e( 'Session price', 'gravitas-coaching' ); ?></strong></label><input class="widefat" id="elevora-stat-price" name="elevora_trainer_statistics[price]" type="text" value="<?php echo esc_attr( $price ); ?>" placeholder="<?php esc_attr_e( '$165', 'gravitas-coaching' ); ?>"><small><?php esc_html_e( 'Include the currency symbol, for example $165, £150, or ₹5,000.', 'gravitas-coaching' ); ?></small></p>
	<p><label for="elevora-stat-rating"><strong><?php esc_html_e( 'Rating', 'gravitas-coaching' ); ?></strong></label><input class="widefat" id="elevora-stat-rating" name="elevora_trainer_statistics[rating]" type="number" min="0" max="5" step="0.1" value="<?php echo esc_attr( $rating ); ?>"><small><?php esc_html_e( 'Use a score from 0 to 5, for example 4.8.', 'gravitas-coaching' ); ?></small></p>
	<p><label for="elevora-stat-experience"><strong><?php esc_html_e( 'Years of experience', 'gravitas-coaching' ); ?></strong></label><input class="widefat" id="elevora-stat-experience" name="elevora_trainer_statistics[experience]" type="number" min="0" step="1" value="<?php echo esc_attr( $experience ); ?>"><small><?php esc_html_e( 'The plus sign is added automatically.', 'gravitas-coaching' ); ?></small></p>
	<p><label for="elevora-stat-sessions"><strong><?php esc_html_e( 'Sessions completed', 'gravitas-coaching' ); ?></strong></label><input class="widefat" id="elevora-stat-sessions" name="elevora_trainer_statistics[sessions]" type="number" min="0" step="1" value="<?php echo esc_attr( $sessions ); ?>"><small><?php esc_html_e( 'The plus sign is added automatically.', 'gravitas-coaching' ); ?></small></p>
	<style>#elevora-trainer-statistics label{display:block;margin-bottom:5px}#elevora-trainer-statistics small{display:block;margin-top:5px;color:#646970;line-height:1.4}</style>
	<?php
}

function elevora_save_trainer_statistics( $post_id ) {
	if ( 'trainer' !== get_post_type( $post_id ) || ! isset( $_POST['elevora_trainer_statistics_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elevora_trainer_statistics_nonce'] ) ), 'elevora_save_trainer_statistics' ) || ! current_user_can( 'edit_post', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	$stats = isset( $_POST['elevora_trainer_statistics'] ) && is_array( $_POST['elevora_trainer_statistics'] ) ? wp_unslash( $_POST['elevora_trainer_statistics'] ) : array();
	if ( isset( $stats['rating'] ) ) {
		update_post_meta( $post_id, 'elevora_rating', min( 5, max( 0, (float) $stats['rating'] ) ) );
	}
	if ( isset( $stats['experience'] ) ) {
		update_post_meta( $post_id, 'elevora_experience', absint( $stats['experience'] ) );
	}
	if ( isset( $stats['sessions'] ) ) {
		update_post_meta( $post_id, 'elevora_sessions', absint( $stats['sessions'] ) );
	}
	if ( isset( $stats['price'] ) ) {
		update_post_meta( $post_id, 'elevora_hourly_rate', sanitize_text_field( $stats['price'] ) );
	}
}
add_action( 'save_post_trainer', 'elevora_save_trainer_statistics' );

function elevora_text_box_field( $post_id, $key, $label, $default = '', $textarea = false, $description = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	$value = '' !== $value ? $value : $default;
	?>
	<p class="elevora-display-field"><label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
	<?php if ( $textarea ) : ?><textarea class="widefat" rows="4" id="<?php echo esc_attr( $key ); ?>" name="elevora_display_text[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $value ); ?></textarea><?php else : ?><input class="widefat" id="<?php echo esc_attr( $key ); ?>" name="elevora_display_text[<?php echo esc_attr( $key ); ?>]" type="text" value="<?php echo esc_attr( $value ); ?>"><?php endif; ?>
	<?php if ( $description ) : ?><small><?php echo esc_html( $description ); ?></small><?php endif; ?></p>
	<?php
}

function elevora_render_trainer_booking_text( $post ) {
	wp_nonce_field( 'elevora_save_trainer_booking_text', 'elevora_trainer_booking_text_nonce' );
	elevora_text_box_field( $post->ID, 'elevora_booking_eyebrow', __( 'Card heading', 'gravitas-coaching' ), __( 'Start a conversation', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_rate_label', __( 'Price label', 'gravitas-coaching' ), __( 'Sessions from', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_availability', __( 'Availability message', 'gravitas-coaching' ), '', false, __( 'Example: Two consultations available next week', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_booking_benefits', __( 'Checklist items', 'gravitas-coaching' ), "Introductory fit call\nClear goals and next steps\nFlexible online sessions", true, __( 'Enter one item per line.', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_availability_button_text', __( 'Button text', 'gravitas-coaching' ), __( 'Check availability', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_availability_url', __( 'Button URL or shortcode', 'gravitas-coaching' ), '', false, __( 'Leave empty to use the Discovery Call URL.', 'gravitas-coaching' ) );
	elevora_display_text_box_style();
}

function elevora_render_service_page_text( $post ) {
	wp_nonce_field( 'elevora_save_service_page_text', 'elevora_service_page_text_nonce' );
	elevora_text_box_field( $post->ID, 'elevora_service_duration', __( 'Duration value', 'gravitas-coaching' ), '', false, __( 'Example: 4 weeks', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_duration_label', __( 'Duration label', 'gravitas-coaching' ), __( 'Typical duration', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_service_price', __( 'Investment value', 'gravitas-coaching' ), '', false, __( 'Example: From $650', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_investment_label', __( 'Investment label', 'gravitas-coaching' ), __( 'Investment', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_service_format', __( 'Format value', 'gravitas-coaching' ), '', false, __( 'Leave empty to use the assigned delivery methods.', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_format_label', __( 'Format label', 'gravitas-coaching' ), __( 'Format', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_includes_title', __( 'Includes card heading', 'gravitas-coaching' ), __( 'This service includes', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_includes_items', __( 'Included items', 'gravitas-coaching' ), "A clear starting assessment\nPersonalized session plan\nPractical tools and reflection\nProgress check-ins", true, __( 'Enter one item per line.', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_get_started_text', __( 'Button text', 'gravitas-coaching' ), __( 'Get started', 'gravitas-coaching' ) );
	elevora_text_box_field( $post->ID, 'elevora_booking_url', __( 'Button URL or shortcode', 'gravitas-coaching' ), '#contact' );
	elevora_display_text_box_style();
}

function elevora_display_text_box_style() {
	echo '<style>.elevora-display-field{margin:0 0 16px}.elevora-display-field label,.elevora-display-field small{display:block}.elevora-display-field label{margin-bottom:6px}.elevora-display-field small{margin-top:5px;color:#646970}.elevora-display-field textarea{white-space:pre-wrap}</style>';
}

function elevora_save_display_text_box( $post_id ) {
	$post_type = get_post_type( $post_id );
	if ( ! in_array( $post_type, array( 'trainer', 'coaching_service' ), true ) || ! current_user_can( 'edit_post', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return;
	}
	$valid_nonce = 'trainer' === $post_type
		? isset( $_POST['elevora_trainer_booking_text_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elevora_trainer_booking_text_nonce'] ) ), 'elevora_save_trainer_booking_text' )
		: isset( $_POST['elevora_service_page_text_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elevora_service_page_text_nonce'] ) ), 'elevora_save_service_page_text' );
	if ( ! $valid_nonce || empty( $_POST['elevora_display_text'] ) || ! is_array( $_POST['elevora_display_text'] ) ) {
		return;
	}
	$allowed = 'trainer' === $post_type
		? array( 'elevora_booking_eyebrow', 'elevora_rate_label', 'elevora_availability', 'elevora_booking_benefits', 'elevora_availability_button_text', 'elevora_availability_url' )
		: array( 'elevora_service_duration', 'elevora_duration_label', 'elevora_service_price', 'elevora_investment_label', 'elevora_service_format', 'elevora_format_label', 'elevora_includes_title', 'elevora_includes_items', 'elevora_get_started_text', 'elevora_booking_url' );
	$textarea_fields = array( 'elevora_booking_benefits', 'elevora_includes_items' );
	$url_fields = array( 'elevora_availability_url', 'elevora_booking_url' );
	$input = wp_unslash( $_POST['elevora_display_text'] );
	foreach ( $allowed as $key ) {
		if ( ! array_key_exists( $key, $input ) ) {
			continue;
		}
		$value = in_array( $key, $textarea_fields, true ) ? sanitize_textarea_field( $input[ $key ] ) : ( in_array( $key, $url_fields, true ) ? elevora_sanitize_url_or_shortcode( $input[ $key ] ) : sanitize_text_field( $input[ $key ] ) );
		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_trainer', 'elevora_save_display_text_box' );
add_action( 'save_post_coaching_service', 'elevora_save_display_text_box' );

function elevora_render_meta_box( $post, $box ) {
	wp_nonce_field( 'elevora_save_meta', 'elevora_meta_nonce' );
	$fields = isset( $box['args']['fields'] ) ? $box['args']['fields'] : array();
	?>
	<div class="elevora-meta-grid">
		<?php foreach ( $fields as $key => $settings ) :
			$value = get_post_meta( $post->ID, $key, true );
			$type  = $settings[1];
			if ( 'ids' === $type && is_array( $value ) ) {
				$value = implode( ', ', array_map( 'absint', $value ) );
			}
			?>
			<p class="elevora-meta-field">
				<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $settings[0] ); ?></strong></label>
				<?php if ( 'textarea' === $type ) : ?>
					<textarea class="widefat" rows="4" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
				<?php elseif ( 'checkbox' === $type ) : ?>
					<label><input type="checkbox" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $value ); ?>> <?php esc_html_e( 'Yes', 'gravitas-coaching' ); ?></label>
				<?php else : ?>
					<input class="widefat" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( in_array( $type, array( 'email', 'url', 'number' ), true ) ? $type : 'text' ); ?>" <?php echo 'number-step' === $type ? 'step="0.1" min="0" max="5"' : ''; ?> value="<?php echo esc_attr( $value ); ?>">
				<?php endif; ?>
			</p>
		<?php endforeach; ?>
	</div>
	<style>.elevora-meta-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 20px}.elevora-meta-field{margin:0}.elevora-meta-field label:first-child{display:block;margin-bottom:6px}@media(max-width:782px){.elevora-meta-grid{grid-template-columns:1fr}}</style>
	<?php
}

function elevora_save_meta_boxes( $post_id ) {
	if ( ! isset( $_POST['elevora_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elevora_meta_nonce'] ) ), 'elevora_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$post_type   = get_post_type( $post_id );
	$definitions = elevora_meta_box_definitions();
	if ( empty( $definitions[ $post_type ] ) ) {
		return;
	}
	foreach ( $definitions[ $post_type ] as $key => $settings ) {
		$type = $settings[1];
		if ( 'checkbox' === $type ) {
			update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? 1 : 0 );
			continue;
		}
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );
		switch ( $type ) {
			case 'email':
				$value = sanitize_email( $raw );
				break;
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			case 'url_or_shortcode':
				$value = elevora_sanitize_url_or_shortcode( $raw );
				break;
			case 'number':
				$value = absint( $raw );
				break;
			case 'number-step':
				$value = min( 5, max( 0, (float) $raw ) );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'ids':
				$value = array_values( array_filter( array_map( 'absint', preg_split( '/\s*,\s*/', $raw ) ) ) );
				break;
			default:
				$value = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post', 'elevora_save_meta_boxes' );

function elevora_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_elevora_trainer',
			'title'  => __( 'Trainer Profile', 'gravitas-coaching' ),
			'fields' => array(
				array( 'key' => 'field_elevora_role', 'label' => __( 'Professional title', 'gravitas-coaching' ), 'name' => 'elevora_role', 'type' => 'text', 'required' => 1 ),
				array( 'key' => 'field_elevora_tagline', 'label' => __( 'Short tagline', 'gravitas-coaching' ), 'name' => 'elevora_tagline', 'type' => 'text' ),
				array( 'key' => 'field_elevora_email', 'label' => __( 'Public email', 'gravitas-coaching' ), 'name' => 'elevora_email', 'type' => 'email' ),
				array( 'key' => 'field_elevora_phone', 'label' => __( 'Public phone', 'gravitas-coaching' ), 'name' => 'elevora_phone', 'type' => 'text' ),
				array( 'key' => 'field_elevora_website', 'label' => __( 'Website', 'gravitas-coaching' ), 'name' => 'elevora_website', 'type' => 'url' ),
				array( 'key' => 'field_elevora_booking_url', 'label' => __( 'Discovery Call URL or Shortcode', 'gravitas-coaching' ), 'name' => 'elevora_booking_url', 'type' => 'text', 'instructions' => __( 'Enter a link URL or a booking shortcode.', 'gravitas-coaching' ) ),
				array( 'key' => 'field_elevora_video_url', 'label' => __( 'Introduction video URL', 'gravitas-coaching' ), 'name' => 'elevora_video_url', 'type' => 'url' ),
				array( 'key' => 'field_elevora_credentials', 'label' => __( 'Credentials', 'gravitas-coaching' ), 'name' => 'elevora_credentials', 'type' => 'textarea', 'new_lines' => 'br' ),
				array( 'key' => 'field_elevora_trainer_services', 'label' => __( 'Services', 'gravitas-coaching' ), 'name' => 'elevora_trainer_services', 'type' => 'relationship', 'post_type' => array( 'coaching_service' ), 'return_format' => 'id' ),
				array( 'key' => 'field_elevora_portfolio', 'label' => __( 'Portfolio', 'gravitas-coaching' ), 'name' => 'elevora_portfolio', 'type' => 'gallery', 'return_format' => 'id', 'preview_size' => 'medium' ),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'trainer' ) ) ),
			'position' => 'normal',
			'style'    => 'default',
			'active'   => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_elevora_service',
			'title'  => __( 'Service Details', 'gravitas-coaching' ),
			'fields' => array(
				array( 'key' => 'field_elevora_service_summary', 'label' => __( 'Card summary', 'gravitas-coaching' ), 'name' => 'elevora_service_summary', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_elevora_service_icon', 'label' => __( 'Icon', 'gravitas-coaching' ), 'name' => 'elevora_service_icon', 'type' => 'select', 'choices' => array( 'compass' => 'Compass', 'spark' => 'Spark', 'target' => 'Target', 'people' => 'People', 'heart' => 'Heart', 'chart' => 'Chart' ) ),
				array( 'key' => 'field_elevora_service_featured', 'label' => __( 'Featured service', 'gravitas-coaching' ), 'name' => 'elevora_service_featured', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_elevora_service_trainers', 'label' => __( 'Trainers', 'gravitas-coaching' ), 'name' => 'elevora_service_trainers', 'type' => 'relationship', 'post_type' => array( 'trainer' ), 'return_format' => 'id' ),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'coaching_service' ) ) ),
			'position' => 'normal',
			'active'   => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'    => 'group_elevora_testimonial',
			'title'  => __( 'Testimonial Details', 'gravitas-coaching' ),
			'fields' => array(
				array( 'key' => 'field_elevora_client_role', 'label' => __( 'Client role', 'gravitas-coaching' ), 'name' => 'elevora_client_role', 'type' => 'text' ),
				array( 'key' => 'field_elevora_client_company', 'label' => __( 'Client company', 'gravitas-coaching' ), 'name' => 'elevora_client_company', 'type' => 'text' ),
				array( 'key' => 'field_elevora_testimonial_rating', 'label' => __( 'Rating', 'gravitas-coaching' ), 'name' => 'elevora_testimonial_rating', 'type' => 'number', 'min' => 1, 'max' => 5 ),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'testimonial' ) ) ),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'elevora_register_acf_fields' );
