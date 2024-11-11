<?php
/**
 * Google Analytics 4 Extensions - Data Layer Functions
 *
 * @package ga4-extensions
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Retrieves the post author's username.
 *
 * @return string Author username or empty string.
 */
function ga4_ext_get_post_author() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$post_author = get_userdata( $post->post_author );

	return $post_author ? $post_author->user_login : 'unknown';
}

/**
 * Retrieves the post categories as a space-separated string.
 *
 * @return string Categories or empty string.
 */
function ga4_ext_get_post_category() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$terms = get_the_terms( $post->ID, 'category' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return 'uncategorized';
	}

	$term_slugs = wp_list_pluck( $terms, 'slug' );

	return trim( implode( ' ', $term_slugs ) );
}

/**
 * Retrieves the post tags as a space-separated string.
 *
 * @return string Tags or empty string.
 */
function ga4_ext_get_post_tags() {
	if ( ! is_single() ) {
		return '';
	}

	global $post;

	if ( ! $post ) {
		return '';
	}

	$terms = get_the_terms( $post->ID, 'post_tag' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	$term_slugs = wp_list_pluck( $terms, 'slug' );

	return trim( implode( ' ', $term_slugs ) );
}

/**
 * Is the current user a subscriber?
 *
 * @return int
 */
function ga4_ext_is_subscriber(): int {
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();

		if (
			$user instanceof WP_User
			&& in_array( 'subscriber', (array) $user->roles, true )
		) {
			return 1;
		}
	}

	return 0;
}

/**
 * Enqueues the custom dataLayer script with post author, category, and tags.
 */
function ga4_ext_enqueue_data_layer_script() {
	$data = [];

	$data['user_properties'] = [
		'is_subscriber' => ga4_ext_is_subscriber(),
	];

	// If on single post, add post data
	if ( is_single() ) {
		$post_author   = ga4_ext_get_post_author();
		$post_category = ga4_ext_get_post_category();
		$post_tags     = ga4_ext_get_post_tags();

		$data['post_author']   = $post_author;
		$data['post_category'] = $post_category;
		$data['post_tags']     = $post_tags;
	}

	$json_data = wp_json_encode( $data );
	if ( false === $json_data ) {
		return;
	}

	// Add the dataLayer.push script inline
	wp_register_script( 'ga4-ext-data-layer', '' );
	wp_enqueue_script( 'ga4-ext-data-layer' );
	wp_add_inline_script(
		'ga4-ext-data-layer',
		'window.dataLayer = window.dataLayer || [];
window.dataLayer.push(' . wp_json_encode( $data ) . ');',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'ga4_ext_enqueue_data_layer_script', 1 );
