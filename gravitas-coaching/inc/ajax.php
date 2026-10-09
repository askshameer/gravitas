<?php
/**
 * Front-end AJAX endpoints.
 *
 * @package Elevora
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function elevora_ajax_filter_trainers() {
	check_ajax_referer( 'elevora_directory', 'nonce' );
	$source = array();
	$keys   = array( 'trainer_search', 'specialization', 'location', 'format', 'language', 'sort', 'paged' );
	foreach ( $keys as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			$source[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		}
	}
	$query = new WP_Query( elevora_trainer_query_args( $source ) );
	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/card', 'trainer' );
		}
	} else {
		get_template_part( 'template-parts/content', 'none', array( 'message' => __( 'No trainers match those filters yet. Try widening your search.', 'gravitas-coaching' ) ) );
	}
	$cards = ob_get_clean();
	ob_start();
	elevora_pagination( $query );
	$pagination = ob_get_clean();
	wp_reset_postdata();
	wp_send_json_success(
		array(
			'cards'      => $cards,
			'pagination' => $pagination,
			'count'      => $query->found_posts,
			'url'        => add_query_arg( array_filter( $source ), get_post_type_archive_link( 'trainer' ) ),
		)
	);
}
add_action( 'wp_ajax_elevora_filter_trainers', 'elevora_ajax_filter_trainers' );
add_action( 'wp_ajax_nopriv_elevora_filter_trainers', 'elevora_ajax_filter_trainers' );

