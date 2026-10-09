<?php
/**
 * Public helpers.
 *
 * @package Gravitas_Video
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gravitas_video_get_option( $key, $default = '' ) {
	$options = get_option( 'gravitas_video_options', array() );
	return array_key_exists( $key, $options ) ? $options[ $key ] : $default;
}

function gravitas_video_get_meta( $post_id, $key, $default = '' ) {
	$value = get_post_meta( $post_id, '_gravitas_' . $key, true );
	return '' === $value ? $default : $value;
}

function gravitas_video_user_can_watch( $post_id, $user_id = 0 ) {
	$user_id = $user_id ?: get_current_user_id();
	$access  = gravitas_video_get_meta( $post_id, 'access_level', 'public' );
	$allowed = true;

	if ( 'logged_in' === $access ) {
		$allowed = $user_id > 0;
	} elseif ( 'members' === $access ) {
		$allowed = false;
		$levels  = array_filter( array_map( 'absint', explode( ',', gravitas_video_get_meta( $post_id, 'pmpro_levels' ) ) ) );
		if ( function_exists( 'pmpro_hasMembershipLevel' ) && $user_id ) {
			$allowed = empty( $levels ) ? pmpro_hasMembershipLevel( null, $user_id ) : pmpro_hasMembershipLevel( $levels, $user_id );
		}
		if ( ! $allowed && function_exists( 'wc_memberships_get_user_active_memberships' ) && $user_id ) {
			$allowed = ! empty( wc_memberships_get_user_active_memberships( $user_id ) );
		}
		$product_id = absint( gravitas_video_get_meta( $post_id, 'woo_product_id' ) );
		if ( ! $allowed && $product_id && function_exists( 'wc_customer_bought_product' ) && $user_id ) {
			$user    = get_userdata( $user_id );
			$allowed = $user && wc_customer_bought_product( $user->user_email, $user_id, $product_id );
		}
		$ihc_levels = array_filter( array_map( 'absint', explode( ',', gravitas_video_get_meta( $post_id, 'ihc_levels' ) ) ) );
		if ( ! $allowed && $user_id && function_exists( 'ihc_user_has_level' ) ) {
			if ( $ihc_levels ) {
				foreach ( $ihc_levels as $level_id ) {
					if ( ihc_user_has_level( $user_id, $level_id ) ) {
						$allowed = true;
						break;
					}
				}
			} elseif ( class_exists( '\Indeed\Ihc\UserSubscriptions' ) ) {
				$allowed = ! empty( \Indeed\Ihc\UserSubscriptions::getAllForUser( $user_id, true ) );
			}
		}
	}

	return (bool) apply_filters( 'gravitas_video_user_has_access', $allowed, $post_id, $user_id, $access );
}

/** Return the most useful join URL for the active membership integration. */
function gravitas_video_membership_url( $post_id ) {
	$product_id = absint( gravitas_video_get_meta( $post_id, 'woo_product_id' ) );
	if ( $product_id && function_exists( 'get_permalink' ) ) {
		return get_permalink( $product_id );
	}
	if ( function_exists( 'pmpro_url' ) ) {
		return pmpro_url( 'levels' );
	}
	$ihc_register = absint( get_option( 'ihc_general_register_default_page' ) );
	if ( $ihc_register ) {
		return get_permalink( $ihc_register );
	}
	return wp_registration_url();
}

function gravitas_video_format_count( $number ) {
	$number = absint( $number );
	if ( $number >= 1000000 ) {
		return number_format_i18n( $number / 1000000, 1 ) . 'M';
	}
	if ( $number >= 1000 ) {
		return number_format_i18n( $number / 1000, 1 ) . 'K';
	}
	return number_format_i18n( $number );
}

function gravitas_video_render_player( $post_id = 0 ) {
	$post_id = $post_id ?: get_the_ID();
	$objects = gravitas_video();
	return $objects['player']->render( $post_id );
}
