<?php
/**
 * Archive and search query handling.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_filter_trainer_archive( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'trainer' ) && ! $query->is_tax( array( 'trainer_specialization', 'trainer_location', 'coaching_format', 'trainer_language' ) ) ) {
		return;
	}

	$query->set( 'posts_per_page', 9 );
	$tax_query = (array) $query->get( 'tax_query' );
	$filters   = array(
		'specialization' => 'trainer_specialization',
		'location'       => 'trainer_location',
		'format'         => 'coaching_format',
		'language'       => 'trainer_language',
	);
	foreach ( $filters as $parameter => $taxonomy ) {
		if ( isset( $_GET[ $parameter ] ) && '' !== $_GET[ $parameter ] ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( $_GET[ $parameter ] ) ),
			);
		}
	}
	if ( $tax_query ) {
		$query->set( 'tax_query', $tax_query );
	}
	if ( isset( $_GET['trainer_search'] ) && '' !== $_GET['trainer_search'] ) {
		$query->set( 's', sanitize_text_field( wp_unslash( $_GET['trainer_search'] ) ) );
	}

	$sort = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : 'recommended';
	switch ( $sort ) {
		case 'rating':
			$query->set( 'meta_key', 'elevora_rating' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
			break;
		case 'experience':
			$query->set( 'meta_key', 'elevora_experience' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
			break;
		case 'price-low':
			$query->set( 'meta_key', 'elevora_hourly_rate' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			break;
		case 'newest':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
			break;
		default:
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
}
add_action( 'pre_get_posts', 'elevora_filter_trainer_archive' );

function elevora_search_post_types( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	/*
	 * Respect a post type that was requested explicitly. The Gravitas Video
	 * archive filter searches within gravitas_video, so broadening the search
	 * here would silently drop the videos and break the filter.
	 */
	$requested = '';
	if ( isset( $_GET['post_type'] ) && is_string( $_GET['post_type'] ) ) {
		$requested = sanitize_key( wp_unslash( $_GET['post_type'] ) );
	}

	$post_type   = $query->get( 'post_type' );
	$is_specific = ( $requested && 'any' !== $requested )
		|| ( $post_type && ! is_array( $post_type ) && 'post' !== $post_type && 'any' !== $post_type );

	if ( $is_specific ) {
		return;
	}

	$query->set( 'post_type', array( 'post', 'page', 'trainer', 'coaching_service', 'product' ) );
}
add_action( 'pre_get_posts', 'elevora_search_post_types' );

function elevora_trainer_query_args( $source = array() ) {
	$args = array(
		'post_type'      => 'trainer',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => isset( $source['paged'] ) ? max( 1, absint( $source['paged'] ) ) : 1,
	);
	if ( ! empty( $source['trainer_search'] ) ) {
		$args['s'] = sanitize_text_field( $source['trainer_search'] );
	}
	$filters = array(
		'specialization' => 'trainer_specialization',
		'location'       => 'trainer_location',
		'format'         => 'coaching_format',
		'language'       => 'trainer_language',
	);
	foreach ( $filters as $parameter => $taxonomy ) {
		if ( ! empty( $source[ $parameter ] ) ) {
			$args['tax_query'][] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => sanitize_title( $source[ $parameter ] ) );
		}
	}
	$sort = isset( $source['sort'] ) ? sanitize_key( $source['sort'] ) : 'recommended';
	if ( 'rating' === $sort ) {
		$args['meta_key'] = 'elevora_rating';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'DESC';
	} elseif ( 'experience' === $sort ) {
		$args['meta_key'] = 'elevora_experience';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'DESC';
	} elseif ( 'price-low' === $sort ) {
		$args['meta_key'] = 'elevora_hourly_rate';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'ASC';
	} elseif ( 'newest' === $sort ) {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	} else {
		$args['orderby'] = array( 'menu_order' => 'ASC', 'date' => 'DESC' );
	}
	return $args;
}

/**
 * Resolve unmatched single post, page, or custom post type requests.
 *
 * Prevents WordPress from falling back to the front page or redirecting
 * to home when rewrite rules in the database are desynchronized or missing.
 *
 * @param WP $wp Current WordPress environment instance.
 */
function elevora_resolve_unmatched_post_request( $wp ) {
	if ( ! empty( $wp->query_vars['name'] ) || ! empty( $wp->query_vars['p'] ) || ! empty( $wp->query_vars['pagename'] ) || ! empty( $wp->query_vars['page_id'] ) || ! empty( $wp->query_vars['category_name'] ) || ! empty( $wp->query_vars['tag'] ) || ! empty( $wp->query_vars['post_type'] ) ) {
		return;
	}

	if ( empty( $wp->request ) ) {
		return;
	}

	$slug = sanitize_title( basename( untrailingslashit( $wp->request ) ) );
	if ( empty( $slug ) ) {
		return;
	}

	// 1. Check for standard blog post first.
	$post = get_page_by_path( $slug, OBJECT, 'post' );
	if ( ! $post || 'publish' !== $post->post_status ) {
		$posts = get_posts(
			array(
				'name'        => $slug,
				'post_type'   => 'post',
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);
		if ( ! empty( $posts ) ) {
			$post = $posts[0];
		}
	}

	if ( $post && 'publish' === $post->post_status ) {
		$wp->query_vars['name']      = $post->post_name;
		$wp->query_vars['post_type'] = 'post';
		return;
	}

	// 2. Check custom post types and pages.
	foreach ( array( 'trainer', 'coaching_service', 'gravitas_video', 'page' ) as $post_type ) {
		$item = get_page_by_path( $slug, OBJECT, $post_type );
		if ( ! $item || 'publish' !== $item->post_status ) {
			$items = get_posts(
				array(
					'name'        => $slug,
					'post_type'   => $post_type,
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			);
			if ( ! empty( $items ) ) {
				$item = $items[0];
			}
		}

		if ( $item && 'publish' === $item->post_status ) {
			if ( 'page' === $post_type ) {
				$wp->query_vars['pagename'] = $item->post_name;
				$wp->query_vars['page_id']  = $item->ID;
			} else {
				$wp->query_vars['name']      = $item->post_name;
				$wp->query_vars['post_type'] = $post_type;
			}
			return;
		}
	}
}
add_action( 'parse_request', 'elevora_resolve_unmatched_post_request', 1 );

