<?php
/**
 * Custom content types and taxonomies.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_register_post_types() {
	register_post_type(
		'trainer',
		array(
			'labels' => array(
				'name'               => __( 'Trainers', 'gravitas-coaching' ),
				'singular_name'      => __( 'Trainer', 'gravitas-coaching' ),
				'add_new_item'       => __( 'Add New Trainer', 'gravitas-coaching' ),
				'edit_item'          => __( 'Edit Trainer', 'gravitas-coaching' ),
				'new_item'           => __( 'New Trainer', 'gravitas-coaching' ),
				'view_item'          => __( 'View Trainer', 'gravitas-coaching' ),
				'search_items'       => __( 'Search Trainers', 'gravitas-coaching' ),
				'not_found'          => __( 'No trainers found', 'gravitas-coaching' ),
				'not_found_in_trash' => __( 'No trainers found in Trash', 'gravitas-coaching' ),
				'all_items'          => __( 'All Trainers', 'gravitas-coaching' ),
				'menu_name'          => __( 'Trainers', 'gravitas-coaching' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'trainers', 'with_front' => false ),
			'menu_icon'    => 'dashicons-businessperson',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'custom-fields' ),
			'taxonomies'   => array( 'trainer_specialization', 'trainer_location', 'coaching_format', 'trainer_language' ),
		)
	);

	register_post_type(
		'coaching_service',
		array(
			'labels' => array(
				'name'          => __( 'Services', 'gravitas-coaching' ),
				'singular_name' => __( 'Service', 'gravitas-coaching' ),
				'add_new_item'  => __( 'Add New Service', 'gravitas-coaching' ),
				'edit_item'     => __( 'Edit Service', 'gravitas-coaching' ),
				'view_item'     => __( 'View Service', 'gravitas-coaching' ),
				'search_items'  => __( 'Search Services', 'gravitas-coaching' ),
				'all_items'     => __( 'All Services', 'gravitas-coaching' ),
				'menu_name'     => __( 'Services', 'gravitas-coaching' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'coaching-services', 'with_front' => false ),
			'menu_icon'    => 'dashicons-lightbulb',
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields' ),
			'taxonomies'   => array( 'service_category', 'service_delivery', 'service_audience' ),
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels' => array(
				'name'          => __( 'Testimonials', 'gravitas-coaching' ),
				'singular_name' => __( 'Testimonial', 'gravitas-coaching' ),
				'add_new_item'  => __( 'Add New Testimonial', 'gravitas-coaching' ),
				'edit_item'     => __( 'Edit Testimonial', 'gravitas-coaching' ),
				'menu_name'     => __( 'Testimonials', 'gravitas-coaching' ),
			),
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-format-quote',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
		)
	);

	$taxonomies = array(
		'trainer_specialization' => array( 'trainer', __( 'Specializations', 'gravitas-coaching' ), __( 'Specialization', 'gravitas-coaching' ), true, 'specialization' ),
		'trainer_location'       => array( 'trainer', __( 'Locations', 'gravitas-coaching' ), __( 'Location', 'gravitas-coaching' ), true, 'trainer-location' ),
		'coaching_format'        => array( 'trainer', __( 'Coaching Formats', 'gravitas-coaching' ), __( 'Coaching Format', 'gravitas-coaching' ), false, 'coaching-format' ),
		'trainer_language'       => array( 'trainer', __( 'Languages', 'gravitas-coaching' ), __( 'Language', 'gravitas-coaching' ), false, 'trainer-language' ),
		'service_category'       => array( 'coaching_service', __( 'Service Categories', 'gravitas-coaching' ), __( 'Service Category', 'gravitas-coaching' ), true, 'service-category' ),
		'service_delivery'       => array( 'coaching_service', __( 'Delivery Methods', 'gravitas-coaching' ), __( 'Delivery Method', 'gravitas-coaching' ), false, 'service-delivery' ),
		'service_audience'       => array( 'coaching_service', __( 'Audiences', 'gravitas-coaching' ), __( 'Audience', 'gravitas-coaching' ), false, 'service-audience' ),
	);

	foreach ( $taxonomies as $taxonomy => $settings ) {
		register_taxonomy(
			$taxonomy,
			$settings[0],
			array(
				'labels' => array(
					'name'          => $settings[1],
					'singular_name' => $settings[2],
					'search_items'  => sprintf( __( 'Search %s', 'gravitas-coaching' ), $settings[1] ),
					'all_items'     => sprintf( __( 'All %s', 'gravitas-coaching' ), $settings[1] ),
					'edit_item'     => sprintf( __( 'Edit %s', 'gravitas-coaching' ), $settings[2] ),
					'add_new_item'  => sprintf( __( 'Add New %s', 'gravitas-coaching' ), $settings[2] ),
				),
				'public'            => true,
				'hierarchical'      => $settings[3],
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $settings[4], 'with_front' => false ),
			)
		);
	}
}
add_action( 'init', 'elevora_register_post_types', 5 );

function elevora_register_post_meta() {
	$fields = array(
		'elevora_role'              => 'string',
		'elevora_tagline'           => 'string',
		'elevora_experience'        => 'integer',
		'elevora_hourly_rate'       => 'string',
		'elevora_rating'            => 'number',
		'elevora_sessions'          => 'integer',
		'elevora_email'             => 'string',
		'elevora_phone'             => 'string',
		'elevora_website'           => 'string',
		'elevora_booking_url'       => 'string',
		'elevora_availability_url'  => 'string',
		'elevora_video_url'         => 'string',
		'elevora_credentials'       => 'string',
		'elevora_availability'      => 'string',
		'elevora_booking_eyebrow'   => 'string',
		'elevora_rate_label'        => 'string',
		'elevora_booking_benefits'  => 'string',
		'elevora_availability_button_text' => 'string',
		'elevora_trainer_services'  => 'array',
		'elevora_portfolio'         => 'array',
		'elevora_service_summary'   => 'string',
		'elevora_service_duration'  => 'string',
		'elevora_service_price'     => 'string',
		'elevora_duration_label'    => 'string',
		'elevora_investment_label'  => 'string',
		'elevora_format_label'      => 'string',
		'elevora_service_format'    => 'string',
		'elevora_includes_title'    => 'string',
		'elevora_includes_items'    => 'string',
		'elevora_get_started_text'  => 'string',
		'elevora_service_icon'      => 'string',
		'elevora_service_featured'  => 'boolean',
		'elevora_service_trainers'  => 'array',
		'elevora_client_role'       => 'string',
		'elevora_client_company'    => 'string',
		'elevora_testimonial_rating'=> 'integer',
	);

	foreach ( $fields as $key => $type ) {
		$args = array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'sanitize_callback' => 'elevora_sanitize_registered_meta',
			'auth_callback'     => function() { return current_user_can( 'edit_posts' ); },
		);
		if ( 'array' === $type ) {
			$args['show_in_rest'] = array( 'schema' => array( 'type' => 'array', 'items' => array( 'type' => 'integer' ) ) );
		}
		register_post_meta( '', $key, $args );
	}
}
add_action( 'init', 'elevora_register_post_meta', 20 );

function elevora_sanitize_registered_meta( $value, $meta_key ) {
	$array_fields = array( 'elevora_trainer_services', 'elevora_portfolio', 'elevora_service_trainers' );
	$url_fields   = array( 'elevora_website', 'elevora_video_url' );
	if ( in_array( $meta_key, $array_fields, true ) ) {
		return array_values( array_filter( array_map( 'absint', (array) $value ) ) );
	}
	if ( in_array( $meta_key, array( 'elevora_booking_url', 'elevora_availability_url' ), true ) ) {
		return elevora_sanitize_url_or_shortcode( $value );
	}
	if ( in_array( $meta_key, $url_fields, true ) ) {
		return esc_url_raw( $value );
	}
	if ( 'elevora_email' === $meta_key ) {
		return sanitize_email( $value );
	}
	if ( in_array( $meta_key, array( 'elevora_credentials', 'elevora_booking_benefits', 'elevora_includes_items' ), true ) ) {
		return sanitize_textarea_field( $value );
	}
	return sanitize_text_field( $value );
}
